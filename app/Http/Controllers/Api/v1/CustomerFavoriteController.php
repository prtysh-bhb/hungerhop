<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\CustomerFavoriteItem;
use App\Models\CustomerProfile;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerFavoriteController extends Controller
{
    /**
     * Add item to favorites
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addFavorite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:menu_item,restaurant',
            'item_id' => 'required_if:type,menu_item|exists:menu_items,id',
            'restaurant_id' => 'required_if:type,restaurant|exists:restaurants,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $customerProfile = $this->getCustomerProfile();
        if (! $customerProfile) {
            return $this->customerNotFoundResponse();
        }

        $type = $request->input('type');
        $itemId = $request->input('item_id');
        $restaurantId = $request->input('restaurant_id');
        $tenantId = null;

        if ($type === CustomerFavoriteItem::TYPE_MENU_ITEM) {
            $menuItem = MenuItem::find($itemId);
            if (! $menuItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu item not found.',
                ], 404);
            }
            $restaurantId = $menuItem->restaurant_id;
            $tenantId = $menuItem->tenant_id;
        } else {
            $restaurant = Restaurant::find($restaurantId);
            if (! $restaurant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant not found.',
                ], 404);
            }
            $tenantId = $restaurant->tenant_id;
        }

        try {
            DB::beginTransaction();

            $existingFavorite = CustomerFavoriteItem::withTrashed()
                ->where('customer_id', $customerProfile->id)
                ->where('type', $type)
                ->where('item_id', $type === CustomerFavoriteItem::TYPE_MENU_ITEM ? $itemId : null)
                ->where('restaurant_id', $restaurantId)
                ->first();

            if ($existingFavorite) {
                if ($existingFavorite->trashed()) {
                    $existingFavorite->restore();
                    $existingFavorite->update([
                        'added_at' => now(),
                        'tenant_id' => $tenantId,
                    ]);

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'Favorite restored successfully.',
                        'data' => $this->formatFavoriteResponse($existingFavorite),
                    ], 200);
                }

                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Already in your favorites.',
                ], 409);
            }

            $favorite = CustomerFavoriteItem::create([
                'customer_id' => $customerProfile->id,
                'type' => $type,
                'item_id' => $type === CustomerFavoriteItem::TYPE_MENU_ITEM ? $itemId : null,
                'restaurant_id' => $restaurantId,
                'tenant_id' => $tenantId,
                'added_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Added to favorites successfully.',
                'data' => $this->formatFavoriteResponse($favorite),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to add favorite.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Remove item from favorites
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFavorite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:customer_favorite_items,id',
            'type' => 'required|in:menu_item,restaurant',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        $customerProfile = $this->getCustomerProfile();
        if (! $customerProfile) {
            return $this->customerNotFoundResponse();
        }

        $id = $request->input('id');
        $type = $request->input('type');

        $favorite = CustomerFavoriteItem::where('id', $id)
            ->where('customer_id', $customerProfile->id)
            ->where('type', $type)
            ->first();

        if (! $favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Favorite not found.',
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Removed from favorites successfully.',
        ], 200);
    }

    /**
     * List all favorite items for the customer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listFavorites(Request $request)
    {
        $customerProfile = $this->getCustomerProfile();
        if (! $customerProfile) {
            return $this->customerNotFoundResponse();
        }

        $type = $request->input('type');

        $query = CustomerFavoriteItem::where('customer_id', $customerProfile->id)
            ->with([
                'menuItem',
                'restaurant', // Only eager load restaurant, not non-existent relations
            ]);

        if ($type && in_array($type, [CustomerFavoriteItem::TYPE_MENU_ITEM, CustomerFavoriteItem::TYPE_RESTAURANT])) {
            $query->where('type', $type);
        }

        $favorites = $query->orderBy('added_at', 'desc')
            ->get();

        $formattedFavorites = $favorites->map(function ($favorite) {
            return $this->formatFavoriteForList($favorite);
        });

        return response()->json([
            'success' => true,
            'message' => 'Favorites retrieved successfully.',
            'data' => [
                'favorites' => $formattedFavorites,
                'total_count' => $formattedFavorites->count(),
                'menu_items_count' => $formattedFavorites->where('type', CustomerFavoriteItem::TYPE_MENU_ITEM)->count(),
                'restaurants_count' => $formattedFavorites->where('type', CustomerFavoriteItem::TYPE_RESTAURANT)->count(),
            ],
        ], 200);
    }

    /**
     * Toggle favorite status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleFavorite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:menu_item,restaurant',
            'item_id' => 'required_if:type,menu_item|exists:menu_items,id',
            'restaurant_id' => 'required_if:type,restaurant|exists:restaurants,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $customerProfile = $this->getCustomerProfile();
        if (! $customerProfile) {
            return $this->customerNotFoundResponse();
        }

        $type = $request->input('type');
        $itemId = $request->input('item_id');
        $restaurantId = $request->input('restaurant_id');
        $tenantId = null;

        if ($type === CustomerFavoriteItem::TYPE_MENU_ITEM) {
            $menuItem = MenuItem::find($itemId);
            if (! $menuItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu item not found.',
                ], 404);
            }
            $restaurantId = $menuItem->restaurant_id;
            $tenantId = $menuItem->tenant_id;
        } else {
            $restaurant = Restaurant::find($restaurantId);
            if (! $restaurant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant not found.',
                ], 404);
            }
            $tenantId = $restaurant->tenant_id;
        }

        $existingFavorite = CustomerFavoriteItem::withTrashed()
            ->where('customer_id', $customerProfile->id)
            ->where('type', $type)
            ->where('item_id', $type === CustomerFavoriteItem::TYPE_MENU_ITEM ? $itemId : null)
            ->where('restaurant_id', $restaurantId)
            ->first();

        try {
            DB::beginTransaction();

            if ($existingFavorite && ! $existingFavorite->trashed()) {
                $existingFavorite->delete();
                $isFavorite = false;
                $message = 'Removed from favorites.';
            } else {
                if ($existingFavorite && $existingFavorite->trashed()) {
                    $existingFavorite->restore();
                    $existingFavorite->update([
                        'added_at' => now(),
                        'tenant_id' => $tenantId,
                    ]);
                    $favorite = $existingFavorite;
                } else {
                    $favorite = CustomerFavoriteItem::create([
                        'customer_id' => $customerProfile->id,
                        'type' => $type,
                        'item_id' => $type === CustomerFavoriteItem::TYPE_MENU_ITEM ? $itemId : null,
                        'restaurant_id' => $restaurantId,
                        'tenant_id' => $tenantId,
                        'added_at' => now(),
                    ]);
                }
                $isFavorite = true;
                $message = 'Added to favorites.';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_favourite' => $isFavorite,
                'data' => $isFavorite ? $this->formatFavoriteResponse($favorite) : null,
            ], $isFavorite ? 201 : 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle favorite.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Check if an item is in favorites
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkFavorite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:menu_item,restaurant',
            'item_id' => 'required_if:type,menu_item|nullable|integer',
            'restaurant_id' => 'required_if:type,restaurant|nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $customerProfile = $this->getCustomerProfile();
        if (! $customerProfile) {
            return $this->customerNotFoundResponse();
        }

        $type = $request->input('type');
        $itemId = $request->input('item_id');
        $restaurantId = $request->input('restaurant_id');

        $isFavorite = CustomerFavoriteItem::where('customer_id', $customerProfile->id)
            ->where('type', $type)
            ->where('item_id', $type === CustomerFavoriteItem::TYPE_MENU_ITEM ? $itemId : null)
            ->where('restaurant_id', $restaurantId)
            ->exists();

        return response()->json([
            'success' => true,
            'type' => $type,
            'item_id' => $itemId,
            'restaurant_id' => $restaurantId,
            'is_favourite' => $isFavorite,
        ], 200);
    }

    /**
     * Get favorites by type
     *
     * @param  string  $type
     * @return \Illuminate\Http\JsonResponse
     */
    // public function getFavoritesByType($type)
    // {
    //     if (! in_array($type, [CustomerFavoriteItem::TYPE_MENU_ITEM, CustomerFavoriteItem::TYPE_RESTAURANT])) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Invalid favorite type.',
    //         ], 422);
    //     }

    //     $customerProfile = $this->getCustomerProfile();
    //     if (! $customerProfile) {
    //         return $this->customerNotFoundResponse();
    //     }

    //     $favorites = CustomerFavoriteItem::where('customer_id', $customerProfile->id)
    //         ->where('type', $type)
    //         ->with($type === CustomerFavoriteItem::TYPE_MENU_ITEM ? 'menuItem' : 'restaurant')
    //         ->orderBy('added_at', 'desc')
    //         ->get();

    //     $formattedFavorites = $favorites->map(function ($favorite) {
    //         return $this->formatFavoriteForList($favorite);
    //     });

    //     return response()->json([
    //         'success' => true,
    //         'message' => ucfirst(str_replace('_', ' ', $type)).' favorites retrieved successfully.',
    //         'data' => [
    //             'type' => $type,
    //             'favorites' => $formattedFavorites,
    //             'total_count' => $formattedFavorites->count(),
    //         ],
    //     ], 200);
    // }

    /**
     * Clear all favorites
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearAllFavorites()
    {
        $customerProfile = $this->getCustomerProfile();
        if (! $customerProfile) {
            return $this->customerNotFoundResponse();
        }

        $deletedCount = CustomerFavoriteItem::where('customer_id', $customerProfile->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'All favorites cleared successfully.',
            'data' => [
                'deleted_count' => $deletedCount,
            ],
        ], 200);
    }

    /**
     * Get customer profile for authenticated user
     *
     * @return CustomerProfile|null
     */
    private function getCustomerProfile()
    {
        $user = auth()->user();

        return CustomerProfile::where('user_id', $user->id)->first();
    }

    /**
     * Customer not found response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function customerNotFoundResponse()
    {
        return response()->json([
            'success' => false,
            'message' => 'Customer profile not found.',
        ], 404);
    }

    /**
     * Format favorite for list response
     *
     * @return array
     */
    private function formatFavoriteForList(CustomerFavoriteItem $favorite)
    {
        $baseData = [
            'id' => $favorite->id,
            'type' => $favorite->type,
            'added_at' => $favorite->added_at?->toDateTimeString(),
            'created_at' => $favorite->created_at?->toDateTimeString(),
        ];

        if ($favorite->type === CustomerFavoriteItem::TYPE_MENU_ITEM) {
            $menuItem = $favorite->menuItem;
            $restaurant = $favorite->restaurant;

            return array_merge($baseData, [
                'item_id' => $favorite->item_id,
                'menu_item' => $menuItem ? [
                    'id' => $menuItem->id,
                    'name' => $menuItem->item_name,
                    'description' => $menuItem->description,
                    'base_price' => $menuItem->base_price,
                    'image_url' => $menuItem->image_url,
                    'is_available' => $menuItem->is_available,
                    'is_vegetarian' => $menuItem->is_vegetarian,
                    'category' => $menuItem->category ? [
                        'id' => $menuItem->category->id,
                        'name' => $menuItem->category->name,
                    ] : null,
                ] : null,
                'restaurant' => $restaurant ? $this->formatRestaurantResponse($restaurant) : null,
            ]);
        } else {
            $restaurant = $favorite->restaurant;

            return array_merge($baseData, [
                'restaurant_id' => $favorite->restaurant_id,
                'restaurant' => $restaurant ? $this->formatRestaurantResponse($restaurant) : null,
            ]);
        }
    }

    /**
     * Format restaurant response
     *
     * @return array
     */
    private function formatRestaurantResponse(Restaurant $restaurant)
    {
        return [
            'id' => (string) $restaurant->id,
            'name' => $restaurant->restaurant_name,
            'image_url' => $restaurant->image_url,
            'rating' => $restaurant->average_rating,
            'description' => $restaurant->description,
            'full_address' => $restaurant->address,
            'short_description' => $restaurant->description,
            'estimated_delivery_time' => (string) $restaurant->estimated_delivery_time,
            'minimum_order_amount' => (string) $restaurant->minimum_order_amount,
            'cost_for_two' => (string) ($restaurant->minimum_order_amount ? $restaurant->minimum_order_amount * 2 : null),
            'is_open' => $restaurant->is_open,
            'cuisines' => $restaurant->cuisine_type,
            'location' => $restaurant->location ? [
                'address' => $restaurant->address,
                'city' => $restaurant->city,
                'state' => $restaurant->state,
                'country' => $restaurant->country,
                'postal_code' => $restaurant->postal_code,
            ] : null,
        ];
    }

    /**
     * Format basic favorite response
     *
     * @return array
     */
    private function formatFavoriteResponse(CustomerFavoriteItem $favorite)
    {
        return [
            'id' => $favorite->id,
            'type' => $favorite->type,
            'item_id' => $favorite->item_id,
            'restaurant_id' => $favorite->restaurant_id,
            'tenant_id' => $favorite->tenant_id,
            'added_at' => $favorite->added_at?->toDateTimeString(),
            'created_at' => $favorite->created_at?->toDateTimeString(),
        ];
    }
}
