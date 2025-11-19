<?php

namespace Database\Seeders;

use App\Models\MenuTemplate;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class MenuTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all tenants to create templates for each
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // Default restaurant menu templates
            $templates = [
                [
                    'tenant_id' => $tenant->id,
                    'template_name' => 'Quick Service Menu',
                    'description' => 'Fast food and quick service restaurant template with essential categories like burgers, pizzas, beverages, and desserts.',
                ],
                [
                    'tenant_id' => $tenant->id,
                    'template_name' => 'Fine Dining Menu',
                    'description' => 'Elegant fine dining template with sophisticated categories including appetizers, soups, main courses, and premium desserts.',
                ],
                [
                    'tenant_id' => $tenant->id,
                    'template_name' => 'Indian Cuisine Menu',
                    'description' => 'Traditional Indian restaurant template featuring starters, curries, biryanis, breads, and traditional sweets.',
                ],
                [
                    'tenant_id' => $tenant->id,
                    'template_name' => 'Cafe & Bistro Menu',
                    'description' => 'Casual dining template perfect for cafes and bistros with coffee, snacks, sandwiches, and light meals.',
                ],
                [
                    'tenant_id' => $tenant->id,
                    'template_name' => 'Pizza & Italian Menu',
                    'description' => 'Italian cuisine focused template with pizzas, pastas, salads, and Italian specialties.',
                ],
                [
                    'tenant_id' => $tenant->id,
                    'template_name' => 'Street Food Menu',
                    'description' => 'Popular street food template featuring chaat, rolls, momos, and other street delicacies.',
                ],
            ];

            foreach ($templates as $templateData) {
                MenuTemplate::updateOrCreate(
                    [
                        'tenant_id' => $templateData['tenant_id'],
                        'template_name' => $templateData['template_name'],
                    ],
                    $templateData
                );
            }
        }

        // If no tenants exist, create some basic templates for the first tenant
        if ($tenants->isEmpty()) {
            $basicTemplates = [
                [
                    'tenant_id' => 1, // Assuming first tenant will have ID 1
                    'template_name' => 'Basic Restaurant Menu',
                    'description' => 'A simple restaurant menu template with essential food categories.',
                ],
                [
                    'tenant_id' => 1,
                    'template_name' => 'Indian Cuisine Menu',
                    'description' => 'Traditional Indian restaurant template with popular Indian food categories.',
                ],
                [
                    'tenant_id' => 1,
                    'template_name' => 'Fast Food Menu',
                    'description' => 'Quick service menu template for fast food restaurants.',
                ],
            ];

            foreach ($basicTemplates as $templateData) {
                MenuTemplate::create($templateData);
            }
        }
    }
}
