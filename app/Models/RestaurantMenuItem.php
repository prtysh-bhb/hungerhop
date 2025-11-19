<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantMenuItem extends BaseTenantModel
{
    use SoftDeletes;

    protected $fillable = ['restaurant_id', 'parent_menu_item_id', 'name', 'description', 'price', 'image_url'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function parentItem()
    {
        return $this->belongsTo(MenuItem::class, 'parent_menu_item_id');
    }
}
