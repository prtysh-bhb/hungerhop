<?php

namespace App\Services\Api\v1;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\CustomerAddress;
use App\Models\CustomerProfile;
use App\Models\DeliveryPartner;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Create a new order
     */
    public function createOrder(array $data, $user): array
    {
        // Get customer profile for authenticated user
        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (! $customerProfile) {
            return [
                'success' => false,
                'message' => 'Customer profile not found for user.',
                'status_code' => 404,
            ];
        }

        $orderItems = $data['order_items'];
        unset($data['order_items']);

        // Get restaurant_id & tenant_id from the first item
        $firstItem = $orderItems[0];
        $menuItem = MenuItem::find($firstItem['item_id']);
        if (! $menuItem) {
            return [
                'success' => false,
                'message' => 'Menu item not found.',
                'status_code' => 422,
            ];
        }

        $restaurantId = $menuItem->restaurant_id;
        $tenantId = $menuItem->tenant_id;

        // Ensure all items are from the same restaurant
        foreach ($orderItems as $item) {
            $itemMenu = MenuItem::find($item['item_id']);
            if (! $itemMenu || $itemMenu->restaurant_id != $restaurantId) {
                return [
                    'success' => false,
                    'message' => 'All items must be from the same restaurant.',
                    'status_code' => 422,
                ];
            }
        }

        // Check if restaurant can accept new orders
        $restaurant = Restaurant::find($restaurantId);
        if (! $restaurant) {
            return [
                'success' => false,
                'message' => 'Restaurant not found.',
                'status_code' => 422,
            ];
        }

        if (! $restaurant->canAcceptNewOrders()) {
            $message = 'Order cannot be accepted.';
            if ($restaurant->is_paused) {
                $message = 'Order cannot be accepted due to restaurant is temporarily closed.';
            } elseif (! $restaurant->is_open) {
                $message = 'Order cannot be accepted due to restaurant is closed.';
            } elseif (! $restaurant->accepts_orders) {
                $message = 'Order cannot be accepted due to restaurant is not accepting orders.';
            } elseif ($restaurant->status !== 'approved') {
                $message = 'Order cannot be accepted due to restaurant is not available.';
            }

            return [
                'success' => false,
                'message' => $message,
                'status_code' => 422,
            ];
        }

        // Validate that delivery_address_id belongs to this customer
        $deliveryAddressId = $data['delivery_address_id'];
        $address = CustomerAddress::where('id', $deliveryAddressId)
            ->where('customer_id', $customerProfile->id)
            ->first();

        if (! $address) {
            return [
                'success' => false,
                'message' => 'Invalid delivery_address_id: Address does not belong to the customer.',
                'status_code' => 422,
            ];
        }

        // Calculate subtotal from menu_items base_price
        $itemsBreakdown = $this->calculateItemsBreakdown($orderItems);
        $subtotal = $itemsBreakdown['subtotal'];

        // Calculate delivery fee based on distance
        $deliveryCalculation = $this->calculateDeliveryDetails($restaurant, $address);
        if (! $deliveryCalculation['success']) {
            return $deliveryCalculation;
        }

        $deliveryDistance = $deliveryCalculation['distance'];
        $deliveryFee = $deliveryCalculation['delivery_fee'];

        // Get tax percentage from restaurant and calculate tax amount
        $taxPercentage = (float) ($restaurant->tax_percentage ?? 0);
        $taxAmount = round(($subtotal * $taxPercentage) / 100, 2);

        // Get platform fee from .env
        $platformFee = (float) env('PLATFORM_FEE', 0);

        // Calculate discount (if any)
        $discountAmount = 0;

        // Calculate total amount
        $totalAmount = $subtotal + $taxAmount + $deliveryFee + $platformFee - $discountAmount;

        // Calculate restaurant amount
        $restaurantCommissionPercentage = (float) ($restaurant->restaurant_commission_percentage ?? 0);
        $platformCommission = round(($subtotal * $restaurantCommissionPercentage) / 100, 2);
        $restaurantAmount = $subtotal - $platformCommission;

        // Generate unique order number
        do {
            $orderNumber = 'ORD'.strtoupper(uniqid());
        } while (Order::where('order_number', $orderNumber)->exists());

        // Prepare order data
        $orderData = $data;
        $orderData['order_number'] = $orderNumber;
        $orderData['customer_id'] = $customerProfile->id;
        $orderData['restaurant_id'] = $restaurantId;
        $orderData['tenant_id'] = $tenantId;
        $orderData['status'] = 'draft';
        $orderData['payment_status'] = 'pending';
        $orderData['subtotal'] = $subtotal;
        $orderData['delivery_fee'] = $deliveryFee;
        $orderData['tax_amount'] = $taxAmount;
        $orderData['discount_amount'] = $discountAmount;
        $orderData['restaurant_amount'] = $restaurantAmount;
        $orderData['delivery_amount'] = $deliveryFee;
        $orderData['platform_fee'] = $platformFee;
        $orderData['total_amount'] = $totalAmount;

        DB::beginTransaction();
        try {
            $order = Order::create($orderData);

            // Create initial order status record
            OrderStatus::create([
                'order_id' => $order->id,
                'status' => 'draft',
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                $menuItem = MenuItem::find($item['item_id']);
                $item['order_id'] = $order->id;
                $item['tenant_id'] = $order->tenant_id;
                $item['item_name'] = $menuItem ? $menuItem->item_name : 'Unknown Item';
                $item['unit_price'] = $menuItem ? $menuItem->base_price : 0;
                $item['total_price'] = $item['unit_price'] * ($item['quantity'] ?? 1);
                OrderItem::create($item);
            }

            // Create Payment Record
            $payment = $this->createPaymentRecord($order);

            Log::info("Payment record created for order {$order->id}: Payment ID {$payment->id}");

            // Get delivery info preview
            $deliveryPreview = $this->getDeliveryPreview($order);

            DB::commit();

            // Build response with billing details
            return [
                'success' => true,
                'message' => 'Order created successfully. Proceed to checkout.',
                'data' => $this->buildStandardizedOrderResponse($order, $payment),
                'status_code' => 201,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to create order.',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    /**
     * Edit an existing order (only allowed before order is prepared)
     */
    public function editOrder(int $orderId, array $data, $user): array
    {
        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (! $customerProfile) {
            return [
                'success' => false,
                'message' => 'Customer profile not found for user.',
                'status_code' => 404,
            ];
        }

        $order = Order::where('id', $orderId)
            ->where('customer_id', $customerProfile->id)
            ->first();

        if (! $order) {
            return [
                'success' => false,
                'message' => 'Order not found or does not belong to you.',
                'status_code' => 404,
            ];
        }

        // Only allow editing if order is in draft status
        $editableStatuses = ['draft'];
        if (! in_array($order->status, $editableStatuses)) {
            return [
                'success' => false,
                'message' => 'Order cannot be edited after placement.',
                'current_status' => $order->status,
                'status_code' => 403,
            ];
        }

        $restaurant = Restaurant::find($order->restaurant_id);
        if (! $restaurant) {
            return [
                'success' => false,
                'message' => 'Restaurant not found.',
                'status_code' => 422,
            ];
        }

        DB::beginTransaction();
        try {
            $updateData = [];

            // Extract coupon_code from order_items if present
            $couponCode = null;
            if (isset($data['order_items']) && is_array($data['order_items']) && count($data['order_items']) > 0) {
                if (isset($data['order_items'][0]['coupon_code'])) {
                    $couponCode = $data['order_items'][0]['coupon_code'];
                    unset($data['order_items'][0]['coupon_code']);
                }
            }
            // Also check root level coupon_code
            if (isset($data['coupon_code'])) {
                $couponCode = $data['coupon_code'];
                unset($data['coupon_code']);
            }
            
            // Coupon logic
            $coupon = null;
            $discountAmount = $order->discount_amount;
            if (isset($couponCode) && ! empty($couponCode)) {
                $coupon = Coupon::where('code', $couponCode)
                ->where('is_active', true)
                ->where('valid_from', '<=', now())
                ->where('valid_to', '>=', now())
                ->first();
                if (! $coupon) {
                    DB::rollBack();

                    return [
                        'success' => false,
                        'message' => 'Invalid or expired coupon code.',
                        'status_code' => 422,
                    ];
                }
                // Check min order value
                $currentSubtotal = isset($updateData['subtotal']) ? $updateData['subtotal'] : $order->subtotal;
                if ($coupon->min_order_value && $currentSubtotal < $coupon->min_order_value) {
                    DB::rollBack();

                    return [
                        'success' => false,
                        'message' => 'Order does not meet the minimum value for this coupon.',
                        'status_code' => 422,
                    ];
                }
                // Calculate discount
                if ($coupon->discount_type === 'flat') {
                    $discountAmount = (float) $coupon->discount_value;
                } elseif ($coupon->discount_type === 'percentage') {
                    $discountAmount = ($currentSubtotal * $coupon->discount_value) / 100;
                    if ($coupon->max_discount) {
                        $discountAmount = min($discountAmount, $coupon->max_discount);
                    }
                }
                CouponUsage::where('order_id', $order->id)->delete();
                CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'used_at' => now(),
                ]);

                $updateData['discount_amount'] = $discountAmount;
            }

            // Update special instructions if provided
            if (isset($data['special_instructions'])) {
                $updateData['special_instructions'] = $data['special_instructions'];
            }

            // Update delivery address if provided
            if (isset($data['delivery_address_id'])) {
                $address = CustomerAddress::where('id', $data['delivery_address_id'])
                    ->where('customer_id', $customerProfile->id)
                    ->first();

                if (! $address) {
                    DB::rollBack();

                    return [
                        'success' => false,
                        'message' => 'Invalid delivery_address_id: Address does not belong to the customer.',
                        'status_code' => 422,
                    ];
                }

                // Recalculate delivery fee with new address
                $deliveryCalculation = $this->calculateDeliveryDetails($restaurant, $address);
                if (! $deliveryCalculation['success']) {
                    DB::rollBack();

                    return $deliveryCalculation;
                }

                $updateData['delivery_address_id'] = $data['delivery_address_id'];
                $updateData['delivery_fee'] = $deliveryCalculation['delivery_fee'];
                $updateData['delivery_amount'] = $deliveryCalculation['delivery_fee'];
            }

            // Update order items if provided
            if (isset($data['order_items']) && is_array($data['order_items'])) {
                // Ensure all items are from the same restaurant as the order
                foreach ($data['order_items'] as $item) {
                    $menuItem = MenuItem::find($item['item_id']);
                    if (! $menuItem) {
                        DB::rollBack();

                        return [
                            'success' => false,
                            'message' => "Menu item with ID {$item['item_id']} not found.",
                            'status_code' => 422,
                        ];
                    }
                    if ($menuItem->restaurant_id != $order->restaurant_id) {
                        DB::rollBack();

                        return [
                            'success' => false,
                            'message' => 'All items must be from the same restaurant.',
                            'status_code' => 422,
                        ];
                    }
                }

                // Delete existing order items
                OrderItem::where('order_id', $order->id)->delete();

                // Calculate new items
                $itemsBreakdown = $this->calculateItemsBreakdown($data['order_items']);
                $subtotal = $itemsBreakdown['subtotal'];

                // Create new order items
                foreach ($data['order_items'] as $item) {
                    $menuItem = MenuItem::find($item['item_id']);
                    OrderItem::create([
                        'order_id' => $order->id,
                        'tenant_id' => $order->tenant_id,
                        'item_id' => $item['item_id'],
                        'item_name' => $menuItem->item_name,
                        'quantity' => $item['quantity'],
                        'unit_price' => $menuItem->base_price,
                        'total_price' => $menuItem->base_price * $item['quantity'],
                        'special_instructions' => $item['special_instructions'] ?? null,
                    ]);
                }

                $updateData['subtotal'] = $subtotal;

                // Recalculate tax
                $taxPercentage = (float) ($restaurant->tax_percentage ?? 0);
                $taxAmount = round(($subtotal * $taxPercentage) / 100, 2);
                $updateData['tax_amount'] = $taxAmount;

                // Recalculate restaurant amount
                $restaurantCommissionPercentage = (float) ($restaurant->restaurant_commission_percentage ?? 0);
                $platformCommission = round(($subtotal * $restaurantCommissionPercentage) / 100, 2);
                $updateData['restaurant_amount'] = $subtotal - $platformCommission;
            }

            // Recalculate total if any amount changed
            if (! empty($updateData)) {
                $newSubtotal = $updateData['subtotal'] ?? $order->subtotal;
                $newTaxAmount = $updateData['tax_amount'] ?? $order->tax_amount;
                $newDeliveryFee = $updateData['delivery_fee'] ?? $order->delivery_fee;
                $platformFee = $order->platform_fee;
                $discountAmount = $updateData['discount_amount'] ?? $order->discount_amount;

                $updateData['total_amount'] = $newSubtotal + $newTaxAmount + $newDeliveryFee + $platformFee - $discountAmount;

                $order->update($updateData);

                // Update payment amount if exists
                Payment::where('order_id', $order->id)->update(['amount' => $updateData['total_amount']]);
            }

            // Create order status record for edit
            OrderStatus::create([
                'order_id' => $order->id,
                'status' => 'edited',
            ]);

            DB::commit();

            // Refresh order and load relationships
            $order->refresh();
            $order->load(['orderItems', 'restaurant', 'deliveryAddress', 'customer.user']);

            // Get the latest payment record
            $payment = Payment::where('order_id', $order->id)->latest()->first();

            return [
                'success' => true,
                'message' => 'Order updated successfully.',
                'data' => $this->buildStandardizedOrderResponse($order, $payment),
                'status_code' => 200,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order edit failed: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to update order.',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    public function applyCouponToOrder(int $orderId, string $couponCode, $user): array
    {
        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (! $customerProfile) {
            return [
                'success' => false,
                'message' => 'Customer profile not found.',
                'status_code' => 404,
            ];
        }

        $order = Order::where('id', $orderId)
            ->where('customer_id', $customerProfile->id)
            ->first();

        if (! $order) {
            return [
                'success' => false,
                'message' => 'Order not found or does not belong to you.',
                'status_code' => 404,
            ];
        }
        // Check if a coupon is already applied
        $coupon_code = Coupon::where('code', $couponCode)->first();
        $coupon_if_applied = CouponUsage::where('order_id', $order->id)
            ->where('coupon_id', $coupon_code->id)
            ->first();
        if ($coupon_if_applied) {
            return [
                'success' => false,
                'message' => 'A coupon has already been applied to this order.',
                'status_code' => 403,
            ];
        }
        // Coupon can be applied ONLY in draft
        if ($order->status !== 'draft') {
            return [
                'success' => false,
                'message' => 'Coupon can only be applied before order placement.',
                'current_status' => $order->status,
                'status_code' => 403,
            ];
        }

        // Normalize coupon code
        $couponCode = strtoupper(trim($couponCode));

        $coupon = Coupon::where('code', $couponCode)
            ->where('is_active', true)
            ->first();

        if (! $coupon) {
            return [
                'success' => false,
                'message' => 'Invalid or inactive coupon code.',
                'status_code' => 422,
            ];
        }

        // Validity window
        if ($coupon->valid_from && now()->lt($coupon->valid_from)) {
            return [
                'success' => false,
                'message' => 'Coupon is not yet valid.',
                'status_code' => 422,
            ];
        }

        if ($coupon->valid_to && now()->gt($coupon->valid_to)) {
            return [
                'success' => false,
                'message' => 'Coupon has expired.',
                'status_code' => 422,
            ];
        }

        // Scope check (restaurant coupons)
        if ($coupon->coupon_scope === 'restaurant') {
            if (! isset($coupon->restaurant_id) || $coupon->restaurant_id != $order->restaurant_id) {
                return [
                    'success' => false,
                    'message' => 'Coupon is not applicable to this restaurant.',
                    'status_code' => 422,
                ];
            }
        }

        $subtotal = (float) $order->subtotal;

        // Minimum order value
        if ($subtotal < (float) $coupon->min_order_value) {
            return [
                'success' => false,
                'message' => 'Order does not meet minimum value for this coupon.',
                'min_order_value' => $coupon->min_order_value,
                'status_code' => 422,
            ];
        }

        // Total usage limit
        if ($coupon->usage_limit !== null &&
            $coupon->usages()->count() >= $coupon->usage_limit) {
            return [
                'success' => false,
                'message' => 'Coupon usage limit reached.',
                'status_code' => 422,
            ];
        }

        // Per-user usage limit
        if ($coupon->usages()
            ->where('user_id', $user->id)
            ->count() >= $coupon->usage_per_user) {
            return [
                'success' => false,
                'message' => 'You have already used this coupon.',
                'status_code' => 422,
            ];
        }

        // Calculate discount
        if ($coupon->discount_type === 'flat') {
            $discountAmount = min($coupon->discount_value, $subtotal);
        } else {
            $discountAmount = ($subtotal * $coupon->discount_value) / 100;
            if ($coupon->max_discount !== null) {
                $discountAmount = min($discountAmount, $coupon->max_discount);
            }
        }

        $discountAmount = round($discountAmount, 2);

        DB::beginTransaction();
        try {
            // Delete any existing coupon for this order
            CouponUsage::where('order_id', $order->id)->delete();

            // Calculate new total
            $newTotal = $order->subtotal +
                $order->tax_amount +
                $order->delivery_fee +
                $order->platform_fee -
                $discountAmount;

            // Update order
            $order->update([
                'discount_amount' => $discountAmount,
                'total_amount' => $newTotal,
            ]);

            // Update payment amount
            Payment::where('order_id', $order->id)->update(['amount' => $newTotal]);

            // Record coupon usage
            CouponUsage::create([
                'coupon_id' => $coupon->id,
                'user_id' => $user->id,
                'order_id' => $order->id,
                'used_at' => now(),
            ]);
            DB::commit();

            // Refresh order to get updated data
            $order->refresh();
            $payment = Payment::where('order_id', $order->id)->latest()->first();

            return [
                'success' => true,
                'message' => 'Coupon applied successfully.',
                'data' => $this->buildStandardizedOrderResponse($order, $payment),
                'status_code' => 200,
            ];

        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Failed to apply coupon.',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    public function removeCouponFromOrder(int $orderId, $user, $couponCode)
    {
        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (! $customerProfile) {
            return [
                'success' => false,
                'message' => 'Customer profile not found.',
                'status_code' => 404,
            ];
        }

        $order = Order::where('id', $orderId)
            ->where('customer_id', $customerProfile->id)
            ->first();

        if (! $order) {
            return [
                'success' => false,
                'message' => 'Order not found or does not belong to you.',
                'status_code' => 404,
            ];
        }

        // Coupon can be removed ONLY in draft
        if ($order->status !== 'draft') {
            return [
                'success' => false,
                'message' => 'Coupon can only be removed before order placement.',
                'current_status' => $order->status,
                'status_code' => 403,
            ];
        }
        $couponCode = strtoupper(trim($couponCode));
        $coupon = Coupon::where('code', $couponCode)->first();
        
        if (! $coupon) {
            return [
                'success' => false,
                'message' => 'Coupon code not found.',
                'status_code' => 422,
            ];
        }
        $couponUsage = CouponUsage::where('order_id', $order->id)
            ->where('coupon_id', $coupon->id)
            ->first();

        if (! $couponUsage) {
            return [
                'success' => false,
                'message' => 'This coupon is not applied to this order.',
                'status_code' => 422,
            ];
        }

        DB::beginTransaction();
        try {
            // Calculate new total without discount
            $newTotal = $order->subtotal +
                $order->tax_amount +
                $order->delivery_fee +
                $order->platform_fee;

            // Remove coupon details from order
            $order->update([
                'discount_amount' => 0,
                'total_amount' => $newTotal,
            ]);

            // Update payment amount
            Payment::where('order_id', $order->id)->update(['amount' => $newTotal]);

            // Remove the specific coupon usage record
            CouponUsage::where('order_id', $order->id)
                ->where('coupon_id', $coupon->id)
                ->delete();

            DB::commit();

            // Refresh order to get updated data
            $order->refresh();
            $payment = Payment::where('order_id', $order->id)->latest()->first();

            return [
                'success' => true,
                'message' => 'Coupon removed successfully.',
                'data' => $this->buildStandardizedOrderResponse($order, $payment),
                'status_code' => 200,
            ];

        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Failed to remove coupon.',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    /**
     * Get checkout details for an order
     */
    public function getCheckout(int $orderId, $user): array
    {
        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (! $customerProfile) {
            return [
                'success' => false,
                'message' => 'Customer profile not found for user.',
                'status_code' => 404,
            ];
        }

        $order = Order::where('id', $orderId)
            ->where('customer_id', $customerProfile->id)
            ->with(['orderItems', 'restaurant', 'deliveryAddress', 'customer.user'])
            ->first();

        if (! $order) {
            return [
                'success' => false,
                'message' => 'Order not found or does not belong to you.',
                'status_code' => 404,
            ];
        }

        // If order is in draft, mark as placed
        if ($order->status === 'draft') {
            $order->status = 'placed';
            $order->save();
            // Add order status history
            OrderStatus::create([
                'order_id' => $order->id,
                'status' => 'placed',
            ]);
        }

        // Get payment details
        $payment = Payment::where('order_id', $order->id)->first();

        // Build checkout response with billing details
        return [
            'success' => true,
            'message' => 'Checkout details retrieved successfully.',
            'data' => $this->buildStandardizedOrderResponse($order, $payment),
            'status_code' => 200,
        ];
    }

    public function cancelOrder(int $orderId, $user, ?string $cancellationReason = null): array
    {
        try {
            return DB::transaction(function () use ($orderId, $user, $cancellationReason) {

                // Get customer profile
                $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
                if (! $customerProfile) {
                    return [
                        'success' => false,
                        'message' => 'Customer profile not found for user.',
                        'status_code' => 404,
                    ];
                }

                // Find order safely
                $order = Order::where('id', $orderId)
                    ->where('customer_id', $customerProfile->id)
                    ->lockForUpdate()
                    ->first();

                if (! $order) {
                    return [
                        'success' => false,
                        'message' => 'Order not found.',
                        'status_code' => 404,
                    ];
                }

                // Status restriction
                $nonCancellableStatuses = ['cancelled', 'completed', 'delivered', 'arrived'];
                if (in_array($order->status, $nonCancellableStatuses)) {
                    return [
                        'success' => false,
                        'message' => 'Order cannot be cancelled in its current status.',
                        'current_status' => $order->status,
                        'status_code' => 403,
                    ];
                }

                // $limitSeconds = (int) env('ORDER_CANCELLATION_TIME_LIMIT_SECONDS', 300);
                $limitSeconds = 10;
                // $createdAt = Carbon::parse($order->created_at);
                // $now = Carbon::now();
                // $secondsPassed = $now->diffInSeconds($createdAt);
                $secondsPassed = Carbon::now('UTC')->diffInSeconds($order->created_at);

                if ($secondsPassed > $limitSeconds) {
                    return [
                        'success' => false,
                        'message' => "Order can only be cancelled within {$limitSeconds} seconds of placing it.",
                        'time_passed_seconds' => $secondsPassed,
                        'time_limit_seconds' => $limitSeconds,
                        'status_code' => 403,
                    ];
                }
                $order->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => $cancellationReason,
                    'cancelled_at' => now(),
                ]);

                // Status history
                OrderStatus::create([
                    'order_id' => $order->id,
                    'status' => 'cancelled',
                    'remarks' => $cancellationReason,
                ]);

                return [
                    'success' => true,
                    'message' => 'Order cancelled successfully.',
                    'order_id' => $order->id,
                    'status_code' => 200,
                ];
            });
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Failed to cancel order.',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    /**
     * List all orders for a customer
     */
    public function listOrders($user): array
    {
        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (! $customerProfile) {
            return [
                'success' => false,
                'message' => 'Customer profile not found for user.',
                'status_code' => 404,
            ];
        }

        $orders = Order::where('customer_id', $customerProfile->id)
            ->where('status', '!=', 'draft')
            ->with(['restaurant', 'orderItems', 'deliveryAssignment.partner.user', 'deliveryAddress'])
            ->orderBy('created_at', 'desc')
            ->get();

        $orderData = $orders->map(function ($order) {
            return $this->buildOrderListItem($order);
        });

        return [
            'success' => true,
            'order_count' => $orders->count(),
            'total_orders_amount' => $orders->sum('total_amount'),
            'orders' => $orderData,
            'status_code' => 200,
        ];
    }

    /**
     * Get order details
     */
    public function getOrderDetails(int $orderId): array
    {
        $order = Order::with(['customer.user', 'restaurant', 'deliveryAddress', 'orderItems'])
            ->find($orderId);

        if (! $order) {
            return [
                'success' => false,
                'message' => 'Order not found',
                'status_code' => 404,
            ];
        }

        $payment = Payment::where('order_id', $order->id)->first();

        return [
            'success' => true,
            'data' => $this->buildStandardizedOrderResponse($order, $payment),
            'status_code' => 200,
        ];
    }

    /**
     * Calculate items breakdown and subtotal
     */
    private function calculateItemsBreakdown(array $orderItems): array
    {
        $subtotal = 0;
        $items = [];

        foreach ($orderItems as $item) {
            $menuItem = MenuItem::find($item['item_id']);
            if ($menuItem) {
                $itemTotal = (float) ($menuItem->base_price ?? 0) * ($item['quantity'] ?? 1);
                $subtotal += $itemTotal;
                $items[] = [
                    'id' => $item['item_id'],
                    'name' => $menuItem->item_name,
                    'quantity' => $item['quantity'],
                    'price' => (float) ($menuItem->base_price ?? 0),
                    'total_price' => (float) $itemTotal,
                    'special_instructions' => $item['special_instructions'] ?? null,
                ];
            }
        }

        return [
            'subtotal' => (float) $subtotal,
            'items' => $items,
        ];
    }

    /**
     * Calculate delivery details (distance and fee)
     */
    private function calculateDeliveryDetails(Restaurant $restaurant, CustomerAddress $address): array
    {
        $restaurant_lat = (float) $restaurant->latitude;
        $restaurant_lng = (float) $restaurant->longitude;
        $customer_lat = (float) $address->latitude;
        $customer_lng = (float) $address->longitude;

        // Validate coordinates
        if (abs($customer_lat) > 90 || abs($customer_lng) > 180 ||
            abs($restaurant_lat) > 90 || abs($restaurant_lng) > 180) {
            return [
                'success' => false,
                'message' => 'Invalid coordinates detected for restaurant or delivery address.',
                'status_code' => 422,
            ];
        }

        // Calculate distance
        $distance = $this->calculateDistance(
            $restaurant_lat, $restaurant_lng,
            $customer_lat, $customer_lng
        );
        $distance = 10; // temp override for testing
        // Check if delivery destination is within range
        if ($distance > 15) {
            return [
                'success' => false,
                'message' => 'Delivery destination is too far. Maximum allowed distance is 15km.',
                'distance_km' => round($distance, 2),
                'status_code' => 422,
            ];
        }

        // Calculate delivery fee
        $deliveryFee = $this->calculateDeliveryFee($distance);
        $estimatedTime = $this->calculateEstimatedDeliveryTime($distance);

        return [
            'success' => true,
            'distance' => $distance,
            'delivery_fee' => $deliveryFee,
            'estimated_time' => $estimatedTime,
            'restaurant_location' => [
                'latitude' => $restaurant_lat,
                'longitude' => $restaurant_lng,
            ],
            'customer_location' => [
                'latitude' => $customer_lat,
                'longitude' => $customer_lng,
            ],
        ];
    }

    /**
     * Create payment record for order
     */
    private function createPaymentRecord(Order $order): Payment
    {
        $paymentGateway = match ($order->payment_method) {
            'cod' => 'wallet',
            'wallet' => 'wallet',
            'upi' => 'phonepe',
            'card' => 'stripe',
            default => 'manual',
        };

        $paymentStatus = match ($order->payment_method) {
            'cod' => 'pending',
            'wallet' => 'completed',
            'card' => 'completed',
            'upi' => 'pending',
            default => 'pending',
        };

        return Payment::create([
            'order_id' => $order->id,
            'tenant_id' => $order->tenant_id,
            'payment_method' => $order->payment_method,
            'payment_gateway' => $paymentGateway,
            'amount' => $order->total_amount,
            'currency' => 'INR',
            'status' => $paymentStatus,
            'initiated_at' => now(),
        ]);
    }

    /**
     * Get delivery preview information
     */
    private function getDeliveryPreview(Order $order): array
    {
        $order->load(['deliveryAddress', 'restaurant']);

        if (! $order->deliveryAddress || ! $order->restaurant) {
            return [
                'nearest_partner' => null,
                'delivery_info' => null,
            ];
        }

        $customer_lat = (float) $order->deliveryAddress->latitude;
        $customer_lng = (float) $order->deliveryAddress->longitude;
        $restaurant_lat = (float) $order->restaurant->latitude;
        $restaurant_lng = (float) $order->restaurant->longitude;

        // Find nearest available delivery partner
        $partners = DeliveryPartner::where('is_available', true)
            ->where('is_online', true)
            ->where('status', 'approved')
            ->get();

        $nearest = null;
        $minDistance = null;

        foreach ($partners as $partner) {
            if ($partner->current_latitude !== null && $partner->current_longitude !== null) {
                $distance = $this->calculateDistance(
                    $restaurant_lat, $restaurant_lng,
                    (float) $partner->current_latitude, (float) $partner->current_longitude
                );

                if ($minDistance === null || $distance < $minDistance) {
                    $minDistance = $distance;
                    $nearest = $partner;
                }
            }
        }

        $partnerPreview = null;
        if ($nearest) {
            $nearest->load('user');
            $partnerName = $nearest->user ? $nearest->user->first_name.' '.$nearest->user->last_name : 'Unknown';

            $partnerPreview = [
                'id' => (string) $nearest->id,
                'name' => $partnerName,
                'vehicle_type' => $nearest->vehicle_type,
                'average_rating' => $nearest->average_rating,
                'distance_to_restaurant_km' => (string) round($minDistance, 2),
                'note' => 'This is an estimated delivery partner. Actual assignment will be made when order is ready for pickup.',
            ];
        }

        return [
            'nearest_partner' => $partnerPreview,
        ];
    }

    /**
     * Build billing details array
     */
    public function buildBillingDetails(Order $order): array
    {
        $taxPercentage = 0;
        if ($order->restaurant) {
            $taxPercentage = (float) ($order->restaurant->tax_percentage ?? 0);
        }

        // Calculate delivery distance if addresses available
        $distanceKm = null;
        if ($order->deliveryAddress && $order->restaurant) {
            $distanceKm = round($this->calculateDistance(
                (float) $order->restaurant->latitude,
                (float) $order->restaurant->longitude,
                (float) $order->deliveryAddress->latitude,
                (float) $order->deliveryAddress->longitude
            ), 2);
        }

        return [
            'subtotal' => [
                'amount' => number_format($order->subtotal, 2, '.', ''),
                'description' => 'Items Total',
            ],

            'tax' => [
                'amount' => number_format($order->tax_amount, 2, '.', ''),
                'percentage' => number_format($taxPercentage, 2, '.', ''),
                'description' => "Tax ({$taxPercentage}%)",
            ],

            'delivery_fee' => [
                'amount' => number_format($order->delivery_fee, 2, '.', ''),
                'distance_km' => $distanceKm !== null
                    ? number_format($distanceKm, 2, '.', '')
                    : null,
                'description' => 'Delivery Fee (based on distance)',
            ],

            'platform_fee' => [
                'amount' => number_format($order->platform_fee, 2, '.', ''),
                'description' => 'Platform Fee',
            ],

            'discount' => [
                'amount' => number_format($order->discount_amount, 2, '.', ''),
                'description' => 'Discount',
            ],

            'total_amount' => [
                'amount' => number_format($order->total_amount, 2, '.', ''),
                'description' => 'Total Payable',
                'breakdown' => "Subtotal (₹{$order->subtotal}) + Tax (₹{$order->tax_amount}) + Delivery Fee (₹{$order->delivery_fee}) + Platform Fee (₹{$order->platform_fee}) - Discount (₹{$order->discount_amount}) = ₹{$order->total_amount}",
            ],
        ];
    }

    /**
     * Build standardized order response (used for create, edit, checkout, and get details)
     */
    private function buildStandardizedOrderResponse(Order $order, ?Payment $payment = null): array
    {
        // Get delivery distance
        $distanceKm = null;
        if ($order->deliveryAddress && $order->restaurant) {
            $distanceKm = round($this->calculateDistance(
                (float) $order->restaurant->latitude,
                (float) $order->restaurant->longitude,
                (float) $order->deliveryAddress->latitude,
                (float) $order->deliveryAddress->longitude
            ), 2);
        }

        // Get estimated delivery time
        $estimatedDeliveryTime = $this->calculateEstimatedDeliveryTime($distanceKm ?? 0);

        // Get delivery preview (for new orders only)
        $deliveryPreview = null;
        if (in_array($order->status, ['draft', 'placed'])) {
            $deliveryPreview = $this->getDeliveryPreview($order);
        }

        // Get tax percentage
        $taxPercentage = (float) ($order->restaurant->tax_percentage ?? 0);

        // Get coupon details if applied
        $couponDetails = null;
        $couponUsage = CouponUsage::where('order_id', $order->id)
            ->with('coupon')
            ->first();

        if ($couponUsage && $couponUsage->coupon) {
            $userCouponUsageCount = CouponUsage::where('coupon_id', $couponUsage->coupon_id)
                ->where('user_id', $order->customer->user_id)
                ->count();

            $couponDetails = [
                'coupon_id' => (string) $couponUsage->coupon->id,
                'coupon_code' => (string) $couponUsage->coupon->code,
                'coupon_name' => (string) ($couponUsage->coupon->name ?? $couponUsage->coupon->code),
                'discount_type' => (string) $couponUsage->coupon->discount_type,
                'discount_value' => (float) $couponUsage->coupon->discount_value,
                'discount_applied' => (float) $order->discount_amount,
                'user_usage_count' => (int) $userCouponUsageCount,
                'total_usage_limit' => $couponUsage->coupon->usage_limit ? (int) $couponUsage->coupon->usage_limit : null,
                'usage_per_user' => (int) $couponUsage->coupon->usage_per_user,
                'applied_at' => $couponUsage->used_at ? $couponUsage->used_at->toISOString() : null,
            ];
        }

        return [
            'order' => [
                'id' => (string) $order->id,
                'order_number' => (string) $order->order_number,
                'status' => (string) $order->status,
                'payment_status' => (string) $order->payment_status,
                'special_instructions' => (string) ($order->special_instructions ?? ''),
                'created_at' => ($order->created_at instanceof \Carbon\Carbon) ? $order->created_at->toISOString() : (string) $order->created_at,
                'updated_at' => ($order->updated_at instanceof \Carbon\Carbon) ? $order->updated_at->toISOString() : (string) $order->updated_at,
            ],
            'order_items' => $order->orderItems->map(function ($item) {
                return [
                    'id' => $item->item_id,
                    'name' => (string) $item->item_name,
                    'quantity' => (int) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'total_price' => (float) $item->total_price,
                    'special_instructions' => (string) ($item->special_instructions ?? ''),
                ];
            })->toArray(),
            'billing_details' => $this->buildBillingDetails($order),
            'payment' => $payment ? [
                'id' => (string) $payment->id,
                'amount' => (string) $payment->amount,
                'currency' => (string) $payment->currency,
                'payment_method' => (string) $payment->payment_method,
                'payment_gateway' => (string) $payment->payment_gateway,
                'status' => (string) $payment->status,
                'initiated_at' => ($payment->initiated_at instanceof \Carbon\Carbon) ? $payment->initiated_at->toISOString() : (string) $payment->initiated_at,
            ] : null,
            'delivery_info' => [
                'distance_km' => (string) ($distanceKm !== null ? $distanceKm : ''),
                'delivery_fee' => (string) $order->delivery_fee,
                'estimated_delivery_time' => (string) $estimatedDeliveryTime,
                'restaurant_location' => $order->restaurant ? [
                    'latitude' => (float) $order->restaurant->latitude,
                    'longitude' => (float) $order->restaurant->longitude,
                ] : null,
                'customer_location' => $order->deliveryAddress ? [
                    'latitude' => (float) $order->deliveryAddress->latitude,
                    'longitude' => (float) $order->deliveryAddress->longitude,
                ] : null,
            ],
            'estimated_delivery_partner' => $deliveryPreview['nearest_partner'] ?? null,
            'delivery_address' => $order->deliveryAddress ? [
                'id' => (string) $order->deliveryAddress->id,
                'address_line_1' => (string) $order->deliveryAddress->address_line_1,
                'address_line_2' => (string) $order->deliveryAddress->address_line_2,
                'city' => (string) $order->deliveryAddress->city,
                'state' => (string) $order->deliveryAddress->state,
                'postal_code' => (string) $order->deliveryAddress->postal_code,
                'latitude' => (float) $order->deliveryAddress->latitude,
                'longitude' => (float) $order->deliveryAddress->longitude,
            ] : null,
            'restaurant' => $order->restaurant ? [
                'id' => (string) $order->restaurant->id,
                'name' => (string) $order->restaurant->restaurant_name,
                'address' => (string) $order->restaurant->address,
                'phone' => (string) ($order->restaurant->phone ?? ''),
                'tax_percentage' => (float) $taxPercentage,
            ] : null,
            'customer' => $order->customer && $order->customer->user ? [
                'id' => (string) $order->customer->id,
                'name' => (string) ($order->customer->user->first_name.' '.$order->customer->user->last_name),
                'email' => (string) $order->customer->user->email,
                'phone' => (string) ($order->customer->user->phone ?? ''),
            ] : null,
            'coupon' => $couponDetails,
        ];
    }

    /**
     * Build order list item
     */
    private function buildOrderListItem(Order $order): array
    {
        // Get coupon details if applied
        $couponDetails = null;
        $couponUsage = CouponUsage::where('order_id', $order->id)
            ->with('coupon')
            ->first();

        if ($couponUsage && $couponUsage->coupon) {
            $userCouponUsageCount = CouponUsage::where('coupon_id', $couponUsage->coupon_id)
                ->where('user_id', $order->customer->user_id)
                ->count();

            $couponDetails = [
                'coupon_id' => (string) $couponUsage->coupon->id,
                'coupon_code' => (string) $couponUsage->coupon->code,
                'coupon_name' => (string) ($couponUsage->coupon->name ?? $couponUsage->coupon->code),
                'discount_type' => (string) $couponUsage->coupon->discount_type,
                'discount_value' => (float) $couponUsage->coupon->discount_value,
                'discount_applied' => (float) $order->discount_amount,
                'user_usage_count' => (int) $userCouponUsageCount,
            ];
        }

        return [
            'order_id' => (string) $order->id,
            'order_number' => (string) $order->order_number,
            'status' => (string) $order->status,
            'payment_status' => (string) $order->payment_status,
            'placed_at' => $order->created_at->format('Y-m-d H:i:s'),
            'billing_details' => $this->buildBillingDetails($order),
            'restaurant' => $order->restaurant ? [
                'id' => (string) ($order->restaurant->id ?? ''),
                'name' => (string) ($order->restaurant->restaurant_name ?? ''),
                'address' => (string) ($order->restaurant->address ?? ''),
            ] : null,
            'items' => $order->orderItems->map(function ($item) {
                return [
                    'name' => (string) $item->item_name,
                    'quantity' => (int) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'total_price' => (float) $item->total_price,
                ];
            })->toArray(),
            'coupon' => $couponDetails,
            'delivery_partner' => $order->deliveryAssignment && $order->deliveryAssignment->partner && $order->deliveryAssignment->partner->user
                ? [
                    'name' => (string) ($order->deliveryAssignment->partner->user->name ?? ''),
                    'phone' => (string) ($order->deliveryAssignment->partner->user->phone ?? ''),
                ]
                : null,
        ];
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371; // Radius of the Earth in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Calculate delivery fee based on distance
     */
    private function calculateDeliveryFee($distance): float
    {
        if ($distance <= 3) {
            return 20; // ₹20 for 0-3km
        } elseif ($distance <= 6) {
            return 40; // ₹40 for 3-6km
        } elseif ($distance <= 10) {
            return 60; // ₹60 for 6-10km
        } else {
            return 80; // ₹80 for 10-15km
        }
    }

    /**
     * Calculate estimated delivery time based on distance
     */
    private function calculateEstimatedDeliveryTime($distance): string
    {
        $travelTimeMinutes = ($distance / 25) * 60; // 25 km/h average speed
        $totalMinutes = ceil($travelTimeMinutes + 10); // +10 mins for pickup

        if ($totalMinutes <= 20) {
            return '15-20 mins';
        } elseif ($totalMinutes <= 30) {
            return '25-30 mins';
        } elseif ($totalMinutes <= 45) {
            return '35-45 mins';
        } else {
            return '45-60 mins';
        }
    }
}
