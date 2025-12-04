<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            [
                'id' => 1,
                'country_id' => 1,
                'name' => 'Andhra Pradesh',
                'state_code' => 'AP',
            ],
            [
                'id' => 2,
                'country_id' => 1,
                'name' => 'Karnataka',
                'state_code' => 'KA',
            ],
            [
                'id' => 3,
                'country_id' => 1,
                'name' => 'Tamil Nadu',
                'state_code' => 'TN',
            ],
            [
                'id' => 4,
                'country_id' => 1,
                'name' => 'Maharashtra',
                'state_code' => 'MH',
            ],
            [
                'id' => 5,
                'country_id' => 1,
                'name' => 'Kerala',
                'state_code' => 'KL',
            ],
            [
                'id' => 6,
                'country_id' => 1,
                'name' => 'Delhi',
                'state_code' => 'DL',
            ],
            [
                'id' => 7,
                'country_id' => 1,
                'name' => 'Gujarat',
                'state_code' => 'GJ',
            ],
            [
                'id' => 8,
                'country_id' => 1,
                'name' => 'Rajasthan',
                'state_code' => 'RJ',
            ],
            [
                'id' => 9,
                'country_id' => 1,
                'name' => 'Uttar Pradesh',
                'state_code' => 'UP',
            ],
            [
                'id' => 10,
                'country_id' => 1,
                'name' => 'West Bengal',
                'state_code' => 'WB',
            ],
        ];

        foreach ($states as $state) {
            State::updateOrCreate(
                ['id' => $state['id']],
                [
                    'country_id' => $state['country_id'],
                    'name' => $state['name'],
                    'iso_code' => $state['state_code'] ?? null,
                ],
            );
        }
    }
}
