@extends('layouts.admin')

@section('title', 'View Document')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Document Details</h3>
                    <div class="box-tools float-right">
                        <a href="{{ route('restaurant-admin.documents.index') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-list"></i> Back to Documents
                        </a>
                        <a href="{{ route('restaurant-admin.documents.edit', $document->id) }}" class="btn btn-warning btn-sm">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('restaurant-admin.documents.download', $document->id) }}" class="btn btn-success btn-sm">
                            <i class="fa fa-download"></i> Download
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <!-- Document Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Document Information</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Document Type:</strong></td>
                                            <td>{{ $document->document_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Original Filename:</strong></td>
                                            <td>{{ $document->original_filename }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>File Size:</strong></td>
                                            <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                        </tr>
                                        <tr>
                                            <td><strong>MIME Type:</strong></td>
                                            <td>{{ $document->mime_type }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($document->status === 'approved')
                                                    <span class="badge badge-success">{{ ucfirst($document->status) }}</span>
                                                @elseif($document->status === 'rejected')
                                                    <span class="badge badge-danger">{{ ucfirst($document->status) }}</span>
                                                @else
                                                    <span class="badge badge-warning">{{ ucfirst($document->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Uploaded:</strong></td>
                                            <td>{{ $document->uploaded_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        @if($document->expires_at)
                                        <tr>
                                            <td><strong>Expires:</strong></td>
                                            <td>
                                                {{ $document->expires_at->format('M d, Y') }}
                                                @if($document->expires_at->isPast())
                                                    <span class="badge badge-danger ml-2">Expired</span>
                                                @elseif($document->expires_at->diffInDays() <= 30)
                                                    <span class="badge badge-warning ml-2">Expiring Soon</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Restaurant Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Restaurant Information</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Restaurant:</strong></td>
                                            <td>{{ $document->restaurant->restaurant_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $document->restaurant->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $document->restaurant->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>City:</strong></td>
                                            <td>{{ $document->restaurant->city }}, {{ $document->restaurant->state }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($document->restaurant->status === 'approved')
                                                    <span class="badge badge-success">{{ ucfirst($document->restaurant->status) }}</span>
                                                @elseif($document->restaurant->status === 'rejected')
                                                    <span class="badge badge-danger">{{ ucfirst($document->restaurant->status) }}</span>
                                                @else
                                                    <span class="badge badge-warning">{{ ucfirst($document->restaurant->status) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Review Information -->
                    @if($document->reviewed_at || $document->admin_notes || $document->rejection_reason)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Review Information</h4>
                                </div>
                                <div class="card-body">
                                    @if($document->reviewed_at)
                                    <div class="mb-3">
                                        <strong>Reviewed On:</strong> {{ $document->reviewed_at->format('M d, Y h:i A') }}
                                        @if($document->reviewer)
                                        <br><strong>Reviewed By:</strong> {{ $document->reviewer->first_name }} {{ $document->reviewer->last_name }}
                                        @endif
                                    </div>
                                    @endif

                                    @if($document->admin_notes)
                                    <div class="mb-3">
                                        <strong>Admin Notes:</strong>
                                        <p class="mt-2 p-3 bg-light border rounded">{{ $document->admin_notes }}</p>
                                    </div>
                                    @endif

                                    @if($document->rejection_reason)
                                    <div class="mb-3">
                                        <strong>Rejection Reason:</strong>
                                        <p class="mt-2 p-3 bg-danger-light border border-danger rounded text-danger">{{ $document->rejection_reason }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Document Preview -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Document Preview</h4>
                                </div>
                                <div class="card-body text-center">
                                    @if(str_contains($document->mime_type, 'image/'))
                                        <img src="{{ asset('storage/' . $document->document_path) }}" 
                                             alt="{{ $document->document_name }}" 
                                             class="img-fluid rounded border"
                                             style="max-height: 500px;">
                                    @elseif(str_contains($document->mime_type, 'pdf'))
                                        <div class="pdf-preview">
                                            <i class="fa fa-file-pdf-o fa-5x text-danger mb-3"></i>
                                            <h5>PDF Document</h5>
                                            <p>{{ $document->original_filename }}</p>
                                            <a href="{{ route('restaurant-admin.documents.download', $document->id) }}" 
                                               class="btn btn-primary">
                                                <i class="fa fa-download"></i> Download to View
                                            </a>
                                        </div>
                                    @else
                                        <div class="file-preview">
                                            <i class="fa fa-file-o fa-5x text-muted mb-3"></i>
                                            <h5>{{ $document->mime_type }}</h5>
                                            <p>{{ $document->original_filename }}</p>
                                            <a href="{{ route('restaurant-admin.documents.download', $document->id) }}" 
                                               class="btn btn-primary">
                                                <i class="fa fa-download"></i> Download File
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('restaurant-admin.documents.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Back to List
                                    </a>
                                </div>
                                <div>
                                    <a href="{{ route('restaurant-admin.documents.edit', $document->id) }}" class="btn btn-warning">
                                        <i class="fa fa-edit"></i> Edit Document
                                    </a>
                                    <a href="{{ route('restaurant-admin.documents.download', $document->id) }}" class="btn btn-success">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                    <form method="POST" action="{{ route('restaurant-admin.documents.destroy', $document->id) }}" class="d-inline-block" 
                                          onsubmit="return confirm('Are you sure you want to delete this document?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Add any page-specific JavaScript here
});
</script>
@endsection
