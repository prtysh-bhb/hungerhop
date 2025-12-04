<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DeliveryPartnerPasswordController extends Controller
{
    /**
     * Send OTP to delivery partner's email for password reset
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Find user with delivery_partner role
        $user = User::where('email', $request->email)
            ->where('role', 'delivery_partner')
            ->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'No delivery partner account found with this email address.',
            ], 404);
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(15);

        // Store OTP in password_reset_tokens table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($otp),
                'created_at' => now(),
            ]
        );

        // Send OTP via email (you can also send via SMS)
        try {
            Mail::raw("Your HungerHop password reset OTP is: {$otp}\n\nThis OTP is valid for 15 minutes.\n\nIf you did not request this, please ignore this email.", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('HungerHop - Password Reset OTP');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email: '.$e->getMessage());
            // Continue anyway - in production you might want to handle this differently
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset OTP has been sent to your email.',
            'data' => [
                'email' => $request->email,
                'otp_expires_in' => '15 minutes',
            ],
        ], 200);
    }

    /**
     * Verify OTP sent to delivery partner
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        // Find user with delivery_partner role
        $user = User::where('email', $request->email)
            ->where('role', 'delivery_partner')
            ->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'No delivery partner account found with this email address.',
            ], 404);
        }

        // Get the stored OTP
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $resetRecord) {
            return response()->json([
                'success' => false,
                'message' => 'No password reset request found. Please request a new OTP.',
            ], 404);
        }

        // Check if OTP is expired (15 minutes)
        $createdAt = \Carbon\Carbon::parse($resetRecord->created_at);
        if ($createdAt->diffInMinutes(now()) > 15) {
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired. Please request a new one.',
            ], 422);
        }

        // Verify OTP
        if (! Hash::check($request->otp, $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP. Please check and try again.',
            ], 422);
        }

        // Generate a temporary reset token for the next step
        $resetToken = Str::random(64);

        // Update the record with the reset token
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->update([
                'token' => Hash::make($resetToken),
                'created_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully. You can now reset your password.',
            'data' => [
                'email' => $request->email,
                'reset_token' => $resetToken,
            ],
        ], 200);
    }

    /**
     * Reset password using the reset token
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'reset_token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Find user with delivery_partner role
        $user = User::where('email', $request->email)
            ->where('role', 'delivery_partner')
            ->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'No delivery partner account found with this email address.',
            ], 404);
        }

        // Get the stored reset token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $resetRecord) {
            return response()->json([
                'success' => false,
                'message' => 'No password reset request found. Please start the process again.',
            ], 404);
        }

        // Check if token is expired (15 minutes from OTP verification)
        $createdAt = \Carbon\Carbon::parse($resetRecord->created_at);
        if ($createdAt->diffInMinutes(now()) > 15) {
            return response()->json([
                'success' => false,
                'message' => 'Reset token has expired. Please start the process again.',
            ], 422);
        }

        // Verify reset token
        if (! Hash::check($request->reset_token, $resetRecord->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset token. Please start the process again.',
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the reset token
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        Log::info("Delivery partner password reset successful for user: {$user->id}");

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully. You can now login with your new password.',
        ], 200);
    }

    /**
     * Change password for authenticated delivery partner
     * Requires current password verification
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (! $user || $user->role !== 'delivery_partner') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only delivery partners can use this endpoint.',
            ], 403);
        }

        // Verify current password
        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        // Check if new password is same as current
        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'New password cannot be the same as current password.',
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        Log::info("Delivery partner password changed for user: {$user->id}");

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ], 200);
    }
}
