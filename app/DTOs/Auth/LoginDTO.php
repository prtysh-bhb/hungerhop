<?php

namespace App\DTOs\Auth;

use App\DTOs\BaseDTO;

class LoginDTO extends BaseDTO
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
        public readonly bool $remember = false
    ) {}

    /**
     * Create LoginDTO from request data
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            username: $data['username'],
            password: $data['password'],
            remember: (bool) ($data['remember'] ?? false)
        );
    }

    /**
     * Get the field type (email or phone)
     */
    public function getFieldType(): string
    {
        return filter_var($this->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
    }

    /**
     * Get credentials for authentication
     */
    public function getCredentials(): array
    {
        return [
            $this->getFieldType() => $this->username,
            'password' => $this->password,
        ];
    }
}
