<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Searchable;
use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryPartner extends Model
{
    use Auditable, HasFactory, Searchable, SoftDeletes, TracksActivity;

    protected $fillable = [
        'user_id',
        'vehicle_type',
        'vehicle_number',
        'license_number',
        'profile_image_url',
        'current_latitude',
        'current_longitude',
        'is_available',
        'is_online',
        'total_deliveries',
        'total_earnings',
        'average_rating',
        'total_reviews',
        'commission_percentage',
        'last_location_update',
        'status',
        'approved_at',
        'approved_by',
    ];

    protected array $searchable = [
        'vehicle_number',
        'license_number',
        'status',
    ];

    protected $casts = [
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8',
        'total_earnings' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'is_available' => 'boolean',
        'is_online' => 'boolean',
        'approved_at' => 'datetime',
        'last_location_update' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(DeliveryPartnerDocument::class, 'partner_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
