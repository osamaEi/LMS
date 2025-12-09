<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with(['term.program', 'teacher'])
            ->withCount('sessions')
            ->latest()
            ->paginate(15);

        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $terms = Term::with('program')->where('status', 'active')->get();
        $teachers = User::where('role', 'teacher')->get();

        return view('admin.subjects.create', compact('terms', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'term_id' => 'required|exists:terms,id',
            'teacher_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:subjects,code',
            'description' => 'nullable|string',
            'banner_photo' => 'nullable|string',
            'credits' => 'nullable|integer|min:1',
            'total_hours' => 'nullable|integer|min:1',
            'max_students' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive,completed',
        ]);

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم إضافة المادة بنجاح');
    }

    public function show(Subject $subject)
    {
        $subject->load(['term.program', 'teacher', 'sessions' => function($query) {
            $query->latest();
        }]);

        return view('admin.subjects.show', compact('subject'));
    }

    public function edit(Subject $subject)
    {
        $terms = Term::with('program')->where('status', 'active')->get();
        $teachers = User::where('role', 'teacher')->get();

        return view('admin.subjects.edit', compact('subject', 'terms', 'teachers'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'term_id' => 'required|exists:terms,id',
            'teacher_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string',
            'banner_photo' => 'nullable|string',
            'credits' => 'nullable|integer|min:1',
            'total_hours' => 'nullable|integer|min:1',
            'max_students' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive,completed',
        ]);

        $subject->update($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم تحديث المادة بنجاح');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم حذف المادة بنجاح');
    }
}
