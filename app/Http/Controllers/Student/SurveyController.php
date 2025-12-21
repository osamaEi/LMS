<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SatisfactionSurvey;
use App\Models\SurveyQuestion;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index()
    {
        $student = auth()->user();

        // Get surveys available to student
        $availableSurveys = SatisfactionSurvey::where('status', 'active')
            ->where('type', 'student')
            ->where(function($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->whereDoesntHave('responses', function($q) use ($student) {
                $q->where('user_id', $student->id);
            })
            ->with('subject')
            ->get();

        // Get completed surveys
        $completedSurveys = SatisfactionSurvey::whereHas('responses', function($q) use ($student) {
                $q->where('user_id', $student->id);
            })
            ->with('subject')
            ->get();

        return view('student.surveys.index', compact('availableSurveys', 'completedSurveys'));
    }

    public function show(SatisfactionSurvey $survey)
    {
        $student = auth()->user();

        // Check if already completed
        $hasCompleted = SurveyResponse::where('user_id', $student->id)
            ->whereHas('question', function($q) use ($survey) {
                $q->where('survey_id', $survey->id);
            })
            ->exists();

        if ($hasCompleted) {
            return redirect()->route('student.surveys.index')
                ->with('info', 'لقد قمت بإكمال هذا الاستبيان مسبقاً');
        }

        $survey->load('questions');

        return view('student.surveys.show', compact('survey'));
    }

    public function submit(Request $request, SatisfactionSurvey $survey)
    {
        $student = auth()->user();

        // Validate responses
        $rules = [];
        foreach ($survey->questions as $question) {
            $key = "responses.{$question->id}";
            if ($question->is_required) {
                if ($question->type === 'rating') {
                    $rules[$key] = 'required|integer|min:1|max:5';
                } else {
                    $rules[$key] = 'required|string';
                }
            }

            // Comment required for low ratings
            if ($question->requires_comment_on_low_rating) {
                $rules["comments.{$question->id}"] = 'required_if:responses.' . $question->id . ',1,2|nullable|string';
            }
        }

        $validated = $request->validate($rules);

        // Save responses
        foreach ($survey->questions as $question) {
            $responseData = [
                'question_id' => $question->id,
                'user_id' => $student->id,
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

        return redirect()->route('student.surveys.index')
            ->with('success', 'شكراً لك! تم إرسال إجاباتك بنجاح');
    }
}
