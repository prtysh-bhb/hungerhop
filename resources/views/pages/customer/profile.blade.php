@extends('layouts.admin')

@section('title', 'Customer Profile Management')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Customer Profile Management</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i
                                        class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.customers') }}">Customers</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Profile Management</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="text-end">
                <a href="{{ route('admin.customers') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-2"></i>Back to Customers
                </a>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Customer Info Card -->
            <div class="col-xl-4 col-lg-5">
                <div class="box">
                    <div class="box-body text-center">
                        <div class="profile-img">
                            <img src="{{ $customer->customerProfile && $customer->customerProfile->profile_image_url
                                ? asset('storage/' . $customer->customerProfile->profile_image_url)
                                : asset('images/avatar/default-avatar.png') }}"
                                alt="Customer Avatar" class="rounded-circle bg-primary-light" width="100" height="100">
                        </div>
                        <h4 class="mt-20 mb-0">{{ $customer->first_name }} {{ $customer->last_name }}</h4>
                        <p class="text-muted">Customer ID: CUST{{ str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</p>

                        <div class="row mt-30">
                            <div class="col-4">
                                <div class="text-center">
                                    <h4 class="mb-0 text-primary">{{ $orderStats['total_orders'] }}</h4>
                                    <p class="text-muted">Total Orders</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center">
                                    <h4 class="mb-0 text-success">${{ number_format($orderStats['total_spent'], 2) }}</h4>
                                    <p class="text-muted">Total Spent</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center">
                                    <h4 class="mb-0 text-warning">{{ $customer->customerProfile->loyalty_points ?? 0 }}</h4>
                                    <p class="text-muted">Loyalty Points</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-30">
                            <span
                                class="badge badge-lg 
                                @if ($customer->status == 'active') badge-success
                                @elseif($customer->status == 'suspended') badge-warning  
                                @else badge-secondary @endif">
                                {{ ucfirst($customer->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="box">
                    <div class="box-header">
                        <h4 class="box-title">Quick Stats</h4>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Joined Date:</span>
                                    <strong>{{ $customer->created_at->format('M d, Y') }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Last Login:</span>
                                    <strong>{{ $customer->last_login_at ? $customer->last_login_at->format('M d, Y') : 'Never' }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Average Order:</span>
                                    <strong>${{ number_format($orderStats['avg_order_value'], 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Last Order:</span>
                                    <strong>{{ $orderStats['last_order'] ? $orderStats['last_order']->created_at->format('M d, Y') : 'No orders' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Management -->
            <div class="col-xl-8 col-lg-7">
                <!-- Tabs -->
                <div class="box">
                    <div class="box-header">
                        <ul class="nav nav-tabs customtab2" role="tablist">
                            <li class="nav-item"> <a class="nav-link active" data-bs-toggle="tab" href="#profile"
                                    role="tab">Profile Information</a> </li>
                            <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#addresses"
                                    role="tab">Addresses</a> </li>
                            <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#orders"
                                    role="tab">Order History</a> </li>
                            <li class="nav-item"> <a class="nav-link" data-bs-toggle="tab" href="#loyalty"
                                    role="tab">Loyalty Points</a> </li>
                        </ul>
                    </div>
                    <div class="box-body">
                        <div class="tab-content">
                            <!-- Profile Information Tab -->
                            <div class="tab-pane active" id="profile" role="tabpanel">
                                <form action="{{ route('admin.customers.profile.update', $customer->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>First Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="first_name" id="first_name"
                                                    value="{{ old('first_name', $customer->first_name) }}" minlength="2"
                                                    maxlength="15" required pattern="[a-zA-Z]+"
                                                    title="First name must contain only letters (2-15 characters, no spaces)">
                                                <div class="invalid-feedback" style="display: none;"></div>
                                                @error('first_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Last Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="last_name"
                                                    id="last_name" value="{{ old('last_name', $customer->last_name) }}"
                                                    minlength="2" maxlength="15" required pattern="[a-zA-Z]+"
                                                    title="Last name must contain only letters (2-15 characters, no spaces)">
                                                <div class="invalid-feedback" style="display: none;"></div>
                                                @error('last_name')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" name="email" id="email"
                                                    value="{{ old('email', $customer->email) }}" minlength="7"
                                                    maxlength="30" pattern="[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.com$"
                                                    title="Email must end with .com domain and be 7-30 characters"
                                                    required>
                                                <div class="invalid-feedback" style="display: none;"></div>
                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Phone <span class="text-danger">*</span></label>
                                                <input type="tel" class="form-control" name="phone" id="phone"
                                                    value="{{ old('phone', $customer->phone) }}" minlength="10"
                                                    maxlength="15" pattern="[1-9][0-9]{9,14}"
                                                    title="Phone must be 10-15 digits, cannot start with 0 or be all zeros"
                                                    required>
                                                <div class="invalid-feedback" style="display: none;"></div>
                                                @error('phone')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Date of Birth</label>
                                                <input type="date" class="form-control" name="date_of_birth"
                                                    id="date_of_birth"
                                                    value="{{ $customer->customerProfile->date_of_birth ?? '' }}"
                                                    max="{{ date('Y-m-d', strtotime('-5 years')) }}">
                                                <small class="text-muted">Must be at least 5 years old</small>
                                                <div class="invalid-feedback" style="display: none;"></div>
                                                @error('date_of_birth')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Gender</label>
                                                <select class="form-select" name="gender">
                                                    <option value="">Select Gender</option>
                                                    <option value="male"
                                                        {{ ($customer->customerProfile->gender ?? '') == 'male' ? 'selected' : '' }}>
                                                        Male</option>
                                                    <option value="female"
                                                        {{ ($customer->customerProfile->gender ?? '') == 'female' ? 'selected' : '' }}>
                                                        Female</option>
                                                    <option value="other"
                                                        {{ ($customer->customerProfile->gender ?? '') == 'other' ? 'selected' : '' }}>
                                                        Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>Profile Image</label>
                                                <input type="file" class="form-control" name="profile_image"
                                                    accept="image/*">
                                                <small class="text-muted">Max file size: 2MB. Supported formats: JPG, PNG,
                                                    GIF</small>
                                                @error('profile_image')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end mt-20">
                                        <button type="submit" class="btn btn-primary">Update Profile</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Addresses Tab -->
                            <div class="tab-pane" id="addresses" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Customer Addresses</h5>
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#addAddressModal">
                                        <i class="fa fa-plus"></i> Add Address
                                    </button>
                                </div>

                                <div class="row">
                                    @forelse($customer->addresses as $address)
                                        <div class="col-md-6 mb-3">
                                            <div class="card {{ $address->is_default ? 'border-primary' : '' }}">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0">
                                                        {{ $address->is_default ? 'Default Address' : 'Address' }}
                                                        @if ($address->is_default)
                                                            <span class="badge bg-primary ms-2">Default</span>
                                                        @endif
                                                    </h6>
                                                    <button class="btn btn-sm btn-outline-danger delete-address"
                                                        data-address-id="{{ $address->id }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                                <div class="card-body">
                                                    <p class="mb-1"><strong>{{ $address->address_line1 }}</strong></p>
                                                    @if ($address->address_line2)
                                                        <p class="mb-1">{{ $address->address_line2 }}</p>
                                                    @endif
                                                    @if ($address->landmark)
                                                        <p class="mb-1 text-info"><i
                                                                class="fa fa-map-marker me-1"></i>{{ $address->landmark }}
                                                        </p>
                                                    @endif
                                                    <p class="mb-0 text-muted">{{ $address->city }},
                                                        {{ $address->state }} {{ $address->postal_code }}</p>
                                                    @if ($address->address_type)
                                                        <span
                                                            class="badge bg-secondary mt-2">{{ ucfirst($address->address_type) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="text-center py-4">
                                                <i class="fa fa-map-marker fa-3x text-muted mb-3"></i>
                                                <h5>No addresses found</h5>
                                                <p class="text-muted">This customer hasn't added any addresses yet.</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Order History Tab -->
                            <div class="tab-pane" id="orders" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Order #</th>
                                                <th>Restaurant</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($customer->orders()->latest()->take(10)->get() as $order)
                                                <tr>
                                                    <td><strong>{{ $order->order_number }}</strong></td>
                                                    <td>{{ $order->restaurant->name ?? 'N/A' }}</td>
                                                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                                    <td>
                                                        <span
                                                            class="badge 
                                                        @if ($order->status == 'completed') bg-success
                                                        @elseif($order->status == 'cancelled') bg-danger
                                                        @elseif($order->status == 'pending') bg-warning
                                                        @else bg-info @endif">
                                                            {{ ucfirst($order->status) }}
                                                        </span>
                                                    </td>
                                                    <td><strong>${{ number_format($order->total_amount, 2) }}</strong></td>
                                                    <td>
                                                        <a href="#" class="btn btn-sm btn-outline-primary">View
                                                            Details</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <i class="fa fa-shopping-cart fa-3x text-muted mb-3"></i>
                                                        <h5>No orders found</h5>
                                                        <p class="text-muted">This customer hasn't placed any orders yet.
                                                        </p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Loyalty Points Tab -->
                            <div class="tab-pane" id="loyalty" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card bg-gradient-primary text-white">
                                            <div class="card-body text-center">
                                                <h2>{{ $customer->customerProfile->loyalty_points ?? 0 }}</h2>
                                                <p class="mb-0">Current Loyalty Points</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <form action="{{ route('admin.customers.loyalty.update', $customer->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')

                                            <div class="form-group">
                                                <label>Update Loyalty Points</label>
                                                <input type="number" class="form-control" name="points"
                                                    value="{{ $customer->customerProfile->loyalty_points ?? 0 }}"
                                                    min="0" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Reason for Update</label>
                                                <input type="text" class="form-control" name="reason"
                                                    placeholder="e.g., Manual adjustment, Bonus points" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Update Points</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Add Address Modal -->
    <div class="modal fade" id="addAddressModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.customers.addresses.store', $customer->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Address Line 1 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="address_line1" id="address_line1"
                                        minlength="5" maxlength="255" required>
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Address Line 2</label>
                                    <input type="text" class="form-control" name="address_line2" id="address_line2"
                                        maxlength="255">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>City <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="city" id="city"
                                        minlength="2" maxlength="100" pattern="[a-zA-Z\s]+"
                                        title="City must contain only letters and spaces" required>
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>State <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="state" id="state"
                                        minlength="2" maxlength="100" pattern="[a-zA-Z\s]+"
                                        title="State must contain only letters and spaces" required>
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Postal Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="postal_code" id="postal_code"
                                        minlength="4" maxlength="20" pattern="[0-9]+"
                                        title="Postal code must contain only numbers (4-20 digits)" required>
                                    <div class="invalid-feedback" style="display: none;"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Address Type</label>
                                    <select class="form-select" name="address_type">
                                        <option value="home">Home</option>
                                        <option value="work">Work</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Landmark (Optional)</label>
                                    <input type="text" class="form-control" name="landmark" id="landmark"
                                        maxlength="255" placeholder="e.g., Near Central Mall">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Latitude (Optional)</label>
                                    <input type="number" class="form-control" name="latitude" id="latitude"
                                        step="0.00000001" min="-90" max="90" placeholder="e.g., 12.9715987">
                                    <small class="text-muted">Between -90 and 90</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Longitude (Optional)</label>
                                    <input type="number" class="form-control" name="longitude" id="longitude"
                                        step="0.00000001" min="-180" max="180" placeholder="e.g., 77.5945627">
                                    <small class="text-muted">Between -180 and 180</small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_default" value="1"
                                        id="isDefault">
                                    <label class="form-check-label" for="isDefault">
                                        Set as default address
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Address</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Real-time validation for first name and last name (letters only, no spaces)
            $('#first_name, #last_name').on('input', function() {
                this.value = this.value.replace(/[^a-zA-Z]/g, '');
            });

            // Real-time validation for phone (digits only)
            $('#phone').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                // Prevent all zeros
                if (this.value && /^0+$/.test(this.value)) {
                    showError(this, 'Phone number cannot be all zeros');
                } else {
                    clearError(this);
                }
            });

            // Real-time validation for email (.com domain and length)
            $('#email').on('input', function() {
                // Limit to 30 characters
                if (this.value.length > 30) {
                    this.value = this.value.slice(0, 30);
                }

                // Real-time length validation
                if (this.value.length > 0 && this.value.length < 7) {
                    showError(this, 'Email must be at least 7 characters long');
                } else {
                    clearError(this);
                }
            });

            $('#email').on('blur', function() {
                const email = this.value;
                if (email && email.length >= 7 && email.length <= 30) {
                    if (!email.endsWith('.com')) {
                        showError(this, 'Email must end with .com domain');
                    } else {
                        clearError(this);
                    }
                } else if (email && (email.length < 7 || email.length > 30)) {
                    showError(this, 'Email must be between 7 and 30 characters');
                }
            });

            // Real-time validation for city and state (letters and spaces only)
            $('#city, #state').on('input', function() {
                this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
            });

            // Real-time validation for postal code (numbers only)
            $('#postal_code').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Date of birth validation - must be at least 5 years old
            $('#date_of_birth').on('change', function() {
                const selectedDate = new Date(this.value);
                const today = new Date();
                const fiveYearsAgo = new Date(today.getFullYear() - 5, today.getMonth(), today.getDate());

                if (selectedDate > today) {
                    showError(this, 'Date of birth cannot be in the future');
                    this.value = '';
                } else if (selectedDate > fiveYearsAgo) {
                    showError(this, 'You must be at least 5 years old');
                    this.value = '';
                } else {
                    clearError(this);
                }
            });

            // Helper functions for inline errors
            function showError(element, message) {
                const feedbackDiv = $(element).siblings('.invalid-feedback');
                if (feedbackDiv.length) {
                    feedbackDiv.text(message).show();
                    $(element).addClass('is-invalid');
                }
            }

            function clearError(element) {
                const feedbackDiv = $(element).siblings('.invalid-feedback');
                if (feedbackDiv.length) {
                    feedbackDiv.hide();
                    $(element).removeClass('is-invalid');
                }
            }

            // Delete address functionality
            $('.delete-address').on('click', function(e) {
                e.preventDefault();
                var addressId = $(this).data('address-id');
                var card = $(this).closest('.col-md-6');

                Swal.fire({
                    title: 'Delete Address',
                    text: "Are you sure you want to delete this address?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/customers/{{ $customer->id }}/addresses/${addressId}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Deleted!',
                                        response.message,
                                        'success'
                                    );
                                    card.fadeOut(300, function() {
                                        $(this).remove();
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'There was an error deleting the address.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

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
        });
    </script>
@endsection
