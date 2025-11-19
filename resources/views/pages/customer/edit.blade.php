@extends('layouts.admin')

@section('title', 'Edit Customer')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Edit Customer</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i
                                        class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.customers') }}">Customers</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="text-end">
                <a href="{{ route('admin.customers') }}" class="btn btn-outline-secondary me-2">
                    <i class="fa fa-arrow-left me-2"></i>Back to Customers
                </a>
                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-outline-info">
                    <i class="fa fa-eye me-2"></i>View Details
                </a>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Customer Information</h4>
                        <p class="subtitle">Update basic customer information and account status</p>
                    </div>
                    <div class="box-body">
                        <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Customer Profile Section -->
                                <div class="col-md-4 text-center mb-4">
                                    <div class="profile-img">
                                        <img src="{{ $customer->customerProfile && $customer->customerProfile->profile_image_url
                                            ? asset('storage/' . $customer->customerProfile->profile_image_url)
                                            : asset('images/avatar/default-avatar.png') }}"
                                            alt="Customer Avatar" class="rounded-circle bg-primary-light" width="120"
                                            height="120">
                                    </div>
                                    <h5 class="mt-3 text-center">Customer ID</h5>
                                    <p class="text-muted text-center">CUST{{ str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</p>

                                    <div class="mt-3">
                                        <label class="form-label fw-600">Account Status</label>
                                        <select class="form-select" name="status" required>
                                            <option value="active" {{ $customer->status == 'active' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="suspended"
                                                {{ $customer->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                            <option value="pending" {{ $customer->status == 'pending' ? 'selected' : '' }}>
                                                Pending</option>
                                        </select>
                                        <small class="text-muted">
                                            Active: Can login and place orders<br>
                                            Suspended: Account temporarily blocked<br>
                                            Pending: Registration pending approval
                                        </small>
                                    </div>
                                </div>

                                <!-- Form Fields -->
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-600">First Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('first_name') is-invalid @enderror"
                                                    id="first_name" name="first_name"
                                                    value="{{ old('first_name', $customer->first_name) }}"
                                                    pattern="[A-Za-z\s]+" minlength="2" maxlength="50"
                                                    title="Only letters and spaces allowed" required>
                                                <small class="text-muted">Only letters and spaces (2-50 characters)</small>
                                                @error('first_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="invalid-feedback" id="first_name_error"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-600">Last Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('last_name') is-invalid @enderror"
                                                    id="last_name" name="last_name"
                                                    value="{{ old('last_name', $customer->last_name) }}"
                                                    pattern="[A-Za-z\s]+" minlength="2" maxlength="50"
                                                    title="Only letters and spaces allowed" required>
                                                <small class="text-muted">Only letters and spaces (2-50 characters)</small>
                                                @error('last_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="invalid-feedback" id="last_name_error"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-600">Email Address <span
                                                        class="text-danger">*</span></label>
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror" id="email"
                                                    name="email" value="{{ old('email', $customer->email) }}"
                                                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" minlength="7"
                                                    maxlength="100" title="Enter a valid email address" required>
                                                <small class="text-muted">Valid email address (e.g.,
                                                    user@example.com)</small>
                                                @error('email')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="invalid-feedback" id="email_error"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-600">Phone Number</label>
                                                <input type="tel"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    id="phone" name="phone"
                                                    value="{{ old('phone', $customer->phone) }}" pattern="[0-9]{10,15}"
                                                    minlength="10" maxlength="15" inputmode="numeric"
                                                    title="Enter 10-15 digit phone number">
                                                <small class="text-muted">Only numbers (10-15 digits)</small>
                                                @error('phone')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                <div class="invalid-feedback" id="phone_error"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Account Statistics -->
                                    <div class="mt-4">
                                        <h6 class="fw-600 mb-3">Account Statistics</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="text-center p-3 bg-light rounded">
                                                    <h5 class="mb-1 text-primary">{{ $customer->orders()->count() }}</h5>
                                                    <small class="text-muted">Total Orders</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center p-3 bg-light rounded">
                                                    <h5 class="mb-1 text-success">
                                                        ${{ number_format($customer->orders()->sum('total_amount'), 2) }}
                                                    </h5>
                                                    <small class="text-muted">Total Spent</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center p-3 bg-light rounded">
                                                    <h5 class="mb-1 text-warning">
                                                        {{ $customer->customerProfile->loyalty_points ?? 0 }}</h5>
                                                    <small class="text-muted">Loyalty Points</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center p-3 bg-light rounded">
                                                    <h5 class="mb-1 text-info">
                                                        {{ $customer->created_at->diffForHumans() }}</h5>
                                                    <small class="text-muted">Joined</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h6 class="mb-0">Additional Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <strong>Registration Date:</strong><br>
                                                    <span
                                                        class="text-muted">{{ $customer->created_at->format('M d, Y H:i') }}</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Last Login:</strong><br>
                                                    <span
                                                        class="text-muted">{{ $customer->last_login_at ? $customer->last_login_at->format('M d, Y H:i') : 'Never' }}</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Phone Verified:</strong><br>
                                                    <span
                                                        class="badge {{ $customer->phone_verified_at ? 'bg-success' : 'bg-warning' }}">
                                                        {{ $customer->phone_verified_at ? 'Verified' : 'Not Verified' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.customers') }}" class="btn btn-light">Cancel</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save me-2"></i>Update Customer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="box">
                    <div class="box-header">
                        <h4 class="box-title">Quick Actions</h4>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <a href="{{ route('admin.customers.profile', $customer->id) }}"
                                    class="btn btn-outline-primary btn-block">
                                    <i class="fa fa-user me-2"></i>Manage Profile
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.customers.show', $customer->id) }}"
                                    class="btn btn-outline-info btn-block">
                                    <i class="fa fa-eye me-2"></i>View Details
                                </a>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-outline-warning btn-block"
                                    onclick="resetPassword()">
                                    <i class="fa fa-key me-2"></i>Reset Password
                                </button>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-outline-danger btn-block"
                                    onclick="deleteCustomer()">
                                    <i class="fa fa-trash me-2"></i>Delete Customer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validation functions
            function validateName(input) {
                const value = input.value.trim();
                const namePattern = /^[A-Za-z\s]+$/;
                const isValid = namePattern.test(value) && value.length >= 2 && value.length <= 50;

                updateValidationUI(input, isValid,
                    isValid ? '' : 'Name should contain only letters and spaces (2-50 characters)');

                return isValid;
            }

            function validateEmail(input) {
                const value = input.value.trim();
                const emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
                const isValid = emailPattern.test(value) && value.length >= 7 && value.length <= 100;

                updateValidationUI(input, isValid,
                    isValid ? '' : 'Please enter a valid email address (e.g., user@example.com)');

                return isValid;
            }

            function validatePhone(input) {
                const value = input.value.trim();
                if (value === '') return true; // Phone is optional

                const phonePattern = /^[0-9]{10,15}$/;
                const isValid = phonePattern.test(value);

                updateValidationUI(input, isValid,
                    isValid ? '' : 'Phone number must contain only numbers (10-15 digits)');

                return isValid;
            }

            function updateValidationUI(input, isValid, errorMessage) {
                const errorElement = input.parentElement.querySelector('.invalid-feedback:not(.d-block)');

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

            // Real-time validation for First Name
            const firstNameInput = document.getElementById('first_name');
            if (firstNameInput) {
                firstNameInput.addEventListener('input', function(e) {
                    // Remove any non-letter and non-space characters
                    this.value = this.value.replace(/[^A-Za-z\s]/g, '');
                    validateName(this);
                });

                firstNameInput.addEventListener('keypress', function(e) {
                    // Prevent typing of non-letter and non-space characters
                    const char = String.fromCharCode(e.which || e.keyCode);
                    if (!/[A-Za-z\s]/.test(char)) {
                        e.preventDefault();
                        return false;
                    }
                });
            }

            // Real-time validation for Last Name
            const lastNameInput = document.getElementById('last_name');
            if (lastNameInput) {
                lastNameInput.addEventListener('input', function(e) {
                    // Remove any non-letter and non-space characters
                    this.value = this.value.replace(/[^A-Za-z\s]/g, '');
                    validateName(this);
                });

                lastNameInput.addEventListener('keypress', function(e) {
                    // Prevent typing of non-letter and non-space characters
                    const char = String.fromCharCode(e.which || e.keyCode);
                    if (!/[A-Za-z\s]/.test(char)) {
                        e.preventDefault();
                        return false;
                    }
                });
            }

            // Real-time validation for Email
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.addEventListener('input', function() {
                    validateEmail(this);
                });

                emailInput.addEventListener('blur', function() {
                    // Convert to lowercase on blur
                    this.value = this.value.toLowerCase().trim();
                    validateEmail(this);
                });
            }

            // Real-time validation for Phone
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function(e) {
                    // Remove any non-numeric characters
                    this.value = this.value.replace(/[^0-9]/g, '');
                    validatePhone(this);
                });

                phoneInput.addEventListener('keypress', function(e) {
                    // Prevent typing of non-numeric characters
                    const char = String.fromCharCode(e.which || e.keyCode);
                    if (!/[0-9]/.test(char)) {
                        e.preventDefault();
                        return false;
                    }
                });

                phoneInput.addEventListener('paste', function(e) {
                    // Handle paste event - only allow numbers
                    e.preventDefault();
                    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                    const cleanedText = pastedText.replace(/[^0-9]/g, '');
                    this.value = cleanedText;
                    validatePhone(this);
                });
            }

            // Form submission validation
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const isFirstNameValid = validateName(firstNameInput);
                    const isLastNameValid = validateName(lastNameInput);
                    const isEmailValid = validateEmail(emailInput);
                    const isPhoneValid = validatePhone(phoneInput);

                    if (!isFirstNameValid || !isLastNameValid || !isEmailValid || !isPhoneValid) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please fix the validation errors before submitting the form.'
                        });
                        return false;
                    }
                });
            }
        });

        function resetPassword() {
            Swal.fire({
                title: 'Reset Customer Password',
                text: "This will send a password reset email to the customer. Continue?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send reset email!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Here you would typically make an AJAX call to reset password
                    Swal.fire(
                        'Email Sent!',
                        'Password reset email has been sent to the customer.',
                        'success'
                    );
                }
            });
        }

        function deleteCustomer() {
            Swal.fire({
                title: 'Delete Customer',
                text: "Are you sure you want to delete this customer? This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete customer!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a form and submit delete request
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('admin.customers.destroy', $customer->id) }}';

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = '{{ csrf_token() }}';

                    form.appendChild(methodInput);
                    form.appendChild(tokenInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Show success/error messages
        @if (session('success'))
            Swal.fire(
                'Success!',
                '{{ session('success') }}',
                'success'
            );
        @endif

        @if (session('error'))
            Swal.fire(
                'Error!',
                '{{ session('error') }}',
                'error'
            );
        @endif
    </script>
@endsection
