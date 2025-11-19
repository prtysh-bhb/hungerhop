<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ActionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind action classes if needed
        $this->app->singleton(
            \App\Actions\User\CreateUserAction::class
        );
    }

    public function boot(): void
    {
        // Bootstrapping actions if needed
    }
}
