<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = User::where('role', 'teacher')
            ->withCount('subjects');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $teachers = $query->latest()->paginate(12)->withQueryString();

        // Load both pivot subjects (for modal) and legacy teacher_id subjects (for display)
        $teachers->load('assignedSubjects', 'subjects', 'teachingPrograms');

        $stats = [
            'total'        => User::where('role', 'teacher')->count(),
            'this_month'   => User::where('role', 'teacher')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'with_subjects'=> User::where('role', 'teacher')->has('subjects')->count(),
        ];

        $programsByType = Program::with(['terms.subjects' => function ($q) {
            $q->select('subjects.id', 'subjects.name_ar', 'subjects.name_en', 'subjects.code')
              ->orderBy('subjects.name_ar');
        }])->orderBy('name_ar')->get()->groupBy('type');

        // All programs for the program-assign modal (excluding diplomas)
        $allPrograms = Program::select('id', 'name_ar', 'type', 'status')
            ->whereIn('type', ['training', 'english', 'course'])
            ->orderBy('type')->orderBy('name_ar')->get()->groupBy('type');

        return view('admin.teachers.index', compact('teachers', 'stats', 'search', 'programsByType', 'allPrograms'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'national_id' => 'required|digits:10|unique:users,national_id',
            'phone'       => 'nullable|string|max:20',
            'gender'      => 'nullable|in:male,female',
            'nationality' => 'nullable|string|max:100',
            'password'    => 'required|string|min:8|confirmed',
        ]);

        $validated['role'] = 'teacher';
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'تم إضافة ال مدرب  بنجاح');
    }

    public function show(User $teacher)
    {
        $teacher->load([
            'assignedSubjects.program',
            'assignedSubjects.term.program',
            'subjects.program',
            'subjects.term.program',
            'teachingPrograms',
        ]);

        $allSubjects = $teacher->assignedSubjects->merge($teacher->subjects)->unique('id');

        $diplomaSubjects = $allSubjects->filter(function ($subject) {
            $prog = $subject->program ?? $subject->term?->program;
            return $prog && $prog->type === 'diploma';
        })->values();

        $courses = $teacher->teachingPrograms->filter(
            fn($p) => in_array($p->type, ['training', 'english', 'course'])
        )->values();

        return view('admin.teachers.show', compact('teacher', 'diplomaSubjects', 'courses'));
    }

    public function edit(User $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, User $teacher)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $teacher->id,
            'national_id' => 'nullable|digits:10|unique:users,national_id,' . $teacher->id,
            'phone'       => 'nullable|string|max:20',
            'gender'      => 'nullable|in:male,female',
            'nationality' => 'nullable|string|max:100',
            'password'    => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $teacher->update($validated);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'تم تحديث بيانات ال مدرب  بنجاح');
    }

    public function destroy(User $teacher)
    {
        $teacher->delete();

        return redirect()->route('admin.teachers.index')
            ->with('success', 'تم حذف ال مدرب  بنجاح');
    }

    public function assignSubjects(Request $request, User $teacher)
    {
        $subjectIds = array_map('intval', $request->input('subjects', []));

        // Sync pivot table
        $teacher->assignedSubjects()->sync($subjectIds);

        // Keep teacher_id column in sync:
        // Clear teacher_id on subjects removed from this teacher
        Subject::where('teacher_id', $teacher->id)
            ->whereNotIn('id', $subjectIds)
            ->update(['teacher_id' => null]);

        // Set teacher_id on newly assigned subjects that have no teacher yet
        if (!empty($subjectIds)) {
            Subject::whereIn('id', $subjectIds)
                ->whereNull('teacher_id')
                ->update(['teacher_id' => $teacher->id]);
        }

        return back()->with('success', 'تم تحديث المقررات للمدرب' . $teacher->name);
    }

    public function assignPrograms(Request $request, User $teacher)
    {
        $request->validate(['program_ids' => 'nullable|array', 'program_ids.*' => 'exists:programs,id']);
        $teacher->teachingPrograms()->sync($request->input('program_ids', []));

        return back()->with('success', 'تم تعيين الدورات للمدرب' . $teacher->name);
    }

    public function toggleStatus(User $teacher)
    {
        $teacher->update([
            'status' => $teacher->status === 'active' ? 'inactive' : 'active',
        ]);

        $label = $teacher->status === 'active' ? 'تم تفعيل المتدرب' : 'تم تعطيل المتدرب';

        return back()->with('success', $label . ' ' . $teacher->name);
    }

    public function export()
    {
        $teachers = User::where('role', 'teacher')->latest()->get();

        $filename = 'teachers_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($teachers) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel Arabic support
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['#', 'الاسم', 'البريد الإلكتروني', 'رقم الهوية', 'رقم الهاتف', 'تاريخ التسجيل']);

            foreach ($teachers as $i => $teacher) {
                fputcsv($file, [
                    $i + 1,
                    $teacher->name,
                    $teacher->email,
                    $teacher->national_id ?? '',
                    $teacher->phone ?? '',
                    $teacher->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
