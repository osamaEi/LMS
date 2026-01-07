<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\SessionFile;
use App\Models\Subject;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SessionController extends Controller
{
    protected $zoomService;

    public function __construct(ZoomService $zoomService)
    {
        $this->zoomService = $zoomService;
    }

    public function index()
    {
        $sessions = Session::with(['subject.term.program'])
            ->latest('scheduled_at')
            ->paginate(15);

        return view('admin.sessions.index', compact('sessions'));
    }

    public function create()
    {
        $subjects = Subject::with(['term.program', 'units'])->where('status', 'active')->get();

        return view('admin.sessions.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'unit_id' => 'nullable|exists:units,id',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'session_number' => 'required|integer|min:1',
            'type' => 'required|in:live_zoom,recorded_video',
            'scheduled_at' => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:1',

            // Zoom fields
            'zoom_meeting_id' => 'nullable|string',
            'zoom_join_url' => 'nullable|url',
            'zoom_password' => 'nullable|string',

            // Video fields
            'video_file' => 'nullable|file|mimes:mp4,avi,mov,mkv,wmv,flv|max:512000', // Max 500MB
            'video_url' => 'nullable|url',
            'video_platform' => 'nullable|in:youtube,vimeo,external,local',

            // File uploads
            'files.*' => 'nullable|file|max:10240',
        ]);

        // Handle video file upload if present
        if ($request->hasFile('video_file')) {
            $videoFile = $request->file('video_file');
            $path = $videoFile->store('session-videos', 'public');

            $validated['video_path'] = $path;
            $validated['video_platform'] = 'local';
            $validated['video_size'] = $videoFile->getSize();
        }

        // Filter out null values to avoid database constraint violations
        $validated = array_filter($validated, function($value) {
            return $value !== null && $value !== '';
        });

        // Set default value for video_platform if video_url is provided but platform is not
        if (!empty($validated['video_url']) && empty($validated['video_platform'])) {
            $validated['video_platform'] = 'external';
        }

        // Remove video_file from validated data as it's already processed
        unset($validated['video_file']);

        // Automatically create Zoom meeting if type is live_zoom
        if ($validated['type'] === 'live_zoom' && empty($validated['zoom_meeting_id'])) {
            try {
                $meetingData = [
                    'topic' => $validated['title_ar'],
                    'type' => 2, // Scheduled meeting
                    'start_time' => isset($validated['scheduled_at'])
                        ? \Carbon\Carbon::parse($validated['scheduled_at'])->toIso8601String()
                        : now()->addHour()->toIso8601String(),
                    'duration' => $validated['duration_minutes'] ?? 60,
                    'timezone' => 'Asia/Riyadh',
                    'agenda' => $validated['description_ar'] ?? '',
                ];

                $meeting = $this->zoomService->createMeeting($meetingData);

                if ($meeting) {
                    $validated['zoom_meeting_id'] = $meeting['id'];
                    $validated['zoom_join_url'] = $meeting['join_url'];
                    $validated['zoom_password'] = $meeting['password'] ?? null;

                    Log::info('Zoom meeting created automatically', [
                        'meeting_id' => $meeting['id'],
                        'title' => $validated['title_ar']
                    ]);
                } else {
                    Log::error('Failed to auto-create Zoom meeting for session: ' . $validated['title_ar']);
                    return back()->withInput()->withErrors([
                        'zoom' => 'فشل إنشاء اجتماع Zoom تلقائياً. يرجى التحقق من إعدادات Zoom API أو إنشاء الاجتماع يدوياً.'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Zoom auto-creation exception: ' . $e->getMessage());
                return back()->withInput()->withErrors([
                    'zoom' => 'حدث خطأ أثناء إنشاء اجتماع Zoom: ' . $e->getMessage()
                ]);
            }
        }

        $session = Session::create($validated);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $path = $file->store('session-files', 'public');

                SessionFile::create([
                    'session_id' => $session->id,
                    'title' => $file->getClientOriginalName(),
                    'type' => 'pdf',
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'order' => $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.sessions.index')
            ->with('success', 'تم إضافة الدرس بنجاح');
    }

    public function show(Session $session)
    {
        $session->load(['subject.term.program', 'files']);

        return view('admin.sessions.show', compact('session'));
    }

    public function showZoom(Session $session)
    {
        $session->load(['subject.term.program']);

        return view('admin.sessions.zoom', compact('session'));
    }

    public function showZoomDashboard(Session $session)
    {
        $session->load(['subject.term.program']);

        return view('admin.sessions.zoom-dashboard', compact('session'));
    }

    public function edit(Session $session)
    {
        $subjects = Subject::with('term.program')->where('status', 'active')->get();
        $session->load('files');

        return view('admin.sessions.edit', compact('session', 'subjects'));
    }

    public function update(Request $request, Session $session)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'session_number' => 'required|integer|min:1',
            'type' => 'required|in:live_zoom,recorded_video',
            'scheduled_at' => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:1',

            // Zoom fields
            'zoom_meeting_id' => 'nullable|string',
            'zoom_join_url' => 'nullable|url',
            'zoom_password' => 'nullable|string',

            // Video fields
            'video_url' => 'nullable|url',
            'video_platform' => 'nullable|in:youtube,vimeo,external,local',

            // File uploads
            'files.*' => 'nullable|file|max:10240',
        ]);

        // Filter out null values to avoid database constraint violations
        $validated = array_filter($validated, function($value) {
            return $value !== null;
        });

        // Set default value for video_platform if video_url is provided but platform is not
        if (!empty($validated['video_url']) && empty($validated['video_platform'])) {
            $validated['video_platform'] = 'external';
        }

        // Automatically create Zoom meeting if type changed to live_zoom and no meeting exists
        if ($validated['type'] === 'live_zoom' && empty($validated['zoom_meeting_id']) && empty($session->zoom_meeting_id)) {
            try {
                $meetingData = [
                    'topic' => $validated['title_ar'],
                    'type' => 2, // Scheduled meeting
                    'start_time' => isset($validated['scheduled_at'])
                        ? \Carbon\Carbon::parse($validated['scheduled_at'])->toIso8601String()
                        : now()->addHour()->toIso8601String(),
                    'duration' => $validated['duration_minutes'] ?? 60,
                    'timezone' => 'Asia/Riyadh',
                    'agenda' => $validated['description_ar'] ?? '',
                ];

                $meeting = $this->zoomService->createMeeting($meetingData);

                if ($meeting) {
                    $validated['zoom_meeting_id'] = $meeting['id'];
                    $validated['zoom_join_url'] = $meeting['join_url'];
                    $validated['zoom_password'] = $meeting['password'] ?? null;

                    Log::info('Zoom meeting created automatically on update', [
                        'meeting_id' => $meeting['id'],
                        'session_id' => $session->id
                    ]);
                } else {
                    Log::error('Failed to auto-create Zoom meeting for session: ' . $session->id);
                    return back()->withInput()->withErrors([
                        'zoom' => 'فشل إنشاء اجتماع Zoom تلقائياً. يرجى التحقق من إعدادات Zoom API أو إنشاء الاجتماع يدوياً.'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Zoom auto-creation exception on update: ' . $e->getMessage());
                return back()->withInput()->withErrors([
                    'zoom' => 'حدث خطأ أثناء إنشاء اجتماع Zoom: ' . $e->getMessage()
                ]);
            }
        }
        // Update existing Zoom meeting if details changed
        elseif ($validated['type'] === 'live_zoom' && !empty($session->zoom_meeting_id)) {
            try {
                $meetingData = [];

                if (isset($validated['title_ar']) && $validated['title_ar'] !== $session->title_ar) {
                    $meetingData['topic'] = $validated['title_ar'];
                }

                if (isset($validated['scheduled_at']) && $validated['scheduled_at'] !== $session->scheduled_at) {
                    $meetingData['start_time'] = \Carbon\Carbon::parse($validated['scheduled_at'])->toIso8601String();
                }

                if (isset($validated['duration_minutes']) && $validated['duration_minutes'] !== $session->duration_minutes) {
                    $meetingData['duration'] = $validated['duration_minutes'];
                }

                if (isset($validated['description_ar']) && $validated['description_ar'] !== $session->description_ar) {
                    $meetingData['agenda'] = $validated['description_ar'];
                }

                // Only update if there are changes
                if (!empty($meetingData)) {
                    $updated = $this->zoomService->updateMeeting($session->zoom_meeting_id, $meetingData);

                    if ($updated) {
                        Log::info('Zoom meeting updated automatically', [
                            'meeting_id' => $session->zoom_meeting_id,
                            'session_id' => $session->id
                        ]);
                    } else {
                        Log::warning('Failed to update Zoom meeting: ' . $session->zoom_meeting_id);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Zoom auto-update exception: ' . $e->getMessage());
                // Don't block the session update if Zoom update fails
            }
        }

        $session->update($validated);

        // Handle new file uploads
        if ($request->hasFile('files')) {
            $currentMaxOrder = $session->files()->max('order') ?? 0;

            foreach ($request->file('files') as $index => $file) {
                $path = $file->store('session-files', 'public');

                SessionFile::create([
                    'session_id' => $session->id,
                    'title' => $file->getClientOriginalName(),
                    'type' => 'pdf',
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'order' => $currentMaxOrder + $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.sessions.index')
            ->with('success', 'تم تحديث الدرس بنجاح');
    }

    public function destroy(Session $session)
    {
        // Delete Zoom meeting if exists
        if ($session->zoom_meeting_id) {
            try {
                $deleted = $this->zoomService->deleteMeeting($session->zoom_meeting_id);
                if ($deleted) {
                    Log::info('Zoom meeting deleted automatically', [
                        'meeting_id' => $session->zoom_meeting_id,
                        'session_id' => $session->id
                    ]);
                } else {
                    Log::warning('Failed to delete Zoom meeting: ' . $session->zoom_meeting_id);
                }
            } catch (\Exception $e) {
                Log::error('Zoom deletion exception: ' . $e->getMessage());
                // Don't block session deletion if Zoom deletion fails
            }
        }

        // Delete associated files
        foreach ($session->files as $file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }

        $session->delete();

        return redirect()->route('admin.sessions.index')
            ->with('success', 'تم حذف الدرس بنجاح');
    }

    public function deleteFile(SessionFile $file)
    {
        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return back()->with('success', 'تم حذف الملف بنجاح');
    }

    public function storeBatch(Request $request)
    {
        $sessionsData = json_decode($request->input('sessions'), true);

        if (empty($sessionsData)) {
            return redirect()->route('admin.sessions.create')
                ->with('error', 'لا توجد جلسات لإضافتها');
        }

        $createdCount = 0;
        $errors = [];

        foreach ($sessionsData as $sessionData) {
            try {
                $validated = [
                    'subject_id' => $sessionData['subject_id'],
                    'title_ar' => $sessionData['title_ar'],
                    'title_en' => $sessionData['title_en'] ?? $sessionData['title_ar'],
                    'description_ar' => $sessionData['description_ar'] ?? null,
                    'description_en' => $sessionData['description_en'] ?? null,
                    'session_number' => $sessionData['session_number'] ?? 1,
                    'type' => 'live_zoom',
                    'scheduled_at' => $sessionData['scheduled_at'],
                    'duration_minutes' => $sessionData['duration_minutes'] ?? 60,
                ];

                // Filter out null values
                $validated = array_filter($validated, function($value) {
                    return $value !== null && $value !== '';
                });

                // Automatically create Zoom meeting
                try {
                    $meetingData = [
                        'topic' => $validated['title_ar'],
                        'type' => 2, // Scheduled meeting
                        'start_time' => \Carbon\Carbon::parse($validated['scheduled_at'])->toIso8601String(),
                        'duration' => $validated['duration_minutes'] ?? 60,
                        'timezone' => 'Asia/Riyadh',
                        'agenda' => $validated['description_ar'] ?? '',
                    ];

                    $meeting = $this->zoomService->createMeeting($meetingData);

                    if ($meeting) {
                        $validated['zoom_meeting_id'] = $meeting['id'];
                        $validated['zoom_join_url'] = $meeting['join_url'];
                        $validated['zoom_password'] = $meeting['password'] ?? null;

                        Log::info('Zoom meeting created for batch session', [
                            'meeting_id' => $meeting['id'],
                            'title' => $validated['title_ar']
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Zoom creation failed for batch session: ' . $e->getMessage());
                }

                Session::create($validated);
                $createdCount++;

            } catch (\Exception $e) {
                Log::error('Failed to create batch session: ' . $e->getMessage());
                $errors[] = $sessionData['title_ar'] ?? 'جلسة غير معروفة';
            }
        }

        if ($createdCount > 0) {
            $message = "تم إنشاء {$createdCount} جلسة بنجاح";
            if (!empty($errors)) {
                $message .= ". فشل إنشاء: " . implode(', ', $errors);
            }
            return redirect()->route('admin.sessions.index')->with('success', $message);
        }

        return redirect()->route('admin.sessions.create')
            ->with('error', 'فشل إنشاء الجلسات. يرجى المحاولة مرة أخرى.');
    }
}
