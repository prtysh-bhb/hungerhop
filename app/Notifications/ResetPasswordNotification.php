<?php

namespace App\Notifications;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', ['token' => $this->token], false).'?email='.urlencode($notifiable->getEmailForPasswordReset()));

        \Log::info('Password reset URL for '.$notifiable->getEmailForPasswordReset().': '.$url); // temporary debug log

        $minutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Reset Your Password')
            ->greeting('Hello '.($notifiable->name ?? ''))
            ->line('You requested a password reset. Click the button below to reset your password.')
            ->action('Reset Password', $url)
            ->line("This link will expire in {$minutes} minutes.")
            ->line('If you did not request this, ignore this email.');
    }
}
