<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\Student\MyProgramResource;
use App\Http\Resources\Student\ProgramSubjectResource;
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

        $student = auth()->user();

        $data = $allPrograms->map(function ($program) use ($student) {
            $pivotStatus     = $program->pivot?->status               ?? $program->pivot_status              ?? 'approved';
            $pivotTermNumber = $program->pivot?->current_term_number  ?? $program->pivot_current_term_number ?? 1;
            $pivotEnrolledAt = $program->pivot?->enrolled_at          ?? $program->pivot_enrolled_at         ?? null;

            $isDiploma       = $program->type === 'diploma';
            $currentTerm     = null;
            $programTeachers = collect();
            $supervisor      = null;

            if ($pivotStatus === 'approved' || $pivotStatus === 'completed') {
                if ($isDiploma) {
                    $program->loadMissing('supervisor');
                    $supervisor = $program->supervisor ?? null;
                    $classId    = $student->classIdForProgram($program->id);

                    $hasClassTerms = $classId
                        ? \App\Models\Term::where('program_id', $program->id)->where('class_id', $classId)->exists()
                        : false;
                    $termScope = fn($q) => $hasClassTerms ? $q->where('class_id', $classId) : $q->whereNull('class_id');

                    $currentTerm = $program->terms()
                        ->where('status', 'active')
                        ->where(fn($q) => $termScope($q))
                        ->orderBy('term_number')
                        ->with(['subjects' => fn($q) => $termScope($q)
                            ->with('teacher:id,name,specialization,profile_photo')])
                        ->first();
                } else {
                    $program->loadMissing('teachers');
                    $programTeachers = $program->teachers;
                }
            }

            return (new MyProgramResource($program))->additional([
                'pivot_status'     => $pivotStatus,
                'pivot_term_number'=> $pivotTermNumber,
                'pivot_enrolled_at'=> $pivotEnrolledAt,
                'current_term'     => $currentTerm,
                'supervisor'       => $supervisor,
                'teachers'         => $programTeachers,
            ])->resolve();
        })->values();

        return response()->json([
            'success' => true,
            'total'   => $data->count(),
            'data'    => $data,
        ]);
    }

    /**
     * GET /api/v1/student/my-program/{id}
     * Show one enrolled program. Diploma → subjects grouped by term.
     * Course/training/english → the program's sessions.
     */
    public function showOne($id)
    {
        $student   = auth()->user();
        $programId = (int) $id;

        // Resolve the program from the pivot, falling back to the legacy primary column.
        $program     = $student->programs()->where('programs.id', $programId)->first();
        $pivotStatus = $program?->pivot?->status;

        if (!$program && $student->program_id === $programId) {
            $program     = $student->program;
            $pivotStatus = $student->program_status ?? 'approved';
        }

        if (!$program) {
            return response()->json(['success' => false, 'message' => 'غير مسجل في هذا البرنامج'], 403);
        }

        if ($pivotStatus === 'pending') {
            return response()->json(['success' => false, 'message' => 'طلب التسجيل قيد المراجعة'], 403);
        }

        $classId = $student->classIdForProgram($program->id);

        $payload = [
            'id'              => $program->id,
            'name_ar'         => $program->name_ar,
            'name_en'         => $program->name_en,
            'type'            => $program->type,
            'is_diploma'      => $program->type === 'diploma',
            'description_ar'  => $program->description_ar ?? null,
            'description_en'  => $program->description_en ?? null,
            'image'           => $program->image ? asset('storage/' . $program->image) : null,
            'duration_months' => $program->duration_months ?? null,
            'duration_hours'  => $program->duration_hours ?? null,
            'status'          => $program->status,
            'enrollment_status' => $pivotStatus,
            'class_id'          => $classId,
        ];

        if ($program->type === 'diploma') {
            // Prefer the student's own class terms; fall back to shared (class_id NULL)
            $hasClassTerms = $classId
                ? \App\Models\Term::where('program_id', $program->id)->where('class_id', $classId)->exists()
                : false;
            $termScope = fn($q) => $hasClassTerms ? $q->where('class_id', $classId) : $q->whereNull('class_id');

            $terms = $program->terms()
                ->where(fn($q) => $termScope($q))
                ->orderBy('term_number')
                ->get();

            $enrolledSubjectIds = Enrollment::where('student_id', $student->id)
                ->pluck('subject_id')
                ->flip();

            $payload['terms'] = $terms->map(function ($term) use ($classId, $enrolledSubjectIds) {
                $subjects = Subject::with(['teacher:id,name,profile_photo'])
                    ->withCount([
                        'sessions',
                        'sessions as recordings_count' => fn($q) => $q->where(
                            fn($w) => $w->whereNotNull('video_path')->orWhereNotNull('video_url')
                        ),
                    ])
                    ->where(function ($q) use ($term) {
                        $q->where('term_id', $term->id)
                          ->orWhereHas('terms', fn($tq) => $tq->where('terms.id', $term->id));
                    })
                    ->when($classId, fn($q) => $q->where(
                        fn($w) => $w->where('class_id', $classId)->orWhereNull('class_id')
                    ))
                    ->get();

                $subjects->each(fn($s) => $s->is_enrolled = isset($enrolledSubjectIds[$s->id]));

                return [
                    'id'          => $term->id,
                    'term_number' => $term->term_number,
                    'name'        => $term->name ?? ('الفصل ' . $term->term_number),
                    'status'      => $term->status,
                    'start_date'  => $term->start_date?->format('Y-m-d'),
                    'end_date'    => $term->end_date?->format('Y-m-d'),
                    'subjects'    => ProgramSubjectResource::collection($subjects)->resolve(),
                ];
            })->values();

            return response()->json(['success' => true, 'data' => $payload]);
        }

        // ── Course / training / english: program sessions ───────────────────
        $sessions = Session::where('program_id', $program->id)
            ->when($classId, fn($q) => $q->where('class_id', $classId))
            ->orderBy('session_number')
            ->get();

        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('session_id');

        $payload['sessions'] = $sessions->map(function ($session) use ($attendances) {
            $attendance = $attendances->get($session->id);

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
                'attendance'       => [
                    'attended'         => $attendance?->attended ?? false,
                    'joined_at'        => $attendance?->joined_at,
                    'duration_minutes' => $attendance?->duration_minutes ?? 0,
                ],
            ];
        })->values();

        return response()->json(['success' => true, 'data' => $payload]);
    }

    /**
     * GET /api/v1/student/sessions-by/{key}
     *
     * Same payload as sessionsBy(), but with a single value in the path:
     *   /sessions-by/5         → numeric  → program id 5
     *   /sessions-by/MATH101   → non-numeric → subject with that code
     *
     * Subject codes are only unique per program (see the
     * subjects_program_code_unique index), so a code the student holds in two
     * different programs is ambiguous — that returns 409 with the candidates
     * rather than silently picking one. Use ?program_id= to disambiguate.
     */
    public function sessionsByKey(Request $request, string $key)
    {
        $student = auth()->user();

        // Numeric key → program id. Hand straight over to the query-param action.
        if (ctype_digit($key)) {
            $request->query->set('program_id', (int) $key);
            $request->request->remove('subject_id');
            return $this->sessionsBy($request);
        }

        // Non-numeric key → subject code, resolved within the student's programs
        // only (so one student's code can't leak another program's subject).
        $matches = Subject::where('code', $key)
            ->whereIn('program_id', $student->allProgramIds())
            ->get();

        if ($matches->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'المادة غير موجودة أو غير مسجّل في برنامجها',
            ], 404);
        }

        // Same code in more than one of the student's programs → ask, don't guess.
        if ($matches->count() > 1) {
            $scoped = $request->filled('program_id')
                ? $matches->where('program_id', $request->integer('program_id'))
                : $matches;

            if ($scoped->count() !== 1) {
                return response()->json([
                    'success'    => false,
                    'message'    => 'الكود مكرر في أكثر من برنامج — حدّد program_id',
                    'candidates' => $scoped->map(fn($s) => [
                        'subject_id' => $s->id,
                        'program_id' => $s->program_id,
                        'name'       => $s->name_ar,
                    ])->values(),
                ], 409);
            }

            $matches = $scoped;
        }

        $request->query->set('subject_id', $matches->first()->id);
        $request->query->remove('program_id');

        return $this->sessionsBy($request);
    }

    /**
     * GET /api/v1/student/sessions-by?program_id={id}   or   ?subject_id={id}
     * Return the sessions of a program or a subject, scoped to the student's class.
     *
     * Exactly one of program_id / subject_id is required. Sessions are limited to
     * the student's class for that program (class_id NULL sessions are shared and
     * always included).
     */
    public function sessionsBy(Request $request)
    {
        $request->validate([
            // Exactly one of the two — required if the other is missing, and
            // prohibited if the other is present (no silent tie-breaking).
            // Each id is checked against its OWN table via `exists`, so passing a
            // subject id in the program_id slot (or vice-versa) fails with a 422
            // instead of silently returning an empty list.
            'program_id' => 'required_without:subject_id|prohibits:subject_id|integer|exists:programs,id',
            'subject_id' => 'required_without:program_id|integer|exists:subjects,id',
        ]);

        $student = auth()->user();

        // Resolve the anchor program so we can find the student's class for it.
        if ($request->filled('subject_id')) {
            $subject = Subject::find($request->integer('subject_id'));
            if (!$subject) {
                return response()->json(['success' => false, 'message' => 'المادة غير موجودة'], 404);
            }
            // A subject's program comes from its own program_id, or its term's program.
            $programId = $subject->program_id ?? $subject->term?->program_id;
        } else {
            $programId = $request->integer('program_id');
        }

        // The student must be enrolled in (or belong to a class of) this program.
        if (!$programId || !$student->allProgramIds()->contains($programId)) {
            return response()->json(['success' => false, 'message' => 'غير مسجل في هذا البرنامج'], 403);
        }

        $classId = $student->classIdForProgram((int) $programId);

        // Build the session query for either a single subject or the whole program.
        $query = Session::query()
            ->when($request->filled('subject_id'),
                fn($q) => $q->where('subject_id', $request->integer('subject_id')),
                fn($q) => $q->where('program_id', $programId))
            // Class scoping: the student's class + class-agnostic (NULL) sessions.
            ->when($classId, fn($q) => $q->where(
                fn($w) => $w->where('class_id', $classId)->orWhereNull('class_id')
            ))
            ->orderBy('session_number');

        $sessions = $query->get();

        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('session_id');

        $data = $sessions->map(function ($session) use ($attendances) {
            $attendance = $attendances->get($session->id);

            $status = match (true) {
                !is_null($session->ended_at)   => 'ended',
                !is_null($session->started_at) => 'live',
                default                        => 'upcoming',
            };

            return [
                'id'               => $session->id,
                'title'            => $session->title_ar ?? $session->title ?? null,
                'session_number'   => $session->session_number,
                'subject_id'       => $session->subject_id,
                'type'             => $session->type,
                'status'           => $status,
                'scheduled_at'     => $session->scheduled_at,
                'duration_minutes' => $session->duration_minutes,
                'join_url'         => $session->type === 'live_zoom' ? $session->zoom_join_url : null,
                'zoom_meeting_id'  => $session->zoom_meeting_id ?? null,
                'started_at'       => $session->started_at,
                'video_url'        => $session->type === 'recorded_video' ? $session->getVideoUrl() : null,
                'recording_url'    => $session->recording_url ?? null,
                'attendance'       => [
                    'attended'         => $attendance?->attended ?? false,
                    'joined_at'        => $attendance?->joined_at,
                    'duration_minutes' => $attendance?->duration_minutes ?? 0,
                ],
            ];
        })->values();

        return response()->json([
            'success'    => true,
            'program_id' => (int) $programId,
            'subject_id' => $request->filled('subject_id') ? $request->integer('subject_id') : null,
            'class_id'   => $classId,
            'total'      => $data->count(),
            'data'       => $data,
        ]);
    }

    /**
     * GET /api/v1/student/files-by?program_id={id}   or   ?subject_id={id}
     * Return the files of a program or a subject the student has access to.
     *
     * Exactly one of program_id / subject_id is required. SubjectFile rows are not
     * class-scoped (no class_id column) — access is gated by program enrollment.
     */
    public function filesBy(Request $request)
    {
        $request->validate([
            // Exactly one of the two — required if the other is missing, and
            // prohibited if the other is present (no silent tie-breaking).
            // Each id is checked against its OWN table via `exists`, so passing a
            // subject id in the program_id slot (or vice-versa) fails with a 422
            // instead of silently returning an empty list.
            'program_id' => 'required_without:subject_id|prohibits:subject_id|integer|exists:programs,id',
            'subject_id' => 'required_without:program_id|integer|exists:subjects,id',
        ]);

        $student = auth()->user();

        // Resolve the anchor program to authorize access.
        if ($request->filled('subject_id')) {
            $subject = Subject::find($request->integer('subject_id'));
            if (!$subject) {
                return response()->json(['success' => false, 'message' => 'المادة غير موجودة'], 404);
            }
            $programId = $subject->program_id ?? $subject->term?->program_id;
        } else {
            $programId = $request->integer('program_id');
        }

        if (!$programId || !$student->allProgramIds()->contains($programId)) {
            return response()->json(['success' => false, 'message' => 'غير مسجل في هذا البرنامج'], 403);
        }

        $files = SubjectFile::query()
            ->when($request->filled('subject_id'),
                fn($q) => $q->where('subject_id', $request->integer('subject_id')),
                fn($q) => $q->where('program_id', $programId))
            ->orderBy('order')
            ->get();

        $data = $files->map(fn($f) => [
            'id'    => $f->id,
            'title' => $f->title,
            'url'   => filter_var($f->file_path, FILTER_VALIDATE_URL)
                ? $f->file_path
                : asset('storage/' . $f->file_path),
            'type'  => $f->file_type,
            'size'  => $f->file_size,
            'name'  => $f->file_original_name,
            'order' => $f->order,
        ])->values();

        return response()->json([
            'success'    => true,
            'program_id' => (int) $programId,
            'subject_id' => $request->filled('subject_id') ? $request->integer('subject_id') : null,
            'total'      => $data->count(),
            'data'       => $data,
        ]);
    }

    /**
     * GET /api/v1/student/homework-by?program_id={id}   or   ?subject_id={id}
     * Return the homework of a program or a subject, scoped to the student's class,
     * each with the student's own submission if any.
     *
     * Exactly one of program_id / subject_id is required.
     */
    public function homeworkBy(Request $request)
    {
        $request->validate([
            // Exactly one of the two — required if the other is missing, and
            // prohibited if the other is present (no silent tie-breaking).
            // Each id is checked against its OWN table via `exists`, so passing a
            // subject id in the program_id slot (or vice-versa) fails with a 422
            // instead of silently returning an empty list.
            'program_id' => 'required_without:subject_id|prohibits:subject_id|integer|exists:programs,id',
            'subject_id' => 'required_without:program_id|integer|exists:subjects,id',
        ]);

        $student = auth()->user();

        // Resolve the anchor program to authorize access and find the student's class.
        if ($request->filled('subject_id')) {
            $subject = Subject::find($request->integer('subject_id'));
            if (!$subject) {
                return response()->json(['success' => false, 'message' => 'المادة غير موجودة'], 404);
            }
            $programId = $subject->program_id ?? $subject->term?->program_id;
        } else {
            $programId = $request->integer('program_id');
        }

        if (!$programId || !$student->allProgramIds()->contains($programId)) {
            return response()->json(['success' => false, 'message' => 'غير مسجل في هذا البرنامج'], 403);
        }

        $classId = $student->classIdForProgram((int) $programId);

        $homeworks = Homework::query()
            ->when($request->filled('subject_id'),
                fn($q) => $q->where('subject_id', $request->integer('subject_id')),
                fn($q) => $q->where('program_id', $programId))
            // Class scoping: the student's class + class-agnostic (NULL) homework.
            ->when($classId, fn($q) => $q->where(
                fn($w) => $w->where('class_id', $classId)->orWhereNull('class_id')
            ))
            ->orderByDesc('due_date')
            ->get();

        $submissions = HomeworkSubmission::where('student_id', $student->id)
            ->whereIn('homework_id', $homeworks->pluck('id'))
            ->get()
            ->keyBy('homework_id');

        $data = $homeworks->map(function ($hw) use ($submissions) {
            $submission = $submissions->get($hw->id);

            return [
                'id'             => $hw->id,
                'title_ar'       => $hw->title_ar,
                'title_en'       => $hw->title_en,
                'description_ar' => $hw->description_ar,
                'description_en' => $hw->description_en,
                'subject_id'     => $hw->subject_id,
                'due_date'       => $hw->due_date?->format('Y-m-d'),
                'attachment_url' => $hw->file_url,
                'submission'     => $submission ? [
                    'id'           => $submission->id,
                    'content'      => $submission->content,
                    'file_url'     => $submission->file_path
                        ? (filter_var($submission->file_path, FILTER_VALIDATE_URL)
                            ? $submission->file_path
                            : asset('storage/' . $submission->file_path))
                        : null,
                    'submitted_at' => $submission->submitted_at?->toIso8601String(),
                    'grade'        => $submission->grade,
                    'feedback'     => $submission->feedback,
                ] : null,
            ];
        })->values();

        return response()->json([
            'success'    => true,
            'program_id' => (int) $programId,
            'subject_id' => $request->filled('subject_id') ? $request->integer('subject_id') : null,
            'class_id'   => $classId,
            'total'      => $data->count(),
            'data'       => $data,
        ]);
    }

    /**
     * GET /api/v1/student/program-subjects?filter=current|upcoming|past
     * Get all subjects in student's program grouped by term, with optional filter
     */
    public function subjects(Request $request)
    {
        $student = auth()->user();

        $filter = $request->query('filter', 'current'); // current | past | all

        // The class is the anchor — a student may belong to a class without being
        // enrolled in its program. Resolve the class from the student_programs pivot
        // first, then the legacy users.class_id column.
        $classId = $student->programs()->wherePivotNotNull('class_id')->first()?->pivot?->class_id
            ?? $student->class_id;

        $class   = $classId ? \App\Models\ProgramClass::find($classId) : null;
        $program = $class?->program ?? $student->program;

        if (!$program) {
            return response()->json(['success' => false, 'message' => 'غير مسجل في برنامج أو فصل'], 403);
        }

        // Prefer the student's own class terms; fall back to shared (class_id NULL)
        $hasClassTerms = $classId
            ? \App\Models\Term::where('program_id', $program->id)->where('class_id', $classId)->exists()
            : false;
        $termScope = fn($q) => $hasClassTerms ? $q->where('class_id', $classId) : $q->whereNull('class_id');

        $termsQuery = $program->terms()->orderBy('term_number')->where(fn($q) => $termScope($q));

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

        // Load subjects via BOTH relationships: direct term_id and pivot term_subject,
        // restricted to the student's own class (class-less shared subjects included).
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
            ->when($classId, fn($q) => $q->where(
                fn($w) => $w->where('class_id', $classId)->orWhereNull('class_id')
            ));

        $subjects = $subjectQuery->get();

        $enrolledSubjectIds = Enrollment::where('student_id', $student->id)
            ->pluck('subject_id')
            ->flip();

        // Flag enrollment for the resource to expose without an extra lookup.
        $subjects->each(fn($subject) => $subject->is_enrolled = isset($enrolledSubjectIds[$subject->id]));

        $data = ProgramSubjectResource::collection($subjects);

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
            'success'    => true,
            'filter'     => $filter,
            'class_id'   => $classId,
            'class_name' => $class?->name,
            'terms'      => $termData,
            'data'       => $data,
            'total'      => $data->count(),
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
        $classId = $student->classIdForProgram($program->id);

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

            // Attendance is only counted when the student actually joins a session
            // (attended = true) — mirrors the /student/attendance web page. We only
            // count joined sessions; there is no "absent" denominator.
            $sessionIds = Session::whereIn('subject_id', $termSubjectIds)->pluck('id');

            $attendedSessions = Attendance::where('student_id', $student->id)
                ->whereIn('session_id', $sessionIds)
                ->where('attended', true)
                ->count();

            $totalSessions  = $attendedSessions;
            $attendanceRate = $totalSessions > 0 ? 100.0 : 0;

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
            // Only joined sessions are counted (attended = true) — mirrors the
            // /student/attendance web page.
            $sessionIds = Session::where('program_id', $program->id)->pluck('id');

            $attendedSessions = Attendance::where('student_id', $student->id)
                ->whereIn('session_id', $sessionIds)
                ->where('attended', true)
                ->count();

            $totalSessions  = $attendedSessions;
            $attendanceRate = $totalSessions > 0 ? 100.0 : 0;

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
