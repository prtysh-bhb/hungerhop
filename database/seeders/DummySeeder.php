<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DummySeeder extends Seeder
{
    /**
     * Run dummy / demo data seeders.
     */
    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('  HungerHop DUMMY Data Seeder');
        $this->command->info('========================================');
        $this->command->newLine();

        $this->call([
            UserSeeder::class,
            TenantSeeder::class,
            RestaurantSeeder::class,
            CustomerSeeder::class,
            DummyCustomerSeeder::class,
            DeliveryPartnerSeeder::class,
            MenuSeeder::class,
            // TestMenuCategorySeeder::class,
            OrderSeeder::class,
            PaymentSeeder::class,
            ReviewSeeder::class,
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
