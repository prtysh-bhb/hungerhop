<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Searchable;
use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use Auditable, HasFactory, Searchable, SoftDeletes, TenantScoped;

    protected $fillable = [
        'tenant_id',
        'restaurant_id',
        'created_by',
        'promotion_code',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'minimum_order_amount',
        'maximum_discount_amount',
        'usage_limit_per_customer',
        'total_usage_limit',
        'current_usage_count',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected array $searchable = [
        'promotion_code',
        'title',
        'description',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'maximum_discount_amount' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
