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
            'max_grade' => 'required|integer|min:1|max:1000',
            'grade'     => 'nullable|integer|min:0|lte:max_grade',
            'feedback'  => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'grade.lte'       => 'الدرجة لا يمكن أن تكون أكبر من الدرجة القصوى.',
            'max_grade.min'   => 'الدرجة القصوى يجب أن تكون 1 على الأقل.',
        ];
    }
}
