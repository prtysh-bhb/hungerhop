<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\MenuItemReview;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

// --- IGNORE ---

class ReviewController extends Controller
{
    /**
     * Get reviews for a restaurant.
     * GET /restaurant/{id}/reviews
     */
    public function getReviews(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:restaurants,id',
        ]);
        $user = auth()->user();
        $restaurant = Restaurant::where('id', $validated['id'])
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
    public function addReview(Request $request)
    {
        $user = auth()->user();
        $validated = Validator::make($request->all(), [
            'id' => 'required|integer|exists:restaurants,id',
            'user_id' => 'required|integer',
            'customer_id' => 'required|integer|exists:customer_profiles,id',
            'reviewable_type' => 'required|string|in:restaurant,delivery_partner',
            'reviewable_id' => 'required|integer', // Now required since we support both types
            'order_id' => 'required|integer|exists:orders,id',
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        if ($validated->fails()) {
            return response()->json(['success' => false, 'errors' => $validated->errors()], 422);
        }

        $validated = $validated->validated();
        try {
            $restaurant = Restaurant::where('id', $validated['id'])
                ->when($user && in_array($user->role, ['admin', 'owner']), function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->firstOrFail();
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        }

        // Validate order belongs to customer and restaurant
        $order = Order::where('id', $request->order_id)
            ->where('customer_id', $request->customer_id)
            ->where('restaurant_id', $restaurant->id)
            ->first();
        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found for this customer and restaurant.',
            ], 404);
        }

        // Determine the correct reviewable_id based on reviewable_type
        $reviewableId = $request->reviewable_type === 'restaurant'
            ? $restaurant->id
            : $request->reviewable_id;

        // Create review directly using Review model (not through restaurant relationship)
        $review = Review::create([
            'order_id' => $request->order_id,
            'tenant_id' => $restaurant->tenant_id,
            'customer_id' => $request->customer_id,
            'reviewable_type' => $validated['reviewable_type'],
            'reviewable_id' => $reviewableId,
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

    // Add menu item reviews methods here
    public function addMenuItemReview(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:menu_items,id',
            'order_item_id' => 'required|integer|exists:orders,id',
            'customer_id' => 'required|integer|exists:customer_profiles,id',
            'item_id' => 'required|integer|exists:menu_items,id',
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string',
        ]);
        $user = auth()->user();
        // Assuming MenuItem model exists
        $menuItem = MenuItem::where('id', $validated['id'])
            ->when($user && in_array($user->role, ['admin', 'owner']), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->firstOrFail();
        // Validate order item belongs to customer and menu item
        $orderItem = OrderItem::where('id', $request->order_item_id)
            ->where('customer_id', $request->customer_id)
            ->where('menu_item_id', $menuItem->id)
            ->first();
        if (! $orderItem) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found for this customer and menu item.',
            ], 404);
        }
        // Create menu item review
        $menuItemReview = MenuItemReview::create([
            'order_item_id' => $request->order_item_id,
            'customer_id' => $request->customer_id,
            'item_id' => $menuItem->id,
            'rating' => $request->rating,
            'review_text' => $request->comment ?? null,
            'images' => $request->images ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Menu item review added successfully.',
            'data' => $menuItemReview,
        ]);
    }

    public function getMenuItemReviews(Request $request)
    {
        try {
            $validated = $request->validate([
                'item_id' => 'required|integer|exists:menu_items,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        }
        // Get the authenticated user (if needed for authorization)
        $user = auth()->user();

        // Get reviews for the specific menu item
        $menuItemReviews = MenuItemReview::where('item_id', $validated['item_id'])
            ->with(['customer:id,user_id', 'customer.user:id,first_name,last_name']) // Load customer with user details
            ->get(['id', 'order_item_id', 'customer_id', 'item_id', 'rating', 'review_text', 'images', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => $menuItemReviews,
            'total_reviews' => $menuItemReviews->count(),
            'average_rating' => $menuItemReviews->avg('rating'),
        ]);
    }
}
