<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\StoreMenuItemRequest;
use App\Http\Requests\Menu\UpdateMenuItemRequest;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Services\MenuItemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    protected MenuItemService $menuItemService;

    public function __construct(MenuItemService $menuItemService)
    {
        $this->menuItemService = $menuItemService;
    }

    /**
     * Display a listing of menu items
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            abort(403, 'Access denied. User not properly configured.');
        }

        $menuItems = $this->menuItemService->getMenuItems($request->all());
        $categories = MenuCategory::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('pages.restaurant_staff.menu.index', compact('menuItems', 'categories'));
    }

    /**
     * Show the form for creating a new menu item
     */
    public function create(): View
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            abort(403, 'Access denied. User not properly configured.');
        }

        $categories = MenuCategory::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('pages.restaurant_staff.menu.create', compact('categories'));
    }

    /**
     * Store a newly created menu item
     */
    public function store(StoreMenuItemRequest $request): RedirectResponse
    {
        try {
            \Log::info('=== STORE METHOD START ===');
            \Log::info('Raw request data:', $request->all());
            \Log::info('User info:', [
                'id' => Auth::id(),
                'tenant_id' => Auth::user()->tenant_id ?? 'null',
                'restaurant_id' => Auth::user()->restaurant_id ?? 'null',
                'role' => Auth::user()->role ?? 'null',
            ]);

            $validatedData = $request->validated();
            \Log::info('Validated data:', $validatedData);

            $menuItem = $this->menuItemService->create($validatedData);
            \Log::info('Menu item created successfully', ['id' => $menuItem->id]);
            \Log::info('=== STORE METHOD SUCCESS ===');

            return redirect()->route('restaurant.menu.list')
                ->with('success', 'Menu item created successfully!');
        } catch (\Exception $e) {
            \Log::error('=== STORE METHOD ERROR ===');
            \Log::error('Failed to create menu item: '.$e->getMessage());
            \Log::error('Stack trace: '.$e->getTraceAsString());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create menu item: '.$e->getMessage());
        }
    }

    /**
     * Show the form for editing a menu item
     */
    public function edit(MenuItem $menuItem): View
    {
        $user = Auth::user();
        if (! $user || ! $user->tenant_id) {
            abort(403, 'Access denied. User not properly configured.');
        }

        // Ensure user can only edit items from their tenant
        if ($menuItem->tenant_id !== $user->tenant_id) {
            abort(403, 'Access denied.');
        }

        $categories = MenuCategory::where('tenant_id', $user->tenant_id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('pages.restaurant_staff.menu.create', compact('menuItem', 'categories'));
    }

    /**
     * Update a menu item
     */
    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem): RedirectResponse
    {
        try {
            $user = Auth::user();

            // Ensure user can only update items from their tenant
            if ($menuItem->tenant_id !== $user->tenant_id) {
                abort(403, 'Access denied.');
            }

            $this->menuItemService->update($menuItem, $request->validated());

            return redirect()->route('restaurant.menu.list')
                ->with('success', 'Menu item updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to update menu item: '.$e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update menu item: '.$e->getMessage());
        }
    }

    /**
     * Remove a menu item
     */
    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        try {
            $user = Auth::user();

            // Ensure user can only delete items from their tenant
            if ($menuItem->tenant_id !== $user->tenant_id) {
                abort(403, 'Access denied.');
            }

            $this->menuItemService->delete($menuItem);

            return redirect()->route('restaurant.menu.list')
                ->with('success', 'Menu item deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to delete menu item: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to delete menu item: '.$e->getMessage());
        }
    }

    /**
     * Toggle menu item availability
     */
    public function toggleAvailability(MenuItem $menuItem): RedirectResponse
    {
        try {
            $user = Auth::user();

            // Ensure user can only toggle items from their tenant
            if ($menuItem->tenant_id !== $user->tenant_id) {
                abort(403, 'Access denied.');
            }

            $menuItem->update(['is_available' => ! $menuItem->is_available]);

            $status = $menuItem->is_available ? 'available' : 'unavailable';

            return redirect()->back()
                ->with('success', "Menu item marked as {$status}!");
        } catch (\Exception $e) {
            \Log::error('Failed to toggle menu item availability: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to update menu item availability.');
        }
    }

    /**
     * Display the specified menu item
     */
    public function show(MenuItem $menuItem): View
    {
        $user = Auth::user();
        if (! $user || $menuItem->tenant_id !== $user->tenant_id) {
            abort(403, 'Access denied.');
        }

        return view('pages.restaurant_staff.menu.show', compact('menuItem'));
    }

    /**
     * Duplicate a menu item
     */
    public function duplicate(MenuItem $menuItem): RedirectResponse
    {
        try {
            $user = Auth::user();
            if (! $user || $menuItem->tenant_id !== $user->tenant_id) {
                abort(403, 'Access denied.');
            }

            $duplicateData = $menuItem->toArray();

            // Remove unique fields and modify for duplication
            unset($duplicateData['id'], $duplicateData['created_at'], $duplicateData['updated_at'], $duplicateData['deleted_at']);

            // Add "(Copy)" to the name and generate new SKU
            $duplicateData['item_name'] = $menuItem->item_name.' (Copy)';
            $duplicateData['sku'] = null; // Will be auto-generated by service

            // Reset sales data
            $duplicateData['total_sales'] = 0;
            $duplicateData['total_reviews'] = 0;
            $duplicateData['average_rating'] = 0.00;

            $duplicatedItem = $this->menuItemService->create($duplicateData);

            return redirect()->route('restaurant.menu.list')
                ->with('success', "Menu item '{$duplicatedItem->item_name}' has been duplicated successfully!");
        } catch (\Exception $e) {
            \Log::error('Failed to duplicate menu item: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to duplicate menu item.');
        }
    }
}
