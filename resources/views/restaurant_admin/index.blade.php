@extends('layouts.admin')

@section('title', 'Restaurant Admin Dashboard')

@section('content')
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h3 class="page-title">Restaurant Administration</h3>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Restaurant Admin</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <!-- Stats Row -->
        <div class="row">
            <div class="col-xl-3 col-md-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex align-items-start">
                            <div>
                                <img src="{{ asset('images/svg-icon/color-svg/custom-14.svg') }}" class="w-80 me-20"
                                    alt="" />
                            </div>
                            <div>
                                <h2 class="my-0 fw-700">{{ $stats['total_restaurants'] }}</h2>
                                <p class="text-fade mb-0">Total Restaurants</p>
                                <p class="fs-12 mb-0 text-success">
                                    <a href="{{ route('restaurant-admin.list') }}" class="text-primary">View All</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex align-items-start">
                            <div>
                                <img src="{{ asset('images/svg-icon/color-svg/custom-15.svg') }}" class="w-80 me-20"
                                    alt="" />
                            </div>
                            <div>
                                <h2 class="my-0 fw-700 text-warning">{{ $stats['pending_approvals'] }}</h2>
                                <p class="text-fade mb-0">Pending Approval</p>
                                <p class="fs-12 mb-0">
                                    <a href="{{ route('restaurant-admin.list') }}?status=pending"
                                        class="text-warning">Review Now</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex align-items-start">
                            <div>
                                <img src="{{ asset('images/svg-icon/color-svg/custom-16.svg') }}" class="w-80 me-20"
                                    alt="" />
                            </div>
                            <div>
                                <h2 class="my-0 fw-700 text-success">{{ $stats['approved_restaurants'] }}</h2>
                                <p class="text-fade mb-0">Approved</p>
                                <p class="fs-12 mb-0">
                                    <a href="{{ route('restaurant-admin.list') }}?status=approved" class="text-success">View
                                        Active</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex align-items-start">
                            <div>
                                <img src="{{ asset('images/svg-icon/color-svg/custom-14.svg') }}" class="w-80 me-20"
                                    alt="" />
                            </div>
                            <div>
                                <h2 class="my-0 fw-700 text-danger">{{ $stats['rejected_restaurants'] }}</h2>
                                <p class="text-fade mb-0">Rejected</p>
                                <p class="fs-12 mb-0">
                                    <a href="{{ route('restaurant-admin.list') }}?status=rejected" class="text-danger">View
                                        Rejected</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Row -->
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Quick Actions</h4>
                    </div>
                    <div class="box-body text-center">
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <a href="{{ route('restaurant-admin.registration.create') }}"
                                    class="btn btn-primary btn-block mb-3">
                                    <i class="fa fa-plus me-2"></i>Add New Restaurant
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <a href="{{ route('restaurant-admin.documents.create') }}"
                                    class="btn btn-info btn-block mb-3">
                                    <i class="fa fa-upload me-2"></i>Upload Document
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <a href="{{ route('restaurant-admin.verification.index') }}"
                                    class="btn btn-warning btn-block mb-3">
                                    <i class="fa fa-check-circle me-2"></i>Document Verification
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <a href="{{ route('restaurant-admin.list') }}" class="btn btn-success btn-block mb-3">
                                    <i class="fa fa-cogs me-2"></i>Manage Restaurants
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Restaurants -->
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Recent Restaurant Registrations</h4>
                        <div class="box-tools pull-right">
                            <a href="{{ route('restaurant-admin.list') }}" class="btn btn-sm btn-primary">View All</a>
                        </div>
                    </div>
                    <div class="box-body">
                        @if ($recent_restaurants->count() > 0)
                            <div class="table-responsive">
                                <table class="table no-border">
                                    <thead>
                                        <tr class="text-uppercase bg-lightest">
                                            <th>Restaurant</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                            <th>Registered</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recent_restaurants as $restaurant)
                                            <tr class="hover-primary {{ $restaurant->is_paused ? 'table-danger' : '' }}">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if ($restaurant->image_url)
                                                            <img src="storage/{{ $restaurant->image_url }}"
                                                                class="avatar avatar-md rounded me-3"
                                                                alt="{{ $restaurant->restaurant_name }}">
                                                        @else
                                                            <div class="avatar avatar-md rounded me-3 bg-primary-light">
                                                                <i class="fa fa-restaurant"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-0">{{ $restaurant->restaurant_name }}
                                                                @if ($restaurant->is_paused)
                                                                    <small class="badge badge-danger ml-2">PAUSED</small>
                                                                @endif
                                                            </h6>
                                                            <p class="text-fade mb-0">{{ $restaurant->cuisine_type }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <h6 class="mb-0">
                                                        {{ $restaurant->address ? $restaurant->address : 'N/A' }}
                                                    </h6>
                                                    <span
                                                        class="text-fade">{{ $restaurant->cityRelation ? $restaurant->cityRelation->name : $restaurant->city }},
                                                        {{ $restaurant->stateRelation ? $restaurant->stateRelation->name : $restaurant->state }}</span>
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
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <span
                                                        class="text-fade">{{ $restaurant->created_at->format('M d, Y') }}</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
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

                                                        @if ($restaurant->status === 'approved')
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

                                                            @if ($canManage)
                                                                @if ($restaurant->is_paused)
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-success pause-toggle-btn"
                                                                        data-restaurant-id="{{ $restaurant->id }}"
                                                                        data-action="continue"
                                                                        title="Continue Restaurant">
                                                                        <i class="fa fa-play"></i>
                                                                    </button>
                                                                @else
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger pause-toggle-btn"
                                                                        data-restaurant-id="{{ $restaurant->id }}"
                                                                        data-action="pause" title="Pause Restaurant">
                                                                        <i class="fa fa-pause"></i>
                                                                    </button>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <img src="{{ asset('images/svg-icon/color-svg/custom-14.svg') }}" class="w-120"
                                    alt="No Data">
                                <h5 class="mt-3">No Recent Registrations</h5>
                                <p class="text-fade">Start by adding your first restaurant</p>
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

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <div class="icon-circle-lg bg-success text-white mx-auto mb-3"
                            style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 40px;">
                            <i class="fa fa-check"></i>
                        </div>
                    </div>
                    <h3 class="mb-2 fw-bold" id="successTitle">Restaurant Created Successfully!</h3>
                    <p class="text-muted mb-4" id="successMessage">
                        Your restaurant <span id="restaurantName" class="fw-bold"></span> has been successfully registered
                        and is pending approval.
                    </p>
                    <div class="alert alert-info mb-4" style="text-align: left;">
                        <i class="fa fa-info-circle me-2"></i>
                        <strong>Next Steps:</strong>
                        <ul class="mb-0 mt-2" style="padding-left: 20px;">
                            <li>Your restaurant is now pending approval</li>
                            <li>Admin will review your restaurant details</li>
                            <li>You will receive notification once approved</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="{{ route('restaurant-admin.list') }}" class="btn btn-primary">
                        <i class="fa fa-list me-2"></i>View All Restaurants
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Check if we should show success modal (from query parameter)
            const urlParams = new URLSearchParams(window.location.search);
            const showSuccessModal = urlParams.get('show_success_modal');
            const restaurantName = urlParams.get('restaurant_name');

            if (showSuccessModal === 'true' || showSuccessModal === '1') {
                // Update modal content with restaurant name if provided
                if (restaurantName) {
                    document.getElementById('restaurantName').textContent = restaurantName;
                }

                // Show the modal
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();

                // Clean up URL - remove query parameters to avoid showing modal again on refresh
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            // Add any dashboard-specific JavaScript here

            // Auto-refresh stats every 5 minutes
            setInterval(function() {
                // You can add AJAX calls to refresh stats
            }, 300000);

            // Handle pause/continue toggle buttons
            $('.pause-toggle-btn').on('click', function() {
                const button = $(this);
                const restaurantId = button.data('restaurant-id');
                const action = button.data('action');
                const row = button.closest('tr');
                const restaurantNameContainer = row.find('h6');

                // Disable button during request
                button.prop('disabled', true);

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
                                // Restaurant was paused
                                row.addClass('table-danger');

                                // Add paused badge if not exists
                                if (!restaurantNameContainer.find('.badge-danger').length) {
                                    restaurantNameContainer.append(
                                        '<small class="badge badge-danger ml-2">PAUSED</small>'
                                    );
                                }

                                // Change button to "Continue"
                                button.removeClass('btn-danger').addClass('btn-success');
                                button.attr('data-action', 'continue');
                                button.attr('title', 'Continue Restaurant');
                                button.html('<i class="fa fa-play"></i>');
                            } else {
                                // Restaurant was continued
                                row.removeClass('table-danger');

                                // Remove paused badge
                                restaurantNameContainer.find('.badge-danger:contains("PAUSED")')
                                    .remove();

                                // Change button to "Pause"
                                button.removeClass('btn-success').addClass('btn-danger');
                                button.attr('data-action', 'pause');
                                button.attr('title', 'Pause Restaurant');
                                button.html('<i class="fa fa-pause"></i>');
                            }

                            // Show success message
                            showNotification(response.message, 'success');
                        } else {
                            showNotification(response.message ||
                                'Error updating restaurant status', 'error');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Error updating restaurant status';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showNotification(message, 'error');
                    },
                    complete: function() {
                        // Re-enable button
                        button.prop('disabled', false);
                    }
                });
            });

            // Simple notification function
            function showNotification(message, type) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const notification = `
                    <div class="alert ${alertClass} alert-dismissible fade show" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;

                $('body').append(notification);

                // Auto-remove after 5 seconds
                setTimeout(function() {
                    $('.alert').fadeOut(500, function() {
                        $(this).remove();
                    });
                }, 5000);
            }
        });
    </script>
@endpush
