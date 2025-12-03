<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Track;
use App\Models\Term;
use Illuminate\Database\Seeder;

class TrackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على البرامج الموجودة
        $programs = Program::all();

        if ($programs->isEmpty()) {
            $this->command->warn('No programs found. Please create programs first.');
            return;
        }

        foreach ($programs as $index => $program) {
            // إنشاء مسار واحد لكل برنامج
            $track = Track::create([
                'program_id' => $program->id,
                'name' => "مسار {$program->name} الأساسي",
                'code' => 'TRACK-' . str_pad($program->id, 3, '0', STR_PAD_LEFT) . '-01',
                'description' => "المسار الأساسي لبرنامج {$program->name} يتكون من 10 أرباع دراسية",
                'total_terms' => 10,
                'duration_months' => 30, // 10 أرباع × 3 أشهر
                'status' => 'active',
            ]);

            // إنشاء 10 أرباع للمسار
            $this->createTermsForTrack($track);

            $this->command->info("Track created for program: {$program->name}");
        }

        $this->command->info('Tracks seeded successfully!');
    }

    /**
     * إنشاء 10 أرباع للمسار
     */
    protected function createTermsForTrack(Track $track): void
    {
        $startDate = now();
        $durationMonths = 3; // مدة كل ربع 3 أشهر

        for ($i = 1; $i <= 10; $i++) {
            // حساب تواريخ الربع
            $termStartDate = $startDate->copy()->addMonths(($i - 1) * $durationMonths);
            $termEndDate = $termStartDate->copy()->addMonths($durationMonths)->subDay();

            // تواريخ التسجيل (قبل بداية الربع بأسبوعين وتنتهي قبل 3 أيام من البداية)
            $registrationStart = $termStartDate->copy()->subWeeks(2);
            $registrationEnd = $termStartDate->copy()->subDays(3);

            // تحديد حالة الربع
            $status = match (true) {
                $i === 1 => 'active',
                $i === 2 => 'upcoming',
                default => 'upcoming',
            };

            Term::create([
                'program_id' => $track->program_id,
                'track_id' => $track->id,
                'term_number' => $i,
                'name' => "الربع {$i} - {$track->name}",
                'start_date' => $termStartDate,
                'end_date' => $termEndDate,
                'registration_start_date' => $registrationStart,
                'registration_end_date' => $registrationEnd,
                'status' => $status,
            ]);
        }
    }
}
