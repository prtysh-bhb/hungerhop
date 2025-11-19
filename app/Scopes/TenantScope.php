<?php

namespace App\Scopes;

use App\Services\TenantContextService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    protected TenantContextService $tenantContext;

    public function __construct()
    {
        $this->tenantContext = app(TenantContextService::class);
    }

    public function apply(Builder $builder, Model $model)
    {
        $tenantId = $this->tenantContext->getTenantId();

        if ($tenantId) {
            $builder->where($model->getTable().'.tenant_id', $tenantId);
        }
    }
}
