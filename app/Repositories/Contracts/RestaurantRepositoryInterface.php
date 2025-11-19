<?php

namespace App\Repositories\Contracts;

use App\Models\Restaurant;

interface RestaurantRepositoryInterface
{
    public function create(array $data): Restaurant;

    public function findById(int $id): ?Restaurant;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function getByTenant(int $tenantId);

    public function getPending();

    public function getApproved();
}
