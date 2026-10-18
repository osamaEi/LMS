<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\StudentQuizClassAuditSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StudentQuizClassAuditTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'quiz_audit_test', 'database.connections.quiz_audit_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('quiz_audit_test');

        foreach (['users', 'programs', 'program_classes', 'subjects', 'terms', 'enrollments'] as $name) {
            Schema::create($name, function (Blueprint $table) use ($name) {
                $table->id();
                $table->softDeletes();
                if ($name !== 'programs') {
                    $table->integer('program_id')->nullable();
                    $table->integer('class_id')->nullable();
                }
                if ($name === 'users') {
                    $table->string('role')->default('student');
                    $table->string('status')->default('active');
                }
                if ($name === 'subjects') {
                    $table->integer('term_id')->nullable();
                }
                if ($name === 'enrollments') {
                    $table->integer('student_id');
                    $table->integer('subject_id');
                    $table->string('status')->default('active');
                }
            });
        }
        Schema::create('student_programs', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->integer('program_id');
            $table->integer('class_id')->nullable();
            $table->string('status')->default('approved');
            $table->integer('current_term_number')->default(1);
            $table->date('enrolled_at')->nullable();
            $table->timestamps();
        });
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->integer('subject_id')->nullable();
            $table->integer('program_id')->nullable();
            $table->integer('class_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('ends_at')->nullable();
        });

        DB::table('users')->insert(['id' => 303, 'program_id' => 1]);
        DB::table('programs')->insert([['id' => 1], ['id' => 2]]);
        DB::table('program_classes')->insert([
            ['id' => 13, 'program_id' => 1],
            ['id' => 31, 'program_id' => 1],
            ['id' => 99, 'program_id' => 2],
        ]);
        DB::table('subjects')->insert(['id' => 212, 'program_id' => 1]);
        DB::table('student_programs')->insert(['student_id' => 303, 'program_id' => 1]);
        DB::table('quizzes')->insert([
            ['id' => 62, 'subject_id' => 212, 'program_id' => null, 'class_id' => 13],
            ['id' => 63, 'subject_id' => 212, 'program_id' => null, 'class_id' => null],
            ['id' => 64, 'subject_id' => null, 'program_id' => 2, 'class_id' => 99],
        ]);
    }

    public function test_missing_class_is_reported_but_shared_and_unrelated_quizzes_are_not(): void
    {
        $rows = $this->audit();
        $this->assertCount(1, $rows);
        $this->assertSame(62, $rows[0]['quiz_id']);
        $this->assertSame('missing_class_assignment', $rows[0]['reason']);
        $this->assertDatabaseHas('student_programs', ['student_id' => 303, 'class_id' => null]);
    }

    public function test_legacy_class_fallback_and_pivot_precedence_match_quiz_access(): void
    {
        DB::table('users')->where('id', 303)->update(['class_id' => 13]);
        $this->assertSame([], $this->audit());

        DB::table('student_programs')->where('student_id', 303)->update(['class_id' => 31]);
        $rows = $this->audit();
        $this->assertCount(1, $rows);
        $this->assertSame('different_class_review_required', $rows[0]['reason']);
        $this->assertSame(31, $rows[0]['current_class_id']);
    }

    private function audit(): array
    {
        return iterator_to_array((new StudentQuizClassAuditSeeder)->findBlockedQuizzes(User::findOrFail(303)));
    }
}
