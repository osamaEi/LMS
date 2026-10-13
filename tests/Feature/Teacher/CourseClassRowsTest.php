<?php

namespace Tests\Feature\Teacher;

use App\Http\Controllers\Teacher\CourseController;
use App\Models\{Program, ProgramClass, Session, User};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Schema};
use Tests\Feature\Homework\HomeworkTestCase;

class CourseClassRowsTest extends HomeworkTestCase
{
    protected function buildSchema(): void
    {
        config(['database.default' => 'course_rows', 'database.connections.course_rows' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('course_rows');
        parent::buildSchema();
        Schema::create('program_teacher', function (Blueprint $table) {
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('teacher_id');
            $table->timestamps();
        });
        Schema::create('subject_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
        });
    }

    public function test_program_repeats_per_authorized_class_with_separate_counts_and_type_filter(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $this->actingAs($teacher);
        $program = Program::create(['name_ar' => 'English', 'type' => 'english']);
        $first = ProgramClass::create(['program_id' => $program->id, 'name' => 'A', 'teacher_id' => $teacher->id]);
        $second = ProgramClass::create(['program_id' => $program->id, 'name' => 'B', 'teacher_id' => $teacher->id]);
        $other = ProgramClass::create(['program_id' => $program->id, 'name' => 'C', 'teacher_id' => 999]);
        Session::create(['program_id' => $program->id, 'class_id' => $first->id, 'teacher_id' => $teacher->id]);
        Session::create(['program_id' => $program->id, 'class_id' => $first->id, 'teacher_id' => 999]);
        DB::table('student_programs')->insert([
            ['program_id' => $program->id, 'class_id' => $first->id, 'student_id' => 100],
            ['program_id' => $program->id, 'class_id' => $other->id, 'student_id' => 101],
        ]);
        $controller = app(CourseController::class);
        $rows = $controller->index(Request::create('/', 'GET', ['type' => 'english']))->getData()['programs'];
        $this->assertSame([$first->id, $second->id], $rows->pluck('class_id')->all());
        $this->assertSame([$program->id, $program->id], $rows->pluck('id')->all());
        $this->assertSame([1, 0], $rows->pluck('sessions_count')->all());
        $this->assertSame([1, 0], $rows->pluck('class_student_count')->all());
        $this->assertCount(0, $controller->index(Request::create('/', 'GET', ['type' => 'course']))->getData()['programs']);

        $teacher->teachingPrograms()->attach($program);
        $rows = $controller->index(Request::create('/'))->getData()['programs'];
        $this->assertSame([$first->id, $second->id, $other->id], $rows->pluck('class_id')->all());
    }
}
