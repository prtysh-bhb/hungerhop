<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreMenuItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->tenant_id;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Basic required fields
            'item_name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0|max:99999.99',
            'menu_category_id' => 'required|exists:menu_categories,id',
            // Optional fields (only those present in DB)
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_vegetarian' => 'nullable|boolean',
            'is_vegan' => 'nullable|boolean',
            'is_gluten_free' => 'nullable|boolean',
            'ingredients' => 'nullable|string|max:1000',
            'allergens' => 'nullable|string|max:500',
            'preparation_time' => 'nullable|integer|min:1|max:240',
            'is_available' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'item_name.required' => 'Menu item name is required.',
            'item_name.max' => 'Menu item name cannot exceed 255 characters.',
            'base_price.required' => 'Base price is required.',
            'base_price.numeric' => 'Base price must be a valid number.',
            'base_price.min' => 'Base price cannot be negative.',
            'base_price.max' => 'Base price cannot exceed 99,999.99.',
            'menu_category_id.required' => 'Category selection is required.',
            'menu_category_id.exists' => 'Selected category does not exist.',
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'Image must be in JPEG, PNG, JPG, or GIF format.',
            'image.max' => 'Image size cannot exceed 2MB.',
            'preparation_time.min' => 'Preparation time must be at least 1 minute.',
            'preparation_time.max' => 'Preparation time cannot exceed 240 minutes (4 hours).',
            'available_until.after' => 'Available until time must be after available from time.',
            'variations.json' => 'Variations must be in valid JSON format.',
            'customizations.json' => 'Customizations must be in valid JSON format.',
            'availability_schedule.json' => 'Availability schedule must be in valid JSON format.',
        ];
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        // Convert string boolean values to actual booleans
        $booleanFields = [
            'is_vegetarian', 'is_vegan', 'is_gluten_free',
            'is_available', 'is_popular', 'track_inventory', 'has_variations',
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $value = $this->input($field);
                $this->merge([
                    $field => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                ]);
            }
        }

        // Convert empty strings to null for optional fields
        $nullableFields = [
            'description', 'ingredients', 'allergens',
            'meta_title', 'meta_description', 'meta_keywords', 'tags',
        ];

        foreach ($nullableFields as $field) {
            if ($this->has($field) && $this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }
    }
}
