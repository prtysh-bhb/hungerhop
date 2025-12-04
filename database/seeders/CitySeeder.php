<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            [
                'id' => 1,
                'state_id' => 1,
                'name' => 'Hyderabad',
            ],
            [
                'id' => 2,
                'state_id' => 1,
                'name' => 'Visakhapatnam',
            ],
            [
                'id' => 3,
                'state_id' => 2,
                'name' => 'Bangalore',
            ],
            [
                'id' => 4,
                'state_id' => 2,
                'name' => 'Mysore',
            ],
            [
                'id' => 5,
                'state_id' => 2,
                'name' => 'Mangalore',
            ],
            [
                'id' => 6,
                'state_id' => 3,
                'name' => 'Chennai',
            ],
            [
                'id' => 7,
                'state_id' => 3,
                'name' => 'Coimbatore',
            ],
            [
                'id' => 8,
                'state_id' => 4,
                'name' => 'Mumbai',
            ],
            [
                'id' => 9,
                'state_id' => 4,
                'name' => 'Pune',
            ],
            [
                'id' => 10,
                'state_id' => 5,
                'name' => 'Kochi',
            ],
            [
                'id' => 11,
                'state_id' => 5,
                'name' => 'Thiruvananthapuram',
            ],
            [
                'id' => 12,
                'state_id' => 6,
                'name' => 'New Delhi',
            ],
            [
                'id' => 13,
                'state_id' => 7,
                'name' => 'Ahmedabad',
            ],
            [
                'id' => 14,
                'state_id' => 7,
                'name' => 'Surat',
            ],
            [
                'id' => 15,
                'state_id' => 8,
                'name' => 'Jaipur',
            ],
            [
                'id' => 16,
                'state_id' => 9,
                'name' => 'Lucknow',
            ],
            [
                'id' => 17,
                'state_id' => 9,
                'name' => 'Kanpur',
            ],
            [
                'id' => 18,
                'state_id' => 10,
                'name' => 'Kolkata',
            ],
        ];

        foreach ($cities as $city) {
            City::updateOrCreate(
                ['id' => $city['id']],
                [
                    'state_id' => $city['state_id'],
                    'name' => $city['name'],
                ],
            );
        }
    }
}
