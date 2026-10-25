<?php

namespace Tests\Feature;

use App\Models\{AttendanceApology, Program, ProgramClass, Session, Subject, Term, User};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
use Tests\Feature\Homework\HomeworkTestCase;

class EndedPeriodDeactivationTest extends HomeworkTestCase
{
    protected function buildSchema(): void
    {
        config(['database.default' => 'ended_period_test', 'database.connections.ended_period_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('ended_period_test');
        parent::buildSchema();
        Schema::table('terms', function (Blueprint $table) {
            $table->unsignedBigInteger('class_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
        });
        Schema::table('program_classes', function (Blueprint $table) {
            $table->date('end_date')->nullable();
        });
        Schema::create('attendance_apologies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('session_id');
            $table->text('reason')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('status')->default('pending');
            $table->text('review_note')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('session_id');
            $table->boolean('attended')->default(false);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    private function scenario(): array
    {
        $this->travelTo(now()->setDate(2026, 10, 25)->setTime(10, 0));

        $running = Term::create(['start_date' => '2026-10-01', 'end_date' => '2026-12-31']);
        $ended = Term::create(['start_date' => '2026-07-01', 'end_date' => '2026-10-24']);
        $deactivated = Term::create(['start_date' => '2026-10-01', 'end_date' => '2026-12-31', 'status' => 'completed']);
        $deleted = Term::create(['start_date' => '2026-10-01', 'end_date' => '2026-12-31']);
        $deleted->delete();

        $subjects = [
            'running'     => Subject::create(['name_ar' => 'running', 'term_id' => $running->id]),
            'ended'       => Subject::create(['name_ar' => 'ended', 'term_id' => $ended->id]),
            'deactivated' => Subject::create(['name_ar' => 'deactivated', 'term_id' => $deactivated->id]),
            'deleted'     => Subject::create(['name_ar' => 'deleted', 'term_id' => $deleted->id]),
            // Shared across quarters: stays active while any of them runs.
            'shared'      => Subject::create(['name_ar' => 'shared', 'term_id' => $ended->id]),
            'no_term'     => Subject::create(['name_ar' => 'no term']),
        ];
        $subjects['shared']->terms()->attach([$ended->id, $running->id]);

        $course = Program::create(['name_ar' => 'course', 'type' => 'course']);
        $diploma = Program::create(['name_ar' => 'diploma', 'type' => 'diploma']);
        $classes = [
            'course_open'   => ProgramClass::create(['program_id' => $course->id, 'name' => 'open', 'end_date' => '2026-10-25']),
            'course_ended'  => ProgramClass::create(['program_id' => $course->id, 'name' => 'ended', 'end_date' => '2026-10-24']),
            'diploma_ended' => ProgramClass::create(['program_id' => $diploma->id, 'name' => 'diploma', 'end_date' => '2026-10-24']),
        ];

        return [$subjects, $classes, $course];
    }

    public function test_command_deactivates_subjects_of_ended_quarters_and_ended_courses(): void
    {
        [$subjects, $classes] = $this->scenario();

        $this->artisan('academics:deactivate-ended')->assertSuccessful();

        $status = fn ($model) => $model->fresh()->status;
        $this->assertSame('active', $status($subjects['running']));
        $this->assertSame('inactive', $status($subjects['ended']));
        $this->assertSame('inactive', $status($subjects['deactivated']));
        $this->assertSame('inactive', $status($subjects['deleted']));
        $this->assertSame('active', $status($subjects['shared']));
        $this->assertSame('active', $status($subjects['no_term']));
        $this->assertSame('active', $status($classes['course_open']));
        $this->assertSame('inactive', $status($classes['course_ended']));
        // Diploma classes end per quarter, not per class date.
        $this->assertSame('active', $status($classes['diploma_ended']));
    }

    public function test_excuses_of_ended_periods_are_closed_and_hidden(): void
    {
        [$subjects, $classes, $course] = $this->scenario();
        $student = User::factory()->create(['role' => 'student']);

        $sessions = collect([
            'running'      => Session::create(['subject_id' => $subjects['running']->id]),
            'ended'        => Session::create(['subject_id' => $subjects['ended']->id]),
            'deactivated'  => Session::create(['subject_id' => $subjects['deactivated']->id]),
            'deleted'      => Session::create(['subject_id' => $subjects['deleted']->id]),
            'shared'       => Session::create(['subject_id' => $subjects['shared']->id]),
            'course_open'  => Session::create(['program_id' => $course->id, 'class_id' => $classes['course_open']->id]),
            'course_ended' => Session::create(['program_id' => $course->id, 'class_id' => $classes['course_ended']->id]),
        ]);
        $sessions->each(fn ($s) => AttendanceApology::create(['student_id' => $student->id, 'session_id' => $s->id, 'reason' => 'sick']));

        $closed = $sessions->map->apologiesClosed()->filter()->keys()->all();
        $this->assertEqualsCanonicalizing(['ended', 'deactivated', 'deleted', 'course_ended'], $closed);

        $visible = AttendanceApology::inOpenPeriod()->pluck('session_id')->all();
        $this->assertEqualsCanonicalizing($sessions->only(['running', 'shared', 'course_open'])->pluck('id')->all(), $visible);

        // Submitting a new excuse for an ended quarter is refused.
        AttendanceApology::query()->delete();
        $this->actingAs($student)
            ->post("/student/sessions/{$sessions['ended']->id}/apology", ['reason' => 'I was sick'])
            ->assertSessionHas('error');
        $this->assertSame(0, AttendanceApology::count());
    }
}
