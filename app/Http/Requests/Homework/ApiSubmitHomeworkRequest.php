<?php

namespace App\Http\Requests\Homework;

use Illuminate\Foundation\Http\FormRequest;

/**
 * API student submission request. The "at least one of content/file/file_url"
 * check is intentionally NOT enforced here — the controller handles that case
 * so it can return the existing custom JSON 422 body.
 */
class ApiSubmitHomeworkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content'  => 'nullable|string|max:3000',
            'file'     => 'nullable|file|max:20480',
            'file_url' => 'nullable|string|max:2048',
        ];
    }
}
