<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DownloadZoomRecording;
use App\Models\Session as ClassSession;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RecordingController extends Controller
{
    protected $zoomService;

    public function __construct(ZoomService $zoomService)
    {
        $this->zoomService = $zoomService;
    }

    /**
     * Display all recordings
     */
    public function index(Request $request)
    {
        $query = ClassSession::where('type', 'live_zoom')
            ->with(['subject'])
            ->whereNotNull('zoom_meeting_id')
            ->latest('scheduled_at');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('recording_status', $request->status);
        }

        // Filter by subject
        if ($request->has('subject_id') && $request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $sessions = $query->paginate(20);

        // Get subjects for filter
        $subjects = \App\Models\Subject::orderBy('name_ar')->get();

        return view('admin.recordings.index', compact('sessions', 'subjects'));
    }

    /**
     * Sync recording for a specific session
     */
    public function sync($sessionId)
    {
        try {
            $session = ClassSession::findOrFail($sessionId);

            if (!$session->zoom_meeting_id) {
                return back()->with('error', 'هذه الجلسة ليس لها meeting ID في Zoom');
            }

            // Get recording from Zoom
            $recordingData = $this->zoomService->getMeetingRecordings($session->zoom_meeting_id);

            if (!$recordingData || empty($recordingData['recording_files'])) {
                return back()->with('error', 'لم يتم العثور على تسجيل لهذه الجلسة في Zoom');
            }

            // Find video file
            $videoFile = $this->findVideoFile($recordingData['recording_files']);

            if (!$videoFile) {
                return back()->with('error', 'لم يتم العثور على ملف فيديو في التسجيل');
            }

            // Dispatch download job
            DownloadZoomRecording::dispatch($session, $videoFile);

            return back()->with('success', 'تم إضافة التسجيل إلى قائمة التحميل. سيتم تحميله قريباً.');
        } catch (\Exception $e) {
            Log::error('Recording sync error', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'حدث خطأ أثناء مزامنة التسجيل: ' . $e->getMessage());
        }
    }

    /**
     * Delete recording
     */
    public function destroy($sessionId)
    {
        try {
            $session = ClassSession::findOrFail($sessionId);

            // Delete files from storage
            if ($session->recording_file_path) {
                Storage::delete($session->recording_file_path);
            }

            if ($session->recording_thumbnail) {
                Storage::delete($session->recording_thumbnail);
            }

            // Reset recording fields
            $session->update([
                'recording_file_path' => null,
                'recording_thumbnail' => null,
                'recording_status' => 'pending',
                'recording_size' => null,
                'recording_duration' => null,
                'recording_synced_at' => null,
            ]);

            return back()->with('success', 'تم حذف التسجيل بنجاح');
        } catch (\Exception $e) {
            Log::error('Recording delete error', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'حدث خطأ أثناء حذف التسجيل');
        }
    }

    /**
     * Delete recording from Zoom cloud
     */
    public function deleteFromZoom($sessionId)
    {
        try {
            $session = ClassSession::findOrFail($sessionId);

            if (!$session->zoom_meeting_id || !$session->zoom_recording_id) {
                return back()->with('error', 'لا يوجد تسجيل في Zoom لهذه الجلسة');
            }

            $deleted = $this->zoomService->deleteRecording(
                $session->zoom_meeting_id,
                $session->zoom_recording_id
            );

            if ($deleted) {
                return back()->with('success', 'تم حذف التسجيل من Zoom بنجاح');
            }

            return back()->with('error', 'فشل حذف التسجيل من Zoom');
        } catch (\Exception $e) {
            Log::error('Zoom recording delete error', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'حدث خطأ أثناء حذف التسجيل من Zoom');
        }
    }

    /**
     * Download recording file
     */
    public function download($sessionId)
    {
        try {
            $session = ClassSession::findOrFail($sessionId);

            if (!$session->recording_file_path || !Storage::exists($session->recording_file_path)) {
                return back()->with('error', 'ملف التسجيل غير موجود');
            }

            return Storage::download($session->recording_file_path);
        } catch (\Exception $e) {
            Log::error('Recording download error', [
                'session_id' => $sessionId,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'حدث خطأ أثناء تحميل التسجيل');
        }
    }

    /**
     * Find video file from recording files
     */
    protected function findVideoFile(array $recordingFiles): ?array
    {
        $priorities = [
            'MP4' => 10,
            'M4A' => 5,
        ];

        $bestFile = null;
        $bestPriority = -1;

        foreach ($recordingFiles as $file) {
            $fileType = strtoupper($file['file_type'] ?? '');
            $recordingType = strtoupper($file['recording_type'] ?? '');

            if (in_array($fileType, ['TIMELINE', 'TRANSCRIPT', 'CHAT'])) {
                continue;
            }

            if (!in_array($recordingType, ['shared_screen_with_speaker_view', 'gallery_view', 'speaker_view', 'active_speaker'])) {
                if ($fileType !== 'M4A') {
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
