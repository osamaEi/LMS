<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StudentDashboardAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'dashboard_access', 'database.connections.dashboard_access' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('dashboard_access');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Test user');
            $table->string('role');
            $table->string('status')->default('active');
            $table->string('remember_token')->nullable();
            $table->softDeletes();
        });
    }

    private function user(string $role, string $status = 'active'): User
    {
        $id = DB::table('users')->insertGetId(compact('role', 'status'));

        return User::findOrFail($id);
    }

    public function test_admin_can_enter_student_account_and_return_even_if_student_is_suspended(): void
    {
        foreach (['admin', 'super_admin'] as $role) {
            $admin = $this->user($role);
            $student = $this->user('student');
            $this->actingAs($admin)->post(route('admin.students.dashboard-access', $student))
                ->assertRedirect(route('student.dashboard'))
                ->assertSessionHas('student_dashboard_admin_id', $admin->id);
            $this->assertAuthenticatedAs($student);
            $this->get(route('admin.students.index', ['search' => 'نورة']))
                ->assertRedirect(route('student.dashboard'));
            $this->assertAuthenticatedAs($student);

            DB::table('users')->where('id', $student->id)->update(['status' => 'suspended']);
            $this->actingAs($student->fresh())->post(route('student-dashboard-access.stop'))
                ->assertRedirect(route('admin.students.index'))
                ->assertSessionMissing('student_dashboard_admin_id');
            $this->assertAuthenticatedAs($admin);
        }
    }

    public function test_non_admins_and_guests_cannot_enter_student_accounts(): void
    {
        $student = $this->user('student');
        $url = route('admin.students.dashboard-access', $student);
        $this->post($url)->assertRedirect(route('login'));
        foreach (['student', 'teacher'] as $role) {
            $this->actingAs($this->user($role))->post($url)->assertForbidden();
        }
    }

    public function test_only_active_student_targets_are_allowed(): void
    {
        $admin = $this->user('admin');
        $this->actingAs($admin)->post(route('admin.students.dashboard-access', $this->user('teacher')))
            ->assertNotFound();
        foreach (['inactive', 'suspended', 'pending'] as $status) {
            $this->post(route('admin.students.dashboard-access', $this->user('student', $status)))
                ->assertForbidden();
        }
        $this->assertAuthenticatedAs($admin);
    }

    public function test_return_requires_an_existing_authorized_admin_session(): void
    {
        $student = $this->user('student');
        $this->actingAs($student)->post(route('student-dashboard-access.stop'))->assertForbidden();
        $admin = $this->user('admin', 'suspended');
        $this->withSession(['student_dashboard_admin_id' => $admin->id])
            ->post(route('student-dashboard-access.stop'))->assertForbidden();
        DB::table('users')->where('id', $admin->id)->update(['role' => 'student', 'status' => 'active']);
        $this->post(route('student-dashboard-access.stop'))->assertForbidden();
        $this->assertAuthenticatedAs($student);
    }
}
