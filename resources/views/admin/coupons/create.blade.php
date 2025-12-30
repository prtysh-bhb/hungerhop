@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Add Coupon</h2>

    <form method="POST" action="{{ route('admin.coupons.store') }}">
        @csrf

        {{-- Code --}}
        <div class="form-group mb-2">
            <label>Code</label>
            <input type="text"
                   name="code"
                   value="{{ old('code') }}"
                   class="form-control @error('code') is-invalid @enderror"
                   required>
            @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Title --}}
        <div class="form-group mb-2">
            <label>Title</label>
            <input type="text"
                   name="title"
                   value="{{ old('title') }}"
                   class="form-control @error('title') is-invalid @enderror"
                   required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Description --}}
        <div class="form-group mb-2">
            <label>Description</label>
            <input type="text"
                   name="description"
                   value="{{ old('description') }}"
                   class="form-control @error('description') is-invalid @enderror">
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Discount Type --}}
        <div class="form-group mb-2">
            <label>Discount Type</label>
            <select name="discount_type"
                    class="form-control @error('discount_type') is-invalid @enderror"
                    required>
                <option value="">-- Select --</option>
                <option value="flat" {{ old('discount_type') == 'flat' ? 'selected' : '' }}>Flat</option>
                <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
            </select>
            @error('discount_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Discount Value --}}
        <div class="form-group mb-2">
            <label>Discount Value</label>
            <input type="number"
                   name="discount_value"
                   value="{{ old('discount_value') }}"
                   class="form-control @error('discount_value') is-invalid @enderror"
                   min="1" step="0.01" required>
            @error('discount_value')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Max Discount --}}
        <div class="form-group mb-2">
            <label>Max Discount</label>
            <input type="number"
                   name="max_discount"
                   value="{{ old('max_discount') }}"
                   class="form-control @error('max_discount') is-invalid @enderror"
                   step="0.01">
            @error('max_discount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Min Order Value --}}
        <div class="form-group mb-2">
            <label>Min Order Value</label>
            <input type="number"
                   name="min_order_value"
                   value="{{ old('min_order_value') }}"
                   class="form-control @error('min_order_value') is-invalid @enderror"
                   min="0" step="0.01" required>
            @error('min_order_value')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Usage Limit --}}
        <div class="form-group mb-2">
            <label>Usage Limit</label>
            <input type="number"
                   name="usage_limit"
                   value="{{ old('usage_limit') }}"
                   class="form-control @error('usage_limit') is-invalid @enderror"
                   min="1">
            @error('usage_limit')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Usage Per User --}}
        <div class="form-group mb-2">
            <label>Usage Per User</label>
            <input type="number"
                   name="usage_per_user"
                   value="{{ old('usage_per_user') }}"
                   class="form-control @error('usage_per_user') is-invalid @enderror"
                   min="1" required>
            @error('usage_per_user')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Valid From --}}
        <div class="form-group mb-2">
            <label>Valid From</label>
            <input type="date"
                   name="valid_from"
                   value="{{ old('valid_from') }}"
                   class="form-control @error('valid_from') is-invalid @enderror">
            @error('valid_from')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Valid To --}}
        <div class="form-group mb-2">
            <label>Valid To</label>
            <input type="date"
                   name="valid_to"
                   value="{{ old('valid_to') }}"
                   class="form-control @error('valid_to') is-invalid @enderror">
            @error('valid_to')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Coupon Scope --}}
        <div class="form-group mb-2">
            <label>Coupon Scope</label>
            <select name="coupon_scope"
                    class="form-control @error('coupon_scope') is-invalid @enderror"
                    required>
                <option value="">-- Select --</option>
                <option value="global" {{ old('coupon_scope') == 'global' ? 'selected' : '' }}>Global</option>
                <option value="restaurant" {{ old('coupon_scope') == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
            </select>
            @error('coupon_scope')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Is Active --}}
        <div class="form-group mb-3">
            <label>
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                Is Active
            </label>
        </div>

        <button type="submit" class="btn btn-success">Add Coupon</button>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
