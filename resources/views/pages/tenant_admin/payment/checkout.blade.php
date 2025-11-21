 @php
                                $newPlanTotal =
                                    $planLimits['base_fee'] +
                                    $planLimits['max_restaurants'] * $planLimits['per_restaurant_fee'];

                                // Get current plan value (what was already paid)
                                $currentPlanLimits = $tenant->getPlanLimits($tenant->subscription_plan);
                                $alreadyPaid = 0;

                                if ($currentPlanLimits) {
                                    $alreadyPaid =
                                        $currentPlanLimits['base_fee'] +
                                        $currentPlanLimits['max_restaurants'] *
                                            $currentPlanLimits['per_restaurant_fee'];
                                }

                                // The actual amount to pay is in $subscriptionAmount (from pending payment)
                                // $amountToPay = $subscriptionAmount;
                            $subscriptionAmount = $newPlanTotal - $alreadyPaid;
                            $amountToPay = $subscriptionAmount;
                            @endphp

@extends('layouts.admin')

@section('title', 'Payment Checkout')

@section('content')
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Secure Checkout</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.tenant') }}"><i
                                        class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item">Payment</li>
                            <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        @if (isset($isUpgradePayment) && $isUpgradePayment)
            <!-- Upgrade Payment Notice -->
            <div class="alert alert-success alert-dismissible fade show mb-4">
                <div class="d-flex align-items-center">
                    <i class="fa fa-arrow-up fa-2x text-success me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">🎉 Plan Upgrade Payment</h5>
                        <p class="mb-1">
                            You're upgrading to <strong>{{ $planLimits['name'] }}</strong>!
                            @if ($tenant->isWithin3DayPricingWindow())
                                <span class="badge bg-warning text-dark">Special 3-Day Pricing</span>
                            @endif
                        </p>
                        <small class="text-muted">
                            <i class="fa fa-info-circle"></i>
                            You only pay the difference between your current and new plan.
                        </small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-8 col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">
                            @if (isset($isUpgradePayment) && $isUpgradePayment)
                                Plan Upgrade - Payment Due
                            @else
                                Your Plan
                            @endif
                        </h4>
                    </div>
                    <div class="box-body">
                        <div
                            class="d-flex justify-content-between flex-wrap gap-2 align-items-center p-15 bg-primary-light rounded">
                            <div>
                                <div class="fs-18 fw-700">{{ $planLimits['name'] ?? $tenant->subscription_plan }} Plan</div>
                                <div class="text-muted">{{ $tenant->tenant_name }} · {{ $tenant->total_restaurants }}
                                    Restaurant(s) · {{ $planLimits['max_banners'] ?? 1 }} Banner(s)</div>
                                @if (isset($isUpgradePayment) && $isUpgradePayment)
                                    <div class="mt-2">
                                        <span class="badge bg-success">Upgrade Cost Only</span>
                                        @if ($tenant->isWithin3DayPricingWindow())
                                            <span class="badge bg-warning text-dark">3-Day Special Pricing</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="fs-24 fw-800 text-primary">₹{{ number_format((float) $subscriptionAmount) }}</div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Billing Period</h4>
                    </div>
                    <div class="box-body">
                        <p class="mb-0">
                            <span class="fw-700">{{ date('M d, Y', strtotime($billingPeriod['start'])) }}</span>
                            to
                            <span class="fw-700">{{ date('M d, Y', strtotime($billingPeriod['end'])) }}</span>
                        </p>
                        <small class="text-muted">Your subscription activates immediately after successful payment.</small>
                    </div>
                </div>

                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Choose Payment Method</h4>
                    </div>
                    <div class="box-body">
                        <!-- TEMPORARY: Testing Mode Notice -->
                        <div class="alert alert-info" role="alert">
                            <i class="fa fa-cog me-2"></i>
                            <strong>Testing Mode:</strong> Stripe API is temporarily disabled. Payments will be simulated
                            for testing purposes.
                        </div>

                        <form id="payment-form" class="w-100pc">
                            @csrf

                            <!-- Payment Method Tabs -->
                            <ul class="nav nav-tabs mb-3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#credit-card" role="tab">
                                        <i class="fa fa-credit-card me-2"></i>Credit/Debit Card
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#upi" role="tab">
                                        <i class="fa fa-mobile me-2"></i>UPI
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#netbanking" role="tab">
                                        <i class="fa fa-university me-2"></i>Net Banking
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#wallet" role="tab">
                                        <i class="fa fa-wallet me-2"></i>Wallet
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content border rounded p-20 bg-white">
                                <!-- Credit/Debit Card Tab -->
                                <div class="tab-pane fade show active" id="credit-card" role="tabpanel">
                                    <div class="mb-15">
                                        <label class="form-label fw-600 mb-10">Card Details</label>
                                        <div id="card-element" class="form-control"
                                            style="min-height: 60px; padding: 15px;"></div>
                                        <div id="card-errors" class="text-danger mt-10" role="alert"></div>
                                    </div>
                                    <div class="d-flex align-items-center gap-10 mt-15">
                                        <i class="fa fa-shield-alt text-success"></i>
                                    </div>
                                </div>

                                <!-- UPI Tab -->
                                <div class="tab-pane fade" id="upi" role="tabpanel">
                                    <div class="mb-20">
                                        <label class="form-label fw-600 mb-10">UPI ID</label>
                                        <input type="text" class="form-control" name="upi_id"
                                            placeholder="Enter your UPI ID (e.g., user@paytm)">
                                        <div id="upi-errors" class="text-danger mt-10" role="alert"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-md-3 mb-15">
                                            <div class="upi-option text-center p-15 border rounded cursor-pointer">
                                                <i class="fa fa-google text-primary" style="font-size: 24px;"></i>
                                                <div class="mt-5"><small>Google Pay</small></div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-15">
                                            <div class="upi-option text-center p-15 border rounded cursor-pointer">
                                                <i class="fa fa-mobile-alt text-success" style="font-size: 24px;"></i>
                                                <div class="mt-5"><small>PhonePe</small></div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-15">
                                            <div class="upi-option text-center p-15 border rounded cursor-pointer">
                                                <i class="fa fa-credit-card text-info" style="font-size: 24px;"></i>
                                                <div class="mt-5"><small>Paytm</small></div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-15">
                                            <div class="upi-option text-center p-15 border rounded cursor-pointer">
                                                <i class="fa fa-university text-warning" style="font-size: 24px;"></i>
                                                <div class="mt-5"><small>BHIM UPI</small></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Net Banking Tab -->
                                <div class="tab-pane fade" id="netbanking" role="tabpanel">
                                    <div class="mb-20">
                                        <label class="form-label fw-600 mb-10">Select Your Bank</label>
                                        <select class="form-control" name="bank_code">
                                            <option value="">Choose your bank</option>
                                            <option value="SBI">State Bank of India</option>
                                            <option value="HDFC">HDFC Bank</option>
                                            <option value="ICICI">ICICI Bank</option>
                                            <option value="AXIS">Axis Bank</option>
                                            <option value="PNB">Punjab National Bank</option>
                                            <option value="BOB">Bank of Baroda</option>
                                            <option value="CANARA">Canara Bank</option>
                                            <option value="UNION">Union Bank of India</option>
                                        </select>
                                        <div id="netbanking-errors" class="text-danger mt-10" role="alert"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 col-md-3 mb-15">
                                            <div class="bank-option text-center p-15 border rounded cursor-pointer">
                                                <i class="fa fa-university text-primary" style="font-size: 24px;"></i>
                                                <div class="mt-5"><small>SBI</small></div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-15">
                                            <div class="bank-option text-center p-15 border rounded cursor-pointer">
                                                <i class="fa fa-university text-danger" style="font-size: 24px;"></i>
                                                <div class="mt-5"><small>HDFC</small></div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-15">
                                            <div class="bank-option text-center p-15 border rounded cursor-pointer">
                                                <i class="fa fa-university text-info" style="font-size: 24px;"></i>
                                                <div class="mt-5"><small>ICICI</small></div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3 mb-15">
                                            <div class="bank-option text-center p-15 border rounded cursor-pointer">
                                                <i class="fa fa-university text-success" style="font-size: 24px;"></i>
                                                <div class="mt-5"><small>Axis</small></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Wallet Tab -->
                                <div class="tab-pane fade" id="wallet" role="tabpanel">
                                    <div class="row">
                                        <div class="col-6 col-md-4 mb-20">
                                            <div class="wallet-option text-center p-20 border rounded cursor-pointer"
                                                data-wallet="paytm">
                                                <i class="fa fa-wallet text-primary" style="font-size: 32px;"></i>
                                                <div class="fw-600 mt-10">Paytm Wallet</div>
                                                <small class="text-muted">Pay using Paytm balance</small>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4 mb-20">
                                            <div class="wallet-option text-center p-20 border rounded cursor-pointer"
                                                data-wallet="mobikwik">
                                                <i class="fa fa-wallet text-success" style="font-size: 32px;"></i>
                                                <div class="fw-600 mt-10">MobiKwik</div>
                                                <small class="text-muted">Pay using MobiKwik wallet</small>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-4 mb-20">
                                            <div class="wallet-option text-center p-20 border rounded cursor-pointer"
                                                data-wallet="freecharge">
                                                <i class="fa fa-wallet text-warning" style="font-size: 32px;"></i>
                                                <div class="fw-600 mt-10">Freecharge</div>
                                                <small class="text-muted">Pay using Freecharge wallet</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="wallet-errors" class="text-danger mt-10" role="alert"></div>
                                </div>
                            </div>

                            <div id="general-errors" class="text-danger mb-15" role="alert"></div>
                            <button type="submit" id="submit" class="btn btn-primary w-100 py-15">
                                <span id="button-text">Pay ₹{{ number_format((float) $amountToPay) }}
                                    Securely</span>
                                <span id="spinner" class="spinner-border spinner-border-sm align-middle ms-2 d-none"
                                    role="status" aria-hidden="true"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Order Summary</h4>
                    </div>
                    <div class="box-body">
                        @if (isset($isUpgradePayment) && $isUpgradePayment)
                            <!-- Upgrade Payment Summary -->
                            <div class="alert alert-light p-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-arrow-up text-success me-2"></i>
                                    <div>
                                        <small class="text-muted">Plan Upgrade</small>
                                        <div class="fw-600">{{ $planLimits['name'] }}</div>
                                    </div>
                                </div>
                            </div>

                           

                            <!-- New Plan Breakdown -->
                            <div class="mb-3">
                                <small class="text-muted d-block mb-2"><strong>New Plan Details:</strong></small>
                                <div class="d-flex justify-content-between py-2">
                                    <span>Base Fee</span>
                                    <span>₹{{ number_format($planLimits['base_fee']) }}</span>
                                </div>
                                <div class="d-flex justify-content-between py-2">
                                    <span>Restaurants ({{ $planLimits['max_restaurants'] }} ×
                                        ₹{{ number_format($planLimits['per_restaurant_fee']) }})</span>
                                    <span>₹{{ number_format($planLimits['max_restaurants'] * $planLimits['per_restaurant_fee']) }}</span>
                                </div>
                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span>Banners</span>
                                    <span>{{ $planLimits['max_banners'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between py-2 fw-600">
                                    <span>New Plan Total</span>
                                    <span>₹{{ number_format($newPlanTotal) }}</span>
                                </div>
                            </div>

                            @if ($alreadyPaid > 0)
                                <div class="d-flex justify-content-between py-2 border-top pt-3">
                                    <span><small>Current Plan ({{ $tenant->subscription_plan }})</small></span>
                                    <span><small>₹{{ number_format($alreadyPaid) }}</small></span>
                                </div>
                                <div class="d-flex justify-content-between py-2 pb-3 border-bottom">
                                    <span class="text-muted"><small>Calculation: New Plan - Current Plan</small></span>
                                    <span class="text-muted"><small>₹{{ number_format($newPlanTotal) }} -
                                            ₹{{ number_format($alreadyPaid) }}</small></span>
                                </div>
                                @if ($tenant->isWithin3DayPricingWindow())
                                    <div class="alert alert-success p-2 mt-2 mb-2">
                                        <small><i class="fa fa-clock"></i> <strong>3-Day Special Pricing</strong> - Pay
                                            only the difference!</small>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-info p-2 mt-2 mb-2">
                                    <small><i class="fa fa-info-circle"></i> First subscription - Pay full plan
                                        amount</small>
                                </div>
                            @endif
                        @else
                            <!-- Regular Subscription Summary -->
                            <div class="d-flex justify-content-between py-5 border-bottom">
                                <span>Base Fee</span>
                                <span>₹{{ number_format((float) $planLimits['base_fee']) }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-5 border-bottom">
                                <span>Restaurants ({{ $planLimits['max_restaurants'] }} ×
                                    ₹{{ number_format($planLimits['per_restaurant_fee']) }})</span>
                                <span>₹{{ number_format($planLimits['max_restaurants'] * $planLimits['per_restaurant_fee']) }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-5 border-bottom">
                                <span>Banners</span>
                                <span>{{ $planLimits['max_banners'] }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between pt-10 mt-5 border-top"
                            style="background: #fff9e6; padding: 15px; border-radius: 8px; margin-top: 15px !important;">
                            <span class="fs-16 fw-700">
                                @if (isset($isUpgradePayment) && $isUpgradePayment)
                                    💰 Amount to Pay Now
                                @else
                                    💰 Total Amount
                                @endif
                            </span>
                            <span
                                class="fs-18 fw-800 text-primary">₹{{ number_format((float) $subscriptionAmount) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .upi-option,
        .bank-option,
        .wallet-option {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .upi-option:hover,
        .bank-option:hover,
        .wallet-option:hover {
            border-color: #007bff !important;
            background: #f8f9ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.15);
        }

        .upi-option.selected,
        .bank-option.selected,
        .wallet-option.selected {
            border-color: #007bff !important;
            background: #e3f2fd;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
        }

        .nav-tabs .nav-link {
            color: #495057;
            font-weight: 500;
            border: 1px solid transparent;
        }

        .nav-tabs .nav-link.active {
            color: #007bff;
            border-bottom-color: #007bff;
            background-color: #fff;
        }

        .nav-tabs .nav-link:hover {
            border-color: #e9ecef #e9ecef #dee2e6;
            color: #007bff;
        }

        #card-element {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            min-height: 60px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        #card-element:focus-within {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .tab-content {
            border-top: none;
            border-radius: 0 0 8px 8px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            font-weight: 600;
            font-size: 16px;
            padding: 12px 24px;
        }
    </style>
@endpush

@section('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        (function() {
            const stripe = Stripe('{{ config('services.stripe.key') }}');
            let elements;
            let cardElement;
            const form = document.getElementById('payment-form');
            const errorEl = document.getElementById('card-errors');
            const generalErrorEl = document.getElementById('general-errors');
            const upiErrorEl = document.getElementById('upi-errors');
            const netbankingErrorEl = document.getElementById('netbanking-errors');
            const walletErrorEl = document.getElementById('wallet-errors');
            const submitBtn = document.getElementById('submit');
            const spinner = document.getElementById('spinner');
            const buttonText = document.getElementById('button-text');

            let selectedPaymentMethod = 'card';
            let selectedOptions = {};

            // Clear all error messages
            function clearErrors() {
                [errorEl, generalErrorEl, upiErrorEl, netbankingErrorEl, walletErrorEl].forEach(el => {
                    if (el) el.textContent = '';
                });
            }

            // Show error in appropriate element
            function showError(message, method = 'general') {
                clearErrors();
                console.error('Payment Error:', message);

                const errorElement = {
                    'card': errorEl,
                    'creditcard': errorEl,
                    'upi': upiErrorEl,
                    'netbanking': netbankingErrorEl,
                    'wallet': walletErrorEl,
                    'general': generalErrorEl
                } [method] || generalErrorEl;

                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }

            // Reset button state
            function resetButton() {
                submitBtn.disabled = false;
                spinner.classList.add('d-none');
                buttonText.textContent = 'Pay ₹{{ number_format((float) $subscriptionAmount) }} Securely';
            }

            // Initialize Stripe Card Element
            function initStripe() {
                try {
                    elements = stripe.elements();
                    cardElement = elements.create('card', {
                        style: {
                            base: {
                                fontSize: '16px',
                                color: '#495057',
                                '::placeholder': {
                                    color: '#adb5bd'
                                }
                            },
                            invalid: {
                                color: '#dc3545'
                            }
                        }
                    });
                    cardElement.mount('#card-element');
                    cardElement.on('change', function(e) {
                        if (e.error) {
                            showError(e.error.message, 'card');
                        } else {
                            clearErrors();
                        }
                    });
                    console.log('Stripe initialized successfully');
                } catch (error) {
                    console.error('Stripe initialization failed:', error);
                    showError('Payment system initialization failed. Please refresh the page.');
                }
            }

            // Tab switching
            document.querySelectorAll('.nav-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    const target = this.getAttribute('href').substring(1);
                    selectedPaymentMethod = target.replace('-', '');
                    clearErrors();
                    console.log('Payment method changed to:', selectedPaymentMethod);
                });
            });

            // Selection handlers
            document.querySelectorAll('.upi-option, .bank-option, .wallet-option').forEach(option => {
                option.addEventListener('click', function() {
                    this.parentElement.parentElement.querySelectorAll(
                        '.upi-option, .bank-option, .wallet-option').forEach(o => o.classList
                        .remove('selected'));
                    this.classList.add('selected');

                    if (this.classList.contains('wallet-option')) {
                        selectedOptions.wallet = this.dataset.wallet;
                    }
                });
            });

            async function createIntent(paymentData) {
                try {
                    console.log('Creating payment intent with data:', paymentData);

                    const requestBody = {
                        payment_gateway: 'stripe',
                        ...paymentData
                    };
                    console.log('Request body:', requestBody);

                    const res = await fetch('{{ route('admin.tenant.payment.create') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(requestBody)
                    });

                    console.log('Response status:', res.status);
                    console.log('Response headers:', Object.fromEntries(res.headers));

                    const data = await res.json();
                    console.log('Server response:', data);

                    if (!res.ok) {
                        console.error('HTTP Error:', res.status, res.statusText);
                        throw new Error(data.error || `Server error: ${res.status} ${res.statusText}`);
                    }

                    if (!data.success) {
                        console.error('API Error:', data.error);
                        throw new Error(data.error || 'Payment creation failed');
                    }

                    console.log('Payment intent created successfully:', {
                        payment_id: data.payment_id,
                        client_secret: data.client_secret ? 'present' : 'missing',
                        amount: data.amount
                    });

                    return data;
                } catch (error) {
                    console.error('Create intent error:', error);
                    console.error('Error stack:', error.stack);
                    throw error;
                }
            }

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                clearErrors();
                console.log('Payment form submitted');

                submitBtn.disabled = true;
                spinner.classList.remove('d-none');
                buttonText.textContent = 'Processing...';

                try {
                    // Determine current payment method from active tab
                    const activeTab = document.querySelector('.nav-tabs .nav-link.active');
                    if (!activeTab) {
                        throw new Error('Please select a payment method');
                    }

                    const activeTabHref = activeTab.getAttribute('href');
                    selectedPaymentMethod = activeTabHref.replace('#', '').replace('-', '');
                    console.log('Processing payment with method:', selectedPaymentMethod);

                    if (selectedPaymentMethod === 'creditcard' || selectedPaymentMethod === 'card') {
                        console.log('Handling card payment...');
                        await handleCardPayment();
                    } else {
                        console.log('Handling other payment method...');
                        await handleOtherPayment();
                    }
                } catch (err) {
                    console.error('Payment error:', err);
                    showError(err.message || 'Payment failed. Please try again.', selectedPaymentMethod);
                    resetButton();
                }
            });

            async function handleCardPayment() {
                try {
                    console.log('=== Starting Card Payment Process ===');
                    console.log('Card element initialized:', !!cardElement);

                    // Check if card element is properly initialized
                    if (!cardElement) {
                        throw new Error('Card payment system not initialized. Please refresh the page.');
                    }

                    // Create payment intent
                    console.log('Step 1: Creating payment intent...');
                    const {
                        client_secret,
                        payment_id
                    } = await createIntent({
                        payment_method: 'card'
                    });

                    console.log('Step 2: Payment intent created successfully');
                    console.log('Client secret received:', client_secret ? 'Yes' : 'No');
                    console.log('Payment ID:', payment_id);

                    // Confirm payment with Stripe
                    console.log('Step 3: Confirming payment with Stripe...');
                    const confirmPaymentData = {
                        payment_method: {
                            card: cardElement,
                            billing_details: {
                                name: '{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}',
                                email: '{{ auth()->user()->email }}'
                            }
                        }
                    };
                    console.log('Stripe confirmation data:', confirmPaymentData);

                    // TEMPORARY: Comment out actual Stripe confirmation for testing
                    // const { error, paymentIntent } = await stripe.confirmCardPayment(client_secret, confirmPaymentData);

                    // TEMPORARY: Simulate successful payment for testing
                    const error = null;
                    const paymentIntent = {
                        id: 'pi_test_' + Date.now(),
                        status: 'succeeded'
                    };

                    console.log('TEMPORARY: Simulating successful payment for testing');

                    if (error) {
                        console.error('=== Stripe Payment Error ===');
                        console.error('Error code:', error.code);
                        console.error('Error type:', error.type);
                        console.error('Error message:', error.message);
                        console.error('Full error object:', error);

                        // Handle specific card errors
                        let errorMessage = error.message;
                        if (error.code === 'card_declined') {
                            errorMessage = 'Your card was declined. Please try a different card or payment method.';
                        } else if (error.code === 'incorrect_cvc') {
                            errorMessage = 'Your card\'s security code is incorrect.';
                        } else if (error.code === 'expired_card') {
                            errorMessage = 'Your card has expired.';
                        } else if (error.code === 'insufficient_funds') {
                            errorMessage = 'Your card has insufficient funds.';
                        } else if (error.code === 'authentication_required') {
                            errorMessage = 'Your card requires authentication. Please try again.';
                        }
                        throw new Error(errorMessage);
                    }

                    console.log('=== Payment Intent Success ===');
                    console.log('Payment intent status:', paymentIntent.status);
                    console.log('Payment intent ID:', paymentIntent.id);
                    console.log('Payment intent object:', paymentIntent);

                    // Only proceed if payment was successful
                    if (paymentIntent.status === 'succeeded') {
                        console.log('Step 4: Payment succeeded, processing completion...');
                        await handleSuccess(payment_id, paymentIntent.id);
                    } else {
                        console.error('Payment not successful:', paymentIntent.status);
                        throw new Error('Payment was not completed successfully. Status: ' + paymentIntent.status);
                    }
                } catch (error) {
                    console.error('=== Card Payment Handler Error ===');
                    console.error('Error message:', error.message);
                    console.error('Error stack:', error.stack);
                    throw error;
                }
            }

            async function handleOtherPayment() {
                try {
                    console.log('=== Handling Other Payment Method ===');
                    console.log('Selected payment method:', selectedPaymentMethod);

                    let paymentData = {
                        payment_method: selectedPaymentMethod
                    };

                    // Validate required fields based on payment method
                    if (selectedPaymentMethod === 'upi') {
                        const upiId = document.querySelector('input[name="upi_id"]').value.trim();
                        if (!upiId) throw new Error('Please enter your UPI ID');
                        if (!upiId.includes('@')) throw new Error('Please enter a valid UPI ID (e.g., user@paytm)');
                        paymentData.upi_id = upiId;
                    } else if (selectedPaymentMethod === 'netbanking') {
                        const bankCode = document.querySelector('select[name="bank_code"]').value;
                        if (!bankCode) throw new Error('Please select your bank');
                        paymentData.bank_code = bankCode;
                    } else if (selectedPaymentMethod === 'wallet') {
                        if (!selectedOptions.wallet) throw new Error('Please select a wallet');
                        paymentData.wallet_type = selectedOptions.wallet;
                    }

                    console.log('Payment data:', paymentData);

                    // Create PaymentIntent for non-card methods through Stripe
                    const {
                        client_secret,
                        payment_id
                    } = await createIntent(paymentData);

                    console.log('Payment intent created, client secret received');
                    console.log('Using automatic payment methods approach for compatibility');

                    // Use Stripe's automatic payment method handling which detects available methods
                    const {
                        error,
                        paymentIntent
                    } = await stripe.confirmPayment({
                        clientSecret: client_secret,
                        confirmParams: {
                            return_url: window.location.href + '?payment_success=1',
                            payment_method_data: {
                                billing_details: {
                                    name: '{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}',
                                    email: '{{ auth()->user()->email }}'
                                }
                            }
                        },
                        redirect: 'if_required'
                    });

                    if (error) {
                        console.error('=== Stripe Payment Confirmation Error ===');
                        console.error('Error code:', error.code);
                        console.error('Error type:', error.type);
                        console.error('Error message:', error.message);
                        console.error('Full error object:', error);

                        // Handle specific error cases for Indian payment methods
                        let errorMessage = error.message;
                        if (error.code === 'payment_method_not_available') {
                            errorMessage =
                                'This payment method is not available with your current Stripe configuration. Please try card payment or contact support.';
                        } else if (error.code === 'payment_intent_authentication_failure') {
                            errorMessage = 'Payment authentication failed. Please try again.';
                        } else if (error.code === 'invalid_request_error' && error.message.includes(
                                'payment method type')) {
                            errorMessage =
                                'Indian payment methods (UPI/NetBanking) require additional Stripe configuration. Please use card payment for now.';
                        }

                        throw new Error(errorMessage);
                    }

                    console.log('=== Payment Confirmation Success ===');
                    console.log('Payment intent status:', paymentIntent.status);
                    console.log('Payment intent ID:', paymentIntent.id);

                    if (paymentIntent.status === 'succeeded') {
                        console.log('Payment succeeded immediately');
                        await handleSuccess(payment_id, paymentIntent.id);
                    } else if (paymentIntent.status === 'requires_action') {
                        console.log('Payment requires additional action (redirect/authentication)');
                        // Stripe will handle the redirect automatically
                        buttonText.textContent = 'Redirecting for authentication...';
                    } else if (paymentIntent.status === 'processing') {
                        console.log('Payment is processing...');
                        buttonText.textContent = 'Processing payment...';
                        // We'll need to check payment status periodically or via webhook
                        await handleSuccess(payment_id, paymentIntent.id);
                    } else {
                        console.error('Unexpected payment status:', paymentIntent.status);
                        throw new Error('Payment was not completed successfully. Status: ' + paymentIntent.status);
                    }
                } catch (error) {
                    console.error('=== Other Payment Handler Error ===');
                    console.error('Error message:', error.message);
                    console.error('Error stack:', error.stack);
                    throw error;
                }
            }

            async function handleSuccess(paymentId, transactionId) {
                try {
                    console.log('=== Processing Payment Success ===');
                    console.log('Payment ID:', paymentId);
                    console.log('Transaction ID:', transactionId);

                    buttonText.textContent = 'Completing payment...';

                    const requestData = {
                        payment_id: paymentId,
                        gateway_transaction_id: transactionId
                    };
                    console.log('Success request data:', requestData);

                    const res = await fetch('{{ route('admin.tenant.payment.success') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(requestData)
                    });

                    console.log('Success response status:', res.status);
                    console.log('Success response headers:', Object.fromEntries(res.headers));

                    const data = await res.json();
                    console.log('Success handler response:', data);

                    if (!res.ok) {
                        console.error('Success handler HTTP error:', res.status, res.statusText);
                        throw new Error(data.error || `Server error: ${res.status} ${res.statusText}`);
                    }

                    if (!data.success) {
                        console.error('Success handler API error:', data.error);
                        throw new Error(data.error || 'Payment verification failed');
                    }

                    // Show success message before redirect
                    buttonText.textContent = 'Payment successful! Redirecting...';
                    console.log('Payment completed successfully, redirecting to:', data.redirect_url);

                    // Small delay to show success message
                    setTimeout(() => {
                        window.location.href = data.redirect_url ||
                            '{{ route('admin.dashboard.tenant') }}';
                    }, 1000);

                } catch (error) {
                    console.error('=== Success Handler Error ===');
                    console.error('Error message:', error.message);
                    console.error('Error stack:', error.stack);
                    throw error;
                }
            }

            // Initialize on load
            document.addEventListener('DOMContentLoaded', function() {
                console.log('=== Payment System Initialization ===');
                console.log('Stripe public key configured:', '{{ config('services.stripe.key') }}' ? 'Yes' :
                    'No');
                console.log('CSRF token available:', '{{ csrf_token() }}' ? 'Yes' : 'No');
                console.log('Payment amount: ₹{{ $subscriptionAmount }}');
                console.log('Payment amount in paisa:', {{ (int) ($subscriptionAmount * 100) }});
                console.log('Tenant ID: {{ auth()->user()->tenant_id ?? 'N/A' }}');
                console.log('User Name: {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}');
                console.log('User Email: {{ auth()->user()->email }}');

                // Check if amount meets minimum requirement
                const amountInPaisa = {{ (int) ($subscriptionAmount * 100) }};
                if (amountInPaisa < 50) {
                    console.error(
                        '⚠️  AMOUNT TOO SMALL: ₹{{ $subscriptionAmount }} ({{ (int) ($subscriptionAmount * 100) }} paisa) is below Stripe minimum of ₹0.50 (50 paisa)'
                    );
                    showError(
                        'Payment amount (₹{{ $subscriptionAmount }}) is too small. Minimum amount is ₹0.50',
                        'general');
                    submitBtn.disabled = true;
                } else {
                    console.log('✅ Amount validation passed');
                }

                initStripe();
            });
        })();
    </script>
@endsection
