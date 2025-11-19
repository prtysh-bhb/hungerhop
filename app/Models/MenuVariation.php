<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class MenuVariation extends BaseTenantModel
{
    use SoftDeletes;

    protected $fillable = ['menu_item_id', 'label', 'price_delta'];

    public function item()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
