<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use SoftDeletes;

    protected $table = 'wallets';

    protected $fillable = [
        'user_id',
        'user_type',
        'current_balance',
        'pending_balance',
        'available_balance',
        'total_credited',
        'total_debited',
        'is_active',
        'daily_transfer_limit',
        'monthly_transfer_limit',
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'available_balance' => 'decimal:2',
        'total_credited' => 'decimal:2',
        'total_debited' => 'decimal:2',
        'daily_transfer_limit' => 'decimal:2',
        'monthly_transfer_limit' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the wallet
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
