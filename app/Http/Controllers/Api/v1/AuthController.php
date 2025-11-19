<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ApiLoginRequest;
use App\Http\Requests\Auth\ApiRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Register a new customer
     */
    public function register(ApiRegisterRequest $request): JsonResponse
    {
        try {
            // Create new customer user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'customer',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'message' => 'Customer registered successfully',
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
                        'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toDateTimeString() : null,
                        'created_at' => $user->created_at ? $user->created_at->toDateTimeString() : null,
                    ],
                    'token' => [
                        'access_token' => $token,
                        'token_type' => 'bearer',
                        'expires_in' => 3600, // Fixed 1 hour
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Customer registration failed: '.$e->getMessage());
            \Log::error('Stack trace: '.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => 'Something went wrong during registration. Please try again.',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Customer login
     */
    public function login(ApiLoginRequest $request): JsonResponse
    {
        try {
            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
            ];

            // Find user by email
            $user = User::where('email', $request->email)->first();

            // Check if user exists
            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'error' => 'No account found with this email address.',
                ], 401);
            }

            // Check if user is a customer
            if ($user->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied',
                    'error' => 'This login is only for customers.',
                ], 403);
            }

            // Check if user is active
            if ($user->status !== 'active') {
                $statusMessage = match ($user->status) {
                    'inactive' => 'Your account is inactive. Please contact support.',
                    'suspended' => 'Your account has been suspended. Please contact support.',
                    'pending_approval' => 'Your account is pending approval.',
                    default => 'Your account is not accessible at this time.'
                };

                return response()->json([
                    'success' => false,
                    'message' => 'Account not accessible',
                    'error' => $statusMessage,
                ], 403);
            }

            // Attempt to create token
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'error' => 'Incorrect email or password.',
                ], 401);
            }

            // Update last login
            $user->update(['last_login_at' => now()]);
            $user->refresh(); // Refresh to get updated data

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
                        'status' => $user->status,
                        'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toDateTimeString() : null,
                        'last_login_at' => $user->last_login_at ? $user->last_login_at->toDateTimeString() : null,
                    ],
                    'token' => [
                        'access_token' => $token,
                        'token_type' => 'bearer',
                        'expires_in' => 36000, // Fixed 10 hours
                    ],
                ],
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Customer login failed: '.$e->getMessage());
            \Log::error('Stack trace: '.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => 'Something went wrong during login. Please try again.',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get authenticated customer
     */
    public function me(): JsonResponse
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                    'error' => 'No authenticated user found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'User profile retrieved successfully',
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
                        'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->toDateTimeString() : null,
                        'last_login_at' => $user->last_login_at ? $user->last_login_at->toDateTimeString() : null,
                        'created_at' => $user->created_at ? $user->created_at->toDateTimeString() : null,
                        'updated_at' => $user->updated_at ? $user->updated_at->toDateTimeString() : null,
                    ],
                ],
            ], 200);

        } catch (TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token expired',
                'error' => 'Your session has expired. Please login again.',
            ], 401);

        } catch (TokenInvalidException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token invalid',
                'error' => 'Invalid token provided.',
            ], 401);

        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token error',
                'error' => 'Token not provided or malformed.',
            ], 401);

        } catch (\Exception $e) {
            \Log::error('Get user profile failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user',
                'error' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }

    /**
     * Customer logout
     */
    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out',
                'data' => null,
            ], 200);

        } catch (JWTException $e) {
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
     * Refresh JWT token
     */
    public function refresh(): JsonResponse
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'token' => [
                        'access_token' => $newToken,
                        'token_type' => 'bearer',
                        'expires_in' => 36000, // Fixed 10 hours
                    ],
                ],
            ], 200);

        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed',
                'error' => 'Could not refresh token.',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed',
                'error' => 'Something went wrong during token refresh.',
            ], 500);
        }
    }
}
