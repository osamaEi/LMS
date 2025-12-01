<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class InitiateNafathRequest extends FormRequest
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
            'national_id' => ['required', 'string', 'size:10', 'regex:/^[0-9]{10}$/'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'national_id.required' => 'National ID is required',
            'national_id.size' => 'National ID must be exactly 10 digits',
            'national_id.regex' => 'National ID must contain only numbers',
        ];
    }
}
