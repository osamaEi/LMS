<?php

namespace Tests\Feature\Teacher;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CertificateUrlTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'certificate_test', 'database.connections.certificate_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('certificate_test');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
        (require database_path('migrations/2026_10_17_000001_add_certificate_url_to_users_table.php'))->up();
    }

    public function test_teacher_can_save_and_clear_own_certificate(): void
    {
        $teacher = User::create(['name' => 'Teacher', 'email' => 'teacher@example.com', 'role' => 'teacher', 'status' => 'active']);
        $other = User::create(['name' => 'Other', 'email' => 'other@example.com', 'role' => 'teacher', 'status' => 'active']);
        $data = ['name' => $teacher->name, 'email' => $teacher->email, 'certificate_url' => 'https://example.com/certificate.pdf'];
        $this->actingAs($teacher)->putJson(route('teacher.profile.update'), $data)->assertOk();
        $this->assertSame($data['certificate_url'], $teacher->fresh()->certificate_url);
        $this->assertNull($other->fresh()->certificate_url);
        $data['certificate_url'] = '';
        $this->putJson(route('teacher.profile.update'), $data)->assertOk();
        $this->assertNull($teacher->fresh()->certificate_url);
    }

    public function test_admin_can_save_teacher_certificate(): void
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'role' => 'admin', 'status' => 'active']);
        $teacher = User::create(['name' => 'Teacher', 'email' => 'teacher@example.com', 'role' => 'teacher', 'status' => 'active']);
        $data = ['name' => $teacher->name, 'email' => $teacher->email, 'certificate_url' => 'https://example.com/certificate.pdf'];
        $this->actingAs($admin)->put(route('admin.teachers.update', $teacher), $data)->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame($data['certificate_url'], $teacher->fresh()->certificate_url);
    }

    public function test_unsafe_url_is_rejected(): void
    {
        $teacher = User::create(['name' => 'Teacher', 'email' => 'teacher@example.com', 'role' => 'teacher', 'status' => 'active']);
        $this->actingAs($teacher)->putJson(route('teacher.profile.update'), [
            'name' => $teacher->name, 'email' => $teacher->email, 'certificate_url' => 'javascript:alert(1)',
        ])->assertUnprocessable()->assertJsonValidationErrors('certificate_url');
        $this->assertNull($teacher->fresh()->certificate_url);
    }
}
