<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'restaurant_id' => null,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->unique()->phoneNumber,
            'password' => bcrypt('password'),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'role' => $this->faker->randomElement([
                'super_admin', 'tenant_admin', 'location_admin',
                'restaurant_staff', 'customer', 'delivery_partner',
            ]),
            'status' => $this->faker->randomElement(['active', 'pending_approval']),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'last_login_at' => now(),
            'fcm_token' => Str::random(30),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
