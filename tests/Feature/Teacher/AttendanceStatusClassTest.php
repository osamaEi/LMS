<?php

namespace Tests\Feature\Teacher;

use App\Http\Controllers\Teacher\AttendanceStatusController;
use App\Models\{ProgramClass, Session, Subject, User};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Cache, DB, Schema};
use Tests\Feature\Homework\HomeworkTestCase;

class AttendanceStatusClassTest extends HomeworkTestCase
{
    protected function buildSchema(): void
    {
        config(['database.default' => 'attendance_class', 'database.connections.attendance_class' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('attendance_class');
        Cache::flush();
        parent::buildSchema();
        Schema::table('subjects', fn (Blueprint $t) => $t->float('absence_limit_percent')->nullable());
        Schema::create('settings', function (Blueprint $t) {
            $t->id(); $t->string('key'); $t->string('value'); $t->string('type')->default('string'); $t->timestamps();
        });
        Schema::create('subject_teacher', function (Blueprint $t) {
            $t->unsignedBigInteger('subject_id'); $t->unsignedBigInteger('teacher_id'); $t->timestamps();
        });
        Schema::create('attendances', function (Blueprint $t) {
            $t->id(); $t->integer('student_id'); $t->integer('session_id'); $t->boolean('attended');
        });
        Schema::create('attendance_apologies', function (Blueprint $t) {
            $t->id(); $t->integer('student_id'); $t->integer('session_id'); $t->string('status');
        });
        Schema::create('attendance_exemptions', function (Blueprint $t) {
            $t->id(); $t->integer('student_id'); $t->integer('subject_id');
        });
    }

    public function test_class_cards_and_details_do_not_mix_students_sessions_or_absences(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $otherStudent = User::factory()->create(['role' => 'student']);
        $this->actingAs($teacher);
        $subject = Subject::create(['name_ar' => 'Shared subject']);
        $teacher->assignedSubjects()->attach($subject);
        $first = ProgramClass::create(['name' => 'A', 'teacher_id' => $teacher->id]);
        $second = ProgramClass::create(['name' => 'B', 'teacher_id' => $teacher->id]);
        $foreign = ProgramClass::create(['name' => 'C', 'teacher_id' => 999]);
        foreach ([[$first, $teacher->id, true], [$first, $teacher->id, false], [$second, $teacher->id, false], [$foreign, 999, false]] as [$class, $teacherId, $attended]) {
            $session = Session::create(['subject_id' => $subject->id, 'class_id' => $class->id, 'teacher_id' => $teacherId, 'status' => 'completed']);
            DB::table('attendances')->insert(['session_id' => $session->id, 'student_id' => $student->id, 'attended' => $attended]);
            if ($class->id === $second->id) {
                DB::table('attendances')->insert(['session_id' => $session->id, 'student_id' => $otherStudent->id, 'attended' => false]);
            }
            if ($class->id === $first->id && !$attended) {
                DB::table('attendance_apologies')->insert(['session_id' => $session->id, 'student_id' => $student->id, 'status' => 'approved']);
            }
        }
        $future = Session::create(['subject_id' => $subject->id, 'class_id' => $first->id, 'teacher_id' => $teacher->id, 'status' => 'scheduled']);
        DB::table('attendances')->insert(['session_id' => $future->id, 'student_id' => $student->id, 'attended' => false]);

        $controller = app(AttendanceStatusController::class);
        $cards = $controller->index()->getData()['subjects'];
        $this->assertSame([$first->id, $second->id], $cards->pluck('class_id')->all());
        $this->assertSame([3, 1], $cards->pluck('sessions')->all());
        $this->assertSame([1, 2], $cards->pluck('students')->all());
        $data = $controller->show(Request::create('/', 'GET', ['class_id' => $first->id]), $subject)->getData();
        $this->assertSame(3, $data['totalSessions']);
        $this->assertCount(1, $data['students']);
        $this->assertSame(0, $data['students']->first()->absent);
        $this->assertSame(1, $data['students']->first()->excused);
        $this->assertFalse($data['students']->first()->banned);

        $this->get(route('teacher.attendance-status.show', ['subject' => $subject->id, 'class_id' => $foreign->id]))->assertForbidden();
        $this->get(route('teacher.attendance-status.show', $subject))->assertRedirect(route('teacher.attendance-status.index'));
    }
}
