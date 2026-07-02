<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Homework\SubmitHomeworkRequest;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Services\HomeworkService;

class HomeworkController extends Controller
{
    public function __construct(protected HomeworkService $homeworkService)
    {
    }

    public function index()
    {
        $student = auth()->user();

        $homeworks = $this->homeworkService->homeworksForStudentWeb($student);

        $mySubmissions = $this->homeworkService->submissionsFor($student, $homeworks->pluck('id'));

        return view('student.homework.index', compact('homeworks', 'mySubmissions'));
    }

    public function submit(SubmitHomeworkRequest $request, Homework $homework)
    {
        $student = auth()->user();

        // Reject submissions after the deadline (due_date is a date, so the whole
        // due day is still allowed — only days after it are rejected).
        if ($this->homeworkService->isPastDeadline($homework)) {
            return back()->withErrors(['content' => 'انتهى موعد تسليم هذا الواجب.']);
        }

        $this->homeworkService->submit(
            $homework,
            $student,
            ['content' => $request->input('content')],
            $request->file('file')
        );

        return back()->with('success', 'تم تسليم الواجب بنجاح ✓');
    }

    public function deleteSubmission(HomeworkSubmission $submission)
    {
        if ($submission->student_id !== auth()->id()) abort(403);

        $this->homeworkService->deleteSubmission($submission);

        return back()->with('success', 'تم حذف التسليم');
    }
}
