<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Subject;
use App\Models\SubjectFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->get('search');
        $status     = $request->get('status');
        $programId  = $request->get('program_id');
        $teacherId  = $request->get('teacher_id');
        $noTeacher  = $request->boolean('no_teacher');

        $query = Subject::with(['program', 'teacher'])->withCount(['sessions', 'files']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('teacher', fn($tq) => $tq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($programId) {
            $query->where('program_id', $programId);
        }

        if ($noTeacher) {
            $query->whereNull('teacher_id');
        } elseif ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }

        $subjects = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'     => Subject::count(),
            'active'    => Subject::where('status', 'active')->count(),
            'inactive'  => Subject::where('status', 'inactive')->count(),
            'completed' => Subject::where('status', 'completed')->count(),
        ];

        $programs = Program::orderBy('name_ar')->get(['id', 'name_ar', 'name_en']);
        $teachers = User::where('role', 'teacher')->orderBy('name')->get(['id', 'name']);

        $filters = compact('search', 'status', 'programId', 'teacherId', 'noTeacher');

        return view('admin.subjects.index', compact('subjects', 'stats', 'programs', 'teachers', 'filters'));
    }

    public function create()
    {
        $programs = Program::where('status', 'active')->get();
        $teachers = User::where('role', 'teacher')->get();

        return view('admin.subjects.create', compact('programs', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id'    => 'nullable|exists:programs,id',
            'term_id'       => 'nullable|exists:terms,id',
            'teacher_id'    => 'nullable|exists:users,id',
            'name_ar'       => 'nullable|string|max:255',
            'name_en'       => 'nullable|string|max:255',
            'code'          => 'nullable|string',
            'description_ar'=> 'nullable|string',
            'description_en'=> 'nullable|string',
            'banner_photo'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'credits'       => 'nullable|integer|min:1',
            'status'        => 'nullable|in:active,inactive,completed',
        ]);

        if ($request->hasFile('banner_photo')) {
            $validated['banner_photo'] = $request->file('banner_photo')->store('uploads/images', 'public');
        }

        $subject = Subject::create($validated);

        if (!empty($validated['term_id'])) {
            $subject->terms()->syncWithoutDetaching([$validated['term_id']]);
        }

        if ($request->filled('program_id')) {
            return redirect()->route('admin.programs.show', $validated['program_id'])
                ->with('success', 'تم إضافة المقرر بنجاح');
        }

        return redirect()->route('admin.subjects.show', $subject)
            ->with('success', 'تم إضافة المقرر  بنجاح');
    }

    public function show(Subject $subject)
    {
        $subject->load(['program', 'teacher', 'files', 'units', 'sessions' => function ($q) {
            $q->orderBy('session_number');
        }]);

        return view('admin.subjects.show', compact('subject'));
    }

    public function edit(Subject $subject)
    {
        $programs = Program::where('status', 'active')->get();
        $teachers = User::where('role', 'teacher')->get();

        return view('admin.subjects.edit', compact('subject', 'programs', 'teachers'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'program_id' => 'nullable|exists:programs,id',
            'teacher_id' => 'nullable|exists:users,id',
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'code' => 'nullable|string|unique:subjects,code,' . $subject->id,
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'banner_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'credits' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,inactive,completed',
        ]);

        if ($request->hasFile('banner_photo')) {
            $validated['banner_photo'] = $request->file('banner_photo')->store('uploads/images', 'public');
        }

        $subject->update($validated);

        return redirect()->route('admin.subjects.show', $subject)
            ->with('success', 'تم تحديث المقرر  بنجاح');
    }

    public function assignTeacher(Request $request, Subject $subject)
    {
        $request->validate(['teacher_id' => 'nullable|exists:users,id']);
        $subject->update(['teacher_id' => $request->teacher_id ?: null]);
        return back()->with('success', 'تم تعيين المدرب بنجاح');
    }

    public function assignTeachers(Request $request, Subject $subject)
    {
        $request->validate(['teacher_ids' => 'nullable|array', 'teacher_ids.*' => 'exists:users,id']);
        $ids = array_map('intval', $request->input('teacher_ids', []));
        $subject->teachers()->sync($ids);
        $subject->update(['teacher_id' => count($ids) ? $ids[0] : null]);
        return back()->with('success', 'تم تعيين المدربين بنجاح');
    }

    public function toggleStatus(Request $request, Subject $subject)
    {
        $new = $request->input('status');
        abort_unless(in_array($new, ['active', 'inactive']), 422);
        $subject->update(['status' => $new]);

        return back()->with('success', $new === 'active' ? 'تم تنشيط المقرر' : 'تم قفل المقرر');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم حذف المقرر  بنجاح');
    }

    public function uploadFile(Request $request, Subject $subject)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:51200', // 50MB
            'description' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $path = $file->store('subject-files/' . $subject->id, 'public');

        SubjectFile::create([
            'subject_id'         => $subject->id,
            'title'              => $request->title,
            'file_path'          => $path,
            'file_original_name' => $file->getClientOriginalName(),
            'file_size'          => $file->getSize(),
            'file_type'          => $file->getClientOriginalExtension(),
            'description'        => $request->description,
            'order'              => $subject->files()->count(),
        ]);

        return back()->with('success', 'تم رفع الملف بنجاح');
    }

    public function deleteFile(Subject $subject, SubjectFile $file)
    {
        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return back()->with('success', 'تم حذف الملف بنجاح');
    }
}
