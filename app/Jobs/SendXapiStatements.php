<?php

namespace App\Jobs;

use App\Services\Xapi\XapiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendXapiStatements implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $limit = 50
    ) {}

    public function handle(XapiService $xapiService): void
    {
        if (!config('xapi.enabled', false)) {
            return;
        }

        Log::info('Processing xAPI statements job', ['limit' => $this->limit]);

        // Process activity logs into xAPI statements
        $processed = $xapiService->processBatch($this->limit);
        Log::info("Processed {$processed} activity logs into xAPI statements");

        // Send pending statements
        $result = $xapiService->sendPending($this->limit);
        Log::info("Sent xAPI statements", $result);
    }
}
