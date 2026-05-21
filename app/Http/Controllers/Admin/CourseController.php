<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    public function index()
    {
        $programs = Program::where('type', 'course')
            ->withCount(['terms', 'tracks'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total'    => Program::where('type', 'course')->count(),
            'active'   => Program::where('type', 'course')->where('status', 'active')->count(),
            'inactive' => Program::where('type', 'course')->where('status', 'inactive')->count(),
        ];

        return view('admin.courses.index', compact('programs', 'stats'));
    }

    public function create()
    {
        return view('admin.courses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar'         => 'nullable|string|max:255',
            'name_en'         => 'nullable|string|max:255',
            'code'            => 'nullable|string|unique:programs,code',
            'description_ar'  => 'nullable|string',
            'description_en'  => 'nullable|string',
            'duration_months' => 'nullable|integer|min:1',
            'price'           => 'nullable|numeric|min:0',
            'status'          => 'nullable|in:active,inactive',
            'supervisor_name' => 'nullable|string|max:255',
            'course_type'     => 'nullable|in:developmental,qualifying',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->only([
            'name_ar', 'name_en', 'code', 'description_ar', 'description_en',
            'duration_months', 'price', 'status', 'supervisor_name', 'course_type',
        ]);

        $data['type'] = 'course';

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/images', 'public');
        }

        Program::create($data);

        return redirect()->route('admin.courses.index')
            ->with('success', 'تم إضافة الدورة بنجاح');
    }

    public function show(Program $course)
    {
        $course->load([
            'terms' => function ($query) {
                $query->withCount('subjects')
                      ->with(['subjects' => fn($q) => $q->with(['teacher:id,name', 'teachers:id,name'])->orderBy('name_ar')])
                      ->orderBy('term_number');
            },
        ]);

        $allSubjects = Subject::orderBy('name_ar')->get(['id', 'name_ar', 'name_en', 'code']);
        $teachers    = User::where('role', 'teacher')->orderBy('name')->get(['id', 'name']);
        $program     = $course;

        return view('admin.programs.show', compact('program', 'allSubjects', 'teachers'))
            ->with('backRoute', 'admin.courses.index')
            ->with('pageLabel', 'الدورات والبرامج التأهيلية');
    }

    public function edit(Program $course)
    {
        return view('admin.courses.edit', ['program' => $course]);
    }

    public function update(Request $request, Program $course)
    {
        $validated = $request->validate([
            'name_ar'         => 'nullable|string|max:255',
            'name_en'         => 'nullable|string|max:255',
            'code'            => 'nullable|string|unique:programs,code,' . $course->id,
            'description_ar'  => 'nullable|string',
            'description_en'  => 'nullable|string',
            'duration_months' => 'nullable|integer|min:1',
            'price'           => 'nullable|numeric|min:0',
            'status'          => 'nullable|in:active,inactive',
            'supervisor_name' => 'nullable|string|max:255',
            'course_type'     => 'nullable|in:developmental,qualifying',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_image'    => 'nullable|boolean',
        ]);

        $validated['type'] = 'course';

        if ($request->hasFile('image')) {
            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }
            $validated['image'] = $request->file('image')->store('uploads/images', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }
            $validated['image'] = null;
        }

        unset($validated['remove_image']);
        $course->update($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', 'تم تحديث الدورة بنجاح');
    }

    public function destroy(Program $course)
    {
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'تم حذف الدورة بنجاح');
    }
}
