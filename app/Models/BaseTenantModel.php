<?php

namespace App\Models;

use App\Scopes\TenantScope;
use App\Services\TenantContextService;
use Illuminate\Database\Eloquent\Model;

class BaseTenantModel extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (! $model->tenant_id) {
                $model->tenant_id = app(TenantContextService::class)->getTenantId();
            }
        });
    }
}
