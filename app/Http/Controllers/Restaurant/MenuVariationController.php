<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\MenuVariation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MenuVariationController extends Controller
{
    /**
     * Display variations for a menu item
     */
    public function index(MenuItem $menuItem): View
    {
        $user = Auth::user();

        // Ensure user can only view variations from their tenant
        if ($menuItem->tenant_id !== $user->tenant_id) {
            abort(403, 'Access denied.');
        }

        $variations = $menuItem->variations()
            ->orderBy('sort_order')
            ->orderBy('variation_name')
            ->get();

        return view('pages.restaurant_staff.menu_variations', compact('menuItem', 'variations'));
    }

    /**
     * Show the form for creating a new variation
     */
    public function create(MenuItem $menuItem): View
    {
        $user = Auth::user();

        // Ensure user can only create variations for their tenant's items
        if ($menuItem->tenant_id !== $user->tenant_id) {
            abort(403, 'Access denied.');
        }

        return view('pages.restaurant_staff.add_variation', compact('menuItem'));
    }

    /**
     * Store a newly created variation
     */
    public function store(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $user = Auth::user();

        // Ensure user can only create variations for their tenant's items
        if ($menuItem->tenant_id !== $user->tenant_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'variation_name' => 'required|string|max:255',
            'variation_type' => 'required|in:size,topping,addon,sauce,cooking_style,spice_level',
            'price_modifier' => 'required|numeric',
            'price_modifier_type' => 'required|in:fixed,percentage',
            'description' => 'nullable|string|max:500',
            'is_available' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'max_selections' => 'nullable|integer|min:0',
            'is_required' => 'nullable|boolean',
        ]);

        $validated['menu_item_id'] = $menuItem->id;
        $validated['is_available'] = $validated['is_available'] ?? true;
        $validated['is_required'] = $validated['is_required'] ?? false;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        MenuVariation::create($validated);

        // Update menu item to indicate it has variations
        $menuItem->update(['has_variations' => true]);

        return redirect()->route('restaurant.menu.variations.index', $menuItem)
            ->with('success', 'Variation created successfully!');
    }

    /**
     * Show the form for editing a variation
     */
    public function edit(MenuItem $menuItem, MenuVariation $variation): View
    {
        $user = Auth::user();

        // Ensure user can only edit variations from their tenant
        if ($menuItem->tenant_id !== $user->tenant_id || $variation->menuItem->tenant_id !== $user->tenant_id) {
            abort(403, 'Access denied.');
        }

        return view('pages.restaurant_staff.add_variation', compact('menuItem', 'variation'));
    }

    /**
     * Update a variation
     */
    public function update(Request $request, MenuItem $menuItem, MenuVariation $variation): RedirectResponse
    {
        $user = Auth::user();

        // Ensure user can only update variations from their tenant
        if ($menuItem->tenant_id !== $user->tenant_id || $variation->menuItem->tenant_id !== $user->tenant_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'variation_name' => 'required|string|max:255',
            'variation_type' => 'required|in:size,topping,addon,sauce,cooking_style,spice_level',
            'price_modifier' => 'required|numeric',
            'price_modifier_type' => 'required|in:fixed,percentage',
            'description' => 'nullable|string|max:500',
            'is_available' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'max_selections' => 'nullable|integer|min:0',
            'is_required' => 'nullable|boolean',
        ]);

        $validated['is_available'] = $validated['is_available'] ?? false;
        $validated['is_required'] = $validated['is_required'] ?? false;

        $variation->update($validated);

        return redirect()->route('restaurant.menu.variations.index', $menuItem)
            ->with('success', 'Variation updated successfully!');
    }

    /**
     * Remove a variation
     */
    public function destroy(MenuItem $menuItem, MenuVariation $variation): RedirectResponse
    {
        $user = Auth::user();

        // Ensure user can only delete variations from their tenant
        if ($menuItem->tenant_id !== $user->tenant_id || $variation->menuItem->tenant_id !== $user->tenant_id) {
            abort(403, 'Access denied.');
        }

        $variation->delete();

        // Check if menu item still has variations
        if ($menuItem->variations()->count() === 0) {
            $menuItem->update(['has_variations' => false]);
        }

        return redirect()->route('restaurant.menu.variations.index', $menuItem)
            ->with('success', 'Variation deleted successfully!');
    }

    /**
     * Toggle variation availability
     */
    public function toggleAvailability(MenuItem $menuItem, MenuVariation $variation): JsonResponse
    {
        $user = Auth::user();

        // Ensure user can only toggle variations from their tenant
        if ($menuItem->tenant_id !== $user->tenant_id || $variation->menuItem->tenant_id !== $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $variation->update(['is_available' => ! $variation->is_available]);

        return response()->json([
            'success' => true,
            'is_available' => $variation->is_available,
            'message' => 'Variation availability updated successfully!',
        ]);
    }

    /**
     * Update variation sort order
     */
    public function updateSortOrder(Request $request, MenuItem $menuItem): JsonResponse
    {
        $user = Auth::user();

        // Ensure user can only update variations from their tenant
        if ($menuItem->tenant_id !== $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $validated = $request->validate([
            'variations' => 'required|array',
            'variations.*.id' => 'required|exists:menu_variations,id',
            'variations.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['variations'] as $variationData) {
            $variation = MenuVariation::find($variationData['id']);

            // Ensure variation belongs to the menu item and user's tenant
            if ($variation && $variation->menu_item_id === $menuItem->id && $variation->menuItem->tenant_id === $user->tenant_id) {
                $variation->update(['sort_order' => $variationData['sort_order']]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Variation order updated successfully!',
        ]);
    }

    /**
     * Bulk create variations
     */
    public function bulkStore(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $user = Auth::user();

        // Ensure user can only create variations for their tenant's items
        if ($menuItem->tenant_id !== $user->tenant_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'variations' => 'required|array|min:1',
            'variations.*.variation_name' => 'required|string|max:255',
            'variations.*.variation_type' => 'required|in:size,topping,addon,sauce,cooking_style,spice_level',
            'variations.*.price_modifier' => 'required|numeric',
            'variations.*.price_modifier_type' => 'required|in:fixed,percentage',
            'variations.*.description' => 'nullable|string|max:500',
            'variations.*.is_available' => 'nullable|boolean',
            'variations.*.is_required' => 'nullable|boolean',
        ]);

        foreach ($validated['variations'] as $index => $variationData) {
            $variationData['menu_item_id'] = $menuItem->id;
            $variationData['is_available'] = $variationData['is_available'] ?? true;
            $variationData['is_required'] = $variationData['is_required'] ?? false;
            $variationData['sort_order'] = $index;

            MenuVariation::create($variationData);
        }

        // Update menu item to indicate it has variations
        $menuItem->update(['has_variations' => true]);

        return redirect()->route('restaurant.menu.variations.index', $menuItem)
            ->with('success', count($validated['variations']).' variations created successfully!');
    }

    /**
     * Get variations for a menu item (API endpoint)
     */
    public function getVariations(MenuItem $menuItem): JsonResponse
    {
        $user = Auth::user();

        // Ensure user can only view variations from their tenant
        if ($menuItem->tenant_id !== $user->tenant_id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $variations = $menuItem->variations()
            ->where('is_available', true)
            ->orderBy('sort_order')
            ->orderBy('variation_name')
            ->get();

        return response()->json([
            'success' => true,
            'variations' => $variations,
        ]);
    }
}
