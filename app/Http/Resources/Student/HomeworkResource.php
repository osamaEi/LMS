<?php

namespace App\Http\Resources\Student;

use App\Models\HomeworkSubmission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeworkResource extends JsonResource
{
    protected ?HomeworkSubmission $submission;

    public function __construct($resource, ?HomeworkSubmission $submission = null)
    {
        parent::__construct($resource);
        $this->submission = $submission;
    }

    public function toArray(Request $request): array
    {
        $subject = $this->subject;
        $program = $this->program;

        return [
            'id'             => $this->id,
            'title_ar'       => $this->title_ar,
            'title_en'       => $this->title_en,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'due_date'       => $this->due_date?->format('Y-m-d'),
            'is_overdue'     => $this->due_date && $this->due_date->isPast() && !$this->submission,
            'attachment_url' => $this->file_url,
            'subject' => $subject ? [
                'id'      => $subject->id,
                'name_ar' => $subject->name_ar,
                'name_en' => $subject->name_en,
                'code'    => $subject->code,
            ] : null,
            'program' => $program ? [
                'id'      => $program->id,
                'name_ar' => $program->name_ar,
                'name_en' => $program->name_en,
            ] : null,
            'submission' => $this->submission
                ? (new HomeworkSubmissionResource($this->submission))->toArray($request)
                : null,
        ];
    }
}
