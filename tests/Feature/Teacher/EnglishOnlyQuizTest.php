<?php

namespace Tests\Feature\Teacher;

use App\Http\Controllers\Teacher\QuizController;
use App\Models\{Quiz, Question, QuestionOption, Program};
use App\Repositories\QuizRepository;
use App\Services\{QuizService, NotificationService};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema, Validator};
use Tests\TestCase;

class EnglishOnlyQuizTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'quiz_lang_test', 'database.connections.quiz_lang_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('quiz_lang_test');
        foreach ([new Quiz, new Question, new QuestionOption] as $model) {
            Schema::create($model->getTable(), function (Blueprint $table) use ($model) {
                $table->id();
                foreach ($model->getFillable() as $column) {
                    $table->string($column)->nullable();
                }
                $table->timestamps();
            });
        }
    }

    private function questionRules(): array
    {
        $method = new \ReflectionMethod(QuizController::class, 'questionRules');

        return $method->invoke(app(QuizController::class));
    }

    public function test_question_text_is_required_in_either_language_only(): void
    {
        $base = ['type' => 'multiple_choice', 'marks' => 1, 'order' => 1];

        $english = Validator::make($base + [
            'question_en' => 'What is 2 + 2?',
            'options' => [['text_en' => '4', 'is_correct' => 1], ['text_en' => '5']],
        ], $this->questionRules());
        $this->assertFalse($english->fails(), json_encode($english->errors()->toArray()));

        $arabic = Validator::make($base + [
            'question_ar' => 'كم ناتج ٢ + ٢؟',
            'options' => [['text_ar' => '٤', 'is_correct' => 1], ['text_ar' => '٥']],
        ], $this->questionRules());
        $this->assertFalse($arabic->fails());

        $empty = Validator::make($base + [
            'options' => [['text_en' => '4', 'is_correct' => 1], []],
        ], $this->questionRules());
        $this->assertTrue($empty->fails());
        $this->assertArrayHasKey('question_ar', $empty->errors()->toArray());
        $this->assertArrayHasKey('options.1.text_ar', $empty->errors()->toArray());
    }

    public function test_english_only_question_is_stored_in_english_and_displayed_everywhere(): void
    {
        $quiz = Quiz::create(['created_by' => 7, 'title_en' => 'Unit 1 Quiz']);
        $service = new QuizService(\Mockery::mock(QuizRepository::class), \Mockery::mock(NotificationService::class));

        $question = $service->createQuestion($quiz, [
            'type' => 'multiple_choice', 'question_en' => 'What is 2 + 2?', 'marks' => 1, 'order' => 1,
            'options' => [['text_en' => '4', 'is_correct' => 1], ['text_en' => '5']],
        ])->fresh('options');

        $this->assertNull($quiz->fresh()->getRawOriginal('title_ar'));
        $this->assertSame('Unit 1 Quiz', $quiz->fresh()->title_ar);
        $this->assertNull($question->getRawOriginal('question_ar'));
        $this->assertSame('What is 2 + 2?', $question->question_en);
        $this->assertSame('What is 2 + 2?', $question->question_ar);
        $this->assertNull($question->options[0]->getRawOriginal('option_ar'));
        $this->assertSame('4', $question->options[0]->option_ar);
    }

    public function test_duplicate_keeps_arabic_columns_empty_for_english_quiz(): void
    {
        $program = new Program(['id' => 5]);
        $program->id = 5;
        $program->setRelation('targetClasses', collect([['id' => 20]]));
        $repository = \Mockery::mock(QuizRepository::class)->makePartial();
        $repository->shouldReceive('programsForTeacher')->with(7)
            ->andReturn(new \Illuminate\Database\Eloquent\Collection([$program]));
        $notifications = \Mockery::mock(NotificationService::class);
        $notifications->shouldReceive('notifyQuizCreated')->once();

        $source = Quiz::create(['created_by' => 7, 'program_id' => 5, 'class_id' => 10, 'title_en' => 'Quiz']);
        $question = $source->questions()->create(['question_en' => 'Question?', 'type' => 'multiple_choice', 'marks' => 1, 'order' => 1]);
        $question->options()->create(['option_en' => 'Answer', 'is_correct' => true, 'order' => 1]);

        $copy = (new QuizService($repository, $notifications))->duplicateForClass($source, 'program:5', 20, 7, []);

        $copiedQuestion = $copy->questions()->with('options')->first();
        $this->assertNull($copy->fresh()->getRawOriginal('title_ar'));
        $this->assertNull($copiedQuestion->getRawOriginal('question_ar'));
        $this->assertSame('Question?', $copiedQuestion->question_en);
        $this->assertNull($copiedQuestion->options->first()->getRawOriginal('option_ar'));
        $this->assertSame('Answer', $copiedQuestion->options->first()->option_en);
    }
}
