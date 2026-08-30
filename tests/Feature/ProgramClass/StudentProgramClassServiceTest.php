<?php

namespace Tests\Feature\ProgramClass;

use App\Models\ProgramClass;
use App\Models\User;
use App\Services\ProgramClassService;
use App\Services\StudentProgramClassService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StudentProgramClassServiceTest extends TestCase
{
    private StudentProgramClassService $service;

    private ProgramClassService $programClassService;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');

        $this->createTables();
        $this->service = app(StudentProgramClassService::class);
        $this->programClassService = app(ProgramClassService::class);
    }

    public function test_it_assigns_an_eligible_student_to_the_class_and_legacy_column(): void
    {
        [$programId, $programClass] = $this->programAndClass();
        $student = $this->student($programId);

        $assigned = $this->service->assignStudents($programClass, [$student->id]);

        $this->assertSame(1, $assigned);
        $this->assertDatabaseHas('student_programs', [
            'student_id' => $student->id,
            'program_id' => $programId,
            'class_id' => $programClass->id,
        ]);
        $this->assertSame($programClass->id, $student->fresh()->class_id);
    }

    public function test_it_rejects_assignments_to_an_inactive_class(): void
    {
        [$programId, $programClass] = $this->programAndClass(status: 'inactive');
        $student = $this->student($programId);

        $this->expectException(ValidationException::class);

        $this->service->assignStudents($programClass, [$student->id]);
    }

    public function test_it_rejects_the_whole_assignment_when_capacity_would_be_exceeded(): void
    {
        [$programId, $programClass] = $this->programAndClass(maxStudents: 1);
        $first = $this->student($programId);
        $second = $this->student($programId);
        $this->service->assignStudents($programClass, [$first->id]);

        try {
            $this->service->assignStudents($programClass, [$second->id]);
            $this->fail('Expected the class capacity validation to fail.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('student_ids', $exception->errors());
        }

        $this->assertDatabaseMissing('student_programs', [
            'student_id' => $second->id,
            'class_id' => $programClass->id,
        ]);
    }

    public function test_soft_deleting_a_class_clears_pivot_and_legacy_assignments(): void
    {
        [$programId, $programClass] = $this->programAndClass();
        $student = $this->student($programId);
        $this->service->assignStudents($programClass, [$student->id]);

        $this->programClassService->delete($programClass);

        $this->assertNull(DB::table('student_programs')->where('student_id', $student->id)->value('class_id'));
        $this->assertNull($student->fresh()->class_id);
        $this->assertNotNull(ProgramClass::withTrashed()->findOrFail($programClass->id)->deleted_at);
    }

    private function programAndClass(string $status = 'active', ?int $maxStudents = null): array
    {
        $programId = DB::table('programs')->insertGetId([
            'name_ar' => 'برنامج تجريبي',
            'type' => 'diploma',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $programClass = ProgramClass::create([
            'program_id' => $programId,
            'name' => 'المجموعة أ',
            'status' => $status,
            'max_students' => $maxStudents,
        ]);

        return [$programId, $programClass];
    }

    private function student(int $programId): User
    {
        $id = DB::table('users')->insertGetId([
            'name' => 'طالب '.uniqid(),
            'email' => uniqid().'@example.test',
            'password' => 'password',
            'role' => 'student',
            'status' => 'active',
            'program_id' => $programId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::findOrFail($id);
    }

    private function createTables(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('program_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->string('name');
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
            $table->integer('max_students')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('student');
            $table->string('status')->default('active');
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('student_programs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('program_id');
            $table->string('status')->default('approved');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedTinyInteger('current_term_number')->default(1);
            $table->date('enrolled_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'program_id']);
        });
    }
}
