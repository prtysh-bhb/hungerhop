<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
}
