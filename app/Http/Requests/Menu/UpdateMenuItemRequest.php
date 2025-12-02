<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateMenuItemRequest extends FormRequest
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
            // Basic required fields - Name max 50 chars, allows letters, numbers, spaces and special chars
            'item_name' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9\s&\'.\/\-]+$/'],
            'base_price' => 'required|numeric|min:0|max:99999.99',
            'menu_category_id' => 'required|exists:menu_categories,id',
            
            // Optional fields with proper limits
            'description' => 'nullable|string|max:255', // Single-line max 255
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Max 2MB
            'is_vegetarian' => 'nullable|boolean',
            'is_vegan' => 'nullable|boolean',
            'is_gluten_free' => 'nullable|boolean',
            'ingredients' => 'nullable|string|max:500', // Multiline max 500
            'allergens' => 'nullable|string|max:255', // Multiline max 255
            'preparation_time' => 'nullable|integer|min:1|max:240',
            'is_available' => 'nullable|boolean',
            'is_popular' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'item_name.required' => 'Menu item name is required.',
            'item_name.max' => 'Menu item name cannot exceed 50 characters.',
            'item_name.regex' => 'Menu item name can only contain letters, numbers, spaces and special characters (&, \', ., /, -).',
            'base_price.required' => 'Base price is required.',
            'base_price.numeric' => 'Base price must be a valid number.',
            'base_price.min' => 'Base price cannot be negative.',
            'base_price.max' => 'Base price cannot exceed 99,999.99.',
            'menu_category_id.required' => 'Category selection is required.',
            'menu_category_id.exists' => 'Selected category does not exist.',
            'description.max' => 'Description cannot exceed 255 characters.',
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'Image must be in JPEG, PNG, JPG, GIF, or WebP format.',
            'image.max' => 'Image size cannot exceed 2MB.',
            'ingredients.max' => 'Ingredients cannot exceed 500 characters.',
            'allergens.max' => 'Allergens cannot exceed 255 characters.',
            'preparation_time.min' => 'Preparation time must be at least 1 minute.',
            'preparation_time.max' => 'Preparation time cannot exceed 240 minutes (4 hours).',
            'sort_order.max' => 'Sort order cannot exceed 9999.',
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
