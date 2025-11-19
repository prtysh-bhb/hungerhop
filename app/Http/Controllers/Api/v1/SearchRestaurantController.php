<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class SearchRestaurantController extends Controller
{
    /**
     * Search restaurants by name, city, or cuisine type.
     * If no query is provided, return all approved restaurants.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $request->validate([
            'query' => 'nullable|string',
        ]);

        $query = $request->input('query');

        // Base query for approved restaurants only
        $restaurantQuery = Restaurant::where('status', 'approved');

        // If query is provided, filter by name, city, or cuisine type
        if (! empty($query)) {
            $restaurantQuery->where(function ($q) use ($query) {
                $q->where('restaurant_name', 'like', "%$query%")
                    ->orWhere('city', 'like', "%$query%")
                    ->orWhere('cuisine_type', 'like', "%$query%");
            });
        }

        // Get restaurants with additional fields including pause status
        $restaurants = $restaurantQuery->get([
            'id',
            'restaurant_name',
            'city',
            'cuisine_type',
            'address',
            'phone',
            'email',
            'image_url',
            'is_paused',
            'minimum_order_amount',
            'base_delivery_fee',
            'estimated_delivery_time',
        ]);

        // Transform the data to include pause message
        $transformedRestaurants = $restaurants->map(function ($restaurant) {
            $data = [
                'id' => $restaurant->id,
                'restaurant_name' => $restaurant->restaurant_name,
                'city' => $restaurant->cityRelation->name,
                'cuisine_type' => $restaurant->cuisine_type,
                'address' => $restaurant->address,
                'phone' => $restaurant->phone,
                'email' => $restaurant->email,
                'image_url' => $restaurant->image_url,
                'minimum_order_amount' => $restaurant->minimum_order_amount,
                'base_delivery_fee' => $restaurant->base_delivery_fee,
                'estimated_delivery_time' => $restaurant->estimated_delivery_time,
                'is_paused' => $restaurant->is_paused,
                'status' => $restaurant->is_paused ? 'paused' : 'active',
            ];

            // Add pause message if restaurant is paused
            if ($restaurant->is_paused) {
                $data['pause_message'] = 'This restaurant is temporarily paused and not accepting new orders at the moment.';
                $data['can_order'] = false;
            } else {
                $data['pause_message'] = null;
                $data['can_order'] = true;
            }

            return $data;
        });

        return response()->json([
            'success' => true,
            'message' => empty($query) ? 'All approved restaurants retrieved successfully' : 'Search results retrieved successfully',
            'total_count' => $transformedRestaurants->count(),
            'query' => $query,
            'data' => $transformedRestaurants,
        ]);
    }
}
