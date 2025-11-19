<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\Payment;
use App\Services\StripeService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $stripe;

    public function __construct(StripeService $stripe)
    {
        $this->stripe = $stripe;
    }

    /**
     * Create a PaymentIntent for an order
     */
    public function createIntent(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'currency' => 'nullable|string|in:inr,usd',
        ]);

        $order = Order::findOrFail($request->order_id);
        $user = auth()->user();

        // ✅ Ownership check fixed
        $customerProfile = CustomerProfile::find($order->customer_id);
        if (! $customerProfile || $customerProfile->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: This order does not belong to the authenticated user.',
            ], 403);
        }

        // ✅ Correct column for amount
        $amount = $order->total_amount ?? null;

        if (! $amount || $amount < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Order amount is invalid.',
            ], 422);
        }

        $payment = $this->stripe->createIntent(
            $order,
            $amount,
            $request->currency ?? 'inr'
        );

        return response()->json([
            'success' => true,
            // 'payment' => $payment,
            'amount' => $amount,
            'user_id' => $user->id,
            'user_name' => $user->first_name.' '.$user->last_name,
        ]);
    }

    /**
     * Confirm a payment with Stripe
     */
    public function confirm(Request $request)
    {
        $validate = $request->validate([
            'payment_id' => 'required|integer|exists:payments,id',
            'status' => 'required|string|in:completed,pending,cancelled',
        ]);

        $payment = $this->stripe->confirmPayment($validate['payment_id'], $validate['status']);

        $message = '';
        switch ($validate['status']) {
            case 'completed':
                $message = 'Your payment was successful.';
                break;
            case 'pending':
                $message = 'Your payment is pending. Please wait for confirmation.';
                break;
            case 'cancelled':
                $message = 'Your payment was cancelled.';
                break;
            default:
                $message = '';
        }

        return response()->json([
            'success' => true,
            'payment_id' => $payment->id,
            'status' => $payment->status,
            'message' => $message,
        ]);
    }

    public function history(Request $request)
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.',
            ], 401);
        }

        // Get the customer profile for the current user
        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();

        if (! $customerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
                'payments' => [],
            ]);
        }

        // Get all orders for this customer
        $orderIds = Order::where('customer_id', $customerProfile->id)->pluck('id');

        // Get all payments for these orders
        $payments = Payment::whereIn('order_id', $orderIds)
            ->with(['order' => function ($query) {
                $query->select('id', 'order_number', 'total_amount', 'status', 'created_at');
            }])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'payments' => $payments,
            'user_id' => $user->id,
            'customer_profile_id' => $customerProfile->id,
        ]);
    }
}
