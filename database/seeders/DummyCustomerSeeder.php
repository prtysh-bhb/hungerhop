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
        // 1. Create Tenant
        $tenantId = DB::table('tenants')->insertGetId([
            'tenant_name' => 'Demo Tenant',
            'contact_person' => 'John Doe',
            'email' => 'tenant@example.com',
            'phone' => '1234567890',
            'subscription_plan' => 'LITE',
            'monthly_base_fee' => 100.00,
            'per_restaurant_fee' => 10.00,
            'banner_limit' => 3,
            'status' => 'approved',
            'subscription_start_date' => Carbon::now(),
            'next_billing_date' => Carbon::now()->addMonth(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Create User (Customer)
        $userId = DB::table('users')->insertGetId([
            'tenant_id' => $tenantId,
            'email' => 'customer@example.com',
            'phone' => '9876543210',
            'password' => Hash::make('password123'),
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'role' => 'customer',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Create Customer Profile
        $customerProfileId = DB::table('customer_profiles')->insertGetId([
            'user_id' => $userId,
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
        ]);

        // 4. Create Customer Address
        DB::table('customer_addresses')->insert([
            'customer_id' => $customerProfileId,
            'address_type' => 'home',
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
        ]);
    }
}
