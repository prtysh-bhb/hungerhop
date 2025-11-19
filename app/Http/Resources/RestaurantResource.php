<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'restaurant_name' => $this->restaurant_name,
            'slug' => $this->slug,
            'description' => $this->description,
            'cuisine_type' => $this->cuisine_type,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'phone' => $this->phone,
            'email' => $this->email,
            'website_url' => $this->website_url,
            'image_url' => $this->full_image_url,
            'cover_image_url' => $this->full_cover_image_url,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'delivery_radius_km' => $this->delivery_radius_km,
            'minimum_order_amount' => $this->minimum_order_amount,
            'base_delivery_fee' => $this->base_delivery_fee,
            'estimated_delivery_time' => $this->estimated_delivery_time,
            'tax_percentage' => $this->tax_percentage,
            'restaurant_commission_percentage' => $this->restaurant_commission_percentage,
            'is_open' => $this->is_open,
            'accepts_orders' => $this->accepts_orders,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'average_rating' => $this->average_rating,
            'total_reviews' => $this->total_reviews,
            'total_orders' => $this->total_orders,
            'is_featured' => $this->is_featured,
            'setup_completed' => $this->setup_completed,
            'onboarding_step' => $this->onboarding_step,
            'approved_at' => $this->approved_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

            // Relationships
            'location_admin' => $this->whenLoaded('locationAdmin', function () {
                return [
                    'id' => $this->locationAdmin->id,
                    'name' => $this->locationAdmin->name,
                    'email' => $this->locationAdmin->email,
                ];
            }),

            'approved_by' => $this->whenLoaded('approvedByUser', function () {
                return [
                    'id' => $this->approvedByUser->id,
                    'name' => $this->approvedByUser->name,
                ];
            }),

            'working_hours' => $this->whenLoaded('workingHours'),
            'documents' => $this->whenLoaded('documents'),
        ];
    }
}
