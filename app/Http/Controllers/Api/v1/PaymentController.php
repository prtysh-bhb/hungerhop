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

    public function paymentMethods(Request $request)
    {
        $methods = [
            [
                'key' => 'cod',
                'name' => 'Cash on Delivery',
                'description' => 'Pay with cash when your order arrives.',
            ],
            [
                'key' => 'card',
                'name' => 'Credit/Debit Card',
                'description' => 'Pay securely using your card.',
            ],
            [
                'key' => 'wallet',
                'name' => 'Wallet',
                'description' => 'Pay using your HungerHop wallet balance.',
            ],
            [
                'key' => 'upi',
                'name' => 'UPI',
                'description' => 'Pay using UPI apps like PhonePe, Google Pay.',
            ],
        ];

        return response()->json([
            'success' => true,
            'payment_methods' => $methods,
        ]);
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

        // ✅ Check if order already has a successful payment (with valid transaction ID)
        $existingPayment = Payment::where('order_id', $order->id)
            ->where('status', 'completed')
            ->whereNotNull('gateway_transaction_id')
            ->where('gateway_transaction_id', '!=', '')
            ->first();

        if ($existingPayment) {
            return response()->json([
                'success' => false,
                'message' => 'This order already has a completed payment.',
                'payment_id' => $existingPayment->id,
                'transaction_id' => $existingPayment->gateway_transaction_id,
            ], 422);
        }

        // Clean up any incomplete payment records (no transaction ID)
        Payment::where('order_id', $order->id)
            ->where(function ($query) {
                $query->whereNull('gateway_transaction_id')
                    ->orWhere('gateway_transaction_id', '');
            })
            ->delete();

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
            'payment_intent_id' => $payment->gateway_transaction_id,
            'client_secret' => $payment->gateway_response ? json_decode($payment->gateway_response)->client_secret : null, // THIS IS CRUCIAL FOR FRONTEND
            'amount' => $amount,
            'currency' => $request->currency ?? 'inr',
            'order_id' => $order->id,
            'payment_id' => $payment->id,
            'user_id' => $user->id,
            'user_name' => $user->first_name.' '.$user->last_name,
            'payment_getway' => $payment->payment_gateway,
            'payment_getway_response' => $payment->gateway_response ? json_decode($payment->gateway_response) : null,
        ]);
    }

    /**
     * Confirm a payment with Stripe
     */
    public function confirm(Request $request)
    {
        // dd($request->all());
        $validate = $request->validate([
            'payment_id' => 'required|integer|exists:payments,id',
            'status' => 'required|string|in:completed,pending,cancelled',
        ]);

        $payment = $this->stripe->confirmPayment($validate['payment_id'], $validate['status']);
        $order = Order::find($payment->order_id);
        // payment_status enum: pending, completed, failed, refunded
        $order->payment_status = $payment->status === 'completed' ? 'completed' : $payment->status;
        $order->save();

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

    /**
     * Confirm payment with payment method (from Stripe Elements/Checkout)
     *
     * For testing, you can use these payment_method values:
     * - "card" or "visa" -> pm_card_visa (success)
     * - "mastercard" -> pm_card_mastercard (success)
     * - "amex" -> pm_card_amex (success)
     * - "card_declined" -> Will be declined
     * - "card_insufficient" -> Insufficient funds
     * - Or pass a real Stripe payment method ID (pm_xxx)
     */
    public function confirmWithMethod(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        $result = $this->stripe->confirmWithPaymentMethod(
            $request->payment_intent_id,
            $request->payment_method
        );

        // If payment succeeded, update order status
        // payment_status enum: pending, completed, failed, refunded
        if ($result['success'] ?? false) {
            $payment = Payment::where('gateway_transaction_id', $request->payment_intent_id)->first();
            if ($payment && $payment->order) {
                $payment->order->update(['payment_status' => 'completed']);
            }
        }

        return response()->json($result);
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

        // Decode gateway_response for each payment
        $payments = $payments->map(function ($payment) {
            $paymentArr = $payment->toArray();
            $paymentArr['gateway_response'] = $payment->gateway_response ? json_decode($payment->gateway_response) : null;

            return $paymentArr;
        });

        return response()->json([
            'success' => true,
            'payments' => $payments,
            'user_id' => $user->id,
            'customer_profile_id' => $customerProfile->id,
        ]);
    }
}
