<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Get available coupons for customer
     * GET /api/v1/coupons
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $restaurantId = $request->input('restaurant_id');
        $subtotal = (float) $request->input('subtotal', 0);

        $coupons = Coupon::query()
            ->active()
            ->valid()
            ->where(function ($q) use ($restaurantId) {
                $q->where('coupon_scope', 'global');

                if ($restaurantId) {
                    $q->orWhere(function ($sq) use ($restaurantId) {
                        $sq->where('coupon_scope', 'restaurant')
                            ->where('restaurant_id', $restaurantId);
                    });
                }
            })
            ->get()
            ->filter(function ($coupon) use ($user, $subtotal) {

                // Minimum order check
                if ($subtotal > 0 && $subtotal < $coupon->min_order_value) {
                    return false;
                }

                // Total usage limit
                if ($coupon->usage_limit !== null &&
                    $coupon->usages()->count() >= $coupon->usage_limit) {
                    return false;
                }

                // Per-user usage limit
                if ($coupon->usages()
                    ->where('user_id', $user->id)
                    ->count() >= $coupon->usage_per_user) {
                    return false;
                }

                return true;
            })
            ->values()
            ->map(function ($coupon) {
                return [
                    'id' => (string) $coupon->id,
                    'code' => $coupon->code,
                    'title' => $coupon->title,
                    'description' => $coupon->description,
                    'discount_type' => $coupon->discount_type,
                    'discount_value' => $coupon->discount_value,
                    'max_discount' => $coupon->max_discount,
                    'min_order_value' => $coupon->min_order_value,
                ];
            });

        return response()->json([
            'success' => true,
            'count' => $coupons->count(),
            'coupons' => $coupons,
        ]);
    }
}
