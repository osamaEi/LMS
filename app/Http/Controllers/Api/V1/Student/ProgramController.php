<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentProgramResource;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Session;
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
        $program = $student->program;

        // ─── No program ────────────────────────────────────────────────────────
        if (!$program) {
            return response()->json([
                'success' => true,
                'data'    => new StudentProgramResource(['status' => 'no_program']),
            ]);
        }

        // ─── Pending ───────────────────────────────────────────────────────────
        if ($student->program_status === 'pending') {
            return response()->json([
                'success' => true,
                'data'    => new StudentProgramResource([
                    'status'  => 'pending',
                    'program' => $program,
                ]),
            ]);
        }

        // ─── Enrolled ──────────────────────────────────────────────────────────
        $program->load(['terms' => fn($q) => $q->orderBy('term_number')
            ->with(['subjects' => fn($sq) => $sq->with([
                'teacher:id,name',
                'sessions:id,subject_id,type,duration_minutes',
            ])])
        ]);

        $enrollments    = Enrollment::where('student_id', $student->id)->get();
        $enrollmentsMap = $enrollments->keyBy('subject_id');
        $enrolledIds    = $enrollments->pluck('subject_id');

        $totalAttendances   = Attendance::where('student_id', $student->id)->count();
        $presentAttendances = Attendance::where('student_id', $student->id)->where('attended', true)->count();
        $attendanceRate     = $totalAttendances > 0
            ? round(($presentAttendances / $totalAttendances) * 100, 1)
            : 0;

        $totalSessions     = Session::whereIn('subject_id', $enrolledIds)->count();
        $completedSessions = Session::whereIn('subject_id', $enrolledIds)->whereNotNull('ended_at')->count();

        $currentTermNumber = $student->current_term_number ?? 1;
        $totalTerms        = $program->terms->count();

        return response()->json([
            'success' => true,
            'data'    => new StudentProgramResource([
                'status'          => 'enrolled',
                'program'         => $program,
                'enrollments'     => $enrollments,
                'enrollments_map' => $enrollmentsMap,
                'statistics'      => [
                    'total_subjects'      => $enrollments->count(),
                    'total_sessions'      => $totalSessions,
                    'completed_sessions'  => $completedSessions,
                    'attendance_rate'     => $attendanceRate,
                    'current_term'        => $currentTermNumber,
                    'total_terms'         => $totalTerms,
                    'progress_percentage' => $totalTerms > 0
                        ? (int) round(($currentTermNumber / $totalTerms) * 100)
                        : 0,
                ],
            ]),
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
