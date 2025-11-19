@extends('layouts.admin')

@section('title', 'Tenant Details')

@section('styles')
    <style>
        .timeline {
            padding-left: 2rem;
            position: relative;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: -2rem;
            top: 0.5rem;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #4e73df;
        }

        .timeline-item:after {
            content: '';
            position: absolute;
            left: -1.8rem;
            top: 1rem;
            width: 2px;
            height: calc(100% - 0.5rem);
            background: #e5e7eb;
        }

        .timeline-item:last-child:after {
            display: none;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="content-header">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="page-title mb-2">Tenant Details</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i
                                    class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.tenants.index') }}">Tenants</a></li>
                        <li class="breadcrumb-item active">{{ $tenant->tenant_name }}</li>
                    </ol>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-warning">
                        <i class="fa fa-edit"></i> Edit Tenant
                    </a>
                    <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>

        <section class="content">
            <!-- Tenant Info -->
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-lg bg-primary text-white me-3">
                                    <i class="fa fa-building"></i>
                                </div>
                                <div>
                                    <h3 class="mb-1">{{ $tenant->tenant_name }}</h3>
                                    <p class="text-muted mb-0">Contact: {{ $tenant->contact_person }}</p>
                                </div>
                            </div>
                            <div class="d-flex gap-4 text-muted">
                                <span><i class="fa fa-envelope me-1"></i> {{ $tenant->email }}</span>
                                <span><i class="fa fa-phone me-1"></i> {{ $tenant->phone }}</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            @switch($tenant->status)
                                @case('approved')
                                    <span class="badge badge-success badge-lg">Approved</span>
                                @break

                                @case('pending')
                                    <span class="badge badge-warning badge-lg">Pending</span>
                                @break

                                @case('suspended')
                                    <span class="badge badge-danger badge-lg">Suspended</span>
                                @break

                                @case('rejected')
                                    <span class="badge badge-secondary badge-lg">Rejected</span>
                                @break

                                @default
                                    <span class="badge badge-secondary badge-lg">{{ ucfirst($tenant->status) }}</span>
                            @endswitch
                            <div class="mt-2 text-muted">
                                <small>Created: {{ $tenant->created_at->format('M d, Y') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="box">
                        <div class="box-body text-center">
                            <div class="avatar avatar-lg bg-primary text-white mx-auto mb-3">
                                <i class="fa fa-cutlery" aria-hidden="true"></i>
                            </div>
                            <h4 class="mb-2">{{ $stats['total_restaurants'] }}</h4>
                            <p class="text-muted mb-1">Total Restaurants</p>
                            <small class="text-muted">Limit: {{ $tenant->total_restaurants }}</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="box">
                        <div class="box-body text-center">
                            <div class="avatar avatar-lg bg-success text-white mx-auto mb-3">
                                <i class="fa fa-check-circle"></i>
                            </div>
                            <h4 class="mb-2">{{ $stats['active_restaurants'] }}</h4>
                            <p class="text-muted mb-1">Active Restaurants</p>
                            <small class="text-success">Approved & Running</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="box">
                        <div class="box-body text-center">
                            <div class="avatar avatar-lg bg-warning text-white mx-auto mb-3">
                                <i class="fa fa-clock-o"></i>
                            </div>
                            <h4 class="mb-2">{{ $stats['pending_restaurants'] }}</h4>
                            <p class="text-muted mb-1">Pending Approval</p>
                            <small class="text-warning">Awaiting Review</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="box">
                        <div class="box-body text-center">
                            <div class="avatar avatar-lg bg-info text-white mx-auto mb-3">
                                <i class="fa fa-dollar"></i>
                            </div>
                            <h4 class="mb-2">${{ number_format($stats['revenue_this_month'], 2) }}</h4>
                            <p class="text-muted mb-1">Monthly Revenue</p>
                            <small class="text-info">Current Month</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Subscription Details -->
                <div class="col-md-6">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Subscription Details</h4>
                        </div>
                        <div class="box-body p-0">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-bold">Plan:</td>
                                        <td><span class="badge badge-info">{{ $tenant->subscription_plan }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Monthly Base Fee:</td>
                                        <td>${{ number_format($tenant->monthly_base_fee, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Per Restaurant Fee:</td>
                                        <td>${{ number_format($tenant->per_restaurant_fee, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Banner Limit:</td>
                                        <td>{{ $tenant->banner_limit }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Start Date:</td>
                                        <td>{{ $tenant->subscription_start_date ? $tenant->subscription_start_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Next Billing:</td>
                                        <td>
                                            {{ $tenant->next_billing_date ? $tenant->next_billing_date->format('M d, Y') : 'N/A' }}
                                            @if ($tenant->next_billing_date && $tenant->next_billing_date->isPast())
                                                <span class="badge badge-danger ms-2">Overdue</span>
                                            @elseif($tenant->next_billing_date && $tenant->next_billing_date->diffInDays() <= 7)
                                                <span class="badge badge-warning ms-2">Due Soon</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Approval Information -->
                <div class="col-md-6">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Approval Information</h4>
                        </div>
                        <div class="box-body">
                            @if ($tenant->approved_at)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-lg bg-success text-white me-3">
                                        <i class="fa fa-check"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Approved</h6>
                                        <small
                                            class="text-muted">{{ $tenant->approved_at->format('M d, Y \a\t h:i A') }}</small>
                                    </div>
                                </div>
                                @if ($tenant->approved_by)
                                    <p class="mb-0">
                                        <span class="fw-bold">Approved by:</span>
                                        {{ \App\Models\User::find($tenant->approved_by)?->first_name ?? 'Unknown' }}
                                    </p>
                                @endif
                            @else
                                <div class="text-center py-4">
                                    <div class="avatar avatar-xl bg-warning text-white mx-auto mb-3">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <p class="text-muted mb-3">Pending approval</p>
                                    @if (auth()->user()->role === 'super_admin')
                                        <button type="button" class="btn btn-success btn-sm" onclick="approveTenant()">
                                            <i class="fa fa-check"></i> Approve Now
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Restaurants List -->
            @if ($tenant->restaurants->count() > 0)
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Associated Restaurants ({{ $tenant->restaurants->count() }})</h4>
                    </div>
                    <div class="box-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Restaurant</th>
                                        <th>Contact</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tenant->restaurants as $restaurant)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm bg-primary text-white me-2">
                                                        {{-- <i class="fa fa-restaurant"></i> --}}
                                                        <i class="fa fa-cutlery" aria-hidden="true"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $restaurant->restaurant_name }}</div>
                                                        <small class="text-muted">{{ $restaurant->cuisine_type }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>{{ $restaurant->email }}</div>
                                                <small class="text-muted">{{ $restaurant->phone }}</small>
                                            </td>
                                            <td>
                                                <small>{{ $restaurant->city }}, {{ $restaurant->state }}</small>
                                            </td>
                                            <td>
                                                @switch($restaurant->status)
                                                    @case('approved')
                                                        <span class="badge badge-success">Approved</span>
                                                    @break

                                                    @case('pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @break

                                                    @default
                                                        <span
                                                            class="badge badge-secondary">{{ ucfirst($restaurant->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <small>{{ $restaurant->created_at->format('M d, Y') }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ route('restaurant-admin.show', $restaurant) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fa fa-eye"></i> View
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

            <!-- Activity Timeline -->
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Activity Timeline</h4>
                </div>
                <div class="box-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <h6 class="mb-2">Tenant Created</h6>
                            <p class="text-muted mb-1">{{ $tenant->created_at->format('M d, Y \a\t h:i A') }}</p>
                            <small class="text-muted">Initial tenant registration</small>
                        </div>

                        @if ($tenant->approved_at)
                            <div class="timeline-item">
                                <h6 class="mb-2">Tenant Approved</h6>
                                <p class="text-muted mb-1">{{ $tenant->approved_at->format('M d, Y \a\t h:i A') }}</p>
                                <small class="text-muted">Tenant status changed to approved</small>
                            </div>
                        @endif

                        @foreach ($tenant->restaurants->take(3) as $restaurant)
                            <div class="timeline-item">
                                <h6 class="mb-2">Restaurant Added</h6>
                                <p class="text-muted mb-1">{{ $restaurant->created_at->format('M d, Y \a\t h:i A') }}</p>
                                <small class="text-muted">{{ $restaurant->restaurant_name }} was registered</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        function approveTenant() {
            if (confirm('Are you sure you want to approve this tenant?')) {
                fetch(`/admin/tenants/{{ $tenant->id }}/status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            status: 'approved'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while approving the tenant.');
                    });
            }
        }
    </script>
@endsection
