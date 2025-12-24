<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAssignment;
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

        // 1. Validate base structure
        $validated = Validator::make($request->all(), [
            'order_id' => 'required|integer|exists:orders,id',
            'reviews' => 'required|array|min:1',
            'reviews.*.reviewable_type' => 'required|in:restaurant,delivery_partner',
            'reviews.*.rating' => 'required|numeric|min:1|max:5',
            'reviews.*.comment' => 'nullable|string|max:500',
        ]);

        if ($validated->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validated->errors(),
            ], 422);
        }

        $validated = $validated->validated();
        // 2. Fetch order
        $order = Order::where('id', $validated['order_id'])->first();
        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        // 3. Fetch restaurant
        $restaurant = Restaurant::where('id', $order->restaurant_id)->first();
        if (! $restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant not found for this order.',
            ], 404);
        }

        $customerId = $order->customer_id;

        $createdReviews = [];
        foreach ($validated['reviews'] as $reviewData) {
            // 4. Resolve reviewable_id
            if ($reviewData['reviewable_type'] === 'restaurant') {
                $reviewableId = $restaurant->id;
            } elseif ($reviewData['reviewable_type'] === 'delivery_partner') {
                $assignment = DeliveryAssignment::where('order_id', $order->id)
                    ->whereNotNull('partner_id')
                    ->whereIn('status', ['accepted', 'picked_up', 'delivered'])
                    ->latest()
                    ->first();
                if (! $assignment) {
                    $createdReviews[] = [
                        'success' => false,
                        'message' => 'Delivery partner not assigned to this order yet.',
                        'reviewable_type' => 'delivery_partner',
                    ];

                    continue;
                }
                if (! $assignment->partner_id) {
                    $createdReviews[] = [
                        'success' => false,
                        'message' => 'Delivery partner ID missing in assignment record.',
                        'reviewable_type' => 'delivery_partner',
                    ];

                    continue;
                }
                $reviewableId = $assignment->partner_id;
            } else {
                $createdReviews[] = [
                    'success' => false,
                    'message' => 'Invalid reviewable type.',
                    'reviewable_type' => $reviewData['reviewable_type'],
                ];

                continue;
            }

            // 5. Prevent duplicate review for same order + type
            $alreadyReviewed = Review::where('order_id', $order->id)
                ->where('reviewable_type', $reviewData['reviewable_type'])
                ->exists();
            if ($alreadyReviewed) {
                $createdReviews[] = [
                    'success' => false,
                    'message' => 'You have already reviewed this order.',
                    'reviewable_type' => $reviewData['reviewable_type'],
                ];

                continue;
            }

            // 6. Create review
            $review = Review::create([
                'order_id' => $order->id,
                'tenant_id' => $restaurant->tenant_id,
                'customer_id' => $customerId,
                'reviewable_type' => $reviewData['reviewable_type'],
                'reviewable_id' => $reviewableId,
                'rating' => $reviewData['rating'],
                'review_text' => $reviewData['comment'] ?? null,
                'images' => $request->images ?? null,
                'is_anonymous' => $request->is_anonymous ?? false,
                'is_featured' => false,
            ]);
            $createdReviews[] = [
                'success' => true,
                'message' => 'Review added successfully.',
                'reviewable_type' => $reviewData['reviewable_type'],
                'data' => $review,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Reviews processed.',
            'results' => $createdReviews,
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
