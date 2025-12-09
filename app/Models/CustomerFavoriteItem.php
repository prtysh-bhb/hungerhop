<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerFavoriteItem extends Model
{
    use SoftDeletes;

    protected $table = 'customer_favorite_items';

    protected $fillable = [
        'customer_id',
        'item_id',
        'restaurant_id',
        'tenant_id',
        'added_at',
    ];

    protected $casts = [
        'added_at' => 'datetime',
    ];

    /**
     * Get the customer profile that owns this favorite
     */
    public function customer()
    {
        return $this->belongsTo(CustomerProfile::class, 'customer_id');
    }

    /**
     * Get the menu item that is favorited
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'item_id');
    }

    /**
     * Get the restaurant of the favorited item
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    /**
     * Get the tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
