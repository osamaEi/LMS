<?php

namespace Tests\Feature\Teacher;

use App\Http\Controllers\Teacher\StudentReportController;
use App\Models\{Program, Session, Subject, User};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
use Tests\Feature\Homework\HomeworkTestCase;

class StudentReportClassScopeTest extends HomeworkTestCase
{
    protected function buildSchema(): void
    {
        config(['database.default' => 'report_scope', 'database.connections.report_scope' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('report_scope');
        parent::buildSchema();
        Schema::create('subject_teacher', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('teacher_id');
            $table->timestamps();
        });
    }

    private function reportScope(User $teacher, User $student): array
    {
        return (new \ReflectionMethod(StudentReportController::class, 'scope'))
            ->invoke(new StudentReportController, $teacher, $student);
    }

    public function test_only_subjects_taught_by_teacher_in_students_class_appear(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $program = Program::factory()->create();
        $student->programs()->attach($program, ['class_id' => 10]);
        $own = Subject::create(['name_ar' => 'Taught in student class']);
        $otherClass = Subject::create(['name_ar' => 'Taught in another class']);
        $assigned = Subject::create(['name_ar' => 'Assigned without sessions']);
        $otherTeacher = Subject::create(['name_ar' => 'Another teacher']);
        $unscoped = Subject::create(['name_ar' => 'Session without class']);
        $teacher->assignedSubjects()->attach([$assigned->id, $otherClass->id]);
        DB::table('enrollments')->insert(['student_id' => $student->id, 'subject_id' => $otherClass->id]);
        $session = Session::create(['teacher_id' => $teacher->id, 'subject_id' => $own->id, 'class_id' => 10]);
        Session::create(['teacher_id' => $teacher->id, 'subject_id' => $otherClass->id, 'class_id' => 20]);
        Session::create(['teacher_id' => 999, 'subject_id' => $otherTeacher->id, 'class_id' => 10]);
        Session::create(['teacher_id' => $teacher->id, 'subject_id' => $unscoped->id, 'class_id' => null]);

        $scope = $this->reportScope($teacher, $student);
        $this->assertSame([$own->id], $scope['subject_ids']->all());
        $this->assertSame([$session->id], $scope['session_ids']->all());
        $subjects = (new \ReflectionMethod(StudentReportController::class, 'reportSubjects'))
            ->invoke(new StudentReportController, $student, $scope);
        $this->assertSame([$own->id], $subjects->pluck('subject_id')->all());
        // Class membership suffices even before attendance/enrollment records exist.
        (new \ReflectionMethod(StudentReportController::class, 'authorizeStudent'))
            ->invoke(new StudentReportController, $student, $scope);
    }

    public function test_legacy_class_membership_is_supported_and_shared_subject_is_deduplicated(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $student->forceFill(['class_id' => 10])->save();
        $subject = Subject::create(['name_ar' => 'Shared subject']);
        foreach ([10, 10, 20] as $classId) {
            Session::create(['teacher_id' => $teacher->id, 'subject_id' => $subject->id, 'class_id' => $classId]);
        }
        $scope = $this->reportScope($teacher, $student);
        $this->assertSame([$subject->id], $scope['subject_ids']->all());
        $this->assertCount(2, $scope['session_ids']);
    }

    public function test_student_without_class_has_no_report_scope_even_with_subject_enrollment(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $student = User::factory()->create(['role' => 'student']);
        $subject = Subject::create(['name_ar' => 'Enrolled subject']);
        DB::table('enrollments')->insert(['student_id' => $student->id, 'subject_id' => $subject->id]);
        Session::create(['teacher_id' => $teacher->id, 'subject_id' => $subject->id, 'class_id' => 10]);
        $this->assertTrue($this->reportScope($teacher, $student)['subject_ids']->isEmpty());
    }

    public function test_teacher_cannot_open_or_update_report_when_teaching_only_another_class(): void
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);
        $student = User::factory()->create(['role' => 'student']);
        $student->forceFill(['class_id' => 10])->save();
        $subject = Subject::create(['name_ar' => 'Other class subject']);
        DB::table('enrollments')->insert(['student_id' => $student->id, 'subject_id' => $subject->id]);
        $teacher->assignedSubjects()->attach($subject);
        Session::create(['teacher_id' => $teacher->id, 'subject_id' => $subject->id, 'class_id' => 20]);

        $this->actingAs($teacher)->get(route('teacher.students.report.show', $student))->assertForbidden();
        $this->patch(route('teacher.students.report.update', $student), [
            'totals' => [$subject->id => ['final_grade' => 99]],
        ])->assertForbidden();
        \Illuminate\Support\Facades\Notification::fake();
        $this->post(route('teacher.students.report.send', $student))->assertForbidden();
        \Illuminate\Support\Facades\Notification::assertNothingSent();
    }

    public function test_sending_grades_notifies_only_target_student_with_scoped_saved_grades(): void
    {
        foreach ([new \App\Models\Quiz, new \App\Models\QuizAttempt, new \App\Models\Attendance,
            new \App\Models\ParticipationMark, new \App\Models\AttendanceApology] as $model) {
            Schema::create($model->getTable(), function (Blueprint $table) use ($model) {
                $table->id();
                foreach ($model->getFillable() as $column) {
                    if ($column !== 'id') $table->string($column)->nullable();
                }
                $table->timestamps();
            });
        }
        Schema::table('enrollments', function (Blueprint $table) {
            $table->decimal('final_grade')->nullable();
            $table->string('grade_letter')->nullable();
        });
        \Illuminate\Support\Facades\Notification::fake();
        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);
        $student = User::factory()->create(['role' => 'student']);
        $student->forceFill(['class_id' => 10])->save();
        $own = Subject::create(['name_ar' => 'Own subject']);
        $other = Subject::create(['name_ar' => 'Other class']);
        Session::create(['teacher_id' => $teacher->id, 'subject_id' => $own->id, 'class_id' => 10]);
        Session::create(['teacher_id' => $teacher->id, 'subject_id' => $other->id, 'class_id' => 20]);
        DB::table('enrollments')->insert(['student_id' => $student->id, 'subject_id' => $own->id, 'final_grade' => 87]);

        $this->actingAs($teacher)->post(route('teacher.students.report.send', $student), [
            'subject_id' => $other->id,
        ])->assertForbidden();
        \Illuminate\Support\Facades\Notification::assertNothingSent();
        $this->post(route('teacher.students.report.send', $student), ['subject_id' => $own->id])
            ->assertRedirect(route('teacher.students.report.show', $student));
        \Illuminate\Support\Facades\Notification::assertSentTo($student, \App\Notifications\TeacherGradesNotification::class,
            function ($notification) use ($student, $own) {
                $payload = $notification->toArray($student);
                $this->assertSame(['database'], $notification->via($student));
                $this->assertSame([$own->id], array_column($payload['grades'], 'subject_id'));
                $this->assertEquals(87, $payload['grades'][0]['total']);
                return true;
            });
        \Illuminate\Support\Facades\Notification::assertCount(1);
    }
}
