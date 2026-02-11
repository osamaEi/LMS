<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\QuizAttempt;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * GET /api/v1/student/subjects/{subjectId}/quizzes
     * List available quizzes for a subject
     */
    public function index($subjectId)
    {
        $student = auth()->user();
        $subject = Subject::findOrFail($subjectId);

        $isEnrolled = $student->enrollments()->where('subject_id', $subjectId)->exists();
        if (!$isEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'أنت غير مسجل في هذه المادة',
            ], 403);
        }

        $quizzes = Quiz::where('subject_id', $subjectId)
            ->where('is_active', true)
            ->with('subject:id,name_ar,name_en')
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($quiz) use ($student) {
                $quiz->student_attempts = $quiz->attemptsForStudent($student->id)->whereNotNull('submitted_at')->count();
                $quiz->can_attempt = $quiz->canStudentAttempt($student->id);
                $quiz->best_score = $quiz->bestScoreForStudent($student->id);
                $quiz->has_in_progress = $quiz->attemptsForStudent($student->id)->whereNull('submitted_at')->exists();
                $quiz->is_available = $quiz->isAvailable();
                $quiz->has_started = $quiz->hasStarted();
                $quiz->has_ended = $quiz->hasEnded();
                return $quiz;
            });

        return response()->json([
            'success' => true,
            'data' => [
                'subject' => $subject,
                'quizzes' => $quizzes,
            ],
        ]);
    }

    /**
     * GET /api/v1/student/subjects/{subjectId}/quizzes/{quizId}
     * Show quiz details
     */
    public function show($subjectId, $quizId)
    {
        $student = auth()->user();
        Subject::findOrFail($subjectId);

        $isEnrolled = $student->enrollments()->where('subject_id', $subjectId)->exists();
        if (!$isEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'أنت غير مسجل في هذه المادة',
            ], 403);
        }

        $quiz = Quiz::where('subject_id', $subjectId)
            ->with('subject:id,name_ar,name_en')
            ->withCount('questions')
            ->findOrFail($quizId);

        $attempts = $quiz->attemptsForStudent($student->id)
            ->whereNotNull('submitted_at')
            ->orderBy('submitted_at', 'desc')
            ->get();

        $activeAttempt = $quiz->attemptsForStudent($student->id)->whereNull('submitted_at')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'quiz' => $quiz,
                'attempts' => $attempts,
                'active_attempt' => $activeAttempt,
                'can_attempt' => $quiz->canStudentAttempt($student->id),
                'is_available' => $quiz->isAvailable(),
            ],
        ]);
    }

    /**
     * POST /api/v1/student/subjects/{subjectId}/quizzes/{quizId}/start
     * Start a new quiz attempt
     */
    public function start($subjectId, $quizId)
    {
        $student = auth()->user();
        Subject::findOrFail($subjectId);

        $quiz = Quiz::where('subject_id', $subjectId)->findOrFail($quizId);

        $isEnrolled = $student->enrollments()->where('subject_id', $subjectId)->exists();
        if (!$isEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'أنت غير مسجل في هذه المادة',
            ], 403);
        }

        if (!$quiz->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'الاختبار غير متاح حالياً',
            ], 422);
        }

        if (!$quiz->canStudentAttempt($student->id)) {
            return response()->json([
                'success' => false,
                'message' => 'لقد استنفدت جميع المحاولات المتاحة',
            ], 422);
        }

        // Check for existing in-progress attempt
        $existingAttempt = $quiz->attemptsForStudent($student->id)->whereNull('submitted_at')->first();
        if ($existingAttempt) {
            return response()->json([
                'success' => true,
                'message' => 'لديك محاولة جارية بالفعل',
                'data' => [
                    'attempt' => $existingAttempt,
                    'questions' => $quiz->questions()->with('options')->get(),
                ],
            ]);
        }

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'started_at' => now(),
            'ends_at' => $quiz->duration_minutes ? now()->addMinutes($quiz->duration_minutes) : null,
            'ip_address' => request()->ip(),
        ]);

        $questions = $quiz->questions()->with('options')->get();
        if ($quiz->shuffle_questions) {
            $questions = $questions->shuffle()->values();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'attempt' => $attempt,
                'questions' => $questions,
            ],
        ]);
    }

    /**
     * GET /api/v1/student/subjects/{subjectId}/quizzes/{quizId}/take
     * Get quiz questions for in-progress attempt
     */
    public function take($subjectId, $quizId)
    {
        $student = auth()->user();

        $quiz = Quiz::where('subject_id', $subjectId)
            ->with('questions.options')
            ->findOrFail($quizId);

        $attempt = QuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $student->id)
            ->whereNull('submitted_at')
            ->first();

        if (!$attempt) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد محاولة جارية',
            ], 404);
        }

        // Check time expiry
        if ($attempt->ends_at && now() > $attempt->ends_at) {
            $this->submitAttempt($attempt);
            return response()->json([
                'success' => false,
                'message' => 'انتهى وقت الاختبار وتم تسليمه تلقائياً',
                'data' => ['attempt_id' => $attempt->id],
            ], 422);
        }

        $questions = $quiz->questions;
        if ($quiz->shuffle_questions) {
            $questions = $questions->shuffle()->values();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'quiz' => $quiz->only(['id', 'title', 'duration_minutes']),
                'attempt' => $attempt,
                'questions' => $questions,
                'remaining_seconds' => $attempt->ends_at ? now()->diffInSeconds($attempt->ends_at, false) : null,
            ],
        ]);
    }

    /**
     * POST /api/v1/student/subjects/{subjectId}/quizzes/{quizId}/submit
     * Submit quiz answers
     */
    public function submit(Request $request, $subjectId, $quizId)
    {
        $student = auth()->user();
        Subject::findOrFail($subjectId);
        Quiz::where('subject_id', $subjectId)->findOrFail($quizId);

        $attempt = QuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $student->id)
            ->whereNull('submitted_at')
            ->firstOrFail();

        // Save answers
        if ($request->has('answers')) {
            foreach ($request->answers as $questionId => $answerData) {
                StudentAnswer::updateOrCreate(
                    [
                        'attempt_id' => $attempt->id,
                        'question_id' => $questionId,
                    ],
                    [
                        'selected_option_id' => $answerData['option_id'] ?? null,
                        'answer_text' => $answerData['text'] ?? null,
                    ]
                );
            }
        }

        $this->submitAttempt($attempt);

        $attempt->refresh();

        return response()->json([
            'success' => true,
            'message' => 'تم تسليم الاختبار بنجاح',
            'data' => [
                'attempt_id' => $attempt->id,
                'score' => $attempt->score,
                'total_marks' => $attempt->total_marks,
                'percentage' => $attempt->percentage,
                'time_spent_seconds' => $attempt->time_spent_seconds,
            ],
        ]);
    }

    /**
     * GET /api/v1/student/subjects/{subjectId}/quizzes/{quizId}/result/{attemptId}
     * Show quiz result
     */
    public function result($subjectId, $quizId, $attemptId)
    {
        $student = auth()->user();

        $attempt = QuizAttempt::where('student_id', $student->id)
            ->whereNotNull('submitted_at')
            ->with(['quiz.subject:id,name_ar,name_en', 'answers.question.options', 'answers.selectedOption'])
            ->findOrFail($attemptId);

        $quiz = $attempt->quiz;

        if ($quiz->id != $quizId || $quiz->subject_id != $subjectId) {
            return response()->json([
                'success' => false,
                'message' => 'نتيجة غير صالحة',
            ], 404);
        }

        $data = [
            'attempt' => $attempt,
            'quiz' => $quiz,
            'show_results' => $quiz->show_results,
        ];

        // Only include answers if quiz allows showing correct answers
        if ($quiz->show_correct_answers) {
            $data['answers'] = $attempt->answers->load('question.options', 'selectedOption');
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    private function submitAttempt(QuizAttempt $attempt)
    {
        DB::beginTransaction();

        try {
            foreach ($attempt->answers as $answer) {
                $answer->autoGrade();
            }

            $answeredQuestionIds = $attempt->answers->pluck('question_id')->toArray();
            $unansweredQuestions = $attempt->quiz->questions()
                ->whereNotIn('id', $answeredQuestionIds)
                ->get();

            foreach ($unansweredQuestions as $question) {
                StudentAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'is_correct' => false,
                    'marks_obtained' => 0,
                ]);
            }

            $timeSpent = $attempt->started_at->diffInSeconds(now());

            $attempt->update([
                'submitted_at' => now(),
                'time_spent_seconds' => $timeSpent,
            ]);

            $attempt->calculateScore();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
