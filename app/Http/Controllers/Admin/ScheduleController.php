<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $sessions = Session::with(['subject.term.program', 'program', 'teacher'])
            ->orderBy('scheduled_at')
            ->get();

        $calSessions = $sessions->map(function ($s) {
            $subjectName  = $s->subject->name_ar ?? null;
            $programName  = $s->subject->term->program->name_ar ?? $s->program->name_ar ?? null;
            $displayName  = $subjectName ?? $programName ?? 'جلسة';
            $isCourse     = is_null($s->subject_id) && !is_null($s->program_id);

            return [
                'id'               => $s->id,
                'title'            => $s->title_ar ?: ($displayName . ' #' . $s->session_number),
                'subject_name'     => $subjectName ?? $programName ?? '',
                'program_name'     => $programName ?? '',
                'teacher_name'     => $s->teacher?->name ?? '',
                'scheduled_at'     => $s->scheduled_at ? \Carbon\Carbon::parse($s->scheduled_at)->toIso8601String() : null,
                'duration_minutes' => $s->duration_minutes,
                'type'             => $s->type ?? '',
                'status'           => $s->status ?? '',
                'session_number'   => $s->session_number,
                'subject_id'       => $s->subject_id,
                'program_id'       => $s->program_id,
                'is_course'        => $isCourse,
                'attendance_count' => $s->attendances()->count(),
            ];
        })->filter(fn($s) => $s['scheduled_at'])->values();

        $stats = [
            'total'     => $sessions->count(),
            'upcoming'  => $sessions->filter(fn($s) => $s->scheduled_at && \Carbon\Carbon::parse($s->scheduled_at)->isFuture())->count(),
            'live'      => $sessions->where('status', 'live')->count(),
            'completed' => $sessions->where('status', 'completed')->count(),
        ];

        return view('admin.schedule.index', compact('calSessions', 'stats'));
    }

    public function sessionStudents(Session $session)
    {
        $session->load('subject.term');

        // Students enrolled directly in the subject (course/training programs)
        $fromEnrollments = Enrollment::where('subject_id', $session->subject_id)
            ->where('status', 'active')
            ->with('student:id,name,email')
            ->get()
            ->pluck('student')
            ->filter();

        // Students enrolled in the diploma program that owns this subject's term
        $programId = $session->subject?->term?->program_id;
        $fromProgram = $programId
            ? User::where('role', 'student')
                ->where('program_id', $programId)
                ->where('program_status', 'approved')
                ->get(['id', 'name', 'email'])
            : collect();

        $assigned = Attendance::where('session_id', $session->id)
            ->pluck('student_id')
            ->toArray();

        $students = $fromEnrollments->merge($fromProgram)
            ->unique('id')
            ->values()
            ->map(fn($s) => [
                'id'       => $s->id,
                'name'     => $s->name,
                'email'    => $s->email,
                'assigned' => in_array($s->id, $assigned),
            ]);

        return response()->json([
            'session' => [
                'id'           => $session->id,
                'title'        => $session->title_ar ?: (($session->subject->name_ar ?? 'جلسة') . ' #' . $session->session_number),
                'scheduled_at' => $session->scheduled_at,
            ],
            'students' => $students,
        ]);
    }

    public function assignStudents(Request $request, Session $session)
    {
        $request->validate([
            'student_ids'   => 'required|array',
            'student_ids.*' => 'integer|exists:users,id',
        ]);

        $inserted = 0;
        foreach ($request->student_ids as $studentId) {
            $exists = Attendance::where('session_id', $session->id)
                ->where('student_id', $studentId)
                ->exists();

            if (!$exists) {
                Attendance::create([
                    'session_id' => $session->id,
                    'student_id' => $studentId,
                    'attended'   => false,
                ]);
                $inserted++;
            }
        }

        return response()->json(['success' => true, 'inserted' => $inserted]);
    }
}
