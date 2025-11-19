@extends('layouts.admin')

@section('title', 'Edit Tenant')

@section('styles')
<style>
.form-section {
    background: #fff;
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.section-title {
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f8f9fc;
}
.plan-card {
    border: 2px solid #e3e6f0;
    border-radius: 0.5rem;
    padding: 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}
.plan-card:hover {
    border-color: #4e73df;
    transform: translateY(-2px);
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}
.plan-card.selected {
    border-color: #4e73df;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.plan-card.selected .plan-price {
    color: white;
}
.plan-price {
    font-size: 2rem;
    font-weight: bold;
    color: #4e73df;
}
.feature-list {
    list-style: none;
    padding: 0;
}
.feature-list li {
    padding: 0.25rem 0;
}
.feature-list li i {
    color: #28a745;
    margin-right: 0.5rem;
}
.status-alert {
    padding: 1rem;
    border-radius: 0.35rem;
    margin-bottom: 1rem;
}
</style>
@endsection

@section('content')
<div class="container-full">
    <!-- Content Header -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Edit Tenant</h4>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.tenants.index') }}">Tenants</a></li>
                        <li class="breadcrumb-item active">Edit {{ $tenant->tenant_name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="text-end">
                <a href="{{ route('admin.tenants.show', $tenant) }}" class="btn btn-info">
                    <i class="fa fa-eye"></i> View Details
                </a>
                <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <section class="content">
        <!-- Status Alert -->
        @if($tenant->status === 'pending')
        <div class="status-alert" style="background: #fff3cd; border: 1px solid #ffeaa7; color: #856404;">
            <i class="fa fa-exclamation-triangle"></i> 
            This tenant is still pending approval. You can approve it after updating the details.
        </div>
        @elseif($tenant->status === 'suspended')
        <div class="status-alert" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24;">
            <i class="fa fa-ban"></i> 
            This tenant is currently suspended. Consider reactivating after reviewing the details.
        </div>
        @endif

        <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST" id="tenantForm">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="form-section">
                <h5 class="section-title">
                    <i class="fa fa-building text-primary"></i> Basic Information
                </h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tenant_name" class="form-label">Tenant Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('tenant_name') is-invalid @enderror" 
                                   id="tenant_name" name="tenant_name" value="{{ old('tenant_name', $tenant->tenant_name) }}" required>
                            @error('tenant_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contact_person" class="form-label">Contact Person <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                                   id="contact_person" name="contact_person" value="{{ old('contact_person', $tenant->contact_person) }}" required>
                            @error('contact_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $tenant->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $tenant->phone) }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscription Plan -->
            <div class="form-section">
                <h5 class="section-title">
                    <i class="fa fa-credit-card text-primary"></i> Subscription Plan
                </h5>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="plan-card" data-plan="LITE" onclick="selectPlan('LITE')">
                            <h6>LITE</h6>
                            <div class="plan-price">$49</div>
                            <p class="text-muted">per month</p>
                            <ul class="feature-list">
                                <li><i class="fa fa-check"></i> Up to 3 restaurants</li>
                                <li><i class="fa fa-check"></i> 5 banners per restaurant</li>
                                <li><i class="fa fa-check"></i> Basic support</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="plan-card" data-plan="PLUS" onclick="selectPlan('PLUS')">
                            <h6>PLUS</h6>
                            <div class="plan-price">$99</div>
                            <p class="text-muted">per month</p>
                            <ul class="feature-list">
                                <li><i class="fa fa-check"></i> Up to 10 restaurants</li>
                                <li><i class="fa fa-check"></i> 15 banners per restaurant</li>
                                <li><i class="fa fa-check"></i> Priority support</li>
                                <li><i class="fa fa-check"></i> Analytics dashboard</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="plan-card" data-plan="PRO_MAX" onclick="selectPlan('PRO_MAX')">
                            <h6>PRO MAX</h6>
                            <div class="plan-price">$199</div>
                            <p class="text-muted">per month</p>
                            <ul class="feature-list">
                                <li><i class="fa fa-check"></i> Unlimited restaurants</li>
                                <li><i class="fa fa-check"></i> Unlimited banners</li>
                                <li><i class="fa fa-check"></i> 24/7 premium support</li>
                                <li><i class="fa fa-check"></i> Advanced analytics</li>
                                <li><i class="fa fa-check"></i> Custom integrations</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <input type="hidden" name="subscription_plan" id="subscription_plan" 
                       value="{{ old('subscription_plan', $tenant->subscription_plan) }}">
            </div>

            <!-- Business Configuration -->
            <div class="form-section">
                <h5 class="section-title">
                    <i class="fa fa-cogs text-primary"></i> Business Configuration
                </h5>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="total_restaurants" class="form-label">Restaurant Limit</label>
                            <input type="number" class="form-control @error('total_restaurants') is-invalid @enderror" 
                                   id="total_restaurants" name="total_restaurants" min="1" 
                                   value="{{ old('total_restaurants', $tenant->total_restaurants) }}">
                            @error('total_restaurants')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="banner_limit" class="form-label">Banner Limit</label>
                            <input type="number" class="form-control @error('banner_limit') is-invalid @enderror" 
                                   id="banner_limit" name="banner_limit" min="1" 
                                   value="{{ old('banner_limit', $tenant->banner_limit) }}">
                            @error('banner_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="monthly_base_fee" class="form-label">Monthly Base Fee ($)</label>
                            <input type="number" class="form-control @error('monthly_base_fee') is-invalid @enderror" 
                                   id="monthly_base_fee" name="monthly_base_fee" step="0.01" min="0" 
                                   value="{{ old('monthly_base_fee', $tenant->monthly_base_fee) }}">
                            @error('monthly_base_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="per_restaurant_fee" class="form-label">Per Restaurant Fee ($)</label>
                            <input type="number" class="form-control @error('per_restaurant_fee') is-invalid @enderror" 
                                   id="per_restaurant_fee" name="per_restaurant_fee" step="0.01" min="0" 
                                   value="{{ old('per_restaurant_fee', $tenant->per_restaurant_fee) }}">
                            @error('per_restaurant_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscription Dates -->
            <div class="form-section">
                <h5 class="section-title">
                    <i class="fa fa-calendar text-primary"></i> Subscription Dates
                </h5>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="subscription_start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control @error('subscription_start_date') is-invalid @enderror" 
                                   id="subscription_start_date" name="subscription_start_date" 
                                   value="{{ old('subscription_start_date', $tenant->subscription_start_date?->format('Y-m-d')) }}">
                            @error('subscription_start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="subscription_end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control @error('subscription_end_date') is-invalid @enderror" 
                                   id="subscription_end_date" name="subscription_end_date" 
                                   value="{{ old('subscription_end_date', $tenant->subscription_end_date?->format('Y-m-d')) }}">
                            @error('subscription_end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="next_billing_date" class="form-label">Next Billing Date</label>
                            <input type="date" class="form-control @error('next_billing_date') is-invalid @enderror" 
                                   id="next_billing_date" name="next_billing_date" 
                                   value="{{ old('next_billing_date', $tenant->next_billing_date?->format('Y-m-d')) }}">
                            @error('next_billing_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Management -->
            @if(auth()->user()->role === 'super_admin')
            <div class="form-section">
                <h5 class="section-title">
                    <i class="fa fa-shield text-primary"></i> Status Management
                </h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="pending" {{ old('status', $tenant->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ old('status', $tenant->status) === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="suspended" {{ old('status', $tenant->status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="rejected" {{ old('status', $tenant->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="admin_notes" class="form-label">Admin Notes</label>
                            <textarea class="form-control @error('admin_notes') is-invalid @enderror" 
                                      id="admin_notes" name="admin_notes" rows="3" 
                                      placeholder="Internal notes about this tenant...">{{ old('admin_notes', $tenant->admin_notes) }}</textarea>
                            @error('admin_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Form Actions -->
            <div class="form-section">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fa fa-save"></i> Update Tenant
                        </button>
                        <button type="button" class="btn btn-secondary btn-lg ms-2" onclick="resetForm()">
                            <i class="fa fa-undo"></i> Reset Changes
                        </button>
                    </div>
                    
                    @if(auth()->user()->role === 'super_admin' && $tenant->status !== 'approved')
                    <div>
                        <button type="button" class="btn btn-info" onclick="approveAndSave()">
                            <i class="fa fa-check-circle"></i> Save & Approve
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </form>
    </section>
</div>
@endsection

@section('scripts')
<script>
// Plan selection
function selectPlan(plan) {
    // Remove selection from all cards
    document.querySelectorAll('.plan-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selection to clicked card
    document.querySelector(`[data-plan="${plan}"]`).classList.add('selected');
    
    // Update hidden input
    document.getElementById('subscription_plan').value = plan;
    
    // Update limits based on plan
    updatePlanLimits(plan);
}

function updatePlanLimits(plan) {
    const limits = {
        'LITE': { restaurants: 3, banners: 5, baseFee: 49, perRestaurant: 0 },
        'PLUS': { restaurants: 10, banners: 15, baseFee: 99, perRestaurant: 5 },
        'PRO_MAX': { restaurants: 999, banners: 999, baseFee: 199, perRestaurant: 10 }
    };
    
    if (limits[plan]) {
        document.getElementById('total_restaurants').value = limits[plan].restaurants;
        document.getElementById('banner_limit').value = limits[plan].banners;
        document.getElementById('monthly_base_fee').value = limits[plan].baseFee;
        document.getElementById('per_restaurant_fee').value = limits[plan].perRestaurant;
    }
}

function resetForm() {
    if (confirm('Are you sure you want to reset all changes?')) {
        document.getElementById('tenantForm').reset();
        
        // Reset plan selection
        const currentPlan = '{{ $tenant->subscription_plan }}';
        selectPlan(currentPlan);
    }
}

function approveAndSave() {
    if (confirm('This will save the changes and approve the tenant. Continue?')) {
        document.getElementById('status').value = 'approved';
        document.getElementById('tenantForm').submit();
    }
}

// Initialize plan selection on page load
document.addEventListener('DOMContentLoaded', function() {
    const currentPlan = '{{ $tenant->subscription_plan }}';
    if (currentPlan) {
        selectPlan(currentPlan);
    }
});

// Form validation
document.getElementById('tenantForm').addEventListener('submit', function(e) {
    const requiredFields = ['tenant_name', 'contact_person', 'email', 'phone'];
    let isValid = true;
    
    requiredFields.forEach(field => {
        const element = document.getElementById(field);
        if (!element.value.trim()) {
            element.classList.add('is-invalid');
            isValid = false;
        } else {
            element.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields.');
    }
});
</script>
@endsection
