<?php

namespace Tests\Feature\Teacher;

use App\Http\Controllers\Teacher\SubjectController;
use App\Models\{Subject, SubjectFile, Term, User};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Schema, Storage};
use Tests\Feature\Homework\HomeworkTestCase;

class CurrentQuarterSubjectsTest extends HomeworkTestCase
{
    protected function buildSchema(): void
    {
        config(['database.default' => 'quarter_test', 'database.connections.quarter_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('quarter_test');
        parent::buildSchema();
        Schema::table('terms', function (Blueprint $table) {
            $table->unsignedBigInteger('class_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
        });
        Schema::create('subject_teacher', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('teacher_id');
            $table->timestamps();
        });
        Schema::create('subject_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id');
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function test_only_assigned_current_quarter_subjects_appear_for_both_term_relations(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 13)->setTime(23, 59));
        $teacher = User::factory()->create(['role' => 'teacher']);
        $this->actingAs($teacher);
        $current = Term::create(['name_ar' => 'Current', 'class_id' => 10, 'start_date' => '2026-10-13', 'end_date' => '2026-10-13']);
        $past = Term::create(['name_ar' => 'Past', 'class_id' => 20, 'start_date' => '2026-07-01', 'end_date' => '2026-10-12']);
        $future = Term::create(['start_date' => '2026-10-14', 'end_date' => '2026-12-31']);
        $inactive = Term::create(['start_date' => '2026-10-01', 'end_date' => '2026-12-31', 'status' => 'completed']);
        $direct = Subject::create(['name_ar' => 'Direct', 'term_id' => $current->id]);
        $shared = Subject::create(['name_ar' => 'Shared', 'term_id' => $past->id]);
        $shared->terms()->attach([$current->id, $past->id]);
        $excluded = collect([$past, $future, $inactive])->map(fn ($term) => Subject::create(['name_ar' => 'Excluded', 'term_id' => $term->id]));
        $noTerm = Subject::create(['name_ar' => 'No term']);
        Subject::create(['name_ar' => 'Other teacher', 'term_id' => $current->id]);
        $teacher->assignedSubjects()->attach([$direct->id, $shared->id, $noTerm->id, ...$excluded->pluck('id')->all()]);

        $data = app(SubjectController::class)->index(Request::create('/teacher/my-subjects'))->getData();
        $this->assertEqualsCanonicalizing([$direct->id, $shared->id], $data['subjects']->modelKeys());
        $this->assertSame([10], $data['subjectClassIds'][$shared->id]->all());
        $this->assertNull($data['subjects']->find($shared->id)->term);
        $this->assertSame([$current->id], $data['subjects']->find($shared->id)->terms->modelKeys());

        $filtered = app(SubjectController::class)->index(Request::create('/teacher/my-subjects', 'GET', ['class_id' => 20]))->getData();
        $this->assertCount(0, $filtered['subjects']);
        $this->travelBack();
    }

    public function test_file_deletion_requires_assignment_and_removes_the_stored_file(): void
    {
        Storage::fake('public');
        $teacher = User::factory()->create(['role' => 'teacher']);
        $subject = Subject::create(['name_ar' => 'Subject']);
        $file = SubjectFile::create(['subject_id' => $subject->id, 'file_path' => 'subject-files/worksheet.doc']);
        Storage::disk('public')->put($file->file_path, 'worksheet');
        $this->actingAs($teacher)->delete(route('teacher.files.destroy', $file))->assertNotFound();
        Storage::disk('public')->assertExists($file->file_path);
        $this->assertDatabaseHas('subject_files', ['id' => $file->id]);

        $teacher->assignedSubjects()->attach($subject);
        $this->from(route('teacher.my-subjects.show', $subject->id))
            ->delete(route('teacher.files.destroy', $file))
            ->assertRedirect(route('teacher.my-subjects.show', $subject->id))
            ->assertSessionHas('success');
        Storage::disk('public')->assertMissing($file->file_path);
        $this->assertDatabaseMissing('subject_files', ['id' => $file->id]);
    }
}
