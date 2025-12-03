<?php

namespace Database\Seeders;

use App\Models\Promotion;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates promotions for restaurants
     * Each promotion code is unique per restaurant (appended with restaurant ID)
     */
    public function run(): void
    {
        $now = Carbon::now();

        $promotions = [
            // Percentage discounts
            [
                'promotion_code' => 'WELCOME20',
                'title' => 'Welcome Discount',
                'description' => 'Get 20% off on your first order',
                'discount_type' => 'percentage',
                'discount_value' => 20.00,
                'minimum_order_amount' => 200.00,
                'maximum_discount_amount' => 100.00,
                'usage_limit_per_customer' => 1,
                'total_usage_limit' => 1000,
            ],
            [
                'promotion_code' => 'FLAT50',
                'title' => 'Flat ₹50 Off',
                'description' => 'Flat ₹50 off on orders above ₹300',
                'discount_type' => 'fixed_amount',
                'discount_value' => 50.00,
                'minimum_order_amount' => 300.00,
                'maximum_discount_amount' => 50.00,
                'usage_limit_per_customer' => 3,
                'total_usage_limit' => 500,
            ],
            [
                'promotion_code' => 'WEEKEND25',
                'title' => 'Weekend Special',
                'description' => '25% off on weekend orders',
                'discount_type' => 'percentage',
                'discount_value' => 25.00,
                'minimum_order_amount' => 400.00,
                'maximum_discount_amount' => 150.00,
                'usage_limit_per_customer' => 2,
                'total_usage_limit' => 300,
            ],
            [
                'promotion_code' => 'LUNCH15',
                'title' => 'Lunch Special',
                'description' => '15% off on lunch orders (11 AM - 3 PM)',
                'discount_type' => 'percentage',
                'discount_value' => 15.00,
                'minimum_order_amount' => 250.00,
                'maximum_discount_amount' => 75.00,
                'usage_limit_per_customer' => 10,
                'total_usage_limit' => 1000,
            ],
            [
                'promotion_code' => 'FREEDELIVERY',
                'title' => 'Free Delivery',
                'description' => 'Free delivery on orders above ₹500',
                'discount_type' => 'fixed_amount',
                'discount_value' => 40.00,
                'minimum_order_amount' => 500.00,
                'maximum_discount_amount' => 40.00,
                'usage_limit_per_customer' => 5,
                'total_usage_limit' => 800,
            ],
        ];

        $restaurants = Restaurant::with('tenant')->where('status', 'approved')->get();
        $promotionCount = 0;

        if ($restaurants->isEmpty()) {
            $this->command->warn('No approved restaurants found. Run RestaurantSeeder first.');
            return;
        }

        foreach ($restaurants as $restaurant) {
            $locationAdmin = User::where('restaurant_id', $restaurant->id)
                ->where('role', 'location_admin')
                ->first();

            // Each restaurant gets 2-4 promotions with unique codes
            $selectedPromos = collect($promotions)->shuffle()->take(rand(2, 4));
            
            foreach ($selectedPromos as $promoData) {
                // Make promotion code unique by appending restaurant ID
                $uniqueCode = $promoData['promotion_code'] . $restaurant->id;

                Promotion::create([
                    'restaurant_id' => $restaurant->id,
                    'tenant_id' => $restaurant->tenant_id,
                    'promotion_code' => $uniqueCode,
                    'created_by' => $locationAdmin ? $locationAdmin->id : null,
                    'title' => $promoData['title'],
                    'description' => $promoData['description'],
                    'discount_type' => $promoData['discount_type'],
                    'discount_value' => $promoData['discount_value'],
                    'minimum_order_amount' => $promoData['minimum_order_amount'],
                    'maximum_discount_amount' => $promoData['maximum_discount_amount'],
                    'usage_limit_per_customer' => $promoData['usage_limit_per_customer'],
                    'total_usage_limit' => $promoData['total_usage_limit'],
                    'current_usage_count' => rand(0, (int)($promoData['total_usage_limit'] * 0.2)),
                    'valid_from' => $now->copy()->subDays(rand(0, 10)),
                    'valid_until' => $now->copy()->addDays(rand(15, 45)),
                    'is_active' => true,
                ]);
                $promotionCount++;
            }
        }

        $this->command->info("✓ Created {$promotionCount} Promotions");
    }
}
