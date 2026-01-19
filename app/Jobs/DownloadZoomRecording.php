<?php

namespace App\Jobs;

use App\Models\ClassSession;
use App\Services\ZoomService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class DownloadZoomRecording implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes timeout
    public $tries = 3; // Retry 3 times if failed

    protected $session;
    protected $recordingFile;

    /**
     * Create a new job instance.
     */
    public function __construct(ClassSession $session, array $recordingFile)
    {
        $this->session = $session;
        $this->recordingFile = $recordingFile;
    }

    /**
     * Execute the job.
     */
    public function handle(ZoomService $zoomService): void
    {
        try {
            Log::info('Starting recording download', [
                'session_id' => $this->session->id,
                'meeting_id' => $this->session->zoom_meeting_id,
                'recording_id' => $this->recordingFile['id'] ?? 'unknown'
            ]);

            // Update status to processing
            $this->session->update(['recording_status' => 'processing']);

            // Download the recording
            $fileContent = $zoomService->downloadRecording($this->recordingFile['download_url']);

            if (!$fileContent) {
                throw new Exception('Failed to download recording from Zoom');
            }

            // Generate file name
            $fileName = $this->generateFileName();

            // Save to local storage
            $path = 'recordings/' . $fileName;
            Storage::put($path, $fileContent);

            Log::info('Recording saved to storage', [
                'session_id' => $this->session->id,
                'path' => $path,
                'size' => strlen($fileContent)
            ]);

            // Download thumbnail if available
            $thumbnailPath = null;
            if (isset($this->recordingFile['thumbnail_url'])) {
                $thumbnailPath = $this->downloadThumbnail($zoomService);
            }

            // Update session with recording info
            $this->session->update([
                'zoom_recording_id' => $this->recordingFile['id'] ?? null,
                'recording_download_url' => $this->recordingFile['download_url'] ?? null,
                'recording_file_path' => $path,
                'recording_thumbnail' => $thumbnailPath,
                'recording_status' => 'completed',
                'recording_size' => $this->recordingFile['file_size'] ?? strlen($fileContent),
                'recording_duration' => $this->calculateDuration(),
                'recording_synced_at' => now(),
            ]);

            Log::info('Recording download completed successfully', [
                'session_id' => $this->session->id,
                'path' => $path
            ]);

            // Optionally: Delete from Zoom cloud to save space
            // $zoomService->deleteRecording($this->session->zoom_meeting_id, $this->recordingFile['id']);

        } catch (Exception $e) {
            Log::error('Recording download failed', [
                'session_id' => $this->session->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update status to failed
            $this->session->update(['recording_status' => 'failed']);

            // Re-throw to trigger retry
            throw $e;
        }
    }

    /**
     * Generate unique file name for the recording
     */
    protected function generateFileName(): string
    {
        $extension = $this->recordingFile['file_extension'] ?? 'mp4';
        $date = now()->format('Y-m-d');
        $sessionId = $this->session->id;

        return "{$date}_session_{$sessionId}.{$extension}";
    }

    /**
     * Download thumbnail image
     */
    protected function downloadThumbnail(ZoomService $zoomService): ?string
    {
        try {
            if (empty($this->recordingFile['thumbnail_url'])) {
                return null;
            }

            $thumbnailContent = $zoomService->downloadRecording($this->recordingFile['thumbnail_url']);

            if (!$thumbnailContent) {
                return null;
            }

            $thumbnailName = $this->generateThumbnailFileName();
            $thumbnailPath = 'recordings/thumbnails/' . $thumbnailName;

            Storage::put($thumbnailPath, $thumbnailContent);

            Log::info('Thumbnail downloaded', [
                'session_id' => $this->session->id,
                'path' => $thumbnailPath
            ]);

            return $thumbnailPath;
        } catch (Exception $e) {
            Log::warning('Failed to download thumbnail', [
                'session_id' => $this->session->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Generate thumbnail file name
     */
    protected function generateThumbnailFileName(): string
    {
        $date = now()->format('Y-m-d');
        $sessionId = $this->session->id;

        return "{$date}_session_{$sessionId}_thumb.jpg";
    }

    /**
     * Calculate recording duration in seconds
     */
    protected function calculateDuration(): ?int
    {
        // Try to get duration from recording file data
        if (isset($this->recordingFile['recording_duration'])) {
            return (int) $this->recordingFile['recording_duration'];
        }

        // Fallback to session duration if available
        if ($this->session->duration_minutes) {
            return $this->session->duration_minutes * 60;
        }

        return null;
    }

    /**
     * Handle a job failure.
     */
    public function failed(Exception $exception): void
    {
        Log::error('Recording download job failed permanently', [
            'session_id' => $this->session->id,
            'error' => $exception->getMessage()
        ]);

        // Update status to failed
        $this->session->update(['recording_status' => 'failed']);
    }
}
