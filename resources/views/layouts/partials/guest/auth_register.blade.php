<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="HungerHop - Restaurant Registration">
    <meta name="author" content="HungerHop">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <title>HungerHop - Register</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/bootstrap/dist/css/bootstrap.min.css') }}">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/icons/font-awesome/css/font-awesome.min.css') }}">

    <!-- Themify Icons -->
    <link rel="stylesheet" href="{{ asset('assets/icons/themify-icons/themify-icons.css') }}">

    <!-- Ion Icons -->
    <link rel="stylesheet" href="{{ asset('assets/icons/Ionicons/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Perfect Scrollbar -->
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/perfect-scrollbar/css/perfect-scrollbar.css') }}">

    <!-- Vendors Style-->
    <link rel="stylesheet" href="{{ asset('css/vendors_css.css') }}">

    <!-- Style-->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/skin_color.css') }}">

    <style>
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            background: transparent;
            border: none;
            padding: 5px;
        }

        .password-field-wrapper {
            position: relative;
        }

        .password-field-wrapper .form-control {
            padding-right: 45px !important;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }

        .success-message {
            color: #28a745;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .terms-error {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        /* Make all input groups consistent */
        .input-group {
            width: 100%;
        }

        /* Input group text (icons) - left side rounded */
        .input-group-text {
            border-top-left-radius: 0.25rem !important;
            border-bottom-left-radius: 0.25rem !important;
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        /* Form controls - right side rounded */
        .input-group .form-control {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
            border-top-right-radius: 0.25rem !important;
            border-bottom-right-radius: 0.25rem !important;
        }
    </style>


</head>

<body class="hold-transition theme-primary bg-img"
    style="background-image: url({{ asset('images/auth-bg/bg-2.jpg') }})">

    <div class="container h-p100">
        <div class="row align-items-center justify-content-md-center h-p100">

            <div class="col-12">
                <div class="row justify-content-center g-0">
                    <div class="col-lg-5 col-md-5 col-12">
                        <div class="bg-white rounded10 shadow-lg">
                            <div class="content-top-agile p-20 pb-0">
                                <h2 class="text-primary">Get started with Us</h2>
                                <p class="mb-0">Register a new membership</p>
                            </div>
                            <div class="p-40">
                                <!-- Display Success Messages -->
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

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

                                <form action="{{ route('register.submit') }}" method="post" id="registerForm">
                                    @csrf
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text bg-transparent"><i
                                                            class="ti-user"></i></span>
                                                    <input type="text" name="first_name" id="first_name"
                                                        class="form-control ps-15 bg-transparent @error('first_name') is-invalid @enderror"
                                                        placeholder="First Name" required
                                                        value="{{ old('first_name') }}" minlength="2" maxlength="15"
                                                        title="First name should only contain letters and spaces (2-15 characters)">
                                                </div>
                                                @error('first_name')
                                                    <div class="error-message">{{ $message }}</div>
                                                @else
                                                    <div class="error-message" id="first_name-error" style="display:none;">
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text bg-transparent"><i
                                                            class="ti-user"></i></span>
                                                    <input type="text" name="last_name" id="last_name"
                                                        class="form-control ps-15 bg-transparent @error('last_name') is-invalid @enderror"
                                                        placeholder="Last Name" required value="{{ old('last_name') }}"
                                                        minlength="2" maxlength="15"
                                                        title="Last name should only contain letters and spaces (2-15 characters)">
                                                </div>
                                                @error('last_name')
                                                    <div class="error-message">{{ $message }}</div>
                                                @else
                                                    <div class="error-message" id="last_name-error" style="display:none;">
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text bg-transparent"><i
                                                            class="ti-email"></i></span>
                                                    <input type="email" name="email" id="email"
                                                        class="form-control ps-15 bg-transparent @error('email') is-invalid @enderror"
                                                        placeholder="Email (must end with .com)" required
                                                        value="{{ old('email') }}" minlength="7" maxlength="30"
                                                        title="Email must be valid, end with .com, and be 7-30 characters long">
                                                </div>
                                                @error('email')
                                                    <div class="error-message">{{ $message }}</div>
                                                @else
                                                    <div class="error-message" id="email-error" style="display:none;">
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text bg-transparent"><i
                                                            class="ti-mobile"></i></span>
                                                    <input type="text" name="phone" id="phone"
                                                        class="form-control ps-15 bg-transparent @error('phone') is-invalid @enderror"
                                                        placeholder="Phone (10-15 digits)" required
                                                        value="{{ old('phone') }}" minlength="10" maxlength="15"
                                                        inputmode="numeric"
                                                        title="Phone number must be between 10 to 15 digits">
                                                </div>
                                                @error('phone')
                                                    <div class="error-message">{{ $message }}</div>
                                                @else
                                                    <div class="error-message" id="phone-error" style="display:none;">
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="role" value="customer">
                                    @error('role')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <div class="password-field-wrapper">
                                                    <div class="input-group mb-1">
                                                        <span class="input-group-text bg-transparent"><i
                                                                class="ti-lock"></i></span>
                                                        <input type="password" name="password" id="password"
                                                            class="form-control ps-15 bg-transparent @error('password') is-invalid @enderror"
                                                            placeholder="Password" required minlength="8"
                                                            style="padding-right: 45px;"
                                                            title="Password must be at least 8 characters with uppercase, lowercase, numbers, and symbols">
                                                        <span class="password-toggle"
                                                            onclick="togglePassword('password')">
                                                            <i class="fa fa-eye-slash" id="password-eye"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                @error('password')
                                                    <div class="error-message">{{ $message }}</div>
                                                @else
                                                    <div class="error-message" id="password-error" style="display:none;">
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <div class="password-field-wrapper">
                                                    <div class="input-group mb-1">
                                                        <span class="input-group-text bg-transparent"><i
                                                                class="ti-lock"></i></span>
                                                        <input type="password" name="password_confirmation"
                                                            id="password_confirmation"
                                                            class="form-control ps-15 bg-transparent"
                                                            placeholder="Confirm Password" required minlength="8"
                                                            style="padding-right: 45px;"
                                                            title="Please confirm your password">
                                                        <span class="password-toggle"
                                                            onclick="togglePassword('password_confirmation')">
                                                            <i class="fa fa-eye-slash"
                                                                id="password_confirmation-eye"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="error-message" id="password_confirmation-error"
                                                    style="display:none;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="checkbox">
                                                <input type="checkbox" id="basic_checkbox_1" name="terms"
                                                    @if (old('terms')) checked @endif>
                                                <label for="basic_checkbox_1">I agree to the <a class="text-warning"
                                                        href="#"><b>Terms</b></a></label>
                                            </div>
                                            @error('terms')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror
                                            <div class="terms-error" id="terms-error">You must accept the terms and
                                                conditions to continue.</div>
                                        </div>
                                        <!-- /.col -->
                                        <div class="col-12 text-center">
                                            <button type="submit" class="btn btn-info margin-top-10"
                                                id="registerBtn">REGISTER</button>
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                </form>
                                <div class="text-center">
                                    <p class="mt-15 mb-0">Already have an account?<a href="{{ route('login') }}"
                                            class="text-danger ms-5"> Sign In</a></p>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <p class="mt-20 text-white">- Register With -</p>
                            <p class="gap-items-2 mb-20">
                                <a class="btn btn-social-icon btn-round btn-facebook" href="#"><i
                                        class="fa fa-facebook"></i></a>
                                <a class="btn btn-social-icon btn-round btn-twitter" href="#"><i
                                        class="fa fa-twitter"></i></a>
                                <a class="btn btn-social-icon btn-round btn-instagram" href="#"><i
                                        class="fa fa-instagram"></i></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/vendor_plugins/JqueryPrintArea/demo/jquery-2.1.0.js') }}"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/vendor_components/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Perfect Scrollbar -->
    <script src="{{ asset('assets/vendor_components/perfect-scrollbar/dist/perfect-scrollbar.min.js') }}"></script>

    <!-- Feather Icons -->
    <script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>

    <!-- Vendor JS -->
    <script src="{{ asset('js/vendors.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('js/template.js') }}"></script>

    <script>
        // Password show/hide toggle functionality
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(fieldId + '-eye');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        }

        // Clear error message helper
        function clearError(fieldId) {
            const errorElement = document.getElementById(fieldId + '-error');
            if (errorElement) {
                errorElement.style.display = 'none';
                errorElement.textContent = '';
            }
        }

        // Show error message helper
        function showError(fieldId, message) {
            const errorElement = document.getElementById(fieldId + '-error');
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }
        }

        // Form validation with improved error handling
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            const registerBtn = document.getElementById('registerBtn');

            // Phone number validation - only allow digits
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 15) {
                    this.value = this.value.slice(0, 15);
                }

                // Clear error on input
                clearError('phone');

                // Show real-time validation
                if (this.value.length > 0 && this.value.length < 10) {
                    showError('phone', 'Phone number must be at least 10 digits.');
                } else if (this.value.length >= 10 && this.value.length <= 15) {
                    clearError('phone');
                }
            });

            // First name validation - only letters and spaces
            const firstNameInput = document.getElementById('first_name');
            firstNameInput.addEventListener('input', function(e) {
                const beforeCursor = this.selectionStart;
                const beforeValue = this.value;
                this.value = this.value.replace(/[^a-zA-Z\s]/g, '');

                // Adjust cursor position if characters were removed
                if (beforeValue.length !== this.value.length) {
                    this.selectionStart = this.selectionEnd = beforeCursor - (beforeValue.length - this
                        .value.length);
                }

                clearError('first_name');
            });

            // Last name validation - only letters and spaces
            const lastNameInput = document.getElementById('last_name');
            lastNameInput.addEventListener('input', function(e) {
                const beforeCursor = this.selectionStart;
                const beforeValue = this.value;
                this.value = this.value.replace(/[^a-zA-Z\s]/g, '');

                // Adjust cursor position if characters were removed
                if (beforeValue.length !== this.value.length) {
                    this.selectionStart = this.selectionEnd = beforeCursor - (beforeValue.length - this
                        .value.length);
                }

                clearError('last_name');
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
            }); // Password validation
            const passwordInput = document.getElementById('password');
            passwordInput.addEventListener('input', function(e) {
                clearError('password');
            });

            // Password confirmation validation
            const passwordConfirmInput = document.getElementById('password_confirmation');
            passwordConfirmInput.addEventListener('input', function(e) {
                clearError('password_confirmation');
            });

            // Terms checkbox validation with message
            const termsCheckbox = document.getElementById('basic_checkbox_1');
            const termsError = document.getElementById('terms-error');

            termsCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    termsError.style.display = 'none';
                }
            });

            // Form submission validation with inline errors (NO ALERTS)
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Always prevent default first

                console.log('Form submission attempted');

                let isValid = true;

                // Clear all previous errors
                clearError('first_name');
                clearError('last_name');
                clearError('email');
                clearError('phone');
                clearError('password');
                clearError('password_confirmation');
                termsError.style.display = 'none';

                // Get form values
                const firstName = document.getElementById('first_name').value.trim();
                const lastName = document.getElementById('last_name').value.trim();
                const email = document.getElementById('email').value.trim();
                const phone = document.getElementById('phone').value.trim();
                const password = document.getElementById('password').value;
                const passwordConfirmation = document.getElementById('password_confirmation').value;
                const terms = termsCheckbox.checked;

                // First name validation
                if (firstName.length < 2 || firstName.length > 15) {
                    showError('first_name', 'First name must be between 2 and 15 characters.');
                    isValid = false;
                } else if (!/^[a-zA-Z\s]+$/.test(firstName)) {
                    showError('first_name', 'First name can only contain letters and spaces.');
                    isValid = false;
                }

                // Last name validation
                if (lastName.length < 2 || lastName.length > 15) {
                    showError('last_name', 'Last name must be between 2 and 15 characters.');
                    isValid = false;
                } else if (!/^[a-zA-Z\s]+$/.test(lastName)) {
                    showError('last_name', 'Last name can only contain letters and spaces.');
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

                // Phone validation - 10 to 15 digits
                if (!/^[0-9]{10,15}$/.test(phone)) {
                    showError('phone',
                        'Phone number must be between 10 to 15 digits without any special characters.');
                    isValid = false;
                }

                // Password validation
                if (password.length < 8) {
                    showError('password', 'Password must be at least 8 characters long.');
                    isValid = false;
                } else {
                    const errors = [];
                    if (!/[a-z]/.test(password)) errors.push('one lowercase letter');
                    if (!/[A-Z]/.test(password)) errors.push('one uppercase letter');
                    if (!/[0-9]/.test(password)) errors.push('one number');
                    if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) errors.push('one special character');

                    if (errors.length > 0) {
                        showError('password', 'Password must contain ' + errors.join(', ') + '.');
                        isValid = false;
                    }
                }

                // Password confirmation validation
                if (password !== passwordConfirmation) {
                    showError('password_confirmation', 'Password confirmation does not match.');
                    isValid = false;
                }

                // Terms validation
                if (!terms) {
                    termsError.style.display = 'block';
                    isValid = false;
                }

                // If validation passes, submit the form
                if (isValid) {
                    console.log('Form validation passed, submitting form');
                    form.submit(); // Submit the form programmatically
                } else {
                    console.log('Validation failed - errors shown inline');
                }
            });

            // Social media buttons - prevent default navigation
            document.querySelectorAll('.btn-social-icon').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Social media registration coming soon');
                });
            });
        });
    </script>

</body>

</html>
{{-- Blade Form → Route → Request Validation → Controller → Database → Authentication → Role-based Redirect --}}
