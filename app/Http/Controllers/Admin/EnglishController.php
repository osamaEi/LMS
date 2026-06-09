<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EnglishController extends Controller
{
    private static array $levelNames = [
        0  => 'التمهيدي',
        1  => 'التأسيسي',
        2  => 'المبتدئ',
        3  => 'المستوى الأول',
        4  => 'المستوى الثاني',
        5  => 'المستوى الثالث',
        6  => 'المستوى الرابع',
        7  => 'المستوى الخامس',
        8  => 'المستوى السادس',
        9  => 'المستوى السابع',
        10 => 'المستوى الثامن',
        11 => 'المستوى التاسع',
        12 => 'المستوى العاشر',
        13 => 'المستوى الحادي عشر',
        14 => 'المستوى الثاني عشر',
    ];

    public function index()
    {
        $programs = Program::where('type', 'english')
            ->orderBy('level')
            ->paginate(20);

        $stats = [
            'total'    => Program::where('type', 'english')->count(),
            'active'   => Program::where('type', 'english')->where('status', 'active')->count(),
            'inactive' => Program::where('type', 'english')->where('status', 'inactive')->count(),
        ];

        $levelNames = self::$levelNames;

        return view('admin.english.index', compact('programs', 'stats', 'levelNames'));
    }

    public function create()
    {
        $levelNames = self::$levelNames;
        return view('admin.english.create', compact('levelNames'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar'         => 'nullable|string|max:255',
            'name_en'         => 'nullable|string|max:255',
            'code'            => 'nullable|string|unique:programs,code',
            'description_ar'  => 'nullable|string',
            'description_en'  => 'nullable|string',
            'level'           => 'required|integer|min:0|max:14',
            'duration_months' => 'nullable|integer|min:1',
            'price'           => 'nullable|numeric|min:0',
            'status'          => 'nullable|in:active,inactive',
            'supervisor_name' => 'nullable|string|max:255',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->only([
            'name_ar', 'name_en', 'code', 'description_ar', 'description_en',
            'level', 'duration_months', 'price', 'status', 'supervisor_name',
        ]);

        $data['type'] = 'english';

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads/images', 'public');
        }

        Program::create($data);

        return redirect()->route('admin.english.index')
            ->with('success', 'تم إضافة الدورة بنجاح');
    }

    public function show(Program $english)
    {
        $english->load([
            'terms' => function ($query) {
                $query->withCount('subjects')
                      ->with(['subjects' => fn($q) => $q->with(['teacher:id,name', 'teachers:id,name'])->orderBy('name_ar')])
                      ->orderBy('term_number');
            },
        ]);

        $allSubjects = Subject::orderBy('name_ar')->get(['id', 'name_ar', 'name_en', 'code']);
        $teachers    = User::where('role', 'teacher')->orderBy('name')->get(['id', 'name']);
        $program     = $english;
        $classes     = \App\Models\ProgramClass::where('program_id', $program->id)
                            ->withCount('students')
                            ->with('teacher:id,name')
                            ->latest()
                            ->get();

        return view('admin.programs.show', compact('program', 'allSubjects', 'teachers', 'classes'))
            ->with('backRoute', 'admin.english.index')
            ->with('pageLabel', 'دورات اللغة الإنجليزية');
    }

    public function edit(Program $english)
    {
        $levelNames = self::$levelNames;
        return view('admin.english.edit', ['program' => $english, 'levelNames' => $levelNames]);
    }

    public function update(Request $request, Program $english)
    {
        $validated = $request->validate([
            'name_ar'         => 'nullable|string|max:255',
            'name_en'         => 'nullable|string|max:255',
            'code'            => 'nullable|string|unique:programs,code,' . $english->id,
            'description_ar'  => 'nullable|string',
            'description_en'  => 'nullable|string',
            'level'           => 'required|integer|min:0|max:14',
            'duration_hours'  => 'nullable|integer|min:1',
            'price'           => 'nullable|numeric|min:0',
            'status'          => 'nullable|in:active,inactive',
            'supervisor_name' => 'nullable|string|max:255',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_image'    => 'nullable|boolean',
        ]);

        $validated['type'] = 'english';

        if ($request->hasFile('image')) {
            if ($english->image) {
                Storage::disk('public')->delete($english->image);
            }
            $validated['image'] = $request->file('image')->store('uploads/images', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($english->image) {
                Storage::disk('public')->delete($english->image);
            }
            $validated['image'] = null;
        }

        unset($validated['remove_image']);
        $english->update($validated);

        return redirect()->route('admin.english.index')
            ->with('success', 'تم تحديث الدورة بنجاح');
    }

    public function destroy(Program $english)
    {
        $english->delete();

        return redirect()->route('admin.english.index')
            ->with('success', 'تم حذف الدورة بنجاح');
    }
}
