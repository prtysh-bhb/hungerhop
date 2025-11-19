<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerProfile extends Model
{
    use Auditable, HasFactory, Searchable, SoftDeletes;

    protected $fillable = [
        'user_id',
        'date_of_birth',
        'gender',
        'profile_image_url',
        'total_orders',
        'total_spent',
        'loyalty_points',
        'referral_code',
        'referred_by',
    ];

    protected array $searchable = [
        'referral_code',
        'gender',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'total_spent' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class, 'customer_id');
    }

    public function referredBy()
    {
        return $this->belongsTo(CustomerProfile::class, 'referred_by');
    }

    public function referrals()
    {
        return $this->hasMany(CustomerProfile::class, 'referred_by');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function address()
    {
        // Returns the latest address for the customer (customize as needed)
        return $this->hasOne(CustomerAddress::class, 'customer_id')->latestOfMany();
    }
}
