@extends('layouts.admin')

@section('title', 'Restaurant Details')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Restaurant Details</h3>
                        <div class="box-tools float-right">
                            <a href="{{ route('restaurant-admin.management.edit', $restaurant->id) }}"
                                class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Edit Restaurant
                            </a>
                            <a href="{{ route('restaurant-admin.management.index') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-list"></i> Back to Management
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <!-- Restaurant Image -->
                            <div class="col-md-4">
                                <div class="text-center">
                                    @php
                                        $imagePath = null;
                                        if ($restaurant->image_url) {
                                            $fullPath = public_path('storage/' . $restaurant->image_url);
                                            if (file_exists($fullPath)) {
                                                $imagePath = asset('storage/' . $restaurant->image_url);
                                            }
                                        }
                                    @endphp

                                    @if ($imagePath)
                                        <img src="{{ $imagePath }}" alt="{{ $restaurant->restaurant_name }}"
                                            class="img-fluid rounded"
                                            style="max-width: 100%; height: 200px; object-fit: cover;"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="bg-secondary text-white align-items-center justify-content-center rounded"
                                            style="height: 200px; display:none;">
                                            <i class="fa fa-utensils fa-3x"></i>
                                        </div>
                                    @else
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded"
                                            style="height: 200px;">
                                            <i class="fa fa-utensils fa-3x"></i>
                                        </div>
                                    @endif

                                    <div class="mt-3">
                                        <span
                                            class="badge badge-lg 
                                        @switch($restaurant->status)
                                            @case('pending') badge-warning @break
                                            @case('approved') badge-success @break
                                            @case('rejected') badge-danger @break
                                            @case('suspended') badge-secondary @break
                                            @default badge-light
                                        @endswitch
                                    ">
                                            {{ ucfirst($restaurant->status) }}
                                        </span>

                                        @if ($restaurant->is_featured)
                                            <span class="badge badge-lg badge-warning ml-2">
                                                <i class="fa fa-star"></i> Featured
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Restaurant Information -->
                            <div class="col-md-8">
                                <h2 class="text-center">{{ $restaurant->restaurant_name }}</h2>
                                <p class="text-muted text-center">{{ $restaurant->slug }}</p>
                                @if ($restaurant->contact_person_name)
                                    <p class="text-center"><strong>Contact Person:</strong>
                                        {{ $restaurant->contact_person_name }}</p>
                                @endif
                                @if ($restaurant->description)
                                    <p>{{ $restaurant->description }}</p>
                                @endif
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3>Business Details</h3>
                                        <ul class="list-unstyled">
                                            <li><strong>Cuisine:</strong> {{ $restaurant->cuisine_type ?? 'N/A' }}</li>
                                            <li><strong>Delivery Radius:</strong> {{ $restaurant->delivery_radius_km }} km
                                            </li>
                                            <li><strong>Min Order:</strong>
                                                ${{ number_format($restaurant->minimum_order_amount, 2) }}</li>
                                            <li><strong>Delivery Fee:</strong>
                                                ${{ number_format($restaurant->base_delivery_fee, 2) }}</li>
                                        </ul>
                                    </div>

                                    <div class="col-md-6">
                                        <h3>Contact Information</h3>
                                        <ul class="list-unstyled">
                                            <li><strong>Email:</strong> {{ $restaurant->email }}</li>
                                            <li><strong>Phone:</strong> {{ $restaurant->phone }}</li>
                                            @if ($restaurant->website_url)
                                                <li><strong>Website:</strong> <a href="{{ $restaurant->website_url }}"
                                                        target="_blank">{{ $restaurant->website_url }}</a></li>
                                            @endif
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Address and Location -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h4>Address</h4>
                                <p>
                                    {{ $restaurant->address }}<br>
                                    {{ $restaurant->cityRelation ? $restaurant->cityRelation->name : $restaurant->city }},
                                    {{ $restaurant->stateRelation ? $restaurant->stateRelation->name : $restaurant->state }}
                                    {{ $restaurant->postal_code }}
                                </p>

                                @if ($restaurant->latitude && $restaurant->longitude)
                                    <p><strong>Coordinates:</strong> {{ $restaurant->latitude }},
                                        {{ $restaurant->longitude }}</p>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <h4>Business Hours</h4>
                                @if ($restaurant->business_hours)
                                    <div class="business-hours">
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
                                            @foreach ($days as $key => $day)
                                                @php
                                                    $dayData = $businessHours[$key] ?? null;
                                                    $isOpen = $dayData['is_open'] ?? false;
                                                    $openingTime = $dayData['opening_time'] ?? null;
                                                    $closingTime = $dayData['closing_time'] ?? null;
                                                @endphp
                                                <div class="d-flex justify-content-between">
                                                    <span><strong>{{ $day }}:</strong></span>
                                                    <span>
                                                        @if ($isOpen && $openingTime && $closingTime)
                                                            {{ \Carbon\Carbon::createFromFormat('H:i', $openingTime)->format('h:i A') }}
                                                            -
                                                            {{ \Carbon\Carbon::createFromFormat('H:i', $closingTime)->format('h:i A') }}
                                                        @else
                                                            <span class="text-muted">Closed</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted">Business hours data format issue</p>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-muted">No business hours specified</p>
                                @endif
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-blue"><i class="fa fa-shopping-bag"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Orders</span>
                                        <span class="info-box-number">{{ $restaurant->total_orders }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-yellow"><i class="fa fa-star"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Average Rating</span>
                                        <span
                                            class="info-box-number">{{ number_format($restaurant->average_rating, 1) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green"><i class="fa fa-comments"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Reviews</span>
                                        <span class="info-box-number">{{ $restaurant->total_reviews }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <span class="info-box-icon bg-red"><i class="fa fa-percent"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Commission</span>
                                        <span
                                            class="info-box-number">{{ $restaurant->restaurant_commission_percentage }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        @if ($restaurant->special_instructions)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h4>Special Instructions</h4>
                                    <div class="alert alert-info">
                                        {{ $restaurant->special_instructions }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Associated Users -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h4>Associated Users</h4>
                                <ul class="list-unstyled">
                                    @if ($restaurant->user)
                                        <li><strong>Owner:</strong> {{ $restaurant->user->first_name }}
                                            {{ $restaurant->user->last_name }} ({{ $restaurant->user->email }})</li>
                                    @endif
                                    @if ($restaurant->locationAdmin)
                                        <li><strong>Location Admin:</strong> {{ $restaurant->locationAdmin->first_name }}
                                            {{ $restaurant->locationAdmin->last_name }}
                                            ({{ $restaurant->locationAdmin->email }})</li>
                                    @endif
                                    @if ($restaurant->tenant)
                                        <li><strong>Tenant:</strong> {{ $restaurant->tenant->name }}</li>
                                    @endif
                                </ul>
                            </div>

                            <div class="col-md-6">
                                <h4>Status Information</h4>
                                <ul class="list-unstyled">
                                    <li><strong>Created:</strong> {{ $restaurant->created_at->format('M d, Y H:i') }}</li>
                                    <li><strong>Updated:</strong> {{ $restaurant->updated_at->format('M d, Y H:i') }}</li>
                                    @if ($restaurant->approved_at)
                                        <li><strong>Approved:</strong> {{ $restaurant->approved_at->format('M d, Y H:i') }}
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <!-- Documents Section -->
                        @if ($restaurant->documents && $restaurant->documents->count() > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h4>Documents</h4>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Verified</th>
                                                    <th>Expires</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($restaurant->documents as $document)
                                                    <tr>
                                                        <td>{{ ucwords(str_replace('_', ' ', $document->document_type)) }}
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge 
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
                                                            @if ($document->is_verified)
                                                                <span class="badge badge-success"><i
                                                                        class="fa fa-check"></i></span>
                                                            @else
                                                                <span class="badge badge-secondary"><i
                                                                        class="fa fa-clock-o"></i></span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($document->expires_at)
                                                                {{ $document->expires_at->format('M d, Y') }}
                                                            @else
                                                                <span class="text-muted">No Expiry</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('restaurant-admin.documents.view', $document->id) }}"
                                                                class="btn btn-xs btn-info">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('restaurant-admin.documents.download', $document->id) }}"
                                                                class="btn btn-xs btn-success">
                                                                <i class="fa fa-download"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="box-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="btn-group">
                                    @if ($restaurant->status === 'pending')
                                        <button type="button" class="btn btn-success"
                                            onclick="updateStatus('approved')">
                                            <i class="fa fa-check"></i> Approve
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="updateStatus('rejected')">
                                            <i class="fa fa-times"></i> Reject
                                        </button>
                                    @elseif($restaurant->status === 'approved')
                                        <button type="button" class="btn btn-warning"
                                            onclick="updateStatus('suspended')">
                                            <i class="fa fa-ban"></i> Suspend
                                        </button>
                                    @elseif($restaurant->status === 'suspended')
                                        <button type="button" class="btn btn-success"
                                            onclick="updateStatus('approved')">
                                            <i class="fa fa-check"></i> Reactivate
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('restaurant-admin.management.edit', $restaurant->id) }}"
                                    class="btn btn-warning">
                                    <i class="fa fa-edit"></i> Edit Restaurant
                                </a>
                                <button type="button" class="btn btn-danger" onclick="deleteRestaurant()">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateStatus(newStatus) {
            let confirmMessage = `Are you sure you want to ${newStatus} this restaurant?`;
            let rejectionReason = null;

            if (newStatus === 'rejected') {
                rejectionReason = prompt('Please provide a reason for rejection:');
                if (rejectionReason === null) {
                    return; // User cancelled
                }
                if (rejectionReason.trim() === '') {
                    alert('Rejection reason is required.');
                    return;
                }
            }

            if (confirm(confirmMessage)) {
                let data = {
                    status: newStatus,
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
        }

        function deleteRestaurant() {
            if (confirm(
                    'Are you sure you want to delete this restaurant? This action cannot be undone and will delete all associated data.'
                )) {
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
                        alert('Failed to delete restaurant: ' + (xhr.responseJSON ? xhr.responseJSON.message :
                            'Unknown error'));
                    }
                });
            }
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
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
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
            background: rgba(0, 0, 0, 0.2);
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

        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 0.75rem;
        }

        .business-hours {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
        }

        .btn-xs {
            padding: 0.125rem 0.25rem;
            font-size: 0.75rem;
            line-height: 1.5;
            border-radius: 0.15rem;
        }
    </style>
@endpush
