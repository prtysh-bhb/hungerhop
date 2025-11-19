<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordRecoveryService;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    protected $service;

    public function __construct(PasswordRecoveryService $service)
    {
        $this->service = $service;
    }

    /**
     * Send reset link email
     */
    public function forgot(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $res = $this->service->sendResetLink($request->email);

        if (! $res['success']) {
            return response()->json([
                'message' => $res['message'],
            ], 422);
        }

        return response()->json([
            'message' => 'Password reset link sent to email.',
        ], 200);
    }

    /**
     * Reset the password using token
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $res = $this->service->resetPassword($request->only('email', 'password', 'password_confirmation', 'token'));

        if (! $res['success']) {
            return response()->json([
                'message' => $res['message'],
            ], 422);
        }

        return response()->json([
            'message' => 'Password reset successful.',
        ], 200);
    }
}
