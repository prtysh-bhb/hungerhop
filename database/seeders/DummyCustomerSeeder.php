<?php

// database/seeders/DummyCustomerSeeder.php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyCustomerSeeder extends Seeder
{
    public function run()
    {
        // Use updateOrCreate to avoid duplicate errors and ensure idempotency
        $tenantEmail = 'tenant.demo.'.uniqid().'@example.com';
        $tenantPhone = '12345'.rand(10000, 99999);
        $tenant = DB::table('tenants')->updateOrInsert(
            [
                'email' => $tenantEmail,
            ],
            [
                'tenant_name' => 'Demo Tenant',
                'contact_person' => 'John Doe',
                'phone' => $tenantPhone,
                'subscription_plan' => 'LITE',
                'monthly_base_fee' => 100.00,
                'per_restaurant_fee' => 10.00,
                'banner_limit' => 3,
                'status' => 'approved',
                'subscription_start_date' => Carbon::now(),
                'next_billing_date' => Carbon::now()->addMonth(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        // Get tenant id
        $tenantId = DB::table('tenants')->where('email', $tenantEmail)->value('id');

        // 2. Create User (Customer)
        $userEmail = 'customer.demo.'.uniqid().'@example.com';
        $userPhone = '98765'.rand(10000, 99999);
        $user = DB::table('users')->updateOrInsert(
            [
                'email' => $userEmail,
            ],
            [
                'tenant_id' => $tenantId,
                'phone' => $userPhone,
                'password' => Hash::make('password123'),
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'role' => 'customer',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $userId = DB::table('users')->where('email', $userEmail)->value('id');

        // 3. Create Customer Profile (updateOrInsert for idempotency)
        $customerProfile = DB::table('customer_profiles')->updateOrInsert(
            [
                'user_id' => $userId,
            ],
            [
                'date_of_birth' => '1990-01-01',
                'gender' => 'female',
                'profile_image_url' => null,
                'total_orders' => 0,
                'total_spent' => 0.00,
                'loyalty_points' => 0,
                'referral_code' => Str::random(8),
                'referred_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $customerProfileId = DB::table('customer_profiles')->where('user_id', $userId)->value('id');

        // 4. Create Customer Address (updateOrInsert for idempotency)
        DB::table('customer_addresses')->updateOrInsert(
            [
                'customer_id' => $customerProfileId,
                'address_type' => 'home',
            ],
            [
                'address_line1' => '123 Main Street',
                'address_line2' => 'Apt 4B',
                'landmark' => 'Near City Park',
                'city' => 'Metropolis',
                'state' => 'StateName',
                'postal_code' => '123456',
                'latitude' => 28.613939,
                'longitude' => 77.209023,
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        $this->command->info('✓ Dummy customer seeded: '.$userEmail.' / password123');
    }
}
