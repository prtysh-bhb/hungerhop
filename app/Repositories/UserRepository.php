<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByPhone(string $phone): ?User
    {
        return $this->model->where('phone', $phone)->first();
    }

    public function findByEmailOrPhone(string $identifier): ?User
    {
        return $this->model->where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();
    }

    public function findActiveUsers(): Collection
    {
        return $this->model->where('status', 'active')->get();
    }

    public function findByRole(string $role): Collection
    {
        return $this->model->where('role', $role)->get();
    }

    public function findByTenant(int $tenantId): Collection
    {
        return $this->model->where('tenant_id', $tenantId)->get();
    }

    public function findByRestaurant(int $restaurantId): Collection
    {
        return $this->model->where('restaurant_id', $restaurantId)->get();
    }
}
