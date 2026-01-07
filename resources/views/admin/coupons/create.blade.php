@extends('layouts.admin')

@section('content')
    <div class="content-header m-2">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Add Coupon</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    <i class="mdi mdi-home-outline"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.coupons.index') }}">Coupons</a>
                            </li>
                            <li class="breadcrumb-item active">Add</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="mdi mdi-arrow-left"></i> Back
                </a>
            </div>

        </div>
    </div>
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.coupons.store') }}">
                    @csrf

                    <div class="row">
                        {{-- Basic Information --}}
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <h6 class="text-muted mb-3 pb-2 border-bottom">Basic Information</h6>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Code <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="code" value="{{ old('code') }}"
                                        class="form-control form-control-sm @error('code') is-invalid @enderror"
                                        placeholder="e.g., SAVE20" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Title <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="title" value="{{ old('title') }}"
                                        class="form-control form-control-sm @error('title') is-invalid @enderror"
                                        placeholder="Coupon title" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>


                            </div>
                        </div>

                        {{-- Discount Details --}}
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <h6 class="text-muted mb-3 pb-2 border-bottom">Discount Details</h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-semibold">Discount Type <span
                                                class="text-danger">*</span></label>
                                        <select name="discount_type"
                                            class="form-select form-select-sm @error('discount_type') is-invalid @enderror"
                                            required>
                                            <option value="">-- Select --</option>
                                            <option value="flat" {{ old('discount_type') == 'flat' ? 'selected' : '' }}>
                                                Flat Amount</option>
                                            <option value="percentage"
                                                {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage
                                            </option>
                                        </select>
                                        @error('discount_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-semibold">Discount Value <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="discount_value" value="{{ old('discount_value') }}"
                                            class="form-control form-control-sm @error('discount_value') is-invalid @enderror"
                                            min="1" step="0.01" required>
                                        @error('discount_value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-semibold">Max Discount</label>
                                        <input type="number" name="max_discount" value="{{ old('max_discount') }}"
                                            class="form-control form-control-sm @error('max_discount') is-invalid @enderror"
                                            placeholder="Optional" step="0.01">
                                        @error('max_discount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-semibold">Min Order <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="min_order_value" value="{{ old('min_order_value') }}"
                                            class="form-control form-control-sm @error('min_order_value') is-invalid @enderror"
                                            min="0" step="0.01" required>
                                        @error('min_order_value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Description</label>
                        <textarea name="description" class="form-control form-control-sm @error('description') is-invalid @enderror"
                            rows="2" placeholder="Optional description">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        {{-- Usage Limits --}}
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <h6 class="text-muted mb-3 pb-2 border-bottom">Usage Limits</h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-semibold">Total Usage Limit</label>
                                        <input type="number" name="usage_limit" value="{{ old('usage_limit') }}"
                                            class="form-control form-control-sm @error('usage_limit') is-invalid @enderror"
                                            placeholder="Optional" min="1">
                                        @error('usage_limit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-semibold">Per User Limit <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="usage_per_user" value="{{ old('usage_per_user') }}"
                                            class="form-control form-control-sm @error('usage_per_user') is-invalid @enderror"
                                            min="1" required>
                                        @error('usage_per_user')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Validity --}}
                        <div class="col-lg-6">
                            <div class="mb-4">
                                <h6 class="text-muted mb-3 pb-2 border-bottom">Validity</h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-semibold">Valid From</label>
                                        <input type="date" name="valid_from" value="{{ old('valid_from') }}"
                                            class="form-control form-control-sm @error('valid_from') is-invalid @enderror">
                                        @error('valid_from')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small fw-semibold">Valid To</label>
                                        <input type="date" name="valid_to" value="{{ old('valid_to') }}"
                                            class="form-control form-control-sm @error('valid_to') is-invalid @enderror">
                                        @error('valid_to')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Scope & Status --}}
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Coupon Scope <span
                                        class="text-danger">*</span></label>
                                <select name="coupon_scope"
                                    class="form-select form-select-sm @error('coupon_scope') is-invalid @enderror"
                                    required>
                                    <option value="">-- Select Scope --</option>
                                    <option value="global" {{ old('coupon_scope') == 'global' ? 'selected' : '' }}>Global
                                    </option>
                                    <option value="restaurant"
                                        {{ old('coupon_scope') == 'restaurant' ? 'selected' : '' }}>Restaurant Specific
                                    </option>
                                </select>
                                @error('coupon_scope')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3 mt-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                        id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label small fw-semibold" for="is_active">
                                        Active Coupon
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            <i class="fa fa-plus me-1"></i> Create Coupon
                        </button>
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-sm">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
