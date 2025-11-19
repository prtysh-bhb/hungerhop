<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerProfileController extends Controller
{
    /**
     * Show customer profile management page
     */
    public function index($customerId)
    {
        $customer = User::with(['customerProfile', 'addresses'])
            ->where('role', 'customer')
            ->findOrFail($customerId);

        // Calculate order statistics separately
        $orderStats = [
            'total_orders' => $customer->orders()->count(),
            'total_spent' => $customer->orders()->sum('total_amount'),
            'avg_order_value' => $customer->orders()->count() > 0 ? $customer->orders()->avg('total_amount') : 0,
            'last_order' => $customer->orders()->latest()->first(),
        ];

        return view('pages.customer.profile', compact('customer', 'orderStats'));
    }

    /**
     * Update customer profile
     */
    public function updateProfile(Request $request, $customerId)
    {
        $request->validate([
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:15',
                'regex:/^[a-zA-Z]+$/', // Only letters, no spaces
            ],
            'last_name' => [
                'required',
                'string',
                'min:2',
                'max:15',
                'regex:/^[a-zA-Z]+$/', // Only letters, no spaces
            ],
            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.com$/', // Must end with .com
                'unique:users,email,'.$customerId,
            ],
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^[1-9][0-9]{9,14}$/', // 10-15 digits, not starting with 0, not all zeros
            ],
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'first_name.regex' => 'First name must contain only letters (no spaces or special characters).',
            'first_name.min' => 'First name must be at least 2 characters.',
            'first_name.max' => 'First name cannot exceed 15 characters.',
            'last_name.regex' => 'Last name must contain only letters (no spaces or special characters).',
            'last_name.min' => 'Last name must be at least 2 characters.',
            'last_name.max' => 'Last name cannot exceed 15 characters.',
            'email.regex' => 'Email must end with .com domain.',
            'phone.regex' => 'Phone number must be 10-15 digits and cannot be all zeros.',
            'phone.min' => 'Phone number must be at least 10 digits.',
            'phone.max' => 'Phone number cannot exceed 15 digits.',
            'date_of_birth.before' => 'Date of birth must be a past date.',
            'profile_image.max' => 'Profile image must not exceed 2MB.',
        ]);

        $customer = User::where('role', 'customer')->findOrFail($customerId);

        // Update user basic info
        $customer->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // Update or create customer profile
        $profileData = [
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
        ];

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = 'customer_'.$customerId.'_'.time().'.'.$file->getClientOriginalExtension();
            $path = $file->storeAs('customer_profiles', $filename, 'public');
            $profileData['profile_image_url'] = $path;
        }

        $customer->customerProfile()->updateOrCreate(
            ['user_id' => $customerId],
            $profileData
        );

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    /**
     * Update customer address
     */
    public function updateAddress(Request $request, $customerId)
    {
        $request->validate([
            'address_line1' => [
                'required',
                'string',
                'min:5',
                'max:255',
            ],
            'address_line2' => [
                'nullable',
                'string',
                'max:255',
            ],
            'city' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-Z\s]+$/', // Only letters and spaces
            ],
            'state' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[a-zA-Z\s]+$/', // Only letters and spaces
            ],
            'postal_code' => [
                'required',
                'string',
                'min:4',
                'max:20',
                'regex:/^[0-9]+$/', // Only numbers
            ],
            'address_type' => 'nullable|in:home,work,other',
            'landmark' => [
                'nullable',
                'string',
                'max:255',
            ],
            'is_default' => 'nullable|boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ], [
            'address_line1.required' => 'Address line 1 is required.',
            'address_line1.min' => 'Address must be at least 5 characters.',
            'city.regex' => 'City must contain only letters and spaces.',
            'city.min' => 'City must be at least 2 characters.',
            'state.regex' => 'State must contain only letters and spaces.',
            'state.min' => 'State must be at least 2 characters.',
            'postal_code.regex' => 'Postal code must contain only numbers.',
            'postal_code.min' => 'Postal code must be at least 4 digits.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
        ]);

        $customer = User::where('role', 'customer')->findOrFail($customerId);

        // Ensure customer profile exists
        if (! $customer->customerProfile) {
            return redirect()->back()->with('error', 'Customer profile not found. Please create a profile first.');
        }

        $customerProfileId = $customer->customerProfile->id;

        // If this is set as default, remove default from other addresses
        if ($request->is_default) {
            CustomerAddress::where('customer_id', $customerProfileId)
                ->update(['is_default' => false]);
        }

        CustomerAddress::create([
            'customer_id' => $customerProfileId,
            'address_line1' => $request->address_line1,
            'address_line2' => $request->address_line2,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'address_type' => $request->address_type ?? 'home',
            'landmark' => $request->landmark,
            'latitude' => $request->latitude ?? 0.00000000, // Default to 0 if not provided
            'longitude' => $request->longitude ?? 0.00000000, // Default to 0 if not provided
            'is_default' => $request->is_default ?? false,
        ]);

        return redirect()->back()->with('success', 'Address added successfully');
    }

    /**
     * Delete customer address
     */
    public function deleteAddress($customerId, $addressId)
    {
        $customer = User::where('role', 'customer')->findOrFail($customerId);

        // Ensure customer profile exists
        if (! $customer->customerProfile) {
            return response()->json([
                'success' => false,
                'message' => 'Customer profile not found.',
            ], 404);
        }

        $customerProfileId = $customer->customerProfile->id;

        $address = CustomerAddress::where('customer_id', $customerProfileId)
            ->findOrFail($addressId);

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully',
        ]);
    }

    /**
     * Update loyalty points
     */
    public function updateLoyaltyPoints(Request $request, $customerId)
    {
        $request->validate([
            'points' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
        ]);

        $customer = User::where('role', 'customer')->findOrFail($customerId);

        $customer->customerProfile()->updateOrCreate(
            ['user_id' => $customerId],
            ['loyalty_points' => $request->points]
        );

        // You can add a loyalty points history table here if needed

        return redirect()->back()->with('success', 'Loyalty points updated successfully');
    }
}
