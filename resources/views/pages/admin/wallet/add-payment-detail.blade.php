@extends('layouts.admin')

@section('title', 'Add Payment Method')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Add Payment Method</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i
                                        class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.wallet.index') }}">Wallet</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.wallet.payment-details') }}">Payment
                                    Methods</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add New</li>
                        </ol>
                    </nav>
                </div>
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

        <div class="row">
            <div class="col-lg-8 col-12">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title">Payment Method Details</h4>
                    </div>
                    <form action="{{ route('admin.wallet.payment-details.store') }}" method="POST">
                        @csrf
                        <div class="box-body -w-100">
                            <!-- Payment Type Selection -->
                            <div class="form-group">
                                <label class="form-label">Payment Type <span class="text-danger">*</span></label>
                                <div class="row g-3">

                                    <!-- Bank -->
                                    <div class="col-md-6">
                                        <label class="payment-type-card w-100 col-md-6">

                                            <input type="radio" name="pay_type" value="bank" class="d-none"
                                                {{ old('pay_type', 'bank') === 'bank' ? 'checked' : '' }}>

                                            <div
                                                class="card text-center p-3 
                    {{ old('pay_type', 'bank') === 'bank' ? 'border-primary shadow-sm' : '' }}">

                                                <i class="fa fa-university fa-2x text-primary mb-2"></i>

                                                <h6 class="mb-0">Bank Account</h6>
                                                <small class="text-muted">Add your bank details</small>
                                            </div>

                                        </label>
                                    </div>

                                    <!-- UPI -->
                                    <div class="col-md-6">
                                        <label class="payment-type-card w-100">

                                            <input type="radio" name="pay_type" value="upi" class="d-none"
                                                {{ old('pay_type') === 'upi' ? 'checked' : '' }}>

                                            <div
                                                class="card text-center p-3
                    {{ old('pay_type') === 'upi' ? 'border-primary shadow-sm' : '' }}">

                                                <i class="fa fa-mobile fa-2x text-success mb-2"></i>

                                                <h6 class="mb-0">UPI</h6>
                                                <small class="text-muted">Add your UPI ID</small>
                                            </div>

                                        </label>
                                    </div>

                                </div>

                                @error('pay_type')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>


                            <hr class="my-4">

                            <!-- Bank Details -->
                            <div id="bankDetails" class="{{ old('pay_type', 'bank') === 'bank' ? '' : 'd-none' }}">
                                <h5 class="mb-3">Bank Account Details</h5>
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
                                            placeholder="Enter account number" value="{{ old('pay_bank_account_number') }}"
                                            maxlength="50">
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

                            <!-- UPI Details -->
                            <div id="upiDetails" class="{{ old('pay_type') === 'upi' ? '' : 'd-none' }}">
                                <h5 class="mb-3">UPI Details</h5>
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
                        </div>
                        <div class="box-footer">
                            <a href="{{ route('admin.wallet.payment-details') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary float-end">
                                <i class="fa fa-save me-1"></i> Save Payment Method
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="col-lg-4 col-12">
                <div class="box bg-info-light">
                    <div class="box-body">
                        <h5><i class="fa fa-shield me-2"></i>Security</h5>
                        <p class="mb-2">Your payment information is secure:</p>
                        <ul class="ps-3">
                            <li class="mb-2">Account numbers are masked for display</li>
                            <li class="mb-2">UPI IDs are partially hidden</li>
                            <li class="mb-2">Data is encrypted in transit</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Payment type card selection
            $('.payment-type-card input[type="radio"]').on('change', function() {
                $('.payment-type-card .card').removeClass('border-primary selected');
                $(this).closest('.payment-type-card').find('.card').addClass('border-primary selected');

                var payType = $(this).val();
                if (payType === 'bank') {
                    $('#bankDetails').removeClass('d-none');
                    $('#upiDetails').addClass('d-none');
                } else {
                    $('#bankDetails').addClass('d-none');
                    $('#upiDetails').removeClass('d-none');
                }
            });

            // Make whole card clickable
            $('.payment-type-card').on('click', function() {
                $(this).find('input[type="radio"]').prop('checked', true).trigger('change');
            });

            // IFSC uppercase
            $('#pay_bank_ifsc').on('input', function() {
                $(this).val($(this).val().toUpperCase());
            });
        });
    </script>

    <style>
        .payment-type-card .card {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid #e9ecef;
        }

        .payment-type-card .card:hover {
            border-color: #7367f0;
            box-shadow: 0 0 10px rgba(115, 103, 240, 0.2);
        }

        .payment-type-card .card.selected {
            border-color: #7367f0 !important;
            background-color: rgba(115, 103, 240, 0.05);
        }
    </style>
@endsection
