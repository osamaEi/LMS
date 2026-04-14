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
        $currentTerm =  $program->terms()->orderBy('term_number')->where('status', 'active')->first();

        $currentTermNumber = $currentTerm?->term_number ?? 1;

        return response()->json([
            'success' => true,
            'data'    => new StudentProgramResource([
                'status'       => 'enrolled',
                'program'      => $program,
                'current_term' => $currentTermNumber,
                'current_term_name' => $currentTerm?->name,
            ]),
        ]);
    }

    /**
     * GET /api/v1/student/term-attendance
     * Get student's attendance stats for their current term
     */
    public function termAttendance()
    {
        $student = auth()->user();
        $program = $student->program;

        if (!$program || $student->program_status === 'pending') {
            return response()->json(['success' => false, 'message' => 'غير مسجل في برنامج'], 403);
        }

        $currentTerm = $program->terms()->orderBy('term_number')->where('status', 'active')->first();

        if (!$currentTerm) {
            return response()->json(['success' => true, 'data' => null]);
        }

        // Subjects in this term that the student is enrolled in
        $termSubjectIds = $currentTerm->subjects()->pluck('subjects.id');

        $enrolledSubjectIds = Enrollment::where('student_id', $student->id)
            ->whereIn('subject_id', $termSubjectIds)
            ->pluck('subject_id');

        $sessionIds = Session::whereIn('subject_id', $enrolledSubjectIds)->pluck('id');
        $totalSessions  = $sessionIds->count();

        $attendedSessions = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessionIds)
            ->where('attended', true)
            ->count();

        $attendanceRate = $totalSessions > 0
            ? round(($attendedSessions / $totalSessions) * 100, 1)
            : 0;

        return response()->json([
            'success' => true,
            'data'    => [
                'term_name'        => $currentTerm->name,
                'term_number'      => $currentTerm->term_number,
                'total_sessions'   => $totalSessions,
                'attended_sessions'=> $attendedSessions,
                'attendance_rate'  => $attendanceRate,
                'minimum_required' => 80,
                'is_at_risk'       => $attendanceRate < 80,
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
