<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantWorkingHour extends BaseTenantModel
{
    use Auditable, HasFactory, SoftDeletes, TenantScoped;

    protected $fillable = [
        'restaurant_id',
        'tenant_id',
        'day_of_week',
        'is_open',
        'open_time',
        'close_time',
        'break_start_time',
        'break_end_time',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
