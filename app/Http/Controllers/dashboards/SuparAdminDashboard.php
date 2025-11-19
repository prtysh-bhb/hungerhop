<?php

namespace App\Http\Controllers\dashboards;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class SuparAdminDashboard extends Controller
{
    // Dashboard for Super Admin

    public function index()
    {
        // Get recent reviews with customer and user data (first_name, last_name)
        $reviews = Review::with(['customer.user:id,first_name,last_name'])->get();

        return view('pages.super_admin.dashboard.index', [
            'totalOrders' => Order::count(),
            'completedOrders' => Order::where('status', 'delivered')->count(),
            'canceledOrders' => Order::where('status', 'canceled')->count(),
            'totalRevenue' => Order::where('status', 'delivered')->sum('total_amount'),
            'reviews' => $reviews,
        ]);
    }

    /**
     * Get updated dashboard statistics for AJAX requests
     */
    public function getStats()
    {
        return response()->json([
            'totalOrders' => Order::count(),
            'completedOrders' => Order::where('status', 'delivered')->count(),
            'canceledOrders' => Order::where('status', 'cancelled')->count(),
            'totalRevenue' => number_format((float) Order::where('status', 'delivered')->sum('total_amount'), 2),
        ]);
    }

    /**
     * Get recent orders for real-time updates
     */
    public function getRecentOrders(Request $request)
    {
        $limit = $request->get('limit', 5);

        $orders = Order::with(['customer.user:id,first_name,last_name', 'restaurant:id,name'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer && $order->customer->user ?
                        $order->customer->user->first_name.' '.$order->customer->user->last_name :
                        'Guest Customer',
                    'restaurant_name' => $order->restaurant ? $order->restaurant->name : 'Unknown Restaurant',
                    'status' => $order->status,
                    'total_amount' => number_format((float) $order->total_amount, 2),
                    'created_at' => $order->created_at->diffForHumans(),
                ];
            });

        return response()->json($orders);
    }
}
