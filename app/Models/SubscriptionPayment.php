<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'subscription_plan',
        'restaurant_count',
        'base_amount',
        'per_restaurant_amount',
        'total_amount',
        'billing_period_start',
        'billing_period_end',
        'payment_method',
        'payment_gateway',
        'gateway_transaction_id',
        'status',
        'due_date',
        'paid_at',
        'failure_reason',
        'auto_retry_count',
        'next_retry_date',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'per_restaurant_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'next_retry_date' => 'date',
        'auto_retry_count' => 'integer',
    ];

    // Status Constants
    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_COMPLETED,
        self::STATUS_FAILED,
        self::STATUS_CANCELLED,
    ];

    // Payment Method Constants
    public const PAYMENT_METHOD_CARD = 'card';

    public const PAYMENT_METHOD_UPI = 'upi';

    public const PAYMENT_METHOD_NETBANKING = 'netbanking';

    public const PAYMENT_METHOD_WALLET = 'wallet';

    public const PAYMENT_METHODS = [
        self::PAYMENT_METHOD_CARD,
        self::PAYMENT_METHOD_UPI,
        self::PAYMENT_METHOD_NETBANKING,
        self::PAYMENT_METHOD_WALLET,
    ];

    // Payment Gateway Constants - Only Stripe supported
    public const GATEWAY_STRIPE = 'stripe';

    public const GATEWAYS = [
        self::GATEWAY_STRIPE,
    ];

    /**
     * Relationships
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeCurrentBillingPeriod($query)
    {
        $today = now()->toDateString();

        return $query->where('billing_period_start', '<=', $today)
            ->where('billing_period_end', '>=', $today);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now()->toDateString())
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_FAILED]);
    }

    /**
     * Helper Methods
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isOverdue()
    {
        return $this->due_date < now()->toDateString() &&
               in_array($this->status, [self::STATUS_PENDING, self::STATUS_FAILED]);
    }

    public function markAsCompleted($gatewayTransactionId = null)
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'paid_at' => now(),
            'gateway_transaction_id' => $gatewayTransactionId,
            'failure_reason' => null,
        ]);

        // Update tenant subscription status
        $this->tenant->update([
            'status' => Tenant::STATUS_APPROVED,
            'subscription_start_date' => $this->billing_period_start,
            'next_billing_date' => \Carbon\Carbon::parse($this->billing_period_end)->addDay()->toDateString(),
            'approved_at' => now(),
        ]);

        // Update tenant admin user status to active if they were pending approval
        $tenantAdmin = $this->tenant->users()->where('role', 'tenant_admin')->first();
        if ($tenantAdmin && $tenantAdmin->status === 'pending_approval') {
            $tenantAdmin->update([
                'status' => 'active',
                'email_verified_at' => $tenantAdmin->email_verified_at ?: now(),
            ]);
        }
    }

    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'failure_reason' => $reason,
            'auto_retry_count' => $this->auto_retry_count + 1,
            'next_retry_date' => now()->addDays(3)->toDateString(),
        ]);
    }

    /**
     * Calculate billing period for a given plan and start date
     */
    public static function calculateBillingPeriod($startDate = null)
    {
        $start = $startDate ? \Carbon\Carbon::parse($startDate) : now();
        $end = $start->copy()->addMonth()->subDay();

        return [
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'due_date' => $end->toDateString(),
        ];
    }

    /**
     * Calculate total amount based on plan and restaurant count
     */
    public static function calculateAmount($plan, $restaurantCount, $baseAmount, $perRestaurantAmount)
    {
        $total = $baseAmount + ($restaurantCount * $perRestaurantAmount);

        return round($total, 2);
    }
}
