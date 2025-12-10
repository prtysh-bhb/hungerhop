<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentDetail extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payment_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'pay_type',
        'pay_bank_name',
        'pay_bank_account_number',
        'pay_bank_ifsc',
        'pay_upi_id',
        'account_holder_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        //
    ];

    /**
     * Pay type constants
     */
    const PAY_TYPE_BANK = 'bank';

    const PAY_TYPE_UPI = 'upi';

    /**
     * Get the user that owns the payment detail.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallet transactions for this payment detail.
     */
    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'payment_detail_id');
    }

    /**
     * Scope to get only bank payment details.
     */
    public function scopeBank($query)
    {
        return $query->where('pay_type', self::PAY_TYPE_BANK);
    }

    /**
     * Scope to get only UPI payment details.
     */
    public function scopeUpi($query)
    {
        return $query->where('pay_type', self::PAY_TYPE_UPI);
    }

    /**
     * Check if payment detail is bank type.
     */
    public function isBank(): bool
    {
        return $this->pay_type === self::PAY_TYPE_BANK;
    }

    /**
     * Check if payment detail is UPI type.
     */
    public function isUpi(): bool
    {
        return $this->pay_type === self::PAY_TYPE_UPI;
    }

    /**
     * Get masked account number for display.
     */
    public function getMaskedAccountNumberAttribute(): ?string
    {
        if (! $this->pay_bank_account_number) {
            return null;
        }

        $length = strlen($this->pay_bank_account_number);
        if ($length <= 4) {
            return $this->pay_bank_account_number;
        }

        return str_repeat('*', $length - 4).substr($this->pay_bank_account_number, -4);
    }

    /**
     * Get masked UPI ID for display.
     */
    public function getMaskedUpiIdAttribute(): ?string
    {
        if (! $this->pay_upi_id) {
            return null;
        }

        $parts = explode('@', $this->pay_upi_id);
        if (count($parts) !== 2) {
            return $this->pay_upi_id;
        }

        $username = $parts[0];
        $provider = $parts[1];

        if (strlen($username) <= 3) {
            return $this->pay_upi_id;
        }

        $maskedUsername = substr($username, 0, 2).str_repeat('*', strlen($username) - 3).substr($username, -1);

        return $maskedUsername.'@'.$provider;
    }
}
