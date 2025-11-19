<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="HungerHop - Restaurant Login">
    <meta name="author" content="HungerHop">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <title>HungerHop - Sign In</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/bootstrap/dist/css/bootstrap.min.css') }}">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/icons/font-awesome/css/font-awesome.min.css') }}">

    <!-- Themify Icons -->
    <link rel="stylesheet" href="{{ asset('assets/icons/themify-icons/themify-icons.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Ion Icons -->
    <link rel="stylesheet" href="{{ asset('assets/icons/Ionicons/css/ionicons.min.css') }}">

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
    style="background-image: url({{ asset('images/auth-bg/bg-1.jpg') }})">

    <div class="container h-p100">
        <div class="row align-items-center justify-content-md-center h-p100">

            <div class="col-12">
                <div class="row justify-content-center g-0">
                    <div class="col-lg-5 col-md-5 col-12">
                        <div class="bg-white rounded10 shadow-lg">
                            <div class="content-top-agile p-20 pb-0">
                                <h2 class="text-primary">Welcome to HungerHop</h2>
                                <p class="mb-0">Sign in to continue as a customer.</p>
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

                                <form action="{{ route('login.submit') }}" method="post" id="loginForm">
                                    @csrf
                                    <!-- Hidden field to ensure remember is always sent -->
                                    <input type="hidden" name="remember" value="0">
                                    <div class="form-group">
                                        <div class="input-group mb-1">
                                            <span class="input-group-text bg-transparent"><i class="ti-user"></i></span>
                                            <input type="text" name="username" id="username"
                                                class="form-control ps-15 bg-transparent @error('username') is-invalid @enderror"
                                                placeholder="Email or Phone" required value="{{ old('username') }}"
                                                minlength="3" maxlength="255">
                                        </div>
                                        @error('username')
                                            <div class="error-message">{{ $message }}</div>
                                        @else
                                            <div class="error-message" id="username-error" style="display:none;"></div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <div class="password-field-wrapper">
                                            <div class="input-group mb-1">
                                                <span class="input-group-text bg-transparent"><i
                                                        class="ti-lock"></i></span>
                                                <input type="password" name="password" id="password"
                                                    class="form-control ps-15 bg-transparent @error('password') is-invalid @enderror"
                                                    placeholder="Password" required minlength="8"
                                                    style="padding-right: 45px;">
                                                <span class="password-toggle" onclick="togglePassword('password')">
                                                    <i class="fa fa-eye-slash" id="password-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                        @error('password')
                                            <div class="error-message">{{ $message }}</div>
                                        @else
                                            <div class="error-message" id="password-error" style="display:none;"></div>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        {{-- <div class="col-6">
                                            <div class="checkbox">
                                                <input type="checkbox" id="basic_checkbox_1" name="remember"
                                                    value="1">
                                                <label for="basic_checkbox_1">Remember Me</label>
                                            </div>
                                        </div> --}}
                                        <!-- /.col -->
                                        {{-- <div class="col-6">
                                            <div class="fog-pwd text-end">
                                                <a href="{{ route('password.request') }}" class="hover-warning"><i class="ion ion-locked"></i> Forgot pwd?</a><br>
                                            </div>
                                        </div> --}}
                                        <!-- /.col -->
                                        <div class="col-12 text-end mb-10">
                                            <a href="{{ route('password.request') }}" class="hover-warning">
                                                <i class="ion ion-locked"></i> Forgot Password?
                                            </a>
                                        </div>
                                        <div class="col-12 text-center">
                                            <button type="submit" class="btn btn-danger mt-10">SIGN IN</button>
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                </form>

                                <div class="text-center">
                                    <p class="mt-15 mb-0">
                                        <a href="{{ route('guest.delivery-partner.register-form') }}"
                                            class=class="btn btn-outline-success w-100">
                                            <i class="fa fa-plus me-1"></i> Register as Delivery Partner
                                        </a>
                                        <span class="mx-3">|</span>
                                        Don't have an account? <a href="{{ route('register') }}"
                                            class="text-warning ms-5">Sign Up</a>
                                    </p>

                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="mt-20 text-white">- Sign With -</p>
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

    <!-- Chat Popup -->
    <script src="{{ asset('js/pages/chat-popup.js') }}"></script>

    <!-- ApexCharts -->
    <script src="{{ asset('assets/vendor_components/apexcharts-bundle/dist/apexcharts.min.js') }}"></script>

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

        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');

            // Clear errors on input
            const usernameInput = document.getElementById('username');
            usernameInput.addEventListener('input', function(e) {
                clearError('username');
            });

            const passwordInput = document.getElementById('password');
            passwordInput.addEventListener('input', function(e) {
                clearError('password');
            });

            // Form submission validation
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Always prevent default first

                console.log('Login form submission attempted');

                let isValid = true;

                // Clear all previous errors
                clearError('username');
                clearError('password');

                // Get form values
                const username = document.getElementById('username').value.trim();
                const password = document.getElementById('password').value;

                // Username validation
                if (username.length < 3) {
                    showError('username', 'Email or phone must be at least 3 characters.');
                    isValid = false;
                } else if (username.length > 255) {
                    showError('username', 'Email or phone may not be greater than 255 characters.');
                    isValid = false;
                }

                // Password validation
                if (password.length < 8) {
                    showError('password', 'Password must be at least 8 characters.');
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
                    console.log('Social media login coming soon');
                });
            });
        });
    </script>


</body>

</html>
