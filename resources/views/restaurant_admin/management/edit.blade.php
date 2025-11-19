@extends('layouts.admin')

@section('title', 'Edit Restaurant')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit Restaurant - {{ $restaurant->restaurant_name }}</h3>
                    <div class="box-tools float-right">
                        <a href="{{ route('restaurant-admin.management.show', $restaurant->id) }}" class="btn btn-info btn-sm">
                            <i class="fa fa-eye"></i> View Details
                        </a>
                        <a href="{{ route('restaurant-admin.management.index') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-list"></i> Back to Management
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('restaurant-admin.management.update', $restaurant->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-12">
                                <h4 class="mb-3">Basic Information</h4>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="restaurant_name">Restaurant Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="restaurant_name" name="restaurant_name" value="{{ old('restaurant_name', $restaurant->restaurant_name) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="tenant_id">Tenant</label>
                                <select class="form-control" id="tenant_id" name="tenant_id">
                                    <option value="">Select Tenant</option>
                                    @foreach($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" {{ old('tenant_id', $restaurant->tenant_id) == $tenant->id ? 'selected' : '' }}>
                                            {{ $tenant->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $restaurant->email) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $restaurant->phone) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cuisine_type">Cuisine Type</label>
                                    <input type="text" class="form-control" id="cuisine_type" name="cuisine_type" value="{{ old('cuisine_type', $restaurant->cuisine_type) }}" placeholder="e.g., Italian, Chinese, Mexican">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="website_url">Website URL</label>
                                    <input type="url" class="form-control" id="website_url" name="website_url" value="{{ old('website_url', $restaurant->website_url) }}">
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $restaurant->description) }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Address Information -->
                        <div class="row">
                            <div class="col-12">
                                <h4 class="mt-4 mb-3">Address Information</h4>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address', $restaurant->address) }}</textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $restaurant->city) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state">State <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="state" name="state" value="{{ old('state', $restaurant->state) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="postal_code">ZIP Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ old('postal_code', $restaurant->postal_code) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude">Latitude <span class="text-danger">*</span></label>
                                    <input type="number" step="any" class="form-control" id="latitude" name="latitude" value="{{ old('latitude', $restaurant->latitude) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude">Longitude <span class="text-danger">*</span></label>
                                    <input type="number" step="any" class="form-control" id="longitude" name="longitude" value="{{ old('longitude', $restaurant->longitude) }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Business Configuration -->
                        <div class="row">
                            <div class="col-12">
                                <h4 class="mt-4 mb-3">Business Configuration</h4>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="delivery_radius_km">Delivery Radius (KM) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="delivery_radius_km" name="delivery_radius_km" value="{{ old('delivery_radius_km', $restaurant->delivery_radius_km) }}" min="1" max="50" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="minimum_order_amount">Minimum Order Amount <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="minimum_order_amount" name="minimum_order_amount" value="{{ old('minimum_order_amount', $restaurant->minimum_order_amount) }}" min="0" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="base_delivery_fee">Base Delivery Fee <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="base_delivery_fee" name="base_delivery_fee" value="{{ old('base_delivery_fee', $restaurant->base_delivery_fee) }}" min="0" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="restaurant_commission_percentage">Commission Percentage <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="restaurant_commission_percentage" name="restaurant_commission_percentage" value="{{ old('restaurant_commission_percentage', $restaurant->restaurant_commission_percentage) }}" min="0" max="100" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="estimated_delivery_time">Estimated Delivery Time (minutes) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="estimated_delivery_time" name="estimated_delivery_time" value="{{ old('estimated_delivery_time', $restaurant->estimated_delivery_time) }}" min="10" max="120" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tax_percentage">Tax Percentage <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control" id="tax_percentage" name="tax_percentage" value="{{ old('tax_percentage', $restaurant->tax_percentage) }}" min="0" max="50" required>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="business_hours">Business Hours</label>
                                    <textarea class="form-control" id="business_hours" name="business_hours" rows="3" placeholder="e.g., Mon-Fri: 9:00 AM - 10:00 PM, Sat-Sun: 10:00 AM - 11:00 PM">{{ old('business_hours', is_string($restaurant->business_hours) ? $restaurant->business_hours : (is_array($restaurant->business_hours) ? json_encode($restaurant->business_hours) : '')) }}</textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="special_instructions">Special Instructions</label>
                                    <textarea class="form-control" id="special_instructions" name="special_instructions" rows="3">{{ old('special_instructions', $restaurant->special_instructions) }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Images -->
                        <div class="row">
                            <div class="col-12">
                                <h4 class="mt-4 mb-3">Images</h4>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image">Restaurant Image</label>
                                    <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                                    <small class="form-text text-muted">Upload restaurant logo or main image (JPG, PNG, max 2MB)</small>
                                    
                                    @if($restaurant->image_url)
                                        <div class="mt-2">
                                            <p><strong>Current Image:</strong></p>
                                            <img src="{{ $restaurant->image_url }}" alt="Current Image" class="img-thumbnail" style="max-width: 200px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cover_image">Cover Image</label>
                                    <input type="file" class="form-control-file" id="cover_image" name="cover_image" accept="image/*">
                                    <small class="form-text text-muted">Upload cover image (JPG, PNG, max 2MB)</small>
                                    
                                    @if($restaurant->cover_image_url)
                                        <div class="mt-2">
                                            <p><strong>Current Cover Image:</strong></p>
                                            <img src="{{ $restaurant->cover_image_url }}" alt="Current Cover Image" class="img-thumbnail" style="max-width: 200px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status and Settings -->
                        <div class="row">
                            <div class="col-12">
                                <h4 class="mt-4 mb-3">Status and Settings</h4>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="location_admin_id">Location Admin</label>
                                <select class="form-control" id="location_admin_id" name="location_admin_id">
                                    <option value="">Select Location Admin</option>
                                    @foreach($locationAdmins as $admin)
                                        <option value="{{ $admin->id }}" {{ old('location_admin_id', $restaurant->location_admin_id) == $admin->id ? 'selected' : '' }}>
                                            {{ $admin->first_name }} {{ $admin->last_name }} ({{ $admin->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="pending" {{ old('status', $restaurant->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ old('status', $restaurant->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="suspended" {{ old('status', $restaurant->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                        <option value="rejected" {{ old('status', $restaurant->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $restaurant->is_featured) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_featured">
                                        Featured Restaurant
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_open" name="is_open" value="1" {{ old('is_open', $restaurant->is_open) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_open">
                                        Currently Open
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="accepts_orders" name="accepts_orders" value="1" {{ old('accepts_orders', $restaurant->accepts_orders) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="accepts_orders">
                                        Accepts Orders
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="box-footer">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> Update Restaurant
                            </button>
                            <a href="{{ route('restaurant-admin.management.show', $restaurant->id) }}" class="btn btn-info">
                                <i class="fa fa-eye"></i> View Details
                            </a>
                            <a href="{{ route('restaurant-admin.management.index') }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Cancel
                            </a>
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
$(document).ready(function() {
    // Auto-generate slug when restaurant name changes
    $('#restaurant_name').on('input', function() {
        let name = $(this).val();
        let slug = name.toLowerCase()
            .replace(/[^a-z0-9 -]/g, '') // Remove invalid chars
            .replace(/\s+/g, '-') // Replace spaces with -
            .replace(/-+/g, '-'); // Replace multiple - with single -
        
        // Optional: Show the generated slug to user
        if (slug) {
            $('#slug-preview').text(slug);
        }
    });
    
    // Validate coordinates
    $('#latitude, #longitude').on('input', function() {
        let lat = parseFloat($('#latitude').val());
        let lng = parseFloat($('#longitude').val());
        
        if (lat && lng) {
            // Optional: Show location on map or validate coordinates
            if (lat < -90 || lat > 90) {
                $('#latitude').addClass('is-invalid');
            } else {
                $('#latitude').removeClass('is-invalid');
            }
            
            if (lng < -180 || lng > 180) {
                $('#longitude').addClass('is-invalid');
            } else {
                $('#longitude').removeClass('is-invalid');
            }
        }
    });
    
    // File upload preview
    $('input[type="file"]').on('change', function() {
        let file = this.files[0];
        if (file) {
            let reader = new FileReader();
            let preview = $(this).siblings('.preview');
            
            reader.onload = function(e) {
                if (preview.length === 0) {
                    preview = $('<div class="preview mt-2"><img class="img-thumbnail" style="max-width: 200px;"></div>');
                    $(this).after(preview);
                }
                preview.find('img').attr('src', e.target.result);
            }.bind(this);
            
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.form-check {
    padding-top: 0.375rem;
    margin-bottom: 1rem;
}

.is-invalid {
    border-color: #dc3545;
}

.img-thumbnail {
    border-radius: 4px;
}

h4 {
    color: #495057;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
}

.box-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
    padding: 1rem;
}

@media (max-width: 768px) {
    .btn {
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush
