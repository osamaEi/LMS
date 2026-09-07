<?php

namespace Tests\Feature\Student;

use App\Models\Setting;
use App\Models\User;
use App\Services\AttendanceLimitService as Limit;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema, Cache};
use Tests\TestCase;

class AttendanceLimitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'limit_test', 'database.connections.limit_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('limit_test');
        Cache::flush();
        Schema::create('settings', function (Blueprint $t) {
            $t->id(); $t->string('key'); $t->string('value'); $t->string('type')->default('string'); $t->timestamps();
        });
        Schema::create('subjects', function (Blueprint $t) {
            $t->id(); $t->float('absence_limit_percent')->nullable(); $t->integer('term_id')->nullable(); $t->softDeletes();
        });
        Schema::create('class_sessions', function (Blueprint $t) {
            $t->id(); $t->integer('subject_id'); $t->string('status'); $t->timestamp('ended_at')->nullable(); $t->softDeletes();
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
        DB::table('subjects')->insert(['id' => 1]);
        for ($id = 1; $id <= 10; $id++) {
            DB::table('class_sessions')->insert(['id' => $id, 'subject_id' => 1, 'status' => $id <= 3 ? 'completed' : 'scheduled']);
            DB::table('attendances')->insert(['student_id' => 1, 'session_id' => $id, 'attended' => false]);
        }
    }

    public function test_only_completed_absences_count_against_all_subject_sessions(): void
    {
        $status = Limit::statusFor(1, 1);
        $this->assertSame(10, $status['total']);
        $this->assertSame(3, $status['absent']);
        $this->assertTrue($status['blocked']);
        DB::table('attendances')->where('session_id', 3)->update(['attended' => true]);
        $this->assertFalse(Limit::statusFor(1, 1)['blocked']);
        $this->assertNull(Limit::blockReason(1, null));
    }

    public function test_approved_apology_and_exemption_lift_ban(): void
    {
        DB::table('attendance_apologies')->insert(['student_id' => 1, 'session_id' => 3, 'status' => 'pending']);
        $this->assertTrue(Limit::statusFor(1, 1)['blocked']);
        DB::table('attendance_apologies')->update(['status' => 'approved']);
        $this->assertFalse(Limit::statusFor(1, 1)['blocked']);
        DB::table('attendance_apologies')->update(['status' => 'rejected']);
        DB::table('attendance_exemptions')->insert(['student_id' => 1, 'subject_id' => 1]);
        $this->assertFalse(Limit::statusFor(1, 1)['blocked']);
    }

    public function test_subject_override_and_disabled_setting_are_respected(): void
    {
        DB::table('subjects')->update(['absence_limit_percent' => 30]);
        $this->assertFalse(Limit::statusFor(1, 1)['blocked']);
        DB::table('subjects')->update(['absence_limit_percent' => 10]);
        $this->assertTrue(Limit::statusFor(1, 1)['blocked']);
        Setting::set(Limit::SETTING_ENABLED, '0');
        $this->assertNull(Limit::blockReason(1, 1));
    }

    public function test_web_join_is_blocked_before_marking_attendance(): void
    {
        $student = new User;
        $student->forceFill(['id' => 1, 'role' => 'student', 'status' => 'active']);
        $this->actingAs($student)->get(route('student.sessions.join-zoom', 4))->assertForbidden();
        $this->assertEquals(0, DB::table('attendances')->where('session_id', 4)->value('attended'));
    }

    public function test_both_api_join_routes_enforce_the_same_ban(): void
    {
        $student = new User;
        $student->forceFill(['id' => 1, 'role' => 'student', 'status' => 'active']);
        \Laravel\Sanctum\Sanctum::actingAs($student);
        foreach (['join', 'join-zoom'] as $action) {
            $this->postJson('/api/v1/student/sessions/4/' . $action)
                ->assertForbidden()->assertJson(['success' => false]);
        }
        $this->assertEquals(0, DB::table('attendances')->where('session_id', 4)->value('attended'));
    }
}
