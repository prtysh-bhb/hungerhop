<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\Api\v1\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Create a new order and its items.
     *
     * This method calculates:
     * - Subtotal: Sum of all order items (item price x quantity)
     * - Tax Amount: Calculated from restaurant's tax_percentage
     * - Delivery Fee: Based on distance between restaurant and customer
     * - Platform Fee: From PLATFORM_FEE in .env
     * - Total Amount: subtotal + tax + delivery_fee + platform_fee - discount
     *
     * Example JSON request:
     * {
     *   "delivery_address_id": 5,
     *   "payment_method": "cod",
     *   "special_instructions": "Leave at the door",
     *   "order_items": [
     *     {
     *       "item_id": 1,
     *       "quantity": 2,
     *       "special_instructions": "Extra cheese"
     *     }
     *   ]
     * }
     *
     * Note: order_number is generated automatically
     * Note: item_name is fetched automatically from menu_items table
     */
    public function CreateOrder(Request $request)
    {
        $user = auth()->user();

        // Validate request
        $validator = \Validator::make($request->all(), [
            'delivery_address_id' => 'required|exists:customer_addresses,id',
            'payment_method' => 'required|string|in:cod,wallet,upi,card',
            'special_instructions' => 'nullable|string',
            'order_items' => 'required|array|min:1',
            'order_items.*.item_id' => 'required|exists:menu_items,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.special_instructions' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $result = $this->orderService->createOrder($validator->validated(), $user);
        $statusCode = $result['status_code'] ?? 200;
        unset($result['status_code']);

        return response()->json($result, $statusCode);
    }

    public function editOrder(Request $request, $id)
    {
        $user = auth()->user();
        // Validate request
        $validator = \Validator::make($request->all(), [
            'special_instructions' => 'nullable|string',
            'delivery_address_id' => 'nullable|exists:customer_addresses,id',
            'order_items' => 'nullable|array|min:1',
            'order_items.*.item_id' => 'required_with:order_items|exists:menu_items,id',
            'order_items.*.quantity' => 'required_with:order_items|integer|min:1',
            'order_items.*.special_instructions' => 'nullable|string',
            'order_items.*.coupon_code' => 'nullable|string',
            'coupon_code' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $orderId = $id;
        $result = $this->orderService->editOrder($orderId, $validated, $user);
        $statusCode = $result['status_code'] ?? 200;
        unset($result['status_code']);

        return response()->json($result, $statusCode);
    }

    public function applyCoupon(Request $request, $id)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $couponCode = strtoupper(trim($request->coupon_code));

        $result = $this->orderService->applyCouponToOrder(
            (int) $id,
            $couponCode,
            $user
        );

        $statusCode = $result['status_code'] ?? 200;
        unset($result['status_code']);

        return response()->json($result, $statusCode);
    }

    /**
     * Get checkout details for an order.
     * Returns complete order information with billing breakdown.
     *
     * Example: GET /api/v1/orders/checkout?order_id=5
     */
    public function checkout(Request $request, $id)
    {
        $user = auth()->user();

        $orderId = $id;
        $result = $this->orderService->getCheckout($orderId, $user);
        $statusCode = $result['status_code'] ?? 200;
        unset($result['status_code']);

        return response()->json($result, $statusCode);
    }

    /**
     * List all orders for the authenticated customer.
     * Returns orders with billing details.
     */
    public function listOrders(Request $request)
    {
        $user = auth()->user();

        $result = $this->orderService->listOrders($user);
        $statusCode = $result['status_code'] ?? 200;
        unset($result['status_code']);

        return response()->json($result, $statusCode);
    }

    /**
     * Get details of a specific order.
     * Returns complete order information with billing breakdown.
     */
    public function getOrderDetails(Request $request)
    {
        $id = $request->input('id');

        if (! $id) {
            return response()->json(['success' => false, 'message' => 'Order ID is required'], 422);
        }

        $result = $this->orderService->getOrderDetails($id, $request->user());
        $statusCode = $result['status_code'] ?? 200;
        unset($result['status_code']);

        return response()->json($result, $statusCode);
    }

    public function removeCoupon(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $user = auth()->user();
        $couponCode = strtoupper(trim($request->coupon_code));
        $result = $this->orderService->removeCouponFromOrder((int) $id, $user, $couponCode);

        $statusCode = $result['status_code'] ?? 200;
        unset($result['status_code']);

        return response()->json($result, $statusCode);
    }

    public function cancelOrder(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer|exists:orders,id',
            'cancellation_reason' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = auth()->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $orderId = $request->input('order_id');
        $cancellationReason = $request->input('cancellation_reason');

        $result = $this->orderService->cancelOrder(
            $orderId,
            $user,
            $cancellationReason
        );

        $statusCode = $result['status_code'] ?? 200;
        unset($result['status_code']);

        return response()->json($result, $statusCode);
    }
}
