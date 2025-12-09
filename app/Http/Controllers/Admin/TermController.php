<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Term;
use App\Models\Track;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index()
    {
        $terms = Term::with(['program', 'track'])
            ->withCount('subjects')
            ->latest()
            ->paginate(15);

        return view('admin.terms.index', compact('terms'));
    }

    public function create()
    {
        $programs = Program::where('status', 'active')->get();
        $tracks = Track::where('status', 'active')->get();

        return view('admin.terms.create', compact('programs', 'tracks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'track_id' => 'nullable|exists:tracks,id',
            'term_number' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'registration_start_date' => 'nullable|date',
            'registration_end_date' => 'nullable|date|after:registration_start_date',
            'status' => 'required|in:upcoming,active,completed',
        ]);

        Term::create($validated);

        return redirect()->route('admin.terms.index')
            ->with('success', 'تم إضافة الفصل الدراسي بنجاح');
    }

    public function show(Term $term)
    {
        $term->load(['program', 'track', 'subjects' => function($query) {
            $query->withCount('sessions')->latest();
        }]);

        return view('admin.terms.show', compact('term'));
    }

    public function edit(Term $term)
    {
        $programs = Program::where('status', 'active')->get();
        $tracks = Track::where('status', 'active')->get();

        return view('admin.terms.edit', compact('term', 'programs', 'tracks'));
    }

    public function update(Request $request, Term $term)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'track_id' => 'nullable|exists:tracks,id',
            'term_number' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'registration_start_date' => 'nullable|date',
            'registration_end_date' => 'nullable|date|after:registration_start_date',
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
