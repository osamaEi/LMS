<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestNafathConnection extends Command
{
    protected $signature = 'nafath:test {national_id=1003131311}';
    protected $description = 'Test Nafath API connection with raw cURL';

    public function handle()
    {
        $apiUrl = config('services.nafath.api_url', 'https://rabet-nafath.api.elm.sa');
        $appId = config('services.nafath.app_id', '');
        $appKey = config('services.nafath.app_key', '');
        $nationalId = $this->argument('national_id');
        $requestId = \Illuminate\Support\Str::uuid()->toString();

        $endpoint = $apiUrl . '/stg/api/v1/mfa/request?local=ar&requestId=' . $requestId;

        $this->info("=== Nafath API Connection Test ===");
        $this->info("Endpoint: $endpoint");
        $this->info("APP-ID: $appId");
        $this->info("APP-KEY: " . substr($appKey, 0, 8) . '...');
        $this->info("National ID: $nationalId");
        $this->info("Request ID: $requestId");
        $this->newLine();

        // Test 1: Laravel HTTP Client
        $this->info("--- Test 1: Laravel HTTP Client ---");
        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withHeaders([
                    'APP-ID' => $appId,
                    'APP-KEY' => $appKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->post($endpoint, [
                    'nationalId' => $nationalId,
                    'service' => 'OpenAccount',
                ]);

            $this->info("HTTP Status: " . $response->status());
            $this->info("Response Body: " . $response->body());
            $this->info("Response Headers: " . json_encode($response->headers(), JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            $this->error("Laravel HTTP Error: " . $e->getMessage());
        }

        $this->newLine();

        // Test 2: Raw cURL
        $this->info("--- Test 2: Raw cURL ---");
        $requestId2 = \Illuminate\Support\Str::uuid()->toString();
        $endpoint2 = $apiUrl . '/stg/api/v1/mfa/request?local=ar&requestId=' . $requestId2;
        $body = json_encode([
            'nationalId' => $nationalId,
            'service' => 'OpenAccount',
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $endpoint2,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => [
                'APP-ID: ' . $appId,
                'APP-KEY: ' . $appKey,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_VERBOSE => true,
            CURLOPT_HEADER => true,
        ]);

        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $responseHeaders = substr($result, 0, $headerSize);
        $responseBody = substr($result, $headerSize);
        $curlError = curl_error($ch);
        $curlInfo = curl_getinfo($ch);

        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
        fclose($verbose);
        curl_close($ch);

        $this->info("HTTP Status: $httpCode");
        $this->info("Response Body: $responseBody");
        $this->info("Response Headers: $responseHeaders");
        if ($curlError) {
            $this->error("cURL Error: $curlError");
        }
        $this->info("Effective URL: " . $curlInfo['url']);
        $this->info("Primary IP: " . ($curlInfo['primary_ip'] ?? 'N/A'));
        $this->info("SSL Verify Result: " . ($curlInfo['ssl_verify_result'] ?? 'N/A'));

        $this->newLine();
        $this->info("--- Verbose cURL Log ---");
        $this->line($verboseLog);

        // Test 3: Try without /stg/ prefix
        $this->newLine();
        $this->info("--- Test 3: Without /stg/ prefix ---");
        $requestId3 = \Illuminate\Support\Str::uuid()->toString();
        $endpoint3 = $apiUrl . '/api/v1/mfa/request?local=ar&requestId=' . $requestId3;
        $this->info("Endpoint: $endpoint3");

        try {
            $response3 = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withHeaders([
                    'APP-ID' => $appId,
                    'APP-KEY' => $appKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->post($endpoint3, [
                    'nationalId' => $nationalId,
                    'service' => 'OpenAccount',
                ]);

            $this->info("HTTP Status: " . $response3->status());
            $this->info("Response Body: " . $response3->body());
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }

        return 0;
    }
}
