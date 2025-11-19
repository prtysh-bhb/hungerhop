<?php

namespace App\Services;

class TenantContextService
{
    protected ?int $tenantId = null;

    public function setTenantId(?int $tenantId): void
    {
        session(['tenant_id' => $tenantId]);
        $this->tenantId = $tenantId;
    }

    public function getTenantId(): ?int
    {
        return $this->tenantId ?? session('tenant_id');
    }

    public function forgetTenant(): void
    {
        session()->forget('tenant_id');
        $this->tenantId = null;
    }
}
