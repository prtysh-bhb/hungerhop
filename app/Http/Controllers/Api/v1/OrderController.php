<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\CustomerProfile;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;

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

            foreach ($orderItems as $item) {
                $menuItem = MenuItem::find($item['item_id']);
                $item['order_id'] = $order->id;
                $item['tenant_id'] = $order->tenant_id;
                $item['unit_price'] = $menuItem ? $menuItem->base_price : 0;
                $item['total_price'] = $item['unit_price'] * ($item['quantity'] ?? 1);
                OrderItem::create($item);
            }

            \DB::commit();

            return response()->json(['success' => true, 'order_id' => $order->id, 'message' => 'Order created successfully.', 'data' => $order], 201);
        } catch (\Exception $e) {
            \DB::rollBack();

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
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
}
