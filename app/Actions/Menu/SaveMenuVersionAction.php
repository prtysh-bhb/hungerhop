<?php

namespace App\Actions\Menu;

use App\Models\MenuVersion;
use App\Models\Restaurant;
use Exception;
use Log;

class SaveMenuVersionAction
{
    public function execute(Restaurant $restaurant, string $versionName): void
    {
        try {
            $menuSnapshot = $restaurant->menuItems()->with('parentItem.variations')->get()->toArray();

            MenuVersion::create([
                'restaurant_id' => $restaurant->id,
                'version_name' => $versionName,
                'snapshot' => $menuSnapshot,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to save menu version: '.$e->getMessage());
            throw new \RuntimeException('Could not save menu version');
        }
    }
}
