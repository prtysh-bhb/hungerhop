<?php

namespace Database\Seeders;

use App\Models\Country;
use File;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(storage_path('app/private/json/countries.json'));
        $countries = json_decode($json, true);

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['id' => $country['id']],
                [
                    'name' => $country['name'],
                    'iso_code' => $country['iso2'] ?? null,
                ]
            );
        }
    }
}
