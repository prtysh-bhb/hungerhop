<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Searchable;
use App\Traits\TenantScoped;
use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuCategory extends BaseTenantModel
{
    use Auditable, HasFactory, Searchable, SoftDeletes, TenantScoped, TracksActivity;

    protected $fillable = [
        'tenant_id',
        'restaurant_id',
        'name',
        'description',
        'image_url',
        'sort_order',
        'is_active',
        'menu_template_id',
    ];

    protected array $searchable = [
        'name',
        'description',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    // Alias for compatibility with controller expecting menuItems
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'menu_category_id');
    }

    public function items()
    {
        return $this->hasMany(MenuItem::class, 'menu_category_id');
    }

    public function template()
    {
        return $this->belongsTo(MenuTemplate::class, 'menu_template_id');
    }
}
