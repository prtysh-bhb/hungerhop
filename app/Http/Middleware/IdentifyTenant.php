<?php

namespace App\Http\Middleware;

use App\Services\TenantContextService;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected TenantContextService $tenantContext;

    public function __construct(TenantContextService $tenantContext)
    {
        $this->tenantContext = $tenantContext;
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {

            $user = Auth::user();

            if ($user->hasRole('super_admin')) {
                $tenantId = session('active_tenant_id');
                if ($tenantId) {
                    $this->tenantContext->setTenantId($tenantId);
                } else {
                    $this->tenantContext->forgetTenant();
                }
            } elseif (in_array($user->role, ['tenant_admin', 'location_admin', 'restaurant_staff'])) {
                $this->tenantContext->setTenantId($user->tenant_id);
            } else {
                $this->tenantContext->forgetTenant();
            }
        }

        return $next($request);
    }
}
