<?php

namespace App\Repositories;

use App\Models\Restaurant;
use App\Repositories\Contracts\RestaurantRepositoryInterface;

class RestaurantRepository implements RestaurantRepositoryInterface
{
    public function create(array $data): Restaurant
    {
        return Restaurant::create($data);
    }

    public function findById(int $id): ?Restaurant
    {
        return Restaurant::find($id);
    }

    public function update(int $id, array $data): bool
    {
        return Restaurant::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Restaurant::where('id', $id)->delete();
    }

    public function getByTenant(int $tenantId)
    {
        return Restaurant::where('tenant_id', $tenantId)->get();
    }

    public function getPending()
    {
        return Restaurant::pending()->get();
    }

    public function getApproved()
    {
        return Restaurant::approved()->get();
    }
}
