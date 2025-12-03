<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\MenuItemReview;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\MenuItem;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates reviews for restaurants and menu items
     */
    public function run(): void
    {
        $now = Carbon::now();

        $reviewTexts = [
            5 => [
                'Absolutely amazing food! Will definitely order again.',
                'Best food I have ever had. Highly recommended!',
                'Outstanding quality and taste. 10/10!',
                'Perfect delivery and delicious food. Love it!',
                'Exceeded all expectations. Must try!',
            ],
            4 => [
                'Really good food, quick delivery.',
                'Great taste, portion size could be better.',
                'Very satisfied with the order. Will order again.',
                'Tasty food with nice packaging.',
                'Good quality, slightly pricey but worth it.',
            ],
            3 => [
                'Food was okay, nothing special.',
                'Average taste, delivery was on time.',
                'Decent food, expected better.',
                'Okay experience, might try again.',
                'Food was fine, nothing to complain about.',
            ],
            2 => [
                'Below average, food was cold when delivered.',
                'Not impressed, taste could be better.',
                'Disappointing experience, long wait time.',
                'Food quality needs improvement.',
                'Expected much better based on reviews.',
            ],
            1 => [
                'Terrible experience, food was stale.',
                'Very bad, would not recommend.',
                'Worst food ever, complete waste of money.',
                'Never ordering again, horrible taste.',
                'Extremely disappointed, avoid at all costs.',
            ],
        ];

        $menuItemReviewTexts = [
            5 => ['Delicious!', 'Perfect taste!', 'Best ever!', 'Amazing!', 'Must try!'],
            4 => ['Really good', 'Tasty', 'Nice dish', 'Enjoyed it', 'Good quality'],
            3 => ['Okay', 'Average', 'Decent', 'Not bad', 'Passable'],
            2 => ['Below average', 'Disappointing', 'Could be better', 'Not great', 'Mediocre'],
            1 => ['Bad', 'Terrible', 'Avoid', 'Waste', 'Horrible'],
        ];

        // Get delivered orders for creating reviews
        $deliveredOrders = Order::where('status', 'delivered')
            ->with(['customer', 'restaurant', 'items.menuItem'])
            ->get();

        $restaurantReviewCount = 0;
        $menuItemReviewCount = 0;

        foreach ($deliveredOrders as $order) {
            // 70% chance of leaving a review
            if (rand(1, 10) > 7) {
                continue;
            }

            // Weighted random rating (more 4s and 5s)
            $rating = $this->getWeightedRating();
            $reviewText = $reviewTexts[$rating][array_rand($reviewTexts[$rating])];
            
            $reviewDate = Carbon::parse($order->actual_delivery_time ?? $order->updated_at)
                ->addHours(rand(1, 48));

            // Create Restaurant Review
            Review::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'reviewable_type' => Restaurant::class,
                    'reviewable_id' => $order->restaurant_id,
                ],
                [
                    'customer_id' => $order->customer_id,
                    'tenant_id' => $order->tenant_id,
                    'rating' => $rating,
                    'review_text' => $reviewText,
                    'created_at' => $reviewDate,
                    'updated_at' => $reviewDate,
                ]
            );
            $restaurantReviewCount++;

            // Create Menu Item Reviews (50% chance per item)
            foreach ($order->items as $orderItem) {
                if (rand(0, 1) === 0) {
                    continue;
                }

                $itemRating = $this->getWeightedRating();
                $itemReviewText = $menuItemReviewTexts[$itemRating][array_rand($menuItemReviewTexts[$itemRating])];

                MenuItemReview::updateOrCreate(
                    [
                        'order_item_id' => $orderItem->id,
                        'customer_id' => $order->customer_id,
                        'item_id' => $orderItem->item_id,
                    ],
                    [
                        'rating' => $itemRating,
                        'review_text' => $itemReviewText,
                        'images' => null,
                        'created_at' => $reviewDate,
                        'updated_at' => $reviewDate,
                    ]
                );
                $menuItemReviewCount++;
            }
        }

        // Update restaurant average ratings
        $restaurants = Restaurant::all();
        foreach ($restaurants as $restaurant) {
            $avgRating = Review::where('reviewable_type', Restaurant::class)
                ->where('reviewable_id', $restaurant->id)
                ->avg('rating');
            
            $totalReviews = Review::where('reviewable_type', Restaurant::class)
                ->where('reviewable_id', $restaurant->id)
                ->count();

            if ($avgRating) {
                $restaurant->update([
                    'average_rating' => round($avgRating, 1),
                    'total_reviews' => $totalReviews,
                ]);
            }
        }

        // Update menu item average ratings
        $menuItems = MenuItem::all();
        foreach ($menuItems as $menuItem) {
            $avgRating = MenuItemReview::where('item_id', $menuItem->id)->avg('rating');
            $totalReviews = MenuItemReview::where('item_id', $menuItem->id)->count();

            if ($avgRating) {
                $menuItem->update([
                    'average_rating' => round($avgRating, 1),
                    'total_reviews' => $totalReviews,
                ]);
            }
        }

        $this->command->info("✓ Created {$restaurantReviewCount} Restaurant Reviews and {$menuItemReviewCount} Menu Item Reviews");
    }

    /**
     * Get a weighted random rating (more likely to be 4 or 5)
     */
    private function getWeightedRating(): int
    {
        $weights = [
            5 => 40,  // 40% chance
            4 => 35,  // 35% chance
            3 => 15,  // 15% chance
            2 => 7,   // 7% chance
            1 => 3,   // 3% chance
        ];

        $rand = rand(1, 100);
        $cumulative = 0;

        foreach ($weights as $rating => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $rating;
            }
        }

        return 4;
    }
}
