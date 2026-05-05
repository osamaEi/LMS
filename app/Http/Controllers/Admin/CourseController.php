<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Subject;
use App\Models\Term;
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
        $subjects    = Subject::orderBy('name_ar')->get(['id', 'name_ar', 'name_en', 'code']);
        $supervisors = User::whereIn('role', ['admin', 'super_admin', 'teacher'])
                           ->orderBy('name')->get(['id', 'name', 'role']);
        return view('admin.courses.create', compact('subjects', 'supervisors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar'            => 'nullable|string|max:255',
            'name_en'            => 'nullable|string|max:255',
            'code'               => 'nullable|string|unique:programs,code',
            'description_ar'     => 'nullable|string',
            'description_en'     => 'nullable|string',
            'duration_months'    => 'nullable|integer|min:1',
            'price'              => 'nullable|numeric|min:0',
            'status'             => 'nullable|in:active,inactive',
            'supervisor_id'      => 'nullable|exists:users,id',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'terms'              => 'nullable|array',
            'terms.*.name_ar'    => 'nullable|string|max:255',
            'terms.*.name_en'    => 'nullable|string|max:255',
            'terms.*.term_number'=> 'nullable|integer',
            'terms.*.start_date' => 'nullable|date',
            'terms.*.end_date'   => 'nullable|date',
            'terms.*.status'     => 'nullable|in:active,inactive,upcoming,completed',
            'terms.*.subjects'   => 'nullable|array',
            'terms.*.subjects.*' => 'exists:subjects,id',
        ]);

        $data = $request->only([
            'name_ar', 'name_en', 'code', 'description_ar', 'description_en',
            'duration_months', 'price', 'status', 'supervisor_id',
        ]);

        $data['type'] = 'course';

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/images', 'public');
        }

        $program = Program::create($data);

        foreach ($request->input('terms', []) as $i => $termData) {
            $subjectIds = $termData['subjects'] ?? [];
            $term = Term::create([
                'program_id'  => $program->id,
                'term_number' => $termData['term_number'] ?? ($i + 1),
                'name_ar'     => $termData['name_ar'] ?? null,
                'name_en'     => $termData['name_en'] ?? null,
                'start_date'  => $termData['start_date'] ?? null,
                'end_date'    => $termData['end_date'] ?? null,
                'status'      => $termData['status'] ?? 'upcoming',
            ]);
            if (!empty($subjectIds)) {
                $term->subjects()->sync($subjectIds);
            }
        }

        return redirect()->route('admin.courses.index')
            ->with('success', 'تم إضافة الدورة بنجاح');
    }

    public function edit(Program $course)
    {
        $supervisors = User::whereIn('role', ['admin', 'super_admin', 'teacher'])
                           ->orderBy('name')->get(['id', 'name', 'role']);
        return view('admin.courses.edit', ['program' => $course, 'supervisors' => $supervisors]);
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
            'supervisor_id'   => 'nullable|exists:users,id',
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
