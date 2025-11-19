@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Customer Management</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i
                                        class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Customers</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="text-end">
                <span class="badge badge-info">Total Customers: {{ $customers->count() }}</span>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Customer List</h4>
                        <div class="box-tools pull-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-xs btn-outline dropdown-toggle"
                                    data-bs-toggle="dropdown">Filter by Status</button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item filter-status" href="#" data-status="all">All
                                        Customers</a>
                                    <a class="dropdown-item filter-status" href="#" data-status="active">Active</a>
                                    <a class="dropdown-item filter-status" href="#"
                                        data-status="suspended">Suspended</a>
                                    <a class="dropdown-item filter-status" href="#" data-status="pending">Pending</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive rounded card-table">
                            <table class="table border-no" id="customersTable">
                                <thead>
                                    <tr>
                                        <th>Customer ID</th>
                                        <th>Join Date</th>
                                        <th>Customer Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Location</th>
                                        <th>Total Orders</th>
                                        <th>Total Spent</th>
                                        <th>Last Order</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $customer)
                                        <tr class="hover-primary">
                                            <td class="text-center">
                                                <div class="d-flex flex-column align-items-center justify-content-center">
                                                    <img src="{{ $customer['profile_image_url'] }}" alt="Avatar" class="avatar avatar-sm rounded-circle mb-1">
                                                    <strong>{{ $customer['customer_id'] }}</strong>
                                                </div>
                                            </td>
                                            </td>
                                            <td>{{ $customer['join_date'] }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    {{-- <div class="w-10 h-10">
                                                        <img src="{{ $customer['profile_image_url'] }}" alt="Avatar"
                                                            class="avatar avatar-sm rounded-circle">
                                                    </div> --}}
                                                    {{-- <div> --}}
                                                        <h6 class="mb-0">{{ $customer['name'] }}</h6>
                                                        {{-- <small class="text-muted">{{ $customer['loyalty_points'] }} loyalty
                                                            points</small>
                                                    </div> --}}
                                                {{-- </div> --}}
                                            </td>
                                            <td>{{ $customer['email'] }}</td>
                                            <td>{{ $customer['phone'] ?: 'N/A' }}</td>
                                            <td>
                                                <select
                                                    class="form-select w-90 p-0.5 font-light text-center form-select-sm status-select status-{{ $customer['status'] }}"
                                                    data-customer-id="{{ $customer['id'] }}">
                                                    <option value="active"
                                                        {{ $customer['status'] == 'active' ? 'selected' : '' }}>
                                                        Active
                                                    </option>
                                                    <option value="suspended"
                                                        {{ $customer['status'] == 'suspended' ? 'selected' : '' }}>
                                                        Suspend
                                                    </option>
                                                    <option value="pending"
                                                        {{ $customer['status'] == 'pending' ? 'selected' : '' }}>
                                                        Pending
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <span class="text-muted" title="{{ $customer['location'] }}">
                                                    {{ Str::limit($customer['location'], 30) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $customer['total_orders'] }}</span>
                                            </td>
                                            <td>
                                                <strong class="text-success">{{ $customer['total_spent'] }}</strong>
                                            </td>
                                            <td>{{ $customer['last_order_date'] }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <a class="hover-primary dropdown-toggle no-caret"
                                                        data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.customers.show', $customer['id']) }}">
                                                            <i class="fa fa-eye me-2"></i>View Details
                                                        </a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.customers.profile', $customer['id']) }}">
                                                            <i class="fa fa-user me-2"></i>Manage Profile
                                                        </a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.customers.edit', $customer['id']) }}">
                                                            <i class="fa fa-edit me-2"></i>Edit
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger delete-customer" href="#"
                                                            data-customer-id="{{ $customer['id'] }}">
                                                            <i class="fa fa-trash me-2"></i>Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="fa fa-users fa-3x mb-3"></i>
                                                    <h5>No customers found</h5>
                                                    <p>There are no customers in the system yet.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection

@section('scripts')
    <!-- Data Table -->
    <script src="{{ asset('assets/vendor_components/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/pages/data-table.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#customersTable').DataTable({
                responsive: true,
                ordering: true,
                pageLength: 25,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                columnDefs: [{
                        orderable: false,
                        targets: [10]
                    } // Actions column
                ]
            });

            // Status filter functionality
            $('.filter-status').on('click', function(e) {
                e.preventDefault();
                var status = $(this).data('status');

                if (status === 'all') {
                    table.columns(5).search('').draw();
                } else {
                    table.columns(5).search(status).draw();
                }
            });

            // Status update functionality
            $('.status-select').on('change', function() {
                var customerId = $(this).data('customer-id');
                var newStatus = $(this).val();
                var selectElement = $(this);

                Swal.fire({
                    title: 'Update Customer Status',
                    text: `Are you sure you want to change the status to "${newStatus}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, update it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/customers/${customerId}/status`,
                            method: 'PATCH',
                            data: {
                                status: newStatus,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Updated!',
                                        response.message,
                                        'success'
                                    );
                                    // Update the status badge color
                                    updateStatusBadge(selectElement, newStatus);
                                }
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'There was an error updating the status.',
                                    'error'
                                );
                                // Revert the select to previous value
                                selectElement.val(selectElement.data('original-value'));
                            }
                        });
                    } else {
                        // Revert the select to previous value
                        selectElement.val(selectElement.data('original-value'));
                    }
                });
            });

            // Store original values for status selects
            $('.status-select').each(function() {
                $(this).data('original-value', $(this).val());
            });

            // Update status select on change
            $('.status-select').on('focus', function() {
                $(this).data('original-value', $(this).val());
            });

            // Delete customer functionality
            $(document).on('click', '.delete-customer', function(e) {
                e.preventDefault();
                var customerId = $(this).data('customer-id');
                var row = $(this).closest('tr');

                Swal.fire({
                    title: 'Delete Customer',
                    text: "Are you sure you want to delete this customer? This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/admin/customers/${customerId}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Deleted!',
                                        response.message,
                                        'success'
                                    );
                                    table.row(row).remove().draw();
                                }
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'There was an error deleting the customer.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            function updateStatusBadge(selectElement, status) {
                // Remove all status classes
                selectElement.removeClass('status-active status-suspended status-pending');

                // Add the new status class
                selectElement.addClass('status-' + status);

                // Update the data attribute
                selectElement.data('original-value', status);
            }
        });
    </script>

    <style>
        .status-select {
            border: 1px solid #dee2e6;
            background: transparent;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 4px;
        }

        .status-select.status-active {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .status-select.status-suspended {
            background-color: #fff3cd;
            color: #856404;
            border-color: #ffeaa7;
        }

        .status-select.status-pending {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .status-select option {
            background-color: white;
            color: #333;
            padding: 5px;
        }

        .avatar {
            width: 40px;
            height: 40px;
        }

        .table td {
            vertical-align: middle;
        }
    </style>
@endsection
