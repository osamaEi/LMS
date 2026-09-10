<?php

namespace Tests\Feature\Homework;

use App\Models\{Homework, Program, ProgramClass, Subject, User};
use App\Services\HomeworkService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

class HomeworkVisibilityTest extends HomeworkTestCase
{
    protected function buildSchema(): void
    {
        config(['database.default' => 'homework_visibility', 'database.connections.homework_visibility' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('homework_visibility');
        parent::buildSchema();
        Schema::table('homeworks', fn (Blueprint $table) => $table->unsignedBigInteger('class_id')->nullable());
    }

    public function test_new_student_without_enrollment_has_no_homework_on_web_or_api(): void
    {
        $student = User::factory()->create(['program_id' => null]);
        $subject = Subject::create(['name_ar' => 'Unassigned subject']);
        $homework = Homework::create(['subject_id' => $subject->id, 'title_ar' => 'Other homework']);
        Homework::create(['title_ar' => 'Unassigned homework']);
        $service = app(HomeworkService::class);

        $this->assertTrue($service->homeworksForStudentWeb($student)->isEmpty());
        $this->assertTrue($service->homeworksForStudentApi($student)->isEmpty());
        $this->expectException(ModelNotFoundException::class);
        $service->findAccessibleForStudent($homework->id, $student);
    }

    public function test_program_homework_is_limited_to_students_class_in_web_api_and_details(): void
    {
        $program = Program::factory()->create();
        $student = User::factory()->create(['program_id' => $program->id]);
        $ownClass = ProgramClass::create(['program_id' => $program->id, 'name' => 'Own class']);
        $otherClass = ProgramClass::create(['program_id' => $program->id, 'name' => 'Other class']);
        $student->forceFill(['class_id' => $ownClass->id])->save();
        $shared = Homework::create(['program_id' => $program->id, 'title_ar' => 'Shared']);
        $own = Homework::create(['program_id' => $program->id, 'class_id' => $ownClass->id, 'title_ar' => 'Own']);
        $other = Homework::create(['program_id' => $program->id, 'class_id' => $otherClass->id, 'title_ar' => 'Other']);
        $service = app(HomeworkService::class);

        $this->assertEqualsCanonicalizing([$shared->id, $own->id], $service->homeworksForStudentWeb($student)->modelKeys());
        $this->assertEqualsCanonicalizing([$shared->id, $own->id], $service->homeworksForStudentApi($student)->modelKeys());
        $this->assertSame($own->id, $service->findAccessibleForStudent($own->id, $student)->id);
        $this->expectException(ModelNotFoundException::class);
        $service->findAccessibleForStudent($other->id, $student);
    }

    public function test_student_without_class_cannot_see_class_targeted_program_homework(): void
    {
        $program = Program::factory()->create();
        $student = User::factory()->create(['program_id' => $program->id]);
        Homework::create(['program_id' => $program->id, 'class_id' => 123, 'title_ar' => 'Class homework']);
        $this->assertTrue(app(HomeworkService::class)->homeworksForStudentWeb($student)->isEmpty());
        $this->assertTrue(app(HomeworkService::class)->homeworksForStudentApi($student)->isEmpty());
    }

    public function test_new_student_cannot_submit_unrelated_homework_by_direct_url(): void
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'active', 'program_id' => null]);
        $homework = Homework::create(['title_ar' => 'Unassigned homework']);
        $this->actingAs($student)->post(route('student.homework.submit', $homework), ['content' => 'Answer'])
            ->assertNotFound();
        $this->assertDatabaseCount('homework_submissions', 0);
    }
}
