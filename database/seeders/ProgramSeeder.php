<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'name' => 'Diploma in Information Technology',
                'code' => 'DIT-2025',
                'description' => 'Comprehensive diploma program covering IT fundamentals, programming, and system administration.',
                'type' => 'diploma',
                'duration_months' => 24,
                'price' => 15000.00,
                'status' => 'active',
            ],
            [
                'name' => 'Diploma in Business Administration',
                'code' => 'DBA-2025',
                'description' => 'Business management and administration diploma with focus on modern business practices.',
                'type' => 'diploma',
                'duration_months' => 18,
                'price' => 12000.00,
                'status' => 'active',
            ],
            [
                'name' => 'Web Development Training',
                'code' => 'WDT-2025',
                'description' => 'Intensive training program for full-stack web development.',
                'type' => 'training',
                'duration_months' => 6,
                'price' => 5000.00,
                'status' => 'active',
            ],
            [
                'name' => 'Digital Marketing Training',
                'code' => 'DMT-2025',
                'description' => 'Comprehensive digital marketing training including SEO, social media, and content marketing.',
                'type' => 'training',
                'duration_months' => 4,
                'price' => 4000.00,
                'status' => 'active',
            ],
            [
                'name' => 'Data Analysis Training',
                'code' => 'DAT-2025',
                'description' => 'Data analysis and visualization training with Excel, SQL, and Python.',
                'type' => 'training',
                'duration_months' => 5,
                'price' => 4500.00,
                'status' => 'active',
            ],
        ];

        foreach ($programs as $program) {
            Program::create($program);
        }
    }
}
