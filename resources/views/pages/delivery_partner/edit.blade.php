@extends('layouts.admin')

@section('title', 'Edit Delivery Partner')

@push('styles')
    <style>
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }
    </style>
@endpush

@section('content')
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Edit Delivery Partner</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('restaurant.dashboard') }}"><i
                                        class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('partners.index') }}">Delivery Partners</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="row">
            <div class="col-12 col-md-8 col-lg-6 mx-auto">
                <div class="box">
                    <div class="box-body">
                        <!-- Display Error Messages -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('partners.update', $partner->id) }}" method="POST" id="editPartnerForm">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                    id="first_name" name="first_name"
                                    value="{{ old('first_name', optional($partner->user)->first_name) }}" required
                                    minlength="2" maxlength="15" placeholder="Enter first name (2-15 characters)">
                                @error('first_name')
                                    <div class="error-message">{{ $message }}</div>
                                @else
                                    <div class="error-message" id="first_name-error" style="display:none;"></div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                    id="last_name" name="last_name"
                                    value="{{ old('last_name', optional($partner->user)->last_name) }}" required
                                    minlength="2" maxlength="15" placeholder="Enter last name (2-15 characters)">
                                @error('last_name')
                                    <div class="error-message">{{ $message }}</div>
                                @else
                                    <div class="error-message" id="last_name-error" style="display:none;"></div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    id="phone" name="phone"
                                    value="{{ old('phone', optional($partner->user)->phone) }}" required minlength="10"
                                    maxlength="15" inputmode="numeric" placeholder="Enter phone number (10-15 digits)">
                                @error('phone')
                                    <div class="error-message">{{ $message }}</div>
                                @else
                                    <div class="error-message" id="phone-error" style="display:none;"></div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email"
                                    value="{{ old('email', optional($partner->user)->email) }}" required minlength="7"
                                    maxlength="30" placeholder="Enter email (must end with .com, 7-30 characters)">
                                @error('email')
                                    <div class="error-message">{{ $message }}</div>
                                @else
                                    <div class="error-message" id="email-error" style="display:none;"></div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="vehicle_type" class="form-label">Vehicle Type</label>
                                <select class="form-control @error('vehicle_type') is-invalid @enderror" id="vehicle_type"
                                    name="vehicle_type">
                                    <option value="">Select Vehicle Type</option>
                                    @foreach ($vehicleTypes as $type)
                                        <option value="{{ $type->value }}"
                                            {{ old('vehicle_type', $partner->vehicle_type) == $type->value ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_type')
                                    <div class="error-message">{{ $message }}</div>
                                @else
                                    <div class="error-message" id="vehicle_type-error" style="display:none;"></div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="vehicle_number" class="form-label">Vehicle Number</label>
                                <input type="text" class="form-control @error('vehicle_number') is-invalid @enderror"
                                    id="vehicle_number" name="vehicle_number"
                                    value="{{ old('vehicle_number', $partner->vehicle_number) }}" minlength="6"
                                    maxlength="15" placeholder="e.g., MH 12 AB 1234" style="text-transform: uppercase;">
                                @error('vehicle_number')
                                    <div class="error-message">{{ $message }}</div>
                                @else
                                    <div class="error-message" id="vehicle_number-error" style="display:none;"></div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="license_number" class="form-label">License Number</label>
                                <input type="text" class="form-control @error('license_number') is-invalid @enderror"
                                    id="license_number" name="license_number"
                                    value="{{ old('license_number', $partner->license_number) }}" minlength="16"
                                    maxlength="20" placeholder="Enter license number (16-20 characters)"
                                    style="text-transform: uppercase;">
                                @error('license_number')
                                    <div class="error-message">{{ $message }}</div>
                                @else
                                    <div class="error-message" id="license_number-error" style="display:none;"></div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="is_available" class="form-label">Availability <span
                                        class="text-danger">*</span></label>
                                <select class="form-control @error('is_available') is-invalid @enderror" id="is_available"
                                    name="is_available" required>
                                    <option value="1"
                                        {{ old('is_available', $partner->is_available) == 1 ? 'selected' : '' }}>Available
                                    </option>
                                    <option value="0"
                                        {{ old('is_available', $partner->is_available) == 0 ? 'selected' : '' }}>Not
                                        Available</option>
                                </select>
                                @error('is_available')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span
                                        class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status"
                                    name="status" required>
                                    <option value="active"
                                        {{ old('status', optional($partner->user)->status) == 'active' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="inactive"
                                        {{ old('status', optional($partner->user)->status) == 'inactive' ? 'selected' : '' }}>
                                        Inactive</option>
                                    <option value="pending_approval"
                                        {{ old('status', optional($partner->user)->status) == 'pending_approval' ? 'selected' : '' }}>
                                        Pending Approval</option>
                                    <option value="suspended"
                                        {{ old('status', optional($partner->user)->status) == 'suspended' ? 'selected' : '' }}>
                                        Suspended</option>
                                </select>
                                @error('status')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('partners.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editPartnerForm');

            // Helper functions
            function clearError(fieldId) {
                const errorElement = document.getElementById(fieldId + '-error');
                const inputElement = document.getElementById(fieldId);
                if (errorElement) {
                    errorElement.style.display = 'none';
                    errorElement.textContent = '';
                }
                if (inputElement) {
                    inputElement.classList.remove('is-invalid');
                }
            }

            function showError(fieldId, message) {
                const errorElement = document.getElementById(fieldId + '-error');
                const inputElement = document.getElementById(fieldId);
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.style.display = 'block';
                }
                if (inputElement) {
                    inputElement.classList.add('is-invalid');
                }
            }

            // First name validation - only letters (no spaces)
            const firstNameInput = document.getElementById('first_name');
            firstNameInput.addEventListener('input', function(e) {
                const beforeCursor = this.selectionStart;
                const beforeValue = this.value;

                // Remove any non-letter characters (including spaces)
                this.value = this.value.replace(/[^a-zA-Z]/g, '');

                // Adjust cursor position
                if (beforeValue.length !== this.value.length) {
                    this.selectionStart = this.selectionEnd = beforeCursor - (beforeValue.length - this
                        .value.length);
                }

                clearError('first_name');
            });

            // Last name validation - only letters (no spaces)
            const lastNameInput = document.getElementById('last_name');
            lastNameInput.addEventListener('input', function(e) {
                const beforeCursor = this.selectionStart;
                const beforeValue = this.value;

                // Remove any non-letter characters (including spaces)
                this.value = this.value.replace(/[^a-zA-Z]/g, '');

                // Adjust cursor position
                if (beforeValue.length !== this.value.length) {
                    this.selectionStart = this.selectionEnd = beforeCursor - (beforeValue.length - this
                        .value.length);
                }

                clearError('last_name');
            });

            // Phone number validation - only allow digits
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 15) {
                    this.value = this.value.slice(0, 15);
                }
                clearError('phone');

                if (this.value.length > 0 && this.value.length < 10) {
                    showError('phone', 'Phone number must be at least 10 digits.');
                } else if (/^0+$/.test(this.value)) {
                    showError('phone', 'Phone number cannot be all zeros.');
                }
            });

            // Email validation
            const emailInput = document.getElementById('email');
            emailInput.addEventListener('input', function(e) {
                // Limit to 30 characters
                if (this.value.length > 30) {
                    this.value = this.value.slice(0, 30);
                }
                clearError('email');

                // Real-time length validation
                if (this.value.length > 0 && this.value.length < 7) {
                    showError('email', 'Email must be at least 7 characters long.');
                } else if (this.value.length >= 7 && this.value.length <= 30) {
                    clearError('email');
                }
            }); // Vehicle number validation - allow uppercase letters, numbers, spaces, and hyphens
            const vehicleNumberInput = document.getElementById('vehicle_number');
            if (vehicleNumberInput) {
                vehicleNumberInput.addEventListener('input', function(e) {
                    this.value = this.value.toUpperCase();
                    this.value = this.value.replace(/[^A-Z0-9\s-]/g, '');
                    if (this.value.length > 15) {
                        this.value = this.value.slice(0, 15);
                    }
                    clearError('vehicle_number');
                });
            }

            // License number validation - 16 to 20 alphanumeric characters
            const licenseNumberInput = document.getElementById('license_number');
            if (licenseNumberInput) {
                licenseNumberInput.addEventListener('input', function(e) {
                    this.value = this.value.toUpperCase();
                    this.value = this.value.replace(/[^A-Z0-9]/g, '');
                    if (this.value.length > 20) {
                        this.value = this.value.slice(0, 20);
                    }
                    clearError('license_number');

                    if (this.value.length > 0 && this.value.length < 16) {
                        showError('license_number', 'License number must be at least 16 characters.');
                    }
                });
            }

            // Form submission validation
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                let isValid = true;

                // Clear all previous errors
                clearError('first_name');
                clearError('last_name');
                clearError('email');
                clearError('phone');
                clearError('vehicle_number');
                clearError('license_number');

                // Get form values
                const firstName = firstNameInput.value.trim();
                const lastName = lastNameInput.value.trim();
                const email = emailInput.value.trim();
                const phone = phoneInput.value.trim();
                const vehicleNumber = vehicleNumberInput ? vehicleNumberInput.value.trim() : '';
                const licenseNumber = licenseNumberInput ? licenseNumberInput.value.trim() : '';

                // First name validation
                if (firstName.length < 2 || firstName.length > 15) {
                    showError('first_name', 'First name must be between 2 and 15 characters.');
                    isValid = false;
                } else if (!/^[a-zA-Z]+$/.test(firstName)) {
                    showError('first_name', 'First name can only contain letters (no spaces).');
                    isValid = false;
                }

                // Last name validation
                if (lastName.length < 2 || lastName.length > 15) {
                    showError('last_name', 'Last name must be between 2 and 15 characters.');
                    isValid = false;
                } else if (!/^[a-zA-Z]+$/.test(lastName)) {
                    showError('last_name', 'Last name can only contain letters (no spaces).');
                    isValid = false;
                }

                // Email validation - must end with .com and be 7-30 characters
                if (email.length < 7 || email.length > 30) {
                    showError('email', 'Email must be between 7 and 30 characters long.');
                    isValid = false;
                } else if (!/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.com$/.test(email)) {
                    showError('email', 'Email must be valid and end with .com domain.');
                    isValid = false;
                }

                // Phone validation - 10 to 15 digits, not all zeros
                if (!/^[0-9]{10,15}$/.test(phone)) {
                    showError('phone', 'Phone number must be between 10 to 15 digits.');
                    isValid = false;
                } else if (/^0+$/.test(phone)) {
                    showError('phone', 'Phone number cannot be all zeros.');
                    isValid = false;
                }

                // Vehicle number validation (if provided)
                if (vehicleNumber && vehicleNumber.length > 0) {
                    if (vehicleNumber.length < 6 || vehicleNumber.length > 15) {
                        showError('vehicle_number', 'Vehicle number must be between 6 and 15 characters.');
                        isValid = false;
                    } else if (!/^[A-Z]{2}[\s-]?[0-9]{1,2}[\s-]?[A-Z]{1,2}[\s-]?[0-9]{1,4}$/.test(
                            vehicleNumber)) {
                        showError('vehicle_number',
                            'Please enter a valid vehicle number (e.g., MH 12 AB 1234).');
                        isValid = false;
                    }
                }

                // License number validation (if provided)
                if (licenseNumber && licenseNumber.length > 0) {
                    if (licenseNumber.length < 16 || licenseNumber.length > 20) {
                        showError('license_number', 'License number must be between 16 to 20 characters.');
                        isValid = false;
                    } else if (!/^[A-Z0-9]{16,20}$/.test(licenseNumber)) {
                        showError('license_number',
                            'License number must contain only letters and numbers.');
                        isValid = false;
                    }
                }

                // If validation passes, submit the form
                if (isValid) {
                    form.submit();
                } else {
                    // Scroll to first error
                    const firstError = document.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstError.focus();
                    }
                }
            });
        });
    </script>
@endpush
