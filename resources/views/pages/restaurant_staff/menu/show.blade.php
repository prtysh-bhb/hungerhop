{{-- Extend the main layout --}}
@extends('layouts.admin')

{{-- Define the title for this page --}}
@section('title', 'Menu Item Details')

{{-- Define the main content for this page --}}
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Menu Item Details</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('restaurant.menu.list') }}">Menu Items</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $menuItem->item_name }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="btn-group ms-2">
                    <a href="{{ route('restaurant.menu.edit', $menuItem->id) }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('restaurant.menu.duplicate', $menuItem->id) }}" class="btn btn-info btn-sm">
                        <i class="fa fa-copy"></i> Duplicate
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Left Column - Item Information -->
            <div class="col-lg-8 col-12">
                <!-- Basic Information Card -->
                <div class="box">
                    <div class="box-header bg-primary">
                        <h4 class="box-title text-white">
                            <i class="fa fa-info-circle me-2"></i>Basic Information
                        </h4>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="fw-600 text-muted mb-1">Item Name</label>
                                    <p class="fs-16 fw-700 text-dark">{{ $menuItem->item_name }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="fw-600 text-muted mb-1">Category</label>
                                    <p class="fs-14">
                                        <span
                                            class="badge badge-light-primary">{{ $menuItem->category->name ?? 'Uncategorized' }}</span>
                                    </p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="fw-600 text-muted mb-1">SKU Code</label>
                                    <p class="fs-14 text-dark">{{ $menuItem->sku }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="fw-600 text-muted mb-1">Base Price</label>
                                    <p class="fs-18 fw-700 text-success">${{ number_format($menuItem->base_price, 2) }}</p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="fw-600 text-muted mb-1">Status</label>
                                    <p>
                                        @if ($menuItem->is_available)
                                            <span class="badge badge-success-light">
                                                <i class="fa fa-check-circle me-1"></i>Available
                                            </span>
                                        @else
                                            <span class="badge badge-danger-light">
                                                <i class="fa fa-times-circle me-1"></i>Unavailable
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="fw-600 text-muted mb-1">Sort Order</label>
                                    <p class="fs-14 text-dark">{{ $menuItem->sort_order }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description & Details Card -->
                <div class="box">
                    <div class="box-header bg-primary">
                        <h4 class="box-title text-white">
                            <i class="fa fa-file-text me-2"></i>Description & Details
                        </h4>
                    </div>
                    <div class="box-body">
                        @if ($menuItem->description)
                            <div class="info-item mb-4">
                                <label class="fw-600 text-muted mb-2">Description</label>
                                <p class="fs-14 text-dark bg-light p-3 rounded">{{ $menuItem->description }}</p>
                            </div>
                        @endif

                        <div class="row">
                            @if ($menuItem->ingredients)
                                <div class="col-md-6">
                                    <div class="info-item mb-3">
                                        <label class="fw-600 text-muted mb-2">Ingredients</label>
                                        <p class="fs-14 text-dark bg-light p-3 rounded">{{ $menuItem->ingredients }}</p>
                                    </div>
                                </div>
                            @endif
                            @if ($menuItem->allergens)
                                <div class="col-md-6">
                                    <div class="info-item mb-3">
                                        <label class="fw-600 text-muted mb-2">Allergens</label>
                                        <p class="fs-14 text-danger bg-light p-3 rounded border border-danger">
                                            {{ $menuItem->allergens }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Additional Information Card -->
                <div class="box">
                    <div class="box-header bg-info">
                        <h4 class="box-title text-white">
                            <i class="fa fa-cogs me-2"></i>Additional Information
                        </h4>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="fw-600 text-muted mb-1">Preparation Time</label>
                                    <p class="fs-14">
                                        <span class="badge badge-light-warning">
                                            <i class="fa fa-clock-o me-1"></i>{{ $menuItem->preparation_time }} minutes
                                        </span>
                                    </p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="fw-600 text-muted mb-1">Availability Schedule</label>
                                    <p class="fs-14">
                                        @if ($menuItem->available_from && $menuItem->available_until)
                                            {{ \Carbon\Carbon::parse($menuItem->available_from)->format('h:i A') }} -
                                            {{ \Carbon\Carbon::parse($menuItem->available_until)->format('h:i A') }}
                                        @else
                                            <span class="text-muted">All day</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <label class="fw-600 text-muted mb-1">Has Variations</label>
                                    <p>
                                        @if ($menuItem->has_variations)
                                            <span class="badge badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="info-item mb-3">
                                    <label class="fw-600 text-muted mb-1">Track Inventory</label>
                                    <p>
                                        @if ($menuItem->track_inventory)
                                            <span class="badge badge-info">Yes ({{ $menuItem->inventory_count ?? 0 }} in
                                                stock)</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dietary & Tags Card -->
                <div class="box">
                    <div class="box-header bg-success">
                        <h4 class="box-title text-white">
                            <i class="fa fa-tags me-2"></i>Dietary Information & Tags
                        </h4>
                    </div>
                    <div class="box-body">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @if ($menuItem->is_vegetarian)
                                <span class="badge badge-success">
                                    <i class="fa fa-leaf me-1"></i>Vegetarian
                                </span>
                            @endif
                            @if ($menuItem->is_vegan)
                                <span class="badge badge-success">
                                    <i class="fa fa-leaf me-1"></i>Vegan
                                </span>
                            @endif
                            @if ($menuItem->is_gluten_free)
                                <span class="badge badge-warning">
                                    <i class="fa fa-heart me-1"></i>Gluten Free
                                </span>
                            @endif
                            @if ($menuItem->is_popular)
                                <span class="badge badge-danger">
                                    <i class="fa fa-star me-1"></i>Popular
                                </span>
                            @endif
                            @if (!$menuItem->is_vegetarian && !$menuItem->is_vegan && !$menuItem->is_gluten_free && !$menuItem->is_popular)
                                <span class="text-muted">No dietary tags assigned</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics Card -->
                <div class="box">
                    <div class="box-header bg-warning">
                        <h4 class="box-title text-white">
                            <i class="fa fa-chart-line me-2"></i>Performance Metrics
                        </h4>
                    </div>
                    <div class="box-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="metric-card">
                                    <div class="metric-value text-primary fs-24 fw-700">{{ $menuItem->total_sales ?? 0 }}
                                    </div>
                                    <div class="metric-label text-muted fs-12">Total Sales</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="metric-card">
                                    <div class="metric-value text-info fs-24 fw-700">{{ $menuItem->total_reviews ?? 0 }}
                                    </div>
                                    <div class="metric-label text-muted fs-12">Total Reviews</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="metric-card">
                                    <div class="metric-value text-success fs-24 fw-700">
                                        {{ number_format($menuItem->average_rating ?? 0, 1) }}/5</div>
                                    <div class="metric-label text-muted fs-12">Average Rating</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO Information Card -->
                @if ($menuItem->meta_title || $menuItem->meta_description)
                    <div class="box">
                        <div class="box-header bg-dark">
                            <h4 class="box-title text-white">
                                <i class="fa fa-search me-2"></i>SEO Information
                            </h4>
                        </div>
                        <div class="box-body">
                            @if ($menuItem->meta_title)
                                <div class="info-item mb-3">
                                    <label class="fw-600 text-muted mb-1">Meta Title</label>
                                    <p class="fs-14 text-dark">{{ $menuItem->meta_title }}</p>
                                </div>
                            @endif
                            @if ($menuItem->meta_description)
                                <div class="info-item">
                                    <label class="fw-600 text-muted mb-1">Meta Description</label>
                                    <p class="fs-14 text-dark">{{ $menuItem->meta_description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column - Image & Actions -->
            <div class="col-lg-4 col-12">
                <!-- Image Card -->
                <div class="box">
                    <div class="box-header bg-primary">
                        <h4 class="box-title text-white">
                            <i class="fa fa-image me-2"></i>Item Image
                        </h4>
                    </div>
                    <div class="box-body text-center p-4">
                        @if ($menuItem->image_url)
                            <img src="{{ $menuItem->image_url }}" class="img-fluid rounded shadow"
                                alt="{{ $menuItem->item_name }}" style="max-height: 280px; object-fit: cover;" />
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                style="height: 200px;">
                                <div class="text-center">
                                    <i class="fa fa-image fs-48 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No image uploaded</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="box">
                    <div class="box-header bg-info">
                        <h4 class="box-title text-white">
                            <i class="fa fa-bolt me-2"></i>Quick Actions
                        </h4>
                    </div>
                    <div class="box-body">
                        <div class="d-grid gap-3">
                            <a href="{{ route('restaurant.menu.edit', $menuItem->id) }}" class="btn btn-primary btn-lg">
                                <i class="fa fa-edit me-2"></i> Edit Menu Item
                            </a>

                            <form action="{{ route('restaurant.menu.toggle', $menuItem->id) }}" method="POST"
                                class="d-grid">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="btn {{ $menuItem->is_available ? 'btn-warning' : 'btn-success' }} btn-lg">
                                    <i class="fa fa-{{ $menuItem->is_available ? 'eye-slash' : 'eye' }} me-2"></i>
                                    {{ $menuItem->is_available ? 'Mark Unavailable' : 'Mark Available' }}
                                </button>
                            </form>

                            <a href="{{ route('restaurant.menu.duplicate', $menuItem->id) }}"
                                class="btn btn-info btn-lg">
                                <i class="fa fa-copy me-2"></i> Duplicate Item
                            </a>

                            <form action="{{ route('restaurant.menu.destroy', $menuItem->id) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this menu item? This action cannot be undone.')"
                                class="d-grid">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-lg">
                                    <i class="fa fa-trash me-2"></i> Delete Menu Item
                                </button>
                            </form>

                            <a href="{{ route('restaurant.menu.list') }}" class="btn btn-secondary btn-lg">
                                <i class="fa fa-arrow-left me-2"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Status Overview Card -->
                <div class="box">
                    <div class="box-header bg-success">
                        <h4 class="box-title text-white">
                            <i class="fa fa-info-circle me-2"></i>Status Overview
                        </h4>
                    </div>
                    <div class="box-body">
                        <div class="status-list">
                            <div class="status-item d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-600">Availability:</span>
                                @if ($menuItem->is_available)
                                    <span class="badge badge-success">Available</span>
                                @else
                                    <span class="badge badge-danger">Unavailable</span>
                                @endif
                            </div>
                            <div class="status-item d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-600">Inventory Tracking:</span>
                                @if ($menuItem->track_inventory)
                                    <span class="badge badge-info">Enabled</span>
                                @else
                                    <span class="badge badge-secondary">Disabled</span>
                                @endif
                            </div>
                            <div class="status-item d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-600">Variations:</span>
                                @if ($menuItem->has_variations)
                                    <span class="badge badge-success">Yes</span>
                                @else
                                    <span class="badge badge-secondary">No</span>
                                @endif
                            </div>
                            <div class="status-item d-flex justify-content-between align-items-center">
                                <span class="fw-600">Created:</span>
                                <span class="text-muted fs-12">{{ $menuItem->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection

@push('styles')
    <style>
        .info-item {
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 15px;
        }

        .info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .metric-card {
            padding: 20px 10px;
            border-radius: 8px;
            background: #f8f9fa;
        }

        .status-list .status-item {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .status-list .status-item:last-child {
            border-bottom: none;
        }

        .box-header {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .badge-success-light {
            background-color: rgba(40, 199, 111, 0.1);
            color: #28c76f;
            border: 1px solid rgba(40, 199, 111, 0.2);
        }

        .badge-danger-light {
            background-color: rgba(234, 84, 85, 0.1);
            color: #ea5455;
            border: 1px solid rgba(234, 84, 85, 0.2);
        }

        .badge-light-primary {
            background-color: rgba(115, 103, 240, 0.1);
            color: #7367f0;
            border: 1px solid rgba(115, 103, 240, 0.2);
        }

        .badge-light-warning {
            background-color: rgba(255, 159, 67, 0.1);
            color: #ff9f43;
            border: 1px solid rgba(255, 159, 67, 0.2);
        }

        .btn-lg {
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
@endpush
