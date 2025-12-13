<?php

namespace App\Console\Commands;

use App\Models\Session;
use App\Services\ZoomService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateZoomMeetingSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoom:update-settings {session_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Zoom meeting settings to enable instant join (join_before_host)';

    protected $zoomService;

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
        $sessionId = $this->argument('session_id');

        if ($sessionId) {
            // Update specific session
            $session = Session::find($sessionId);

            if (!$session) {
                $this->error("Session with ID {$sessionId} not found.");
                return 1;
            }

            if (!$session->zoom_meeting_id) {
                $this->error("Session {$sessionId} does not have a Zoom meeting ID.");
                return 1;
            }

            $this->info("Updating Zoom meeting for session {$sessionId}...");
            $result = $this->updateMeetingSettings($session);

            if ($result) {
                $this->info("✅ Successfully updated Zoom meeting {$session->zoom_meeting_id}");
                return 0;
            } else {
                $this->error("❌ Failed to update Zoom meeting {$session->zoom_meeting_id}");
                return 1;
            }
        } else {
            // Update all sessions with Zoom meetings
            $sessions = Session::whereNotNull('zoom_meeting_id')->get();

            if ($sessions->isEmpty()) {
                $this->info("No sessions with Zoom meetings found.");
                return 0;
            }

            $this->info("Found {$sessions->count()} sessions with Zoom meetings.");

            $bar = $this->output->createProgressBar($sessions->count());
            $bar->start();

            $successCount = 0;
            $failCount = 0;

            foreach ($sessions as $session) {
                if ($this->updateMeetingSettings($session)) {
                    $successCount++;
                } else {
                    $failCount++;
                }
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            $this->info("✅ Successfully updated: {$successCount}");
            if ($failCount > 0) {
                $this->warn("❌ Failed to update: {$failCount}");
            }

            return 0;
        }
    }

    /**
     * Update settings for a specific Zoom meeting
     */
    private function updateMeetingSettings(Session $session): bool
    {
        try {
            $meetingData = [
                'settings' => [
                    'join_before_host' => true,
                    'mute_upon_entry' => false,
                    'waiting_room' => false,
                    'auto_recording' => 'cloud',
                    'meeting_chat' => true,
                    'allow_participants_chat_with' => 2,
                    'allow_participants_save_chats' => true,
                ]
            ];

            $result = $this->zoomService->updateMeeting($session->zoom_meeting_id, $meetingData);

            if ($result) {
                Log::info('Zoom meeting settings updated via command', [
                    'session_id' => $session->id,
                    'meeting_id' => $session->zoom_meeting_id,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to update Zoom meeting settings', [
                'session_id' => $session->id,
                'meeting_id' => $session->zoom_meeting_id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
