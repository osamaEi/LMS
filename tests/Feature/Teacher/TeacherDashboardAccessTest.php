<?php

namespace Tests\Feature\Teacher;

use App\Models\User;
use Tests\TestCase;

class TeacherDashboardAccessTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('teacher.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_non_teacher_cannot_open_teacher_dashboard(): void
    {
        $student = User::factory()->make([
            'role' => 'student',
            'status' => 'active',
        ]);

        $this->actingAs($student)
            ->get(route('teacher.dashboard'))
            ->assertForbidden();
    }

    public function test_suspended_teacher_is_logged_out_and_redirected(): void
    {
        $teacher = User::factory()->make([
            'role' => 'teacher',
            'status' => 'suspended',
        ]);

        $this->actingAs($teacher)
            ->get(route('teacher.dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
