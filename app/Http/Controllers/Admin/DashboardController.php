<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     * For super_admin and tenant_admin roles.
     */
    public function index()
    {
        $user = Auth::user();

        // Ensure only admin roles can access this dashboard
        if (! in_array($user->role, ['super_admin', 'tenant_admin'])) {
            abort(403, 'Unauthorized access.');
        }

        return view('pages.admin.dashboard.index', [
            'user' => $user,
            'dashboardType' => 'admin',
        ]);
    }
}
