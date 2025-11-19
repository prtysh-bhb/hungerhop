<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            UserSeeder::class,
            MenuTemplateSeeder::class,
            MenuCategorySeeder::class,
        ]);

        // Tenant::factory()
        //     ->count(3)
        //     ->create()
        //     ->each(function ($tenant) {
        //         // Create 5 users for each tenant
        //         User::factory()->count(5)->create(['tenant_id' => $tenant->id]);

        //         // Create 2 restaurants per tenant
        //         Restaurant::factory()->count(2)->create([
        //             'tenant_id' => $tenant->id,
        //             'location_admin_id' => User::factory()->create(['tenant_id' => $tenant->id])->id,
        //         ]);
        //     });
    }
}
