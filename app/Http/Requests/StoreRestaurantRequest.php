<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRestaurantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $user = auth()->user();
        $rules = [
            'restaurant_name' => [
                'required',
                'string',
                'max:255',
            ],
            'phone' => 'required|string|max:15|regex:/^[+]?[0-9\s\-\(\)]+$/',
            'email' => 'required|email|max:255',
            'postal_code' => 'required|string|max:10',
            'location_admin_id' => 'required|integer|exists:users,id',
            'state_id' => 'required|integer|exists:states,id',
            'city_id' => 'required|integer|exists:cities,id',
            'address' => 'required|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'minimum_order_amount' => 'required|numeric|min:0|max:10000',
            'base_delivery_fee' => 'required|numeric|min:0|max:1000',
            'delivery_radius_km' => 'required|numeric|min:1|max:50',
            'estimated_delivery_time' => 'required|integer|min:10|max:120',
            'tax_percentage' => 'required|numeric|min:0|max:30',
            'restaurant_commission_percentage' => 'required|numeric|min:0|max:50',
            'is_open' => 'required|boolean',
            'image_url' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048',
            ],
            'cover_image_url' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120',
            ],
        ];

        // Role-specific validation
        if ($user->role === 'super_admin') {
            $rules['tenant_selection'] = 'required|in:existing,new';
            $rules['tenant_id'] = 'required_if:tenant_selection,existing|nullable|exists:tenants,id';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'tenant_selection.required' => 'Please select whether to add restaurant to existing tenant or create new.',
            'tenant_id.required_if' => 'Please select a tenant when adding to existing tenant.',
            'restaurant_name.unique' => 'A restaurant with this name already exists.',
            'email.unique' => 'This email address is already registered with another restaurant.',
            'phone.regex' => 'Please enter a valid phone number.',
            'latitude.between' => 'Invalid latitude value.',
            'longitude.between' => 'Invalid longitude value.',
            'delivery_radius_km.max' => 'Delivery radius cannot exceed 50 km.',
            'image_url.dimensions' => 'Restaurant image must be at least 300x300 pixels and not larger than 2000x2000 pixels.',
            'cover_image_url.dimensions' => 'Cover image must be at least 800x400 pixels and not larger than 3000x2000 pixels.',
        ];
    }

    protected function prepareForValidation()
    {
        $user = auth()->user();

        // Auto-assign tenant for tenant_admin
        if ($user->role === 'tenant_admin') {
            $this->merge([
                'tenant_id' => $user->tenant_id,
                'tenant_selection' => 'existing',
            ]);
        }

        // Set user_id
        $this->merge(['user_id' => $user->id]);

        // Clean phone number
        if ($this->has('phone')) {
            $this->merge([
                'phone' => preg_replace('/[^+0-9]/', '', $this->phone),
            ]);
        }
    }
}
