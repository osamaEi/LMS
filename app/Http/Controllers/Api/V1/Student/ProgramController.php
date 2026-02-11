<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Term;
use App\Models\Subject;
use App\Models\Session;
use App\Models\Attendance;
use App\Models\Enrollment;
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

        // Check pending status
        if ($student->program_status === 'pending') {
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'pending',
                    'message' => 'طلب التسجيل في انتظار موافقة الإدارة',
                ],
            ]);
        }

        $program = $student->program;

        if (!$program) {
            $availablePrograms = Program::where('status', 'active')
                ->withCount('terms')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => 'no_program',
                    'available_programs' => $availablePrograms,
                ],
            ]);
        }

        $track = $student->track;
        $currentTermNumber = $student->current_term_number ?? 1;

        $terms = Term::where('program_id', $program->id)
            ->orderBy('term_number', 'asc')
            ->with(['subjects' => function ($q) use ($student) {
                $q->with(['teacher:id,name', 'sessions']);
            }])
            ->get();

        $enrollments = Enrollment::where('student_id', $student->id)
            ->with(['subject.term', 'subject.teacher:id,name'])
            ->get();

        $subjects = Subject::whereIn('term_id', $terms->pluck('id'))->get();

        // Statistics
        $totalSessions = Session::whereHas('subject.enrollments', fn($q) => $q->where('student_id', $student->id))->count();
        $completedSessions = Session::whereHas('subject.enrollments', fn($q) => $q->where('student_id', $student->id))->whereNotNull('ended_at')->count();
        $totalAttendances = Attendance::where('student_id', $student->id)->count();
        $presentAttendances = Attendance::where('student_id', $student->id)->where('attended', true)->count();
        $attendanceRate = $totalAttendances > 0 ? round(($presentAttendances / $totalAttendances) * 100, 1) : 0;

        $stats = [
            'total_subjects' => $subjects->count(),
            'total_sessions' => $totalSessions,
            'completed_sessions' => $completedSessions,
            'attendance_rate' => $attendanceRate,
            'current_term' => $currentTermNumber,
            'total_terms' => $terms->count(),
            'progress_percentage' => $terms->count() > 0 ? round(($currentTermNumber / $terms->count()) * 100, 1) : 0,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'status' => 'enrolled',
                'program' => $program,
                'track' => $track,
                'terms' => $terms,
                'enrollments' => $enrollments,
                'statistics' => $stats,
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
            ], 422);
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
