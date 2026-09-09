<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
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
            'national_id' => ['required', 'digits:10'],
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
            'password' => ['required', Password::min(8)],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'national_id.required' => 'National ID is required',
            'national_id.digits' => 'National ID must be 10 digits',
            'otp.required' => 'OTP code is required',
            'otp.size' => 'OTP must be 6 digits',
            'otp.regex' => 'OTP must contain only numbers',
            'password.required' => 'Password is required',
        ];
    }
}
