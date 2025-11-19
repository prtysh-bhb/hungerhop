<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'tenant_name' => $this->faker->company,
            'contact_person' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'subscription_plan' => $this->faker->randomElement(['LITE', 'PLUS', 'PRO_MAX']),
            'total_restaurants' => 0,
            'monthly_base_fee' => $this->faker->randomFloat(2, 50, 200),
            'per_restaurant_fee' => $this->faker->randomFloat(2, 10, 50),
            'banner_limit' => $this->faker->numberBetween(1, 10),
            'status' => $this->faker->randomElement(['pending', 'approved']),
            'subscription_start_date' => now(),
            'next_billing_date' => now()->addMonth(),
            'approved_at' => now(),
            'approved_by' => null,
        ];
    }
}
