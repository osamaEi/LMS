<?php

namespace Tests\Feature\Student;

use App\Models\User;
use App\Services\StudentGradesService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GradeBreakdownTest extends TestCase
{
    public function test_latest_released_breakdown_appears_even_without_attempts(): void
    {
        config(['database.default' => 'breakdown_test', 'database.connections.breakdown_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('breakdown_test');
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->integer('teacher_id')->nullable();
            $table->softDeletes();
        });
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->integer('subject_id');
            $table->string('status');
            $table->timestamp('graded_at')->nullable();
            $table->softDeletes();
        });
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('results_released_at')->nullable();
        });
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->integer('subject_id');
            $table->decimal('final_grade')->nullable();
            $table->softDeletes();
        });
        DB::table('subjects')->insert(['id' => 7, 'name_ar' => 'Math']);
        // An unreleased attempt must never enter the student's grade summary.
        DB::table('quiz_attempts')->insert(['student_id' => 10, 'submitted_at' => now()]);
        $student = new User;
        $student->id = 10;
        $latest = ['subject_id' => 7, 'name' => 'Math', 'attendance' => 0,
            'participation' => 25, 'midterm' => 18, 'final' => 38, 'total' => 81];
        $service = $this->partialMock(StudentGradesService::class);
        $service->shouldReceive('sentGradeReports')->once()->with($student)->andReturn(collect([
            (object) ['rows' => collect([$latest])],
            (object) ['rows' => collect([array_replace($latest, ['total' => 50])])],
        ]));

        $overview = $service->overview($student);

        $this->assertSame(0, $overview['totalQuizzes']);
        $this->assertSame($latest, $overview['subjectGrades'][7]['breakdown']);
        $this->assertSame(81.0, $overview['subjectGrades'][7]['percentage']);
    }
}
