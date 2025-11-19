<?php

namespace App\DTOs\Menu;

class MenuTemplateData
{
    public function __construct(
        public readonly string $template_name,
        public readonly ?string $description,
        public readonly int $tenant_id,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            template_name: $data['template_name'],
            description: $data['description'] ?? null,
            tenant_id: $data['tenant_id']
        );
    }
}
