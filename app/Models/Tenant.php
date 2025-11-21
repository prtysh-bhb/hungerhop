<?php

namespace App\Models;

use App\Traits\Searchable;
use App\Traits\TracksActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, Searchable, SoftDeletes, TracksActivity;

    protected $table = 'tenants';

    protected $fillable = [
        'tenant_name',
        'contact_person',
        'email',
        'phone',
        'subscription_plan',
        'total_restaurants',
        'monthly_base_fee',
        'per_restaurant_fee',
        'banner_limit',
        'status',
        'subscription_start_date',
        'next_billing_date',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'subscription_start_date' => 'date',
        'next_billing_date' => 'date',
        'approved_at' => 'datetime',
        'monthly_base_fee' => 'decimal:2',
        'per_restaurant_fee' => 'decimal:2',
    ];

    public const PLAN_LITE = 'LITE';

    public const PLAN_PLUS = 'PLUS';

    public const PLAN_PRO_MAX = 'PRO_MAX';

    public const PLANS = [
        self::PLAN_LITE,
        self::PLAN_PLUS,
        self::PLAN_PRO_MAX,
    ];

    // Status Constants
    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_SUSPENDED = 'suspended';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_SUBSCRIPTION_EXPIRED = 'subscription_expired';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_SUSPENDED,
        self::STATUS_REJECTED,
        self::STATUS_SUBSCRIPTION_EXPIRED,
    ];

    protected array $searchable = ['tenant_name', 'email', 'phone'];

    /**
     * Relationships
     */
    public function restaurants()
    {
        return $this->hasMany(Restaurant::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscriptionPayments()
    {
        return $this->hasMany(SubscriptionPayment::class);
    }

    /**
     * Payment and Subscription Status Methods
     */
    public function hasActiveSubscription()
    {
        // Check if tenant has a completed payment for current billing period
        return $this->subscriptionPayments()
            ->completed()
            ->currentBillingPeriod()
            ->exists();
    }

    public function hasCompletedAnyPayment()
    {
        // Check if tenant has ever completed a payment
        return $this->subscriptionPayments()
            ->completed()
            ->exists();
    }

    public function getCurrentSubscriptionPayment()
    {
        return $this->subscriptionPayments()
            ->currentBillingPeriod()
            ->latest()
            ->first();
    }

    public function getLatestSubscriptionPayment()
    {
        return $this->subscriptionPayments()
            ->latest()
            ->first();
    }

    public function isPaymentRequired()
    {
        // Payment is required if:
        // 1. No completed payments exist, OR
        // 2. No active subscription for current period, OR
        // 3. Status is pending
        return ! $this->hasCompletedAnyPayment() ||
               ! $this->hasActiveSubscription() ||
               $this->status === self::STATUS_PENDING;
    }

    public function canAccessFeatures()
    {
        // Can access features if:
        // 1. Status is approved, AND
        // 2. Has active subscription, AND
        // 3. Not suspended
        return $this->status === self::STATUS_APPROVED &&
               $this->hasActiveSubscription() &&
               $this->status !== self::STATUS_SUSPENDED;
    }

    public function getSubscriptionStatusText()
    {
        if (! $this->hasCompletedAnyPayment()) {
            return 'Payment Required';
        }

        if (! $this->hasActiveSubscription()) {
            return 'Subscription Expired';
        }

        switch ($this->status) {
            case self::STATUS_PENDING:
                return 'Payment Required';
            case self::STATUS_APPROVED:
                return 'Active';
            case self::STATUS_SUSPENDED:
                return 'Suspended';
            case self::STATUS_REJECTED:
                return 'Rejected';
            default:
                return ucfirst($this->status);
        }
    }

    public function getSubscriptionStatusClass()
    {
        if (! $this->hasCompletedAnyPayment() || $this->status === self::STATUS_PENDING) {
            return 'warning';
        }

        if (! $this->hasActiveSubscription()) {
            return 'danger';
        }

        switch ($this->status) {
            case self::STATUS_APPROVED:
                return 'success';
            case self::STATUS_SUSPENDED:
            case self::STATUS_REJECTED:
                return 'danger';
            default:
                return 'secondary';
        }
    }

    /**
     * Get plan limits for any plan (not just current)
     */
    public function getPlanLimits($plan = null)
    {
        $targetPlan = $plan ?? $this->subscription_plan;

        $planLimits = [
            self::PLAN_LITE => [
                'max_restaurants' => 5,
                'max_banners' => 1,
                'base_fee' => 1200,
                'per_restaurant_fee' => 500,
                'name' => 'Lite Plan',
            ],
            self::PLAN_PLUS => [
                'max_restaurants' => 20,
                'max_banners' => 3,
                'base_fee' => 2000,
                'per_restaurant_fee' => 1000,
                'name' => 'Plus Plan',
            ],
            self::PLAN_PRO_MAX => [
                'max_restaurants' => 30,
                'max_banners' => 10,
                'base_fee' => 2500,
                'per_restaurant_fee' => 1500,
                'name' => 'Pro Max Plan',
            ],
        ];

        return $planLimits[$targetPlan] ?? null;
    }

    public function calculateSubscriptionAmount()
    {
        $limits = $this->getPlanLimits();
        if (! $limits) {
            return 0;
        }

        return $limits['base_fee'] + ($limits['max_restaurants'] * $limits['per_restaurant_fee']);
    }

    /**
     * Calculate upgrade cost when changing to a new plan
     * Returns only the difference amount to be paid
     */
    public function calculateUpgradeCost($newPlan)
    {
        $currentPlanLimits = $this->getPlanLimits();
        $newPlanLimits = $this->getPlanLimits($newPlan);

        if (! $currentPlanLimits || ! $newPlanLimits) {
            return null;
        }

        // Get current subscription payment to calculate remaining days
        $currentSubscription = $this->getCurrentSubscriptionPayment();

        if (! $currentSubscription) {
            // No current subscription, charge full amount for new plan
            return $newPlanLimits['base_fee'] + ($newPlanLimits['max_restaurants'] * $newPlanLimits['per_restaurant_fee']);
        }

        // Check if payment was made within last 3 days (special pricing window)
        $paymentDate = $currentSubscription->paid_at ?? $currentSubscription->created_at;
        $daysSincePayment = now()->diffInDays($paymentDate);
        $isWithin3DayWindow = $daysSincePayment <= 3;

        // Calculate remaining days in current billing period
        $today = now();
        $billingEndDate = $currentSubscription->billing_period_end;
        $remainingDays = $today->diffInDays($billingEndDate, false);

        if ($remainingDays <= 0) {
            // Current plan has expired
            if ($isWithin3DayWindow) {
                // Still within 3-day window, charge only difference
                $currentPlanTotal = $currentPlanLimits['base_fee'] + ($currentPlanLimits['max_restaurants'] * $currentPlanLimits['per_restaurant_fee']);
                $newPlanTotal = $newPlanLimits['base_fee'] + ($newPlanLimits['max_restaurants'] * $newPlanLimits['per_restaurant_fee']);

                return max(0, $newPlanTotal - $currentPlanTotal);
            } else {
                // Outside 3-day window, charge full amount for new plan
                return $newPlanLimits['base_fee'] + ($newPlanLimits['max_restaurants'] * $newPlanLimits['per_restaurant_fee']);
            }
        }

        // Special 3-day window pricing: Only charge the difference between plans
        if ($isWithin3DayWindow) {
            $currentPlanTotal = $currentPlanLimits['base_fee'] + ($currentPlanLimits['max_restaurants'] * $currentPlanLimits['per_restaurant_fee']);
            $newPlanTotal = $newPlanLimits['base_fee'] + ($newPlanLimits['max_restaurants'] * $newPlanLimits['per_restaurant_fee']);
            $planDifference = $newPlanTotal - $currentPlanTotal;

            // Return only the difference (can be negative for downgrades)
            return max(0, $planDifference);
        }

        // Standard prorated billing for changes after 3 days
        // Calculate daily rate for both plans
        $daysInMonth = 30; // Assuming 30-day billing cycle
        $currentDailyRate = ($currentPlanLimits['base_fee'] + ($currentPlanLimits['max_restaurants'] * $currentPlanLimits['per_restaurant_fee'])) / $daysInMonth;
        $newDailyRate = ($newPlanLimits['base_fee'] + ($newPlanLimits['max_restaurants'] * $newPlanLimits['per_restaurant_fee'])) / $daysInMonth;

        // Calculate refund for remaining days of current plan
        $refundAmount = $currentDailyRate * $remainingDays;

        // Calculate cost for remaining days of new plan
        $newPlanCost = $newDailyRate * $remainingDays;

        // Return the difference (what user needs to pay extra)
        $upgradeCost = $newPlanCost - $refundAmount;

        return max(0, $upgradeCost); // Ensure non-negative
    }

    /**
     * Check if a plan upgrade is available
     */
    public function canUpgradeToPlan($newPlan)
    {
        $currentPlanLimits = $this->getPlanLimits();
        $newPlanLimits = $this->getPlanLimits($newPlan);

        if (! $currentPlanLimits || ! $newPlanLimits) {
            return false;
        }

        // Check if new plan is actually an upgrade (higher base fee)
        return $newPlanLimits['base_fee'] > $currentPlanLimits['base_fee'];
    }

    /**
     * Check if user is within 3-day special pricing window
     */
    public function isWithin3DayPricingWindow()
    {
        $currentSubscription = $this->getCurrentSubscriptionPayment();

        if (! $currentSubscription) {
            return false;
        }

        $paymentDate = $currentSubscription->paid_at ?? $currentSubscription->created_at;
        $daysSincePayment = now()->diffInDays($paymentDate);

        return $daysSincePayment <= 3;
    }

    /**
     * Get pricing window information for display
     */
    public function getPricingWindowInfo()
    {
        $currentSubscription = $this->getCurrentSubscriptionPayment();

        if (! $currentSubscription) {
            return null;
        }

        $paymentDate = $currentSubscription->paid_at ?? $currentSubscription->created_at;
        $daysSincePayment = now()->diffInDays($paymentDate);
        $isWithinWindow = $daysSincePayment <= 3;
        $remainingDays = max(0, 3 - $daysSincePayment);

        return [
            'is_within_window' => $isWithinWindow,
            'days_since_payment' => $daysSincePayment,
            'remaining_days' => $remainingDays,
            'payment_date' => $paymentDate,
            'window_expires_at' => $paymentDate->copy()->addDays(3),
        ];
    }
}
