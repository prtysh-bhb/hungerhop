@extends('layouts.admin')

@section('title', 'Restaurant Details')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Header Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="h4 mb-1 text-dark">{{ $restaurant->restaurant_name }}</h2>
                                <p class="text-muted mb-0">Restaurant Details</p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('restaurant-admin.list') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-list me-1"></i> Back to List
                                </a>
                                <a href="{{ route('restaurant-admin.edit', $restaurant->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit me-1"></i> Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <!-- Basic Information -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent border-bottom-0 py-3">
                                <h5 class="mb-0 text-primary">
                                    <i class="fa fa-info-circle me-2"></i>Basic Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Restaurant Name</label>
                                        <p class="mb-2 fw-semibold">{{ $restaurant->restaurant_name }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Slug</label>
                                        <p class="mb-2"><code class="bg-light px-2 py-1 rounded">{{ $restaurant->slug }}</code></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Contact Person</label>
                                        <p class="mb-2">{{ $restaurant->contact_person_name ?? 'Not specified' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Status</label>
                                        <p class="mb-2">
                                            @if ($restaurant->status === 'approved')
                                                <span class="badge bg-success">{{ ucfirst($restaurant->status) }}</span>
                                            @elseif($restaurant->status === 'rejected')
                                                <span class="badge bg-danger">{{ ucfirst($restaurant->status) }}</span>
                                            @elseif($restaurant->status === 'suspended')
                                                <span class="badge bg-warning">{{ ucfirst($restaurant->status) }}</span>
                                            @else
                                                <span class="badge bg-info">{{ ucfirst($restaurant->status) }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Email</label>
                                        <p class="mb-2">{{ $restaurant->email }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Phone</label>
                                        <p class="mb-2">{{ $restaurant->phone }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Website</label>
                                        <p class="mb-2">
                                            @if ($restaurant->website_url)
                                                <a href="{{ $restaurant->website_url }}" target="_blank" class="text-decoration-none">
                                                    {{ $restaurant->website_url }}
                                                </a>
                                            @else
                                                <span class="text-muted">Not provided</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Cuisine Type</label>
                                        <p class="mb-2">{{ $restaurant->cuisine_type ?? 'Not specified' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent border-bottom-0 py-3">
                                <h5 class="mb-0 text-primary">
                                    <i class="fa fa-map-marker-alt me-2"></i>Location Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Address</label>
                                        <p class="mb-2">{{ $restaurant->address }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">City</label>
                                        <p class="mb-2">{{ $restaurant->cityRelation ? $restaurant->cityRelation->name : 'Not specified' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">State</label>
                                        <p class="mb-2">{{ $restaurant->stateRelation ? $restaurant->stateRelation->name : 'Not specified' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Postal Code</label>
                                        <p class="mb-2">{{ $restaurant->postal_code }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Coordinates</label>
                                        <p class="mb-2">{{ $restaurant->latitude }}, {{ $restaurant->longitude }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small mb-1">Delivery Radius</label>
                                        <p class="mb-2">{{ $restaurant->delivery_radius_km }} km</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        @if ($restaurant->description || $restaurant->business_hours || $restaurant->special_instructions)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent border-bottom-0 py-3">
                                <h5 class="mb-0 text-primary">
                                    <i class="fa fa-file-alt me-2"></i>Additional Information
                                </h5>
                            </div>
                            <div class="card-body">
                                @if ($restaurant->description)
                                    <div class="mb-4">
                                        <label class="form-label text-muted small mb-2">Description</label>
                                        <p class="text-muted mb-0">{{ $restaurant->description }}</p>
                                    </div>
                                @endif

                                @if ($restaurant->business_hours)
                                    <div class="mb-4">
                                        <label class="form-label text-muted small mb-2">Business Hours</label>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless">
                                                <thead>
                                                    <tr>
                                                        <th class="text-muted small">Day</th>
                                                        <th class="text-muted small">Status</th>
                                                        <th class="text-muted small">Opening Time</th>
                                                        <th class="text-muted small">Closing Time</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $businessHours = is_string($restaurant->business_hours) ? json_decode($restaurant->business_hours, true) : $restaurant->business_hours;
                                                        $days = [
                                                            'monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday',
                                                            'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday',
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
                                                            <tr>
                                                                <td class="small">{{ $day }}</td>
                                                                <td>
                                                                    @if ($isOpen)
                                                                        <span class="badge bg-success">Open</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">Closed</span>
                                                                    @endif
                                                                </td>
                                                                <td class="small">
                                                                    @if ($isOpen && $openingTime)
                                                                        {{ \Carbon\Carbon::createFromFormat('H:i', $openingTime)->format('h:i A') }}
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td class="small">
                                                                    @if ($isOpen && $closingTime)
                                                                        {{ \Carbon\Carbon::createFromFormat('H:i', $closingTime)->format('h:i A') }}
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted small">Business hours data not available</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                @if ($restaurant->special_instructions)
                                    <div>
                                        <label class="form-label text-muted small mb-2">Special Instructions</label>
                                        <p class="text-muted mb-0">{{ $restaurant->special_instructions }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Menu Items -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent border-bottom-0 py-3">
                                <h5 class="mb-0 text-primary">
                                    <i class="fa fa-utensils me-2"></i>Menu Items
                                </h5>
                            </div>
                            <div class="card-body">
                                @if ($restaurant->menuItems && $restaurant->menuItems->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless">
                                            <thead>
                                                <tr>
                                                    <th class="text-muted small">Item Name</th>
                                                    <th class="text-muted small">Category</th>
                                                    <th class="text-muted small">Base Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($restaurant->menuItems as $item)
                                                    <tr>
                                                        <td>{{ $item->item_name }}</td>
                                                        <td>{{ $item->category ? $item->category->name : 'N/A' }}</td>
                                                        <td>${{ number_format($item->base_price, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">No menu items found for this restaurant.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-4">
                        <!-- Business Information -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent border-bottom-0 py-3">
                                <h5 class="mb-0 text-primary">
                                    <i class="fa fa-chart-line me-2"></i>Business Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label text-muted small mb-1">Minimum Order</label>
                                        <p class="mb-2 fw-semibold">${{ number_format($restaurant->minimum_order_amount, 2) }}</p>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-muted small mb-1">Delivery Fee</label>
                                        <p class="mb-2 fw-semibold">${{ number_format($restaurant->base_delivery_fee, 2) }}</p>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-muted small mb-1">Commission</label>
                                        <p class="mb-2">{{ $restaurant->restaurant_commission_percentage }}%</p>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-muted small mb-1">Tax Percentage</label>
                                        <p class="mb-2">{{ $restaurant->tax_percentage }}%</p>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-muted small mb-1">Delivery Time</label>
                                        <p class="mb-2">{{ $restaurant->estimated_delivery_time }} minutes</p>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-muted small mb-1">Status</label>
                                        <div class="d-flex gap-2">
                                            @if ($restaurant->is_open)
                                                <span class="badge bg-success">Open</span>
                                            @else
                                                <span class="badge bg-danger">Closed</span>
                                            @endif
                                            @if ($restaurant->accepts_orders)
                                                <span class="badge bg-success">Accepts Orders</span>
                                            @else
                                                <span class="badge bg-danger">Not Accepting</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent border-bottom-0 py-3">
                                <h5 class="mb-0 text-primary">
                                    <i class="fa fa-chart-bar me-2"></i>Statistics
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label text-muted small mb-1">Total Orders</label>
                                        <p class="mb-2 fw-semibold">{{ $restaurant->total_orders ?? 0 }}</p>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-muted small mb-1">Total Reviews</label>
                                        <p class="mb-2 fw-semibold">{{ $restaurant->total_reviews ?? 0 }}</p>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-muted small mb-1">Average Rating</label>
                                        @if ($restaurant->average_rating)
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-semibold">{{ number_format($restaurant->average_rating, 1) }}/5.0</span>
                                                <div class="text-warning">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $restaurant->average_rating)
                                                            <i class="fa fa-star"></i>
                                                        @else
                                                            <i class="fa fa-star-o"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-muted mb-0">No ratings yet</p>
                                        @endif
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-muted small mb-1">Featured</label>
                                        <p class="mb-2">
                                            @if ($restaurant->is_featured)
                                                <span class="badge bg-warning text-dark">Featured</span>
                                            @else
                                                <span class="badge bg-secondary">Not Featured</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Franchise Information -->
                        @if ($restaurant->tenant)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent border-bottom-0 py-3">
                                <h5 class="mb-0 text-primary">
                                    <i class="fa fa-building me-2"></i>Franchise Information
                                </h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $isIndependentRestaurant = $restaurant->isFirstRestaurantForTenant();
                                @endphp

                                @if ($isIndependentRestaurant)
                                    <div class="alert alert-info py-2 mb-3">
                                        <i class="fa fa-info-circle me-1"></i>
                                        <small><strong>Independent Restaurant</strong> - Main restaurant for this franchise</small>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <label class="form-label text-muted small mb-1">Franchise Name</label>
                                            <p class="mb-2">{{ $restaurant->tenant->tenant_name }}</p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-muted small mb-1">Owner Name</label>
                                            <p class="mb-2">{{ $restaurant->tenant->contact_person ?? 'Not specified' }}</p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-muted small mb-1">Franchise Email</label>
                                            <p class="mb-2">{{ $restaurant->tenant->email ?? 'Not specified' }}</p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-muted small mb-1">Franchise Phone</label>
                                            <p class="mb-2">{{ $restaurant->tenant->phone ?? 'Not specified' }}</p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-muted small mb-1">Subscription Plan</label>
                                            <p class="mb-2"><span class="badge bg-primary">{{ strtoupper($restaurant->tenant->subscription_plan) }}</span></p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-muted small mb-1">Total Restaurants</label>
                                            <p class="mb-2">{{ $restaurant->tenant->total_restaurants }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning py-2 mb-3">
                                        <i class="fa fa-building me-1"></i>
                                        <small><strong>Existing Franchise</strong> - Part of an existing franchise</small>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <label class="form-label text-muted small mb-1">Franchise Name</label>
                                            <p class="mb-2">{{ $restaurant->tenant->tenant_name }}</p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-muted small mb-1">Franchise Owner</label>
                                            <p class="mb-2">{{ $restaurant->tenant->contact_person ?? 'Not specified' }}</p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-muted small mb-1">Total Restaurants</label>
                                            <p class="mb-2">{{ $restaurant->tenant->total_restaurants }}</p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-muted small mb-1">Franchise Status</label>
                                            <p class="mb-2">
                                                <span class="badge bg-{{ $restaurant->tenant->status === 'approved' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($restaurant->tenant->status) }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-muted small mb-1">Subscription Plan</label>
                                            <p class="mb-2"><span class="badge bg-primary">{{ strtoupper($restaurant->tenant->subscription_plan) }}</span></p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Images -->
                        @if ($restaurant->image_url || $restaurant->cover_image_url)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent border-bottom-0 py-3">
                                <h5 class="mb-0 text-primary">
                                    <i class="fa fa-images me-2"></i>Images
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @if ($restaurant->image_url)
                                        <div class="col-12">
                                            <label class="form-label text-muted small mb-2">Restaurant Image</label>
                                            <img src="{{ $restaurant->image_url }}" alt="Restaurant Image" class="img-fluid rounded border">
                                        </div>
                                    @endif
                                    @if ($restaurant->cover_image_url)
                                        <div class="col-12">
                                            <label class="form-label text-muted small mb-2">Cover Image</label>
                                            <img src="{{ $restaurant->cover_image_url }}" alt="Cover Image" class="img-fluid rounded border">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Timestamps -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent border-bottom-0 py-3">
                                <h5 class="mb-0 text-primary">
                                    <i class="fa fa-clock me-2"></i>Timestamps
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label class="form-label text-muted small mb-1">Created</label>
                                        <p class="mb-2">{{ $restaurant->created_at->addHours(5.5)->format('M d, Y h:i A') }}</p>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-muted small mb-1">Last Updated</label>
                                        <p class="mb-2">{{ $restaurant->updated_at->addHours(5.5)->format('M d, Y h:i A') }}</p>
                                    </div>
                                    @if ($restaurant->approved_at)
                                        <div class="col-12">
                                            <label class="form-label text-muted small mb-1">Approved</label>
                                            <p class="mb-2">{{ $restaurant->approved_at->addHours(5.5)->format('M d, Y h:i A') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Section -->
                @if ($restaurant->documents && $restaurant->documents->count() > 0)
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-transparent border-bottom-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-primary">
                                <i class="fa fa-file me-2"></i>Documents ({{ $restaurant->documents->count() }})
                            </h5>
                            <a href="{{ route('restaurant-admin.documents.create', ['restaurant_id' => $restaurant->id]) }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus me-1"></i> Add Document
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless">
                                <thead>
                                    <tr>
                                        <th class="text-muted small">Document Type</th>
                                        <th class="text-muted small">Status</th>
                                        <th class="text-muted small">Uploaded</th>
                                        <th class="text-muted small">Expires</th>
                                        <th class="text-muted small">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($restaurant->documents as $document)
                                        <tr>
                                            <td>{{ $document->document_name }}</td>
                                            <td>
                                                @if ($document->status === 'approved')
                                                    <span class="badge bg-success">{{ ucfirst($document->status) }}</span>
                                                @elseif($document->status === 'rejected')
                                                    <span class="badge bg-danger">{{ ucfirst($document->status) }}</span>
                                                @else
                                                    <span class="badge bg-warning">{{ ucfirst($document->status) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $document->uploaded_at->format('M d, Y') }}</td>
                                            <td>
                                                @if ($document->expires_at)
                                                    {{ $document->expires_at->format('M d, Y') }}
                                                    @if ($document->expires_at->isPast())
                                                        <span class="badge bg-danger ms-1">Expired</span>
                                                    @elseif($document->expires_at->diffInDays() <= 30)
                                                        <span class="badge bg-warning ms-1">Soon</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No expiry</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('restaurant-admin.documents.view', $document->id) }}" class="btn btn-outline-info">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('restaurant-admin.documents.download', $document->id) }}" class="btn btn-outline-success">
                                                        <i class="fa fa-download"></i>
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
                @endif

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-4 p-3 bg-light rounded">
                    <a href="{{ route('restaurant-admin.list') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Back to List
                    </a>
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('restaurant-admin.destroy', $restaurant->id) }}" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this restaurant? This action cannot be undone.');">
                        <a href="{{ route('restaurant-admin.edit', $restaurant->id) }}" class="btn btn-warning">
                            <i class="fa fa-edit me-1"></i> Edit Restaurant
                        </a>
                        <a href="{{ route('restaurant-admin.documents.create', ['restaurant_id' => $restaurant->id]) }}" class="btn btn-primary">
                            <i class="fa fa-plus me-1"></i> Add Document
                        </a>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-trash me-1"></i> Delete
                            </button>
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
            // Add any page-specific JavaScript here
        });
    </script>
@endsection

<style>
.card {
    border-radius: 12px;
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.badge {
    font-size: 0.75em;
    font-weight: 500;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
}

.table-sm th,
.table-sm td {
    padding: 0.5rem;
}

.form-label {
    font-weight: 500;
}

.alert {
    border-radius: 8px;
    border: none;
}
</style>