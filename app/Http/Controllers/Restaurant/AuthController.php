<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show restaurant registration form
     */
    public function showRegistrationForm()
    {
        return view('pages.restaurant.registration');
    }

    /**
     * Handle restaurant registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'restaurant_name' => 'required|string|max:255',
            'owner_first_name' => 'required|string|max:255',
            'owner_last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'business_hours' => 'nullable|array',
            'business_hours_json' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create user account for restaurant owner
            $user = User::create([
                'first_name' => $request->owner_first_name,
                'last_name' => $request->owner_last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'restaurant_staff',
                'status' => 'pending_approval',
            ]);

            // Create restaurant record
            $restaurant = Restaurant::create([
                'name' => $request->restaurant_name,
                'owner_id' => $user->id,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'business_hours' => $this->processBusinessHours($request),
                'status' => 'pending_approval',
            ]);

            return redirect()->route('restaurant.login.form')
                ->with('success', 'Restaurant registration successful! Your account is pending approval. You will be notified once approved.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Registration failed. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Show restaurant login form
     */
    public function showLoginForm()
    {
        return view('pages.restaurant.login');
    }

    /**
     * Handle restaurant login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return redirect()->back()
                ->withErrors(['email' => 'Invalid credentials'])
                ->withInput($request->only('email'));
        }

        // Check if user is restaurant staff
        if ($user->role !== 'restaurant_staff') {
            return redirect()->back()
                ->withErrors(['email' => 'This login is only for restaurant staff'])
                ->withInput($request->only('email'));
        }

        // Check if user account is active
        if ($user->status !== 'active') {
            $message = match ($user->status) {
                'inactive' => 'Your account is inactive. Please contact support.',
                'suspended' => 'Your account has been suspended. Please contact support.',
                'pending_approval' => 'Your account is pending approval. Please wait for admin approval.',
                default => 'Your account is not accessible at this time.'
            };

            return redirect()->back()
                ->withErrors(['email' => $message])
                ->withInput($request->only('email'));
        }

        // Login the user
        Auth::login($user);

        return redirect()->route('restaurant.dashboard')
            ->with('success', 'Welcome back, '.$user->first_name.'!');
    }

    /**
     * Handle restaurant logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('restaurant.login.form')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Process business hours input
     * Convert structured form input to properly formatted JSON
     */
    private function processBusinessHours($request)
    {
        // Check if we have the new structured format
        if ($request->has('business_hours_json') && ! empty($request->business_hours_json)) {
            $businessHours = json_decode($request->business_hours_json, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return json_encode($businessHours);
            }
        }

        // Fallback to old format if business_hours_json is not available
        $businessHours = $request->business_hours;
        if (empty($businessHours) || trim($businessHours) === '') {
            return null;
        }

        // If it's already valid JSON, decode and re-encode to ensure proper format
        $decoded = json_decode($businessHours, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return json_encode($decoded);
        }

        // If it's a simple string (like "Mon-Fri: 9:00 AM - 10:00 PM"),
        // convert to a basic JSON structure
        return json_encode(['description' => trim($businessHours)]);
    }
}
