<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DeliveryAssignment;

class DeliveryPartnerLocationController extends Controller
{
    /**
     * Update delivery partner's current location
     * This endpoint should be called every 1 minute from the mobile app
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLocation(Request $request)
    {
        $user = auth()->user();

        // Verify user is a delivery partner
        if (! $user || $user->role !== 'delivery_partner') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only delivery partners can update location.',
            ], 403);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0', // GPS accuracy in meters
            'speed' => 'nullable|numeric|min:0', // Speed in km/h
            'heading' => 'nullable|numeric|between:0,360', // Direction in degrees
            'is_online' => 'nullable|boolean', // Partner online status
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find delivery partner profile
        $partner = DeliveryPartner::where('user_id', $user->id)->first();

        if (! $partner) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery partner profile not found.',
            ], 404);
        }

        // Check if partner is approved
        if ($partner->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is not approved yet. Location tracking is disabled.',
                'status' => $partner->status,
            ], 403);
        }

        // Store previous location for distance calculation
        $previousLat = $partner->current_latitude;
        $previousLng = $partner->current_longitude;

        // Update location
        $partner->update([
            'current_latitude' => $request->latitude,
            'current_longitude' => $request->longitude,
            'last_location_update' => now(),
            'is_online' => $request->has('is_online') ? $request->is_online : $partner->is_online,
        ]);

        // Calculate distance moved since last update (if previous location exists)
        $distanceMoved = null;
        if ($previousLat && $previousLng) {
            $distanceMoved = $this->calculateDistance(
                $previousLat,
                $previousLng,
                $request->latitude,
                $request->longitude
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'data' => [
                'partner_id' => $partner->id,
                'latitude' => $partner->current_latitude,
                'longitude' => $partner->current_longitude,
                'last_update' => $partner->last_location_update->toDateTimeString(),
                'is_online' => $partner->is_online,
                'is_available' => $partner->is_available,
                'distance_moved_km' => $distanceMoved ? round($distanceMoved, 3) : null,
            ],
        ]);
    }

    /**
     * Get current location of delivery partner
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLocation(Request $request)
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'delivery_partner') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only delivery partners can access this endpoint.',
            ], 403);
        }

        $partner = DeliveryPartner::where('user_id', $user->id)->first();

        if (! $partner) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery partner profile not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'partner_id' => $partner->id,
                'latitude' => $partner->current_latitude,
                'longitude' => $partner->current_longitude,
                'last_update' => $partner->last_location_update?->toDateTimeString(),
                'is_online' => $partner->is_online,
                'is_available' => $partner->is_available,
                'status' => $partner->status,
            ],
        ]);
    }

    /**
     * Toggle delivery partner online/offline status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleOnlineStatus(Request $request)
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'delivery_partner') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied.',
            ], 403);
        }

        $partner = DeliveryPartner::where('user_id', $user->id)->first();

        if (! $partner) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery partner profile not found.',
            ], 404);
        }

        if ($partner->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is not approved yet.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'is_online' => 'required|boolean',
            'latitude' => 'required_if:is_online,true|nullable|numeric|between:-90,90',
            'longitude' => 'required_if:is_online,true|nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updateData = [
            'is_online' => $request->is_online,
            'is_available' => $request->is_online, // When going online, set available
        ];

        // Update location if going online
        if ($request->is_online && $request->latitude && $request->longitude) {
            $updateData['current_latitude'] = $request->latitude;
            $updateData['current_longitude'] = $request->longitude;
            $updateData['last_location_update'] = now();
        }

        $partner->update($updateData);

        return response()->json([
            'success' => true,
            'message' => $request->is_online ? 'You are now online' : 'You are now offline',
            'data' => [
                'partner_id' => $partner->id,
                'is_online' => $partner->is_online,
                'is_available' => $partner->is_available,
                'latitude' => $partner->current_latitude,
                'longitude' => $partner->current_longitude,
            ],
        ]);
    }

    /**
     * Toggle availability status (can accept new orders or not)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleAvailability(Request $request)
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'delivery_partner') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied.',
            ], 403);
        }

        $partner = DeliveryPartner::where('user_id', $user->id)->first();

        if (! $partner) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery partner profile not found.',
            ], 404);
        }

        if (! $partner->is_online) {
            return response()->json([
                'success' => false,
                'message' => 'You must be online to change availability status.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'is_available' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $partner->update([
            'is_available' => $request->is_available,
        ]);

        return response()->json([
            'success' => true,
            'message' => $request->is_available ? 'You are now available for deliveries' : 'You are now unavailable for new deliveries',
            'data' => [
                'partner_id' => $partner->id,
                'is_online' => $partner->is_online,
                'is_available' => $partner->is_available,
            ],
        ]);
    }

    /**
     * Update online status only (simple true/false toggle)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    // public function updateOnlineStatus(Request $request)
    // {
    //     $user = auth()->user();

    //     if (! $user || $user->role !== 'delivery_partner') {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Access denied.',
    //         ], 403);
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'is_online' => 'required|boolean',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation failed',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     $partner = DeliveryPartner::where('user_id', $user->id)->first();

    //     if (! $partner) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Delivery partner profile not found.',
    //         ], 404);
    //     }

    //     if ($partner->status !== 'approved') {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Your account is not approved yet.',
    //             'status' => $partner->status,
    //         ], 403);
    //     }

    //     // If going offline, also set unavailable
    //     $updateData = [
    //         'is_online' => $request->is_online,
    //         'is_available' => $request->is_online
    //     ];

    //     if (! $request->is_online) {
    //         $updateData['is_available'] = false;
    //         $partner->update($updateData);
    //     }

    //     $partner->update($updateData);

    //     return response()->json([
    //         'success' => true,
    //         'message' => $request->is_online ? 'You are now online' : 'You are now offline',
    //         'data' => [
    //             'partner_id' => $partner->id,
    //             'is_online' => $partner->is_online,
    //             'is_available' => $partner->is_available,
    //         ],
    //     ]);
    // }

    /**
     * Get delivery partner location by partner ID (for customers/admins tracking)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function trackPartner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'partner_id' => 'required|integer|exists:delivery_partners,id',
            'order_id' => 'nullable|integer|exists:orders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $partner = DeliveryPartner::with('user:id,first_name,last_name,phone')
            ->find($request->partner_id);

        if (! $partner) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery partner not found.',
            ], 404);
        }

        // If order_id is provided, verify assignment
        if ($request->order_id) {
            $assignment = DeliveryAssignment::where('partner_id', $partner->id)
                ->where('order_id', $request->order_id)
                ->whereIn('status', ['assigned', 'accepted', 'picked_up', 'in_transit'])
                ->first();

            if (! $assignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'This delivery partner is not assigned to your order.',
                ], 403);
            }
        }

        // Check if location is recent (within last 5 minutes)
        $isLocationFresh = $partner->last_location_update &&
            $partner->last_location_update->diffInMinutes(now()) <= 5;

        return response()->json([
            'success' => true,
            'data' => [
                'partner_id' => $partner->id,
                'partner_name' => $partner->user ? $partner->user->first_name.' '.$partner->user->last_name : null,
                'partner_phone' => $partner->user?->phone,
                'vehicle_type' => $partner->vehicle_type,
                'vehicle_number' => $partner->vehicle_number,
                'latitude' => $partner->current_latitude,
                'longitude' => $partner->current_longitude,
                'last_update' => $partner->last_location_update?->toDateTimeString(),
                'is_location_fresh' => $isLocationFresh,
                'is_online' => $partner->is_online,
            ],
        ]);
    }

    /**
     * Batch update location with history (for when app was offline)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function batchUpdateLocation(Request $request)
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'delivery_partner') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'locations' => 'required|array|min:1|max:60', // Max 60 entries (1 hour of 1-min updates)
            'locations.*.latitude' => 'required|numeric|between:-90,90',
            'locations.*.longitude' => 'required|numeric|between:-180,180',
            'locations.*.timestamp' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $partner = DeliveryPartner::where('user_id', $user->id)->first();

        if (! $partner) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery partner profile not found.',
            ], 404);
        }

        // Get the most recent location from batch
        $locations = collect($request->locations)->sortByDesc('timestamp');
        $latestLocation = $locations->first();

        // Update with most recent location
        $partner->update([
            'current_latitude' => $latestLocation['latitude'],
            'current_longitude' => $latestLocation['longitude'],
            'last_location_update' => $latestLocation['timestamp'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Batch location update successful',
            'data' => [
                'partner_id' => $partner->id,
                'locations_processed' => count($request->locations),
                'latest_latitude' => $partner->current_latitude,
                'latest_longitude' => $partner->current_longitude,
                'last_update' => $partner->last_location_update->toDateTimeString(),
            ],
        ]);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     *
     * @param  float  $lat1
     * @param  float  $lon1
     * @param  float  $lat2
     * @param  float  $lon2
     * @return float Distance in kilometers
     */
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
