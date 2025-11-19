<?php

namespace App\Models;

use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, TracksActivity;

    protected $fillable = [
        'order_id',
        'tenant_id',
        'payment_method',
        'payment_gateway',
        'gateway_transaction_id',
        'gateway_payment_id',
        'amount',
        'currency',
        'status',
        'gateway_response',
        'failure_reason',
        'initiated_at',
        'completed_at',
    ];

    protected $dates = [
        'initiated_at',
        'completed_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
