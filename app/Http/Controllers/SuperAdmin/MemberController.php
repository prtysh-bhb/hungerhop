<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Tenant;
use App\Models\User;

class MemberController extends Controller
{
    public function index()
    {
        // Get all users with role 'tenant_admin'
        // $users = User::where('role', 'tenant_admin')->get();
        $tenants = Tenant::all();

        return view('pages.super_admin.members.index', compact('tenants'));
    }

    public function show_details($id)
    {
        // Get all tenants
        $tenants = Tenant::all();

        // Get all restaurants where tenant_id matches any tenant's id
        $tenantIds = $tenants->pluck('id');
        // $restaurants = Restaurant::whereIn('tenant_id', $tenantIds)->get();

        $restaurants = Restaurant::where('tenant_id', $id)->get();

        return view('pages.super_admin.members.show_details', compact('restaurants', 'tenants'));
    }
}
