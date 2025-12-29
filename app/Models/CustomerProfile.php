<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Traits\Auditable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerProfile extends Model

{
    use Auditable, HasFactory, Searchable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'date_of_birth',
        'gender',
        'profile_image_url',
        'total_orders',
        'total_spent',
        'loyalty_points',
        'referral_code',
        'referred_by',
    ];

    protected array $searchable = [
        'referral_code',
        'gender',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'total_spent' => 'decimal:2',
    ];

    public function user()
    {
        // Use withoutGlobalScope to bypass TenantScope since customers may not have tenant_id
        return $this->belongsTo(User::class)->withoutGlobalScope(TenantScope::class);
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class, 'customer_id');
    }

    public function referredBy()
    {
        return $this->belongsTo(CustomerProfile::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(CustomerProfile::class, 'referred_by');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function address()
    {
        // Returns the latest address for the customer (customize as needed)
        return $this->hasOne(CustomerAddress::class, 'customer_id')->latestOfMany();
    }
    
    /**
     * Check if a restaurant is in the customer's favorites
     */
    public function hasFavoriteRestaurant($restaurantId)
    {
        return $this->favoriteItems()->where('restaurant_id', $restaurantId)->where('type', 'restaurant')->exists();
    }

    /**
     * Check if a menu item is in the customer's favorites
     */
    public function hasFavoriteMenuItem($itemId)
    {
        return $this->favoriteItems()->where('item_id', $itemId)->where('type', 'menu_item')->exists();
    }

    /**
     * Relationship: all favorite items for this customer
     */
    public function favoriteItems()
    {
        return $this->hasMany(\App\Models\CustomerFavoriteItem::class, 'customer_id');
    }
}
