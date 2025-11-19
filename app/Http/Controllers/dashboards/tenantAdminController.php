<?php

namespace App\Http\Controllers\dashboards;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;

class TenantAdminController extends Controller
{
    /**
     * Display the tenant admin dashboard.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // You can add any data processing here for the dashboard
        $user = auth()->user();
        $totalOrders = 0;
        $completedOrders = 0;
        $canceledOrders = 0;
        $totalRevenue = 0;
        $reviews = collect(); // Initialize as empty collection

        // Try to get actual data if possible
        try {
            $user = auth()->user();
            $tenantId = $user->tenant_id ?? null;

            if ($tenantId) {
                $totalOrders = Order::where('tenant_id', $tenantId)->count();
                $completedOrders = Order::where('tenant_id', $tenantId)->where('status', 'delivered')->count();
                $canceledOrders = Order::where('tenant_id', $tenantId)->whereIn('status', ['cancelled', 'canceled'])->count();
                $totalRevenue = Order::where('tenant_id', $tenantId)->where('status', 'delivered')->sum('total_amount') ?? 0;

                // Get reviews only for the current tenant with proper user relationship
                $reviews = Review::where('tenant_id', $tenantId)
                    ->with([
                        'customer:id,user_id',
                        'customer.user:id,first_name,last_name',
                        'order:id,restaurant_id',
                        'order.restaurant:id,restaurant_name',
                    ])
                    ->latest()
                    ->take(10)
                    ->get();

                // Log for debugging
                \Log::info('Dashboard Reviews Count: '.$reviews->count().' for tenant: '.$tenantId);
            } else {
                \Log::warning('No tenant ID found for user: '.$user->id);
            }
        } catch (\Exception $e) {
            // Log the error and use default values
            \Log::error('Tenant Admin Dashboard Error: '.$e->getMessage());
            $reviews = collect(); // Ensure reviews is always a collection
        }
        // Example: Get dashboard statistics
        $dashboardData = [
            'totalOrders' => $totalOrders,
            'totalDelivered' => $completedOrders,
            'totalCanceled' => $canceledOrders,
            'totalRevenue' => $totalRevenue,
            'reviews' => $reviews,
        ];

        return view('pages.tenant_admin.dashboard.index', compact('dashboardData'));
    }

    /**
     * Get updated dashboard statistics for AJAX requests
     */
    public function getStats()
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id ?? null;

        // Debug the user and tenant info
        \Log::info('TenantAdmin getStats called:', [
            'user_id' => $user ? $user->id : 'No user',
            'user_tenant_id' => $tenantId,
            'user_email' => $user ? $user->email : 'No user',
        ]);

        $totalOrders = 0;
        $completedOrders = 0;
        $canceledOrders = 0;
        $totalRevenue = 0;

        if ($tenantId) {
            try {
                $totalOrders = Order::where('tenant_id', $tenantId)->count();
                $completedOrders = Order::where('tenant_id', $tenantId)->where('status', 'delivered')->count();
                $canceledOrders = Order::where('tenant_id', $tenantId)->whereIn('status', ['cancelled', 'canceled'])->count();
                $totalRevenue = Order::where('tenant_id', $tenantId)->where('status', 'delivered')->sum('total_amount') ?? 0;

            } catch (\Exception $e) {
                \Log::error('Tenant Dashboard Stats Error: '.$e->getMessage());
            }
        }

        return response()->json([
            'totalOrders' => $totalOrders,
            'completedOrders' => $completedOrders,
            'canceledOrders' => $canceledOrders,
            'totalRevenue' => (float) $totalRevenue,
        ]);
    }
}
