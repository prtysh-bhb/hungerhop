@extends('layouts.admin')

@section('title', 'Payment History')

@section('styles')
<style>
.filter-card {
    background: white;
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    vertical-align: middle;
    border-top: 1px solid #e3e6f0;
}

.table tbody tr:hover {
    background-color: #f8f9fc;
}

.btn-group .btn {
    margin-right: 2px;
}

.badge {
    font-size: 0.75rem;
}
</style>
@endsection

@section('content')
<div class="container-full">
    <!-- Content Header -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Payment History</h4>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.tenant') }}"><i class="mdi mdi-home-outline"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.tenant.payment.plans') }}">Payment</a></li>
                        <li class="breadcrumb-item active">History</li>
                    </ol>
                </nav>
            </div>
            <div class="text-end">
                <a href="{{ route('admin.tenant.payment.plans') }}" class="btn btn-primary">
                    <i class="fa fa-credit-card me-2"></i>Make Payment
                </a>
            </div>
        </div>
    </div>

    <section class="content">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="box">
                    <div class="box-body text-center">
                        <h3 class="text-success">{{ $payments->where('status', 'completed')->count() }}</h3>
                        <p class="text-muted">Completed Payments</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box">
                    <div class="box-body text-center">
                        <h3 class="text-warning">{{ $payments->where('status', 'pending')->count() }}</h3>
                        <p class="text-muted">Pending Payments</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box">
                    <div class="box-body text-center">
                        <h3 class="text-danger">{{ $payments->where('status', 'failed')->count() }}</h3>
                        <p class="text-muted">Failed Payments</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="box">
                    <div class="box-body text-center">
                        <h3 class="text-primary">₹{{ number_format($payments->where('status', 'completed')->sum('total_amount'), 2) }}</h3>
                        <p class="text-muted">Total Paid</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-card">
            <form method="GET" action="{{ route('admin.tenant.payment.history') }}">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-filter me-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.tenant.payment.history') }}" class="btn btn-secondary">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Payment History Table -->
        @if($payments->count() > 0)
            <div class="box" style="margin-top: 15px;">
                <div class="box-header with-border">
                    <h4 class="box-title">Payment History ({{ $payments->total() }} records)</h4>
                </div>
                <div class="box-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Payment ID</th>
                                    <th>Plan</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment Method</th>
                                    <th>Billing Period</th>
                                    <th>Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                <tr>
                                    <td>
                                        <strong>#{{ $payment->id }}</strong>
                                        @if($payment->gateway_transaction_id)
                                            <br><small class="text-muted">{{ Str::limit($payment->gateway_transaction_id, 20) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($payment->subscription_plan) }}</span>
                                        <br><small class="text-muted">{{ $payment->restaurant_count }} Restaurant(s)</small>
                                    </td>
                                    <td>
                                        <strong class="text-primary">₹{{ number_format($payment->total_amount, 2) }}</strong>
                                        <br><small class="text-muted">Base: ₹{{ number_format($payment->base_amount, 2) }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match($payment->status) {
                                                'completed' => 'success',
                                                'pending' => 'warning',
                                                'failed' => 'danger',
                                                'cancelled' => 'secondary',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">{{ ucfirst($payment->status) }}</span>
                                        @if($payment->failure_reason)
                                            <br><small class="text-danger">{{ Str::limit($payment->failure_reason, 30) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst($payment->payment_method) }}</span>
                                        <br><small class="text-muted">{{ ucfirst($payment->payment_gateway) }}</small>
                                    </td>
                                    <td>
                                        <small>
                                            <strong>From:</strong> {{ $payment->billing_period_start->format('M d, Y') }}<br>
                                            <strong>To:</strong> {{ $payment->billing_period_end->format('M d, Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <small>
                                            <strong>Created:</strong> {{ $payment->created_at->format('M d, Y') }}<br>
                                            @if($payment->paid_at)
                                                <strong>Paid:</strong> {{ $payment->paid_at->format('M d, Y') }}
                                            @else
                                                <strong>Due:</strong> {{ $payment->due_date->format('M d, Y') }}
                                            @endif
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="viewPaymentDetails({{ $payment->id }})">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            @if($payment->status == 'completed')
                                                <a href="{{ route('admin.tenant.payment.invoice', $payment) }}" 
                                                   class="btn btn-sm btn-outline-success">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            @endif
                                            @if($payment->status == 'pending')
                                                <a href="{{ route('admin.tenant.payment.checkout') }}" 
                                                   class="btn btn-sm btn-outline-warning">
                                                    <i class="fa fa-credit-card"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Showing {{ $payments->firstItem() ?? 0 }} to {{ $payments->lastItem() ?? 0 }} 
                            of {{ $payments->total() }} results
                        </div>
                        <div>
                            {{ $payments->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="empty-state" style="margin-top: 15px;">
                <i class="fa fa-credit-card"></i>
                <h4>No Payment History</h4>
                <p>You haven't made any payments yet.</p>
                <a href="{{ route('admin.tenant.payment.plans') }}" class="btn btn-primary">
                    <i class="fa fa-credit-card me-2"></i>Make Your First Payment
                </a>
            </div>
        @endif
    </section>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="paymentDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function viewPaymentDetails(paymentId) {
    // Find payment details from the current page data
    const payments = @json($payments->items());
    const payment = payments.find(p => p.id == paymentId);
    
    if (!payment) {
        alert('Payment details not found');
        return;
    }
    
    const content = `
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Payment ID:</strong></td>
                        <td>#${payment.id}</td>
                    </tr>
                    <tr>
                        <td><strong>Plan:</strong></td>
                        <td>${payment.subscription_plan}</td>
                    </tr>
                    <tr>
                        <td><strong>Amount:</strong></td>
                        <td>₹${new Intl.NumberFormat('en-IN').format(payment.total_amount)}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td><span class="badge bg-${getStatusColor(payment.status)}">${payment.status.charAt(0).toUpperCase() + payment.status.slice(1)}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Payment Method:</strong></td>
                        <td>${payment.payment_method}</td>
                    </tr>
                    <tr>
                        <td><strong>Payment Gateway:</strong></td>
                        <td>${payment.payment_gateway}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Base Amount:</strong></td>
                        <td>₹${new Intl.NumberFormat('en-IN').format(payment.base_amount)}</td>
                    </tr>
                    <tr>
                        <td><strong>Restaurant Count:</strong></td>
                        <td>${payment.restaurant_count}</td>
                    </tr>
                    <tr>
                        <td><strong>Per Restaurant Fee:</strong></td>
                        <td>₹${new Intl.NumberFormat('en-IN').format(payment.per_restaurant_amount)}</td>
                    </tr>
                    <tr>
                        <td><strong>Due Date:</strong></td>
                        <td>${new Date(payment.due_date).toLocaleDateString()}</td>
                    </tr>
                    <tr>
                        <td><strong>Created:</strong></td>
                        <td>${new Date(payment.created_at).toLocaleString()}</td>
                    </tr>
                    ${payment.paid_at ? `<tr><td><strong>Paid At:</strong></td><td>${new Date(payment.paid_at).toLocaleString()}</td></tr>` : ''}
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <h6>Billing Period</h6>
                <div class="alert alert-info">
                    <strong>From:</strong> ${new Date(payment.billing_period_start).toLocaleDateString()} <br>
                    <strong>To:</strong> ${new Date(payment.billing_period_end).toLocaleDateString()}
                </div>
            </div>
        </div>
        
        ${payment.gateway_transaction_id ? `
        <div class="row">
            <div class="col-12">
                <h6>Transaction Details</h6>
                <div class="alert alert-secondary">
                    <strong>Transaction ID:</strong> ${payment.gateway_transaction_id}
                </div>
            </div>
        </div>
        ` : ''}
        
        ${payment.failure_reason ? `
        <div class="row">
            <div class="col-12">
                <h6>Failure Information</h6>
                <div class="alert alert-danger">
                    ${payment.failure_reason}
                </div>
            </div>
        </div>
        ` : ''}
    `;
    
    $('#paymentDetailsContent').html(content);
    $('#paymentDetailsModal').modal('show');
}

function getStatusColor(status) {
    switch(status) {
        case 'completed': return 'success';
        case 'pending': return 'warning';
        case 'failed': return 'danger';
        case 'cancelled': return 'secondary';
        default: return 'secondary';
    }
}
</script>
@endsection