<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantPayment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Only check for tenant admins
        if (! $user || $user->role !== 'tenant_admin') {
            return $next($request);
        }

        // Get the tenant
        $tenant = $user->tenant;
        if (! $tenant) {
            return redirect()->route('login')->with('error', 'Tenant not found.');
        }

        // Check if payment is required or user status is pending_approval
        $requiresPayment = $tenant->isPaymentRequired() || $user->status === 'pending_approval';

        if ($requiresPayment) {
            // Allow access to dashboard and payment routes only
            $allowedRoutes = [
                'admin.dashboard.tenant',
                'admin.tenant.payment.plans',
                'admin.tenant.payment.checkout',
                'admin.tenant.payment.create',
                'admin.tenant.payment.success',
                'admin.tenant.payment.failure',
                'admin.tenant.payment.history',
                'admin.tenant.payment.invoice',
                'logout',
            ];

            $currentRoute = $request->route()->getName();

            // Check if current route is allowed
            $isAllowed = in_array($currentRoute, $allowedRoutes);

            // If trying to access restricted route, redirect to dashboard with message
            if (! $isAllowed) {
                $message = $user->status === 'pending_approval'
                    ? 'Please complete your subscription payment to activate your account and access all features.'
                    : 'Payment is required to access this feature. Please complete your subscription payment.';

                return redirect()->route('admin.dashboard.tenant')
                    ->with('payment_required', $message);
            }
        }

        return $next($request);
    }
}
