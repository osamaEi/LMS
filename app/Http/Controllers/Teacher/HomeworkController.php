<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Homework\GradeSubmissionRequest;
use App\Http\Requests\Homework\StoreHomeworkRequest;
use App\Http\Requests\Homework\UpdateHomeworkRequest;
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
        $teacher = auth()->user();

        ['subjects' => $subjects, 'homeworks' => $homeworks] =
            $this->homeworkService->teacherHomeworkDashboard($teacher);

        return view('teacher.homework.index', compact('subjects', 'homeworks'));
    }

    public function store(StoreHomeworkRequest $request)
    {
        $data = $request->only([
            'subject_id', 'program_id',
            'title_ar', 'title_en', 'description_ar', 'description_en', 'due_date',
        ]);

        $this->homeworkService->create($data, $request->file('file'));

        return back()->with('success', 'تم إضافة الواجب بنجاح وإشعار المتدربون');
    }

    public function update(UpdateHomeworkRequest $request, Homework $homework)
    {
        $data = $request->only(['title_ar', 'title_en', 'description_ar', 'description_en', 'due_date']);

        $this->homeworkService->update($homework, $data, $request->file('file'));

        return back()->with('success', 'تم تحديث الواجب بنجاح');
    }

    public function destroy(Homework $homework)
    {
        $this->homeworkService->delete($homework);

        return back()->with('success', 'تم حذف الواجب');
    }

    public function gradeSubmission(GradeSubmissionRequest $request, Homework $homework, HomeworkSubmission $submission)
    {
        $this->homeworkService->gradeSubmission($submission, $request->only(['grade', 'feedback']));

        return back()->with('success', 'تم حفظ التقييم بنجاح');
    }
}
