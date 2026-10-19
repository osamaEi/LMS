<?php

namespace Tests\Feature\Teacher;

use App\Models\{Program, Quiz, QuizAttempt, Subject, User};
use App\Repositories\QuizRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Blade, DB, Schema};
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class QuizResultVisibilityTest extends TestCase
{
    public static function settings(): array
    {
        return [
            ['subject', null, false], ['subject', '0', false], ['subject', '1', true],
            ['program', null, false], ['program', '0', false], ['program', '1', true],
        ];
    }

    #[DataProvider('settings')]
    public function test_edit_persists_result_visibility(string $scope, ?string $value, bool $expected): void
    {
        config(['database.default' => 'visibility_test', 'database.connections.visibility_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('visibility_test');
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            foreach ((new Quiz)->getFillable() as $column) $table->string($column)->nullable();
            $table->timestamps();
        });
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->integer('teacher_id');
            $table->softDeletes();
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
        });
        Schema::create('subject_teacher', function (Blueprint $table) {
            $table->integer('subject_id');
            $table->integer('teacher_id');
        });
        DB::table('subjects')->insert(['id' => 224, 'teacher_id' => 1]);
        $teacher = new User(['role' => 'teacher', 'status' => 'active']);
        $teacher->id = 1;
        $this->actingAs($teacher);
        $program = new Program;
        $program->id = 224;
        $this->partialMock(QuizRepository::class)->shouldReceive('programsForTeacher')
            ->andReturn(new \Illuminate\Database\Eloquent\Collection([$program]));
        $quiz = Quiz::create([$scope.'_id' => 224, 'show_results' => !$expected]);
        $payload = ['title_ar' => 'Test', 'type' => 'quiz', 'total_marks' => 10,
            'pass_marks' => 5, 'max_attempts' => 1];
        if ($value !== null) $payload['show_results'] = $value;
        $route = $scope === 'subject' ? 'teacher.quizzes.update' : 'teacher.quizzes.program.update';

        $this->put(route($route, [224, $quiz->id]), $payload)
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame($expected, $quiz->fresh()->show_results);
    }

    public function test_released_result_stays_hidden_on_both_student_pages_when_disabled(): void
    {
        $quiz = new Quiz(['show_results' => false, 'title_ar' => 'Quiz']);
        $quiz->id = 50;
        $attempt = new QuizAttempt(['results_released_at' => now(), 'score' => 9, 'percentage' => 90]);
        $attempt->setRelation('quiz', $quiz)->setRelation('answers', collect());
        $this->assertTrue($attempt->isReleased());
        $this->assertFalse($attempt->canShowResults());
        foreach (['student/quizzes/result.blade.php', 'student/quizzes/program/result.blade.php'] as $path) {
            $source = file_get_contents(resource_path('views/'.$path));
            $source = preg_replace('/^@(?:extends|section)\([^\r\n]*\)\s*$/m', '', $source);
            $source = str_replace('@endsection', '', $source);
            $html = Blade::render($source, [
                'quiz' => $quiz, 'attempt' => $attempt, 'showResults' => false, 'showCorrectAnswers' => true,
                'subject' => (object) ['id' => 224, 'name' => 'Subject'],
                'program' => (object) ['id' => 224, 'name' => 'Program'],
            ]);
            $this->assertStringContainsString('تم تسليم إجابتك', $html);
            $this->assertStringNotContainsString('مراجعة إجاباتي', $html);
        }
    }
}
