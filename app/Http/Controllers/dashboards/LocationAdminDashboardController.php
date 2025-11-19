<?php

namespace App\Http\Controllers\dashboards;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Review;
use App\Models\User;

class LocationAdminDashboardController extends Controller
{
    // Dashboard for Location Admin

    public function index()
    {
        // Simple approach with hard-coded values for testing
        $totalOrders = 0;
        $completedOrders = 0;
        $canceledOrders = 0;
        $totalRevenue = 0;

        // Try to get actual data if possible
        try {
            $user = auth()->user();
            $restaurantIds = [];

            // Get restaurants based on user role
            if ($user->role === 'location_admin') {
                // For location_admin: get restaurants they directly manage
                $restaurantIds = Restaurant::where('location_admin_id', $user->id)
                    ->pluck('id')
                    ->toArray();
            } elseif ($user->role === 'tenant_admin') {
                // For tenant_admin: get all restaurants in their tenant
                $restaurantIds = Restaurant::where('tenant_id', $user->tenant_id)
                    ->pluck('id')
                    ->toArray();
            }

            if (! empty($restaurantIds)) {
                // Get orders for restaurants managed by this admin
                $totalOrders = Order::whereIn('restaurant_id', $restaurantIds)->count();
                $completedOrders = Order::whereIn('restaurant_id', $restaurantIds)->where('status', 'delivered')->count();
                $canceledOrders = Order::whereIn('restaurant_id', $restaurantIds)->where('status', 'canceled')->count();
                $totalRevenue = Order::whereIn('restaurant_id', $restaurantIds)->where('status', 'delivered')->sum('total_amount') ?? 0;

                // Get reviews only for restaurants managed by this admin
                // This handles both order-based reviews and direct restaurant reviews
                $reviews = Review::where(function ($query) use ($restaurantIds) {
                    // Reviews through orders
                    $query->whereHas('order', function ($orderQuery) use ($restaurantIds) {
                        $orderQuery->whereIn('restaurant_id', $restaurantIds);
                    })
                    // OR direct restaurant reviews
                        ->orWhere(function ($directQuery) use ($restaurantIds) {
                            $directQuery->where('reviewable_type', 'App\\Models\\Restaurant')
                                ->whereIn('reviewable_id', $restaurantIds);
                        });
                })
                    ->with([
                        'customer:id,user_id',
                        'customer.user:id,first_name,last_name',
                        'order:id,restaurant_id',
                        'order.restaurant:id,restaurant_name',
                        'reviewable', // Load the reviewable model (restaurant)
                    ])
                    ->latest()
                    ->take(10)
                    ->get();

                // Log for debugging
                \Log::info('Location Admin Dashboard - Role: '.$user->role.', Restaurants managed: '.count($restaurantIds).', Reviews found: '.$reviews->count().' for user: '.$user->id);
            } else {
                \Log::warning('No restaurants found for user: '.$user->id.' (Role: '.$user->role.')');
                $reviews = collect([]);
            }
        } catch (\Exception $e) {
            // Log the error and use default values
            \Log::error('Location Admin Dashboard Error: '.$e->getMessage());
            $reviews = collect([]);
        }

        //  $reviews = Review::with(['customer.user:id,first_name,last_name'])->get();
        // Ensure variables are always defined
        return view('pages.location_admin.dashboard.index', [
            'totalOrders' => $totalOrders,
            'completedOrders' => $completedOrders,
            'canceledOrders' => $canceledOrders,
            'totalRevenue' => $totalRevenue,
            'reviews' => $reviews ?? collect([]),
        ]);
    }
}
