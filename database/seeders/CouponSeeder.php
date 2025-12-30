<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::query()->delete();

        Coupon::insert([
            [
                'code' => 'SAVE50',
                'title' => 'Flat ₹50 Off',
                'description' => 'Get flat ₹50 off on your order',
                'discount_type' => 'flat',
                'discount_value' => 50,
                'max_discount' => null,
                'min_order_value' => 299,
                'usage_limit' => 100,
                'usage_per_user' => 1,
                'valid_from' => Carbon::now()->subDays(5),
                'valid_to' => Carbon::now()->addDays(30),
                'coupon_scope' => 'global',
                'is_active' => true,
                'created_by' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'DISC10',
                'title' => '10% Discount',
                'description' => 'Get 10% off up to ₹100',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'max_discount' => 100,
                'min_order_value' => 499,
                'usage_limit' => null, // unlimited
                'usage_per_user' => 2,
                'valid_from' => Carbon::now()->subDays(2),
                'valid_to' => Carbon::now()->addDays(15),
                'coupon_scope' => 'global',
                'is_active' => true,
                'created_by' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
