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
            border: 2px solid #e3e6f0;
            border-radius: 0.35rem;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            height: 100%;
            background: white;
        }

        .plan-card:hover,
        .plan-card.selected {
            border-color: #4e73df;
            background: #f8f9fc;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .plan-card input[type="radio"] {
            top: 15px;
            right: 15px;
            width: 20px;
            height: 20px;
            cursor: pointer;
            z-index: 2;
        }

        .plan-card .radio-indicator {
            top: 15px;
            right: 15px;
            width: 20px;
            height: 20px;
            border: 3px solid #007bff;
            border-radius: 50%;
            background: white;
            cursor: pointer;
            z-index: 1;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
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
            <div class="payment-card">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="mb-3">
                            <i class="fa fa-credit-card text-primary me-2"></i>
                            Your Subscription Plan
                        </h3>

                        @if ($pendingPayment)
                            <div class="payment-status-pending">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-clock fa-2x me-3"></i>
                                    <div>
                                        <h5 class="mb-1">Payment Pending</h5>
                                        <p class="mb-0">You have a pending payment. Complete it to activate your
                                            subscription.</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="payment-plan-card current-plan">
                            <div class="plan-badge">Current Plan</div>

                            <div class="mb-3">
                                <h2>{{ $planLimits['name'] ?? $tenant->subscription_plan }}</h2>
                                <div class="amount-display">₹{{ number_format((float) $subscriptionAmount) }}</div>
                                <p>per month</p>
                            </div>

                            @if ($planLimits)
                                <ul class="payment-features">
                                    <li><i class="fa fa-store"></i> Up to {{ $planLimits['max_restaurants'] }} Restaurants
                                    </li>
                                    <li><i class="fa fa-image"></i> {{ $planLimits['max_banners'] }} Banner(s)</li>
                                    <li><i class="fa fa-support"></i> Priority Support</li>
                                    <li><i class="fa fa-chart-line"></i> Analytics & Reports</li>
                                    <li><i class="fa fa-mobile"></i> Mobile App Access</li>
                                </ul>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Current Usage Summary -->
                        <div class="payment-card mb-3"
                            style="background: #f8f9fc; border: 1px solid #e3e6f0; padding: 1.5rem;">
                            <h6 class="mb-3"><i class="fa fa-info-circle text-info"></i> Current Usage</h6>
                            <div class="usage-stats">
                                <div class="usage-item d-flex justify-content-between mb-2">
                                    <span><i class="fa fa-store text-primary"></i> Restaurants:</span>
                                    <span class="badge bg-primary">{{ $tenant->total_restaurants }}</span>
                                </div>
                                <div class="usage-item d-flex justify-content-between mb-2">
                                    <span><i class="fa fa-image text-warning"></i> Banners:</span>
                                    <span class="badge bg-warning">{{ $tenant->banner_limit }}</span>
                                </div>
                                @if ($planLimits)
                                    <div class="mt-2 pt-2" style="border-top: 1px solid #dee2e6; font-size: 0.875rem;">
                                        <div class="text-muted">Plan Limits:</div>
                                        <div class="d-flex justify-content-between">
                                            <span>Max Restaurants:</span>
                                            <span>{{ $planLimits['max_restaurants'] }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Max Banners:</span>
                                            <span>{{ $planLimits['max_banners'] }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="payment-summary">
                            <h5 class="mb-3">Payment Summary</h5>

                            <div class="payment-breakdown">
                                <div class="breakdown-row">
                                    <span>Base Fee:</span>
                                    <span>₹{{ number_format((float) $tenant->monthly_base_fee) }}</span>
                                </div>
                                <div class="breakdown-row">
                                    <span>Restaurants ({{ $tenant->total_restaurants }}):</span>
                                    <span>₹{{ number_format($tenant->total_restaurants * (float) $tenant->per_restaurant_fee) }}</span>
                                </div>
                                <div class="breakdown-row">
                                    <span>Total Monthly:</span>
                                    <span>₹{{ number_format((float) $subscriptionAmount) }}</span>
                                </div>
                            </div>

                            <div class="mt-3">
                                @if ($pendingPayment)
                                    <a href="{{ route('admin.tenant.payment.checkout') }}"
                                        class="btn btn-warning btn-lg w-150">
                                        <i class="fa fa-credit-card me-2"></i>Complete Payment
                                    </a>
                                @else
                                    <a href="{{ route('admin.tenant.payment.checkout') }}"
                                        class="btn btn-primary btn-lg w-150">
                                        <i class="fa fa-credit-card me-2"></i>Pay Now
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Plan Selection Section -->
            <div class="payment-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">
                        <i class="fa fa-tags text-success me-2"></i>
                        Choose Your Plan
                    </h4>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="change-plan-btn">
                        <i class="fa fa-edit me-1"></i>Change Plan
                    </button>
                </div>

                <div class="d-flex justify-content-center">
                    <div style="max-width: 1000px; width: 100%;">
                        <form id="plan-change-form" action="{{ route('admin.tenant.payment.update-plan') }}" method="POST">
                            @csrf
                            <div class="row justify-content-center p-3">
                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                            <label
                                class="plan-card {{ $tenant->subscription_plan === 'LITE' || empty($tenant->subscription_plan) ? 'selected' : '' }}"
                                for="plan_lite">
                                <input type="radio" id="plan_lite" name="subscription_plan" value="LITE"
                                    {{ $tenant->subscription_plan === 'LITE' || empty($tenant->subscription_plan) ? 'checked' : '' }}>
                                <div class="radio-indicator"></div>
                                <div class="text-center">
                                    <div class="icon-circle bg-info text-white mx-auto mb-2"
                                        style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                        <i class="fa fa-rocket fa-2x"></i>
                                    </div>
                                    <h6>Lite Plan</h6>
                                    <div class="h4 text-primary">₹3,700<small class="text-muted fs-6">/month</small></div>
                                    <small class="text-muted">Includes 5 restaurants</small>
                                    @if ($tenant->subscription_plan !== 'LITE')
                                        @php
                                            $upgradeCost = $tenant->calculateUpgradeCost('LITE');
                                            $isWithin3Days = $tenant->isWithin3DayPricingWindow();
                                        @endphp
                                        @if ($upgradeCost > 0)
                                            <div class="upgrade-cost text-success">
                                                <small>Upgrade cost: ₹{{ number_format($upgradeCost, 2) }}</small>
                                                @if ($isWithin3Days)
                                                    <div class="text-warning" style="font-size: 0.75rem;">
                                                        <i class="fa fa-star"></i> Special 3-day pricing!
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif($tenant->canUpgradeToPlan('LITE'))
                                            <div class="downgrade-note text-warning">
                                                <small>Downgrade (credit applied)</small>
                                            </div>
                                        @endif
                                    @else
                                        <div class="current-plan-badge text-primary">
                                            <small><i class="fa fa-check-circle"></i> Current Plan</small>
                                        </div>
                                    @endif
                                    <p class="text-muted small mb-2">Basic features for small businesses</p>
                                    <div class="plan-features">
                                        <small>✓ Up to 5 Restaurants</small><br>
                                        <small>✓ 1 Banner</small><br>
                                        <small>✓ Basic Support</small><br>
                                        <small>✓ ₹500 per restaurant</small>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-md-4">
                            <label class="plan-card {{ $tenant->subscription_plan === 'PLUS' ? 'selected' : '' }}"
                                for="plan_plus">
                                <input type="radio" id="plan_plus" name="subscription_plan" value="PLUS"
                                    {{ $tenant->subscription_plan === 'PLUS' ? 'checked' : '' }}>
                                <div class="radio-indicator"></div>
                                <div class="text-center">
                                    <div class="icon-circle bg-warning text-white mx-auto mb-2"
                                        style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                        <i class="fa fa-star fa-2x"></i>
                                    </div>
                                    <h6>Plus Plan</h6>
                                    <div class="h4 text-primary">₹2,000</div>
                                    @if ($tenant->subscription_plan !== 'PLUS')
                                        @php
                                            $upgradeCost = $tenant->calculateUpgradeCost('PLUS');
                                            $isWithin3Days = $tenant->isWithin3DayPricingWindow();
                                        @endphp
                                        @if ($upgradeCost > 0)
                                            <div class="upgrade-cost text-success">
                                                <small>Upgrade cost: ₹{{ number_format($upgradeCost, 2) }}</small>
                                                @if ($isWithin3Days)
                                                    <div class="text-warning" style="font-size: 0.75rem;">
                                                        <i class="fa fa-star"></i> Special 3-day pricing!
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="downgrade-note text-warning">
                                                <small>Downgrade (credit applied)</small>
                                            </div>
                                        @endif
                                    @else
                                        <div class="current-plan-badge text-primary">
                                            <small><i class="fa fa-check-circle"></i> Current Plan</small>
                                        </div>
                                    @endif
                                    <p class="text-muted small mb-2">Advanced features for growing businesses</p>
                                    <div class="plan-features">
                                        <small>✓ <strong>20 Restaurants Included</strong></small><br>
                                        <small>✓ 3 Banners</small><br>
                                        <small>✓ Priority Support</small><br>
                                        <small>✓ Advanced analytics</small>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                            <label class="plan-card {{ $tenant->subscription_plan === 'PRO_MAX' ? 'selected' : '' }}"
                                for="plan_pro">
                                <input type="radio" id="plan_pro" name="subscription_plan" value="PRO_MAX"
                                    {{ $tenant->subscription_plan === 'PRO_MAX' ? 'checked' : '' }}>
                                <div class="radio-indicator"></div>
                                <div class="text-center">
                                    <div class="icon-circle bg-success text-white mx-auto mb-2"
                                        style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                        <i class="fa-sharp fa-solid fa-crown"></i>
                                    </div>
                                    <h6>Pro Max Plan</h6>
                                    <div class="h4 text-primary">₹47,500<small class="text-muted fs-6">/month</small>
                                    </div>
                                    <small class="text-muted">Includes 30 restaurants</small>
                                    @if ($tenant->subscription_plan !== 'PRO_MAX')
                                        @php
                                            $upgradeCost = $tenant->calculateUpgradeCost('PRO_MAX');
                                            $isWithin3Days = $tenant->isWithin3DayPricingWindow();
                                        @endphp
                                        @if ($upgradeCost > 0)
                                            <div class="upgrade-cost text-success">
                                                <small>Upgrade cost: ₹{{ number_format($upgradeCost, 2) }}</small>
                                                @if ($isWithin3Days)
                                                    <div class="text-warning" style="font-size: 0.75rem;">
                                                        <i class="fa fa-star"></i> Special 3-day pricing!
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="downgrade-note text-warning">
                                                <small>Downgrade (credit applied)</small>
                                            </div>
                                        @endif
                                    @else
                                        <div class="current-plan-badge text-primary">
                                            <small><i class="fa fa-check-circle"></i> Current Plan</small>
                                        </div>
                                    @endif
                                    <p class="text-muted small mb-2">Premium features for enterprise</p>
                                    <div class="plan-features">
                                        <small>✓ <strong>30 Restaurants Included</strong></small><br>
                                        <small>✓ 10 Banners</small><br>
                                        <small>✓ Premium Support</small><br>
                                        <small>✓ All enterprise features</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-success btn-lg px-5">
                                    <i class="fa fa-check me-2"></i>Update Plan
                                </button>
                                <button type="button" class="btn btn-secondary btn-lg px-5 ms-3" id="cancel-change-btn">
                                    <i class="fa fa-times me-2"></i>Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Plan Comparison Table -->
                <div id="plan-comparison" class="mt-4">
                    <h5 class="mb-3">Plan Comparison</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Features</th>
                                    <th class="text-center">Lite</th>
                                    <th class="text-center">Plus</th>
                                    <th class="text-center">Pro Max</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Restaurants Included</strong></td>
                                    <td class="text-center"><span class="badge bg-primary">5 Restaurants</span></td>
                                    <td class="text-center"><span class="badge bg-warning text-dark">20 Restaurants</span>
                                    </td>
                                    <td class="text-center"><span class="badge bg-success">30 Restaurants</span></td>
                                </tr>
                                <tr>
                                    <td>Banner Limit</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">3</td>
                                    <td class="text-center">10</td>
                                </tr>
                                <tr>
                                    <td>Monthly Total Cost</td>
                                    <td class="text-center"><strong>₹3,700</strong><br><small class="text-muted">(₹1,200 +
                                            5×₹500)</small></td>
                                    <td class="text-center"><strong>₹22,000</strong><br><small class="text-muted">(₹2,000
                                            + 20×₹1,000)</small></td>
                                    <td class="text-center"><strong>₹47,500</strong><br><small class="text-muted">(₹2,500
                                            + 30×₹1,500)</small></td>
                                </tr>
                                <tr>
                                    <td>Support Level</td>
                                    <td class="text-center">Basic</td>
                                    <td class="text-center">Priority</td>
                                    <td class="text-center">Premium</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Benefits Section -->
            <div class="payment-card">
                <h4 class="mb-4">
                    <i class="fa fa-gift text-success me-2"></i>
                    What You Get With Your Subscription
                </h4>

                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="icon-circle bg-primary text-white mx-auto mb-3"
                                style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fa fa-store fa-2x"></i>
                            </div>
                            <h6>Restaurant Management</h6>
                            <p class="text-muted small">Manage multiple restaurants from one dashboard</p>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="icon-circle bg-success text-white mx-auto mb-3"
                                style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fa fa-chart-line fa-2x"></i>
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
                                <i class="fa fa-headset fa-2x"></i>
                            </div>
                            <h6>24/7 Support</h6>
                            <p class="text-muted small">Priority customer support</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History Link -->
            <div class="text-center">
                <a href="{{ route('admin.tenant.payment.history') }}" class="btn btn-outline-secondary">
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

            // Form validation before submission
            $('#plan-change-form').on('submit', function(e) {
                const selectedPlan = $('input[name="subscription_plan"]:checked').val();
                const currentPlan = '{{ $tenant->subscription_plan }}';

                if (selectedPlan === currentPlan) {
                    e.preventDefault();
                    alert('You have selected the same plan. Please choose a different plan to continue.');
                    return false;
                }

                if (!confirm(
                        'Are you sure you want to change your subscription plan? This will affect your billing immediately.'
                    )) {
                    e.preventDefault();
                    return false;
                }
            });

            // Set initial selected plan
            $('input[name="subscription_plan"]:checked').closest('.plan-card').addClass('selected');
        });
    </script>
@endsection
