<?php

namespace App\Actions\User;

use App\Actions\BaseAction;
use App\DTOs\Auth\RegisterDTO;
use App\Models\User;
use App\Services\UserService;

class CreateUserAction extends BaseAction
{
    public function __construct(
        private UserService $userService
    ) {}

    public function execute(array $data = []): User
    {
        // Create DTO from array data
        $registerDTO = RegisterDTO::fromRequest($data);

        // Convert DTO to user data and create user
        return $this->userService->createUser($registerDTO->toUserData());
    }

    /**
     * Execute with DTO directly
     */
    public function executeWithDTO(RegisterDTO $registerDTO): User
    {
        return $this->userService->createUser($registerDTO->toUserData());
    }
}
