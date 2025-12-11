@extends('layouts.admin')

@section('title', 'Transaction History')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Transaction History</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i
                                        class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.wallet.index') }}">Wallet</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Transaction History</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">All Transactions</h4>
                    </div>
                    <div class="box-body">
                        <!-- Filters -->
                        <form method="GET" action="{{ route('admin.wallet.transactions') }}" class="mb-4">
                            <div class="row align-items-end">
                                <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                    <label class="form-label">Type</label>
                                    <select name="type" class="form-select">
                                        <option value="">All Types</option>
                                        <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Credit
                                        </option>
                                        <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Debit
                                        </option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                                            Completed</option>
                                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed
                                        </option>
                                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                            Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                    <label class="form-label">From Date</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ request('start_date') }}">
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                    <label class="form-label">To Date</label>
                                    <input type="date" name="end_date" class="form-control"
                                        value="{{ request('end_date') }}">
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="ti-filter me-1"></i> Filter
                                    </button>
                                </div>
                                <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                    <a href="{{ route('admin.wallet.transactions') }}" class="btn btn-secondary w-100">
                                        <i class="ti-reload me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- Transactions Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date & Time</th>
                                        <th>Type</th>
                                        <th>Reason</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $index => $transaction)
                                        <tr>
                                            <td>{{ $transactions->firstItem() + $index }}</td>
                                            <td>
                                                {{ $transaction->created_at->format('d M Y') }}
                                                <br>
                                                <small
                                                    class="text-muted">{{ $transaction->created_at->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                @if ($transaction->type === 'in')
                                                    <span class="badge bg-success">
                                                        <i class="fa fa-arrow-down me-1"></i>Credit
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fa fa-arrow-up me-1"></i>Debit
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ ucwords(str_replace('_', ' ', $transaction->reason)) }}</td>
                                            <td>
                                                @if ($transaction->type === 'in')
                                                    <span
                                                        class="text-success fw-bold">+₹{{ number_format($transaction->amount, 2) }}</span>
                                                @else
                                                    <span
                                                        class="text-danger fw-bold">-₹{{ number_format($transaction->amount, 2) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($transaction->paymentDetail)
                                                    @if ($transaction->paymentDetail->pay_type === 'bank')
                                                        <span class="text-muted">
                                                            <i class="fa fa-university me-1"></i>
                                                            {{ $transaction->paymentDetail->pay_bank_name }}
                                                            <br>
                                                            <small>{{ $transaction->paymentDetail->masked_account_number }}</small>
                                                        </span>
                                                    @else
                                                        <span class="text-muted">
                                                            <i class="fa fa-mobile me-1"></i>
                                                            UPI
                                                            <br>
                                                            <small>{{ $transaction->paymentDetail->masked_upi_id }}</small>
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($transaction->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning">
                                                            <i class="fa fa-clock me-1"></i>Pending
                                                        </span>
                                                    @break

                                                    @case('completed')
                                                        <span class="badge bg-success">
                                                            <i class="fa fa-check me-1"></i>Completed
                                                        </span>
                                                    @break

                                                    @case('failed')
                                                        <span class="badge bg-danger">
                                                            <i class="fa fa-times me-1"></i>Failed
                                                        </span>
                                                    @break

                                                    @case('cancelled')
                                                        <span class="badge bg-secondary">
                                                            <i class="fa fa-ban me-1"></i>Cancelled
                                                        </span>
                                                    @break

                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fa fa-inbox fa-3x mb-3"></i>
                                                        <h5>No transactions found</h5>
                                                        <p>No transactions match your filter criteria.</p>
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

            <div class="row mt-3">
                <div class="col-12">
                    <a href="{{ route('admin.wallet.index') }}" class="btn btn-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Back to Wallet
                    </a>
                </div>
            </div>
        </section>
    @endsection
