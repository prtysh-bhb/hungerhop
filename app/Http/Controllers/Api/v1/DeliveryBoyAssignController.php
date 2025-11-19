<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAssignment;
use App\Models\DeliveryPartner;
use App\Models\DeliveryZone;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeliveryBoyAssignController extends Controller
{
    // Assign a delivery boy to an order
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|integer',
        ]);

        // Load order with related restaurant + delivery address
        $order = Order::with(['deliveryAddress', 'restaurant'])->find($validated['order_id']);

        if (! $order || ! $order->deliveryAddress || ! $order->restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Order, restaurant, or delivery address not found',
            ], 404);
        }

        $customer_lat = (float) $order->deliveryAddress->latitude;
        $customer_lng = (float) $order->deliveryAddress->longitude;
        $restaurant_lat = (float) $order->restaurant->latitude;
        $restaurant_lng = (float) $order->restaurant->longitude;

        // Validate coordinates
        if (abs($customer_lat) > 90 || abs($customer_lng) > 180 ||
            abs($restaurant_lat) > 90 || abs($restaurant_lng) > 180) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coordinates detected',
            ], 422);
        }

        Log::info("Customer Lat/Lng: {$customer_lat}, {$customer_lng}");
        Log::info("Restaurant Lat/Lng: {$restaurant_lat}, {$restaurant_lng}");

        // **NEW: Check delivery zones first**
        $deliveryZone = DeliveryZone::getDeliveryZoneForLocation(
            $customer_lat,
            $customer_lng,
            $order->restaurant_id,
            $order->tenant_id
        );

        if (! $deliveryZone) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery not available to this location. The address is outside our delivery zones.',
            ], 422);
        }

        // Use zone-based delivery fee and estimated time
        $deliveryFee = $deliveryZone->delivery_fee;
        $estimatedDeliveryTime = $deliveryZone->estimated_delivery_time;

        Log::info("Delivery Zone: {$deliveryZone->zone_name}, Fee: {$deliveryFee}");

        // Calculate distance between restaurant and customer for delivery partner assignment
        $restaurantToCustomerDistance = $this->calculateDistance(
            $restaurant_lat, $restaurant_lng,
            $customer_lat, $customer_lng
        );

        Log::info("Restaurant to Customer distance: {$restaurantToCustomerDistance} km");

        if ($restaurantToCustomerDistance > 15) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery destination is too far. Maximum allowed distance is 15km.',
                'distance_km' => round($restaurantToCustomerDistance, 2),
            ], 422);
        }

        // Find nearest delivery partner
        $partners = DeliveryPartner::where('is_available', true)->where('is_online', true)->get();

        $nearest = null;
        $minDistance = null;

        foreach ($partners as $partner) {
            if ($partner->current_latitude !== null && $partner->current_longitude !== null) {
                $partner_lat = (float) $partner->current_latitude;
                $partner_lng = (float) $partner->current_longitude;

                // Calculate distance from delivery partner to customer address
                $distance = $this->calculateDistance(
                    $customer_lat, $customer_lng,
                    $partner_lat, $partner_lng
                );

                Log::info("Partner {$partner->id} distance to customer: {$distance} km");

                if ($minDistance === null || $distance < $minDistance) {
                    $minDistance = $distance;
                    $nearest = $partner;
                }
            }
        }

        if (! $nearest) {
            return response()->json([
                'success' => false,
                'message' => 'No available delivery partner found',
            ], 404);
        }
        try {
            // Assign order to nearest delivery partner
            DeliveryAssignment::create([
                'order_id' => $order->id,
                'tenant_id' => $order->tenant_id ?? null,
                'partner_id' => $nearest->id,
                'pickup_latitude' => $restaurant_lat,
                'pickup_longitude' => $restaurant_lng,
                'delivery_latitude' => $customer_lat,
                'delivery_longitude' => $customer_lng,
                'status' => 'assigned',
                'assigned_at' => now(),
            ]);

            $order->status = 'assigned_to_delivery';
            $order->delivery_fee = $deliveryFee; // Use zone-based delivery fee

            // Calculate total amount properly: subtotal + tax_amount + delivery_fee - discount_amount
            $subtotal = $order->subtotal ?? 0;
            $taxAmount = $order->tax_amount ?? 0;
            $discountAmount = $order->discount_amount ?? 0;

            $order->total_amount = $subtotal + $taxAmount + $deliveryFee - $discountAmount;

            $order->save();

        } catch (\Exception $e) {
            Log::error('Failed to assign order: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign order.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order assigned to nearest delivery partner.',
            'data' => [
                'order_id' => $validated['order_id'],
                'delivery_partner_id' => $nearest->id,
                'delivery_partner_name' => $nearest->user->first_name.' '.$nearest->user->last_name,
                'distance_km' => round($restaurantToCustomerDistance, 2),
                'delivery_fee' => $deliveryFee,
                'total_amount' => $order->total_amount,
                'delivery_zone' => [
                    'id' => $deliveryZone->id,
                    'name' => $deliveryZone->zone_name,
                    'estimated_delivery_time' => $estimatedDeliveryTime,
                ],
            ],
        ]);
    }

    // Accept assignment
    public function acceptAssignment(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|integer',
        ]);

        $delivery_boy_id = auth()->id();
        $delivery_partner = DeliveryPartner::where('user_id', $delivery_boy_id)->first();
        $order = Order::find($validated['order_id']);
        try {
            if (! $order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found.',
                ], 404);
            }
            // Check if order is already accepted by someone else
            $alreadyAccepted = DeliveryAssignment::where('order_id', $order->id)
                ->where('status', 'accepted')
                ->first();
            if ($alreadyAccepted) {
                if ($alreadyAccepted->partner_id != $delivery_partner->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Order already accepted by another delivery partner.',
                    ], 403);
                } else {
                    return response()->json([
                        'success' => true,
                        'message' => 'Assignment already accepted by you.',
                        'data' => [
                            'order_id' => $validated['order_id'],
                            'delivery_boy_id' => $delivery_boy_id,
                        ],
                    ]);
                }
            }
            // Accept if assignment_status is not already accepted/rejected
            DeliveryAssignment::create([
                'order_id' => $order->id,
                'tenant_id' => $order->tenant_id ?? null,
                'partner_id' => $delivery_partner->id ?? null,
                'pickup_latitude' => $order->restaurant->latitude ?? null,
                'pickup_longitude' => $order->restaurant->longitude ?? null,
                'delivery_latitude' => $order->deliveryAddress->latitude ?? null,
                'delivery_longitude' => $order->deliveryAddress->longitude ?? null,
                'status' => 'accepted',
                'accepted_at' => now(),
                'assigned_at' => $order->assigned_at ?? now(),
            ]);
            Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);

            return response()->json([
                'success' => true,
                'message' => 'Assignment accepted',
                'data' => [
                    'order_id' => $validated['order_id'],
                    'delivery_boy_id' => $delivery_boy_id,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Accept assignment failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to accept assignment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Reject assignment
    public function rejectAssignment(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|integer',
            'reason' => 'nullable|string',
        ]);

        $delivery_boy_id = auth()->id();
        $delivery_partner = DeliveryPartner::where('user_id', $delivery_boy_id)->first();
        $order = Order::with('deliveryAddress')->find($validated['order_id']);
        if ($order) {
            $deliveryAddress = $order->deliveryAddress;
            try {
                DeliveryAssignment::create([
                    'order_id' => $order->id,
                    'tenant_id' => $order->tenant_id ?? null,
                    'partner_id' => $delivery_partner->id ?? null,
                    'pickup_latitude' => $order->restaurant->latitude ?? null,
                    'pickup_longitude' => $order->restaurant->longitude ?? null,
                    'delivery_latitude' => $deliveryAddress ? $deliveryAddress->latitude : null,
                    'delivery_longitude' => $deliveryAddress ? $deliveryAddress->longitude : null,
                    'status' => 'rejected',
                    'rejection_reason' => $validated['reason'] ?? null,
                    'assigned_at' => $order->assigned_at ?? now(),
                ]);
            } catch (\Exception $e) {
                Log::error('DeliveryAssignment insert failed: '.$e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Assignment rejected, but failed to log assignment.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Assignment rejected',
        ]);
    }

    /**
     * Check delivery availability for a location before order placement
     */
    public function checkDeliveryAvailability(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'restaurant_id' => 'required|integer|exists:restaurants,id',
            'tenant_id' => 'nullable|integer|exists:tenants,id',
        ]);

        $latitude = $validated['latitude'];
        $longitude = $validated['longitude'];
        $restaurantId = $validated['restaurant_id'];
        $tenantId = $validated['tenant_id'] ?? null;

        try {
            $zone = DeliveryZone::getDeliveryZoneForLocation($latitude, $longitude, $restaurantId, $tenantId);

            if (! $zone) {
                return response()->json([
                    'success' => false,
                    'available' => false,
                    'message' => 'Delivery not available to this location',
                ]);
            }

            return response()->json([
                'success' => true,
                'available' => true,
                'data' => [
                    'zone_id' => $zone->id,
                    'zone_name' => $zone->zone_name,
                    'delivery_fee' => $zone->delivery_fee,
                    'minimum_order_amount' => $zone->minimum_order_amount,
                    'estimated_delivery_time' => $zone->estimated_delivery_time,
                ],
                'message' => 'Delivery available to this location',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to check delivery availability: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to check delivery availability',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Find nearest delivery partner to the order's customer address
    public function findNearestPartner(Request $request)
    {
        // Keep this method as is for backward compatibility
        // This method is not used in the new zone-based flow
    }

    // Helper function to calculate distance between two lat/lng points (Haversine formula)
    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    // Helper function to calculate delivery fee based on distance (kept as fallback)
    private function calculateDeliveryFee($distance)
    {
        if ($distance <= 3) {
            return 20; // ₹20 for 0-3km
        } elseif ($distance <= 6) {
            return 40; // ₹40 for 3-6km
        } elseif ($distance <= 10) {
            return 60; // ₹60 for 6-10km
        } else {
            // For distances > 10km but <= 15km, you can set a higher fee
            // or use a formula. For now, I'll set it to ₹80
            return 80; // ₹80 for 10-15km
        }
    }
}
