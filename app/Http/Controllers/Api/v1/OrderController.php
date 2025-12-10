<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\Api\v1\OrderService;
use Illuminate\Http\Request;

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
     *   "order_number": "ORD1234",
     *   "delivery_address_id": 5,
     *   "payment_method": "cod",
     *   "special_instructions": "Leave at the door",
     *   "order_items": [
     *     {
     *       "item_id": 1,
     *       "item_name": "Pizza Margherita",
     *       "quantity": 2,
     *       "special_instructions": "Extra cheese"
     *     }
     *   ]
     * }
     */
    public function CreateOrder(Request $request)
    {
        $user = auth()->user();

        // Validate request
        $validator = \Validator::make($request->all(), [
            'order_number' => 'required|string|unique:orders,order_number',
            'delivery_address_id' => 'required|exists:customer_addresses,id',
            'payment_method' => 'required|string|in:cod,wallet,upi,card',
            'special_instructions' => 'nullable|string',
            'order_items' => 'required|array|min:1',
            'order_items.*.item_id' => 'required|exists:menu_items,id',
            'order_items.*.item_name' => 'required|string',
            'order_items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $result = $this->orderService->createOrder($validator->validated(), $user);
        $statusCode = $result['status_code'] ?? 200;
        unset($result['status_code']);

        return response()->json($result, $statusCode);
    }

    /**
     * Edit an existing order.
     * Only orders with status: placed, pending, or confirmed can be edited.
     *
     * Example JSON request:
     * {
     *   "order_id": 5,
     *   "special_instructions": "Ring the doorbell twice",
     *   "delivery_address_id": 6,
     *   "order_items": [
     *     {
     *       "item_id": 1,
     *       "item_name": "Pizza Margherita",
     *       "quantity": 3,
     *       "special_instructions": "No olives"
     *     }
     *   ]
     * }
     */
    public function editOrder(Request $request, $id)
    {
        $user = auth()->user();
        // Validate request
        $validator = \Validator::make($request->all(), [
            'special_instructions' => 'nullable|string',
            'delivery_address_id' => 'nullable|exists:customer_addresses,id',
            'order_items' => 'nullable|array|min:1',
            'order_items.*.item_id' => 'required_with:order_items|exists:menu_items,id',
            'order_items.*.item_name' => 'required_with:order_items|string',
            'order_items.*.quantity' => 'required_with:order_items|integer|min:1',
            'order_items.*.special_instructions' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $validated = $request->all();

        $validated = $validator->validated();
        $orderId = $id;

        $result = $this->orderService->editOrder($orderId, $validated, $user);
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

        $result = $this->orderService->getOrderDetails($id);
        $statusCode = $result['status_code'] ?? 200;
        unset($result['status_code']);

        return response()->json($result, $statusCode);
    }
}
