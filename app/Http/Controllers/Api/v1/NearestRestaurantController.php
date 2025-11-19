<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class NearestRestaurantController extends Controller
{
    /**
     * Get menu items grouped by category for a restaurant.
     * GET /restaurant/{id}/menu
     */
    public function menuWithCategories(Request $request)
    {
        $user = auth()->user();
        $restaurant = Restaurant::where('id', $request->id)
            ->when($user && in_array($user->role, ['admin', 'owner']), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->firstOrFail();

        $categoryName = $request->input('category');
        $categoriesInput = $request->input('categories');

        $categoriesQuery = $restaurant->menuCategories();
        if (is_array($categoriesInput) && count($categoriesInput) > 0) {
            $categoriesQuery->whereIn('name', $categoriesInput);
        } elseif ($categoryName) {
            $categoriesQuery->where('name', $categoryName);
        }
        $categories = $categoriesQuery->with(['menuItems' => function ($q) use ($restaurant) {
            $q->where('restaurant_id', $restaurant->id);
        }])->get();

        $result = $categories->map(function ($cat) {
            return [
                'category' => $cat->name ?? $cat->category_name ?? '',
                'items' => $cat->menuItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->item_name,
                        'price' => $item->base_price,
                        'description' => $item->description,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Get reviews for a restaurant.
     * GET /restaurant/{id}/reviews
     */
    public function getReviews($id)
    {
        $user = auth()->user();
        $restaurant = Restaurant::where('id', $id)
            ->when($user && in_array($user->role, ['admin', 'owner']), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->firstOrFail();
        $reviews = $restaurant->reviews()->get(['id', 'customer_id', 'rating', 'review_text', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    /**
     * Add a review for a restaurant.
     * POST /restaurant/{id}/reviews
     */
    public function addReview(Request $request, $id)
    {
        $user = auth()->user();
        $restaurant = Restaurant::where('id', $id)
            ->when($user && in_array($user->role, ['admin', 'owner']), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->firstOrFail();

        try {
            $validated = $request->validate([
                'user_id' => 'required|integer',
                'customer_id' => 'required|integer|exists:customer_profiles,id',
                'order_id' => 'required|integer|exists:orders,id',
                'rating' => 'required|numeric|min:1|max:5',
                'comment' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        }

        // Validate order belongs to customer and restaurant
        $order = Order::where('id', $request->order_id)
            ->where('customer_id', $request->customer_id)
            ->where('restaurant_id', $id)
            ->first();
        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found for this customer and restaurant.',
            ], 404);
        }

        // Create review with all required fields
        $review = $restaurant->reviews()->create([
            'order_id' => $request->order_id,
            'tenant_id' => $restaurant->tenant_id,
            'customer_id' => $request->customer_id,
            'reviewable_type' => 'restaurant',
            'reviewable_id' => $restaurant->id,
            'rating' => $request->rating,
            'review_text' => $request->comment ?? null,
            'images' => $request->images ?? null,
            'is_anonymous' => $request->is_anonymous ?? false,
            'admin_response' => null,
            'admin_responded_at' => null,
            'admin_responded_by' => null,
            'is_featured' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => $review,
        ]);
    }

    /**
     * Helper to get timing from business_hours field.
     */
    private function getTiming($business_hours)
    {
        if (is_array($business_hours)) {
            return $business_hours;
        }
        if (is_string($business_hours) && $business_hours !== '') {
            $decoded = json_decode($business_hours, true);

            return is_array($decoded) ? $decoded : null;
        }

        return null;
    }

    /**
     * List restaurants with optional filters: cuisine, rating, distance.
     * GET /restaurants
     */
    public function list(Request $request)
    {
        $user = auth()->user();
        $query = Restaurant::query();
        // Only show restaurants owned by the logged-in user (if owner/admin)
        if ($user && in_array($user->role, ['admin', 'owner'])) {
            $query->where('user_id', $user->id);
        }

        // Filter by restaurant_name
        if ($request->filled('restaurant_name')) {
            $query->where('restaurant_name', 'like', "%{$request->restaurant_name}%");
        }

        // Filter by cuisine_type
        if ($request->filled('cuisine_type')) {
            $query->where('cuisine_type', 'like', "%{$request->cuisine_type}%");
        }

        // Filter by minimum average_rating
        if ($request->filled('min_rating')) {
            $query->where('average_rating', '>=', $request->min_rating);
        }

        // Filter by distance if lat/lng provided
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $radius = $request->radius ?? env('NEAREST_RESTAURANT_RADIUS');
            $query->selectRaw(
                '*, (
                    6371 * acos(
                        cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?))
                        + sin(radians(?)) * sin(radians(latitude))
                    )
                ) AS distance',
                [$latitude, $longitude, $latitude]
            )
                ->having('distance', '<=', $radius)
                ->orderBy('distance');
        }

        $restaurants = $query->get(['id', 'restaurant_name', 'cuisine_type', 'average_rating', 'latitude', 'longitude', 'address', 'business_hours']);

        // Add distance if calculated
        $restaurants = $restaurants->map(function ($restaurant) {
            return [
                'id' => $restaurant->id,
                'restaurant_name' => $restaurant->restaurant_name,
                'cuisine_type' => $restaurant->cuisine_type,
                'average_rating' => $restaurant->average_rating,
                'distance' => isset($restaurant->distance) ? round($restaurant->distance, 2) : null,
                'timing' => $this->getTiming($restaurant->business_hours)[strtolower(now()->format('l'))] ?? null,
                'address' => $restaurant->address,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $restaurants,
        ]);
    }

    /**
     * Get restaurant details by id.
     * GET /restaurant/details?id={id}
     */
    public function details(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:restaurants,id',
        ]);

        $user = auth()->user();
        $restaurant = Restaurant::where('id', $request->id)
            ->when($user && in_array($user->role, ['admin', 'owner']), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $restaurant->id,
                'restaurant_name' => $restaurant->restaurant_name,
                'cuisine_type' => $restaurant->cuisine_type,
                'average_rating' => $restaurant->average_rating,
                'timing' => $this->getTiming($restaurant->business_hours)[strtolower(now()->format('l'))] ?? null,
                'address' => $restaurant->address,
                'latitude' => $restaurant->latitude,
                'longitude' => $restaurant->longitude,
                'offers' => $restaurant->offers ?? [],
            ],
        ]);
    }
}
