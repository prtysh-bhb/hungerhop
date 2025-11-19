<?php

namespace App\DTOs\Auth;

use App\DTOs\BaseDTO;

class UserDTO extends BaseDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $role,
        public readonly string $status,
        public readonly ?string $phone = null,
        public readonly ?int $tenant_id = null,
        public readonly ?int $restaurant_id = null,
        public readonly ?string $fcm_token = null,
        public readonly ?string $phone_verified_at = null,
        public readonly ?string $last_login_at = null
    ) {}

    /**
     * Create UserDTO from User model
     */
    public static function fromUser(\App\Models\User $user): self
    {
        return new self(
            name: $user->name,
            email: $user->email,
            first_name: $user->first_name,
            last_name: $user->last_name,
            role: $user->role,
            status: $user->status,
            phone: $user->phone,
            tenant_id: $user->tenant_id,
            restaurant_id: $user->restaurant_id,
            fcm_token: $user->fcm_token,
            phone_verified_at: $user->phone_verified_at,
            last_login_at: $user->last_login_at
        );
    }

    /**
     * Get full name
     */
    public function getFullName(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    /**
     * Get display name
     */
    public function getDisplayName(): string
    {
        return $this->name ?: $this->getFullName();
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user has specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Get formatted role name
     */
    public function getFormattedRole(): string
    {
        return ucfirst(str_replace('_', ' ', $this->role));
    }

    /**
     * Check if phone is verified
     */
    public function isPhoneVerified(): bool
    {
        return ! is_null($this->phone_verified_at);
    }
}
