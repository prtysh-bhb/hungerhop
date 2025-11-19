<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemCustomization extends Model
{
    use HasFactory, TenantScoped;

    protected $fillable = [
        'order_item_id',
        'variation_id',
        'option_id',
        'tenant_id',
        'variation_name',
        'option_name',
        'price_modifier',
    ];

    protected $casts = [
        'price_modifier' => 'decimal:2',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
