<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Subject::with(['term.program', 'teacher'])->withCount('sessions');

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
        $terms = Term::with('program')->where('status', 'active')->get();
        $teachers = User::where('role', 'teacher')->get();

        return view('admin.subjects.create', compact('terms', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'term_id' => 'required|exists:terms,id',
            'teacher_id' => 'nullable|exists:users,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'code' => 'required|string|unique:subjects,code',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'banner_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'credits' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive,completed',
        ]);

        // Handle image upload
        if ($request->hasFile('banner_photo')) {
            $validated['banner_photo'] = $request->file('banner_photo')->store('subjects', 'public');
        }

        $subject = Subject::create($validated);

        // If coming from program show page, redirect back there
        if ($request->header('referer') && str_contains($request->header('referer'), 'programs')) {
            $term = Term::find($request->term_id);
            return redirect()->route('admin.programs.show', $term->program_id)
                ->with('success', 'تم إضافة المادة بنجاح');
        }

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
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'code' => 'required|string|unique:subjects,code,' . $subject->id,
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'banner_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'credits' => 'nullable|integer|min:1',
            'status' => 'required|in:active,inactive,completed',
        ]);

        // Handle image upload
        if ($request->hasFile('banner_photo')) {
            $validated['banner_photo'] = $request->file('banner_photo')->store('subjects', 'public');
        }

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
