<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CustomerProfile;
use App\Models\CustomerAddress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates customer users with profiles and addresses
     */
    public function run(): void
    {
        $customers = [
            [
                'email' => 'customer1@hungerhop.com',
                'first_name' => 'Arun',
                'last_name' => 'Kumar',
                'phone' => '9876500001',
                'profile' => [
                    'date_of_birth' => '1990-05-15',
                    'gender' => 'male',
                    'total_orders' => 25,
                    'total_spent' => 12500.00,
                    'loyalty_points' => 1250,
                    'referral_code' => 'ARUN1990',
                ],
                'addresses' => [
                    [
                        'address_type' => 'home',
                        'address_line1' => '45, 1st Cross, Koramangala',
                        'address_line2' => 'Near Forum Mall',
                        'landmark' => 'Opposite BDA Complex',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'postal_code' => '560034',
                        'latitude' => 12.9352,
                        'longitude' => 77.6245,
                        'is_default' => true,
                    ],
                    [
                        'address_type' => 'work',
                        'address_line1' => '100, Tech Park, Whitefield',
                        'address_line2' => 'Block A, 5th Floor',
                        'landmark' => 'Near ITPL',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'postal_code' => '560066',
                        'latitude' => 12.9698,
                        'longitude' => 77.7500,
                        'is_default' => false,
                    ],
                ],
            ],
            [
                'email' => 'customer2@hungerhop.com',
                'first_name' => 'Priya',
                'last_name' => 'Sharma',
                'phone' => '9876500002',
                'profile' => [
                    'date_of_birth' => '1995-08-20',
                    'gender' => 'female',
                    'total_orders' => 40,
                    'total_spent' => 22000.00,
                    'loyalty_points' => 2200,
                    'referral_code' => 'PRIYA1995',
                ],
                'addresses' => [
                    [
                        'address_type' => 'home',
                        'address_line1' => '78, 4th Main, HSR Layout',
                        'address_line2' => 'Sector 2',
                        'landmark' => 'Near BDA Park',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'postal_code' => '560102',
                        'latitude' => 12.9116,
                        'longitude' => 77.6474,
                        'is_default' => true,
                    ],
                ],
            ],
            [
                'email' => 'customer3@hungerhop.com',
                'first_name' => 'Rajesh',
                'last_name' => 'Nair',
                'phone' => '9876500003',
                'profile' => [
                    'date_of_birth' => '1988-12-10',
                    'gender' => 'male',
                    'total_orders' => 60,
                    'total_spent' => 35000.00,
                    'loyalty_points' => 3500,
                    'referral_code' => 'RAJESH1988',
                ],
                'addresses' => [
                    [
                        'address_type' => 'home',
                        'address_line1' => '22, 2nd Stage, Indiranagar',
                        'address_line2' => '100 Feet Road',
                        'landmark' => 'Near Indiranagar Metro',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'postal_code' => '560038',
                        'latitude' => 12.9784,
                        'longitude' => 77.6408,
                        'is_default' => true,
                    ],
                ],
            ],
            [
                'email' => 'customer4@hungerhop.com',
                'first_name' => 'Sneha',
                'last_name' => 'Reddy',
                'phone' => '9876500004',
                'profile' => [
                    'date_of_birth' => '1992-03-25',
                    'gender' => 'female',
                    'total_orders' => 15,
                    'total_spent' => 8500.00,
                    'loyalty_points' => 850,
                    'referral_code' => 'SNEHA1992',
                ],
                'addresses' => [
                    [
                        'address_type' => 'home',
                        'address_line1' => '55, 5th Block, Jayanagar',
                        'address_line2' => '4th T Block',
                        'landmark' => 'Near Jain Temple',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'postal_code' => '560041',
                        'latitude' => 12.9308,
                        'longitude' => 77.5838,
                        'is_default' => true,
                    ],
                ],
            ],
            [
                'email' => 'customer5@hungerhop.com',
                'first_name' => 'Vikram',
                'last_name' => 'Rao',
                'phone' => '9876500005',
                'profile' => [
                    'date_of_birth' => '1985-07-08',
                    'gender' => 'male',
                    'total_orders' => 80,
                    'total_spent' => 48000.00,
                    'loyalty_points' => 4800,
                    'referral_code' => 'VIKRAM1985',
                ],
                'addresses' => [
                    [
                        'address_type' => 'home',
                        'address_line1' => '33, MG Road',
                        'address_line2' => 'Trinity Circle',
                        'landmark' => 'Near MG Road Metro',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'postal_code' => '560001',
                        'latitude' => 12.9716,
                        'longitude' => 77.5946,
                        'is_default' => true,
                    ],
                    [
                        'address_type' => 'work',
                        'address_line1' => '200, Electronic City Phase 1',
                        'address_line2' => 'Infosys Campus',
                        'landmark' => 'Near Infosys Gate',
                        'city' => 'Bangalore',
                        'state' => 'Karnataka',
                        'postal_code' => '560100',
                        'latitude' => 12.8399,
                        'longitude' => 77.6770,
                        'is_default' => false,
                    ],
                ],
            ],
        ];

        $customerCount = 0;

        foreach ($customers as $custData) {
            // Create User
            $user = User::updateOrCreate(
                ['email' => $custData['email']],
                [
                    'first_name' => $custData['first_name'],
                    'last_name' => $custData['last_name'],
                    'phone' => $custData['phone'],
                    'role' => 'customer',
                    'status' => 'active',
                    'password' => Hash::make('Admin@123'),
                    'email_verified_at' => now(),
                ]
            );

            // Create Customer Profile
            $profile = CustomerProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'date_of_birth' => $custData['profile']['date_of_birth'],
                    'gender' => $custData['profile']['gender'],
                    'total_orders' => $custData['profile']['total_orders'],
                    'total_spent' => $custData['profile']['total_spent'],
                    'loyalty_points' => $custData['profile']['loyalty_points'],
                    'referral_code' => $custData['profile']['referral_code'],
                ]
            );

            // Create Addresses
            foreach ($custData['addresses'] as $addrData) {
                CustomerAddress::updateOrCreate(
                    [
                        'customer_id' => $profile->id,
                        'address_line1' => $addrData['address_line1'],
                    ],
                    [
                        'address_type' => $addrData['address_type'],
                        'address_line2' => $addrData['address_line2'],
                        'landmark' => $addrData['landmark'],
                        'city' => $addrData['city'],
                        'state' => $addrData['state'],
                        'postal_code' => $addrData['postal_code'],
                        'latitude' => $addrData['latitude'],
                        'longitude' => $addrData['longitude'],
                        'is_default' => $addrData['is_default'],
                    ]
                );
            }

            $customerCount++;
        }

        $this->command->info("✓ Created {$customerCount} Customers with Profiles and Addresses");
        $this->command->info('  - All customer passwords: Admin@123');
    }
}
