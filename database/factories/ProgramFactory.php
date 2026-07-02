<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        return [
            'name_ar' => 'برنامج ' . fake()->unique()->words(2, true),
            'name_en' => 'Program ' . fake()->unique()->words(2, true),
            'code'    => strtoupper(fake()->unique()->bothify('PRG-###')),
            'type'    => 'diploma',
            'status'  => 'active',
        ];
    }
}
