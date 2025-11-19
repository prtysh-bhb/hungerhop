<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => [
                'required',
                'string',
                'min:2',
                'max:14',
                'regex:/^[a-zA-Z\s]+$/', // Only letters and spaces
            ],
            'last_name' => [
                'required',
                'string',
                'min:2',
                'max:14',
                'regex:/^[a-zA-Z\s]+$/', // Only letters and spaces
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com)$/', // Must end with .com
            ],
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^[0-9]{10,15}$/', // 10 to 15 digits, no special characters
                'unique:users',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'password_confirmation' => 'required|string',
            'terms' => 'required|accepted',
            'role' => 'required|string|in:super_admin,tenant_admin,location_admin,restaurant_staff,customer,delivery_partner',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'The first name field is required.',
            'first_name.min' => 'The first name must be at least 2 characters.',
            'first_name.max' => 'The first name may not be greater than 15 characters.',
            'first_name.regex' => 'The first name may only contain letters and spaces.',
            'last_name.required' => 'The last name field is required.',
            'last_name.min' => 'The last name must be at least 2 characters.',
            'last_name.max' => 'The last name may not be greater than 15 characters.',
            'last_name.regex' => 'The last name may only contain letters and spaces.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.regex' => 'The email must end with .com domain.',
            'phone.required' => 'The phone number field is required.',
            'phone.min' => 'The phone number must be at least 10 digits.',
            'phone.max' => 'The phone number may not be greater than 15 digits.',
            'phone.regex' => 'The phone number must be between 10 to 15 digits without any special characters.',
            'phone.unique' => 'This phone number is already registered.',
            'password.required' => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password_confirmation.required' => 'The password confirmation field is required.',
            'terms.required' => 'You must accept the terms and conditions.',
            'terms.accepted' => 'You must accept the terms and conditions.',
            'role.in' => 'Please select a valid role.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'phone' => 'phone number',
            'password_confirmation' => 'password confirmation',
        ];
    }
}
