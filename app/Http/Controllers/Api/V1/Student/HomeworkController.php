<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Homework\ApiSubmitHomeworkRequest;
use App\Http\Resources\Student\HomeworkResource;
use App\Http\Resources\Student\HomeworkSubmissionResource;
use App\Models\HomeworkSubmission;
use App\Services\HomeworkService;
use Illuminate\Http\Request;

class HomeworkController extends Controller
{
    public function __construct(protected HomeworkService $homeworkService)
    {
    }

    /**
     * GET /api/v1/student/homework
     * All homework assigned to the student (same logic as web dashboard)
     */
    public function index(Request $request)
    {
        $student = auth()->user();

        $homeworks = $this->homeworkService->homeworksForStudentApi($student);

        $mySubmissions = $this->homeworkService->submissionsFor($student, $homeworks->pluck('id'));

        // Optional filter
        $filter = $request->query('filter'); // pending | submitted | graded
        $data = $homeworks->map(fn($hw) => (new HomeworkResource($hw, $mySubmissions->get($hw->id)))->toArray($request));

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
        $homework = $this->homeworkService->findAccessibleForStudent((int) $id, $student);

        $submission = HomeworkSubmission::where('student_id', $student->id)
            ->where('homework_id', $homework->id)
            ->first();

        return response()->json([
            'success' => true,
            'data'    => (new HomeworkResource($homework, $submission))->toArray(request()),
        ]);
    }

    /**
     * POST /api/v1/student/homework/{id}/submit
     * Submit homework (text content and/or file upload or URL)
     */
    public function submit(ApiSubmitHomeworkRequest $request, $id)
    {
        $student  = auth()->user();
        $homework = $this->homeworkService->findAccessibleForStudent((int) $id, $student);

        if (!$request->filled('content') && !$request->hasFile('file') && !$request->filled('file_url')) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى كتابة نص الإجابة أو رفع ملف أو إرسال رابط.',
            ], 422);
        }

        $submission = $this->homeworkService->submit(
            $homework,
            $student,
            ['content' => $request->input('content')],
            $request->file('file'),
            $request->input('file_url')
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تسليم الواجب بنجاح',
            'data'    => (new HomeworkSubmissionResource($submission))->toArray($request),
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

        $this->homeworkService->deleteSubmission($submission);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف التسليم بنجاح',
        ]);
    }
}
