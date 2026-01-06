<?php

namespace App\Http\Controllers\Api\v1;

use App\Enums\VehicleTypeEnums;
use App\Http\Controllers\Controller;
use App\Models\DeliveryPartner;
use App\Models\DeliveryPartnerDocument;
use App\Models\PaymentDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
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

        // Determine unified status
        $deliveryPartner = $user->deliveryPartner;
        $documents = DeliveryPartnerDocument::where('partner_id', $deliveryPartner->id)->get();

        $status = $deliveryPartner->status; // Default to delivery_partner status
        $rejectionReason = null;

        // If delivery partner status is pending, check documents
        if ($deliveryPartner->status === 'pending') {
            if ($documents->count() === 0) {
                // No documents uploaded
                $status = 'pending_documents';
            } else {
                // Check if any document is rejected
                $rejectedDoc = $documents->firstWhere('status', 'rejected');
                if ($rejectedDoc) {
                    $status = 'rejected';
                    $rejectionReason = $rejectedDoc->rejection_reason ?? 'Your documents were rejected. Please contact admin for details.';
                } elseif ($documents->every(fn ($doc) => $doc->status === 'approved')) {
                    // All documents approved, but still pending approval from admin
                    $status = 'pending_approval';
                } else {
                    // Some documents are pending review
                    $status = 'pending_approval';
                }
            }
        } elseif ($deliveryPartner->status === 'rejected') {
            // Partner was rejected at delivery_partner level
            $status = 'rejected';
            $rejectionReason = $deliveryPartner->rejection_reason ?? 'Your application was rejected. Please contact admin for details.';
        }
        $paymentDetail = PaymentDetail::where('user_id', $user->id)->exists();

        $responseData = [
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
                        'vehicle_type' => $deliveryPartner->vehicle_type,
                        'vehicle_number' => $deliveryPartner->vehicle_number,
                        'license_number' => $deliveryPartner->license_number,
                        'rating' => (string) number_format((float) ($deliveryPartner->average_rating ?? 0), 1),
                        'current_latitude' => $deliveryPartner->current_latitude,
                        'current_longitude' => $deliveryPartner->current_longitude,
                        'total_deliveries' => $deliveryPartner->total_deliveries,
                    ],
                    'status' => $status,
                    'is_available' => $deliveryPartner->is_available,
                    'is_online' => $deliveryPartner->is_online,
                    'has_payment_details' => $paymentDetail,
                ],
                'token' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => 360000,
                ],
            ],
        ];

        // Add rejection reason if rejected
        if ($status === 'rejected' && $rejectionReason) {
            $responseData['data']['user']['rejection_reason'] = $rejectionReason;
        }

        return response()->json($responseData, 200);

    }

    public function logout(Request $request)
    {
        try {
            $token = JWTAuth::getToken();
            $user = null;

            // Try authenticating user safely
            if ($token) {
                try {
                    $user = JWTAuth::authenticate($token);
                } catch (TokenExpiredException|TokenInvalidException $e) {
                    // Token invalid/expired → continue logout silently
                }
            }

            // Role-based cleanup (BEST EFFORT)
            if ($user && $user->role === 'delivery_partner') {
                $deliveryPartner = DeliveryPartner::where('user_id', $user->id)->first();

                if ($deliveryPartner) {
                    $deliveryPartner->update([
                        'is_online' => false,
                        'is_available' => false,
                    ]);
                }
            }

            // Invalidate token if possible
            if ($token) {
                try {
                    JWTAuth::invalidate($token, true); // Force invalidation
                } catch (JWTException $e) {
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out',
            ], 200);

        } catch (\Throwable $e) {
            // Logout must NEVER fail
            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out',
            ], 200);
        }
    }

    /**
     * Get Delivery Partner Profile (Self Introduction)
     * Returns complete delivery partner profile data
     */
    public function getProfile(Request $request)
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
                'message' => 'Unauthorized. Only delivery partners can access this endpoint.',
            ], 403);
        }

        $deliveryPartner = DeliveryPartner::where('user_id', $user->id)->first();
        if (! $deliveryPartner) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery partner profile not found.',
            ], 404);
        }

        // Get documents
        $documents = DeliveryPartnerDocument::where('partner_id', $deliveryPartner->id)->get();

        // Calculate unified status (same as login)
        $status = $deliveryPartner->status;
        $rejectionReason = null;

        if ($deliveryPartner->status === 'pending') {
            if ($documents->count() === 0) {
                $status = 'pending_documents';
            } else {
                $rejectedDoc = $documents->firstWhere('status', 'rejected');
                if ($rejectedDoc) {
                    $status = 'rejected';
                    $rejectionReason = $rejectedDoc->rejection_reason ?? 'Your documents were rejected. Please contact admin for details.';
                } elseif ($documents->every(fn ($doc) => $doc->status === 'approved')) {
                    $status = 'pending_approval';
                } else {
                    $status = 'pending_approval';
                }
            }
        } elseif ($deliveryPartner->status === 'rejected') {
            $status = 'rejected';
            $rejectionReason = $deliveryPartner->rejection_reason ?? 'Your application was rejected. Please contact admin for details.';
        }

        // Get payment details
        $paymentDetail = PaymentDetail::where('user_id', $user->id)->first();

        // Build documents array
        $documentsArray = $documents->map(function ($doc) {
            return [
                'id' => (string) $doc->id,
                'type' => (string) $doc->document_type,
                'status' => (string) $doc->status,
                'rejection_reason' => (string) ($doc->rejection_reason ?? ''),
                'uploaded_at' => $doc->created_at ? $doc->created_at->toISOString() : null,
                'approved_at' => $doc->approved_at ? $doc->approved_at->toISOString() : null,
            ];
        })->toArray();

        $responseData = [
            'success' => true,
            'message' => 'Profile retrieved successfully.',
            'data' => [
                'user' => [
                    'id' => (string) $user->id,
                    'first_name' => (string) $user->first_name,
                    'last_name' => (string) $user->last_name,
                    'full_name' => (string) ($user->first_name.' '.$user->last_name),
                    'email' => (string) $user->email,
                    'phone' => (string) $user->phone,
                    'role' => (string) $user->role,
                ],
                'delivery_partner' => [
                    'id' => (string) $deliveryPartner->id,
                    'vehicle_type' => (string) ($deliveryPartner->vehicle_type ?? ''),
                    'vehicle_number' => (string) ($deliveryPartner->vehicle_number ?? ''),
                    'license_number' => (string) ($deliveryPartner->license_number ?? ''),
                    'current_latitude' => $deliveryPartner->current_latitude ? (float) $deliveryPartner->current_latitude : null,
                    'current_longitude' => $deliveryPartner->current_longitude ? (float) $deliveryPartner->current_longitude : null,
                    'is_available' => (bool) $deliveryPartner->is_available,
                    'is_online' => (bool) $deliveryPartner->is_online,
                    'status' => (string) $status,
                    'total_deliveries' => (int) $deliveryPartner->total_deliveries,
                    'total_earnings' => (float) $deliveryPartner->total_earnings,
                    'average_rating' => (float) $deliveryPartner->average_rating,
                    'total_reviews' => (int) $deliveryPartner->total_reviews,
                    'commission_percentage' => (float) $deliveryPartner->commission_percentage,
                ],
                'documents' => [
                    'uploaded_documents' => $documentsArray,
                    'total_documents' => count($documentsArray),
                    'pending_documents' => $documents->where('status', 'pending')->count(),
                    'approved_documents' => $documents->where('status', 'approved')->count(),
                    'rejected_documents' => $documents->where('status', 'rejected')->count(),
                ],
                'payment_details' => $paymentDetail ? [
                    'id' => (string) $paymentDetail->id,
                    'bank_name' => (string) ($paymentDetail->bank_name ?? ''),
                    'account_holder_name' => (string) ($paymentDetail->account_holder_name ?? ''),
                    'account_number' => (string) ($paymentDetail->account_number ?? ''),
                    'ifsc_code' => (string) ($paymentDetail->ifsc_code ?? ''),
                ] : null,
                'has_payment_details' => (bool) $paymentDetail,
            ],
            'status_code' => 200,
        ];

        // Add rejection reason if applicable
        if ($status === 'rejected' && $rejectionReason) {
            $responseData['data']['rejection_reason'] = $rejectionReason;
        }

        return response()->json($responseData, 200);
    }

    /**
     * Delivery Partner Self-Registration (API)
     * This allows delivery partners to register themselves with basic info only
     * Vehicle and location details can be added later via updateVehicleAndLocation endpoint
     */
    public function register(Request $request)
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
        ];

        try {
            $validated = $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

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

            // Create delivery partner profile with placeholder values
            $deliveryPartner = DeliveryPartner::create([
                'user_id' => $user->id,
                'vehicle_type' => null,
                'vehicle_number' => null,
                'license_number' => null,
                'current_latitude' => null,
                'current_longitude' => null,
                'is_available' => false,
                'is_online' => false,
                'status' => 'pending',
                'total_deliveries' => 0,
                'total_earnings' => 0.00,
                'average_rating' => 0.00,
                'total_reviews' => 0,
                'commission_percentage' => 15.00,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registration successful! Please complete your profile by adding vehicle and location details.',
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
                        'next_step' => 'Add vehicle and location details using /delivery-partner/vehicle-location endpoint',
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

    /**
     * Add/Update Vehicle and Location Details (API)
     * This endpoint allows delivery partners to add vehicle and location info after registration
     */
    public function updateVehicleAndLocation(Request $request)
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

        $validator = Validator::make($request->all(), [
            'vehicle_type' => 'required|string|in:'.implode(',', array_column(VehicleTypeEnums::cases(), 'value')),
            'vehicle_number' => 'required|string|max:20',
            'license_number' => 'required|string|max:50',
            'current_longitude' => 'required|numeric|between:-180,180',
            'current_latitude' => 'required|numeric|between:-90,90',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        try {
            $deliveryPartner->update([
                'vehicle_type' => $validated['vehicle_type'],
                'vehicle_number' => $validated['vehicle_number'],
                'license_number' => $validated['license_number'],
                'current_latitude' => $validated['current_latitude'],
                'current_longitude' => $validated['current_longitude'],
            ]);

            // Calculate unified status (same as login)
            $documents = DeliveryPartnerDocument::where('partner_id', $deliveryPartner->id)->get();
            $status = $deliveryPartner->status;
            $rejectionReason = null;
            $nextStep = '';

            if ($deliveryPartner->status === 'pending') {
                if ($documents->count() === 0) {
                    $status = 'pending_documents';
                    $nextStep = 'Upload required documents using /delivery-partner/upload-documents endpoint';
                } else {
                    $rejectedDoc = $documents->firstWhere('status', 'rejected');
                    if ($rejectedDoc) {
                        $status = 'rejected';
                        $rejectionReason = $rejectedDoc->rejection_reason ?? 'Your documents were rejected. Please contact admin for details.';
                    } elseif ($documents->every(fn ($doc) => $doc->status === 'approved')) {
                        $status = 'pending_approval';
                        $nextStep = 'Your documents are approved. Awaiting final admin approval.';
                    } else {
                        $status = 'pending_approval';
                        $nextStep = 'Documents pending review by admin.';
                    }
                }
            } elseif ($deliveryPartner->status === 'rejected') {
                $status = 'rejected';
                $rejectionReason = $deliveryPartner->rejection_reason ?? 'Your application was rejected. Please contact admin for details.';
            }

            $responseData = [
                'success' => true,
                'message' => 'Vehicle and location details added successfully.',
                'data' => [
                    'delivery_partner' => [
                        'id' => $deliveryPartner->id,
                        'vehicle_type' => $deliveryPartner->vehicle_type,
                        'vehicle_number' => $deliveryPartner->vehicle_number,
                        'license_number' => $deliveryPartner->license_number,
                        'current_latitude' => $deliveryPartner->current_latitude,
                        'current_longitude' => $deliveryPartner->current_longitude,
                        'is_available' => $deliveryPartner->is_available,
                        'is_online' => $deliveryPartner->is_online,
                    ],
                    'status' => $status,
                    'next_step' => $nextStep,
                ],
            ];

            if ($rejectionReason) {
                $responseData['data']['rejection_reason'] = $rejectionReason;
            }

            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update vehicle and location details: '.$e->getMessage(),
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

        // Check if delivery partner is approved
        // if ($deliveryPartner->status !== 'approved') {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Your account has not been approved yet. You cannot update your profile until your documents are verified and approved by admin.',
        //         'status' => $deliveryPartner->status,
        //         'action' => 'pending_verification',
        //     ], 403);
        // }

        // 🔥 Manual Validation (NOT request->validate)
        $validator = Validator::make($request->all(), [

            'first_name' => 'sometimes|required|string|min:2|max:100',
            'last_name' => 'sometimes|required|string|min:2|max:100',
            // 'email' => [
            //     'sometimes',
            //     'required',
            //     'email',
            //     'max:255',
            //     Rule::unique('users', 'email')->ignore($user->id),
            // ],
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

    /**
     * Upload Delivery Partner Documents (API)
     * This endpoint allows authenticated delivery partners to upload their documents
     */
    public function uploadDocuments(Request $request)
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

        $allowedDocuments = [
            'id_proof',
            'driving_license',
            'rc',
            'address_proof',
            'bank_passbook',
        ];

        $rules = [];
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
                    'errors' => ['documents' => ['At least one document is required (id_proof, driving_license, rc, address_proof, or bank_passbook)']],
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
            $uploadedDocuments = [];

            // Handle document upload - Process each document type
            foreach ($allowedDocuments as $documentType) {
                if ($request->hasFile($documentType)) {
                    $file = $request->file($documentType);
                    $documentPath = $file->store('delivery_partner_documents', 'public');

                    $document = DeliveryPartnerDocument::create([
                        'partner_id' => $deliveryPartner->id,
                        'document_type' => $documentType,
                        'document_path' => $documentPath,
                        'document_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'status' => 'pending',
                        'uploaded_at' => now(),
                    ]);

                    $uploadedDocuments[] = [
                        'document_type' => $documentType,
                        'document_name' => $file->getClientOriginalName(),
                        'status' => 'pending',
                        'uploaded_at' => $document->uploaded_at,
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Documents uploaded successfully. They are pending admin review.',
                'data' => [
                    'delivery_partner_id' => $deliveryPartner->id,
                    'is_available' => $deliveryPartner->is_available,
                    'is_online' => $deliveryPartner->is_online,
                    'documents_uploaded' => $uploadedDocuments,
                    'documents_count' => count($uploadedDocuments),
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Document upload failed: '.$e->getMessage(),
                'errors' => ['general' => [$e->getMessage()]],
            ], 422);
        }
    }
}
