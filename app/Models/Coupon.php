<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';

    protected $fillable = [
        'code',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'max_discount',
        'min_order_value',
        'usage_limit',
        'usage_per_user',
        'valid_from',
        'valid_to',
        'coupon_scope',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'discount_value' => 'float',
        'max_discount' => 'float',
        'min_order_value' => 'float',
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
    ];

    /* =========================
     | Relationships
     ========================= */
    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    /* =========================
     | Scopes
     ========================= */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = Carbon::now();

        return $query->where(function ($q) use ($now) {
            $q->whereNull('valid_from')
                ->orWhere('valid_from', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('valid_to')
                ->orWhere('valid_to', '>=', $now);
        });
    }

    /* =========================
     | Business Helpers
     ========================= */

    /**
     * Check total usage limit
     */
    public function isUsageLimitReached(): bool
    {
        if ($this->usage_limit === null) {
            return false;
        }

        return $this->usages()->count() >= $this->usage_limit;
    }

    /**
     * Check per-user usage limit
     */
    public function isUserLimitReached(int $userId): bool
    {
        return $this->usages()
            ->where('user_id', $userId)
            ->count() >= $this->usage_per_user;
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(float $orderAmount): float
    {
        if ($orderAmount < $this->min_order_value) {
            return 0;
        }

        if ($this->discount_type === 'flat') {
            return min($this->discount_value, $orderAmount);
        }

        // Percentage
        $discount = ($orderAmount * $this->discount_value) / 100;

        if ($this->max_discount !== null) {
            $discount = min($discount, $this->max_discount);
        }

        return round($discount, 2);
    }
}
