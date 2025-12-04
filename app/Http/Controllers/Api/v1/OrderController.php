<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    //   Create a new order and its items.

    //  Example JSON request:
    //  {
    //  "order_number": "ORD1234",
    //     "delivery_address_id": 5,
    //     "payment_method": "cod",
    //     "special_instructions": "Leave at the door",
    //     "order_items": [
    //       {
    //         "item_id": 1,
    //         "item_name": "Pizza Margherita",
    //         "quantity": 2,
    //         "special_instructions": "Extra cheese"
    //       }
    //     ]
    //   }

    public function CreateOrder(Request $request)
    {
        $user = auth()->user();

        // Get customer profile for authenticated user
        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (! $customerProfile) {
            return response()->json(['success' => false, 'message' => 'Customer profile not found for user.'], 404);
        }

        // Validate request
        $validator = \Validator::make($request->all(), [
            'order_number' => 'required|string|unique:orders,order_number',
            'delivery_address_id' => 'required|exists:customer_addresses,id',
            'payment_method' => 'required|string',
            'special_instructions' => 'nullable|string',
            'order_items' => 'required|array|min:1',
            'order_items.*.item_id' => 'required|exists:menu_items,id',
            'order_items.*.item_name' => 'required|string',
            'order_items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $orderItems = $validated['order_items'];
        unset($validated['order_items']);

        // Get restaurant_id & tenant_id from the first item
        $firstItem = $orderItems[0];
        $menuItem = MenuItem::find($firstItem['item_id']);
        if (! $menuItem) {
            return response()->json(['success' => false, 'message' => 'Menu item not found.'], 422);
        }
        $restaurantId = $menuItem->restaurant_id;
        $tenantId = $menuItem->tenant_id;

        // Check if restaurant can accept new orders
        $restaurant = Restaurant::find($restaurantId);
        if (! $restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant not found.',
            ], 422);
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

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }

        // Validate that delivery_address_id belongs to this customer
        $deliveryAddressId = $validated['delivery_address_id'];
        $address = CustomerAddress::where('id', $deliveryAddressId)
            ->where('customer_id', $customerProfile->id)
            ->first();
        if (! $address) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid delivery_address_id: Address does not belong to the customer.',
            ], 422);
        }

        // Calculate subtotal from menu_items base_price
        $subtotal = 0;
        foreach ($orderItems as $item) {
            $menuItem = MenuItem::find($item['item_id']);
            if ($menuItem) {
                $subtotal += ($menuItem->base_price ?? 0) * ($item['quantity'] ?? 1);
            }
        }

        // Prepare order data
        $orderData = $validated;
        $orderData['customer_id'] = $customerProfile->id;
        $orderData['restaurant_id'] = $restaurantId;
        $orderData['tenant_id'] = $tenantId;
        $orderData['payment_status'] = 'pending'; // Default
        $orderData['subtotal'] = $subtotal;
        $orderData['delivery_fee'] = 0;
        $orderData['tax_amount'] = 0;
        $orderData['discount_amount'] = 0;
        $orderData['restaurant_amount'] = 0;
        $orderData['delivery_amount'] = 0;
        $orderData['platform_fee'] = 0;

        // Calculate total_amount
        $orderData['total_amount'] =
            ($orderData['subtotal'] ?? 0) +
            ($orderData['tax_amount'] ?? 0) +
            ($orderData['delivery_fee'] ?? 0) -
            ($orderData['discount_amount'] ?? 0);

        \DB::beginTransaction();
        try {
            $order = Order::create($orderData);

            // Create initial order status record
            OrderStatus::create([
                'order_id' => $order->id,
                'status' => 'placed',
            ]);

            foreach ($orderItems as $item) {
                $menuItem = MenuItem::find($item['item_id']);
                $item['order_id'] = $order->id;
                $item['tenant_id'] = $order->tenant_id;
                $item['unit_price'] = $menuItem ? $menuItem->base_price : 0;
                $item['total_price'] = $item['unit_price'] * ($item['quantity'] ?? 1);
                OrderItem::create($item);
            }

            // Create Payment Record
            // Note: For COD orders, we still create a payment record with 'wallet' gateway as placeholder
            // Valid payment_gateway values: razorpay, stripe, paytm, phonepe, wallet
            $paymentGateway = match ($order->payment_method) {
                'cod' => 'wallet', // COD uses wallet as placeholder gateway
                'upi' => 'phonepe',
                'card' => 'stripe',
                default => 'stripe',
            };

            $payment = Payment::create([
                'order_id' => $order->id,
                'tenant_id' => $order->tenant_id,
                'payment_method' => $order->payment_method,
                'payment_gateway' => $paymentGateway,
                'amount' => $order->total_amount,
                'currency' => 'INR',
                'status' => $order->payment_method === 'cod' ? 'pending' : 'initiated',
                'initiated_at' => now(),
            ]);

            Log::info("Payment record created for order {$order->id}: Payment ID {$payment->id}");

            // Get delivery info preview (estimated fee, nearest partner) - but DON'T assign yet
            // Delivery partner will be assigned when order status changes to 'ready_for_pickup'
            $deliveryPreview = $this->getDeliveryPreview($order);

            // Update order with estimated delivery fee
            if ($deliveryPreview['delivery_info']) {
                $estimatedDeliveryFee = $deliveryPreview['delivery_info']['delivery_fee'] ?? 0;
                $order->delivery_fee = $estimatedDeliveryFee;
                $order->total_amount = ($order->subtotal ?? 0) + ($order->tax_amount ?? 0) + $estimatedDeliveryFee - ($order->discount_amount ?? 0);
                $order->save();

                // Update payment amount with delivery fee
                $payment->amount = $order->total_amount;
                $payment->save();
            }

            \DB::commit();

            // Prepare response with all details
            $response = [
                'success' => true,
                'message' => 'Order placed successfully! A delivery partner will be assigned when your order is ready for pickup.',
                'data' => [
                    'order' => [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                        'subtotal' => $order->subtotal,
                        'delivery_fee' => $order->delivery_fee,
                        'tax_amount' => $order->tax_amount,
                        'discount_amount' => $order->discount_amount,
                        'total_amount' => $order->total_amount,
                        'payment_method' => $order->payment_method,
                        'payment_status' => $order->payment_status,
                        'special_instructions' => $order->special_instructions,
                        'created_at' => $order->created_at,
                    ],
                    'payment' => [
                        'id' => $payment->id,
                        'amount' => $payment->amount,
                        'currency' => $payment->currency,
                        'payment_method' => $payment->payment_method,
                        'payment_gateway' => $payment->payment_gateway,
                        'status' => $payment->status,
                        'initiated_at' => $payment->initiated_at,
                    ],
                    'delivery_preview' => $deliveryPreview['delivery_info'] ?? null,
                    'estimated_delivery_partner' => $deliveryPreview['nearest_partner'] ?? null,
                    'restaurant' => [
                        'id' => $restaurant->id,
                        'name' => $restaurant->restaurant_name,
                        'address' => $restaurant->address,
                    ],
                ],
                'info' => 'Delivery partner will be assigned when order is ready for pickup.',
            ];

            // Add partner preview message if available
            if ($deliveryPreview['nearest_partner']) {
                $partnerName = $deliveryPreview['nearest_partner']['name'];
                $response['delivery_message'] = "Your order will be delivered by {$partnerName} (or nearest available partner) once it's ready.";
            }

            return response()->json($response, 201);
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Order creation failed: '.$e->getMessage());

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Assign nearest available delivery partner to the order
     */
    private function assignDeliveryPartner(Order $order): array
    {
        // Load order relationships
        $order->load(['deliveryAddress', 'restaurant']);

        if (! $order->deliveryAddress || ! $order->restaurant) {
            Log::warning("Order {$order->id}: Missing delivery address or restaurant for assignment");

            return [
                'success' => false,
                'message' => 'Missing delivery address or restaurant information',
                'delivery_partner' => null,
                'delivery_info' => null,
            ];
        }

        $customer_lat = (float) $order->deliveryAddress->latitude;
        $customer_lng = (float) $order->deliveryAddress->longitude;
        $restaurant_lat = (float) $order->restaurant->latitude;
        $restaurant_lng = (float) $order->restaurant->longitude;

        // Validate coordinates
        if (abs($customer_lat) > 90 || abs($customer_lng) > 180 ||
            abs($restaurant_lat) > 90 || abs($restaurant_lng) > 180) {
            Log::warning("Order {$order->id}: Invalid coordinates detected");

            return [
                'success' => false,
                'message' => 'Invalid coordinates detected',
                'delivery_partner' => null,
                'delivery_info' => null,
            ];
        }

        // Calculate distance between restaurant and customer
        $restaurantToCustomerDistance = $this->calculateDistance(
            $restaurant_lat, $restaurant_lng,
            $customer_lat, $customer_lng
        );

        Log::info("Order {$order->id}: Restaurant to Customer distance: {$restaurantToCustomerDistance} km");

        if ($restaurantToCustomerDistance > 15) {
            Log::warning("Order {$order->id}: Delivery destination too far ({$restaurantToCustomerDistance} km)");

            return [
                'success' => false,
                'message' => 'Delivery destination is too far. Maximum allowed distance is 15km.',
                'delivery_partner' => null,
                'delivery_info' => [
                    'distance_km' => round($restaurantToCustomerDistance, 2),
                ],
            ];
        }

        // Check delivery zones - with fallback for distance-based fee
        $deliveryZone = DeliveryZone::getDeliveryZoneForLocation(
            $customer_lat,
            $customer_lng,
            $order->restaurant_id,
            $order->tenant_id
        );

        // Calculate delivery fee
        if ($deliveryZone) {
            $deliveryFee = $deliveryZone->delivery_fee;
            $estimatedDeliveryTime = $deliveryZone->estimated_delivery_time;
            Log::info("Order {$order->id}: Delivery Zone: {$deliveryZone->zone_name}, Fee: {$deliveryFee}");
        } else {
            // Fallback: Calculate delivery fee based on distance
            $deliveryFee = $this->calculateDeliveryFee($restaurantToCustomerDistance);
            $estimatedDeliveryTime = $this->calculateEstimatedDeliveryTime($restaurantToCustomerDistance);
            Log::info("Order {$order->id}: No delivery zone found. Using distance-based fee: {$deliveryFee}");
        }

        // Find nearest available delivery partner
        $partners = DeliveryPartner::where('is_available', true)
            ->where('is_online', true)
            ->where('status', 'approved')
            ->get();

        $nearest = null;
        $minDistance = null;

        foreach ($partners as $partner) {
            if ($partner->current_latitude !== null && $partner->current_longitude !== null) {
                $partner_lat = (float) $partner->current_latitude;
                $partner_lng = (float) $partner->current_longitude;

                // Calculate distance from delivery partner to restaurant (pickup point)
                $distance = $this->calculateDistance(
                    $restaurant_lat, $restaurant_lng,
                    $partner_lat, $partner_lng
                );

                Log::info("Order {$order->id}: Partner {$partner->id} distance to restaurant: {$distance} km");

                if ($minDistance === null || $distance < $minDistance) {
                    $minDistance = $distance;
                    $nearest = $partner;
                }
            }
        }

        if (! $nearest) {
            Log::warning("Order {$order->id}: No available delivery partner found");

            return [
                'success' => false,
                'message' => 'No available delivery partner found. Order will be assigned when a partner becomes available.',
                'delivery_partner' => null,
                'delivery_info' => [
                    'distance_km' => round($restaurantToCustomerDistance, 2),
                    'delivery_fee' => $deliveryFee,
                    'estimated_delivery_time' => $estimatedDeliveryTime,
                ],
            ];
        }

        try {
            // Create delivery assignment
            $assignment = DeliveryAssignment::create([
                'order_id' => $order->id,
                'tenant_id' => $order->tenant_id,
                'partner_id' => $nearest->id,
                'pickup_latitude' => $restaurant_lat,
                'pickup_longitude' => $restaurant_lng,
                'delivery_latitude' => $customer_lat,
                'delivery_longitude' => $customer_lng,
                'estimated_distance_km' => $restaurantToCustomerDistance,
                'estimated_duration_minutes' => $this->getEstimatedMinutes($estimatedDeliveryTime),
                'delivery_fee' => $deliveryFee,
                'status' => 'assigned',
                'assigned_at' => now(),
            ]);

            // Update order with delivery fee and status
            $order->status = 'assigned_to_delivery';
            $order->delivery_fee = $deliveryFee;

            // Recalculate total amount with delivery fee
            $order->total_amount = ($order->subtotal ?? 0) + ($order->tax_amount ?? 0) + $deliveryFee - ($order->discount_amount ?? 0);
            $order->save();

            // Create order status record for assignment
            OrderStatus::create([
                'order_id' => $order->id,
                'status' => 'assigned_to_delivery',
            ]);

            // Load partner's user details
            $nearest->load('user');
            $partnerName = $nearest->user ? $nearest->user->first_name.' '.$nearest->user->last_name : 'Unknown';
            $partnerPhone = $nearest->user ? $nearest->user->phone : null;

            Log::info("Order {$order->id}: Assigned to delivery partner {$nearest->id} ({$partnerName})");

            return [
                'success' => true,
                'message' => 'Delivery partner assigned successfully',
                'delivery_partner' => [
                    'id' => $nearest->id,
                    'name' => $partnerName,
                    'phone' => $partnerPhone,
                    'vehicle_type' => $nearest->vehicle_type,
                    'vehicle_number' => $nearest->vehicle_number,
                    'average_rating' => $nearest->average_rating,
                    'total_deliveries' => $nearest->total_deliveries,
                    'distance_to_restaurant_km' => round($minDistance, 2),
                ],
                'delivery_info' => [
                    'assignment_id' => $assignment->id,
                    'distance_km' => round($restaurantToCustomerDistance, 2),
                    'delivery_fee' => $deliveryFee,
                    'estimated_delivery_time' => $estimatedDeliveryTime,
                    'delivery_zone' => $deliveryZone ? [
                        'id' => $deliveryZone->id,
                        'name' => $deliveryZone->zone_name,
                    ] : null,
                ],
            ];

        } catch (\Exception $e) {
            Log::error("Order {$order->id}: Failed to assign delivery partner - ".$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to assign delivery partner: '.$e->getMessage(),
                'delivery_partner' => null,
                'delivery_info' => null,
            ];
        }
    }

    /**
     * Get delivery preview information without assigning (for order creation)
     * Returns estimated delivery fee and nearest available partner info
     */
    private function getDeliveryPreview(Order $order): array
    {
        // Load order relationships
        $order->load(['deliveryAddress', 'restaurant']);

        if (! $order->deliveryAddress || ! $order->restaurant) {
            Log::warning("Order {$order->id}: Missing delivery address or restaurant for preview");

            return [
                'nearest_partner' => null,
                'delivery_info' => null,
            ];
        }

        $customer_lat = (float) $order->deliveryAddress->latitude;
        $customer_lng = (float) $order->deliveryAddress->longitude;
        $restaurant_lat = (float) $order->restaurant->latitude;
        $restaurant_lng = (float) $order->restaurant->longitude;

        // Validate coordinates
        if (abs($customer_lat) > 90 || abs($customer_lng) > 180 ||
            abs($restaurant_lat) > 90 || abs($restaurant_lng) > 180) {
            Log::warning("Order {$order->id}: Invalid coordinates for preview");

            return [
                'nearest_partner' => null,
                'delivery_info' => null,
            ];
        }

        // Calculate distance between restaurant and customer
        $restaurantToCustomerDistance = $this->calculateDistance(
            $restaurant_lat, $restaurant_lng,
            $customer_lat, $customer_lng
        );

        Log::info("Order {$order->id}: Preview - Restaurant to Customer distance: {$restaurantToCustomerDistance} km");

        if ($restaurantToCustomerDistance > 15) {
            Log::warning("Order {$order->id}: Preview - Delivery destination too far ({$restaurantToCustomerDistance} km)");

            return [
                'nearest_partner' => null,
                'delivery_info' => [
                    'distance_km' => round($restaurantToCustomerDistance, 2),
                    'delivery_fee' => 0,
                    'estimated_delivery_time' => 'N/A',
                    'warning' => 'Delivery destination is too far. Maximum allowed distance is 15km.',
                ],
            ];
        }

        // Check delivery zones - with fallback for distance-based fee
        $deliveryZone = DeliveryZone::getDeliveryZoneForLocation(
            $customer_lat,
            $customer_lng,
            $order->restaurant_id,
            $order->tenant_id
        );

        // Calculate delivery fee
        if ($deliveryZone) {
            $deliveryFee = $deliveryZone->delivery_fee;
            $estimatedDeliveryTime = $deliveryZone->estimated_delivery_time;
            Log::info("Order {$order->id}: Preview - Delivery Zone: {$deliveryZone->zone_name}, Fee: {$deliveryFee}");
        } else {
            // Fallback: Calculate delivery fee based on distance
            $deliveryFee = $this->calculateDeliveryFee($restaurantToCustomerDistance);
            $estimatedDeliveryTime = $this->calculateEstimatedDeliveryTime($restaurantToCustomerDistance);
            Log::info("Order {$order->id}: Preview - No delivery zone found. Using distance-based fee: {$deliveryFee}");
        }

        // Find nearest available delivery partner (for preview, NOT assignment)
        $partners = DeliveryPartner::where('is_available', true)
            ->where('is_online', true)
            ->where('status', 'approved')
            ->get();

        $nearest = null;
        $minDistance = null;

        foreach ($partners as $partner) {
            if ($partner->current_latitude !== null && $partner->current_longitude !== null) {
                $partner_lat = (float) $partner->current_latitude;
                $partner_lng = (float) $partner->current_longitude;

                // Calculate distance from delivery partner to restaurant (pickup point)
                $distance = $this->calculateDistance(
                    $restaurant_lat, $restaurant_lng,
                    $partner_lat, $partner_lng
                );

                if ($minDistance === null || $distance < $minDistance) {
                    $minDistance = $distance;
                    $nearest = $partner;
                }
            }
        }

        // Prepare delivery info
        $deliveryInfo = [
            'distance_km' => round($restaurantToCustomerDistance, 2),
            'delivery_fee' => $deliveryFee,
            'estimated_delivery_time' => $estimatedDeliveryTime,
            'delivery_zone' => $deliveryZone ? [
                'id' => $deliveryZone->id,
                'name' => $deliveryZone->zone_name,
            ] : null,
        ];

        // Prepare partner preview info (if available)
        $partnerPreview = null;
        if ($nearest) {
            $nearest->load('user');
            $partnerName = $nearest->user ? $nearest->user->first_name.' '.$nearest->user->last_name : 'Unknown';

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
            'delivery_info' => $deliveryInfo,
        ];
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the Earth in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in kilometers
    }

    /**
     * Calculate delivery fee based on distance
     */
    private function calculateDeliveryFee($distance)
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
    private function calculateEstimatedDeliveryTime($distance)
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

    /**
     * Convert estimated time string to minutes
     */
    private function getEstimatedMinutes($timeString)
    {
        // Extract max minutes from string like "25-30 mins"
        preg_match('/(\d+)\s*mins?$/i', $timeString, $matches);

        return isset($matches[1]) ? (int) $matches[1] : 30;
    }

    public function getOrderDetails(Request $request)
    {

        $id = $request->input('id');
        $order = Order::with(['customer.user', 'restaurant', 'deliveryAddress', 'orderItems'])->find($id);
        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }
        // Use deliveryAddress for the order's address
        $customer_address_latitude = $order->deliveryAddress->latitude ?? null;
        $customer_address_longitude = $order->deliveryAddress->longitude ?? null;

        $restaurant_latitude = $order->restaurant->latitude ?? null;
        $restaurant_longitude = $order->restaurant->longitude ?? null;

        $distance = null;
        if ($customer_address_latitude && $customer_address_longitude && $restaurant_latitude && $restaurant_longitude) {
            // dd("customer",$order->deliveryAddress->id ,$customer_address_latitude, $customer_address_longitude,
            // "restaurant",$order->restaurant->id,$restaurant_latitude, $restaurant_longitude);
            $distance = $this->calculateDistance(
                $customer_address_latitude,
                $customer_address_longitude,
                $restaurant_latitude,
                $restaurant_longitude
            );
        }

        $response = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'total_amount' => $order->total_amount,
            'restaurant' => $order->restaurant->restaurant_name,
            'customer' => [
                'id' => $order->customer->id,
                'name' => $order->customer->user ? ($order->customer->user->first_name.' '.$order->customer->user->last_name) : null,
                'email' => $order->customer->user->email ?? null,
                'phone' => $order->customer->user->phone ?? null,
            ],
            'delivery_distance_km' => $distance ? round($distance, 2) : null,
        ];

        return response()->json(['success' => true, 'order' => $response], 200);
    }
}
