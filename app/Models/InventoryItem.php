<?php

namespace App\Models;

class InventoryItem extends BaseTenantModel
{
    protected $fillable = ['restaurant_id', 'item_name', 'stock_quantity', 'is_available'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
