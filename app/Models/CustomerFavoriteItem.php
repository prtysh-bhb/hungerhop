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
        'type',            // ✅ NEW
        'item_id',
        'restaurant_id',
        'tenant_id',
        'added_at',
    ];

    protected $casts = [
        'added_at'   => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Constants for favorite types
     */
    public const TYPE_MENU_ITEM = 'menu_item';
    public const TYPE_RESTAURANT = 'restaurant';

    /* =====================
     | Relationships
     ===================== */

    /**
     * Customer who owns this favorite
     */
    public function customer()
    {
        return $this->belongsTo(CustomerProfile::class, 'customer_id');
    }

    /**
     * Favorited menu item (only when type = menu_item)
     */
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'item_id');
    }

    /**
     * Favorited restaurant
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    /**
     * Tenant (multi-tenant support)
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /* =====================
     | Query Scopes (Optional but Recommended)
     ===================== */

    public function scopeMenuItems($query)
    {
        return $query->where('type', self::TYPE_MENU_ITEM);
    }

    public function scopeRestaurants($query)
    {
        return $query->where('type', self::TYPE_RESTAURANT);
    }
}
