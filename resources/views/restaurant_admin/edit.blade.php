@extends('layouts.admin')

@section('title', 'Edit Restaurant')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Restaurant: {{ $restaurant->restaurant_name }}</h3>
                        <div class="box-tools float-right">
                            <a href="{{ route('restaurant-admin.show', $restaurant->id) }}" class="btn btn-info btn-sm">
                                <i class="fa fa-eye"></i> View Details
                            </a>
                            <a href="{{ route('restaurant-admin.list') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-list"></i> Back to List
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <!-- Error Messages -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Success Message -->
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('restaurant-admin.update', $restaurant->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Basic Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4 class="card-title">Basic Information</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="restaurant_name">Restaurant Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('restaurant_name') is-invalid @enderror"
                                                    id="restaurant_name" name="restaurant_name"
                                                    value="{{ old('restaurant_name', $restaurant->restaurant_name) }}"
                                                    required>
                                                @error('restaurant_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="slug">Slug <span class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('slug') is-invalid @enderror" id="slug"
                                                    name="slug" value="{{ old('slug', $restaurant->slug) }}" required>
                                                <small class="form-text text-muted">URL-friendly version of the restaurant
                                                    name</small>
                                                @error('slug')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email <span class="text-danger">*</span></label>
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror" id="email"
                                                    name="email" value="{{ old('email', $restaurant->email) }}"
                                                    minlength="7" maxlength="100"
                                                    pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" required>
                                                <small class="text-muted">Valid email format required (7-100
                                                    characters)</small>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Phone <span class="text-danger">*</span></label>
                                                <input type="tel"
                                                    class="form-control @error('phone') is-invalid @enderror" id="phone"
                                                    name="phone" value="{{ old('phone', $restaurant->phone) }}"
                                                    minlength="10" maxlength="15" pattern="[1-9][0-9]{9,14}"
                                                    inputmode="numeric" required>
                                                <small class="text-muted">10-15 digits only, cannot start with 0</small>
                                                @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="website_url">Website URL</label>
                                                <input type="url"
                                                    class="form-control @error('website_url') is-invalid @enderror"
                                                    id="website_url" name="website_url"
                                                    value="{{ old('website_url', $restaurant->website_url) }}">
                                                @error('website_url')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cuisine_type">Cuisine Type</label>
                                                <input type="text"
                                                    class="form-control @error('cuisine_type') is-invalid @enderror"
                                                    id="cuisine_type" name="cuisine_type"
                                                    value="{{ old('cuisine_type', $restaurant->cuisine_type) }}">
                                                @error('cuisine_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                            rows="3">{{ old('description', $restaurant->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Restaurant Type & Franchise Details -->
                            @if (auth()->user()->role === 'super_admin')
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h4 class="card-title">Restaurant Type & Franchise Details</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i>
                                            <strong>Super Admin Mode:</strong> You can modify restaurant type and franchise
                                            details.
                                        </div>

                                        <!-- Restaurant Type Selection -->
                                        <div class="form-group">
                                            <label class="form-label"><strong>Restaurant Type</strong></label>
                                            <div class="row">
                                                <div class="col-md-4 ">
                                                    <div class="card border-secondary" style="cursor: pointer;"
                                                        onclick="selectRestaurantType('no_change')">
                                                        <div class="card-body text-left ">
                                                            <input type="radio" class="form-check-input"
                                                                name="tenant_selection" id="no_change" value="no_change"
                                                                {{ old('tenant_selection', 'no_change') == 'no_change' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="no_change"
                                                                style="cursor: pointer;">
                                                                <i class="fa fa-lock fa-2x text-secondary mb-2"></i>
                                                                <h6 class="text-secondary"><strong>Keep Current
                                                                        Franchise</strong></h6>
                                                                <small
                                                                    class="text-muted">{{ $restaurant->tenant->tenant_name ?? 'No franchise assigned' }}</small>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card border-primary" style="cursor: pointer;"
                                                        onclick="selectRestaurantType('new')">
                                                        <div class="card-body">
                                                            <input type="radio" class="form-check-input"
                                                                name="tenant_selection" id="new_independent"
                                                                value="new"
                                                                {{ old('tenant_selection') == 'new' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="new_independent"
                                                                style="cursor: pointer;">
                                                                <i class="fa fa-plus-circle fa-2x text-success mb-2"></i>
                                                                <h6 class="text-primary"><strong>Update Franchise
                                                                        Details</strong></h6>
                                                                <small class="text-muted">Modify current franchise
                                                                    information</small>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card border-info" style="cursor: pointer;"
                                                        onclick="selectRestaurantType('existing')">
                                                        <div class="card-body">
                                                            <input type="radio" class="form-check-input"
                                                                name="tenant_selection" id="existing_franchise"
                                                                value="existing"
                                                                {{ old('tenant_selection') == 'existing' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="existing_franchise"
                                                                style="cursor: pointer;">
                                                                <i class="fa fa-building fa-2x text-info mb-2"></i>
                                                                <h6 class="text-info"><strong>Move to Different
                                                                        Franchise</strong></h6>
                                                                <small class="text-muted">Select different franchise
                                                                    below</small>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Franchise Owner Details Section -->
                                        <div id="tenant-details-section" style="display: none;">
                                            <h5 class="mb-3 mt-4">Franchise Owner Details</h5>
                                            <div class="alert alert-warning">
                                                <i class="fa fa-exclamation-triangle"></i>
                                                <strong>Note:</strong> These details will update the franchise information.
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="contact_person">Franchise Owner Name <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text"
                                                            class="form-control @error('contact_person') is-invalid @enderror"
                                                            id="contact_person" name="contact_person"
                                                            value="{{ old('contact_person', $restaurant->tenant->contact_person ?? '') }}"
                                                            placeholder="Enter franchise owner name">
                                                        <small class="form-text text-muted">Main contact person for the
                                                            franchise</small>
                                                        @error('contact_person')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="tenant_email">Franchise Email <span
                                                                class="text-danger">*</span></label>
                                                        <input type="email"
                                                            class="form-control @error('tenant_email') is-invalid @enderror"
                                                            id="tenant_email" name="tenant_email"
                                                            value="{{ old('tenant_email', $restaurant->tenant->email ?? '') }}"
                                                            placeholder="franchise@example.com">
                                                        <small class="form-text text-muted">Main email for franchise
                                                            communications</small>
                                                        @error('tenant_email')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="tenant_phone">Franchise Phone <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text"
                                                            class="form-control @error('tenant_phone') is-invalid @enderror"
                                                            id="tenant_phone" name="tenant_phone"
                                                            value="{{ old('tenant_phone', $restaurant->tenant->phone ?? '') }}"
                                                            placeholder="+91-9876543210">
                                                        <small class="form-text text-muted">Main contact number for
                                                            franchise</small>
                                                        @error('tenant_phone')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Existing Franchise Selection -->
                                        <div id="existing-tenant-section" style="display: none;">
                                            <h5 class="mb-3 mt-4">Select Existing Franchise</h5>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="tenant_id">Choose Franchise <span
                                                                class="text-danger">*</span></label>
                                                        <select
                                                            class="form-control @error('tenant_id') is-invalid @enderror"
                                                            id="tenant_id" name="tenant_id">
                                                            <option value="">Select Existing Franchise</option>
                                                            @if (isset($tenants) && $tenants->count() > 0)
                                                                @foreach ($tenants as $tenant)
                                                                    <option value="{{ $tenant->id }}"
                                                                        {{ old('tenant_id', $restaurant->tenant_id) == $tenant->id ? 'selected' : '' }}>
                                                                        {{ $tenant->tenant_name }}
                                                                        ({{ $tenant->email ?? 'No Email' }})
                                                                    </option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <small class="form-text text-muted">Restaurant will be moved under
                                                            this franchise</small>
                                                        @error('tenant_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="alert alert-info">
                                                        <i class="fa fa-info-circle"></i>
                                                        <strong>Current Franchise:</strong>
                                                        {{ $restaurant->tenant->tenant_name ?? 'None' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Contact Person Name for Restaurant -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4 class="card-title">Restaurant Contact Details</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="contact_person_name">Contact Person Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('contact_person_name') is-invalid @enderror"
                                                    id="contact_person_name" name="contact_person_name"
                                                    value="{{ old('contact_person_name', $restaurant->contact_person_name) }}"
                                                    placeholder="Enter full name of contact person" required>
                                                <small class="form-text text-muted">This person will be the location admin
                                                    for this restaurant</small>
                                                @error('contact_person_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4 class="card-title">Location Information</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="address">Address <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3"
                                            minlength="10" maxlength="500" required>{{ old('address', $restaurant->address) }}</textarea>
                                        <small class="text-muted">Minimum 10 characters, maximum 500 characters</small>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="state_id">State <span class="text-danger">*</span></label>
                                                <select class="form-control @error('state_id') is-invalid @enderror"
                                                    id="state_id" name="state_id" required>
                                                    <option value="">Select State</option>
                                                    @if (isset($states))
                                                        @foreach ($states as $state)
                                                            <option value="{{ $state->id }}"
                                                                {{ old('state_id', $restaurant->state) == $state->id ? 'selected' : '' }}>
                                                                {{ $state->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('state_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="city_id">City <span class="text-danger">*</span></label>
                                                <select class="form-control @error('city_id') is-invalid @enderror"
                                                    id="city_id" name="city_id" required>
                                                    <option value="">Select City</option>
                                                    <!-- Cities will be loaded via AJAX based on selected state -->
                                                </select>
                                                @error('city_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="postal_code">Postal Code <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('postal_code') is-invalid @enderror"
                                                    id="postal_code" name="postal_code"
                                                    value="{{ old('postal_code', $restaurant->postal_code) }}"
                                                    minlength="4" maxlength="10" pattern="[0-9A-Za-z\s\-]+" required>
                                                <small class="text-muted">4-10 characters</small>
                                                @error('postal_code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="latitude">Latitude <span class="text-danger">*</span></label>
                                                <input type="number" step="any"
                                                    class="form-control @error('latitude') is-invalid @enderror"
                                                    id="latitude" name="latitude"
                                                    value="{{ old('latitude', $restaurant->latitude) }}" min="-90"
                                                    max="90" required>
                                                <small class="text-muted">Between -90 and 90</small>
                                                @error('latitude')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="longitude">Longitude <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" step="any"
                                                    class="form-control @error('longitude') is-invalid @enderror"
                                                    id="longitude" name="longitude"
                                                    value="{{ old('longitude', $restaurant->longitude) }}" min="-180"
                                                    max="180" required>
                                                <small class="text-muted">Between -180 and 180</small>
                                                @error('longitude')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Business Configuration -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4 class="card-title">Business Configuration</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="minimum_order_amount">Minimum Order Amount ($) <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" step="0.01"
                                                    class="form-control @error('minimum_order_amount') is-invalid @enderror"
                                                    id="minimum_order_amount" name="minimum_order_amount"
                                                    value="{{ old('minimum_order_amount', $restaurant->minimum_order_amount) }}"
                                                    min="0" max="10000" required>
                                                <small class="text-muted">0 to 10,000</small>
                                                @error('minimum_order_amount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="base_delivery_fee">Base Delivery Fee ($) <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" step="0.01"
                                                    class="form-control @error('base_delivery_fee') is-invalid @enderror"
                                                    id="base_delivery_fee" name="base_delivery_fee"
                                                    value="{{ old('base_delivery_fee', $restaurant->base_delivery_fee) }}"
                                                    min="0" max="1000" required>
                                                <small class="text-muted">0 to 1,000</small>
                                                @error('base_delivery_fee')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="delivery_radius_km">Delivery Radius (km) <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" step="0.1"
                                                    class="form-control @error('delivery_radius_km') is-invalid @enderror"
                                                    id="delivery_radius_km" name="delivery_radius_km"
                                                    value="{{ old('delivery_radius_km', $restaurant->delivery_radius_km) }}"
                                                    min="1" max="50" required>
                                                <small class="text-muted">1 to 50 km</small>
                                                @error('delivery_radius_km')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="restaurant_commission_percentage">Commission (%) <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" step="0.01" min="0" max="100"
                                                    class="form-control @error('restaurant_commission_percentage') is-invalid @enderror"
                                                    id="restaurant_commission_percentage"
                                                    name="restaurant_commission_percentage"
                                                    value="{{ old('restaurant_commission_percentage', $restaurant->restaurant_commission_percentage) }}"
                                                    required>
                                                <small class="text-muted">0 to 100%</small>
                                                @error('restaurant_commission_percentage')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="tax_percentage">Tax Percentage (%) <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" step="0.01" min="0" max="50"
                                                    class="form-control @error('tax_percentage') is-invalid @enderror"
                                                    id="tax_percentage" name="tax_percentage"
                                                    value="{{ old('tax_percentage', $restaurant->tax_percentage) }}"
                                                    required>
                                                <small class="text-muted">0 to 50%</small>
                                                @error('tax_percentage')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="estimated_delivery_time">Est. Delivery Time (minutes) <span
                                                        class="text-danger">*</span></label>
                                                <input type="number"
                                                    class="form-control @error('estimated_delivery_time') is-invalid @enderror"
                                                    id="estimated_delivery_time" name="estimated_delivery_time"
                                                    value="{{ old('estimated_delivery_time', $restaurant->estimated_delivery_time) }}"
                                                    min="10" max="120" required>
                                                <small class="text-muted">10 to 120 minutes</small>
                                                @error('estimated_delivery_time')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status & Settings -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4 class="card-title">Status & Settings</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="status">Status <span class="text-danger">*</span></label>
                                                <select class="form-control @error('status') is-invalid @enderror"
                                                    id="status" name="status" required>
                                                    <option value="">Select Status</option>
                                                    <option value="pending"
                                                        {{ old('status', $restaurant->status) == 'pending' ? 'selected' : '' }}>
                                                        Pending</option>
                                                    <option value="approved"
                                                        {{ old('status', $restaurant->status) == 'approved' ? 'selected' : '' }}>
                                                        Approved</option>
                                                    <option value="rejected"
                                                        {{ old('status', $restaurant->status) == 'rejected' ? 'selected' : '' }}>
                                                        Rejected</option>
                                                    <option value="suspended"
                                                        {{ old('status', $restaurant->status) == 'suspended' ? 'selected' : '' }}>
                                                        Suspended</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="location_admin_id">Location Admin</label>
                                                <select
                                                    class="form-control @error('location_admin_id') is-invalid @enderror"
                                                    id="location_admin_id" name="location_admin_id">
                                                    <option value="">No Location Admin</option>
                                                    @if (isset($locationAdmins))
                                                        @foreach ($locationAdmins as $admin)
                                                            <option value="{{ $admin->id }}"
                                                                {{ old('location_admin_id', $restaurant->location_admin_id) == $admin->id ? 'selected' : '' }}>
                                                                {{ $admin->first_name }} {{ $admin->last_name }}
                                                                ({{ $admin->email }})
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('location_admin_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox"
                                                    class="form-check-input @error('is_open') is-invalid @enderror"
                                                    id="is_open" name="is_open" value="1"
                                                    {{ old('is_open', $restaurant->is_open) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_open">Is Open</label>
                                                @error('is_open')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox"
                                                    class="form-check-input @error('accepts_orders') is-invalid @enderror"
                                                    id="accepts_orders" name="accepts_orders" value="1"
                                                    {{ old('accepts_orders', $restaurant->accepts_orders) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="accepts_orders">Accepts
                                                    Orders</label>
                                                @error('accepts_orders')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox"
                                                    class="form-check-input @error('is_featured') is-invalid @enderror"
                                                    id="is_featured" name="is_featured" value="1"
                                                    {{ old('is_featured', $restaurant->is_featured) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_featured">Is Featured</label>
                                                @error('is_featured')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Images -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4 class="card-title">Images</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="image">Restaurant Image</label>
                                                @if ($restaurant->image_url)
                                                    <div class="mb-2">
                                                        <img src="{{ asset('storage/' . $restaurant->image_url) }}"
                                                            alt="Current Image" class="img-thumbnail"
                                                            style="max-height: 150px;">
                                                        <p class="text-muted small">Current restaurant image</p>
                                                    </div>
                                                @endif
                                                <input type="file"
                                                    class="form-control-file @error('image') is-invalid @enderror"
                                                    id="image" name="image" accept="image/*"><br>
                                                <small class="form-text text-muted">Upload a new image to replace the
                                                    current one</small>
                                                @error('image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cover_image">Cover Image</label>
                                                @if ($restaurant->cover_image_url)
                                                    <div class="mb-2">
                                                        <img src="{{ asset('storage/' . $restaurant->cover_image_url) }}"
                                                            alt="Current Cover" class="img-thumbnail"
                                                            style="max-height: 150px;">
                                                        <p class="text-muted small">Current cover image</p>
                                                    </div>
                                                @endif
                                                <input type="file"
                                                    class="form-control-file @error('cover_image') is-invalid @enderror"
                                                    id="cover_image" name="cover_image" accept="image/*"><br>
                                                <small class="form-text text-muted">Upload a new cover image to replace the
                                                    current one</small>
                                                @error('cover_image')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h4 class="card-title">Additional Information</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="business_hours">Business Hours</label>
                                        @error('business_hours')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror

                                        <div class="card mt-2">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th width="20%">Day</th>
                                                                <th width="15%">Status</th>
                                                                <th width="25%">Opening Time</th>
                                                                <th width="25%">Closing Time</th>
                                                                <th width="15%">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $days = [
                                                                    'monday' => 'Monday',
                                                                    'tuesday' => 'Tuesday',
                                                                    'wednesday' => 'Wednesday',
                                                                    'thursday' => 'Thursday',
                                                                    'friday' => 'Friday',
                                                                    'saturday' => 'Saturday',
                                                                    'sunday' => 'Sunday',
                                                                ];

                                                                // Parse existing business hours
                                                                $existingHours = [];
                                                                if ($restaurant->business_hours) {
                                                                    if (is_string($restaurant->business_hours)) {
                                                                        $decoded = json_decode(
                                                                            $restaurant->business_hours,
                                                                            true,
                                                                        );
                                                                        $existingHours = $decoded ?: [];
                                                                    } else {
                                                                        $existingHours = $restaurant->business_hours;
                                                                    }
                                                                }
                                                            @endphp

                                                            @foreach ($days as $key => $day)
                                                                @php
                                                                    $dayData = $existingHours[$key] ?? [];
                                                                    $isOpen = $dayData['is_open'] ?? true;
                                                                    $openTime = $dayData['opening_time'] ?? '09:00';
                                                                    $closeTime = $dayData['closing_time'] ?? '22:00';
                                                                @endphp
                                                                <tr id="row-{{ $key }}">
                                                                    <td class="align-middle">
                                                                        <strong>{{ $day }}</strong>
                                                                        <input type="hidden"
                                                                            name="business_hours[{{ $key }}][day]"
                                                                            value="{{ $key }}">
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <div class="form-check form-switch">
                                                                            <input type="checkbox"
                                                                                class="form-check-input day-toggle"
                                                                                id="toggle-{{ $key }}"
                                                                                name="business_hours[{{ $key }}][is_open]"
                                                                                value="1"
                                                                                {{ old("business_hours.{$key}.is_open", $isOpen) ? 'checked' : '' }}
                                                                                onchange="toggleDayHours('{{ $key }}')">
                                                                            <label class="form-check-label"
                                                                                for="toggle-{{ $key }}">
                                                                                <span class="open-text">Open</span>
                                                                                <span class="closed-text"
                                                                                    style="display: none;">Closed</span>
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <input type="time"
                                                                            class="form-control form-control-sm time-input"
                                                                            id="opening-{{ $key }}"
                                                                            name="business_hours[{{ $key }}][opening_time]"
                                                                            value="{{ old("business_hours.{$key}.opening_time", $openTime) }}">
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <input type="time"
                                                                            class="form-control form-control-sm time-input"
                                                                            id="closing-{{ $key }}"
                                                                            name="business_hours[{{ $key }}][closing_time]"
                                                                            value="{{ old("business_hours.{$key}.closing_time", $closeTime) }}">
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-outline-primary copy-btn"
                                                                            onclick="copyToAll('{{ $key }}')"
                                                                            title="Copy to all days">
                                                                            <i class="fa fa-copy"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <button type="button" class="btn btn-success btn-sm"
                                                                onclick="openAllDays()">
                                                                <i class="fa fa-check"></i> Open All
                                                            </button>
                                                            <button type="button" class="btn btn-warning btn-sm ml-2"
                                                                onclick="closeAllDays()">
                                                                <i class="fa fa-times"></i> Close All
                                                            </button>
                                                        </div>
                                                        <div class="col-md-6 text-right">
                                                            <small class="text-muted">
                                                                <i class="fa fa-info-circle"></i> Toggle days open/closed
                                                                and set operating hours
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="special_instructions">Special Instructions</label>
                                        <textarea class="form-control @error('special_instructions') is-invalid @enderror" id="special_instructions"
                                            name="special_instructions" rows="3">{{ old('special_instructions', $restaurant->special_instructions) }}</textarea>
                                        @error('special_instructions')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('restaurant-admin.show', $restaurant->id) }}"
                                        class="btn btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Back to Details
                                    </a>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Update Restaurant
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Scroll to error alert if validation errors exist
            @if ($errors->any())
                const errorAlert = document.querySelector('.alert-danger');
                if (errorAlert) {
                    errorAlert.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            @endif

            // Prepare business hours JSON before form submission
            $('form').submit(function() {
                const open = $('input[name="business_hours[open]"]').val();
                const close = $('input[name="business_hours[close]"]').val();

                const businessHours = {
                    open: open,
                    close: close
                };

                $('#business_hours_json').val(JSON.stringify(businessHours));
            });

            // Auto-generate slug from restaurant name
            $('#restaurant_name').on('input', function() {
                let name = $(this).val();
                let slug = name.toLowerCase()
                    .replace(/[^a-z0-9 -]/g, '') // Remove invalid chars
                    .replace(/\s+/g, '-') // Replace spaces with -
                    .replace(/-+/g, '-') // Replace multiple - with single -
                    .trim('-'); // Trim - from start and end
                $('#slug').val(slug);
            });

            // Restaurant Type Selection Logic
            const noChangeRadio = document.getElementById('no_change');
            const newIndependentRadio = document.getElementById('new_independent');
            const existingFranchiseRadio = document.getElementById('existing_franchise');
            const tenantDetailsSection = document.getElementById('tenant-details-section');
            const existingTenantSection = document.getElementById('existing-tenant-section');

            function toggleSections() {
                if (!tenantDetailsSection || !existingTenantSection) {
                    return;
                }

                if (newIndependentRadio && newIndependentRadio.checked) {
                    tenantDetailsSection.style.display = 'block';
                    existingTenantSection.style.display = 'none';

                    // Make tenant fields optional for updates
                    const contactPerson = document.getElementById('contact_person');
                    const tenantEmail = document.getElementById('tenant_email');
                    const tenantPhone = document.getElementById('tenant_phone');
                    const tenantId = document.getElementById('tenant_id');

                    if (contactPerson) contactPerson.required = false;
                    if (tenantEmail) tenantEmail.required = false;
                    if (tenantPhone) tenantPhone.required = false;
                    if (tenantId) tenantId.required = false;

                } else if (existingFranchiseRadio && existingFranchiseRadio.checked) {
                    tenantDetailsSection.style.display = 'none';
                    existingTenantSection.style.display = 'block';

                    // Make tenant selection required
                    const contactPerson = document.getElementById('contact_person');
                    const tenantEmail = document.getElementById('tenant_email');
                    const tenantPhone = document.getElementById('tenant_phone');
                    const tenantId = document.getElementById('tenant_id');

                    if (tenantId) tenantId.required = true;
                    if (contactPerson) contactPerson.required = false;
                    if (tenantEmail) tenantEmail.required = false;
                    if (tenantPhone) tenantPhone.required = false;
                } else {
                    // No change selected - hide both sections
                    tenantDetailsSection.style.display = 'none';
                    existingTenantSection.style.display = 'none';

                    // Make all tenant fields optional
                    const contactPerson = document.getElementById('contact_person');
                    const tenantEmail = document.getElementById('tenant_email');
                    const tenantPhone = document.getElementById('tenant_phone');
                    const tenantId = document.getElementById('tenant_id');

                    if (contactPerson) contactPerson.required = false;
                    if (tenantEmail) tenantEmail.required = false;
                    if (tenantPhone) tenantPhone.required = false;
                    if (tenantId) tenantId.required = false;
                }
            }

            // Function for card click selection
            window.selectRestaurantType = function(type) {
                if (type === 'no_change' && noChangeRadio) {
                    noChangeRadio.checked = true;
                } else if (type === 'new' && newIndependentRadio) {
                    newIndependentRadio.checked = true;
                } else if (type === 'existing' && existingFranchiseRadio) {
                    existingFranchiseRadio.checked = true;
                }
                toggleSections();
            };

            // Event listeners for radio buttons
            if (noChangeRadio) {
                noChangeRadio.addEventListener('change', toggleSections);
            }
            if (newIndependentRadio) {
                newIndependentRadio.addEventListener('change', toggleSections);
            }
            if (existingFranchiseRadio) {
                existingFranchiseRadio.addEventListener('change', toggleSections);
            }

            // Initialize on page load
            setTimeout(toggleSections, 100);

            // State/City AJAX loading
            const stateSelect = document.getElementById('state_id');
            const citySelect = document.getElementById('city_id');
            const currentCityId = '{{ old('city_id', $restaurant->city) }}';

            if (stateSelect && citySelect) {
                // Load cities when state changes
                stateSelect.addEventListener('change', function() {
                    const stateId = this.value;
                    citySelect.innerHTML = '<option value="">Select City</option>';

                    if (stateId) {
                        fetch(`/admin/get-cities/${stateId}`)
                            .then(response => response.json())
                            .then(cities => {
                                cities.forEach(city => {
                                    const option = document.createElement('option');
                                    option.value = city.id;
                                    option.textContent = city.name;
                                    citySelect.appendChild(option);
                                });

                                // Restore selected city if editing
                                if (currentCityId) {
                                    citySelect.value = currentCityId;
                                }
                            })
                            .catch(error => {
                                console.error('Error loading cities:', error);
                                alert('Error loading cities. Please try again.');
                            });
                    }
                });

                // Load cities on page load if state is already selected
                if (stateSelect.value) {
                    const stateId = stateSelect.value;
                    fetch(`/admin/get-cities/${stateId}`)
                        .then(response => response.json())
                        .then(cities => {
                            citySelect.innerHTML = '<option value="">Select City</option>';
                            cities.forEach(city => {
                                const option = document.createElement('option');
                                option.value = city.id;
                                option.textContent = city.name;
                                if (currentCityId == city.id) {
                                    option.selected = true;
                                }
                                citySelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error loading cities:', error);
                        });
                }
            }

            // Phone number validation - only allow numeric input
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    const cursorPosition = this.selectionStart;
                    const oldLength = this.value.length;
                    this.value = this.value.replace(/[^0-9]/g, '');
                    const newLength = this.value.length;

                    const diff = oldLength - newLength;
                    if (diff > 0) {
                        this.setSelectionRange(cursorPosition - diff, cursorPosition - diff);
                    }

                    if (this.value.length > 15) {
                        this.value = this.value.slice(0, 15);
                    }
                });

                phoneInput.addEventListener('keypress', function(e) {
                    const char = String.fromCharCode(e.which);
                    if (!/[0-9]/.test(char)) {
                        e.preventDefault();
                        return false;
                    }
                });

                phoneInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                    const numericOnly = pastedText.replace(/[^0-9]/g, '');
                    if (numericOnly) {
                        const currentValue = this.value;
                        const cursorPosition = this.selectionStart;
                        this.value = currentValue.substring(0, cursorPosition) + numericOnly + currentValue
                            .substring(this.selectionEnd);
                        this.dispatchEvent(new Event('input'));
                    }
                });
            }

            // Postal code validation - alphanumeric, space, hyphen only
            const postalCodeInput = document.getElementById('postal_code');
            if (postalCodeInput) {
                postalCodeInput.addEventListener('input', function() {
                    const cursorPosition = this.selectionStart;
                    const oldLength = this.value.length;
                    this.value = this.value.replace(/[^0-9A-Za-z\s\-]/g, '');
                    const newLength = this.value.length;

                    const diff = oldLength - newLength;
                    if (diff > 0) {
                        this.setSelectionRange(cursorPosition - diff, cursorPosition - diff);
                    }

                    if (this.value.length > 10) {
                        this.value = this.value.slice(0, 10);
                    }
                });

                postalCodeInput.addEventListener('keypress', function(e) {
                    const char = String.fromCharCode(e.which);
                    if (!/[0-9A-Za-z\s\-]/.test(char)) {
                        e.preventDefault();
                        return false;
                    }
                });

                postalCodeInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                    const validChars = pastedText.replace(/[^0-9A-Za-z\s\-]/g, '');
                    if (validChars) {
                        const currentValue = this.value;
                        const cursorPosition = this.selectionStart;
                        this.value = currentValue.substring(0, cursorPosition) + validChars + currentValue
                            .substring(this.selectionEnd);
                        this.dispatchEvent(new Event('input'));
                    }
                });
            }

            // Form validation
            $('form').on('submit', function(e) {
                let isValid = true;

                // Check required fields
                $('input[required], select[required]').each(function() {
                    if (!$(this).val()) {
                        $(this).addClass('is-invalid');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                }
            });

            // Remove invalid class on input
            $('input, select, textarea').on('input change', function() {
                $(this).removeClass('is-invalid');
            });

            // Business Hours Management Functions
            window.toggleDayHours = function(day) {
                const toggle = document.getElementById(`toggle-${day}`);
                const openingInput = document.getElementById(`opening-${day}`);
                const closingInput = document.getElementById(`closing-${day}`);
                const row = document.getElementById(`row-${day}`);
                const openText = row.querySelector('.open-text');
                const closedText = row.querySelector('.closed-text');

                if (toggle.checked) {
                    // Day is open
                    openingInput.disabled = false;
                    closingInput.disabled = false;
                    openingInput.required = true;
                    closingInput.required = true;
                    row.style.opacity = '1';
                    openText.style.display = 'inline';
                    closedText.style.display = 'none';
                } else {
                    // Day is closed
                    openingInput.disabled = true;
                    closingInput.disabled = true;
                    openingInput.required = false;
                    closingInput.required = false;
                    row.style.opacity = '0.6';
                    openText.style.display = 'none';
                    closedText.style.display = 'inline';
                }
            };

            window.copyToAll = function(sourceDay) {
                const sourceToggle = document.getElementById(`toggle-${sourceDay}`);
                const sourceOpening = document.getElementById(`opening-${sourceDay}`);
                const sourceClosing = document.getElementById(`closing-${sourceDay}`);

                const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

                if (confirm(`Copy ${sourceDay}'s hours to all other days?`)) {
                    days.forEach(day => {
                        if (day !== sourceDay) {
                            const toggle = document.getElementById(`toggle-${day}`);
                            const opening = document.getElementById(`opening-${day}`);
                            const closing = document.getElementById(`closing-${day}`);

                            toggle.checked = sourceToggle.checked;
                            opening.value = sourceOpening.value;
                            closing.value = sourceClosing.value;

                            toggleDayHours(day);
                        }
                    });
                }
            };

            window.openAllDays = function() {
                const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                days.forEach(day => {
                    const toggle = document.getElementById(`toggle-${day}`);
                    toggle.checked = true;
                    toggleDayHours(day);
                });
            };

            window.closeAllDays = function() {
                const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                if (confirm('Close all days?')) {
                    days.forEach(day => {
                        const toggle = document.getElementById(`toggle-${day}`);
                        toggle.checked = false;
                        toggleDayHours(day);
                    });
                }
            };

            // Initialize business hours on page load
            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            days.forEach(day => {
                toggleDayHours(day);
            });
        });
    </script>
@endsection
