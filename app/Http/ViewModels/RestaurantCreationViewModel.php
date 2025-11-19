<?php

namespace App\ViewModels;

use App\Models\State;
use App\Models\User;

class RestaurantCreationViewModel
{
    public function getFormData(): array
    {
        return [
            'states' => $this->getStates(),
            'location_admins' => $this->getLocationAdmins(),
            'cuisine_types' => $this->getCuisineTypes(),
            'delivery_fee_types' => $this->getDeliveryFeeTypes(),
            'time_slots' => $this->getTimeSlots(),
            'validation_rules' => $this->getValidationRules(),
        ];
    }

    private function getStates(): array
    {
        return State::where('country_id', config('app.country_id', 1))
            ->get(['id', 'name'])
            ->toArray();
    }

    private function getLocationAdmins(): array
    {
        return User::where('role', 'location_admin')
            ->where('tenant_id', auth()->user()->tenant_id ?? 1)
            ->get(['id', 'name', 'email'])
            ->toArray();
    }

    private function getCuisineTypes(): array
    {
        return [
            'indian' => 'Indian',
            'chinese' => 'Chinese',
            'italian' => 'Italian',
            'mexican' => 'Mexican',
            'thai' => 'Thai',
            'continental' => 'Continental',
            'fast_food' => 'Fast Food',
            'beverages' => 'Beverages',
            'desserts' => 'Desserts',
            'mediterranean' => 'Mediterranean',
            'japanese' => 'Japanese',
            'korean' => 'Korean',
            'american' => 'American',
            'multi_cuisine' => 'Multi Cuisine',
        ];
    }

    private function getDeliveryFeeTypes(): array
    {
        return [
            'fixed' => 'Fixed Amount',
            'distance_based' => 'Distance Based',
            'free' => 'Free Delivery',
        ];
    }

    private function getTimeSlots(): array
    {
        $slots = [];
        for ($hour = 0; $hour < 24; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 30) {
                $time = sprintf('%02d:%02d', $hour, $minute);
                $slots[$time] = date('g:i A', strtotime($time));
            }
        }

        return $slots;
    }

    private function getValidationRules(): array
    {
        return [
            'image_max_size' => '2MB',
            'cover_image_max_size' => '5MB',
            'max_delivery_radius' => 50,
            'min_delivery_time' => 10,
            'max_delivery_time' => 120,
        ];
    }
}
