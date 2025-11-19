<?php

namespace Database\Seeders;

use App\Models\MenuCategory;
use App\Models\MenuTemplate;
use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class MenuCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all templates and restaurants
        $templates = MenuTemplate::all();
        $restaurants = Restaurant::all();

        foreach ($templates as $template) {
            $categories = $this->getCategoriesForTemplate($template->template_name, $template->tenant_id);

            foreach ($categories as $categoryData) {
                // If there are restaurants for this tenant, assign categories to first restaurant
                $restaurant = $restaurants->where('tenant_id', $template->tenant_id)->first();

                if ($restaurant) {
                    $categoryData['restaurant_id'] = $restaurant->id;
                }

                $categoryData['menu_template_id'] = $template->id;
                $categoryData['tenant_id'] = $template->tenant_id;

                MenuCategory::updateOrCreate(
                    [
                        'tenant_id' => $categoryData['tenant_id'],
                        'name' => $categoryData['name'],
                        'menu_template_id' => $template->id,
                    ],
                    $categoryData
                );
            }
        }

        // If no templates exist, create basic categories for existing restaurants
        if ($templates->isEmpty() && $restaurants->isNotEmpty()) {
            foreach ($restaurants as $restaurant) {
                $basicCategories = $this->getBasicCategories($restaurant->tenant_id, $restaurant->id);

                foreach ($basicCategories as $categoryData) {
                    MenuCategory::updateOrCreate(
                        [
                            'tenant_id' => $categoryData['tenant_id'],
                            'restaurant_id' => $categoryData['restaurant_id'],
                            'name' => $categoryData['name'],
                        ],
                        $categoryData
                    );
                }
            }
        }

        // Fallback: Create basic categories if no templates and no restaurants
        if ($templates->isEmpty() && $restaurants->isEmpty()) {
            $fallbackCategories = $this->getBasicCategories(1, 1); // Assuming tenant_id=1, restaurant_id=1

            foreach ($fallbackCategories as $categoryData) {
                MenuCategory::create($categoryData);
            }
        }
    }

    /**
     * Get categories based on template name
     */
    private function getCategoriesForTemplate(string $templateName, int $tenantId): array
    {
        switch ($templateName) {
            case 'Quick Service Menu':
                return [
                    [
                        'name' => 'Burgers & Sandwiches',
                        'description' => 'Delicious burgers and sandwiches with fresh ingredients',
                        'sort_order' => 1,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Pizzas',
                        'description' => 'Freshly baked pizzas with various toppings',
                        'sort_order' => 2,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Fried Chicken',
                        'description' => 'Crispy and juicy fried chicken pieces',
                        'sort_order' => 3,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Beverages',
                        'description' => 'Refreshing drinks and beverages',
                        'sort_order' => 4,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Desserts',
                        'description' => 'Sweet treats and desserts',
                        'sort_order' => 5,
                        'is_active' => true,
                    ],
                ];

            case 'Fine Dining Menu':
                return [
                    [
                        'name' => 'Appetizers',
                        'description' => 'Elegant starters to begin your dining experience',
                        'sort_order' => 1,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Soups & Salads',
                        'description' => 'Fresh soups and crisp salads',
                        'sort_order' => 2,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Main Courses',
                        'description' => 'Exquisite main dishes crafted with premium ingredients',
                        'sort_order' => 3,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Seafood Specialties',
                        'description' => 'Fresh seafood preparations',
                        'sort_order' => 4,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Premium Desserts',
                        'description' => 'Artfully crafted desserts',
                        'sort_order' => 5,
                        'is_active' => true,
                    ],
                ];

            case 'Indian Cuisine Menu':
                return [
                    [
                        'name' => 'Starters',
                        'description' => 'Traditional Indian appetizers and snacks',
                        'sort_order' => 1,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Curries',
                        'description' => 'Authentic Indian curries with rich flavors',
                        'sort_order' => 2,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Biryanis & Rice',
                        'description' => 'Aromatic biryanis and rice dishes',
                        'sort_order' => 3,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Indian Breads',
                        'description' => 'Fresh rotis, naans, and other Indian breads',
                        'sort_order' => 4,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Dal & Vegetables',
                        'description' => 'Traditional lentils and vegetable preparations',
                        'sort_order' => 5,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Indian Sweets',
                        'description' => 'Traditional Indian desserts and sweets',
                        'sort_order' => 6,
                        'is_active' => true,
                    ],
                ];

            case 'Cafe & Bistro Menu':
                return [
                    [
                        'name' => 'Coffee & Tea',
                        'description' => 'Freshly brewed coffee and premium teas',
                        'sort_order' => 1,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Breakfast',
                        'description' => 'Healthy and delicious breakfast options',
                        'sort_order' => 2,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Sandwiches & Wraps',
                        'description' => 'Fresh sandwiches and wraps',
                        'sort_order' => 3,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Salads',
                        'description' => 'Healthy and fresh salad bowls',
                        'sort_order' => 4,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Pastries & Bakery',
                        'description' => 'Fresh baked goods and pastries',
                        'sort_order' => 5,
                        'is_active' => true,
                    ],
                ];

            case 'Pizza & Italian Menu':
                return [
                    [
                        'name' => 'Classic Pizzas',
                        'description' => 'Traditional Italian pizzas',
                        'sort_order' => 1,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Specialty Pizzas',
                        'description' => 'Unique pizza creations with premium toppings',
                        'sort_order' => 2,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Pasta',
                        'description' => 'Authentic Italian pasta dishes',
                        'sort_order' => 3,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Italian Starters',
                        'description' => 'Traditional Italian appetizers',
                        'sort_order' => 4,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Italian Desserts',
                        'description' => 'Classic Italian sweet treats',
                        'sort_order' => 5,
                        'is_active' => true,
                    ],
                ];

            case 'Street Food Menu':
                return [
                    [
                        'name' => 'Chaat',
                        'description' => 'Popular Indian street chaat items',
                        'sort_order' => 1,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Rolls & Wraps',
                        'description' => 'Delicious rolls and wraps',
                        'sort_order' => 2,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Momos',
                        'description' => 'Steamed and fried momos with various fillings',
                        'sort_order' => 3,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Dosas & South Indian',
                        'description' => 'South Indian street food favorites',
                        'sort_order' => 4,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Beverages',
                        'description' => 'Fresh juices and street-side drinks',
                        'sort_order' => 5,
                        'is_active' => true,
                    ],
                ];

            default:
                return $this->getBasicCategories($tenantId);
        }
    }

    /**
     * Get basic categories for restaurants without specific templates
     */
    private function getBasicCategories(int $tenantId, ?int $restaurantId = null): array
    {
        $categories = [
            [
                'tenant_id' => $tenantId,
                'name' => 'Starters',
                'description' => 'Appetizers and starters to begin your meal',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'tenant_id' => $tenantId,
                'name' => 'Main Course',
                'description' => 'Hearty main dishes and entrees',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'tenant_id' => $tenantId,
                'name' => 'Beverages',
                'description' => 'Refreshing drinks and beverages',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'tenant_id' => $tenantId,
                'name' => 'Desserts',
                'description' => 'Sweet treats and desserts',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        if ($restaurantId) {
            foreach ($categories as &$category) {
                $category['restaurant_id'] = $restaurantId;
            }
        }

        return $categories;
    }
}
