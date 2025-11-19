<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeliveryZoneController extends Controller
{
    /**
     * Get all delivery zones for a restaurant
     */
    public function index(Request $request)
    {
        Log::info('DeliveryZone index called', $request->all());

        $restaurantId = $request->get('restaurant_id');

        if ($restaurantId) {
            $zones = DeliveryZone::where('restaurant_id', $restaurantId)->get();
        } else {
            $zones = DeliveryZone::all();
        }

        return response()->json([
            'success' => true,
            'data' => $zones,
            'message' => 'Delivery zones retrieved successfully',
        ]);
    }

    /**
     * Create a new delivery zone
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|integer|exists:restaurants,id',
            'tenant_id' => 'required|integer|exists:tenants,id',
            'zone_name' => 'required|string|max:255',
            'zone_polygon' => 'required|array|min:3',
            'zone_polygon.*.lat' => 'required|numeric|between:-90,90',
            'zone_polygon.*.lng' => 'required|numeric|between:-180,180',
            'delivery_fee' => 'required|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'estimated_delivery_time' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $zone = DeliveryZone::create($validated);

        return response()->json([
            'success' => true,
            'data' => $zone,
            'message' => 'Delivery zone created successfully',
        ], 201);
    }

    /**
     * Get a specific delivery zone
     */
    public function show($id)
    {
        $zone = DeliveryZone::find($id);

        if (! $zone) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery zone not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $zone,
        ]);
    }

    /**
     * Update a delivery zone
     */
    public function update(Request $request, $id)
    {
        $zone = DeliveryZone::find($id);

        if (! $zone) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery zone not found',
            ], 404);
        }

        $validated = $request->validate([
            'zone_name' => 'sometimes|string|max:255',
            'zone_polygon' => 'sometimes|array|min:3',
            'delivery_fee' => 'sometimes|numeric|min:0',
            'minimum_order_amount' => 'sometimes|numeric|min:0',
            'estimated_delivery_time' => 'sometimes|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ]);

        $zone->update($validated);

        return response()->json([
            'success' => true,
            'data' => $zone,
            'message' => 'Delivery zone updated successfully',
        ]);
    }

    /**
     * Delete a delivery zone
     */
    public function destroy($id)
    {
        $zone = DeliveryZone::find($id);

        if (! $zone) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery zone not found',
            ], 404);
        }

        $zone->delete();

        return response()->json([
            'success' => true,
            'message' => 'Delivery zone deleted successfully',
        ]);
    }

    /**
     * Check if delivery is available to a specific location
     */
    public function checkDeliveryAvailability(Request $request)
    {
        Log::info('checkDeliveryAvailability called', $request->all());

        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'restaurant_id' => 'required|integer',
        ]);

        $zone = DeliveryZone::getDeliveryZoneForLocation(
            $validated['latitude'],
            $validated['longitude'],
            $validated['restaurant_id']
        );

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
    }

    /**
     * Get delivery fee for a specific location
     */
    public function getDeliveryFee(Request $request)
    {
        try {
            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'restaurant_id' => 'required|integer',
            ]);

            $latitude = $validated['latitude'];
            $longitude = $validated['longitude'];
            $restaurantId = $validated['restaurant_id'];

            $deliveryFee = DeliveryZone::getDeliveryFeeForLocation($latitude, $longitude, $restaurantId);
            $estimatedTime = DeliveryZone::getEstimatedDeliveryTime($latitude, $longitude, $restaurantId);

            if ($deliveryFee === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Delivery not available to this location',
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'delivery_fee' => $deliveryFee,
                    'estimated_delivery_time' => $estimatedTime,
                ],
                'message' => 'Delivery fee calculated successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting delivery fee: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get delivery fee',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
