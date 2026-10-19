<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\V1\Student\QuizController;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StudentQuizReleaseTest extends TestCase
{
    public function test_best_score_only_includes_released_submitted_attempts_for_this_student(): void
    {
        config(['database.default' => 'release_test', 'database.connections.release_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('release_test');
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->integer('quiz_id');
            $table->integer('student_id');
            $table->integer('score');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('results_released_at')->nullable();
        });
        $quiz = new Quiz(['show_results' => true]);
        $quiz->id = 38;
        DB::table('quiz_attempts')->insert([
            'quiz_id' => 38, 'student_id' => 303, 'score' => 10,
            'submitted_at' => now(), 'results_released_at' => null,
        ]);
        $this->assertNull($quiz->bestScoreForStudent(303));
        DB::table('quiz_attempts')->insert([
            'quiz_id' => 38, 'student_id' => 303, 'score' => 5,
            'submitted_at' => now(), 'results_released_at' => now(),
        ]);
        $this->assertEquals(5, $quiz->bestScoreForStudent(303));
        $this->assertNull($quiz->bestScoreForStudent(304));
        DB::table('quiz_attempts')->where('score', 10)->update(['results_released_at' => now()]);
        $this->assertEquals(10, $quiz->bestScoreForStudent(303));
    }

    public function test_api_hides_unreleased_grades_and_loaded_answers(): void
    {
        $attempt = new QuizAttempt(['score' => 10, 'percentage' => 100, 'passed' => true]);
        $attempt->setRelation('quiz', new Quiz(['show_results' => true]));
        $attempt->setRelation('answers', collect([['marks_obtained' => 10, 'is_correct' => true]]));
        $serialize = new \ReflectionMethod(QuizController::class, 'studentAttemptData');
        $data = $serialize->invoke(new QuizController, $attempt);
        $this->assertNull($data['score']);
        $this->assertNull($data['percentage']);
        $this->assertNull($data['passed']);
        $this->assertArrayNotHasKey('answers', $data);

        $attempt->results_released_at = now();
        $data = $serialize->invoke(new QuizController, $attempt);
        $this->assertEquals(10, $data['score']);
        $this->assertEquals(100, $data['percentage']);
        $this->assertTrue($data['passed']);

        $attempt->quiz->show_results = false;
        $data = $serialize->invoke(new QuizController, $attempt);
        $this->assertNull($data['score']);
        $this->assertNull($data['percentage']);
        $this->assertNull($data['passed']);
    }
}
