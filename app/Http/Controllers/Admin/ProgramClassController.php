<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\User;
use Illuminate\Http\Request;

class ProgramClassController extends Controller
{
    public function index(Request $request)
    {
        $query = ProgramClass::with(['program', 'teacher'])
            ->withCount('students');

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $classes  = $query->latest()->paginate(20)->withQueryString();
        $programs = Program::orderBy('name_ar')->get(['id', 'name_ar', 'name_en']);
        $teachers = User::where('role', 'teacher')->orderBy('name')->get(['id', 'name']);

        return view('admin.classes.index', compact('classes', 'programs', 'teachers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'program_id'   => 'required|exists:programs,id',
            'name'         => 'required|string|max:255',
            'teacher_id'   => 'nullable|exists:users,id',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'max_students' => 'nullable|integer|min:1',
            'status'       => 'nullable|in:active,inactive,completed',
        ]);

        $class = ProgramClass::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'class' => $class->load('teacher')]);
        }

        return back()->with('success', 'تم إنشاء المجموعة بنجاح');
    }

    public function update(Request $request, ProgramClass $class)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'teacher_id'   => 'nullable|exists:users,id',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'max_students' => 'nullable|integer|min:1',
            'status'       => 'nullable|in:active,inactive,completed',
        ]);

        $class->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'تم تحديث المجموعة');
    }

    public function destroy(ProgramClass $class)
    {
        // Unassign students first
        User::where('class_id', $class->id)->update(['class_id' => null]);
        $class->delete();

        return response()->json(['success' => true]);
    }

    public function students(ProgramClass $class)
    {
        $students = $class->students()->get(['id', 'name', 'email', 'national_id', 'status', 'profile_photo']);
        return response()->json(['students' => $students]);
    }

    public function assignStudents(Request $request, ProgramClass $class)
    {
        $request->validate([
            'student_ids'   => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        $inserted = User::whereIn('id', $request->student_ids)
            ->where(function ($q) use ($class) {
                $q->whereNull('class_id')->orWhere('class_id', '!=', $class->id);
            })
            ->where('program_id', $class->program_id)
            ->update(['class_id' => $class->id]);

        return response()->json(['success' => true, 'assigned' => $inserted]);
    }

    public function removeStudent(Request $request, ProgramClass $class)
    {
        $request->validate(['student_id' => 'required|exists:users,id']);
        User::where('id', $request->student_id)->where('class_id', $class->id)->update(['class_id' => null]);
        return response()->json(['success' => true]);
    }

    public function availableStudents(ProgramClass $class)
    {
        $students = User::where('program_id', $class->program_id)
            ->where('role', 'student')
            ->where(function ($q) use ($class) {
                $q->whereNull('class_id')->orWhere('class_id', $class->id);
            })
            ->get(['id', 'name', 'email', 'national_id', 'class_id']);

        return response()->json(['students' => $students]);
    }
}
