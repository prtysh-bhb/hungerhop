<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Searchable;
use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantBanner extends Model
{
    use Auditable, HasFactory, Searchable, SoftDeletes, TenantScoped;

    protected $fillable = [
        'restaurant_id',
        'tenant_id',
        'title',
        'description',
        'image_url',
        'link_type',
        'link_id',
        'external_url',
        'banner_position',
        'sort_order',
        'click_count',
        'is_active',
        'valid_from',
        'valid_until',
    ];

    protected array $searchable = [
        'title',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'click_count' => 'integer',
        'sort_order' => 'integer',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
