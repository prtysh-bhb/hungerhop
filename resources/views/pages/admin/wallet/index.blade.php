@extends('layouts.admin')

@section('title', 'My Wallet')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">My Wallet</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i
                                        class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Wallet</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Wallet Summary Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex align-items-center">
                            <div class="me-15 bg-primary-light h-50 w-50 l-h-60 rounded text-center">
                                <i class="fa fa-wallet fa-lg text-primary"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">₹{{ number_format($wallet['current_balance'], 2) }}</h5>
                                <p class="text-muted mb-0">Current Balance</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex align-items-center">
                            <div class="me-15 bg-success-light h-50 w-50 l-h-60 rounded text-center">
                                <i class="fa fa-arrow-down fa-lg text-success"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">₹{{ number_format($wallet['total_credits'], 2) }}</h5>
                                <p class="text-muted mb-0">Total Credits</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex align-items-center">
                            <div class="me-15 bg-danger-light h-50 w-50 l-h-60 rounded text-center">
                                <i class="fa fa-arrow-up fa-lg text-danger"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">₹{{ number_format($wallet['total_debits'], 2) }}</h5>
                                <p class="text-muted mb-0">Total Debits</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex align-items-center">
                            <div class="me-15 bg-warning-light h-50 w-50 l-h-60 rounded text-center">
                                <i class="fa fa-clock fa-lg text-warning"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">₹{{ number_format($wallet['pending_withdrawals'], 2) }}</h5>
                                <p class="text-muted mb-0">Pending Withdrawals</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ route('admin.wallet.add-money') }}" class="btn btn-success">
                                <i class="fa fa-plus me-2"></i>Add Money
                            </a>
                            <a href="{{ route('admin.wallet.withdraw') }}" class="btn btn-danger">
                                <i class="fa fa-minus me-2"></i>Withdraw
                            </a>
                            <a href="{{ route('admin.wallet.payment-details') }}" class="btn btn-primary">
                                <i class="fa fa-credit-card me-2"></i>Payment Methods
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Recent Transactions</h4>
                        <div class="box-tools pull-right">
                            <a href="{{ route('admin.wallet.transactions') }}" class="btn btn-sm btn-outline-primary">
                                View All <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
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
                                            <td>{{ $transaction->created_at->format('d M Y, h:i A') }}</td>
                                            <td>
                                                @if ($transaction->type === 'in')
                                                    <span class="badge bg-success">Credit</span>
                                                @else
                                                    <span class="badge bg-danger">Debit</span>
                                                @endif
                                            </td>
                                            <td>{{ ucwords(str_replace('_', ' ', $transaction->reason)) }}</td>
                                            <td>
                                                @if ($transaction->type === 'in')
                                                    <span
                                                        class="text-success">+₹{{ number_format($transaction->amount, 2) }}</span>
                                                @else
                                                    <span
                                                        class="text-danger">-₹{{ number_format($transaction->amount, 2) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($transaction->paymentDetail)
                                                    @if ($transaction->paymentDetail->pay_type === 'bank')
                                                        <span class="text-muted">
                                                            <i class="fa fa-university me-1"></i>
                                                            {{ $transaction->paymentDetail->pay_bank_name }}
                                                            ({{ $transaction->paymentDetail->masked_account_number }})
                                                        </span>
                                                    @else
                                                        <span class="text-muted">
                                                            <i class="fa fa-mobile me-1"></i>
                                                            UPI: {{ $transaction->paymentDetail->masked_upi_id }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($transaction->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @break

                                                    @case('completed')
                                                        <span class="badge bg-success">Completed</span>
                                                    @break

                                                    @case('failed')
                                                        <span class="badge bg-danger">Failed</span>
                                                    @break

                                                    @case('cancelled')
                                                        <span class="badge bg-secondary">Cancelled</span>
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
                                                        <p>You haven't made any wallet transactions yet.</p>
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

            <!-- Payment Methods Summary -->
            @if ($paymentDetails->count() > 0)
                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h4 class="box-title">My Payment Methods</h4>
                            </div>
                            <div class="box-body">
                                <div class="row g-3">
                                    @foreach ($paymentDetails as $pd)
                                        <div class="col-md-4 col-sm-6">
                                            <div class="box mb-0 h-100 border shadow-sm payment-method-card">
                                                <div class="box-body">
                                                    @if ($pd->pay_type === 'bank')
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div
                                                                class="me-3 bg-primary-light h-50 w-50 l-h-50 rounded text-center flex-shrink-0">
                                                                <i class="ti-home fs-20 text-primary"></i>
                                                            </div>
                                                            <div class="text-truncate">
                                                                <h6 class="mb-0 fw-bold text-truncate">{{ $pd->pay_bank_name }}</h6>
                                                                <small class="text-muted">Bank Account</small>
                                                            </div>
                                                        </div>
                                                        <p class="mb-1 detail-line"><strong>A/C:</strong>
                                                            <span class="text-break">{{ $pd->masked_account_number }}</span>
                                                        </p>
                                                        <p class="mb-1 detail-line"><strong>IFSC:</strong>
                                                            <span class="text-break">{{ $pd->pay_bank_ifsc }}</span>
                                                        </p>
                                                    @else
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div
                                                                class="me-3 bg-success-light h-50 w-50 l-h-50 rounded text-center flex-shrink-0">
                                                                <i class="ti-mobile fs-20 text-success"></i>
                                                            </div>
                                                            <div class="text-truncate">
                                                                <h6 class="mb-0 fw-bold">UPI</h6>
                                                                <small class="text-muted">UPI Payment</small>
                                                            </div>
                                                        </div>
                                                        <p class="mb-1 detail-line"><strong>UPI ID:</strong>
                                                            <span class="text-break">{{ $pd->masked_upi_id }}</span>
                                                        </p>
                                                    @endif
                                                    <p class="mb-0 text-muted"><small class="text-break">{{ $pd->account_holder_name }}</small>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </section>
    @endsection

@push('styles')
    <style>
        .payment-method-card {
            min-height: 180px;
        }

        .payment-method-card .detail-line {
            font-size: 0.95rem;
            line-height: 1.2;
        }

        .payment-method-card .text-break {
            word-break: break-word;
            overflow-wrap: anywhere;
        }
    </style>
@endpush
