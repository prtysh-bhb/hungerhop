@extends('layouts.admin')

@section('title', 'Payment Methods')

@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Payment Methods</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i
                                        class="mdi mdi-home-outline"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.wallet.index') }}">Wallet</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Payment Methods</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="text-end">
                <a href="{{ route('admin.wallet.index') }}" class="btn btn-secondary me-2">
                    <i class="ti-arrow-left me-1"></i> Back
                </a>
                <a href="{{ route('admin.wallet.payment-details.create') }}" class="btn btn-primary">
                    <i class="ti-plus me-1"></i> Add New
                </a>
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

        <div class="row">
            <!-- Total Methods -->
            <div class="col-xxxl-4 col-lg-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex align-items-start">
                            <!-- ICON -->
                            <div class="me-20">
                                <div class="h-50 w-50 l-h-50 rounded text-center bg-primary-light">
                                    <i class="ti-credit-card fs-24 text-primary"></i>
                                </div>
                            </div>
                            <!-- TEXT -->
                            <div>
                                <h2 class="my-0 fw-700">{{ $paymentDetails->count() }}</h2>
                                <p class="text-fade mb-0">Total Methods</p>
                                <p class="fs-12 mb-0 text-success">
                                    <span class="badge badge-pill badge-success-light me-5">
                                        <i class="fa fa-arrow-up"></i>
                                    </span>
                                    Updated
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Bank Accounts -->
            <div class="col-xxxl-4 col-lg-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex align-items-start">
                            <!-- ICON -->
                            <div class="me-20">
                                <div class="h-50 w-50 l-h-50 rounded text-center bg-info-light">
                                    <i class="ti-home fs-24 text-info"></i>
                                </div>
                            </div>
                            <!-- TEXT -->
                            <div>
                                <h2 class="my-0 fw-700">{{ $paymentDetails->where('pay_type', 'bank')->count() }}</h2>
                                <p class="text-fade mb-0">Bank Accounts</p>
                                <p class="fs-12 mb-0 text-success">
                                    <span class="badge badge-pill badge-success-light me-5">
                                        <i class="fa fa-arrow-up"></i>
                                    </span>
                                    Updated
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- Total UPI -->
            <div class="col-xxxl-4 col-lg-6 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex align-items-start">
                            <!-- ICON -->
                            <div class="me-20">
                                <div class="h-50 w-50 l-h-50 rounded text-center bg-success-light">
                                    <i class="ti-mobile fs-24 text-success"></i>
                                </div>
                            </div>
                            <!-- TEXT -->
                            <div>
                                <h2 class="my-0 fw-700">{{ $paymentDetails->where('pay_type', 'upi')->count() }}</h2>
                                <p class="text-fade mb-0">UPI IDs</p>
                                <p class="fs-12 mb-0 text-success">
                                    <span class="badge badge-pill badge-success-light me-5">
                                        <i class="fa fa-arrow-up"></i>
                                    </span>
                                    Updated
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>


        <!-- Payment Methods Grid -->
        <div class="row">
            @forelse($paymentDetails as $pd)
                <div class="col-xl-4 col-lg-6 col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <div class="d-flex align-items-center">
                                @if ($pd->pay_type === 'bank')
                                    <div class="me-15 bg-primary-light h-50 w-50 l-h-50 rounded text-center">
                                        <i class="ti-home fs-24 text-primary"></i>
                                    </div>
                                @else
                                    <div class="me-15 bg-success-light h-50 w-50 l-h-50 rounded text-center">
                                        <i class="ti-mobile fs-24 text-success"></i>
                                    </div>
                                @endif
                                <div>
                                    <h5 class="box-title mb-0">
                                        {{ $pd->pay_type === 'bank' ? $pd->pay_bank_name : 'UPI Payment' }}
                                    </h5>
                                    <span class="badge {{ $pd->pay_type === 'bank' ? 'badge-primary' : 'badge-success' }}">
                                        {{ $pd->pay_type === 'bank' ? 'Bank Account' : 'UPI' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="mb-15">
                                <small class="text-muted d-block">Account Holder</small>
                                <strong class="fs-16">{{ $pd->account_holder_name }}</strong>
                            </div>

                            <div class="bg-light rounded p-15 mb-15">
                                @if ($pd->pay_type === 'bank')
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Account Number</small>
                                            <strong>{{ $pd->masked_account_number }}</strong>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">IFSC Code</small>
                                            <strong>{{ $pd->pay_bank_ifsc }}</strong>
                                        </div>
                                    </div>
                                @else
                                    <div>
                                        <small class="text-muted d-block">UPI ID</small>
                                        <strong>{{ $pd->masked_upi_id }}</strong>
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-10 border-top">
                                <small class="text-muted">
                                    <i class="ti-calendar me-1"></i>
                                    Added {{ $pd->created_at->diffForHumans() }}
                                </small>
                                <form action="{{ route('admin.wallet.payment-details.delete', $pd->id) }}" method="POST"
                                    class="delete-form d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                        data-pd-id="{{ $pd->id }}">
                                        <i class="ti-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="box">
                        <div class="box-body text-center py-50">
                            <div class="mb-20">
                                <i class="ti-credit-card fs-50 text-muted"></i>
                            </div>
                            <h4>No Payment Methods Yet</h4>
                            <p class="text-muted mb-20">You haven't added any payment methods to your wallet.</p>
                            <a href="{{ route('admin.wallet.payment-details.create') }}" class="btn btn-primary">
                                <i class="ti-plus me-1"></i> Add Payment Method
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Information Box -->
        @if ($paymentDetails->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="box bg-light">
                        <div class="box-body">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                    <i class="ti-info-alt fs-24 text-primary me-15"></i>
                                    <div>
                                        <h6 class="mb-0">About Payment Methods</h6>
                                        <small class="text-muted">
                                            Payment methods are used for withdrawals from your wallet.
                                            You can add multiple bank accounts or UPI IDs.
                                        </small>
                                    </div>
                                </div>
                                <a href="{{ route('admin.wallet.payment-details.create') }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ti-plus me-1"></i> Add More
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection

@section('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Delete confirmation
            $('.delete-btn').on('click', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');

                Swal.fire({
                    title: 'Delete Payment Method?',
                    text: "This action cannot be undone. You won't be able to withdraw to this method.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>

    <style>
        .bg-white-translucent {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .h-50 {
            height: 50px !important;
        }

        .w-50 {
            width: 50px !important;
        }

        .l-h-50 {
            line-height: 50px !important;
        }

        .fs-24 {
            font-size: 24px;
        }

        .fs-16 {
            font-size: 16px;
        }

        .fs-50 {
            font-size: 50px;
        }

        .py-50 {
            padding-top: 50px;
            padding-bottom: 50px;
        }

        .mb-15 {
            margin-bottom: 15px;
        }

        .me-15 {
            margin-right: 15px;
        }

        .p-15 {
            padding: 15px;
        }

        .pt-10 {
            padding-top: 10px;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .box-title {
            font-size: 16px;
        }
    </style>
@endsection
