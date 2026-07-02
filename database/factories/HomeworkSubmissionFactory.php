<?php

namespace Database\Factories;

use App\Models\HomeworkSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

class HomeworkSubmissionFactory extends Factory
{
    protected $model = HomeworkSubmission::class;

    public function definition(): array
    {
        return [
            'homework_id'  => null,
            'student_id'   => null,
            'content'      => fake()->sentence(),
            'file_path'    => null,
            'file_name'    => null,
            'submitted_at' => now(),
            'grade'        => null,
            'feedback'     => null,
        ];
    }
}
