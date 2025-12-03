<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\Models\SubscriptionPayment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates tenants with different subscription plans and their admin users
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Tenant 1: LITE Plan - Active
        $tenant1 = Tenant::updateOrCreate(
            ['email' => 'tenant.lite@hungerhop.com'],
            [
                'tenant_name' => 'Foodie Express',
                'contact_person' => 'Rahul Sharma',
                'phone' => '9876543001',
                'subscription_plan' => Tenant::PLAN_LITE,
                'total_restaurants' => 2,
                'monthly_base_fee' => 1200.00,
                'per_restaurant_fee' => 500.00,
                'banner_limit' => 1,
                'status' => Tenant::STATUS_APPROVED,
                'subscription_start_date' => $now->copy()->subDays(15)->toDateString(),
                'next_billing_date' => $now->copy()->addDays(15)->toDateString(),
                'approved_at' => $now->copy()->subDays(15),
            ]
        );

        // Create Tenant Admin for Tenant 1
        User::updateOrCreate(
            ['email' => 'tenant.lite@hungerhop.com'],
            [
                'first_name' => 'Rahul',
                'last_name' => 'Sharma',
                'phone' => '9876543001',
                'tenant_id' => $tenant1->id,
                'role' => 'tenant_admin',
                'status' => 'active',
                'password' => Hash::make('Admin@123'),
                'email_verified_at' => now(),
            ]
        );

        // Create Subscription Payment for Tenant 1
        SubscriptionPayment::updateOrCreate(
            ['tenant_id' => $tenant1->id, 'billing_period_start' => $now->copy()->subDays(15)->toDateString()],
            [
                'subscription_plan' => Tenant::PLAN_LITE,
                'restaurant_count' => 2,
                'base_amount' => 1200.00,
                'per_restaurant_amount' => 500.00,
                'total_amount' => 2200.00,
                'billing_period_end' => $now->copy()->addDays(15)->toDateString(),
                'payment_method' => 'card',
                'payment_gateway' => 'stripe',
                'gateway_transaction_id' => 'txn_lite_' . uniqid(),
                'status' => SubscriptionPayment::STATUS_COMPLETED,
                'due_date' => $now->copy()->addDays(15)->toDateString(),
                'paid_at' => $now->copy()->subDays(15),
            ]
        );

        // Tenant 2: PLUS Plan - Active
        $tenant2 = Tenant::updateOrCreate(
            ['email' => 'tenant.plus@hungerhop.com'],
            [
                'tenant_name' => 'Tasty Bites Network',
                'contact_person' => 'Priya Patel',
                'phone' => '9876543002',
                'subscription_plan' => Tenant::PLAN_PLUS,
                'total_restaurants' => 5,
                'monthly_base_fee' => 2000.00,
                'per_restaurant_fee' => 1000.00,
                'banner_limit' => 3,
                'status' => Tenant::STATUS_APPROVED,
                'subscription_start_date' => $now->copy()->subDays(10)->toDateString(),
                'next_billing_date' => $now->copy()->addDays(20)->toDateString(),
                'approved_at' => $now->copy()->subDays(10),
            ]
        );

        // Create Tenant Admin for Tenant 2
        User::updateOrCreate(
            ['email' => 'tenant.plus@hungerhop.com'],
            [
                'first_name' => 'Priya',
                'last_name' => 'Patel',
                'phone' => '9876543002',
                'tenant_id' => $tenant2->id,
                'role' => 'tenant_admin',
                'status' => 'active',
                'password' => Hash::make('Admin@123'),
                'email_verified_at' => now(),
            ]
        );

        // Create Subscription Payment for Tenant 2
        SubscriptionPayment::updateOrCreate(
            ['tenant_id' => $tenant2->id, 'billing_period_start' => $now->copy()->subDays(10)->toDateString()],
            [
                'subscription_plan' => Tenant::PLAN_PLUS,
                'restaurant_count' => 5,
                'base_amount' => 2000.00,
                'per_restaurant_amount' => 1000.00,
                'total_amount' => 7000.00,
                'billing_period_end' => $now->copy()->addDays(20)->toDateString(),
                'payment_method' => 'upi',
                'payment_gateway' => 'stripe',
                'gateway_transaction_id' => 'txn_plus_' . uniqid(),
                'status' => SubscriptionPayment::STATUS_COMPLETED,
                'due_date' => $now->copy()->addDays(20)->toDateString(),
                'paid_at' => $now->copy()->subDays(10),
            ]
        );

        // Tenant 3: PRO_MAX Plan - Active
        $tenant3 = Tenant::updateOrCreate(
            ['email' => 'tenant.promax@hungerhop.com'],
            [
                'tenant_name' => 'Mega Food Chain',
                'contact_person' => 'Amit Kumar',
                'phone' => '9876543003',
                'subscription_plan' => Tenant::PLAN_PRO_MAX,
                'total_restaurants' => 10,
                'monthly_base_fee' => 2500.00,
                'per_restaurant_fee' => 1500.00,
                'banner_limit' => 10,
                'status' => Tenant::STATUS_APPROVED,
                'subscription_start_date' => $now->copy()->subDays(5)->toDateString(),
                'next_billing_date' => $now->copy()->addDays(25)->toDateString(),
                'approved_at' => $now->copy()->subDays(5),
            ]
        );

        // Create Tenant Admin for Tenant 3
        User::updateOrCreate(
            ['email' => 'tenant.promax@hungerhop.com'],
            [
                'first_name' => 'Amit',
                'last_name' => 'Kumar',
                'phone' => '9876543003',
                'tenant_id' => $tenant3->id,
                'role' => 'tenant_admin',
                'status' => 'active',
                'password' => Hash::make('Admin@123'),
                'email_verified_at' => now(),
            ]
        );

        // Create Subscription Payment for Tenant 3
        SubscriptionPayment::updateOrCreate(
            ['tenant_id' => $tenant3->id, 'billing_period_start' => $now->copy()->subDays(5)->toDateString()],
            [
                'subscription_plan' => Tenant::PLAN_PRO_MAX,
                'restaurant_count' => 10,
                'base_amount' => 2500.00,
                'per_restaurant_amount' => 1500.00,
                'total_amount' => 17500.00,
                'billing_period_end' => $now->copy()->addDays(25)->toDateString(),
                'payment_method' => 'card',
                'payment_gateway' => 'stripe',
                'gateway_transaction_id' => 'txn_promax_' . uniqid(),
                'status' => SubscriptionPayment::STATUS_COMPLETED,
                'due_date' => $now->copy()->addDays(25)->toDateString(),
                'paid_at' => $now->copy()->subDays(5),
            ]
        );

        $this->command->info('✓ Created 3 Tenants with Subscription Plans:');
        $this->command->info('  - Foodie Express (LITE): tenant.lite@hungerhop.com / Admin@123');
        $this->command->info('  - Tasty Bites Network (PLUS): tenant.plus@hungerhop.com / Admin@123');
        $this->command->info('  - Mega Food Chain (PRO_MAX): tenant.promax@hungerhop.com / Admin@123');
    }
}
