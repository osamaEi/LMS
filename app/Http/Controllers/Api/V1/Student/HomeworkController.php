<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeworkController extends Controller
{
    /**
     * GET /api/v1/student/homework
     * All homework assigned to the student (same logic as web dashboard)
     */
    public function index(Request $request)
    {
        $student = auth()->user();

        $subjectIds = Subject::where(function ($q) use ($student) {
            $q->where('program_id', $student->program_id)
              ->orWhereHas('term', fn($tq) => $tq->where('program_id', $student->program_id))
              ->orWhereHas('terms', fn($tq) => $tq->where('program_id', $student->program_id))
              ->orWhereHas('enrollments', fn($eq) => $eq->where('student_id', $student->id));
        })->pluck('id');

        // Homework from subjects (diploma)
        $subjectHomeworks = Homework::whereHas('session', fn($q) => $q->whereIn('subject_id', $subjectIds))
            ->with(['session.subject:id,name_ar,name_en,code'])
            ->orderByDesc('created_at')
            ->get();

        // Homework from programs (course/training/english)
        $programHomeworks = Homework::whereHas('session', fn($q) => $q->where('program_id', $student->program_id))
            ->with(['session.program:id,name_ar,name_en'])
            ->orderByDesc('created_at')
            ->get();

        $homeworks = $subjectHomeworks->merge($programHomeworks)->sortByDesc('created_at')->values();

        $mySubmissions = HomeworkSubmission::where('student_id', $student->id)
            ->whereIn('homework_id', $homeworks->pluck('id'))
            ->get()
            ->keyBy('homework_id');

        // Optional filter
        $filter = $request->query('filter'); // pending | submitted | graded
        $data = $homeworks->map(fn($hw) => $this->formatHomework($hw, $mySubmissions->get($hw->id)));

        if ($filter === 'pending') {
            $data = $data->filter(fn($hw) => $hw['submission'] === null)->values();
        } elseif ($filter === 'submitted') {
            $data = $data->filter(fn($hw) => $hw['submission'] !== null && $hw['submission']['grade'] === null)->values();
        } elseif ($filter === 'graded') {
            $data = $data->filter(fn($hw) => isset($hw['submission']['grade']) && $hw['submission']['grade'] !== null)->values();
        }

        return response()->json([
            'success' => true,
            'filter'  => $filter ?? 'all',
            'total'   => $data->count(),
            'stats'   => [
                'total'     => $homeworks->count(),
                'pending'   => $homeworks->filter(fn($hw) => !$mySubmissions->has($hw->id))->count(),
                'submitted' => $mySubmissions->filter(fn($s) => $s->grade === null)->count(),
                'graded'    => $mySubmissions->filter(fn($s) => $s->grade !== null)->count(),
            ],
            'data'    => $data->values(),
        ]);
    }

    /**
     * GET /api/v1/student/homework/{id}
     * Single homework with submission details
     */
    public function show($id)
    {
        $student  = auth()->user();
        $homework = $this->findAccessibleHomework($id, $student);

        $submission = HomeworkSubmission::where('student_id', $student->id)
            ->where('homework_id', $homework->id)
            ->first();

        return response()->json([
            'success' => true,
            'data'    => $this->formatHomework($homework, $submission),
        ]);
    }

    /**
     * POST /api/v1/student/homework/{id}/submit
     * Submit homework (text content and/or file upload or URL)
     */
    public function submit(Request $request, $id)
    {
        $student  = auth()->user();
        $homework = $this->findAccessibleHomework($id, $student);

        $request->validate([
            'content'    => 'nullable|string|max:3000',
            'file'       => 'nullable|file|max:20480',
            'file_url'   => 'nullable|string|max:2048',
        ]);

        if (!$request->filled('content') && !$request->hasFile('file') && !$request->filled('file_url')) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى كتابة نص الإجابة أو رفع ملف أو إرسال رابط.',
            ], 422);
        }

        $data = [
            'homework_id'  => $homework->id,
            'student_id'   => $student->id,
            'content'      => $request->input('content'),
            'submitted_at' => now(),
        ];

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('homework-submissions', 'public');
            $data['file_path'] = $path;
            $data['file_name'] = $request->file('file')->getClientOriginalName();
        } elseif ($request->filled('file_url')) {
            $data['file_path'] = $request->input('file_url');
            $data['file_name'] = basename($request->input('file_url'));
        }

        $submission = HomeworkSubmission::updateOrCreate(
            ['homework_id' => $homework->id, 'student_id' => $student->id],
            $data
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تسليم الواجب بنجاح',
            'data'    => $this->formatSubmission($submission),
        ], 201);
    }

    /**
     * DELETE /api/v1/student/homework/submissions/{id}
     * Delete a submission
     */
    public function deleteSubmission($id)
    {
        $submission = HomeworkSubmission::where('student_id', auth()->id())
            ->findOrFail($id);

        if ($submission->file_path && !filter_var($submission->file_path, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($submission->file_path);
        }

        $submission->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف التسليم بنجاح',
        ]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function findAccessibleHomework($id, $student)
    {
        $subjectIds = Subject::where(function ($q) use ($student) {
            $q->where('program_id', $student->program_id)
              ->orWhereHas('term', fn($tq) => $tq->where('program_id', $student->program_id))
              ->orWhereHas('terms', fn($tq) => $tq->where('program_id', $student->program_id))
              ->orWhereHas('enrollments', fn($eq) => $eq->where('student_id', $student->id));
        })->pluck('id');

        return Homework::where('id', $id)
            ->where(function ($q) use ($subjectIds, $student) {
                $q->whereHas('session', fn($sq) => $sq->whereIn('subject_id', $subjectIds))
                  ->orWhereHas('session', fn($sq) => $sq->where('program_id', $student->program_id));
            })
            ->with(['session.subject:id,name_ar,name_en,code', 'session.program:id,name_ar,name_en'])
            ->firstOrFail();
    }

    private function formatHomework(Homework $homework, ?HomeworkSubmission $submission): array
    {
        $subject = $homework->session?->subject;
        $program = $homework->session?->program;

        return [
            'id'             => $homework->id,
            'title_ar'       => $homework->title_ar,
            'title_en'       => $homework->title_en,
            'description_ar' => $homework->description_ar,
            'description_en' => $homework->description_en,
            'due_date'       => $homework->due_date?->format('Y-m-d'),
            'is_overdue'     => $homework->due_date && $homework->due_date->isPast() && !$submission,
            'attachment_url' => $homework->file_url,
            'session'        => $homework->session ? [
                'id'      => $homework->session->id,
                'title'   => $homework->session->title_ar ?? $homework->session->title,
                'subject' => $subject ? [
                    'id'      => $subject->id,
                    'name_ar' => $subject->name_ar,
                    'name_en' => $subject->name_en,
                    'code'    => $subject->code,
                ] : null,
                'program' => $program ? [
                    'id'      => $program->id,
                    'name_ar' => $program->name_ar,
                    'name_en' => $program->name_en,
                ] : null,
            ] : null,
            'submission' => $submission ? $this->formatSubmission($submission) : null,
        ];
    }

    private function formatSubmission(HomeworkSubmission $submission): array
    {
        return [
            'id'           => $submission->id,
            'content'      => $submission->content,
            'file_url'     => $submission->file_path
                ? (filter_var($submission->file_path, FILTER_VALIDATE_URL)
                    ? $submission->file_path
                    : asset('storage/' . $submission->file_path))
                : null,
            'file_name'    => $submission->file_name,
            'submitted_at' => $submission->submitted_at?->toIso8601String(),
            'grade'        => $submission->grade,
            'feedback'     => $submission->feedback,
        ];
    }
}
