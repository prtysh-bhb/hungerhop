{{-- Extend the main layout --}}
@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <h4 class="page-title">Order #{{ $order->id }} Details</h4>
                <div class="d-inline-block align-items-center">
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="mdi mdi-home-outline"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('restaurant.orders') }}">Orders</a></li>
                            <li class="breadcrumb-item active">Order Details</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Left column (Customer info, Delivery Person, Customer Favourite) -->
            <div class="col-xxxl-4 col-12">
                <div class="box">
                    <div class="box-body">
                        <div class="d-flex align-items-center">
                            <img class="me-10 rounded-circle avatar avatar-xl b-2 border-primary"
                                src="{{ asset('images/avatar/1.jpg') }}" alt="">
                            <div>
                                <h4 class="mb-0">
                                    {{ optional($order->customer->user)->first_name ? optional($order->customer->user)->first_name . ' ' . optional($order->customer->user)->last_name : 'N/A' }}
                                </h4>
                                <span class="fs-14 text-info">Customer</span>
                            </div>
                        </div>
                    </div>

                    <div class="box-body border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-phone me-10 fs-24"></i>
                            <h4 class="mb-0">
                                {{ optional($order->customer->user)->phone ?? ($order->customer->phone ?? 'No Phone') }}
                            </h4>
                        </div>
                    </div>

                    <div class="box-body border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-map-marker me-10 fs-24"></i>
                            <h4 class="mb-0 text-black">
                                @if ($order->deliveryAddress)
                                    {{ $order->deliveryAddress->address_line1 }}@if ($order->deliveryAddress->address_line2)
                                        , {{ $order->deliveryAddress->address_line2 }}
                                    @endif,
                                    {{ $order->deliveryAddress->city }}, {{ $order->deliveryAddress->state }},
                                    {{ $order->deliveryAddress->postal_code }}
                                @else
                                    No Address
                                @endif
                            </h4>
                        </div>
                    </div>

                    <div class="box-body">
                        <h4 class="mb-10">Order Notes</h4>
                        @php
                            $instructions = $order->items->pluck('special_instructions')->filter()->all();
                        @endphp
                        @if (!empty($instructions))
                            <ul class="mb-0">
                                @foreach ($instructions as $note)
                                    <li>{{ $note }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p>No special instructions</p>
                        @endif
                    </div>
                </div>

                <div class="box mt-20">
                    <div class="box-header no-border">
                        <h4 class="box-title">Delivery Person</h4>
                    </div>
                    <div class="box-body">
                        @php
                            // Direct query instead of using controller method
                            $deliveryAssignment = \App\Models\DeliveryAssignment::where(
                                'order_id',
                                $order->id,
                            )->first();
                            $deliveryInfo = null;

                            if ($deliveryAssignment) {
                                $deliveryPartner = \App\Models\DeliveryPartner::find($deliveryAssignment->partner_id);

                                if ($deliveryPartner) {
                                    // Use withoutGlobalScope to bypass TenantScope - delivery partners have NULL tenant_id
                                    $user = \App\Models\User::withoutGlobalScope(\App\Scopes\TenantScope::class)->find(
                                        $deliveryPartner->user_id,
                                    );

                                    if ($user) {
                                        $deliveryInfo = [
                                            'user' => $user,
                                            'partner' => $deliveryPartner,
                                            'assignment' => $deliveryAssignment,
                                        ];
                                    }
                                }
                            }
                        @endphp

                        @if ($deliveryInfo && $deliveryInfo['user'])
                            <div class="text-center mb-20">
                                <img src="{{ asset('images/avatar/3.jpg') }}"
                                    class="mb-10 avatar avatar-xxl b-2 border-primary" alt="">
                                <div>
                                    <h4 class="mb-5 font-weight-500">{{ $deliveryInfo['user']->first_name }}
                                        {{ $deliveryInfo['user']->last_name }}</h4>
                                </div>
                                <div class="user-social-acount mt-10">
                                    <a href="tel:{{ $deliveryInfo['user']->phone }}"
                                        class="btn btn-circle btn-primary-light"><i class="fa fa-phone"></i></a>
                                    @if (
                                        $deliveryInfo['partner'] &&
                                            $deliveryInfo['partner']->current_latitude &&
                                            $deliveryInfo['partner']->current_longitude)
                                        <a href="https://maps.google.com/?q={{ $deliveryInfo['partner']->current_latitude }},{{ $deliveryInfo['partner']->current_longitude }}"
                                            target="_blank" class="btn btn-circle btn-primary-light"><i
                                                class="fa fa-map-marker"></i></a>
                                    @else
                                        <a href="javascript:void(0);" class="btn btn-circle btn-primary-light disabled"><i
                                                class="fa fa-map-marker"></i></a>
                                    @endif
                                </div>
                            </div>

                            <!-- Delivery Partner Details -->
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <td class="fw-600">Phone:</td>
                                        <td>{{ $deliveryInfo['user']->phone ?? 'N/A' }}</td>
                                    </tr>
                                    @if ($deliveryInfo['partner'])
                                        <tr>
                                            <td class="fw-600">Vehicle:</td>
                                            <td>{{ ucfirst($deliveryInfo['partner']->vehicle_type ?? 'N/A') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-600">Vehicle No:</td>
                                            <td>{{ $deliveryInfo['partner']->vehicle_number ?? 'N/A' }}</td>
                                        </tr>
                                    @endif
                                    @if ($deliveryInfo['assignment'])
                                        <tr>
                                            <td class="fw-600">Assignment Status:</td>
                                            <td>
                                                @php
                                                    $statusColors = [
                                                        'assigned' => 'warning',
                                                        'accepted' => 'info',
                                                        'picked_up' => 'primary',
                                                        'delivered' => 'success',
                                                        'rejected' => 'danger',
                                                        'cancelled' => 'danger',
                                                    ];
                                                    $statusColor =
                                                        $statusColors[$deliveryInfo['assignment']->status] ??
                                                        'secondary';
                                                @endphp
                                                <span class="badge badge-{{ $statusColor }}">
                                                    {{ ucfirst(str_replace('_', ' ', $deliveryInfo['assignment']->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-600">Assigned At:</td>
                                            <td>{{ $deliveryInfo['assignment']->assigned_at ? $deliveryInfo['assignment']->assigned_at->format('M d, Y H:i') : 'N/A' }}
                                            </td>
                                        </tr>
                                        @if ($deliveryInfo['assignment']->accepted_at)
                                            <tr>
                                                <td class="fw-600">Accepted At:</td>
                                                <td>{{ $deliveryInfo['assignment']->accepted_at->format('M d, Y H:i') }}
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($deliveryInfo['assignment']->picked_up_at)
                                            <tr>
                                                <td class="fw-600">Picked Up At:</td>
                                                <td>{{ $deliveryInfo['assignment']->picked_up_at->format('M d, Y H:i') }}
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($deliveryInfo['assignment']->delivered_at)
                                            <tr>
                                                <td class="fw-600">Delivered At:</td>
                                                <td>{{ $deliveryInfo['assignment']->delivered_at->format('M d, Y H:i') }}
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($deliveryInfo['assignment']->estimated_distance_km)
                                            <tr>
                                                <td class="fw-600">Distance:</td>
                                                <td>{{ number_format((float) $deliveryInfo['assignment']->estimated_distance_km, 2) }}
                                                    km</td>
                                            </tr>
                                        @endif
                                    @endif
                                </tbody>
                            </table>
                        @else
                            <div class="text-center">
                                <p class="text-muted">No delivery partner assigned yet.</p>
                                @if (in_array($order->status, ['placed', 'accepted', 'preparing']))
                                    <small class="text-info">Delivery partner will be assigned when order is ready for
                                        pickup.</small>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="box mt-20">
                    <div class="box-header no-border">
                        <h4 class="box-title">Customer Favourite</h4>
                    </div>
                    <div class="box-body text-center">
                        <div class="bar mx-auto"
                            data-peity='{ "fill": ["#2196f3", "#3da643", "#FDAC42"], "height": 150, "width": 320, "padding":0.2 }'>
                            52,38,24</div>
                        <div class="d-flex justify-content-center mt-30">
                            <div class="d-flex text-left">
                                <i class="fa fa-circle text-info mr-5"></i>
                                <h4 class="font-weight-600">25% <br><small class="text-fade">Pizza</small></h4>
                            </div>
                            <div class="d-flex text-left mx-50">
                                <i class="fa fa-circle text-danger mr-5"></i>
                                <h4 class="font-weight-600">15% <br><small class="text-fade">Juice </small></h4>
                            </div>
                            <div class="d-flex text-left">
                                <i class="fa fa-circle text-primary mr-5"></i>
                                <h4 class="font-weight-600">21% <br><small class="text-fade">Dessert </small></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right column (Order progress + items) -->
            <div class="col-xxxl-8 col-12">
                <!-- Order status with badge and progress bar -->
                <div class="box">
                    <div class="box-body">
                        @php
                            $steps = [
                                'placed' => 'Order Placed',
                                'accepted' => 'Order Accepted',
                                'preparing' => 'Preparing',
                                'ready_for_pickup' => 'Ready for Pickup',
                                'assigned_to_delivery' => 'Assigned to Delivery',
                                'picked_up' => 'Picked Up',
                                'out_for_delivery' => 'Out for Delivery',
                                'delivered' => 'Delivered',
                            ];

                            $statusKeys = array_keys($steps);
                            $currentIndex = array_search($order->status, $statusKeys);
                        @endphp

                        <ol class="c-progress-steps mb-0">
                            @foreach ($steps as $key => $label)
                                @php $index = array_search($key, $statusKeys); @endphp
                                <li
                                    class="c-progress-steps__step 
                            {{ $index < $currentIndex ? 'done' : '' }}
                            {{ $index === $currentIndex ? 'current' : '' }}">
                                    <span>{{ $label }}</span>
                                </li>
                            @endforeach

                            {{-- Cancelled/Rejected --}}
                            @if (in_array($order->status, ['cancelled', 'rejected']))
                                <li class="c-progress-steps__step done current text-danger">
                                    <span>{{ ucfirst($order->status) }}</span>
                                </li>
                            @endif
                        </ol>
                    </div>

                </div>
                <!-- Order Status Change History -->
                <div class="box mb-4">
                    <div class="box-header">
                        <h4 class="box-title">Order Status History</h4>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table product-overview">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Status</th>
                                        <th>Status Changed At (IST)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $tz = new DateTimeZone('Asia/Kolkata');
                                        // Show all status changes for this order
                                        $statuses = \App\Models\OrderStatus::where('order_id', $order->id)
                                            ->orderBy('created_at')
                                            ->get();
                                    @endphp
                                    @foreach ($statuses as $statusChange)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ ucfirst($statusChange->status) }}</td>
                                            <td>
                                                @php
                                                    $dt = $statusChange->created_at
                                                        ? $statusChange->created_at->setTimezone($tz)
                                                        : null;
                                                @endphp
                                                {{ $dt ? $dt->format('h:i:s A  d-m-Y') : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if ($statuses->isEmpty())
                                        <tr>
                                            <td colspan="3" class="text-center">No status history available for this
                                                order.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>



                {{-- status change  --}}
                {{-- @if ((auth()->user()->tenant_admin ?? false) || (auth()->user()->location_admin ?? false)) --}}
                <div class="mb-3">
                    <form action="{{ route('restaurant.orders.updateStatus', $order->id) }}" method="POST"
                        class="d-flex align-items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <label for="order-status" class="me-2 fw-bold">Change Status:</label>
                        <select name="status" id="order-status" class="form-select w-auto me-2">
                            @foreach ($steps as $key => $label)
                                <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }}>
                                    {{ $label }}</option>
                            @endforeach
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled
                            </option>
                            <option value="rejected" {{ $order->status === 'rejected' ? 'selected' : '' }}>Rejected
                            </option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    </form>
                </div>
                {{-- @endif --}}


                <!-- Order items -->
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive-xl">
                            <table class="table product-overview">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Item Info</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                        <th style="text-align:center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->items as $item)
                                        @php
                                            // prefer menu_items.name if present, else fallback to order_items.item_name
                                            $menuName = optional($item->menuItem)->item_name;
                                            $imageUrl =
                                                optional($item->menuItem)->image_url ??
                                                ($item->product_image ?? asset('images/avatar/1.jpg'));
                                            $unitPrice = $item->unit_price ?? ($item->price ?? 0);
                                            $lineTotal = $order->total_amount ?? $unitPrice * ($item->quantity ?? 1);
                                        @endphp
                                        <tr>
                                            <td>
                                                <img src="{{ $imageUrl }}" alt="{{ $menuName ?? 'Item' }}"
                                                    width="80"
                                                    onerror="this.src='{{ asset('images/avatar/1.jpg') }}'">
                                            </td>
                                            <td>

                                                <h4>{{ $menuName ?? ($item->item_name ?? 'Item') }}</h4>
                                                @php $categoryName = optional(optional($item->menuItem)->category)->name; @endphp
                                                <small class="text-muted d-block">Category:
                                                    {{ $categoryName ?? '-' }}</small>
                                            </td>
                                            <td>${{ number_format($unitPrice, 2) }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>
                                                @php
                                                    $statusLabels = [
                                                        'placed' => 'Placed',
                                                        'accepted' => 'Accepted',
                                                        'preparing' => 'Preparing',
                                                        'ready_for_pickup' => 'Ready for Pickup',
                                                        'assigned_to_delivery' => 'Assigned to Delivery',
                                                        'picked_up' => 'Picked Up',
                                                        'out_for_delivery' => 'Out for Delivery',
                                                        'delivered' => 'Delivered',
                                                        'cancelled' => 'Cancelled',
                                                        'rejected' => 'Rejected',
                                                    ];
                                                    $statusClasses = [
                                                        'placed' => 'badge badge-secondary',
                                                        'accepted' => 'badge badge-info',
                                                        'preparing' => 'badge badge-warning',
                                                        'ready_for_pickup' => 'badge badge-primary',
                                                        'assigned_to_delivery' => 'badge badge-primary',
                                                        'picked_up' => 'badge badge-info',
                                                        'out_for_delivery' => 'badge badge-info',
                                                        'delivered' => 'badge badge-success',
                                                        'cancelled' => 'badge badge-danger',
                                                        'rejected' => 'badge badge-danger',
                                                    ];
                                                    $orderStatus = $order->status ?? 'placed';
                                                @endphp
                                                <span
                                                    class="{{ $statusClasses[$orderStatus] ?? 'badge badge-secondary' }}">{{ $statusLabels[$orderStatus] ?? 'Unknown' }}</span>
                                            </td>
                                            <td align="center" class="fw-900">${{ number_format($lineTotal, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No items found for this order.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="box">*

                    <div class="box-body">
                        <div id="chartdiv2" class="h-300"></div>
                    </div>
                </div>

                <!-- Total -->
                {{-- <div class="box">
                <div class="box-body text-end">
                   
                    <h3 class="fw-900">Grand Total: ${{ number_format($order->total_amount, 2) }}</h3>
                </div>
            </div> --}}

            </div>
        </div>
    </section>
@endsection
