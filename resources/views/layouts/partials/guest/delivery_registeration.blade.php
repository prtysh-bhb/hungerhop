<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="HungerHop - Delivery Partner Registration">
    <meta name="author" content="HungerHop">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <title>HungerHop - Delivery Partner Register</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/icons/font-awesome/css/font-awesome.min.css') }}">
    <!-- Themify Icons -->
    <link rel="stylesheet" href="{{ asset('assets/icons/themify-icons/themify-icons.css') }}">
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
        body {
            background-image: url('{{ asset('images/auth-bg/bg-1.jpg') }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            min-height: 100vh;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.10), 0 1.5px 4px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            border-radius: 10px 10px 0 0;
        }

        .form-label {
            font-weight: 500;
            color: #333;
        }

        .form-control,
        .form-select {
            border-radius: 6px;
            border: 1px solid #e3e3e3;
            box-shadow: none;
            font-size: 1rem;
        }

        .btn-success {
            background: linear-gradient(90deg, #28a745 0, #218838 100%);
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.08);
        }

        .btn-success:hover {
            background: linear-gradient(90deg, #218838 0, #28a745 100%);
        }

        .alert {
            border-radius: 6px;
        }

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
        .input-group .form-control,
        .input-group .form-select {
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
                    <div class="col-lg-8 col-md-8 col-12">
                        <div class="bg-white rounded10 shadow-lg">
                            <div class="content-top-agile p-20 pb-0">
                                <h2 class="text-success"><i class="fa fa-motorcycle me-2"></i> Delivery Partner
                                    Registration</h2>
                                <p class="mb-0">Register to become a delivery partner</p>
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

                                <hr>
                                <form method="POST" action="{{ route('guest.delivery-partner.register') }}"
                                    enctype="multipart/form-data" id="deliveryPartnerForm">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text bg-transparent"><i
                                                            class="ti-user"></i></span>
                                                    <input type="text" name="first_name" id="first_name"
                                                        class="form-control ps-15 bg-transparent @error('first_name') is-invalid @enderror"
                                                        placeholder="First Name" required
                                                        value="{{ old('first_name') }}" minlength="2" maxlength="15">
                                                </div>
                                                @error('first_name')
                                                    <div class="error-message">{{ $message }}</div>
                                                @else
                                                    <div class="error-message" id="first_name-error" style="display:none;">
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text bg-transparent"><i
                                                            class="ti-user"></i></span>
                                                    <input type="text" name="last_name" id="last_name"
                                                        class="form-control ps-15 bg-transparent @error('last_name') is-invalid @enderror"
                                                        placeholder="Last Name" required value="{{ old('last_name') }}"
                                                        minlength="2" maxlength="15">
                                                </div>
                                                @error('last_name')
                                                    <div class="error-message">{{ $message }}</div>
                                                @else
                                                    <div class="error-message" id="last_name-error" style="display:none;">
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text bg-transparent"><i
                                                            class="ti-email"></i></span>
                                                    <input type="email" name="email" id="email"
                                                        class="form-control ps-15 bg-transparent @error('email') is-invalid @enderror"
                                                        placeholder="Email (must end with .com)" required
                                                        value="{{ old('email') }}" minlength="7" maxlength="30">
                                                </div>
                                                @error('email')
                                                    <div class="error-message">{{ $message }}</div>
                                                @else
                                                    <div class="error-message" id="email-error" style="display:none;">
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text bg-transparent"><i
                                                            class="ti-mobile"></i></span>
                                                    <input type="text" name="phone" id="phone"
                                                        class="form-control ps-15 bg-transparent @error('phone') is-invalid @enderror"
                                                        placeholder="Phone (10-15 digits)" required
                                                        value="{{ old('phone') }}" minlength="10" maxlength="15"
                                                        inputmode="numeric">
                                                </div>
                                                @error('phone')
                                                    <div class="error-message">{{ $message }}</div>
                                                @else
                                                    <div class="error-message" id="phone-error" style="display:none;">
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="password-field-wrapper">
                                                    <div class="input-group mb-1">
                                                        <span class="input-group-text bg-transparent"><i
                                                                class="ti-lock"></i></span>
                                                        <input type="password" name="password" id="password"
                                                            class="form-control ps-15 bg-transparent @error('password') is-invalid @enderror"
                                                            placeholder="Password" required minlength="8"
                                                            style="padding-right: 45px;">
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
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="password-field-wrapper">
                                                    <div class="input-group mb-1">
                                                        <span class="input-group-text bg-transparent"><i
                                                                class="ti-lock"></i></span>
                                                        <input type="password" name="password_confirmation"
                                                            id="password_confirmation"
                                                            class="form-control ps-15 bg-transparent"
                                                            placeholder="Confirm Password" required minlength="8"
                                                            style="padding-right: 45px;">
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
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text bg-transparent"><i
                                                            class="fa fa-car"></i></span>
                                                    <select name="vehicle_type" id="vehicle_type"
                                                        class="form-select ps-15 bg-transparent @error('vehicle_type') is-invalid @enderror"
                                                        required>
                                                        <option value="">Select Vehicle Type</option>
                                                        @foreach (\App\Enums\VehicleTypeEnums::cases() as $type)
                                                            <option value="{{ $type->value }}"
                                                                {{ old('vehicle_type') == $type->value ? 'selected' : '' }}>
                                                                {{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('vehicle_type')
                                                    <div class="error-message">{{ $message }}</div>
                                                @else
                                                    <div class="error-message" id="vehicle_type-error"
                                                        style="display:none;"></div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text bg-transparent"><i
                                                            class="fa fa-id-card"></i></span>
                                                    <input type="text" name="vehicle_number" id="vehicle_number"
                                                        class="form-control ps-15 bg-transparent @error('vehicle_number') is-invalid @enderror"
                                                        placeholder="Vehicle Number (e.g., MH 12 AB 1234)" required
                                                        value="{{ old('vehicle_number') }}" maxlength="15"
                                                        style="text-transform: capitalize;">
                                                </div>
                                                @error('vehicle_number')
                                                    <div class="error-message">{{ $message }}</div>
                                                @else
                                                    <div class="error-message" id="vehicle_number-error"
                                                        style="display:none;"></div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text bg-transparent"><i
                                                            class="fa fa-id-badge"></i></span>
                                                    <input type="text" name="license_number" id="license_number"
                                                        class="form-control ps-15 bg-transparent @error('license_number') is-invalid @enderror"
                                                        placeholder="License Number (e.g., MH1420110012345)" required
                                                        value="{{ old('license_number') }}" maxlength="16"
                                                        minlength="15" style="text-transform: capitalize;">
                                                </div>
                                                @error('license_number')
                                                    <div class="error-message">{{ $message }}</div>
                                                @else
                                                    <div class="error-message" id="license_number-error"
                                                        style="display:none;"></div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">

                                                <input type="file" name="profile_image" id="profile_image"
                                                    class="form-control @error('profile_image') is-invalid @enderror"
                                                    accept="image/*">
                                                <small class="text-muted">Max size: 2MB (jpg, jpeg, png)</small>
                                                @error('profile_image')
                                                    <div class="error-message">{{ $message }}</div>
                                                @else
                                                    <div class="error-message" id="profile_image-error"
                                                        style="display:none;"></div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text bg-transparent"><i
                                                            class="fa fa-map-marker"></i></span>
                                                    <input type="text" name="current_latitude"
                                                        id="current_latitude"
                                                        class="form-control ps-15 bg-transparent @error('current_latitude') is-invalid @enderror"
                                                        placeholder="Current Latitude" required
                                                        value="{{ old('current_latitude') }}">
                                                </div>
                                                @error('current_latitude')
                                                    <div class="error-message">{{ $message }}</div>
                                                @else
                                                    <div class="error-message" id="current_latitude-error"
                                                        style="display:none;"></div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="input-group mb-1">
                                                    <span class="input-group-text bg-transparent"><i
                                                            class="fa fa-map-marker"></i></span>
                                                    <input type="text" name="current_longitude"
                                                        id="current_longitude"
                                                        class="form-control ps-15 bg-transparent @error('current_longitude') is-invalid @enderror"
                                                        placeholder="Current Longitude" required
                                                        value="{{ old('current_longitude') }}">
                                                </div>
                                                @error('current_longitude')
                                                    <div class="error-message">{{ $message }}</div>
                                                @else
                                                    <div class="error-message" id="current_longitude-error"
                                                        style="display:none;"></div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" id="is_available"
                                                        name="is_available" value="1"
                                                        {{ old('is_available') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_available">Is
                                                        Available</label>
                                                </div>
                                                @error('is_available')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" id="is_online"
                                                        name="is_online" value="1"
                                                        {{ old('is_online') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_online">Is Online</label>
                                                </div>
                                                @error('is_online')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-4">
                                    <h4 class="mb-3 text-success"><i class="fa fa-file-upload me-2"></i> Upload
                                        Document</h4>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="document_type" class="form-label">Document Type</label>
                                                <select name="document_type" id="document_type"
                                                    class="form-select @error('document_type') is-invalid @enderror"
                                                    required>
                                                    <option value="">Select Document Type</option>
                                                    <option value="id_proof"
                                                        {{ old('document_type') == 'id_proof' ? 'selected' : '' }}>ID
                                                        Proof</option>
                                                    <option value="driving_license"
                                                        {{ old('document_type') == 'driving_license' ? 'selected' : '' }}>
                                                        Driving License</option>
                                                    <option value="rc"
                                                        {{ old('document_type') == 'rc' ? 'selected' : '' }}>RC
                                                    </option>
                                                    <option value="address_proof"
                                                        {{ old('document_type') == 'address_proof' ? 'selected' : '' }}>
                                                        Address Proof</option>
                                                    <option value="bank_passbook"
                                                        {{ old('document_type') == 'bank_passbook' ? 'selected' : '' }}>
                                                        Bank Passbook</option>
                                                </select>
                                                @error('document_type')
                                                    <div class="error-message">{{ $message }}</div>
                                                @else
                                                    <div class="error-message" id="document_type-error"
                                                        style="display:none;"></div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="document_file" class="form-label">Document File </label>
                                                <input type="file" name="document_file" id="document_file"
                                                    class="form-control @error('document_file') is-invalid @enderror"
                                                    required accept="image/*,application/pdf">
                                                    <small class="text-muted">Max size: 2MB (jpg, jpeg, png)</small>
                                                @error('document_file')
                                                    <div class="error-message">{{ $message }}</div>
                                                @else
                                                    <div class="error-message" id="document_file-error"
                                                        style="display:none;"></div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-4">
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-info margin-top-10"
                                            id="registerBtn">REGISTER</button>
                                    </div>
                                    <div class="mt-3 text-center">
                                        <p class="text-center">Already have an account?<a href="{{ route('login') }}"
                                                class="text-danger ms-5"> Sign In</a></p>
                                    </div>
                                </form>
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

            // Form validation
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('deliveryPartnerForm');

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
                    } else if (this.value.length >= 10 && /^0+$/.test(this.value)) {
                        showError('phone', 'Phone number cannot be all zeros.');
                    }
                });

                // First name validation - only letters and spaces
                const firstNameInput = document.getElementById('first_name');
                firstNameInput.addEventListener('input', function(e) {
                    const beforeCursor = this.selectionStart;
                    const beforeValue = this.value;
                    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
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
                }); // Vehicle number validation - allow uppercase letters, numbers, spaces, and hyphens
                const vehicleNumberInput = document.getElementById('vehicle_number');
                vehicleNumberInput.addEventListener('input', function(e) {
                    this.value = this.value.toUpperCase();
                    this.value = this.value.replace(/[^A-Z0-9\s-]/g, '');
                    if (this.value.length > 15) {
                        this.value = this.value.slice(0, 15);
                    }
                    clearError('vehicle_number');
                });

                // License number validation - Indian format (e.g., MH1420110012345)
                const licenseNumberInput = document.getElementById('license_number');
                licenseNumberInput.addEventListener('input', function(e) {
                    this.value = this.value.toUpperCase();
                    this.value = this.value.replace(/[^A-Z0-9]/g, '');
                    if (this.value.length > 16) {
                        this.value = this.value.slice(0, 16);
                    }
                    clearError('license_number');
                    if (this.value.length > 0 && this.value.length < 15) {
                        showError('license_number', 'License number must be at least 15 characters.');
                    } else if (this.value.length >= 15 && this.value.length <= 16) {
                        clearError('license_number');
                    }
                });

                // Profile image validation
                const profileImageInput = document.getElementById('profile_image');
                if (profileImageInput) {
                    profileImageInput.addEventListener('change', function(e) {
                        clearError('profile_image');

                        if (this.files && this.files[0]) {
                            const file = this.files[0];
                            const fileSize = file.size / 1024 / 1024; // Convert to MB
                            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];

                            if (!allowedTypes.includes(file.type)) {
                                showError('profile_image', 'Profile photo must be a jpeg, jpg, or png file.');
                                this.value = '';
                            } else if (fileSize > 2) {
                                showError('profile_image', 'Profile photo size must not exceed 2MB.');
                                this.value = '';
                            }
                        }
                    });
                }

                // Password validation
                const passwordInput = document.getElementById('password');
                passwordInput.addEventListener('input', function(e) {
                    clearError('password');
                });

                const passwordConfirmInput = document.getElementById('password_confirmation');
                passwordConfirmInput.addEventListener('input', function(e) {
                    clearError('password_confirmation');
                });

                // Form submission validation
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    console.log('Form submission attempted');

                    let isValid = true;

                    // Clear all previous errors
                    clearError('first_name');
                    clearError('last_name');
                    clearError('email');
                    clearError('phone');
                    clearError('password');
                    clearError('password_confirmation');
                    clearError('vehicle_number');
                    clearError('license_number');

                    // Get form values
                    const firstName = document.getElementById('first_name').value.trim();
                    const lastName = document.getElementById('last_name').value.trim();
                    const email = document.getElementById('email').value.trim();
                    const phone = document.getElementById('phone').value.trim();
                    const password = document.getElementById('password').value;
                    const passwordConfirmation = document.getElementById('password_confirmation').value;
                    const vehicleNumber = document.getElementById('vehicle_number').value.trim();
                    const licenseNumber = document.getElementById('license_number').value.trim();

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
                        showError('phone', 'Phone number must be between 10 to 15 digits.');
                        isValid = false;
                    } else if (/^0+$/.test(phone)) {
                        showError('phone', 'Phone number cannot be all zeros.');
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

                    // Vehicle number validation
                    if (vehicleNumber.length < 6 || vehicleNumber.length > 15) {
                        showError('vehicle_number', 'Vehicle number must be between 6 and 15 characters.');
                        isValid = false;
                    } else if (!/^[A-Z]{2}[\s-]?[0-9]{1,2}[\s-]?[A-Z]{1,2}[\s-]?[0-9]{1,4}$/.test(
                            vehicleNumber)) {
                        showError('vehicle_number',
                            'Please enter a valid vehicle number (e.g., MH 12 AB 1234).');
                        isValid = false;
                    }

                    // License number validation - Indian format: 2 state code + 2 RTO code + 4 year + 7 unique number
                    if (licenseNumber.length < 15 || licenseNumber.length > 16) {
                        showError('license_number', 'License number must be 15 to 16 characters.');
                        isValid = false;
                    } else if (!/^[A-Z]{2}[0-9]{2}[0-9]{4}[0-9]{7,8}$/.test(licenseNumber)) {
                        showError('license_number',
                            'License number format: 2 letters + 2 digits + 4 digits (year) + 7-8 digits (e.g., MH1420110012345).'
                        );
                        isValid = false;
                    }

                    // If validation passes, submit the form
                    if (isValid) {
                        console.log('Form validation passed, submitting form');
                        form.submit();
                    } else {
                        console.log('Validation failed - errors shown inline');
                    }
                });
            });
        </script>
</body>

</html>
