<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoFullSeeder extends Seeder
{
    public function run()
    {

        $now = Carbon::now();

        // 1. Insert a tenant
        $uniqueEmail = 'tenant_'.uniqid().'@example.com';
        $tenantId = DB::table('tenants')->insertGetId([
            'tenant_name' => 'Demo Tenant',
            'contact_person' => 'Demo Owner',
            'email' => $uniqueEmail,
            'phone' => '1234567890',
            'subscription_plan' => 'LITE',
            'total_restaurants' => 1,
            'monthly_base_fee' => 0,
            'per_restaurant_fee' => 0,
            'banner_limit' => 0,
            'status' => 'approved',
            'subscription_start_date' => $now->toDateString(),
            'next_billing_date' => $now->copy()->addMonth()->toDateString(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Insert a user (customer)
        $userEmail = 'customer_'.uniqid().'@example.com';
        $userId = DB::table('users')->insertGetId([
            'tenant_id' => $tenantId,
            'email' => $userEmail,
            'phone' => '9999999999',
            'password' => bcrypt('password'),
            'first_name' => 'Demo',
            'last_name' => 'Customer',
            'role' => 'customer',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Insert a customer profile
        $customerId = DB::table('customer_profiles')->insertGetId([
            'user_id' => $userId,
            'total_orders' => 0,
            'total_spent' => 0,
            'loyalty_points' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 4. Insert a restaurant
        $restaurantSlug = 'demo-restaurant-'.uniqid();
        $restaurantId = DB::table('restaurants')->insertGetId([
            'tenant_id' => $tenantId,
            'restaurant_name' => 'Demo Restaurant',
            'slug' => $restaurantSlug,
            'description' => 'A great demo restaurant',
            'cuisine_type' => 'Indian',
            'address' => '123 Main St',
            'latitude' => 12.9716,
            'longitude' => 77.5946,
            'city' => 'Bangalore',
            'state' => 'Karnataka',
            'postal_code' => '560001',
            'phone' => '0801234567',
            'email' => 'restaurant_'.uniqid().'@example.com',
            'delivery_radius_km' => 10,
            'minimum_order_amount' => 100,
            'base_delivery_fee' => 20,
            'restaurant_commission_percentage' => 80.00,
            'estimated_delivery_time' => 30,
            'tax_percentage' => 5.00,
            'is_open' => true,
            'accepts_orders' => true,
            'status' => 'approved',
            'average_rating' => 0,
            'total_reviews' => 0,
            'total_orders' => 0,
            'business_hours' => json_encode(['monday' => ['open' => '09:00', 'close' => '22:00']]),
            'is_featured' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 5. Insert a menu category
        $categoryId = DB::table('menu_categories')->insertGetId([
            'tenant_id' => $tenantId,
            'restaurant_id' => $restaurantId,
            'category_name' => 'Starters',
            'description' => 'Tasty starters',
            'is_active' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 6. Insert a menu item
        $menuItemId = DB::table('menu_items')->insertGetId([
            'tenant_id' => $tenantId,
            'restaurant_id' => $restaurantId,
            'menu_category_id' => $categoryId,
            'item_name' => 'Paneer Tikka',
            'description' => 'Delicious paneer tikka',
            'base_price' => 250,
            'is_vegetarian' => 1,
            'is_available' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 7. Insert a delivery address (required for orders)
        $addressId = DB::table('customer_addresses')->insertGetId([
            'customer_id' => $customerId,
            'address_line_1' => '123 Main St',
            'city' => 'Bangalore',
            'state' => 'Karnataka',
            'postal_code' => '560001',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 8. Insert an order
        $orderId = DB::table('orders')->insertGetId([
            'order_number' => 'ORD'.rand(10000, 99999),
            'customer_id' => $customerId,
            'restaurant_id' => $restaurantId,
            'delivery_address_id' => $addressId,
            'tenant_id' => $tenantId,
            'status' => 'delivered',
            'subtotal' => 250,
            'tax_amount' => 12.5,
            'delivery_fee' => 20,
            'discount_amount' => 0,
            'total_amount' => 282.5,
            'restaurant_amount' => 250,
            'delivery_amount' => 20,
            'platform_fee' => 0,
            'payment_method' => 'cod',
            'payment_status' => 'completed',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 9. Insert a review
        DB::table('reviews')->insert([
            [
                'order_id' => $orderId,
                'tenant_id' => $tenantId,
                'customer_id' => $customerId,
                'reviewable_type' => 'restaurant',
                'reviewable_id' => $restaurantId,
                'rating' => 5,
                'review_text' => 'Amazing food!',
                'is_anonymous' => false,
                'is_featured' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
