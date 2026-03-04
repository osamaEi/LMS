<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Term;
use Illuminate\Http\Request;

class TermController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Term::with(['program'])->withCount('subjects');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhereHas('program', function ($pq) use ($search) {
                      $pq->where('name_ar', 'like', "%{$search}%")
                         ->orWhere('name_en', 'like', "%{$search}%");
                  });
            });
        }

        $terms = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'     => Term::count(),
            'active'    => Term::where('status', 'active')->count(),
            'upcoming'  => Term::where('status', 'upcoming')->count(),
            'completed' => Term::where('status', 'completed')->count(),
        ];

        return view('admin.terms.index', compact('terms', 'stats', 'search'));
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

        $term = Term::create($validated);

        // Assign subjects if provided
        if ($request->has('subject_ids') && is_array($request->subject_ids)) {
            $term->subjects()->sync($request->subject_ids);
        }

        if ($request->has('program_id') && $request->header('referer') && str_contains($request->header('referer'), 'programs')) {
            return redirect()->route('admin.programs.show', $request->program_id)
                ->with('success', 'تم إضافة الربع الدراسي بنجاح');
        }

        return redirect()->route('admin.terms.show', $term)
            ->with('success', 'تم إضافة الربع الدراسي بنجاح');
    }

    public function show(Term $term)
    {
        $term->load(['program', 'subjects' => function ($query) {
            $query->with(['files', 'teacher'])->withCount('sessions');
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

        return redirect()->route('admin.terms.show', $term)
            ->with('success', 'تم تحديث الربع الدراسي بنجاح');
    }

    public function destroy(Term $term)
    {
        $term->delete();

        return redirect()->route('admin.terms.index')
            ->with('success', 'تم حذف الربع الدراسي بنجاح');
    }

    public function syncSubjects(Request $request, Term $term)
    {
        $request->validate([
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        $term->subjects()->sync($request->subject_ids ?? []);

        return back()->with('success', 'تم تحديث المواد الدراسية بنجاح');
    }
}
