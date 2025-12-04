@extends('layouts.admin')

@section('title', 'Edit Restaurant')

@section('styles')
    <style>
        .is-invalid {
            border-color: #dc3545 !important;
            border-width: 2px !important;
        }

        .invalid-feedback {
            display: block !important;
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }

        .form-control.is-invalid,
        .form-control-file.is-invalid,
        .form-check-input.is-invalid {
            background-color: #fff5f5;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
    </style>
@endsection

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
                            enctype="multipart/form-data" novalidate>
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
                                                    class="form-control {{ $errors->has('restaurant_name') ? 'is-invalid' : '' }}"
                                                    id="restaurant_name" name="restaurant_name"
                                                    value="{{ old('restaurant_name', $restaurant->restaurant_name) }}"
                                                    required>
                                                @if ($errors->has('restaurant_name'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('restaurant_name') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="slug">Slug <span class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control {{ $errors->has('slug') ? 'is-invalid' : '' }}"
                                                    id="slug" name="slug"
                                                    value="{{ old('slug', $restaurant->slug) }}" required>
                                                <small class="form-text text-muted">URL-friendly version of the restaurant
                                                    name</small>
                                                @if ($errors->has('slug'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('slug') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email <span class="text-danger">*</span></label>
                                                <input type="email"
                                                    class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                                    id="email" name="email"
                                                    value="{{ old('email', $restaurant->email) }}" minlength="7"
                                                    maxlength="100"
                                                    pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" required>
                                                <small class="text-muted">Valid email format required (7-100
                                                    characters)</small>
                                                @if ($errors->has('email'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('email') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Phone <span class="text-danger">*</span></label>
                                                <input type="tel"
                                                    class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                                                    id="phone" name="phone"
                                                    value="{{ old('phone', $restaurant->phone) }}" minlength="10"
                                                    maxlength="15" pattern="[1-9][0-9]{9,14}" inputmode="numeric" required>
                                                <small class="text-muted">10-15 digits only, cannot start with 0</small>
                                                @if ($errors->has('phone'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('phone') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="website_url">Website URL</label>
                                                <input type="url"
                                                    class="form-control {{ $errors->has('website_url') ? 'is-invalid' : '' }}"
                                                    id="website_url" name="website_url"
                                                    value="{{ old('website_url', $restaurant->website_url) }}">
                                                @if ($errors->has('website_url'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('website_url') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cuisine_type">Cuisine Type</label>
                                                <input type="text"
                                                    class="form-control {{ $errors->has('cuisine_type') ? 'is-invalid' : '' }}"
                                                    id="cuisine_type" name="cuisine_type"
                                                    value="{{ old('cuisine_type', $restaurant->cuisine_type) }}">
                                                @if ($errors->has('cuisine_type'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('cuisine_type') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" id="description"
                                            name="description" rows="3">{{ old('description', $restaurant->description) }}</textarea>
                                        @if ($errors->has('description'))
                                            <div class="invalid-feedback" style="display: block;">
                                                {{ $errors->first('description') }}</div>
                                        @endif
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
                                                            class="form-control {{ $errors->has('contact_person') ? 'is-invalid' : '' }}"
                                                            id="contact_person" name="contact_person"
                                                            value="{{ old('contact_person', $restaurant->tenant->contact_person ?? '') }}"
                                                            placeholder="Enter franchise owner name">
                                                        <small class="form-text text-muted">Main contact person for the
                                                            franchise</small>
                                                        @if ($errors->has('contact_person'))
                                                            <div class="invalid-feedback" style="display: block;">
                                                                {{ $errors->first('contact_person') }}</div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="tenant_email">Franchise Email <span
                                                                class="text-danger">*</span></label>
                                                        <input type="email"
                                                            class="form-control {{ $errors->has('tenant_email') ? 'is-invalid' : '' }}"
                                                            id="tenant_email" name="tenant_email"
                                                            value="{{ old('tenant_email', $restaurant->tenant->email ?? '') }}"
                                                            placeholder="franchise@example.com">
                                                        <small class="form-text text-muted">Main email for franchise
                                                            communications</small>
                                                        @if ($errors->has('tenant_email'))
                                                            <div class="invalid-feedback" style="display: block;">
                                                                {{ $errors->first('tenant_email') }}</div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="tenant_phone">Franchise Phone <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text"
                                                            class="form-control {{ $errors->has('tenant_phone') ? 'is-invalid' : '' }}"
                                                            id="tenant_phone" name="tenant_phone"
                                                            value="{{ old('tenant_phone', $restaurant->tenant->phone ?? '') }}"
                                                            placeholder="+91-9876543210">
                                                        <small class="form-text text-muted">Main contact number for
                                                            franchise</small>
                                                        @if ($errors->has('tenant_phone'))
                                                            <div class="invalid-feedback" style="display: block;">
                                                                {{ $errors->first('tenant_phone') }}</div>
                                                        @endif
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
                                                            class="form-control {{ $errors->has('tenant_id') ? 'is-invalid' : '' }}"
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
                                                        @if ($errors->has('tenant_id'))
                                                            <div class="invalid-feedback" style="display: block;">
                                                                {{ $errors->first('tenant_id') }}</div>
                                                        @endif
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
                                                    class="form-control {{ $errors->has('contact_person_name') ? 'is-invalid' : '' }}"
                                                    id="contact_person_name" name="contact_person_name"
                                                    value="{{ old('contact_person_name', $restaurant->contact_person_name) }}"
                                                    placeholder="Enter full name of contact person" required>
                                                <small class="form-text text-muted">This person will be the location admin
                                                    for this restaurant</small>
                                                @if ($errors->has('contact_person_name'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('contact_person_name') }}</div>
                                                @endif
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
                                        <textarea class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}" id="address" name="address"
                                            rows="3" minlength="10" maxlength="500" required>{{ old('address', $restaurant->address) }}</textarea>
                                        <small class="text-muted">Minimum 10 characters, maximum 500 characters</small>
                                        @if ($errors->has('address'))
                                            <div class="invalid-feedback" style="display: block;">
                                                {{ $errors->first('address') }}</div>
                                        @endif
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="state_id">State <span class="text-danger">*</span></label>
                                                <select
                                                    class="form-control {{ $errors->has('state_id') ? 'is-invalid' : '' }}"
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
                                                @if ($errors->has('state_id'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('state_id') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="city_id">City <span class="text-danger">*</span></label>
                                                <select
                                                    class="form-control {{ $errors->has('city_id') ? 'is-invalid' : '' }}"
                                                    id="city_id" name="city_id" required>
                                                    <option value="">Select City</option>
                                                    <!-- Cities will be loaded via AJAX based on selected state -->
                                                </select>
                                                @if ($errors->has('city_id'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('city_id') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="postal_code">Postal Code <span
                                                        class="text-danger">*</span></label>
                                                <input type="text"
                                                    class="form-control {{ $errors->has('postal_code') ? 'is-invalid' : '' }}"
                                                    id="postal_code" name="postal_code"
                                                    value="{{ old('postal_code', $restaurant->postal_code) }}"
                                                    minlength="4" maxlength="10" pattern="[0-9A-Za-z\s\-]+" required>
                                                <small class="text-muted">4-10 characters</small>
                                                @if ($errors->has('postal_code'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('postal_code') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="latitude">Latitude <span class="text-danger">*</span></label>
                                                <input type="number" step="any"
                                                    class="form-control {{ $errors->has('latitude') ? 'is-invalid' : '' }}"
                                                    id="latitude" name="latitude"
                                                    value="{{ old('latitude', $restaurant->latitude) }}" min="-90"
                                                    max="90" required>
                                                <small class="text-muted">Between -90 and 90</small>
                                                @if ($errors->has('latitude'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('latitude') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="longitude">Longitude <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" step="any"
                                                    class="form-control {{ $errors->has('longitude') ? 'is-invalid' : '' }}"
                                                    id="longitude" name="longitude"
                                                    value="{{ old('longitude', $restaurant->longitude) }}" min="-180"
                                                    max="180" required>
                                                <small class="text-muted">Between -180 and 180</small>
                                                @if ($errors->has('longitude'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('longitude') }}</div>
                                                @endif
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
                                                    class="form-control {{ $errors->has('minimum_order_amount') ? 'is-invalid' : '' }}"
                                                    id="minimum_order_amount" name="minimum_order_amount"
                                                    value="{{ old('minimum_order_amount', $restaurant->minimum_order_amount) }}"
                                                    min="0" max="10000" required>
                                                <small class="text-muted">0 to 10,000</small>
                                                @if ($errors->has('minimum_order_amount'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('minimum_order_amount') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="base_delivery_fee">Base Delivery Fee ($) <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" step="0.01"
                                                    class="form-control {{ $errors->has('base_delivery_fee') ? 'is-invalid' : '' }}"
                                                    id="base_delivery_fee" name="base_delivery_fee"
                                                    value="{{ old('base_delivery_fee', $restaurant->base_delivery_fee) }}"
                                                    min="0" max="1000" required>
                                                <small class="text-muted">0 to 1,000</small>
                                                @if ($errors->has('base_delivery_fee'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('base_delivery_fee') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="delivery_radius_km">Delivery Radius (km) <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" step="0.1"
                                                    class="form-control {{ $errors->has('delivery_radius_km') ? 'is-invalid' : '' }}"
                                                    id="delivery_radius_km" name="delivery_radius_km"
                                                    value="{{ old('delivery_radius_km', $restaurant->delivery_radius_km) }}"
                                                    min="1" max="50" required>
                                                <small class="text-muted">1 to 50 km</small>
                                                @if ($errors->has('delivery_radius_km'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('delivery_radius_km') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="restaurant_commission_percentage">Commission (%) <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" step="0.01" min="0" max="100"
                                                    class="form-control {{ $errors->has('restaurant_commission_percentage') ? 'is-invalid' : '' }}"
                                                    id="restaurant_commission_percentage"
                                                    name="restaurant_commission_percentage"
                                                    value="{{ old('restaurant_commission_percentage', $restaurant->restaurant_commission_percentage) }}"
                                                    required>
                                                <small class="text-muted">0 to 100%</small>
                                                @if ($errors->has('restaurant_commission_percentage'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('restaurant_commission_percentage') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="tax_percentage">Tax Percentage (%) <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" step="0.01" min="0" max="50"
                                                    class="form-control {{ $errors->has('tax_percentage') ? 'is-invalid' : '' }}"
                                                    id="tax_percentage" name="tax_percentage"
                                                    value="{{ old('tax_percentage', $restaurant->tax_percentage) }}"
                                                    required>
                                                <small class="text-muted">0 to 50%</small>
                                                @if ($errors->has('tax_percentage'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('tax_percentage') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="estimated_delivery_time">Est. Delivery Time (minutes) <span
                                                        class="text-danger">*</span></label>
                                                <input type="number"
                                                    class="form-control {{ $errors->has('estimated_delivery_time') ? 'is-invalid' : '' }}"
                                                    id="estimated_delivery_time" name="estimated_delivery_time"
                                                    value="{{ old('estimated_delivery_time', $restaurant->estimated_delivery_time) }}"
                                                    min="10" max="120" required>
                                                <small class="text-muted">10 to 120 minutes</small>
                                                @if ($errors->has('estimated_delivery_time'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('estimated_delivery_time') }}</div>
                                                @endif
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
                                                <select
                                                    class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}"
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
                                                @if ($errors->has('status'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('status') }}</div>
                                                @endif
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="location_admin_id">Location Admin</label>
                                                <select
                                                    class="form-control {{ $errors->has('location_admin_id') ? 'is-invalid' : '' }}"
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
                                                @if ($errors->has('location_admin_id'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('location_admin_id') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox"
                                                    class="form-check-input {{ $errors->has('is_open') ? 'is-invalid' : '' }}"
                                                    id="is_open" name="is_open" value="1"
                                                    {{ old('is_open', $restaurant->is_open) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_open">Is Open</label>
                                                @if ($errors->has('is_open'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('is_open') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox"
                                                    class="form-check-input {{ $errors->has('accepts_orders') ? 'is-invalid' : '' }}"
                                                    id="accepts_orders" name="accepts_orders" value="1"
                                                    {{ old('accepts_orders', $restaurant->accepts_orders) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="accepts_orders">Accepts
                                                    Orders</label>
                                                @if ($errors->has('accepts_orders'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('accepts_orders') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input type="checkbox"
                                                    class="form-check-input {{ $errors->has('is_featured') ? 'is-invalid' : '' }}"
                                                    id="is_featured" name="is_featured" value="1"
                                                    {{ old('is_featured', $restaurant->is_featured) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_featured">Is Featured</label>
                                                @if ($errors->has('is_featured'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('is_featured') }}</div>
                                                @endif
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
                                                    class="form-control-file {{ $errors->has('image') ? 'is-invalid' : '' }}"
                                                    id="image" name="image" accept="image/*"><br>
                                                <small class="form-text text-muted">Upload a new image to replace the
                                                    current one</small>
                                                @if ($errors->has('image'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('image') }}</div>
                                                @endif
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
                                                    class="form-control-file {{ $errors->has('cover_image') ? 'is-invalid' : '' }}"
                                                    id="cover_image" name="cover_image" accept="image/*"><br>
                                                <small class="form-text text-muted">Upload a new cover image to replace the
                                                    current one</small>
                                                @if ($errors->has('cover_image'))
                                                    <div class="invalid-feedback" style="display: block;">
                                                        {{ $errors->first('cover_image') }}</div>
                                                @endif
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
                                        @if ($errors->has('business_hours'))
                                            <div class="invalid-feedback d-block" style="display: block;">
                                                {{ $errors->first('business_hours') }}</div>
                                        @endif

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
                                        <textarea class="form-control {{ $errors->has('special_instructions') ? 'is-invalid' : '' }}"
                                            id="special_instructions" name="special_instructions" rows="3">{{ old('special_instructions', $restaurant->special_instructions) }}</textarea>
                                        @if ($errors->has('special_instructions'))
                                            <div class="invalid-feedback" style="display: block;">
                                                {{ $errors->first('special_instructions') }}</div>
                                        @endif
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
            // ============================================
            // PREVENT FORM SUBMISSION ON ENTER KEY
            // ============================================
            $('form').on('keypress', function(e) {
                if (e.which === 13 && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    return false;
                }
            });

            // Also prevent on keydown for better coverage
            $('input').on('keydown', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    // Move to next input field instead
                    const inputs = $('input:visible, select:visible, textarea:visible');
                    const currentIndex = inputs.index(this);
                    if (currentIndex < inputs.length - 1) {
                        inputs.eq(currentIndex + 1).focus();
                    }
                    return false;
                }
            });

            // ============================================
            // VALIDATION HELPER FUNCTIONS
            // ============================================
            function showError(input, message) {
                const $input = $(input);
                $input.addClass('is-invalid');
                let $feedback = $input.parent().find('.invalid-feedback');
                if ($feedback.length === 0) {
                    $feedback = $('<div class="invalid-feedback"></div>');
                    $input.after($feedback);
                }
                $feedback.text(message).show();
            }

            function clearError(input) {
                const $input = $(input);
                $input.removeClass('is-invalid');
                $input.parent().find('.invalid-feedback').hide();
            }

            // ============================================
            // RESTAURANT NAME VALIDATION
            // ============================================
            const restaurantNameInput = document.getElementById('restaurant_name');
            if (restaurantNameInput) {
                // Pattern: Only letters, numbers, &, ., ,, ', -, and spaces. No consecutive spaces.
                const restaurantNamePattern = /^(?!.*\s{2,})([A-Za-z0-9&.,'\- ]+)$/;

                restaurantNameInput.addEventListener('input', function() {
                    clearError(this);
                    // Limit to 50 characters
                    if (this.value.length > 50) {
                        this.value = this.value.slice(0, 50);
                    }
                });

                restaurantNameInput.addEventListener('blur', function() {
                    const value = this.value.trim();
                    if (!value) {
                        showError(this, 'Restaurant name is required.');
                    } else if (value.length < 3) {
                        showError(this, 'Restaurant name must be at least 3 characters.');
                    } else if (value.length > 50) {
                        showError(this, 'Restaurant name cannot exceed 50 characters.');
                    } else if (!restaurantNamePattern.test(value)) {
                        showError(this,
                            'Restaurant name contains invalid characters. Only letters, numbers, &, ., \', -, and spaces allowed.'
                        );
                    } else {
                        clearError(this);
                    }
                });
            }

            // ============================================
            // CONTACT PERSON NAME VALIDATION
            // ============================================
            const contactPersonNameInput = document.getElementById('contact_person_name');
            if (contactPersonNameInput) {
                // Pattern: Only letters, &, ., ', -, and spaces. No consecutive spaces.
                const contactPersonPattern = /^(?!.*\s{2,})([A-Za-z&.'\- ]+)$/;

                contactPersonNameInput.addEventListener('input', function() {
                    clearError(this);
                    // Remove numbers and invalid special characters
                    this.value = this.value.replace(/[0-9!@#$%^*()_+=\[\]{}|;:",<>?/\\`~]/g, '');
                if (this.value.length > 100) {
                    this.value = this.value.slice(0, 100);
                }
            });

            contactPersonNameInput.addEventListener('blur', function() {
                const value = this.value.trim();
                if (!value) {
                    showError(this, 'Contact person name is required.');
                } else if (value.length < 3) {
                    showError(this, 'Contact person name must be at least 3 characters.');
                } else if (value.length > 100) {
                    showError(this, 'Contact person name cannot exceed 100 characters.');
                } else if (!contactPersonPattern.test(value)) {
                    showError(this,
                        'Contact person name can only contain letters and basic punctuation.');
                } else {
                    clearError(this);
                }
            });
        }

        // ============================================
        // CUISINE TYPE VALIDATION
        // ============================================
        const cuisineTypeInput = document.getElementById('cuisine_type');
        if (cuisineTypeInput) {
            // Pattern: Only letters, &, ., ,, ', -, and spaces
            const cuisinePattern = /^[A-Za-z&.',\- ]+$/;

            cuisineTypeInput.addEventListener('input', function() {
                clearError(this);
                // Remove numbers and invalid special characters
                this.value = this.value.replace(/[0-9!@#$%^*()_+=\[\]{}|;:"<>?/\\`~]/g, '');
                    if (this.value.length > 100) {
                        this.value = this.value.slice(0, 100);
                    }
                });

                cuisineTypeInput.addEventListener('blur', function() {
                    const value = this.value.trim();
                    if (value && !cuisinePattern.test(value)) {
                        showError(this,
                            'Cuisine type can only contain letters, commas, and basic punctuation.');
                    } else {
                        clearError(this);
                    }
                });
            }

            // ============================================
            // PHONE NUMBER VALIDATION
            // ============================================
            const phoneInput = document.getElementById('phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    clearError(this);
                    const cursorPosition = this.selectionStart;
                    const oldLength = this.value.length;
                    // Remove non-numeric characters
                    this.value = this.value.replace(/[^0-9]/g, '');
                    const newLength = this.value.length;

                    const diff = oldLength - newLength;
                    if (diff > 0) {
                        this.setSelectionRange(cursorPosition - diff, cursorPosition - diff);
                    }

                    // Limit to 15 digits
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

                phoneInput.addEventListener('blur', function() {
                    const value = this.value.trim();
                    const isAllZeros = /^0+$/.test(value);
                    const startsWithZero = /^0/.test(value);
                    const hasNonZero = /[1-9]/.test(value);

                    if (!value) {
                        showError(this, 'Phone number is required.');
                    } else if (value.length < 10) {
                        showError(this, 'Phone number must be at least 10 digits.');
                    } else if (value.length > 15) {
                        showError(this, 'Phone number cannot exceed 15 digits.');
                    } else if (isAllZeros) {
                        showError(this, 'Phone number cannot be all zeros.');
                    } else if (startsWithZero) {
                        showError(this, 'Phone number cannot start with 0.');
                    } else if (!hasNonZero) {
                        showError(this, 'Phone number must contain at least one non-zero digit.');
                    } else {
                        clearError(this);
                    }
                });

                phoneInput.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                    const numericOnly = pastedText.replace(/[^0-9]/g, '').slice(0, 15);
                    if (numericOnly) {
                        this.value = numericOnly;
                        this.dispatchEvent(new Event('input'));
                    }
                });
            }

            // ============================================
            // EMAIL VALIDATION
            // ============================================
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z][a-zA-Z0-9.-]*\.[a-zA-Z]{2,}$/;

            function setupEmailValidation(inputId) {
                const input = document.getElementById(inputId);
                if (!input) return;

                input.addEventListener('input', function() {
                    clearError(this);
                    if (this.value.length > 100) {
                        this.value = this.value.slice(0, 100);
                    }
                });

                input.addEventListener('blur', function() {
                    const value = this.value.trim();
                    if (this.hasAttribute('required') && !value) {
                        showError(this, 'Email is required.');
                    } else if (value && value.length < 7) {
                        showError(this, 'Email must be at least 7 characters.');
                    } else if (value && !emailPattern.test(value)) {
                        showError(this, 'Please enter a valid email. Domain must start with a letter.');
                    } else {
                        clearError(this);
                    }
                });
            }

            setupEmailValidation('email');
            setupEmailValidation('tenant_email');

            // ============================================
            // ADDRESS VALIDATION
            // ============================================
            const addressInput = document.getElementById('address');
            if (addressInput) {
                addressInput.addEventListener('input', function() {
                    clearError(this);
                    if (this.value.length > 500) {
                        this.value = this.value.slice(0, 500);
                    }
                });

                addressInput.addEventListener('blur', function() {
                    const value = this.value.trim();
                    if (!value) {
                        showError(this, 'Address is required.');
                    } else if (value.length < 10) {
                        showError(this, 'Address must be at least 10 characters.');
                    } else if (value.length > 500) {
                        showError(this, 'Address cannot exceed 500 characters.');
                    } else {
                        clearError(this);
                    }
                });
            }

            // ============================================
            // POSTAL CODE VALIDATION
            // ============================================
            const postalCodeInput = document.getElementById('postal_code');
            if (postalCodeInput) {
                const postalCodePattern = /^[0-9A-Za-z\s\-]+$/;

                postalCodeInput.addEventListener('input', function() {
                    clearError(this);
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

                postalCodeInput.addEventListener('blur', function() {
                    const value = this.value.trim();
                    if (!value) {
                        showError(this, 'Postal code is required.');
                    } else if (value.length < 4) {
                        showError(this, 'Postal code must be at least 4 characters.');
                    } else if (value.length > 10) {
                        showError(this, 'Postal code cannot exceed 10 characters.');
                    } else if (!postalCodePattern.test(value)) {
                        showError(this,
                            'Postal code can only contain letters, numbers, spaces, and hyphens.');
                    } else {
                        clearError(this);
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
                    const validChars = pastedText.replace(/[^0-9A-Za-z\s\-]/g, '').slice(0, 10);
                    if (validChars) {
                        this.value = validChars;
                        this.dispatchEvent(new Event('input'));
                    }
                });
            }

            // ============================================
            // LATITUDE VALIDATION
            // ============================================
            const latitudeInput = document.getElementById('latitude');
            if (latitudeInput) {
                latitudeInput.addEventListener('input', function() {
                    clearError(this);
                });

                latitudeInput.addEventListener('blur', function() {
                    const value = parseFloat(this.value);
                    if (this.value === '' || isNaN(value)) {
                        showError(this, 'Latitude is required and must be a number.');
                    } else if (value < -90 || value > 90) {
                        showError(this, 'Latitude must be between -90 and 90.');
                    } else {
                        clearError(this);
                    }
                });
            }

            // ============================================
            // LONGITUDE VALIDATION
            // ============================================
            const longitudeInput = document.getElementById('longitude');
            if (longitudeInput) {
                longitudeInput.addEventListener('input', function() {
                    clearError(this);
                });

                longitudeInput.addEventListener('blur', function() {
                    const value = parseFloat(this.value);
                    if (this.value === '' || isNaN(value)) {
                        showError(this, 'Longitude is required and must be a number.');
                    } else if (value < -180 || value > 180) {
                        showError(this, 'Longitude must be between -180 and 180.');
                    } else {
                        clearError(this);
                    }
                });
            }

            // ============================================
            // NUMERIC FIELD VALIDATION HELPER
            // ============================================
            function setupNumericValidation(inputId, fieldName, min, max, isRequired = true) {
                const input = document.getElementById(inputId);
                if (!input) return;

                input.addEventListener('input', function() {
                    clearError(this);
                });

                input.addEventListener('blur', function() {
                    const value = parseFloat(this.value);
                    if (isRequired && (this.value === '' || isNaN(value))) {
                        showError(this, `${fieldName} is required.`);
                    } else if (!isNaN(value)) {
                        if (value < min) {
                            showError(this, `${fieldName} must be at least ${min}.`);
                        } else if (value > max) {
                            showError(this, `${fieldName} cannot exceed ${max}.`);
                        } else {
                            clearError(this);
                        }
                    }
                });
            }

            // Setup numeric validations
            setupNumericValidation('delivery_radius_km', 'Delivery radius', 1, 50);
            setupNumericValidation('minimum_order_amount', 'Minimum order amount', 0, 10000);
            setupNumericValidation('base_delivery_fee', 'Base delivery fee', 0, 1000);
            setupNumericValidation('estimated_delivery_time', 'Estimated delivery time', 10, 120);
            setupNumericValidation('tax_percentage', 'Tax percentage', 0, 50);
            setupNumericValidation('restaurant_commission_percentage', 'Commission percentage', 0, 100);

            // ============================================
            // DESCRIPTION VALIDATION
            // ============================================
            const descriptionInput = document.getElementById('description');
            if (descriptionInput) {
                descriptionInput.addEventListener('input', function() {
                    clearError(this);
                    if (this.value.length > 2000) {
                        this.value = this.value.slice(0, 2000);
                    }
                });

                descriptionInput.addEventListener('blur', function() {
                    if (this.value.length > 2000) {
                        showError(this, 'Description cannot exceed 2000 characters.');
                    } else {
                        clearError(this);
                    }
                });
            }

            // ============================================
            // SPECIAL INSTRUCTIONS VALIDATION
            // ============================================
            const specialInstructionsInput = document.getElementById('special_instructions');
            if (specialInstructionsInput) {
                specialInstructionsInput.addEventListener('input', function() {
                    clearError(this);
                    if (this.value.length > 1000) {
                        this.value = this.value.slice(0, 1000);
                    }
                });

                specialInstructionsInput.addEventListener('blur', function() {
                    if (this.value.length > 1000) {
                        showError(this, 'Special instructions cannot exceed 1000 characters.');
                    } else {
                        clearError(this);
                    }
                });
            }

            // ============================================
            // WEBSITE URL VALIDATION
            // ============================================
            const websiteInput = document.getElementById('website_url');
            if (websiteInput) {
                websiteInput.addEventListener('input', function() {
                    clearError(this);
                    if (this.value.length > 255) {
                        this.value = this.value.slice(0, 255);
                    }
                });

                websiteInput.addEventListener('blur', function() {
                    const value = this.value.trim();
                    if (value) {
                        // Check if it's a valid URL
                        try {
                            new URL(value);
                            clearError(this);
                        } catch {
                            showError(this, 'Please enter a valid URL (e.g., https://example.com).');
                        }
                    } else {
                        clearError(this);
                    }
                });
            }

            // ============================================
            // TENANT PHONE VALIDATION
            // ============================================
            const tenantPhoneInput = document.getElementById('tenant_phone');
            if (tenantPhoneInput) {
                tenantPhoneInput.addEventListener('input', function() {
                    clearError(this);
                    // Remove non-numeric characters
                    this.value = this.value.replace(/[^0-9]/g, '');
                    if (this.value.length > 20) {
                        this.value = this.value.slice(0, 20);
                    }
                });

                tenantPhoneInput.addEventListener('blur', function() {
                    const value = this.value.trim();
                    if (value) {
                        const isAllZeros = /^0+$/.test(value);
                        if (value.length < 7) {
                            showError(this, 'Tenant phone must be at least 7 digits.');
                        } else if (value.length > 20) {
                            showError(this, 'Tenant phone cannot exceed 20 digits.');
                        } else if (isAllZeros) {
                            showError(this, 'Tenant phone cannot be all zeros.');
                        } else {
                            clearError(this);
                        }
                    }
                });
            }

            // ============================================
            // FRANCHISE CONTACT PERSON VALIDATION
            // ============================================
            const franchiseContactInput = document.getElementById('contact_person');
            if (franchiseContactInput) {
                const contactPattern = /^[A-Za-z&.'\- ]+$/;

                franchiseContactInput.addEventListener('input', function() {
                    clearError(this);
                    // Remove numbers and invalid special characters
                    this.value = this.value.replace(/[0-9!@#$%^*()_+=\[\]{}|;:",<>?/\\`~]/g, '');
                if (this.value.length > 255) {
                    this.value = this.value.slice(0, 255);
                }
            });

            franchiseContactInput.addEventListener('blur', function() {
                const value = this.value.trim();
                if (value && !contactPattern.test(value)) {
                    showError(this,
                        'Contact person name can only contain letters and basic punctuation.');
                } else {
                    clearError(this);
                }
            });
        }

        // ============================================
        // SELECT FIELDS VALIDATION
        // ============================================
        function setupSelectValidation(selectId, fieldName) {
            const select = document.getElementById(selectId);
            if (!select) return;

            select.addEventListener('change', function() {
                if (this.hasAttribute('required') && !this.value) {
                    showError(this, `Please select a ${fieldName}.`);
                } else {
                    clearError(this);
                }
            });
        }

        setupSelectValidation('state_id', 'state');
        setupSelectValidation('city_id', 'city');
        setupSelectValidation('status', 'status');

        // ============================================
        // SCROLL TO FIRST ERROR
        // ============================================
        @if ($errors->any())
            const firstErrorField = document.querySelector('.is-invalid');
            if (firstErrorField) {
                firstErrorField.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                firstErrorField.focus();
            } else {
                const errorAlert = document.querySelector('.alert-danger');
                if (errorAlert) {
                    errorAlert.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }
        @endif

        // ============================================
        // FORM SUBMISSION VALIDATION
        // ============================================
        $('form').on('submit', function(e) {
            let isValid = true;
            let errorMessages = [];
            let firstInvalidField = null;

            // Restaurant Name validation
            const restaurantName = $('#restaurant_name').val().trim();
            const restaurantNamePattern = /^(?!.*\s{2,})([A-Za-z0-9&.,'\- ]+)$/;
            if (!restaurantName) {
                showError('#restaurant_name', 'Restaurant name is required.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#restaurant_name');
            } else if (restaurantName.length < 3 || restaurantName.length > 50) {
                showError('#restaurant_name', 'Restaurant name must be 3-50 characters.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#restaurant_name');
            } else if (!restaurantNamePattern.test(restaurantName)) {
                showError('#restaurant_name', 'Restaurant name contains invalid characters.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#restaurant_name');
            }

            // Contact Person Name validation
            const contactPersonName = $('#contact_person_name').val().trim();
            const contactPersonPattern = /^(?!.*\s{2,})([A-Za-z&.'\- ]+)$/;
            if (!contactPersonName) {
                showError('#contact_person_name', 'Contact person name is required.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#contact_person_name');
            } else if (contactPersonName.length < 3 || contactPersonName.length > 100) {
                showError('#contact_person_name', 'Contact person name must be 3-100 characters.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#contact_person_name');
            } else if (!contactPersonPattern.test(contactPersonName)) {
                showError('#contact_person_name', 'Contact person name can only contain letters.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#contact_person_name');
            }

            // Email validation
            const email = $('#email').val().trim();
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z][a-zA-Z0-9.-]*\.[a-zA-Z]{2,}$/;
            if (!email) {
                showError('#email', 'Email is required.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#email');
            } else if (!emailPattern.test(email)) {
                showError('#email', 'Please enter a valid email. Domain must start with a letter.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#email');
            }

            // Phone validation
            const phone = $('#phone').val().trim();
            const phonePattern = /^[1-9][0-9]{9,14}$/;
            if (!phone) {
                showError('#phone', 'Phone number is required.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#phone');
            } else if (!phonePattern.test(phone)) {
                showError('#phone',
                    'Phone must be 10-15 digits and cannot start with 0 or be all zeros.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#phone');
            }

            // Address validation
            const address = $('#address').val().trim();
            if (!address) {
                showError('#address', 'Address is required.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#address');
            } else if (address.length < 10 || address.length > 500) {
                showError('#address', 'Address must be 10-500 characters.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#address');
            }

            // State validation
            if (!$('#state_id').val()) {
                showError('#state_id', 'Please select a state.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#state_id');
            }

            // City validation
            if (!$('#city_id').val()) {
                showError('#city_id', 'Please select a city.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#city_id');
            }

            // Postal Code validation
            const postalCode = $('#postal_code').val().trim();
            const postalCodePattern = /^[0-9A-Za-z\s\-]+$/;
            if (!postalCode) {
                showError('#postal_code', 'Postal code is required.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#postal_code');
            } else if (postalCode.length < 4 || postalCode.length > 10) {
                showError('#postal_code', 'Postal code must be 4-10 characters.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#postal_code');
            } else if (!postalCodePattern.test(postalCode)) {
                showError('#postal_code', 'Postal code contains invalid characters.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#postal_code');
            }

            // Latitude validation
            const latitude = parseFloat($('#latitude').val());
            if (isNaN(latitude)) {
                showError('#latitude', 'Latitude is required.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#latitude');
            } else if (latitude < -90 || latitude > 90) {
                showError('#latitude', 'Latitude must be between -90 and 90.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#latitude');
            }

            // Longitude validation
            const longitude = parseFloat($('#longitude').val());
            if (isNaN(longitude)) {
                showError('#longitude', 'Longitude is required.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#longitude');
            } else if (longitude < -180 || longitude > 180) {
                showError('#longitude', 'Longitude must be between -180 and 180.');
                isValid = false;
                if (!firstInvalidField) firstInvalidField = $('#longitude');
            }

            // Numeric field validations
            const numericValidations = [{
                    id: '#delivery_radius_km',
                    name: 'Delivery radius',
                    min: 1,
                    max: 50
                },
                {
                    id: '#minimum_order_amount',
                    name: 'Minimum order amount',
                    min: 0,
                    max: 10000
                },
                {
                    id: '#base_delivery_fee',
                    name: 'Base delivery fee',
                    min: 0,
                    max: 1000
                },
                {
                    id: '#estimated_delivery_time',
                    name: 'Estimated delivery time',
                    min: 10,
                    max: 120
                },
                {
                    id: '#tax_percentage',
                    name: 'Tax percentage',
                    min: 0,
                    max: 50
                },
                {
                    id: '#restaurant_commission_percentage',
                    name: 'Commission',
                    min: 0,
                    max: 100
                }
            ];

            numericValidations.forEach(function(field) {
                const value = parseFloat($(field.id).val());
                if (isNaN(value)) {
                    showError(field.id, `${field.name} is required.`);
                    isValid = false;
                    if (!firstInvalidField) firstInvalidField = $(field.id);
                } else if (value < field.min || value > field.max) {
                    showError(field.id,
                        `${field.name} must be between ${field.min} and ${field.max}.`);
                    isValid = false;
                    if (!firstInvalidField) firstInvalidField = $(field.id);
                }
            });

            // Tenant email validation (if visible)
            const tenantEmail = $('#tenant_email').val();
            if (tenantEmail && tenantEmail.trim()) {
                if (!emailPattern.test(tenantEmail.trim())) {
                    showError('#tenant_email', 'Please enter a valid franchise email.');
                    isValid = false;
                    if (!firstInvalidField) firstInvalidField = $('#tenant_email');
                }
            }

            // Tenant phone validation (if visible)
            const tenantPhone = $('#tenant_phone').val();
            if (tenantPhone && tenantPhone.trim()) {
                const tenantPhonePattern = /^(?!0+$)([0-9]{7,20})$/;
                if (!tenantPhonePattern.test(tenantPhone.trim())) {
                    showError('#tenant_phone',
                        'Tenant phone must be 7-20 digits and cannot be all zeros.');
                    isValid = false;
                    if (!firstInvalidField) firstInvalidField = $('#tenant_phone');
                }
            }

            if (!isValid) {
                e.preventDefault();
                if (firstInvalidField) {
                    firstInvalidField[0].scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    setTimeout(function() {
                        firstInvalidField.focus();
                    }, 500);
                }
                return false;
            }

            // Prepare business hours JSON before form submission
            const open = $('input[name="business_hours[open]"]').val();
            const close = $('input[name="business_hours[close]"]').val();
            const businessHours = {
                open: open,
                close: close
            };
            $('#business_hours_json').val(JSON.stringify(businessHours));
        });

        // Auto-generate slug from restaurant name
        $('#slug').on('input', function() {
            let name = $(this).val();
            let slug = name.toLowerCase()
                .replace(/[^a-z0-9 -]/g, '') // Remove invalid chars
                .replace(/\s+/g, '-') // Replace spaces with -
                .replace(/-+/g, '-') // Replace multiple - with single -
                .trim('-'); // Trim - from start and end
            $('#slug').val(slug);
        });

        // ============================================
        // RESTAURANT TYPE SELECTION LOGIC
        // ============================================
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

                const contactPerson = document.getElementById('contact_person');
                const tenantEmail = document.getElementById('tenant_email');
                const tenantPhone = document.getElementById('tenant_phone');
                const tenantId = document.getElementById('tenant_id');

                if (tenantId) tenantId.required = true;
                if (contactPerson) contactPerson.required = false;
                if (tenantEmail) tenantEmail.required = false;
                if (tenantPhone) tenantPhone.required = false;
            } else {
                tenantDetailsSection.style.display = 'none';
                existingTenantSection.style.display = 'none';

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

        if (noChangeRadio) {
            noChangeRadio.addEventListener('change', toggleSections);
        }
        if (newIndependentRadio) {
            newIndependentRadio.addEventListener('change', toggleSections);
        }
        if (existingFranchiseRadio) {
            existingFranchiseRadio.addEventListener('change', toggleSections);
        }

        setTimeout(toggleSections, 100);

        // ============================================
        // STATE/CITY AJAX LOADING
        // ============================================
        const stateSelect = document.getElementById('state_id');
        const citySelect = document.getElementById('city_id');
        const currentCityId = '{{ old('city_id', $restaurant->city) }}';

        if (stateSelect && citySelect) {
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

        // ============================================
        // BUSINESS HOURS MANAGEMENT
        // ============================================
        window.toggleDayHours = function(day) {
            const toggle = document.getElementById(`toggle-${day}`);
            const openingInput = document.getElementById(`opening-${day}`);
            const closingInput = document.getElementById(`closing-${day}`);
            const row = document.getElementById(`row-${day}`);
            const openText = row.querySelector('.open-text');
            const closedText = row.querySelector('.closed-text');

            if (toggle.checked) {
                openingInput.disabled = false;
                closingInput.disabled = false;
                openingInput.required = true;
                closingInput.required = true;
                row.style.opacity = '1';
                openText.style.display = 'inline';
                closedText.style.display = 'none';
            } else {
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

            // Remove invalid class on input change
            $('input, select, textarea').on('input change', function() {
                clearError(this);
            });
        });
    </script>
@endsection
