<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectWithProgressResource extends JsonResource
{
    /**
     * Pass the student's enrollment keyed by subject_id via additional().
     * ->additional(['enrollments' => $enrollments])
     */
    public function toArray(Request $request): array
    {
        $enrollment = $this->additional['enrollments'][$this->id] ?? null;

        // Total hours from sessions
        $sessions    = $this->whenLoaded('sessions', fn() => $this->sessions, collect());
        $totalMinutes = $sessions->sum('duration_minutes');
        $totalHours   = $totalMinutes > 0 ? round($totalMinutes / 60, 1) : null;

        // Dominant session type (recorded / live)
        $sessionType = null;
        if ($sessions->isNotEmpty()) {
            $typeCounts  = $sessions->countBy('type');
            $sessionType = $typeCounts->sortDesc()->keys()->first();
        }

        // Progress
        $progress         = $enrollment ? (int) ($enrollment->progress ?? 0) : 0;
        $remainingMinutes = null;
        if ($totalMinutes > 0 && $progress < 100) {
            $remainingMinutes = (int) round($totalMinutes * (1 - $progress / 100));
        }

        $data = [
            'id'             => $this->id,
            'name'           => $this->name,
            'name_ar'        => $this->name_ar ?: null,
            'name_en'        => $this->name_en ?: null,
            'description'    => $this->description,
            'code'           => $this->code,
            'color'          => $this->color,
            'banner_photo'   => $this->banner_photo ? asset('storage/' . $this->banner_photo) : null,
            'session_type'   => $sessionType,
            'sessions_count' => $sessions instanceof \Illuminate\Database\Eloquent\Collection
                ? $sessions->count()
                : ($this->sessions_count ?? 0),
            'total_hours'    => $totalHours,
            'teacher'        => $this->teacher_id && $this->relationLoaded('teacher') && $this->teacher
                ? ['id' => $this->teacher->id, 'name' => $this->teacher->name]
                : null,
            'enrollment'     => $enrollment ? array_filter([
                'status'            => $enrollment->status,
                'progress'          => $progress,
                'remaining_minutes' => $remainingMinutes,
                'enrolled_at'       => $enrollment->enrolled_at?->toIso8601String(),
                'final_grade'       => $enrollment->final_grade,
                'grade_letter'      => $enrollment->grade_letter,
                'completion_date'   => $enrollment->completion_date?->format('Y-m-d'),
            ], fn($v) => $v !== null) : null,
        ];

        return array_filter($data, fn($v) => $v !== null);
    }
}
