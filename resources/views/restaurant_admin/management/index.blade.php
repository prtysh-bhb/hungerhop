@extends('layouts.admin')

@section('title', 'Restaurant Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Restaurant Management</h3>
                    <div class="box-tools float-right">
                        <a href="{{ route('restaurant-admin.registration.create') }}" class="btn btn-success btn-sm">
                            <i class="fa fa-plus"></i> Add New Restaurant
                        </a>
                        <a href="{{ route('restaurant-admin.index') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-dashboard"></i> Dashboard
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-control" id="statusFilter">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="suspended">Suspended</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="cityFilter" placeholder="Filter by city...">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchFilter" placeholder="Search restaurants...">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-info btn-block" onclick="applyFilters()">
                                <i class="fa fa-search"></i> Filter
                            </button>
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-inline">
                                <select class="form-control mr-2" id="bulkAction">
                                    <option value="">Select Action</option>
                                    <option value="approve">Approve Selected</option>
                                    <option value="suspend">Suspend Selected</option>
                                    <option value="reject">Reject Selected</option>
                                </select>
                                <button type="button" class="btn btn-warning" onclick="performBulkAction()">
                                    <i class="fa fa-cogs"></i> Apply
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <span id="selectedCount" class="text-muted">0 selected</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="restaurants-table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>City</th>
                                    <th>Status</th>
                                    <th>Orders</th>
                                    <th>Rating</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination will be handled by DataTables -->
                </div>
                
                <div class="box-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Data updates automatically every 30 seconds</small>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-sm btn-default" onclick="refreshTable()">
                                <i class="fa fa-refresh"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Restaurant Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    <div class="form-group">
                        <label for="status">New Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="suspended">Suspended</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any notes about this status change..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateStatus()">Update Status</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this restaurant? This action cannot be undone.</p>
                <p class="text-warning"><strong>Warning:</strong> All associated data will also be deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let restaurantsTable;
let currentRestaurantId = null;

$(document).ready(function() {
    // Initialize DataTable
    restaurantsTable = $('#restaurants-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("restaurant-admin.management.data") }}',
            data: function(d) {
                d.status = $('#statusFilter').val();
                d.city = $('#cityFilter').val();
                d.search = $('#searchFilter').val();
            }
        },
        columns: [
            { data: 'checkbox', orderable: false, searchable: false },
            { data: 'id' },
            { data: 'image', orderable: false, searchable: false },
            { data: 'restaurant_name' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'city' },
            { data: 'status' },
            { data: 'total_orders' },
            { data: 'average_rating' },
            { data: 'created_at' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        order: [[ 1, 'desc' ]],
        pageLength: 25,
        autoWidth: false,
        responsive: true
    });

    // Auto refresh every 30 seconds
    setInterval(function() {
        restaurantsTable.ajax.reload(null, false);
    }, 30000);

    // Select all checkbox
    $('#selectAll').on('change', function() {
        $('input[name="restaurant_ids[]"]').prop('checked', this.checked);
        updateSelectedCount();
    });

    // Individual checkbox change
    $(document).on('change', 'input[name="restaurant_ids[]"]', function() {
        updateSelectedCount();
        
        // Update select all checkbox
        let total = $('input[name="restaurant_ids[]"]').length;
        let checked = $('input[name="restaurant_ids[]"]:checked').length;
        
        $('#selectAll').prop('indeterminate', checked > 0 && checked < total);
        $('#selectAll').prop('checked', checked === total);
    });
});

function applyFilters() {
    restaurantsTable.ajax.reload();
}

function refreshTable() {
    restaurantsTable.ajax.reload();
}

function updateSelectedCount() {
    let count = $('input[name="restaurant_ids[]"]:checked').length;
    $('#selectedCount').text(count + ' selected');
}

function changeStatus(restaurantId, currentStatus) {
    currentRestaurantId = restaurantId;
    $('#status').val(currentStatus);
    $('#statusModal').modal('show');
}

function updateStatus() {
    let status = $('#status').val();
    let notes = $('#notes').val();
    
    $.ajax({
        url: '{{ route("restaurant-admin.management.update-status", ":id") }}'.replace(':id', currentRestaurantId),
        method: 'POST',
        data: {
            status: status,
            notes: notes,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            $('#statusModal').modal('hide');
            restaurantsTable.ajax.reload();
            
            // Show success message
            showAlert('success', 'Restaurant status updated successfully!');
        },
        error: function(xhr) {
            let message = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred';
            showAlert('error', message);
        }
    });
}

function performBulkAction() {
    let action = $('#bulkAction').val();
    let selectedIds = [];
    
    $('input[name="restaurant_ids[]"]:checked').each(function() {
        selectedIds.push($(this).val());
    });
    
    if (!action) {
        showAlert('warning', 'Please select an action');
        return;
    }
    
    if (selectedIds.length === 0) {
        showAlert('warning', 'Please select restaurants to update');
        return;
    }
    
    if (confirm('Are you sure you want to ' + action + ' ' + selectedIds.length + ' restaurant(s)?')) {
        $.ajax({
            url: '{{ route("restaurant-admin.management.bulk-update-status") }}',
            method: 'POST',
            data: {
                action: action,
                restaurant_ids: selectedIds,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                restaurantsTable.ajax.reload();
                $('#bulkAction').val('');
                $('#selectAll').prop('checked', false);
                updateSelectedCount();
                
                showAlert('success', response.message);
            },
            error: function(xhr) {
                let message = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred';
                showAlert('error', message);
            }
        });
    }
}

function toggleRestaurant(restaurantId) {
    $.ajax({
        url: '{{ route("restaurant-admin.management.toggle", ":id") }}'.replace(':id', restaurantId),
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            restaurantsTable.ajax.reload();
            showAlert('success', response.message);
        },
        error: function(xhr) {
            let message = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred';
            showAlert('error', message);
        }
    });
}

function deleteRestaurant(restaurantId) {
    currentRestaurantId = restaurantId;
    $('#deleteModal').modal('show');
}

function confirmDelete() {
    $.ajax({
        url: '{{ route("restaurant-admin.management.destroy", ":id") }}'.replace(':id', currentRestaurantId),
        method: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            $('#deleteModal').modal('hide');
            restaurantsTable.ajax.reload();
            showAlert('success', 'Restaurant deleted successfully!');
        },
        error: function(xhr) {
            let message = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred';
            showAlert('error', message);
        }
    });
}

function showAlert(type, message) {
    let alertClass = type === 'success' ? 'alert-success' : 
                    type === 'error' ? 'alert-danger' : 
                    type === 'warning' ? 'alert-warning' : 'alert-info';
    
    let alert = `<div class="alert ${alertClass} alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        ${message}
    </div>`;
    
    $('.box-body').prepend(alert);
    
    // Auto dismiss after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endpush

@push('styles')
<style>
.table img {
    border-radius: 4px;
}

.badge {
    font-size: 0.75em;
}

.btn-group .btn {
    margin: 0 1px;
}

.form-inline .form-control {
    margin-right: 10px;
}

.table-responsive {
    border: none;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .form-inline {
        flex-direction: column;
    }
    
    .form-inline .form-control {
        margin-bottom: 10px;
        width: 100%;
    }
}
</style>
@endpush
