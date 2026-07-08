<?php

namespace App\Http\Requests\Homework;

use Illuminate\Foundation\Http\FormRequest;

class StoreHomeworkRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization is intentionally left open here to preserve the existing
        // (currently disabled) teacher authorization behavior.
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id'     => 'nullable|required_without:program_id|exists:subjects,id',
            'program_id'     => 'nullable|required_without:subject_id|exists:programs,id',
            'class_id'       => 'nullable|required_with:subject_id|exists:program_classes,id',
            'title_ar'       => 'nullable|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'due_date'       => 'nullable|date',
            'file'           => 'nullable|file|max:20480',
        ];
    }
}
