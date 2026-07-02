<?php

namespace Tests\Feature\Homework;

use App\Models\Enrollment;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\HomeworkCreatedNotification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

/**
 * Regression tests for the teacher homework management endpoints. Homework is
 * tied to a subject (المقرر) or program, not a session.
 */
class TeacherHomeworkTest extends HomeworkTestCase
{
    use DatabaseTransactions;

    private function teacher(): User
    {
        return User::factory()->create(['role' => 'teacher', 'status' => 'active']);
    }

    private function subjectForTeacher(User $teacher): Subject
    {
        return Subject::factory()->create(['teacher_id' => $teacher->id]);
    }

    public function test_store_creates_homework_and_notifies_active_enrolled_students(): void
    {
        Storage::fake('public');
        Notification::fake();

        $teacher = $this->teacher();
        $subject = $this->subjectForTeacher($teacher);

        // One active enrolled student (should be notified) + one inactive (should not).
        $active = User::factory()->create(['role' => 'student']);
        $inactive = User::factory()->create(['role' => 'student']);
        Enrollment::factory()->create(['subject_id' => $subject->id, 'student_id' => $active->id, 'status' => 'active']);
        Enrollment::factory()->create(['subject_id' => $subject->id, 'student_id' => $inactive->id, 'status' => 'withdrawn']);

        $response = $this->actingAs($teacher)
            ->from('/teacher/homework')
            ->post('/teacher/homework', [
                'subject_id'  => $subject->id,
                'title_ar'    => 'واجب اختبار',
                'description_ar' => 'وصف',
                'due_date'    => now()->addWeek()->toDateString(),
                'file'        => UploadedFile::fake()->create('hw.pdf', 100),
            ]);

        $response->assertRedirect('/teacher/homework');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('homeworks', [
            'subject_id' => $subject->id,
            'title_ar'   => 'واجب اختبار',
        ]);

        $homework = Homework::where('subject_id', $subject->id)->first();
        Storage::disk('public')->assertExists($homework->file_path);

        Notification::assertSentTo($active, HomeworkCreatedNotification::class);
        Notification::assertNotSentTo($inactive, HomeworkCreatedNotification::class);
    }

    public function test_store_requires_subject_or_program(): void
    {
        $teacher = $this->teacher();

        $this->actingAs($teacher)
            ->from('/teacher/homework')
            ->post('/teacher/homework', ['title_ar' => 'بدون مقرر'])
            ->assertSessionHasErrors('subject_id');
    }

    public function test_update_replaces_file_and_deletes_old_one(): void
    {
        Storage::fake('public');

        $teacher = $this->teacher();
        $subject = $this->subjectForTeacher($teacher);

        $oldPath = UploadedFile::fake()->create('old.pdf', 50)->store('homework-files', 'public');
        $homework = Homework::factory()->create([
            'subject_id' => $subject->id,
            'file_path'  => $oldPath,
            'file_name'  => 'old.pdf',
        ]);

        Storage::disk('public')->assertExists($oldPath);

        $this->actingAs($teacher)
            ->from('/teacher/homework')
            ->put("/teacher/homework/{$homework->id}", [
                'title_ar' => 'محدث',
                'file'     => UploadedFile::fake()->create('new.pdf', 60),
            ])
            ->assertRedirect('/teacher/homework')
            ->assertSessionHas('success');

        $homework->refresh();
        $this->assertSame('محدث', $homework->title_ar);
        $this->assertNotSame($oldPath, $homework->file_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($homework->file_path);
    }

    public function test_destroy_deletes_homework_and_file(): void
    {
        Storage::fake('public');

        $teacher = $this->teacher();
        $subject = $this->subjectForTeacher($teacher);

        $path = UploadedFile::fake()->create('hw.pdf', 40)->store('homework-files', 'public');
        $homework = Homework::factory()->create(['subject_id' => $subject->id, 'file_path' => $path]);

        $this->actingAs($teacher)
            ->from('/teacher/homework')
            ->delete("/teacher/homework/{$homework->id}")
            ->assertRedirect('/teacher/homework')
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('homeworks', ['id' => $homework->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_grade_submission_saves_grade_and_feedback(): void
    {
        $teacher = $this->teacher();
        $subject = $this->subjectForTeacher($teacher);
        $homework = Homework::factory()->create(['subject_id' => $subject->id]);
        $student = User::factory()->create(['role' => 'student']);
        $submission = HomeworkSubmission::factory()->create([
            'homework_id' => $homework->id,
            'student_id'  => $student->id,
        ]);

        $this->actingAs($teacher)
            ->from('/teacher/homework')
            ->put("/teacher/homework/{$homework->id}/submissions/{$submission->id}/grade", [
                'grade'     => 4,
                'max_grade' => 5,
                'feedback'  => 'عمل جيد',
            ])
            ->assertRedirect('/teacher/homework')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('homework_submissions', [
            'id'        => $submission->id,
            'grade'     => 4,
            'max_grade' => 5,
            'feedback'  => 'عمل جيد',
        ]);
    }

    public function test_grade_submission_rejects_grade_above_max(): void
    {
        $teacher = $this->teacher();
        $subject = $this->subjectForTeacher($teacher);
        $homework = Homework::factory()->create(['subject_id' => $subject->id]);
        $student = User::factory()->create(['role' => 'student']);
        $submission = HomeworkSubmission::factory()->create([
            'homework_id' => $homework->id,
            'student_id'  => $student->id,
        ]);

        // 6 out of 5 is invalid — grade must not exceed max_grade.
        $this->actingAs($teacher)
            ->from('/teacher/homework')
            ->put("/teacher/homework/{$homework->id}/submissions/{$submission->id}/grade", [
                'grade'     => 6,
                'max_grade' => 5,
            ])
            ->assertSessionHasErrors('grade');
    }
}
