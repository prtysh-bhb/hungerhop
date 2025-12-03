@extends('layouts.admin')

@section('title', 'Create New Tenant')

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

        .plan-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.25rem;
            cursor: pointer;
            transition: all 0.2s;
            height: 100%;
            background: white;
        }

        .plan-card:hover {
            border-color: #4e73df;
        }

        .plan-card.selected {
            border-color: #4e73df;
            background: #f8faff;
        }

        .plan-card input[type="radio"] {
            display: none;
        }

        .plan-features {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
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
            border-color: #4e73df;
            outline: none;
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
            transition: background-color 0.2s;
        }

        .btn-success {
            background-color: #10b981;
            color: white;
        }

        .btn-success:hover {
            background-color: #059669;
        }

        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #4b5563;
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

        .icon-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
        }

        .plan-price {
            font-weight: 600;
            color: #059669;
            margin: 0.5rem 0;
        }

        .text-danger {
            color: #dc3545;
        }
    </style>
@endsection

@section('content')
    <div class="container-full">
        <!-- Content Header -->
        <div class="content-header">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <h4 style="margin: 0 0 0.5rem 0; color: #2d3748;">Create New Tenant</h4>
                    <nav>
                        <ol style="display: flex; list-style: none; padding: 0; margin: 0; gap: 0.5rem; font-size: 0.9rem;">
                            <li><a href="{{ route('admin.dashboard') }}" style="color: #6b7280;">Home</a></li>
                            <li style="color: #6b7280;">/</li>
                            <li><a href="{{ route('admin.tenants.index') }}" style="color: #6b7280;">Tenants</a></li>
                            <li style="color: #6b7280;">/</li>
                            <li style="color: #2d3748;">Create</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <section class="content">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.tenants.store') }}" method="POST" id="tenantForm">
                @csrf

                <!-- Basic Information -->
                <div class="form-section">
                    <h5>Basic Information</h5>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label for="tenant_name">Tenant Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tenant_name" name="tenant_name"
                                value="{{ old('tenant_name') }}" required>
                            <div class="field-hint">Only letters and spaces (2-100 characters)</div>
                            <div class="invalid-feedback" id="tenant_name_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="contact_person">Contact Person <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person"
                                value="{{ old('contact_person') }}" required>
                            <div class="field-hint">Only letters and spaces (2-100 characters)</div>
                            <div class="invalid-feedback" id="contact_person_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email') }}" required>
                            <div class="field-hint">Valid email address (e.g., user@example.com)</div>
                            <div class="invalid-feedback" id="email_error"></div>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                value="{{ old('phone') }}" required>
                            <div class="field-hint">Only numbers (10-15 digits)</div>
                            <div class="invalid-feedback" id="phone_error"></div>
                        </div>
                    </div>
                </div>

                <!-- Subscription Plan -->
                <div class="form-section">
                    <h5>Subscription Plan</h5>
                    <div class="plan-grid">
                        <label class="plan-card" for="plan_lite">
                            <input type="radio" id="plan_lite" name="subscription_plan" value="LITE"
                                {{ old('subscription_plan') == 'LITE' ? 'checked' : '' }}>
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

                        <label class="plan-card" for="plan_plus">
                            <input type="radio" id="plan_plus" name="subscription_plan" value="PLUS"
                                {{ old('subscription_plan') == 'PLUS' ? 'checked' : '' }}>
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

                        <label class="plan-card" for="plan_pro">
                            <input type="radio" id="plan_pro" name="subscription_plan" value="PRO_MAX"
                                {{ old('subscription_plan') == 'PRO_MAX' ? 'checked' : '' }}>
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
                <div class="form-section pt-4">
                    <h5>Business Configuration</h5>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label for="total_restaurants">Total Restaurants Allowed</label>
                            <input type="number" class="form-control" id="total_restaurants" name="total_restaurants"
                                value="{{ old('total_restaurants', 0) }}" readonly>
                            <div class="field-hint">Determined by selected plan</div>
                            <div id="restaurant-limit-error" class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label for="banner_limit">Banner Limit</label>
                            <input type="number" class="form-control" id="banner_limit" name="banner_limit"
                                value="{{ old('banner_limit', 0) }}" readonly>
                            <div class="field-hint">Determined by selected plan</div>
                            <div id="banner-limit-error" class="invalid-feedback"></div>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>
                                    Pending</option>
                                <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved
                                </option>
                                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended
                                </option>
                                <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Pricing Configuration -->
                <div class="form-section">
                    <h5>Pricing Configuration</h5>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label for="monthly_base_fee">Monthly Base Fee</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" id="monthly_base_fee" name="monthly_base_fee"
                                    value="{{ old('monthly_base_fee', 0) }}" readonly>
                            </div>
                            <div class="field-hint">Fixed monthly fee</div>
                        </div>

                        <div class="form-group">
                            <label for="per_restaurant_fee">Per Restaurant Fee</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" id="per_restaurant_fee"
                                    name="per_restaurant_fee" value="{{ old('per_restaurant_fee', 0) }}" readonly>
                            </div>
                            <div class="field-hint">Additional fee per restaurant</div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="form-section">
                    <div style="text-align: center;">
                        <button type="submit" class="btn btn-success" style="padding: 0.75rem 2rem;">
                            Create Tenant
                        </button>
                        <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary"
                            style="margin-left: 1rem; padding: 0.75rem 2rem;">
                            Cancel
                        </a>
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
            // Check if phone starts with valid digit (not all zeros)
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
                errorElement.style.display = 'none';
            } else {
                input.classList.add('is-invalid');
                errorElement.textContent = errorMessage;
                errorElement.style.display = 'block';
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

        // Handle plan selection - both click on card and radio change
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
            radio.checked = true;

            // Update form values
            updatePlanValues(radio.value);
        }

        // Add click event to plan cards
        planCards.forEach(card => {
            const radio = card.querySelector('input[type="radio"]');

            card.addEventListener('click', function() {
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
                `${limits.name} allows up to ${limits.maxRestaurants} restaurants and ${limits.maxBanners} banner(s). Monthly fee: ₹${limits.baseFee}`;
                document.getElementById('plan-limit-text').textContent = warningText;
                document.getElementById('plan-limit-warning').style.display = 'block';
            }

            // Set initial selected plan on page load
            const initialPlan = document.querySelector('input[name="subscription_plan"]:checked');
            if (initialPlan) {
                const initialCard = initialPlan.closest('.plan-card');
                selectPlanCard(initialCard, initialPlan);
            }

            // Form submission validation
            document.getElementById('tenantForm').addEventListener('submit', function(e) {
                // Validate all fields before submission
                const isTenantNameValid = validateName(document.getElementById('tenant_name'));
                const isContactPersonValid = validateName(document.getElementById('contact_person'));
                const isEmailValid = validateEmail(document.getElementById('email'));
                const isPhoneValid = validatePhone(document.getElementById('phone'));

                // Check if a plan is selected
                const selectedPlan = document.querySelector('input[name="subscription_plan"]:checked');
                if (!selectedPlan) {
                    e.preventDefault();
                    alert('Please select a subscription plan before submitting the form.');
                    return false;
                }

                // Prevent form submission if any validation fails
                if (!isTenantNameValid || !isContactPersonValid || !isEmailValid || !isPhoneValid) {
                    e.preventDefault();
                    alert('Please fix the validation errors before submitting the form.');
                    return false;
                }
            });
        });
    </script>
@endsection
