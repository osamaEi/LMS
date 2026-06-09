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

    public function show(ProgramClass $class)
    {
        $class->load([
            'program',
            'teacher',
            'terms' => function ($query) {
                $query->withCount('subjects')
                      ->with(['subjects' => fn($q) => $q->with(['teacher:id,name', 'teachers:id,name'])->orderBy('name_ar')])
                      ->orderBy('term_number');
            },
        ]);
        $class->loadCount('students');

        // Program (diploma) subjects available to pick from the dropdown — program-wide only
        $programSubjects = \App\Models\Subject::where('program_id', $class->program_id)
            ->whereNull('class_id')
            ->orderBy('name_ar')
            ->get(['id', 'name_ar', 'name_en', 'code', 'credits', 'teacher_id', 'class_id']);

        // Subjects already placed in a term of this class — exclude them so a subject
        // can only live in one term. A clone's code is "{sourceCode}-C{classId}".
        $usedCodes = \App\Models\Subject::where('program_id', $class->program_id)
            ->where('class_id', $class->id)
            ->whereNotNull('term_id')
            ->pluck('code')
            ->filter()
            ->all();
        $suffix = '-C' . $class->id;
        $usedSubjectIds = $programSubjects->filter(function ($s) use ($usedCodes, $suffix) {
            return $s->code && in_array($s->code . $suffix, $usedCodes, true);
        })->pluck('id')->all();

        $teachers = User::where('role', 'teacher')->orderBy('name')->get(['id', 'name']);

        return view('admin.classes.show', compact('class', 'programSubjects', 'usedSubjectIds', 'teachers'));
    }

    /**
     * Attach an existing program subject to a class term.
     * If the source subject is program-wide (class_id null), clone it for this class
     * so other classes keep their own copy; otherwise assign directly.
     */
    public function attachSubject(Request $request, ProgramClass $class)
    {
        $data = $request->validate([
            'term_id'    => 'required|exists:terms,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $term = \App\Models\Term::findOrFail($data['term_id']);
        abort_unless($term->class_id == $class->id, 403, 'الربع لا يخص هذه المجموعة');

        $source = \App\Models\Subject::findOrFail($data['subject_id']);

        if ($source->class_id === null) {
            // Clone the program-wide subject into this class.
            // Code is unique per (program_id, code), so suffix it for the class copy.
            $subject = $source->replicate(['class_id']);
            $subject->class_id = $class->id;
            $subject->term_id  = $term->id;
            $baseCode = $source->code ? $source->code . '-C' . $class->id : null;
            $subject->code = $baseCode;

            // Enforce: a subject can only live in one term per class
            $existing = $baseCode
                ? \App\Models\Subject::where('program_id', $class->program_id)->where('code', $baseCode)->first()
                : null;
            if ($existing && $existing->term_id) {
                $msg = 'المقرر مُسند بالفعل لربع آخر في هذه المجموعة';
                return $request->wantsJson()
                    ? response()->json(['success' => false, 'message' => $msg], 422)
                    : back()->with('error', $msg);
            }
            if ($existing) {
                // Cloned before but not in a term yet — assign it
                $existing->update(['term_id' => $term->id, 'class_id' => $class->id]);
                $existing->terms()->syncWithoutDetaching([$term->id]);
                return $request->wantsJson()
                    ? response()->json(['success' => true])
                    : back()->with('success', 'تم إضافة المقرر للمجموعة');
            }
            $subject->save();
            if ($source->teacher_id) {
                $subject->teachers()->sync($source->teachers()->pluck('users.id')->all());
            }
        } else {
            // Already class-specific — just move it under this term
            abort_unless($source->class_id == $class->id, 403, 'المقرر يخص مجموعة أخرى');
            $subject = $source;
            $subject->term_id = $term->id;
            $subject->save();
        }

        $subject->terms()->syncWithoutDetaching([$term->id]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'تم إضافة المقرر للمجموعة');
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
                $q->where('program_id', $class->program_id)
                  ->orWhereHas('programs', fn($sq) => $sq->where('programs.id', $class->program_id));
            })
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
        $students = User::where('role', 'student')
            ->where(function ($q) use ($class) {
                // Primary program assignment OR via student_programs pivot
                $q->where('program_id', $class->program_id)
                  ->orWhereHas('programs', fn($sq) => $sq->where('programs.id', $class->program_id));
            })
            ->get(['id', 'name', 'email', 'national_id', 'class_id']);

        return response()->json(['students' => $students]);
    }
}
