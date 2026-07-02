<?php

namespace Database\Factories;

use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        return [
            'student_id'  => null,
            'subject_id'  => null,
            'enrolled_at' => now(),
            'status'      => 'active',
        ];
    }
}
