<?php

namespace App\Console\Commands;

use App\Services\ActivityLogService;
use Illuminate\Console\Command;

class CleanOldActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity-logs:clean {--months=12 : Number of months to retain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old activity logs that have been synced to xAPI';

    /**
     * Execute the console command.
     */
    public function handle(ActivityLogService $activityLogService): int
    {
        $retentionMonths = (int) $this->option('months');

        $this->info("Cleaning activity logs older than {$retentionMonths} months...");

        $deletedCount = $activityLogService->cleanOldLogs($retentionMonths);

        $this->info("Deleted {$deletedCount} old activity logs.");

        return Command::SUCCESS;
    }
}
