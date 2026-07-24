<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\Student\ProgramSubjectResource;
use App\Http\Resources\Student\SubjectFileResource;
use App\Http\Resources\Student\SubjectHomeworkResource;
use App\Http\Resources\Student\SubjectResource;
use App\Http\Resources\Student\SubjectSessionResource;
use App\Models\Subject;
use App\Models\Session;
use App\Models\Attendance;
use App\Models\Unit;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * GET /api/v1/student/subjects/{id}
     * Show subject details with sessions and attendance
     */
    public function show($id)
    {
        // Access is gated by program membership (same rule as the other subject
        // endpoints) rather than an enrollments row, so a subject listed by
        // /my-program/{id}/subjects can always be opened.
        $resolved = $this->resolveSubjectForStudent($id, [
            'term',
            'teacher:id,name,profile_photo',
        ]);
        if ($resolved instanceof \Illuminate\Http\JsonResponse) {
            return $resolved;
        }
        [$subject, $classId] = $resolved;

        $student = auth()->user();

        $subject->loadCount([
            'sessions',
            'sessions as recordings_count' => fn($q) => $q->where(
                fn($w) => $w->whereNotNull('video_path')->orWhereNotNull('video_url')
            ),
        ]);

        $subject->is_enrolled = \App\Models\Enrollment::where('student_id', $student->id)
            ->where('subject_id', $subject->id)
            ->exists();

        // The term this subject sits in — direct term_id, else the pivot.
        $term = $subject->term ?? $subject->terms()->orderBy('term_number')->first();

        $sessions = $this->subjectSessionQuery($id, $classId)->get();

        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('session_id');

        $totalSessions    = $sessions->count();
        $attendedSessions = $attendances->where('attended', true)->count();

        $data = (new ProgramSubjectResource($subject))->resolve() + [
            'term_id'     => $term?->id,
            'term_number' => $term?->term_number,
            'term_name'   => $term ? ($term->name ?? ('الفصل ' . $term->term_number)) : null,
            'program_id'  => $term?->program_id,
            'class_id'    => $classId,
            'progress'    => [
                'total_sessions'    => $totalSessions,
                'attended_sessions' => $attendedSessions,
                'percentage'        => $totalSessions > 0
                    ? round(($attendedSessions / $totalSessions) * 100, 1)
                    : 0,
            ],
        ];

        return response()->json([
            'success' => true,
            'subject' => $data,
        ]);
    }

    /**
     * Resolve a subject for the authenticated student and verify access.
     *
     * Returns [Subject $subject, ?int $classId] on success, or a JsonResponse
     * (403) when the subject is class-scoped to a class the student isn't in.
     */
    private function resolveSubjectForStudent($id, array $with = [])
    {
        $student       = auth()->user();
        $allProgramIds = $student->allProgramIds();

        // Find the subject and verify it belongs to one of the student's programs
        $subject = Subject::where(function ($q) use ($allProgramIds) {
            $q->whereHas('term', fn($tq) => $tq->whereIn('program_id', $allProgramIds))
              ->orWhereHas('terms', fn($tq) => $tq->whereIn('program_id', $allProgramIds));
        })->with($with)
          ->findOrFail($id);

        // Resolve the program this subject belongs to.
        $subjectProgramId = $subject->term?->program_id
            ?? $subject->terms()->pluck('program_id')->first();

        // Resolve the student's class. The class is the anchor: prefer the class
        // tied to this subject's program, then any pivot class, then the legacy
        // users.class_id — so the filter works even if the student isn't formally
        // enrolled in the program.
        $classId = ($subjectProgramId ? $student->classIdForProgram((int) $subjectProgramId) : null)
            ?? $student->programs()->wherePivotNotNull('class_id')->first()?->pivot?->class_id
            ?? $student->class_id;

        // Guard: a class-scoped subject must match the student's class
        if ($subject->class_id !== null && $subject->class_id != $classId) {
            return response()->json(['success' => false, 'message' => 'هذا المقرر غير متاح لمجموعتك'], 403);
        }

        return [$subject, $classId];
    }

    /**
     * Build the base session query for a subject, scoped to the student's class.
     */
    private function subjectSessionQuery($subjectId, ?int $classId, array $with = [])
    {
        $query = Session::where('subject_id', $subjectId)
            ->with($with)
            ->orderBy('session_number', 'asc');

        // Always scope to the student's class. With no class, only show class-less
        // sessions so other classes' sessions never leak.
        if ($classId) {
            $query->where('class_id', $classId);
        } else {
            $query->whereNull('class_id');
        }

        return $query;
    }

    /**
     * Map a file model to its API representation.
     */
    private function mapFile($f): array
    {
        return [
            'id'    => $f->id,
            'title' => $f->title,
            'url'   => asset('storage/' . $f->file_path),
            'type'  => $f->file_type,
            'size'  => $f->file_size,
        ];
    }

    /**
     * GET /api/v1/student/subjects/{id}/info
     * Return subject details (name, code, banner, teacher).
     */
    public function info($id)
    {
        $resolved = $this->resolveSubjectForStudent($id, ['teacher:id,name,profile_photo']);
        if ($resolved instanceof \Illuminate\Http\JsonResponse) {
            return $resolved;
        }
        [$subject] = $resolved;

        return response()->json([
            'success' => true,
            'data'    => (new SubjectResource($subject))->resolve(),
        ]);
    }

    /**
     * GET /api/v1/student/subjects/{id}/sessions/{classId}
     * List a subject's sessions for a specific class. classId is mandatory and
     * must be a class the student belongs to.
     */
    public function sessions($id, $classId)
    {
        $resolved = $this->resolveSubjectForStudent($id, ['term.program', 'teacher:id,name,profile_photo']);
        if ($resolved instanceof \Illuminate\Http\JsonResponse) {
            return $resolved;
        }
        [$subject] = $resolved;

        $student = auth()->user();
        $classId = (int) $classId;

        // The class in the URL must be one the student actually belongs to.
        $studentClassIds = $student->programs()->pluck('student_programs.class_id')
            ->push($student->class_id)
            ->filter()->map(fn($c) => (int) $c)->unique();

        if (!$studentClassIds->contains($classId)) {
            return response()->json(['success' => false, 'message' => 'هذا الفصل غير متاح لك'], 403);
        }

        // The subject, if class-scoped, must match the requested class.
        if ($subject->class_id !== null && (int) $subject->class_id !== $classId) {
            return response()->json(['success' => false, 'message' => 'هذا المقرر غير متاح لهذا الفصل'], 403);
        }

        $sessions = $this->subjectSessionQuery($id, $classId, ['files'])->get();

        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('session_id');

        $totalSessions    = $sessions->count();
        $attendedSessions = $attendances->where('attended', true)->count();

        $resource = SubjectSessionResource::collection($sessions)
            ->additional(['attendances' => $attendances]);

        return response()->json([
            'success' => true,
            'data' => $resource->resolve(),
        ]);
    }

    /**
     * GET /api/v1/student/subjects/{id}/homework
     * List the subject's homework.
     */
    public function homework($id)
    {
        $resolved = $this->resolveSubjectForStudent($id, ['term', 'terms']);
        if ($resolved instanceof \Illuminate\Http\JsonResponse) {
            return $resolved;
        }
        [$subject, $classId] = $resolved;

        $homeworks = \App\Models\Homework::where('subject_id', $subject->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => SubjectHomeworkResource::collection($homeworks)->resolve(),
        ]);
    }

    /**
     * GET /api/v1/student/subjects/{id}/files
     * List the subject's own files and files attached to its sessions.
     */
    public function files($id)
    {
        $resolved = $this->resolveSubjectForStudent($id, ['term', 'terms', 'files']);
        if ($resolved instanceof \Illuminate\Http\JsonResponse) {
            return $resolved;
        }
        [$subject, $classId] = $resolved;

        $sessions = $this->subjectSessionQuery($id, $classId, ['files'])->get();

        $sessionFiles = $sessions->flatMap(function ($session) {
            return $session->files->map(function ($file) use ($session) {
                return (new SubjectFileResource($file))
                    ->additional(['session' => $session])
                    ->resolve();
            });
        })->values();

        $subjectFiles = SubjectFileResource::collection($subject->files)->resolve();

        return response()->json([
            'success' => true,
          'subject_files' => $subjectFiles,
                
         
        ]);
    }

    /**
     * GET /api/v1/student/units/{id}
     * Show unit details with its sessions
     */
    public function showUnit($id)
    {
        $student = auth()->user();

        $unit = Unit::with(['subject.enrollments' => function ($q) use ($student) {
            $q->where('student_id', $student->id);
        }])->findOrFail($id);

        // Verify enrollment
        if ($unit->subject->enrollments->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'أنت غير مسجل في هذه المقرر ',
            ], 406);
        }

        $sessions = Session::where('unit_id', $id)
            ->with('files')
            ->orderBy('session_number', 'asc')
            ->get();

        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('session_id');

        return response()->json([
            'success' => true,
            'data' => [
                'unit' => $unit,
                'sessions' => $sessions,
                'attendances' => $attendances,
            ],
        ]);
    }
}
