<?php

namespace Tests\Feature\Student;

use App\Models\AttendanceApology;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApologySubmissionTest extends TestCase
{
    public function test_two_uploads_on_one_day_for_different_absence_dates_are_kept_separately(): void
    {
        config(['database.default' => 'apology_test', 'database.connections.apology_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('apology_test');
        Storage::fake('public');
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->dateTime('scheduled_at');
            $table->softDeletes();
        });
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->integer('session_id');
            $table->boolean('attended');
        });
        Schema::create('attendance_apologies', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->integer('session_id');
            $table->text('reason');
            $table->string('attachment_path')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->unique(['student_id', 'session_id']);
        });
        DB::table('class_sessions')->insert([
            ['id' => 1, 'scheduled_at' => '2026-10-15 09:00:00'],
            ['id' => 2, 'scheduled_at' => '2026-10-16 09:00:00'],
        ]);
        $student = new User(['role' => 'student', 'status' => 'active']);
        $student->id = 10;
        $this->actingAs($student);
        $this->travelTo(now()->setDate(2026, 10, 19)->setTime(9, 0));
        foreach ([1, 2] as $id) {
            $this->post('/student/sessions/'.$id.'/apology', [
                'reason' => 'Medical absence',
                'attachment' => UploadedFile::fake()->create('excuse.pdf', 10, 'application/pdf'),
            ])->assertRedirect()->assertSessionHas('success');
            $this->travel(1)->hours();
        }
        $rows = AttendanceApology::orderBy('id')->get();
        $this->assertCount(2, $rows);
        $this->assertSame('2026-10-19 09:00', $rows[0]->created_at->format('Y-m-d H:i'));
        $this->assertSame('2026-10-19 10:00', $rows[1]->created_at->format('Y-m-d H:i'));
        $this->assertNotSame($rows[0]->attachment_path, $rows[1]->attachment_path);
        Storage::disk('public')->assertExists($rows->pluck('attachment_path')->all());
        $html = view('student.apologies.upload-time', ['apology' => $rows[0]])->render();
        $this->assertStringContainsString('2026/10/19 09:00', $html);
        $this->travelBack();
    }
}
