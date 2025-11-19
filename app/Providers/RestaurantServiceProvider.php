<?php

namespace App\Providers;

use App\Repositories\Contracts\RestaurantRepositoryInterface;
use App\Repositories\RestaurantRepository;
use Illuminate\Support\ServiceProvider;

class RestaurantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RestaurantRepositoryInterface::class, RestaurantRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
