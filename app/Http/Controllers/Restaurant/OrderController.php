<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Restaurant;
use app\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function ShowList(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;
        $restaurantIds = Restaurant::where('tenant_id', $tenantId)->pluck('id');
        // Get all orders with relations
        $orders = Order::with(['customer.user', 'restaurant', 'deliveryAddress'])->whereIn('restaurant_id', $restaurantIds)->get();

        return view('pages.restaurant_staff.order.index', compact('orders'));
    }

    /**
     * Update the status of an order.
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate([
            'status' => 'required|string',
        ]);
        $order->status = $request->input('status');
        $order->save();

        // Insert into order_statuses table
        OrderStatus::create([
            'order_id' => $order->id,
            'status' => $order->status,
        ]);

        return redirect()->route('restaurant.order.details', $order->id)
            ->with('success', 'Order status updated successfully.');
    }

    public function ShowDetails($id)
    {
        $order = Order::with([
            'restaurant',
            'customer.user',
            'deliveryAddress',
            'items.menuItem.category',
        ])->findOrFail($id);

        return view('pages.restaurant_staff.order.show', compact('order'));
    }

    // Remove duplicate and misplaced method, and define getDeliveryPartnerForOrder only once outside of ShowDetails

    /**
     * Get the delivery partner user for a given order (or null if not assigned).
     */
    public static function getDeliveryPartnerForOrder($orderId)
    {
        $deliveryAssignment = \App\Models\DeliveryAssignment::where('order_id', $orderId)->first();
        if (! $deliveryAssignment) {
            return null;
        }
        $deliveryPartner = \App\Models\DeliveryPartner::find($deliveryAssignment->partner_id);
        if (! $deliveryPartner) {
            return null;
        }
        $user = $deliveryPartner->user;
        if (! $user) {
            return null;
        }

        return [
            'user' => $user,
            'partner' => $deliveryPartner,
            'assignment' => $deliveryAssignment,
        ];
    }
}
