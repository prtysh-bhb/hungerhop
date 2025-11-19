<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Restaurant;
use App\Models\State;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Log;

class RestaurantAdminController extends Controller
{
    /**
     * Display the restaurant admin dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Get statistics
        $stats = [
            'total_restaurants' => Restaurant::count(),
            'pending_approvals' => Restaurant::where('status', 'pending')->count(),
            'approved_restaurants' => Restaurant::where('status', 'approved')->count(),
            'rejected_restaurants' => Restaurant::where('status', 'rejected')->count(),
        ];

        // Get recent restaurants
        $recent_restaurants = Restaurant::with(['tenant', 'locationAdmin', 'cityRelation', 'stateRelation'])
            ->latest()
            ->take(5)
            ->get();

        return view('restaurant_admin.index', compact('stats', 'recent_restaurants'));
    }

    /**
     * Show the restaurant registration form
     */
    public function showRegistrationForm()
    {
        $user = auth()->user();

        // Get all states, or filter by country if needed
        $states = State::orderBy('name', 'asc')->get(['id', 'name']);

        // If no states found with country_id filter, get all states
        if ($states->isEmpty()) {
            $states = State::orderBy('name', 'asc')->get(['id', 'name']);
        }

        // Existing dropdown should list tenant_name
        $tenants = collect();
        if ($user->role === 'super_admin') {
            $tenants = Tenant::where('status', Tenant::STATUS_APPROVED)
                ->orderBy('tenant_name')
                ->get(['id', 'tenant_name']);
        }

        $locationAdmins = User::where('role', 'location_admin')
            ->get(['id', 'first_name', 'last_name', 'email']);

        return view('restaurant_admin.registration.create', compact('states', 'tenants', 'locationAdmins'));
    }

    /**
     * Store a new restaurant registration
     */
    public function storeRegistration(Request $request)
    {
        // Define all validation rules upfront with comprehensive validation

        $rules = [
            // Basic Information
            'restaurant_name' => 'required|string|min:3|max:50',
            'contact_person_name' => 'required|string|min:3|max:50',
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^[1-9][0-9]{9,14}$/', // Must start with 1-9, total 10-15 digits
            ],
            'email' => 'required|email|min:7|max:100',

            // Address Information
            'address' => 'required|string|min:10|max:500',
            'state_id' => 'required|integer|exists:states,id',
            'city_id' => 'required|integer|exists:cities,id',
            'postal_code' => [
                'required',
                'string',
                'min:4',
                'max:10',
                'regex:/^[0-9A-Za-z\s\-]+$/',
            ],
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',

            // Business Configuration
            'delivery_radius_km' => 'required|numeric|min:1|max:50',
            'minimum_order_amount' => 'required|numeric|min:0|max:10000',
            'base_delivery_fee' => 'required|numeric|min:0|max:1000',
            'estimated_delivery_time' => 'required|integer|min:10|max:120',
            'tax_percentage' => 'required|numeric|min:0|max:50',
            'restaurant_commission_percentage' => 'required|numeric|min:0|max:100',

            // Optional fields
            'cuisine_type' => 'nullable|string|max:100',
            'website_url' => 'nullable|url|max:255',
            'is_open' => 'sometimes|boolean',
            'tenant_id' => 'sometimes|nullable|exists:tenants,id',
            'image_url' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',

            // Business hours validation rules
            'business_hours' => 'required|array',
            'business_hours.*.day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'business_hours.*.is_open' => 'nullable|boolean',
            'business_hours.*.opening_time' => 'nullable|date_format:H:i',
            'business_hours.*.closing_time' => 'nullable|date_format:H:i|after:business_hours.*.opening_time',
        ];

        // Custom validation messages
        $messages = [
            // Restaurant name
            'restaurant_name.required' => 'Restaurant name is required.',
            'restaurant_name.min' => 'Restaurant name must be at least 3 characters.',
            'restaurant_name.max' => 'Restaurant name cannot exceed 50 characters.',

            // Contact person
            'contact_person_name.required' => 'Contact person name is required.',
            'contact_person_name.min' => 'Contact person name must be at least 3 characters.',
            'contact_person_name.max' => 'Contact person name cannot exceed 50 characters.',

            // Phone
            'phone.required' => 'Phone number is required.',
            'phone.min' => 'Phone number must be at least 10 digits.',
            'phone.max' => 'Phone number cannot exceed 15 digits.',
            'phone.regex' => 'Phone number must be valid (10-15 digits, cannot start with 0).',

            // Email
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.min' => 'Email must be at least 7 characters.',
            'email.max' => 'Email cannot exceed 100 characters.',

            // Address
            'address.required' => 'Address is required.',
            'address.min' => 'Address must be at least 10 characters.',
            'address.max' => 'Address cannot exceed 500 characters.',

            // State and City
            'state_id.required' => 'Please select a state.',
            'state_id.exists' => 'Selected state is invalid.',
            'city_id.required' => 'Please select a city.',
            'city_id.exists' => 'Selected city is invalid.',

            // Postal code
            'postal_code.required' => 'Postal code is required.',
            'postal_code.min' => 'Postal code must be at least 4 characters.',
            'postal_code.max' => 'Postal code cannot exceed 10 characters.',
            'postal_code.regex' => 'Postal code can only contain letters, numbers, spaces, and hyphens.',

            // Latitude and Longitude
            'latitude.required' => 'Latitude is required.',
            'latitude.numeric' => 'Latitude must be a number.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.required' => 'Longitude is required.',
            'longitude.numeric' => 'Longitude must be a number.',
            'longitude.between' => 'Longitude must be between -180 and 180.',

            // Business Configuration
            'delivery_radius_km.required' => 'Delivery radius is required.',
            'delivery_radius_km.min' => 'Delivery radius must be at least 1 km.',
            'delivery_radius_km.max' => 'Delivery radius cannot exceed 50 km.',

            'minimum_order_amount.required' => 'Minimum order amount is required.',
            'minimum_order_amount.min' => 'Minimum order amount cannot be negative.',
            'minimum_order_amount.max' => 'Minimum order amount seems too high (max 10,000).',

            'base_delivery_fee.required' => 'Base delivery fee is required.',
            'base_delivery_fee.min' => 'Delivery fee cannot be negative.',
            'base_delivery_fee.max' => 'Delivery fee seems too high (max 1,000).',

            'estimated_delivery_time.required' => 'Estimated delivery time is required.',
            'estimated_delivery_time.min' => 'Delivery time must be at least 10 minutes.',
            'estimated_delivery_time.max' => 'Delivery time cannot exceed 120 minutes.',

            'tax_percentage.required' => 'Tax percentage is required.',
            'tax_percentage.min' => 'Tax percentage cannot be negative.',
            'tax_percentage.max' => 'Tax percentage cannot exceed 50%.',

            'restaurant_commission_percentage.required' => 'Commission percentage is required.',
            'restaurant_commission_percentage.min' => 'Commission percentage cannot be negative.',
            'restaurant_commission_percentage.max' => 'Commission percentage cannot exceed 100%.',

            // Business hours
            'business_hours.required' => 'Business hours are required.',
            'business_hours.*.closing_time.after' => 'Closing time must be after opening time.',

        ];

        // Add conditional validation for super_admin
        if (auth()->user()->role === 'super_admin' && $request->has('tenant_selection')) {
            if ($request->tenant_selection === 'new') {
                $rules['contact_person'] = 'required|string|min:3|max:255';
                $rules['tenant_email'] = 'required|email|min:7|max:100|unique:users,email';
                $rules['tenant_phone'] = [
                    'required',
                    'string',
                    'min:10',
                    'max:15',
                    'regex:/^[1-9][0-9]{9,14}$/',
                    'unique:users,phone', // Add unique validation for phone
                ];
                // Require location admin fields for new franchise
                $rules['location_admin_name'] = 'required|string|min:3|max:255';
                $rules['location_admin_email'] = 'required|email|min:7|max:100|unique:users,email';
                $rules['location_admin_phone'] = [
                    'required',
                    'string',
                    'min:10',
                    'max:15',
                    'regex:/^[1-9][0-9]{9,14}$/',
                    'unique:users,phone', // Add unique validation for phone
                ];

                // Add custom messages for franchise fields
                $messages['contact_person.required'] = 'Franchise owner name is required.';
                $messages['contact_person.min'] = 'Franchise owner name must be at least 3 characters.';
                $messages['tenant_email.required'] = 'Franchise email is required.';
                $messages['tenant_email.unique'] = 'This email is already registered.';
                $messages['tenant_phone.required'] = 'Franchise phone is required.';
                $messages['tenant_phone.unique'] = 'This phone number is already registered.';
                $messages['tenant_phone.regex'] = 'Franchise phone must be valid (10-15 digits, cannot start with 0).';
                $messages['location_admin_name.required'] = 'Location admin name is required.';
                $messages['location_admin_email.required'] = 'Location admin email is required.';
                $messages['location_admin_email.unique'] = 'This location admin email is already registered.';
                $messages['location_admin_phone.required'] = 'Location admin phone is required.';
                $messages['location_admin_phone.unique'] = 'This location admin phone number is already registered.';
                $messages['location_admin_phone.regex'] = 'Location admin phone must be valid (10-15 digits, cannot start with 0).';
            } elseif ($request->tenant_selection === 'existing') {
                $rules['tenant_id'] = 'required|integer|exists:tenants,id';
                $rules['location_admin_id'] = 'required|integer|exists:users,id';

                $messages['tenant_id.required'] = 'Please select a franchise.';
                $messages['tenant_id.exists'] = 'Selected franchise is invalid.';
                $messages['location_admin_id.required'] = 'Please select a location admin.';
                $messages['location_admin_id.exists'] = 'Selected location admin is invalid.';
            }
        } else {
            // For tenant_admin, require location_admin_id
            $rules['location_admin_id'] = 'required|integer|exists:users,id';
            $messages['location_admin_id.required'] = 'Please select a location admin.';
            $messages['location_admin_id.exists'] = 'Selected location admin is invalid.';
        }

        if ('location_admin_phone' == 'tenant_phone') {
            $messages['location_admin_phone'] = 'Location admin phone cannot be the same as franchise phone.';
        }

        // Validate all data at once with custom messages
        $data = $request->validate($rules, $messages);

        // Ensure business_hours exists in data
        if (! isset($data['business_hours']) || ! is_array($data['business_hours'])) {
            return back()->withInput()->withErrors(['business_hours' => 'Business hours are required.']);
        }

        // Additional business hours validation
        $hasOpenDay = false;
        $businessHoursErrors = [];

        foreach ($data['business_hours'] as $day => $hours) {
            if (isset($hours['is_open']) && $hours['is_open'] == '1') {
                $hasOpenDay = true;

                // Validate that open days have opening and closing times
                if (empty($hours['opening_time'])) {
                    $businessHoursErrors[] = 'Opening time is required for '.ucfirst($day);
                }
                if (empty($hours['closing_time'])) {
                    $businessHoursErrors[] = 'Closing time is required for '.ucfirst($day);
                }

                // Validate closing time is after opening time
                if (! empty($hours['opening_time']) && ! empty($hours['closing_time'])) {
                    if ($hours['opening_time'] >= $hours['closing_time']) {
                        $businessHoursErrors[] = 'Closing time must be after opening time for '.ucfirst($day);
                    }
                }
            }
        }

        // Check if at least one day is open
        if (! $hasOpenDay) {
            $businessHoursErrors[] = 'Restaurant must be open at least one day of the week.';
        }

        // Return with errors if any business hours validation failed
        if (! empty($businessHoursErrors)) {
            return back()->withInput()->withErrors(['business_hours' => implode(' ', $businessHoursErrors)]);
        }

        // Process business hours
        $businessHours = $this->processBusinessHours($data['business_hours']);

        try {
            return DB::transaction(function () use ($request, $data, $businessHours) {
                $tenantId = null;
                $locationAdminId = null;

                if (auth()->user()->role === 'super_admin') {
                    if ($request->filled('tenant_id') && $request->tenant_selection === 'existing') {
                        $tenantId = (int) $request->tenant_id;
                        $locationAdminId = $data['location_admin_id'];
                    } else {
                        // Create new tenant logic...
                        $tenantInput = $request->validate([
                            'contact_person' => 'required|string|max:255',
                            'tenant_email' => 'required|email|max:255|unique:tenants,email',
                            'tenant_phone' => 'required|string|max:20',
                        ]);

                        $tenant = Tenant::create([
                            'tenant_name' => $data['restaurant_name'],
                            'contact_person' => $tenantInput['contact_person'],
                            'email' => $tenantInput['tenant_email'],
                            'phone' => $tenantInput['tenant_phone'],
                            'subscription_plan' => Tenant::PLAN_LITE,
                            'total_restaurants' => 0,
                            'monthly_base_fee' => 0,
                            'per_restaurant_fee' => 0,
                            'banner_limit' => 5,
                            'status' => Tenant::STATUS_APPROVED,
                            'subscription_start_date' => now(),
                            'next_billing_date' => now()->addMonth(),
                        ]);

                        $tenantId = $tenant->id;

                        // Create new location admin user (check if email or phone exists first)
                        $existingLocationAdminEmail = User::where('email', $data['location_admin_email'])->first();
                        $existingLocationAdminPhone = User::where('phone', $data['location_admin_phone'])->first();

                        if ($existingLocationAdminEmail) {
                            throw new \Exception('Location admin email already exists: '.$data['location_admin_email']);
                        }

                        if ($existingLocationAdminPhone) {
                            throw new \Exception('Location admin phone number already exists: '.$data['location_admin_phone']);
                        }

                        $locationAdmin = User::create([
                            'tenant_id' => $tenantId,
                            'restaurant_id' => null, // will be set after restaurant creation if needed
                            'first_name' => $data['location_admin_name'],
                            'last_name' => '',
                            'email' => $data['location_admin_email'],
                            'phone' => $data['location_admin_phone'],
                            'role' => 'location_admin',
                            'status' => 'active',
                            'password' => $data['location_admin_phone'],
                            'email_verified_at' => now(),
                        ]);
                        $locationAdminId = $locationAdmin->id;

                        // Create tenant admin user using franchise details (check if email or phone exists first)
                        $existingTenantAdminEmail = User::where('email', $tenantInput['tenant_email'])->first();
                        $existingTenantAdminPhone = User::where('phone', $tenantInput['tenant_phone'])->first();

                        if ($existingTenantAdminEmail) {
                            throw new \Exception('Franchise owner email already exists: '.$tenantInput['tenant_email']);
                        }

                        if ($existingTenantAdminPhone) {
                            throw new \Exception('Franchise owner phone number already exists: '.$tenantInput['tenant_phone']);
                        }

                        $tenantAdmin = User::create([
                            'tenant_id' => $tenantId,
                            'restaurant_id' => null, // tenant_admin is not tied to specific restaurant
                            'email' => $tenantInput['tenant_email'],
                            'phone' => $tenantInput['tenant_phone'],
                            'password' => $tenantInput['tenant_phone'], // password is mobile number
                            'first_name' => $tenantInput['contact_person'],
                            'last_name' => 'Owner',
                            'role' => 'tenant_admin',
                            'status' => 'active',
                            'email_verified_at' => now(),
                        ]);

                        Log::info('New franchise created with both admins:', [
                            'tenant_id' => $tenantId,
                            'location_admin_id' => $locationAdminId,
                            'tenant_admin_id' => $tenantAdmin->id,
                            'tenant_admin_email' => $tenantInput['tenant_email'],
                            'tenant_admin_phone' => $tenantInput['tenant_phone'],
                        ]);
                    }
                } else {
                    $tenantId = auth()->user()->tenant_id;
                    $locationAdminId = $data['location_admin_id'];
                }

                // Create restaurant with business hours
                $restaurant = Restaurant::create([
                    'tenant_id' => $tenantId,
                    'location_admin_id' => $locationAdminId,
                    'user_id' => auth()->id(),
                    'restaurant_name' => $data['restaurant_name'],
                    'contact_person_name' => $data['contact_person_name'],
                    'slug' => Str::slug($data['restaurant_name']),
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'postal_code' => $data['postal_code'],
                    'state' => $data['state_id'],
                    'city' => $data['city_id'],
                    'address' => $data['address'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'website_url' => $data['website_url'] ?? null,
                    'cuisine_type' => $data['cuisine_type'] ?? null,
                    'minimum_order_amount' => $data['minimum_order_amount'],
                    'base_delivery_fee' => $data['base_delivery_fee'],
                    'delivery_radius_km' => $data['delivery_radius_km'],
                    'estimated_delivery_time' => $data['estimated_delivery_time'],
                    'tax_percentage' => $data['tax_percentage'],
                    'restaurant_commission_percentage' => $data['restaurant_commission_percentage'],
                    'is_open' => (bool) ($data['is_open'] ?? false),
                    'business_hours' => $businessHours,
                    'status' => 'pending',
                ]);

                // If we just created a location admin, update their restaurant_id
                if (isset($locationAdmin) && $restaurant) {
                    $locationAdmin->restaurant_id = $restaurant->id;
                    $locationAdmin->save();
                }

                // Keep tenant count in sync
                Tenant::where('id', $tenantId)->increment('total_restaurants');

                return redirect()
                    ->route('restaurant-admin.list')
                    ->with('success', 'Restaurant created successfully with business hours.');
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors())->with('error', 'Please fix the highlighted fields.');
        } catch (\Throwable $e) {
            Log::error('Restaurant create failed', ['msg' => $e->getMessage(), 'data' => $data]);

            return back()->withInput()->with('error', 'Failed to create restaurant: '.$e->getMessage());
        }
    }

    /**
     * Process business hours input - Updated method
     */
    private function processBusinessHours(array $businessHoursData): string
    {
        $processedHours = [];

        foreach ($businessHoursData as $day => $hours) {
            // Handle both array and direct input formats
            if (is_array($hours)) {
                $isOpen = isset($hours['is_open']) && $hours['is_open'] == '1';
                $dayName = $hours['day'] ?? $day;
                $openingTime = $hours['opening_time'] ?? null;
                $closingTime = $hours['closing_time'] ?? null;
            } else {
                // If hours is not an array, skip it
                continue;
            }

            $processedHours[$dayName] = [
                'day' => $dayName,
                'is_open' => $isOpen,
                'opening_time' => $isOpen ? $openingTime : null,
                'closing_time' => $isOpen ? $closingTime : null,
            ];
        }

        return json_encode($processedHours);
    }

    /**
     * Display all restaurants with filtering and search
     */
    public function list(Request $request)
    {
        $user = auth()->user();
        $query = Restaurant::with(['tenant', 'locationAdmin', 'user']);

        // Apply role-based filtering
        switch ($user->role) {
            case 'location_admin':
                // Location admin can only see restaurants where they are assigned
                $query->where('location_admin_id', $user->id);
                break;

            case 'tenant_admin':
                // Tenant admin can see all restaurants under their tenant
                $query->where('tenant_id', $user->tenant_id);
                break;

            case 'super_admin':
                // Super admin can see all restaurants
                break;

            default:
                // Other roles should not access this page
                abort(403, 'Unauthorized access');
        }

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

        // Get statistics based on user role
        $statsQuery = Restaurant::query();
        switch ($user->role) {
            case 'location_admin':
                $statsQuery->where('location_admin_id', $user->id);
                break;
            case 'tenant_admin':
                $statsQuery->where('tenant_id', $user->tenant_id);
                break;
        }

        $stats = [
            'total' => $statsQuery->count(),
            'pending' => $statsQuery->where('status', 'pending')->count(),
            'approved' => $statsQuery->where('status', 'approved')->count(),
            'rejected' => $statsQuery->where('status', 'rejected')->count(),
            'suspended' => $statsQuery->where('status', 'suspended')->count(),
        ];

        return view('restaurant_admin.list', compact('restaurants', 'stats'));
    }

    /**
     * Show restaurant details
     */
    public function show($id)
    {
        $user = auth()->user();
        $query = Restaurant::with(['tenant', 'locationAdmin', 'user', 'documents', 'cityRelation', 'stateRelation']);

        // Apply role-based filtering
        switch ($user->role) {
            case 'location_admin':
                // Location admin can only see restaurants where they are assigned
                $query->where('location_admin_id', $user->id);
                break;

            case 'tenant_admin':
                // Tenant admin can see all restaurants under their tenant
                $query->where('tenant_id', $user->tenant_id);
                break;

            case 'super_admin':
                // Super admin can see all restaurants
                break;

            default:
                // Other roles should not access this page
                abort(403, 'Unauthorized access');
        }

        $restaurant = $query->findOrFail($id);

        // Debug: Check if contact_person_name exists
        Log::info('Restaurant data:', [
            'id' => $restaurant->id,
            'contact_person_name' => $restaurant->contact_person_name,
            'all_attributes' => $restaurant->getAttributes(),
        ]);

        return view('restaurant_admin.show', compact('restaurant'));
    }

    /**
     * Approve restaurant - only update status
     */
    public function approve($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $restaurant = Restaurant::findOrFail($id);

                if ($restaurant->status === 'approved') {
                    return redirect()->back()->with('info', 'Restaurant is already approved.');
                }

                // Update restaurant status only
                $restaurant->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                ]);

                return redirect()->back()->with('success', 'Restaurant approved successfully!');
            });

        } catch (\Exception $e) {
            Log::error('Restaurant approval failed: '.$e->getMessage());

            return redirect()->back()->with('error', 'Failed to approve restaurant: '.$e->getMessage());
        }
    }

    /**
     * Show edit restaurant form
     */
    public function edit($id)
    {
        $user = auth()->user();
        $query = Restaurant::with('tenant');

        // Apply role-based filtering
        switch ($user->role) {
            case 'location_admin':
                // Location admin can only edit restaurants where they are assigned
                $query->where('location_admin_id', $user->id);
                break;

            case 'tenant_admin':
                // Tenant admin can edit all restaurants under their tenant
                $query->where('tenant_id', $user->tenant_id);
                break;

            case 'super_admin':
                // Super admin can edit all restaurants
                break;

            default:
                // Other roles should not access this page
                abort(403, 'Unauthorized access');
        }

        $restaurant = $query->findOrFail($id);

        // Get all approved tenants for franchise selection
        $tenants = Tenant::where('status', Tenant::STATUS_APPROVED)
            ->orderBy('tenant_name')
            ->get(['id', 'tenant_name', 'email', 'contact_person']);

        $locationAdmins = User::whereIn('role', ['tenant_admin', 'location_admin'])
            ->where('status', 'active')
            ->get();

        // Get all states for dropdown
        $states = State::orderBy('name', 'asc')->get(['id', 'name']);

        return view('restaurant_admin.edit', compact('restaurant', 'tenants', 'locationAdmins', 'states'));
    }

    /**
     * Update restaurant
     */
    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        $validated = $request->validate([
            'tenant_id' => 'nullable|exists:tenants,id',
            'location_admin_id' => 'nullable|exists:users,id',
            'restaurant_name' => 'required|string|min:3|max:255',
            'contact_person_name' => 'nullable|string|min:3|max:255',
            'description' => 'nullable|string',
            'cuisine_type' => 'nullable|string|max:100',
            'address' => 'required|string|min:10|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'city_id' => 'required|integer|exists:cities,id',
            'state_id' => 'required|integer|exists:states,id',
            'postal_code' => [
                'required',
                'string',
                'min:4',
                'max:10',
                'regex:/^[0-9A-Za-z\s\-]+$/',
            ],
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^[1-9][0-9]{9,14}$/',
            ],
            'email' => ['required', 'email', 'min:7', 'max:100', Rule::unique('restaurants')->ignore($restaurant->id)],
            'website_url' => 'nullable|url|max:255',
            'delivery_radius_km' => 'required|numeric|min:1|max:50',
            'minimum_order_amount' => 'required|numeric|min:0|max:10000',
            'base_delivery_fee' => 'required|numeric|min:0|max:1000',
            'restaurant_commission_percentage' => 'required|numeric|between:0,100',
            'estimated_delivery_time' => 'required|integer|min:10|max:120',
            'tax_percentage' => 'required|numeric|between:0,50',
            'special_instructions' => 'nullable|string|max:1000',
            'business_hours' => 'nullable|array',
            'business_hours_json' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'tenant_selection' => 'nullable|string|in:no_change,new,existing',
            // Franchise owner details for 'new' type
            'contact_person' => 'nullable|string|max:255',
            'tenant_email' => 'nullable|email|max:255',
            'tenant_phone' => 'nullable|string|max:20',
        ], [
            // Custom error messages
            'restaurant_name.required' => 'Restaurant name is required.',
            'restaurant_name.min' => 'Restaurant name must be at least 3 characters.',
            'address.required' => 'Address is required.',
            'address.min' => 'Address must be at least 10 characters.',
            'address.max' => 'Address cannot exceed 500 characters.',
            'state_id.required' => 'Please select a state.',
            'state_id.exists' => 'Selected state is invalid.',
            'city_id.required' => 'Please select a city.',
            'city_id.exists' => 'Selected city is invalid.',
            'postal_code.required' => 'Postal code is required.',
            'postal_code.min' => 'Postal code must be at least 4 characters.',
            'postal_code.regex' => 'Postal code can only contain letters, numbers, spaces, and hyphens.',
            'phone.required' => 'Phone number is required.',
            'phone.min' => 'Phone number must be at least 10 digits.',
            'phone.regex' => 'Phone number must be valid (10-15 digits, cannot start with 0).',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
            'delivery_radius_km.min' => 'Delivery radius must be at least 1 km.',
            'delivery_radius_km.max' => 'Delivery radius cannot exceed 50 km.',
            'minimum_order_amount.max' => 'Minimum order amount seems too high (max 10,000).',
            'base_delivery_fee.max' => 'Delivery fee seems too high (max 1,000).',
            'estimated_delivery_time.min' => 'Delivery time must be at least 10 minutes.',
            'estimated_delivery_time.max' => 'Delivery time cannot exceed 120 minutes.',
            'tax_percentage.between' => 'Tax percentage must be between 0 and 50%.',
            'restaurant_commission_percentage.between' => 'Commission percentage must be between 0 and 100%.',
        ]);

        // Handle tenant changes only for super admin
        if (auth()->user()->role === 'super_admin') {
            if ($request->has('tenant_selection')) {
                if ($request->tenant_selection === 'existing' && $request->filled('tenant_id')) {
                    // User selected an existing franchise
                    $validated['tenant_id'] = $request->tenant_id;
                } elseif ($request->tenant_selection === 'new') {
                    // User wants to update/create franchise details
                    if ($request->filled(['contact_person', 'tenant_email', 'tenant_phone'])) {
                        // Update existing tenant details or create new one
                        $tenant = $restaurant->tenant;
                        if ($tenant) {
                            $tenant->update([
                                'contact_person' => $request->contact_person,
                                'email' => $request->tenant_email,
                                'phone' => $request->tenant_phone,
                                'tenant_name' => $validated['restaurant_name'], // Update tenant name to match restaurant
                            ]);
                        }
                        // Keep the current tenant_id
                        $validated['tenant_id'] = $restaurant->tenant_id;
                    } else {
                        // No franchise details provided, keep current tenant
                        $validated['tenant_id'] = $restaurant->tenant_id;
                    }
                } else {
                    // No change or no selection made, keep current tenant
                    $validated['tenant_id'] = $restaurant->tenant_id;
                }
            } else {
                // No tenant_selection parameter, keep current tenant
                $validated['tenant_id'] = $restaurant->tenant_id;
            }
        } else {
            // Non-super admin, keep current tenant
            $validated['tenant_id'] = $restaurant->tenant_id;
        }

        // Remove fields that shouldn't be saved to restaurant table
        unset($validated['tenant_selection'], $validated['contact_person'], $validated['tenant_email'], $validated['tenant_phone']);

        // Handle image uploads
        if ($request->hasFile('image')) {
            // Delete old image
            if ($restaurant->image_url) {
                $oldImagePath = str_replace('/storage/', '', $restaurant->image_url);
                Storage::disk('public')->delete($oldImagePath);
            }

            $imagePath = $request->file('image')->store('restaurants/images', 'public');
            $validated['image_url'] = Storage::url($imagePath);
        }

        if ($request->hasFile('cover_image')) {
            // Delete old cover image
            if ($restaurant->cover_image_url) {
                $oldCoverImagePath = str_replace('/storage/', '', $restaurant->cover_image_url);
                Storage::disk('public')->delete($oldCoverImagePath);
            }

            $coverImagePath = $request->file('cover_image')->store('restaurants/covers', 'public');
            $validated['cover_image_url'] = Storage::url($coverImagePath);
        }

        // Update slug if name changed
        if ($restaurant->restaurant_name !== $validated['restaurant_name']) {
            $slug = Str::slug($validated['restaurant_name']);
            $counter = 1;
            while (Restaurant::where('slug', $slug)->where('id', '!=', $restaurant->id)->exists()) {
                $slug = Str::slug($validated['restaurant_name']).'-'.$counter;
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        // FIXED: Parse business hours if provided
        if (isset($validated['business_hours']) && is_array($validated['business_hours'])) {
            $validated['business_hours'] = $this->processBusinessHours($validated['business_hours']);
        } elseif ($request->has('business_hours_json') && ! empty($request->business_hours_json)) {
            // Handle JSON string input
            $businessHours = json_decode($request->business_hours_json, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['business_hours'] = json_encode($businessHours);
            }
        }

        // Map state_id and city_id to state and city columns
        if (isset($validated['state_id'])) {
            $validated['state'] = $validated['state_id'];
            unset($validated['state_id']);
        }
        if (isset($validated['city_id'])) {
            $validated['city'] = $validated['city_id'];
            unset($validated['city_id']);
        }

        $restaurant->update($validated);

        return redirect()->route('restaurant-admin.list')
            ->with('success', 'Restaurant updated successfully!');
    }

    /**
     * Delete restaurant
     */
    public function destroy($id)
    {
        $restaurant = Restaurant::findOrFail($id);

        // Delete associated images
        if ($restaurant->image_url) {
            $imagePath = str_replace('/storage/', '', $restaurant->image_url);
            Storage::disk('public')->delete($imagePath);
        }

        if ($restaurant->cover_image_url) {
            $coverImagePath = str_replace('/storage/', '', $restaurant->cover_image_url);
            Storage::disk('public')->delete($coverImagePath);
        }

        $restaurant->delete();

        return redirect()->route('restaurant-admin.list')
            ->with('success', 'Restaurant deleted successfully!');
    }

    /**
     * Toggle restaurant pause status
     */
    public function togglePause($id)
    {
        try {
            $restaurant = Restaurant::findOrFail($id);
            $user = auth()->user();

            // Check if user can manage this restaurant
            $canManage = false;
            if ($user->role === 'super_admin') {
                $canManage = true;
            } elseif ($user->role === 'tenant_admin') {
                $canManage = $restaurant->tenant_id === $user->tenant_id;
            } elseif ($user->role === 'location_admin') {
                $canManage = $restaurant->location_admin_id === $user->id;
            }

            if (! $canManage) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to manage this restaurant.',
                ], 403);
            }

            // Toggle the pause status
            $restaurant->is_paused = ! $restaurant->is_paused;
            $restaurant->save();

            $message = $restaurant->is_paused
                ? 'Restaurant has been paused successfully.'
                : 'Restaurant has been resumed successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_paused' => $restaurant->is_paused,
            ]);

        } catch (\Exception $e) {
            Log::error('Restaurant pause toggle failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update restaurant status.',
            ], 500);
        }
    }
}
