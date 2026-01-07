@extends('layouts.admin')
@section('title', 'Coupons Management')
@section('content')
    <div class="content-header">
        <div class="d-flex align-items-center">

            <div class="me-auto">
                <h4 class="page-title">Coupons</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    <i class="mdi mdi-home-outline"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item active">Coupons</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div>
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                    <i class="mdi mdi-plus"></i> Add Coupon
                </a>
            </div>
        </div>
    </div>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Debug Route Check --}}
    @if (!Route::has('admin.coupons.toggle'))
        <div class="alert alert-warning">
            <strong>Warning:</strong> Route 'admin.coupons.toggle' is not defined!
            <br>Add this to your routes: <code>Route::post('/admin/coupons/{coupon}/toggle', [CouponController::class,
                'toggle'])->name('admin.coupons.toggle');</code>
        </div>
    @endif
    {{-- Coupons Table --}}
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th width="5%">ID</th>
                    <th>Code</th>
                    <th>Title</th>
                    <th>Discount</th>
                    <th>Min Order</th>
                    <th>Status</th>
                    <th width="18%">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($coupons as $coupon)
                    <tr>
                        <td>{{ $coupon->id }}</td>

                        <td class="fw-bold">
                            {{ $coupon->code }}
                        </td>

                        <td>{{ $coupon->title }}</td>

                        {{-- Discount --}}
                        <td>
                            @if ($coupon->discount_type === 'percentage')
                                {{ $coupon->discount_value }}%
                                @if ($coupon->max_discount)
                                    <br>
                                    <small class="text-muted">
                                        Max ₹{{ number_format($coupon->max_discount, 2) }}
                                    </small>
                                @endif
                            @else
                                ₹{{ number_format($coupon->discount_value, 2) }}
                            @endif
                        </td>

                        {{-- Min Order --}}
                        <td>
                            ₹{{ number_format($coupon->min_order_value, 2) }}
                        </td>

                        {{-- ON / OFF Switch with Status Badge --}}
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <form method="POST" action="{{ route('admin.coupons.toggle', $coupon->id) }}"
                                    class="m-0 toggle-coupon-form" data-coupon-id="{{ $coupon->id }}">
                                    @csrf
                                    <label class="switch mb-0" style="vertical-align: middle;">
                                        <input type="checkbox" class="toggle-coupon-switch"
                                            data-coupon-id="{{ $coupon->id }}"
                                            {{ $coupon->is_active ? 'checked' : '' }}>
                                        <span class="switch-indicator"></span>
                                    </label>
                                </form>
                                <span class="badge {{ $coupon->is_active ? 'bg-success' : 'bg-secondary' }} status-badge"
                                    id="status-badge-{{ $coupon->id }}">
                                    {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                data-bs-target="#deleteCouponModal"
                                data-action="{{ route('admin.coupons.delete', $coupon->id) }}">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No coupons found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    </div>

    {{-- DELETE CONFIRMATION MODAL --}}
    <div class="modal fade" id="deleteCouponModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title text-danger">Delete Coupon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-0">
                        Are you sure you want to delete this coupon?
                        <br>
                        <small class="text-muted">
                            This action cannot be undone.
                        </small>
                    </p>
                </div>

                <div class="modal-footer d-flex">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <form method="POST" id="deleteCouponForm">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            Yes, Delete
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete modal logic
            const deleteModal = document.getElementById('deleteCouponModal');
            const deleteForm = document.getElementById('deleteCouponForm');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const action = button.getAttribute('data-action');
                    deleteForm.setAttribute('action', action);
                });
            }

            // AJAX toggle logic
            document.querySelectorAll('.toggle-coupon-switch').forEach(function(switchEl) {
                switchEl.addEventListener('change', function(e) {
                    const couponId = this.getAttribute('data-coupon-id');
                    const form = document.querySelector('.toggle-coupon-form[data-coupon-id="' +
                        couponId + '"]');
                    const url = form.getAttribute('action');
                    const token = form.querySelector('input[name="_token"]').value;
                    // Disable switch while processing
                    this.disabled = true;
                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Update badge
                            const badge = document.getElementById('status-badge-' + couponId);
                            if (data.success) {
                                if (data.is_active) {
                                    badge.classList.remove('bg-secondary');
                                    badge.classList.add('bg-success');
                                    badge.textContent = 'Active';
                                } else {
                                    badge.classList.remove('bg-success');
                                    badge.classList.add('bg-secondary');
                                    badge.textContent = 'Inactive';
                                }
                            } else {
                                // Revert switch if failed
                                this.checked = !this.checked;
                                alert('Failed to update status.');
                            }
                        })
                        .catch(() => {
                            this.checked = !this.checked;
                            alert('Failed to update status.');
                        })
                        .finally(() => {
                            this.disabled = false;
                        });
                });
            });
        });
    </script>
@endsection
