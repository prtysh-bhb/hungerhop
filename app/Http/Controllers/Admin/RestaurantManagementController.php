<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RestaurantManagementController extends Controller
{
    /**
     * Display restaurants for management
     */
    public function index(Request $request)
    {
        $query = Restaurant::with(['tenant', 'locationAdmin', 'user', 'documents']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%'.$request->city.'%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('restaurant_name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%');
            });
        }

        $restaurants = $query->paginate(15);

        // Get statistics for the dashboard
        $stats = [
            'total' => Restaurant::count(),
            'pending' => Restaurant::where('status', 'pending')->count(),
            'approved' => Restaurant::where('status', 'approved')->count(),
            'suspended' => Restaurant::where('status', 'suspended')->count(),
            'rejected' => Restaurant::where('status', 'rejected')->count(),
        ];

        return view('restaurant_admin.management.index', compact('restaurants', 'stats'));
    }

    /**
     * Show restaurant details
     */
    public function show($id)
    {
        $restaurant = Restaurant::with([
            'tenant',
            'locationAdmin',
            'user',
            'documents.reviewer',
            'workingHours',
            'cityRelation',
            'stateRelation',
        ])->findOrFail($id);

        // Get document verification status
        $requiredDocuments = [
            'food_safety_certificate',
            'business_license',
            'pan_card',
            'gst_certificate',
            'owner_id_proof',
        ];

        $documentStatus = [];
        foreach ($requiredDocuments as $docType) {
            $doc = $restaurant->documents->where('document_type', $docType)->first();
            $documentStatus[$docType] = $doc ? $doc->status : 'missing';
        }

        return view('restaurant_admin.management.show', compact('restaurant', 'documentStatus'));
    }

    /**
     * Show edit restaurant form
     */
    public function edit($id)
    {
        $restaurant = Restaurant::with('tenant')->findOrFail($id);

        // Load tenants for super admin
        $tenants = collect();
        if (auth()->user()->role === 'super_admin') {
            $tenants = Tenant::where('status', 'approved')->get(['id', 'tenant_name', 'email']);
        }

        $locationAdmins = User::whereIn('role', ['tenant_admin', 'location_admin'])
            ->where('status', 'active')
            ->get();

        return view('restaurant_admin.edit', compact('restaurant', 'tenants', 'locationAdmins'));
    }

    /**
     * Update restaurant
     */
    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        $validated = $request->validate([
            'restaurant_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cuisine_type' => 'nullable|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:restaurants,email,'.$restaurant->id,
            'website_url' => 'nullable|url',
            'delivery_radius_km' => 'required|integer|min:1|max:50',
            'minimum_order_amount' => 'required|numeric|min:0',
            'base_delivery_fee' => 'required|numeric|min:0',
            'estimated_delivery_time' => 'required|integer|min:10|max:120',
            'tax_percentage' => 'required|numeric|between:0,50',
            'is_open' => 'boolean',
            'accepts_orders' => 'boolean',
            'is_featured' => 'boolean',
            'special_instructions' => 'nullable|string',
        ]);

        $restaurant->update($validated);

        return redirect()->route('restaurant-admin.management.show', $restaurant->id)
            ->with('success', 'Restaurant updated successfully!');
    }

    /**
     * Update restaurant status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,approved,suspended,rejected',
                'rejection_reason' => 'required_if:status,rejected|nullable|string|max:1000',
            ]);

            $restaurant = Restaurant::findOrFail($id);

            return DB::transaction(function () use ($restaurant, $validated, $request) {
                // Update restaurant status
                $restaurant->update([
                    'status' => $validated['status'],
                    'approved_at' => $validated['status'] === 'approved' ? now() : null,
                    'approved_by' => $validated['status'] === 'approved' ? Auth::id() : null,
                ]);

                // If approved, create users
                if ($validated['status'] === 'approved') {
                    $this->createUsersOnApproval($restaurant);
                }

                // Handle rejection reason
                if ($validated['status'] === 'rejected' && $request->filled('rejection_reason')) {
                    $existingInstructions = $restaurant->special_instructions;
                    $rejectionNote = 'Rejection Reason: '.$validated['rejection_reason'];

                    $restaurant->update([
                        'special_instructions' => $existingInstructions ?
                            $existingInstructions."\n\n".$rejectionNote :
                            $rejectionNote,
                    ]);
                }

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Restaurant status updated successfully!',
                        'status' => $validated['status'],
                    ]);
                }

                return redirect()->back()->with('success', 'Restaurant status updated successfully!');
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: '.implode(', ', $e->validator->errors()->all()),
                ], 422);
            }

            return redirect()->back()->withErrors($e->validator->errors());
        } catch (\Exception $e) {
            \Log::error('Restaurant status update failed: '.$e->getMessage(), [
                'restaurant_id' => $id,
                'status' => $request->get('status'),
                'user_id' => Auth::id(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update status: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update status: '.$e->getMessage());
        }
    }

    /**
     * Create users when restaurant is approved
     */
    private function createUsersOnApproval($restaurant)
    {
        // Split contact person name into first and last name
        $contactPersonName = $restaurant->contact_person_name ?? $restaurant->restaurant_name.' Admin';
        $nameParts = explode(' ', trim($contactPersonName));
        $firstName = $nameParts[0];
        $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : 'Admin';

        // Check if location_admin user already exists with this email
        $existingLocationAdmin = User::where('email', $restaurant->email)->first();

        if ($existingLocationAdmin) {
            \Log::warning('Location admin email already exists during approval', [
                'email' => $restaurant->email,
                'restaurant_id' => $restaurant->id,
                'existing_user_id' => $existingLocationAdmin->id,
            ]);

            // Update existing user to be location_admin for this restaurant
            $existingLocationAdmin->update([
                'role' => 'location_admin',
                'tenant_id' => $restaurant->tenant_id,
                'restaurant_id' => $restaurant->id,
            ]);

            $locationAdmin = $existingLocationAdmin;
        } else {
            // Create new location_admin user
            $locationAdmin = User::create([
                'tenant_id' => $restaurant->tenant_id,
                'restaurant_id' => $restaurant->id,
                'email' => $restaurant->email,
                'phone' => $restaurant->phone,
                'password' => Hash::make('password123'),
                'first_name' => $firstName,    // First part of contact person name
                'last_name' => $lastName,      // Rest of contact person name
                'role' => 'location_admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        }

        // Update restaurant with location_admin_id
        $restaurant->update(['location_admin_id' => $locationAdmin->id]);

        \Log::info('Location admin created/assigned', [
            'restaurant_id' => $restaurant->id,
            'location_admin_id' => $locationAdmin->id,
        ]);

        // Check if this is a new independent restaurant (tenant has only 1 restaurant)
        $tenantRestaurantCount = Restaurant::where('tenant_id', $restaurant->tenant_id)->count();

        if ($tenantRestaurantCount === 1) {
            // This is the first restaurant for this tenant (New Independent Restaurant)

            // Check if tenant_admin already exists for this tenant
            $tenantAdminExists = User::where('tenant_id', $restaurant->tenant_id)
                ->where('role', 'tenant_admin')
                ->exists();

            if (! $tenantAdminExists) {
                // Create tenant_admin user using tenant details
                $tenant = Tenant::find($restaurant->tenant_id);

                // Check if email already exists
                $existingTenantUser = User::where('email', $tenant->email)->first();

                if ($existingTenantUser) {
                    \Log::warning('Tenant admin email already exists during approval', [
                        'email' => $tenant->email,
                        'restaurant_id' => $restaurant->id,
                        'existing_user_id' => $existingTenantUser->id,
                    ]);

                    // Use existing user as tenant admin if they don't have tenant_admin role
                    if ($existingTenantUser->role !== 'tenant_admin') {
                        $existingTenantUser->update([
                            'role' => 'tenant_admin',
                            'tenant_id' => $restaurant->tenant_id,
                        ]);
                    }
                } else {
                    $tenantAdmin = User::create([
                        'tenant_id' => $restaurant->tenant_id,
                        'restaurant_id' => null, // tenant_admin is not tied to specific restaurant
                        'email' => $tenant->email,
                        'phone' => $tenant->phone,
                        'password' => Hash::make('password123'),
                        'first_name' => $tenant->contact_person,
                        'last_name' => 'Owner',
                        'role' => 'tenant_admin',
                        'status' => 'active',
                        'email_verified_at' => now(),
                    ]);

                    \Log::info('New independent restaurant - both users created', [
                        'restaurant_id' => $restaurant->id,
                        'location_admin_id' => $locationAdmin->id,
                        'tenant_admin_id' => $tenantAdmin->id,
                    ]);
                }
            } else {
                \Log::info('New independent restaurant - tenant admin already exists', [
                    'restaurant_id' => $restaurant->id,
                    'tenant_id' => $restaurant->tenant_id,
                ]);
            }
        } else {
            // This is "Add to Existing Franchise" - only location_admin created
            \Log::info('Existing franchise restaurant - only location admin created', [
                'restaurant_id' => $restaurant->id,
                'location_admin_id' => $locationAdmin->id,
                'tenant_restaurant_count' => $tenantRestaurantCount,
            ]);
        }
    }

    /**
     * Toggle restaurant status (open/closed)
     */
    public function toggle(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        $field = $request->input('field', 'is_open');
        $allowedFields = ['is_open', 'accepts_orders', 'is_featured'];

        if (! in_array($field, $allowedFields)) {
            return response()->json(['success' => false, 'message' => 'Invalid field']);
        }

        $restaurant->update([
            $field => ! $restaurant->$field,
        ]);

        $message = match ($field) {
            'is_open' => $restaurant->is_open ? 'Restaurant opened' : 'Restaurant closed',
            'accepts_orders' => $restaurant->accepts_orders ? 'Now accepting orders' : 'Stopped accepting orders',
            'is_featured' => $restaurant->is_featured ? 'Restaurant featured' : 'Restaurant unfeatured',
        };

        return response()->json([
            'success' => true,
            'message' => $message,
            'new_value' => $restaurant->$field,
        ]);
    }

    /**
     * Bulk update restaurant status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'restaurant_ids' => 'required|array',
            'restaurant_ids.*' => 'exists:restaurants,id',
            'status' => 'required|in:pending,approved,suspended,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
        ]);

        $updated = 0;

        DB::transaction(function () use ($validated, &$updated) {
            foreach ($validated['restaurant_ids'] as $restaurantId) {
                $restaurant = Restaurant::find($restaurantId);
                if ($restaurant) {
                    $restaurant->update([
                        'status' => $validated['status'],
                        'approved_at' => $validated['status'] === 'approved' ? now() : null,
                        'approved_by' => $validated['status'] === 'approved' ? Auth::id() : null,
                    ]);

                    if ($validated['status'] === 'rejected' && isset($validated['rejection_reason'])) {
                        $restaurant->update([
                            'special_instructions' => 'Rejection Reason: '.$validated['rejection_reason'],
                        ]);
                    }

                    $updated++;
                }
            }
        });

        return redirect()->back()->with('success', "Successfully updated {$updated} restaurants.");
    }

    /**
     * Delete restaurant
     */
    public function destroy(Request $request, $id)
    {
        try {
            $restaurant = Restaurant::findOrFail($id);

            // Check if restaurant has orders
            if ($restaurant->total_orders > 0) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete restaurant with existing orders. Consider suspending instead.',
                    ], 400);
                }

                return redirect()->back()->with('error', 'Cannot delete restaurant with existing orders. Consider suspending instead.');
            }

            // Soft delete the restaurant
            $restaurant->delete();

            // Update tenant restaurant count
            if ($restaurant->tenant_id) {
                Tenant::where('id', $restaurant->tenant_id)->decrement('total_restaurants');
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Restaurant deleted successfully!',
                ]);
            }

            return redirect()->route('restaurant-admin.management.index')
                ->with('success', 'Restaurant deleted successfully!');

        } catch (\Exception $e) {
            \Log::error('Restaurant deletion failed: '.$e->getMessage(), [
                'restaurant_id' => $id,
                'user_id' => Auth::id(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete restaurant: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete restaurant: '.$e->getMessage());
        }
    }

    /**
     * Get restaurant data for AJAX requests
     */
    public function getData(Request $request)
    {
        $restaurants = Restaurant::with(['tenant', 'locationAdmin'])
            ->select(['id', 'restaurant_name', 'status', 'city', 'phone', 'email', 'created_at'])
            ->get()
            ->map(function ($restaurant) {
                return [
                    'id' => $restaurant->id,
                    'name' => $restaurant->restaurant_name,
                    'status' => $restaurant->status,
                    'city' => $restaurant->city,
                    'phone' => $restaurant->phone,
                    'email' => $restaurant->email,
                    'created_at' => $restaurant->created_at->format('M d, Y'),
                    'status_badge' => $this->getStatusBadge($restaurant->status),
                ];
            });

        return response()->json($restaurants);
    }

    /**
     * Get status badge HTML
     */
    private function getStatusBadge($status)
    {
        $badges = [
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'approved' => '<span class="badge badge-success">Approved</span>',
            'suspended' => '<span class="badge badge-danger">Suspended</span>',
            'rejected' => '<span class="badge badge-secondary">Rejected</span>',
        ];

        return $badges[$status] ?? '<span class="badge badge-light">Unknown</span>';
    }
}
