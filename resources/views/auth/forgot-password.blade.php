<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="HungerHop - Forgot Password">
    <meta name="author" content="HungerHop">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <title>HungerHop - Forgot Password</title>

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

        .forgot-icon {
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
                                <div class="forgot-icon">
                                    <i class="ion ion-locked"></i>
                                </div>
                                <h2 class="text-primary">Forgot Password?</h2>
                                <p class="mb-0">Enter your email address and we'll send you a link to reset your
                                    password.</p>
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

                                <!-- Display Error Messages -->
                                @if ($errors->any())
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

                                <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm" novalidate>
                                    @csrf
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text bg-transparent"><i
                                                    class="ti-email"></i></span>
                                            <input type="email" name="email" id="email"
                                                class="form-control ps-15 bg-transparent @error('email') is-invalid @enderror"
                                                placeholder="Enter your email address"
                                                value="{{ old('email') }}" maxlength="255">
                                        </div>
                                        @error('email')
                                            <div class="error-message">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <button type="submit" class="btn btn-danger mt-2">
                                                <i class="fa fa-paper-plane me-2"></i> Send Reset Link
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <div class="text-center">
                                    <p class="mt-20 mb-0">
                                        Remember your password?
                                        <a href="{{ route('login') }}" class="text-warning ms-2">
                                            Back to Sign In
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
