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
        $order = Order::find($assignment->order_id);
        $customer = $order ? $order->customer : null;
        $customerUser = $customer ? $customer->user : null;
        $customerAddress = $order ? $order->deliveryAddress : null;
        $restaurant = $order ? $order->restaurant : null;

        // Calculate distance between pickup and delivery from assignment table only
        $pickup_latitude = $assignment->pickup_latitude;
        $pickup_longitude = $assignment->pickup_longitude;
        $delivery_latitude = $assignment->delivery_latitude;
        $delivery_longitude = $assignment->delivery_longitude;
        $distance = null;
        if ($pickup_latitude && $pickup_longitude && $delivery_latitude && $delivery_longitude) {
            $distance = $this->calculateDistance(
                $pickup_latitude,
                $pickup_longitude,
                $delivery_latitude,
                $delivery_longitude
            );
        }

        return [
            'assignment_id' => $assignment->id,
            'order_id' => $assignment->order_id,
            'order_number' => $order ? $order->order_number : null,
            'status' => $assignment->status,
            'assigned_at' => $assignment->assigned_at,
            'accepted_at' => $assignment->accepted_at,
            'picked_up_at' => $assignment->picked_up_at,
            'delivered_at' => $assignment->delivered_at,
            'delivery_fee' => $assignment->delivery_fee,
            'tip_amount' => $assignment->tip_amount,
            'estimated_distance_km' => $assignment->estimated_distance_km,
            'estimated_duration_minutes' => $assignment->estimated_duration_minutes,
            'delivery_partner_name' => $user->first_name.' '.$user->last_name,
            'pickup_latitude' => $pickup_latitude,
            'pickup_longitude' => $pickup_longitude,
            'delivery_latitude' => $delivery_latitude,
            'delivery_longitude' => $delivery_longitude,
            'distance_pickup_to_delivery_km' => $distance ? round($distance, 2) : null,
            'restaurant' => $restaurant ? [
                'id' => $restaurant->id,
                'name' => $restaurant->restaurant_name,
                'address' => $restaurant->address,
                'phone' => $restaurant->phone,
            ] : null,
            'customer' => $customerUser ? [
                'name' => $customerUser->first_name.' '.$customerUser->last_name,
                'phone' => $customerUser->phone,
            ] : null,
            'customer_address' => $customerAddress ? [
                'address_line1' => $customerAddress->address_line1,
                'address_line2' => $customerAddress->address_line2,
                'city' => $customerAddress->city,
                'state' => $customerAddress->state,
                'postal_code' => $customerAddress->postal_code,
            ] : null,
            'order_details' => $order ? [
                'total_amount' => $order->total_amount,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'special_instructions' => $order->special_instructions,
            ] : null,
        ];
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
