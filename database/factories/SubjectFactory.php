<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        return [
            'program_id' => null,
            'term_id'    => null,
            'name_ar'    => 'مادة ' . fake()->words(2, true),
            'name_en'    => 'Subject ' . fake()->words(2, true),
            'code'       => strtoupper(fake()->unique()->bothify('SUB-###')),
            'status'     => 'active',
        ];
    }
}
