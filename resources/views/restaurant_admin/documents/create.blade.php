@extends('layouts.admin')

@section('title', 'Upload Document')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Upload Document</h3>
                        <div class="box-tools float-right">
                            <a href="{{ route('restaurant-admin.documents.index') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-list"></i> Back to Documents
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa fa-check-circle"></i> {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h5 class="mb-2"><i class="fa fa-exclamation-triangle"></i> Please fix the following
                                    errors:</h5>
                                <ul class="mb-0 pl-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <form action="{{ route('restaurant-admin.documents.store') }}" method="POST"
                            enctype="multipart/form-data" id="documentForm">
                            @csrf

                            <div class="row">
                                <!-- Restaurant Selection -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="restaurant_id">Restaurant <span class="text-danger">*</span></label>
                                        <select class="form-control @error('restaurant_id') is-invalid @enderror"
                                            id="restaurant_id" name="restaurant_id" required>
                                            <option value="">Select Restaurant</option>
                                            @foreach ($restaurants as $restaurant)
                                                <option value="{{ $restaurant->id }}"
                                                    {{ old('restaurant_id') == $restaurant->id ? 'selected' : '' }}>
                                                    {{ $restaurant->restaurant_name }} ({{ $restaurant->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('restaurant_id')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Document Type -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="document_type">Document Type <span class="text-danger">*</span></label>
                                        <select class="form-control @error('document_type') is-invalid @enderror"
                                            id="document_type" name="document_type" required>
                                            <option value="">Select Document Type</option>
                                            <option value="business_license"
                                                {{ old('document_type') == 'business_license' ? 'selected' : '' }}>Business
                                                License</option>
                                            <option value="food_safety_certificate"
                                                {{ old('document_type') == 'food_safety_certificate' ? 'selected' : '' }}>
                                                Food Safety Certificate</option>
                                            <option value="pan_card"
                                                {{ old('document_type') == 'pan_card' ? 'selected' : '' }}>PAN Card
                                            </option>
                                            <option value="gst_certificate"
                                                {{ old('document_type') == 'gst_certificate' ? 'selected' : '' }}>GST
                                                Certificate</option>
                                            <option value="owner_id_proof"
                                                {{ old('document_type') == 'owner_id_proof' ? 'selected' : '' }}>Owner ID
                                                Proof</option>
                                            <option value="bank_details"
                                                {{ old('document_type') == 'bank_details' ? 'selected' : '' }}>Bank Details
                                            </option>
                                            <option value="insurance_certificate"
                                                {{ old('document_type') == 'insurance_certificate' ? 'selected' : '' }}>
                                                Insurance Certificate</option>
                                            <option value="fire_safety_certificate"
                                                {{ old('document_type') == 'fire_safety_certificate' ? 'selected' : '' }}>
                                                Fire Safety Certificate</option>
                                            <option value="trade_license"
                                                {{ old('document_type') == 'trade_license' ? 'selected' : '' }}>Trade
                                                License</option>
                                            <option value="pollution_certificate"
                                                {{ old('document_type') == 'pollution_certificate' ? 'selected' : '' }}>
                                                Pollution Certificate</option>
                                        </select>
                                        @error('document_type')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div> <!-- File Upload -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="document_file">Document File <span class="text-danger">*</span></label>
                                        <input type="file"
                                            class="form-control-file @error('document_file') is-invalid @enderror"
                                            id="document_file" name="document_file" required
                                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                        <small class="form-text text-muted">
                                            Supported formats: PDF, JPG, PNG, DOC, DOCX. Maximum size: 10MB
                                        </small>
                                        @error('document_file')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                        <div id="filePreview" class="mt-2" style="display: none;">
                                            <div class="card" style="max-width: 300px;">
                                                <div class="card-body">
                                                    <div id="fileInfo"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Document Details -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="document_number">Document Number</label>
                                        <input type="text"
                                            class="form-control @error('document_number') is-invalid @enderror"
                                            id="document_number" name="document_number"
                                            value="{{ old('document_number') }}" placeholder="License/Certificate Number"
                                            minlength="3" maxlength="100">
                                        <small class="form-text text-muted">3-100 characters</small>
                                        @error('document_number')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="issued_by">Issued By</label>
                                        <input type="text"
                                            class="form-control @error('issued_by') is-invalid @enderror" id="issued_by"
                                            name="issued_by" value="{{ old('issued_by') }}"
                                            placeholder="Issuing Authority" minlength="3" maxlength="255">
                                        <small class="form-text text-muted">3-255 characters</small>
                                        @error('issued_by')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="issued_date">Issue Date</label>
                                        <input type="date"
                                            class="form-control @error('issued_date') is-invalid @enderror"
                                            id="issued_date" name="issued_date" value="{{ old('issued_date') }}"
                                            max="{{ date('Y-m-d') }}">
                                        <small class="form-text text-muted">Cannot be in the future</small>
                                        @error('issued_date')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expires_at">Expiry Date</label>
                                        <input type="date"
                                            class="form-control @error('expires_at') is-invalid @enderror" id="expires_at"
                                            name="expires_at" value="{{ old('expires_at') }}"
                                            min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}">
                                        <small class="form-text text-muted">Leave blank if document doesn't expire. Must be
                                            after today and issue date.</small>
                                        @error('expires_at')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                            rows="3" placeholder="Additional notes about this document" maxlength="1000">{{ old('description') }}</textarea>
                                        <small class="form-text text-muted">Maximum 1000 characters</small>
                                        @error('description')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Status and Verification -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select class="form-control @error('status') is-invalid @enderror" id="status"
                                            name="status">
                                            <option value="pending"
                                                {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending
                                            </option>
                                            <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>
                                                Approved</option>
                                            <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>
                                                Rejected</option>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-check mt-4">
                                            <input type="checkbox" class="form-check-input" id="is_verified"
                                                name="is_verified" value="1"
                                                {{ old('is_verified') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_verified">
                                                Mark as Verified
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Admin Notes -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="admin_notes">Admin Notes</label>
                                        <textarea class="form-control @error('admin_notes') is-invalid @enderror" id="admin_notes" name="admin_notes"
                                            rows="3" placeholder="Internal notes for administrators" maxlength="1000">{{ old('admin_notes') }}</textarea>
                                        <small class="form-text text-muted">Maximum 1000 characters</small>
                                        @error('admin_notes')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer">
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="fa fa-upload"></i> Upload Document
                                </button>
                                <a href="{{ route('restaurant-admin.documents.index') }}" class="btn btn-secondary">
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
            // Scroll to error alert if validation errors exist
            @if ($errors->any())
                const errorAlert = $('.alert-danger');
                if (errorAlert.length) {
                    $('html, body').animate({
                        scrollTop: errorAlert.offset().top - 100
                    }, 500);
                }
            @endif

            // Date validation
            function validateDates() {
                const issuedDate = $('#issued_date').val();
                const expiryDate = $('#expires_at').val();
                const today = new Date().toISOString().split('T')[0];

                // Check if issue date is not in the future
                if (issuedDate && issuedDate > today) {
                    alert('Issue date cannot be in the future');
                    $('#issued_date').val('');
                    return false;
                }

                // Check if expiry date is after issue date
                if (issuedDate && expiryDate && expiryDate <= issuedDate) {
                    alert('Expiry date must be after issue date');
                    $('#expires_at').val('');
                    return false;
                }

                return true;
            }

            $('#issued_date, #expires_at').on('change', validateDates);

            // File upload preview
            $('#document_file').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    const fileType = file.type;
                    const fileName = file.name;

                    let fileIcon = 'fa-file-o';
                    if (fileType.includes('pdf')) fileIcon = 'fa-file-pdf-o';
                    else if (fileType.includes('image')) fileIcon = 'fa-file-image-o';
                    else if (fileType.includes('word')) fileIcon = 'fa-file-word-o';

                    $('#fileInfo').html(`
                <div class="d-flex align-items-center">
                    <i class="fa ${fileIcon} fa-2x text-primary mr-3"></i>
                    <div>
                        <strong>${fileName}</strong><br>
                        <small class="text-muted">${fileSize} MB - ${fileType}</small>
                    </div>
                </div>
            `);

                    $('#filePreview').show();

                    // Validate file size
                    if (file.size > 10 * 1024 * 1024) { // 10MB
                        alert('File size exceeds 10MB limit. Please select a smaller file.');
                        $(this).val('');
                        $('#filePreview').hide();
                        return;
                    }

                    // Validate file type
                    const allowedTypes = ['.pdf', '.jpg', '.jpeg', '.png', '.doc', '.docx'];
                    const fileExtension = '.' + fileName.split('.').pop().toLowerCase();

                    if (!allowedTypes.includes(fileExtension)) {
                        alert('Invalid file type. Please upload PDF, JPG, PNG, DOC, or DOCX files only.');
                        $(this).val('');
                        $('#filePreview').hide();
                        return;
                    }
                } else {
                    $('#filePreview').hide();
                }
            });

            // Form submission
            $('#documentForm').on('submit', function(e) {
                // Validate restaurant selection
                if (!$('#restaurant_id').val()) {
                    e.preventDefault();
                    alert('Please select a restaurant');
                    $('#restaurant_id').focus();
                    return false;
                }

                // Validate document type
                if (!$('#document_type').val()) {
                    e.preventDefault();
                    alert('Please select a document type');
                    $('#document_type').focus();
                    return false;
                }

                // Validate file selection
                const fileInput = $('#document_file')[0];
                if (!fileInput.files[0]) {
                    e.preventDefault();
                    alert('Please select a document file to upload');
                    $('#document_file').focus();
                    return false;
                }

                // Validate dates one more time
                if (!validateDates()) {
                    e.preventDefault();
                    return false;
                }

                // Validate text lengths
                const documentNumber = $('#document_number').val();
                if (documentNumber && documentNumber.length < 3) {
                    e.preventDefault();
                    alert('Document number must be at least 3 characters');
                    $('#document_number').focus();
                    return false;
                }

                const issuedBy = $('#issued_by').val();
                if (issuedBy && issuedBy.length < 3) {
                    e.preventDefault();
                    alert('Issuing authority must be at least 3 characters');
                    $('#issued_by').focus();
                    return false;
                }

                // Disable submit button to prevent double submission
                $('#submitBtn').prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin"></i> Uploading...');
            }); // Restaurant change handler - could be used to show existing documents
            $('#restaurant_id').on('change', function() {
                const restaurantId = $(this).val();
                if (restaurantId) {
                    // Optionally show existing documents for this restaurant
                    loadExistingDocuments(restaurantId);
                }
            });

            // Auto-populate issue date to today
            $('#issued_date').val(new Date().toISOString().split('T')[0]);
        });

        function loadExistingDocuments(restaurantId) {
            // This function could load and display existing documents for the selected restaurant
            // to help users avoid duplicate uploads
            $.get(`{{ route('restaurant-admin.documents.by-restaurant', ':id') }}`.replace(':id', restaurantId))
                .done(function(data) {
                    if (data.length > 0) {
                        let existingDocs = data.map(doc => `
                    <small class="badge badge-info">${doc.document_type.replace('_', ' ')}</small>
                `).join(' ');

                        $('#restaurant_id').after(`
                    <small class="text-muted">
                        Existing documents: ${existingDocs}
                    </small>
                `);
                    }
                })
                .fail(function() {
                    console.log('Could not load existing documents');
                });
        }
    </script>
@endpush

@push('styles')
    <style>
        .form-control-file {
            border: 2px dashed #ddd;
            border-radius: 4px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        .form-control-file.is-invalid {
            border-color: #dc3545;
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: none;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .invalid-feedback.d-block {
            display: block !important;
        }

        .alert {
            border-radius: 0.35rem;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c2c7;
            color: #842029;
        }

        .alert-danger h5 {
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .alert-danger ul {
            margin-bottom: 0;
        }

        .alert-success {
            background-color: #d1e7dd;
            border-color: #badbcc;
            color: #0f5132;
        }

        .form-control-file:hover {
            border-color: #007bff;
        }

        #filePreview .card {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
        }

        .badge {
            font-size: 0.75em;
            margin-right: 5px;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .form-control-file {
                padding: 15px;
                font-size: 0.9rem;
            }
        }
    </style>
@endpush
