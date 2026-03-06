<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\Session;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeworkController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();

        // All sessions for this teacher's subjects, with homework
        $sessions = Session::whereHas('subject', fn($q) => $q->where('teacher_id', $teacher->id))
            ->with(['subject', 'homework'])
            ->orderByDesc('scheduled_at')
            ->get();

        $withHomework    = $sessions->filter(fn($s) => $s->homework)->values();
        $withoutHomework = $sessions->filter(fn($s) => !$s->homework)->values();

        return view('teacher.homework.index', compact('withHomework', 'withoutHomework'));
    }

    public function store(Request $request, Session $session)
    {
        $this->authorizeSession($session);

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

        Homework::create($data);

        return back()->with('success', 'تم إضافة الواجب بنجاح');
    }

    public function update(Request $request, Session $session)
    {
        $this->authorizeSession($session);

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

    private function authorizeSession(Session $session): void
    {
        $session->loadMissing('subject');
        if ($session->subject->teacher_id !== auth()->id()) {
            abort(403);
        }
    }
}
