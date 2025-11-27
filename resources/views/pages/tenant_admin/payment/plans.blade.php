@extends('layouts.admin')

@section('title', 'Subscription Payment')

@section('styles')
    <style>
        .payment-plan-card {
            border: 2px solid #e3e6f0;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
            height: 100%;
            position: relative;
            background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
        }

        .payment-plan-card.current-plan {
            border-color: #4e73df;
            background: linear-gradient(135deg, #4e73df 0%, #6c5ce7 100%);
            color: white;
            transform: scale(1.05);
        }

        .payment-plan-card.current-plan .plan-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .payment-plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .plan-badge {
            position: absolute;
            top: -10px;
            right: 20px;
            background: #4e73df;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .payment-features {
            text-align: left;
            margin: 1.5rem 0;
        }

        .payment-features li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
        }

        .payment-features li i {
            color: #28a745;
            margin-right: 10px;
            width: 20px;
        }

        .payment-card {
            background: white;
            border-radius: 0.5rem;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .payment-summary {
            background: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            padding: 1.5rem;
        }

        .amount-display {
            font-size: 3rem;
            font-weight: bold;
            color: #4e73df;
        }

        .payment-status-pending {
            background: linear-gradient(45deg, #ffeaa7, #fdcb6e);
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .payment-breakdown {
            background: white;
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            padding: 1rem;
        }

        .breakdown-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }

        .breakdown-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .plan-card {
            position: relative;
            /* IMPORTANT */
        }

        .plan-card input[type="radio"] {
            position: absolute;
            /* IMPORTANT */
            top: 15px;
            right: 15px;
            width: 20px;
            height: 20px;
            cursor: pointer;
            z-index: 3;
            opacity: 0;
            /* optional (hide real radio but keep click) */
        }


        .plan-card:hover,
        .plan-card.selected {
            border-color: #4e73df;
            background: #f8f9fc;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }


        .plan-card .radio-indicator {
            position: absolute;
            /* IMPORTANT */
            top: 15px;
            right: 15px;
            z-index: 2;
        }


        .plan-card.selected .radio-indicator {
            background: #007bff;
            border-color: #007bff;
        }

        .plan-card.selected .radio-indicator::after {
            content: '✓';
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .plan-card:hover .radio-indicator {
            transform: scale(1.1);
            border-color: #0056b3;
        }

        .plan-features {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            text-align: left;
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .payment-card {
            position: relative;
            z-index: 1;
        }

        #plan-change-form .plan-card {
            position: relative;
            z-index: 9999 !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-full">
        <!-- Content Header -->
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="me-auto">
                    <h4 class="page-title">Subscription Payment</h4>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.tenant') }}"><i
                                        class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item active">Payment</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

                    @if (session('banner_suggestions') || session('restaurant_suggestions'))
                        <hr>
                        <h6><i class="fa fa-lightbulb"></i> Suggestions:</h6>
                        <ul class="mb-0">
                            @if (session('banner_suggestions'))
                                @foreach (session('banner_suggestions') as $suggestion)
                                    <li>{{ $suggestion }}</li>
                                @endforeach
                            @endif
                            @if (session('restaurant_suggestions'))
                                @foreach (session('restaurant_suggestions') as $suggestion)
                                    <li>{{ $suggestion }}</li>
                                @endforeach
                            @endif
                        </ul>
                    @endif
                </div>
            @endif

            <!-- Account Status Alert for Pending Approval -->
            @if (auth()->user()->status === 'pending_approval')
                <div class="alert alert-info alert-dismissible fade show" style="margin-bottom: 2rem;">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-info-circle fa-2x text-info me-3"></i>
                        <div>
                            <h5 class="alert-heading mb-1">Account Activation Required</h5>
                            <p class="mb-2">
                                Welcome to {{ config('app.name') }}! Your account has been created successfully.
                                To activate your account and start using all platform features, please complete your first
                                subscription payment below.
                            </p>
                            <small class="mb-2">
                                <strong>Login Credentials:</strong> Email: {{ auth()->user()->email }} | Password:
                                {{ auth()->user()->phone }}
                                (You can change your password after activation)
                            </small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            <!-- 3-Day Special Pricing Window Alert -->
            @php $pricingWindow = $tenant->getPricingWindowInfo(); @endphp
            @if ($pricingWindow && $pricingWindow['is_within_window'])
                <div class="alert alert-success alert-dismissible fade show" style="margin-bottom: 2rem;">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-clock fa-2x text-success me-3"></i>
                        <div>
                            <h5 class="alert-heading mb-1">🎉 Special Pricing Window Active!</h5>
                            <p class="mb-2">
                                You paid for your current plan {{ $pricingWindow['days_since_payment'] }} day(s) ago.
                                <strong>You have {{ $pricingWindow['remaining_days'] }} day(s) left</strong> to change your
                                plan and pay only the difference between plans!
                            </p>
                            <small class="text-muted">
                                <i class="fa fa-info-circle"></i>
                                Special pricing expires on
                                {{ $pricingWindow['window_expires_at']->format('M d, Y \a\t h:i A') }}
                            </small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            <!-- Current Plan Display -->
            <div class="payment-card mb-4">
                <div class="row">
                    <!-- Current Plan Display -->
                    <div class="col-lg-8">
                        <h4 class="mb-4">
                            <i class="fa fa-credit-card text-primary me-2"></i>
                            Your Current Subscription Plan
                        </h4>

                        @if ($pendingPayment)
                            <div class="alert alert-warning alert-dismissible fade show mb-3">
                                <i class="fa fa-clock me-2"></i>
                                <strong>Payment Pending</strong> — Please complete your payment to activate your plan.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div
                            style="background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%); border: 2px solid #4e73df; border-radius: 0.5rem; padding: 2rem;">
                            <h5 class="fw-bold text-primary mb-2">{{ $planLimits['name'] ?? $tenant->subscription_plan }}
                            </h5>
                            <div class="h3 text-primary mb-1">₹{{ number_format((float) $subscriptionAmount) }}<small
                                    class="text-muted fs-6">/month</small></div>

                            @if ($planLimits)
                                <div class="mt-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fa fa-store text-primary me-3" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <small class="text-muted d-block">Restaurants Included</small>
                                            <strong>{{ $planLimits['max_restaurants'] }} Restaurants</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fa fa-image text-info me-3" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <small class="text-muted d-block">Banner Limit</small>
                                            <strong>{{ $planLimits['max_banners'] }} Banners</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fa fa-headset text-success me-3" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <small class="text-muted d-block">Support Level</small>
                                            <strong>Priority Support</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-chart-line text-secondary me-3" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <small class="text-muted d-block">Advanced Tools</small>
                                            <strong>Analytics & Reports</strong>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Summary Sidebar -->
                    <div class="col-lg-4">
                        <!-- Current Usage Card -->
                        <div
                            style="background: white; border: 1px solid #e3e6f0; border-radius: 0.5rem; padding: 1.5rem; margin-bottom: 1.5rem;">
                            <h6 class="mb-3 fw-bold">
                                <i class="fa fa-info-circle text-info me-2"></i>Current Usage
                            </h6>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Active Restaurants</small>
                                <strong class="text-primary">{{ $tenant->total_restaurants }}</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <small class="text-muted">Active Banners</small>
                                <strong class="text-primary">{{ $tenant->banner_limit }}</strong>
                            </div>

                            @if ($planLimits)
                                <hr class="my-2">
                                <small class="text-muted d-block mb-2"><strong>Plan Limits</strong></small>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">Max Restaurants</small>
                                    <span class="badge bg-light text-dark">{{ $planLimits['max_restaurants'] }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Max Banners</small>
                                    <span class="badge bg-light text-dark">{{ $planLimits['max_banners'] }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Payment Breakdown Card -->
                        <div
                            style="background: #f8f9fc; border: 1px solid #e3e6f0; border-radius: 0.5rem; padding: 1.5rem;">
                            <h6 class="mb-3 fw-bold">Payment Breakdown</h6>

                            <div class="breakdown-row">
                                <span class="text-muted">Base Fee</span>
                                <span>₹{{ number_format((float) $tenant->monthly_base_fee) }}</span>
                            </div>
                            <div class="breakdown-row">
                                <span class="text-muted">Restaurants ({{ $tenant->total_restaurants }})</span>
                                <span>₹{{ number_format($tenant->total_restaurants * (float) $tenant->per_restaurant_fee) }}</span>
                            </div>
                            <div class="breakdown-row">
                                <span><strong>Total Monthly</strong></span>
                                <span
                                    class="text-primary"><strong>₹{{ number_format((float) $subscriptionAmount) }}</strong></span>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('admin.tenant.payment.checkout') }}"
                                    class="btn {{ $pendingPayment ? 'btn-warning' : 'btn-success' }} ">
                                    <i class="fa fa-credit-card me-2"></i>
                                    {{ $pendingPayment ? 'Complete Payment' : 'Pay Now' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Plan Selection Section -->
            <div class="plan-selection">
                <div class="plan-header">
                    <h3>
                        <i class="fa fa-tags text-success me-2"></i>
                        Choose Your Plan
                    </h3>
                    <button type="button" class="btn-outline" id="change-plan-btn">
                        <i class="fa fa-edit me-1"></i>Change Plan
                    </button>
                </div>

                <div class="plan-container">
                    <form id="plan-change-form" action="{{ route('admin.tenant.payment.update-plan') }}" method="POST">
                        @csrf
                        <div class="plan-grid">
                            <!-- Lite Plan -->
                            <div class="plan-column">
                                <label
                                    class="plan-option {{ $tenant->subscription_plan === 'LITE' || empty($tenant->subscription_plan) ? 'selected' : '' }}"
                                    for="plan_lite">
                                    <input type="radio" id="plan_lite" name="subscription_plan" value="LITE"
                                        {{ $tenant->subscription_plan === 'LITE' || empty($tenant->subscription_plan) ? 'checked' : '' }}>
                                    <div class="plan-card">
                                        <div class="plan-icon bg-info">
                                            <i class="fa fa-rocket"></i>
                                        </div>
                                        <h4>Lite Plan</h4>
                                        <div class="plan-price">₹3,700<small>/month</small></div>
                                        <div class="plan-desc">Base Fee: ₹1,200 + (5 × ₹500)</div>
                                        <small class="plan-includes">Includes 5 restaurants</small>

                                        @if ($tenant->subscription_plan !== 'LITE')
                                            @php
                                                $upgradeCost = $tenant->calculateUpgradeCost('LITE');
                                                $isWithin3Days = $tenant->isWithin3DayPricingWindow();
                                                $currentSubscription = $tenant->getCurrentSubscriptionPayment();
                                                $alreadyPaid = $currentSubscription
                                                    ? $currentSubscription->total_amount
                                                    : 0;
                                                $litePlanLimits = $tenant->getPlanLimits('LITE');
                                                $newPlanTotal =
                                                    $litePlanLimits['base_fee'] +
                                                    5 * $litePlanLimits['per_restaurant_fee'];
                                                if ($alreadyPaid == 0) {
                                                    $upgradeCost = $newPlanTotal;
                                                }
                                            @endphp
                                            @if ($upgradeCost > 0)
                                                <div class="upgrade-details">
                                                    <div class="breakdown-header">Payment Breakdown:</div>
                                                    @if ($alreadyPaid > 0)
                                                        <div class="breakdown-item">Current Plan:
                                                            ₹{{ number_format($alreadyPaid, 2) }}</div>
                                                    @endif
                                                    <div class="breakdown-item">New Plan:
                                                        ₹{{ number_format($newPlanTotal, 2) }}/month</div>
                                                    <div class="breakdown-note">
                                                        (₹{{ number_format($litePlanLimits['base_fee']) }} base + 5 ×
                                                        ₹{{ number_format($litePlanLimits['per_restaurant_fee']) }})</div>
                                                    <div class="breakdown-divider"></div>
                                                    <div class="pay-now">Pay Now: ₹{{ number_format($upgradeCost, 2) }}
                                                    </div>
                                                    @if ($isWithin3Days && $alreadyPaid > 0)
                                                        <div class="special-pricing">
                                                            <i class="fa fa-star"></i> Special 3-day pricing active!
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif($tenant->canUpgradeToPlan('LITE'))
                                                <div class="downgrade-note">
                                                    Downgrade (credit applied)
                                                </div>
                                            @endif
                                        @else
                                            <div class="current-plan">
                                                <i class="fa fa-check-circle"></i> Current Plan
                                            </div>
                                        @endif

                                        <p class="plan-summary">Basic features for small businesses</p>
                                        <div class="plan-features">
                                            <div class="feature">✓ Up to 5 Restaurants</div>
                                            <div class="feature">✓ 1 Banner</div>
                                            <div class="feature">✓ Basic Support</div>
                                            <div class="feature">✓ ₹500 per restaurant</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Plus Plan -->
                            <div class="plan-column">
                                <label class="plan-option {{ $tenant->subscription_plan === 'PLUS' ? 'selected' : '' }}"
                                    for="plan_plus">
                                    <input type="radio" id="plan_plus" name="subscription_plan" value="PLUS"
                                        {{ $tenant->subscription_plan === 'PLUS' ? 'checked' : '' }}>
                                    <div class="plan-card">
                                        <div class="plan-icon bg-warning">
                                            <i class="fa fa-star"></i>
                                        </div>
                                        <h4>Plus Plan</h4>
                                        <div class="plan-price">₹22,000<small>/month</small></div>
                                        <div class="plan-desc">Base Fee: ₹2,000 + (20 × ₹1,000)</div>
                                        <small class="plan-includes">Includes 20 restaurants</small>

                                        @if ($tenant->subscription_plan !== 'PLUS')
                                            @php
                                                $upgradeCost = $tenant->calculateUpgradeCost('PLUS');
                                                $isWithin3Days = $tenant->isWithin3DayPricingWindow();
                                                $currentSubscription = $tenant->getCurrentSubscriptionPayment();
                                                $alreadyPaid = $currentSubscription
                                                    ? $currentSubscription->total_amount
                                                    : 0;
                                                $plusPlanLimits = $tenant->getPlanLimits('PLUS');
                                                $newPlanTotal =
                                                    $plusPlanLimits['base_fee'] +
                                                    20 * $plusPlanLimits['per_restaurant_fee'];
                                                if ($alreadyPaid == 0) {
                                                    $upgradeCost = $newPlanTotal;
                                                }
                                            @endphp
                                            @if ($upgradeCost > 0)
                                                <div class="upgrade-details">
                                                    <div class="breakdown-header">Payment Breakdown:</div>
                                                    @if ($alreadyPaid > 0)
                                                        <div class="breakdown-item">Current Plan:
                                                            ₹{{ number_format($alreadyPaid, 2) }}</div>
                                                    @endif
                                                    <div class="breakdown-item">New Plan:
                                                        ₹{{ number_format($newPlanTotal, 2) }}/month</div>
                                                    <div class="breakdown-note">
                                                        (₹{{ number_format($plusPlanLimits['base_fee']) }} base + 20 ×
                                                        ₹{{ number_format($plusPlanLimits['per_restaurant_fee']) }})</div>
                                                    <div class="breakdown-divider"></div>
                                                    <div class="pay-now">Pay Now: ₹{{ number_format($upgradeCost, 2) }}
                                                    </div>
                                                    @if ($isWithin3Days && $alreadyPaid > 0)
                                                        <div class="special-pricing">
                                                            <i class="fa fa-star"></i> Special 3-day pricing active!
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="downgrade-note">
                                                    Downgrade (credit applied)
                                                </div>
                                            @endif
                                        @else
                                            <div class="current-plan">
                                                <i class="fa fa-check-circle"></i> Current Plan
                                            </div>
                                        @endif

                                        <p class="plan-summary">Advanced features for growing businesses</p>
                                        <div class="plan-features">
                                            <div class="feature">✓ <strong>20 Restaurants Included</strong></div>
                                            <div class="feature">✓ 3 Banners</div>
                                            <div class="feature">✓ Priority Support</div>
                                            <div class="feature">✓ Advanced analytics</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Pro Max Plan -->
                            <div class="plan-column">
                                <label
                                    class="plan-option {{ $tenant->subscription_plan === 'PRO_MAX' ? 'selected' : '' }}"
                                    for="plan_pro">
                                    <input type="radio" id="plan_pro" name="subscription_plan" value="PRO_MAX"
                                        {{ $tenant->subscription_plan === 'PRO_MAX' ? 'checked' : '' }}>
                                    <div class="plan-card">
                                        <div class="plan-icon bg-success">
                                            <i class="fa-sharp fa-solid fa-crown"></i>
                                        </div>
                                        <h4>Pro Max Plan</h4>
                                        <div class="plan-price">₹47,500<small>/month</small></div>
                                        <div class="plan-desc">Base Fee: ₹2,500 + (30 × ₹1,500)</div>
                                        <small class="plan-includes">Includes 30 restaurants</small>

                                        @if ($tenant->subscription_plan !== 'PRO_MAX')
                                            @php
                                                $upgradeCost = $tenant->calculateUpgradeCost('PRO_MAX');
                                                $isWithin3Days = $tenant->isWithin3DayPricingWindow();
                                                $currentSubscription = $tenant->getCurrentSubscriptionPayment();
                                                $alreadyPaid = $currentSubscription
                                                    ? $currentSubscription->total_amount
                                                    : 0;
                                                $proMaxPlanLimits = $tenant->getPlanLimits('PRO_MAX');
                                                $newPlanTotal =
                                                    $proMaxPlanLimits['base_fee'] +
                                                    30 * $proMaxPlanLimits['per_restaurant_fee'];
                                                if ($alreadyPaid == 0) {
                                                    $upgradeCost = $newPlanTotal;
                                                }
                                            @endphp
                                            @if ($upgradeCost > 0)
                                                <div class="upgrade-details">
                                                    <div class="breakdown-header">Payment Breakdown:</div>
                                                    @if ($alreadyPaid > 0)
                                                        <div class="breakdown-item">Current Plan:
                                                            ₹{{ number_format($alreadyPaid, 2) }}</div>
                                                    @endif
                                                    <div class="breakdown-item">New Plan:
                                                        ₹{{ number_format($newPlanTotal, 2) }}/month</div>
                                                    <div class="breakdown-note">
                                                        (₹{{ number_format($proMaxPlanLimits['base_fee']) }} base + 30 ×
                                                        ₹{{ number_format($proMaxPlanLimits['per_restaurant_fee']) }})
                                                    </div>
                                                    <div class="breakdown-divider"></div>
                                                    <div class="pay-now">Pay Now: ₹{{ number_format($upgradeCost, 2) }}
                                                    </div>
                                                    @if ($isWithin3Days && $alreadyPaid > 0)
                                                        <div class="special-pricing">
                                                            <i class="fa fa-star"></i> Special 3-day pricing active!
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="downgrade-note">
                                                    Downgrade (credit applied)
                                                </div>
                                            @endif
                                        @else
                                            <div class="current-plan">
                                                <i class="fa fa-check-circle"></i> Current Plan
                                            </div>
                                        @endif

                                        <p class="plan-summary">Premium features for enterprise</p>
                                        <div class="plan-features">
                                            <div class="feature">✓ <strong>30 Restaurants Included</strong></div>
                                            <div class="feature">✓ 10 Banners</div>
                                            <div class="feature">✓ Premium Support</div>
                                            <div class="feature">✓ All enterprise features</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="plan-actions">
                            <button type="submit" class="btn-primary">
                                <i class="fa fa-check me-2"></i>Update Plan
                            </button>
                            <button type="button" class="btn-secondary" id="cancel-change-btn">
                                <i class="fa fa-times me-2"></i>Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <style>
                .plan-selection {
                    background: #fff;
                    border-radius: 8px;
                    padding: 20px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }

                .plan-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 20px;
                    padding-bottom: 15px;
                    border-bottom: 1px solid #eee;
                }

                .plan-header h3 {
                    margin: 0;
                    color: #333;
                    font-size: 1.25rem;
                }

                .btn-outline {
                    background: transparent;
                    border: 1px solid #007bff;
                    color: #007bff;
                    padding: 6px 12px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 0.875rem;
                }

                .btn-outline:hover {
                    background: #007bff;
                    color: white;
                }

                .plan-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                    gap: 20px;
                    margin-bottom: 20px;
                }

                .plan-option {
                    display: block;
                    cursor: pointer;
                }

                .plan-option input {
                    display: none;
                }

                .plan-card {
                    border: 2px solid #e9ecef;
                    border-radius: 8px;
                    padding: 20px;
                    text-align: center;
                    transition: all 0.3s ease;
                    background: #fff;
                    height: 100%;
                    position: relative;
                }

                /* Selected plan styling */
                .plan-option input:checked+.plan-card,
                .plan-option.selected .plan-card {
                    border-color: #007bff;
                    background: #f8f9fa;
                    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
                }

                /* Hover effect */
                .plan-option:hover .plan-card {
                    border-color: #007bff;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                }

                .plan-icon {
                    width: 60px;
                    height: 60px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin: 0 auto 15px;
                    color: white;
                }

                .bg-info {
                    background: #17a2b8;
                }

                .bg-warning {
                    background: #ffc107;
                }

                .bg-success {
                    background: #28a745;
                }

                .plan-card h4 {
                    margin: 0 0 10px 0;
                    color: #333;
                    font-size: 1.125rem;
                }

                .plan-price {
                    font-size: 1.5rem;
                    font-weight: bold;
                    color: #007bff;
                    margin-bottom: 5px;
                }

                .plan-price small {
                    font-size: 0.875rem;
                    color: #6c757d;
                    font-weight: normal;
                }

                .plan-desc,
                .plan-includes {
                    color: #6c757d;
                    font-size: 0.875rem;
                    margin-bottom: 5px;
                }

                .upgrade-details {
                    background: #f0f8ff;
                    border-radius: 5px;
                    padding: 10px;
                    margin: 10px 0;
                    border-left: 3px solid #4CAF50;
                    text-align: left;
                }

                .breakdown-header {
                    font-weight: bold;
                    font-size: 0.875rem;
                    margin-bottom: 5px;
                }

                .breakdown-item,
                .breakdown-note {
                    font-size: 0.8rem;
                    color: #6c757d;
                    margin-bottom: 2px;
                }

                .breakdown-divider {
                    border-top: 1px dashed #ddd;
                    margin: 8px 0;
                }

                .pay-now {
                    font-weight: bold;
                    color: #28a745;
                    font-size: 0.875rem;
                }

                .special-pricing {
                    color: #ffc107;
                    font-size: 0.75rem;
                    margin-top: 5px;
                }

                .downgrade-note,
                .current-plan {
                    color: #ffc107;
                    font-size: 0.875rem;
                    margin: 10px 0;
                }

                .current-plan {
                    color: #007bff;
                }

                .plan-summary {
                    color: #6c757d;
                    font-size: 0.875rem;
                    margin: 10px 0;
                }

                .plan-features {
                    text-align: left;
                    margin-top: 15px;
                }

                .feature {
                    font-size: 0.875rem;
                    color: #495057;
                    margin-bottom: 5px;
                }

                .plan-actions {
                    text-align: center;
                    margin-top: 20px;
                }

                .btn-primary {
                    background: #28a745;
                    color: white;
                    border: none;
                    padding: 10px 25px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 1rem;
                    margin-right: 10px;
                }

                .btn-secondary {
                    background: #6c757d;
                    color: white;
                    border: none;
                    padding: 10px 25px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 1rem;
                }

                .btn-primary:hover {
                    background: #218838;
                }

                .btn-secondary:hover {
                    background: #5a6268;
                }

                @media (max-width: 768px) {
                    .plan-grid {
                        grid-template-columns: 1fr;
                    }

                    .plan-header {
                        flex-direction: column;
                        gap: 10px;
                        text-align: center;
                    }

                    .plan-actions {
                        display: flex;
                        flex-direction: column;
                        gap: 10px;
                    }

                    .btn-primary,
                    .btn-secondary {
                        margin-right: 0;
                        width: 100%;
                    }
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Handle plan selection
                    const planOptions = document.querySelectorAll('.plan-option input[type="radio"]');

                    planOptions.forEach(option => {
                        option.addEventListener('change', function() {
                            // Remove selected class from all options
                            document.querySelectorAll('.plan-option').forEach(opt => {
                                opt.classList.remove('selected');
                            });

                            // Add selected class to the checked option's parent label
                            if (this.checked) {
                                this.closest('.plan-option').classList.add('selected');
                            }
                        });
                    });

                    // Initialize selected state on page load
                    planOptions.forEach(option => {
                        if (option.checked) {
                            option.closest('.plan-option').classList.add('selected');
                        }
                    });

                    // Optional: Add click handler for the entire card for better UX
                    document.querySelectorAll('.plan-card').forEach(card => {
                        card.addEventListener('click', function(e) {
                            // Don't trigger if clicking on a button or link inside the card
                            if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A' || e.target
                                .closest('button') || e.target.closest('a')) {
                                return;
                            }

                            const radioInput = this.closest('.plan-option').querySelector(
                                'input[type="radio"]');
                            if (radioInput && !radioInput.disabled) {
                                radioInput.checked = true;
                                radioInput.dispatchEvent(new Event('change'));
                            }
                        });
                    });
                });
            </script>

            <!-- Benefits Section -->
            <div class="payment-card" style="padding: 15px;">
                <h4 class="mb-4">
                    <i class="fa fa-gift text-success me-2"></i>
                    What You Get With Your Subscription
                </h4>

                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="icon-circle bg-primary text-white mx-auto mb-3"
                                style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fa fa-cutlery fa-2x"></i>
                            </div>
                            <h6>Restaurant Management</h6>
                            <p class="text-muted small">Manage multiple restaurants from one dashboard</p>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="icon-circle bg-success text-white mx-auto mb-3"
                                style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fa fa-bar-chart-o fa-2x"></i>
                            </div>
                            <h6>Analytics & Reports</h6>
                            <p class="text-muted small">Detailed insights and performance reports</p>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="icon-circle bg-warning text-white mx-auto mb-3"
                                style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fa fa-mobile fa-2x"></i>
                            </div>
                            <h6>Mobile App</h6>
                            <p class="text-muted small">Manage your business on the go</p>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="icon-circle bg-info text-white mx-auto mb-3"
                                style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fa fa-phone fa-2x"></i>
                            </div>
                            <h6>24/7 Support</h6>
                            <p class="text-muted small">Priority customer support</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History Link -->
            <div class="text-center">
                <a href="{{ route('admin.tenant.payment.history') }}" class="btn btn-primary mt-4">
                    <i class="fa fa-history me-2"></i>View Payment History
                </a>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initially hide the form and show comparison
            $('#plan-change-form').hide();

            // Plan selection functionality - toggle behavior
            $('#change-plan-btn').on('click', function() {
                if ($('#plan-change-form').is(':visible')) {
                    // Hide form
                    $('#plan-change-form').slideUp();
                    $(this).html('<i class="fa fa-edit me-1"></i>Change Plan').removeClass(
                        'btn-outline-secondary').addClass('btn-outline-primary');
                    $('#plan-comparison').slideDown();
                } else {
                    // Show form
                    $('#plan-change-form').slideDown();
                    $(this).html('<i class="fa fa-eye-slash me-1"></i>Hide Selection').removeClass(
                        'btn-outline-primary').addClass('btn-outline-secondary');
                    $('#plan-comparison').slideUp();
                }
            });

            $('#cancel-change-btn').on('click', function() {
                $('#plan-change-form').slideUp();
                $('#change-plan-btn').html('<i class="fa fa-edit me-1"></i>Change Plan').removeClass(
                    'btn-outline-secondary').addClass('btn-outline-primary');
                $('#plan-comparison').slideDown();

                // Reset to current plan
                const currentPlan = '{{ $tenant->subscription_plan }}';
                $('input[name="subscription_plan"]').prop('checked', false);
                $('input[value="' + currentPlan + '"]').prop('checked', true);
                $('.plan-card').removeClass('selected');
                $('input[value="' + currentPlan + '"]').closest('.plan-card').addClass('selected');
            });

            // Handle plan card selection
            $('input[name="subscription_plan"]').on('change', function() {
                console.log('Plan selection changed to:', $(this).val()); // Debug log
                $('.plan-card').removeClass('selected');
                $(this).closest('.plan-card').addClass('selected');

                const plan = $(this).val();
                updatePlanPreview(plan);
                updateRestaurantCountLimits(plan);
            });

            // Handle restaurant count changes
            $('#restaurant_count').on('input', function() {
                const selectedPlan = $('input[name="subscription_plan"]:checked').val();
                if (selectedPlan) {
                    updateCostPreview(selectedPlan, parseInt($(this).val()) || 1);
                }
            });

            // Function to update restaurant count limits based on selected plan
            function updateRestaurantCountLimits(plan) {
                const planDetails = {
                    'LITE': {
                        maxRestaurants: 5
                    },
                    'PLUS': {
                        maxRestaurants: 20
                    },
                    'PRO_MAX': {
                        maxRestaurants: 30
                    }
                };

                if (planDetails[plan]) {
                    const maxRestaurants = planDetails[plan].maxRestaurants;
                    $('#restaurant_count').attr('max', maxRestaurants);

                    // Adjust current value if it exceeds the new limit
                    const currentValue = parseInt($('#restaurant_count').val());
                    if (currentValue > maxRestaurants) {
                        $('#restaurant_count').val(maxRestaurants);
                        updateCostPreview(plan, maxRestaurants);
                    }
                }
            }

            // Function to update cost preview
            function updateCostPreview(plan, restaurantCount) {
                const planDetails = {
                    'LITE': {
                        baseAmount: 1200,
                        perRestaurant: 500
                    },
                    'PLUS': {
                        baseAmount: 2000,
                        perRestaurant: 1000
                    },
                    'PRO_MAX': {
                        baseAmount: 2500,
                        perRestaurant: 1500
                    }
                };

                if (planDetails[plan]) {
                    const details = planDetails[plan];
                    const restaurantCost = restaurantCount * details.perRestaurant;
                    const totalAmount = details.baseAmount + restaurantCost;

                    // Update the preview elements
                    $('#preview-base').text('₹' + details.baseAmount.toLocaleString());
                    $('#preview-count').text(restaurantCount);
                    $('#preview-restaurant-cost').text('₹' + restaurantCost.toLocaleString());
                    $('#preview-total').text('₹' + totalAmount.toLocaleString());
                }
            }

            // Handle plan card click
            $('.plan-card').on('click', function() {
                console.log('Plan card clicked'); // Debug log
                const radio = $(this).find('input[type="radio"]');
                console.log('Radio found:', radio.length); // Debug log
                radio.prop('checked', true).trigger('change');
            });

            // Function to update plan preview
            function updatePlanPreview(plan) {
                const planDetails = {
                    'LITE': {
                        name: 'Lite Plan',
                        baseAmount: 1200,
                        perRestaurant: 500,
                        maxRestaurants: 5,
                        maxBanners: 1
                    },
                    'PLUS': {
                        name: 'Plus Plan',
                        baseAmount: 2000,
                        perRestaurant: 1000,
                        maxRestaurants: 20,
                        maxBanners: 3
                    },
                    'PRO_MAX': {
                        name: 'Pro Max Plan',
                        baseAmount: 2500,
                        perRestaurant: 1500,
                        maxRestaurants: 30,
                        maxBanners: 10
                    }
                };

                if (planDetails[plan]) {
                    const details = planDetails[plan];
                    const currentRestaurants = {{ $tenant->total_restaurants }};
                    const totalAmount = details.baseAmount + (currentRestaurants * details.perRestaurant);

                    // Show preview or update some UI element
                    console.log('Selected plan:', details.name, 'Total amount:', totalAmount);
                }
            }

            // Function to update order summary
            function updateOrderSummary(plan) {
                const planDetails = {
                    'LITE': {
                        name: 'Lite Plan',
                        baseAmount: 1200,
                        perRestaurant: 500,
                        maxRestaurants: 5,
                        maxBanners: 1
                    },
                    'PLUS': {
                        name: 'Plus Plan',
                        baseAmount: 2000,
                        perRestaurant: 1000,
                        maxRestaurants: 20,
                        maxBanners: 3
                    },
                    'PRO_MAX': {
                        name: 'Pro Max Plan',
                        baseAmount: 2500,
                        perRestaurant: 1500,
                        maxRestaurants: 30,
                        maxBanners: 10
                    }
                };

                if (planDetails[plan]) {
                    const details = planDetails[plan];
                    const currentRestaurants = {{ $tenant->total_restaurants }};

                    // Calculate costs
                    const restaurantCost = details.maxRestaurants * details.perRestaurant;
                    const totalAmount = details.baseAmount + restaurantCost;

                    // Calculate amount to pay (considering already paid amount)
                    const alreadyPaid = {{ $alreadyPaid ?? 0 }};
                    let amountToPay = totalAmount;

                    // If there's a previous payment, calculate upgrade cost
                    if (alreadyPaid > 0) {
                        // Use the same calculation as the PHP backend
                        const currentPlan = '{{ $tenant->subscription_plan }}';
                        const currentPlanDetails = planDetails[currentPlan];
                        if (currentPlanDetails) {
                            const currentTotal = currentPlanDetails.baseAmount + (currentPlanDetails
                                .maxRestaurants * currentPlanDetails.perRestaurant);
                            amountToPay = Math.max(0, totalAmount - currentTotal);
                        }
                    }

                    // Update all order summary fields
                    $('#summary-plan-name').text(details.name);
                    $('#summary-base-fee').text('₹' + details.baseAmount.toLocaleString());
                    $('#summary-restaurants').text(details.maxRestaurants);
                    $('#summary-per-restaurant').text('₹' + details.perRestaurant.toLocaleString());
                    $('#summary-restaurant-cost').text('₹' + restaurantCost.toLocaleString());
                    $('#summary-banners').text(details.maxBanners);
                    $('#summary-total').text('₹' + totalAmount.toLocaleString());
                    $('#summary-pay-now').text('₹' + amountToPay.toLocaleString());

                    // Show the order summary
                    $('#order-summary').slideDown();
                }
            }

            // Update order summary when plan selection changes
            $('input[name="subscription_plan"]').on('change', function() {
                const selectedPlan = $(this).val();
                updateOrderSummary(selectedPlan);
            });

            // Hide order summary when cancel button is clicked
            $('#cancel-change-btn').on('click', function() {
                $('#order-summary').slideUp();
            });

            // Form validation before submission
            $('#plan-change-form').on('submit', function(e) {
                const selectedPlan = $('input[name="subscription_plan"]:checked').val();
                const currentPlan = '{{ $tenant->subscription_plan }}';

                if (selectedPlan === currentPlan) {
                    e.preventDefault();
                    alert('You have selected the same plan. Please choose a different plan to continue.');
                    return false;
                }

                // Get payment breakdown from the selected plan card
                const selectedCard = $('input[name="subscription_plan"]:checked').closest('.plan-card');
                const paymentBreakdown = selectedCard.find('.upgrade-cost-details');

                // Plan details
                const planDetails = {
                    'LITE': {
                        name: 'Lite Plan',
                        maxBanners: 1,
                        baseAmount: 1200,
                        perRestaurant: 500,
                        restaurants: 5
                    },
                    'PLUS': {
                        name: 'Plus Plan',
                        maxBanners: 3,
                        baseAmount: 2000,
                        perRestaurant: 1000,
                        restaurants: 20
                    },
                    'PRO_MAX': {
                        name: 'Pro Max Plan',
                        maxBanners: 10,
                        baseAmount: 2500,
                        perRestaurant: 1500,
                        restaurants: 30
                    }
                };

                const currentBanners = {{ $tenant->banner_limit }};
                const newPlanBanners = planDetails[selectedPlan]?.maxBanners || 0;

                // Build confirmation message with payment details
                let confirmMessage = `You are about to change to ${planDetails[selectedPlan].name}\n\n`;

                // Add payment breakdown if available
                if (paymentBreakdown.length > 0) {
                    const payNowText = paymentBreakdown.find('strong:contains("Pay Now")').parent().text();
                    const alreadyPaidText = paymentBreakdown.find('.text-decoration-line-through').text();

                    if (payNowText) {
                        confirmMessage += `💰 Payment Details:\n`;
                        if (alreadyPaidText) {
                            confirmMessage += `   Already Paid: ${alreadyPaidText}\n`;
                        }
                        confirmMessage += `   ${payNowText}\n\n`;
                    }
                }

                // Show informational alert if banner limit will be reduced
                if (currentBanners > newPlanBanners) {
                    confirmMessage +=
                        `⚠️ Note: Your banner limit will be reduced from ${currentBanners} to ${newPlanBanners} banners.\nPlease adjust your active banners accordingly after the plan change.\n\n`;
                }

                confirmMessage += `Do you want to proceed?`;

                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                    return false;
                }
            });

            // Set initial selected plan
            $('input[name="subscription_plan"]:checked').closest('.plan-card').addClass('selected');
        });
    </script>
@endsection
