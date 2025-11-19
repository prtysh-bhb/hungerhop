<?php

namespace App\Http\Requests\Guest;

use App\Enums\VehicleTypeEnums;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class DeliveryPartnerRegistrationRequest extends FormRequest
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
                'max:15',
                'regex:/^[a-zA-Z\s]+$/', // Only letters and spaces
            ],
            'last_name' => [
                'required',
                'string',
                'min:2',
                'max:15',
                'regex:/^[a-zA-Z\s]+$/', // Only letters and spaces
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com)$/', // Must end with .com
            ],
            'phone' => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^[0-9]{10,15}$/', // 10 to 15 digits
                'unique:users,phone',
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
            'vehicle_type' => [
                'required',
                'string',
                'in:'.implode(',', array_column(VehicleTypeEnums::cases(), 'value')),
            ],
            'vehicle_number' => [
                'required',
                'string',
                'min:6',
                'max:15',
                'regex:/^[A-Z]{2}[\s-]?[0-9]{1,2}[\s-]?[A-Z]{1,2}[\s-]?[0-9]{1,4}$/', // Indian vehicle number format
            ],
            'license_number' => [
                'required',
                'string',
                'min:15',
                'max:20',
                'regex:/^[A-Z0-9]{15,20}$/', // 15 to 20 alphanumeric characters
            ],
            'profile_image' => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,jpg,png',
                'max:2048', // Max 2MB
            ],
            'current_latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],
            'current_longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],
            'is_available' => 'nullable|boolean',
            'is_online' => 'nullable|boolean',
            'document_type' => 'required|in:id_proof,driving_license,rc,address_proof,bank_passbook',
            'document_file' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // Max 5MB
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
            'phone.regex' => 'The phone number must be between 10 to 15 digits.',
            'phone.unique' => 'This phone number is already registered.',

            'password.required' => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password_confirmation.required' => 'The password confirmation field is required.',

            'vehicle_type.required' => 'Please select a vehicle type.',
            'vehicle_type.in' => 'Please select a valid vehicle type.',

            'vehicle_number.required' => 'The vehicle number field is required.',
            'vehicle_number.regex' => 'Please enter a valid Indian vehicle number (e.g., MH 12 AB 1234).',
            'vehicle_number.min' => 'Vehicle number must be at least 6 characters.',
            'vehicle_number.max' => 'Vehicle number may not be greater than 15 characters.',

            'license_number.required' => 'The license number field is required.',
            'license_number.min' => 'License number must be at least 16 characters.',
            'license_number.max' => 'License number may not be greater than 20 characters.',
            'license_number.regex' => 'License number must be between 16 to 20 alphanumeric characters.',

            'profile_image.image' => 'Profile photo must be an image file.',
            'profile_image.mimes' => 'Profile photo must be a file of type: jpeg, jpg, png.',
            'profile_image.max' => 'Profile photo size may not be greater than 2MB.',

            'current_latitude.required' => 'Current latitude is required.',
            'current_latitude.between' => 'Latitude must be between -90 and 90.',

            'current_longitude.required' => 'Current longitude is required.',
            'current_longitude.between' => 'Longitude must be between -180 and 180.',

            'document_type.required' => 'Please select a document type.',
            'document_type.in' => 'Please select a valid document type.',

            'document_file.required' => 'Please upload a document file.',
            'document_file.mimes' => 'Document must be a file of type: jpeg, png, jpg, pdf.',
            'document_file.max' => 'Document file size may not be greater than 5MB.',
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
            'vehicle_type' => 'vehicle type',
            'vehicle_number' => 'vehicle number',
            'license_number' => 'license number',
            'current_latitude' => 'latitude',
            'current_longitude' => 'longitude',
            'profile_image' => 'profile photo',
            'document_type' => 'document type',
            'document_file' => 'document file',
        ];
    }
}
