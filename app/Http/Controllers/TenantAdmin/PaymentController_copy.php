<?php

namespace App\Http\Controllers\TenantAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController_copy extends Controller
{
    /**
     * Show subscription plans for payment
     */
    public function plans()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (! $tenant) {
            return redirect()->route('admin.dashboard.tenant')->with('error', 'Tenant not found.');
        }

        // Get current plan details
        $planLimits = $tenant->getPlanLimits();
        $subscriptionAmount = $tenant->calculateSubscriptionAmount();

        // Check if there's already a pending payment
        $pendingPayment = $tenant->subscriptionPayments()
            ->where('status', SubscriptionPayment::STATUS_PENDING)
            ->latest()
            ->first();

        return view('pages.tenant_admin.payment.plans', compact('tenant', 'planLimits', 'subscriptionAmount', 'pendingPayment'));
    }

    /**
     * Show checkout page for payment
     */
    public function checkout(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (! $tenant) {
            return redirect()->route('admin.dashboard.tenant')->with('error', 'Tenant not found.');
        }

        // Calculate billing period
        $billingPeriod = SubscriptionPayment::calculateBillingPeriod();
        $subscriptionAmount = $tenant->calculateSubscriptionAmount();
        $planLimits = $tenant->getPlanLimits();

        return view('pages.tenant_admin.payment.checkout', compact('tenant', 'billingPeriod', 'subscriptionAmount', 'planLimits'));
    }

    /**
     * Create a new subscription payment record
     */
    public function createPayment(Request $request)
    {
        \Log::info('Payment creation request started', ['request_data' => $request->all()]);

        $validated = $request->validate([
            'payment_method' => 'nullable|in:'.implode(',', SubscriptionPayment::PAYMENT_METHODS),
            'payment_gateway' => 'required|in:stripe', // Only Stripe allowed
        ]);

        $user = Auth::user();
        $tenant = $user->tenant;

        if (! $tenant) {
            \Log::error('Tenant not found for user', ['user_id' => $user->id]);

            return response()->json(['error' => 'Tenant not found'], 404);
        }

        \Log::info('Processing payment for tenant', ['tenant_id' => $tenant->id, 'tenant_name' => $tenant->tenant_name]);

        DB::beginTransaction();
        try {
            // Check if there's already a pending payment
            $existingPayment = $tenant->subscriptionPayments()
                ->where('status', SubscriptionPayment::STATUS_PENDING)
                ->latest()
                ->first();

            if ($existingPayment) {
                // Update existing payment
                $payment = $existingPayment;
                $payment->update([
                    'payment_method' => $validated['payment_method'] ?? SubscriptionPayment::PAYMENT_METHOD_CARD,
                    'payment_gateway' => 'stripe',
                ]);
            } else {
                // Create new payment record
                $billingPeriod = SubscriptionPayment::calculateBillingPeriod();
                $totalAmount = $tenant->calculateSubscriptionAmount();

                $payment = SubscriptionPayment::create([
                    'tenant_id' => $tenant->id,
                    'subscription_plan' => $tenant->subscription_plan,
                    'restaurant_count' => $tenant->total_restaurants,
                    'base_amount' => $tenant->monthly_base_fee,
                    'per_restaurant_amount' => $tenant->per_restaurant_fee,
                    'total_amount' => $totalAmount,
                    'billing_period_start' => $billingPeriod['start'],
                    'billing_period_end' => $billingPeriod['end'],
                    'due_date' => $billingPeriod['due_date'],
                    'payment_method' => $validated['payment_method'] ?? SubscriptionPayment::PAYMENT_METHOD_CARD,
                    'payment_gateway' => 'stripe',
                    'status' => SubscriptionPayment::STATUS_PENDING,
                ]);
            }

            DB::commit();
            \Log::info('Payment record created/updated', ['payment_id' => $payment->id, 'total_amount' => $payment->total_amount]);

            // Create Stripe payment
            return $this->createStripePayment($payment, $request->all());

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Payment creation failed', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Failed to create payment: '.$e->getMessage()], 500);
        }
    }

    /**
     * Create Stripe payment intent
     */
    private function createStripePayment($payment, $requestData = [])
    {
        \Log::info('=== Creating Stripe Payment Intent ===', [
            'payment_id' => $payment->id,
            'amount' => $payment->total_amount,
            'tenant_id' => $payment->tenant_id,
            'tenant_name' => $payment->tenant->tenant_name ?? 'unknown',
        ]);

        try {
            // Check Stripe configuration
            $stripeSecret = config('services.stripe.secret');
            $stripePublic = config('services.stripe.key');

            \Log::info('Stripe configuration check', [
                'secret_configured' => ! empty($stripeSecret),
                'public_configured' => ! empty($stripePublic),
                'secret_length' => $stripeSecret ? strlen($stripeSecret) : 0,
            ]);

            if (! $stripeSecret) {
                \Log::error('Stripe secret key not configured');
                throw new \Exception('Payment system not properly configured - missing secret key');
            }

            \Stripe\Stripe::setApiKey($stripeSecret);
            \Log::info('Stripe API key set successfully');

            // Validate minimum amount for INR (₹0.50 = 50 paisa)
            $amountInPaisa = (int) ($payment->total_amount * 100);
            \Log::info('Payment amount validation', [
                'amount_inr' => $payment->total_amount,
                'amount_paisa' => $amountInPaisa,
                'minimum_required' => 50,
            ]);

            if ($amountInPaisa < 50) {
                \Log::error('Payment amount too small for Stripe', [
                    'amount_paisa' => $amountInPaisa,
                    'minimum_required' => 50,
                    'amount_inr' => $payment->total_amount,
                ]);
                throw new \Exception('Payment amount (₹'.$payment->total_amount.') is too small. Minimum amount is ₹0.50');
            }

            // Use automatic payment methods for compatibility with standard Stripe setup
            \Log::info('Using automatic payment methods for broad compatibility');

            $paymentIntentData = [
                'amount' => $amountInPaisa,
                'currency' => 'inr',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'payment_id' => $payment->id,
                    'tenant_id' => $payment->tenant_id,
                    'plan' => $payment->subscription_plan,
                    'requested_method' => $requestData['payment_method'] ?? 'card',
                ],
                'description' => 'Subscription payment for '.($payment->tenant->tenant_name ?? 'tenant'),
            ];

            \Log::info('Payment intent will use automatic payment methods', [
                'requested_method' => $requestData['payment_method'] ?? 'card',
            ]);

            \Log::info('Creating Stripe PaymentIntent with data:', $paymentIntentData);

            $paymentIntent = \Stripe\PaymentIntent::create($paymentIntentData);

            \Log::info('Stripe PaymentIntent created successfully', [
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount,
                'status' => $paymentIntent->status,
                'currency' => $paymentIntent->currency,
                'client_secret_length' => strlen($paymentIntent->client_secret),
            ]);

            // Update payment with Stripe payment intent ID
            $payment->update([
                'gateway_payment_id' => $paymentIntent->id,
                'gateway_payment_status' => $paymentIntent->status,
            ]);

            \Log::info('Payment record updated with Stripe data');

            $response = [
                'success' => true,
                'payment_id' => $payment->id,
                'client_secret' => $paymentIntent->client_secret,
                'amount' => $payment->total_amount,
                'currency' => 'INR',
            ];

            \Log::info('Returning successful response', [
                'payment_id' => $response['payment_id'],
                'amount' => $response['amount'],
                'client_secret_provided' => ! empty($response['client_secret']),
            ]);

            return response()->json($response);

        } catch (\Stripe\Exception\CardException $e) {
            \Log::error('=== Stripe Card Exception ===', [
                'error_code' => $e->getError()->code,
                'error_type' => $e->getError()->type,
                'error_message' => $e->getError()->message,
                'error_param' => $e->getError()->param,
                'full_error' => $e->getError(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getError()->message,
            ], 400);
        } catch (\Stripe\Exception\RateLimitException $e) {
            \Log::error('=== Stripe Rate Limit Exception ===', [
                'error_message' => $e->getMessage(),
                'http_status' => $e->getHttpStatus(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Too many requests made to the API too quickly',
            ], 429);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            \Log::error('=== Stripe Invalid Request Exception ===', [
                'error_message' => $e->getMessage(),
                'http_status' => $e->getHttpStatus(),
                'stripe_param' => $e->getStripeParam(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Invalid parameters were supplied to Stripe\'s API: '.$e->getMessage(),
            ], 400);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            \Log::error('=== Stripe Authentication Exception ===', [
                'error_message' => $e->getMessage(),
                'http_status' => $e->getHttpStatus(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Authentication with Stripe\'s API failed',
            ], 401);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            \Log::error('=== Stripe API Connection Exception ===', [
                'error_message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Network communication with Stripe failed',
            ], 503);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('=== Stripe API Error Exception ===', [
                'error_message' => $e->getMessage(),
                'http_status' => $e->getHttpStatus(),
                'stripe_code' => $e->getStripeCode(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Stripe API error: '.$e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            \Log::error('=== General Exception in Payment Creation ===', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Payment creation failed: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle payment success callback
     */
    public function paymentSuccess(Request $request)
    {
        \Log::info('Payment success callback received', ['request_data' => $request->all()]);

        $validated = $request->validate([
            'payment_id' => 'required|exists:subscription_payments,id',
            'gateway_transaction_id' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $payment = SubscriptionPayment::find($validated['payment_id']);

            \Log::info('Processing payment success', [
                'payment_id' => $payment->id,
                'current_status' => $payment->status,
                'transaction_id' => $validated['gateway_transaction_id'],
            ]);

            if (! $payment || $payment->status !== SubscriptionPayment::STATUS_PENDING) {
                \Log::warning('Invalid payment or payment already processed', [
                    'payment_id' => $validated['payment_id'],
                    'status' => $payment ? $payment->status : 'not_found',
                ]);

                return response()->json(['error' => 'Invalid payment or payment already processed'], 400);
            }

            // Mark payment as completed and update tenant
            $payment->markAsCompleted($validated['gateway_transaction_id']);

            \Log::info('Payment marked as completed', ['payment_id' => $payment->id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment completed successfully! Your subscription is now active.',
                'redirect_url' => route('admin.dashboard.tenant'),
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Payment success processing failed', [
                'error' => $e->getMessage(),
                'payment_id' => $validated['payment_id'] ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Failed to process payment: '.$e->getMessage()], 500);
        }
    }

    /**
     * Handle payment failure callback
     */
    public function paymentFailure(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:subscription_payments,id',
            'failure_reason' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $payment = SubscriptionPayment::find($validated['payment_id']);

            if (! $payment) {
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // Mark payment as failed
            $payment->markAsFailed($validated['failure_reason'] ?? 'Payment failed');

            DB::commit();

            return response()->json([
                'success' => false,
                'message' => 'Payment failed. Please try again.',
                'redirect_url' => route('admin.tenant.payment.plans'),
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => 'Failed to process payment failure: '.$e->getMessage()], 500);
        }
    }

    /**
     * Show payment history
     */
    public function history()
    {
        $user = Auth::user();
        $tenant = $user->tenant;

        if (! $tenant) {
            return redirect()->route('admin.dashboard.tenant')->with('error', 'Tenant not found.');
        }

        $payments = $tenant->subscriptionPayments()
            ->latest()
            ->paginate(10);

        return view('pages.tenant_admin.payment.history', compact('payments', 'tenant'));
    }

    /**
     * Download payment invoice
     */
    public function downloadInvoice(SubscriptionPayment $payment)
    {
        $user = Auth::user();

        // Check if user can access this payment
        if ($payment->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized');
        }

        // Here you would generate and return the invoice PDF
        // For now, we'll just redirect back with a message
        return back()->with('info', 'Invoice download feature will be implemented soon.');
    }

    /**
     * Handle Stripe webhook
     */
    public function handleStripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        // Handle the event
        if ($event['type'] === 'payment_intent.succeeded') {
            $paymentIntent = $event['data']['object'];

            // Find the payment by payment_intent_id
            $payment = SubscriptionPayment::where('gateway_payment_id', $paymentIntent['id'])->first();

            if ($payment) {
                $payment->update([
                    'status' => SubscriptionPayment::STATUS_COMPLETED,
                    'paid_at' => now(),
                    'gateway_transaction_id' => $paymentIntent['id'],
                ]);

                // Update tenant status to active
                $tenant = $payment->tenant;
                if ($tenant && $tenant->status === 'pending_approval') {
                    $tenant->update(['status' => 'active']);
                }
            }
        }

        return response('Success', 200);
    }
}
