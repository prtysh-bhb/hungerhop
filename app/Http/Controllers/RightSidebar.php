<?php

namespace App\Http\Controllers;

use App\Models\DeliveryPartner;
use App\Models\DeliveryPartnerDocument;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\RestaurantDocument;
use App\Models\Tenant;
use App\Models\User;

class RightSidebar extends Controller
{
    /**
     * Get recent activities from multiple tables
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRecentActivities()
    {
        $activities = collect();

        try {
            // Get recent menu categories
            $menuCategories = MenuCategory::orderBy('updated_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    $isRecent = $item->created_at && $item->updated_at ?
                        $item->created_at->diffInMinutes($item->updated_at) < 5 : false;

                    return [
                        'type' => 'menu_category',
                        'message' => 'Menu category "'.$item->name.'" was '.($isRecent ? 'created' : 'updated'),
                        'user' => 'Admin',
                        'time' => $item->updated_at,
                        'border_class' => 'border-primary',
                    ];
                });

            // Get recent menu items
            $menuItems = MenuItem::orderBy('updated_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    $isRecent = $item->created_at && $item->updated_at ?
                        $item->created_at->diffInMinutes($item->updated_at) < 5 : false;

                    return [
                        'type' => 'menu_item',
                        'message' => 'Menu item "'.$item->name.'" was '.($isRecent ? 'added' : 'updated'),
                        'user' => 'Staff',
                        'time' => $item->updated_at,
                        'border_class' => 'border-success',
                    ];
                });

            // Get recent restaurants
            $restaurants = Restaurant::orderBy('updated_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    $isRecent = $item->created_at && $item->updated_at ?
                        $item->created_at->diffInMinutes($item->updated_at) < 5 : false;

                    return [
                        'type' => 'restaurant',
                        'message' => 'Restaurant "'.$item->name.'" was '.($isRecent ? 'registered' : 'updated'),
                        'user' => 'Admin',
                        'time' => $item->updated_at,
                        'border_class' => 'border-info',
                    ];
                });

            // Get recent tenants
            $tenants = Tenant::orderBy('updated_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($item) {
                    $isRecent = $item->created_at && $item->updated_at ?
                        $item->created_at->diffInMinutes($item->updated_at) < 5 : false;

                    return [
                        'type' => 'tenant',
                        'message' => 'Tenant "'.$item->name.'" was '.($isRecent ? 'created' : 'updated'),
                        'user' => 'Super Admin',
                        'time' => $item->updated_at,
                        'border_class' => 'border-warning',
                    ];
                });

            // Get recent users
            $users = User::orderBy('updated_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    $isRecent = $item->created_at && $item->updated_at ?
                        $item->created_at->diffInMinutes($item->updated_at) < 5 : false;

                    return [
                        'type' => 'user',
                        'message' => 'User "'.$item->first_name.' '.$item->last_name.'" was '.($isRecent ? 'registered' : 'updated'),
                        'user' => $item->first_name ?? 'System',
                        'time' => $item->updated_at,
                        'border_class' => 'border-danger',
                    ];
                });

            // Get recent delivery partners
            $deliveryPartners = DeliveryPartner::orderBy('updated_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($item) {
                    $isRecent = $item->created_at && $item->updated_at ?
                        $item->created_at->diffInMinutes($item->updated_at) < 5 : false;

                    return [
                        'type' => 'delivery_partner',
                        'message' => 'Delivery partner "'.($item->user->first_name ?? 'Partner').' '.($item->user->last_name ?? '').'" was '.($isRecent ? 'registered' : 'updated'),
                        'user' => 'Admin',
                        'time' => $item->updated_at,
                        'border_class' => 'border-secondary',
                    ];
                });

            // Get recent restaurant documents
            $restaurantDocuments = RestaurantDocument::with('restaurant:id,restaurant_name')
                ->orderBy('updated_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($item) {
                    $isRecent = $item->created_at && $item->updated_at ?
                        $item->created_at->diffInMinutes($item->updated_at) < 5 : false;

                    $restaurantName = $item->restaurant->restaurant_name ?? 'Restaurant';
                    $action = $isRecent ? 'uploaded' : 'updated';

                    return [
                        'type' => 'restaurant_document',
                        'message' => "Document \"{$item->document_name}\" for {$restaurantName} was {$action}",
                        'user' => 'Admin',
                        'time' => $item->updated_at,
                        'border_class' => 'border-info',
                    ];
                });

            // Get recent delivery partner documents
            $deliveryPartnerDocuments = DeliveryPartnerDocument::with('partner.user:id,first_name,last_name')
                ->orderBy('updated_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($item) {
                    $isRecent = $item->created_at && $item->updated_at ?
                        $item->created_at->diffInMinutes($item->updated_at) < 5 : false;

                    $partnerName = ($item->partner->user->first_name ?? 'Partner').' '.($item->partner->user->last_name ?? '');
                    $action = $isRecent ? 'uploaded' : 'updated';

                    return [
                        'type' => 'delivery_partner_document',
                        'message' => "Document \"{$item->document_name}\" for {$partnerName} was {$action}",
                        'user' => 'Admin',
                        'time' => $item->updated_at,
                        'border_class' => 'border-warning',
                    ];
                });

            // Get recent order status changes
            $orders = Order::orderBy('updated_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    $statusMessages = [
                        'pending' => 'Order #'.$item->id.' is pending confirmation',
                        'confirmed' => 'Order #'.$item->id.' was confirmed',
                        'preparing' => 'Order #'.$item->id.' is being prepared',
                        'ready_for_pickup' => 'Order #'.$item->id.' is ready for pickup',
                        'assigned_to_delivery' => 'Order #'.$item->id.' was assigned for delivery',
                        'out_for_delivery' => 'Order #'.$item->id.' is out for delivery',
                        'delivered' => 'Order #'.$item->id.' was delivered successfully',
                        'canceled' => 'Order #'.$item->id.' was canceled',
                        'rejected' => 'Order #'.$item->id.' was rejected',
                    ];

                    $borderClasses = [
                        'pending' => 'border-warning',
                        'confirmed' => 'border-info',
                        'preparing' => 'border-primary',
                        'ready_for_pickup' => 'border-success',
                        'assigned_to_delivery' => 'border-info',
                        'out_for_delivery' => 'border-primary',
                        'delivered' => 'border-success',
                        'canceled' => 'border-danger',
                        'rejected' => 'border-danger',
                    ];

                    return [
                        'type' => 'order',
                        'message' => $statusMessages[$item->status] ?? 'Order #'.$item->id.' status updated',
                        'user' => 'System',
                        'time' => $item->updated_at,
                        'border_class' => $borderClasses[$item->status] ?? 'border-secondary',
                    ];
                });

            // Combine all activities
            $activities = $activities
                ->concat($menuCategories)
                ->concat($menuItems)
                ->concat($restaurants)
                ->concat($tenants)
                ->concat($users)
                ->concat($deliveryPartners)
                ->concat($restaurantDocuments)
                ->concat($deliveryPartnerDocuments)
                ->concat($orders)
                ->sortByDesc('time') // Sort by most recent first (descending)
                ->take(10)
                ->values(); // Reset collection keys

        } catch (\Exception $e) {
            \Log::error('Right Sidebar Activities Error: '.$e->getMessage());

            // Return default activities if there's an error
            $activities = collect([
                [
                    'type' => 'system',
                    'message' => 'Welcome to the dashboard',
                    'user' => 'System',
                    'time' => now(),
                    'border_class' => 'border-primary',
                ],
            ]);
        }

        return $activities;
    }

    /**
     * Get right sidebar data including activities and order counts
     *
     * @return array
     */
    public function getSidebarData()
    {
        $recentActivities = $this->getRecentActivities();

        // Get order counts (existing functionality)
        $preparing_orders = Order::where('status', 'assigned_to_delivery')->count();
        $out_for_delivery_orders = Order::where('status', 'out_for_delivery')->count();

        return [
            'recent_activities' => $recentActivities,
            'preparing_orders' => $preparing_orders,
            'out_for_delivery_orders' => $out_for_delivery_orders,
        ];
    }
}
