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

            // 2. Super Admin User
            UserSeeder::class,

            // 3. Tenants with Tenant Admins and Subscriptions
            TenantSeeder::class,

            // 4. Restaurants with Location Admins
            RestaurantSeeder::class,

            // 5. Customers with Profiles and Addresses
            CustomerSeeder::class,

            // 6. Delivery Partners with Documents
            DeliveryPartnerSeeder::class,

            // 7. Menu Categories and Items
            MenuSeeder::class,

            // 8. Orders with Items and Delivery Assignments
            OrderSeeder::class,

            // 9. Reviews for Restaurants and Menu Items
            ReviewSeeder::class,

            // 10. Promotions
            PromotionSeeder::class,

            // 11. Payments
            PaymentSeeder::class,

            // 12. Banners
            BannerSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('========================================');
        $this->command->info('  Seeding Complete!');
        $this->command->info('========================================');
        $this->command->newLine();
        $this->command->info('All users password: Admin@123');
        $this->command->info('------------------');
        $this->command->info('Super Admin: superadmin@hungerhop.com');
        $this->command->info('Tenant Admin (LITE): tenant.lite@hungerhop.com');
        $this->command->info('Tenant Admin (PLUS): tenant.plus@hungerhop.com');
        $this->command->info('Tenant Admin (PRO_MAX): tenant.promax@hungerhop.com');
        $this->command->info('Location Admins: loc.*@hungerhop.com');
        $this->command->info('Customers: customer1-5@hungerhop.com');
        $this->command->info('Delivery Partners: delivery1-5@hungerhop.com');
        $this->command->newLine();
    }
}
