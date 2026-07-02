<?php

namespace Tests\Feature\Homework;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

/**
 * Base test case for the Homework feature suite.
 *
 * The project's full migration set cannot be run on a clean database
 * (several legacy migrations use MySQL-only raw SQL / non-idempotent index
 * drops). To get a reliable, self-contained regression harness for the
 * Homework refactor, this base class builds ONLY the tables the homework
 * flows actually touch, matching the real column shapes those flows query.
 *
 * Each test method runs inside a DB transaction that is rolled back on
 * teardown, so data never leaks between tests. The schema itself is built
 * once per test method in setUp() (dropped first for a clean slate).
 */
abstract class HomeworkTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->buildSchema();
    }

    protected function buildSchema(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ([
            'homework_submissions',
            'homeworks',
            'enrollments',
            'term_subject',
            'student_programs',
            'class_sessions',
            'subjects',
            'program_classes',
            'terms',
            'programs',
            'users',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('role')->default('student');
            $table->string('status')->default('active');
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('student_code')->nullable();
            $table->string('zoom_join_url')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('code')->nullable();
            $table->string('type')->nullable();
            $table->string('status')->default('active');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->integer('term_number')->nullable();
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('program_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->string('name');
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->string('status')->default('active');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('term_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('code')->nullable();
            $table->string('status')->default('active');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();
            $table->integer('session_number')->nullable();
            $table->string('type')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('student_programs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('program_id');
            $table->string('status')->default('approved');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedTinyInteger('current_term_number')->default(1);
            $table->date('enrolled_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'program_id']);
        });

        Schema::create('term_subject', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('term_id');
            $table->unsignedBigInteger('subject_id');
            $table->timestamps();
            $table->unique(['term_id', 'subject_id']);
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('subject_id');
            $table->timestamp('enrolled_at')->nullable();
            $table->string('status')->default('active');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('homeworks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->date('due_date')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->timestamps();
        });

        Schema::create('homework_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('homework_id');
            $table->unsignedBigInteger('student_id');
            $table->text('content')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('grade')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->unique(['homework_id', 'student_id']);
        });

        Schema::enableForeignKeyConstraints();
    }
}
