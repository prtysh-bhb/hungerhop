<?php

namespace App\Http\Controllers\Auth;

use App\Actions\User\CreateUserAction;
use App\DTOs\Auth\RegisterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function __construct(
        private CreateUserAction $createUserAction
    ) {}

    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        // If user is already logged in, redirect to dashboard
        if (Auth::check()) {
            $user = Auth::user();
            $redirectPath = $this->getRedirectPathForRole($user->role);

            return redirect()->to($redirectPath);
        }

        return view('layouts.partials.guest.auth_register');
    }

    /**
     * Handle registration request.
     */
    public function register(RegisterRequest $request)
    {
        try {
            // Debug: Log the validated request data
            \Log::info('Registration attempt:', $request->validated());

            // Create RegisterDTO from validated request data
            $registerDTO = RegisterDTO::fromRequest($request->validated());

            // Debug: Log the DTO data
            \Log::info('RegisterDTO created:', $registerDTO->toUserData());

            // Create the user using the action with DTO
            $user = $this->createUserAction->executeWithDTO($registerDTO);

            // Debug: Log user creation success
            \Log::info('User created successfully:', ['id' => $user->id, 'email' => $user->email]);

            // Send verification email (optional)
            // $user->sendEmailVerificationNotification()

            // Auto-login for customers, redirect to approval page for others
            if ($registerDTO->shouldAutoActivate()) {
                $user->update(['status' => 'active']); // Auto-approve customers
                Auth::login($user);

                // Role-based redirect after registration
                $redirectPath = $this->getRedirectPathForRole($user->role);

                return redirect()->to($redirectPath)
                    ->with('success', 'Registration successful! Welcome to HungerHop.');
            } else {
                return redirect()->route('login')
                    ->with('success', 'Registration successful! Your account is pending approval. You will be notified once approved.');
            }

        } catch (\Exception $e) {
            // Debug: Log the error
            \Log::error('Registration failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withErrors(['registration' => 'Registration failed: '.$e->getMessage()])
                ->withInput($request->except(['password', 'password_confirmation']));
        }
    }

    /**
     * Handle restaurant staff registration.
     */
    public function registerRestaurantStaff(Request $request)
    {
        // Validate the request with additional restaurant-specific fields
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => ['required', 'string', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()],
            'password_confirmation' => 'required|string',
            'terms' => 'required|accepted',
            'restaurant_id' => 'required|exists:restaurants,id',
            'staff_role' => 'required|string|in:location_admin,manager,chef,cashier',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        try {
            // Prepare user data for restaurant staff
            $userData = [
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->password,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'role' => 'restaurant_staff',
                'status' => 'pending_approval',
                'restaurant_id' => $request->restaurant_id,
                'tenant_id' => null, // Will be set based on restaurant's tenant
            ];

            // Create the user
            $user = $this->createUserAction->execute($userData);

            // Create restaurant staff record
            // RestaurantStaff::create([
            //     'restaurant_id' => $request->restaurant_id,
            //     'user_id' => $user->id,
            //     'role' => $request->staff_role,
            //     'is_active' => false, // Will be activated after approval
            // ]);

            return redirect()->route('login')
                ->with('success', 'Restaurant staff registration successful! Your account is pending approval.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['registration' => 'Registration failed. Please try again.'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }
    }

    /**
     * Handle delivery partner registration.
     */
    public function registerDeliveryPartner(Request $request)
    {
        // Validate the request with additional delivery partner-specific fields
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => ['required', 'string', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()],
            'password_confirmation' => 'required|string',
            'terms' => 'required|accepted',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'vehicle_type' => 'required|string|in:bike,car,bicycle',
            'license_number' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        try {
            // Prepare user data for delivery partner
            $userData = [
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->password,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'role' => 'delivery_partner',
                'status' => 'pending_approval',
                'tenant_id' => null,
                'restaurant_id' => null,
            ];

            // Create the user
            $user = $this->createUserAction->execute($userData);

            // Create delivery partner record
            // DeliveryPartner::create([
            //     'user_id' => $user->id,
            //     'vehicle_type' => $request->vehicle_type,
            //     'license_number' => $request->license_number,
            //     'status' => 'pending_verification',
            // ]);

            return redirect()->route('login')
                ->with('success', 'Delivery partner registration successful! Your account is pending approval and document verification.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['registration' => 'Registration failed. Please try again.'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }
    }

    /**
     * Get the redirect path based on user role after registration.
     */
    private function getRedirectPathForRole(string $role): string
    {
        return match ($role) {
            'super_admin', 'tenant_admin' => route('admin.dashboard'),
            'restaurant_staff', 'location_admin', 'delivery_partner' => route('restaurant.dashboard'),
            'customer' => route('customer.dashboard'),
            default => route('customer.dashboard') // Default fallback
        };
    }
}
