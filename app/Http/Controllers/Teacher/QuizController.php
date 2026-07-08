<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Subject;
use App\Repositories\QuizRepository;
use App\Services\QuizService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct(
        protected QuizService $quizService,
        protected QuizRepository $quizzes,
    ) {}

    /**
     * Show the global create form: pick a subject, then its target class.
     */
    public function createGlobal()
    {
        $classes = $this->quizService->selectableClasses(auth()->id());

        return view('teacher.quizzes.create-global', compact('classes'));
    }

    /**
     * Store a quiz created from the global form and redirect to add questions.
     */
    public function storeGlobal(Request $request)
    {
        $teacher = auth()->user();

        $validated = $request->validate([
            'subject_id'       => 'required|exists:subjects,id',
            'class_id'         => 'required|exists:program_classes,id',
            'title_ar'         => 'required|string|max:255',
            'title_en'         => 'nullable|string|max:255',
            'description_ar'   => 'nullable|string',
            'type'             => 'required|in:quiz,midterm,exam,homework,paper',
            'duration_minutes' => 'nullable|integer|min:1',
            'total_marks'      => 'required|numeric|min:1',
            'pass_marks'       => 'required|numeric|min:0',
            'max_attempts'     => 'required|integer|min:1',
            'starts_at'        => 'nullable|date',
            'ends_at'          => 'nullable|date|after_or_equal:starts_at',
        ]);

        // Authorize the subject against the teacher's assignments.
        $subject = Subject::assignedToTeacher($teacher->id)->findOrFail($validated['subject_id']);

        $quiz = $this->quizService->createForSubject(
            $subject,
            $validated['class_id'] ?? null,
            $teacher->id,
            [
                'title_ar'         => $validated['title_ar'],
                'title_en'         => $validated['title_en'] ?? null,
                'description_ar'   => $validated['description_ar'] ?? null,
                'type'             => $validated['type'],
                'duration_minutes' => $validated['duration_minutes'] ?? null,
                'total_marks'      => $validated['total_marks'],
                'pass_marks'       => $validated['pass_marks'],
                'max_attempts'     => $validated['max_attempts'],
                'show_results'     => $request->boolean('show_results', true),
                'starts_at'        => $validated['starts_at'] ?? null,
                'ends_at'          => $validated['ends_at'] ?? null,
                'is_active'        => true,
            ],
        );

        return redirect()->route('teacher.quizzes.show', [$subject->id, $quiz->id])
            ->with('success', 'تم إنشاء الاختبار بنجاح. يمكنك الآن إضافة الأسئلة.');
    }

    /**
     * Display a listing of quizzes for a subject
     */
    public function index($subjectId)
    {
        $teacher = auth()->user();

        $subject = Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $quizzes = $this->quizzes->forSubject($subjectId);

        return view('teacher.quizzes.index', compact('subject', 'quizzes'));
    }

    /**
     * Show the form for creating a new quiz
     */
    public function create($subjectId)
    {
        $teacher = auth()->user();

        $subject = Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        return view('teacher.quizzes.create', compact('subject'));
    }

    /**
     * Store a newly created quiz
     */
    public function store(Request $request, $subjectId)
    {
        $teacher = auth()->user();

        $subject = Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'type' => 'required|in:quiz,midterm,exam,homework,paper',
            'duration_minutes' => 'nullable|integer|min:1',
            'total_marks' => 'required|numeric|min:1',
            'pass_marks' => 'required|numeric|min:0',
            'max_attempts' => 'required|integer|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
        ]);

        try {
            $quiz = $this->quizService->create($subjectId, $teacher->id, [
                'title_ar' => $validated['title_ar'],
                'title_en' => $validated['title_en'] ?? null,
                'description_ar' => $validated['description_ar'] ?? null,
                'description_en' => $validated['description_en'] ?? null,
                'type' => $validated['type'],
                'duration_minutes' => $validated['duration_minutes'] ?? null,
                'total_marks' => $validated['total_marks'],
                'pass_marks' => $validated['pass_marks'],
                'max_attempts' => $validated['max_attempts'],
                'shuffle_questions' => $request->boolean('shuffle_questions'),
                'shuffle_answers' => $request->boolean('shuffle_answers'),
                'show_results' => $request->boolean('show_results', true),
                'show_correct_answers' => $request->boolean('show_correct_answers'),
                'starts_at' => $validated['starts_at'] ?? null,
                'ends_at' => $validated['ends_at'] ?? null,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()->route('teacher.quizzes.show', [$subjectId, $quiz->id])
                ->with('success', 'تم إنشاء الاختبار بنجاح. يمكنك الآن إضافة الأسئلة.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'حدث خطأ أثناء إنشاء الاختبار: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified quiz with its questions
     */
    public function show($subjectId, $quizId)
    {
        $teacher = auth()->user();

        $subject = Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $quiz = $this->quizzes->findForSubject($subjectId, $quizId, ['questions.options'], ['attempts']);

        return view('teacher.quizzes.show', compact('subject', 'quiz'));
    }

    /**
     * Export the quiz as a printable PDF (question paper).
     * ?answers=1 includes the correct answers (answer key).
     */
    public function exportPdf(Request $request, $subjectId, $quizId)
    {
        $teacher = auth()->user();

        $subject = Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $quiz = $this->quizzes->findForSubject($subjectId, $quizId, [
            'questions' => fn($q) => $q->orderBy('order'),
            'questions.options',
        ]);

        $withAnswers = $request->boolean('answers');

        return $this->renderQuizPdf($subject, $quiz, $withAnswers);
    }

    /**
     * Export the question paper (questions only, no answer key) straight from the
     * quiz overview page, which is keyed on the quiz alone.
     */
    public function overviewPdf(Quiz $quiz)
    {
        abort_unless($quiz->created_by == auth()->id(), 403);

        $quiz->load(['questions' => fn($q) => $q->orderBy('order'), 'questions.options', 'subject']);

        return $this->renderQuizPdf($quiz->subject, $quiz, withAnswers: false);
    }

    /**
     * Build the Arabic-shaped PDF for a quiz. mPDF handles letter shaping + RTL
     * bidi, which DomPDF cannot.
     */
    private function renderQuizPdf(?Subject $subject, Quiz $quiz, bool $withAnswers)
    {
        $pdf = \Mccarlosen\LaravelMpdf\Facades\LaravelMpdf::loadView(
            'teacher.quizzes.pdf',
            compact('subject', 'quiz', 'withAnswers'),
            [],
            [
                'format'         => 'A4',
                'orientation'    => 'P',
                'mode'           => 'utf-8',
                'directionality' => 'rtl',
                'default_font'   => 'dejavusans',
                'autoScriptToLang' => true,
                'autoLangToFont'   => true,
            ]
        );

        $slug = 'quiz-' . $quiz->id . ($withAnswers ? '-answers' : '');

        return $pdf->download($slug . '.pdf');
    }

    /**
     * Show the form for editing the specified quiz
     */
    public function edit($subjectId, $quizId)
    {
        $teacher = auth()->user();

        $subject = Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $quiz = $this->quizzes->findForSubject($subjectId, $quizId);

        return view('teacher.quizzes.edit', compact('subject', 'quiz'));
    }

    /**
     * Update the specified quiz
     */
    public function update(Request $request, $subjectId, $quizId)
    {
        $teacher = auth()->user();

        Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $quiz = $this->quizzes->findForSubject($subjectId, $quizId);

        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'type' => 'required|in:quiz,midterm,exam,homework,paper',
            'duration_minutes' => 'nullable|integer|min:1',
            'total_marks' => 'required|numeric|min:1',
            'pass_marks' => 'required|numeric|min:0',
            'max_attempts' => 'required|integer|min:1',
            'shuffle_questions' => 'boolean',
            'shuffle_answers' => 'boolean',
            'show_results' => 'boolean',
            'show_correct_answers' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
        ]);

        $validated['shuffle_questions'] = $request->boolean('shuffle_questions');
        $validated['shuffle_answers'] = $request->boolean('shuffle_answers');
        $validated['show_results'] = $request->boolean('show_results', true);
        $validated['show_correct_answers'] = $request->boolean('show_correct_answers');
        $validated['is_active'] = $request->boolean('is_active', true);

        $this->quizService->update($quiz, $validated);

        return redirect()->route('teacher.quizzes.show', [$subjectId, $quiz->id])
            ->with('success', 'تم تحديث الاختبار بنجاح');
    }

    /**
     * Remove the specified quiz
     */
    public function destroy($subjectId, $quizId)
    {
        $teacher = auth()->user();

        Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $quiz = $this->quizzes->findForSubject($subjectId, $quizId);

        $this->quizService->delete($quiz);

        return redirect()->route('teacher.quizzes.index', $subjectId)
            ->with('success', 'تم حذف الاختبار بنجاح');
    }

    /**
     * Show form to add a question
     */
    public function createQuestion($subjectId, $quizId)
    {
        $teacher = auth()->user();

        $subject = Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $quiz = $this->quizzes->findForSubject($subjectId, $quizId);

        $nextOrder = $this->quizzes->nextQuestionOrder($quiz);

        return view('teacher.quizzes.questions.create', compact('subject', 'quiz', 'nextOrder'));
    }

    /**
     * Remove option rows whose Arabic text is blank, then re-index, so a
     * multiple-choice question submitted with only 2 of 4 boxes filled
     * validates against the remaining (non-empty) options.
     */
    private function pruneEmptyOptions(Request $request): void
    {
        // Options only apply to multiple-choice questions — drop them entirely
        // for other types so the array rules (min:2) don't fire on empty input.
        if ($request->input('type') !== 'multiple_choice') {
            $request->request->remove('options');
            return;
        }

        $options = $request->input('options');
        if (!is_array($options)) {
            return;
        }

        $cleaned = array_values(array_filter($options, fn($opt) =>
            is_array($opt) && trim((string) ($opt['text_ar'] ?? '')) !== ''
        ));

        $request->merge(['options' => $cleaned]);
    }

    /**
     * Store a new question
     */
    public function storeQuestion(Request $request, $subjectId, $quizId)
    {
        $teacher = auth()->user();

        Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $quiz = $this->quizzes->findForSubject($subjectId, $quizId);

        // Drop option rows with no Arabic text so half-filled choice lists validate.
        $this->pruneEmptyOptions($request);

        $validated = $request->validate([
            'type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'question_ar' => 'required|string',
            'question_en' => 'nullable|string',
            'explanation_ar' => 'nullable|string',
            'explanation_en' => 'nullable|string',
            'marks' => 'required|numeric|min:0',
            'order' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*.text_ar' => 'required_if:type,multiple_choice|string',
            'options.*.text_en' => 'nullable|string',
            'options.*.is_correct' => 'nullable|boolean',
            'correct_answer' => 'required_if:type,true_false|in:true,false',
        ]);

        try {
            $this->quizService->createQuestion($quiz, $validated, $request->file('image'));

            if ($request->has('add_another')) {
                return redirect()->route('teacher.quizzes.questions.create', [$subjectId, $quizId])
                    ->with('success', 'تم إضافة السؤال بنجاح. يمكنك إضافة سؤال آخر.');
            }

            return redirect()->route('teacher.quizzes.show', [$subjectId, $quizId])
                ->with('success', 'تم إضافة السؤال بنجاح');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'حدث خطأ أثناء إضافة السؤال: ' . $e->getMessage()]);
        }
    }

    /**
     * Show form to edit a question
     */
    public function editQuestion($subjectId, $quizId, $questionId)
    {
        $teacher = auth()->user();

        $subject = Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $quiz = $this->quizzes->findForSubject($subjectId, $quizId);

        $question = $this->quizzes->findQuestion($quizId, $questionId, ['options']);

        return view('teacher.quizzes.questions.edit', compact('subject', 'quiz', 'question'));
    }

    /**
     * Update a question
     */
    public function updateQuestion(Request $request, $subjectId, $quizId, $questionId)
    {
        $teacher = auth()->user();

        Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $this->quizzes->findForSubject($subjectId, $quizId);

        $question = $this->quizzes->findQuestion($quizId, $questionId);

        // Drop option rows with no Arabic text so half-filled choice lists validate.
        $this->pruneEmptyOptions($request);

        $validated = $request->validate([
            'type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'question_ar' => 'required|string',
            'question_en' => 'nullable|string',
            'explanation_ar' => 'nullable|string',
            'explanation_en' => 'nullable|string',
            'marks' => 'required|numeric|min:0',
            'order' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
            'remove_image' => 'nullable|boolean',
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*.text_ar' => 'required_if:type,multiple_choice|string',
            'options.*.text_en' => 'nullable|string',
            'options.*.is_correct' => 'nullable|boolean',
            'correct_answer' => 'required_if:type,true_false|in:true,false',
        ]);

        try {
            $this->quizService->updateQuestion(
                $question,
                $validated,
                $request->file('image'),
                $request->boolean('remove_image'),
            );

            return redirect()->route('teacher.quizzes.show', [$subjectId, $quizId])
                ->with('success', 'تم تحديث السؤال بنجاح');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'حدث خطأ أثناء تحديث السؤال: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a question
     */
    public function destroyQuestion($subjectId, $quizId, $questionId)
    {
        $teacher = auth()->user();

        Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $this->quizzes->findForSubject($subjectId, $quizId);

        $question = $this->quizzes->findQuestion($quizId, $questionId);

        $this->quizService->deleteQuestion($question);

        return back()->with('success', 'تم حذف السؤال بنجاح');
    }

    /**
     * Display quiz results/attempts
     */
    public function results($subjectId, $quizId)
    {
        $teacher = auth()->user();

        $subject = Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $quiz = $this->quizzes->findForSubject($subjectId, $quizId, [], ['questions']);

        $attempts = $this->quizzes->submittedAttempts($quizId);

        $stats = $this->quizzes->attemptStats($quizId);

        return view('teacher.quizzes.results', compact('subject', 'quiz', 'attempts', 'stats'));
    }

    /**
     * Review a specific attempt
     */
    public function reviewAttempt($subjectId, $quizId, $attemptId)
    {
        $teacher = auth()->user();

        $subject = Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $quiz = $this->quizzes->findForSubject($subjectId, $quizId);

        $attempt = $this->quizzes->findAttempt($quizId, $attemptId, [
            'student', 'answers.question.options', 'answers.selectedOption',
        ]);

        return view('teacher.quizzes.review', compact('subject', 'quiz', 'attempt'));
    }

    /**
     * Overview: all quizzes across all subjects this teacher owns.
     */
    public function overview(Request $request)
    {
        $teacher = auth()->user();

        $search = $request->get('search', '');
        $type   = $request->get('type', '');

        $quizzes = $this->quizzes->overviewForTeacher($teacher->id, $search, $type);
        $stats   = $this->quizzes->overviewStats($teacher->id);

        return view('teacher.quizzes.overview', compact('quizzes', 'stats', 'search', 'type'));
    }

    /**
     * Overview detail: one quiz — questions + all attempts + eligible students.
     */
    public function overviewShow(Quiz $quiz)
    {
        $teacher = auth()->user();
        abort_unless($quiz->created_by == $teacher->id, 403);

        $quiz->load(['subject:id,name_ar,name_en', 'creator:id,name', 'questions.options']);

        $attempts = $quiz->attempts()
            ->with('student:id,name,email')
            ->orderByDesc('submitted_at')
            ->orderByDesc('started_at')
            ->get();

        $attemptByStudent = $attempts->groupBy('student_id')->map(function ($group) {
            return $group->sortByDesc(fn($a) => $a->submitted_at ?? $a->started_at)->first();
        });

        $subject = $quiz->subject;
        $eligibleStudents = $subject
            ? $this->quizzes->eligibleStudentsFor($subject, $attemptByStudent)
            : collect();

        $stats = [
            'eligible'       => $eligibleStudents->count(),
            'not_attempted'  => $eligibleStudents->filter(fn($s) => !$s->attempt)->count(),
            'completed'      => $attempts->whereNotNull('submitted_at')->count(),
            'in_progress'    => $attempts->whereNull('submitted_at')->count(),
            'passed'         => $attempts->where('passed', true)->count(),
            'avg_percentage' => round((float) $attempts->whereNotNull('submitted_at')->avg('percentage'), 1),
        ];

        return view('teacher.quizzes.overview-show', compact('quiz', 'attempts', 'eligibleStudents', 'stats'));
    }

    /**
     * Overview attempt: one student's full solution.
     */
    public function overviewAttempt(Quiz $quiz, QuizAttempt $attempt)
    {
        $teacher = auth()->user();
        abort_unless($quiz->created_by == $teacher->id, 403);
        abort_unless($attempt->quiz_id === $quiz->id, 404);

        $quiz->load(['subject:id,name_ar,name_en']);
        $attempt->load(['student:id,name,email', 'answers.question.options', 'answers.selectedOption']);

        $questions         = $quiz->questions()->with('options')->orderBy('order')->get();
        $answersByQuestion = $attempt->answers->keyBy('question_id');

        return view('teacher.quizzes.overview-attempt', compact('quiz', 'attempt', 'questions', 'answersByQuestion'));
    }

    /**
     * Grade a manual answer (essay/short answer)
     */
    public function gradeAnswer(Request $request, $subjectId, $quizId, $attemptId, $answerId)
    {
        $teacher = auth()->user();

        Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $this->quizzes->findForSubject($subjectId, $quizId);

        $attempt = $this->quizzes->findAttempt($quizId, $attemptId);

        $answer = $attempt->answers()->findOrFail($answerId);

        $validated = $request->validate([
            'marks' => 'required|numeric|min:0|max:' . $answer->question->marks,
            'feedback' => 'nullable|string',
        ]);

        $answer->manualGrade($validated['marks'], $validated['feedback']);

        return back()->with('success', 'تم تصحيح الإجابة بنجاح');
    }
}
