<?php

namespace App\Services;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseService
{
    public function __construct(
        protected BaseRepositoryInterface $repository
    ) {}

    public function getAllItems(): Collection
    {
        return $this->repository->all();
    }

    public function getPaginatedItems(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function findItem(int $id): ?Model
    {
        return $this->repository->find($id);
    }

    public function findItemOrFail(int $id): Model
    {
        return $this->repository->findOrFail($id);
    }

    public function createItem(array $data): Model
    {
        return $this->repository->create($data);
    }

    public function updateItem(Model $model, array $data): bool
    {
        return $this->repository->update($model, $data);
    }

    public function deleteItem(Model $model): bool
    {
        return $this->repository->delete($model);
    }
}
