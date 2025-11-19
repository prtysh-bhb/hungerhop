<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Example: User Repository binding
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            fn (Application $app) => new \App\Repositories\UserRepository(
                $app->make(\App\Models\User::class)
            )
        );
    }

    public function boot(): void
    {
        //
    }
}
