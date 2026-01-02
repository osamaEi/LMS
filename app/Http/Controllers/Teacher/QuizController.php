<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuizAttempt;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuizController extends Controller
{
    /**
     * Display a listing of quizzes for a subject
     */
    public function index($subjectId)
    {
        $teacher = auth()->user();

        $subject = Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        $quizzes = Quiz::where('subject_id', $subjectId)
            ->withCount(['questions', 'attempts'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teacher.quizzes.index', compact('subject', 'quizzes'));
    }

    /**
     * Show the form for creating a new quiz
     */
    public function create($subjectId)
    {
        $teacher = auth()->user();

        $subject = Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        return view('teacher.quizzes.create', compact('subject'));
    }

    /**
     * Store a newly created quiz
     */
    public function store(Request $request, $subjectId)
    {
        $teacher = auth()->user();

        $subject = Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'type' => 'required|in:quiz,exam,homework',
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

        $validated['subject_id'] = $subjectId;
        $validated['created_by'] = $teacher->id;
        $validated['shuffle_questions'] = $request->boolean('shuffle_questions');
        $validated['shuffle_answers'] = $request->boolean('shuffle_answers');
        $validated['show_results'] = $request->boolean('show_results', true);
        $validated['show_correct_answers'] = $request->boolean('show_correct_answers');
        $validated['is_active'] = $request->boolean('is_active', true);

        $quiz = Quiz::create($validated);

        return redirect()->route('teacher.quizzes.show', [$subjectId, $quiz->id])
            ->with('success', 'تم إنشاء الاختبار بنجاح. يمكنك الآن إضافة الأسئلة.');
    }

    /**
     * Display the specified quiz with its questions
     */
    public function show($subjectId, $quizId)
    {
        $teacher = auth()->user();

        $subject = Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        $quiz = Quiz::where('subject_id', $subjectId)
            ->with(['questions.options'])
            ->withCount('attempts')
            ->findOrFail($quizId);

        return view('teacher.quizzes.show', compact('subject', 'quiz'));
    }

    /**
     * Show the form for editing the specified quiz
     */
    public function edit($subjectId, $quizId)
    {
        $teacher = auth()->user();

        $subject = Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        $quiz = Quiz::where('subject_id', $subjectId)->findOrFail($quizId);

        return view('teacher.quizzes.edit', compact('subject', 'quiz'));
    }

    /**
     * Update the specified quiz
     */
    public function update(Request $request, $subjectId, $quizId)
    {
        $teacher = auth()->user();

        Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        $quiz = Quiz::where('subject_id', $subjectId)->findOrFail($quizId);

        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'type' => 'required|in:quiz,exam,homework',
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

        $quiz->update($validated);

        return redirect()->route('teacher.quizzes.show', [$subjectId, $quiz->id])
            ->with('success', 'تم تحديث الاختبار بنجاح');
    }

    /**
     * Remove the specified quiz
     */
    public function destroy($subjectId, $quizId)
    {
        $teacher = auth()->user();

        Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        $quiz = Quiz::where('subject_id', $subjectId)->findOrFail($quizId);

        // Delete question images
        foreach ($quiz->questions as $question) {
            if ($question->image) {
                Storage::disk('public')->delete($question->image);
            }
        }

        $quiz->delete();

        return redirect()->route('teacher.quizzes.index', $subjectId)
            ->with('success', 'تم حذف الاختبار بنجاح');
    }

    /**
     * Show form to add a question
     */
    public function createQuestion($subjectId, $quizId)
    {
        $teacher = auth()->user();

        $subject = Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        $quiz = Quiz::where('subject_id', $subjectId)->findOrFail($quizId);

        $nextOrder = $quiz->questions()->max('order') + 1;

        return view('teacher.quizzes.questions.create', compact('subject', 'quiz', 'nextOrder'));
    }

    /**
     * Store a new question
     */
    public function storeQuestion(Request $request, $subjectId, $quizId)
    {
        $teacher = auth()->user();

        Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        $quiz = Quiz::where('subject_id', $subjectId)->findOrFail($quizId);

        $validated = $request->validate([
            'type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'question_ar' => 'required|string',
            'question_en' => 'nullable|string',
            'explanation_ar' => 'nullable|string',
            'explanation_en' => 'nullable|string',
            'marks' => 'required|numeric|min:0.1',
            'order' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*.text_ar' => 'required_if:type,multiple_choice|string',
            'options.*.text_en' => 'nullable|string',
            'options.*.is_correct' => 'nullable|boolean',
            'correct_answer' => 'required_if:type,true_false|in:true,false',
        ]);

        DB::beginTransaction();

        try {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('question-images', 'public');
            }

            $question = Question::create([
                'quiz_id' => $quiz->id,
                'type' => $validated['type'],
                'question_ar' => $validated['question_ar'],
                'question_en' => $validated['question_en'] ?? null,
                'explanation_ar' => $validated['explanation_ar'] ?? null,
                'explanation_en' => $validated['explanation_en'] ?? null,
                'marks' => $validated['marks'],
                'order' => $validated['order'],
                'image' => $imagePath,
            ]);

            // Create options based on question type
            if ($validated['type'] === 'multiple_choice' && isset($validated['options'])) {
                foreach ($validated['options'] as $index => $option) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_ar' => $option['text_ar'],
                        'option_en' => $option['text_en'] ?? null,
                        'is_correct' => isset($option['is_correct']) && $option['is_correct'],
                        'order' => $index + 1,
                    ]);
                }
            } elseif ($validated['type'] === 'true_false') {
                // Create True option
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_ar' => 'صح',
                    'option_en' => 'True',
                    'is_correct' => $validated['correct_answer'] === 'true',
                    'order' => 1,
                ]);
                // Create False option
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_ar' => 'خطأ',
                    'option_en' => 'False',
                    'is_correct' => $validated['correct_answer'] === 'false',
                    'order' => 2,
                ]);
            }

            DB::commit();

            if ($request->has('add_another')) {
                return redirect()->route('teacher.quizzes.questions.create', [$subjectId, $quizId])
                    ->with('success', 'تم إضافة السؤال بنجاح. يمكنك إضافة سؤال آخر.');
            }

            return redirect()->route('teacher.quizzes.show', [$subjectId, $quizId])
                ->with('success', 'تم إضافة السؤال بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();

            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            return back()->withInput()->withErrors(['error' => 'حدث خطأ أثناء إضافة السؤال: ' . $e->getMessage()]);
        }
    }

    /**
     * Show form to edit a question
     */
    public function editQuestion($subjectId, $quizId, $questionId)
    {
        $teacher = auth()->user();

        $subject = Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        $quiz = Quiz::where('subject_id', $subjectId)->findOrFail($quizId);

        $question = Question::where('quiz_id', $quizId)
            ->with('options')
            ->findOrFail($questionId);

        return view('teacher.quizzes.questions.edit', compact('subject', 'quiz', 'question'));
    }

    /**
     * Update a question
     */
    public function updateQuestion(Request $request, $subjectId, $quizId, $questionId)
    {
        $teacher = auth()->user();

        Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        Quiz::where('subject_id', $subjectId)->findOrFail($quizId);

        $question = Question::where('quiz_id', $quizId)->findOrFail($questionId);

        $validated = $request->validate([
            'type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'question_ar' => 'required|string',
            'question_en' => 'nullable|string',
            'explanation_ar' => 'nullable|string',
            'explanation_en' => 'nullable|string',
            'marks' => 'required|numeric|min:0.1',
            'order' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048',
            'remove_image' => 'nullable|boolean',
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*.text_ar' => 'required_if:type,multiple_choice|string',
            'options.*.text_en' => 'nullable|string',
            'options.*.is_correct' => 'nullable|boolean',
            'correct_answer' => 'required_if:type,true_false|in:true,false',
        ]);

        DB::beginTransaction();

        try {
            // Handle image
            $imagePath = $question->image;

            if ($request->boolean('remove_image') && $question->image) {
                Storage::disk('public')->delete($question->image);
                $imagePath = null;
            }

            if ($request->hasFile('image')) {
                if ($question->image) {
                    Storage::disk('public')->delete($question->image);
                }
                $imagePath = $request->file('image')->store('question-images', 'public');
            }

            $question->update([
                'type' => $validated['type'],
                'question_ar' => $validated['question_ar'],
                'question_en' => $validated['question_en'] ?? null,
                'explanation_ar' => $validated['explanation_ar'] ?? null,
                'explanation_en' => $validated['explanation_en'] ?? null,
                'marks' => $validated['marks'],
                'order' => $validated['order'],
                'image' => $imagePath,
            ]);

            // Delete existing options
            $question->options()->delete();

            // Create new options based on question type
            if ($validated['type'] === 'multiple_choice' && isset($validated['options'])) {
                foreach ($validated['options'] as $index => $option) {
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_ar' => $option['text_ar'],
                        'option_en' => $option['text_en'] ?? null,
                        'is_correct' => isset($option['is_correct']) && $option['is_correct'],
                        'order' => $index + 1,
                    ]);
                }
            } elseif ($validated['type'] === 'true_false') {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_ar' => 'صح',
                    'option_en' => 'True',
                    'is_correct' => $validated['correct_answer'] === 'true',
                    'order' => 1,
                ]);
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_ar' => 'خطأ',
                    'option_en' => 'False',
                    'is_correct' => $validated['correct_answer'] === 'false',
                    'order' => 2,
                ]);
            }

            DB::commit();

            return redirect()->route('teacher.quizzes.show', [$subjectId, $quizId])
                ->with('success', 'تم تحديث السؤال بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'حدث خطأ أثناء تحديث السؤال: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a question
     */
    public function destroyQuestion($subjectId, $quizId, $questionId)
    {
        $teacher = auth()->user();

        Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        Quiz::where('subject_id', $subjectId)->findOrFail($quizId);

        $question = Question::where('quiz_id', $quizId)->findOrFail($questionId);

        if ($question->image) {
            Storage::disk('public')->delete($question->image);
        }

        $question->delete();

        return back()->with('success', 'تم حذف السؤال بنجاح');
    }

    /**
     * Display quiz results/attempts
     */
    public function results($subjectId, $quizId)
    {
        $teacher = auth()->user();

        $subject = Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        $quiz = Quiz::where('subject_id', $subjectId)
            ->withCount('questions')
            ->findOrFail($quizId);

        $attempts = QuizAttempt::where('quiz_id', $quizId)
            ->whereNotNull('submitted_at')
            ->with('student')
            ->orderBy('submitted_at', 'desc')
            ->paginate(20);

        // Calculate statistics
        $stats = [
            'total_attempts' => $attempts->total(),
            'passed' => QuizAttempt::where('quiz_id', $quizId)->whereNotNull('submitted_at')->where('passed', true)->count(),
            'failed' => QuizAttempt::where('quiz_id', $quizId)->whereNotNull('submitted_at')->where('passed', false)->count(),
            'average_score' => QuizAttempt::where('quiz_id', $quizId)->whereNotNull('submitted_at')->avg('percentage') ?? 0,
            'highest_score' => QuizAttempt::where('quiz_id', $quizId)->whereNotNull('submitted_at')->max('percentage') ?? 0,
            'lowest_score' => QuizAttempt::where('quiz_id', $quizId)->whereNotNull('submitted_at')->min('percentage') ?? 0,
        ];

        return view('teacher.quizzes.results', compact('subject', 'quiz', 'attempts', 'stats'));
    }

    /**
     * Review a specific attempt
     */
    public function reviewAttempt($subjectId, $quizId, $attemptId)
    {
        $teacher = auth()->user();

        $subject = Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        $quiz = Quiz::where('subject_id', $subjectId)->findOrFail($quizId);

        $attempt = QuizAttempt::where('quiz_id', $quizId)
            ->with(['student', 'answers.question.options', 'answers.selectedOption'])
            ->findOrFail($attemptId);

        return view('teacher.quizzes.review', compact('subject', 'quiz', 'attempt'));
    }

    /**
     * Grade a manual answer (essay/short answer)
     */
    public function gradeAnswer(Request $request, $subjectId, $quizId, $attemptId, $answerId)
    {
        $teacher = auth()->user();

        Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        Quiz::where('subject_id', $subjectId)->findOrFail($quizId);

        $attempt = QuizAttempt::where('quiz_id', $quizId)->findOrFail($attemptId);

        $answer = $attempt->answers()->findOrFail($answerId);

        $validated = $request->validate([
            'marks' => 'required|numeric|min:0|max:' . $answer->question->marks,
            'feedback' => 'nullable|string',
        ]);

        $answer->manualGrade($validated['marks'], $validated['feedback']);

        return back()->with('success', 'تم تصحيح الإجابة بنجاح');
    }
}
