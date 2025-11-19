<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordRecoveryService;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    protected $service;

    public function __construct(PasswordRecoveryService $service)
    {
        $this->service = $service;
    }

    public function show(Request $request, $token)
    {
        $email = $request->query('email');

        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $res = $this->service->resetPassword($request->only('email', 'password', 'password_confirmation', 'token'));

        if (! $res['success']) {
            return back()->withErrors(['error' => $res['message']]);
        }

        return redirect()->route('login')->with('status', $res['message']);
    }
}
