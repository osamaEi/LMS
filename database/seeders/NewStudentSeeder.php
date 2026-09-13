<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Creates one bare demo student: the user row only (role student, active,
 * confirmed, email verified).
 *
 * No program, class, term or enrollments are assigned — the student lands in
 * the same unassigned state as a fresh signup, ready to be placed into a
 * program through the admin panel.
 *
 * Options can be passed before running:
 *   (new NewStudentSeeder)->setOptions(['email' => 'x@y.z'])->run();
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
        // phone/national_id are unique across trashed rows too, so walk the
        // suffix forward until none of them is taken.
        $suffix = (string) $this->freeSuffix();

        $email = $this->options['email'] ?? "student{$suffix}@demo.test";
        $this->plainPassword = $this->options['password'] ?? 'password';

        // Reuse the row when the email already exists — including a trashed one,
        // which still holds the unique email/phone/national_id.
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

        $this->student = $student->fresh();

        $this->command?->info("Student created: {$email} / {$this->plainPassword} (ID {$this->student->id})");
    }

    /**
     * First suffix N (starting past the highest user id, trashed included)
     * whose derived demo email / phone / national_id are all still free.
     */
    protected function freeSuffix(): int
    {
        $n = (int) User::withTrashed()->max('id') + 1;

        while (User::withTrashed()->where(function ($q) use ($n) {
            $q->where('email', "student{$n}@demo.test")
              ->orWhere('phone', '05' . str_pad((string) $n, 8, '0', STR_PAD_LEFT))
              ->orWhere('national_id', '1' . str_pad((string) $n, 9, '0', STR_PAD_LEFT));
        })->exists()) {
            $n++;
        }

        return $n;
    }
}
