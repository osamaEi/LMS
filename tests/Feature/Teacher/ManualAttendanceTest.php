<?php

namespace Tests\Feature\Teacher;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ManualAttendanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'manual_att_test', 'database.connections.manual_att_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('manual_att_test');

        Schema::create('users', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('role'); $t->string('status'); $t->timestamps(); $t->softDeletes();
        });
        Schema::create('subjects', function (Blueprint $t) {
            $t->id(); $t->integer('teacher_id')->nullable(); $t->softDeletes();
        });
        Schema::create('subject_teacher', function (Blueprint $t) {
            $t->id(); $t->integer('subject_id'); $t->integer('teacher_id');
        });
        Schema::create('class_sessions', function (Blueprint $t) {
            $t->id(); $t->integer('subject_id')->nullable(); $t->integer('teacher_id')->nullable();
            $t->timestamp('started_at')->nullable(); $t->timestamp('scheduled_at')->nullable();
            $t->timestamps(); $t->softDeletes();
        });
        Schema::create('attendances', function (Blueprint $t) {
            $t->id(); $t->integer('student_id'); $t->integer('session_id');
            $t->boolean('attended')->default(false); $t->boolean('is_late')->default(false);
            $t->timestamp('joined_at')->nullable(); $t->timestamps();
        });

        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Teacher', 'role' => 'teacher', 'status' => 'active'],
            ['id' => 2, 'name' => 'Student', 'role' => 'student', 'status' => 'active'],
        ]);
        DB::table('subjects')->insert(['id' => 1, 'teacher_id' => 1]);
        // Started over an hour ago: a stamped save time would count as a late join.
        DB::table('class_sessions')->insert([
            'id' => 1, 'subject_id' => 1, 'teacher_id' => 1, 'started_at' => now()->subMinutes(67),
        ]);
        DB::table('attendances')->insert(['student_id' => 2, 'session_id' => 1, 'attended' => false]);
    }

    public function test_manual_attendance_records_no_join_time_and_is_not_late(): void
    {
        $this->actingAs(User::findOrFail(1))->from('/teacher')
            ->post(route('teacher.my-subjects.sessions.attendance.save', [1, 1]), ['student_ids' => [2]])
            ->assertRedirect('/teacher')
            ->assertSessionHasNoErrors();

        $row = DB::table('attendances')->where(['student_id' => 2, 'session_id' => 1])->first();
        $this->assertEquals(1, $row->attended);
        $this->assertEquals(0, $row->is_late);
        $this->assertNull($row->joined_at);
    }
}
