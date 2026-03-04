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
        $search = $request->get('search');

        $query = Subject::with(['program', 'teacher'])->withCount(['sessions', 'files']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('teacher', function ($tq) use ($search) {
                      $tq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $subjects = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'     => Subject::count(),
            'active'    => Subject::where('status', 'active')->count(),
            'inactive'  => Subject::where('status', 'inactive')->count(),
            'completed' => Subject::where('status', 'completed')->count(),
        ];

        return view('admin.subjects.index', compact('subjects', 'stats', 'search'));
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
            'program_id' => 'nullable|exists:programs,id',
            'teacher_id' => 'nullable|exists:users,id',
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'code' => 'nullable|string|unique:subjects,code',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'banner_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'credits' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,inactive,completed',
        ]);

        if ($request->hasFile('banner_photo')) {
            $validated['banner_photo'] = $request->file('banner_photo')->store('subjects', 'public');
        }

        $subject = Subject::create($validated);

        return redirect()->route('admin.subjects.show', $subject)
            ->with('success', 'تم إضافة المادة بنجاح');
    }

    public function show(Subject $subject)
    {
        $subject->load(['program', 'teacher', 'files', 'sessions' => function ($q) {
            $q->latest();
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
            'program_id' => 'required|exists:programs,id',
            'teacher_id' => 'nullable|exists:users,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'code' => 'required|string|unique:subjects,code,' . $subject->id,
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'banner_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'credits' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive,completed',
        ]);

        if ($request->hasFile('banner_photo')) {
            $validated['banner_photo'] = $request->file('banner_photo')->store('subjects', 'public');
        }

        $subject->update($validated);

        return redirect()->route('admin.subjects.show', $subject)
            ->with('success', 'تم تحديث المادة بنجاح');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('admin.subjects.index')
            ->with('success', 'تم حذف المادة بنجاح');
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
