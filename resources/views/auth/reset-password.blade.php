<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="HungerHop - Reset Password">
    <meta name="author" content="HungerHop">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <title>HungerHop - Reset Password</title>

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

        .reset-icon {
            font-size: 4rem;
            color: #f72d4e;
            margin-bottom: 20px;
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
                            <div class="content-top-agile p-20 pb-0 text-center">
                                <div class="reset-icon">
                                    <i class="ion ion-key"></i>
                                </div>
                                <h2 class="text-primary">Reset Password</h2>
                                <p class="mb-0">Enter your new password below.</p>
                            </div>
                            <div class="p-40">
                                <!-- Display Success Messages -->
                                @if (session('status'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fa fa-check-circle me-2"></i>
                                        {{ session('status') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                <!-- Display validation errors (generic) -->
                                @if ($errors->any() && !$errors->has('error'))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong><i class="fa fa-exclamation-triangle me-2"></i>Please fix the following
                                            errors:</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif

                                <!-- Special handling for token / expired message -->
                                @if ($errors->has('error'))
                                    @php
                                        $errorMsg = $errors->first('error');
                                        $isExpired =
                                            stripos($errorMsg, 'expire') !== false ||
                                            stripos($errorMsg, 'expired') !== false;
                                    @endphp

                                    @if ($isExpired)
                                        <div class="alert alert-warning">
                                            <i class="fa fa-clock-o me-2"></i>
                                            <strong>Link Expired</strong><br>
                                            {{ $errorMsg }} <br><br>
                                            <a href="{{ route('password.request') }}" class="btn btn-danger w-100">
                                                <i class="fa fa-refresh me-2"></i>Request a New Reset Link
                                            </a>
                                            <small class="d-block mt-3 text-muted text-center">
                                                You will be redirected automatically in <span id="countdown"
                                                    class="fw-bold">8</span> seconds.
                                            </small>
                                        </div>

                                        <script>
                                            (function() {
                                                var t = 8;
                                                var el = document.getElementById('countdown');
                                                var iv = setInterval(function() {
                                                    t--;
                                                    if (el) el.innerText = t;
                                                    if (t <= 0) {
                                                        clearInterval(iv);
                                                        window.location.href = "{{ route('password.request') }}";
                                                    }
                                                }, 1000);
                                            })();
                                        </script>
                                    @else
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="fa fa-exclamation-triangle me-2"></i>
                                            {{ $errorMsg }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    @endif
                                @endif

                                <!-- Only show the reset form if there's no token-expired error -->
                                @if (!$errors->has('error') || (isset($isExpired) && !$isExpired))
                                    <form method="POST" action="{{ route('password.update') }}"
                                        id="resetPasswordForm">
                                        @csrf
                                        <input type="hidden" name="token" value="{{ $token }}">

                                        <div class="form-group">
                                            <div class="input-group mb-3">
                                                <span class="input-group-text bg-transparent"><i
                                                        class="ti-email"></i></span>
                                                <input type="email" name="email" id="email"
                                                    class="form-control ps-15 bg-transparent"
                                                    value="{{ $email ?? old('email') }}" required readonly>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="password-field-wrapper">
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text bg-transparent"><i
                                                            class="ti-lock"></i></span>
                                                    <input type="password" name="password" id="password"
                                                        class="form-control ps-15 bg-transparent @error('password') is-invalid @enderror"
                                                        placeholder="New Password" required minlength="8"
                                                        style="padding-right: 45px;">
                                                    <span class="password-toggle"
                                                        onclick="togglePassword('password')">
                                                        <i class="fa fa-eye-slash" id="password-eye"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            @error('password')
                                                <div class="error-message">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <div class="password-field-wrapper">
                                                <div class="input-group mb-3">
                                                    <span class="input-group-text bg-transparent"><i
                                                            class="ti-lock"></i></span>
                                                    <input type="password" name="password_confirmation"
                                                        id="password_confirmation"
                                                        class="form-control ps-15 bg-transparent"
                                                        placeholder="Confirm New Password" required minlength="8"
                                                        style="padding-right: 45px;">
                                                    <span class="password-toggle"
                                                        onclick="togglePassword('password_confirmation')">
                                                        <i class="fa fa-eye-slash" id="password_confirmation-eye"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <button type="submit" class="btn btn-danger mt-10 w-100">
                                                    <i class="fa fa-key me-2"></i>Reset Password
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                @endif

                                <div class="text-center">
                                    <p class="mt-20 mb-0">
                                        Remember your password?
                                        <a href="{{ route('login') }}" class="text-warning ms-2">
                                            <i class="fa fa-arrow-left me-1"></i>Back to Sign In
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="mt-20 text-white">- Need Help? -</p>
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

        document.addEventListener('DOMContentLoaded', function() {
            // Social media buttons - prevent default navigation
            document.querySelectorAll('.btn-social-icon').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Social media help coming soon');
                });
            });
        });
    </script>

</body>

</html>
