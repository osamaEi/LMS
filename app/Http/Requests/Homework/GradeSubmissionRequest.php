<?php

namespace App\Http\Requests\Homework;

use Illuminate\Foundation\Http\FormRequest;

class GradeSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'grade'    => 'nullable|integer|min:0|max:100',
            'feedback' => 'nullable|string|max:500',
        ];
    }
}
