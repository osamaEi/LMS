<?php

namespace App\Http\Requests\Homework;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Web student submission request.
 */
class SubmitHomeworkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'nullable|string|max:3000',
            'file'    => 'nullable|file|max:20480',
        ];
    }

    /**
     * Preserve the original "at least one of content/file" rule and its exact
     * Arabic error message keyed on `content`.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (!$this->filled('content') && !$this->hasFile('file')) {
                    $validator->errors()->add('content', 'يرجى كتابة نص الإجابة أو رفع ملف.');
                }
            },
        ];
    }
}
