<?php

namespace App\DTOs\Auth;

use App\DTOs\BaseDTO;

class RegisterDTO extends BaseDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly ?string $phone = null,
        public readonly string $role = 'customer',
        public readonly string $status = 'pending_approval',
        public readonly ?int $tenant_id = null,
        public readonly ?int $restaurant_id = null
    ) {}

    /**
     * Create RegisterDTO from request data
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            email: $data['email'],
            password: $data['password'],
            first_name: $data['first_name'],
            last_name: $data['last_name'],
            phone: $data['phone'] ?? null,
            role: $data['role'] ?? 'customer',
            status: $data['status'] ?? 'pending_approval',
            tenant_id: $data['tenant_id'] ?? null,
            restaurant_id: $data['restaurant_id'] ?? null
        );
    }

    /**
     * Convert to array for user creation
     */
    public function toUserData(): array
    {
        return [
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $this->password,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'role' => $this->role,
            'status' => $this->status,
            'tenant_id' => $this->tenant_id,
            'restaurant_id' => $this->restaurant_id,
        ];
    }

    /**
     * Check if user should be auto-activated
     */
    public function shouldAutoActivate(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Get the active status for this user type
     */
    public function getActiveStatus(): string
    {
        return $this->shouldAutoActivate() ? 'active' : $this->status;
    }
}
