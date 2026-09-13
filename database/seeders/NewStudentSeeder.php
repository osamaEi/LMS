<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Creates one ready-to-use demo student:
 *  - user row (role student, active, confirmed)
 *  - primary program + class + term on the user and in student_programs
 *  - enrollments for every subject of the active term
 *
 * Options can be passed before running:
 *   (new NewStudentSeeder)->setOptions(['email' => 'x@y.z'])->run();
 *
 * Sessions are not created here — they belong to the class, so the student
 * picks up whatever the class already has scheduled.
 */
class NewStudentSeeder extends Seeder
{
    /** @var array<string, mixed> */
    protected array $options = [];

    /** The student created by the last run(). */
    public ?User $student = null;

    /** The plain password used, so the caller can display it. */
    public string $plainPassword = 'password';

    /** @param array<string, mixed> $options */
    public function setOptions(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function run(): void
    {
        $suffix = (string) (User::withTrashed()->max('id') + 1);

        $email = $this->options['email'] ?? "student{$suffix}@demo.test";
        $this->plainPassword = $this->options['password'] ?? 'password';

        $student = User::withTrashed()->firstOrNew(['email' => $email]);

        $student->fill([
            'name'             => $this->options['name'] ?? "طالب تجريبي {$suffix}",
            'password'         => Hash::make($this->plainPassword),
            'phone'            => $this->options['phone'] ?? '05' . str_pad($suffix, 8, '0', STR_PAD_LEFT),
            'national_id'      => $this->options['national_id'] ?? '1' . str_pad($suffix, 9, '0', STR_PAD_LEFT),
            'date_of_birth'    => $this->options['date_of_birth'] ?? '2000-01-01',
            'gender'           => $this->options['gender'] ?? 'male',
            'nationality'      => $this->options['nationality'] ?? 'سعودي',
            'role'             => 'student',
            'status'           => 'active',
            'is_terms'         => 1,
            'is_confirm_user'  => 1,
            'date_of_register' => now()->toDateString(),
        ]);

        $student->deleted_at = null;
        $student->email_verified_at = now();
        $student->save();

        // Spatie role, when the roles table has been seeded.
        if (class_exists(\Spatie\Permission\Models\Role::class)
            && \Spatie\Permission\Models\Role::where('name', 'student')->exists()) {
            $student->syncRoles(['student']);
        }

        $this->attachProgram($student);

        $this->student = $student->fresh();

        $this->command?->info("Student created: {$email} / {$this->plainPassword} (ID {$this->student->id})");
    }

    /**
     * Put the student into a program + class + term and enroll them in that
     * term's subjects. Silently skips whatever data does not exist yet.
     */
    protected function attachProgram(User $student): void
    {
        $program = isset($this->options['program_id'])
            ? Program::find($this->options['program_id'])
            : Program::query()->orderBy('id')->first();

        if (! $program) {
            $this->command?->warn('No program found — student created without a program.');

            return;
        }

        $term = Term::where('program_id', $program->id)
            ->orderBy('term_number')
            ->first();

        $termNumber = $term->term_number ?? 1;

        $class = ProgramClass::where('program_id', $program->id)->orderBy('id')->first()
            ?? ProgramClass::create([
                'name'       => "فصل تجريبي - {$program->id}",
                'program_id' => $program->id,
            ]);

        // class_id is not mass assignable on User — set it directly.
        $student->forceFill([
            'program_id'          => $program->id,
            'program_status'      => 'approved',
            'class_id'            => $class->id,
            'current_term_number' => $termNumber,
        ])->save();

        DB::table('student_programs')->updateOrInsert(
            ['student_id' => $student->id, 'program_id' => $program->id],
            [
                'status'              => 'approved',
                'class_id'            => $class->id,
                'current_term_number' => $termNumber,
                'enrolled_at'         => now()->toDateString(),
                'created_at'          => now(),
                'updated_at'          => now(),
            ]
        );

        if (! $term) {
            return;
        }

        foreach (Subject::where('term_id', $term->id)->get() as $subject) {
            Enrollment::withTrashed()->updateOrCreate(
                ['student_id' => $student->id, 'subject_id' => $subject->id],
                ['status' => 'active', 'enrolled_at' => now(), 'deleted_at' => null]
            );
        }
    }
}
