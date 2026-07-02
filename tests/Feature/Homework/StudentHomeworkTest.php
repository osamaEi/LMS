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
 * Regression tests for the student (web) homework endpoints. These lock the
 * current observable behavior (deadline rejection, resubmission, ownership
 * check, file handling) BEFORE the Controller -> Service -> Repository refactor.
 */
class StudentHomeworkTest extends HomeworkTestCase
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

    private function homeworkForStudent(User $student, ?string $dueDate = null): Homework
    {
        $subject = Subject::factory()->create(['program_id' => $student->program_id]);

        return Homework::factory()->create([
            'subject_id' => $subject->id,
            'due_date'   => $dueDate,
        ]);
    }

    public function test_submit_stores_submission_with_content(): void
    {
        $student  = $this->student();
        $homework = $this->homeworkForStudent($student, now()->addWeek()->toDateString());

        $this->actingAs($student)
            ->from('/student/homework')
            ->post("/student/homework/{$homework->id}/submit", ['content' => 'إجابتي'])
            ->assertRedirect('/student/homework')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('homework_submissions', [
            'homework_id' => $homework->id,
            'student_id'  => $student->id,
            'content'     => 'إجابتي',
        ]);
    }

    public function test_submit_rejected_after_deadline(): void
    {
        $student  = $this->student();
        $homework = $this->homeworkForStudent($student, now()->subDay()->toDateString());

        $this->actingAs($student)
            ->from('/student/homework')
            ->post("/student/homework/{$homework->id}/submit", ['content' => 'متأخر'])
            ->assertRedirect('/student/homework')
            ->assertSessionHasErrors('content');

        $this->assertDatabaseMissing('homework_submissions', [
            'homework_id' => $homework->id,
            'student_id'  => $student->id,
        ]);
    }

    public function test_submit_requires_content_or_file(): void
    {
        $student  = $this->student();
        $homework = $this->homeworkForStudent($student, now()->addWeek()->toDateString());

        $this->actingAs($student)
            ->from('/student/homework')
            ->post("/student/homework/{$homework->id}/submit", [])
            ->assertRedirect('/student/homework')
            ->assertSessionHasErrors('content');
    }

    public function test_resubmission_updates_existing_submission(): void
    {
        $student  = $this->student();
        $homework = $this->homeworkForStudent($student, now()->addWeek()->toDateString());

        HomeworkSubmission::factory()->create([
            'homework_id' => $homework->id,
            'student_id'  => $student->id,
            'content'     => 'أول محاولة',
        ]);

        $this->actingAs($student)
            ->from('/student/homework')
            ->post("/student/homework/{$homework->id}/submit", ['content' => 'محاولة ثانية'])
            ->assertRedirect('/student/homework');

        $this->assertDatabaseCount('homework_submissions', 1);
        $this->assertDatabaseHas('homework_submissions', [
            'homework_id' => $homework->id,
            'student_id'  => $student->id,
            'content'     => 'محاولة ثانية',
        ]);
    }

    public function test_delete_submission_forbidden_for_non_owner(): void
    {
        $owner = $this->student();
        $other = $this->student();
        $homework = $this->homeworkForStudent($owner, now()->addWeek()->toDateString());

        $submission = HomeworkSubmission::factory()->create([
            'homework_id' => $homework->id,
            'student_id'  => $owner->id,
        ]);

        $this->actingAs($other)
            ->delete("/student/homework/submissions/{$submission->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('homework_submissions', ['id' => $submission->id]);
    }

    public function test_owner_can_delete_submission_and_its_file(): void
    {
        Storage::fake('public');

        $student  = $this->student();
        $homework = $this->homeworkForStudent($student, now()->addWeek()->toDateString());

        $path = UploadedFile::fake()->create('answer.pdf', 20)->store('homework-submissions', 'public');
        $submission = HomeworkSubmission::factory()->create([
            'homework_id' => $homework->id,
            'student_id'  => $student->id,
            'file_path'   => $path,
        ]);

        $this->actingAs($student)
            ->from('/student/homework')
            ->delete("/student/homework/submissions/{$submission->id}")
            ->assertRedirect('/student/homework')
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('homework_submissions', ['id' => $submission->id]);
        Storage::disk('public')->assertMissing($path);
    }
}
