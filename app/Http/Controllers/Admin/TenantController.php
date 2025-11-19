<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    /**
     * Display a listing of tenants
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Tenant::query();

        // Role-based filtering
        if ($user->role === 'tenant_admin') {
            // Tenant admin can only see their own tenant
            $query->where('id', $user->tenant_id);
        }
        // Super admin can see all tenants (no additional filter)

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tenant_name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('contact_person', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('subscription_plan')) {
            $query->where('subscription_plan', $request->subscription_plan);
        }

        $tenants = $query->latest()->paginate(15);

        // Get statistics based on user role
        if ($user->role === 'tenant_admin') {
            // For tenant admin, show stats only for their tenant
            $tenantId = $user->tenant_id;
            $stats = [
                'total' => Tenant::where('id', $tenantId)->count(),
                'pending' => Tenant::where('id', $tenantId)->where('status', Tenant::STATUS_PENDING)->count(),
                'approved' => Tenant::where('id', $tenantId)->where('status', Tenant::STATUS_APPROVED)->count(),
                'suspended' => Tenant::where('id', $tenantId)->where('status', Tenant::STATUS_SUSPENDED)->count(),
                'rejected' => Tenant::where('id', $tenantId)->where('status', Tenant::STATUS_REJECTED)->count(),
            ];
        } else {
            // For super admin, show stats for all tenants
            $stats = [
                'total' => Tenant::count(),
                'pending' => Tenant::where('status', Tenant::STATUS_PENDING)->count(),
                'approved' => Tenant::where('status', Tenant::STATUS_APPROVED)->count(),
                'suspended' => Tenant::where('status', Tenant::STATUS_SUSPENDED)->count(),
                'rejected' => Tenant::where('status', Tenant::STATUS_REJECTED)->count(),
            ];
        }

        return view('pages.admin.tenants.index', compact('tenants', 'stats'));
    }

    /**
     * Show the form for creating a new tenant
     */
    public function create()
    {
        return view('pages.admin.tenants.create');
    }

    /**
     * Store a newly created tenant
     */
    public function store(Request $request)
    {
        // Define plan limits
        $planLimits = [
            'LITE' => [
                'max_restaurants' => 5,
                'max_banners' => 1,
                'base_fee' => 1200,
                'per_restaurant_fee' => 500,
            ],
            'PLUS' => [
                'max_restaurants' => 20,
                'max_banners' => 3,
                'base_fee' => 2000,
                'per_restaurant_fee' => 1000,
            ],
            'PRO_MAX' => [
                'max_restaurants' => 30,
                'max_banners' => 10,
                'base_fee' => 2500,
                'per_restaurant_fee' => 1500,
            ],
        ];

        $validated = $request->validate([
            'tenant_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'subscription_plan' => 'required|in:'.implode(',', Tenant::PLANS),
            'total_restaurants' => 'required|integer|min:1|max:1000',
            'monthly_base_fee' => 'required|numeric|min:0',
            'per_restaurant_fee' => 'required|numeric|min:0',
            'banner_limit' => 'required|integer|min:1|max:100',
            'subscription_start_date' => 'nullable|date',
            'next_billing_date' => 'nullable|date|after:subscription_start_date',
            'status' => 'nullable|in:'.implode(',', Tenant::STATUSES),
        ]);

        // Set default status if not provided
        if (empty($validated['status'])) {
            $validated['status'] = 'pending';
        }

        // Remove subscription date fields - these will be filled when payment is completed
        unset($validated['subscription_start_date']);
        unset($validated['next_billing_date']);

        // Validate against plan limits
        $selectedPlan = $validated['subscription_plan'];
        if (isset($planLimits[$selectedPlan])) {
            $limits = $planLimits[$selectedPlan];

            // Check restaurant limit
            if ($validated['total_restaurants'] > $limits['max_restaurants']) {
                return back()->withInput()->withErrors([
                    'total_restaurants' => "For {$selectedPlan} plan, maximum restaurants allowed is {$limits['max_restaurants']}. You entered {$validated['total_restaurants']}.",
                ]);
            }

            // Check banner limit
            if ($validated['banner_limit'] > $limits['max_banners']) {
                return back()->withInput()->withErrors([
                    'banner_limit' => "For {$selectedPlan} plan, maximum banners allowed is {$limits['max_banners']}. You entered {$validated['banner_limit']}.",
                ]);
            }

            // Validate pricing (allow some flexibility but warn if significantly different)
            $expectedBaseFee = $limits['base_fee'];
            $expectedPerRestaurantFee = $limits['per_restaurant_fee'];

            if (abs($validated['monthly_base_fee'] - $expectedBaseFee) > ($expectedBaseFee * 0.2)) {
                return back()->withInput()->withErrors([
                    'monthly_base_fee' => "For {$selectedPlan} plan, recommended base fee is ₹{$expectedBaseFee}. Your entered amount (₹{$validated['monthly_base_fee']}) differs significantly.",
                ]);
            }

            if (abs($validated['per_restaurant_fee'] - $expectedPerRestaurantFee) > ($expectedPerRestaurantFee * 0.2)) {
                return back()->withInput()->withErrors([
                    'per_restaurant_fee' => "For {$selectedPlan} plan, recommended per restaurant fee is ₹{$expectedPerRestaurantFee}. Your entered amount (₹{$validated['per_restaurant_fee']}) differs significantly.",
                ]);
            }
        }

        // Set approved_at and approved_by if status is approved
        // if ($validated['status'] === Tenant::STATUS_APPROVED) {
        //     $validated['approved_at'] = now();
        //     $validated['approved_by'] = Auth::id();
        // }

        DB::beginTransaction();

        try {
            // Create tenant
            $tenant = Tenant::create($validated);

            // Create user account for tenant admin
            // Split contact person name into first and last name
            $nameParts = explode(' ', trim($validated['contact_person']), 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['phone']), // Phone as password
                'tenant_id' => $tenant->id,
                'role' => 'tenant_admin',
                'status' => 'pending_approval',
            ]);

            DB::commit();

            return redirect()->route('admin.tenants.index')
                ->with('success', 'Tenant created successfully! User account has been created with email as username and phone as password.');

        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()
                ->withErrors(['error' => 'Failed to create tenant: '.$e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified tenant
     */
    public function show(Tenant $tenant)
    {
        $user = Auth::user();

        // Check if tenant admin is trying to access another tenant
        if ($user->role === 'tenant_admin' && $tenant->id !== $user->tenant_id) {
            abort(403, 'Unauthorized: You can only access your own tenant.');
        }

        $tenant->load(['restaurants']);

        // Get tenant statistics
        $stats = [
            'total_restaurants' => $tenant->restaurants()->count(),
            'active_restaurants' => $tenant->restaurants()->where('status', 'approved')->count(),
            'pending_restaurants' => $tenant->restaurants()->where('status', 'pending')->count(),
            'revenue_this_month' => 0, // You can calculate this based on your business logic
        ];

        return view('pages.admin.tenants.show', compact('tenant', 'stats'));
    }

    /**
     * Show the form for editing the specified tenant
     */
    public function edit(Tenant $tenant)
    {
        $user = Auth::user();

        // Check if tenant admin is trying to edit another tenant
        if ($user->role === 'tenant_admin' && $tenant->id !== $user->tenant_id) {
            abort(403, 'Unauthorized: You can only edit your own tenant.');
        }

        return view('pages.admin.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified tenant
     */
    public function update(Request $request, Tenant $tenant)
    {
        $user = Auth::user();

        // Check if tenant admin is trying to update another tenant
        if ($user->role === 'tenant_admin' && $tenant->id !== $user->tenant_id) {
            abort(403, 'Unauthorized: You can only update your own tenant.');
        }
        $validated = $request->validate([
            'tenant_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('tenants')->ignore($tenant->id)],
            'phone' => 'required|string|max:20',
            'subscription_plan' => 'required|in:'.implode(',', Tenant::PLANS),
            'total_restaurants' => 'required|integer|min:1|max:1000',
            'monthly_base_fee' => 'required|numeric|min:0',
            'per_restaurant_fee' => 'required|numeric|min:0',
            'banner_limit' => 'required|integer|min:1|max:100',
            'subscription_start_date' => 'required|date',
            'next_billing_date' => 'required|date|after:subscription_start_date',
            'status' => 'required|in:'.implode(',', Tenant::STATUSES),
        ]);

        // Set approved_at and approved_by if status is being changed to approved
        if ($validated['status'] === Tenant::STATUS_APPROVED && $tenant->status !== Tenant::STATUS_APPROVED) {
            $validated['approved_at'] = now();
            $validated['approved_by'] = Auth::id();
        }

        $tenant->update($validated);

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant updated successfully!');
    }

    /**
     * Remove the specified tenant
     */
    public function destroy(Tenant $tenant)
    {
        $user = Auth::user();

        // Only super admin can delete tenants
        if ($user->role !== 'super_admin') {
            abort(403, 'Unauthorized: Only super admin can delete tenants.');
        }

        // Check if tenant has restaurants
        if ($tenant->restaurants()->count() > 0) {
            return redirect()->route('admin.tenants.index')
                ->with('error', 'Cannot delete tenant with associated restaurants. Please remove restaurants first.');
        }

        $tenant->delete();

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant deleted successfully!');
    }

    /**
     * Update tenant status
     */
    public function updateStatus(Request $request, Tenant $tenant)
    {
        $user = Auth::user();

        // Check if tenant admin is trying to update another tenant's status
        if ($user->role === 'tenant_admin' && $tenant->id !== $user->tenant_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: You can only update your own tenant status.',
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:'.implode(',', Tenant::STATUSES),
            'reason' => 'nullable|string|max:500',
        ]);

        $oldStatus = $tenant->status;

        // Set approved_at and approved_by if status is being changed to approved
        if ($validated['status'] === Tenant::STATUS_APPROVED && $oldStatus !== Tenant::STATUS_APPROVED) {
            $tenant->approved_at = now();
            $tenant->approved_by = Auth::id();
        }

        $tenant->status = $validated['status'];
        $tenant->save();

        return response()->json([
            'success' => true,
            'message' => 'Tenant status updated successfully!',
            'new_status' => $validated['status'],
        ]);
    }
}
