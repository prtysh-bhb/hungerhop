<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the customer dashboard.
     * For customer role.
     */
    public function index()
    {
        $user = Auth::user();

        // Ensure only customers can access this dashboard
        if ($user->role !== 'customer') {
            abort(403, 'Unauthorized access.');
        }

        // For now, redirect customers to the restaurant dashboard
        // You can create a separate customer dashboard later
        return view('pages.restaurants.dashboard.index', [
            'user' => $user,
            'dashboardType' => 'customer',
        ]);
    }
}
