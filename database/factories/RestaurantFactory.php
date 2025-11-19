<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Restaurant>
 */
class RestaurantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Restaurant::class;

    public function definition(): array
    {
        $name = $this->faker->company;

        return [
            'tenant_id' => Tenant::factory(),
            'location_admin_id' => User::factory(),
            'restaurant_name' => $name,
            'slug' => Str::slug($name).'-'.rand(1000, 9999),
            'description' => $this->faker->paragraph,
            'cuisine_type' => $this->faker->word,
            'address' => $this->faker->address,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'postal_code' => $this->faker->postcode,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->companyEmail,
            'image_url' => $this->faker->imageUrl(640, 480, 'restaurant'),
            'cover_image_url' => $this->faker->imageUrl(1280, 480, 'food'),
            'delivery_radius_km' => rand(1, 15),
            'minimum_order_amount' => $this->faker->randomFloat(2, 100, 500),
            'base_delivery_fee' => $this->faker->randomFloat(2, 10, 50),
            'restaurant_commission_percentage' => $this->faker->randomFloat(2, 50, 90),
            'estimated_delivery_time' => rand(20, 60),
            'tax_percentage' => $this->faker->randomFloat(2, 5, 18),
            'is_open' => true,
            'accepts_orders' => true,
            'status' => $this->faker->randomElement(['pending', 'approved']),
            'average_rating' => $this->faker->randomFloat(2, 3, 5),
            'total_reviews' => rand(0, 200),
            'total_orders' => rand(0, 1000),
            'approved_at' => now(),
            'approved_by' => null,
        ];
    }
}
