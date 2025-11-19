<?php

namespace App\Services;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordRecoveryService
{
    protected $broker;

    public function __construct()
    {
        $this->broker = Password::broker('users'); // uses default password broker / password_resets table
    }

    /**
     * Send reset link to user's email (uses Password broker and Notification)
     */
    public function sendResetLink(string $email): array
    {
        $response = $this->broker->sendResetLink(['email' => $email]);

        if ($response === Password::RESET_LINK_SENT) {
            return ['success' => true, 'message' => 'Password reset link sent to your email.'];
        }

        // map failures
        return ['success' => false, 'message' => trans($response)];
    }

    /**
     * Reset password using broker which validates token and expiration
     *
     * @param  array  $credentials  ['email','password','password_confirmation','token']
     */
    public function resetPassword(array $credentials): array
    {
        $response = $this->broker->reset(
            $credentials,
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        if ($response === Password::PASSWORD_RESET) {
            return ['success' => true, 'message' => 'Password reset successful.'];
        }

        if ($response === Password::INVALID_TOKEN) {
            return ['success' => false, 'message' => 'This reset link has expired or is invalid. Please request a new one.'];
        }

        return ['success' => false, 'message' => trans($response)];
    }
}
