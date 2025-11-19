<?php

namespace App\Actions\Menu;

use App\Models\MenuVersion;
use App\Models\RestaurantMenuItem;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RollbackMenuVersionAction
{
    public function execute(MenuVersion $version): void
    {
        try {
            DB::transaction(function () use ($version) {
                $restaurantId = $version->restaurant_id;

                RestaurantMenuItem::where('restaurant_id', $restaurantId)->delete();

                foreach ($version->snapshot as $item) {
                    RestaurantMenuItem::create([
                        'restaurant_id' => $restaurantId,
                        'parent_menu_item_id' => $item['parent_menu_item_id'],
                        'name' => $item['name'],
                        'description' => $item['description'],
                        'price' => $item['price'],
                        'image_url' => $item['image_url'],
                    ]);
                }
            });
        } catch (Exception $e) {
            Log::error('Failed to rollback menu version: '.$e->getMessage());
            throw new \RuntimeException('Could not rollback menu version');
        }
    }
}
