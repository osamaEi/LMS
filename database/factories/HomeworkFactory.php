<?php

namespace Database\Factories;

use App\Models\Homework;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class HomeworkFactory extends Factory
{
    protected $model = Homework::class;

    public function definition(): array
    {
        return [
            'session_id'     => null,
            'subject_id'     => Subject::factory(),
            'program_id'     => null,
            'title_ar'       => 'واجب ' . fake()->words(2, true),
            'title_en'       => 'Homework ' . fake()->words(2, true),
            'description_ar' => fake()->sentence(),
            'description_en' => fake()->sentence(),
            'due_date'       => now()->addWeek()->toDateString(),
            'file_path'      => null,
            'file_name'      => null,
        ];
    }
}
