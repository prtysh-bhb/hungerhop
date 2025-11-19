<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryAssignment extends Model
{
    use SoftDeletes;

    protected $table = 'delivery_assignments';

    protected $fillable = [
        'order_id',
        'tenant_id',
        'partner_id',
        'assigned_by',
        'pickup_latitude',
        'pickup_longitude',
        'delivery_latitude',
        'delivery_longitude',
        'estimated_distance_km',
        'estimated_duration_minutes',
        'delivery_fee',
        'tip_amount',
        'status',
        'assigned_at',
        'accepted_at',
        'picked_up_at',
        'delivered_at',
        'rejection_reason',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relationships (optional, for eager loading)
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }
}
