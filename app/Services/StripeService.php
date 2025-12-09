<?php

namespace App\Services;

use App\Models\Payment;
use Stripe\Exception\ApiErrorException;
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
        try {
            $user = auth()->user();

            // Create or get Stripe Customer
            if (! $user->stripe_customer_id) {
                $customer = $this->stripe->customers->create([
                    'email' => $user->email,
                    'name' => $user->first_name.' '.$user->last_name,
                ]);

                $user->update(['stripe_customer_id' => $customer->id]);
            } else {
                $customer = $this->stripe->customers->retrieve($user->stripe_customer_id);
            }

            $intent = $this->stripe->paymentIntents->create([
                'amount' => intval($amount * 100),
                'currency' => $currency,
                'customer' => $customer->id, // <-- this links email to the payment!
                'metadata' => [
                    'order_id' => $order->id,
                    'tenant_id' => $order->tenant_id,
                ],
                'payment_method_types' => ['card'],
            ]);

            $payment = Payment::create([
                'order_id' => $order->id,
                'tenant_id' => $order->tenant_id,
                'payment_method' => 'card',
                'payment_gateway' => 'stripe',
                'gateway_transaction_id' => $intent->id,
                'client_secret' => $intent->client_secret,
                'amount' => $amount,
                'currency' => $currency,
                'status' => 'pending',
                'initiated_at' => now(),
                'gateway_response' => json_encode($intent),
            ]);

            return $payment;

        } catch (\Exception $e) {
            // Log error and throw
            \Log::error('Stripe Intent Creation Failed: '.$e->getMessage());
            throw new \Exception('Payment intent creation failed: '.$e->getMessage());
        }
    }

    /**
     * Confirm a payment by checking Stripe intent status.
     */
    public function confirmPayment($paymentId, $status = null)
    {
        $payment = Payment::findOrFail($paymentId);

        // COMMENTED OUT: Actual Stripe API integration
        // Uncomment this when you want to verify with actual Stripe

        try {
            $intent = $this->stripe->paymentIntents->retrieve($payment->gateway_transaction_id);

            // Update status based on Stripe's response
            $stripeStatus = $intent->status;
            $statusMap = [
                'succeeded' => 'completed',
                'processing' => 'pending',
                'requires_payment_method' => 'cancelled',
                'canceled' => 'cancelled',
            ];

            $status = $status ?? $statusMap[$stripeStatus] ?? 'pending';

        } catch (ApiErrorException $e) {
            \Log::error('Stripe Payment Confirmation Failed: '.$e->getMessage());
            $status = $status ?? 'cancelled';
        }

        if ($status) {
            $payment->update([
                'status' => $status,
                'completed_at' => $status === 'completed' ? now() : null,
            ]);
        }

        return $payment;
    }

    /**
     * Confirm payment with a specific payment method
     * Supports test payment methods for demo/testing
     */
    public function confirmWithPaymentMethod($paymentIntentId, $paymentMethod)
    {
        try {
            // Map simple names to Stripe test payment method IDs
            $testPaymentMethods = [
                'card' => 'pm_card_visa',
                'visa' => 'pm_card_visa',
                'mastercard' => 'pm_card_mastercard',
                'amex' => 'pm_card_amex',
                'card_declined' => 'pm_card_visa_chargeDeclined',
                'card_insufficient' => 'pm_card_visa_chargeDeclinedInsufficientFunds',
            ];

            // If user passed a simple name, convert to test payment method ID
            if (isset($testPaymentMethods[strtolower($paymentMethod)])) {
                $paymentMethod = $testPaymentMethods[strtolower($paymentMethod)];
            }

            $intent = $this->stripe->paymentIntents->confirm(
                $paymentIntentId,
                ['payment_method' => $paymentMethod]
            );

            // Find and update payment record
            $payment = Payment::where('gateway_transaction_id', $paymentIntentId)->first();
            if ($payment) {
                $newStatus = match ($intent->status) {
                    'succeeded' => 'completed',
                    'processing' => 'pending',
                    'requires_action' => 'pending',
                    'requires_confirmation' => 'pending',
                    default => 'pending',
                };

                $payment->update([
                    'status' => $newStatus,
                    'completed_at' => $newStatus === 'completed' ? now() : null,
                    'gateway_response' => json_encode($intent),
                ]);

                // Update order payment status if payment succeeded
                // payment_status enum: pending, completed, failed, refunded
                if ($newStatus === 'completed' && $payment->order) {
                    $payment->order->update(['payment_status' => 'completed']);
                }
            }

            return [
                'success' => $intent->status === 'succeeded',
                'status' => $intent->status,
                'payment_status' => $payment?->status ?? 'unknown',
                'payment_id' => $payment?->id,
                'payment_intent_id' => $intent->id,
                'amount' => $intent->amount / 100,
                'currency' => $intent->currency,
                'message' => $this->getStatusMessage($intent->status),
            ];

        } catch (ApiErrorException $e) {
            \Log::error('Stripe Payment Method Confirmation Failed: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'hint' => 'Use a valid Stripe payment method ID (e.g., pm_card_visa) or simple names: card, visa, mastercard, amex',
            ];
        }
    }

    /**
     * Get human-readable status message
     */
    private function getStatusMessage($status)
    {
        return match ($status) {
            'succeeded' => 'Payment completed successfully!',
            'processing' => 'Payment is being processed.',
            'requires_action' => 'Additional action required (e.g., 3D Secure).',
            'requires_payment_method' => 'Payment method required.',
            'requires_confirmation' => 'Payment needs confirmation.',
            'canceled' => 'Payment was canceled.',
            default => 'Payment status: '.$status,
        };
    }

    /**
     * Retrieve payment intent from Stripe
     */
    public function retrievePaymentIntent($paymentIntentId)
    {
        // COMMENTED OUT: Actual Stripe call
        /*
        try {
            return $this->stripe->paymentIntents->retrieve($paymentIntentId);
        } catch (ApiErrorException $e) {
            \Log::error('Stripe Retrieve Intent Failed: ' . $e->getMessage());
            throw $e;
        }
        */

        // TEMPORARY: Mock response
        return [
            'id' => $paymentIntentId,
            'status' => 'succeeded',
            'amount' => 1000,
            'currency' => 'inr',
            'mock' => true,
        ];
    }
}
