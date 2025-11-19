<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the restaurant dashboard.
     * For restaurant_staff, location_admin, and delivery_partner roles.
     */
    public function index()
    {
        $user = Auth::user();

        // Ensure only restaurant/delivery roles can access this dashboard
        if (! in_array($user->role, ['restaurant_staff', 'location_admin', 'delivery_partner'])) {
            abort(403, 'Unauthorized access.');
        }

        return view('pages.restaurants.dashboard.index', [
            'user' => $user,
            'dashboardType' => 'restaurant',
        ]);
    }
}
