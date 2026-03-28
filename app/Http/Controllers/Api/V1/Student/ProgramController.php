<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProgramResource;
use App\Http\Resources\SubjectWithProgressResource;
use App\Http\Resources\TermResource;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Session;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * GET /api/v1/student/my-program
     * Show student's program details
     */
    public function show()
    {
        $student = auth()->user();

        // ─── Pending ───────────────────────────────────────────────────────────
        if ($student->program_status === 'pending') {
            return response()->json([
                'success' => true,
                'data' => [
                    'status'  => 'pending',
                    'message' => 'طلب التسجيل في انتظار موافقة الإدارة',
                    'program' => new ProgramResource($student->program),
                ],
            ]);
        }

        // ─── No program ────────────────────────────────────────────────────────
        $program = $student->program;

        if (!$program) {
            $available = Program::where('status', 'active')->withCount('terms')->get();
            return response()->json([
                'success' => true,
                'data' => [
                    'status'             => 'no_program',
                    'available_programs' => ProgramResource::collection($available),
                ],
            ]);
        }

        $currentTermNumber = $student->current_term_number ?? 1;

        // ─── Load program terms with pivot subjects ─────────────────────────────
        $program->load(['terms' => fn($q) => $q->orderBy('term_number')
            ->with(['subjects' => fn($sq) => $sq->with(['teacher:id,name', 'sessions:id,subject_id,type,duration_minutes'])
                ->withCount('sessions')])
            ->withCount('subjects')
        ]);

        $allTerms   = $program->terms;
        $totalTerms = $allTerms->count();

        // Current term
        $currentTerm = $allTerms->firstWhere('term_number', $currentTermNumber);

        // ─── Student enrollments keyed by subject_id ───────────────────────────
        $enrollments = Enrollment::where('student_id', $student->id)
            ->get()
            ->keyBy('subject_id');

        // ─── All enrolled subjects ──────────────────────────────────────────────
        $enrolledSubjectIds = $enrollments->keys();

        $allSubjects = Subject::whereIn('id', $enrolledSubjectIds)
            ->with(['teacher:id,name', 'sessions:id,subject_id,type,duration_minutes', 'term:id,term_number,name_ar,name_en'])
            ->get();

        // Current term subjects
        $currentTermSubjectIds = $currentTerm
            ? $currentTerm->subjects->pluck('id')
            : collect();

        $currentTermSubjects = $allSubjects->whereIn('id', $currentTermSubjectIds)->values();

        $previousTermSubjects = $allSubjects->whereNotIn('id', $currentTermSubjectIds)->values();

        // ─── Attendance stats for current term ─────────────────────────────────
        $totalAttendances   = Attendance::where('student_id', $student->id)->count();
        $presentAttendances = Attendance::where('student_id', $student->id)->where('attended', true)->count();
        $attendanceRate     = $totalAttendances > 0
            ? round(($presentAttendances / $totalAttendances) * 100, 1)
            : 0;

        // ─── Session stats ──────────────────────────────────────────────────────
        $totalSessions     = Session::whereIn('subject_id', $enrolledSubjectIds)->count();
        $completedSessions = Session::whereIn('subject_id', $enrolledSubjectIds)->whereNotNull('ended_at')->count();

        // ─── Build current term data with completion percentage ─────────────────
        $currentTermData = null;
        if ($currentTerm) {
            $currentTermData = array_merge(
                (new TermResource($currentTerm))->resolve(),
                [
                    'completion_percentage'   => $attendanceRate,
                    'min_attendance_required' => 80,
                ]
            );
        }

        // ─── Statistics ─────────────────────────────────────────────────────────
        $statistics = [
            'total_subjects'          => $allSubjects->count(),
            'total_sessions'          => $totalSessions,
            'completed_sessions'      => $completedSessions,
            'attendance_rate'         => $attendanceRate,
            'current_term_number'     => $currentTermNumber,
            'total_terms'             => $totalTerms,
            'overall_progress_percentage' => $totalTerms > 0
                ? round(($currentTermNumber / $totalTerms) * 100, 1)
                : 0,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'status'                 => 'enrolled',
                'program'                => new ProgramResource($program),
                'current_term'           => $currentTermData,
                'statistics'             => $statistics,
                'all_subjects'           => SubjectWithProgressResource::collection($allSubjects)
                    ->additional(['enrollments' => $enrollments]),
                'current_term_subjects'  => SubjectWithProgressResource::collection($currentTermSubjects)
                    ->additional(['enrollments' => $enrollments]),
                'previous_subjects'      => SubjectWithProgressResource::collection($previousTermSubjects)
                    ->additional(['enrollments' => $enrollments]),
            ],
        ]);
    }

    /**
     * POST /api/v1/student/enroll-program
     * Enroll in a program
     */
    public function enroll(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
        ]);

        $student = auth()->user();

        if ($student->program_id) {
            return response()->json([
                'success' => false,
                'message' => 'أنت مسجل بالفعل في برنامج دراسي',
            ], 409);
        }

        $program = Program::where('id', $request->program_id)
            ->where('status', 'active')
            ->firstOrFail();

        $student->update([
            'program_id' => $program->id,
            'program_status' => 'pending',
            'current_term_number' => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال طلب التسجيل في البرنامج: ' . $program->name . '. في انتظار موافقة الإدارة.',
        ]);
    }
}
