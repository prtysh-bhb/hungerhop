<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPartner;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeliveryPartnerWalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

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

        // Use WalletService to process transaction
        $result = $this->walletService->processTransaction($user, $validated);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null,
        ], $result['status_code']);
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

        // Use WalletService to get wallet details
        $result = $this->walletService->getWalletDetails($user);

        return response()->json([
            'success' => $result['success'],
            'data' => $result['data'] ?? null,
            'message' => $result['message'] ?? null,
        ], $result['status_code']);
    }

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

        // Use WalletService to add payment detail
        $result = $this->walletService->addPaymentDetail($user, $validator->validated());

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null,
        ], $result['status_code']);
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

        // Use WalletService to delete payment detail
        $result = $this->walletService->deletePaymentDetail($user, (int) $request->payment_detail_id);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
        ], $result['status_code']);
    }
}
