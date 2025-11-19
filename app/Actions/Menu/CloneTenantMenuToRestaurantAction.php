<?php

namespace App\Actions\Menu;

use App\Models\MenuTemplate;
use App\Models\Restaurant;
use App\Models\RestaurantMenuItem;
use Illuminate\Support\Facades\DB;

class CloneTenantMenuToRestaurantAction
{
    public function execute(Restaurant $restaurant): void
    {
        DB::transaction(function () use ($restaurant) {
            $template = MenuTemplate::where('tenant_id', $restaurant->tenant_id)->first();
            if (! $template) {
                return;
            }

            foreach ($template->categories as $category) {
                foreach ($category->items as $item) {
                    $cloned = RestaurantMenuItem::create([
                        'restaurant_id' => $restaurant->id,
                        'parent_menu_item_id' => $item->id,
                        'name' => $item->name,
                        'description' => $item->description,
                        'price' => $item->price,
                        'image_url' => $item->image_url,
                    ]);
                }
            }
        });
    }
}
