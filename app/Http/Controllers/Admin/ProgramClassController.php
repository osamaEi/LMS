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
        if ($request->filled('type')) {
            $query->whereHas('program', fn($q) => $q->where('type', $request->type));
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

        // Students assigned via legacy users.class_id OR via the student_programs pivot
        $classStudents = User::where('role', 'student')
            ->where(function ($q) use ($class) {
                $q->where('class_id', $class->id)
                  ->orWhereHas('programs', fn($sq) => $sq
                      ->where('programs.id', $class->program_id)
                      ->where('student_programs.class_id', $class->id));
            })
            ->get(['id', 'name', 'email', 'national_id', 'phone', 'class_id']);

        // Make the combined set available to the view as $class->students
        $class->setRelation('students', $classStudents);
        $studentsCount = $classStudents->count();

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

        // All teachers in the system — so the assign-teacher modal is never empty.
        $teachers = User::where('role', 'teacher')->orderBy('name')->get(['id', 'name']);

        // Subjects belonging to THIS class (used as the session subject picker for diplomas).
        // Attach each subject's term so the form can derive the term end date.
        $classSubjects = $class->terms->flatMap(function ($term) {
            return $term->subjects->each(fn($s) => $s->setRelation('term', $term));
        })->values();

        // Existing sessions for this class
        $sessions = \App\Models\Session::where('class_id', $class->id)
            ->with('subject:id,name_ar,name_en', 'teacher:id,name')
            ->orderBy('scheduled_at')
            ->get();

        return view('admin.classes.show', compact('class', 'programSubjects', 'usedSubjectIds', 'classSubjects', 'sessions', 'teachers', 'studentsCount'));
    }

    /**
     * Generate weekly recurring sessions scoped to this class.
     * Diploma: requires a subject that belongs to this class.
     * Course/English: program-level sessions (no subject).
     */
    public function generateSessions(Request $request, ProgramClass $class)
    {
        $isDiploma = $class->program && $class->program->type === 'diploma';

        $data = $request->validate([
            'subject_id'       => [$isDiploma ? 'required' : 'nullable', 'exists:subjects,id'],
            'teacher_id'       => 'required|exists:users,id',
            'days'             => 'required|array|min:1',
            'days.*'           => 'integer|min:0|max:6',
            'time'             => 'required|date_format:H:i',
            'start_date'       => 'required|date',
            'end_date'         => 'nullable|date|after:start_date',
        ]);

        $class->loadMissing('students');

        // Errors should land back on the Sessions tab of this class
        $err = fn($msg) => redirect()->to(route('admin.classes.show', $class->id) . '#sessions')->with('error', $msg);

        // Resolve FK column/value + title label + the end-of-period date
        if ($isDiploma) {
            $subject = \App\Models\Subject::with(['term', 'teachers', 'terms'])->findOrFail($data['subject_id']);
            
            // Check if subject belongs to a term of this class
            $belongsToClass = $subject->terms->contains(fn($t) => $t->class_id === $class->id);
            if (!$belongsToClass) {
                return $err('المقرر لا يخص هذه المجموعة');
            }
            // The session teacher must be one assigned to this subject
            $assignedTeacherIds = $subject->teachers->pluck('id')
                ->merge($subject->teacher_id ? [$subject->teacher_id] : [])
                ->unique();
            if (!$assignedTeacherIds->contains((int) $data['teacher_id'])) {
                return $err('المدرب المختار غير معيّن لهذا المقرر');
            }
            
            // Get the term of this subject that belongs to this class
            $term = $subject->terms->first(fn($t) => $t->class_id === $class->id);
            
            $fkCol = 'subject_id';
            $fkVal = $subject->id;
            $label = $subject->name_ar ?? 'جلسة';
            // Sessions run weekly until the term ends
            $end = $term?->end_date;
        } else {
            $fkCol = 'program_id';
            $fkVal = $class->program_id;
            $label = $class->program->name_ar ?? 'جلسة';
            // Courses/English: bound by the class end date
            $end = $class->end_date;
        }
        $programId = $class->program_id;

        // No auto end date — use the one the admin entered, and persist it for next time
        if (!$end) {
            if (empty($data['end_date'])) {
                return $err('حدّد تاريخ نهاية الجلسات');
            }
            $end = \Carbon\Carbon::parse($data['end_date']);
            if ($isDiploma && $term) {
                $term->update(['end_date' => $end->toDateString()]);
            } elseif (!$isDiploma) {
                $class->update(['end_date' => $end->toDateString()]);
            }
        }

        $teacherId = $data['teacher_id'];

        // Build weekly recurring datetimes from start_date until the period end
        [$hh, $mm] = explode(':', $data['time']);
        $start = \Carbon\Carbon::parse($data['start_date']);
        $end   = \Carbon\Carbon::parse($end)->endOfDay();
        $days  = array_map('intval', $data['days']);

        if ($start->gt($end)) {
            return $err('تاريخ البداية بعد نهاية الفترة');
        }

        $dates = [];
        $cur   = $start->copy();
        while ($cur->lte($end)) {
            if (in_array($cur->dayOfWeek, $days)) {
                $dates[] = $cur->copy()->setHour((int) $hh)->setMinute((int) $mm)->setSecond(0);
            }
            $cur->addDay();
        }

        if (empty($dates)) {
            return $err('لا توجد أيام مطابقة في الفترة المحددة');
        }

        $nextNumber = \App\Models\Session::where($fkCol, $fkVal)->where('class_id', $class->id)->max('session_number') ?? 0;
        $students   = $class->students;
        $created    = 0;

        foreach ($dates as $scheduledAt) {
            $nextNumber++;
            $session = \App\Models\Session::create([
                $fkCol             => $fkVal,
                'program_id'       => $programId,
                'class_id'         => $class->id,
                'teacher_id'       => $teacherId,
                'type'             => 'live_zoom',
                'status'           => 'scheduled',
                'scheduled_at'     => $scheduledAt,
                'duration_minutes' => 60,
                'session_number'   => $nextNumber,
                'title_ar'         => $label . ' (#' . $nextNumber . ')',
            ]);

            foreach ($students as $student) {
                \App\Models\Attendance::firstOrCreate([
                    'session_id' => $session->id,
                    'student_id' => $student->id,
                ], ['attended' => false]);
            }
            $created++;
        }

        return redirect()->to(route('admin.classes.show', $class->id) . '#sessions')
            ->with('success', "تم إنشاء {$created} جلسة وإسناد {$students->count()} طالب لكل جلسة");
    }

    /**
     * Delete all sessions of this class (and their attendance).
     */
    public function clearSessions(ProgramClass $class)
    {
        $sessions = \App\Models\Session::where('class_id', $class->id)->get();
        $count = $sessions->count();

        foreach ($sessions as $session) {
            \App\Models\Attendance::where('session_id', $session->id)->delete();
            $session->delete();
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'deleted' => $count]);
        }

        return redirect()->to(route('admin.classes.show', $class->id) . '#sessions')
            ->with('success', "تم حذف {$count} جلسة");
    }

    /**
     * Attach an existing program subject to a class term.
     * If the source subject is program-wide (class_id null), clone it for this class
     * so other classes keep their own copy; otherwise assign directly.
     */
    public function attachSubject(Request $request, ProgramClass $class)
    {
        $data = $request->validate([
            'term_id'      => 'required|exists:terms,id',
            'subject_id'   => 'nullable|exists:subjects,id',          // single (modal)
            'subject_ids'  => 'nullable|array',                       // multiple (checkboxes)
            'subject_ids.*'=> 'integer|exists:subjects,id',
        ]);

        $term = \App\Models\Term::findOrFail($data['term_id']);
        abort_unless($term->class_id == $class->id, 403, 'الربع لا يخص هذه المجموعة');

        // Collect chosen subject IDs from either input
        $ids = collect($data['subject_ids'] ?? [])
            ->merge(!empty($data['subject_id']) ? [$data['subject_id']] : [])
            ->unique()->values();

        if ($ids->isEmpty()) {
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'اختر مقرراً واحداً على الأقل'], 422)
                : back()->with('error', 'اختر مقرراً واحداً على الأقل');
        }

        $attached = 0;
        $skipped  = [];
        foreach ($ids as $sid) {
            $result = $this->attachOneSubject($class, $term, (int) $sid);
            if ($result === true) {
                $attached++;
            } else {
                $skipped[] = $result; // error message
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => $attached > 0, 'attached' => $attached, 'skipped' => $skipped]);
        }

        $msg = "تم إضافة {$attached} مقرر للمجموعة";
        $redirect = redirect()->to(route('admin.classes.show', $class->id) . '#terms');
        return !empty($skipped)
            ? $redirect->with('error', $msg . ' — تم تخطي: ' . implode('، ', array_unique($skipped)))
            : $redirect->with('success', $msg);
    }

    /**
     * Attach a single subject to a class term.
     * Returns true on success, or a string error message to skip.
     */
    private function attachOneSubject(ProgramClass $class, \App\Models\Term $term, int $sourceId)
    {
        $source = \App\Models\Subject::find($sourceId);
        if (!$source) return 'مقرر غير موجود';

        if ($source->class_id === null) {
            // Clone the program-wide subject into this class.
            $subject = $source->replicate(['class_id']);
            $subject->class_id = $class->id;
            $subject->term_id  = $term->id;
            $baseCode = $source->code ? $source->code . '-C' . $class->id : null;
            $subject->code = $baseCode;

            $existing = $baseCode
                ? \App\Models\Subject::where('program_id', $class->program_id)->where('code', $baseCode)->first()
                : null;
            if ($existing && $existing->term_id) {
                return ($source->name_ar ?: $source->name_en) . ' (مُسند لربع آخر)';
            }
            if ($existing) {
                $existing->update(['term_id' => $term->id, 'class_id' => $class->id]);
                $existing->terms()->syncWithoutDetaching([$term->id]);
                return true;
            }
            $subject->save();
            if ($source->teacher_id) {
                $subject->teachers()->sync($source->teachers()->pluck('users.id')->all());
            }
        } else {
            if ($source->class_id != $class->id) {
                return ($source->name_ar ?: $source->name_en) . ' (يخص مجموعة أخرى)';
            }
            $subject = $source;
            $subject->term_id = $term->id;
            $subject->save();
        }

        $subject->terms()->syncWithoutDetaching([$term->id]);
        return true;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'program_id'      => 'required|exists:programs,id',
            'name'            => 'required|string|max:255',
            'supervisor_name' => 'nullable|string|max:255',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'max_students'    => 'nullable|integer|min:1',
            'status'          => 'nullable|in:active,inactive,completed',
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
            'name'            => 'required|string|max:255',
            'supervisor_name' => 'nullable|string|max:255',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
            'max_students'    => 'nullable|integer|min:1',
            'status'          => 'nullable|in:active,inactive,completed',
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
        // Students assigned via legacy users.class_id OR via the student_programs pivot
        $students = User::where('role', 'student')
            ->where(function ($q) use ($class) {
                $q->where('class_id', $class->id)
                  ->orWhereHas('programs', fn($sq) => $sq
                      ->where('programs.id', $class->program_id)
                      ->where('student_programs.class_id', $class->id));
            })
            ->get(['id', 'name', 'email', 'national_id', 'status', 'profile_photo']);

        return response()->json(['students' => $students]);
    }

    public function assignStudents(Request $request, ProgramClass $class)
    {
        $request->validate([
            'student_ids'   => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        // Only students who belong to this class's program (primary or via pivot)
        $students = User::whereIn('id', $request->student_ids)
            ->where(function ($q) use ($class) {
                $q->where('program_id', $class->program_id)
                  ->orWhereHas('programs', fn($sq) => $sq->where('programs.id', $class->program_id));
            })
            ->get();

        $assigned = 0;
        foreach ($students as $student) {
            // Source of truth: the student_programs pivot (one class per program).
            // Ensure a pivot row exists for this program, then set its class_id.
            if ($student->programs()->where('programs.id', $class->program_id)->exists()) {
                $student->programs()->updateExistingPivot($class->program_id, ['class_id' => $class->id]);
            } else {
                // Primary-program enrollment with no pivot row yet — create one
                $student->programs()->attach($class->program_id, [
                    'class_id'   => $class->id,
                    'status'     => $student->program_status ?? 'approved',
                    'enrolled_at'=> now(),
                ]);
            }

            // Mirror to legacy users.class_id ONLY for the student's primary program
            if ($student->program_id == $class->program_id) {
                $student->update(['class_id' => $class->id]);
            }
            $assigned++;
        }

        return response()->json(['success' => true, 'assigned' => $assigned]);
    }

    public function removeStudent(Request $request, ProgramClass $class)
    {
        $request->validate(['student_id' => 'required|exists:users,id']);

        $student = User::find($request->student_id);
        if ($student) {
            // Clear the per-program pivot assignment
            if ($student->programs()->where('programs.id', $class->program_id)->exists()) {
                $student->programs()->updateExistingPivot($class->program_id, ['class_id' => null]);
            }
            // Clear legacy column if it points to this class
            if ($student->class_id == $class->id) {
                $student->update(['class_id' => null]);
            }
        }
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
            ->with(['programs' => fn($q) => $q->where('programs.id', $class->program_id)])
            ->get(['id', 'name', 'email', 'national_id', 'class_id', 'program_id']);

        // Report the student's class WITHIN THIS program only (so a student in another
        // program's class isn't falsely shown as "in another group" here).
        $students->transform(function ($s) use ($class) {
            $s->class_id = $s->classIdForProgram((int) $class->program_id);
            return $s;
        });

        return response()->json(['students' => $students]);
    }
}
