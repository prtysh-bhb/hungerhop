<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\VehicleTypeEnums;
use App\Http\Controllers\Controller;
use App\Models\DeliveryPartner;
use App\Models\DeliveryPartnerDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeliveryPartner_login extends Controller
{
    public function login(Request $request)
    {

        $credentials = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'No account found with this email address.'], 401);
        }
        // Only allow delivery partners to login
        if ($user->role !== 'delivery_partner') {
            return response()->json(['success' => false, 'message' => 'Access denied: Only delivery partners can login here.'], 403);
        }
        if (! Hash::check($request->password, $user->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }
        if (! $token = JWTAuth::fromUser($user)) {
            return response()->json(['success' => false, 'message' => 'Could not create token.'], 500);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->first_name.' '.$user->last_name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => 360000, // Fixed 10 hour
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out',
            ], 200);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => 'Could not invalidate token.',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => 'Something went wrong during logout.',
            ], 500);
        }
    }

    /**
     * Delivery Partner Self-Registration (API)
     * This allows delivery partners to register themselves
     * Admin approval is required before they can start working
     */
    public function register(Request $request)
    {
        // Debug: Check if request is being treated as API
        if (! $request->expectsJson()) {
            return response()->json([
                'debug' => 'Request not expecting JSON. Add Accept: application/json header',
                'headers' => $request->headers->all(),
            ], 400);
        }

        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:20|unique:users,phone',
                'password' => 'required|string|min:6|confirmed',
                'vehicle_type' => 'required|string|in:'.implode(',', array_column(VehicleTypeEnums::cases(), 'value')),
                'vehicle_number' => 'required|string|max:20',
                'license_number' => 'required|string|max:50',
                'current_longitude' => 'required|numeric|between:-180,180',
                'current_latitude' => 'required|numeric|between:-90,90',
                'document_type' => 'required|in:id_proof,driving_license,rc,address_proof,bank_passbook',
                // 'document_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB max
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
                'debug_data' => $request->all(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create user as delivery_partner (pending approval)
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => 'delivery_partner',
                'status' => 'pending_approval', // Requires admin approval
            ]);

            // Create delivery partner profile
            $deliveryPartner = DeliveryPartner::create([

                'user_id' => $user->id,
                'vehicle_type' => $validated['vehicle_type'],
                'vehicle_number' => $validated['vehicle_number'],
                'license_number' => $validated['license_number'],
                'current_latitude' => $validated['current_latitude'],
                'current_longitude' => $validated['current_longitude'],
                'is_available' => false, // Not available until approved
                'is_online' => false,
                'status' => 'pending', // Pending admin approval
                'total_deliveries' => 0,
                'total_earnings' => 0.00,
                'average_rating' => 0.00,
                'total_reviews' => 0,
                'commission_percentage' => 15.00, // Default commission
            ]);

            // Handle document upload
            // $documentPath = $request->file('document_file')->store('delivery_partner_documents', 'public');

            // $deliveryPartnerDocument = DeliveryPartnerDocument::create([
            //     'partner_id' => $deliveryPartner->id,
            //     'document_type' => $validated['document_type'],
            //         'document_path' => $documentPath,
            //         'document_name' => $request->file('document_file')->getClientOriginalName(),
            //         'file_size' => $request->file('document_file')->getSize(),
            //         'mime_type' => $request->file('document_file')->getMimeType(),
            //     'status' => 'pending', // Pending admin review
            //     'uploaded_at' => now(),
            // ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registration successful! Your application is pending admin approval. You will be notified once approved.',
                'data' => [
                    'user_id' => $user->id,
                    'delivery_partner_id' => $deliveryPartner->id,
                    'status' => 'pending_approval',
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'vehicle_type' => $deliveryPartner->vehicle_type,
                    'vehicle_number' => $deliveryPartner->vehicle_number,
                    'application_submitted_at' => now()->toDateTimeString(),
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Registration failed: '.$e->getMessage(),
                'errors' => ['general' => [$e->getMessage()]],
            ], 422);
        }
    }
}
