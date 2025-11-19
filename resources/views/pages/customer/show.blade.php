@extends('layouts.admin')

@section('title', 'Customer Details')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Customer Details</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.customers') }}">Customers</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Details</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="text-end">
                <a href="{{ route('admin.customers') }}" class="btn btn-outline-secondary me-2">
                    <i class="fa fa-arrow-left me-2"></i>Back to Customers
                </a>
                <a href="{{ route('admin.customers.profile', $customer->id) }}" class="btn btn-primary">
                    <i class="fa fa-edit me-2"></i>Manage Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Customer Overview -->
            <div class="col-xl-4 col-lg-5">
                <div class="box">
                    <div class="box-body text-center">
                        <div class="profile-img">
                            <img src="{{ $customer->customerProfile && $customer->customerProfile->profile_image_url 
                                ? asset('storage/' . $customer->customerProfile->profile_image_url) 
                                : asset('images/avatar/default-avatar.png') }}" 
                                alt="Customer Avatar" class="rounded-circle bg-primary-light" width="120" height="120">
                        </div>
                        <h4 class="mt-20 mb-0">{{ $customer->first_name }} {{ $customer->last_name }}</h4>
                        <p class="text-muted">Customer ID: CUST{{ str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</p>
                        
                        <div class="mt-20">
                            <span class="badge badge-lg 
                                @if($customer->status == 'active') badge-success
                                @elseif($customer->status == 'suspended') badge-warning  
                                @else badge-secondary
                                @endif">
                                {{ ucfirst($customer->status) }}
                            </span>
                        </div>

                        <div class="row mt-30">
                            <div class="col-12">
                                <div class="bg-light p-15 rounded10">
                                    <div class="row">
                                        <div class="col-6 text-center">
                                            <h5 class="mb-0 text-primary">{{ $customer->orders()->count() }}</h5>
                                            <p class="text-muted mb-0">Total Orders</p>
                                        </div>
                                        <div class="col-6 text-center">
                                            <h5 class="mb-0 text-success">${{ number_format($customer->orders()->sum('total_amount'), 2) }}</h5>
                                            <p class="text-muted mb-0">Total Spent</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="box">
                    <div class="box-header">
                        <h4 class="box-title">Contact Information</h4>
                    </div>
                    <div class="box-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Email:</span>
                                <strong>{{ $customer->email }}</strong>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Phone:</span>
                                <strong>{{ $customer->phone ?: 'Not provided' }}</strong>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Joined:</span>
                                <strong>{{ $customer->created_at->format('M d, Y') }}</strong>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Last Login:</span>
                                <strong>{{ $customer->last_login_at ? $customer->last_login_at->format('M d, Y') : 'Never' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                @if($customer->customerProfile)
                <div class="box">
                    <div class="box-header">
                        <h4 class="box-title">Personal Information</h4>
                    </div>
                    <div class="box-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Date of Birth:</span>
                                <strong>{{ $customer->customerProfile->date_of_birth ? $customer->customerProfile->date_of_birth->format('M d, Y') : 'Not provided' }}</strong>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Gender:</span>
                                <strong>{{ $customer->customerProfile->gender ? ucfirst($customer->customerProfile->gender) : 'Not provided' }}</strong>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Loyalty Points:</span>
                                <strong class="text-warning">{{ $customer->customerProfile->loyalty_points ?? 0 }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Customer Activity -->
            <div class="col-xl-8 col-lg-7">
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3 col-6">
                        <div class="box box-body bg-primary">
                            <h6 class="text-white">Total Orders</h6>
                            <div class="text-white">
                                <h3 class="mb-0 fw-700">{{ $customer->orders()->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="box box-body bg-success">
                            <h6 class="text-white">Completed Orders</h6>
                            <div class="text-white">
                                <h3 class="mb-0 fw-700">{{ $customer->orders()->where('status', 'completed')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="box box-body bg-warning">
                            <h6 class="text-white">Cancelled Orders</h6>
                            <div class="text-white">
                                <h3 class="mb-0 fw-700">{{ $customer->orders()->where('status', 'cancelled')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="box box-body bg-info">
                            <h6 class="text-white">Avg Order Value</h6>
                            <div class="text-white">
                                <h3 class="mb-0 fw-700">${{ $customer->orders()->count() > 0 ? number_format($customer->orders()->avg('total_amount'), 0) : '0' }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="box">
                    <div class="box-header">
                        <h4 class="box-title">Recent Orders</h4>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Restaurant</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>Payment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customer->orders()->latest()->take(10)->get() as $order)
                                    <tr>
                                        <td><strong>{{ $order->order_number }}</strong></td>
                                        <td>{{ $order->restaurant->name ?? 'N/A' }}</td>
                                        <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($order->status == 'completed') bg-success
                                                @elseif($order->status == 'cancelled') bg-danger
                                                @elseif($order->status == 'pending') bg-warning
                                                @else bg-info
                                                @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td><strong>${{ number_format($order->total_amount, 2) }}</strong></td>
                                        <td>
                                            <span class="badge 
                                                @if($order->payment_status == 'paid') bg-success
                                                @elseif($order->payment_status == 'failed') bg-danger
                                                @else bg-warning
                                                @endif">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fa fa-shopping-cart fa-3x text-muted mb-3"></i>
                                            <h5>No orders found</h5>
                                            <p class="text-muted">This customer hasn't placed any orders yet.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Customer Addresses -->
                <div class="box">
                    <div class="box-header">
                        <h4 class="box-title">Customer Addresses</h4>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            @forelse($customer->addresses as $address)
                            <div class="col-md-6 mb-3">
                                <div class="card {{ $address->is_default ? 'border-primary' : '' }}">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            {{ $address->is_default ? 'Default Address' : ucfirst($address->address_type ?? 'Address') }}
                                            @if($address->is_default)
                                                <span class="badge bg-primary ms-2">Default</span>
                                            @endif
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-1"><strong>{{ $address->address_line1 }}</strong></p>
                                        @if($address->address_line2)
                                            <p class="mb-1">{{ $address->address_line2 }}</p>
                                        @endif
                                        @if($address->landmark)
                                            <p class="mb-1 text-info"><i class="fa fa-map-marker me-1"></i>{{ $address->landmark }}</p>
                                        @endif
                                        <p class="mb-0 text-muted">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                        @if($address->address_type)
                                            <span class="badge bg-secondary mt-2">{{ ucfirst($address->address_type) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="text-center py-4">
                                    <i class="fa fa-map-marker fa-3x text-muted mb-3"></i>
                                    <h5>No addresses found</h5>
                                    <p class="text-muted">This customer hasn't added any addresses yet.</p>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
