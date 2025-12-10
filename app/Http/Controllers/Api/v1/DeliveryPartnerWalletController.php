<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPartner;
use App\Models\PaymentDetail;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DeliveryPartnerWalletController extends Controller
{
    /**
     * Process wallet transaction (Add or Withdraw based on type)
     * type: "in" = Add money to wallet
     * type: "out" = Withdraw money from wallet
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function walletTransaction(Request $request)
    {
        $user = auth()->user();

        // Check if user is a delivery partner
        if (! $user || $user->role !== 'delivery_partner') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only delivery partners can access this API.',
            ], 403);
        }

        // Check if delivery partner profile exists
        $partner = DeliveryPartner::where('user_id', $user->id)->first();
        if (! $partner) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery partner profile not found.',
            ], 404);
        }

        // Base validation rules
        $rules = [
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:1|max:100000',
            'reason' => 'nullable|string|max:100',
        ];

        // Add conditional validation based on type
        if ($request->input('type') === 'in') {
            // For adding balance - need payment details
            $rules['pay_type'] = 'required|in:bank,upi';
            $rules['pay_bank_name'] = 'required_if:pay_type,bank|nullable|string|max:150';
            $rules['pay_bank_account_number'] = 'required_if:pay_type,bank|nullable|string|max:50';
            $rules['pay_bank_ifsc'] = 'required_if:pay_type,bank|nullable|string|max:20';
            $rules['pay_upi_id'] = 'required_if:pay_type,upi|nullable|string|max:100';
            $rules['account_holder_name'] = 'nullable|string|max:150';
        } else {
            // For withdrawal - need existing payment detail ID
            $rules['payment_detail_id'] = 'required|exists:payment_details,id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $transactionType = $validated['type'];
        $amount = (float) $validated['amount'];
        $currentBalance = (float) $user->wallet_balance;

        // For withdrawal, check sufficient balance
        if ($transactionType === 'out' && $currentBalance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient wallet balance.',
                'data' => [
                    'current_balance' => $currentBalance,
                    'requested_amount' => $amount,
                ],
            ], 400);
        }

        DB::beginTransaction();
        try {
            $balanceBefore = $currentBalance;
            $paymentDetail = null;

            if ($transactionType === 'in') {
                // Create or update payment detail for deposit
                $paymentDetailData = [
                    'user_id' => $user->id,
                    'pay_type' => $validated['pay_type'],
                    'account_holder_name' => $validated['account_holder_name'] ?? $user->first_name.' '.$user->last_name,
                ];

                if ($validated['pay_type'] === 'bank') {
                    $paymentDetailData['pay_bank_name'] = $validated['pay_bank_name'];
                    $paymentDetailData['pay_bank_account_number'] = $validated['pay_bank_account_number'];
                    $paymentDetailData['pay_bank_ifsc'] = $validated['pay_bank_ifsc'];
                    $paymentDetailData['pay_upi_id'] = null;
                } else {
                    $paymentDetailData['pay_upi_id'] = $validated['pay_upi_id'];
                    $paymentDetailData['pay_bank_name'] = null;
                    $paymentDetailData['pay_bank_account_number'] = null;
                    $paymentDetailData['pay_bank_ifsc'] = null;
                }

                // Check if this payment detail already exists
                $existingPaymentDetail = PaymentDetail::where('user_id', $user->id)
                    ->where('pay_type', $validated['pay_type']);

                if ($validated['pay_type'] === 'bank') {
                    $existingPaymentDetail->where('pay_bank_account_number', $validated['pay_bank_account_number']);
                } else {
                    $existingPaymentDetail->where('pay_upi_id', $validated['pay_upi_id']);
                }

                $paymentDetail = $existingPaymentDetail->first();

                if (! $paymentDetail) {
                    $paymentDetail = PaymentDetail::create($paymentDetailData);
                } else {
                    $paymentDetail->update($paymentDetailData);
                }

                $defaultReason = WalletTransaction::REASON_DEPOSIT;
            } else {
                // For withdrawal, get existing payment detail
                $paymentDetail = PaymentDetail::where('id', $validated['payment_detail_id'])
                    ->where('user_id', $user->id)
                    ->first();

                if (! $paymentDetail) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Payment detail not found or does not belong to you.',
                    ], 404);
                }

                $defaultReason = WalletTransaction::REASON_WITHDRAWAL;
            }

            // Create wallet transaction (model boot method will auto-update user balance)
            $transaction = WalletTransaction::create([
                'user_id' => $user->id,
                'payment_detail_id' => $paymentDetail->id,
                'type' => $transactionType === 'in' ? WalletTransaction::TYPE_IN : WalletTransaction::TYPE_OUT,
                'reason' => $validated['reason'] ?? $defaultReason,
                'amount' => $amount,
                'status' => WalletTransaction::STATUS_PENDING,
            ]);

            // Refresh user to get updated balance
            $user->refresh();
            $balanceAfter = (float) $user->wallet_balance;

            DB::commit();

            $actionLabel = $transactionType === 'in' ? 'deposit' : 'withdrawal';
            Log::info("Wallet {$actionLabel} successful for user {$user->id}: Amount ₹{$amount}, New Balance: ₹{$balanceAfter}");

            // Build response
            $responseData = [
                'transaction' => [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'type_label' => $transactionType === 'in' ? 'Credit' : 'Debit',
                    'amount' => $transaction->amount,
                    'reason' => $transaction->reason,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at,
                ],
                'payment_detail' => [
                    'id' => $paymentDetail->id,
                    'pay_type' => $paymentDetail->pay_type,
                    'pay_bank_name' => $paymentDetail->pay_bank_name,
                    'masked_account_number' => $paymentDetail->masked_account_number,
                    'masked_upi_id' => $paymentDetail->masked_upi_id,
                    'account_holder_name' => $paymentDetail->account_holder_name,
                ],
                'wallet' => [
                    'previous_balance' => $balanceBefore,
                    'amount' => $amount,
                    'transaction_type' => $transactionType === 'in' ? 'added' : 'withdrawn',
                    'current_balance' => $balanceAfter,
                ],
            ];

            $message = $transactionType === 'in'
                ? 'Amount added to wallet successfully.'
                : 'Withdrawal processed successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $responseData,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            $actionLabel = $transactionType === 'in' ? 'deposit' : 'withdrawal';
            Log::error("Wallet {$actionLabel} failed for user {$user->id}: ".$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to process wallet transaction.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get wallet balance and recent transactions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWalletDetails(Request $request)
    {
        $user = auth()->user();

        // Check if user is a delivery partner
        if (! $user || $user->role !== 'delivery_partner') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only delivery partners can access this API.',
            ], 403);
        }

        // Get recent transactions
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'type_label' => $transaction->type === 'in' ? 'Credit' : 'Debit',
                    'amount' => $transaction->amount,
                    'formatted_amount' => $transaction->formatted_amount,
                    'reason' => $transaction->reason,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                ];
            });

        // Get payment details
        $paymentDetails = PaymentDetail::where('user_id', $user->id)
            ->get()
            ->map(function ($pd) {
                return [
                    'id' => $pd->id,
                    'pay_type' => $pd->pay_type,
                    'pay_bank_name' => $pd->pay_bank_name,
                    'masked_account_number' => $pd->masked_account_number,
                    'masked_upi_id' => $pd->masked_upi_id,
                    'account_holder_name' => $pd->account_holder_name,
                ];
            });

        // Calculate totals
        $totalCredits = WalletTransaction::where('user_id', $user->id)
            ->where('type', WalletTransaction::TYPE_IN)
            ->where('status', WalletTransaction::STATUS_COMPLETED)
            ->sum('amount');

        $totalDebits = WalletTransaction::where('user_id', $user->id)
            ->where('type', WalletTransaction::TYPE_OUT)
            ->where('status', WalletTransaction::STATUS_COMPLETED)
            ->sum('amount');

        $pendingWithdrawals = WalletTransaction::where('user_id', $user->id)
            ->where('type', WalletTransaction::TYPE_OUT)
            ->where('status', WalletTransaction::STATUS_PENDING)
            ->sum('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'wallet' => [
                    'current_balance' => (float) $user->wallet_balance,
                    'total_credits' => (float) $totalCredits,
                    'total_debits' => (float) $totalDebits,
                    'pending_withdrawals' => (float) $pendingWithdrawals,
                ],
                'payment_details' => $paymentDetails,
                'recent_transactions' => $transactions,
            ],
        ], 200);
    }

    /**
     * Get transaction history with filters
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function getTransactionHistory(Request $request)
    // {
    //     $user = auth()->user();

    //     // Check if user is a delivery partner
    //     if (! $user || $user->role !== 'delivery_partner') {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Access denied. Only delivery partners can access this API.',
    //         ], 403);
    //     }

    //     // Validate filters
    //     $validator = Validator::make($request->all(), [
    //         'type' => 'nullable|in:in,out',
    //         'status' => 'nullable|in:pending,completed,failed,cancelled',
    //         'start_date' => 'nullable|date',
    //         'end_date' => 'nullable|date|after_or_equal:start_date',
    //         'per_page' => 'nullable|integer|min:5|max:100',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation failed.',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     $query = WalletTransaction::where('user_id', $user->id);

    //     // Apply filters
    //     if ($request->has('type')) {
    //         $query->where('type', $request->type);
    //     }

    //     if ($request->has('status')) {
    //         $query->where('status', $request->status);
    //     }

    //     if ($request->has('start_date')) {
    //         $query->whereDate('created_at', '>=', $request->start_date);
    //     }

    //     if ($request->has('end_date')) {
    //         $query->whereDate('created_at', '<=', $request->end_date);
    //     }

    //     $perPage = $request->get('per_page', 20);
    //     $transactions = $query->orderByDesc('created_at')->paginate($perPage);

    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'transactions' => $transactions->items(),
    //             'pagination' => [
    //                 'current_page' => $transactions->currentPage(),
    //                 'last_page' => $transactions->lastPage(),
    //                 'per_page' => $transactions->perPage(),
    //                 'total' => $transactions->total(),
    //             ],
    //         ],
    //     ], 200);
    // }

    /**
     * Add or update payment detail
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPaymentDetail(Request $request)
    {
        $user = auth()->user();

        // Check if user is a delivery partner
        if (! $user || $user->role !== 'delivery_partner') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only delivery partners can access this API.',
            ], 403);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'pay_type' => 'required|in:bank,upi',
            'pay_bank_name' => 'required_if:pay_type,bank|nullable|string|max:150',
            'pay_bank_account_number' => 'required_if:pay_type,bank|nullable|string|max:50',
            'pay_bank_ifsc' => 'required_if:pay_type,bank|nullable|string|max:20',
            'pay_upi_id' => 'required_if:pay_type,upi|nullable|string|max:100',
            'account_holder_name' => 'nullable|string|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        DB::beginTransaction();
        try {
            $paymentDetailData = [
                'user_id' => $user->id,
                'pay_type' => $validated['pay_type'],
                'account_holder_name' => $validated['account_holder_name'] ?? $user->first_name.' '.$user->last_name,
            ];

            if ($validated['pay_type'] === 'bank') {
                $paymentDetailData['pay_bank_name'] = $validated['pay_bank_name'];
                $paymentDetailData['pay_bank_account_number'] = $validated['pay_bank_account_number'];
                $paymentDetailData['pay_bank_ifsc'] = $validated['pay_bank_ifsc'];
            } else {
                $paymentDetailData['pay_upi_id'] = $validated['pay_upi_id'];
            }

            $paymentDetail = PaymentDetail::create($paymentDetailData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment detail added successfully.',
                'data' => [
                    'id' => $paymentDetail->id,
                    'pay_type' => $paymentDetail->pay_type,
                    'pay_bank_name' => $paymentDetail->pay_bank_name,
                    'masked_account_number' => $paymentDetail->masked_account_number,
                    'masked_upi_id' => $paymentDetail->masked_upi_id,
                    'account_holder_name' => $paymentDetail->account_holder_name,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to add payment detail for user {$user->id}: ".$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to add payment detail.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete payment detail
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePaymentDetail(Request $request)
    {
        $user = auth()->user();

        // Check if user is a delivery partner
        if (! $user || $user->role !== 'delivery_partner') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only delivery partners can access this API.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'payment_detail_id' => 'required|exists:payment_details,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $paymentDetail = PaymentDetail::where('id', $request->payment_detail_id)
            ->where('user_id', $user->id)
            ->first();

        if (! $paymentDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Payment detail not found or does not belong to you.',
            ], 404);
        }

        // Check if there are pending transactions with this payment detail
        $pendingTransactions = WalletTransaction::where('payment_detail_id', $paymentDetail->id)
            ->where('status', WalletTransaction::STATUS_PENDING)
            ->count();

        if ($pendingTransactions > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete payment detail with pending transactions.',
            ], 400);
        }

        $paymentDetail->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment detail deleted successfully.',
        ], 200);
    }
}
