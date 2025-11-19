<?php

namespace Database\Seeders;

use App\Models\State;
use File;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(storage_path('app/private/json/states.json'));
        $states = json_decode($json, true);

        foreach ($states as $state) {
            State::updateOrCreate(
                ['id' => $state['id']],
                [
                    'country_id' => $state['country_id'],
                    'name' => $state['name'],
                    'iso_code' => $state['state_code'] ?? null,
                ]
            );
        }
    }
}
