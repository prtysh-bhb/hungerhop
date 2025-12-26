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
        $request->validate(
            [
                'email' => [
                    'required',
                    'string',
                    'email:rfc,dns',
                    'regex:/^(?!\.)[A-Za-z0-9._%+-]+(?<!\.)@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/',
                    'exists:users,email',
                ],
            ],
            [
                'email.required' => 'Email address is required.',
                'email.string' => 'Email must be a valid string.',
                'email.email' => 'Enter a valid email address.',
                'email.regex' => 'Email must not contain spaces, invalid characters, or extra dots.',
                'email.exists' => 'This email is not registered with us.',
            ]
        );

        $res = $this->service->sendResetLink($request->email);

        if (! $res['success']) {
            return back()->withErrors(['email' => $res['message']]);
        }

        return back()->with('status', $res['message']);
    }
}
