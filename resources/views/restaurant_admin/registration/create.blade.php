@extends('layouts.admin')

@section('title', 'Register New Restaurant')

@section('styles')
    <style>
        /* ====== COMPACT GLOBAL TWEAKS ====== */
        .compact-ui .card,
        .compact-ui .box {
            border-radius: 8px;
        }

        .compact-ui .card-header,
        .compact-ui .box-header {
            padding: .5rem .75rem;
        }

        .compact-ui .card-body,
        .compact-ui .box-body,
        .compact-ui .box-footer {
            padding: .75rem;
        }

        .compact-ui .form-group {
            margin-bottom: .5rem;
        }

        .compact-ui label.form-label,
        .compact-ui .form-group>label {
            margin-bottom: .15rem;
            font-weight: 600;
            font-size: .92rem;
        }

        .compact-ui .form-control,
        .compact-ui .form-select,
        .compact-ui .form-control-file,
        .compact-ui textarea {
            padding: .375rem .5rem;
            font-size: .9rem;
            line-height: 1.35;
        }

        .compact-ui .btn {
            padding: .3rem .6rem;
            font-size: .9rem;
            border-radius: .35rem;
        }

        .compact-ui .btn-sm {
            padding: .2rem .45rem;
            font-size: .825rem;
        }

        .compact-ui .alert {
            padding: .4rem .6rem;
            margin-bottom: .5rem;
            font-size: .9rem;
        }

        .compact-ui h3.card-title,
        .compact-ui .box-title {
            font-size: 1.05rem;
            margin: 0;
        }

        .compact-ui h4 {
            font-size: 1rem;
            margin: .25rem 0 .5rem;
        }

        /* ====== BUSINESS HOURS ====== */
        .business-hours-card {
            border: 1px solid #dee2e6;
            border-radius: 6px;
        }

        .table-hours th,
        .table-hours td {
            padding: .4rem .5rem !important;
            vertical-align: middle;
        }

        .table-hours thead th {
            background: #f8f9fa;
            font-size: .9rem;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            font-size: .85rem;
        }

        .status-open .fa {
            color: #198754;
        }

        .status-closed .fa {
            color: #dc3545;
        }

        /* ====== FORM ERROR STYLES ====== */
        .is-invalid {
            border-color: #dc3545 !important;
        }

        .invalid-feedback {
            display: block;
            color: #dc3545;
            font-size: .85rem;
            margin-top: .2rem;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid compact-ui">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fa fa-plus-circle text-success"></i>
                            <span class="ms-1">Restaurant Registration System</span>
                        </h3>
                        <div class="card-tools d-flex gap-2">
                            <a href="{{ route('restaurant-admin.list') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-list"></i> Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body pt-2 pb-2">
                        <p class="text-muted mb-0">Complete restaurant registration with document verification and advanced
                            management features.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Box -->
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border d-flex justify-content-between align-items-center">
                        <h3 class="box-title">Register New Restaurant</h3>
                        <div class="box-tools">
                            {{-- <a href="{{ route('restaurant-admin.list') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-list"></i> Back to List
                            </a> --}}
                        </div>
                    </div>
                    <div class="box-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle"></i> {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                              
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h5 class="mb-2"><i class="fa fa-exclamation-triangle"></i> Please fix the following errors:</h5>
                                <ul class="mb-0 pl-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('restaurant-admin.registration.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- Hidden fields preserved -->
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                            @if (auth()->user()->role === 'tenant_admin')
                                <input type="hidden" name="tenant_id" value="{{ auth()->user()->tenant_id }}">
                                <input type="hidden" name="tenant_selection" value="existing">
                            @endif

                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="restaurant_name">Restaurant Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('restaurant_name') is-invalid @enderror" id="restaurant_name"
                                            name="restaurant_name" value="{{ old('restaurant_name') }}" required>
                                        @error('restaurant_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_person_name">Contact Person Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('contact_person_name') is-invalid @enderror" id="contact_person_name"
                                            name="contact_person_name" value="{{ old('contact_person_name') }}"
                                            placeholder="Enter full name of contact person" required>
                                        @error('contact_person_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                    <!-- Contact details -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                            value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                                            value="{{ old('phone') }}" required>
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @if (auth()->user()->role === 'super_admin')
                                    <div class="row mb-2 mt-1">
                                        <div class="col-12">
                                            <div class="card bg-primary text-white">
                                                <div class="card-header py-2">
                                                    <h5 class="mb-0 text-white">
                                                        <i class="fa fa-user-shield"></i>
                                                        <span class="ms-1">Super Admin Mode: You can create restaurants
                                                            for any tenant</span>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2">
                                            <div class="form-group">
                                                <label class="form-label mb-1"><strong>Restaurant Type</strong> <span
                                                        class="text-danger">*</span></label>
                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <div class="card border-primary shadow-sm small-card h-100"
                                                            style="cursor:pointer" onclick="selectRestaurantType('new')">
                                                            <div class="card-body py-2">
                                                                <input type="radio" class="form-check-input"
                                                                    name="tenant_selection" id="new_independent"
                                                                    value="new"
                                                                    {{ old('tenant_selection', 'new') == 'new' ? 'checked' : '' }}>
                                                                <label class="form-check-label d-block mt-1"
                                                                    for="new_independent">
                                                                    <i
                                                                        class="fa fa-plus-circle fa-lg text-success mb-1"></i>
                                                                    <div class="fw-semibold text-primary mt-1">New
                                                                        Independent Restaurant</div>
                                                                    <small class="text-muted d-block">Creates new franchise
                                                                        automatically</small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="card border-primary shadow-sm small-card h-100"
                                                            style="cursor:pointer"
                                                            onclick="selectRestaurantType('existing')">
                                                            <div class="card-body py-2">
                                                                <input type="radio" class="form-check-input"
                                                                    name="tenant_selection" id="existing_franchise"
                                                                    value="existing"
                                                                    {{ old('tenant_selection') == 'existing' ? 'checked' : '' }}>
                                                                <label class="form-check-label d-block mt-1"
                                                                    for="existing_franchise">
                                                                    <i class="fa fa-building fa-lg text-info mb-1"></i>
                                                                    <div class="fw-semibold text-info mt-1">Add to Existing
                                                                        Franchise</div>
                                                                    <small class="text-muted d-block">Select existing
                                                                        franchise below</small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @error('tenant_selection')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Tenant Details Section (New Independent) --}}
                                <div id="tenant-details-section" style="display: none;">
                                    <div class="row">
                                        <div class="col-12">
                                            <h4 class="mb-2 mt-3">Franchise Owner Details</h4>
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i>
                                                <strong>Note:</strong> These details will be used to create a new franchise
                                                automatically.
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="contact_person">Franchise Owner Name <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('contact_person') is-invalid @enderror" id="contact_person"
                                                    name="contact_person" value="{{ old('contact_person') }}"
                                                    placeholder="Enter franchise owner name">
                                                @error('contact_person')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="tenant_email">Franchise Email <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" class="form-control @error('tenant_email') is-invalid @enderror" id="tenant_email"
                                                    name="tenant_email" value="{{ old('tenant_email') }}"
                                                    placeholder="franchise@example.com">
                                                @error('tenant_email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="tenant_phone">Franchise Phone <span
                                                        class="text-danger">*</span></label>
                                                 <input type="tel" class="form-control @error('tenant_phone') is-invalid @enderror" id="tenant_phone"
                                                    name="tenant_phone" value="{{ old('tenant_phone') }}" required>
                                                @error('tenant_phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Existing Tenant Selection --}}
                                <div id="existing-tenant-section" style="display: none;">
                                    <div class="row">
                                        <div class="col-12">
                                            <h4 class="mb-2 mt-3">Select Existing Franchise</h4>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tenant_id">Choose Franchise <span
                                                        class="text-danger">*</span></label>
                                                <select id="tenant_id" name="tenant_id" class="form-select @error('tenant_id') is-invalid @enderror">
                                                    <option value="">Select Existing Franchise</option>
                                                    @foreach ($tenants as $t)
                                                        <option value="{{ $t->id }}" {{ old('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->tenant_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('tenant_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="alert alert-warning">
                                                <i class="fa fa-exclamation-triangle"></i>
                                                <strong>Existing Franchise:</strong> The restaurant will use the selected
                                                franchise's billing and management settings.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            

                                <!-- Address Information -->
                                <div class="col-12">
                                    <h4 class="mt-2 mb-2">Address Information</h4>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Address <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="state_id">State <span class="text-danger">*</span></label>
                                        <select class="form-control @error('state_id') is-invalid @enderror" id="state_id" name="state_id" required>
                                            <option value="">Select State</option>
                                            @if (isset($states))
                                                @foreach ($states as $state)
                                                    <option value="{{ $state->id }}"
                                                        {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                                        {{ $state->name }}</option>
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
                                        <select class="form-control @error('city_id') is-invalid @enderror" id="city_id" name="city_id" required>
                                            <option value="">Select City</option>
                                            <!-- Cities loaded via AJAX -->
                                        </select>
                                        @error('city_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="postal_code">ZIP Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code"
                                            value="{{ old('postal_code') }}" required>
                                        @error('postal_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Lat/Long -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="latitude">Latitude <span class="text-danger">*</span></label>
                                        <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" id="latitude"
                                            name="latitude" value="{{ old('latitude') }}" required>
                                        @error('latitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="longitude">Longitude <span class="text-danger">*</span></label>
                                        <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" id="longitude"
                                            name="longitude" value="{{ old('longitude') }}" required>
                                        @error('longitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Business Information -->
                                <div class="col-12">
                                    <h4 class="mt-2 mb-2">Business Information</h4>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cuisine_type">Cuisine Type</label>
                                        <input type="text" class="form-control" id="cuisine_type" name="cuisine_type"
                                            value="{{ old('cuisine_type') }}"
                                            placeholder="e.g., Italian, Chinese, Mexican">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="website_url">Website URL</label>
                                        <input type="url" class="form-control" id="website_url" name="website_url"
                                            value="{{ old('website_url') }}">
                                    </div>
                                </div>

                                <!-- Business Hours -->
                                <div class="col-12">
                                    <h4 class="mt-2 mb-2">Business Hours</h4>
                                </div>

                                <div class="col-12">
                                    <div class="card business-hours-card">
                                        <div class="card-header py-2">
                                            <h5 class="mb-0">Daily Operating Hours</h5>
                                        </div>
                                        <div class="card-body py-2">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hours">
                                                    <thead>
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
                                                        @endphp

                                                        @foreach ($days as $key => $day)
                                                            <tr id="row-{{ $key }}">
                                                                <td class="align-middle">
                                                                    <strong>{{ $day }}</strong>
                                                                    <input type="hidden"
                                                                        name="business_hours[{{ $key }}][day]"
                                                                        value="{{ $key }}">
                                                                </td>
                                                                <td class="align-middle">
                                                                    <div
                                                                        class="form-check form-switch d-inline-flex align-items-center gap-2">
                                                                        <input type="checkbox"
                                                                            class="form-check-input day-toggle"
                                                                            id="toggle-{{ $key }}"
                                                                            name="business_hours[{{ $key }}][is_open]"
                                                                            value="1"
                                                                            {{ old("business_hours.{$key}.is_open", 1) ? 'checked' : '' }}
                                                                            onchange="toggleDayHours('{{ $key }}')">
                                                                        <label class="form-check-label mb-0"
                                                                            for="toggle-{{ $key }}">
                                                                            <span class="status-pill status-open open-text"
                                                                                style="display:inline;">
                                                                                <i class="fa fa-check-circle"></i> Open
                                                                            </span>
                                                                            <span
                                                                                class="status-pill status-closed closed-text"
                                                                                style="display:none;">
                                                                                <i class="fa fa-times-circle"></i> Closed
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                                <td class="align-middle">
                                                                    <input type="time" class="form-control time-input"
                                                                        id="opening-{{ $key }}"
                                                                        name="business_hours[{{ $key }}][opening_time]"
                                                                        value="{{ old("business_hours.{$key}.opening_time", '09:00') }}">
                                                                </td>
                                                                <td class="align-middle">
                                                                    <input type="time" class="form-control time-input"
                                                                        id="closing-{{ $key }}"
                                                                        name="business_hours[{{ $key }}][closing_time]"
                                                                        value="{{ old("business_hours.{$key}.closing_time", '22:00') }}">
                                                                </td>
                                                                <td class="align-middle">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-primary copy-btn"
                                                                        onclick="copyToAll('{{ $key }}')"
                                                                        title="Copy to all days">
                                                                        <i class="fa fa-copy"></i> Copy
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="mt-2">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <button type="button" class="btn btn-success btn-sm"
                                                            onclick="openAllDays()">
                                                            <i class="fa fa-check"></i> Open All Days
                                                        </button>
                                                        <button type="button" class="btn btn-warning btn-sm ml-2"
                                                            onclick="closeAllDays()">
                                                            <i class="fa fa-times"></i> Close All Days
                                                        </button>
                                                    </div>
                                                    <div class="col-md-6 text-right">
                                                        <small class="text-muted">
                                                            <i class="fa fa-info-circle"></i>
                                                            Use the toggle to mark days as open/closed. Set times for open
                                                            days.
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @error('business_hours')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Descriptions -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="special_instructions">Special Instructions</label>
                                        <textarea class="form-control" id="special_instructions" name="special_instructions" rows="3">{{ old('special_instructions') }}</textarea>
                                    </div>
                                </div>

                                <!-- Business Configuration -->
                                <div class="col-12">
                                    <h4 class="mt-2 mb-2">Business Configuration</h4>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="delivery_radius_km">Delivery Radius (KM) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('delivery_radius_km') is-invalid @enderror" id="delivery_radius_km"
                                            name="delivery_radius_km" value="{{ old('delivery_radius_km', 10) }}"
                                            min="1" max="50" required>
                                        @error('delivery_radius_km')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="minimum_order_amount">Minimum Order Amount <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control @error('minimum_order_amount') is-invalid @enderror"
                                            id="minimum_order_amount" name="minimum_order_amount"
                                            value="{{ old('minimum_order_amount', 0) }}" min="0" max="10000"
                                            required>
                                        @error('minimum_order_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="base_delivery_fee">Base Delivery Fee <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control @error('base_delivery_fee') is-invalid @enderror" id="base_delivery_fee"
                                            name="base_delivery_fee" value="{{ old('base_delivery_fee', 0) }}"
                                            min="0" max="1000" required>
                                        @error('base_delivery_fee')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="restaurant_commission_percentage">Commission Percentage <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control @error('restaurant_commission_percentage') is-invalid @enderror"
                                            id="restaurant_commission_percentage" name="restaurant_commission_percentage"
                                            value="{{ old('restaurant_commission_percentage', 80) }}" min="0"
                                            max="100" required>
                                        @error('restaurant_commission_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="estimated_delivery_time">Estimated Delivery Time (minutes) <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('estimated_delivery_time') is-invalid @enderror" id="estimated_delivery_time"
                                            name="estimated_delivery_time"
                                            value="{{ old('estimated_delivery_time', 30) }}" min="10"
                                            max="120" required>
                                        @error('estimated_delivery_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tax_percentage">Tax Percentage <span
                                                class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control @error('tax_percentage') is-invalid @enderror" id="tax_percentage"
                                            name="tax_percentage" value="{{ old('tax_percentage', 0) }}" min="0"
                                            max="50" required>
                                        @error('tax_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Image Upload -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="image">Restaurant Image</label>
                                        <input type="file" class="form-control-file" id="image_url" name="image_url"
                                            accept="image/*">
                                        <small class="form-text text-muted">Upload restaurant logo or main image (JPG, PNG,
                                            max 2MB)</small>
                                    </div>
                                </div>

                                <!-- Location Admin -->
                                <div class="col-12" id="location-admin-section">
                                    <h4 class="mt-2 mb-2">Location Admin</h4>
                                    <!-- New independent -->
                                    <div id="location-admin-fields" style="display: none;">
                                        <div class="form-group">
                                            <label for="location_admin_name">Location Admin Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('location_admin_name') is-invalid @enderror" id="location_admin_name"
                                                name="location_admin_name" value="{{ old('location_admin_name') }}"
                                                placeholder="Enter location admin name">
                                            @error('location_admin_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="location_admin_email">Location Admin Email <span
                                                    class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('location_admin_email') is-invalid @enderror" id="location_admin_email"
                                                name="location_admin_email" value="{{ old('location_admin_email') }}"
                                                placeholder="admin@example.com">
                                            @error('location_admin_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="location_admin_phone">Location Admin Phone <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('location_admin_phone') is-invalid @enderror" id="location_admin_phone"
                                                name="location_admin_phone" value="{{ old('location_admin_phone') }}"
                                                placeholder="+91-9876543210">
                                            @error('location_admin_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- Existing franchise -->
                                    <div id="location-admin-dropdown">
                                        <select class="form-control @error('location_admin_id') is-invalid @enderror" id="location_admin_id" name="location_admin_id">
                                            <option value="">Select Location Admin</option>
                                            @foreach ($locationAdmins as $admin)
                                                <option value="{{ $admin->id }}"
                                                    {{ old('location_admin_id') == $admin->id ? 'selected' : '' }}>
                                                    {{ $admin->first_name }} {{ $admin->last_name }}
                                                    ({{ $admin->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('location_admin_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Features -->
                                <div class="col-12">
                                    <h4 class="mt-2 mb-2">Features</h4>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_featured"
                                            name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_featured">Featured Restaurant</label>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_open" name="is_open"
                                            value="1" {{ old('is_open', 1) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_open">Restaurant is Open</label>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                            value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer d-flex justify-content-between align-items-center mt-2">
                                <div>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-save"></i> Register Restaurant
                                    </button>
                                    <a href="{{ route('restaurant-admin.list') }}" class="btn btn-secondary">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
                                </div>
                                <small class="text-muted">All fields validated before submission.</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize business hours functionality
    initializeBusinessHours();

    // Handle franchise type selection for super admin
    const newIndependentRadio = document.getElementById('new_independent');
    const existingFranchiseRadio = document.getElementById('existing_franchise');
    const tenantDetailsSection = document.getElementById('tenant-details-section');
    const existingTenantSection = document.getElementById('existing-tenant-section');
    const locationAdminFields = document.getElementById('location-admin-fields');
    const locationAdminDropdown = document.getElementById('location-admin-dropdown');

    function toggleSections() {
        if (!tenantDetailsSection || !existingTenantSection) return;

        if (newIndependentRadio && newIndependentRadio.checked) {
            tenantDetailsSection.style.display = 'block';
            existingTenantSection.style.display = 'none';

            if (locationAdminFields) locationAdminFields.style.display = 'block';
            if (locationAdminDropdown) locationAdminDropdown.style.display = 'none';

            // Set required fields for new franchise
            setFieldRequired('contact_person', true);
            setFieldRequired('tenant_email', true);
            setFieldRequired('tenant_phone', true);
            setFieldRequired('tenant_id', false);

            setFieldRequired('location_admin_name', true);
            setFieldRequired('location_admin_email', true);
            setFieldRequired('location_admin_phone', true);
            setFieldRequired('location_admin_id', false);

        } else if (existingFranchiseRadio && existingFranchiseRadio.checked) {
            tenantDetailsSection.style.display = 'none';
            existingTenantSection.style.display = 'block';

            if (locationAdminFields) locationAdminFields.style.display = 'none';
            if (locationAdminDropdown) locationAdminDropdown.style.display = 'block';

            setFieldRequired('tenant_id', true);
            setFieldRequired('contact_person', false);
            setFieldRequired('tenant_email', false);
            setFieldRequired('tenant_phone', false);

            setFieldRequired('location_admin_id', true);
            setFieldRequired('location_admin_name', false);
            setFieldRequired('location_admin_email', false);
            setFieldRequired('location_admin_phone', false);

        } else {
            tenantDetailsSection.style.display = 'none';
            existingTenantSection.style.display = 'none';
            if (locationAdminFields) locationAdminFields.style.display = 'none';
            if (locationAdminDropdown) locationAdminDropdown.style.display = 'block';
        }
    }

    function setFieldRequired(fieldId, required) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.required = required;
        }
    }

    window.selectRestaurantType = function(type) {
        if (type === 'new' && newIndependentRadio) newIndependentRadio.checked = true;
        else if (type === 'existing' && existingFranchiseRadio) existingFranchiseRadio.checked = true;
        toggleSections();
    };

    if (newIndependentRadio) newIndependentRadio.addEventListener('change', toggleSections);
    if (existingFranchiseRadio) existingFranchiseRadio.addEventListener('change', toggleSections);

    // Initialize sections on page load
    setTimeout(toggleSections, 100);

    // State/City AJAX loading
    const stateSelect = document.getElementById('state_id');
    const citySelect = document.getElementById('city_id');

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
                    })
                    .catch(error => {
                        console.error('Error loading cities:', error);
                        alert('Error loading cities. Please try again.');
                    });
            }
        });
    }
});

// Business Hours Management
function initializeBusinessHours() {
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    days.forEach(day => {
        toggleDayHours(day);
    });
}

function toggleDayHours(day) {
    const toggle = document.getElementById(`toggle-${day}`);
    const openingInput = document.getElementById(`opening-${day}`);
    const closingInput = document.getElementById(`closing-${day}`);
    const row = document.getElementById(`row-${day}`);
    if (!toggle || !openingInput || !closingInput || !row) return;

    const openText = row.querySelector('.open-text');
    const closedText = row.querySelector('.closed-text');

    if (toggle.checked) {
        openingInput.disabled = false;
        closingInput.disabled = false;
        openingInput.required = true;
        closingInput.required = true;
        row.style.opacity = '1';
        if (openText) openText.style.display = 'inline';
        if (closedText) closedText.style.display = 'none';
    } else {
        openingInput.disabled = true;
        closingInput.disabled = true;
        openingInput.required = false;
        closingInput.required = false;
        row.style.opacity = '.65';
        if (openText) openText.style.display = 'none';
        if (closedText) closedText.style.display = 'inline';
    }
}

function copyToAll(sourceDay) {
    const sourceToggle = document.getElementById(`toggle-${sourceDay}`);
    const sourceOpening = document.getElementById(`opening-${sourceDay}`);
    const sourceClosing = document.getElementById(`closing-${sourceDay}`);
    if (!sourceToggle || !sourceOpening || !sourceClosing) return;

    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    if (confirm(`Copy ${sourceDay}'s hours to all other days?`)) {
        days.forEach(day => {
            if (day !== sourceDay) {
                const toggle = document.getElementById(`toggle-${day}`);
                const opening = document.getElementById(`opening-${day}`);
                const closing = document.getElementById(`closing-${day}`);
                if (toggle && opening && closing) {
                    toggle.checked = sourceToggle.checked;
                    opening.value = sourceOpening.value;
                    closing.value = sourceClosing.value;
                    toggleDayHours(day);
                }
            }
        });
        alert('Hours copied to all days successfully!');
    }
}

function openAllDays() {
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    days.forEach(day => {
        const toggle = document.getElementById(`toggle-${day}`);
        if (toggle) {
            toggle.checked = true;
            toggleDayHours(day);
        }
    });
    alert('All days set to open!');
}

function closeAllDays() {
    const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    if (confirm('Close all days? This will mark the restaurant as closed every day.')) {
        days.forEach(day => {
            const toggle = document.getElementById(`toggle-${day}`);
            if (toggle) {
                toggle.checked = false;
                toggleDayHours(day);
            }
        });
        alert('All days set to closed!');
    }
}
</script>
@endpush