<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndiaStateSeeder extends Seeder
{
    public function run(): void
    {
        $india = DB::table('countries')->where('iso_code', 'IN')->orWhere('name', 'India')->first();

        if (! $india) {
            $this->command->error('India country not found in countries table. Please insert it first.');

            return;
        }

        $indiaId = $india->id;

        $states = [
            // States
            ['name' => 'Andhra Pradesh', 'iso' => 'AP'],
            ['name' => 'Arunachal Pradesh', 'iso' => 'AR'],
            ['name' => 'Assam', 'iso' => 'AS'],
            ['name' => 'Bihar', 'iso' => 'BR'],
            ['name' => 'Chhattisgarh', 'iso' => 'CT'],
            ['name' => 'Goa', 'iso' => 'GA'],
            ['name' => 'Gujarat', 'iso' => 'GJ'],
            ['name' => 'Haryana', 'iso' => 'HR'],
            ['name' => 'Himachal Pradesh', 'iso' => 'HP'],
            ['name' => 'Jharkhand', 'iso' => 'JH'],
            ['name' => 'Karnataka', 'iso' => 'KA'],
            ['name' => 'Kerala', 'iso' => 'KL'],
            ['name' => 'Madhya Pradesh', 'iso' => 'MP'],
            ['name' => 'Maharashtra', 'iso' => 'MH'],
            ['name' => 'Manipur', 'iso' => 'MN'],
            ['name' => 'Meghalaya', 'iso' => 'ML'],
            ['name' => 'Mizoram', 'iso' => 'MZ'],
            ['name' => 'Nagaland', 'iso' => 'NL'],
            ['name' => 'Odisha', 'iso' => 'OR'],
            ['name' => 'Punjab', 'iso' => 'PB'],
            ['name' => 'Rajasthan', 'iso' => 'RJ'],
            ['name' => 'Sikkim', 'iso' => 'SK'],
            ['name' => 'Tamil Nadu', 'iso' => 'TN'],
            ['name' => 'Telangana', 'iso' => 'TG'],
            ['name' => 'Tripura', 'iso' => 'TR'],
            ['name' => 'Uttar Pradesh', 'iso' => 'UP'],
            ['name' => 'Uttarakhand', 'iso' => 'UT'],
            ['name' => 'West Bengal', 'iso' => 'WB'],

            // Union Territories
            ['name' => 'Andaman and Nicobar Islands', 'iso' => 'AN'],
            ['name' => 'Chandigarh', 'iso' => 'CH'],
            ['name' => 'Dadra and Nagar Haveli and Daman and Diu', 'iso' => 'DN'],
            ['name' => 'Delhi', 'iso' => 'DL'],
            ['name' => 'Jammu and Kashmir', 'iso' => 'JK'],
            ['name' => 'Ladakh', 'iso' => 'LA'],
            ['name' => 'Lakshadweep', 'iso' => 'LD'],
            ['name' => 'Puducherry', 'iso' => 'PY'],
        ];

        foreach ($states as $s) {
            State::updateOrCreate(
                [
                    'country_id' => $indiaId,
                    'name' => $s['name'],
                ],
                [
                    'country_id' => $indiaId,
                    'name' => $s['name'],
                    'iso_code' => $s['iso'] ?? null,
                ]
            );
        }

        $this->command->info('India states seeded/updated successfully.');
    }
}
