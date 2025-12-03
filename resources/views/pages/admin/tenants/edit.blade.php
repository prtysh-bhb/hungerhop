@extends('layouts.admin')

@section('title', 'Edit Tenant')

@section('styles')
    <style>
        .form-section {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .form-section h5 {
            color: #2d3748;
            margin-bottom: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .subscription-plan-section {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .subscription-plan-section h5 {
            color: #2d3748;
            margin-bottom: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .plan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .plan-card {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            position: relative;
            overflow: hidden;
        }

        .plan-card:hover {
            border-color: #3b82f6;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }

        .plan-card.selected {
            border-color: #3b82f6;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.15);
        }

        .plan-card.selected::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 60px;
            height: 60px;
            background: #3b82f6;
            clip-path: polygon(100% 0, 0 0, 100% 100%);
        }

        .plan-card.selected::after {
            content: '✓';
            position: absolute;
            top: 8px;
            right: 8px;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        .plan-content {
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .plan-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
        }

        .plan-icon.lite {
            background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
        }

        .plan-icon.plus {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .plan-icon.pro {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .plan-title {
            margin: 0.5rem 0;
            color: #2d3748;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .plan-description {
            color: #6b7280;
            margin: 0.5rem 0;
            font-size: 0.9rem;
            min-height: 40px;
        }

        .plan-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1f2937;
            margin: 0.75rem 0;
        }

        .plan-features {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #f1f5f9;
            text-align: left;
        }

        .plan-feature {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            color: #4b5563;
            font-size: 0.9rem;
        }

        .plan-feature i {
            color: #3b82f6;
            font-size: 0.8rem;
        }

        .plan-card input[type="radio"] {
            display: none;
        }

        .plan-limit-warning {
            margin-top: 1rem;
            padding: 0.75rem;
            background: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 6px;
            color: #92400e;
            display: none;
        }

        .plan-limit-warning i {
            margin-right: 0.5rem;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 0.8rem;
            margin-top: 0.25rem;
            display: none;
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #4a5568;
            display: block;
        }

        .form-control {
            border-radius: 6px;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            width: 100%;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .input-group {
            display: flex;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #d1d5db;
            padding: 0.75rem;
            border-right: none;
            border-radius: 6px 0 0 6px;
        }

        .input-group .form-control {
            border-radius: 0 6px 6px 0;
            border-left: none;
        }

        .alert {
            border-radius: 6px;
            padding: 0.75rem 1rem;
        }

        .btn {
            border-radius: 6px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(75, 85, 99, 0.2);
        }

        .btn-info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .btn-info:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        .field-hint {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .content-header {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .text-danger {
            color: #dc3545;
        }

        .status-alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .plan-grid {
                grid-template-columns: 1fr;
            }

            .form-section div[style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-full">
        <!-- Content Header -->
        <div class="content-header">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h4 style="margin: 0 0 0.5rem 0; color: #2d3748;">Edit Tenant</h4>
                    <nav>
                        <ol style="display: flex; list-style: none; padding: 0; margin: 0; gap: 0.5rem; font-size: 0.9rem;">
                            <li><a href="{{ route('admin.dashboard') }}" style="color: #6b7280;">Home</a></li>
                            <li style="color: #6b7280;">/</li>
                            <li><a href="{{ route('admin.tenants.index') }}" style="color: #6b7280;">Tenants</a></li>
                            <li style="color: #6b7280;">/</li>
                            <li style="color: #2d3748;">Edit {{ $tenant->tenant_name }}</li>
                        </ol>
                    </nav>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('admin.tenants.show', $tenant) }}" class="btn btn-info">
                        <i class="fa fa-eye"></i> View Details
                    </a>
                    <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <section class="content">
            <!-- Status Alert -->
            @if ($tenant->status === 'pending')
                <div class="status-alert" style="background: #fff3cd; border: 1px solid #ffeaa7; color: #856404;">
                    <i class="fa fa-exclamation-triangle"></i>
                    This tenant is still pending approval. You can approve it after updating the details.
                </div>
            @elseif($tenant->status === 'suspended')
                <div class="status-alert" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24;">
                    <i class="fa fa-ban"></i>
                    This tenant is currently suspended. Consider reactivating after reviewing the details.
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST" id="tenantForm">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="form-section">
                    <h5><i class="fa fa-info-circle"></i> Basic Information</h5>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label for="tenant_name">Tenant Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tenant_name" name="tenant_name"
                                value="{{ old('tenant_name', $tenant->tenant_name) }}" required>
                            <div class="field-hint">Only letters and spaces (2-100 characters)</div>
                            <div class="invalid-feedback" id="tenant_name_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="contact_person">Contact Person <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person"
                                value="{{ old('contact_person', $tenant->contact_person) }}" required>
                            <div class="field-hint">Only letters and spaces (2-100 characters)</div>
                            <div class="invalid-feedback" id="contact_person_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email', $tenant->email) }}" required>
                            <div class="field-hint">Valid email address (e.g., user@example.com)</div>
                            <div class="invalid-feedback" id="email_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                value="{{ old('phone', $tenant->phone) }}" required>
                            <div class="field-hint">Only numbers (10-15 digits)</div>
                            <div class="invalid-feedback" id="phone_error"></div>
                        </div>
                    </div>
                </div>

                <!-- Subscription Plan -->
                <div class="form-section">
                    <h5>Subscription Plan</h5>
                    <div class="plan-grid">
                        <label
                            class="plan-card {{ old('subscription_plan', $tenant->subscription_plan) == 'LITE' ? 'selected' : '' }}"
                            for="plan_lite" data-plan="LITE">
                            <input type="radio" id="plan_lite" name="subscription_plan" value="LITE"
                                {{ old('subscription_plan', $tenant->subscription_plan) == 'LITE' ? 'checked' : '' }}>
                            <div class="plan-content">
                                <div class="icon-circle bg-info">
                                    <i class="fa fa-star"></i>
                                </div>
                                <h6>Lite Plan</h6>
                                <p>Basic features for small businesses</p>
                                <div class="plan-price">₹1,200/month</div>
                                <div class="plan-features">
                                    <small>
                                        Up to 5 Restaurants<br>
                                        1 Banner
                                    </small>
                                </div>
                            </div>
                        </label>

                        <label
                            class="plan-card {{ old('subscription_plan', $tenant->subscription_plan) == 'PLUS' ? 'selected' : '' }}"
                            for="plan_plus" data-plan="PLUS">
                            <input type="radio" id="plan_plus" name="subscription_plan" value="PLUS"
                                {{ old('subscription_plan', $tenant->subscription_plan) == 'PLUS' ? 'checked' : '' }}>
                            <div class="plan-content">
                                <div class="icon-circle bg-warning">
                                    <i class="fa fa-star"></i>
                                </div>
                                <h6>Plus Plan</h6>
                                <p>Advanced features for growing businesses</p>
                                <div class="plan-price">₹2,000/month</div>
                                <div class="plan-features">
                                    <small>
                                        Up to 20 Restaurants<br>
                                        3 Banners
                                    </small>
                                </div>
                            </div>
                        </label>

                        <label
                            class="plan-card {{ old('subscription_plan', $tenant->subscription_plan) == 'PRO_MAX' ? 'selected' : '' }}"
                            for="plan_pro" data-plan="PRO_MAX">
                            <input type="radio" id="plan_pro" name="subscription_plan" value="PRO_MAX"
                                {{ old('subscription_plan', $tenant->subscription_plan) == 'PRO_MAX' ? 'checked' : '' }}>
                            <div class="plan-content">
                                <div class="icon-circle bg-success">
                                    <i class="fa fa-star"></i>
                                </div>
                                <h6>Pro Max Plan</h6>
                                <p>Premium features for enterprise</p>
                                <div class="plan-price">₹2,500/month</div>
                                <div class="plan-features">
                                    <small>
                                        Up to 30 Restaurants<br>
                                        10 Banners
                                    </small>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Plan Limit Warning -->
                    <div id="plan-limit-warning" class="alert alert-info mt-3" style="display: none;">
                        <i class="fa fa-info-circle me-2"></i>
                        <span id="plan-limit-text"></span>
                    </div>
                </div>

                <style>
                    .plan-grid {
                        display: grid;
                        grid-template-columns: 1fr 1fr 1fr;
                        gap: 1rem;
                    }

                    .plan-card {
                        border: 2px solid #e2e8f0;
                        border-radius: 8px;
                        padding: 1.5rem;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        background: white;
                    }

                    .plan-card:hover {
                        border-color: #cbd5e0;
                        transform: translateY(-2px);
                    }

                    .plan-card.selected {
                        border-color: #3b82f6;
                        background-color: #f0f9ff;
                        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
                    }

                    .plan-content {
                        text-align: center;
                    }

                    .icon-circle {
                        width: 60px;
                        height: 60px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 1rem;
                    }

                    .plan-card h6 {
                        margin: 0.5rem 0;
                        color: #2d3748;
                        font-weight: 600;
                    }

                    .plan-card p {
                        color: #6b7280;
                        margin: 0.5rem 0;
                        font-size: 0.9rem;
                    }

                    .plan-price {
                        font-size: 1.25rem;
                        font-weight: bold;
                        color: #1f2937;
                        margin: 0.5rem 0;
                    }

                    .plan-features small {
                        color: #6b7280;
                        line-height: 1.4;
                    }

                    .plan-card input[type="radio"] {
                        display: none;
                    }

                    @media (max-width: 768px) {
                        .plan-grid {
                            grid-template-columns: 1fr;
                        }
                    }
                </style>

                <!-- Business Configuration -->
                <div class="form-section">
                    <h5><i class="fa fa-cog"></i> Business Configuration</h5>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label for="total_restaurants">Total Restaurants Allowed</label>
                            <input type="number" class="form-control" id="total_restaurants" name="total_restaurants"
                                value="{{ old('total_restaurants', $tenant->total_restaurants) }}" readonly>
                            <div class="field-hint">Determined by selected plan</div>
                        </div>

                        <div class="form-group">
                            <label for="banner_limit">Banner Limit</label>
                            <input type="number" class="form-control" id="banner_limit" name="banner_limit"
                                value="{{ old('banner_limit', $tenant->banner_limit) }}" readonly>
                            <div class="field-hint">Determined by selected plan</div>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="pending"
                                    {{ old('status', $tenant->status) == 'pending' ? 'selected' : '' }}>
                                    Pending</option>
                                <option value="approved"
                                    {{ old('status', $tenant->status) == 'approved' ? 'selected' : '' }}>
                                    Approved</option>
                                <option value="suspended"
                                    {{ old('status', $tenant->status) == 'suspended' ? 'selected' : '' }}>
                                    Suspended</option>
                                <option value="rejected"
                                    {{ old('status', $tenant->status) == 'rejected' ? 'selected' : '' }}>
                                    Rejected</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Pricing Configuration -->
                <div class="form-section">
                    <h5><i class="fa fa-money-bill-wave"></i> Pricing Configuration</h5>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label for="monthly_base_fee">Monthly Base Fee</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" id="monthly_base_fee" name="monthly_base_fee"
                                    value="{{ old('monthly_base_fee', $tenant->monthly_base_fee) }}" readonly>
                            </div>
                            <div class="field-hint">Fixed monthly fee</div>
                        </div>

                        <div class="form-group">
                            <label for="per_restaurant_fee">Per Restaurant Fee</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" id="per_restaurant_fee"
                                    name="per_restaurant_fee"
                                    value="{{ old('per_restaurant_fee', $tenant->per_restaurant_fee) }}" readonly>
                            </div>
                            <div class="field-hint">Additional fee per restaurant</div>
                        </div>
                    </div>
                </div>

                <!-- Subscription Dates -->
                <div class="form-section p-4">
                    <h5><i class="fa fa-calendar-alt"></i> Subscription Dates</h5>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label for="subscription_start_date">Start Date</label>
                            <input type="date" class="form-control" id="subscription_start_date"
                                name="subscription_start_date"
                                value="{{ old('subscription_start_date', $tenant->subscription_start_date?->format('Y-m-d')) }}"
                                max="{{ date('Y-m-d') }}">
                            <div class="field-hint">Cannot be a future date</div>
                            <div class="invalid-feedback" id="subscription_start_date_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="subscription_end_date">End Date</label>
                            <input type="date" class="form-control" id="subscription_end_date"
                                name="subscription_end_date"
                                value="{{ old('subscription_end_date', $tenant->subscription_end_date?->format('Y-m-d')) }}"
                                min="{{ date('Y-m-d') }}" max="{{ date('Y-m-d', strtotime('+10 years')) }}">
                            <div class="field-hint">Subscription expiry date</div>
                            <div class="invalid-feedback" id="subscription_end_date_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="next_billing_date">Next Billing Date</label>
                            <input type="date" class="form-control" id="next_billing_date" name="next_billing_date"
                                value="{{ old('next_billing_date', $tenant->next_billing_date?->format('Y-m-d')) }}"
                                min="{{ date('Y-m-d') }}" max="{{ date('Y-m-d', strtotime('+2 years')) }}">
                            <div class="field-hint">Must be between today and 2 years from now</div>
                            <div class="invalid-feedback" id="next_billing_date_error"></div>
                        </div>
                    </div>
                </div>

                <!-- Admin Notes -->
                @if (auth()->user()->role === 'super_admin')
                    <div class="form-section">
                        <h5><i class="fa fa-sticky-note"></i> Admin Notes</h5>
                        <div class="form-group">
                            <label for="admin_notes">Internal Notes</label>
                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"
                                placeholder="Internal notes about this tenant...">{{ old('admin_notes', $tenant->admin_notes) }}</textarea>
                        </div>
                    </div>
                @endif

                <!-- Submit Buttons -->
                <div class="form-section">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; gap: 1rem;">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> Update Tenant
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                <i class="fa fa-undo"></i> Reset Changes
                            </button>
                        </div>
                        @if (auth()->user()->role === 'super_admin' && $tenant->status !== 'approved')
                            <button type="button" class="btn btn-info" onclick="approveAndSave()">
                                <i class="fa fa-check-circle"></i> Save & Approve
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Define plan limits
            const planLimits = {
                'LITE': {
                    maxRestaurants: 5,
                    maxBanners: 1,
                    baseFee: 1200,
                    perRestaurantFee: 500,
                    name: 'Lite Plan'
                },
                'PLUS': {
                    maxRestaurants: 20,
                    maxBanners: 3,
                    baseFee: 2000,
                    perRestaurantFee: 1000,
                    name: 'Plus Plan'
                },
                'PRO_MAX': {
                    maxRestaurants: 30,
                    maxBanners: 10,
                    baseFee: 2500,
                    perRestaurantFee: 1500,
                    name: 'Pro Max Plan'
                }
            };

            // Validation functions
            function validateName(input) {
                const value = input.value.trim();
                // Only allow letters (A-Z, a-z) and spaces - no numbers or special characters
                const namePattern = /^[A-Za-z][A-Za-z\s]*$/;
                // Check if value contains any numbers or special characters
                const hasInvalidChars = /[0-9!@#$%^&*()_+=\[\]{}|;:'",.<>?/\\`~-]/.test(value);
            const isValid = namePattern.test(value) && !hasInvalidChars && value.length >= 2 && value.length <=
                100;

            let errorMessage = '';
            if (!isValid) {
                if (hasInvalidChars) {
                    errorMessage = 'Name cannot contain numbers or special characters';
                } else if (value.length < 2) {
                    errorMessage = 'Name must be at least 2 characters long';
                } else if (value.length > 100) {
                    errorMessage = 'Name cannot exceed 100 characters';
                } else {
                    errorMessage = 'Name should contain only letters and spaces';
                }
            }

            updateValidationUI(input, isValid, errorMessage);

            return isValid;
        }

        function validateEmail(input) {
            const value = input.value.trim();
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z][a-zA-Z0-9.-]*\.[a-zA-Z]{2,}$/;
            const isValid = emailPattern.test(value) && value.length >= 5 && value.length <= 100;

            updateValidationUI(input, isValid,
                isValid ? '' :
                'Please enter a valid email (e.g., user@gmail.com). Domain must start with a letter.');

            return isValid;
        }

        function validatePhone(input) {
            const value = input.value.trim();
            const phonePattern = /^[0-9]{10,15}$/;
            // Check if all digits are zeros
            const isAllZeros = /^0+$/.test(value);
            // Check if phone has at least one non-zero digit
            const hasValidDigits = /[1-9]/.test(value);
            const isValid = phonePattern.test(value) && !isAllZeros && hasValidDigits;

            let errorMessage = '';
            if (!isValid) {
                if (isAllZeros) {
                    errorMessage = 'Phone number cannot be all zeros';
                } else if (!hasValidDigits) {
                    errorMessage = 'Phone number must contain at least one non-zero digit';
                } else {
                    errorMessage = 'Phone number must contain only numbers (10-15 digits)';
                }
            }

            updateValidationUI(input, isValid, errorMessage);

            return isValid;
        }

        function updateValidationUI(input, isValid, errorMessage) {
            const errorElement = input.parentElement.querySelector('.invalid-feedback');

            if (isValid) {
                input.classList.remove('is-invalid');
                if (errorElement) {
                    errorElement.style.display = 'none';
                }
            } else {
                input.classList.add('is-invalid');
                if (errorElement) {
                    errorElement.textContent = errorMessage;
                    errorElement.style.display = 'block';
                }
            }
        }

        // Real-time validation for input fields
        document.getElementById('tenant_name').addEventListener('input', function() {
            validateName(this);
        });

        document.getElementById('contact_person').addEventListener('input', function() {
            validateName(this);
        });

        document.getElementById('email').addEventListener('input', function() {
            validateEmail(this);
        });

        document.getElementById('phone').addEventListener('input', function() {
            // Remove any non-numeric characters
            this.value = this.value.replace(/[^0-9]/g, '');
            validatePhone(this);
        });

        // Handle plan selection
        const planCards = document.querySelectorAll('.plan-card');
        const planInputs = document.querySelectorAll('input[name="subscription_plan"]');

        // Function to update form values based on selected plan
        function updatePlanValues(planValue) {
            const limits = planLimits[planValue];

            if (limits) {
                // Update fees and limits based on plan
                document.getElementById('monthly_base_fee').value = limits.baseFee;
                document.getElementById('per_restaurant_fee').value = limits.perRestaurantFee;
                document.getElementById('total_restaurants').value = limits.maxRestaurants;
                document.getElementById('banner_limit').value = limits.maxBanners;

                // Show plan limitations
                showPlanLimitations(planValue, limits);
            }
        }

        // Function to select a plan card
        function selectPlanCard(selectedCard, radio) {
            // Remove selected class from all cards
            planCards.forEach(card => card.classList.remove('selected'));

            // Add selected class to clicked card
            selectedCard.classList.add('selected');

            // Ensure the radio is checked
            if (radio) {
                radio.checked = true;
            }

            // Update form values
            const planValue = selectedCard.dataset.plan;
            updatePlanValues(planValue);
        }

        // Add click event to plan cards
        planCards.forEach(card => {
            card.addEventListener('click', function(e) {
                const radio = card.querySelector('input[type="radio"]');
                selectPlanCard(card, radio);
            });
        });

        // Also handle direct radio change events
        planInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.checked) {
                    const card = this.closest('.plan-card');
                    selectPlanCard(card, this);
                }
            });
        });

        // Function to show plan limitations
        function showPlanLimitations(plan, limits) {
            const warningText =
                `${limits.name} allows up to ${limits.maxRestaurants} restaurants and ${limits.maxBanners} banner(s). Monthly base fee: ₹${limits.baseFee}`;
            document.getElementById('plan-limit-text').textContent = warningText;
            document.getElementById('plan-limit-warning').style.display = 'flex';
        }

        // Set initial selected plan on page load
        const initialPlan = document.querySelector('input[name="subscription_plan"]:checked');
        if (initialPlan) {
            const initialCard = initialPlan.closest('.plan-card');
            selectPlanCard(initialCard, initialPlan);
        } else {
            // If no plan is selected, select the first one by default
            const firstCard = document.querySelector('.plan-card');
            if (firstCard) {
                selectPlanCard(firstCard, firstCard.querySelector('input[type="radio"]'));
            }
        }

        // Subscription date validation
        function validateDateYear(value) {
            // Check if year is valid (between 2000 and current year + 10)
            const year = parseInt(value.split('-')[0]);
            const currentYear = new Date().getFullYear();
            const minYear = 2000;
            const maxYear = currentYear + 10;

            if (isNaN(year) || year < minYear || year > maxYear) {
                return {
                    isValid: false,
                    message: `Year must be between ${minYear} and ${maxYear}`
                };
            }
            return {
                isValid: true,
                message: ''
            };
        }

        function validateStartDate(input) {
            const value = input.value;
            if (!value) return true; // Optional field

            // First validate the year
            const yearValidation = validateDateYear(value);
            if (!yearValidation.isValid) {
                updateValidationUI(input, false, yearValidation.message);
                return false;
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const selectedDate = new Date(value);
            const isValid = selectedDate <= today;

            updateValidationUI(input, isValid,
                isValid ? '' : 'Start date cannot be a future date');

            return isValid;
        }

        function validateNextBillingDate(input) {
            const value = input.value;
            if (!value) return true; // Optional field

            // First validate the year
            const yearValidation = validateDateYear(value);
            if (!yearValidation.isValid) {
                updateValidationUI(input, false, yearValidation.message);
                return false;
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const twoYearsFromNow = new Date();
            twoYearsFromNow.setFullYear(twoYearsFromNow.getFullYear() + 2);
            const selectedDate = new Date(value);

            const isValidMin = selectedDate >= today;
            const isValidMax = selectedDate <= twoYearsFromNow;
            const isValid = isValidMin && isValidMax;

            let errorMessage = '';
            if (!isValidMin) {
                errorMessage = 'Next billing date cannot be in the past';
            } else if (!isValidMax) {
                errorMessage = 'Next billing date cannot be more than 2 years from now';
            }

            updateValidationUI(input, isValid, errorMessage);

            return isValid;
        }

        function validateEndDate(input) {
            const value = input.value;
            if (!value) return true; // Optional field

            // First validate the year
            const yearValidation = validateDateYear(value);
            if (!yearValidation.isValid) {
                updateValidationUI(input, false, yearValidation.message);
                return false;
            }

            // End date should be after start date if start date exists
            const startDateInput = document.getElementById('subscription_start_date');
            if (startDateInput && startDateInput.value) {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(value);
                if (endDate <= startDate) {
                    updateValidationUI(input, false, 'End date must be after start date');
                    return false;
                }
            }

            updateValidationUI(input, true, '');
            return true;
        }

        // Add date validation listeners
        document.getElementById('subscription_start_date').addEventListener('change', function() {
            validateStartDate(this);
            // Re-validate end date when start date changes
            const endDateInput = document.getElementById('subscription_end_date');
            if (endDateInput && endDateInput.value) {
                validateEndDate(endDateInput);
            }
        });

        document.getElementById('subscription_end_date').addEventListener('change', function() {
            validateEndDate(this);
        });

        document.getElementById('next_billing_date').addEventListener('change', function() {
            validateNextBillingDate(this);
        });

        // Form submission validation
        document.getElementById('tenantForm').addEventListener('submit', function(e) {
            // Validate all fields before submission
            const isTenantNameValid = validateName(document.getElementById('tenant_name'));
            const isContactPersonValid = validateName(document.getElementById('contact_person'));
            const isEmailValid = validateEmail(document.getElementById('email'));
            const isPhoneValid = validatePhone(document.getElementById('phone'));
            const isStartDateValid = validateStartDate(document.getElementById(
                'subscription_start_date'));
            const isEndDateValid = validateEndDate(document.getElementById(
                'subscription_end_date'));
            const isNextBillingDateValid = validateNextBillingDate(document.getElementById(
                'next_billing_date'));

            // Check if a plan is selected
            const selectedPlan = document.querySelector('input[name="subscription_plan"]:checked');
            if (!selectedPlan) {
                e.preventDefault();
                alert('Please select a subscription plan before submitting the form.');
                return false;
            }

            // Prevent form submission if any validation fails
            if (!isTenantNameValid || !isContactPersonValid || !isEmailValid || !isPhoneValid || !
                isStartDateValid || !isEndDateValid || !isNextBillingDateValid) {
                e.preventDefault();
                alert('Please fix the validation errors before submitting the form.');
                return false;
            }
        });

        // Reset form function
        window.resetForm = function() {
            if (confirm('Are you sure you want to reset all changes?')) {
                document.getElementById('tenantForm').reset();

                // Reset plan selection to original
                const currentPlan = '{{ $tenant->subscription_plan }}';
                const planRadio = document.querySelector(
                    `input[name="subscription_plan"][value="${currentPlan}"]`);
                    if (planRadio) {
                        const card = planRadio.closest('.plan-card');
                        selectPlanCard(card, planRadio);
                    }

                    // Clear validation errors
                    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
                }
            };

            // Approve and save function
            window.approveAndSave = function() {
                if (confirm('This will save the changes and approve the tenant. Continue?')) {
                    document.getElementById('status').value = 'approved';
                    document.getElementById('tenantForm').submit();
                }
            };
        });
    </script>
@endsection
