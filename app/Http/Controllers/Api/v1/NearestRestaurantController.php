<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
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
                        'is_available' => $item->is_available,
                        'is_veg' => $item->is_veg,
                        'is_vaegan' => $item->is_vegan,

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
     * Get menu items by category name
     * POST /api/v1/restaurant/menu
     */
    public function menuByCategoryWithRestaurant(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:100',
        ]);

        $category = MenuCategory::whereRaw(
            'LOWER(name) = ?',
            [strtolower($validated['category'])]
        )->first();

        if (! $category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found',
            ], 404);
        }

        // 2. Fetch menu items with restaurant
        $menuItems = MenuItem::with('restaurant')
            ->where('menu_category_id', $category->id)
            ->get()
            ->groupBy('restaurant_id');

        // 3. Build response
        // $data = $menuItems->map(function ($items) use ($category) {
        //     $restaurant = $items->first()->restaurant;

        //     return [
        //         'id' => $restaurant->id,
        //         'name' => $restaurant->restaurant_name,
        //         'address' => $restaurant->address,
        //         'rating' => (float) $restaurant->average_rating,
        //         'is_open' => (bool) $restaurant->is_open,
        //         'logo' => $restaurant->full_image_url,
        //         'title' => $category->name,
        //         'menu_item' => $items->map(function ($item) {
        //             return [
        //                 'id' => $item->id,
        //                 'name' => $item->item_name,
        //                 'price' => (float) $item->base_price,
        //                 'description' => $item->description,
        //                 'image' => $item->image_url
        //                     ? url('storage/'.$item->image_url)
        //                     : null,
        //                 'is_available' => (bool) $item->is_available,
        //                 'is_veg' => (bool) $item->is_veg,
        //                 'is_vegan' => (bool) $item->is_vegan,
        //                 'rating' => (float) $item->average_rating,
        //             ];
        //         })->values(),
        //     ];
        // })->values();

        $data = $menuItems->map(function ($items) use ($category) {

            // 1. Get FIRST NON-DELETED restaurant
            $restaurant = $items
                ->pluck('restaurant')
                ->filter(fn ($r) => $r && $r->deleted_at === null)
                ->first();

            // 2. If ALL restaurants are deleted → skip this group
            if (! $restaurant) {
                return null;
            }

            return [
                'id' => $restaurant->id,
                'name' => $restaurant->restaurant_name,
                'address' => $restaurant->address,
                'rating' => (float) $restaurant->average_rating,
                'is_open' => (bool) $restaurant->is_open,
                'logo' => $restaurant->full_image_url,
                'title' => $category->name,

                'menu_item' => $items
                    // 3. Keep only items whose restaurant is NOT deleted
                    ->filter(fn ($item) => $item->restaurant && $item->restaurant->deleted_at === null)
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->item_name,
                            'price' => (float) $item->base_price,
                            'description' => $item->description,
                            'image' => $item->image_url
                                ? url('storage/'.$item->image_url)
                                : null,
                            'is_available' => (bool) $item->is_available,
                            'is_veg' => (bool) $item->is_veg,
                            'is_vegan' => (bool) $item->is_vegan,
                            'rating' => (float) $item->average_rating,
                        ];
                    })
                    ->values(),
            ];
        })
            ->filter()   // remove null restaurants safely
            ->values();

        return response()->json([
            'success' => true,
            'data' => $data,
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

        $restaurants = $query->get();

        // Map restaurants to structured format
        $restaurants = $restaurants->map(function ($restaurant) {
            $businessHours = $this->getTiming($restaurant->business_hours);
            $todayHours = $businessHours[strtolower(now()->format('l'))] ?? null;

            return [
                'id' => $restaurant->id,
                'name' => $restaurant->restaurant_name,
                'address' => $restaurant->address,
                'latitude' => (string) $restaurant->latitude,
                'longitude' => (string) $restaurant->longitude,
                'rating' => (float) $restaurant->average_rating,
                'total_reviews' => $restaurant->total_reviews,
                'is_open' => $restaurant->is_open,
                'opening_time' => $todayHours['open'] ?? null,
                'closing_time' => $todayHours['close'] ?? null,
                'delivery_time' => $restaurant->estimated_delivery_time.' mins',
                'logo' => $restaurant->full_image_url,
                'cuisine_type' => $restaurant->cuisine_type,
                'distance' => isset($restaurant->distance) ? round($restaurant->distance, 2) : null,
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
        $restaurant = Restaurant::with([
            'menuCategories.menuItems',
            'banners',
            'reviews.customer.user',
        ])->where('id', $request->id)
            ->when($user && in_array($user->role, ['admin', 'owner']), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->firstOrFail();

        // Get active promotions for this restaurant
        $coupons = \App\Models\Promotion::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            })
            ->get()
            ->map(function ($promo) {
                return [
                    'id' => $promo->id,
                    'code' => $promo->promotion_code,
                    'discount_type' => $promo->discount_type,
                    'discount_value' => (float) $promo->discount_value,
                    'min_order_amount' => (float) $promo->minimum_order_amount,
                    'expiry_date' => $promo->valid_until ? $promo->valid_until->format('Y-m-d') : null,
                    'is_active' => $promo->is_active,
                ];
            });

        // Get menu items with categories
        $productData = [];
        foreach ($restaurant->menuCategories as $category) {
            foreach ($category->menuItems as $item) {
                $productData[] = [
                    'id' => $item->id,
                    'name' => $item->item_name,
                    'price' => (float) $item->base_price,
                    'offer_price' => (float) $item->base_price, // You can implement offer logic later
                    'description' => $item->description,
                    'category_id' => $item->menu_category_id,
                    'image' => $item->image_url ? url('storage/'.$item->image_url) : null,
                    'is_available' => $item->is_available,
                    'rating' => (float) $item->average_rating,
                    'is_vegetarian' => $item->is_vegetarian,
                    'is_vegan' => $item->is_vegan,
                ];
            }
        }

        // Restaurant data
        $businessHours = $this->getTiming($restaurant->business_hours);
        $todayHours = $businessHours[strtolower(now()->format('l'))] ?? null;

        $restuarantData = [[
            'id' => $restaurant->id,
            'name' => $restaurant->restaurant_name,
            'address' => $restaurant->address,
            'latitude' => (string) $restaurant->latitude,
            'longitude' => (string) $restaurant->longitude,
            'rating' => (float) $restaurant->average_rating,
            'total_reviews' => $restaurant->total_reviews,
            'is_open' => $restaurant->is_open,
            'opening_time' => $todayHours['open'] ?? null,
            'closing_time' => $todayHours['close'] ?? null,
            'delivery_time' => $restaurant->estimated_delivery_time.' mins',
            'logo' => $restaurant->full_image_url,
            'cuisine_type' => $restaurant->cuisine_type,
            'phone' => $restaurant->phone,
            'email' => $restaurant->email,
        ]];

        // Gallery data (banners)
        $galleryData = $restaurant->banners->map(function ($banner) {
            return [
                'id' => $banner->id,
                'image_url' => $banner->image_url ? url('storage/'.$banner->image_url) : null,
                'title' => $banner->title,
            ];
        });

        // Review data
        $reviewData = $restaurant->reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'user_name' => $review->customer && $review->customer->user
                    ? $review->customer->user->name
                    : 'Anonymous',
                'rating' => $review->rating,
                'review' => $review->review_text,
                'created_at' => $review->created_at->format('Y-m-d'),
            ];
        });

        return response()->json([
            'success' => true,
            'Coupon' => $coupons,
            'Product_Data' => $productData,
            'restuarant_data' => $restuarantData,
            'Gallery_Data' => $galleryData,
            'Review_Data' => $reviewData,
        ]);
    }
}
