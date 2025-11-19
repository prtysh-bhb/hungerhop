<?php

namespace App\Http\Controllers\Auth;

use App\DTOs\Auth\LoginDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        // If user is already logged in, redirect to dashboard
        if (Auth::check()) {
            $user = Auth::user();

            return $this->redirectAfterLogin($user);
        }

        return view('layouts.partials.guest.auth_login');
    }

    /**
     * Handle login request.
     */
    public function login(LoginRequest $request)
    {
        // Create LoginDTO from request
        $loginDTO = LoginDTO::fromRequest($request->validated());

        // Find user by email or phone
        $user = User::where($loginDTO->getFieldType(), $loginDTO->username)->first();

        if (! $user) {
            return redirect()->back()
                ->withErrors(['username' => 'No account found with this email or phone number.'])
                ->withInput($request->only('username'));
        }

        // Check if user is active or special cases
        if ($user->status !== 'active') {
            // Allow tenant_admin with pending_approval status to login for payment
            if ($user->role === 'tenant_admin' && $user->status === 'pending_approval') {
                // Allow login but they'll be restricted by middleware
            } else {
                $message = match ($user->status) {
                    'inactive' => 'Your account is inactive. Please contact support.',
                    'suspended' => 'Your account has been suspended. Please contact support.',
                    'pending_approval' => 'Your account is pending approval. Please wait for admin approval.',
                    default => 'Your account is not accessible at this time.'
                };

                return redirect()->back()
                    ->withErrors(['username' => $message])
                    ->withInput($request->only('username'));
            }
        }

        // Verify password
        if (! Hash::check($loginDTO->password, $user->getAuthPassword())) {
            return redirect()->back()
                ->withErrors(['password' => 'The password you entered is incorrect.'])
                ->withInput($request->only('username'));
        }

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Login the user
        Auth::login($user, $loginDTO->remember);

        // Regenerate session to prevent session fixation attacks
        $request->session()->regenerate();

        // Redirect based on user role
        return $this->redirectAfterLogin($user);
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Redirect user after successful login based on their role.
     */
    protected function redirectAfterLogin(User $user)
    {
        // Check if user came from restaurant-admin area
        $previousUrl = url()->previous();
        $isFromRestaurantAdmin = str_contains($previousUrl, '/restaurant-admin/');

        $redirectPath = match ($user->role) {
            'super_admin', 'tenant_admin' => route('admin.dashboard'),
            'restaurant_staff', 'location_admin', 'delivery_partner' => $isFromRestaurantAdmin ? route('restaurant-admin.list') : route('restaurant.dashboard'),
            'customer' => route('customer.dashboard'),
            default => route('customer.dashboard') // Default fallback
        };

        return redirect()->to($redirectPath)->with('success', 'Welcome back, '.$user->first_name.'!');
    }
}
