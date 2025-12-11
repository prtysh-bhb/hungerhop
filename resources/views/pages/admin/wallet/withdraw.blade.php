@extends('layouts.admin')

@section('title', 'Withdraw from Wallet')
<style>
    #payment {
        /* Makes the element transparent */
        opacity: 0;
        /* Removes it from the page flow so it doesn't take up space */
        position: absolute;
        /* Optional: ensures no pointer events on the invisible element itself */
        pointer-events: none;
    }

    .payment-radio {
        display: none;
    }

    .payment-radio {
        visibility: hidden;
    }
</style>
@section('content')
    <div class="content-header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="page-title mb-1 fw-bold">Withdraw Funds</h4>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                                <i class="mdi mdi-home-outline"></i>
                            </a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.wallet.index') }}">Wallet</a></li>
                        <li class="breadcrumb-item active">Withdraw</li>
                    </ol>
                </nav>
            </div>

            <div class="d-flex align-items-center gap-2">
                <div class="balance-badge">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-wallet me-2 text-primary"></i>
                        <div>
                            <small class="d-block text-muted">Available Balance</small>
                            <span class="fs-5 fw-bold">₹{{ number_format($currentBalance, 2) }}</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.wallet.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <section class="content">
        <!-- Flash Messages -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fa fa-exclamation-circle me-3 fs-5"></i>
                    <div class="flex-grow-1">{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fa fa-check-circle me-3 fs-5"></i>
                    <div class="flex-grow-1">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        <div class="row">
            <!-- Left Column - Withdrawal Form -->
            <div class="col-lg-8">
                @if ($paymentDetails->count() === 0)
                    <!-- Empty State -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <div class="empty-state-icon mb-4">
                                <i class="fa fa-credit-card fa-4x text-muted opacity-50"></i>
                            </div>
                            <h4 class="fw-bold mb-3">No Payment Methods</h4>
                            <p class="text-muted mb-4">You need to add a payment method before you can withdraw funds.</p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="{{ route('admin.wallet.payment-details.create') }}" class="btn btn-primary btn-lg">
                                    <i class="fa fa-plus-circle me-2"></i> Add Payment Method
                                </a>
                                <a href="{{ route('admin.wallet.index') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fa fa-arrow-left me-2"></i> Back to Wallet
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Withdrawal Form Card -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <div class="d-flex align-items-center">
                                <div class="card-icon me-3">
                                    <i class="fa fa-paper-plane fa-lg text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Withdrawal Request</h5>
                                    <small class="text-muted">Fill the details below to withdraw funds</small>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('admin.wallet.withdraw.store') }}" method="POST" id="withdrawForm">
                            @csrf
                            <div class="card-body p-4">
                                <!-- Amount Section -->
                                <div class="mb-4">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <label class="form-label fw-bold mb-0">
                                            <i class="fa fa-money-bill-wave me-2 text-primary"></i>
                                            Withdrawal Amount
                                        </label>
                                        <span class="badge bg-danger">Required</span>
                                    </div>

                                    <div class="amount-input-section">
                                        <div class="input-group input-group-lg mb-2">
                                            <span
                                                class="input-group-text bg-white border-end-0 fw-bold fs-4 text-primary">₹</span>
                                            <input type="number" name="amount" id="amount"
                                                class="form-control form-control-lg border-start-0 shadow-none @error('amount') is-invalid @enderror"
                                                value="{{ old('amount') }}" placeholder="0.00" min="1"
                                                max="{{ $currentBalance }}" step="0.01" required
                                                aria-label="Withdrawal amount">
                                            <button type="button" id="maxAmountBtn" class="btn btn-primary fw-bold px-4">
                                                MAX
                                            </button>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fa fa-info-circle me-1"></i>
                                                Min: ₹1 | Max: ₹{{ number_format($currentBalance, 2) }}
                                            </small>
                                            <button type="button" id="halfAmountBtn"
                                                class="btn btn-sm btn-outline-primary">
                                                50%
                                            </button>
                                        </div>

                                        @error('amount')
                                            <div class="alert alert-danger mt-3 py-2">
                                                <i class="fa fa-exclamation-circle me-2"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Reason Section -->
                                <div class="mb-4">
                                    <label for="reason" class="form-label fw-bold mb-2">
                                        <i class="fa fa-sticky-note me-2 text-muted"></i>
                                        Reason (Optional)
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <input type="text" name="reason" id="reason"
                                            class="form-control form-control-lg @error('reason') is-invalid @enderror"
                                            placeholder="Example: Personal expense, Business withdrawal, etc."
                                            maxlength="100" value="{{ old('reason') }}">
                                    </div>
                                    <small class="text-muted mt-1">
                                        <i class="fa fa-lightbulb me-1"></i>
                                        Helps you identify this transaction later
                                    </small>
                                    @error('reason')
                                        <div class="alert alert-danger mt-3 py-2">
                                            <i class="fa fa-exclamation-circle me-2"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Payment Methods Section -->
                                <div class="payment-methods-section">
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div>
                                            <h5 class="fw-bold mb-0">
                                                <i class="fa fa-credit-card me-2 text-success"></i>
                                                Select Payment Method
                                            </h5>
                                            <small class="text-muted">Choose where to send your money</small>
                                        </div>
                                        <span class="badge bg-danger">Required</span>
                                    </div>

                                    @error('payment_detail_id')
                                        <div class="alert alert-danger mb-4">
                                            <i class="fa fa-exclamation-circle me-2"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    <!-- Payment Methods Grid -->
                                    <div class="row g-3 mb-4">
                                        @foreach ($paymentDetails as $pd)
                                            <div class="col-lg-4 col-md-6">
                                                <input style="display: none;" type="radio" name="payment_detail_id"
                                                    value="{{ $pd->id }}" id="payment-{{ $pd->id }}"
                                                    class="payment-radio"
                                                    {{ old('payment_detail_id') == $pd->id ? 'checked' : '' }}>


                                                <label for="payment-{{ $pd->id }}"
                                                    class="payment-method-card d-block h-100 {{ old('payment_detail_id') == $pd->id ? 'selected' : '' }}">
                                                    <div class="pm-card-inner">
                                                        <!-- Card Header with Icon & Type -->
                                                        <div class="pm-header">
                                                            <div
                                                                class="pm-icon {{ $pd->pay_type === 'bank' ? 'bank' : 'upi' }}">
                                                                @if ($pd->pay_type === 'bank')
                                                                    <i class="fa fa-university"></i>
                                                                @else
                                                                    <i class="fa fa-mobile fa-lg"></i>
                                                                @endif
                                                            </div>
                                                            <div class="pm-type">
                                                                <span
                                                                    class="pm-type-badge {{ $pd->pay_type === 'bank' ? 'bank' : 'upi' }}">
                                                                    {{ $pd->pay_type === 'bank' ? 'Bank Account' : 'UPI' }}
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <!-- Card Body -->
                                                        <div class="pm-body">
                                                            <div class="pm-holder">{{ $pd->account_holder_name }}</div>

                                                            @if ($pd->pay_type === 'bank')
                                                                <div class="pm-detail">
                                                                    <span class="pm-label">A/C:</span>
                                                                    <span
                                                                        class="pm-value">{{ $pd->masked_account_number }}</span>
                                                                </div>
                                                                <div class="pm-detail">
                                                                    <span class="pm-label">IFSC:</span>
                                                                    <span class="pm-value">{{ $pd->pay_bank_ifsc }}</span>
                                                                </div>
                                                            @else
                                                                <div class="pm-detail">
                                                                    <span class="pm-label">UPI:</span>
                                                                    <span class="pm-value">{{ $pd->masked_upi_id }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Add New Payment Method -->
                                    <div class="text-center">
                                        <a href="{{ route('admin.wallet.payment-details.create') }}"
                                            class="btn btn-primary">
                                            <i class="fa fa-plus-circle me-2"></i>
                                            Add New Payment Method
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="card-footer bg-white border-top py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('admin.wallet.index') }}" class="btn btn-outline-secondary">
                                            <i class="fa fa-arrow-left me-2"></i> Cancel
                                        </a>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="withdraw-summary d-none" id="withdrawSummary">
                                            <small class="text-muted d-block">You are withdrawing</small>
                                            <div class="fw-bold fs-5 text-danger" id="summaryAmount">₹0.00</div>
                                        </div>
                                        <button type="submit" class="btn btn-danger"
                                            {{ $currentBalance <= 0 ? 'disabled' : '' }}>
                                            <i class="fa fa-paper-plane me-2"></i>
                                            <span class="withdraw-text">Proceed to Withdraw</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Right Column - Information -->
            <div class="col-lg-4">
                @if ($currentBalance <= 0)
                    <!-- Insufficient Balance Card -->
                    <div class="card bg-gradient-danger text-white border-0 shadow-lg mb-4">
                        <div class="card-body p-4 text-center">
                            <div class="mb-3">
                                <i class="fa fa-wallet fa-4x opacity-50"></i>
                            </div>
                            <h5 class="fw-bold mb-2">Insufficient Balance</h5>
                            <p class="opacity-75 mb-4">You don't have enough balance to make a withdrawal.</p>
                            <a href="{{ route('admin.wallet.add-money') }}" class="btn btn-light btn-lg">
                                <i class="fa fa-plus me-2"></i> Add Money
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Balance Summary Card -->
                    <div class="card text-white border-0 shadow-lg mb-4" style="background-color: #e66430;">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h5 class="fw-bold mb-0">Balance Summary</h5>
                                <i class="fa fa-wallet fa-2x opacity-50"></i>
                            </div>
                            <div class="balance-stats">
                                <div class="balance-item mb-3">
                                    <small class="opacity-75 d-block">Available Balance</small>
                                    <h2 class="fw-bold mb-0">₹{{ number_format($currentBalance, 2) }}</h2>
                                </div>
                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar bg-white" style="width: 100%" role="progressbar"></div>
                                </div>
                                <div class="text-center">
                                    <small class="opacity-75">100% of your balance is available for withdrawal</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Guidelines Card -->
                <div class="card border-primary shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="fw-bold mb-0">
                            <i class="fa fa-info-circle text-primary me-2"></i>
                            Withdrawal Guidelines
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="guidelines-list">
                            <li class="guideline-item">
                                <i class="fa fa-check-circle text-success me-2"></i>
                                <div>
                                    <strong>Minimum Amount:</strong> ₹1
                                </div>
                            </li>
                            <li class="guideline-item">
                                <i class="fa fa-clock text-warning me-2"></i>
                                <div>
                                    <strong>Processing Time:</strong> 1–3 business days
                                </div>
                            </li>
                            <li class="guideline-item">
                                <i class="fa fa-hourglass-half text-info me-2"></i>
                                <div>
                                    <strong>Status:</strong> Pending until processed
                                </div>
                            </li>
                            <li class="guideline-item">
                                <i class="fa fa-shield-alt text-primary me-2"></i>
                                <div>
                                    <strong>Security:</strong> Verify payment details
                                </div>
                            </li>
                            <li class="guideline-item">
                                <i class="fa fa-exclamation-triangle text-danger me-2"></i>
                                <div>
                                    <strong>Note:</strong> Cannot be cancelled once submitted
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="fw-bold mb-0">
                            <i class="fa fa-bolt text-warning me-2"></i>
                            Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <a href="{{ route('admin.wallet.add-money') }}" class="btn btn-success ">
                                    <i class="fa fa-plus me-1"></i> Add Money
                                </a>
                            </div>
                            <div class="col-7">
                                <a href="{{ route('admin.wallet.payment-details') }}" class="btn btn-primary ">
                                    <i class="fa fa-credit-card "></i> Payment Methods
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Max button functionality
            $('#maxAmountBtn').click(function() {
                let maxAmount = parseFloat({{ $currentBalance }});
                $('#amount').val(maxAmount.toFixed(2));
                updateSummary();
            });

            // Half amount button
            $('#halfAmountBtn').click(function() {
                let maxAmount = parseFloat({{ $currentBalance }});
                let halfAmount = maxAmount / 2;
                $('#amount').val(halfAmount.toFixed(2));
                updateSummary();
            });

            // Update summary with amount
            function updateSummary() {
                let amount = $('#amount').val();
                if (amount && parseFloat(amount) > 0) {
                    $('#withdrawSummary').removeClass('d-none');
                    $('#summaryAmount').text('₹' + parseFloat(amount).toFixed(2));
                } else {
                    $('#withdrawSummary').addClass('d-none');
                }
            }

            // Listen for amount input changes
            $('#amount').on('input', function() {
                let maxAmount = parseFloat({{ $currentBalance }});
                let currentAmount = parseFloat($(this).val()) || 0;

                if (currentAmount > maxAmount) {
                    $(this).val(maxAmount.toFixed(2));
                }

                updateSummary();
            });

            // Payment method selection
            $('.payment-method-card').on('click', function() {
                // Remove selection from all cards
                $('.payment-method-card').removeClass('selected');

                // Add selection to clicked card
                $(this).addClass('selected');
            });

            // Initialize selected state on page load
            $('.payment-radio:checked').each(function() {
                $(this).next('.payment-method-card').addClass('selected');
            });

            // Form validation before submit
            $('#withdrawForm').on('submit', function(e) {
                let amount = parseFloat($('#amount').val()) || 0;
                let minAmount = 1;
                let maxAmount = parseFloat({{ $currentBalance }});

                // Validate amount
                if (amount < minAmount) {
                    e.preventDefault();
                    showAlert('Minimum withdrawal amount is ₹' + minAmount, 'warning');
                    $('#amount').focus();
                    return false;
                }

                if (amount > maxAmount) {
                    e.preventDefault();
                    showAlert('Amount cannot exceed available balance of ₹' + maxAmount.toFixed(2),
                        'warning');
                    $('#amount').focus();
                    return false;
                }

                if (!amount || amount <= 0) {
                    e.preventDefault();
                    showAlert('Please enter a valid amount', 'warning');
                    $('#amount').focus();
                    return false;
                }

                // Validate payment method
                if (!$('input[name="payment_detail_id"]:checked').val()) {
                    e.preventDefault();
                    showAlert('Please select a payment method', 'warning');
                    return false;
                }

                // Show confirmation with details
                let selectedCard = $('input[name="payment_detail_id"]:checked').next(
                    '.payment-method-card');
                let accountHolder = selectedCard.find('.pm-holder').text().trim();
                let amountFormatted = '₹' + amount.toFixed(2);

                if (!confirm(`Are you sure you want to withdraw ${amountFormatted} to ${accountHolder}?`)) {
                    e.preventDefault();
                    return false;
                }

                // Show loading state
                $('.withdraw-btn').prop('disabled', true).html(
                    '<i class="fa fa-spinner fa-spin me-2"></i> Processing...');
            });

            function showAlert(message, type = 'info') {
                // Remove existing alerts
                $('.alert-dismissible').remove();

                // Create new alert
                let alertClass = type === 'warning' ? 'alert-warning' : 'alert-info';
                let iconClass = type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';

                let alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show shadow-sm rounded-3 mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fa ${iconClass} me-3 fs-5"></i>
                        <div class="flex-grow-1">${message}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            `;

                $('.content').prepend(alertHtml);

                // Auto dismiss after 5 seconds
                setTimeout(() => {
                    $('.alert-dismissible').alert('close');
                }, 5000);
            }

            // Initialize
            updateSummary();

            // Auto-focus amount field if empty
            if (!$('#amount').val()) {
                $('#amount').focus();
            }
        });
    </script>

    <style>
        /* Balance Badge */
        .balance-badge {
            background: #f8f9fa;
            border: 1px solid #e4e6ef;
            border-radius: 12px;
            padding: 10px 15px;
            min-width: 180px;
        }

        /* Card Icon */
        .card-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #7367f0, #9c8af9);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        /* Hide native radios (we rely on card selection styling) */
        .payment-radio {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        /* Payment Method Cards */
        .payment-method-label {
            cursor: pointer;
        }

        .payment-method-option .card {
            transition: all 0.3s ease;
        }

        .payment-method-option .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Payment Method Cards - Clean Design */
        .payment-method-card {
            cursor: pointer;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            background: #fff;
            transition: all 0.3s ease;
            overflow: hidden;
            min-height: 220px;
        }

        .payment-method-card:hover {
            border-color: #7367f0;
            box-shadow: 0 4px 15px rgba(115, 103, 240, 0.15);
            transform: translateY(-2px);
        }

        .payment-method-card.selected {
            border-color: #7367f0;
            background: linear-gradient(135deg, rgba(115, 103, 240, 0.05) 0%, rgba(115, 103, 240, 0.1) 100%);
            box-shadow: 0 5px 20px rgba(115, 103, 240, 0.2);
        }

        .pm-card-inner {
            padding: 16px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .pm-header {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .pm-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .pm-icon.bank {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }

        .pm-icon.upi {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: #fff;
        }

        .pm-type {
            flex-grow: 1;
        }

        .pm-type-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pm-type-badge.bank {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .pm-type-badge.upi {
            background: rgba(17, 153, 142, 0.1);
            color: #11998e;
        }

        .pm-body {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
        }

        .pm-holder {
            font-weight: 700;
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
            white-space: normal;
            word-break: break-word;
        }

        .pm-detail {
            display: flex;
            align-items: center;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .pm-detail:last-child {
            margin-bottom: 0;
        }

        .pm-label {
            color: #6c757d;
            margin-right: 6px;
            flex-shrink: 0;
        }

        .pm-value {
            color: #495057;
            font-weight: 500;
            white-space: normal;
            word-break: break-word;
        }

        /* Amount Input Section */
        .amount-input-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e4e6ef;
        }

        /* Guidelines List */
        .guidelines-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .guideline-item {
            display: flex;
            align-items: flex-start;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .guideline-item:last-child {
            border-bottom: none;
        }
        /* Empty State */
        .empty-state-icon {
            opacity: 0.3;
        }

        /* Gradient Backgrounds */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%) !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .balance-badge {
                min-width: auto;
                margin-bottom: 15px;
            }

            .content-header .d-flex {
                flex-direction: column;
                align-items: flex-start;
            }

            .card-footer .d-flex {
                flex-direction: column;
                gap: 15px;
            }

            .withdraw-summary {
                text-align: center;
            }
        }
    </style>
@endsection
