<?php

namespace Database\Seeders;

use App\Models\DeliveryPartner;
use App\Models\DeliveryPartnerDocument;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DeliveryPartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates delivery partner users with partner records and documents
     */
    public function run(): void
    {
        $now = Carbon::now();

        $partners = [
            [
                'email' => 'delivery1@hungerhop.com',
                'first_name' => 'Ramesh',
                'last_name' => 'Kumar',
                'phone' => '9876600001',
                'partner' => [
                    'vehicle_type' => 'motorcycle',
                    'vehicle_number' => 'KA01AB1234',
                    'license_number' => 'KA0120210012345',
                    'current_latitude' => 12.9716,
                    'current_longitude' => 77.5946,
                    'is_available' => true,
                    'is_online' => true,
                    'total_deliveries' => 250,
                    'total_earnings' => 37500.00,
                    'average_rating' => 4.5,
                    'total_reviews' => 180,
                    'commission_percentage' => 15.00,
                    'status' => 'approved',
                ],
            ],
            [
                'email' => 'delivery2@hungerhop.com',
                'first_name' => 'Sunil',
                'last_name' => 'Yadav',
                'phone' => '9876600002',
                'partner' => [
                    'vehicle_type' => 'motorcycle',
                    'vehicle_number' => 'KA01CD5678',
                    'license_number' => 'KA0120200054321',
                    'current_latitude' => 12.9352,
                    'current_longitude' => 77.6245,
                    'is_available' => true,
                    'is_online' => true,
                    'total_deliveries' => 180,
                    'total_earnings' => 27000.00,
                    'average_rating' => 4.3,
                    'total_reviews' => 120,
                    'commission_percentage' => 15.00,
                    'status' => 'approved',
                ],
            ],
            [
                'email' => 'delivery3@hungerhop.com',
                'first_name' => 'Manoj',
                'last_name' => 'Singh',
                'phone' => '9876600003',
                'partner' => [
                    'vehicle_type' => 'car',
                    'vehicle_number' => 'KA02EF9012',
                    'license_number' => 'KA0220190098765',
                    'current_latitude' => 12.9784,
                    'current_longitude' => 77.6408,
                    'is_available' => false,
                    'is_online' => false,
                    'total_deliveries' => 320,
                    'total_earnings' => 48000.00,
                    'average_rating' => 4.7,
                    'total_reviews' => 250,
                    'commission_percentage' => 12.00,
                    'status' => 'approved',
                ],
            ],
            [
                'email' => 'delivery4@hungerhop.com',
                'first_name' => 'Praveen',
                'last_name' => 'Gowda',
                'phone' => '9876600004',
                'partner' => [
                    'vehicle_type' => 'bicycle',
                    'vehicle_number' => 'N/A',
                    'license_number' => 'N/A',
                    'current_latitude' => 12.9308,
                    'current_longitude' => 77.5838,
                    'is_available' => true,
                    'is_online' => true,
                    'total_deliveries' => 90,
                    'total_earnings' => 9000.00,
                    'average_rating' => 4.2,
                    'total_reviews' => 60,
                    'commission_percentage' => 18.00,
                    'status' => 'approved',
                ],
            ],
            [
                'email' => 'delivery5@hungerhop.com',
                'first_name' => 'Kiran',
                'last_name' => 'Reddy',
                'phone' => '9876600005',
                'partner' => [
                    'vehicle_type' => 'motorcycle',
                    'vehicle_number' => 'KA03GH3456',
                    'license_number' => 'KA0320220045678',
                    'current_latitude' => 12.9116,
                    'current_longitude' => 77.6474,
                    'is_available' => true,
                    'is_online' => true,
                    'total_deliveries' => 150,
                    'total_earnings' => 22500.00,
                    'average_rating' => 4.4,
                    'total_reviews' => 100,
                    'commission_percentage' => 15.00,
                    'status' => 'approved',
                ],
            ],
        ];

        // Get super admin for approval
        $superAdmin = User::where('role', 'super_admin')->first();

        $partnerCount = 0;

        foreach ($partners as $partnerData) {
            // Create User
            $user = User::updateOrCreate(
                ['email' => $partnerData['email']],
                [
                    'first_name' => $partnerData['first_name'],
                    'last_name' => $partnerData['last_name'],
                    'phone' => $partnerData['phone'],
                    'role' => 'delivery_partner',
                    'status' => 'active',
                    'password' => Hash::make('Admin@123'),
                    'email_verified_at' => now(),
                ]
            );

            // Create Delivery Partner Record
            $partner = DeliveryPartner::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'vehicle_type' => $partnerData['partner']['vehicle_type'],
                    'vehicle_number' => $partnerData['partner']['vehicle_number'],
                    'license_number' => $partnerData['partner']['license_number'],
                    'current_latitude' => $partnerData['partner']['current_latitude'],
                    'current_longitude' => $partnerData['partner']['current_longitude'],
                    'is_available' => $partnerData['partner']['is_available'],
                    'is_online' => $partnerData['partner']['is_online'],
                    'total_deliveries' => $partnerData['partner']['total_deliveries'],
                    'total_earnings' => $partnerData['partner']['total_earnings'],
                    'average_rating' => $partnerData['partner']['average_rating'],
                    'total_reviews' => $partnerData['partner']['total_reviews'],
                    'commission_percentage' => $partnerData['partner']['commission_percentage'],
                    'status' => $partnerData['partner']['status'],
                    'approved_at' => $now->copy()->subDays(rand(10, 60)),
                    'approved_by' => $superAdmin ? $superAdmin->id : null,
                    'last_location_update' => $now->copy()->subMinutes(rand(1, 30)),
                ]
            );

            // Create Documents for Partner
            $documentTypes = ['id_proof', 'driving_license', 'rc', 'address_proof'];
            foreach ($documentTypes as $docType) {
                DeliveryPartnerDocument::updateOrCreate(
                    [
                        'partner_id' => $partner->id,
                        'document_type' => $docType,
                    ],
                    [
                        'document_path' => "documents/delivery/{$partner->id}/{$docType}.pdf",
                        'document_name' => ucfirst(str_replace('_', ' ', $docType)),
                        'file_size' => rand(100000, 500000),
                        'mime_type' => 'application/pdf',
                        'status' => 'approved',
                        'uploaded_at' => $now->copy()->subDays(rand(30, 90)),
                        'reviewed_at' => $now->copy()->subDays(rand(10, 30)),
                        'reviewed_by' => $superAdmin ? $superAdmin->id : null,
                    ]
                );
            }

            $partnerCount++;
        }

        $this->command->info("✓ Created {$partnerCount} Delivery Partners with Documents");
        $this->command->info('  - All delivery partner passwords: Admin@123');
    }
}
