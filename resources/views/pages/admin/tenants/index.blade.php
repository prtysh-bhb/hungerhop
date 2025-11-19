@extends('layouts.admin')

@section('title', 'Tenant Management')

@section('styles')
<style>
.stats-card {
    transition: transform 0.2s;
}
.stats-card:hover {
    transform: translateY(-2px);
}
.status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}
.tenant-card {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    transition: all 0.3s;
}
.tenant-card:hover {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}
.filter-section {
    background: #f8f9fc;
    border-radius: 0.35rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
}
</style>
@endsection

@section('content')
<div class="container-full">
    <!-- Content Header -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Tenant Management</h4>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item">Admin</li>
                        <li class="breadcrumb-item active">Tenants</li>
                    </ol>
                </nav>
            </div>
            <div class="text-end">
                <a href="{{ route('admin.tenants.create') }}" class="btn btn-success">
                    <i class="fa fa-plus"></i> Add New Tenant
                </a>
            </div>
        </div>
    </div>

    <section class="content">
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6 col-12">
                <div class="box stats-card">
                    <div class="box-body">
                        <div class="d-flex align-items-start">
                            <div>
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-primary text-white me-3">
                                        <i class="fa fa-building"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                        <p class="text-muted mb-0">Total Tenants</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 col-12">
                <div class="box stats-card">
                    <div class="box-body">
                        <div class="d-flex align-items-start">
                            <div>
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-success text-white me-3">
                                        <i class="fa fa-check-circle"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0">{{ $stats['approved'] }}</h3>
                                        <p class="text-muted mb-0">Approved</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 col-12">
                <div class="box stats-card">
                    <div class="box-body">
                        <div class="d-flex align-items-start">
                            <div>
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-warning text-white me-3">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                                        <p class="text-muted mb-0">Pending</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 col-12">
                <div class="box stats-card">
                    <div class="box-body">
                        <div class="d-flex align-items-start">
                            <div>
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-danger text-white me-3">
                                        <i class="fa fa-ban"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0">{{ $stats['suspended'] + $stats['rejected'] }}</h3>
                                        <p class="text-muted mb-0">Inactive</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row">
            <div class="col-12">
                <div class="filter-section">
                    <form method="GET" action="{{ route('admin.tenants.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">Search</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="Search tenants...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">All Statuses</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="subscription_plan">Plan</label>
                                    <select class="form-control" id="subscription_plan" name="subscription_plan">
                                        <option value="">All Plans</option>
                                        <option value="LITE" {{ request('subscription_plan') == 'LITE' ? 'selected' : '' }}>Lite</option>
                                        <option value="PLUS" {{ request('subscription_plan') == 'PLUS' ? 'selected' : '' }}>Plus</option>
                                        <option value="PRO_MAX" {{ request('subscription_plan') == 'PRO_MAX' ? 'selected' : '' }}>Pro Max</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="fa fa-search"></i> Filter
                                        </button>
                                        <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">
                                            <i class="fa fa-refresh"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tenants List -->
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">All Tenants</h4>
                        <div class="box-tools">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fa fa-download"></i> Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="fa fa-file-excel-o"></i> Excel</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fa fa-file-pdf-o"></i> PDF</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        @if($tenants->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tenant Info</th>
                                            <th>Contact</th>
                                            <th>Plan</th>
                                            <th>Restaurants</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tenants as $tenant)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="icon-circle bg-primary text-white me-3">
                                                        <i class="fa fa-building"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $tenant->tenant_name }}</h6>
                                                        <small class="text-muted">{{ $tenant->contact_person }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <small class="d-block"><i class="fa fa-envelope"></i> {{ $tenant->email }}</small>
                                                    <small class="text-muted"><i class="fa fa-phone"></i> {{ $tenant->phone }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-pill badge-info">{{ $tenant->subscription_plan }}</span>
                                                <small class="d-block text-muted">${{ number_format($tenant->monthly_base_fee, 2) }}/mo</small>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $tenant->restaurants()->count() }}</span> / {{ $tenant->total_restaurants }}
                                                <div class="progress mt-1" style="height: 4px;">
                                                    <div class="progress-bar bg-primary" style="width: {{ $tenant->total_restaurants > 0 ? ($tenant->restaurants()->count() / $tenant->total_restaurants) * 100 : 0 }}%"></div>
                                                </div>
                                            </td>
                                            <td>
                                                @switch($tenant->status)
                                                    @case('approved')
                                                        <span class="badge badge-pill badge-success status-badge">Approved</span>
                                                        @break
                                                    @case('pending')
                                                        <span class="badge badge-pill badge-warning status-badge">Pending</span>
                                                        @break
                                                    @case('suspended')
                                                        <span class="badge badge-pill badge-danger status-badge">Suspended</span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge badge-pill badge-dark status-badge">Rejected</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-pill badge-secondary status-badge">{{ ucfirst($tenant->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $tenant->created_at->format('M d, Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.tenants.show', $tenant) }}" class="btn btn-sm btn-primary" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-info" title="Update Status" onclick="updateStatus({{ $tenant->id }})">
                                                        <i class="fa fa-toggle-on"></i>
                                                    </button>
                                                    @if($tenant->restaurants()->count() == 0)
                                                    <button type="button" class="btn btn-sm btn-danger" title="Delete" onclick="deleteTenant({{ $tenant->id }})">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                        <nav class="d-flex align-items-center text-center" aria-label="Page navigation">
                              {{ $tenants->appends(request()->query())->links('pagination::bootstrap-5') }}
                        </nav>

                        <style>
                        /* Layout */
                        .pagination {
                            margin: 0;
                            display: flex;
                            align-items: center;
                            text-align: center;
                        }

                        /* Smaller page links and chevrons */
                        .pagination .page-link {
                            font-size: 0.85rem;
                            padding: 6px 8px;
                            min-width: 36px;
                            height: 32px;
                            line-height: 1;
                            border-radius: 6px;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                        }

                        /* Responsive: center pagination on small screens */
                        @media (max-width: 576px) {
                            .ms-auto { margin-left: 0 !important; }
                            .pagination { justify-content: center; }
                        }
                        </style>
                        @else
                            <div class="text-center py-5">
                                <div class="icon-circle bg-light text-muted mx-auto mb-3" style="width: 80px; height: 80px;">
                                    <i class="fa fa-building" style="font-size: 2rem; line-height: 80px;"></i>
                                </div>
                                <h5 class="text-muted">No tenants found</h5>
                                <p class="text-muted">Get started by creating your first tenant.</p>
                                <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Add First Tenant
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Tenant Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modalStatus">Status</label>
                        <select class="form-control" id="modalStatus" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="suspended">Suspended</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="modalReason">Reason (Optional)</label>
                        <textarea class="form-control" id="modalReason" name="reason" rows="3" placeholder="Enter reason for status change..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this tenant? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentTenantId = null;

function updateStatus(tenantId) {
    currentTenantId = tenantId;
    var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
    statusModal.show();
}

function deleteTenant(tenantId) {
    $('#deleteForm').attr('action', '/admin/tenants/' + tenantId);
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

$('#statusForm').on('submit', function(e) {
    e.preventDefault();
    
    if (!currentTenantId) return;
    
    const formData = new FormData(this);
    
    fetch(`/admin/tenants/${currentTenantId}/status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            var statusModal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
            statusModal.hide();
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

// Auto-submit form on filter change
$('#status, #subscription_plan').on('change', function() {
    $(this).closest('form').submit();
});
</script>
@endsection
