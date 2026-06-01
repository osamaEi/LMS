<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentProgramResource;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Session;
use App\Models\Subject;
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
        $isDiploma = $program->type === 'diploma';

        if ($isDiploma) {
            // Diploma: load supervisor + current term subjects with teachers
            $program->loadMissing('supervisor');

            $currentTerm = $program->terms()
                ->where('status', 'active')
                ->orderBy('term_number')
                ->with(['subjects' => fn($q) => $q->with('teacher:id,name,specialization,profile_photo')])
                ->first();

            $programTeachers = collect();
        } else {
            // Course / training / english: load teachers assigned to this program
            $program->loadMissing('teachers');
            $currentTerm     = null;
            $programTeachers = $program->teachers;
        }

        return response()->json([
            'success' => true,
            'data'    => new StudentProgramResource([
                'status'            => 'enrolled',
                'program'           => $program,
                'current_term'      => $currentTerm?->term_number ?? 1,
                'current_term_name' => $currentTerm?->name,
                'current_term_obj'  => $currentTerm,
                'program_teachers'  => $programTeachers,
            ]),
        ]);
    }

    /**
     * GET /api/v1/student/program-subjects?filter=current|upcoming|past
     * Get all subjects in student's program grouped by term, with optional filter
     */
    public function subjects(Request $request)
    {
        $student = auth()->user();
        $program = $student->program;

        if (!$program || $student->program_status === 'pending') {
            return response()->json(['success' => false, 'message' => 'غير مسجل في برنامج'], 403);
        }

        $filter = $request->query('filter', 'current'); // current | past | all

        $termsQuery = $program->terms()->orderBy('term_number');

        if ($filter === 'current') {
            // Try active terms first; fall back to current_term_number
            $activeTerms = (clone $termsQuery)->where('status', 'active')->get();
            $terms = $activeTerms->isNotEmpty()
                ? $activeTerms
                : (clone $termsQuery)->where('term_number', $student->current_term_number ?? 1)->get();

            // If still empty, just take the first term
            if ($terms->isEmpty()) {
                $terms = (clone $termsQuery)->take(1)->get();
            }
        } elseif ($filter === 'past') {
            $terms = (clone $termsQuery)->where('status', 'completed')->get();
        } else {
            $terms = $termsQuery->get();
        }

        $termIds = $terms->pluck('id');

        // Load subjects via BOTH relationships: direct term_id and pivot term_subject
        $subjectQuery = Subject::with(['teacher:id,name,profile_photo'])
            ->withCount([
                'sessions',
                'sessions as recordings_count' => fn($q) => $q->where(
                    fn($q) => $q->whereNotNull('video_path')->orWhereNotNull('video_url')
                ),
            ])
            ->where(function ($q) use ($termIds) {
                $q->whereIn('term_id', $termIds)
                  ->orWhereHas('terms', fn($tq) => $tq->whereIn('terms.id', $termIds));
            });

        $subjects = $subjectQuery->get();

        $enrolledSubjectIds = Enrollment::where('student_id', $student->id)
            ->pluck('subject_id')
            ->flip();

        $data = $subjects->map(fn($subject) => [
            'id'               => $subject->id,
            'name_ar'          => $subject->name_ar,
            'name_en'          => $subject->name_en,
            'code'             => $subject->code,
            'description_ar'   => $subject->description_ar ?? null,
            'description_en'   => $subject->description_en ?? null,
            'credits'          => $subject->credits,
            'status'           => $subject->status,
            'banner_photo'     => $subject->banner_photo
                ? asset('storage/' . $subject->banner_photo)
                : null,
            'teacher'          => $subject->teacher ? [
                'id'            => $subject->teacher->id,
                'name'          => $subject->teacher->name,
                'profile_photo' => $subject->teacher->profile_photo
                    ? asset('storage/' . $subject->teacher->profile_photo)
                    : null,
            ] : null,
            'sessions_count'   => $subject->sessions_count,
            'recordings_count' => $subject->recordings_count,
            'is_enrolled'      => isset($enrolledSubjectIds[$subject->id]),
        ])->values();

        // Group by term for context
        $termData = $terms->map(fn($term) => [
            'id'          => $term->id,
            'term_number' => $term->term_number,
            'name'        => $term->name ?? ('الفصل ' . $term->term_number),
            'status'      => $term->status,
            'start_date'  => $term->start_date?->format('Y-m-d'),
            'end_date'    => $term->end_date?->format('Y-m-d'),
        ])->values();

        return response()->json([
            'success'  => true,
            'filter'   => $filter,
            'terms'    => $termData,
            'data'     => $data,
            'total'    => $data->count(),
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
