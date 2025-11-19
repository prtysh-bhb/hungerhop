<?php

namespace App\Services;

use App\Actions\Restaurant\CreateRestaurantAction;
use App\DTOs\Restaurant\RestaurantData;
use App\Models\Restaurant;
use App\Models\Tenant;
use App\Models\User;
use App\Repositories\Contracts\RestaurantRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException as UnauthorizedException;

class RestaurantService
{
    public function __construct(
        protected CreateRestaurantAction $createRestaurantAction,
        protected RestaurantRepositoryInterface $restaurantRepository
    ) {}

    public function create(RestaurantData $data): Restaurant
    {
        $user = auth()->user();

        return DB::transaction(function () use ($data, $user) {

            // Handle tenant assignment based on user role
            $tenantId = $this->resolveTenantId($data, $user);

            // Update data with resolved tenant ID
            $data->tenant_id = $tenantId;

            // Business validation
            $this->validateRestaurantData($data);

            return $this->createRestaurantAction->execute($data);
        });
    }

    public function findById(int $id): ?Restaurant
    {
        return $this->restaurantRepository->findById($id);
    }

    public function approve(int $id): bool
    {
        $restaurant = $this->findById($id);
        if (! $restaurant) {
            throw new Exception('Restaurant not found');
        }

        return $restaurant->approve(auth()->user());
    }

    public function reject(int $id): bool
    {
        $restaurant = $this->findById($id);
        if (! $restaurant) {
            throw new Exception('Restaurant not found');
        }

        return $restaurant->reject(auth()->user());
    }

    private function validateRestaurantData(RestaurantData $data): void
    {
        // Check if restaurant name already exists for this tenant
        $exists = Restaurant::where('restaurant_name', $data->restaurant_name)
            ->where('tenant_id', $data->tenant_id)
            ->exists();

        if ($exists) {
            throw new Exception('Restaurant with this name already exists');
        }

        // Validate delivery radius
        if ($data->delivery_radius_km > 50) {
            throw new Exception('Delivery radius cannot exceed 50 km');
        }

        // Validate minimum order amount
        if ($data->minimum_order_amount < 0) {
            throw new Exception('Minimum order amount cannot be negative');
        }
    }

    private function resolveTenantId(RestaurantData $data, User $user): int
    {
        switch ($user->role) {
            case 'super_admin':
                return $this->handleSuperAdminTenant($data);

            case 'tenant_admin':
                return $this->handleTenantAdminTenant($user);

            default:
                throw new UnauthorizedException('User role cannot create restaurants');
        }
    }

    private function handleSuperAdminTenant(RestaurantData $data): int
    {
        // If tenant_id is provided, use existing tenant
        if ($data->tenant_id && $data->tenant_id > 0) {
            $tenant = Tenant::find($data->tenant_id);
            if (! $tenant) {
                throw new Exception('Selected tenant not found');
            }

            return $tenant->id;
        }

        // If no tenant selected, create new tenant for independent restaurant
        return $this->createNewTenant($data);
    }

    private function handleTenantAdminTenant(User $user): int
    {
        if (! $user->tenant_id) {
            throw new Exception('Tenant admin must be associated with a tenant');
        }

        return $user->tenant_id;
    }

    private function createNewTenant(RestaurantData $data): int
    {
        $tenant = Tenant::create([
            'tenant_name' => $data->restaurant_name.' Group',
            'contact_person' => 'Restaurant Owner',
            'email' => $data->email,
            'phone' => $data->phone,
            'subscription_plan' => 'basic',
            'status' => 'active',
            'monthly_base_fee' => 0,
            'per_restaurant_fee' => 0,
            'banner_limit' => 5,
            'subscription_start_date' => now(),
            'next_billing_date' => now()->addMonth(),
        ]);

        // Only log activity if spatie/laravel-activitylog is installed
        try {
            activity()
                ->causedBy(auth()->user())
                ->performedOn($tenant)
                ->log('New tenant created for independent restaurant');
        } catch (\Exception $e) {
            // Activity logging failed, but continue
        }

        return $tenant->id;
    }
}
