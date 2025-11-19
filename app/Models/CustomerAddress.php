<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAddress extends Model
{
    use Auditable, HasFactory, Searchable, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'address_type',
        'address_line1',
        'address_line2',
        'landmark',
        'city',
        'state',
        'postal_code',
        'latitude',
        'longitude',
        'is_default',
    ];

    protected array $searchable = [
        'city',
        'state',
        'postal_code',
        'landmark',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_default' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
