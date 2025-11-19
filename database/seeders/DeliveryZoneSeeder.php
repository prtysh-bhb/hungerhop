<?php

namespace Database\Seeders;

use App\Models\DeliveryZone;
use App\Models\Restaurant;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class DeliveryZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some restaurants and tenants for seeding
        $restaurants = Restaurant::take(5)->get();
        $tenants = Tenant::take(3)->get();

        if ($restaurants->isEmpty() || $tenants->isEmpty()) {
            $this->command->warn('No restaurants or tenants found. Please seed restaurants and tenants first.');

            return;
        }

        // Sample delivery zones for different areas in Mumbai
        $zones = [
            [
                'zone_name' => 'Bandra West Zone',
                'zone_polygon' => [
                    ['lat' => 19.0596, 'lng' => 72.8295],
                    ['lat' => 19.0650, 'lng' => 72.8350],
                    ['lat' => 19.0620, 'lng' => 72.8400],
                    ['lat' => 19.0570, 'lng' => 72.8380],
                    ['lat' => 19.0550, 'lng' => 72.8320],
                ],
                'delivery_fee' => 30.00,
                'minimum_order_amount' => 150.00,
                'estimated_delivery_time' => 25,
            ],
            [
                'zone_name' => 'Andheri East Commercial',
                'zone_polygon' => [
                    ['lat' => 19.1136, 'lng' => 72.8697],
                    ['lat' => 19.1200, 'lng' => 72.8750],
                    ['lat' => 19.1180, 'lng' => 72.8800],
                    ['lat' => 19.1120, 'lng' => 72.8780],
                    ['lat' => 19.1100, 'lng' => 72.8720],
                ],
                'delivery_fee' => 25.00,
                'minimum_order_amount' => 100.00,
                'estimated_delivery_time' => 30,
            ],
            [
                'zone_name' => 'Powai Premium',
                'zone_polygon' => [
                    ['lat' => 19.1176, 'lng' => 72.9060],
                    ['lat' => 19.1250, 'lng' => 72.9120],
                    ['lat' => 19.1230, 'lng' => 72.9180],
                    ['lat' => 19.1160, 'lng' => 72.9150],
                    ['lat' => 19.1140, 'lng' => 72.9080],
                ],
                'delivery_fee' => 40.00,
                'minimum_order_amount' => 200.00,
                'estimated_delivery_time' => 35,
            ],
            [
                'zone_name' => 'Colaba Fort District',
                'zone_polygon' => [
                    ['lat' => 18.9067, 'lng' => 72.8147],
                    ['lat' => 18.9120, 'lng' => 72.8200],
                    ['lat' => 18.9100, 'lng' => 72.8250],
                    ['lat' => 18.9050, 'lng' => 72.8220],
                    ['lat' => 18.9030, 'lng' => 72.8170],
                ],
                'delivery_fee' => 35.00,
                'minimum_order_amount' => 180.00,
                'estimated_delivery_time' => 40,
            ],
            [
                'zone_name' => 'Juhu Beach Area',
                'zone_polygon' => [
                    ['lat' => 19.0883, 'lng' => 72.8264],
                    ['lat' => 19.0930, 'lng' => 72.8320],
                    ['lat' => 19.0910, 'lng' => 72.8370],
                    ['lat' => 19.0860, 'lng' => 72.8340],
                    ['lat' => 19.0840, 'lng' => 72.8290],
                ],
                'delivery_fee' => 45.00,
                'minimum_order_amount' => 250.00,
                'estimated_delivery_time' => 30,
            ],
            [
                'zone_name' => 'Worli Business District',
                'zone_polygon' => [
                    ['lat' => 19.0176, 'lng' => 72.8162],
                    ['lat' => 19.0230, 'lng' => 72.8220],
                    ['lat' => 19.0210, 'lng' => 72.8270],
                    ['lat' => 19.0160, 'lng' => 72.8240],
                    ['lat' => 19.0140, 'lng' => 72.8190],
                ],
                'delivery_fee' => 50.00,
                'minimum_order_amount' => 300.00,
                'estimated_delivery_time' => 25,
            ],
            [
                'zone_name' => 'Thane West Residential',
                'zone_polygon' => [
                    ['lat' => 19.2183, 'lng' => 72.9781],
                    ['lat' => 19.2250, 'lng' => 72.9850],
                    ['lat' => 19.2230, 'lng' => 72.9900],
                    ['lat' => 19.2170, 'lng' => 72.9870],
                    ['lat' => 19.2150, 'lng' => 72.9810],
                ],
                'delivery_fee' => 20.00,
                'minimum_order_amount' => 80.00,
                'estimated_delivery_time' => 35,
            ],
            [
                'zone_name' => 'Malad West Express',
                'zone_polygon' => [
                    ['lat' => 19.1875, 'lng' => 72.8489],
                    ['lat' => 19.1930, 'lng' => 72.8550],
                    ['lat' => 19.1910, 'lng' => 72.8600],
                    ['lat' => 19.1860, 'lng' => 72.8570],
                    ['lat' => 19.1840, 'lng' => 72.8510],
                ],
                'delivery_fee' => 28.00,
                'minimum_order_amount' => 120.00,
                'estimated_delivery_time' => 32,
            ],
        ];

        foreach ($restaurants as $restaurant) {
            // Create 2-3 zones per restaurant
            $zonesToCreate = collect($zones)->random(rand(2, 3));

            foreach ($zonesToCreate as $index => $zoneData) {
                DeliveryZone::create([
                    'restaurant_id' => $restaurant->id,
                    'tenant_id' => $restaurant->tenant_id ?? $tenants->random()->id,
                    'zone_name' => $zoneData['zone_name'].' - '.$restaurant->name,
                    'zone_polygon' => $zoneData['zone_polygon'],
                    'delivery_fee' => $zoneData['delivery_fee'],
                    'minimum_order_amount' => $zoneData['minimum_order_amount'],
                    'estimated_delivery_time' => $zoneData['estimated_delivery_time'],
                    'is_active' => rand(0, 1) ? true : false, // Random active status for testing
                ]);
            }
        }

        // Create some tenant-level zones (zones that apply to all restaurants of a tenant)
        foreach ($tenants->take(2) as $tenant) {
            $globalZones = collect($zones)->random(2);

            foreach ($globalZones as $zoneData) {
                DeliveryZone::create([
                    'restaurant_id' => $tenant->restaurants()->first()->id ?? $restaurants->first()->id,
                    'tenant_id' => $tenant->id,
                    'zone_name' => 'Global - '.$zoneData['zone_name'],
                    'zone_polygon' => $zoneData['zone_polygon'],
                    'delivery_fee' => $zoneData['delivery_fee'] * 0.9, // Slightly lower for global zones
                    'minimum_order_amount' => $zoneData['minimum_order_amount'] * 0.8,
                    'estimated_delivery_time' => $zoneData['estimated_delivery_time'] + 5,
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('Delivery zones seeded successfully!');
        $this->command->info('Created zones for '.$restaurants->count().' restaurants');
        $this->command->info('Total zones created: '.DeliveryZone::count());
    }
}
