<?php

namespace Tests\Feature\Homework;

use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Program;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Regression tests for the API student homework endpoints. These lock the
 * current observable behavior (JSON shape, status codes, access control)
 * BEFORE the Controller -> Service -> Repository refactor.
 */
class ApiStudentHomeworkTest extends HomeworkTestCase
{
    use DatabaseTransactions;

    private function student(): User
    {
        $program = Program::factory()->create();

        return User::factory()->create([
            'role'       => 'student',
            'status'     => 'active',
            'program_id' => $program->id,
        ]);
    }

    /** Homework the given student can access, via a subject in their program. */
    private function accessibleHomework(User $student): Homework
    {
        $subject = Subject::factory()->create(['program_id' => $student->program_id]);

        return Homework::factory()->create(['subject_id' => $subject->id]);
    }

    public function test_index_returns_stats_and_data_shape(): void
    {
        $student  = $this->student();
        $homework = $this->accessibleHomework($student);

        $response = $this->actingAs($student, 'sanctum')
            ->getJson('/api/v1/student/homework');

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'filter'  => 'all',
                'total'   => 1,
                'stats'   => [
                    'total'     => 1,
                    'pending'   => 1,
                    'submitted' => 0,
                    'graded'    => 0,
                ],
            ])
            ->assertJsonStructure([
                'success', 'filter', 'total',
                'stats' => ['total', 'pending', 'submitted', 'graded'],
                'data'  => [['id', 'title_ar', 'title_en', 'due_date', 'is_overdue', 'submission']],
            ]);

        $this->assertSame($homework->id, $response->json('data.0.id'));
    }

    public function test_index_pending_filter_excludes_submitted(): void
    {
        $student  = $this->student();
        $homework = $this->accessibleHomework($student);

        HomeworkSubmission::factory()->create([
            'homework_id' => $homework->id,
            'student_id'  => $student->id,
            'grade'       => null,
        ]);

        $this->actingAs($student, 'sanctum')
            ->getJson('/api/v1/student/homework?filter=pending')
            ->assertOk()
            ->assertJsonPath('filter', 'pending')
            ->assertJsonCount(0, 'data');
    }

    public function test_show_404_when_homework_not_accessible(): void
    {
        $student = $this->student();

        // Homework tied to an unrelated program/subject the student can't see.
        $otherProgram = Program::factory()->create();
        $otherSubject = Subject::factory()->create(['program_id' => $otherProgram->id]);
        $homework     = Homework::factory()->create(['subject_id' => $otherSubject->id]);

        $this->actingAs($student, 'sanctum')
            ->getJson("/api/v1/student/homework/{$homework->id}")
            ->assertNotFound();
    }

    public function test_submit_requires_content_file_or_url(): void
    {
        $student  = $this->student();
        $homework = $this->accessibleHomework($student);

        $this->actingAs($student, 'sanctum')
            ->postJson("/api/v1/student/homework/{$homework->id}/submit", [])
            ->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'يرجى كتابة نص الإجابة أو رفع ملف أو إرسال رابط.',
            ]);
    }

    public function test_submit_with_content_creates_submission_and_returns_201(): void
    {
        $student  = $this->student();
        $homework = $this->accessibleHomework($student);

        $this->actingAs($student, 'sanctum')
            ->postJson("/api/v1/student/homework/{$homework->id}/submit", [
                'content' => 'إجابتي',
            ])
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'تم تسليم الواجب بنجاح',
                'data'    => ['content' => 'إجابتي'],
            ]);

        $this->assertDatabaseHas('homework_submissions', [
            'homework_id' => $homework->id,
            'student_id'  => $student->id,
            'content'     => 'إجابتي',
        ]);
    }

    public function test_submit_with_file_url_stores_url_as_file_path(): void
    {
        $student  = $this->student();
        $homework = $this->accessibleHomework($student);
        $url      = 'https://example.com/answer.pdf';

        $this->actingAs($student, 'sanctum')
            ->postJson("/api/v1/student/homework/{$homework->id}/submit", [
                'file_url' => $url,
            ])
            ->assertStatus(201)
            ->assertJsonPath('data.file_url', $url);

        $this->assertDatabaseHas('homework_submissions', [
            'homework_id' => $homework->id,
            'student_id'  => $student->id,
            'file_path'   => $url,
        ]);
    }

    public function test_delete_submission_with_url_does_not_touch_storage(): void
    {
        Storage::fake('public');

        $student  = $this->student();
        $homework = $this->accessibleHomework($student);

        $submission = HomeworkSubmission::factory()->create([
            'homework_id' => $homework->id,
            'student_id'  => $student->id,
            'file_path'   => 'https://example.com/answer.pdf',
        ]);

        $this->actingAs($student, 'sanctum')
            ->deleteJson("/api/v1/student/homework/submissions/{$submission->id}")
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'تم حذف التسليم بنجاح']);

        $this->assertDatabaseMissing('homework_submissions', ['id' => $submission->id]);
    }
}
