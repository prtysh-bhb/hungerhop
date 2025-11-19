<?php

namespace App\DTOs\Restaurant;

use Illuminate\Http\UploadedFile;

class RestaurantData
{
    public function __construct(
        public ?int $tenant_id, // Changed to nullable
        public string $restaurant_name,
        public string $phone,
        public string $email,
        public string $postal_code,
        public int $location_admin_id,
        public int $state_id,
        public int $city_id,
        public string $address,
        public float $latitude,
        public float $longitude,
        public float $minimum_order_amount,
        public float $base_delivery_fee,
        public float $delivery_radius_km,
        public int $estimated_delivery_time,
        public float $tax_percentage,
        public float $restaurant_commission_percentage,
        public bool $is_open,
        public ?UploadedFile $image_url = null,
        public ?UploadedFile $cover_image_url = null,
        public ?string $description = null,
        public ?string $cuisine_type = null,
        public ?string $website_url = null,
    ) {}

    public static function fromArray(array $data): self
    {
        $user = auth()->user();

        // Handle tenant_id based on role and selection
        $tenantId = null;

        if ($user->role === 'super_admin') {
            if (isset($data['tenant_selection']) && $data['tenant_selection'] === 'existing') {
                $tenantId = isset($data['tenant_id']) ? (int) $data['tenant_id'] : null;
            }
            // For 'new' selection, tenantId remains null (will be created in service)
        } elseif ($user->role === 'tenant_admin') {
            $tenantId = $user->tenant_id ? (int) $user->tenant_id : null;
        }

        // Set default tenant_id if still null
        if ($tenantId === null) {
            $tenantId = 1; // Default tenant or handle as needed
        }

        return new self(
            tenant_id: $tenantId,
            restaurant_name: $data['restaurant_name'],
            phone: $data['phone'],
            email: $data['email'],
            postal_code: $data['postal_code'],
            location_admin_id: (int) $data['location_admin_id'],
            state_id: (int) $data['state_id'],
            city_id: (int) $data['city_id'],
            address: $data['address'],
            latitude: (float) $data['latitude'],
            longitude: (float) $data['longitude'],
            minimum_order_amount: (float) $data['minimum_order_amount'],
            base_delivery_fee: (float) $data['base_delivery_fee'],
            delivery_radius_km: (float) $data['delivery_radius_km'],
            estimated_delivery_time: (int) $data['estimated_delivery_time'],
            tax_percentage: (float) $data['tax_percentage'],
            restaurant_commission_percentage: (float) $data['restaurant_commission_percentage'],
            is_open: (bool) ($data['is_open'] ?? false),
            image_url: $data['image_url'] ?? null,
            cover_image_url: $data['cover_image_url'] ?? null,
            description: $data['description'] ?? null,
            cuisine_type: $data['cuisine_type'] ?? null,
            website_url: $data['website_url'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'tenant_id' => $this->tenant_id,
            'restaurant_name' => $this->restaurant_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'postal_code' => $this->postal_code,
            'location_admin_id' => $this->location_admin_id,
            'state' => $this->state_id,
            'city' => $this->city_id,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'minimum_order_amount' => $this->minimum_order_amount,
            'base_delivery_fee' => $this->base_delivery_fee,
            'delivery_radius_km' => $this->delivery_radius_km,
            'estimated_delivery_time' => $this->estimated_delivery_time,
            'tax_percentage' => $this->tax_percentage,
            'restaurant_commission_percentage' => $this->restaurant_commission_percentage,
            'is_open' => $this->is_open,
            'description' => $this->description,
            'cuisine_type' => $this->cuisine_type,
            'website_url' => $this->website_url,
        ];
    }
}
