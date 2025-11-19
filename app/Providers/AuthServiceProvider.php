<?php

namespace App\Providers;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Override Laravel's default ResetPassword notification
        ResetPasswordBase::toMailUsing(function ($notifiable, $token) {
            return (new ResetPasswordNotification($token))->toMail($notifiable);
        });
    }
}
