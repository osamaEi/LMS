<?php

namespace Database\Factories;

use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionFactory extends Factory
{
    protected $model = Session::class;

    public function definition(): array
    {
        return [
            'subject_id'     => null,
            'program_id'     => null,
            'teacher_id'     => null,
            'title_ar'       => 'جلسة ' . fake()->words(2, true),
            'title_en'       => 'Session ' . fake()->words(2, true),
            'session_number' => fake()->numberBetween(1, 20),
            'type'           => 'live_zoom',
            'status'         => 'scheduled',
            'scheduled_at'   => now(),
        ];
    }
}
