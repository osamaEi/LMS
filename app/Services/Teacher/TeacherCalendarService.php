<?php

namespace App\Services\Teacher;

use App\Models\Quiz;
use App\Models\Session;
use App\Models\User;
use Illuminate\Support\Collection;

class TeacherCalendarService
{
    public function sessions(User $teacher): Collection
    {
        return Session::where('teacher_id', $teacher->id)
            ->with([
                'teacher',
                'subject.program',
                'subject.term.program',
                'program',
                'programClass',
                'subject.programClass',
                'subject.term.programClass',
            ])
            ->orderBy('scheduled_at')
            ->get()
            ->map(fn (Session $session) => [
                'id' => $session->id,
                'title' => $session->title_ar ?: ($session->subject->name_ar ?? $session->program->name_ar ?? 'جلسة'),
                'subject_name' => $session->subject->name_ar ?? '',
                'program_name' => $session->program->name_ar ?? $session->subject?->program?->name_ar ?? '',
                'scheduled_at' => $session->scheduled_at?->toIso8601String(),
                'duration_minutes' => $session->duration_minutes ?? 60,
                'type' => $session->type ?? '',
                'status' => (string) ($session->status ?? ''),
                'session_number' => $session->session_number,
                'class_name' => $session->programClass->name
                    ?? $session->subject?->programClass?->name
                    ?? $session->subject?->term?->programClass?->name
                    ?? '',
                'zoom_join_url' => $session->zoom_join_url,
                'zoom_start_url' => $session->zoom_start_url,
                'subject_id' => $session->subject_id,
                'program_id' => $session->program_id,
            ])
            ->filter(fn (array $session) => $session['scheduled_at'])
            ->values();
    }

    public function quizzes(User $teacher): Collection
    {
        return Quiz::where('created_by', $teacher->id)
            ->whereNotNull('starts_at')
            ->with('subject:id,name_ar')
            ->withCount(['questions', 'attempts'])
            ->get()
            ->map(fn (Quiz $quiz) => [
                'id' => $quiz->id,
                'subject_id' => $quiz->subject_id,
                'title' => $quiz->title_ar,
                'subject_name' => $quiz->subject->name_ar ?? '',
                'type' => $quiz->type,
                'type_label' => $quiz->type_label,
                'starts_at' => $quiz->starts_at->toIso8601String(),
                'ends_at' => $quiz->ends_at?->toIso8601String(),
                'total_marks' => $quiz->total_marks,
                'duration' => $quiz->duration_minutes,
                'questions' => $quiz->questions_count,
                'attempts' => $quiz->attempts_count,
                'is_active' => (bool) $quiz->is_active,
                'url' => route('teacher.quizzes.overview.show', $quiz->id),
            ])
            ->values();
    }
}
