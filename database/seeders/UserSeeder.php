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
        User::updateOrCreate(
            ['email' => 'superadmin@hungerhop.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'phone' => '9876543210',
                'role' => 'super_admin',
                'status' => 'active',
                'password' => Hash::make('SuperAdmin@123'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: superadmin@hungerhop.com');
        $this->command->info('Password: SuperAdmin@123');
    }
}
