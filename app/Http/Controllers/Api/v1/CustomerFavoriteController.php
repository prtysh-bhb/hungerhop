<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\CustomerFavoriteItem;
use App\Models\CustomerProfile;
use App\Models\MenuItem;
use Illuminate\Http\Request;
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
        $user = auth()->user();

        // Get customer profile for authenticated user
        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (! $customerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
            ], 404);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:menu_items,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $itemId = $request->input('item_id');

        // Get the menu item details
        $menuItem = MenuItem::find($itemId);
        if (! $menuItem) {
            return response()->json([
                'success' => false,
                'message' => 'Menu item not found.',
            ], 404);
        }

        // Check if already in favorites (including soft-deleted)
        $existingFavorite = CustomerFavoriteItem::withTrashed()
            ->where('customer_id', $customerProfile->id)
            ->where('item_id', $itemId)
            ->first();

        if ($existingFavorite) {
            // If soft-deleted, restore it
            if ($existingFavorite->trashed()) {
                $existingFavorite->restore();
                $existingFavorite->update([
                    'restaurant_id' => $menuItem->restaurant_id,
                    'tenant_id' => $menuItem->tenant_id,
                    'added_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Item added to favorites successfully.',
                    'data' => [
                        'id' => $existingFavorite->id,
                        'item_id' => $existingFavorite->item_id,
                        'item_name' => $menuItem->item_name,
                        'restaurant_id' => $existingFavorite->restaurant_id,
                        'added_at' => $existingFavorite->added_at->toDateTimeString(),
                    ],
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'Item is already in your favorites.',
            ], 409);
        }

        // Add to favorites
        $favorite = CustomerFavoriteItem::create([
            'customer_id' => $customerProfile->id,
            'item_id' => $itemId,
            'restaurant_id' => $menuItem->restaurant_id,
            'tenant_id' => $menuItem->tenant_id,
            'added_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item added to favorites successfully.',
            'data' => [
                'id' => $favorite->id,
                'item_id' => $favorite->item_id,
                'item_name' => $menuItem->item_name,
                'restaurant_id' => $favorite->restaurant_id,
                'added_at' => $favorite->added_at->toDateTimeString(),
            ],
        ], 201);
    }

    /**
     * Remove item from favorites
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFavorite(Request $request)
    {
        $user = auth()->user();

        // Get customer profile for authenticated user
        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (! $customerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
            ], 404);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $itemId = $request->input('item_id');

        // Find the favorite
        $favorite = CustomerFavoriteItem::where('customer_id', $customerProfile->id)
            ->where('item_id', $itemId)
            ->first();

        if (! $favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in your favorites.',
            ], 404);
        }

        // Remove from favorites (soft delete)
        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from favorites successfully.',
        ], 200);
    }

    /**
     * List all favorite items for the customer
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function listFavorites(Request $request)
    {
        $user = auth()->user();

        // Get customer profile for authenticated user
        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (! $customerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
            ], 404);
        }

        // Get all favorites with menu item and restaurant details
        $favorites = CustomerFavoriteItem::where('customer_id', $customerProfile->id)
            ->with(['menuItem', 'restaurant'])
            ->orderBy('added_at', 'desc')
            ->get();

        // Transform the data
        $favoriteItems = $favorites->map(function ($favorite) {
            $menuItem = $favorite->menuItem;
            $restaurant = $favorite->restaurant;

            return [
                'id' => $favorite->id,
                'item_id' => $favorite->item_id,
                'item_name' => $menuItem ? $menuItem->item_name : null,
                'item_description' => $menuItem ? $menuItem->description : null,
                'base_price' => $menuItem ? $menuItem->base_price : null,
                'image_url' => $menuItem ? $menuItem->image_url : null,
                'is_available' => $menuItem ? $menuItem->is_available : false,
                'is_vegetarian' => $menuItem ? $menuItem->is_vegetarian : null,
                'restaurant' => $restaurant ? [
                    'id' => $restaurant->id,
                    'name' => $restaurant->restaurant_name,
                    'image_url' => $restaurant->image_url ?? null,
                    'rating' => $restaurant->average_rating,
                    'description' => $restaurant->description,
                    'estimated_delivery_time' => $restaurant->estimated_delivery_time,
                    'cost_for_two' =>(string) ($restaurant->minimum_order_amount ? $restaurant->minimum_order_amount * 2 : null),
                ] : null,
                'added_at' => $favorite->added_at ? $favorite->added_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Favorites retrieved successfully.',
            'total_count' => $favoriteItems->count(),
            'data' => $favoriteItems,
        ], 200);
    }

    /**
     * Toggle favorite status (add if not exists, remove if exists)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleFavorite(Request $request)
    {
        $user = auth()->user();

        // Get customer profile for authenticated user
        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (! $customerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
            ], 404);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:menu_items,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $itemId = $request->input('item_id');
        $tenantId = MenuItem::where('id', $itemId)->value('tenant_id');

        // Check if already in favorites (including soft-deleted)
        $existingFavorite = CustomerFavoriteItem::withTrashed()
            ->where('customer_id', $customerProfile->id)
            ->where('item_id', $itemId)
            ->where('tenant_id', $tenantId)
            ->first();

        if ($existingFavorite && ! $existingFavorite->trashed()) {
            // Remove from favorites (soft delete)
            $existingFavorite->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from favorites.',
                'is_favorite' => false,
            ], 200);
        } else {
            // Add to favorites (restore if soft-deleted, or create new)
            $menuItem = MenuItem::find($itemId);

            if ($existingFavorite && $existingFavorite->trashed()) {
                // Restore soft-deleted record
                $existingFavorite->restore();
                $existingFavorite->update([
                    'restaurant_id' => $menuItem->restaurant_id,
                    'tenant_id' => $tenantId,
                    'added_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Item added to favorites.',
                    'is_favorite' => true,
                    'data' => [
                        'id' => $existingFavorite->id,
                        'item_id' => $existingFavorite->item_id,
                        'item_name' => $menuItem->item_name,
                    ],
                ], 201);
            }

            $favorite = CustomerFavoriteItem::create([
                'customer_id' => $customerProfile->id,
                'item_id' => $itemId,
                'restaurant_id' => $menuItem->restaurant_id,
                'tenant_id' => $tenantId,
                'added_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item added to favorites.',
                'is_favorite' => true,
                'data' => [
                    'id' => $favorite->id,
                    'item_id' => $favorite->item_id,
                    'item_name' => $menuItem->item_name,
                ],
            ], 201);
        }
    }

    /**
     * Check if an item is in favorites
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkFavorite(Request $request)
    {
        $user = auth()->user();

        // Get customer profile for authenticated user
        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (! $customerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
            ], 404);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $itemId = $request->input('item_id');

        // Check if in favorites
        $isFavorite = CustomerFavoriteItem::where('customer_id', $customerProfile->id)
            ->where('item_id', $itemId)
            ->exists();

        return response()->json([
            'success' => true,
            'item_id' => $itemId,
            'is_favorite' => $isFavorite,
        ], 200);
    }
}
