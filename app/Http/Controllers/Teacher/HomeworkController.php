<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Session;
use App\Models\Subject;
use App\Notifications\HomeworkCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeworkController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();

        // Classes this teacher is assigned to teach.
        $classIds = \App\Models\ProgramClass::where('teacher_id', $teacher->id)->pluck('id');

        // Sessions whose subject belongs to one of the teacher's classes,
        // OR that are assigned to this teacher directly,
        // OR whose subject was directly assigned (legacy).
        $sessions = Session::where(function ($q) use ($teacher, $classIds) {
                $q->whereHas('subject', function ($sq) use ($teacher, $classIds) {
                    $sq->whereIn('class_id', $classIds)
                       ->orWhereHas('term', fn($tq) => $tq->whereIn('class_id', $classIds))
                       ->orWhere(fn($aq) => $aq->assignedToTeacher($teacher->id));
                })->orWhere('teacher_id', $teacher->id);
            })
            ->with(['subject', 'homework'])
            ->orderByDesc('scheduled_at')
            ->get();

        $withHomework    = $sessions->filter(fn($s) => $s->homework)->values();
        $withoutHomework = $sessions->filter(fn($s) => !$s->homework)->values();

        return view('teacher.homework.index', compact('withHomework', 'withoutHomework'));
    }

    public function store(Request $request, Session $session)
    {
        //$this->authorizeSession($session);

        $request->validate([
            'title_ar'       => 'nullable|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'due_date'       => 'nullable|date',
            'file'           => 'nullable|file|max:20480',
        ]);

        $data = $request->only(['title_ar', 'title_en', 'description_ar', 'description_en', 'due_date']);
        $data['session_id'] = $session->id;

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('homework-files', 'public');
            $data['file_path'] = $path;
            $data['file_name'] = $request->file('file')->getClientOriginalName();
        }

        $homework = Homework::create($data);

        // Notify active students enrolled in this session's subject
        $session->loadMissing('subject');
        $students = Enrollment::where('subject_id', $session->subject_id)
            ->where('status', 'active')
            ->with('student')
            ->get()
            ->pluck('student')
            ->filter();

        foreach ($students as $student) {
            $student->notify(new HomeworkCreatedNotification($homework));
        }

        return back()->with('success', 'تم إضافة الواجب بنجاح وإشعار  المتدربون ');
    }

    public function update(Request $request, Session $session)
    {
       // $this->authorizeSession($session);

        $homework = $session->homework;
        if (!$homework) {
            return back()->withErrors(['homework' => 'الواجب غير موجود']);
        }

        $request->validate([
            'title_ar'       => 'nullable|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'due_date'       => 'nullable|date',
            'file'           => 'nullable|file|max:20480',
        ]);

        $data = $request->only(['title_ar', 'title_en', 'description_ar', 'description_en', 'due_date']);

        if ($request->hasFile('file')) {
            if ($homework->file_path) {
                Storage::disk('public')->delete($homework->file_path);
            }
            $path = $request->file('file')->store('homework-files', 'public');
            $data['file_path'] = $path;
            $data['file_name'] = $request->file('file')->getClientOriginalName();
        }

        $homework->update($data);

        return back()->with('success', 'تم تحديث الواجب بنجاح');
    }

    public function destroy(Session $session)
    {
        $this->authorizeSession($session);

        $homework = $session->homework;
        if ($homework) {
            if ($homework->file_path) {
                Storage::disk('public')->delete($homework->file_path);
            }
            $homework->delete();
        }

        return back()->with('success', 'تم حذف الواجب');
    }

    public function gradeSubmission(Request $request, Session $session, HomeworkSubmission $submission)
    {
        $this->authorizeSession($session);

        $request->validate([
            'grade'    => 'nullable|integer|min:0|max:100',
            'feedback' => 'nullable|string|max:500',
        ]);

        $submission->update([
            'grade'    => $request->grade,
            'feedback' => $request->feedback,
        ]);

        return back()->with('success', 'تم حفظ التقييم بنجاح');
    }

    private function authorizeSession(Session $session): void
    {
        $session->loadMissing('subject');
        if ($session->subject_id && !$session->subject->isAssignedToTeacher(auth()->id())) {
            abort(403);
        }
        if ($session->program_id) {
            $allowed = auth()->user()->teachingPrograms()
                ->whereIn('type', ['training', 'english', 'course'])
                ->where('id', $session->program_id)->exists();
            if (!$allowed) abort(403);
        }
    }
}
