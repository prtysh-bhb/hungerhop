<?php

namespace App\Http\Controllers\Admin\Restaurant;

use App\Actions\Menu\RollbackMenuVersionAction;
use App\Actions\Menu\SaveMenuVersionAction;
use App\Http\Controllers\Controller;
use App\Models\MenuVersion;
use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MenuVersionController extends Controller
{
    public function store(Request $request, Restaurant $restaurant, SaveMenuVersionAction $action): RedirectResponse
    {
        $request->validate(['version_name' => 'required|string|max:255']);

        $action->execute($restaurant, $request->input('version_name'));

        return redirect()->back()->with('success', 'Menu version saved successfully.');
    }

    public function rollback(MenuVersion $version, RollbackMenuVersionAction $action): RedirectResponse
    {
        $action->execute($version);

        return redirect()->back()->with('success', 'Menu rolled back to selected version.');
    }
}
