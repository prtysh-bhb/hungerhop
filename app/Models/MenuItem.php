<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Searchable;
use App\Traits\TenantScoped;
use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends BaseTenantModel
{
    use Auditable, HasFactory, Searchable, SoftDeletes, TenantScoped, TracksActivity;

    protected $fillable = [
        'tenant_id',
        'restaurant_id',
        'menu_category_id',
        'item_name',
        'description',
        'base_price',
        'image_url',
        'is_vegetarian',
        'is_vegan',
        'is_gluten_free',
        'ingredients',
        'allergens',
        'preparation_time',
        'is_available',
        'is_popular',
        'sort_order',
        'total_sales',
        'average_rating',
        'total_reviews',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected array $searchable = [
        'item_name',
        'description',
        'ingredients',
        'allergens',
    ];

    protected $casts = [
        'is_vegetarian' => 'boolean',
        'is_vegan' => 'boolean',
        'is_gluten_free' => 'boolean',
        'is_available' => 'boolean',
        'is_popular' => 'boolean',
        'base_price' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function variations()
    {
        return $this->hasMany(MenuVariation::class);
    }
}
