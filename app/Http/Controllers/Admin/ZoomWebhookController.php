<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\SessionFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ZoomWebhookController extends Controller
{
    /**
     * Handle Zoom webhook events
     *
     * Documentation: https://developers.zoom.us/docs/api/rest/webhook-reference/
     */
    public function handleWebhook(Request $request)
    {
        // Verify webhook authenticity
        $webhookToken = config('services.zoom.webhook_secret_token');
        $authorization = $request->header('authorization');

        if ($webhookToken && $authorization !== $webhookToken) {
            Log::warning('Invalid Zoom webhook token received');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $event = $request->input('event');
        $payload = $request->input('payload');

        Log::info('Zoom webhook received', [
            'event' => $event,
            'payload' => $payload
        ]);

        // Handle different webhook events
        switch ($event) {
            case 'recording.completed':
                return $this->handleRecordingCompleted($payload);

            case 'meeting.ended':
                return $this->handleMeetingEnded($payload);

            default:
                Log::info('Unhandled Zoom webhook event: ' . $event);
                return response()->json(['status' => 'received']);
        }
    }

    /**
     * Handle recording completed event
     * Downloads the recording and attaches it to the session
     */
    private function handleRecordingCompleted(array $payload)
    {
        try {
            $meetingId = $payload['object']['id'] ?? null;
            $recordingFiles = $payload['object']['recording_files'] ?? [];

            if (!$meetingId) {
                Log::error('No meeting ID in recording webhook payload');
                return response()->json(['error' => 'Missing meeting ID'], 400);
            }

            // Find the session by Zoom meeting ID
            $session = Session::where('zoom_meeting_id', $meetingId)->first();

            if (!$session) {
                Log::warning('No session found for Zoom meeting ID: ' . $meetingId);
                return response()->json(['status' => 'session_not_found']);
            }

            Log::info('Processing recording for session', [
                'session_id' => $session->id,
                'meeting_id' => $meetingId,
                'recording_count' => count($recordingFiles)
            ]);

            // Download each recording file
            foreach ($recordingFiles as $index => $recordingFile) {
                $fileType = $recordingFile['file_type'] ?? null;
                $downloadUrl = $recordingFile['download_url'] ?? null;
                $recordingType = $recordingFile['recording_type'] ?? 'unknown';

                // Only process video files (MP4)
                if ($fileType !== 'MP4') {
                    Log::info('Skipping non-video recording file', [
                        'file_type' => $fileType,
                        'recording_type' => $recordingType
                    ]);
                    continue;
                }

                if (!$downloadUrl) {
                    Log::warning('No download URL for recording file');
                    continue;
                }

                try {
                    // Download the recording file
                    $this->downloadRecording($session, $downloadUrl, $recordingType, $index);
                } catch (\Exception $e) {
                    Log::error('Failed to download recording', [
                        'session_id' => $session->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Error handling recording completed webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Download recording file and attach to session
     */
    private function downloadRecording(Session $session, string $downloadUrl, string $recordingType, int $index)
    {
        // Add access token to download URL
        $accessToken = $this->getZoomAccessToken();

        if (!$accessToken) {
            throw new \Exception('Failed to get Zoom access token for recording download');
        }

        Log::info('Downloading Zoom recording', [
            'session_id' => $session->id,
            'recording_type' => $recordingType
        ]);

        // Download the file
        $response = Http::withToken($accessToken)
            ->timeout(300) // 5 minutes timeout for large files
            ->get($downloadUrl);

        if (!$response->successful()) {
            throw new \Exception('Failed to download recording: ' . $response->status());
        }

        // Generate filename
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "zoom-recording-{$session->id}-{$recordingType}-{$timestamp}.mp4";
        $path = "session-files/{$filename}";

        // Save to storage
        Storage::disk('public')->put($path, $response->body());

        // Get file size
        $fileSize = Storage::disk('public')->size($path);

        // Get the maximum order for existing files
        $maxOrder = $session->files()->max('order') ?? 0;

        // Create session file record
        SessionFile::create([
            'session_id' => $session->id,
            'file_name' => "تسجيل الجلسة ({$recordingType})",
            'file_path' => $path,
            'file_type' => 'video/mp4',
            'file_size' => $fileSize,
            'order' => $maxOrder + 1,
        ]);

        Log::info('Recording saved successfully', [
            'session_id' => $session->id,
            'filename' => $filename,
            'size' => $fileSize
        ]);

        // Update session to mark that recording is available
        $session->update([
            'has_recording' => true
        ]);
    }

    /**
     * Handle meeting ended event
     */
    private function handleMeetingEnded(array $payload)
    {
        $meetingId = $payload['object']['id'] ?? null;

        if (!$meetingId) {
            return response()->json(['error' => 'Missing meeting ID'], 400);
        }

        $session = Session::where('zoom_meeting_id', $meetingId)->first();

        if ($session) {
            Log::info('Meeting ended', [
                'session_id' => $session->id,
                'meeting_id' => $meetingId
            ]);

            // You can add additional logic here
            // For example, update session status, send notifications, etc.
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Get Zoom access token for API calls
     */
    private function getZoomAccessToken(): ?string
    {
        try {
            $response = Http::withBasicAuth(
                config('services.zoom.client_id'),
                config('services.zoom.client_secret')
            )
            ->asForm()
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => config('services.zoom.account_id'),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'];
            }

            Log::error('Failed to get Zoom access token: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Zoom OAuth error: ' . $e->getMessage());
            return null;
        }
    }
}
