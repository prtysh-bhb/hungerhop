<?php

namespace App\DTOs;

use ReflectionClass;

abstract class BaseDTO
{
    /**
     * Create a new DTO instance from an array
     */
    public static function fromArray(array $data): static
    {
        $reflection = new ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();

        if (! $constructor) {
            return new static;
        }

        $parameters = $constructor->getParameters();
        $args = [];

        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            $value = $data[$name] ?? null;

            // Handle default values
            if ($value === null && $parameter->isDefaultValueAvailable()) {
                $value = $parameter->getDefaultValue();
            }

            $args[] = $value;
        }

        return new static(...$args);
    }

    /**
     * Convert the DTO to an array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * Convert the DTO to JSON
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Get only the non-null values as an array
     */
    public function toArrayFiltered(): array
    {
        return array_filter($this->toArray(), fn ($value) => $value !== null);
    }
}
