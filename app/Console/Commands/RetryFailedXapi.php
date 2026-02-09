<?php

namespace App\Console\Commands;

use App\Services\Xapi\XapiService;
use Illuminate\Console\Command;

class RetryFailedXapi extends Command
{
    protected $signature = 'xapi:retry-failed';
    protected $description = 'Retry sending failed xAPI statements';

    public function handle(XapiService $xapiService): int
    {
        if (!config('xapi.enabled', false)) {
            $this->warn('xAPI is disabled.');
            return Command::FAILURE;
        }

        $this->info('Retrying failed xAPI statements...');

        $result = $xapiService->retryFailed();

        $this->info("✓ Retry complete: Sent: {$result['sent']}, Failed: {$result['failed']}");

        return Command::SUCCESS;
    }
}
