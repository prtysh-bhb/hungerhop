<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantSwitchController extends Controller
{
    public function switch(Request $request)
    {
        $tenantId = $request->input('tenant_id');

        if (! Tenant::where('id', $tenantId)->exists()) {
            return back()->with('error', 'Invalid tenant selected');
        }

        session(['active_tenant_id' => $tenantId]);

        return back()->with('success', 'Tenant switched successfully');
    }

    public function clear()
    {
        session()->forget('active_tenant_id');

        return back()->with('success', 'Tenant context cleared');
    }
}
