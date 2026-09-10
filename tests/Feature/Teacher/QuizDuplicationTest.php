<?php

namespace Tests\Feature\Teacher;

use App\Models\{Quiz, Question, QuestionOption, Program};
use App\Repositories\QuizRepository;
use App\Services\{QuizService, NotificationService};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema, Storage};
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;
use App\Models\User;
use Tests\TestCase;

class QuizDuplicationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'quiz_copy_test', 'database.connections.quiz_copy_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('quiz_copy_test');
        foreach ([new Quiz, new Question, new QuestionOption] as $model) {
            Schema::create($model->getTable(), function (Blueprint $table) use ($model) {
                $table->id();
                foreach ($model->getFillable() as $column) {
                    $table->string($column)->nullable();
                }
                $table->timestamps();
            });
        }
        Storage::fake('public');
    }

    private function service(bool $allowed = true): QuizService
    {
        $program = new Program(['id' => 5]);
        $program->id = 5;
        $program->setRelation('targetClasses', collect([['id' => 20]]));
        $repository = \Mockery::mock(QuizRepository::class)->makePartial();
        $repository->shouldReceive('programsForTeacher')->with(7)
            ->andReturn(new \Illuminate\Database\Eloquent\Collection($allowed ? [$program] : []));
        $notifications = \Mockery::mock(NotificationService::class);
        $notifications->shouldReceive('notifyQuizCreated')->times($allowed ? 1 : 0);

        return new QuizService($repository, $notifications);
    }

    public function test_copy_preserves_content_and_has_independent_questions_and_images(): void
    {
        $source = Quiz::create(['created_by' => 7, 'program_id' => 5, 'class_id' => 10,
            'title_ar' => 'اختبار', 'total_marks' => 12, 'shuffle_answers' => true,
            'ends_at' => '2020-01-01', 'is_active' => false]);
        Storage::disk('public')->put('uploads/images/source.png', 'image-content');
        $question = $source->questions()->create(['question_ar' => 'سؤال', 'type' => 'multiple_choice',
            'marks' => 12, 'order' => 1, 'image' => 'uploads/images/source.png']);
        $question->options()->create(['option_ar' => 'إجابة', 'is_correct' => true, 'order' => 1]);

        $copy = $this->service()->duplicateForClass($source, 'program:5', 20, 7, []);

        $this->assertNotEquals($source->id, $copy->id);
        $this->assertEquals(20, $copy->class_id);
        $this->assertSame($source->title_ar, $copy->title_ar);
        $this->assertSame($source->total_marks, $copy->total_marks);
        $this->assertTrue($copy->shuffle_answers);
        $this->assertTrue($copy->is_active);
        $this->assertNull($copy->ends_at);
        $copiedQuestion = $copy->questions()->with('options')->first();
        $this->assertNotEquals($question->id, $copiedQuestion->id);
        $this->assertSame($question->question_ar, $copiedQuestion->question_ar);
        $this->assertTrue($copiedQuestion->options->first()->is_correct);
        $this->assertNotSame($question->image, $copiedQuestion->image);
        Storage::disk('public')->delete($copiedQuestion->image);
        Storage::disk('public')->assertExists($question->image);
        $this->assertEquals(10, $source->fresh()->class_id);
        $this->assertFalse($source->fresh()->is_active);
    }

    public function test_unauthorized_destination_rolls_back_copy(): void
    {
        $source = Quiz::create(['created_by' => 7, 'class_id' => 10]);
        try {
            $this->service(false)->duplicateForClass($source, 'program:5', 20, 7, []);
            $this->fail('Expected destination validation failure.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('target', $e->errors());
            $this->assertSame(1, Quiz::count());
        }
    }

    public function test_copy_uses_custom_settings_without_changing_original(): void
    {
        $source = Quiz::create(['created_by' => 7, 'program_id' => 5, 'class_id' => 10,
            'type' => 'quiz', 'total_marks' => 20, 'duration_minutes' => 30]);

        $copy = $this->service()->duplicateForClass($source, 'program:5', 20, 7, [
            'type' => 'exam', 'total_marks' => 50, 'duration_minutes' => 60,
        ]);
        $copy->refresh();
        $this->assertSame('exam', $copy->type);
        $this->assertEquals(50, $copy->total_marks);
        $this->assertEquals(60, $copy->duration_minutes);
        $this->assertSame('quiz', $source->fresh()->type);
        $this->assertEquals(20, $source->fresh()->total_marks);
        $this->assertEquals(30, $source->fresh()->duration_minutes);
    }

    public function test_copy_can_remove_time_limit(): void
    {
        $source = Quiz::create(['created_by' => 7, 'program_id' => 5, 'class_id' => 10,
            'duration_minutes' => 30]);
        $copy = $this->service()->duplicateForClass($source, 'program:5', 20, 7, [
            'duration_minutes' => null,
        ]);
        $this->assertNull($copy->fresh()->duration_minutes);
        $this->assertEquals(30, $source->fresh()->duration_minutes);
    }

    public function test_new_quiz_saves_edited_settings_and_questions_without_modifying_source(): void
    {
        $source = Quiz::create(['created_by' => 7, 'program_id' => 5, 'class_id' => 10,
            'title_ar' => 'Original', 'pass_marks' => 5, 'max_attempts' => 1]);
        $question = $source->questions()->create(['question_ar' => 'Original question',
            'type' => 'multiple_choice', 'marks' => 10, 'order' => 1]);
        $question->options()->create(['option_ar' => 'Original answer', 'is_correct' => true, 'order' => 1]);
        $removed = $source->questions()->create(['question_ar' => 'Excluded question',
            'type' => 'essay', 'marks' => 5, 'order' => 2]);

        $copy = $this->service()->duplicateForClass($source, 'program:5', 20, 7, [
            'title_ar' => 'New quiz', 'description_ar' => 'New description', 'pass_marks' => 8,
            'max_attempts' => 3, 'show_correct_answers' => false, 'shuffle_questions' => true,
            'questions' => [
                ['source_id' => $question->id, 'type' => 'multiple_choice', 'question_ar' => 'Edited question',
                    'marks' => 12, 'options' => [
                        ['option_ar' => 'Edited answer', 'is_correct' => true],
                        ['option_ar' => 'Wrong answer', 'is_correct' => false],
                    ]],
                ['type' => 'essay', 'question_ar' => 'New question', 'marks' => 3],
            ],
        ]);

        $this->assertSame('New quiz', $copy->title_ar);
        $this->assertEquals(8, $copy->pass_marks);
        $this->assertEquals(3, $copy->max_attempts);
        $this->assertTrue($copy->shuffle_questions);
        $this->assertFalse($copy->show_correct_answers);
        $questions = $copy->questions()->with('options')->orderBy('order')->get();
        $this->assertSame(['Edited question', 'New question'], $questions->pluck('question_ar')->all());
        $this->assertSame('Edited answer', $questions[0]->options[0]->option_ar);
        $this->assertEquals(12, $questions[0]->marks);
        $this->assertSame('Original', $source->fresh()->title_ar);
        $this->assertSame('Original question', $question->fresh()->question_ar);
        $this->assertSame('Original answer', $question->options()->first()->option_ar);
        $this->assertNotNull($removed->fresh());
    }

    public function test_question_from_another_quiz_cannot_be_copied(): void
    {
        $source = Quiz::create(['created_by' => 7, 'class_id' => 10]);
        $other = Quiz::create(['created_by' => 99]);
        $question = $other->questions()->create(['question_ar' => 'Private', 'type' => 'essay']);
        $repository = \Mockery::mock(QuizRepository::class)->makePartial();
        $program = new Program;
        $program->id = 5;
        $program->setRelation('targetClasses', collect([['id' => 20]]));
        $repository->shouldReceive('programsForTeacher')->with(7)
            ->andReturn(new \Illuminate\Database\Eloquent\Collection([$program]));
        $notifications = \Mockery::mock(NotificationService::class);
        $notifications->shouldNotReceive('notifyQuizCreated');

        try {
            (new QuizService($repository, $notifications))->duplicateForClass($source, 'program:5', 20, 7, [
                'questions' => [['source_id' => $question->id, 'question_ar' => 'Edited', 'type' => 'essay', 'marks' => 1]],
            ]);
            $this->fail('Expected question ownership validation.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('questions', $e->errors());
            $this->assertSame(2, Quiz::count());
            $this->assertSame(1, Question::count());
        }
    }

    public function test_teacher_can_save_a_new_image_question_from_the_duplicate_form(): void
    {
        $source = Quiz::create(['created_by' => 7, 'program_id' => 5, 'class_id' => 10, 'title_ar' => 'Original']);
        $this->app->instance(QuizService::class, $this->service());
        $teacher = User::factory()->make(['id' => 7, 'role' => 'teacher', 'status' => 'active']);
        $this->actingAs($teacher)->post(route('teacher.quizzes.duplicate.store', $source), [
            'destination' => '20:program:5', 'title_ar' => 'Image quiz', 'type' => 'quiz',
            'total_marks' => 10, 'pass_marks' => 5, 'max_attempts' => 1,
            'shuffle_questions' => 0, 'shuffle_answers' => 0, 'show_results' => 1,
            'show_correct_answers' => 0, 'is_active' => 1,
            'questions' => [['type' => 'essay', 'marks' => 10, 'question_ar' => '',
                'image' => UploadedFile::fake()->image('question.png')]],
        ])->assertSessionHasNoErrors()->assertRedirect(route('teacher.quizzes.overview'));

        $copy = Quiz::where('id', '!=', $source->id)->firstOrFail();
        $question = $copy->questions()->firstOrFail();
        $this->assertSame('سؤال بالصورة', $question->question_ar);
        Storage::disk('public')->assertExists($question->image);
        $this->assertSame(0, $source->questions()->count());
    }

    public function test_duplicate_can_replace_and_remove_images_without_changing_originals(): void
    {
        $source = Quiz::create(['created_by' => 7, 'program_id' => 5, 'class_id' => 10]);
        Storage::disk('public')->put('uploads/images/original.png', 'original');
        $question = $source->questions()->create(['type' => 'essay', 'question_ar' => 'Original',
            'marks' => 1, 'order' => 1, 'image' => 'uploads/images/original.png']);
        $copy = $this->service()->duplicateForClass($source, 'program:5', 20, 7, ['questions' => [
            ['source_id' => $question->id, 'type' => 'essay', 'question_ar' => 'Replacement', 'marks' => 1,
                'image' => UploadedFile::fake()->image('replacement.png')],
            ['source_id' => $question->id, 'type' => 'essay', 'question_ar' => 'Text only', 'marks' => 1, 'remove_image' => 1],
        ]]);
        $questions = $copy->questions()->orderBy('order')->get();
        Storage::disk('public')->assertExists($questions[0]->image);
        $this->assertNotSame($question->image, $questions[0]->image);
        $this->assertNull($questions[1]->image);
        Storage::disk('public')->assertExists($question->fresh()->image);
    }

    public function test_new_quiz_question_stores_an_uploaded_image(): void
    {
        $quiz = Quiz::create(['created_by' => 7]);
        $service = new QuizService(new QuizRepository, \Mockery::mock(NotificationService::class));
        $question = $service->createQuestion($quiz, [
            'type' => 'essay', 'question_ar' => 'سؤال بالصورة', 'marks' => 5, 'order' => 1,
        ], UploadedFile::fake()->image('new-question.jpg'));
        Storage::disk('public')->assertExists($question->image);
        $this->assertSame($quiz->id, $question->quiz_id);
    }

    public function test_duplicate_rejects_non_image_uploads_and_empty_questions(): void
    {
        $source = Quiz::create(['created_by' => 7, 'program_id' => 5, 'class_id' => 10]);
        $service = \Mockery::mock(QuizService::class);
        $service->shouldNotReceive('duplicateForClass');
        $this->app->instance(QuizService::class, $service);
        $teacher = User::factory()->make(['id' => 7, 'role' => 'teacher', 'status' => 'active']);
        $data = [
            'destination' => '20:program:5', 'title_ar' => 'Image quiz', 'type' => 'quiz',
            'total_marks' => 10, 'pass_marks' => 5, 'max_attempts' => 1,
            'shuffle_questions' => 0, 'shuffle_answers' => 0, 'show_results' => 1,
            'show_correct_answers' => 0, 'is_active' => 1,
            'questions' => [['type' => 'essay', 'marks' => 10, 'question_ar' => '',
                'image' => UploadedFile::fake()->create('document.pdf', 10, 'application/pdf')]],
        ];
        $this->actingAs($teacher)->post(route('teacher.quizzes.duplicate.store', $source), $data)
            ->assertSessionHasErrors('questions.0.image');
        unset($data['questions'][0]['image']);
        $this->post(route('teacher.quizzes.duplicate.store', $source), $data)
            ->assertSessionHasErrors('questions.0.question_ar');
        $this->assertSame(1, Quiz::count());
    }

    public function test_duplicate_editor_renders_existing_image_previews(): void
    {
        $quiz = Quiz::create(['created_by' => 7]);
        $quiz->questions()->create(['type' => 'essay', 'question_ar' => 'Image question',
            'image' => 'uploads/images/question.png', 'marks' => 1]);
        $quiz->load('questions.options');
        $html = view('teacher.quizzes.partials.duplicate-questions', compact('quiz'))->render();
        $this->assertStringContainsString('question.png', $html);
        $this->assertStringContainsString('type="file"', $html);
        $this->assertStringContainsString('معاينة صورة السؤال', $html);
    }
}
