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
}
