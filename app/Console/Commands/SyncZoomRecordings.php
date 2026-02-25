<?php

namespace App\Console\Commands;

use App\Jobs\DownloadZoomRecording;
use App\Models\Session as ClassSession;
use App\Services\ZoomService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncZoomRecordings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoom:sync-recordings
                            {--days=1 : Number of days to look back for recordings}
                            {--force : Force re-download even if recording exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Zoom recordings for completed sessions';

    protected $zoomService;

    /**
     * Create a new command instance.
     */
    public function __construct(ZoomService $zoomService)
    {
        parent::__construct();
        $this->zoomService = $zoomService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎥 Starting Zoom recordings sync...');

        $days = (int) $this->option('days');
        $force = $this->option('force');

        // Get date range
        $from = now()->subDays($days)->format('Y-m-d');
        $to = now()->format('Y-m-d');

        $this->info("📅 Looking for recordings from {$from} to {$to}");

        try {
            // Get all recordings from Zoom
            $recordingsData = $this->zoomService->getUserRecordings('me', $from, $to);

            if (!$recordingsData || empty($recordingsData['meetings'])) {
                $this->info('ℹ️  No recordings found in Zoom');
                return Command::SUCCESS;
            }

            $meetings = $recordingsData['meetings'];
            $this->info("📦 Found " . count($meetings) . " meeting(s) with recordings");

            $synced = 0;
            $skipped = 0;
            $failed = 0;

            foreach ($meetings as $meeting) {
                $meetingId = $meeting['id'] ?? $meeting['uuid'];

                $this->line("Processing meeting: {$meetingId}");

                // Find the session in our database
                $session = ClassSession::where('zoom_meeting_id', $meetingId)->first();

                if (!$session) {
                    $this->warn("  ⚠️  Session not found for meeting: {$meetingId}");
                    $skipped++;
                    continue;
                }

                // Skip if recording already downloaded (unless force flag is set)
                if (!$force && $session->recording_status === 'completed' && $session->recording_file_path) {
                    $this->line("  ⏭️  Recording already exists for session #{$session->id}");
                    $skipped++;
                    continue;
                }

                // Get recording files
                $recordingFiles = $meeting['recording_files'] ?? [];

                if (empty($recordingFiles)) {
                    $this->warn("  ⚠️  No recording files found for meeting: {$meetingId}");
                    $skipped++;
                    continue;
                }

                // Find the main video file (MP4 or M4A)
                $videoFile = $this->findVideoFile($recordingFiles);

                if (!$videoFile) {
                    $this->warn("  ⚠️  No video file found for meeting: {$meetingId}");
                    $skipped++;
                    continue;
                }

                try {
                    // Dispatch job to download recording
                    DownloadZoomRecording::dispatch($session, $videoFile);

                    $this->info("  ✅ Queued download for session #{$session->id}");
                    $synced++;
                } catch (\Exception $e) {
                    $this->error("  ❌ Failed to queue download for session #{$session->id}: " . $e->getMessage());
                    $failed++;

                    Log::error('Failed to queue recording download', [
                        'session_id' => $session->id,
                        'meeting_id' => $meetingId,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Summary
            $this->newLine();
            $this->info('📊 Sync Summary:');
            $this->table(
                ['Status', 'Count'],
                [
                    ['✅ Queued for download', $synced],
                    ['⏭️  Skipped', $skipped],
                    ['❌ Failed', $failed],
                ]
            );

            Log::info('Zoom recordings sync completed', [
                'synced' => $synced,
                'skipped' => $skipped,
                'failed' => $failed,
            ]);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Sync failed: ' . $e->getMessage());

            Log::error('Zoom recordings sync error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Find the main video file from recording files
     */
    protected function findVideoFile(array $recordingFiles): ?array
    {
        // Priority order: MP4 video > M4A audio > others
        $priorities = [
            'MP4' => 10,
            'M4A' => 5,
            'TIMELINE' => 0, // Skip timeline files
            'TRANSCRIPT' => 0, // Skip transcript files
            'CHAT' => 0, // Skip chat files
        ];

        $bestFile = null;
        $bestPriority = -1;

        foreach ($recordingFiles as $file) {
            $fileType = strtoupper($file['file_type'] ?? '');
            $recordingType = strtoupper($file['recording_type'] ?? '');

            // Skip non-media files
            if (in_array($fileType, ['TIMELINE', 'TRANSCRIPT', 'CHAT'])) {
                continue;
            }

            // Skip non-video recording types (prefer shared_screen_with_speaker_view or gallery_view)
            if (!in_array($recordingType, ['shared_screen_with_speaker_view', 'gallery_view', 'speaker_view', 'active_speaker'])) {
                if ($fileType !== 'M4A') { // Allow audio files
                    continue;
                }
            }

            $priority = $priorities[$fileType] ?? 1;

            if ($priority > $bestPriority) {
                $bestPriority = $priority;
                $bestFile = $file;
            }
        }

        return $bestFile;
    }
}
