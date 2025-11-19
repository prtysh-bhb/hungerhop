<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Searchable;
use App\Traits\TenantScoped;
use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use Auditable, HasFactory, Searchable, SoftDeletes, TenantScoped, TracksActivity;

    protected $fillable = [
        'order_id',
        'item_id',
        'tenant_id',
        'item_name',
        'quantity',
        'unit_price',
        'total_price',
        'special_instructions',
    ];

    protected array $searchable = [
        'item_name',
        'special_instructions',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relationship for menuItem (for compatibility with Blade usage)
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'item_id');
    }

    public function customizations()
    {
        return $this->hasMany(OrderItemCustomization::class);
    }
}
