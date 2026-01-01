<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index()
    {
        $terms = Term::with(['program'])
            ->withCount('subjects')
            ->latest()
            ->paginate(15);

        return view('admin.terms.index', compact('terms'));
    }

    public function create()
    {
        $programs = Program::where('status', 'active')->get();

        return view('admin.terms.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'term_number' => 'required|integer|min:1',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:upcoming,active,completed',
        ]);

        Term::create($validated);

        // If coming from program show page, redirect back there
        if ($request->has('program_id') && $request->header('referer') && str_contains($request->header('referer'), 'programs')) {
            return redirect()->route('admin.programs.show', $request->program_id)
                ->with('success', 'تم إضافة الفصل الدراسي بنجاح');
        }

        return redirect()->route('admin.terms.index')
            ->with('success', 'تم إضافة الفصل الدراسي بنجاح');
    }

    public function show(Term $term)
    {
        $term->load(['program', 'subjects' => function($query) {
            $query->withCount('sessions')->latest();
        }]);

        return view('admin.terms.show', compact('term'));
    }

    public function edit(Term $term)
    {
        $programs = Program::where('status', 'active')->get();

        return view('admin.terms.edit', compact('term', 'programs'));
    }

    public function update(Request $request, Term $term)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'term_number' => 'required|integer|min:1',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:upcoming,active,completed',
        ]);

        $term->update($validated);

        return redirect()->route('admin.terms.index')
            ->with('success', 'تم تحديث الفصل الدراسي بنجاح');
    }

    public function destroy(Term $term)
    {
        $term->delete();

        return redirect()->route('admin.terms.index')
            ->with('success', 'تم حذف الفصل الدراسي بنجاح');
    }
}
