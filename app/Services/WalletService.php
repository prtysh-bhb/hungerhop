<?php

namespace App\Services;

use App\Models\PaymentDetail;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletService
{
    /**
     * Allowed roles for wallet operations
     */
    const ALLOWED_ROLES = ['delivery_partner', 'location_admin', 'tenant_admin'];

    /**
     * Validate if user has permission for wallet operations
     */
    public function validateUserAccess(User $user): array
    {
        if (!$user || !in_array($user->role, self::ALLOWED_ROLES)) {
            return [
                'success' => false,
                'message' => 'Access denied. Only delivery partners, location admins, and tenant admins can access wallet features.',
                'status_code' => 403,
            ];
        }

        return ['success' => true];
    }

    /**
     * Process wallet transaction (Add or Withdraw)
     * 
     * @param User $user
     * @param array $data - Contains: type, amount, reason, and payment details
     * @return array
     */
    public function processTransaction(User $user, array $data): array
    {
        // Validate user access
        $accessCheck = $this->validateUserAccess($user);
        if (!$accessCheck['success']) {
            return $accessCheck;
        }

        $transactionType = $data['type'];
        $amount = (float) $data['amount'];
        $currentBalance = (float) $user->wallet_balance;

        // For withdrawal, check sufficient balance
        if ($transactionType === 'out' && $currentBalance < $amount) {
            return [
                'success' => false,
                'message' => 'Insufficient wallet balance.',
                'data' => [
                    'current_balance' => $currentBalance,
                    'requested_amount' => $amount,
                ],
                'status_code' => 400,
            ];
        }

        DB::beginTransaction();
        try {
            $balanceBefore = $currentBalance;
            $paymentDetail = null;

            if ($transactionType === 'in') {
                // Create or update payment detail for deposit
                $paymentDetail = $this->createOrUpdatePaymentDetail($user, $data);
                $defaultReason = WalletTransaction::REASON_DEPOSIT;
            } else {
                // For withdrawal, get existing payment detail
                $paymentDetail = PaymentDetail::where('id', $data['payment_detail_id'])
                    ->where('user_id', $user->id)
                    ->first();

                if (!$paymentDetail) {
                    DB::rollBack();
                    return [
                        'success' => false,
                        'message' => 'Payment detail not found or does not belong to you.',
                        'status_code' => 404,
                    ];
                }

                $defaultReason = WalletTransaction::REASON_WITHDRAWAL;
            }

            // Create wallet transaction
            $transaction = WalletTransaction::create([
                'user_id' => $user->id,
                'payment_detail_id' => $paymentDetail->id,
                'type' => $transactionType === 'in' ? WalletTransaction::TYPE_IN : WalletTransaction::TYPE_OUT,
                'reason' => $data['reason'] ?? $defaultReason,
                'amount' => $amount,
                'status' => WalletTransaction::STATUS_COMPLETED,
            ]);

            // Refresh user to get updated balance
            $user->refresh();
            $balanceAfter = (float) $user->wallet_balance;

            DB::commit();

            $actionLabel = $transactionType === 'in' ? 'deposit' : 'withdrawal';
            Log::info("Wallet {$actionLabel} successful for user {$user->id}: Amount ₹{$amount}, New Balance: ₹{$balanceAfter}");

            return [
                'success' => true,
                'message' => $transactionType === 'in'
                    ? 'Amount added to wallet successfully.'
                    : 'Withdrawal processed successfully.',
                'data' => [
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
                ],
                'status_code' => 201,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            $actionLabel = $transactionType === 'in' ? 'deposit' : 'withdrawal';
            Log::error("Wallet {$actionLabel} failed for user {$user->id}: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to process wallet transaction.',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    /**
     * Create or update payment detail
     */
    public function createOrUpdatePaymentDetail(User $user, array $data): PaymentDetail
    {
        $paymentDetailData = [
            'user_id' => $user->id,
            'pay_type' => $data['pay_type'],
            'account_holder_name' => $data['account_holder_name'] ?? $user->first_name . ' ' . $user->last_name,
        ];

        if ($data['pay_type'] === 'bank') {
            $paymentDetailData['pay_bank_name'] = $data['pay_bank_name'];
            $paymentDetailData['pay_bank_account_number'] = $data['pay_bank_account_number'];
            $paymentDetailData['pay_bank_ifsc'] = $data['pay_bank_ifsc'];
            $paymentDetailData['pay_upi_id'] = null;
        } else {
            $paymentDetailData['pay_upi_id'] = $data['pay_upi_id'];
            $paymentDetailData['pay_bank_name'] = null;
            $paymentDetailData['pay_bank_account_number'] = null;
            $paymentDetailData['pay_bank_ifsc'] = null;
        }

        // Check if this payment detail already exists
        $existingQuery = PaymentDetail::where('user_id', $user->id)
            ->where('pay_type', $data['pay_type']);

        if ($data['pay_type'] === 'bank') {
            $existingQuery->where('pay_bank_account_number', $data['pay_bank_account_number']);
        } else {
            $existingQuery->where('pay_upi_id', $data['pay_upi_id']);
        }

        $existingPaymentDetail = $existingQuery->first();

        if (!$existingPaymentDetail) {
            return PaymentDetail::create($paymentDetailData);
        } else {
            $existingPaymentDetail->update($paymentDetailData);
            return $existingPaymentDetail;
        }
    }

    /**
     * Add new payment detail
     */
    public function addPaymentDetail(User $user, array $data): array
    {
        // Validate user access
        $accessCheck = $this->validateUserAccess($user);
        if (!$accessCheck['success']) {
            return $accessCheck;
        }

        DB::beginTransaction();
        try {
            $paymentDetailData = [
                'user_id' => $user->id,
                'pay_type' => $data['pay_type'],
                'account_holder_name' => $data['account_holder_name'] ?? $user->first_name . ' ' . $user->last_name,
            ];

            if ($data['pay_type'] === 'bank') {
                $paymentDetailData['pay_bank_name'] = $data['pay_bank_name'];
                $paymentDetailData['pay_bank_account_number'] = $data['pay_bank_account_number'];
                $paymentDetailData['pay_bank_ifsc'] = $data['pay_bank_ifsc'];
            } else {
                $paymentDetailData['pay_upi_id'] = $data['pay_upi_id'];
            }

            $paymentDetail = PaymentDetail::create($paymentDetailData);

            DB::commit();

            return [
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
                'status_code' => 201,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to add payment detail for user {$user->id}: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to add payment detail.',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    /**
     * Delete payment detail
     */
    public function deletePaymentDetail(User $user, int $paymentDetailId): array
    {
        // Validate user access
        $accessCheck = $this->validateUserAccess($user);
        if (!$accessCheck['success']) {
            return $accessCheck;
        }

        $paymentDetail = PaymentDetail::where('id', $paymentDetailId)
            ->where('user_id', $user->id)
            ->first();

        if (!$paymentDetail) {
            return [
                'success' => false,
                'message' => 'Payment detail not found or does not belong to you.',
                'status_code' => 404,
            ];
        }

        // Check if there are pending transactions with this payment detail
        $pendingTransactions = WalletTransaction::where('payment_detail_id', $paymentDetail->id)
            ->where('status', WalletTransaction::STATUS_PENDING)
            ->count();

        if ($pendingTransactions > 0) {
            return [
                'success' => false,
                'message' => 'Cannot delete payment detail with pending transactions.',
                'status_code' => 400,
            ];
        }

        $paymentDetail->delete();

        return [
            'success' => true,
            'message' => 'Payment detail deleted successfully.',
            'status_code' => 200,
        ];
    }

    /**
     * Get wallet details including balance, transactions, and payment details
     */
    public function getWalletDetails(User $user): array
    {
        // Validate user access
        $accessCheck = $this->validateUserAccess($user);
        if (!$accessCheck['success']) {
            return $accessCheck;
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

        return [
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
            'status_code' => 200,
        ];
    }

    /**
     * Get all payment details for a user
     */
    public function getPaymentDetails(User $user): array
    {
        // Validate user access
        $accessCheck = $this->validateUserAccess($user);
        if (!$accessCheck['success']) {
            return $accessCheck;
        }

        $paymentDetails = PaymentDetail::where('user_id', $user->id)->get();

        return [
            'success' => true,
            'data' => $paymentDetails,
            'status_code' => 200,
        ];
    }

    /**
     * Get transaction history with optional filters
     */
    public function getTransactionHistory(User $user, array $filters = []): array
    {
        // Validate user access
        $accessCheck = $this->validateUserAccess($user);
        if (!$accessCheck['success']) {
            return $accessCheck;
        }

        $query = WalletTransaction::where('user_id', $user->id)
            ->with('paymentDetail');

        // Apply filters
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        $perPage = $filters['per_page'] ?? 20;
        $transactions = $query->orderByDesc('created_at')->paginate($perPage);

        return [
            'success' => true,
            'data' => [
                'transactions' => $transactions,
            ],
            'status_code' => 200,
        ];
    }
}
