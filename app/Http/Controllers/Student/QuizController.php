<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Display available quizzes for the student
     */
    public function index()
    {
        $student = auth()->user();

        // Get enrolled subjects
        $enrolledSubjectIds = $student->enrollments()->pluck('subject_id');

        // Get quizzes from enrolled subjects
        $quizzes = Quiz::whereIn('subject_id', $enrolledSubjectIds)
            ->where('is_active', true)
            ->with('subject')
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($quiz) use ($student) {
                $quiz->student_attempts = $quiz->attemptsForStudent($student->id)->whereNotNull('submitted_at')->count();
                $quiz->can_attempt = $quiz->canStudentAttempt($student->id);
                $quiz->best_score = $quiz->bestScoreForStudent($student->id);
                $quiz->in_progress_attempt = $quiz->attemptsForStudent($student->id)->whereNull('submitted_at')->first();
                return $quiz;
            });

        // Separate by availability
        $availableQuizzes = $quizzes->filter(fn($q) => $q->isAvailable());
        $upcomingQuizzes = $quizzes->filter(fn($q) => !$q->hasStarted());
        $pastQuizzes = $quizzes->filter(fn($q) => $q->hasEnded());

        return view('student.quizzes.index', compact('availableQuizzes', 'upcomingQuizzes', 'pastQuizzes'));
    }

    /**
     * Show quiz details before starting
     */
    public function show($quizId)
    {
        $student = auth()->user();

        $quiz = Quiz::with('subject')
            ->withCount('questions')
            ->findOrFail($quizId);

        // Verify student is enrolled in the subject
        $isEnrolled = $student->enrollments()->where('subject_id', $quiz->subject_id)->exists();

        if (!$isEnrolled) {
            abort(403, 'أنت غير مسجل في هذه المادة');
        }

        $attempts = $quiz->attemptsForStudent($student->id)
            ->whereNotNull('submitted_at')
            ->orderBy('submitted_at', 'desc')
            ->get();

        $canAttempt = $quiz->canStudentAttempt($student->id);
        $remainingAttempts = $quiz->remainingAttempts($student->id);
        $inProgressAttempt = $quiz->attemptsForStudent($student->id)->whereNull('submitted_at')->first();

        return view('student.quizzes.show', compact('quiz', 'attempts', 'canAttempt', 'remainingAttempts', 'inProgressAttempt'));
    }

    /**
     * Start a new quiz attempt
     */
    public function start($quizId)
    {
        $student = auth()->user();

        $quiz = Quiz::findOrFail($quizId);

        // Verify enrollment
        $isEnrolled = $student->enrollments()->where('subject_id', $quiz->subject_id)->exists();
        if (!$isEnrolled) {
            abort(403, 'أنت غير مسجل في هذه المادة');
        }

        // Check if quiz is available
        if (!$quiz->isAvailable()) {
            return back()->with('error', 'الاختبار غير متاح حالياً');
        }

        // Check if student can attempt
        if (!$quiz->canStudentAttempt($student->id)) {
            return back()->with('error', 'لقد استنفدت جميع المحاولات المتاحة');
        }

        // Check for existing in-progress attempt
        $existingAttempt = $quiz->attemptsForStudent($student->id)->whereNull('submitted_at')->first();

        if ($existingAttempt) {
            return redirect()->route('student.quizzes.take', $existingAttempt->id);
        }

        // Create new attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'started_at' => now(),
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('student.quizzes.take', $attempt->id);
    }

    /**
     * Display the quiz taking page
     */
    public function take($attemptId)
    {
        $student = auth()->user();

        $attempt = QuizAttempt::where('student_id', $student->id)
            ->with(['quiz.questions.options', 'answers'])
            ->findOrFail($attemptId);

        // Check if already submitted
        if ($attempt->isCompleted()) {
            return redirect()->route('student.quizzes.result', $attempt->id);
        }

        // Check if time expired
        if ($attempt->hasTimeExpired()) {
            $this->submitAttempt($attempt);
            return redirect()->route('student.quizzes.result', $attempt->id)
                ->with('warning', 'انتهى وقت الاختبار وتم تسليمه تلقائياً');
        }

        $quiz = $attempt->quiz;

        // Get questions (shuffled if enabled)
        $questions = $quiz->questions;
        if ($quiz->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        // Get existing answers
        $existingAnswers = $attempt->answers->keyBy('question_id');

        return view('student.quizzes.take', compact('attempt', 'quiz', 'questions', 'existingAnswers'));
    }

    /**
     * Save answer for a question (AJAX)
     */
    public function saveAnswer(Request $request, $attemptId)
    {
        $student = auth()->user();

        $attempt = QuizAttempt::where('student_id', $student->id)
            ->whereNull('submitted_at')
            ->findOrFail($attemptId);

        // Check if time expired
        if ($attempt->hasTimeExpired()) {
            $this->submitAttempt($attempt);
            return response()->json([
                'success' => false,
                'expired' => true,
                'message' => 'انتهى وقت الاختبار'
            ]);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected_option_id' => 'nullable|exists:question_options,id',
            'answer_text' => 'nullable|string',
        ]);

        // Verify question belongs to quiz
        $quiz = $attempt->quiz;
        $question = $quiz->questions()->find($validated['question_id']);

        if (!$question) {
            return response()->json(['success' => false, 'message' => 'السؤال غير موجود']);
        }

        // Save or update answer
        $answer = StudentAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'selected_option_id' => $validated['selected_option_id'] ?? null,
                'answer_text' => $validated['answer_text'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ الإجابة',
            'answered_count' => $attempt->answers()->count(),
        ]);
    }

    /**
     * Submit the quiz attempt
     */
    public function submit(Request $request, $attemptId)
    {
        $student = auth()->user();

        $attempt = QuizAttempt::where('student_id', $student->id)
            ->whereNull('submitted_at')
            ->findOrFail($attemptId);

        $this->submitAttempt($attempt);

        return redirect()->route('student.quizzes.result', $attempt->id)
            ->with('success', 'تم تسليم الاختبار بنجاح');
    }

    /**
     * Process and submit the attempt
     */
    private function submitAttempt(QuizAttempt $attempt)
    {
        DB::beginTransaction();

        try {
            // Auto-grade objective questions
            foreach ($attempt->answers as $answer) {
                $answer->autoGrade();
            }

            // Mark unanswered questions as wrong
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

            // Calculate time spent
            $timeSpent = $attempt->started_at->diffInSeconds(now());

            // Update attempt
            $attempt->update([
                'submitted_at' => now(),
                'time_spent_seconds' => $timeSpent,
            ]);

            // Calculate score
            $attempt->calculateScore();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Show quiz result
     */
    public function result($attemptId)
    {
        $student = auth()->user();

        $attempt = QuizAttempt::where('student_id', $student->id)
            ->whereNotNull('submitted_at')
            ->with(['quiz.subject', 'answers.question.options', 'answers.selectedOption'])
            ->findOrFail($attemptId);

        $quiz = $attempt->quiz;

        // Check if results should be shown
        $showResults = $quiz->show_results;
        $showCorrectAnswers = $quiz->show_correct_answers;

        return view('student.quizzes.result', compact('attempt', 'quiz', 'showResults', 'showCorrectAnswers'));
    }
}
