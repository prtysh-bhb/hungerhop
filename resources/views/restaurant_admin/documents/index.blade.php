@extends('layouts.admin')

@section('title', 'Document Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Document Management</h3>
                    <div class="box-tools float-right">
                        <a href="{{ route('restaurant-admin.documents.create') }}" class="btn btn-success btn-sm">
                            <i class="fa fa-plus"></i> Upload Document
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
                            <select class="form-control" id="restaurantFilter">
                                <option value="">All Restaurants</option>
                                @foreach($restaurants as $restaurant)
                                    <option value="{{ $restaurant->id }}">{{ $restaurant->restaurant_name }}</option>
                                @endforeach
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
                            <select class="form-control" id="statusFilter">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-info btn-block" onclick="applyFilters()">
                                <i class="fa fa-search"></i> Filter
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="documents-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Restaurant</th>
                                    <th>Document Type</th>
                                    <th>Original Filename</th>
                                    <th>Status</th>
                                    <th>Verification Status</th>
                                    <th>Expires</th>
                                    <th>Uploaded</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($documents as $document)
                                    <tr>
                                        <td>{{ $document->id }}</td>
                                        <td>
                                            <strong>{{ $document?->restaurant?->restaurant_name }}</strong>
                                            <br><small class="text-muted">{{ $document?->restaurant?->email }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ ucwords(str_replace('_', ' ', $document->document_type)) }}</span>
                                        </td>
                                        <td>
                                            {{ $document->original_filename ?? 'N/A' }}
                                            @if($document->file_size)
                                                <br><small class="text-muted">{{ number_format($document->file_size / 1024, 2) }} KB</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @switch($document->status)
                                                    @case('pending') badge-warning @break
                                                    @case('approved') badge-success @break
                                                    @case('rejected') badge-danger @break
                                                    @default badge-light
                                                @endswitch
                                            ">
                                                {{ ucfirst($document->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($document->is_verified)
                                                <span class="badge badge-success"><i class="fa fa-check"></i> Verified</span>
                                            @else
                                                <span class="badge badge-secondary"><i class="fa fa-clock-o"></i> Unverified</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($document->expires_at)
                                                {{ $document->expires_at->format('M d, Y') }}
                                                @if($document->expires_at->isPast())
                                                    <br><span class="badge badge-danger">Expired</span>
                                                @elseif($document->expires_at->diffInDays() <= 30)
                                                    <br><span class="badge badge-warning">Expiring Soon</span>
                                                @endif
                                            @else
                                                <span class="text-muted">No Expiry</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $document->created_at->format('M d, Y') }}
                                            <br><small class="text-muted">{{ $document->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('restaurant-admin.documents.view', $document->id) }}" class="btn btn-info btn-sm" title="View Document">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('restaurant-admin.documents.download', $document->id) }}" class="btn btn-success btn-sm" title="Download">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                                <a href="{{ route('restaurant-admin.documents.edit', $document->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="deleteDocument({{ $document->id }})">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">
                                            <div class="py-5">
                                                <i class="fa fa-file-text-o fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No documents found</h5>
                                                <p class="text-muted">Start by uploading restaurant documents.</p>
                                                <a href="{{ route('restaurant-admin.documents.create') }}" class="btn btn-success">
                                                    <i class="fa fa-plus"></i> Upload Document
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($documents instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $documents->firstItem() ?? 0 }} to {{ $documents->lastItem() ?? 0 }} of {{ $documents->total() }} results
                            </div>
                            <div>
                                {{ $documents->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Document Summary Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-blue"><i class="fa fa-file-text-o"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Documents</span>
                <span class="info-box-number">{{ $stats['total'] ?? 0 }}</span>
            </div>
        </div>
    </div>
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
                <span class="info-box-text">Approved</span>
                <span class="info-box-number">{{ $stats['approved'] ?? 0 }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Expiring Soon</span>
                <span class="info-box-number">{{ $stats['expiring'] ?? 0 }}</span>
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
                <p>Are you sure you want to delete this document? This action cannot be undone.</p>
                <p class="text-warning"><strong>Warning:</strong> The document file will be permanently removed from storage.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Document</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable if it exists
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#documents-table').DataTable({
            "pageLength": 25,
            "order": [[ 0, "desc" ]],
            "columnDefs": [
                { "orderable": false, "targets": [8] } // Actions column
            ]
        });
    }
});

function applyFilters() {
    let restaurant = $('#restaurantFilter').val();
    let type = $('#typeFilter').val();
    let status = $('#statusFilter').val();
    
    let params = new URLSearchParams(window.location.search);
    
    if (restaurant) params.set('restaurant_id', restaurant);
    else params.delete('restaurant_id');
    
    if (type) params.set('document_type', type);
    else params.delete('document_type');
    
    if (status) params.set('status', status);
    else params.delete('status');
    
    window.location.search = params.toString();
}

function deleteDocument(documentId) {
    $('#deleteForm').attr('action', '{{ route("restaurant-admin.documents.destroy", ":id") }}'.replace(':id', documentId));
    $('#deleteModal').modal('show');
}

// Auto-reload page every 60 seconds to get updated data
setInterval(function() {
    if (!$('#deleteModal').hasClass('show')) {
        location.reload();
    }
}, 60000);
</script>
@endpush

@push('styles')
<style>
.badge {
    font-size: 0.75em;
}

.btn-group .btn {
    margin: 0 1px;
}

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

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>
@endpush
