<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::withCount('terms')
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
        return view('admin.programs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'code' => 'required|string|unique:programs,code',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'duration_months' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        Program::create($validated);

        return redirect()->route('admin.programs.index')
            ->with('success', 'تم إضافة المسار بنجاح');
    }

    public function show(Program $program)
    {
        $program->load(['terms' => function($query) {
            $query->withCount('subjects')->latest();
        }]);

        return view('admin.programs.show', compact('program'));
    }

    public function edit(Program $program)
    {
        return view('admin.programs.edit', compact('program'));
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'code' => 'required|string|unique:programs,code,' . $program->id,
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'duration_months' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ]);

        $program->update($validated);

        return redirect()->route('admin.programs.index')
            ->with('success', 'تم تحديث المسار بنجاح');
    }

    public function destroy(Program $program)
    {
        $program->delete();

        return redirect()->route('admin.programs.index')
            ->with('success', 'تم حذف المسار بنجاح');
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
