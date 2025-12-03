<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuTemplate;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates menu categories and items for all restaurants
     */
    public function run(): void
    {
        // Menu templates for different cuisine types
        $menuTemplates = [
            'North Indian' => [
                'Starters' => [
                    ['name' => 'Paneer Tikka', 'price' => 250, 'veg' => true, 'popular' => true],
                    ['name' => 'Chicken Tikka', 'price' => 280, 'veg' => false, 'popular' => true],
                    ['name' => 'Veg Seekh Kebab', 'price' => 220, 'veg' => true, 'popular' => false],
                    ['name' => 'Tandoori Chicken', 'price' => 320, 'veg' => false, 'popular' => true],
                ],
                'Main Course' => [
                    ['name' => 'Butter Chicken', 'price' => 350, 'veg' => false, 'popular' => true],
                    ['name' => 'Dal Makhani', 'price' => 220, 'veg' => true, 'popular' => true],
                    ['name' => 'Palak Paneer', 'price' => 260, 'veg' => true, 'popular' => false],
                    ['name' => 'Kadai Chicken', 'price' => 340, 'veg' => false, 'popular' => false],
                    ['name' => 'Shahi Paneer', 'price' => 280, 'veg' => true, 'popular' => true],
                ],
                'Breads' => [
                    ['name' => 'Butter Naan', 'price' => 50, 'veg' => true, 'popular' => true],
                    ['name' => 'Garlic Naan', 'price' => 60, 'veg' => true, 'popular' => true],
                    ['name' => 'Tandoori Roti', 'price' => 30, 'veg' => true, 'popular' => false],
                    ['name' => 'Laccha Paratha', 'price' => 55, 'veg' => true, 'popular' => false],
                ],
                'Rice' => [
                    ['name' => 'Jeera Rice', 'price' => 150, 'veg' => true, 'popular' => false],
                    ['name' => 'Veg Pulao', 'price' => 180, 'veg' => true, 'popular' => false],
                ],
            ],
            'South Indian' => [
                'Dosas' => [
                    ['name' => 'Masala Dosa', 'price' => 120, 'veg' => true, 'popular' => true],
                    ['name' => 'Plain Dosa', 'price' => 80, 'veg' => true, 'popular' => false],
                    ['name' => 'Mysore Masala Dosa', 'price' => 140, 'veg' => true, 'popular' => true],
                    ['name' => 'Rava Dosa', 'price' => 130, 'veg' => true, 'popular' => false],
                    ['name' => 'Ghee Roast Dosa', 'price' => 150, 'veg' => true, 'popular' => true],
                ],
                'Idlis & Vadas' => [
                    ['name' => 'Idli (2 pcs)', 'price' => 60, 'veg' => true, 'popular' => true],
                    ['name' => 'Medu Vada (2 pcs)', 'price' => 70, 'veg' => true, 'popular' => true],
                    ['name' => 'Idli Vada Combo', 'price' => 100, 'veg' => true, 'popular' => true],
                ],
                'Rice Items' => [
                    ['name' => 'Curd Rice', 'price' => 100, 'veg' => true, 'popular' => false],
                    ['name' => 'Lemon Rice', 'price' => 110, 'veg' => true, 'popular' => false],
                    ['name' => 'Bisi Bele Bath', 'price' => 140, 'veg' => true, 'popular' => true],
                ],
                'Uttapam' => [
                    ['name' => 'Onion Uttapam', 'price' => 120, 'veg' => true, 'popular' => false],
                    ['name' => 'Mixed Veg Uttapam', 'price' => 140, 'veg' => true, 'popular' => false],
                ],
            ],
            'Chinese' => [
                'Starters' => [
                    ['name' => 'Veg Spring Rolls', 'price' => 180, 'veg' => true, 'popular' => true],
                    ['name' => 'Chicken Spring Rolls', 'price' => 200, 'veg' => false, 'popular' => true],
                    ['name' => 'Crispy Chilli Potato', 'price' => 180, 'veg' => true, 'popular' => true],
                    ['name' => 'Manchurian Dry', 'price' => 200, 'veg' => true, 'popular' => false],
                ],
                'Noodles' => [
                    ['name' => 'Veg Hakka Noodles', 'price' => 180, 'veg' => true, 'popular' => true],
                    ['name' => 'Chicken Hakka Noodles', 'price' => 220, 'veg' => false, 'popular' => true],
                    ['name' => 'Schezwan Noodles', 'price' => 200, 'veg' => true, 'popular' => false],
                ],
                'Fried Rice' => [
                    ['name' => 'Veg Fried Rice', 'price' => 180, 'veg' => true, 'popular' => true],
                    ['name' => 'Chicken Fried Rice', 'price' => 220, 'veg' => false, 'popular' => true],
                    ['name' => 'Egg Fried Rice', 'price' => 180, 'veg' => false, 'popular' => false],
                ],
                'Main Course' => [
                    ['name' => 'Chilli Chicken', 'price' => 280, 'veg' => false, 'popular' => true],
                    ['name' => 'Kung Pao Chicken', 'price' => 300, 'veg' => false, 'popular' => false],
                    ['name' => 'Paneer Chilli', 'price' => 260, 'veg' => true, 'popular' => true],
                ],
            ],
            'Italian' => [
                'Pizzas' => [
                    ['name' => 'Margherita Pizza', 'price' => 299, 'veg' => true, 'popular' => true],
                    ['name' => 'Pepperoni Pizza', 'price' => 399, 'veg' => false, 'popular' => true],
                    ['name' => 'Veggie Supreme', 'price' => 349, 'veg' => true, 'popular' => true],
                    ['name' => 'BBQ Chicken Pizza', 'price' => 449, 'veg' => false, 'popular' => false],
                    ['name' => 'Four Cheese Pizza', 'price' => 379, 'veg' => true, 'popular' => false],
                ],
                'Pastas' => [
                    ['name' => 'Spaghetti Bolognese', 'price' => 320, 'veg' => false, 'popular' => true],
                    ['name' => 'Penne Arrabiata', 'price' => 280, 'veg' => true, 'popular' => false],
                    ['name' => 'Alfredo Pasta', 'price' => 300, 'veg' => true, 'popular' => true],
                    ['name' => 'Carbonara', 'price' => 350, 'veg' => false, 'popular' => false],
                ],
                'Sides' => [
                    ['name' => 'Garlic Bread', 'price' => 149, 'veg' => true, 'popular' => true],
                    ['name' => 'Cheesy Garlic Bread', 'price' => 179, 'veg' => true, 'popular' => true],
                    ['name' => 'Bruschetta', 'price' => 199, 'veg' => true, 'popular' => false],
                ],
            ],
            'American' => [
                'Burgers' => [
                    ['name' => 'Classic Burger', 'price' => 199, 'veg' => false, 'popular' => true],
                    ['name' => 'Cheese Burger', 'price' => 229, 'veg' => false, 'popular' => true],
                    ['name' => 'Veggie Burger', 'price' => 179, 'veg' => true, 'popular' => true],
                    ['name' => 'Double Patty Burger', 'price' => 329, 'veg' => false, 'popular' => false],
                    ['name' => 'BBQ Bacon Burger', 'price' => 349, 'veg' => false, 'popular' => true],
                ],
                'Fries & Sides' => [
                    ['name' => 'French Fries', 'price' => 99, 'veg' => true, 'popular' => true],
                    ['name' => 'Loaded Fries', 'price' => 149, 'veg' => true, 'popular' => false],
                    ['name' => 'Onion Rings', 'price' => 129, 'veg' => true, 'popular' => false],
                    ['name' => 'Coleslaw', 'price' => 79, 'veg' => true, 'popular' => false],
                ],
                'Shakes' => [
                    ['name' => 'Chocolate Shake', 'price' => 149, 'veg' => true, 'popular' => true],
                    ['name' => 'Vanilla Shake', 'price' => 129, 'veg' => true, 'popular' => false],
                    ['name' => 'Strawberry Shake', 'price' => 139, 'veg' => true, 'popular' => false],
                ],
            ],
            'Japanese' => [
                'Sushi' => [
                    ['name' => 'California Roll (6 pcs)', 'price' => 350, 'veg' => false, 'popular' => true],
                    ['name' => 'Salmon Nigiri (4 pcs)', 'price' => 400, 'veg' => false, 'popular' => true],
                    ['name' => 'Veg Sushi Roll (6 pcs)', 'price' => 280, 'veg' => true, 'popular' => false],
                    ['name' => 'Dragon Roll (8 pcs)', 'price' => 550, 'veg' => false, 'popular' => true],
                ],
                'Ramen' => [
                    ['name' => 'Tonkotsu Ramen', 'price' => 380, 'veg' => false, 'popular' => true],
                    ['name' => 'Miso Ramen', 'price' => 350, 'veg' => true, 'popular' => false],
                    ['name' => 'Shoyu Ramen', 'price' => 340, 'veg' => false, 'popular' => false],
                ],
                'Sides' => [
                    ['name' => 'Edamame', 'price' => 180, 'veg' => true, 'popular' => true],
                    ['name' => 'Gyoza (6 pcs)', 'price' => 220, 'veg' => false, 'popular' => true],
                    ['name' => 'Miso Soup', 'price' => 120, 'veg' => true, 'popular' => false],
                ],
            ],
            'Hyderabadi' => [
                'Biryani' => [
                    ['name' => 'Chicken Dum Biryani', 'price' => 320, 'veg' => false, 'popular' => true],
                    ['name' => 'Mutton Dum Biryani', 'price' => 380, 'veg' => false, 'popular' => true],
                    ['name' => 'Veg Dum Biryani', 'price' => 250, 'veg' => true, 'popular' => false],
                    ['name' => 'Egg Biryani', 'price' => 280, 'veg' => false, 'popular' => false],
                ],
                'Starters' => [
                    ['name' => 'Mirchi Ka Salan', 'price' => 180, 'veg' => true, 'popular' => true],
                    ['name' => 'Double Ka Meetha', 'price' => 120, 'veg' => true, 'popular' => false],
                    ['name' => 'Kebab Platter', 'price' => 350, 'veg' => false, 'popular' => true],
                ],
                'Accompaniments' => [
                    ['name' => 'Raita', 'price' => 60, 'veg' => true, 'popular' => true],
                    ['name' => 'Salan', 'price' => 80, 'veg' => true, 'popular' => true],
                ],
            ],
            'Mughlai' => [
                'Kebabs' => [
                    ['name' => 'Galouti Kebab', 'price' => 380, 'veg' => false, 'popular' => true],
                    ['name' => 'Seekh Kebab', 'price' => 320, 'veg' => false, 'popular' => true],
                    ['name' => 'Reshmi Kebab', 'price' => 350, 'veg' => false, 'popular' => false],
                    ['name' => 'Paneer Tikka', 'price' => 280, 'veg' => true, 'popular' => true],
                ],
                'Curries' => [
                    ['name' => 'Mughlai Chicken', 'price' => 380, 'veg' => false, 'popular' => true],
                    ['name' => 'Korma', 'price' => 350, 'veg' => false, 'popular' => false],
                    ['name' => 'Nihari', 'price' => 420, 'veg' => false, 'popular' => true],
                ],
                'Breads' => [
                    ['name' => 'Sheermal', 'price' => 80, 'veg' => true, 'popular' => false],
                    ['name' => 'Roomali Roti', 'price' => 60, 'veg' => true, 'popular' => true],
                ],
            ],
        ];

        // Default menu for cuisines not in templates
        $defaultMenu = [
            'Starters' => [
                ['name' => 'Mixed Platter', 'price' => 250, 'veg' => true, 'popular' => true],
                ['name' => 'Special Appetizer', 'price' => 280, 'veg' => false, 'popular' => false],
            ],
            'Main Course' => [
                ['name' => 'Chef Special', 'price' => 350, 'veg' => false, 'popular' => true],
                ['name' => 'House Special Veg', 'price' => 300, 'veg' => true, 'popular' => true],
            ],
            'Beverages' => [
                ['name' => 'Fresh Lime Soda', 'price' => 80, 'veg' => true, 'popular' => true],
                ['name' => 'Cold Coffee', 'price' => 120, 'veg' => true, 'popular' => true],
            ],
        ];

        // Size variations
        $sizeVariations = [
            ['label' => 'Regular', 'price_delta' => 0],
            ['label' => 'Medium', 'price_delta' => 50],
            ['label' => 'Large', 'price_delta' => 100],
        ];

        $restaurants = Restaurant::all();
        $categoryCount = 0;
        $itemCount = 0;

        foreach ($restaurants as $restaurant) {
            $cuisineType = $restaurant->cuisine_type;
            $menu = $menuTemplates[$cuisineType] ?? $defaultMenu;

            // Create a menu template for each restaurant
            $menuTemplate = MenuTemplate::updateOrCreate(
                [
                    'tenant_id' => $restaurant->tenant_id,
                    'template_name' => "{$restaurant->restaurant_name} Menu",
                ],
                [
                    'description' => "Menu template for {$restaurant->restaurant_name}",
                ]
            );

            $sortOrder = 1;
            foreach ($menu as $categoryName => $items) {
                // Create Category
                $category = MenuCategory::updateOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'name' => $categoryName,
                    ],
                    [
                        'tenant_id' => $restaurant->tenant_id,
                        'menu_template_id' => $menuTemplate->id,
                        'description' => "{$categoryName} items from {$restaurant->restaurant_name}",
                        'sort_order' => $sortOrder++,
                        'is_active' => true,
                    ]
                );
                $categoryCount++;

                $itemSortOrder = 1;
                foreach ($items as $itemData) {
                    // Create Menu Item
                    $menuItem = MenuItem::updateOrCreate(
                        [
                            'restaurant_id' => $restaurant->id,
                            'menu_category_id' => $category->id,
                            'item_name' => $itemData['name'],
                        ],
                        [
                            'tenant_id' => $restaurant->tenant_id,
                            'description' => "Delicious {$itemData['name']} prepared with fresh ingredients",
                            'base_price' => $itemData['price'],
                            'is_vegetarian' => $itemData['veg'],
                            'is_vegan' => false,
                            'is_gluten_free' => false,
                            'preparation_time' => rand(15, 30),
                            'is_available' => true,
                            'is_popular' => $itemData['popular'],
                            'sort_order' => $itemSortOrder++,
                            'total_sales' => rand(10, 200),
                            'average_rating' => rand(35, 50) / 10,
                            'total_reviews' => rand(5, 100),
                        ]
                    );
                    $itemCount++;

                    // Add size variations for main dishes (price > 150)
                    // Using DB::table to avoid BaseTenantModel trying to set tenant_id
                    if ($itemData['price'] > 150) {
                        foreach ($sizeVariations as $variation) {
                            \DB::table('menu_variations')->updateOrInsert(
                                [
                                    'menu_item_id' => $menuItem->id,
                                    'label' => $variation['label'],
                                ],
                                [
                                    'price_delta' => $variation['price_delta'],
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]
                            );
                        }
                    }
                }
            }
        }

        $this->command->info("✓ Created {$categoryCount} Menu Categories and {$itemCount} Menu Items");
    }
}
