<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds all data in the correct order respecting foreign key relationships.
     */
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('  HungerHop Database Seeder');
        $this->command->info('========================================');
        $this->command->newLine();

        $this->call([
            // 1. Geographic data (required for restaurants)
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,

            // DeliveryZoneSeeder::class,

            // // 2. Super Admin User
            // UserSeeder::class,

            // // 3. Tenants with Tenant Admins and Subscriptions
            // TenantSeeder::class,

            // // 4. Restaurants with Location Admins
            // RestaurantSeeder::class,

            // // 5. Customers with Profiles and Addresses
            // CustomerSeeder::class,

            // // 6. Delivery Partners with Documents
            // DeliveryPartnerSeeder::class,

            // // 7. Menu Categories and Items
            // MenuSeeder::class,

            // // 8. Orders with Items and Delivery Assignments
            // OrderSeeder::class,

            // // 9. Reviews for Restaurants and Menu Items
            // ReviewSeeder::class,

            // // 10. Promotions
            // PromotionSeeder::class,

            // // 11. Payments
            // PaymentSeeder::class,

            // // 12. Banners
            // BannerSeeder::class,
        ]);

    }
}
