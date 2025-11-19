<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAssignment;
use App\Models\DeliveryPartner;
use App\Models\Order;
use Illuminate\Http\Request;

// This is the dummy controller for delivery partners here delivery partner getting the lontitude and latitude from the two table
// delivery_partner and customer_address
// and counts a distance

class Dummy_DeliveryPartnerController extends Controller
{
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
        $result = $assignments->map(function ($assignment) use ($partner, $user) {
            $order = Order::find($assignment->order_id);
            $customer = $order ? $order->customer : null;
            $customerUser = $customer ? $customer->user : null;
            $customerAddress = $order ? $order->deliveryAddress : null;
            // Calculate distance between delivery partner and customer address
            $distance = null;
            if ($partner->current_latitude && $partner->current_longitude && $customerAddress && $customerAddress->latitude && $customerAddress->longitude) {
                //   dd($partner->user->first_name, $partner->current_latitude, $partner->current_longitude, $customerUser->first_name, $customerUser->last_name, $customerAddress->latitude, $customerAddress->longitude);
                $distance = $this->calculateDistance(
                    $partner->current_latitude,
                    $partner->current_longitude,
                    $customerAddress->latitude,
                    $customerAddress->longitude
                );
            }

            return [
                'assignment_id' => $assignment->id,
                'order_id' => $assignment->order_id,
                'status' => $assignment->status,
                'assigned_at' => $assignment->assigned_at,
                'delivery_partner_name' => $user->first_name.' '.$user->last_name,
                'distance_to_customer_km' => $distance ? round($distance, 2) : null,
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
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
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
        $order = Order::find($order_id);
        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }
        // Get customer info from order
        $customer = $order->customer;
        $customerUser = $customer ? $customer->user : null;
        $customerAddress = $order->deliveryAddress;
        $distance = null;
        if ($partner->current_latitude && $partner->current_longitude && $customerAddress && $customerAddress->latitude && $customerAddress->longitude) {

            $distance = $this->calculateDistance(
                $partner->current_latitude,
                $partner->current_longitude,
                $customerAddress->latitude,
                $customerAddress->longitude
            );
        }
        $result = [
            'assignment_id' => $assignment->id,
            'order_id' => $assignment->order_id,
            'status' => $assignment->status,
            'assigned_at' => $assignment->assigned_at,
            'delivery_partner_name' => $user->first_name.' '.$user->last_name,
            'distance_to_customer_km' => $distance ? round($distance, 2) : null,
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
        ];

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
