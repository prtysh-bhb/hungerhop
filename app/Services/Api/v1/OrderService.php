<?php

namespace App\Services\Api\v1;

use App\Models\CustomerAddress;
use App\Models\CustomerProfile;
use App\Models\DeliveryAssignment;
use App\Models\DeliveryPartner;
use App\Models\DeliveryZone;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Restaurant;
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
        if (!$customerProfile) {
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
        if (!$menuItem) {
            return [
                'success' => false,
                'message' => 'Menu item not found.',
                'status_code' => 422,
            ];
        }

        $restaurantId = $menuItem->restaurant_id;
        $tenantId = $menuItem->tenant_id;

        // Check if restaurant can accept new orders
        $restaurant = Restaurant::find($restaurantId);
        if (!$restaurant) {
            return [
                'success' => false,
                'message' => 'Restaurant not found.',
                'status_code' => 422,
            ];
        }

        if (!$restaurant->canAcceptNewOrders()) {
            $message = 'Order cannot be accepted.';
            if ($restaurant->is_paused) {
                $message = 'Order cannot be accepted due to restaurant is temporarily closed.';
            } elseif (!$restaurant->is_open) {
                $message = 'Order cannot be accepted due to restaurant is closed.';
            } elseif (!$restaurant->accepts_orders) {
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

        if (!$address) {
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
        if (!$deliveryCalculation['success']) {
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

        // Prepare order data
        $orderData = $data;
        $orderData['customer_id'] = $customerProfile->id;
        $orderData['restaurant_id'] = $restaurantId;
        $orderData['tenant_id'] = $tenantId;
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
                'status' => 'placed',
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                $menuItem = MenuItem::find($item['item_id']);
                $item['order_id'] = $order->id;
                $item['tenant_id'] = $order->tenant_id;
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
                'message' => 'Order placed successfully! A delivery partner will be assigned when your order is ready for pickup.',
                'data' => $this->buildOrderResponse($order, $itemsBreakdown['items'], $payment, $deliveryCalculation, $deliveryPreview, $restaurant, $taxPercentage),
                'status_code' => 201,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());

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
        if (!$customerProfile) {
            return [
                'success' => false,
                'message' => 'Customer profile not found for user.',
                'status_code' => 404,
            ];
        }

        $order = Order::where('id', $orderId)
            ->where('customer_id', $customerProfile->id)
            ->first();

        if (!$order) {
            return [
                'success' => false,
                'message' => 'Order not found or does not belong to you.',
                'status_code' => 404,
            ];
        }

        // Check if order can be edited (only placed or pending orders)
        $editableStatuses = ['placed', 'pending', 'confirmed'];
        if (!in_array($order->status, $editableStatuses)) {
            return [
                'success' => false,
                'message' => 'Order cannot be edited. Only orders with status: placed, pending, or confirmed can be edited.',
                'current_status' => $order->status,
                'status_code' => 422,
            ];
        }

        $restaurant = Restaurant::find($order->restaurant_id);
        if (!$restaurant) {
            return [
                'success' => false,
                'message' => 'Restaurant not found.',
                'status_code' => 422,
            ];
        }

        DB::beginTransaction();
        try {
            $updateData = [];

            // Update special instructions if provided
            if (isset($data['special_instructions'])) {
                $updateData['special_instructions'] = $data['special_instructions'];
            }

            // Update delivery address if provided
            if (isset($data['delivery_address_id'])) {
                $address = CustomerAddress::where('id', $data['delivery_address_id'])
                    ->where('customer_id', $customerProfile->id)
                    ->first();

                if (!$address) {
                    DB::rollBack();
                    return [
                        'success' => false,
                        'message' => 'Invalid delivery_address_id: Address does not belong to the customer.',
                        'status_code' => 422,
                    ];
                }

                // Recalculate delivery fee with new address
                $deliveryCalculation = $this->calculateDeliveryDetails($restaurant, $address);
                if (!$deliveryCalculation['success']) {
                    DB::rollBack();
                    return $deliveryCalculation;
                }

                $updateData['delivery_address_id'] = $data['delivery_address_id'];
                $updateData['delivery_fee'] = $deliveryCalculation['delivery_fee'];
                $updateData['delivery_amount'] = $deliveryCalculation['delivery_fee'];
            }

            // Update order items if provided
            if (isset($data['order_items']) && is_array($data['order_items'])) {
                // Delete existing order items
                OrderItem::where('order_id', $order->id)->delete();

                // Calculate new items
                $itemsBreakdown = $this->calculateItemsBreakdown($data['order_items']);
                $subtotal = $itemsBreakdown['subtotal'];

                // Create new order items
                foreach ($data['order_items'] as $item) {
                    $menuItem = MenuItem::find($item['item_id']);
                    if (!$menuItem) {
                        DB::rollBack();
                        return [
                            'success' => false,
                            'message' => "Menu item with ID {$item['item_id']} not found.",
                            'status_code' => 422,
                        ];
                    }

                    // Ensure item belongs to same restaurant
                    if ($menuItem->restaurant_id !== $order->restaurant_id) {
                        DB::rollBack();
                        return [
                            'success' => false,
                            'message' => 'All items must be from the same restaurant.',
                            'status_code' => 422,
                        ];
                    }

                    OrderItem::create([
                        'order_id' => $order->id,
                        'tenant_id' => $order->tenant_id,
                        'item_id' => $item['item_id'],
                        'item_name' => $item['item_name'],
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
            if (!empty($updateData)) {
                $newSubtotal = $updateData['subtotal'] ?? $order->subtotal;
                $newTaxAmount = $updateData['tax_amount'] ?? $order->tax_amount;
                $newDeliveryFee = $updateData['delivery_fee'] ?? $order->delivery_fee;
                $platformFee = $order->platform_fee;
                $discountAmount = $order->discount_amount;

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
            $order->load(['orderItems', 'restaurant', 'deliveryAddress', 'customer']);

            return [
                'success' => true,
                'message' => 'Order updated successfully.',
                'data' => $this->buildOrderDetailsResponse($order),
                'status_code' => 200,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order edit failed: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to update order.',
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
        if (!$customerProfile) {
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

        if (!$order) {
            return [
                'success' => false,
                'message' => 'Order not found or does not belong to you.',
                'status_code' => 404,
            ];
        }

        // Get payment details
        $payment = Payment::where('order_id', $order->id)->first();

        // Build checkout response with billing details
        return [
            'success' => true,
            'message' => 'Checkout details retrieved successfully.',
            'data' => $this->buildCheckoutResponse($order, $payment),
            'status_code' => 200,
        ];
    }

    /**
     * List all orders for a customer
     */
    public function listOrders($user): array
    {
        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (!$customerProfile) {
            return [
                'success' => false,
                'message' => 'Customer profile not found for user.',
                'status_code' => 404,
            ];
        }

        $orders = Order::where('customer_id', $customerProfile->id)
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

        if (!$order) {
            return [
                'success' => false,
                'message' => 'Order not found',
                'status_code' => 404,
            ];
        }

        return [
            'success' => true,
            'order' => $this->buildOrderDetailsResponse($order),
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
                $itemTotal = ($menuItem->base_price ?? 0) * ($item['quantity'] ?? 1);
                $subtotal += $itemTotal;
                $items[] = [
                    'item_id' => $item['item_id'],
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $menuItem->base_price ?? 0,
                    'total_price' => $itemTotal,
                    'special_instructions' => $item['special_instructions'] ?? null,
                ];
            }
        }

        return [
            'subtotal' => $subtotal,
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
            'cod' => 'none',
            'wallet' => 'wallet',
            'upi' => 'phonepe',
            'card' => 'stripe',
            default => 'none',
        };

        $paymentStatus = match ($order->payment_method) {
            'cod' => 'pending',
            'wallet' => 'completed',
            'card' => 'completed',
            default => 'initiated',
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

        if (!$order->deliveryAddress || !$order->restaurant) {
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
            $partnerName = $nearest->user ? $nearest->user->first_name . ' ' . $nearest->user->last_name : 'Unknown';

            $partnerPreview = [
                'id' => $nearest->id,
                'name' => $partnerName,
                'vehicle_type' => $nearest->vehicle_type,
                'average_rating' => $nearest->average_rating,
                'distance_to_restaurant_km' => round($minDistance, 2),
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
                'amount' => (float) $order->subtotal,
                'description' => 'Items Total',
            ],
            'tax' => [
                'amount' => (float) $order->tax_amount,
                'percentage' => $taxPercentage,
                'description' => "Tax ({$taxPercentage}%)",
            ],
            'delivery_fee' => [
                'amount' => (float) $order->delivery_fee,
                'distance_km' => $distanceKm,
                'description' => 'Delivery Fee (based on distance)',
            ],
            'platform_fee' => [
                'amount' => (float) $order->platform_fee,
                'description' => 'Platform Fee',
            ],
            'discount' => [
                'amount' => (float) $order->discount_amount,
                'description' => 'Discount',
            ],
            'total_amount' => [
                'amount' => (float) $order->total_amount,
                'description' => 'Total Payable',
                'breakdown' => "Subtotal (₹{$order->subtotal}) + Tax (₹{$order->tax_amount}) + Delivery Fee (₹{$order->delivery_fee}) + Platform Fee (₹{$order->platform_fee}) - Discount (₹{$order->discount_amount}) = ₹{$order->total_amount}",
            ],
        ];
    }

    /**
     * Build order response for creation
     */
    private function buildOrderResponse(Order $order, array $itemsBreakdown, Payment $payment, array $deliveryCalculation, array $deliveryPreview, Restaurant $restaurant, float $taxPercentage): array
    {
        return [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'special_instructions' => $order->special_instructions,
                'created_at' => $order->created_at,
            ],
            'order_items' => $itemsBreakdown,
            'billing_details' => $this->buildBillingDetails($order),
            'payment' => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'payment_method' => $payment->payment_method,
                'payment_gateway' => $payment->payment_gateway,
                'status' => $payment->status,
                'initiated_at' => $payment->initiated_at,
            ],
            'delivery_info' => [
                'distance_km' => round($deliveryCalculation['distance'], 2),
                'delivery_fee' => $deliveryCalculation['delivery_fee'],
                'estimated_delivery_time' => $deliveryCalculation['estimated_time'],
                'restaurant_location' => $deliveryCalculation['restaurant_location'],
                'customer_location' => $deliveryCalculation['customer_location'],
            ],
            'estimated_delivery_partner' => $deliveryPreview['nearest_partner'] ?? null,
            'restaurant' => [
                'id' => $restaurant->id,
                'name' => $restaurant->restaurant_name,
                'address' => $restaurant->address,
                'tax_percentage' => $taxPercentage,
            ],
        ];
    }

    /**
     * Build checkout response
     */
    private function buildCheckoutResponse(Order $order, ?Payment $payment): array
    {
        $itemsBreakdown = $order->orderItems->map(function ($item) {
            return [
                'item_id' => $item->item_id,
                'item_name' => $item->item_name,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'total_price' => (float) $item->total_price,
                'special_instructions' => $item->special_instructions,
            ];
        });

        // Calculate delivery distance
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
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'special_instructions' => $order->special_instructions,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ],
            'order_items' => $itemsBreakdown,
            'billing_details' => $this->buildBillingDetails($order),
            'payment' => $payment ? [
                'id' => $payment->id,
                'amount' => (float) $payment->amount,
                'currency' => $payment->currency,
                'payment_method' => $payment->payment_method,
                'payment_gateway' => $payment->payment_gateway,
                'status' => $payment->status,
                'initiated_at' => $payment->initiated_at,
            ] : null,
            'delivery_info' => [
                'distance_km' => $distanceKm,
                'delivery_fee' => (float) $order->delivery_fee,
                'estimated_delivery_time' => $this->calculateEstimatedDeliveryTime($distanceKm ?? 0),
            ],
            'delivery_address' => $order->deliveryAddress ? [
                'id' => $order->deliveryAddress->id,
                'address_line_1' => $order->deliveryAddress->address_line_1,
                'address_line_2' => $order->deliveryAddress->address_line_2,
                'city' => $order->deliveryAddress->city,
                'state' => $order->deliveryAddress->state,
                'postal_code' => $order->deliveryAddress->postal_code,
                'latitude' => $order->deliveryAddress->latitude,
                'longitude' => $order->deliveryAddress->longitude,
            ] : null,
            'restaurant' => $order->restaurant ? [
                'id' => $order->restaurant->id,
                'name' => $order->restaurant->restaurant_name,
                'address' => $order->restaurant->address,
                'phone' => $order->restaurant->phone,
                'tax_percentage' => (float) ($order->restaurant->tax_percentage ?? 0),
            ] : null,
            'customer' => $order->customer ? [
                'id' => $order->customer->id,
                'name' => $order->customer->user ? ($order->customer->user->first_name . ' ' . $order->customer->user->last_name) : null,
                'email' => $order->customer->user->email ?? null,
                'phone' => $order->customer->user->phone ?? null,
            ] : null,
        ];
    }

    /**
     * Build order details response
     */
    private function buildOrderDetailsResponse(Order $order): array
    {
        $itemsBreakdown = $order->orderItems->map(function ($item) {
            return [
                'item_id' => $item->item_id,
                'item_name' => $item->item_name,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'total_price' => (float) $item->total_price,
                'special_instructions' => $item->special_instructions,
            ];
        });

        // Calculate delivery distance
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
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'special_instructions' => $order->special_instructions,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'order_items' => $itemsBreakdown,
            'billing_details' => $this->buildBillingDetails($order),
            'delivery_info' => [
                'distance_km' => $distanceKm,
                'delivery_fee' => (float) $order->delivery_fee,
                'estimated_delivery_time' => $this->calculateEstimatedDeliveryTime($distanceKm ?? 0),
            ],
            'restaurant' => $order->restaurant ? [
                'id' => $order->restaurant->id,
                'name' => $order->restaurant->restaurant_name,
                'address' => $order->restaurant->address,
                'tax_percentage' => (float) ($order->restaurant->tax_percentage ?? 0),
            ] : null,
            'customer' => $order->customer ? [
                'id' => $order->customer->id,
                'name' => $order->customer->user ? ($order->customer->user->first_name . ' ' . $order->customer->user->last_name) : null,
                'email' => $order->customer->user->email ?? null,
                'phone' => $order->customer->user->phone ?? null,
            ] : null,
            'delivery_address' => $order->deliveryAddress ? [
                'id' => $order->deliveryAddress->id,
                'address_line_1' => $order->deliveryAddress->address_line_1,
                'city' => $order->deliveryAddress->city,
                'latitude' => $order->deliveryAddress->latitude,
                'longitude' => $order->deliveryAddress->longitude,
            ] : null,
        ];
    }

    /**
     * Build order list item
     */
    private function buildOrderListItem(Order $order): array
    {
        return [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'placed_at' => $order->created_at->format('Y-m-d H:i:s'),
            'billing_details' => $this->buildBillingDetails($order),
            'restaurant' => [
                'id' => $order->restaurant->id ?? null,
                'name' => $order->restaurant->restaurant_name ?? null,
                'address' => $order->restaurant->address ?? null,
            ],
            'items' => $order->orderItems->map(function ($item) {
                return [
                    'item_name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'total_price' => (float) $item->total_price,
                ];
            }),
            'delivery_partner' => $order->deliveryAssignment
                ? [
                    'name' => $order->deliveryAssignment->partner->user->name ?? null,
                    'phone' => $order->deliveryAssignment->partner->user->phone ?? null,
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
