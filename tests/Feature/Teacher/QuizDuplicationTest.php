<?php

namespace Tests\Feature\Teacher;

use App\Models\{Quiz, Question, QuestionOption, Program};
use App\Repositories\QuizRepository;
use App\Services\{QuizService, NotificationService};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema, Storage};
use Illuminate\Validation\ValidationException;
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
}
