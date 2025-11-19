<?php

namespace App\Providers;

use App\Repositories\Contracts\RestaurantRepositoryInterface;
use App\Repositories\RestaurantRepository;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

// use app\Models\Restaurant;
// use App\Models\MenuItem;
// use App\Models\Review;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind UserService
        $this->app->bind(
            RestaurantRepositoryInterface::class,
            RestaurantRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register View Composer for Right Sidebar
        view()->composer('partials.right-sidebar', \App\Http\ViewComposers\RightSidebarComposer::class);

        // Define authorization gates
        Gate::define('access-restaurant-admin', function ($user) {
            return in_array($user->role, ['super_admin', 'tenant_admin']);
        });

        Gate::define('access-tenant-management', function ($user) {
            return in_array($user->role, ['super_admin', 'tenant_admin']);
        });

        Gate::define('access-menu-management', function ($user) {
            return in_array($user->role, ['tenant_admin', 'location_admin', 'restaurant_staff']);
        });

        Gate::define('access-online-store', function ($user) {
            return in_array($user->role, ['tenant_admin', 'location_admin', 'restaurant_staff']);
        });

        Gate::define('access-customers', function ($user) {
            return in_array($user->role, ['super_admin', 'tenant_admin']);
        });

        Gate::define('access-analysis', function ($user) {
            return in_array($user->role, ['super_admin', 'tenant_admin']);
        });

        Gate::define('access-orders', function ($user) {
            return in_array($user->role, ['super_admin', 'tenant_admin', 'location_admin', 'restaurant_staff']);
        });

        Relation::enforceMorphMap([
            'restaurant' => \App\Models\Restaurant::class,
            'menu_item' => \App\Models\MenuItem::class,
            'review' => \App\Models\Review::class,

            // add others here...
        ]);
        Schema::defaultStringLength(191);
    }
}
