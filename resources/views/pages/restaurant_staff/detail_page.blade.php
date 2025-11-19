@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="d-flex align-items-center">
        <div class="me-auto">
            <h4 class="page-title">Restaurant Details</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                <li class="breadcrumb-item">Restaurants</li>
                <li class="breadcrumb-item active">Details</li>
            </ol>
        </div>
    </div>
</div>

<section class="content">
    <div class="row">
        <!-- Left Column -->
        <div class="col-xxxl-4 col-12">
            <div class="box">
                <div class="box-body">
                    <div class="d-flex align-items-center">
                        <img class="me-10 rounded avatar avatar-xl b-2 border-primary"
                             src="{{ $restaurant->image_url ?? asset('images/default-restaurant.png') }}"
                             alt="restaurant image">
                        <div>
                            <h4 class="mb-0">{{ $restaurant->restaurant_name }}</h4>
                            <span class="fs-14 text-info">{{ $restaurant->slug }}</span>
                        </div>
                    </div>
                </div>

                <div class="box-body border-bottom">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-phone me-10 fs-20 text-info"></i>
                        <h5 class="mb-0">{{ $restaurant->phone }}</h5>
                    </div>
                </div>

                <div class="box-body border-bottom">
                    <div class="d-flex align-items-start">
                        <i class="fa fa-map-marker me-10 fs-20 text-danger"></i>
                        <h5 class="mb-0">
                            {{ $restaurant->address }},
                            <br>Postal: {{ $restaurant->postal_code }}
                        </h5>
                    </div>
                </div>

                <div class="box-body">
                    <h5 class="mb-10">Contact Email</h5>
                    <p><a href="mailto:{{ $restaurant->email }}">{{ $restaurant->email }}</a></p>
                </div>
            </div>

            <div class="box">
                <div class="box-header no-border">
                    <h4 class="box-title">Quick Info</h4>
                </div>
                <div class="box-body">
                    <ul class="list-unstyled fs-15">
                        <li><strong>Latitude:</strong> {{ $restaurant->latitude }}</li>
                        <li><strong>Longitude:</strong> {{ $restaurant->longitude }}</li>
                        <li><strong>Delivery Radius:</strong> {{ $restaurant->delivery_radius_km }} km</li>
                        <li><strong>Min Order:</strong> ₹{{ $restaurant->minimum_order_amount }}</li>
                        <li><strong>Delivery Fee:</strong> ₹{{ $restaurant->base_delivery_fee }}</li>
                        <li><strong>Commission:</strong> {{ $restaurant->restaurant_commission_percentage }}%</li>
                        <li><strong>Est. Time:</strong> {{ $restaurant->estimated_delivery_time }} min</li>
                        <li><strong>Tax:</strong> {{ $restaurant->tax_percentage }}%</li>
                        <li><strong>Status:</strong>
                            <span class="badge {{ $restaurant->status === 'approved' ? 'badge-success' : 'badge-warning' }}">
                                {{ ucfirst($restaurant->status) }}
                            </span>
                        </li>
                        <li><strong>Open:</strong> {{ $restaurant->is_open ? 'Yes' : 'No' }}</li>
                        <li><strong>Accepts Orders:</strong> {{ $restaurant->accepts_orders ? 'Yes' : 'No' }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-xxxl-8 col-12">
            <div class="box">
                <div class="box-header no-border">
                    <h4 class="box-title">Statistics</h4>
                </div>
                <div class="box-body d-flex justify-content-around text-center">
                    <div>
                        <h1 class="mb-0">{{ $restaurant->total_orders }}</h1>
                        <p>Total Orders</p>
                    </div>
                    <div>
                        <h1 class="mb-0">{{ $restaurant->total_reviews }}</h1>
                        <p>Total Reviews</p>
                    </div>
                    <div>
                        <h1 class="mb-0">{{ $restaurant->average_rating }}</h1>
                        <p>Avg. Rating</p>
                    </div>
                </div>
            </div>

            @if($restaurant->cover_image_url)
            <div class="box">
                <div class="box-header no-border">
                    <h4 class="box-title">Cover Image</h4>
                </div>
                <div class="box-body text-center">
                    <img src="{{ $restaurant->cover_image_url }}" class="img-fluid rounded shadow"
                         style="max-height: 300px;" alt="Cover Image">
                </div>
            </div>
            @endif

            <div class="box">
                <div class="box-header no-border">
                    <h4 class="box-title">Timestamps</h4>
                </div>
                <div class="box-body">
                    <p><strong>Created At:</strong> {{ $restaurant->created_at->format('d M Y, h:i A') }}</p>
                    <p><strong>Last Updated:</strong> {{ $restaurant->updated_at->format('d M Y, h:i A') }}</p>
                    @if($restaurant->approved_at)
                        <p><strong>Approved At:</strong> {{ $restaurant->approved_at->format('d M Y, h:i A') }}</p>
                    @endif
                    @if($restaurant->approved_by)
                        <p><strong>Approved By:</strong> {{ $restaurant->approved_by }}</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>
@endsection