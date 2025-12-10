@extends('layouts.admin')

@section('title', 'Wallet Transactions')

@section('content')
    <style>
        .pagination {
            display: flex;
            justify-content: flex-end;
            margin-top: 1rem;
        }

        .page-item .page-link {
            border-radius: 6px !important;
            padding: 6px 12px;
            border: 1px solid #dee2e6 !important;
            background-color: #fff !important;
            color: #007bff !important;
        }

        .page-item.active .page-link {
            background-color: #007bff !important;
            border-color: #007bff !important;
            color: #fff !important;
        }

        .page-item.disabled .page-link {
            background-color: #f8f9fa !important;
            border-color: #dee2e6 !important;
            color: #6c757d !important;
        }
    </style>
    <!-- Summary Cards -->
    <div class="row mt-3">
        <div class="col-md-3 col-sm-6">
            <div class="box box-body">
                <div class="d-flex align-items-center">
                    <div class="me-15 bg-success-light rounded-circle p-10">
                        <i class="fa fa-arrow-down fa-2x text-success"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-700">₹{{ number_format($totalCredits, 2) }}</h4>
                        <p class="text-muted mb-0">Total Credits</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="box box-body">
                <div class="d-flex align-items-center">
                    <div class="me-15 bg-danger-light rounded-circle p-10">
                        <i class="fa fa-arrow-up fa-2x text-danger"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-700">₹{{ number_format($totalDebits, 2) }}</h4>
                        <p class="text-muted mb-0">Total Debits</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="box box-body">
                <div class="d-flex align-items-center">
                    <div class="me-15 bg-warning-light rounded-circle p-10">
                        <i class="fa fa-pause fa-2x text-warning" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-700">₹{{ number_format($pendingAmount, 2) }}</h4>
                        <p class="text-muted mb-0">Pending Amount</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="box box-body">
                <div class="d-flex align-items-center">
                    <div class="me-15 bg-info-light rounded-circle p-10">
                        <i class="fa fa-exchange fa-2x text-info"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-700">{{ $totalTransactions }}</h4>
                        <p class="text-muted mb-0">Total Transactions</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">All Wallet Transactions</h4>
                        <div class="box-controls pull-right">
                            <div class="d-flex gap-2">
                                <select id="type-filter" class="form-select form-select-sm" style="width: 150px;">
                                    <option value="">All Types</option>
                                    <option value="in">Credit (In)</option>
                                    <option value="out">Debit (Out)</option>
                                </select>
                                <select id="status-filter" class="form-select form-select-sm" style="width: 150px;">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="completed">Completed</option>
                                    <option value="failed">Failed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="wallet-transactions-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Role</th>
                                        <th>Type</th>
                                        <th>Reason</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Payment Method</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $index => $transaction)
                                        <tr>
                                            <td>{{ $transactions->firstItem() + $index }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-10">
                                                        <img src="{{ asset('images/avatar/avatar-11.png') }}"
                                                            class="avatar avatar-sm rounded-circle" alt="User">
                                                    </div>
                                                    <div>
                                                        <strong>{{ $transaction->user->first_name ?? '' }}
                                                            {{ $transaction->user->last_name ?? '' }}</strong>
                                                        <br>
                                                        <small
                                                            class="text-muted">{{ $transaction->user->email ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $transaction->user->role === 'delivery_partner' ? 'info' : ($transaction->user->role === 'customer' ? 'success' : 'secondary') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $transaction->user->role ?? 'N/A')) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($transaction->type === 'in')
                                                    <span class="badge badge-success-light">
                                                        <i class="fa fa-arrow-down text-success me-1"></i> Credit
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger-light">
                                                        <i class="fa fa-arrow-up text-danger me-1"></i> Debit
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->reason ?? 'N/A' }}</td>
                                            <td>
                                                <strong
                                                    class="{{ $transaction->type === 'in' ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->type === 'in' ? '+' : '-' }}₹{{ number_format($transaction->amount, 2) }}
                                                </strong>
                                            </td>
                                            <td>
                                                @switch($transaction->status)
                                                    @case('completed')
                                                        <span class="badge badge-success">Completed</span>
                                                    @break

                                                    @case('pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @break

                                                    @case('failed')
                                                        <span class="badge badge-danger">Failed</span>
                                                    @break

                                                    @case('cancelled')
                                                        <span class="badge badge-secondary">Cancelled</span>
                                                    @break

                                                    @default
                                                        <span class="badge badge-info">{{ ucfirst($transaction->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if ($transaction->paymentDetail)
                                                    @if ($transaction->paymentDetail->pay_type === 'bank')
                                                        <span class="badge badge-primary-light">
                                                            <i class="fa fa-university me-1"></i> Bank
                                                        </span>
                                                        <br>
                                                        <small
                                                            class="text-muted">{{ $transaction->paymentDetail->pay_bank_name ?? '' }}</small>
                                                    @else
                                                        <span class="badge badge-info-light">
                                                            <i class="fa fa-mobile me-1"></i> UPI
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $transaction->created_at->format('d M Y') }}
                                                <br>
                                                <small
                                                    class="text-muted">{{ $transaction->created_at->format('h:i A') }}</small>
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">
                                                    <div class="py-4">
                                                        <i class="fa fa-wallet fa-3x text-muted mb-3"></i>
                                                        <p class="text-muted">No wallet transactions found.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                         <x-pagination-summary :paginator="$transactions" />

                        </div>
                    </div>
                </div>
            </div>


        @endsection

        @section('scripts')
            <script>
                $(document).ready(function() {
                    // Filter functionality
                    function applyFilters() {
                        var type = $('#type-filter').val();
                        var status = $('#status-filter').val();
                        var url = new URL(window.location.href);

                        if (type) {
                            url.searchParams.set('type', type);
                        } else {
                            url.searchParams.delete('type');
                        }

                        if (status) {
                            url.searchParams.set('status', status);
                        } else {
                            url.searchParams.delete('status');
                        }

                        window.location.href = url.toString();
                    }

                    // Set initial filter values from URL
                    var urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('type')) {
                        $('#type-filter').val(urlParams.get('type'));
                    }
                    if (urlParams.has('status')) {
                        $('#status-filter').val(urlParams.get('status'));
                    }

                    // Apply filters on change
                    $('#type-filter, #status-filter').change(function() {
                        applyFilters();
                    });
                });
            </script>
        @endsection
