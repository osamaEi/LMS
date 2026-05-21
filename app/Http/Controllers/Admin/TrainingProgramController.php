<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Term;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrainingProgramController extends Controller
{
    private string $type = 'training';

    public function index()
    {
        $programs = Program::where('type', $this->type)
            ->withCount(['terms', 'tracks'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total'    => Program::where('type', $this->type)->count(),
            'active'   => Program::where('type', $this->type)->where('status', 'active')->count(),
            'inactive' => Program::where('type', $this->type)->where('status', 'inactive')->count(),
        ];

        return view('admin.training-programs.index', compact('programs', 'stats'));
    }

    public function create()
    {
        $subjects    = Subject::orderBy('name_ar')->get(['id', 'name_ar', 'name_en', 'code']);
        $supervisors = User::whereIn('role', ['admin', 'super_admin', 'teacher'])
                           ->orderBy('name')->get(['id', 'name', 'role']);
        return view('admin.programs.create', compact('subjects', 'supervisors'))
            ->with('programType', $this->type)
            ->with('backRoute', 'admin.training-programs.index')
            ->with('pageLabel', 'البرامج التدريبية ');
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
            'supervisor_name'    => 'nullable|string|max:255',
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
            'duration_months', 'price', 'status', 'supervisor_name',
        ]);

        $data['type'] = $this->type;

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

        return redirect()->route('admin.training-programs.show', $program)
            ->with('success', 'تم إضافة البرنامج التدريبي  بنجاح');
    }

    public function show(Program $training_program)
    {
        $training_program->load([
            'terms' => function ($query) {
                $query->withCount('subjects')
                      ->with(['subjects' => fn($q) => $q->with(['teacher:id,name', 'teachers:id,name'])->orderBy('name_ar')])
                      ->orderBy('term_number');
            },
            'tracks',
        ]);

        $allSubjects = Subject::orderBy('name_ar')->get(['id', 'name_ar', 'name_en', 'code']);
        $teachers    = User::where('role', 'teacher')->orderBy('name')->get(['id', 'name']);
        $program     = $training_program;

        return view('admin.programs.show', compact('program', 'allSubjects', 'teachers'))
            ->with('backRoute', 'admin.training-programs.index')
            ->with('pageLabel', 'البرامج التدريبية ');
    }

    public function edit(Program $training_program)
    {
        $supervisors = User::whereIn('role', ['admin', 'super_admin', 'teacher'])
                           ->orderBy('name')->get(['id', 'name', 'role']);
        $program = $training_program;

        return view('admin.programs.edit', compact('program', 'supervisors'))
            ->with('programType', $this->type)
            ->with('backRoute', 'admin.training-programs.index')
            ->with('pageLabel', 'البرامج التدريبية ');
    }

    public function update(Request $request, Program $training_program)
    {
        $validated = $request->validate([
            'name_ar'         => 'nullable|string|max:255',
            'name_en'         => 'nullable|string|max:255',
            'code'            => 'nullable|string|unique:programs,code,' . $training_program->id,
            'description_ar'  => 'nullable|string',
            'description_en'  => 'nullable|string',
            'duration_months' => 'nullable|integer|min:1',
            'price'           => 'nullable|numeric|min:0',
            'status'          => 'nullable|in:active,inactive',
            'supervisor_name' => 'nullable|string|max:255',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_image'    => 'nullable|boolean',
        ]);

        $validated['type'] = $this->type;

        if ($request->hasFile('image')) {
            if ($training_program->image) {
                Storage::disk('public')->delete($training_program->image);
            }
            $validated['image'] = $request->file('image')->store('uploads/images', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($training_program->image) {
                Storage::disk('public')->delete($training_program->image);
            }
            $validated['image'] = null;
        }

        unset($validated['remove_image']);
        $training_program->update($validated);

        return redirect()->route('admin.training-programs.index')
            ->with('success', 'تم تحديث البرنامج التدريبي  بنجاح');
    }

    public function destroy(Program $training_program)
    {
        $training_program->delete();

        return redirect()->route('admin.training-programs.index')
            ->with('success', 'تم حذف البرنامج التدريبي  بنجاح');
    }
}
