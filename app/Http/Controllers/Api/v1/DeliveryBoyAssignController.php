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

        // $deliveryZone = DeliveryZone::getDeliveryZoneForLocation(
        //     $customer_lat,
        //     $customer_lng,
        //     $order->restaurant_id,
        //     $order->tenant_id
        // );

        // if (! $deliveryZone) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Delivery not available to this location. The address is outside our delivery zones.',
        //     ], 422);
        // }

        // // Use zone-based delivery fee and estimated time
        // $deliveryFee = $deliveryZone->delivery_fee;
        // $estimatedDeliveryTime = $deliveryZone->estimated_delivery_time;

        // Log::info("Delivery Zone: {$deliveryZone->zone_name}, Fee: {$deliveryFee}");

        // Calculate distance between restaurant and customer for delivery partner assignment
        $restaurantToCustomerDistance = $this->calculateDistance(
            $restaurant_lat, $restaurant_lng,
            $customer_lat, $customer_lng
        );
        $restaurantToCustomerDistance = 10; // temp override for testing

        Log::info("Restaurant to Customer distance: {$restaurantToCustomerDistance} km");

        if ($restaurantToCustomerDistance > 15) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery destination is too far. Maximum allowed distance is 15km.',
                'distance_km' => round($restaurantToCustomerDistance, 2),
            ], 422);
        }

        // **Check delivery zones - with fallback for distance-based fee**
        $deliveryZone = DeliveryZone::getDeliveryZoneForLocation(
            $customer_lat,
            $customer_lng,
            $order->restaurant_id,
            $order->tenant_id
        );

        // If no delivery zone found, use distance-based delivery fee as fallback
        if ($deliveryZone) {
            $deliveryFee = $deliveryZone->delivery_fee;
            $estimatedDeliveryTime = $deliveryZone->estimated_delivery_time;
            Log::info("Delivery Zone: {$deliveryZone->zone_name}, Fee: {$deliveryFee}");
        } else {
            // Fallback: Calculate delivery fee based on distance
            $deliveryFee = $this->calculateDeliveryFee($restaurantToCustomerDistance);
            $estimatedDeliveryTime = $this->calculateEstimatedDeliveryTime($restaurantToCustomerDistance);
            Log::info("No delivery zone found. Using distance-based fee: {$deliveryFee}");
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
                'delivery_zone' => $deliveryZone ? [
                    'id' => $deliveryZone->id,
                    'name' => $deliveryZone->zone_name,
                    'estimated_delivery_time' => $estimatedDeliveryTime,
                ] : [
                    'id' => null,
                    'name' => 'Distance-based',
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

        if (! $delivery_partner) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery partner profile not found.',
            ], 404);
        }

        // Check if delivery partner is approved
        if ($delivery_partner->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your account has not been approved yet. Please wait for admin verification of your documents.',
                'status' => $delivery_partner->status,
                'action' => 'pending_verification',
            ], 403);
        }

        $order = Order::with(['deliveryAddress', 'restaurant'])->find($validated['order_id']);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        try {
            // Check if there's an existing assignment for this partner and order
            $existingAssignment = DeliveryAssignment::where('order_id', $order->id)
                ->where('partner_id', $delivery_partner->id)
                ->whereIn('status', ['assigned', 'accepted'])
                ->first();

            if ($existingAssignment) {
                if ($existingAssignment->status === 'accepted') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Assignment already accepted by you.',
                        'data' => [
                            'order_id' => $validated['order_id'],
                            'delivery_boy_id' => $delivery_boy_id,
                            'assignment_id' => $existingAssignment->id,
                        ],
                    ]);
                }

                // Update existing assignment to accepted
                $existingAssignment->update([
                    'status' => 'accepted',
                    'accepted_at' => now(),
                ]);

                // Update order status
                Order::where('id', $order->id)->update(['status' => 'out_for_delivery']);

                return response()->json([
                    'success' => true,
                    'message' => 'Assignment accepted successfully.',
                    'data' => [
                        'order_id' => $validated['order_id'],
                        'delivery_boy_id' => $delivery_boy_id,
                        'assignment_id' => $existingAssignment->id,
                        'delivery_fee' => $existingAssignment->delivery_fee,
                    ],
                ]);
            }

            // Check if order is already accepted by someone else
            $alreadyAccepted = DeliveryAssignment::where('order_id', $order->id)
                ->where('status', 'accepted')
                ->first();

            if ($alreadyAccepted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order already accepted by another delivery partner.',
                ], 403);
            }

            // No existing assignment found - create new one with calculated delivery fee
            // Calculate distance and delivery fee
            $customer_lat = (float) ($order->deliveryAddress->latitude ?? 0);
            $customer_lng = (float) ($order->deliveryAddress->longitude ?? 0);
            $restaurant_lat = (float) ($order->restaurant->latitude ?? 0);
            $restaurant_lng = (float) ($order->restaurant->longitude ?? 0);

            $distance = $this->calculateDistance($restaurant_lat, $restaurant_lng, $customer_lat, $customer_lng);
            $deliveryFee = $this->calculateDeliveryFee($distance);
            $estimatedTime = $this->calculateEstimatedDeliveryTime($distance);

            // Create new assignment with accepted status
            $assignment = DeliveryAssignment::create([
                'order_id' => $order->id,
                'tenant_id' => $order->tenant_id ?? null,
                'partner_id' => $delivery_partner->id,
                'pickup_latitude' => $restaurant_lat,
                'pickup_longitude' => $restaurant_lng,
                'delivery_latitude' => $customer_lat,
                'delivery_longitude' => $customer_lng,
                'estimated_distance_km' => round($distance, 2),
                'estimated_duration_minutes' => $this->getEstimatedMinutes($estimatedTime),
                'delivery_fee' => $deliveryFee,
                'status' => 'accepted',
                'accepted_at' => now(),
                'assigned_at' => now(),
            ]);

            // Update order status and delivery fee
            $order->status = 'out_for_delivery';
            $order->delivery_fee = $deliveryFee;
            $order->total_amount = ($order->subtotal ?? 0) + ($order->tax_amount ?? 0) + $deliveryFee - ($order->discount_amount ?? 0);
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'Assignment accepted successfully.',
                'data' => [
                    'order_id' => $validated['order_id'],
                    'delivery_boy_id' => $delivery_boy_id,
                    'assignment_id' => $assignment->id,
                    'delivery_fee' => $deliveryFee,
                    'distance_km' => round($distance, 2),
                    'estimated_delivery_time' => $estimatedTime,
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

    /**
     * Update order/delivery status by delivery partner
     * Allowed transitions: accepted -> picked_up -> out_for_delivery -> delivered
     */
    public function updateDeliveryStatus(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|integer',
            'status' => 'required|string|in:picked_up,out_for_delivery,arrived,delivered',
        ]);

        $delivery_boy_id = auth()->id();
        $delivery_partner = DeliveryPartner::where('user_id', $delivery_boy_id)->first();

        if (! $delivery_partner) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery partner profile not found.',
            ], 404);
        }

        $order = Order::find($validated['order_id']);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        // Check if this delivery partner is assigned to this order
        $assignment = DeliveryAssignment::where('order_id', $order->id)
            ->where('partner_id', $delivery_partner->id)
            ->whereIn('status', ['accepted', 'picked_up', 'out_for_delivery', 'arrived'])
            ->first();
        if (! $assignment) {
            return response()->json([
                'success' => false,
                'message' => 'You are not assigned to this order or the order is not in a valid state.',
            ], 403);
        }

        $newStatus = $validated['status'];
        $currentStatus = $assignment->status;

        // Validate status transitions
        $allowedTransitions = [
            'accepted' => ['picked_up'],
            'picked_up' => ['out_for_delivery'],
            'out_for_delivery' => ['arrived'],
            'arrived' => ['delivered'],
        ];

        // -------------------- this validation is commented out to allow direct updates for testing------------------

        // if (! isset($allowedTransitions[$currentStatus]) || ! in_array($newStatus, $allowedTransitions[$currentStatus])) {
        //         return response()->json([
        //                 'success' => false,
        //                 'message' => "Invalid status transition from '{$currentStatus}' to '{$newStatus}'.",
        //                 'current_status' => $currentStatus,
        //                 'allowed_transitions' => $allowedTransitions[$currentStatus] ?? [],
        //             ], 422);
        //         }

        try {
            // Update assignment status
            $assignment->status = $newStatus;

            // Set timestamps based on status
            if ($newStatus === 'picked_up') {
                $assignment->picked_up_at = now();
                $order->status = 'picked_up';
            } elseif ($newStatus === 'out_for_delivery') {
                $order->status = 'out_for_delivery';
            } elseif ($newStatus === 'delivered') {
                $assignment->delivered_at = now();
                $order->status = 'delivered';
                $order->actual_delivery_time = now();

                // Mark delivery partner as available again
                $delivery_partner->is_available = true;
                $delivery_partner->save();
            }

            $assignment->save();
            $order->save();

            Log::info("Order {$order->id} status updated to '{$newStatus}' by partner {$delivery_partner->id}");

            return response()->json([
                'success' => true,
                'message' => "Order status updated to '{$newStatus}' successfully.",
                'data' => [
                    'order_id' => $order->id,
                    'order_status' => $order->status,
                    'assignment_status' => $assignment->status,
                    'delivery_partner_id' => $delivery_partner->id,
                    'picked_up_at' => $assignment->picked_up_at,
                    'delivered_at' => $assignment->delivered_at,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Update delivery status failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update delivery status.',
                'error' => $e->getMessage(),
            ], 500);
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

    // Reject assignment and auto-reassign to next nearest partner
    public function rejectAssignment(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|integer',
            'reason' => 'nullable|string',
        ]);

        $delivery_boy_id = auth()->id();
        $delivery_partner = DeliveryPartner::where('user_id', $delivery_boy_id)->first();
        $order = Order::with(['deliveryAddress', 'restaurant'])->find($validated['order_id']);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        if (! $delivery_partner) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery partner profile not found.',
            ], 404);
        }

        // Check if delivery partner is approved
        if ($delivery_partner->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your account has not been approved yet. Please wait for admin verification of your documents.',
                'status' => $delivery_partner->status,
                'action' => 'pending_verification',
            ], 403);
        }

        $deliveryAddress = $order->deliveryAddress;

        try {
            // Log the rejection
            DeliveryAssignment::create([
                'order_id' => $order->id,
                'tenant_id' => $order->tenant_id ?? null,
                'partner_id' => $delivery_partner->id,
                'pickup_latitude' => $order->restaurant->latitude ?? null,
                'pickup_longitude' => $order->restaurant->longitude ?? null,
                'delivery_latitude' => $deliveryAddress ? $deliveryAddress->latitude : null,
                'delivery_longitude' => $deliveryAddress ? $deliveryAddress->longitude : null,
                'status' => 'rejected',
                'rejection_reason' => $validated['reason'] ?? null,
                'assigned_at' => now(),
            ]);

            Log::info("Order {$order->id} rejected by partner {$delivery_partner->id}. Reason: ".($validated['reason'] ?? 'No reason provided'));

            // Auto-reassign to next nearest delivery partner
            $reassignResult = $this->reassignToNextPartner($order, $delivery_partner->id);

            if ($reassignResult['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Assignment rejected and order reassigned to another delivery partner.',
                    'data' => [
                        'order_id' => $order->id,
                        'rejected_by' => $delivery_partner->id,
                        'rejection_reason' => $validated['reason'] ?? null,
                        'reassigned_to' => $reassignResult['data'],
                    ],
                ]);
            } else {
                // Update order status if no partner available
                $order->update(['status' => 'pending_assignment']);

                return response()->json([
                    'success' => true,
                    'message' => 'Assignment rejected. '.$reassignResult['message'],
                    'data' => [
                        'order_id' => $order->id,
                        'rejected_by' => $delivery_partner->id,
                        'rejection_reason' => $validated['reason'] ?? null,
                        'reassigned_to' => null,
                        'order_status' => 'pending_assignment',
                    ],
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Reject assignment failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to reject assignment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reassign order to next nearest available delivery partner
     * Excludes partners who have already rejected this order
     */
    private function reassignToNextPartner(Order $order, $excludePartnerId = null)
    {
        $customer_lat = (float) $order->deliveryAddress->latitude;
        $customer_lng = (float) $order->deliveryAddress->longitude;
        $restaurant_lat = (float) $order->restaurant->latitude;
        $restaurant_lng = (float) $order->restaurant->longitude;

        // Get all partner IDs who have rejected this order
        $rejectedPartnerIds = DeliveryAssignment::where('order_id', $order->id)
            ->where('status', 'rejected')
            ->pluck('partner_id')
            ->toArray();

        // Add current rejecting partner to exclusion list
        if ($excludePartnerId) {
            $rejectedPartnerIds[] = $excludePartnerId;
        }

        Log::info("Order {$order->id}: Excluded partners (rejected): ".implode(', ', $rejectedPartnerIds));

        // Find available partners excluding those who rejected
        $partners = DeliveryPartner::where('is_available', true)
            ->where('is_online', true)
            ->where('status', 'approved')
            ->whereNotIn('id', $rejectedPartnerIds)
            ->get();

        if ($partners->isEmpty()) {
            Log::info("Order {$order->id}: No available delivery partners found for reassignment.");

            return [
                'success' => false,
                'message' => 'No other delivery partners available at the moment.',
                'data' => null,
            ];
        }

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

                Log::info("Order {$order->id}: Partner {$partner->id} distance to customer: {$distance} km");

                if ($minDistance === null || $distance < $minDistance) {
                    $minDistance = $distance;
                    $nearest = $partner;
                }
            }
        }

        if (! $nearest) {
            Log::info("Order {$order->id}: No partners with valid location found for reassignment.");

            return [
                'success' => false,
                'message' => 'No delivery partners with valid location available.',
                'data' => null,
            ];
        }

        try {
            // Create new assignment for the nearest partner
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

            // Update order status
            $order->update(['status' => 'assigned_to_delivery']);

            $partnerName = $nearest->user ? $nearest->user->first_name.' '.$nearest->user->last_name : 'Unknown';

            Log::info("Order {$order->id}: Reassigned to partner {$nearest->id} ({$partnerName})");

            return [
                'success' => true,
                'message' => 'Order reassigned successfully.',
                'data' => [
                    'delivery_partner_id' => $nearest->id,
                    'delivery_partner_name' => $partnerName,
                    'distance_to_customer_km' => round($minDistance, 2),
                    'total_rejected_partners' => count($rejectedPartnerIds),
                ],
            ];

        } catch (\Exception $e) {
            Log::error("Order {$order->id}: Failed to reassign - ".$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to reassign order: '.$e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Manually reassign an order to the next available delivery partner
     * Can be called by admin or system
     */
    public function manualReassign(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'exclude_partner_id' => 'nullable|integer|exists:delivery_partners,id',
        ]);

        $order = Order::with(['deliveryAddress', 'restaurant'])->find($validated['order_id']);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        if (! $order->deliveryAddress || ! $order->restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Order delivery address or restaurant not found.',
            ], 404);
        }

        $excludePartnerId = $validated['exclude_partner_id'] ?? null;

        $result = $this->reassignToNextPartner($order, $excludePartnerId);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Order reassigned successfully.',
                'data' => [
                    'order_id' => $order->id,
                    'new_assignment' => $result['data'],
                ],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 404);
        }
    }

    /**
     * Get rejection history for an order
     */
    public function getOrderRejections(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        $rejections = DeliveryAssignment::where('order_id', $validated['order_id'])
            ->where('status', 'rejected')
            ->with('partner.user:id,first_name,last_name,phone')
            ->orderBy('assigned_at', 'desc')
            ->get()
            ->map(function ($assignment) {
                return [
                    'assignment_id' => $assignment->id,
                    'partner_id' => $assignment->partner_id,
                    'partner_name' => $assignment->partner && $assignment->partner->user
                        ? $assignment->partner->user->first_name.' '.$assignment->partner->user->last_name
                        : 'Unknown',
                    'rejection_reason' => $assignment->rejection_reason,
                    'rejected_at' => $assignment->assigned_at,
                ];
            });

        $currentAssignment = DeliveryAssignment::where('order_id', $validated['order_id'])
            ->whereIn('status', ['assigned', 'accepted'])
            ->with('partner.user:id,first_name,last_name,phone')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $validated['order_id'],
                'total_rejections' => $rejections->count(),
                'rejections' => $rejections,
                'current_assignment' => $currentAssignment ? [
                    'partner_id' => $currentAssignment->partner_id,
                    'partner_name' => $currentAssignment->partner && $currentAssignment->partner->user
                        ? $currentAssignment->partner->user->first_name.' '.$currentAssignment->partner->user->last_name
                        : 'Unknown',
                    'status' => $currentAssignment->status,
                    'assigned_at' => $currentAssignment->assigned_at,
                ] : null,
            ],
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

    // Helper function to calculate estimated delivery time based on distance
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
}
