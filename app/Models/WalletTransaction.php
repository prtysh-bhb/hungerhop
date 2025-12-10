<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletTransaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wallet_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'payment_detail_id',
        'type',
        'reason',
        'amount',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Transaction type constants
     */
    const TYPE_IN = 'in';

    const TYPE_OUT = 'out';

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';

    const STATUS_COMPLETED = 'completed';

    const STATUS_FAILED = 'failed';

    const STATUS_CANCELLED = 'cancelled';

    /**
     * Common reason constants
     */
    const REASON_ORDER_PAYMENT = 'order_payment';

    const REASON_ORDER_REFUND = 'order_refund';

    const REASON_WITHDRAWAL = 'withdrawal';

    const REASON_DEPOSIT = 'deposit';

    const REASON_CASHBACK = 'cashback';

    const REASON_PROMOTIONAL_CREDIT = 'promotional_credit';

    const REASON_DELIVERY_EARNINGS = 'delivery_earnings';

    const REASON_ADJUSTMENT = 'adjustment';

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        // When a transaction is created
        static::created(function ($model) {
            if ($model->status === self::STATUS_COMPLETED) {
                self::updateUserWalletBalance($model);
            }
        });

        // When a transaction is updated (e.g., status changes)
        static::updated(function ($model) {
            // Check if status changed to completed
            if ($model->isDirty('status') && $model->status === self::STATUS_COMPLETED) {
                self::updateUserWalletBalance($model);
            }

            // If status changed from completed to failed/cancelled, reverse the transaction
            if ($model->isDirty('status') && $model->getOriginal('status') === self::STATUS_COMPLETED) {
                if (in_array($model->status, [self::STATUS_FAILED, self::STATUS_CANCELLED])) {
                    self::reverseUserWalletBalance($model);
                }
            }
        });
    }

    /**
     * Update user wallet balance based on transaction type
     */
    protected static function updateUserWalletBalance($transaction)
    {
        $user = User::find($transaction->user_id);
        if (! $user) {
            return;
        }

        $amount = (float) $transaction->amount;

        if ($transaction->type === self::TYPE_IN) {
            // Credit: Add to wallet
            $user->wallet_balance = (float) $user->wallet_balance + $amount;
        } elseif ($transaction->type === self::TYPE_OUT) {
            // Debit: Subtract from wallet
            $user->wallet_balance = (float) $user->wallet_balance - $amount;
        }

        $user->save();
    }

    /**
     * Reverse user wallet balance (when transaction is cancelled/failed after completion)
     */
    protected static function reverseUserWalletBalance($transaction)
    {
        $user = User::find($transaction->user_id);
        if (! $user) {
            return;
        }

        $amount = (float) $transaction->amount;

        if ($transaction->type === self::TYPE_IN) {
            // Reverse credit: Subtract from wallet
            $user->wallet_balance = (float) $user->wallet_balance - $amount;
        } elseif ($transaction->type === self::TYPE_OUT) {
            // Reverse debit: Add back to wallet
            $user->wallet_balance = (float) $user->wallet_balance + $amount;
        }

        $user->save();
    }

    /**
     * Get the user that owns the wallet transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payment detail associated with this transaction.
     */
    public function paymentDetail(): BelongsTo
    {
        return $this->belongsTo(PaymentDetail::class, 'payment_detail_id');
    }

    /**
     * Scope to get only credit (in) transactions.
     */
    public function scopeCredit($query)
    {
        return $query->where('type', self::TYPE_IN);
    }

    /**
     * Scope to get only debit (out) transactions.
     */
    public function scopeDebit($query)
    {
        return $query->where('type', self::TYPE_OUT);
    }

    /**
     * Scope to get only completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope to get only pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Check if transaction is a credit.
     */
    public function isCredit(): bool
    {
        return $this->type === self::TYPE_IN;
    }

    /**
     * Check if transaction is a debit.
     */
    public function isDebit(): bool
    {
        return $this->type === self::TYPE_OUT;
    }

    /**
     * Check if transaction is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Get formatted amount with sign.
     */
    public function getFormattedAmountAttribute(): string
    {
        $sign = $this->isCredit() ? '+' : '-';

        return $sign.'₹'.number_format($this->amount, 2);
    }

    /**
     * Create a credit transaction for a user.
     */
    public static function credit(
        int $userId,
        float $amount,
        string $reason,
        ?int $paymentDetailId = null
    ): self {
        $user = User::findOrFail($userId);
        $balanceAfter = $user->wallet_balance + $amount;

        // Update user wallet balance
        $user->wallet_balance = $balanceAfter;
        $user->save();

        return self::create([
            'user_id' => $userId,
            'payment_detail_id' => $paymentDetailId,
            'type' => self::TYPE_IN,
            'reason' => $reason,
            'amount' => $amount,
            'status' => self::STATUS_COMPLETED,
        ]);
    }

    /**
     * Create a debit transaction for a user.
     */
    public static function debit(
        int $userId,
        float $amount,
        string $reason,
        ?int $paymentDetailId = null
    ): self {
        $user = User::findOrFail($userId);

        if ($user->wallet_balance < $amount) {
            throw new \Exception('Insufficient wallet balance');
        }

        $balanceAfter = $user->wallet_balance - $amount;

        // Update user wallet balance
        $user->wallet_balance = $balanceAfter;
        $user->save();

        return self::create([
            'user_id' => $userId,
            'payment_detail_id' => $paymentDetailId,
            'type' => self::TYPE_OUT,
            'reason' => $reason,
            'amount' => $amount,
            'status' => self::STATUS_COMPLETED,
        ]);
    }
}
