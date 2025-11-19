<?php

namespace App\Services;

use App\DTOs\Auth\RegisterDTO;
use App\DTOs\Auth\UserDTO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Create a new user with hashed password
     */
    public function createUser(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        /** @var User $user */
        $user = $this->repository->create($data);

        return $user;
    }

    /**
     * Create a new user from RegisterDTO
     */
    public function createUserFromDTO(RegisterDTO $registerDTO): User
    {
        $userData = $registerDTO->toUserData();
        if (isset($userData['password'])) {
            $userData['password'] = Hash::make($userData['password']);
        }

        /** @var User $user */
        $user = $this->repository->create($userData);

        return $user;
    }

    /**
     * Get user as DTO
     */
    public function getUserAsDTO(int $userId): ?UserDTO
    {
        /** @var User|null $user */
        $user = $this->repository->find($userId);

        return $user ? UserDTO::fromUser($user) : null;
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        /** @var UserRepositoryInterface $repo */
        $repo = $this->repository;

        return $repo->findByEmail($email);
    }

    /**
     * Find user by phone
     */
    public function findByPhone(string $phone): ?User
    {
        /** @var UserRepositoryInterface $repo */
        $repo = $this->repository;

        return $repo->findByPhone($phone);
    }

    /**
     * Find user by email or phone
     */
    public function findByEmailOrPhone(string $identifier): ?User
    {
        /** @var UserRepositoryInterface $repo */
        $repo = $this->repository;

        return $repo->findByEmailOrPhone($identifier);
    }

    /**
     * Get all active users
     */
    public function getActiveUsers(): Collection
    {
        /** @var UserRepositoryInterface $repo */
        $repo = $this->repository;

        return $repo->findActiveUsers();
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $role): Collection
    {
        /** @var UserRepositoryInterface $repo */
        $repo = $this->repository;

        return $repo->findByRole($role);
    }

    /**
     * Get users by tenant
     */
    public function getUsersByTenant(int $tenantId): Collection
    {
        /** @var UserRepositoryInterface $repo */
        $repo = $this->repository;

        return $repo->findByTenant($tenantId);
    }

    /**
     * Get users by restaurant
     */
    public function getUsersByRestaurant(int $restaurantId): Collection
    {
        /** @var UserRepositoryInterface $repo */
        $repo = $this->repository;

        return $repo->findByRestaurant($restaurantId);
    }

    /**
     * Update user's last login timestamp
     */
    public function updateLastLogin(User $user): bool
    {
        return $this->repository->update($user, ['last_login_at' => now()]);
    }

    /**
     * Activate a user account
     */
    public function activateUser(User $user): bool
    {
        return $this->repository->update($user, ['status' => 'active']);
    }

    /**
     * Deactivate a user account
     */
    public function deactivateUser(User $user): bool
    {
        return $this->repository->update($user, ['status' => 'inactive']);
    }

    /**
     * Suspend a user account
     */
    public function suspendUser(User $user): bool
    {
        return $this->repository->update($user, ['status' => 'suspended']);
    }

    /**
     * Update user's FCM token for push notifications
     */
    public function updateFcmToken(User $user, string $token): bool
    {
        return $this->repository->update($user, ['fcm_token' => $token]);
    }

    /**
     * Verify user's phone number
     */
    public function verifyPhone(User $user): bool
    {
        return $this->repository->update($user, ['phone_verified_at' => now()]);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(User $user, string $role): bool
    {
        return $user->role === $role;
    }

    /**
     * Check if user is active
     */
    public function isActive(User $user): bool
    {
        return $user->status === 'active';
    }
}
