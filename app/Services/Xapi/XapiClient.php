<?php

namespace App\Services\Xapi;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XapiClient
{
    private ?string $endpoint;
    private ?string $username;
    private ?string $password;
    private string $version;

    public function __construct()
    {
        $this->endpoint = config('xapi.lrs_endpoint');
        $this->username = config('xapi.lrs_username');
        $this->password = config('xapi.lrs_password');
        $this->version = config('xapi.version', '1.0.3');
    }

    /**
     * Send a single xAPI statement to the LRS
     */
    public function sendStatement(array $statement): bool
    {
        if (!$this->isConfigured()) {
            Log::warning('xAPI LRS not configured. Skipping statement send.');
            return false;
        }

        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->withHeaders([
                    'X-Experience-API-Version' => $this->version,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->endpoint . '/statements', $statement);

            if ($response->successful()) {
                Log::info('xAPI statement sent successfully', [
                    'statement_id' => $statement['id'] ?? null,
                ]);
                return true;
            }

            Log::error('Failed to send xAPI statement', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Exception sending xAPI statement: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return false;
        }
    }

    /**
     * Send multiple xAPI statements in a batch
     */
    public function sendStatements(array $statements): array
    {
        if (!$this->isConfigured()) {
            Log::warning('xAPI LRS not configured. Skipping batch send.');
            return [
                'success' => false,
                'sent' => 0,
                'failed' => count($statements),
            ];
        }

        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->withHeaders([
                    'X-Experience-API-Version' => $this->version,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->endpoint . '/statements', $statements);

            if ($response->successful()) {
                Log::info('xAPI batch sent successfully', [
                    'count' => count($statements),
                ]);

                return [
                    'success' => true,
                    'sent' => count($statements),
                    'failed' => 0,
                ];
            }

            Log::error('Failed to send xAPI batch', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'sent' => 0,
                'failed' => count($statements),
                'error' => $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('Exception sending xAPI batch: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'sent' => 0,
                'failed' => count($statements),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get statements from LRS (for verification/debugging)
     */
    public function getStatements(array $filters = []): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->withHeaders([
                    'X-Experience-API-Version' => $this->version,
                ])
                ->get($this->endpoint . '/statements', $filters);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Exception getting xAPI statements: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get state for an activity (LRS state API)
     */
    public function getState(string $activityId, string $agent, string $stateId): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->withHeaders([
                    'X-Experience-API-Version' => $this->version,
                ])
                ->get($this->endpoint . '/activities/state', [
                    'activityId' => $activityId,
                    'agent' => $agent,
                    'stateId' => $stateId,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Exception getting xAPI state: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Set state for an activity (LRS state API)
     */
    public function setState(string $activityId, string $agent, string $stateId, array $data): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->withHeaders([
                    'X-Experience-API-Version' => $this->version,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->endpoint . '/activities/state', $data, [
                    'activityId' => $activityId,
                    'agent' => $agent,
                    'stateId' => $stateId,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Exception setting xAPI state: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Test LRS connection
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'xAPI LRS is not configured. Please set XAPI_LRS_ENDPOINT, XAPI_LRS_USERNAME, and XAPI_LRS_PASSWORD in your .env file.',
            ];
        }

        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->withHeaders([
                    'X-Experience-API-Version' => $this->version,
                ])
                ->get($this->endpoint . '/about');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Successfully connected to xAPI LRS',
                    'lrs_info' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to connect to xAPI LRS',
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Exception connecting to xAPI LRS: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check if LRS is configured
     */
    private function isConfigured(): bool
    {
        return !empty($this->endpoint) && !empty($this->username) && !empty($this->password);
    }
}
