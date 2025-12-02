<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompleteProfileRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'national_id' => ['required', 'string', 'regex:/^[0-9]{10}$/', 'unique:users,national_id,' . $userId],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $userId],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9]{10,20}$/', 'max:20', 'unique:users,phone,' . $userId],
            'type' => ['required', 'in:diploma,training'],
            'program_id' => ['required', 'exists:programs,id'],
            'date_of_register' => ['nullable', 'date'],
            'is_terms' => ['required', 'accepted'],
            'is_confirm_user' => ['required', 'accepted'],

            // File uploads
            'national_id_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'certificate_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'payment_billing_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'national_id.regex' => 'National ID must be exactly 10 digits.',
            'phone.regex' => 'Phone number must be between 10 and 20 digits.',
            'is_terms.accepted' => 'You must accept the terms and conditions.',
            'is_confirm_user.accepted' => 'You must confirm your information.',
            'date_of_birth.before' => 'Date of birth must be before today.',
            '*.mimes' => 'File must be a PDF, JPG, JPEG, or PNG.',
            '*.max' => 'File size must not exceed 5MB.',
        ];
    }
}
