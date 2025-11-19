<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guest\DeliveryPartnerRegistrationRequest;
use App\Models\DeliveryPartner;
use App\Models\DeliveryPartnerDocument;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DeliveryPartnerRegistrationController extends Controller
{
    /**
     * Show the delivery partner registration form
     */
    public function showForm()
    {
        // If user is already logged in, redirect to dashboard
        if (Auth::check()) {
            $user = Auth::user();
            $redirectPath = $this->getRedirectPathForRole($user->role);

            return redirect()->to($redirectPath);
        }

        return view('layouts.partials.guest.delivery_registeration');
    }

    /**
     * Handle the registration form submission
     */
    public function register(DeliveryPartnerRegistrationRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // Create user as delivery_partner
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => 'delivery_partner',
                'status' => 'pending_approval',
            ]);

            // Handle profile image upload
            $profileImageUrl = null;
            if ($request->hasFile('profile_image')) {
                $profileImageUrl = $request->file('profile_image')->store('delivery-partner-profiles', 'public');
            }

            // Create delivery partner profile with all required fields
            $deliveryPartner = DeliveryPartner::create([
                'user_id' => $user->id,
                'vehicle_type' => $validated['vehicle_type'],
                'vehicle_number' => $validated['vehicle_number'],
                'license_number' => $validated['license_number'],
                'profile_image_url' => $profileImageUrl,
                'current_latitude' => $validated['current_latitude'],
                'current_longitude' => $validated['current_longitude'],
                'is_available' => $validated['is_available'] ?? false,
                'is_online' => $validated['is_online'] ?? false,
                'status' => 'pending',
            ]);

            // Upload and create delivery partner document
            $documentPath = $request->file('document_file')->store('delivery-partner-documents', 'public');

            $deliveryPartnerDocument = DeliveryPartnerDocument::create([
                'partner_id' => $deliveryPartner->id,
                'document_type' => $validated['document_type'],
                'document_path' => $documentPath,
                'document_name' => $request->file('document_file')->getClientOriginalName(),
                'file_size' => $request->file('document_file')->getSize(),
                'mime_type' => $request->file('document_file')->getMimeType(),
                'status' => 'pending',
                'uploaded_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('login')
                ->with('success', 'Registration successful! Your account is pending approval. You will be notified once approved.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Registration failed: '.$e->getMessage()]);
        }
    }

    /**
     * Get the redirect path based on user role.
     */
    private function getRedirectPathForRole(string $role): string
    {
        return match ($role) {
            'super_admin', 'tenant_admin' => route('admin.dashboard'),
            'restaurant_staff', 'location_admin', 'delivery_partner' => route('restaurant.dashboard'),
            'customer' => route('customer.dashboard'),
            default => route('customer.dashboard')
        };
    }
}
