<?php

namespace App\Services;

use Exception;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZoomService
{
    private ?string $apiKey = null;
    private ?string $apiSecret = null;
    private ?string $accountId = null;
    private ?string $clientId = null;
    private ?string $clientSecret = null;
    private ?string $sdkKey = null;
    private ?string $sdkSecret = null;
    private string $baseUrl = 'https://api.zoom.us/v2';
    private ?string $accessToken = null;

    public function __construct()
    {
        $this->apiKey = config('services.zoom.api_key', '');
        $this->apiSecret = config('services.zoom.api_secret', '');
        $this->accountId = config('services.zoom.account_id', '');
        $this->clientId = config('services.zoom.client_id', '');
        $this->clientSecret = config('services.zoom.client_secret', '');
        $this->sdkKey = config('services.zoom.sdk_key', '');
        $this->sdkSecret = config('services.zoom.sdk_secret', '');
    }

    /**
     * Get OAuth access token for Server-to-Server OAuth
     */
    private function getAccessToken(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        try {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->asForm()
                ->post('https://zoom.us/oauth/token', [
                    'grant_type' => 'account_credentials',
                    'account_id' => $this->accountId,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'];
                return $this->accessToken;
            }

            throw new Exception('Failed to get Zoom access token: ' . $response->body());
        } catch (Exception $e) {
            Log::error('Zoom OAuth error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a Zoom meeting
     *
     * @param array $meetingData
     * @return array|null
     */
    public function createMeeting(array $meetingData): ?array
    {
        try {
            $token = $this->getAccessToken();

            $payload = [
                'topic' => $meetingData['topic'] ?? 'LMS Session',
                'type' => $meetingData['type'] ?? 2, // 1=Instant, 2=Scheduled, 3=Recurring no fixed time, 8=Recurring with fixed time
                'start_time' => $meetingData['start_time'] ?? now()->addHour()->toIso8601String(),
                'duration' => $meetingData['duration'] ?? 60, // Duration in minutes
                'timezone' => $meetingData['timezone'] ?? 'Asia/Riyadh',
                'agenda' => $meetingData['agenda'] ?? '',
                'settings' => [
                    'host_video' => $meetingData['host_video'] ?? true,
                    'participant_video' => $meetingData['participant_video'] ?? true,
                    'join_before_host' => $meetingData['join_before_host'] ?? true, // Allow joining without host
                    'mute_upon_entry' => $meetingData['mute_upon_entry'] ?? false, // Don't mute on entry
                    'watermark' => $meetingData['watermark'] ?? false,
                    'audio' => $meetingData['audio'] ?? 'voip', // voip, telephony, both
                    'auto_recording' => $meetingData['auto_recording'] ?? 'cloud', // none, local, cloud - ALWAYS record to cloud
                    'waiting_room' => $meetingData['waiting_room'] ?? false, // Disabled for easier access
                    'approval_type' => $meetingData['approval_type'] ?? 2, // 0=Automatically approve, 1=Manually approve, 2=No registration required
                    // Chat settings
                    'meeting_chat' => true, // Enable in-meeting chat
                    'allow_participants_chat_with' => 2, // 1=Host only, 2=Everyone, 3=No one
                    'allow_participants_save_chats' => true, // Allow saving chat
                ],
            ];

            // Add password if provided
            if (isset($meetingData['password'])) {
                $payload['password'] = $meetingData['password'];
            }

            $response = Http::withToken($token)
                ->post($this->baseUrl . '/users/me/meetings', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Zoom create meeting error: ' . $response->body());
            return null;
        } catch (Exception $e) {
            Log::error('Zoom service error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update a Zoom meeting
     *
     * @param string $meetingId
     * @param array $meetingData
     * @return bool
     */
    public function updateMeeting(string $meetingId, array $meetingData): bool
    {
        try {
            $token = $this->getAccessToken();

            $payload = [];

            if (isset($meetingData['topic'])) {
                $payload['topic'] = $meetingData['topic'];
            }

            if (isset($meetingData['start_time'])) {
                $payload['start_time'] = $meetingData['start_time'];
            }

            if (isset($meetingData['duration'])) {
                $payload['duration'] = $meetingData['duration'];
            }

            if (isset($meetingData['agenda'])) {
                $payload['agenda'] = $meetingData['agenda'];
            }

            if (isset($meetingData['settings'])) {
                $payload['settings'] = $meetingData['settings'];
            }

            Log::info('Updating Zoom meeting', [
                'meeting_id' => $meetingId,
                'payload' => $payload,
            ]);

            $response = Http::withToken($token)
                ->patch($this->baseUrl . '/meetings/' . $meetingId, $payload);

            if (!$response->successful()) {
                Log::error('Zoom update meeting API error', [
                    'meeting_id' => $meetingId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return $response->successful();
        } catch (Exception $e) {
            Log::error('Zoom update meeting error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a Zoom meeting
     *
     * @param string $meetingId
     * @return bool
     */
    public function deleteMeeting(string $meetingId): bool
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withToken($token)
                ->delete($this->baseUrl . '/meetings/' . $meetingId);

            return $response->successful();
        } catch (Exception $e) {
            Log::error('Zoom delete meeting error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get meeting details
     *
     * @param string $meetingId
     * @return array|null
     */
    public function getMeeting(string $meetingId): ?array
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withToken($token)
                ->get($this->baseUrl . '/meetings/' . $meetingId);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (Exception $e) {
            Log::error('Zoom get meeting error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Start a meeting
     *
     * @param string $meetingId
     * @return bool
     */
    public function startMeeting(string $meetingId): bool
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withToken($token)
                ->put($this->baseUrl . '/meetings/' . $meetingId . '/status', [
                    'action' => 'start'
                ]);

            return $response->successful();
        } catch (Exception $e) {
            Log::error('Zoom start meeting error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * End a meeting
     *
     * @param string $meetingId
     * @return bool
     */
    public function endMeeting(string $meetingId): bool
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withToken($token)
                ->put($this->baseUrl . '/meetings/' . $meetingId . '/status', [
                    'action' => 'end'
                ]);

            return $response->successful();
        } catch (Exception $e) {
            Log::error('Zoom end meeting error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get meeting recordings
     *
     * @param string $meetingId
     * @return array|null
     */
    public function getMeetingRecordings(string $meetingId): ?array
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withToken($token)
                ->get($this->baseUrl . '/meetings/' . $meetingId . '/recordings');

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (Exception $e) {
            Log::error('Zoom get recordings error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * List user's meetings
     *
     * @param string $type upcoming|scheduled|previous
     * @param int $pageSize
     * @return array|null
     */
    public function listMeetings(string $type = 'scheduled', int $pageSize = 30): ?array
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withToken($token)
                ->get($this->baseUrl . '/users/me/meetings', [
                    'type' => $type,
                    'page_size' => $pageSize,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (Exception $e) {
            Log::error('Zoom list meetings error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate Zoom SDK signature for joining meetings
     *
     * IMPORTANT: This uses the exact format required by Zoom Web SDK v3.8.10
     *
     * @param string $meetingNumber
     * @param int $role 0=participant, 1=host
     * @return string|null
     */
    public function generateSignature(string $meetingNumber, int $role = 0): ?string
    {
        try {
            if (empty($this->sdkKey) || empty($this->sdkSecret)) {
                Log::error('Zoom SDK credentials not configured', [
                    'sdk_key_exists' => !empty($this->sdkKey),
                    'sdk_secret_exists' => !empty($this->sdkSecret),
                ]);
                return null;
            }

            $iat = time() - 30; // Current timestamp minus 30 seconds to account for clock drift
            $exp = $iat + 60 * 60 * 2; // Token expires in 2 hours

            // CRITICAL: This exact payload structure is required by Zoom SDK
            // Do NOT add, remove, or modify these fields
            $payload = [
                'sdkKey' => $this->sdkKey,
                'mn' => (string) $meetingNumber,
                'role' => (int) $role,
                'iat' => $iat,
                'exp' => $exp,
                'appKey' => $this->sdkKey,
                'tokenExp' => $exp,
            ];

            Log::info('Generating Zoom signature', [
                'meeting_number' => $meetingNumber,
                'role' => $role,
                'iat' => $iat,
                'exp' => $exp,
                'sdk_key' => $this->sdkKey,
            ]);

            $signature = JWT::encode($payload, $this->sdkSecret, 'HS256');

            Log::info('Zoom signature generated successfully', [
                'signature_length' => strlen($signature),
            ]);

            return $signature;
        } catch (Exception $e) {
            Log::error('Zoom generate signature error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all recordings for a user within a date range
     *
     * @param string $userId User ID or 'me'
     * @param string|null $from Start date (YYYY-MM-DD)
     * @param string|null $to End date (YYYY-MM-DD)
     * @return array|null
     */
    public function getUserRecordings(string $userId = 'me', ?string $from = null, ?string $to = null): ?array
    {
        try {
            $token = $this->getAccessToken();

            $params = [];
            if ($from) {
                $params['from'] = $from;
            }
            if ($to) {
                $params['to'] = $to;
            }

            $response = Http::withToken($token)
                ->get($this->baseUrl . '/users/' . $userId . '/recordings', $params);

            if ($response->successful()) {
                return $response->json();
            }

            if ($response->status() === 404) {
                Log::info("No recordings found for user: {$userId}");
                return ['meetings' => []];
            }

            Log::error('Failed to get user recordings', [
                'user_id' => $userId,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('Exception getting user recordings', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Download a recording file
     *
     * @param string $downloadUrl
     * @return string|null Returns file content
     */
    public function downloadRecording(string $downloadUrl): ?string
    {
        try {
            $token = $this->getAccessToken();

            $response = Http::withToken($token)
                ->timeout(300) // 5 minutes timeout for large files
                ->get($downloadUrl);

            if ($response->successful()) {
                return $response->body();
            }

            Log::error('Failed to download recording', [
                'url' => $downloadUrl,
                'status' => $response->status()
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('Exception downloading recording', [
                'url' => $downloadUrl,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Delete a recording from Zoom cloud
     *
     * @param string $meetingId
     * @param string|null $recordingId
     * @return bool
     */
    public function deleteRecording(string $meetingId, ?string $recordingId = null): bool
    {
        try {
            $token = $this->getAccessToken();

            $url = $recordingId
                ? $this->baseUrl . '/meetings/' . $meetingId . '/recordings/' . $recordingId
                : $this->baseUrl . '/meetings/' . $meetingId . '/recordings';

            $response = Http::withToken($token)
                ->delete($url);

            if ($response->successful() || $response->status() === 204) {
                Log::info('Recording deleted from Zoom', [
                    'meeting_id' => $meetingId,
                    'recording_id' => $recordingId
                ]);
                return true;
            }

            Log::error('Failed to delete recording', [
                'meeting_id' => $meetingId,
                'recording_id' => $recordingId,
                'status' => $response->status()
            ]);

            return false;
        } catch (Exception $e) {
            Log::error('Exception deleting recording', [
                'meeting_id' => $meetingId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
