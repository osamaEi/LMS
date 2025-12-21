<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SatisfactionSurvey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();

        // Get surveys available to teacher
        $availableSurveys = SatisfactionSurvey::where('status', 'active')
            ->where('type', 'teacher')
            ->where(function($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->whereDoesntHave('responses', function($q) use ($teacher) {
                $q->where('user_id', $teacher->id);
            })
            ->get();

        // Get completed surveys
        $completedSurveys = SatisfactionSurvey::whereHas('responses', function($q) use ($teacher) {
                $q->where('user_id', $teacher->id);
            })
            ->get();

        return view('teacher.surveys.index', compact('availableSurveys', 'completedSurveys'));
    }

    public function show(SatisfactionSurvey $survey)
    {
        $teacher = auth()->user();

        $hasCompleted = SurveyResponse::where('user_id', $teacher->id)
            ->whereHas('question', function($q) use ($survey) {
                $q->where('survey_id', $survey->id);
            })
            ->exists();

        if ($hasCompleted) {
            return redirect()->route('teacher.surveys.index')
                ->with('info', 'لقد قمت بإكمال هذا الاستبيان مسبقاً');
        }

        $survey->load('questions');

        return view('teacher.surveys.show', compact('survey'));
    }

    public function submit(Request $request, SatisfactionSurvey $survey)
    {
        $teacher = auth()->user();

        foreach ($survey->questions as $question) {
            $responseData = [
                'question_id' => $question->id,
                'user_id' => $teacher->id,
            ];

            $value = $request->input("responses.{$question->id}");
            $comment = $request->input("comments.{$question->id}");

            if ($question->type === 'rating') {
                $responseData['rating'] = $value;
                $responseData['comment'] = $comment;
            } else {
                $responseData['text_response'] = $value;
            }

            SurveyResponse::create($responseData);
        }

        return redirect()->route('teacher.surveys.index')
            ->with('success', 'شكراً لك! تم إرسال إجاباتك بنجاح');
    }
}
