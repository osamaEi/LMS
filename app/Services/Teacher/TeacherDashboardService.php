<?php

namespace App\Services\Teacher;

use App\Models\Attendance;
use App\Models\SatisfactionSurvey;
use App\Models\Session;
use App\Models\TeacherRating;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TeacherDashboardService
{
    public function __construct(
        private readonly TeacherCalendarService $calendar,
    ) {}

    public function data(User $teacher): array
    {
        return [
            'calSessions' => $this->calendar->sessions($teacher),
            'calQuizzes' => $this->calendar->quizzes($teacher),
            'myZoomLink' => $teacher->zoom_join_url,
            'upcomingSessions' => $this->upcomingSessions($teacher),
            'liveSessions' => $this->liveSessions($teacher),
            'pastSessions' => $this->pastSessions($teacher),
            'recentSessions' => $this->recentSessions($teacher),
            'recentSessionsWithAttendance' => $this->recentSessionsWithAttendance($teacher),
            'teacherRating' => $this->teacherRating($teacher),
            'recentFeedback' => $this->recentFeedback($teacher),
            'openTicketsCount' => $this->openTicketsCount($teacher),
            'pendingSurveys' => $this->pendingSurveys($teacher),
        ];
    }

    private function upcomingSessions(User $teacher): Collection
    {
        return $this->teacherSessions($teacher)
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();
    }

    private function liveSessions(User $teacher): Collection
    {
        return $this->teacherSessions($teacher)
            ->whereNotNull('started_at')
            ->whereNull('ended_at')
            ->orderByDesc('started_at')
            ->get();
    }

    private function pastSessions(User $teacher): Collection
    {
        return $this->teacherSessions($teacher)
            ->where(function ($query) {
                $query->whereNotNull('ended_at')
                    ->orWhere('scheduled_at', '<', now()->subHour());
            })
            ->orderByDesc('scheduled_at')
            ->limit(20)
            ->get();
    }

    private function recentSessions(User $teacher): Collection
    {
        return Session::whereHas(
            'subject',
            fn ($query) => $query->assignedToTeacher($teacher->id),
        )
            ->with('subject')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    private function recentSessionsWithAttendance(User $teacher): Collection
    {
        return Session::whereHas(
            'subject',
            fn ($query) => $query->assignedToTeacher($teacher->id),
        )
            ->where('scheduled_at', '<', now())
            ->with([
                'subject.term',
                'subject.terms',
                'attendances' => fn ($query) => $query
                    ->where('attended', true)
                    ->with('student:id,name'),
            ])
            ->orderByDesc('scheduled_at')
            ->limit(8)
            ->get()
            ->map(function (Session $session) {
                $classId = $this->resolveClassId($session);
                $studentIds = $classId
                    ? DB::table('student_programs')
                        ->where('class_id', $classId)
                        ->distinct()
                        ->pluck('student_id')
                    : collect();

                if ($studentIds->isEmpty()) {
                    $studentIds = $session->attendances->pluck('student_id');
                }

                $studentLookup = $studentIds->flip();
                $attendances = $session->attendances
                    ->filter(fn (Attendance $attendance) => $studentLookup->has($attendance->student_id))
                    ->values();

                $session->setRelation('attendances', $attendances);
                $session->attended_count = $attendances->count();
                $session->enrolled_count = $studentIds->count();

                return $session;
            });
    }

    private function teacherRating(User $teacher): array
    {
        return [
            'overall' => $teacher->getAverageRating(),
            'breakdown' => $teacher->getRatingsBreakdown(),
            'total_ratings' => $teacher->ratingsReceived()->where('is_approved', true)->count(),
        ];
    }

    private function recentFeedback(User $teacher): Collection
    {
        return TeacherRating::where('teacher_id', $teacher->id)
            ->where('is_approved', true)
            ->whereNotNull('comment')
            ->with(['student:id,name', 'subject:id,name_ar,name_en'])
            ->latest()
            ->limit(5)
            ->get();
    }

    private function openTicketsCount(User $teacher): int
    {
        return Ticket::where('user_id', $teacher->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->count();
    }

    private function pendingSurveys(User $teacher): int
    {
        return SatisfactionSurvey::where('status', 'active')
            ->where('type', 'teacher')
            ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->whereDoesntHave(
                'responses',
                fn ($query) => $query->where('user_id', $teacher->id),
            )
            ->count();
    }

    private function teacherSessions(User $teacher)
    {
        return Session::where('teacher_id', $teacher->id)
            ->with(['subject.program', 'subject.term', 'program']);
    }

    private function resolveClassId(Session $session): ?int
    {
        return $session->class_id
            ?? $session->subject?->class_id
            ?? $session->subject?->term?->class_id
            ?? $session->subject?->terms?->firstWhere(fn ($term) => $term->class_id)?->class_id;
    }
}
