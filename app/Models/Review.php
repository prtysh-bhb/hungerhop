<?php

namespace App\Models;

use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends Model
{
    use TracksActivity;

    protected $fillable = [
        'order_id',
        'customer_id',
        'tenant_id',
        'rating',
        'review_text',
        'reviewable_id',
        'reviewable_type',
    ];

    /**
     * Get the parent reviewable model (restaurant, etc).
     */
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the customer who wrote this review.
     */
    public function customer()
    {
        return $this->belongsTo(CustomerProfile::class, 'customer_id', 'id');
    }

    /**
     * Get the user who wrote this review (through customer profile).
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            CustomerProfile::class,
            'id', // Foreign key on CustomerProfile table (what customer_id references)
            'id', // Foreign key on User table (what user_id references)
            'customer_id', // Local key on Review table
            'user_id' // Local key on CustomerProfile table
        );
    }

    /**
     * Get the order this review belongs to.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
