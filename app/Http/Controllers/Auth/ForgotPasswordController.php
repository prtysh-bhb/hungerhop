<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordRecoveryService;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    protected $service;

    public function __construct(PasswordRecoveryService $service)
    {
        $this->service = $service;
    }

    public function show()
    {
        return view('auth.forgot-password');
    }

    public function submit(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $res = $this->service->sendResetLink($request->email);

        if (! $res['success']) {
            return back()->withErrors(['email' => $res['message']]);
        }

        return back()->with('status', $res['message']);
    }
}
