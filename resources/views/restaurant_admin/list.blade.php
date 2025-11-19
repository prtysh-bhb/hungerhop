@extends('layouts.admin')

@section('title', 'Restaurant Management')


@section('content')
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                @if (auth()->user()->role === 'location_admin')
                    <h3 class="page-title">My Restaurant</h3>
                @else
                    <h3 class="page-title">Restaurant Management</h3>
                @endif
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            @if (auth()->user()->role === 'location_admin')
                                <li class="breadcrumb-item"><a href="{{ route('location-admin.dashboard') }}"><i
                                            class="mdi mdi-home-outline"></i></a></li>
                                <li class="breadcrumb-item active" aria-current="page">My Restaurant</li>
                            @else
                                <li class="breadcrumb-item"><a href="{{ route('restaurant-admin.index') }}"><i
                                            class="mdi mdi-home-outline"></i></a></li>
                                <li class="breadcrumb-item active" aria-current="page">Restaurant List</li>
                            @endif
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="d-flex gap-2">
                @if (auth()->user()->role !== 'location_admin')
                    <a href="{{ route('restaurant-admin.registration.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus"></i> Add New Restaurant
                    </a>
                @endif
            </div>
        </div>
    </div>

    <section class="content">
        <!-- Search and Filters -->
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Search & Filters</h4>
                    </div>
                    <div class="box-body">
                        <form method="GET" action="{{ route('restaurant-admin.list') }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Search</label>
                                        <input type="text" class="form-control" name="search"
                                            value="{{ request('search') }}" placeholder="Restaurant name, email, phone...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control" name="status">
                                            <option value="">All Status</option>
                                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                                Pending</option>
                                            <option value="approved"
                                                {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="rejected"
                                                {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            <option value="suspended"
                                                {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>City</label>
                                        <input type="text" class="form-control" name="city"
                                            value="{{ request('city') }}" placeholder="Enter city name...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary p-1 m-0.5">
                                                <i class="fa fa-search"></i> Search
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        @if (auth()->user()->role === 'location_admin')
                            <h4 class="box-title">My Restaurant ({{ $restaurants->total() }} found)</h4>
                        @else
                            <h4 class="box-title">Restaurants ({{ $restaurants->total() }} found)</h4>
                        @endif
                    </div>
                    <div class="box-body">
                        @if ($restaurants->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr class="text-uppercase bg-lightest">
                                            <th>Restaurant</th>
                                            <th>Contact</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                            <th>Orders</th>
                                            <th>Registered</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($restaurants as $restaurant)
                                            <tr class="hover-primary {{ $restaurant->is_paused ? 'table-danger' : '' }}">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if ($restaurant->image_url)
                                                            <img src="{{ $restaurant->image_url }}"
                                                                class="avatar avatar-md rounded me-3"
                                                                alt="{{ $restaurant->restaurant_name }}">
                                                        @else
                                                            <div class="avatar avatar-md rounded me-3 bg-primary-light">
                                                                <i class="fa fa-utensils"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-0">{{ $restaurant->restaurant_name }}</h6>
                                                            <p class="text-fade mb-0">
                                                                {{ $restaurant->cuisine_type ?? 'N/A' }}</p>
                                                            @if ($restaurant->is_paused)
                                                                <small class="badge badge-danger">PAUSED</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>{{ $restaurant->email }}</div>
                                                    <small class="text-fade">{{ $restaurant->phone }}</small>
                                                </td>
                                                <td>
                                                    <div>{{ $restaurant->address }}</div>
                                                    <small
                                                        class="text-fade">{{ $restaurant->cityRelation ? $restaurant->cityRelation->name : $restaurant->city }},
                                                        {{ $restaurant->stateRelation ? $restaurant->stateRelation->name : $restaurant->state }}</small>
                                                </td>
                                                <td>
                                                    @switch($restaurant->status)
                                                        @case('pending')
                                                            <span class="badge badge-warning">Pending</span>
                                                        @break

                                                        @case('approved')
                                                            <span class="badge badge-success">Approved</span>
                                                        @break

                                                        @case('suspended')
                                                            <span class="badge badge-danger">Suspended</span>
                                                        @break

                                                        @case('rejected')
                                                            <span class="badge badge-secondary">Rejected</span>
                                                        @break

                                                        @default
                                                            <span class="badge badge-light">Unknown</span>
                                                    @endswitch
                                                </td>
                                                <td>
                                                    @if (in_array(auth()->user()->role, ['tenant_admin', 'location_admin', 'super_admin']))
                                                        @php
                                                            $canManage = false;
                                                            if (auth()->user()->role === 'super_admin') {
                                                                $canManage = true;
                                                            } elseif (auth()->user()->role === 'tenant_admin') {
                                                                $canManage =
                                                                    $restaurant->tenant_id ===
                                                                    auth()->user()->tenant_id;
                                                            } elseif (auth()->user()->role === 'location_admin') {
                                                                $canManage =
                                                                    $restaurant->location_admin_id ===
                                                                    auth()->user()->id;
                                                            }
                                                        @endphp

                                                        @if ($canManage && $restaurant->status === 'approved')
                                                            <div class="form-check form-switch">
                                                                <input type="checkbox" class="form-check-input pause-toggle"
                                                                    id="pauseToggle{{ $restaurant->id }}"
                                                                    data-restaurant-id="{{ $restaurant->id }}"
                                                                    {{ $restaurant->is_paused ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="pauseToggle{{ $restaurant->id }}">
                                                                    <span class="pause-text">
                                                                        {{ $restaurant->is_paused ? 'PAUSED' : 'ACTIVE' }}
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">
                                                                {{ $restaurant->is_paused ? 'PAUSED' : 'ACTIVE' }}
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>{{ $restaurant->created_at->format('M d, Y') }}</div>
                                                    <small
                                                        class="text-fade">{{ $restaurant->created_at->diffForHumans() }}</small>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="{{ route('restaurant-admin.show', $restaurant->id) }}"
                                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('restaurant-admin.edit', $restaurant->id) }}"
                                                            class="btn btn-sm btn-outline-info" title="Edit">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        @if ($restaurant->status === 'pending')
                                                            <a href="{{ route('restaurant-admin.management.show', $restaurant->id) }}"
                                                                class="btn btn-sm btn-outline-warning" title="Review">
                                                                <i class="fa fa-check-circle"></i>
                                                            </a>
                                                        @endif
                                                        <div class="dropdown d-inline">
                                                            <button
                                                                class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fa fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li><a class="dropdown-item" href="#">View
                                                                        Order*</a></li>
                                                                <li><a class="dropdown-item" href="#">Performance
                                                                        Report*</a></li>
                                                                <li>
                                                                    <hr class="dropdown-divider">
                                                                </li>
                                                                <li><a class="dropdown-item text-warning"
                                                                        href="#">Suspend</a></li>
                                                                <li><a class="dropdown-item text-danger" href="#"
                                                                        onclick="confirmDelete({{ $restaurant->id }})">Delete</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <nav aria-label="Page navigation">
                                {{ $restaurants->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </nav>

<style>
.pagination .page-item.disabled .page-link,
.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
    pointer-events: none;
}

.pagination .page-item.disabled .page-link {
    background-color: #e9ecef;
    color: #6c757d;
    border-color: #dee2e6;
}

.pagination .page-link {
    border-radius: 6px !important;
    padding: 6px 12px;
    font-size: 0.875rem;
}

.pagination {
    justify-content: center;
}
</style>

                        @else
                            <div class="text-center py-5">
                                <img src="{{ asset('images/no-data.svg') }}" class="w-120" alt="No Data">
                                <h5 class="mt-3">No Restaurants Found</h5>
                                <p class="text-fade">
                                    @if (request()->hasAny(['search', 'status', 'city']))
                                        Try adjusting your search criteria or
                                        <a href="{{ route('restaurant-admin.list') }}" class="text-primary">clear
                                            filters</a>
                                    @else
                                        Start by adding your first restaurant
                                    @endif
                                </p>
                                <a href="{{ route('restaurant-admin.registration.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus me-2"></i>Add Restaurant
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this restaurant? This action cannot be undone.')) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/restaurant-admin/delete/${id}`;

                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfInput);

                // Add method spoofing
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        $(document).ready(function() {
            // Auto-submit form on filter change
            $('select[name="status"]').on('change', function() {
                $(this).closest('form').submit();
            });

            // Handle pause toggle
            $('.pause-toggle').on('change', function() {
                const toggle = $(this);
                const restaurantId = toggle.data('restaurant-id');
                const row = toggle.closest('tr');
                const pauseText = toggle.siblings('label').find('.pause-text');

                // Disable toggle during request
                toggle.prop('disabled', true);

                $.ajax({
                    url: `/restaurant-admin/${restaurantId}/toggle-pause`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update UI based on response
                            if (response.is_paused) {
                                row.addClass('table-danger');
                                pauseText.text('PAUSED');

                                // Add paused badge if not exists
                                const restaurantName = row.find('h6').parent();
                                if (!restaurantName.find('.badge-danger').length) {
                                    restaurantName.append(
                                        '<small class="badge badge-danger ml-2">PAUSED</small>'
                                    );
                                }
                            } else {
                                row.removeClass('table-danger');
                                pauseText.text('ACTIVE');

                                // Remove paused badge
                                row.find('.badge-danger:contains("PAUSED")').remove();
                            }

                            // Show success message
                            showNotification(response.message, 'success');
                        } else {
                            // Revert toggle state on error
                            toggle.prop('checked', !toggle.prop('checked'));
                            showNotification(response.message ||
                                'Error updating restaurant status', 'error');
                        }
                    },
                    error: function(xhr) {
                        // Revert toggle state on error
                        toggle.prop('checked', !toggle.prop('checked'));

                        let message = 'Error updating restaurant status';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showNotification(message, 'error');
                    },
                    complete: function() {
                        // Re-enable toggle
                        toggle.prop('disabled', false);
                    }
                });
            });

            // Simple notification function
            function showNotification(message, type) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const alertHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

                // Remove existing alerts
                $('.alert').remove();

                // Add new alert at the top of content
                $('.content').prepend(alertHTML);

                // Auto remove after 5 seconds
                setTimeout(function() {
                    $('.alert').fadeOut();
                }, 5000);
            }
        });
    </script>
@endpush
