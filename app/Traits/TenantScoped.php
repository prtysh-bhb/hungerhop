<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use App\Services\TenantContextService;

trait TenantScoped
{
    protected static function bootTenantScoped(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (! $model->tenant_id) {
                $model->tenant_id = app(TenantContextService::class)->getTenantId();
            }
        });
    }
}
