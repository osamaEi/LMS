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
            $t->id(); $t->string('name_ar')->default('مادة اختبار'); $t->string('name_en')->nullable(); $t->float('absence_limit_percent')->nullable(); $t->integer('term_id')->nullable(); $t->softDeletes();
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

    private function authenticateAlertStudent(int $id = 1): void
    {
        $student = new User;
        $student->forceFill(['id' => $id, 'role' => 'student', 'status' => 'active']);
        \Laravel\Sanctum\Sanctum::actingAs($student);
    }

    public function test_alerts_require_authentication(): void
    {
        $this->getJson('/api/v1/student/attendance-alerts')->assertUnauthorized();
    }

    public function test_alerts_follow_the_actual_join_limit_and_warning_boundary(): void
    {
        $this->authenticateAlertStudent();
        $this->getJson('/api/v1/student/attendance-alerts')->assertOk()
            ->assertJsonPath('data.alerts.0.code', 'absence_limit_exceeded')
            ->assertJsonPath('data.alerts.0.blocked', true)
            ->assertJsonPath('data.alerts.0.absent_sessions', 3);

        DB::table('attendances')->where('session_id', 3)->update(['attended' => true]);
        $this->getJson('/api/v1/student/attendance-alerts')->assertOk()
            ->assertJsonPath('data.alerts.0.severity', 'warning')
            ->assertJsonPath('data.alerts.0.blocked', false)
            ->assertJsonPath('data.alerts.0.remaining_allowed_absences', 0);

        DB::table('attendance_apologies')->insert(['student_id' => 1, 'session_id' => 2, 'status' => 'approved']);
        $this->getJson('/api/v1/student/attendance-alerts')->assertOk()
            ->assertJsonPath('data.alerts.0.remaining_allowed_absences', 1)
            ->assertJsonPath('data.alerts.0.excused_sessions', 1);

        DB::table('attendances')->where('session_id', 1)->update(['attended' => true]);
        $this->getJson('/api/v1/student/attendance-alerts')->assertOk()
            ->assertJsonPath('data.has_alerts', false)->assertJsonPath('data.alerts', []);
    }

    public function test_alerts_respect_subject_override_exemption_and_disabled_limit(): void
    {
        $this->authenticateAlertStudent();
        DB::table('subjects')->update(['absence_limit_percent' => 60]);
        $this->getJson('/api/v1/student/attendance-alerts')->assertOk()->assertJsonPath('data.alerts', []);
        DB::table('subjects')->update(['absence_limit_percent' => null]);
        DB::table('attendance_exemptions')->insert(['student_id' => 1, 'subject_id' => 1]);
        $this->getJson('/api/v1/student/attendance-alerts')->assertOk()->assertJsonPath('data.alerts', []);
        DB::table('attendance_exemptions')->delete();
        Setting::set(Limit::SETTING_ENABLED, '0');
        $this->getJson('/api/v1/student/attendance-alerts')->assertOk()->assertJsonPath('data.alerts', []);
    }

    public function test_alerts_do_not_expose_another_students_attendance(): void
    {
        $this->authenticateAlertStudent(2);
        $this->getJson('/api/v1/student/attendance-alerts?student_id=1')->assertOk()
            ->assertJsonPath('data.has_alerts', false)->assertJsonPath('data.alerts', []);
    }
}
