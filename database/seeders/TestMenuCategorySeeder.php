<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestMenuCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first user to use their tenant_id
        $user = User::first();
        if (! $user) {
            $this->command->info('No users found. Please create a user first.');

            return;
        }

        $categories = [
            [
                'tenant_id' => $user->tenant_id,
                'restaurant_id' => $user->restaurant_id,
                'category_name' => 'Appetizers',
                'description' => 'Start your meal with our delicious appetizers',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'tenant_id' => $user->tenant_id,
                'restaurant_id' => $user->restaurant_id,
                'category_name' => 'Main Courses',
                'description' => 'Our signature main dishes',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'tenant_id' => $user->tenant_id,
                'restaurant_id' => $user->restaurant_id,
                'category_name' => 'Desserts',
                'description' => 'Sweet endings to your meal',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'tenant_id' => $user->tenant_id,
                'restaurant_id' => $user->restaurant_id,
                'category_name' => 'Beverages',
                'description' => 'Refreshing drinks and beverages',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            MenuCategory::updateOrCreate(
                ['category_name' => $category['category_name'], 'tenant_id' => $category['tenant_id']],
                $category
            );
        }

        $this->command->info('Menu categories created successfully!');
    }
}
