<?php

namespace Database\Seeders;

use App\Models\RestaurantBanner;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates banners for restaurants
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Valid link_type values: 'restaurant', 'menu_item', 'promotion', 'external'
        // Valid banner_position values: 'home_slider', 'restaurant_page', 'category_page'
        $bannerTemplates = [
            [
                'title' => 'Grand Opening Sale',
                'description' => 'Celebrate with us! Get 30% off on all orders',
                'link_type' => 'promotion',
                'banner_position' => 'home_slider',
            ],
            [
                'title' => 'Free Delivery Weekend',
                'description' => 'Enjoy free delivery on all orders above Rs. 500',
                'link_type' => 'external',
                'banner_position' => 'home_slider',
            ],
            [
                'title' => 'New Menu Launch',
                'description' => 'Try our exciting new dishes freshly added to the menu',
                'link_type' => 'menu_item',
                'banner_position' => 'restaurant_page',
            ],
            [
                'title' => 'Chef Special',
                'description' => 'Handcrafted dishes by our executive chef',
                'link_type' => 'menu_item',
                'banner_position' => 'restaurant_page',
            ],
            [
                'title' => 'Happy Hours',
                'description' => '20% off from 2 PM to 5 PM daily',
                'link_type' => 'promotion',
                'banner_position' => 'category_page',
            ],
            [
                'title' => 'Visit Our Restaurant',
                'description' => 'Dine in for an amazing experience',
                'link_type' => 'restaurant',
                'banner_position' => 'category_page',
            ],
        ];

        $restaurants = Restaurant::where('status', 'approved')->get();
        $bannerCount = 0;

        foreach ($restaurants as $restaurant) {
            // Get tenant banner limit
            $tenant = $restaurant->tenant;
            $bannerLimit = $tenant ? $tenant->banner_limit : 3;
            
            // Create random number of banners up to limit
            $numBanners = rand(1, min($bannerLimit, count($bannerTemplates)));
            $selectedBanners = collect($bannerTemplates)->random($numBanners);

            $sortOrder = 1;
            foreach ($selectedBanners as $template) {
                RestaurantBanner::updateOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'title' => $template['title'],
                    ],
                    [
                        'tenant_id' => $restaurant->tenant_id,
                        'description' => $template['description'],
                        'image_url' => "banners/{$restaurant->id}/banner_{$sortOrder}.jpg",
                        'link_type' => $template['link_type'],
                        'link_id' => null,
                        'external_url' => $template['link_type'] === 'external' ? 'https://hungerhop.com/offers' : null,
                        'banner_position' => $template['banner_position'],
                        'sort_order' => $sortOrder,
                        'click_count' => rand(50, 500),
                        'is_active' => true,
                        'valid_from' => $now->copy()->subDays(rand(0, 15)),
                        'valid_until' => $now->copy()->addDays(rand(30, 90)),
                    ]
                );
                $sortOrder++;
                $bannerCount++;
            }
        }

        $this->command->info("Created {$bannerCount} Restaurant Banners");
    }
}
