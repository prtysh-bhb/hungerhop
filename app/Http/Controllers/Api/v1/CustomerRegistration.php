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
{
    /**
     * Customer Registration API
     * Creates both User and CustomerProfile records
     */
    public function register(Request $request)
    {
        // Debug: Check if request expects JSON
        if (! $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Request must include Accept: application/json header',
                'debug' => [
                    'headers' => $request->headers->all(),
                ],
            ], 400);
        }

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
            $customerProfile = CustomerProfile::create([
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
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'role' => $user->role,
                        'status' => $user->status,
                    ],
                    'customer_profile' => [
                        'id' => $customerProfile->id,
                        'date_of_birth' => $customerProfile->date_of_birth,
                        'gender' => $customerProfile->gender,
                        'loyalty_points' => $customerProfile->loyalty_points,
                        'total_orders' => $customerProfile->total_orders,
                        'total_spent' => $customerProfile->total_spent,
                    ],
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl', 60) * 60, // Default 1 hour
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

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => $customerProfile,
            'phone' => $user->phone,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'gender' => $customerProfile->gender,
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
    private function formatRestaurantData($restaurant, $customerId, $customerLat = null, $customerLng = null, $includeCategories = true)
    {
        // Calculate distance if coordinates provided
        $distance = null;
        if ($customerLat && $customerLng && $restaurant->latitude && $restaurant->longitude) {
            $distance = $this->calculateDistance(
                $customerLat,
                $customerLng,
                $restaurant->latitude,
                $restaurant->longitude
            );
        }

        // Check if restaurant is favorite (using customer_favorite_items with restaurant_id)
        $isFavorite = CustomerFavoriteItem::where('customer_id', $customerId)
            ->where('restaurant_id', $restaurant->id)
            ->exists() ? 1 : 0;

        $data = [
            'rest_id' => (string) $restaurant->id,
            'rest_title' => $restaurant->restaurant_name,
            'rest_img' => $restaurant->image_url ?? url('images/avatar/default.jpg'),
            'rest_rating' => (string) ($restaurant->average_rating ?? '5'),
            'rest_deliverytime' => (string) ($restaurant->estimated_delivery_time ?? '30'),
            'rest_costfortwo' => (string) ($restaurant->minimum_order_amount * 2 ?? '200'),
            'rest_is_veg' => '0', // You can add this field to restaurants table
            'rest_full_address' => $restaurant->address.', '.$restaurant->city.', '.$restaurant->state.' - '.$restaurant->postal_code,
            'rest_charge' => (string) ($restaurant->base_delivery_fee ?? '50'),
            'rest_is_open' => $restaurant->is_open ? '1' : '0',
            'cou_title' => '', // Coupon title - can be added later
            'cou_subtitle' => '', // Coupon subtitle - can be added later
            'rest_dcharge' => null,
            'rest_morder' => (string) ($restaurant->minimum_order_amount ?? '100'),
            'rest_sdesc' => $restaurant->description ?? '',
            'rest_distance' => $distance ? number_format($distance, 2).' Kms' : null,
            'IS_FAVOURITE' => $isFavorite,
        ];

        // Include categories if requested
        if ($includeCategories && $restaurant->menuCategories) {
            $data['rest_category'] = $restaurant->menuCategories->map(function ($cat) {
                return [
                    'id' => (string) $cat->id,
                    'cat_name' => $cat->name,
                    'cat_status' => '1',
                    'cat_img' => $cat->image_url ?? url('images/avatar/default.jpg'),
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
