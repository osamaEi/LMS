<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * List all quizzes/exams across the platform with filters.
     */
    public function index(Request $request)
    {
        $search  = $request->get('search', '');
        $type    = $request->get('type', '');
        $subject = $request->get('subject_id', '');

        $quizzes = Quiz::query()
            ->with(['subject:id,name_ar,name_en', 'creator:id,name'])
            ->withCount(['questions', 'attempts'])
            ->withCount(['attempts as completed_attempts_count' => fn($q) => $q->whereNotNull('submitted_at')])
            ->when($search, fn($q) => $q->where(fn($w) =>
                $w->where('title_ar', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
            ))
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($subject, fn($q) => $q->where('subject_id', $subject))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Subjects for the filter dropdown (only those that have quizzes)
        $subjects = \App\Models\Subject::whereHas('quizzes')
            ->orderBy('name_ar')
            ->get(['id', 'name_ar', 'name_en']);

        $stats = [
            'total'       => Quiz::count(),
            'exams'       => Quiz::where('type', 'exam')->count(),
            'quizzes'     => Quiz::where('type', 'quiz')->count(),
            'attempts'    => QuizAttempt::whereNotNull('submitted_at')->count(),
        ];

        return view('admin.quizzes.index', compact('quizzes', 'subjects', 'stats', 'search', 'type', 'subject'));
    }

    /**
     * Show one quiz: its questions + every student attempt.
     */
    public function show(Quiz $quiz)
    {
        $quiz->load([
            'subject:id,name_ar,name_en',
            'creator:id,name',
            'questions.options',
        ]);

        $attempts = $quiz->attempts()
            ->with('student:id,name,email')
            ->orderByDesc('submitted_at')
            ->orderByDesc('started_at')
            ->get();

        // Best/last attempt per student, for the eligible-students table.
        $attemptByStudent = $attempts->groupBy('student_id')->map(function ($group) {
            return $group->sortByDesc(fn($a) => $a->submitted_at ?? $a->started_at)->first();
        });

        // Students who "have" this quiz = students enrolled in its subject
        // (via program, term's program, direct enrollment, or class).
        $eligibleStudents = $this->eligibleStudentsFor($quiz)
            ->map(function ($student) use ($attemptByStudent) {
                $student->attempt = $attemptByStudent[$student->id] ?? null;
                return $student;
            });

        $stats = [
            'total_attempts'     => $attempts->count(),
            'completed'          => $attempts->whereNotNull('submitted_at')->count(),
            'in_progress'        => $attempts->whereNull('submitted_at')->count(),
            'passed'             => $attempts->where('passed', true)->count(),
            'avg_percentage'     => round((float) $attempts->whereNotNull('submitted_at')->avg('percentage'), 1),
            'eligible'           => $eligibleStudents->count(),
            'not_attempted'      => $eligibleStudents->filter(fn($s) => !$s->attempt)->count(),
        ];

        return view('admin.quizzes.show', compact('quiz', 'attempts', 'eligibleStudents', 'stats'));
    }

    /**
     * Students enrolled in the quiz's subject (program / term-program / direct
     * enrollment / class). Mirrors the student-side access rule.
     */
    private function eligibleStudentsFor(Quiz $quiz): \Illuminate\Support\Collection
    {
        $subject = $quiz->subject;
        if (!$subject) {
            return collect();
        }

        $programIds = collect([$subject->program_id, optional($subject->term)->program_id])
            ->filter()->unique()->values();
        $classIds = collect([$subject->class_id, optional($subject->term)->class_id])
            ->filter()->unique()->values();

        return \App\Models\User::where('role', 'student')
            ->where(function ($q) use ($subject, $programIds, $classIds) {
                $q->whereHas('enrollments', fn($eq) => $eq->where('subject_id', $subject->id));
                if ($programIds->isNotEmpty()) {
                    $q->orWhereIn('program_id', $programIds)
                      ->orWhereHas('programs', fn($pq) => $pq->whereIn('programs.id', $programIds));
                }
                if ($classIds->isNotEmpty()) {
                    $q->orWhereIn('class_id', $classIds);
                }
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    /**
     * Show a single student's attempt with full answer-by-answer detail.
     */
    public function attempt(Quiz $quiz, QuizAttempt $attempt)
    {
        abort_unless($attempt->quiz_id === $quiz->id, 404);

        $quiz->load(['subject:id,name_ar,name_en']);
        $attempt->load([
            'student:id,name,email',
            'answers.question.options',
            'answers.selectedOption',
        ]);

        // Order answers by question order; build a question→answer map for display.
        $questions = $quiz->questions()->with('options')->orderBy('order')->get();
        $answersByQuestion = $attempt->answers->keyBy('question_id');

        return view('admin.quizzes.attempt', compact('quiz', 'attempt', 'questions', 'answersByQuestion'));
    }
}
