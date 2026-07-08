<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\QuizAttempt;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * The class ids this student belongs to (across all their programs).
     */
    private function studentClassIds($student)
    {
        return $student->allProgramIds()
            ->map(fn($pid) => $student->classIdForProgram((int) $pid))
            ->filter()->unique()->values();
    }

    /**
     * Fetch a quiz within a subject, enforcing this student's class visibility.
     * A quiz targeting another class is treated as not found (404).
     */
    private function findVisibleQuiz($student, $subjectId, $quizId, $query = null)
    {
        $query ??= Quiz::query();

        return $query->where('subject_id', $subjectId)
            ->visibleToClasses($this->studentClassIds($student))
            ->findOrFail($quizId);
    }

    private function canAccessSubject($student, $subjectId): bool
    {
        $classIds = $this->studentClassIds($student);

        return Subject::where('id', $subjectId)
            ->where(function ($q) use ($student, $classIds) {
                $q->whereHas('enrollments', fn($eq) => $eq->where('student_id', $student->id));
                 if ($classIds->isNotEmpty()) {
                    $q->orWhereIn('class_id', $classIds)
                      ->orWhereHas('term', fn($tq) => $tq->whereIn('class_id', $classIds));
                }
            })->exists();
    }

    /**
     * Display available quizzes for the student
     */
    public function index($subjectId)
    {
        $student = auth()->user();
        $subject = Subject::findOrFail($subjectId);

        if (!$this->canAccessSubject($student, $subjectId)) {
            abort(403, 'أنت غير مسجل في هذه المقرر ');
        }

        // A quiz may target one specific class; students of other classes on the
        // same subject must not see it. Scope by this student's class ids.
        $quizzes = Quiz::where('subject_id', $subjectId)
            ->where('is_active', true)
            ->visibleToClasses($this->studentClassIds($student))
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

        return view('student.quizzes.index', compact('subject', 'availableQuizzes', 'upcomingQuizzes', 'pastQuizzes'));
    }

    /**
     * Show quiz details before starting
     */
    public function show($subjectId, $quizId)
    {
        $student = auth()->user();
        $subject = Subject::findOrFail($subjectId);

        if (!$this->canAccessSubject($student, $subjectId)) {
            abort(403, 'أنت غير مسجل في هذه المقرر ');
        }

        $quiz = $this->findVisibleQuiz(
            $student, $subjectId, $quizId,
            Quiz::with('subject')->withCount('questions')
        );

        $attempts = $quiz->attemptsForStudent($student->id)
            ->whereNotNull('submitted_at')
            ->orderBy('submitted_at', 'desc')
            ->get();

        $activeAttempt = $quiz->attemptsForStudent($student->id)->whereNull('submitted_at')->first();

        return view('student.quizzes.show', compact('subject', 'quiz', 'attempts', 'activeAttempt'));
    }

    /**
     * Start a new quiz attempt
     */
    public function start($subjectId, $quizId)
    {
        $student = auth()->user();
        $subject = Subject::findOrFail($subjectId);

        if (!$this->canAccessSubject($student, $subjectId)) {
            abort(403, 'أنت غير مسجل في هذه المقرر ');
        }

        $quiz = $this->findVisibleQuiz($student, $subjectId, $quizId);

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
            return redirect()->route('student.quizzes.take', [$subjectId, $quizId]);
        }

        // Create new attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'started_at' => now(),
            'ends_at' => $quiz->duration_minutes ? now()->addMinutes($quiz->duration_minutes) : null,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('student.quizzes.take', [$subjectId, $quizId]);
    }

    /**
     * Display the quiz taking page
     */
    public function take($subjectId, $quizId)
    {
        $student = auth()->user();
        $subject = Subject::findOrFail($subjectId);
        $quiz = $this->findVisibleQuiz(
            $student, $subjectId, $quizId,
            Quiz::with('questions.options')
        );

        // Get attempt
        $attempt = QuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $student->id)
            ->whereNull('submitted_at')
            ->first();

        if (!$attempt) {
            return redirect()->route('student.quizzes.show', [$subjectId, $quizId])
                ->with('error', 'لا توجد محاولة جارية');
        }

        // Check if time expired
        if ($attempt->ends_at && now() > $attempt->ends_at) {
            $this->submitAttempt($attempt);
            return redirect()->route('student.quizzes.result', [$subjectId, $quizId, $attempt->id])
                ->with('warning', 'انتهى وقت الاختبار وتم تسليمه تلقائياً');
        }

        // Get questions (shuffled if enabled)
        $questions = $quiz->questions;
        if ($quiz->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        return view('student.quizzes.take', compact('subject', 'quiz', 'attempt', 'questions'));
    }

    /**
     * Submit the quiz attempt
     */
    public function submit(Request $request, $subjectId, $quizId)
    {
        $student = auth()->user();
        $subject = Subject::findOrFail($subjectId);
        $quiz = $this->findVisibleQuiz($student, $subjectId, $quizId);

        $attempt = QuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $student->id)
            ->whereNull('submitted_at')
            ->firstOrFail();

        // Save answers from request
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

        return redirect()->route('student.quizzes.result', [$subjectId, $quizId, $attempt->id])
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
    public function result($subjectId, $quizId, $attemptId)
    {
        $student = auth()->user();
        $subject = Subject::findOrFail($subjectId);

        $attempt = QuizAttempt::where('student_id', $student->id)
            ->whereNotNull('submitted_at')
            ->with(['quiz.subject', 'answers.question.options', 'answers.selectedOption'])
            ->findOrFail($attemptId);

        $quiz = $attempt->quiz;

        // Verify the attempt belongs to the correct quiz and subject
        if ($quiz->id != $quizId || $quiz->subject_id != $subjectId) {
            abort(404);
        }

        // Check if results should be shown
        $showResults = $quiz->show_results;
        $showCorrectAnswers = $quiz->show_correct_answers;

        return view('student.quizzes.result', compact('subject', 'attempt', 'quiz', 'showResults', 'showCorrectAnswers'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Course/English PROGRAM quizzes. Visibility: the student must be enrolled
    // in the program AND in the quiz's target class.
    // ─────────────────────────────────────────────────────────────────────────

    /** The class id this student holds in the given program, or null. */
    private function classIdInProgram($student, int $programId): ?int
    {
        return $student->classIdForProgram($programId);
    }

    private function canAccessProgram($student, int $programId): bool
    {
        return $student->allProgramIds()->contains($programId);
    }

    /**
     * Fetch a program quiz enforcing the student's class. A quiz targeting
     * another class is treated as not found (404).
     */
    private function findVisibleProgramQuiz($student, int $programId, $quizId, $query = null): Quiz
    {
        $query ??= Quiz::query();
        $classId = $this->classIdInProgram($student, $programId);

        return $query->where('program_id', $programId)
            ->visibleToClasses($classId ? [$classId] : [])
            ->findOrFail($quizId);
    }

    public function programIndex($programId)
    {
        $student = auth()->user();
        $program = Program::findOrFail($programId);

        if (!$this->canAccessProgram($student, (int) $programId)) {
            abort(403, 'أنت غير مسجل في هذا البرنامج');
        }

        $classId = $this->classIdInProgram($student, (int) $programId);

        $quizzes = Quiz::where('program_id', $programId)
            ->where('is_active', true)
            ->visibleToClasses($classId ? [$classId] : [])
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

        $availableQuizzes = $quizzes->filter(fn($q) => $q->isAvailable());
        $upcomingQuizzes  = $quizzes->filter(fn($q) => !$q->hasStarted());
        $pastQuizzes      = $quizzes->filter(fn($q) => $q->hasEnded());

        return view('student.quizzes.program.index', compact('program', 'availableQuizzes', 'upcomingQuizzes', 'pastQuizzes'));
    }

    public function programShow($programId, $quizId)
    {
        $student = auth()->user();
        $program = Program::findOrFail($programId);

        if (!$this->canAccessProgram($student, (int) $programId)) {
            abort(403, 'أنت غير مسجل في هذا البرنامج');
        }

        $quiz = $this->findVisibleProgramQuiz(
            $student, (int) $programId, $quizId,
            Quiz::withCount('questions')
        );

        $attempts = $quiz->attemptsForStudent($student->id)
            ->whereNotNull('submitted_at')
            ->orderBy('submitted_at', 'desc')
            ->get();

        $activeAttempt = $quiz->attemptsForStudent($student->id)->whereNull('submitted_at')->first();

        return view('student.quizzes.program.show', compact('program', 'quiz', 'attempts', 'activeAttempt'));
    }

    public function programStart($programId, $quizId)
    {
        $student = auth()->user();

        if (!$this->canAccessProgram($student, (int) $programId)) {
            abort(403, 'أنت غير مسجل في هذا البرنامج');
        }

        $quiz = $this->findVisibleProgramQuiz($student, (int) $programId, $quizId);

        if (!$quiz->isAvailable()) {
            return back()->with('error', 'الاختبار غير متاح حالياً');
        }
        if (!$quiz->canStudentAttempt($student->id)) {
            return back()->with('error', 'لقد استنفدت جميع المحاولات المتاحة');
        }

        $existingAttempt = $quiz->attemptsForStudent($student->id)->whereNull('submitted_at')->first();
        if ($existingAttempt) {
            return redirect()->route('student.quizzes.program.take', [$programId, $quizId]);
        }

        QuizAttempt::create([
            'quiz_id'    => $quiz->id,
            'student_id' => $student->id,
            'started_at' => now(),
            'ends_at'    => $quiz->duration_minutes ? now()->addMinutes($quiz->duration_minutes) : null,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('student.quizzes.program.take', [$programId, $quizId]);
    }

    public function programTake($programId, $quizId)
    {
        $student = auth()->user();
        $program = Program::findOrFail($programId);
        $quiz = $this->findVisibleProgramQuiz(
            $student, (int) $programId, $quizId,
            Quiz::with('questions.options')
        );

        $attempt = QuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $student->id)
            ->whereNull('submitted_at')
            ->first();

        if (!$attempt) {
            return redirect()->route('student.quizzes.program.show', [$programId, $quizId])
                ->with('error', 'لا توجد محاولة جارية');
        }

        if ($attempt->ends_at && now() > $attempt->ends_at) {
            $this->submitAttempt($attempt);
            return redirect()->route('student.quizzes.program.result', [$programId, $quizId, $attempt->id])
                ->with('warning', 'انتهى وقت الاختبار وتم تسليمه تلقائياً');
        }

        $questions = $quiz->questions;
        if ($quiz->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        return view('student.quizzes.program.take', compact('program', 'quiz', 'attempt', 'questions'));
    }

    public function programSubmit(Request $request, $programId, $quizId)
    {
        $student = auth()->user();

        if (!$this->canAccessProgram($student, (int) $programId)) {
            abort(403, 'أنت غير مسجل في هذا البرنامج');
        }

        $quiz = $this->findVisibleProgramQuiz($student, (int) $programId, $quizId);

        $attempt = QuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $student->id)
            ->whereNull('submitted_at')
            ->firstOrFail();

        if ($request->has('answers')) {
            foreach ($request->answers as $questionId => $answerData) {
                StudentAnswer::updateOrCreate(
                    ['attempt_id' => $attempt->id, 'question_id' => $questionId],
                    [
                        'selected_option_id' => $answerData['option_id'] ?? null,
                        'answer_text'        => $answerData['text'] ?? null,
                    ]
                );
            }
        }

        $this->submitAttempt($attempt);

        return redirect()->route('student.quizzes.program.result', [$programId, $quizId, $attempt->id])
            ->with('success', 'تم تسليم الاختبار بنجاح');
    }

    public function programResult($programId, $quizId, $attemptId)
    {
        $student = auth()->user();
        $program = Program::findOrFail($programId);

        $attempt = QuizAttempt::where('student_id', $student->id)
            ->whereNotNull('submitted_at')
            ->with(['quiz.program', 'answers.question.options', 'answers.selectedOption'])
            ->findOrFail($attemptId);

        $quiz = $attempt->quiz;

        if ($quiz->id != $quizId || $quiz->program_id != $programId) {
            abort(404);
        }

        $showResults = $quiz->show_results;
        $showCorrectAnswers = $quiz->show_correct_answers;

        return view('student.quizzes.program.result', compact('program', 'attempt', 'quiz', 'showResults', 'showCorrectAnswers'));
    }
}
