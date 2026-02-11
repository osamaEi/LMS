<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\SatisfactionSurvey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    /**
     * GET /api/v1/student/surveys
     * List available and completed surveys
     */
    public function index()
    {
        $student = auth()->user();

        $availableSurveys = SatisfactionSurvey::where('status', 'active')
            ->where('type', 'student')
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->whereDoesntHave('responses', function ($q) use ($student) {
                $q->where('user_id', $student->id);
            })
            ->with('subject:id,name_ar,name_en')
            ->get();

        $completedSurveys = SatisfactionSurvey::whereHas('responses', function ($q) use ($student) {
            $q->where('user_id', $student->id);
        })
            ->with('subject:id,name_ar,name_en')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'available_surveys' => $availableSurveys,
                'completed_surveys' => $completedSurveys,
            ],
        ]);
    }

    /**
     * GET /api/v1/student/surveys/{id}
     * Show survey questions
     */
    public function show($id)
    {
        $student = auth()->user();

        $survey = SatisfactionSurvey::with('questions')->findOrFail($id);

        $hasCompleted = SurveyResponse::where('user_id', $student->id)
            ->whereHas('question', fn($q) => $q->where('survey_id', $survey->id))
            ->exists();

        if ($hasCompleted) {
            return response()->json([
                'success' => false,
                'message' => 'لقد قمت بإكمال هذا الاستبيان مسبقاً',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $survey,
        ]);
    }

    /**
     * POST /api/v1/student/surveys/{id}/submit
     * Submit survey responses
     */
    public function submit(Request $request, $id)
    {
        $student = auth()->user();

        $survey = SatisfactionSurvey::with('questions')->findOrFail($id);

        // Check if already completed
        $hasCompleted = SurveyResponse::where('user_id', $student->id)
            ->whereHas('question', fn($q) => $q->where('survey_id', $survey->id))
            ->exists();

        if ($hasCompleted) {
            return response()->json([
                'success' => false,
                'message' => 'لقد قمت بإكمال هذا الاستبيان مسبقاً',
            ], 422);
        }

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

            if ($question->requires_comment_on_low_rating) {
                $rules["comments.{$question->id}"] = 'required_if:responses.' . $question->id . ',1,2|nullable|string';
            }
        }

        $request->validate($rules);

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

        return response()->json([
            'success' => true,
            'message' => 'شكراً لك! تم إرسال إجاباتك بنجاح',
        ]);
    }
}
