<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\Session;
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

        // Pivot subjects plus legacy teacher_id subjects, shown together in the table.
        $teachers->load('assignedSubjects', 'subjects', 'teachingPrograms');

        $stats = [
            'total'        => User::where('role', 'teacher')->count(),
            'this_month'   => User::where('role', 'teacher')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'with_subjects'=> User::where('role', 'teacher')->has('subjects')->count(),
        ];

        // Assigning subjects/courses/classes is done on the teacher's show page.
        return view('admin.teachers.index', compact('teachers', 'stats', 'search'));
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
        $subjects = $allSubjects->sortBy(fn ($s) => $s->name_ar ?? $s->name)->values();
        $courses  = $teacher->teachingPrograms->sortBy('name_ar')->values();
        $pastClassIds = ProgramClass::pastIds($teacher->teachingClasses->pluck('id'));
        // Current classes first (green), then previous ones (red).
        $classes  = $teacher->teachingClasses
            ->each(fn ($c) => $c->is_past = $pastClassIds->contains($c->id))
            ->sortBy(fn ($c) => ($c->is_past ? '1' : '0') . $c->name)
            ->values();

        // Everything the assign panels on this page need.
        $assignedSubjectIds = $allSubjects->pluck('id')->values();
        $assignedProgramIds = $teacher->teachingPrograms->pluck('id')->values();
        $assignedClassIds   = $teacher->teachingClasses->pluck('id')->values();

        $sessionStats = [
            'total'    => Session::where('teacher_id', $teacher->id)->count(),
            'upcoming' => Session::where('teacher_id', $teacher->id)->where('scheduled_at', '>', now())->count(),
        ];
        $sessionStats['past'] = $sessionStats['total'] - $sessionStats['upcoming'];
        $sessionStats['next'] = Session::where('teacher_id', $teacher->id)
            ->where('scheduled_at', '>', now())->orderBy('scheduled_at')->value('scheduled_at');

        // Related classes: ones the teacher is not the owner of, but teaches
        // sessions or subjects in (session teachers can be reassigned per class).
        $directClassIds  = $teacher->teachingClasses->pluck('id');
        $relatedClassIds = $teacher->teachingClassIds()->diff($directClassIds)->values();
        $relatedClasses = ProgramClass::with(['program:id,name_ar,type', 'teacher:id,name'])
            ->whereIn('id', $relatedClassIds)->get();
        $pastClassIds = $pastClassIds->merge(ProgramClass::pastIds($relatedClassIds));

        // Per-class figures for every class shown on the page, in two queries.
        $allClassIds = $directClassIds->merge($relatedClassIds);
        $now = now();
        $teacherSessionsByClass = Session::where('teacher_id', $teacher->id)
            ->whereIn('class_id', $allClassIds)
            ->selectRaw('class_id, COUNT(*) AS total,
                SUM(CASE WHEN scheduled_at <= ? THEN 1 ELSE 0 END) AS done,
                MIN(CASE WHEN scheduled_at > ? THEN scheduled_at END) AS next_at', [$now, $now])
            ->groupBy('class_id')->get()->keyBy('class_id');
        $allSessionsByClass = Session::whereIn('class_id', $allClassIds)
            ->selectRaw('class_id, COUNT(*) AS total')
            ->groupBy('class_id')->pluck('total', 'class_id');

        $decorate = function ($class, bool $direct) use ($pastClassIds, $teacherSessionsByClass, $allSessionsByClass, $allSubjects) {
            $mine = $teacherSessionsByClass->get($class->id);
            $class->is_past          = $pastClassIds->contains($class->id);
            $class->is_direct        = $direct;
            $class->my_sessions      = (int) ($mine->total ?? 0);
            $class->my_done          = (int) ($mine->done ?? 0);
            $class->my_next_at       = $mine?->next_at ? \Carbon\Carbon::parse($mine->next_at) : null;
            $class->all_sessions     = (int) ($allSessionsByClass[$class->id] ?? 0);
            $class->my_subjects      = $allSubjects->where('class_id', $class->id)->values();
            $class->students_total   = $class->students_count;
            $class->progress         = $class->my_sessions ? (int) round($class->my_done / $class->my_sessions * 100) : 0;
            return $class;
        };

        $relatedClasses = $relatedClasses
            ->map(fn ($c) => $decorate($c, false))
            ->sortBy(fn ($c) => ($c->is_past ? '1' : '0') . $c->name)
            ->values();
        $classes->each(fn ($c) => $decorate($c, true));

        $upcomingSessions = Session::with(['subject:id,name_ar', 'programClass:id,name'])
            ->where('teacher_id', $teacher->id)
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get(['id', 'title_ar', 'title_en', 'subject_id', 'class_id', 'scheduled_at', 'duration_minutes', 'type']);

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

        // Course-type programs, plus any program already assigned whatever its
        // type — assignPrograms() syncs, so an unlisted assignment would be dropped.
        $allPrograms = Program::select('id', 'name_ar', 'type', 'status')
            ->where(fn ($q) => $q->whereIn('type', ['training', 'english', 'course'])
                                 ->orWhereIn('id', $assignedProgramIds))
            ->orderBy('type')->orderBy('name_ar')->get()->groupBy('type');

        // A class belongs to one teacher, so show who currently holds each one.
        $allClasses = ProgramClass::with(['program:id,name_ar', 'teacher:id,name'])
            ->orderBy('name')
            ->get(['id', 'name', 'program_id', 'teacher_id'])
            ->groupBy(fn ($class) => $class->program->name_ar ?? 'بدون برنامج');

        return view('admin.teachers.show', compact(
            'teacher', 'subjects', 'courses', 'classes', 'relatedClasses', 'upcomingSessions', 'sessionStats',
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

        $label = $teacher->status === 'active' ? 'تم تفعيل المدرب' : 'تم تعطيل المدرب';

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
