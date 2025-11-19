<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function findByPhone(string $phone): ?User;

    public function findByEmailOrPhone(string $identifier): ?User;

    public function findActiveUsers(): Collection;

    public function findByRole(string $role): Collection;

    public function findByTenant(int $tenantId): Collection;

    public function findByRestaurant(int $restaurantId): Collection;
}
