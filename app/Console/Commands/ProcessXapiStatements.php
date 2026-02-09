<?php

namespace App\Console\Commands;

use App\Services\Xapi\XapiService;
use Illuminate\Console\Command;

class ProcessXapiStatements extends Command
{
    protected $signature = 'xapi:process {--limit=100 : Number of statements to process}';
    protected $description = 'Process and send pending xAPI statements to LRS';

    public function handle(XapiService $xapiService): int
    {
        if (!config('xapi.enabled', false)) {
            $this->warn('xAPI is disabled. Enable it by setting XAPI_ENABLED=true in .env');
            return Command::FAILURE;
        }

        $limit = (int) $this->option('limit');

        $this->info("Processing up to {$limit} xAPI statements...");

        // Process activity logs
        $processed = $xapiService->processBatch($limit);
        $this->info("✓ Processed {$processed} activity logs into xAPI statements");

        // Send pending
        $result = $xapiService->sendPending($limit);
        $this->info("✓ Sent: {$result['sent']}, Failed: {$result['failed']}");

        // Show stats
        $stats = $xapiService->getStats();
        $this->table(
            ['Status', 'Count'],
            [
                ['Total', $stats['total']],
                ['Pending', $stats['pending']],
                ['Sent', $stats['sent']],
                ['Failed', $stats['failed']],
                ['Retry', $stats['retry']],
            ]
        );

        return Command::SUCCESS;
    }
}
