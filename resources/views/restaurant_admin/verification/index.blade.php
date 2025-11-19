@extends('layouts.admin')

@section('title', 'Document Verification')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Document Verification Queue</h3>
                    <div class="box-tools float-right">
                        <a href="{{ route('restaurant-admin.verification.expiry-report') }}" class="btn btn-warning btn-sm">
                            <i class="fa fa-calendar"></i> Expiry Report
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

                    <!-- Verification Stats -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Pending Review</span>
                                    <span class="info-box-number">{{ $stats['pending'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Approved Today</span>
                                    <span class="info-box-number">{{ $stats['approved_today'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-red"><i class="fa fa-times"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Rejected Today</span>
                                    <span class="info-box-number">{{ $stats['rejected_today'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-orange"><i class="fa fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Expiring Soon</span>
                                    <span class="info-box-number">{{ $stats['expiring_soon'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters and Bulk Actions -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-control" id="statusFilter">
                                <option value="pending">Pending Review</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="">All Statuses</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="typeFilter">
                                <option value="">All Document Types</option>
                                <option value="business_license">Business License</option>
                                <option value="food_license">Food License</option>
                                <option value="tax_certificate">Tax Certificate</option>
                                <option value="insurance_policy">Insurance Policy</option>
                                <option value="health_certificate">Health Certificate</option>
                                <option value="fire_certificate">Fire Certificate</option>
                                <option value="owner_id">Owner ID</option>
                                <option value="bank_details">Bank Details</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="priorityFilter">
                                <option value="">All Priorities</option>
                                <option value="urgent">Urgent (Expiring Soon)</option>
                                <option value="normal">Normal</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-info btn-block" onclick="loadVerificationQueue()">
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
                                    <option value="reject">Reject Selected</option>
                                    <option value="mark_urgent">Mark as Urgent</option>
                                </select>
                                <button type="button" class="btn btn-warning" onclick="performBulkAction()">
                                    <i class="fa fa-cogs"></i> Apply to Selected
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <span id="selectedCount" class="text-muted">0 selected</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="verification-table">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>Priority</th>
                                    <th>Restaurant</th>
                                    <th>Document Type</th>
                                    <th>Submitted</th>
                                    <th>Expires</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="verificationTableBody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>

                    <div id="loadingSpinner" class="text-center py-4" style="display: none;">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Loading verification queue...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Document View Modal -->
<div class="modal fade" id="documentModal" tabindex="-1" role="dialog" aria-labelledby="documentModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentModalLabel">Document Verification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="documentModalBody">
                <!-- Document content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="quickApprove()">
                    <i class="fa fa-check"></i> Quick Approve
                </button>
                <button type="button" class="btn btn-danger" onclick="quickReject()">
                    <i class="fa fa-times"></i> Quick Reject
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Document Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    <div class="form-group">
                        <label for="newStatus">New Status</label>
                        <select class="form-control" id="newStatus" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="adminNotes">Admin Notes</label>
                        <textarea class="form-control" id="adminNotes" name="admin_notes" rows="3" placeholder="Add notes about this verification decision..."></textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="isVerified" name="is_verified" value="1">
                        <label class="form-check-label" for="isVerified">
                            Mark document as verified
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateDocumentStatus()">Update Status</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentDocumentId = null;
let verificationData = [];

$(document).ready(function() {
    loadVerificationQueue();
    
    // Auto-refresh every 2 minutes
    setInterval(loadVerificationQueue, 120000);
    
    // Select all functionality
    $('#selectAll').on('change', function() {
        $('input[name="document_ids[]"]').prop('checked', this.checked);
        updateSelectedCount();
    });
    
    // Individual checkbox change
    $(document).on('change', 'input[name="document_ids[]"]', function() {
        updateSelectedCount();
        updateSelectAllState();
    });
});

function loadVerificationQueue() {
    $('#loadingSpinner').show();
    $('#verificationTableBody').empty();
    
    let params = {
        status: $('#statusFilter').val(),
        document_type: $('#typeFilter').val(),
        priority: $('#priorityFilter').val()
    };
    
    $.get('{{ route("restaurant-admin.verification.queue-data") }}', params)
        .done(function(data) {
            verificationData = data;
            renderVerificationTable(data);
        })
        .fail(function() {
            showAlert('error', 'Failed to load verification queue');
        })
        .always(function() {
            $('#loadingSpinner').hide();
        });
}

function renderVerificationTable(data) {
    let tbody = $('#verificationTableBody');
    tbody.empty();
    
    if (data.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="8" class="text-center py-4">
                    <i class="fa fa-check-circle fa-3x text-success mb-3"></i>
                    <h5>No documents pending verification</h5>
                    <p class="text-muted">All documents have been processed!</p>
                </td>
            </tr>
        `);
        return;
    }
    
    data.forEach(function(document) {
        let priorityBadge = document.is_urgent ? 
            '<span class="badge badge-danger"><i class="fa fa-exclamation-triangle"></i> Urgent</span>' : 
            '<span class="badge badge-secondary">Normal</span>';
            
        let statusBadge = getStatusBadge(document.status);
        let expiryInfo = getExpiryInfo(document.expires_at);
        
        tbody.append(`
            <tr>
                <td><input type="checkbox" name="document_ids[]" value="${document.id}"></td>
                <td>${priorityBadge}</td>
                <td>
                    <strong>${document.restaurant.restaurant_name}</strong>
                    <br><small class="text-muted">${document.restaurant.email}</small>
                </td>
                <td>
                    <span class="badge badge-info">${document.document_type.replace('_', ' ')}</span>
                    ${document.document_number ? '<br><small class="text-muted">#' + document.document_number + '</small>' : ''}
                </td>
                <td>
                    ${formatDate(document.created_at)}
                    <br><small class="text-muted">${timeAgo(document.created_at)}</small>
                </td>
                <td>${expiryInfo}</td>
                <td>${statusBadge}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-info btn-sm" onclick="viewDocument(${document.id})" title="View Document">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-success btn-sm" onclick="changeStatus(${document.id}, 'approved')" title="Approve">
                            <i class="fa fa-check"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="changeStatus(${document.id}, 'rejected')" title="Reject">
                            <i class="fa fa-times"></i>
                        </button>
                        <a href="{{ route('restaurant-admin.verification.download', ':id') }}".replace(':id', ${document.id}) class="btn btn-warning btn-sm" title="Download">
                            <i class="fa fa-download"></i>
                        </a>
                    </div>
                </td>
            </tr>
        `);
    });
}

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge badge-warning">Pending</span>',
        'approved': '<span class="badge badge-success">Approved</span>',
        'rejected': '<span class="badge badge-danger">Rejected</span>'
    };
    return badges[status] || '<span class="badge badge-light">Unknown</span>';
}

function getExpiryInfo(expiresAt) {
    if (!expiresAt) return '<span class="text-muted">No Expiry</span>';
    
    let expiryDate = new Date(expiresAt);
    let now = new Date();
    let diffDays = Math.ceil((expiryDate - now) / (1000 * 60 * 60 * 24));
    
    if (diffDays < 0) {
        return `<span class="text-danger">Expired ${Math.abs(diffDays)} days ago</span>`;
    } else if (diffDays <= 30) {
        return `<span class="text-warning">Expires in ${diffDays} days</span>`;
    } else {
        return `<span class="text-muted">Expires ${formatDate(expiresAt)}</span>`;
    }
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function timeAgo(dateString) {
    let date = new Date(dateString);
    let now = new Date();
    let diffMs = now - date;
    let diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    let diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    
    if (diffDays > 0) return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
    if (diffHours > 0) return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
    return 'Recently';
}

function updateSelectedCount() {
    let count = $('input[name="document_ids[]"]:checked').length;
    $('#selectedCount').text(count + ' selected');
}

function updateSelectAllState() {
    let total = $('input[name="document_ids[]"]').length;
    let checked = $('input[name="document_ids[]"]:checked').length;
    
    $('#selectAll').prop('indeterminate', checked > 0 && checked < total);
    $('#selectAll').prop('checked', checked === total);
}

function viewDocument(documentId) {
    currentDocumentId = documentId;
    
    // Load document details
    $.get('{{ route("restaurant-admin.verification.view", ":id") }}'.replace(':id', documentId))
        .done(function(data) {
            $('#documentModalBody').html(data);
            $('#documentModal').modal('show');
        })
        .fail(function() {
            showAlert('error', 'Failed to load document details');
        });
}

function changeStatus(documentId, status) {
    currentDocumentId = documentId;
    $('#newStatus').val(status);
    $('#isVerified').prop('checked', status === 'approved');
    $('#statusModal').modal('show');
}

function updateDocumentStatus() {
    let status = $('#newStatus').val();
    let adminNotes = $('#adminNotes').val();
    let isVerified = $('#isVerified').is(':checked');
    
    $.post('{{ route("restaurant-admin.verification.update-status", ":id") }}'.replace(':id', currentDocumentId), {
        status: status,
        admin_notes: adminNotes,
        is_verified: isVerified ? 1 : 0,
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
        $('#statusModal').modal('hide');
        loadVerificationQueue();
        showAlert('success', response.message || 'Document status updated successfully');
    })
    .fail(function(xhr) {
        let message = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to update document status';
        showAlert('error', message);
    });
}

function quickApprove() {
    $('#newStatus').val('approved');
    $('#isVerified').prop('checked', true);
    $('#adminNotes').val('Quick approved via verification queue');
    updateDocumentStatus();
    $('#documentModal').modal('hide');
}

function quickReject() {
    $('#newStatus').val('rejected');
    $('#isVerified').prop('checked', false);
    $('#adminNotes').val('Quick rejected via verification queue');
    updateDocumentStatus();
    $('#documentModal').modal('hide');
}

function performBulkAction() {
    let action = $('#bulkAction').val();
    let selectedIds = [];
    
    $('input[name="document_ids[]"]:checked').each(function() {
        selectedIds.push($(this).val());
    });
    
    if (!action) {
        showAlert('warning', 'Please select an action');
        return;
    }
    
    if (selectedIds.length === 0) {
        showAlert('warning', 'Please select documents to update');
        return;
    }
    
    if (confirm(`Are you sure you want to ${action} ${selectedIds.length} document(s)?`)) {
        $.post('{{ route("restaurant-admin.verification.bulk-update-status") }}', {
            action: action,
            document_ids: selectedIds,
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            loadVerificationQueue();
            $('#bulkAction').val('');
            $('#selectAll').prop('checked', false);
            updateSelectedCount();
            showAlert('success', response.message);
        })
        .fail(function(xhr) {
            let message = xhr.responseJSON ? xhr.responseJSON.message : 'Bulk action failed';
            showAlert('error', message);
        });
    }
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
    
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
@endpush

@push('styles')
<style>
.info-box {
    display: block;
    min-height: 90px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 2px;
    margin-bottom: 15px;
}

.info-box-icon {
    border-top-left-radius: 2px;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 2px;
    display: block;
    float: left;
    height: 90px;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0,0,0,0.2);
}

.info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
}

.info-box-text {
    text-transform: uppercase;
    font-weight: bold;
    font-size: 13px;
}

.info-box-number {
    font-weight: bold;
    font-size: 36px;
}

.badge {
    font-size: 0.75em;
}

.btn-group .btn {
    margin: 0 1px;
}
</style>
@endpush
