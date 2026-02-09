<?php

namespace App\Services\Xapi;

use App\Models\ActivityLog;
use App\Models\XapiStatement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class XapiService
{
    public function __construct(
        private XapiStatementBuilder $builder,
        private XapiClient $client,
    ) {}

    /**
     * Process activity log into xAPI statement (but don't send yet)
     */
    public function processActivityLog(ActivityLog $log): ?XapiStatement
    {
        if (!config('xapi.enabled', false)) {
            return null;
        }

        try {
            $statementData = $this->builder->build($log);

            $statement = XapiStatement::create([
                'activity_log_id' => $log->id,
                'user_id' => $log->user_id,
                'verb_id' => $statementData['verb']['id'],
                'verb_display' => $statementData['verb']['display']['en-US'] ?? 'unknown',
                'object_type' => $statementData['object']['objectType'] ?? 'Activity',
                'object_id' => $statementData['object']['id'],
                'object_name' => $statementData['object']['definition']['name']['en-US'] ?? null,
                'statement_json' => $statementData,
                'status' => 'pending',
            ]);

            return $statement;
        } catch (\Exception $e) {
            Log::error('Failed to process activity log to xAPI: ' . $e->getMessage(), [
                'activity_log_id' => $log->id,
                'exception' => $e,
            ]);
            return null;
        }
    }

    /**
     * Process batch of activity logs
     */
    public function processBatch(int $limit = 50): int
    {
        if (!config('xapi.enabled', false)) {
            return 0;
        }

        $logs = ActivityLog::notSentToXapi()
            ->oldest()
            ->limit($limit)
            ->get();

        $processed = 0;
        foreach ($logs as $log) {
            if ($this->processActivityLog($log)) {
                $processed++;
            }
        }

        return $processed;
    }

    /**
     * Send a single xAPI statement to LRS
     */
    public function sendStatement(XapiStatement $statement): bool
    {
        if (!config('xapi.enabled', false)) {
            return false;
        }

        try {
            $success = $this->client->sendStatement($statement->statement_json);

            if ($success) {
                $statement->markAsSent();
                return true;
            }

            $statement->markAsFailed('Failed to send statement to LRS');
            return false;
        } catch (\Exception $e) {
            $statement->markAsFailed($e->getMessage());
            return false;
        }
    }

    /**
     * Send batch of xAPI statements
     */
    public function sendBatch(Collection $statements): array
    {
        if (!config('xapi.enabled', false)) {
            return ['sent' => 0, 'failed' => $statements->count()];
        }

        $statementData = $statements->map(fn($s) => $s->statement_json)->toArray();

        try {
            $result = $this->client->sendStatements($statementData);

            if ($result['success']) {
                // Mark all as sent
                foreach ($statements as $statement) {
                    $statement->markAsSent();
                }

                return [
                    'sent' => $result['sent'],
                    'failed' => 0,
                ];
            }

            // Mark all as failed
            foreach ($statements as $statement) {
                $statement->markAsFailed($result['error'] ?? 'Batch send failed');
            }

            return [
                'sent' => 0,
                'failed' => $statements->count(),
            ];
        } catch (\Exception $e) {
            foreach ($statements as $statement) {
                $statement->markAsFailed($e->getMessage());
            }

            return [
                'sent' => 0,
                'failed' => $statements->count(),
            ];
        }
    }

    /**
     * Send pending xAPI statements
     */
    public function sendPending(int $limit = 50): array
    {
        if (!config('xapi.enabled', false)) {
            return ['sent' => 0, 'failed' => 0];
        }

        $batchSize = config('xapi.batch_size', 50);
        $statements = XapiStatement::pending()
            ->limit($limit)
            ->get();

        $sent = 0;
        $failed = 0;

        // Send in batches
        foreach ($statements->chunk($batchSize) as $batch) {
            $result = $this->sendBatch($batch);
            $sent += $result['sent'];
            $failed += $result['failed'];
        }

        return compact('sent', 'failed');
    }

    /**
     * Retry failed xAPI statements
     */
    public function retryFailed(): array
    {
        if (!config('xapi.enabled', false)) {
            return ['sent' => 0, 'failed' => 0];
        }

        $statements = XapiStatement::retry()->get();

        $sent = 0;
        $failed = 0;

        foreach ($statements as $statement) {
            if ($this->sendStatement($statement)) {
                $sent++;
            } else {
                $failed++;
            }
        }

        return compact('sent', 'failed');
    }

    /**
     * Get xAPI statistics
     */
    public function getStats(): array
    {
        return [
            'total' => XapiStatement::count(),
            'pending' => XapiStatement::pending()->count(),
            'sent' => XapiStatement::sent()->count(),
            'failed' => XapiStatement::failed()->count(),
            'retry' => XapiStatement::retry()->count(),
        ];
    }
}
