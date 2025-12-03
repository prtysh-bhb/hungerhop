<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Restaurant;
use App\Models\State;
use App\Models\Tenant;
use App\Models\User;
use App\Models\RestaurantWorkingHour;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates restaurants for each tenant with location admins
     */
    public function run(): void
    {
        $now = Carbon::now();

        $restaurantData = [
            // Restaurants for Tenant 1 (Foodie Express - LITE)
            [
                'tenant_email' => 'tenant.lite@hungerhop.com',
                'restaurants' => [
                    [
                        'restaurant_name' => 'Spice Garden',
                        'description' => 'Authentic North Indian cuisine with a modern twist',
                        'cuisine_type' => 'North Indian',
                        'address' => '123 MG Road',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'postal_code' => '560001',
                        'latitude' => 12.9716,
                        'longitude' => 77.5946,
                        'phone' => '0801234501',
                        'email' => 'spicegarden@hungerhop.com',
                        'location_admin_email' => 'loc.spicegarden@hungerhop.com',
                        'location_admin_name' => ['Vijay', 'Singh'],
                    ],
                    [
                        'restaurant_name' => 'Tandoori Nights',
                        'description' => 'Best tandoori dishes in town',
                        'cuisine_type' => 'Mughlai',
                        'address' => '456 Brigade Road',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'postal_code' => '560025',
                        'latitude' => 12.9698,
                        'longitude' => 77.6069,
                        'phone' => '0801234502',
                        'email' => 'tandoorinights@hungerhop.com',
                        'location_admin_email' => 'loc.tandoorinights@hungerhop.com',
                        'location_admin_name' => ['Suresh', 'Menon'],
                    ],
                ],
            ],
            // Restaurants for Tenant 2 (Tasty Bites Network - PLUS)
            [
                'tenant_email' => 'tenant.plus@hungerhop.com',
                'restaurants' => [
                    [
                        'restaurant_name' => 'South Spice',
                        'description' => 'Traditional South Indian delicacies',
                        'cuisine_type' => 'South Indian',
                        'address' => '789 Koramangala',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'postal_code' => '560034',
                        'latitude' => 12.9352,
                        'longitude' => 77.6245,
                        'phone' => '0801234503',
                        'email' => 'southspice@hungerhop.com',
                        'location_admin_email' => 'loc.southspice@hungerhop.com',
                        'location_admin_name' => ['Lakshmi', 'Narayanan'],
                    ],
                    [
                        'restaurant_name' => 'Biryani House',
                        'description' => 'Hyderabadi biryani specialists',
                        'cuisine_type' => 'Hyderabadi',
                        'address' => '321 Indiranagar',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'postal_code' => '560038',
                        'latitude' => 12.9784,
                        'longitude' => 77.6408,
                        'phone' => '0801234504',
                        'email' => 'biryanihouse@hungerhop.com',
                        'location_admin_email' => 'loc.biryanihouse@hungerhop.com',
                        'location_admin_name' => ['Mohammed', 'Ali'],
                    ],
                    [
                        'restaurant_name' => 'Chinese Wok',
                        'description' => 'Indo-Chinese fusion cuisine',
                        'cuisine_type' => 'Chinese',
                        'address' => '555 HSR Layout',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'postal_code' => '560102',
                        'latitude' => 12.9116,
                        'longitude' => 77.6474,
                        'phone' => '0801234505',
                        'email' => 'chinesewok@hungerhop.com',
                        'location_admin_email' => 'loc.chinesewok@hungerhop.com',
                        'location_admin_name' => ['David', 'Chen'],
                    ],
                ],
            ],
            // Restaurants for Tenant 3 (Mega Food Chain - PRO_MAX)
            [
                'tenant_email' => 'tenant.promax@hungerhop.com',
                'restaurants' => [
                    [
                        'restaurant_name' => 'Pizza Paradise',
                        'description' => 'Authentic Italian pizzas with fresh ingredients',
                        'cuisine_type' => 'Italian',
                        'address' => '100 Whitefield',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'postal_code' => '560066',
                        'latitude' => 12.9698,
                        'longitude' => 77.7500,
                        'phone' => '0801234506',
                        'email' => 'pizzaparadise@hungerhop.com',
                        'location_admin_email' => 'loc.pizzaparadise@hungerhop.com',
                        'location_admin_name' => ['Marco', 'Rossi'],
                    ],
                    [
                        'restaurant_name' => 'Burger Barn',
                        'description' => 'Gourmet burgers and shakes',
                        'cuisine_type' => 'American',
                        'address' => '200 Electronic City',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'postal_code' => '560100',
                        'latitude' => 12.8399,
                        'longitude' => 77.6770,
                        'phone' => '0801234507',
                        'email' => 'burgerbarn@hungerhop.com',
                        'location_admin_email' => 'loc.burgerbarn@hungerhop.com',
                        'location_admin_name' => ['John', 'Smith'],
                    ],
                    [
                        'restaurant_name' => 'Sushi Station',
                        'description' => 'Fresh Japanese sushi and ramen',
                        'cuisine_type' => 'Japanese',
                        'address' => '300 Marathahalli',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'postal_code' => '560037',
                        'latitude' => 12.9591,
                        'longitude' => 77.6974,
                        'phone' => '0801234508',
                        'email' => 'sushistation@hungerhop.com',
                        'location_admin_email' => 'loc.sushistation@hungerhop.com',
                        'location_admin_name' => ['Yuki', 'Tanaka'],
                    ],
                    [
                        'restaurant_name' => 'Dosa Delight',
                        'description' => 'Crispy dosas with 50+ varieties',
                        'cuisine_type' => 'South Indian',
                        'address' => '400 Jayanagar',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'postal_code' => '560041',
                        'latitude' => 12.9308,
                        'longitude' => 77.5838,
                        'phone' => '0801234509',
                        'email' => 'dosadelight@hungerhop.com',
                        'location_admin_email' => 'loc.dosadelight@hungerhop.com',
                        'location_admin_name' => ['Ravi', 'Iyer'],
                    ],
                ],
            ],
        ];

        $businessHours = [
            'monday' => ['open' => '09:00', 'close' => '22:00'],
            'tuesday' => ['open' => '09:00', 'close' => '22:00'],
            'wednesday' => ['open' => '09:00', 'close' => '22:00'],
            'thursday' => ['open' => '09:00', 'close' => '22:00'],
            'friday' => ['open' => '09:00', 'close' => '23:00'],
            'saturday' => ['open' => '10:00', 'close' => '23:00'],
            'sunday' => ['open' => '10:00', 'close' => '22:00'],
        ];

        $restaurantCount = 0;

        // Get Bangalore city and Karnataka state IDs
        $bangaloreCity = City::where('name', 'Bangalore')->first();
        $karnatakaState = State::where('name', 'Karnataka')->first();

        if (!$bangaloreCity || !$karnatakaState) {
            $this->command->warn('Bangalore city or Karnataka state not found. Please run CitySeeder first.');
            return;
        }

        foreach ($restaurantData as $tenantGroup) {
            $tenant = Tenant::where('email', $tenantGroup['tenant_email'])->first();
            
            if (!$tenant) {
                $this->command->warn("Tenant not found: {$tenantGroup['tenant_email']}");
                continue;
            }

            foreach ($tenantGroup['restaurants'] as $restData) {
                // Create Location Admin User
                $locationAdmin = User::updateOrCreate(
                    ['email' => $restData['location_admin_email']],
                    [
                        'first_name' => $restData['location_admin_name'][0],
                        'last_name' => $restData['location_admin_name'][1],
                        'phone' => '98765' . str_pad($restaurantCount + 10, 5, '0', STR_PAD_LEFT),
                        'tenant_id' => $tenant->id,
                        'role' => 'location_admin',
                        'status' => 'active',
                        'password' => Hash::make('Admin@123'),
                        'email_verified_at' => now(),
                    ]
                );

                // Create Restaurant
                $restaurant = Restaurant::updateOrCreate(
                    ['email' => $restData['email']],
                    [
                        'tenant_id' => $tenant->id,
                        'location_admin_id' => $locationAdmin->id,
                        'restaurant_name' => $restData['restaurant_name'],
                        'contact_person_name' => $restData['location_admin_name'][0] . ' ' . $restData['location_admin_name'][1],
                        'slug' => Str::slug($restData['restaurant_name']) . '-' . $tenant->id,
                        'description' => $restData['description'],
                        'cuisine_type' => $restData['cuisine_type'],
                        'address' => $restData['address'],
                        'latitude' => $restData['latitude'],
                        'longitude' => $restData['longitude'],
                        'city' => $bangaloreCity->id,
                        'state' => $karnatakaState->id,
                        'postal_code' => $restData['postal_code'],
                        'phone' => $restData['phone'],
                        'delivery_radius_km' => 10,
                        'minimum_order_amount' => 150.00,
                        'base_delivery_fee' => 30.00,
                        'restaurant_commission_percentage' => 80.00,
                        'estimated_delivery_time' => 30,
                        'tax_percentage' => 5.00,
                        'is_open' => true,
                        'accepts_orders' => true,
                        'is_paused' => false,
                        'status' => Restaurant::STATUS_APPROVED,
                        'average_rating' => rand(35, 50) / 10,
                        'total_reviews' => rand(50, 500),
                        'total_orders' => rand(100, 1000),
                        'approved_at' => $now->copy()->subDays(rand(5, 30)),
                        'business_hours' => $businessHours,
                        'is_featured' => rand(0, 1) === 1,
                        'setup_completed' => true,
                        'onboarding_step' => 5,
                    ]
                );

                // Update location admin with restaurant_id
                $locationAdmin->update(['restaurant_id' => $restaurant->id]);

                // Create Working Hours
                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                foreach ($days as $index => $day) {
                    RestaurantWorkingHour::updateOrCreate(
                        [
                            'restaurant_id' => $restaurant->id,
                            'day_of_week' => $index,
                        ],
                        [
                            'tenant_id' => $tenant->id,
                            'is_open' => true,
                            'open_time' => $businessHours[$day]['open'],
                            'close_time' => $businessHours[$day]['close'],
                        ]
                    );
                }

                $restaurantCount++;
            }
        }

        $this->command->info("✓ Created {$restaurantCount} Restaurants with Location Admins");
        $this->command->info('  - All location admins password: Admin@123');
    }
}
