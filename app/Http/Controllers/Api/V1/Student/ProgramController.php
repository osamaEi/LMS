<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentProgramResource;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Program;
use App\Models\Session;
use App\Models\Subject;
use App\Models\SubjectFile;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    /**
     * GET /api/v1/student/my-program
     * Return all programs the student is enrolled in (primary + pivot).
     */
    public function show()
    {
        $student = auth()->user();

        // Collect all programs: pivot first, then primary if missing from pivot
        $pivotPrograms = $student->programs()->get(); // student_programs pivot

        $allPrograms = $pivotPrograms;
        if ($student->program_id && !$pivotPrograms->contains('id', $student->program_id)) {
            $primary = $student->program;
            if ($primary) {
                // Attach synthetic pivot data from the users table columns
                $primary->pivot_status              = $student->program_status ?? 'approved';
                $primary->pivot_current_term_number = $student->current_term_number ?? 1;
                $primary->pivot_enrolled_at         = $student->created_at;
                $allPrograms                        = $pivotPrograms->prepend($primary);
            }
        }

        if ($allPrograms->isEmpty()) {
            return response()->json([
                'success' => true,
                'data'    => [],
                'message' => 'لا توجد برامج مسجل فيها',
            ]);
        }

        $data = $allPrograms->map(function ($program) {
            // Pivot fields (from belongsToMany withPivot or synthetic)
            $pivotStatus      = $program->pivot?->status      ?? $program->pivot_status      ?? 'approved';
            $pivotTermNumber  = $program->pivot?->current_term_number ?? $program->pivot_current_term_number ?? 1;
            $pivotEnrolledAt  = $program->pivot?->enrolled_at ?? $program->pivot_enrolled_at ?? null;

            $isDiploma = $program->type === 'diploma';

            $currentTerm     = null;
            $programTeachers = collect();

            if ($pivotStatus === 'approved' || $pivotStatus === 'completed') {
                if ($isDiploma) {
                    $program->loadMissing('supervisor');
                    $classId = auth()->user()->classIdForProgram($program->id);
                    $currentTerm = $program->terms()
                        ->where('status', 'active')
                        ->where(fn($q) => $q->whereNull('class_id')->orWhere('class_id', $classId))
                        ->orderBy('term_number')
                        ->with(['subjects' => fn($q) => $q
                            ->where(fn($sq) => $sq->whereNull('class_id')->orWhere('class_id', $classId))
                            ->with('teacher:id,name,specialization,profile_photo')])
                        ->first();
                } else {
                    $program->loadMissing('teachers');
                    $programTeachers = $program->teachers;
                }
            }

            $result = [
                'id'              => $program->id,
                'name_ar'         => $program->name_ar,
                'name_en'         => $program->name_en,
                'type'            => $program->type,
                'course_type'     => $program->course_type ?? null,
                'description_ar'  => $program->description_ar ?? null,
                'image'           => $program->image ? asset('storage/' . $program->image) : null,
                'duration_months' => $program->duration_months ?? null,
                'duration_hours'  => $program->duration_hours  ?? null,
                'status'          => $program->status,

                // Enrollment info from pivot
                'enrollment_status'   => $pivotStatus,
                'current_term_number' => $pivotTermNumber,
                'enrolled_at'         => $pivotEnrolledAt,
            ];

            if ($isDiploma && $currentTerm) {
                $result['current_term'] = [
                    'id'          => $currentTerm->id,
                    'term_number' => $currentTerm->term_number,
                    'name'        => $currentTerm->name ?? ('الفصل ' . $currentTerm->term_number),
                    'status'      => $currentTerm->status,
                    'subjects'    => $currentTerm->subjects->map(fn($s) => [
                        'id'      => $s->id,
                        'name_ar' => $s->name_ar,
                        'name_en' => $s->name_en,
                        'code'    => $s->code,
                        'teacher' => $s->teacher ? [
                            'id'             => $s->teacher->id,
                            'name'           => $s->teacher->name,
                            'specialization' => $s->teacher->specialization,
                            'profile_photo'  => $s->teacher->profile_photo
                                ? asset('storage/' . $s->teacher->profile_photo)
                                : null,
                        ] : null,
                    ])->values(),
                ];

                if ($program->supervisor ?? null) {
                    $result['supervisor'] = [
                        'id'   => $program->supervisor->id,
                        'name' => $program->supervisor->name,
                    ];
                }
            }

            if (!$isDiploma && $programTeachers->isNotEmpty()) {
                $result['teachers'] = $programTeachers->map(fn($t) => [
                    'id'            => $t->id,
                    'name'          => $t->name,
                    'profile_photo' => $t->profile_photo
                        ? asset('storage/' . $t->profile_photo)
                        : null,
                ])->values();
            }

            return $result;
        })->values();

        return response()->json([
            'success' => true,
            'total'   => $data->count(),
            'data'    => $data,
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

        // Scope to the student's class for this program (plus shared/program-wide records)
        $classId = $student->classIdForProgram($program->id);

        $termsQuery = $program->terms()->orderBy('term_number')
            ->where(fn($q) => $q->whereNull('class_id')->orWhere('class_id', $classId));

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
            })
            ->where(fn($q) => $q->whereNull('class_id')->orWhere('class_id', $classId));

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
     * GET /api/v1/student/my-course
     * For course/training/english students: full course content
     * (sessions with files + homework, program files, attendance)
     */
    public function myCourse(Request $request)
    {
        $student = auth()->user();

        // If program_id provided, load that specific program from pivot
        if ($request->filled('program_id')) {
            $programId = (int) $request->input('program_id');

            // Check pivot enrollment first
            $pivotProgram = $student->programs()->where('programs.id', $programId)->first();
            if ($pivotProgram) {
                $program = $pivotProgram;
                $pivotStatus = $pivotProgram->pivot->status;
            } elseif ($student->program_id === $programId) {
                $program     = $student->program;
                $pivotStatus = $student->program_status;
            } else {
                return response()->json(['success' => false, 'message' => 'غير مسجل في هذا البرنامج'], 403);
            }

            if ($pivotStatus === 'pending') {
                return response()->json(['success' => false, 'message' => 'طلب التسجيل قيد المراجعة'], 403);
            }
        } else {
            // Default: first non-diploma program from all enrollments
            $program = $student->programs()
                ->whereIn('programs.type', ['course', 'training', 'english'])
                ->wherePivot('status', 'approved')
                ->first();

            if (!$program) {
                // Fallback to primary program if it's not diploma
                $primary = $student->program;
                if ($primary && $primary->type !== 'diploma' && $student->program_status === 'approved') {
                    $program = $primary;
                }
            }

            if (!$program) {
                return response()->json(['success' => false, 'message' => 'لا توجد دورات مسجل فيها'], 403);
            }
        }

        if ($program->type === 'diploma') {
            return response()->json(['success' => false, 'message' => 'هذا المسار مخصص للدورات فقط، استخدم /program-subjects'], 422);
        }

        // Determine the student's class for this program
        $pivotClassId = $student->programs()
            ->where('programs.id', $program->id)
            ->first()?->pivot?->class_id;
        $classId = $pivotClassId ?? ($student->program_id === $program->id ? $student->class_id : null);

        // Filter sessions by program and class (if assigned to a class)
        $sessionQuery = Session::where('program_id', $program->id)
            ->with(['files', 'homework'])
            ->orderBy('session_number');

        if ($classId) {
            $sessionQuery->where('class_id', $classId);
        }

        $sessions = $sessionQuery->get();

        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('session_id');

        $homeworkIds = $sessions->pluck('homework')->filter()->pluck('id');
        $mySubmissions = HomeworkSubmission::where('student_id', $student->id)
            ->whereIn('homework_id', $homeworkIds)
            ->get()
            ->keyBy('homework_id');

        // Program files
        $programFiles = SubjectFile::where('program_id', $program->id)
            ->orderBy('order')
            ->get()
            ->map(fn($f) => [
                'id'    => $f->id,
                'title' => $f->title,
                'url'   => asset('storage/' . $f->file_path),
                'type'  => $f->file_type,
                'size'  => $f->file_size,
                'name'  => $f->file_original_name,
            ])->values();

        // Format sessions
        $sessionsData = $sessions->map(function ($session) use ($attendances, $mySubmissions) {
            $attendance = $attendances->get($session->id);
            $homework   = $session->homework;

            $status = match (true) {
                !is_null($session->ended_at)   => 'ended',
                !is_null($session->started_at) => 'live',
                default                        => 'upcoming',
            };

            return [
                'id'               => $session->id,
                'title'            => $session->title_ar ?? $session->title ?? null,
                'session_number'   => $session->session_number,
                'type'             => $session->type,
                'status'           => $status,
                'scheduled_at'     => $session->scheduled_at,
                'duration_minutes' => $session->duration_minutes,
                'join_url'         => $session->type === 'live_zoom' ? $session->zoom_join_url : null,
                'zoom_meeting_id'  => $session->zoom_meeting_id ?? null,
                'video_url'        => $session->type === 'recorded_video' ? $session->getVideoUrl() : null,
                'recording_url'    => $session->recording_url ?? null,
                'files'            => $session->files->map(fn($f) => [
                    'id'    => $f->id,
                    'title' => $f->title,
                    'url'   => asset('storage/' . $f->file_path),
                    'type'  => $f->file_type,
                    'size'  => $f->file_size,
                ])->values(),
                'homework' => $homework ? [
                    'id'             => $homework->id,
                    'title_ar'       => $homework->title_ar,
                    'title_en'       => $homework->title_en,
                    'description_ar' => $homework->description_ar,
                    'due_date'       => $homework->due_date?->format('Y-m-d'),
                    'attachment_url' => $homework->file_url,
                    'submission'     => $mySubmissions->has($homework->id) ? [
                        'id'           => $mySubmissions[$homework->id]->id,
                        'content'      => $mySubmissions[$homework->id]->content,
                        'file_url'     => $mySubmissions[$homework->id]->file_path
                            ? (filter_var($mySubmissions[$homework->id]->file_path, FILTER_VALIDATE_URL)
                                ? $mySubmissions[$homework->id]->file_path
                                : asset('storage/' . $mySubmissions[$homework->id]->file_path))
                            : null,
                        'submitted_at' => $mySubmissions[$homework->id]->submitted_at?->toIso8601String(),
                        'grade'        => $mySubmissions[$homework->id]->grade,
                        'feedback'     => $mySubmissions[$homework->id]->feedback,
                    ] : null,
                ] : null,
                'attendance' => [
                    'attended'         => $attendance?->attended ?? false,
                    'joined_at'        => $attendance?->joined_at,
                    'duration_minutes' => $attendance?->duration_minutes ?? 0,
                ],
                'links' => [
                    'join_api'  => url("/api/v1/student/sessions/{$session->id}/join-zoom"),
                    'leave_api' => url("/api/v1/student/sessions/{$session->id}/leave-zoom"),
                ],
            ];
        })->values();

        // Attendance stats
        $total    = $sessionsData->count();
        $attended = $sessionsData->where('attendance.attended', true)->count();

        $program->loadMissing('teachers');

        return response()->json([
            'success' => true,
            'data'    => [
                'program' => [
                    'id'          => $program->id,
                    'name_ar'     => $program->name_ar,
                    'name_en'     => $program->name_en,
                    'type'        => $program->type,
                    'description' => $program->description_ar,
                    'duration_hours'  => $program->duration_hours ?? null,
                    'duration_months' => $program->duration_months ?? null,
                    'teacher'     => $program->teachers->first() ? [
                        'id'    => $program->teachers->first()->id,
                        'name'  => $program->teachers->first()->name,
                        'photo' => $program->teachers->first()->profile_photo
                            ? asset('storage/' . $program->teachers->first()->profile_photo)
                            : null,
                    ] : null,
                ],
                'files'    => $programFiles,
                'sessions' => $sessionsData,
                'attendance' => [
                    'total_sessions'    => $total,
                    'attended_sessions' => $attended,
                    'absent_sessions'   => $total - $attended,
                    'attendance_rate'   => $total > 0 ? round(($attended / $total) * 100, 1) : 0,
                ],
            ],
        ]);
    }

    /**
     * GET /api/v1/student/term-attendance
     * Get student's attendance stats for their current term
     */
    public function termAttendance()
    {
        $student   = auth()->user();
        $program   = $student->program;
        $isDiploma = $program && $program->type === 'diploma';

        if (!$program || $student->program_status === 'pending') {
            return response()->json(['success' => false, 'message' => 'غير مسجل في برنامج'], 403);
        }

        if ($isDiploma) {
            // ── Diploma: attendance based on term subjects ──────────────────
            $currentTerm = $program->terms()
                ->where('status', 'active')
                ->orderBy('term_number')
                ->first();

            // Fallback: current_term_number or first term
            if (!$currentTerm) {
                $currentTerm = $program->terms()
                    ->where('term_number', $student->current_term_number ?? 1)
                    ->first()
                    ?? $program->terms()->orderBy('term_number')->first();
            }

            if (!$currentTerm) {
                return response()->json(['success' => true, 'data' => null]);
            }

            // All subject IDs in this term (pivot + direct term_id)
            $termSubjectIds = Subject::where(function ($q) use ($currentTerm) {
                $q->where('term_id', $currentTerm->id)
                  ->orWhereHas('terms', fn($tq) => $tq->where('terms.id', $currentTerm->id));
            })->pluck('id');

            // Sessions assigned to this student (via Attendance records) in these subjects
            $assignedSessionIds = Attendance::where('student_id', $student->id)->pluck('session_id');

            $sessionIds = Session::whereIn('subject_id', $termSubjectIds)
                ->whereIn('id', $assignedSessionIds)
                ->pluck('id');

            $totalSessions    = $sessionIds->count();
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
                    'program_name'     => $program->name_ar,
                    'program_type'     => $program->type,
                    'term_name'        => $currentTerm->name ?? ('الفصل ' . $currentTerm->term_number),
                    'term_number'      => $currentTerm->term_number,
                    'total_sessions'   => $totalSessions,
                    'attended_sessions'=> $attendedSessions,
                    'absent_sessions'  => $totalSessions - $attendedSessions,
                    'attendance_rate'  => $attendanceRate,
                    'minimum_required' => 80,
                    'is_at_risk'       => $attendanceRate < 80,
                ],
            ]);

        } else {
            // ── Course / Training / English: attendance based on program sessions ──
            $assignedSessionIds = Attendance::where('student_id', $student->id)->pluck('session_id');

            $sessionIds = Session::where('program_id', $program->id)
                ->whereIn('id', $assignedSessionIds)
                ->pluck('id');

            $totalSessions    = $sessionIds->count();
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
                    'program_name'     => $program->name_ar,
                    'program_type'     => $program->type,
                    'term_name'        => null,
                    'term_number'      => null,
                    'total_sessions'   => $totalSessions,
                    'attended_sessions'=> $attendedSessions,
                    'absent_sessions'  => $totalSessions - $attendedSessions,
                    'attendance_rate'  => $attendanceRate,
                    'minimum_required' => 80,
                    'is_at_risk'       => $attendanceRate < 80,
                ],
            ]);
        }
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
