<?php

namespace Database\Seeders;

use App\Models\City;
use File;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(storage_path('app/private/json/cities.json'));
        $cities = json_decode($json, true);

        foreach ($cities as $city) {
            City::updateOrCreate(
                ['id' => $city['id']],
                [
                    'state_id' => $city['state_id'],
                    'name' => $city['name'],
                ]
            );
        }

    }
}
