@extends('layouts.admin')

@section('title', 'Add Money to Wallet')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Add Money to Wallet</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                           <li class="breadcrumb-item"><a href="{{ route('location-admin.dashboard') }}"><i
                                        class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.wallet.index') }}">Wallet</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Money</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="text-end">
                <span class="badge badge-info fs-6">Current Balance: ₹{{ number_format($currentBalance, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <!-- Flash Messages -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <!-- Info Card -->
        <div class="col-12">
            <div class="box bg-primary-light">
                <div class="box-body">
                    <h5><i class="fa fa-info-circle me-2"></i>Information</h5>
                    <ul class="ps-3">
                        <li class="mb-2">Minimum deposit: <strong>₹1</strong></li>
                        <li class="mb-2">Maximum deposit: <strong>₹100,000</strong></li>
                        <li class="mb-2">Transaction status will be <strong>Pending</strong> until verified</li>
                        <li class="mb-2">Funds will be credited once approved</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Add Money Details</h4>
                    </div>
                    <form action="{{ route('admin.wallet.add-money.store') }}" method="POST" id="addMoneyForm">
                        @csrf
                        <div class="box-body">
                            <!-- Amount -->
                            <div class="form-group mb-3">
                                <label for="amount" class="form-label">Amount (₹) <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" name="amount" id="amount"
                                        class="form-control @error('amount') is-invalid @enderror"
                                        placeholder="Enter amount" value="{{ old('amount') }}" min="1" max="100000"
                                        step="0.01" required>
                                </div>
                                @error('amount')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Reason -->
                            <div class="form-group mb-3">
                                <label for="reason" class="form-label">Reason (Optional)</label>
                                <input type="text" name="reason" id="reason"
                                    class="form-control @error('reason') is-invalid @enderror"
                                    placeholder="e.g., Monthly deposit, Business funds" value="{{ old('reason') }}"
                                    maxlength="100">
                                @error('reason')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">

                            <!-- Payment Method Selection -->
                            <h5 class="mb-3">Payment Method</h5>

                            <div class="form-group mb-3">
                                <label class="form-label">Payment Type <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="pay_type" id="pay_type_bank"
                                            value="bank" {{ old('pay_type', 'bank') === 'bank' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="pay_type_bank">
                                            <i class="fa fa-university me-1"></i> Bank Transfer
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="pay_type" id="pay_type_upi"
                                            value="upi" {{ old('pay_type') === 'upi' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="pay_type_upi">
                                            <i class="fa fa-mobile me-1"></i> UPI
                                        </label>
                                    </div>
                                </div>
                                @error('pay_type')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Bank Details (shown when bank is selected) -->
                            <div id="bankDetails" class="{{ old('pay_type', 'bank') === 'bank' ? '' : 'd-none' }}">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="pay_bank_name" class="form-label">Bank Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="pay_bank_name" id="pay_bank_name"
                                            class="form-control @error('pay_bank_name') is-invalid @enderror"
                                            placeholder="e.g., State Bank of India" value="{{ old('pay_bank_name') }}"
                                            maxlength="150">
                                        @error('pay_bank_name')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="account_holder_name" class="form-label">Account Holder Name</label>
                                        <input type="text" name="account_holder_name" id="account_holder_name"
                                            class="form-control @error('account_holder_name') is-invalid @enderror"
                                            placeholder="e.g., John Doe"
                                            value="{{ old('account_holder_name', auth()->user()->first_name . ' ' . auth()->user()->last_name) }}"
                                            maxlength="150">
                                        @error('account_holder_name')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="pay_bank_account_number" class="form-label">Account Number <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="pay_bank_account_number" id="pay_bank_account_number"
                                            class="form-control @error('pay_bank_account_number') is-invalid @enderror"
                                            placeholder="Enter account number"
                                            value="{{ old('pay_bank_account_number') }}" maxlength="50">
                                        @error('pay_bank_account_number')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="pay_bank_ifsc" class="form-label">IFSC Code <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="pay_bank_ifsc" id="pay_bank_ifsc"
                                            class="form-control @error('pay_bank_ifsc') is-invalid @enderror"
                                            placeholder="e.g., SBIN0001234" value="{{ old('pay_bank_ifsc') }}"
                                            maxlength="20" style="text-transform: uppercase;">
                                        @error('pay_bank_ifsc')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- UPI Details (shown when UPI is selected) -->
                            <div id="upiDetails" class="{{ old('pay_type') === 'upi' ? '' : 'd-none' }}">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="pay_upi_id" class="form-label">UPI ID <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="pay_upi_id" id="pay_upi_id"
                                            class="form-control @error('pay_upi_id') is-invalid @enderror"
                                            placeholder="e.g., yourname@upi" value="{{ old('pay_upi_id') }}"
                                            maxlength="100">
                                        @error('pay_upi_id')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="account_holder_name_upi" class="form-label">Account Holder
                                            Name</label>
                                        <input type="text" name="account_holder_name" id="account_holder_name_upi"
                                            class="form-control" placeholder="e.g., John Doe"
                                            value="{{ old('account_holder_name', auth()->user()->first_name . ' ' . auth()->user()->last_name) }}"
                                            maxlength="150">
                                    </div>
                                </div>
                            </div>

                            @if ($paymentDetails->count() > 0)
                                <hr class="my-4">
                                <h6 class="mb-3">Or Select from Saved Payment Methods</h6>
                                <div class="row">
                                    @foreach ($paymentDetails as $pd)
                                        <div class="col-md-6 mb-2">
                                            <div class="card border cursor-pointer saved-payment-card"
                                                data-pay-type="{{ $pd->pay_type }}"
                                                data-bank-name="{{ $pd->pay_bank_name }}"
                                                data-account-number="{{ $pd->pay_bank_account_number }}"
                                                data-ifsc="{{ $pd->pay_bank_ifsc }}" data-upi-id="{{ $pd->pay_upi_id }}"
                                                data-holder-name="{{ $pd->account_holder_name }}">
                                                <div class="card-body py-2">
                                                    @if ($pd->pay_type === 'bank')
                                                        <div class="d-flex align-items-center">
                                                            <i class="fa fa-university text-primary me-2"></i>
                                                            <div>
                                                                <small class="fw-bold">{{ $pd->pay_bank_name }}</small>
                                                                <br>
                                                                <small
                                                                    class="text-muted">{{ $pd->masked_account_number }}</small>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="d-flex align-items-center">
                                                            <i class="fa fa-mobile text-success me-2"></i>
                                                            <div>
                                                                <small class="fw-bold">UPI</small>
                                                                <br>
                                                                <small class="text-muted">{{ $pd->masked_upi_id }}</small>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="box-footer">
                            <a href="{{ route('admin.wallet.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success float-end">
                                <i class="fa fa-plus me-1"></i> Add Money
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Toggle payment type sections
            $('input[name="pay_type"]').on('change', function() {
                var payType = $(this).val();
                if (payType === 'bank') {
                    $('#bankDetails').removeClass('d-none');
                    $('#upiDetails').addClass('d-none');
                } else {
                    $('#bankDetails').addClass('d-none');
                    $('#upiDetails').removeClass('d-none');
                }
            });

            // Click on saved payment method to auto-fill
            $('.saved-payment-card').on('click', function() {
                var card = $(this);
                var payType = card.data('pay-type');

                // Select the correct radio button
                $('input[name="pay_type"][value="' + payType + '"]').prop('checked', true).trigger(
                    'change');

                if (payType === 'bank') {
                    $('#pay_bank_name').val(card.data('bank-name'));
                    $('#pay_bank_account_number').val(card.data('account-number'));
                    $('#pay_bank_ifsc').val(card.data('ifsc'));
                    $('#account_holder_name').val(card.data('holder-name'));
                } else {
                    $('#pay_upi_id').val(card.data('upi-id'));
                    $('#account_holder_name_upi').val(card.data('holder-name'));
                }

                // Visual feedback
                $('.saved-payment-card').removeClass('border-primary');
                card.addClass('border-primary');
            });

            // IFSC uppercase
            $('#pay_bank_ifsc').on('input', function() {
                $(this).val($(this).val().toUpperCase());
            });
        });
    </script>

    <style>
        .saved-payment-card {
            transition: all 0.2s ease;
        }

        .saved-payment-card:hover {
            border-color: #7367f0 !important;
            cursor: pointer;
        }

        .saved-payment-card.border-primary {
            border-color: #7367f0 !important;
            border-width: 2px !important;
        }
    </style>
@endsection
