<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\CustomerFavoriteItem;
use App\Models\CustomerProfile;
use App\Models\MenuCategory;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CustomerRegistration extends Controller
    /**
     * Get restaurants by category name (matches homepage response structure)
     * POST /api/v1/restaurants/by-category
     */
{
    /**
     * Customer Registration API
     * Creates both User and CustomerProfile records
     */
    public function register(Request $request)
    {
        // Debug: Check if request expects JSON
        // if (! $request->expectsJson()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Request must include Accept: application/json header',
        //         'debug' => [
        //             'headers' => $request->headers->all(),
        //         ],
        //     ], 400);
        // }

        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:20|unique:users,phone',
                'password' => 'required|string|min:6|confirmed',
                'date_of_birth' => 'nullable|date|before:today',
                'gender' => 'nullable|string|in:male,female,other',
                'referral_code' => 'nullable|string|max:50',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create User record
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => 'customer',
                'status' => 'active', // Customers are auto-activated
                'tenant_id' => null, // Customers don't belong to a specific tenant
                'restaurant_id' => null, // Customers aren't tied to a restaurant
            ]);

            // Create CustomerProfile record
            $user->customerProfile()->create([
                'user_id' => $user->id,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'profile_image_url' => null, // Can be updated later
                'total_orders' => 0,
                'total_spent' => 0.00,
                'referral_code' => $validated['referral_code'] ?? null,
                'loyalty_points' => 0, // Welcome bonus points
            ]);

            DB::commit();

            // Generate JWT token for immediate login
            $token = auth('api')->attempt([
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful! Welcome to HungerHop.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'full_name' => $user->first_name.' '.$user->last_name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'role' => $user->role,
                        'status' => $user->status,
                        'date_of_birth' => $user->customerProfile->date_of_birth ? $user->customerProfile->date_of_birth->toDateString() : null,
                        'gender' => $user->customerProfile->gender,
                        'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toDateTimeString() : null,
                        'last_login_at' => $user->last_login_at ? $user->last_login_at->toDateTimeString() : null,
                    ],
                    'token' => [
                        'access_token' => $token,
                        'token_type' => 'bearer',
                        'expires_in' => 36000, // 10 hours to match login
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Registration failed: '.$e->getMessage(),
                'errors' => ['general' => [$e->getMessage()]],
            ], 500);
        }
    }

    /**
     * Add Address for Customer
     */

    // "address_type" : "home",
    //         "address_line1" : "ghar",
    //         "address_line2" : "office",
    //         "landmark" : "near park",
    //         "latitude" : "28.6139",
    //         "longitude" : "77.2090",
    //         "city" : "Delhi",
    //         "state" : "Delhi",
    //         "postal_code" : "110001",
    //         "country" : "India"
    public function addAddress(Request $request)
    {

        $validated = $request->validate([
            'address_type' => 'nullable|string|in:home,work,other',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $user = auth()->user();
        if (! $user || $user->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only customers can add addresses.',
            ], 403);
        }

        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (! $customerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
            ], 404);
        }

        $address = $customerProfile->addresses()->create([
            'customer_id' => $customerProfile->id,
            'address_type' => $validated['address_type'] ?? 'home', // Default to home, can be extended later
            'address_line1' => $validated['address_line1'],
            'address_line2' => $validated['address_line2'] ?? null,
            'landmark' => $validated['landmark'] ?? null,
            'postal_code' => $validated['postal_code'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'is_default' => $validated['is_default'] ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully.',
            'data' => $address,
        ], 201);
    }

    public function addressesList(Request $request)
    {
        $user = auth()->user();
        if (! $user || $user->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only customers can view addresses.',
            ], 403);
        }

        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (! $customerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
            ], 404);
        }

        $addresses = $customerProfile->addresses()->get();

        return response()->json([
            'success' => true,
            'data' => $addresses,
        ], 200);
    }

    public function updateAddress(Request $request, $id)
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only customers can update addresses.',
            ], 403);
        }

        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();

        if (! $customerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
            ], 404);
        }

        $address = $customerProfile->addresses()->find($id);

        if (! $address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found.',
            ], 404);
        }

        $validated = $request->validate([
            'address_type' => 'nullable|string|in:home,work,other',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($validated, $customerProfile, $address) {

            // If setting this address as default, unset others
            if (isset($validated['is_default']) && $validated['is_default']) {
                $customerProfile->addresses()
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            $address->update($validated);
        });

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully.',
            'data' => $address->fresh(),
        ], 200);
    }

    public function deleteAddress(Request $request, $id)
    {
        $user = auth()->user();
        if (! $user || $user->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only customers can delete addresses.',
            ], 403);
        }
        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (! $customerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
            ], 404);
        }
        $address = $customerProfile->addresses()->find($id);
        if (! $address) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found.',
            ], 404);
        }
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully.',
        ], 200);
    }

    // Edit Profile
    public function editProfile(Request $request)
    {
        $user = auth()->user();
        if (! $user || $user->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only customers can edit profile.',
            ], 403);
        }

        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (! $customerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'nullable|string|max:100|regex:/^[A-Za-z\s]+$/',
            'last_name' => 'nullable|string|max:100|regex:/^[A-Za-z\s]+$/',
            'phone' => [
                'nullable',
                'regex:/^\+?[0-9]{10,15}$/',
                'unique:users,phone,'.$user->id,
            ],
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|string|in:male,female,other',
        ], [
            'first_name.string' => 'First name must be a string.',
            'first_name.max' => 'First name cannot exceed 100 characters.',
            'first_name.regex' => 'First name may only contain letters and spaces.',

            'last_name.string' => 'Last name must be a string.',
            'last_name.max' => 'Last name cannot exceed 100 characters.',
            'last_name.regex' => 'Last name may only contain letters and spaces.',

            'phone.regex' => 'Phone number must be 10-15 digits and may start with +.',
            'phone.unique' => 'The phone number is already registered.',

            'gender.in' => 'Gender must be one of: male, female, other.',

            'date_of_birth.date' => 'Date of birth must be a valid date.',
            'date_of_birth.before' => 'Date of birth must be before today.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        if (isset($validated['first_name'])) {
            $user->first_name = $validated['first_name'];
        }
        if (isset($validated['last_name'])) {
            $user->last_name = $validated['last_name'];
        }
        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }
        if (isset($validated['date_of_birth'])) {
            $customerProfile->date_of_birth = $validated['date_of_birth'];
        }
        if (isset($validated['gender'])) {
            $customerProfile->gender = $validated['gender'];
        }

        $user->save();
        unset($validated['first_name']);
        unset($validated['last_name']);
        $customerProfile->save();

        // Refresh models to get updated data
        $user->refresh();
        $customerProfile->refresh();

        // Generate new token
        $token = auth('api')->tokenById($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->first_name.' '.$user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'status' => $user->status,
                    'date_of_birth' => $customerProfile->date_of_birth ? $customerProfile->date_of_birth->toDateString() : null,
                    'gender' => $customerProfile->gender,
                    'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toDateTimeString() : null,
                    'last_login_at' => $user->last_login_at ? $user->last_login_at->toDateTimeString() : null,
                ],
                'token' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => 36000,
                ],
            ],
        ], 200);
    }

    public function homepage(Request $request)
    {
        $user = auth()->user();
        if (! $user || $user->role !== 'customer') {
            return response()->json([
                'ResponseCode' => '401',
                'Result' => 'false',
                'ResponseMsg' => 'Unauthorized. Only customers can access homepage.',
            ], 401);
        }

        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();
        if (! $customerProfile) {
            return response()->json([
                'ResponseCode' => '404',
                'Result' => 'false',
                'ResponseMsg' => 'Customer profile not found.',
            ], 404);
        }

        // Get customer's latitude and longitude from request or default
        $customerLat = $request->input('latitude', null);
        $customerLng = $request->input('longitude', null);

        // =====================
        // 1. BANNERS - Get from tenant banners or use placeholder
        // =====================
        $banners = $this->getBanners();

        // =====================
        // 2. CATEGORY LIST - Get distinct categories from menu_categories
        // =====================
        $categories = $this->getCategories();

        // =====================
        // 3. MAIN DATA - App configuration
        // =====================
        $mainData = $this->getMainData();

        // =====================
        // 4. WALLET BALANCE
        // =====================
        $wallet = Wallet::where('user_id', $user->id)
            ->where('user_type', 'customer')
            ->first();
        $walletBalance = $wallet ? number_format($wallet->available_balance, 2, '.', '') : '0.00';

        // =====================
        // 5. RESTAURANT DATA - All approved & open restaurants
        // =====================
        $restaurantData = $this->getRestaurants($customerProfile->id, $customerLat, $customerLng);

        // =====================
        // 6. POPULAR RESTAURANTS - Featured or high-rated restaurants
        // =====================
        $popularRestaurants = $this->getPopularRestaurants($customerProfile->id, $customerLat, $customerLng);

        // Add gallery data (banners) to HomeData
        $galleryData = $banners;

        return response()->json([
            'ResponseCode' => '200',
            'Result' => 'true',
            'ResponseMsg' => 'Home Data Get Successfully!',
            'HomeData' => [
                'Banner' => $banners,
                'Catlist' => $categories,
                'Main_Data' => $mainData,
                'wallet' => $walletBalance,
                'restuarant_data' => $restaurantData,
                'popular_restuarant' => $popularRestaurants,
                'gallery_data' => $galleryData,
            ],
        ], 200);
    }

    public function self(Request $request)
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only customers can access this endpoint.',
            ], 403);
        }

        $customerProfile = CustomerProfile::where('user_id', $user->id)->first();

        if (! $customerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
            ], 404);
        }

        // Generate new token
        $token = auth('api')->tokenById($user->id);

        return response()->json([
            'success' => true,
            'message' => 'User profile retrieved successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->first_name.' '.$user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'status' => $user->status,
                    'date_of_birth' => $customerProfile->date_of_birth ? $customerProfile->date_of_birth->toDateString() : null,
                    'gender' => $customerProfile->gender,
                    'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toDateTimeString() : null,
                    'last_login_at' => $user->last_login_at ? $user->last_login_at->toDateTimeString() : null,
                ],
                'token' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => 36000,
                ],
            ],
        ], 200);
    }

    /**
     * Get banners for homepage
     */
    private function getBanners()
    {
        // Since banners table doesn't exist, return placeholder data
        // You can create a banners table and model later
        return [
            ['id' => '1', 'img' => url('images/banner/default1.jpg'), 'rid' => '0'],
            ['id' => '2', 'img' => url('images/banner/default2.jpg'), 'rid' => '0'],
        ];
    }

    /**
     * Get distinct categories for homepage
     */
    private function getCategories()
    {
        $categories = MenuCategory::where('is_active', true)
            ->select('id', 'name', 'image_url')
            ->distinct('name')
            ->groupBy('id', 'name', 'image_url')
            ->orderBy('sort_order')
            ->limit(10)
            ->get();

        return $categories->map(function ($cat) {
            return [
                'id' => (string) $cat->id,
                'title' => $cat->name,
                'cat_img' => $cat->image_url ?? asset('images/avatar/default.jpg'),
            ];
        })->toArray();
    }

    /**
     * Get main app configuration data
     */
    private function getMainData()
    {
        return [
            'id' => '1',
            'webname' => config('app.name', 'HungerHop'),
            'weblogo' => url('images/logo.png'),
            'timezone' => config('app.timezone', 'Asia/Kolkata'),
            'currency' => '₹',
            'wname' => 'HungerHop Wallet',
            'pstore' => '100',
            'pdboy' => '4',
            'scredit' => '100',
            'rcredit' => '4',
            'is_dmode' => '0',
            'is_tax' => '1',
            's_note' => '1',
            'note' => 'We deliver fresh food to your doorstep.',
            'tax' => '5',
            'is_tip' => '1',
            'tip' => '10,20,30,50',
        ];
    }

    /**
     * Get all restaurants for homepage
     */
    private function getRestaurants($customerId, $customerLat = null, $customerLng = null)
    {
        $restaurants = Restaurant::where('status', 'approved')
            ->where('is_open', true)
            ->with(['menuCategories' => function ($query) {
                $query->where('is_active', true)->select('id', 'restaurant_id', 'name', 'image_url');
            }])
            ->orderBy('average_rating', 'desc')
            ->limit(20)
            ->get();

        return $restaurants->map(function ($rest) use ($customerId, $customerLat, $customerLng) {
            return $this->formatRestaurantData($rest, $customerId, $customerLat, $customerLng);
        })->toArray();
    }

    /**
     * Get popular/featured restaurants
     */
    private function getPopularRestaurants($customerId, $customerLat = null, $customerLng = null)
    {
        $restaurants = Restaurant::where('status', 'approved')
            ->where('is_open', true)
            ->where(function ($query) {
                $query->where('is_featured', true)
                    ->orWhere('average_rating', '>=', 4);
            })
            ->orderBy('total_orders', 'desc')
            ->limit(10)
            ->get();

        return $restaurants->map(function ($rest) use ($customerId, $customerLat, $customerLng) {
            return $this->formatRestaurantData($rest, $customerId, $customerLat, $customerLng, false);
        })->toArray();
    }

    /**
     * Format restaurant data for API response
     */
    public function formatRestaurantData($restaurant, $customerId, $customerLat = null, $customerLng = null, $includeCategories = true)
    {
        // Calculate distance if coordinates provided
        $distance = null;
        if ($customerLat && $customerLng && $restaurant->latitude && $restaurant->longitude) {
            $distance = $this->calculateDistance($customerLat, $customerLng, $restaurant->latitude, $restaurant->longitude);
        }

        // Check if restaurant is favorite (using customer_favorite_items with restaurant_id)
        $isFavorite = CustomerFavoriteItem::where('customer_id', $customerId)
            ->where('restaurant_id', $restaurant->id)
            ->where('type', CustomerFavoriteItem::TYPE_RESTAURANT)
            ->exists();

        // Unified response structure (match SearchRestaurantController@index)
        $data = [
            'id' => (string) $restaurant->id,
            'name' => $restaurant->restaurant_name,
            'city' => $restaurant->city,
            'cuisine_type' => $restaurant->cuisine_type,
            'address' => $restaurant->address,
            'full_address' => $restaurant->address.', '.$restaurant->city.', '.$restaurant->state.' - '.$restaurant->postal_code,
            'phone' => $restaurant->phone,
            'email' => $restaurant->email,
            'logo_url' => $restaurant->image_url,
            'cover_image_url' => $restaurant->cover_image_url,
            'rating' => (string) number_format((float) ($restaurant->average_rating ?? 0), 1),
            'total_reviews' => (int) ($restaurant->total_reviews ?? 0),
            'estimated_delivery_time' => (string) ($restaurant->estimated_delivery_time ?? '30'),
            'cost_for_two' => (string) number_format((float) (($restaurant->minimum_order_amount ?? 100) * 2), 2),
            'description' => $restaurant->description ?? '',
            'short_description' => $restaurant->description
                ? (strlen($restaurant->description) > 100
                    ? substr($restaurant->description, 0, 100).'...'
                    : $restaurant->description)
                : '',
            'minimum_order_amount' => (string) number_format((float) ($restaurant->minimum_order_amount ?? 0), 2),
            'base_delivery_fee' => (string) number_format((float) ($restaurant->base_delivery_fee ?? 0), 2),
            'delivery_radius_km' => (string) number_format((float) ($restaurant->delivery_radius_km ?? 10), 2),
            'tax_percentage' => (string) number_format((float) ($restaurant->tax_percentage ?? 0), 2),
            'is_open' => (bool) $restaurant->is_open,
            'is_paused' => (bool) $restaurant->is_paused,
            'accepts_orders' => (bool) $restaurant->accepts_orders,
            'is_featured' => (bool) $restaurant->is_featured,
            'status' => $restaurant->is_paused ? 'paused' : ($restaurant->is_open ? 'open' : 'closed'),
            'can_order' => $restaurant->is_open && ! $restaurant->is_paused && $restaurant->accepts_orders,
            'is_favorite' => $isFavorite,
        ];

        // Add status message based on restaurant state
        if ($restaurant->is_paused) {
            $data['status_message'] = 'This restaurant is temporarily paused and not accepting orders.';
        } elseif (! $restaurant->is_open) {
            $data['status_message'] = 'This restaurant is currently closed.';
        } elseif (! $restaurant->accepts_orders) {
            $data['status_message'] = 'This restaurant is not accepting orders at the moment.';
        } else {
            $data['status_message'] = 'Open for orders';
        }

        // Optionally include categories if requested (unchanged)
        if ($includeCategories && $restaurant->menuCategories) {
            $data['categories'] = $restaurant->menuCategories->map(function ($cat) {
                return [
                    'id' => (string) $cat->id,
                    'name' => $cat->name ?? $cat->category_name ?? '',
                ];
            })->toArray();
        }

        return $data;
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Radius of the Earth in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in kilometers
    }
}
