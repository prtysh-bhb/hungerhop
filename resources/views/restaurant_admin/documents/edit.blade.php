@extends('layouts.admin')

@section('title', 'Edit Document')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit Document</h3>
                    <div class="box-tools float-right">
                        <a href="{{ route('restaurant-admin.documents.index') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-list"></i> Back to Documents
                        </a>
                        <a href="{{ route('restaurant-admin.documents.view', $document->id) }}" class="btn btn-info btn-sm">
                            <i class="fa fa-eye"></i> View Document
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

                    <form action="{{ route('restaurant-admin.documents.update', $document->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Current Document Info -->
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h4 class="card-title">Current Document Information</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td><strong>Restaurant:</strong></td>
                                                        <td>{{ $document->restaurant->restaurant_name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Current File:</strong></td>
                                                        <td>{{ $document->original_filename }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>File Size:</strong></td>
                                                        <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td><strong>Uploaded:</strong></td>
                                                        <td>{{ $document->uploaded_at->format('M d, Y') }}</td>
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
                                                        <td><strong>Actions:</strong></td>
                                                        <td>
                                                            <a href="{{ route('restaurant-admin.documents.download', $document->id) }}" 
                                                               class="btn btn-sm btn-success">
                                                                <i class="fa fa-download"></i> Download
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Form -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="document_type">Document Type <span class="text-danger">*</span></label>
                                    <select class="form-control" id="document_type" name="document_type" required>
                                        @foreach($documentTypes as $key => $type)
                                            <option value="{{ $key }}" {{ old('document_type', $document->document_type) == $key ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="pending" {{ old('status', $document->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ old('status', $document->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ old('status', $document->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Replace File -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="document_file">Replace Document File (Optional)</label>
                                    <input type="file" class="form-control-file" id="document_file" name="document_file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <small class="form-text text-muted">
                                        Leave empty to keep current file. Supported formats: PDF, JPG, PNG, DOC, DOCX. Maximum size: 10MB
                                    </small>
                                    <div id="filePreview" class="mt-2" style="display: none;">
                                        <div class="card" style="max-width: 300px;">
                                            <div class="card-body">
                                                <div id="fileInfo"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Expiry Date -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expires_at">Expiry Date</label>
                                    <input type="date" class="form-control" id="expires_at" name="expires_at" 
                                           value="{{ old('expires_at', $document->expires_at ? $document->expires_at->format('Y-m-d') : '') }}" 
                                           min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                    <small class="form-text text-muted">Leave empty if document doesn't expire</small>
                                </div>
                            </div>
                            
                            <!-- Rejection Reason (show only if status is rejected) -->
                            <div class="col-md-12">
                                <div class="form-group" id="rejection-reason-group" style="{{ old('status', $document->status) == 'rejected' ? '' : 'display: none;' }}">
                                    <label for="rejection_reason">Rejection Reason <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" 
                                              placeholder="Please provide reason for rejection">{{ old('rejection_reason', $document->rejection_reason) }}</textarea>
                                </div>
                            </div>
                            
                            <!-- Admin Notes -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="admin_notes">Admin Notes</label>
                                    <textarea class="form-control" id="admin_notes" name="admin_notes" rows="4" 
                                              placeholder="Add any administrative notes about this document">{{ old('admin_notes', $document->admin_notes) }}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group text-right">
                            <a href="{{ route('restaurant-admin.documents.index') }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Update Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Show/hide rejection reason based on status
    $('#status').on('change', function() {
        const status = $(this).val();
        const rejectionGroup = $('#rejection-reason-group');
        const rejectionTextarea = $('#rejection_reason');
        
        if (status === 'rejected') {
            rejectionGroup.show();
            rejectionTextarea.prop('required', true);
        } else {
            rejectionGroup.hide();
            rejectionTextarea.prop('required', false);
            rejectionTextarea.val('');
        }
    });
    
    // File preview functionality
    $('#document_file').on('change', function() {
        const file = this.files[0];
        const preview = $('#filePreview');
        const fileInfo = $('#fileInfo');
        
        if (file) {
            const fileSize = (file.size / 1024).toFixed(2);
            const fileName = file.name;
            const fileType = file.type;
            
            let fileIcon = 'fa-file-o';
            if (fileType.includes('pdf')) {
                fileIcon = 'fa-file-pdf-o text-danger';
            } else if (fileType.includes('image')) {
                fileIcon = 'fa-file-image-o text-primary';
            } else if (fileType.includes('word') || fileType.includes('document')) {
                fileIcon = 'fa-file-word-o text-info';
            }
            
            fileInfo.html(`
                <div class="d-flex align-items-center">
                    <i class="fa ${fileIcon} fa-2x mr-3"></i>
                    <div>
                        <strong>${fileName}</strong><br>
                        <small class="text-muted">${fileSize} KB</small>
                    </div>
                </div>
            `);
            
            preview.show();
        } else {
            preview.hide();
        }
    });
    
    // Form validation
    $('form').on('submit', function(e) {
        const status = $('#status').val();
        const rejectionReason = $('#rejection_reason').val();
        
        if (status === 'rejected' && !rejectionReason.trim()) {
            e.preventDefault();
            alert('Please provide a rejection reason when rejecting a document.');
            $('#rejection_reason').focus();
            return false;
        }
    });
});
</script>
@endsection
