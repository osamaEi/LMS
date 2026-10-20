<?php

namespace Tests\Feature\Teacher;

use App\Http\Controllers\Teacher\StudentsController;
use App\Models\{Program, ProgramClass, Session, Subject, User};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
use Tests\Feature\Homework\HomeworkTestCase;

class StudentsDirectoryTest extends HomeworkTestCase
{
    protected function buildSchema(): void
    {
        config(['database.default' => 'directory_test', 'database.connections.directory_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('directory_test');
        parent::buildSchema();
        Schema::create('subject_teacher', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('teacher_id');
            $table->timestamps();
        });
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('student_id');
        });
    }

    public function test_class_members_appear_without_attendance_or_subject_enrollment(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $program = Program::factory()->create();
        $first = ProgramClass::create(['program_id' => $program->id, 'name' => 'First']);
        $second = ProgramClass::create(['program_id' => $program->id, 'name' => 'Second']);
        foreach ([$first, $second] as $class) {
            Session::create(['teacher_id' => $teacher->id, 'class_id' => $class->id]);
        }
        $legacy = User::factory()->create(['role' => 'student']);
        $legacy->forceFill(['class_id' => $first->id, 'program_id' => $program->id])->save();
        $pivot = User::factory()->create(['role' => 'student']);
        $pivot->programs()->attach($program, ['class_id' => $first->id]);
        DB::table('student_class_memberships')->insert(['student_id' => $pivot->id, 'class_id' => $second->id]);
        $additional = User::factory()->create(['role' => 'student']);
        $additional->programs()->attach($program);
        DB::table('student_class_memberships')->insert(['student_id' => $additional->id, 'class_id' => $second->id]);

        // A student ID must never be interpreted as an assigned subject ID.
        $unrelated = User::factory()->create(['role' => 'student']);
        $subject = Subject::create(['name_ar' => 'Unrelated subject']);
        $subject->forceFill(['id' => $legacy->id])->save();
        DB::table('enrollments')->insert(['student_id' => $unrelated->id, 'subject_id' => $subject->id]);

        $this->actingAs($teacher);
        $data = (new StudentsController)->index()->getData();
        $this->assertSame(3, $data['totalStudents']);
        $groups = $data['grouped']->keyBy('class_id');
        $this->assertEqualsCanonicalizing([$legacy->id, $pivot->id], $groups[$first->id]['students']->pluck('id')->all());
        $this->assertEqualsCanonicalizing([$pivot->id, $additional->id], $groups[$second->id]['students']->pluck('id')->all());
    }
}
