@extends('layouts.admin')

@section('title', 'Restaurant Details')

@section('content')
    <div class="container-fluid px-4">
        <!-- Header with Actions -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <h1 class="h3 mb-0">Restaurant Details</h1>
                <span
                    class="badge ms-3 @switch($restaurant->status)
                    @case('pending') bg-warning @break
                    @case('approved') bg-success @break
                    @case('rejected') bg-danger @break
                    @case('suspended') bg-dark @break
                @endswitch">
                    {{ ucfirst($restaurant->status) }}
                </span>
                @if ($restaurant->is_featured)
                    <span class="badge bg-warning ms-2">
                        <i class="fas fa-star"></i> Featured
                    </span>
                @endif
            </div>
            <div class="btn-group">
                <a href="{{ route('restaurant-admin.index') }}" class="btn btn-primary">
                    <i class="fa fa-arrow-left me-1" aria-hidden="true"></i> Back to List
                </a>
                <a href="{{ route('restaurant-admin.edit', $restaurant->id) }}" class="btn btn-warning">
                    <i class="fa fa-edit me-1" aria-hidden="true"></i> Edit Restaurant
                </a>
                <button type="button" class="btn btn-danger" onclick="deleteRestaurant()">
                    <i class="fa fa-trash me-1" aria-hidden="true"></i> Delete
                </button>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="row">
            <!-- Left Column: Restaurant Info & Image -->
            <div class="col-lg-4">
                <!-- Restaurant Profile Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center">
                        @php
                            $imagePath = null;
                            if ($restaurant->image_url) {
                                $fullPath = public_path('storage/' . $restaurant->image_url);
                                if (file_exists($fullPath)) {
                                    $imagePath = asset('storage/' . $restaurant->image_url);
                                }
                            }
                        @endphp

                        <div class="restaurant-image-wrapper mb-4">
                            @if ($imagePath)
                                <img src="{{ $imagePath }}" alt="{{ $restaurant->restaurant_name }}"
                                    class="img-fluid rounded-circle restaurant-profile-img"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            @endif
                            <div class="restaurant-placeholder {{ $imagePath ? 'd-none' : 'd-flex' }}">
                                <i class="fas fa-utensils fa-3x"></i>
                            </div>
                        </div>

                        <h2 class="h4 mb-1">{{ $restaurant->restaurant_name }}</h2>
                        <p class="text-muted mb-3">{{ $restaurant->slug }}</p>

                        @if ($restaurant->description)
                            <p class="mb-4">{{ $restaurant->description }}</p>
                        @endif

                        <!-- Quick Stats -->
                        <div class="row g-3 mb-4">
                            <div class="col-4">
                                <div class="stat-box">
                                    <div class="stat-number">{{ $restaurant->total_orders }}</div>
                                    <div class="stat-label">Orders</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-box">
                                    <div class="stat-number">{{ number_format($restaurant->average_rating, 1) }}</div>
                                    <div class="stat-label">Rating</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-box">
                                    <div class="stat-number">{{ $restaurant->total_reviews }}</div>
                                    <div class="stat-label">Reviews</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">
                            <i class="fa fa-address-book me-2 text-primary" aria-hidden="true"></i>Contact Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3">
                                <i class="fa fa-envelope me-2 text-primary" aria-hidden="true"></i>
                                <a href="mailto:{{ $restaurant->email }}" class="text-decoration-none">
                                    {{ $restaurant->email }}
                                </a>
                            </li>
                            <li class="mb-3">
                                <i class="fa fa-phone me-2 text-primary" aria-hidden="true"></i>
                                <a href="tel:{{ $restaurant->phone }}" class="text-decoration-none">
                                    {{ $restaurant->phone }}
                                </a>
                            </li>
                            @if ($restaurant->website_url)
                                <li class="mb-3">
                                    <i class="fa fa-globe me-2 text-primary" aria-hidden="true"></i>
                                    <a href="{{ $restaurant->website_url }}" target="_blank" class="text-decoration-none">
                                        {{ $restaurant->website_url }}
                                    </a>
                                </li>
                            @endif
                            @if ($restaurant->contact_person_name)
                                <li class="mb-0">
                                    <i class="fa fa-user me-2 text-primary" aria-hidden="true"></i>
                                    {{ $restaurant->contact_person_name }}
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Associated Users Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">
                            <i class="fa fa-users me-2 text-primary" aria-hidden="true"></i>Associated Users
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @if ($restaurant->user)
                                <li class="mb-3">
                                    <i class="fa fa-user-circle me-2 text-primary" aria-hidden="true"></i>
                                    <strong>Owner:</strong> {{ $restaurant->user->first_name }}
                                    {{ $restaurant->user->last_name }}
                                    <br>
                                    <small class="text-muted ms-4">{{ $restaurant->user->email }}</small>
                                </li>
                            @endif
                            @if ($restaurant->locationAdmin)
                                <li class="mb-3">
                                    <i class="fa fa-user me-2 text-success" aria-hidden="true"></i>
                                    <strong>Location Admin:</strong> {{ $restaurant->locationAdmin->first_name }}
                                    {{ $restaurant->locationAdmin->last_name }}
                                    <br>
                                    <small class="text-muted ms-4">{{ $restaurant->locationAdmin->email }}</small>
                                </li>
                            @endif
                            @if ($restaurant->tenant)
                                <li class="mb-0">
                                    <i class="fa fa-building me-2 text-warning" aria-hidden="true"></i>
                                    <strong>Tenant:</strong> {{ $restaurant->tenant->name }}
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Manage Status Card -->
                <div class="card shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">
                            <i class="fa fa-cog me-2 text-primary" aria-hidden="true"></i>Manage Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-3">
                            <li class="mb-3">
                                <i class="fa fa-calendar-plus me-2 text-primary" aria-hidden="true"></i>
                                <strong>Created:</strong> {{ $restaurant->created_at->format('M d, Y H:i') }}
                            </li>
                            <li class="mb-3">
                                <i class="fa fa-calendar-check me-2 text-primary" aria-hidden="true"></i>
                                <strong>Last Updated:</strong> {{ $restaurant->updated_at->format('M d, Y H:i') }}
                            </li>
                            @if ($restaurant->approved_at)
                                <li class="mb-0">
                                    <i class="fa fa-check-circle me-2 text-success" aria-hidden="true"></i>
                                    <strong>Approved On:</strong> {{ $restaurant->approved_at->format('M d, Y H:i') }}
                                </li>
                            @endif
                        </ul>
                        <div class="d-grid gap-2">
                            @if ($restaurant->status === 'pending')
                                <button class="btn btn-success btn-sm" onclick="updateStatus('approved')">
                                    <i class="fa fa-check me-1" aria-hidden="true"></i> Approve
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="updateStatus('rejected')">
                                    <i class="fa fa-times me-1" aria-hidden="true"></i> Reject
                                </button>
                            @elseif($restaurant->status === 'approved')
                                <button class="btn btn-warning btn-sm" onclick="updateStatus('suspended')">
                                    <i class="fa fa-ban me-1" aria-hidden="true"></i> Suspend
                                </button>
                            @elseif($restaurant->status === 'suspended')
                                <button class="btn btn-success btn-sm" onclick="updateStatus('approved')">
                                    <i class="fa fa-check me-1" aria-hidden="true"></i> Reactivate
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Details & Information -->
            <div class="col-lg-8">
                <!-- Business Details Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">
                            <i class="fas fa-store me-2"></i>Business Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-item mb-3">
                                    <label class="text-muted small">Cuisine Type</label>
                                    <p class="mb-0">{{ $restaurant->cuisine_type ?? 'Not specified' }}</p>
                                </div>
                                <div class="detail-item mb-3">
                                    <label class="text-muted small">Minimum Order</label>
                                    <p class="mb-0">${{ number_format($restaurant->minimum_order_amount, 2) }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item mb-3">
                                    <label class="text-muted small">Delivery Radius</label>
                                    <p class="mb-0">{{ $restaurant->delivery_radius_km }} km</p>
                                </div>
                                <div class="detail-item mb-3">
                                    <label class="text-muted small">Delivery Fee</label>
                                    <p class="mb-0">${{ number_format($restaurant->base_delivery_fee, 2) }}</p>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="detail-item">
                                    <label class="text-muted small">Commission Rate:
                                        {{ $restaurant->restaurant_commission_percentage }}%</label>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar bg-success"
                                                style="width: {{ $restaurant->restaurant_commission_percentage }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address & Location Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>Location Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="detail-item mb-3">
                                    <label class="text-muted small">Address</label>
                                    <p class="mb-1">{{ $restaurant->address }}</p>
                                    <p class="mb-0 text-muted">
                                        {{ $restaurant->cityRelation ? $restaurant->cityRelation->name : $restaurant->city }},
                                        {{ $restaurant->stateRelation ? $restaurant->stateRelation->name : $restaurant->state }}
                                        {{ $restaurant->postal_code }}
                                    </p>
                                </div>
                            </div>
                            @if ($restaurant->latitude && $restaurant->longitude)
                                <div class="col-md-4">
                                    <div class="detail-item">
                                        <label class="text-muted small">Coordinates</label>
                                        <p class="mb-0">
                                            {{ number_format($restaurant->latitude, 6) }},
                                            {{ number_format($restaurant->longitude, 6) }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Business Hours Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">
                            <i class="fas fa-clock me-2"></i>Business Hours
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($restaurant->business_hours)
                            @php
                                $businessHours = is_string($restaurant->business_hours)
                                    ? json_decode($restaurant->business_hours, true)
                                    : $restaurant->business_hours;

                                $days = [
                                    'monday' => 'Monday',
                                    'tuesday' => 'Tuesday',
                                    'wednesday' => 'Wednesday',
                                    'thursday' => 'Thursday',
                                    'friday' => 'Friday',
                                    'saturday' => 'Saturday',
                                    'sunday' => 'Sunday',
                                ];
                            @endphp

                            @if ($businessHours && is_array($businessHours))
                                <div class="business-hours-grid">
                                    @foreach ($days as $key => $day)
                                        @php
                                            $dayData = $businessHours[$key] ?? null;
                                            $isOpen = $dayData['is_open'] ?? false;
                                            $openingTime = $dayData['opening_time'] ?? null;
                                            $closingTime = $dayData['closing_time'] ?? null;
                                        @endphp
                                        <div class="business-hour-item {{ !$isOpen ? 'closed' : '' }}">
                                            <div class="day">{{ $day }}</div>
                                            <div class="time">
                                                @if ($isOpen && $openingTime && $closingTime)
                                                    {{ \Carbon\Carbon::createFromFormat('H:i', $openingTime)->format('h:i A') }}
                                                    -
                                                    {{ \Carbon\Carbon::createFromFormat('H:i', $closingTime)->format('h:i A') }}
                                                @else
                                                    <span class="text-muted">Closed</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-0">No business hours data available</p>
                            @endif
                        @else
                            <p class="text-muted mb-0">Business hours not specified</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Section -->
        @if ($restaurant->documents && $restaurant->documents->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-transparent">
                            <h5 class="mb-0">
                                <i class="fas fa-file-alt me-2"></i>Documents
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Document Type</th>
                                            <th>Status</th>
                                            <th>Verified</th>
                                            <th>Expiry Date</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($restaurant->documents as $document)
                                            <tr>
                                                <td>{{ ucwords(str_replace('_', ' ', $document->document_type)) }}</td>
                                                <td>
                                                    <span
                                                        class="badge @switch($document->status)
                                                @case('pending') bg-warning @break
                                                @case('approved') bg-success @break
                                                @case('rejected') bg-danger @break
                                                @default bg-light text-dark
                                            @endswitch">
                                                        {{ ucfirst($document->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($document->is_verified)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check"></i> Verified
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-clock"></i> Pending
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($document->expires_at)
                                                        <span
                                                            class="{{ $document->expires_at->isPast() ? 'text-danger' : '' }}">
                                                            {{ $document->expires_at->format('M d, Y') }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">No expiry</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('restaurant-admin.documents.view', $document->id) }}"
                                                            class="btn btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('restaurant-admin.documents.download', $document->id) }}"
                                                            class="btn btn-outline-success">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Special Instructions Section -->
        @if ($restaurant->special_instructions)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-info">
                        <div class="card-header bg-info bg-opacity-10 border-info">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Special Instructions
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $restaurant->special_instructions }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Confirmation Modal for Status -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog"
        aria-labelledby="confirmationModalLabel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <div id="confirmIconWrapper" class="icon-circle-lg text-white mx-auto mb-3"
                            style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 40px;">
                            <i id="confirmIcon" class="fa fa-question-circle"></i>
                        </div>
                    </div>
                    <h3 class="mb-2 fw-bold" id="confirmTitle">Confirm Action</h3>
                    <p class="text-muted mb-4" id="confirmMessage">Are you sure?</p>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn" id="confirmActionBtn" onclick="executeConfirmedAction()">
                        <i class="fa fa-check me-2"></i>Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Reason Modal -->
    <div class="modal fade" id="rejectionModal" tabindex="-1" role="dialog" aria-labelledby="rejectionModalLabel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom bg-danger text-white">
                    <h5 class="modal-title" id="rejectionModalLabel">
                        <i class="fa fa-times-circle me-2"></i>Reject Restaurant
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <p class="text-muted mb-3">Please provide a detailed reason for rejecting this restaurant:</p>
                    <textarea class="form-control" id="rejectionReason" rows="5"
                        placeholder="Explain why this restaurant is being rejected..." style="border-color: #dc3545;"></textarea>
                    <small class="text-danger d-block mt-2">This reason will be sent to the restaurant owner.</small>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-danger" onclick="submitRejection()">
                        <i class="fa fa-check me-2"></i>Confirm Rejection
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog"
        aria-labelledby="deleteConfirmModalLabel">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <div class="icon-circle-lg bg-danger text-white mx-auto mb-3"
                            style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 40px;">
                            <i class="fa fa-trash"></i>
                        </div>
                    </div>
                    <h3 class="mb-2 fw-bold text-danger">Delete Restaurant?</h3>
                    <p class="text-muted mb-4">
                        Are you sure you want to delete <strong>{{ $restaurant->restaurant_name }}</strong>? This action
                        cannot be undone and will delete all associated data including orders, menu items, and documents.
                    </p>
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This will permanently remove all restaurant data from the system.
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-danger" onclick="executeDelete()">
                        <i class="fa fa-trash me-2"></i>Yes, Delete Restaurant
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let pendingAction = null;
        let pendingRejectionReason = null;

        function updateStatus(newStatus) {
            if (newStatus === 'rejected') {
                // Show rejection reason modal
                const rejectionModal = new bootstrap.Modal(document.getElementById('rejectionModal'));
                rejectionModal.show();
                pendingAction = 'reject';
            } else if (newStatus === 'approved') {
                // Show approval confirmation modal
                showConfirmationModal('Approve Restaurant?',
                    'Are you sure you want to approve this restaurant? It will become active and visible to customers.',
                    'fa-check-circle', 'btn-success', 'Yes, Approve', 'approved');
            } else if (newStatus === 'suspended') {
                // Show suspension confirmation modal
                showConfirmationModal('Suspend Restaurant?',
                    'Are you sure you want to suspend this restaurant? It will no longer accept orders from customers.',
                    'fa-pause-circle', 'btn-warning', 'Yes, Suspend', 'suspended');
            }
        }

        function showConfirmationModal(title, message, icon, btnColor, btnText, action) {
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmMessage').textContent = message;
            document.getElementById('confirmIcon').className = 'fa ' + icon;

            const iconWrapper = document.getElementById('confirmIconWrapper');
            iconWrapper.className = 'icon-circle-lg text-white mx-auto mb-3';
            iconWrapper.style.cssText =
                'width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 40px;';

            if (btnColor === 'btn-success') {
                iconWrapper.style.backgroundColor = '#10b981';
            } else if (btnColor === 'btn-warning') {
                iconWrapper.style.backgroundColor = '#f59e0b';
            } else if (btnColor === 'btn-danger') {
                iconWrapper.style.backgroundColor = '#ef4444';
            }

            const confirmActionBtn = document.getElementById('confirmActionBtn');
            confirmActionBtn.className = 'btn ' + btnColor;
            confirmActionBtn.innerHTML = '<i class="fa fa-check me-2"></i>' + btnText;

            pendingAction = action;
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            confirmModal.show();
        }

        function executeConfirmedAction() {
            if (pendingAction === 'approved') {
                performStatusUpdate('approved', null);
                bootstrap.Modal.getInstance(document.getElementById('confirmationModal')).hide();
            } else if (pendingAction === 'suspended') {
                performStatusUpdate('suspended', null);
                bootstrap.Modal.getInstance(document.getElementById('confirmationModal')).hide();
            }
        }

        function submitRejection() {
            let rejectionReason = document.getElementById('rejectionReason').value.trim();

            if (!rejectionReason) {
                alert('Please provide a rejection reason.');
                return;
            }

            if (rejectionReason.length < 10) {
                alert('Please provide at least 10 characters for the rejection reason.');
                return;
            }

            performStatusUpdate('rejected', rejectionReason);
            bootstrap.Modal.getInstance(document.getElementById('rejectionModal')).hide();
        }

        function performStatusUpdate(status, rejectionReason) {
            let data = {
                status: status,
                _token: '{{ csrf_token() }}'
            };

            if (rejectionReason) {
                data.rejection_reason = rejectionReason;
            }

            $.post('{{ route('restaurant-admin.management.update-status', $restaurant->id) }}', data)
                .done(function(response) {
                    location.reload();
                })
                .fail(function(xhr) {
                    let errorMessage = 'Unknown error';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            let parsed = JSON.parse(xhr.responseText);
                            errorMessage = parsed.message || parsed.error || errorMessage;
                        } catch (e) {
                            errorMessage = xhr.responseText;
                        }
                    }
                    alert('Failed to update status: ' + errorMessage);
                });
        }

        function deleteRestaurant() {
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            deleteModal.show();
        }

        function executeDelete() {
            $.ajax({
                url: '{{ route('restaurant-admin.management.destroy', $restaurant->id) }}',
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    window.location.href = '{{ route('restaurant-admin.list') }}';
                },
                error: function(xhr) {
                    bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal')).hide();
                    alert('Failed to delete restaurant: ' + (xhr.responseJSON ? xhr.responseJSON.message :
                        'Unknown error'));
                }
            });
        }
    </script>
@endpush

@push('styles')
    <style>
        /* Modern Card Styling */
        .card {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
        }

        /* Restaurant Profile Image */
        .restaurant-image-wrapper {
            position: relative;
            margin: 0 auto;
            width: 200px;
            height: 200px;
        }

        .restaurant-profile-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .restaurant-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            border: 4px solid #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Stat Boxes */
        .stat-box {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            transition: background-color 0.2s;
        }

        .stat-box:hover {
            background: #e9ecef;
        }

        .stat-number {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2d3748;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            color: #718096;
            margin-top: 0.25rem;
        }

        /* Detail Items */
        .detail-item {
            padding: 0.5rem 0;
        }

        .detail-item label {
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
            display: block;
        }

        /* Business Hours Grid */
        .business-hours-grid {
            display: grid;
            gap: 0.75rem;
        }

        .business-hour-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 8px;
            transition: background-color 0.2s;
        }

        .business-hour-item:hover {
            background: #e9ecef;
        }

        .business-hour-item.closed {
            opacity: 0.7;
        }

        .business-hour-item .day {
            font-weight: 600;
            color: #2d3748;
        }

        .business-hour-item .time {
            color: #718096;
        }

        /* User Cards */
        .user-card {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .user-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: white;
            font-size: 1.25rem;
        }

        .user-info h6 {
            margin-bottom: 0.25rem;
            color: #2d3748;
        }

        /* Badge Customization */
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 600;
            border-radius: 6px;
        }

        /* Status Colors */
        .bg-warning {
            background-color: #fbbf24 !important;
        }

        .bg-success {
            background-color: #10b981 !important;
        }

        .bg-danger {
            background-color: #ef4444 !important;
        }

        .bg-dark {
            background-color: #374151 !important;
        }

        /* Button Styles */
        .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 1rem;
        }

        .btn-outline-primary,
        .btn-outline-success {
            border-width: 1px;
        }

        /* Table Styling */
        .table {
            --bs-table-striped-bg: rgba(0, 0, 0, 0.02);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }

        /* Progress Bar */
        .progress {
            border-radius: 10px;
            background-color: #e2e8f0;
        }

        .progress-bar {
            border-radius: 10px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .restaurant-image-wrapper {
                width: 150px;
                height: 150px;
            }

            .stat-number {
                font-size: 1.5rem;
            }

            .business-hour-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.25rem;
            }

            .user-card {
                flex-direction: column;
                text-align: center;
            }

            .user-icon {
                margin-right: 0;
                margin-bottom: 0.75rem;
            }
        }
    </style>
@endpush
