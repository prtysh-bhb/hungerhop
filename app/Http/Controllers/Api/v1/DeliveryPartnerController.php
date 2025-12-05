<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAssignment;
use App\Models\DeliveryPartner;
use App\Models\Order;
use Illuminate\Http\Request;

class DeliveryPartnerController extends Controller
{
    /**
     * Get all assignments for the delivery partner
     */
    public function myAssignments(Request $request)
    {
        $user = auth()->user();
        if (! $user || $user->role !== 'delivery_partner') {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }
        $partner = DeliveryPartner::where('user_id', $user->id)->first();
        if (! $partner) {
            return response()->json(['success' => false, 'message' => 'No delivery partner profile found for this user.'], 404);
        }
        $assignments = DeliveryAssignment::where('partner_id', $partner->id)
            ->orderByDesc('assigned_at')
            ->get();

        // Get counts for each category
        $newOrdersCount = DeliveryAssignment::where('partner_id', $partner->id)
            ->where('status', 'assigned')
            ->count();
        $inProgressCount = DeliveryAssignment::where('partner_id', $partner->id)
            ->whereIn('status', ['accepted', 'picked_up'])
            ->count();
        $completedCount = DeliveryAssignment::where('partner_id', $partner->id)
            ->whereIn('status', ['delivered', 'cancelled', 'rejected'])
            ->count();

        $result = $assignments->map(function ($assignment) use ($user) {
            return $this->formatAssignment($assignment, $user);
        });

        return response()->json([
            'success' => true,
            'summary' => [
                'total_new_orders' => $newOrdersCount,
                'total_in_progress' => $inProgressCount,
                'total_past_orders' => $completedCount,
                'total_assignments' => $assignments->count(),
            ],
            'data' => $result,
        ]);
    }

    /**
     * Get new orders (status = assigned) - orders waiting for delivery partner to accept
     */
    public function newOrders(Request $request)
    {
        $user = auth()->user();
        if (! $user || $user->role !== 'delivery_partner') {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }
        $partner = DeliveryPartner::where('user_id', $user->id)->first();
        if (! $partner) {
            return response()->json(['success' => false, 'message' => 'No delivery partner profile found for this user.'], 404);
        }

        $assignments = DeliveryAssignment::where('partner_id', $partner->id)
            ->where('status', 'assigned')
            ->orderByDesc('assigned_at')
            ->get();

        $result = $assignments->map(function ($assignment) use ($user) {
            return $this->formatAssignment($assignment, $user);

        });

        return response()->json([
            'success' => true,
            'message' => 'New orders waiting for acceptance',
            'total_count' => $assignments->count(),
            'data' => $result,
        ]);
    }

    /**
     * Get in-progress orders (status = accepted or picked_up) - orders delivery partner is working on
     */
    public function inProgressOrders(Request $request)
    {
        $user = auth()->user();
        if (! $user || $user->role !== 'delivery_partner') {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }
        $partner = DeliveryPartner::where('user_id', $user->id)->first();
        if (! $partner) {
            return response()->json(['success' => false, 'message' => 'No delivery partner profile found for this user.'], 404);
        }

        $assignments = DeliveryAssignment::where('partner_id', $partner->id)
            ->whereIn('status', ['accepted', 'picked_up'])
            ->orderByDesc('accepted_at')
            ->get();

        $result = $assignments->map(function ($assignment) use ($user) {
            return $this->formatAssignment($assignment, $user);
        });

        return response()->json([
            'success' => true,
            'message' => 'Orders currently in progress',
            'total_count' => $assignments->count(),
            'data' => $result,
        ]);
    }

    /**
     * Get past/completed orders (status = delivered, cancelled, rejected)
     */
    public function pastOrders(Request $request)
    {
        $user = auth()->user();
        if (! $user || $user->role !== 'delivery_partner') {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }
        $partner = DeliveryPartner::where('user_id', $user->id)->first();
        if (! $partner) {
            return response()->json(['success' => false, 'message' => 'No delivery partner profile found for this user.'], 404);
        }

        $assignments = DeliveryAssignment::where('partner_id', $partner->id)
            ->whereIn('status', ['delivered', 'cancelled', 'rejected'])
            ->orderByDesc('delivered_at')
            ->get();

        $result = $assignments->map(function ($assignment) use ($user) {
            return $this->formatAssignment($assignment, $user);
        });

        return response()->json([
            'success' => true,
            'message' => 'Past/completed orders',
            'total_count' => $assignments->count(),
            'data' => $result,
        ]);
    }

    /**
     * Get order counts summary for delivery partner dashboard
     */
    public function ordersSummary(Request $request)
    {
        $user = auth()->user();
        if (! $user || $user->role !== 'delivery_partner') {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }
        $partner = DeliveryPartner::where('user_id', $user->id)->first();
        if (! $partner) {
            return response()->json(['success' => false, 'message' => 'No delivery partner profile found for this user.'], 404);
        }

        $newOrdersCount = DeliveryAssignment::where('partner_id', $partner->id)
            ->where('status', 'assigned')
            ->count();

        $inProgressCount = DeliveryAssignment::where('partner_id', $partner->id)
            ->whereIn('status', ['accepted', 'picked_up'])
            ->count();

        $deliveredCount = DeliveryAssignment::where('partner_id', $partner->id)
            ->where('status', 'delivered')
            ->count();

        $cancelledCount = DeliveryAssignment::where('partner_id', $partner->id)
            ->where('status', 'cancelled')
            ->count();

        $rejectedCount = DeliveryAssignment::where('partner_id', $partner->id)
            ->where('status', 'rejected')
            ->count();

        $totalEarnings = DeliveryAssignment::where('partner_id', $partner->id)
            ->where('status', 'delivered')
            ->sum('delivery_fee');

        $totalTips = DeliveryAssignment::where('partner_id', $partner->id)
            ->where('status', 'delivered')
            ->sum('tip_amount');

        return response()->json([
            'success' => true,
            'data' => [
                'new_orders' => $newOrdersCount,
                'in_progress' => $inProgressCount,
                'delivered' => $deliveredCount,
                'cancelled' => $cancelledCount,
                'rejected' => $rejectedCount,
                'total_past_orders' => $deliveredCount + $cancelledCount + $rejectedCount,
                'total_earnings' => round($totalEarnings, 2),
                'total_tips' => round($totalTips, 2),
                'total_income' => round($totalEarnings + $totalTips, 2),
            ],
        ]);
    }

    /**
     * Format assignment data for response
     */
    private function formatAssignment($assignment, $user)
    {
        $order = Order::with(['items.menuItem', 'customer.user', 'deliveryAddress', 'restaurant'])->find($assignment->order_id);
        $customer = $order ? $order->customer : null;
        $customerUser = $customer ? $customer->user : null;
        $customerAddress = $order ? $order->deliveryAddress : null;
        $restaurant = $order ? $order->restaurant : null;

        // Calculate distance between pickup and delivery from assignment table only
        $pickup_latitude = $assignment->pickup_latitude;
        $pickup_longitude = $assignment->pickup_longitude;
        $delivery_latitude = $assignment->delivery_latitude;
        $delivery_longitude = $assignment->delivery_longitude;

        // Calculate distance from restaurant (pickup) to customer (delivery)
        $distance = null;
        if ($pickup_latitude && $pickup_longitude && $delivery_latitude && $delivery_longitude) {
            $distance = $this->calculateDistance(
                $pickup_latitude,
                $pickup_longitude,
                $delivery_latitude,
                $delivery_longitude
            );
        }
        // $distance = 2.36; // temp fix for distance calculation issue

        // Calculate delivery fee based on distance if not already set
        $deliveryFee = (string) $assignment->delivery_fee;
        if (! $deliveryFee && $distance) {
            $deliveryFee = $this->calculateDeliveryFee($distance);
        }

        // Calculate estimated delivery time based on distance
        $estimatedTime = null;
        if ($distance) {
            $estimatedTime = $this->calculateEstimatedDeliveryTime($distance);
        }

        // Format order items
        $orderItems = [];
        if ($order && $order->items) {
            $orderItems = $order->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_id' => $item->item_id,
                    'item_name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'special_instructions' => $item->special_instructions,
                    'menu_item' => $item->menuItem ? [
                        'id' => $item->menuItem->id,
                        'name' => $item->menuItem->item_name ?? $item->menuItem->name,
                        'description' => $item->menuItem->description ?? null,
                        'image' => $item->menuItem->image_url ?? $item->menuItem->image ?? null,
                        'is_vegetarian' => $item->menuItem->is_vegetarian ?? null,
                        'is_active' => $item->menuItem->is_active ?? null,
                        'is_available' => $item->menuItem->is_available ?? null,
                    ] : null,
                ];
            })->toArray();
        }

        return [
            'assignment_id' => $assignment->id,
            'order_id' => $assignment->order_id,
            'order_number' => $order ? $order->order_number : null,
            'status' => $assignment->status,

            // Order dates
            'order_date' => $order ? $order->created_at->format('Y-m-d H:i:s') : null,
            'order_date_formatted' => $order ? $order->created_at->format('d M Y, h:i A') : null,
            'order_payment_status' => $order ? $order->payment_status : null,
            'order_payment_method' => $order ? $order->payment_method : null,

            // Delivery dates
            'delivered_date' => $assignment->delivered_at ? $assignment->delivered_at->format('Y-m-d H:i:s') : null,
            'delivered_date_formatted' => $assignment->delivered_at ? $assignment->delivered_at->format('d M Y, h:i A') : null,

            // Assignment timestamps (formatted)
            'assigned_at' => $assignment->assigned_at ? $assignment->assigned_at->format('Y-m-d H:i:s') : null,
            'assigned_at_formatted' => $assignment->assigned_at ? $assignment->assigned_at->format('d M Y, h:i A') : null,
            'accepted_at' => $assignment->accepted_at ? $assignment->accepted_at->format('Y-m-d H:i:s') : null,
            'accepted_at_formatted' => $assignment->accepted_at ? $assignment->accepted_at->format('d M Y, h:i A') : null,
            'picked_up_at' => $assignment->picked_up_at ? $assignment->picked_up_at->format('Y-m-d H:i:s') : null,
            'picked_up_at_formatted' => $assignment->picked_up_at ? $assignment->picked_up_at->format('d M Y, h:i A') : null,
            'delivered_at' => $assignment->delivered_at ? $assignment->delivered_at->format('Y-m-d H:i:s') : null,

            // Distance and delivery info
            'distance_km' => ($distance) ? round($distance, 2) : null,
            'distance_text' => $distance ? round($distance, 2).' km' : 'N/A',
            'delivery_fee' => (string) $deliveryFee ?? 0,
            'estimated_delivery_time' => $estimatedTime,
            'tip_amount' => $assignment->tip_amount ?? 0,
            
            'estimated_distance_km' => (string) $assignment->estimated_distance_km ?? ($distance ? round($distance, 2) : null),
            'estimated_duration_minutes' => $assignment->estimated_duration_minutes ?? ($estimatedTime ? $this->getEstimatedMinutes($estimatedTime) : null),

            // Delivery partner info
            'delivery_partner_name' => $user->first_name.' '.$user->last_name,

            // Coordinates
            'pickup_location' => [
                'latitude' => $pickup_latitude,
                'longitude' => $pickup_longitude,
            ],
            'delivery_location' => [
                'latitude' => $delivery_latitude,
                'longitude' => $delivery_longitude,
            ],

            // Restaurant details
            'restaurant' => $restaurant ? [
                'id' => $restaurant->id,
                'name' => $restaurant->restaurant_name,
                'address' => $restaurant->address,
                'phone' => $restaurant->phone,
                'latitude' => $restaurant->latitude,
                'longitude' => $restaurant->longitude,
            ] : null,

            // Customer details
            'customer' => $customerUser ? [
                'name' => $customerUser->first_name.' '.$customerUser->last_name,
                'phone' => $customerUser->phone,
            ] : null,

            // Customer address
            'customer_address' => $customerAddress ? [
                'address_line1' => $customerAddress->address_line1,
                'address_line2' => $customerAddress->address_line2,
                'city' => $customerAddress->city,
                'state' => $customerAddress->state,
                'postal_code' => $customerAddress->postal_code,
                'latitude' => $customerAddress->latitude,
                'longitude' => $customerAddress->longitude,
            ] : null,

            // Order items (what was ordered)
            'order_items' => $orderItems,
            'items_count' => count($orderItems),

            // Order summary
            'order_summary' => $order ? [
                'subtotal' => $order->subtotal,
                'tax_amount' => $order->tax_amount,
                'delivery_fee' => (string) $order->delivery_fee,
                'discount_amount' => $order->discount_amount,
                'total_amount' => $order->total_amount,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'special_instructions' => $order->special_instructions,
            ] : null,
        ];
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
        // Assuming average speed of 25 km/h in city traffic
        // Plus 10 minutes for pickup and handover
        $travelTimeMinutes = ($distance / 25) * 60;
        $totalMinutes = ceil($travelTimeMinutes + 10);

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

    public function assignmentDetails(Request $request)
    {
        $user = auth()->user();
        if (! $user || $user->role !== 'delivery_partner') {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }
        $partner = DeliveryPartner::where('user_id', $user->id)->first();
        if (! $partner) {
            return response()->json(['success' => false, 'message' => 'No delivery partner profile found for this user.'], 404);
        }
        $order_id = $request->input('order_id');
        if (! $order_id) {
            return response()->json(['success' => false, 'message' => 'order_id is required.'], 422);
        }
        // Find assignment for this partner and order_id
        $assignment = DeliveryAssignment::where('order_id', $order_id)
            ->where('partner_id', $partner->id)
            ->first();
        if (! $assignment) {
            return response()->json(['success' => false, 'message' => 'Assignment not found for this delivery partner and order.'], 404);
        }

        $result = $this->formatAssignment($assignment, $user);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
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
