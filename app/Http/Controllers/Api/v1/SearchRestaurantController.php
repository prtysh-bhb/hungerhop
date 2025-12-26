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
                    ->orWhere('cuisine_type', 'like', "%$query%")
                    ->orWhere('description', 'like', "%$query%");
            });
        }

        // Get all restaurant data
        $restaurants = $restaurantQuery->get();

        // Transform the data to include all required fields
        $transformedRestaurants = $restaurants->map(function ($restaurant) {
            $data = [
                'id' => (string) $restaurant->id,
                'name' => $restaurant->restaurant_name,
                'city' => $restaurant->city,
                'cuisine_type' => $restaurant->cuisine_type,
                'address' => $restaurant->address,
                'full_address' => $restaurant->address.', '.$restaurant->city.', '.$restaurant->state.' - '.$restaurant->postal_code,
                'phone' => $restaurant->phone,
                'email' => $restaurant->email,
                'logo_url' => $restaurant->image_url,
                'cover_image_url' => $restaurant->cover_image_url,

                // Required fields
                'rating' => (string) number_format((float) ($restaurant->average_rating ?? 0), 1),
                'total_reviews' => (int) ($restaurant->total_reviews ?? 0),
                'estimated_delivery_time' => (string) ($restaurant->estimated_delivery_time ?? '30'),
                'cost_for_two' => (string) number_format((float) (($restaurant->minimum_order_amount ?? 100) * 2), 2),
                'description' => $restaurant->description ?? '',
                'short_description' => $restaurant->description
                    ? (strlen($restaurant->description) > 100
                        ? substr($restaurant->description, 0, 100).'...'
                        : $restaurant->description)
                    : '',

                // Order & delivery info
                'minimum_order_amount' => (string) number_format((float) ($restaurant->minimum_order_amount ?? 0), 2),
                'base_delivery_fee' => (string) number_format((float) ($restaurant->base_delivery_fee ?? 0), 2),
                'delivery_radius_km' => (string) number_format((float) ($restaurant->delivery_radius_km ?? 10), 2),
                'tax_percentage' => (string) number_format((float) ($restaurant->tax_percentage ?? 0), 2),

                // Status fields
                'is_open' => (bool) $restaurant->is_open,
                'is_paused' => (bool) $restaurant->is_paused,
                'accepts_orders' => (bool) $restaurant->accepts_orders,
                'is_featured' => (bool) $restaurant->is_featured,
                'status' => $restaurant->is_paused ? 'paused' : ($restaurant->is_open ? 'open' : 'closed'),

                // Can order logic
                'can_order' => $restaurant->is_open && ! $restaurant->is_paused && $restaurant->accepts_orders,
            ];
            $gallery = [];

            if (! empty($restaurant->cover_image_url)) {
                $gallery[] = [
                    'id' => (string) $restaurant->id,
                    'image_url' => url($restaurant->cover_image_url),
                    'title' => $restaurant->restaurant_name,
                ];
            }

            if (empty($gallery)) {
                $gallery[] = [
                    'id' => '0',
                    'image_url' => asset('images/banner/default1.jpg'),
                    'title' => 'Default Banner',
                ];
            }
            $data['gallery'] = $gallery;

            // Add status message based on restaurant state
            if ($restaurant->is_paused) {
                $data['status_message'] = 'This restaurant is temporarily paused and not accepting orders.';
            } elseif (! $restaurant->is_open) {
                $data['status_message'] = 'This restaurant is currently closed.';
            } elseif (! $restaurant->accepts_orders) {
                $data['status_message'] = 'This restaurant is not accepting orders at the moment.';
            } else {
                $data['status_message'] = 'Open for orders';
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
