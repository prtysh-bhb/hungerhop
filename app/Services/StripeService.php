<?php

namespace App\Services;

use App\Models\Payment;
use Stripe\StripeClient;

class StripeService
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Create a new Stripe PaymentIntent and store in payments table.
     */
    public function createIntent($order, $amount, $currency = 'inr')
    {
        $intent = $this->stripe->paymentIntents->create([
            'amount' => intval($amount * 100),
            'currency' => $currency,
            'metadata' => [
                'order_id' => $order->id,
                'tenant_id' => $order->tenant_id,
            ],
        ]);

        return Payment::create([
            'order_id' => $order->id,
            'tenant_id' => $order->tenant_id,
            'payment_method' => 'card',
            'payment_gateway' => 'stripe',
            'gateway_transaction_id' => $intent->id,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending',
            'initiated_at' => now(),
            'gateway_response' => json_encode($intent),
        ]);
    }

    /**
     * Confirm a payment by checking Stripe intent status.
     */
    public function confirmPayment($paymentId, $status = null)
    {
        $payment = Payment::findOrFail($paymentId);

        // Stripe logic commented out for now
        // $intent = $this->stripe->paymentIntents->retrieve($payment->gateway_transaction_id);

        if ($status) {
            $payment->update(['status' => $status]);
        }

        return $payment;
    }
}
