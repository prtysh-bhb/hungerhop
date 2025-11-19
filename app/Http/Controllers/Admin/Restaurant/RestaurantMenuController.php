<?php

namespace App\Http\Controllers\Admin\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Services\Menu\MenuInheritanceService;
use Illuminate\Http\RedirectResponse;

class RestaurantMenuController extends Controller
{
    public function inheritFromTenant(Restaurant $restaurant, MenuInheritanceService $service): RedirectResponse
    {
        $service->inherit($restaurant);

        return redirect()->route('admin.restaurant.menu.index', $restaurant->id)
            ->with('success', 'Menu inherited from tenant successfully.');
    }

    public function index(Restaurant $restaurant)
    {
        $menu = $restaurant->menuItems()->with('parentItem.variations')->get();

        return view('admin.restaurant.menu.index', compact('restaurant', 'menu'));
    }
}
