<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'faqs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tenant_id',
        'question',
        'answer',
        'target_role',
        'category',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /* =====================
     | Relationships
     ===================== */

    /**
     * Tenant relationship (if you have Tenant model)
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /* =====================
     | Query Scopes
     ===================== */

    /**
     * Only active FAQs
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Filter by role (customer, restaurant, delivery_partner, admin)
     */
    public function scopeForRole(Builder $query, string $role): Builder
    {
        return $query->whereIn('target_role', [$role, 'all']);
    }

    /**
     * Filter by tenant (includes global FAQs)
     */
    public function scopeForTenant(Builder $query, ?int $tenantId): Builder
    {
        return $query->where(function ($q) use ($tenantId) {
            $q->whereNull('tenant_id')
                ->orWhere('tenant_id', $tenantId);
        });
    }

    /**
     * Order FAQs properly
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')
            ->orderBy('id', 'desc');
    }
}
