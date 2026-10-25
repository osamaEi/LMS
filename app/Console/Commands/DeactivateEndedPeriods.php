<?php

namespace App\Console\Commands;

use App\Models\ProgramClass;
use App\Models\Subject;
use Illuminate\Console\Command;

class DeactivateEndedPeriods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'academics:deactivate-ended';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate subjects whose quarter has ended and course classes past their end date';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $subjects = Subject::where('status', 'active')->quarterEnded()->update(['status' => 'inactive']);

        // Courses (course / english / training) end with their class end date.
        $classes = ProgramClass::where('status', 'active')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<', today())
            ->whereHas('program', fn ($q) => $q->whereIn('type', ['course', 'english', 'training']))
            ->update(['status' => 'inactive']);

        $this->info("Deactivated {$subjects} subjects and {$classes} course classes.");

        return Command::SUCCESS;
    }
}
