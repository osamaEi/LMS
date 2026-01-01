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
}
