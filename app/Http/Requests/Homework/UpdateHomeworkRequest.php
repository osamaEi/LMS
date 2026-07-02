<?php

namespace App\Http\Requests\Homework;

/**
 * Update keeps the existing subject/program binding, so those fields are not
 * required (and are ignored by the controller). Everything else mirrors store.
 */
class UpdateHomeworkRequest extends StoreHomeworkRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        unset($rules['subject_id'], $rules['program_id']);

        return $rules;
    }
}
