<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScoped;
use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends BaseTenantModel
{
    use Auditable, HasFactory, SoftDeletes, TenantScoped, TracksActivity;

    // Alias for compatibility with API/Blade expecting orderItems
    public function orderItems()
    {
        return $this->items();
    }

    use Auditable, HasFactory, SoftDeletes, TenantScoped;

    protected $fillable = [
        'order_number',
        'customer_id',
        'restaurant_id',
        'delivery_address_id',
        'tenant_id',
        'status',
        'subtotal',
        'tax_amount',
        'delivery_fee',
        'discount_amount',
        'total_amount',
        'restaurant_amount',
        'delivery_amount',
        'platform_fee',
        'payment_method',
        'payment_status',
        'special_instructions',
        'pickup_otp',
        'delivery_otp',
        'pickup_otp_verified_at',
        'delivery_otp_verified_at',
        'estimated_delivery_time',
        'actual_delivery_time',
        'cancellation_reason',
        'rejection_reason',
        'cancelled_by',
        'cancelled_at',
        'auto_accept_at',
    ];

    protected array $searchable = [
        'order_number',
        'status',
        'payment_method',
        'payment_status',
        'cancellation_reason',
        'rejection_reason',
    ];

    /**
     * Attribute casting for dates, timestamps, decimals.
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'restaurant_amount' => 'decimal:2',
        'delivery_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',

        'pickup_otp_verified_at' => 'datetime',
        'delivery_otp_verified_at' => 'datetime',
        'estimated_delivery_time' => 'datetime',
        'actual_delivery_time' => 'datetime',
        'cancelled_at' => 'datetime',
        'auto_accept_at' => 'datetime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(CustomerProfile::class, 'customer_id');
    }

    public function address()
    {
        return $this->belongsTo(CustomerAddress::class, 'delivery_address_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryAddress()
    {
        return $this->belongsTo(CustomerAddress::class, 'delivery_address_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // Relation to menu_items table
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'item_id');
    }
}
