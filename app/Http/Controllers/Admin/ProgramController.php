<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Term;
use App\Models\Subject;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::withCount(['terms', 'tracks'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => Program::count(),
            'active' => Program::where('status', 'active')->count(),
            'inactive' => Program::where('status', 'inactive')->count(),
        ];

        return view('admin.programs.index', compact('programs', 'stats'));
    }

    public function create()
    {
        $subjects = Subject::orderBy('name_ar')->get(['id', 'name_ar', 'name_en', 'code']);
        return view('admin.programs.create', compact('subjects'));
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

        $program = Program::create($request->only([
            'name_ar', 'name_en', 'code', 'description_ar', 'description_en',
            'duration_months', 'price', 'status',
        ]));

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

        return redirect()->route('admin.programs.show', $program)
            ->with('success', 'تم إضافة الدبلومة بنجاح');
    }

    public function show(Program $program)
    {
        $program->load([
            'terms' => function ($query) {
                $query->withCount('subjects')
                      ->with(['subjects' => fn($q) => $q->with('teacher:id,name')->orderBy('name_ar')])
                      ->orderBy('term_number');
            },
            'tracks',
        ]);

        $allSubjects = Subject::orderBy('name_ar')->get(['id', 'name_ar', 'name_en', 'code']);

        return view('admin.programs.show', compact('program', 'allSubjects'));
    }

    public function edit(Program $program)
    {
        return view('admin.programs.edit', compact('program'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'code' => 'nullable|string|unique:programs,code,' . $program->id,
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'duration_months' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
        ]);

        $program->update($validated);

        return redirect()->route('admin.programs.index')
            ->with('success', 'تم تحديث الدبلومة بنجاح');
    }

    public function destroy(Program $program)
    {
        $program->delete();

        return redirect()->route('admin.programs.index')
            ->with('success', 'تم حذف الدبلومة بنجاح');
    }

    public function export()
    {
        $programs = Program::withCount('terms')->latest()->get();

        $filename = 'programs_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($programs) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['#', 'الاسم العربي', 'الاسم الإنجليزي', 'الرمز', 'المدة (أشهر)', 'السعر', 'عدد الفصول', 'الحالة', 'تاريخ الإنشاء']);

            foreach ($programs as $i => $program) {
                fputcsv($file, [
                    $i + 1,
                    $program->name_ar,
                    $program->name_en,
                    $program->code,
                    $program->duration_months ?? '',
                    $program->price ?? '0',
                    $program->terms_count,
                    $program->status === 'active' ? 'نشط' : 'غير نشط',
                    $program->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
