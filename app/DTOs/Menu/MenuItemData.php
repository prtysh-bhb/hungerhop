<?php

namespace App\DTOs\Menu;

class MenuItemData
{
    public function __construct(
        public readonly int $menu_category_id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly float $price,
        public readonly ?string $image_url,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            menu_category_id: $data['menu_category_id'],
            name: $data['name'],
            description: $data['description'] ?? null,
            price: (float) $data['price'],
            image_url: $data['image_url'] ?? null
        );
    }
}
