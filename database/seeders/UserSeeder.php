<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin user
        $superAdminPhone = '98765'.rand(10000, 99999);
        User::updateOrCreate(
            [
                'email' => 'superadmin@hungerhop.com',
            ],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'phone' => $superAdminPhone,
                'role' => 'super_admin',
                'status' => 'active',
                'password' => Hash::make('Admin@123'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✓ Super Admin created: superadmin@hungerhop.com / Admin@123');
    }
}
