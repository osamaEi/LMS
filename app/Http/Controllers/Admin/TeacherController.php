<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramClass;
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

        // Subjects reach a program either through a term or directly via
        // subjects.program_id — the modal has to list both, otherwise saving
        // would silently drop assignments it never rendered.
        $programsByType = Program::with([
            'terms.subjects' => function ($q) {
                $q->select('subjects.id', 'subjects.name_ar', 'subjects.name_en', 'subjects.code')
                  ->orderBy('subjects.name_ar');
            },
            'subjects' => function ($q) {
                $q->select('subjects.id', 'subjects.program_id', 'subjects.name_ar', 'subjects.name_en', 'subjects.code')
                  ->orderBy('subjects.name_ar');
            },
        ])->orderBy('name_ar')->get()->groupBy('type');

        // Subjects tied to no program at all would otherwise be unreachable.
        $orphanSubjects = Subject::whereNull('program_id')
            ->whereNull('term_id')
            ->whereDoesntHave('terms')
            ->orderBy('name_ar')
            ->get(['id', 'name_ar', 'name_en', 'code']);

        // All programs for the program-assign modal (excluding diplomas)
        $allPrograms = Program::select('id', 'name_ar', 'type', 'status')
            ->whereIn('type', ['training', 'english', 'course'])
            ->orderBy('type')->orderBy('name_ar')->get()->groupBy('type');

        return view('admin.teachers.index', compact('teachers', 'stats', 'search', 'programsByType', 'allPrograms', 'orphanSubjects'));
    }

    public function create()
    {
        // Diploma subjects grouped by program → term
        $subjectsByProgram = Program::with(['terms.subjects' => function ($q) {
            $q->select('subjects.id', 'subjects.name_ar', 'subjects.name_en', 'subjects.term_id')
              ->orderBy('subjects.name_ar');
        }])->where('type', 'diploma')->orderBy('name_ar')->get();

        // Course / English / Training programs (for direct program assignment)
        $coursePrograms  = Program::where('type', 'course')->orderBy('name_ar')->get(['id','name_ar','type']);
        $englishPrograms = Program::where('type', 'english')->orderBy('name_ar')->get(['id','name_ar','type']);

        return view('admin.teachers.create', compact('subjectsByProgram', 'coursePrograms', 'englishPrograms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'national_id' => 'required|digits:10|unique:users,national_id',
            'phone'       => 'nullable|string|max:20|unique:users,phone',
            'gender'      => 'nullable|in:male,female',
            'nationality' => 'nullable|string|max:100',
            'password'    => 'required|string|min:8|confirmed',
            'certificate_url' => 'nullable|url:http,https|max:2048',
            'subjects'    => 'nullable|array',
            'subjects.*'  => 'exists:subjects,id',
            'programs'    => 'nullable|array',
            'programs.*'  => 'exists:programs,id',
        ], [
            'email.unique'       => 'البريد الإلكتروني مستخدم بالفعل، يرجى اختيار بريد آخر.',
            'national_id.unique' => 'رقم الهوية مستخدم بالفعل.',
            'national_id.digits' => 'رقم الهوية يجب أن يكون 10 أرقام.',
            'phone.unique'       => 'رقم الهاتف مستخدم بالفعل، يرجى إدخال رقم آخر.',
            'password.confirmed' => 'كلمة المرور وتأكيدها غير متطابقتان.',
            'password.min'       => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'name.required'      => 'الاسم مطلوب.',
            'email.required'     => 'البريد الإلكتروني مطلوب.',
            'national_id.required' => 'رقم الهوية مطلوب.',
        ]);

        $validated['role'] = 'teacher';
        $validated['password'] = Hash::make($validated['password']);

        $teacher = User::create($validated);

        // Assign diploma subjects
        $subjectIds = array_map('intval', $request->input('subjects', []));
        if (!empty($subjectIds)) {
            $teacher->assignedSubjects()->sync($subjectIds);
            Subject::whereIn('id', $subjectIds)->whereNull('teacher_id')
                ->update(['teacher_id' => $teacher->id]);
        }

        // Assign course / english programs
        $programIds = array_map('intval', $request->input('programs', []));
        if (!empty($programIds)) {
            $teacher->teachingPrograms()->sync($programIds);
        }

        return redirect()->route('admin.teachers.index')
            ->with('success', 'تم إضافة المدرب بنجاح');
    }

    public function show(User $teacher)
    {
        $teacher->load([
            'assignedSubjects.program',
            'assignedSubjects.term.program',
            'subjects.program',
            'subjects.term.program',
            'teachingPrograms',
            'teachingClasses.program',
        ]);

        $allSubjects = $teacher->assignedSubjects->merge($teacher->subjects)->unique('id');

        // Every assigned subject, not just diploma ones: a non-diploma or
        // program-less subject can be assigned, so it has to be visible here.
        $diplomaSubjects = $allSubjects->sortBy(fn ($s) => $s->name_ar ?? $s->name)->values();

        $courses = $teacher->teachingPrograms->filter(
            fn($p) => in_array($p->type, ['training', 'english', 'course'])
        )->values();

        // Everything the assign panels on this page need.
        $assignedSubjectIds = $allSubjects->pluck('id')->values();
        $assignedProgramIds = $teacher->teachingPrograms->pluck('id')->values();
        $assignedClassIds   = $teacher->teachingClasses->pluck('id')->values();

        $programsByType = Program::with([
            'terms.subjects' => function ($q) {
                $q->select('subjects.id', 'subjects.name_ar', 'subjects.name_en', 'subjects.code')
                  ->orderBy('subjects.name_ar');
            },
            'subjects' => function ($q) {
                $q->select('subjects.id', 'subjects.program_id', 'subjects.name_ar', 'subjects.name_en', 'subjects.code')
                  ->orderBy('subjects.name_ar');
            },
        ])->orderBy('name_ar')->get()->groupBy('type');

        $orphanSubjects = Subject::whereNull('program_id')
            ->whereNull('term_id')
            ->whereDoesntHave('terms')
            ->orderBy('name_ar')
            ->get(['id', 'name_ar', 'name_en', 'code']);

        $allPrograms = Program::select('id', 'name_ar', 'type', 'status')
            ->whereIn('type', ['training', 'english', 'course'])
            ->orderBy('type')->orderBy('name_ar')->get()->groupBy('type');

        // A class belongs to one teacher, so show who currently holds each one.
        $allClasses = ProgramClass::with('program:id,name_ar')
            ->orderBy('name')
            ->get(['id', 'name', 'program_id', 'teacher_id'])
            ->groupBy(fn ($class) => $class->program->name_ar ?? 'بدون برنامج');

        return view('admin.teachers.show', compact(
            'teacher', 'diplomaSubjects', 'courses',
            'assignedSubjectIds', 'assignedProgramIds', 'assignedClassIds',
            'programsByType', 'orphanSubjects', 'allPrograms', 'allClasses'
        ));
    }

    public function assignClasses(Request $request, User $teacher)
    {
        $validated = $request->validate([
            'class_ids'   => 'nullable|array',
            'class_ids.*' => 'exists:program_classes,id',
        ]);

        $classIds = array_map('intval', $validated['class_ids'] ?? []);

        // program_classes.teacher_id is a single column, so assigning means
        // claiming the class and releasing the ones no longer selected.
        ProgramClass::where('teacher_id', $teacher->id)
            ->when($classIds, fn ($q) => $q->whereNotIn('id', $classIds))
            ->update(['teacher_id' => null]);

        if ($classIds) {
            ProgramClass::whereIn('id', $classIds)->update(['teacher_id' => $teacher->id]);
        }

        return back()->with('success', 'تم تحديث المجموعات للمدرب ' . $teacher->name);
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
            'certificate_url' => 'nullable|url:http,https|max:2048',
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
        $request->validate([
            'subjects'   => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
        ]);

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
