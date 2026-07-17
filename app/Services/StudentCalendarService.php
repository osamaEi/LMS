<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceApology;
use App\Models\Quiz;
use App\Models\Session;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Builds the student weekly-calendar payload (sessions + quizzes).
 *
 * Shared by the web calendar (Student\DashboardController) and the API
 * (Api\V1\Student\ScheduleController@weeklyCalendar) so both always render
 * the exact same schedule.
 */
class StudentCalendarService
{
    /**
     * The grid's 7 teaching periods — same boundaries the API/web calendar use.
     */
    public const PERIODS = [
        ['name' => 'الفترة الصباحية (1)', 'start' => '08:10', 'end' => '09:20'],
        ['name' => 'الفترة الصباحية (2)', 'start' => '09:30', 'end' => '10:40'],
        ['name' => 'الفترة الصباحية (3)', 'start' => '10:50', 'end' => '12:00'],
        ['name' => 'الفترة المسائية (1)', 'start' => '12:20', 'end' => '13:25'],
        ['name' => 'الفترة المسائية (2)', 'start' => '13:35', 'end' => '14:40'],
        ['name' => 'الفترة المسائية (3)', 'start' => '14:50', 'end' => '15:55'],
        ['name' => 'الفترة المسائية (4)', 'start' => '16:00', 'end' => '17:15'],
    ];

    /**
     * Bucket a session's start time into one of the teaching periods.
     *
     * Falls back to the نصف اليوم (morning/evening) split at 12:00 when the
     * time doesn't land inside a period's window (e.g. sits in a break).
     *
     * @return array{period_index:int|null, period_name:string|null, period:string, period_label:string}
     */
    public function periodFor(Carbon $at): array
    {
        $minutes = $at->hour * 60 + $at->minute;

        foreach (self::PERIODS as $i => $p) {
            [$sh, $sm] = array_map('intval', explode(':', $p['start']));
            [$eh, $em] = array_map('intval', explode(':', $p['end']));

            if ($minutes >= $sh * 60 + $sm && $minutes <= $eh * 60 + $em) {
                $isMorning = str_contains($p['name'], 'صباحية');
                return [
                    'period_index' => $i,
                    'period_name'  => $p['name'],
                    'period'       => $isMorning ? 'morning' : 'evening',
                    'period_label' => $isMorning ? 'فترة صباحية' : 'فترة مسائية',
                ];
            }
        }

        $isMorning = $minutes < 12 * 60;
        return [
            'period_index' => null,
            'period_name'  => null,
            'period'       => $isMorning ? 'morning' : 'evening',
            'period_label' => $isMorning ? 'فترة صباحية' : 'فترة مسائية',
        ];
    }

    /** Program IDs the student is enrolled in (pivot + legacy primary). */
    public function programIds($student): Collection
    {
        return $student->allProgramIds();
    }

    /** Subjects accessible to the student, scoped to their class + shared (NULL). */
    public function subjectIds($student): Collection
    {
        $programIds = $this->programIds($student);

        $classIds = $programIds->map(fn($pid) => $student->classIdForProgram((int) $pid))
            ->filter()->unique()->values()->all();

        return Subject::where(function ($q) use ($student, $programIds) {
                $q->whereIn('program_id', $programIds)
                  ->orWhereHas('term', fn($tq) => $tq->whereIn('program_id', $programIds))
                  ->orWhereHas('enrollments', fn($eq) => $eq->where('student_id', $student->id));
            })
            ->where(function ($q) use ($classIds) {
                $q->whereNull('class_id');
                if (!empty($classIds)) {
                    $q->orWhereIn('class_id', $classIds);
                }
            })
            ->pluck('id');
    }

    /**
     * Session cards for the weekly calendar.
     *
     * @param  Carbon|null  $from  Inclusive lower bound on scheduled_at
     * @param  Carbon|null  $to    Inclusive upper bound on scheduled_at
     */
    public function sessions($student, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $programIds = $this->programIds($student);

        $studentClassIds = $programIds
            ->map(fn($pid) => $student->classIdForProgram((int) $pid))
            ->filter()->unique()->values();

        // Sessions assigned to this student (via attendance), scoped to their classes + class-agnostic
        $assignedSessionIds = Attendance::where('student_id', $student->id)->pluck('session_id');
        if ($studentClassIds->isNotEmpty()) {
            $assignedSessionIds = Session::whereIn('id', $assignedSessionIds)
                ->where(fn($q) => $q->whereIn('class_id', $studentClassIds)->orWhereNull('class_id'))
                ->pluck('id');
        }

        $allAttendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $assignedSessionIds)
            ->get()->keyBy('session_id');

        $apologies = AttendanceApology::where('student_id', $student->id)
            ->get()->keyBy('session_id');

        $assignedClassIds = Session::whereIn('id', $assignedSessionIds)
            ->whereNotNull('class_id')->pluck('class_id');

        $calendarClassIds = $studentClassIds->merge($assignedClassIds)->filter()->unique()->values();

        return Session::whereNotNull('scheduled_at')
            ->where(function ($q) use ($calendarClassIds, $assignedSessionIds) {
                $q->whereIn('class_id', $calendarClassIds)
                  ->orWhereIn('id', $assignedSessionIds);
            })
            ->when($from, fn($q) => $q->where('scheduled_at', '>=', $from))
            ->when($to,   fn($q) => $q->where('scheduled_at', '<=', $to))
            ->with(['subject:id,name_ar,name_en', 'teacher:id,name,zoom_join_url'])
            ->orderBy('scheduled_at')
            ->get()
            ->map(function ($s) use ($allAttendances, $apologies) {
                $att = $allAttendances[$s->id] ?? null;
                $apo = $apologies[$s->id] ?? null;
                $at  = Carbon::parse($s->scheduled_at);
                return $this->periodFor($at) + [
                    'id'               => $s->id,
                    'title'            => $s->title_ar ?: ($s->subject->name_ar ?? 'جلسة'),
                    'subject_name'     => $s->subject->name_ar ?? '',
                    'teacher_name'     => $s->teacher->name ?? '',
                    'scheduled_at'     => Carbon::parse($s->scheduled_at)->toIso8601String(),
                    'duration_minutes' => $s->duration_minutes ?? 60,
                    'type'             => $s->type ?? '',
                    'status'           => (string) ($s->status ?? ''),
                    'session_number'   => $s->session_number,
                    'zoom_join_url'    => $s->zoom_join_url,
                    'zoom_start_url'   => $s->zoom_start_url,
                    'started_at'       => $s->started_at ? Carbon::parse($s->started_at)->toIso8601String() : null,
                    'ended_at'         => $s->ended_at ? Carbon::parse($s->ended_at)->toIso8601String() : null,
                    'attended'         => $att ? (bool) $att->attended : null,
                    'apology_status'   => $apo?->status,
                ];
            })
            ->values();
    }

    /**
     * Quiz cards for the weekly calendar.
     *
     * @param  bool  $withUrls  Include the web route URL (web calendar only).
     */
    public function quizzes($student, ?Carbon $from = null, ?Carbon $to = null, bool $withUrls = true): Collection
    {
        $subjectIds = $this->subjectIds($student);

        return Quiz::whereIn('subject_id', $subjectIds)
            ->where('is_active', true)
            ->whereNotNull('starts_at')
            ->when($from, fn($q) => $q->where('starts_at', '>=', $from))
            ->when($to,   fn($q) => $q->where('starts_at', '<=', $to))
            ->with(['subject:id,name_ar'])
            ->get()
            ->map(function ($q) use ($student, $withUrls) {
                $attempt    = $q->attempts()->where('student_id', $student->id)->latest()->first();
                $completed  = $attempt && $attempt->submitted_at;
                $inProgress = $attempt && !$attempt->submitted_at;
                $canStart   = $q->isAvailable() && !$completed
                    && ($q->max_attempts > $q->attempts()->where('student_id', $student->id)->whereNotNull('submitted_at')->count());

                $data = [
                    'id'          => $q->id,
                    'subject_id'  => $q->subject_id,
                    'title'       => $q->title_ar,
                    'subject_name'=> $q->subject->name_ar ?? '',
                    'type'        => $q->type,
                    'type_label'  => $q->type_label,
                    'starts_at'   => Carbon::parse($q->starts_at)->toIso8601String(),
                    'ends_at'     => $q->ends_at ? Carbon::parse($q->ends_at)->toIso8601String() : null,
                    'total_marks' => $q->total_marks,
                    'pass_marks'  => $q->pass_marks,
                    'duration'    => $q->duration_minutes,
                    'questions'   => $q->questions()->count(),
                    'can_start'   => $canStart,
                    'in_progress' => $inProgress,
                    'completed'   => $completed,
                    'score'       => $completed ? $attempt->score : null,
                    'passed'      => $completed ? $attempt->passed : null,
                    'attempt_id'  => $attempt?->id,
                ];

                if ($withUrls) {
                    $data['url'] = route('student.quizzes.show', [$q->subject_id, $q->id]);
                }

                return $data;
            })
            ->values();
    }
}
