<?php

namespace App\DTOs\Menu;

class MenuVariationData
{
    public function __construct(
        public readonly int $menu_item_id,
        public readonly string $label,
        public readonly float $price_delta,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            menu_item_id: $data['menu_item_id'],
            label: $data['label'],
            price_delta: (float) $data['price_delta'],
        );
    }
}
