<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponAdminController extends Controller
{
    /**
     * Show all coupons
     */
    public function index()
    {
        $coupons = Coupon::orderByDesc('created_at')->get();

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show add coupon form
     */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /**
     * Store new coupon
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',

            'discount_type' => 'required|in:flat,percentage',

            'discount_value' => [
                'required',
                'numeric',
                'min:1',
                function ($attr, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percentage' && $value > 100) {
                        $fail('Percentage discount cannot exceed 100.');
                    }
                },
            ],

            'max_discount' => 'nullable|numeric|min:0',
            'min_order_value' => 'required|numeric|min:0',

            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_user' => 'required|integer|min:1',

            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',

            'coupon_scope' => 'required|in:global,restaurant',

            'is_active' => 'nullable|boolean',
        ]);

        // Handle checkbox safely
        $validated['is_active'] = $request->has('is_active');

        // Optional: set creator
        $validated['created_by'] = auth()->id(); // preferred

        Coupon::create($validated);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon added successfully!');
    }

    // In your CouponController.php
    public function toggle(Coupon $coupon)
    {
        try {
            $coupon->update([
                'is_active' => ! $coupon->is_active,
            ]);

            // For AJAX requests
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'is_active' => $coupon->is_active,
                ]);
            }

            // For regular form submission
            return redirect()->back()
                ->with('success', 'Coupon status updated successfully.');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update status',
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update coupon status.');
        }
    }

    /**
     * Delete coupon
     */
    public function destroy($id)
    {
        Coupon::where('id', $id)->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted.');
    }

    /**
     * Toggle is_active
     */
    // public function toggleActive($id)
    // {
    //     $coupon = Coupon::findOrFail($id);
    //     $coupon->is_active = ! $coupon->is_active;
    //     $coupon->save();

    //     return redirect()->route('admin.coupons.index')->with('success', 'Coupon status updated.');
    // }
}
