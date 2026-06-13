<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\Student\SubjectFileResource;
use App\Http\Resources\Student\SubjectHomeworkResource;
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
        $student = auth()->user();

        $subject = Subject::whereHas('enrollments', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
            ->with(['term.program', 'teacher:id,name,email,profile_photo'])
            ->findOrFail($id);

        // Guard: a class-scoped subject must match the student's class
        if ($subject->class_id !== null && $subject->class_id != $student->class_id) {
            return response()->json(['success' => false, 'message' => 'هذا المقرر غير متاح لمجموعتك'], 403);
        }

        $assignedIds = Attendance::where('student_id', $student->id)->pluck('session_id');
        if ($student->class_id) {
            $assignedIds = Session::whereIn('id', $assignedIds)
                ->where('class_id', $student->class_id)->pluck('id');
        }

        $sessions = Session::where('subject_id', $id)
            ->whereIn('id', $assignedIds)
            ->with('files')
            ->orderBy('session_number', 'asc')
            ->get();

        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('session_id');

        // Progress
        $totalSessions = $sessions->count();
        $attendedSessions = $attendances->where('attended', true)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'subject' => $subject,
                'sessions' => $sessions,
                'attendances' => $attendances,
                'progress' => [
                    'total_sessions' => $totalSessions,
                    'attended_sessions' => $attendedSessions,
                    'percentage' => $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100, 1) : 0,
                ],
            ],
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

        // Resolve the program this subject belongs to, then get the student's class for it
        $subjectProgramId = $subject->term?->program_id
            ?? $subject->terms()->pluck('program_id')->first();

        $classId = $subjectProgramId ? $student->classIdForProgram((int) $subjectProgramId) : null;

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

        if ($classId) {
            $query->where('class_id', $classId);
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
     * GET /api/v1/student/subjects/{id}/sessions
     * List all sessions for a subject with join links, schedule and attendance.
     */
    public function sessions($id)
    {
        $resolved = $this->resolveSubjectForStudent($id, ['term.program', 'teacher:id,name,profile_photo']);
        if ($resolved instanceof \Illuminate\Http\JsonResponse) {
            return $resolved;
        }
        [$subject, $classId] = $resolved;

        $student  = auth()->user();
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
     * List homework across the subject's sessions.
     */
    public function homework($id)
    {
        $resolved = $this->resolveSubjectForStudent($id, ['term', 'terms']);
        if ($resolved instanceof \Illuminate\Http\JsonResponse) {
            return $resolved;
        }
        [$subject, $classId] = $resolved;

        $sessions = $this->subjectSessionQuery($id, $classId, ['homework'])->get();

        $sessionsWithHomework = $sessions->filter(fn($s) => $s->homework)->values();

        return response()->json([
            'success' => true,

            'data' => SubjectHomeworkResource::collection($sessionsWithHomework)->resolve(),
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
