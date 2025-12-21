<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SatisfactionSurvey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use App\Models\Subject;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index()
    {
        $surveys = SatisfactionSurvey::with(['subject', 'questions'])
            ->withCount('responses')
            ->latest()
            ->paginate(15);

        return view('admin.surveys.index', compact('surveys'));
    }

    public function create()
    {
        $subjects = Subject::where('status', 'active')->get();
        return view('admin.surveys.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'nullable|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:student,teacher,general',
            'is_mandatory' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:rating,text,multiple_choice,yes_no',
            'questions.*.options' => 'nullable|array',
            'questions.*.is_required' => 'boolean',
            'questions.*.requires_comment_on_low_rating' => 'boolean',
        ]);

        $survey = SatisfactionSurvey::create([
            'subject_id' => $validated['subject_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'is_mandatory' => $validated['is_mandatory'] ?? false,
            'starts_at' => $validated['starts_at'] ?? now(),
            'ends_at' => $validated['ends_at'] ?? null,
            'status' => 'active',
        ]);

        foreach ($validated['questions'] as $index => $questionData) {
            SurveyQuestion::create([
                'survey_id' => $survey->id,
                'question' => $questionData['question'],
                'type' => $questionData['type'],
                'options' => $questionData['options'] ?? null,
                'is_required' => $questionData['is_required'] ?? true,
                'requires_comment_on_low_rating' => $questionData['requires_comment_on_low_rating'] ?? false,
                'order' => $index + 1,
            ]);
        }

        return redirect()->route('admin.surveys.index')
            ->with('success', 'تم إنشاء الاستبيان بنجاح');
    }

    public function show(SatisfactionSurvey $survey)
    {
        $survey->load(['subject', 'questions.responses', 'responses.user']);

        // Calculate statistics
        $stats = [
            'total_responses' => $survey->responses()->distinct('user_id')->count('user_id'),
            'avg_rating' => $survey->getAverageRating(),
            'low_ratings' => SurveyResponse::whereHas('question', function($q) use ($survey) {
                $q->where('survey_id', $survey->id);
            })->where('rating', '<=', 2)->count(),
        ];

        return view('admin.surveys.show', compact('survey', 'stats'));
    }

    public function edit(SatisfactionSurvey $survey)
    {
        $subjects = Subject::where('status', 'active')->get();
        $survey->load('questions');
        return view('admin.surveys.edit', compact('survey', 'subjects'));
    }

    public function update(Request $request, SatisfactionSurvey $survey)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,active,closed',
            'ends_at' => 'nullable|date',
        ]);

        $survey->update($validated);

        return redirect()->route('admin.surveys.show', $survey)
            ->with('success', 'تم تحديث الاستبيان بنجاح');
    }

    public function destroy(SatisfactionSurvey $survey)
    {
        $survey->delete();
        return redirect()->route('admin.surveys.index')
            ->with('success', 'تم حذف الاستبيان بنجاح');
    }

    public function report(SatisfactionSurvey $survey)
    {
        $survey->load(['questions.responses.user', 'subject']);

        $questionStats = [];
        foreach ($survey->questions as $question) {
            $questionStats[$question->id] = [
                'question' => $question->question,
                'type' => $question->type,
                'avg_rating' => $question->getAverageRating(),
                'low_count' => $question->getLowRatingCount(),
                'responses_count' => $question->responses()->count(),
            ];
        }

        return view('admin.surveys.report', compact('survey', 'questionStats'));
    }
}
