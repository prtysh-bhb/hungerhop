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
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->first_name.' '.$user->last_name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'role' => $user->role,
                    'delivery_partner' => [
                        'vehicle_type' => $user->deliveryPartner->vehicle_type,
                        'vehicle_number' => $user->deliveryPartner->vehicle_number,
                        'license_number' => $user->deliveryPartner->license_number,
                        'rating' => (string) number_format((float) ($user->deliveryPartner->average_rating ?? 0), 1),
                        'current_latitude' => $user->deliveryPartner->current_latitude,
                        'current_longitude' => $user->deliveryPartner->current_longitude,
                        'total_deliveries' => $user->deliveryPartner->total_deliveries,
                    ],
                    'status' => $user->status, // pending_approval / active
                ],
                'token' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => 360000,
                ],
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
        $allowedDocuments = [
            'id_proof',
            'driving_license',
            'rc',
            'address_proof',
            'bank_passbook',
        ];

        $rules = [
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
        ];

        // Dynamically add file rules for all allowed document types
        foreach ($allowedDocuments as $doc) {
            $rules[$doc] = 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120';
        }

        try {
            $validated = $request->validate($rules);

            // Ensure at least one document is provided
            $hasDocument = false;
            foreach ($allowedDocuments as $doc) {
                if ($request->hasFile($doc)) {
                    $hasDocument = true;
                    break;
                }
            }

            if (! $hasDocument) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => ['document' => ['At least one document is required (id_proof, driving_license, rc, address_proof, or bank_passbook)']],
                ], 422);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
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

            // Handle document upload - Process each document type
            foreach ($allowedDocuments as $documentType) {
                if ($request->hasFile($documentType)) {
                    $file = $request->file($documentType);
                    $documentPath = $file->store('delivery_partner_documents', 'public');

                    DeliveryPartnerDocument::create([
                        'partner_id' => $deliveryPartner->id,
                        'document_type' => $documentType,
                        'document_path' => $documentPath,
                        'document_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'status' => 'pending', // Pending admin review
                        'uploaded_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registration successful! Your application is pending admin approval. You will be notified once approved.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'full_name' => $user->first_name.' '.$user->last_name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'role' => $user->role,
                        'status' => 'pending_approval',
                        'delivery_partner_id' => $deliveryPartner->id,
                        'vehicle_type' => $deliveryPartner->vehicle_type,
                        'vehicle_number' => $deliveryPartner->vehicle_number,
                        'application_submitted_at' => now()->toDateTimeString(),
                    ],
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

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please login first.',
            ], 401);
        }

        if ($user->role !== 'delivery_partner') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only delivery partners can use this endpoint.',
            ], 403);
        }

        $deliveryPartner = DeliveryPartner::where('user_id', $user->id)->first();
        if (! $deliveryPartner) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery partner profile not found.',
            ], 404);
        }

        // 🔥 Manual Validation (NOT request->validate)
        $validator = Validator::make($request->all(), [

            'first_name' => 'sometimes|required|string|min:2|max:100',
            'last_name' => 'sometimes|required|string|min:2|max:100',
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => [
                'sometimes',
                'required',
                'string',
                'min:10',
                'max:15',
                Rule::unique('users', 'phone')->ignore($user->id),
                'regex:/^[0-9+\-\s]+$/',
            ],

            'vehicle_type' => [
                'sometimes',
                'required',
                'string',
                Rule::in(array_column(VehicleTypeEnums::cases(), 'value')),
            ],

            'vehicle_number' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                'regex:/^[A-Za-z0-9\s\-]+$/',
            ],

            'license_number' => 'sometimes|required|string|max:50',

            'current_longitude' => 'sometimes|required|numeric|between:-180,180',
            'current_latitude' => 'sometimes|required|numeric|between:-90,90',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed',
            ], 422);
        }

        // 👍 Validation Passed
        $validated = $validator->validated();

        // Update User Data
        $user->fill([
            'first_name' => $validated['first_name'] ?? $user->first_name,
            'last_name' => $validated['last_name'] ?? $user->last_name,
            'phone' => $validated['phone'] ?? $user->phone,
            'email' => $validated['email'] ?? $user->email,
        ]);
        $user->save();

        // Update Delivery Partner Data
        $deliveryPartner->fill([
            'vehicle_type' => $validated['vehicle_type'] ?? $deliveryPartner->vehicle_type,
            'vehicle_number' => $validated['vehicle_number'] ?? $deliveryPartner->vehicle_number,
            'license_number' => $validated['license_number'] ?? $deliveryPartner->license_number,
            'current_latitude' => $validated['current_latitude'] ?? $deliveryPartner->current_latitude,
            'current_longitude' => $validated['current_longitude'] ?? $deliveryPartner->current_longitude,
        ]);
        $deliveryPartner->save();

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
                    'delivery_partner' => [
                        'vehicle_type' => $deliveryPartner->vehicle_type,
                        'vehicle_number' => $deliveryPartner->vehicle_number,
                        'license_number' => $deliveryPartner->license_number,
                        'rating' => (string) number_format((float) $deliveryPartner->average_rating, 1),
                        'total_deliveries' => $deliveryPartner->total_deliveries,
                        'current_latitude' => $deliveryPartner->current_latitude,
                        'current_longitude' => $deliveryPartner->current_longitude,
                    ],
                ],
            ],
        ], 200);

    }
}
