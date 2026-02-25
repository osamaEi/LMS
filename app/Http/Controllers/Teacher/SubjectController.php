<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Session;
use App\Models\SessionFile;
use App\Models\Subject;
use App\Services\NotificationService;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SubjectController extends Controller
{
    protected $zoomService;
    protected $notificationService;

    public function __construct(ZoomService $zoomService, NotificationService $notificationService)
    {
        $this->zoomService = $zoomService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display teacher's subjects
     */
    public function index()
    {
        $teacher = auth()->user();

        $subjects = Subject::where('teacher_id', $teacher->id)
            ->with(['term.program'])
            ->withCount(['enrollments', 'sessions'])
            ->orderBy(app()->getLocale() === 'en' ? 'name_en' : 'name_ar')
            ->get();

        return view('teacher.subjects.index', compact('subjects'));
    }

    /**
     * Display subject details with sessions
     */
    public function show($id)
    {
        $teacher = auth()->user();

        $subject = Subject::where('teacher_id', $teacher->id)
            ->with(['term.program', 'units'])
            ->withCount('enrollments')
            ->findOrFail($id);

        $sessions = Session::where('subject_id', $id)
            ->with(['unit', 'files'])
            ->orderBy('session_number', 'asc')
            ->get();

        // Collect all session files across all sessions for the files tab
        $allFiles = $sessions->flatMap(fn($s) => $s->files->map(fn($f) => [
            'file'    => $f,
            'session' => $s,
        ]));

        return view('teacher.subjects.show', compact('subject', 'sessions', 'allFiles'));
    }

    /**
     * Show session creation form
     */
    public function createSession($subjectId)
    {
        $teacher = auth()->user();

        $subject = Subject::where('teacher_id', $teacher->id)
            ->with('units')
            ->findOrFail($subjectId);

        // Get next session number
        $nextSessionNumber = Session::where('subject_id', $subjectId)->max('session_number') + 1;

        return view('teacher.subjects.sessions.create', compact('subject', 'nextSessionNumber'));
    }

    /**
     * Store a new session
     */
    public function storeSession(Request $request, $subjectId)
    {
        $teacher = auth()->user();

        // Verify subject belongs to teacher
        $subject = Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        $validated = $request->validate([
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
            'video_file' => 'nullable|file|mimes:mp4,avi,mov,mkv,wmv,flv|max:512000',
            'video_url' => 'nullable|url',
            'video_platform' => 'nullable|in:youtube,vimeo,external,local',

            // File uploads
            'files.*' => 'nullable|file|max:10240',
        ]);

        $validated['subject_id'] = $subjectId;

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            $videoFile = $request->file('video_file');
            $path = $videoFile->store('session-videos', 'public');

            $validated['video_path'] = $path;
            $validated['video_platform'] = 'local';
            $validated['video_size'] = $videoFile->getSize();
        }

        // Filter out null values
        $validated = array_filter($validated, function($value) {
            return $value !== null && $value !== '';
        });

        // Set default video platform
        if (!empty($validated['video_url']) && empty($validated['video_platform'])) {
            $validated['video_platform'] = 'external';
        }

        unset($validated['video_file']);

        // Create Zoom meeting if type is live_zoom
        if ($validated['type'] === 'live_zoom' && empty($validated['zoom_meeting_id'])) {
            try {
                $meetingData = [
                    'topic' => $validated['title_ar'],
                    'type' => 2,
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

                    Log::info('Zoom meeting created by teacher', [
                        'meeting_id' => $meeting['id'],
                        'teacher_id' => $teacher->id,
                        'title' => $validated['title_ar']
                    ]);
                } else {
                    return back()->withInput()->withErrors([
                        'zoom' => 'فشل إنشاء اجتماع Zoom تلقائياً. يرجى التحقق من إعدادات Zoom API.'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Zoom creation exception by teacher: ' . $e->getMessage());
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

        // Notify enrolled students
        $this->notificationService->notifySessionCreated($session);

        return redirect()->route('teacher.my-subjects.show', $subjectId)
            ->with('success', 'تم إضافة الحصة بنجاح');
    }

    /**
     * Show session edit form
     */
    public function editSession($subjectId, $sessionId)
    {
        $teacher = auth()->user();

        $subject = Subject::where('teacher_id', $teacher->id)
            ->with('units')
            ->findOrFail($subjectId);

        $session = Session::where('subject_id', $subjectId)
            ->with('files')
            ->findOrFail($sessionId);

        return view('teacher.subjects.sessions.edit', compact('subject', 'session'));
    }

    /**
     * Update session
     */
    public function updateSession(Request $request, $subjectId, $sessionId)
    {
        $teacher = auth()->user();

        // Verify subject belongs to teacher
        Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        $session = Session::where('subject_id', $subjectId)->findOrFail($sessionId);

        $validated = $request->validate([
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
            'video_file' => 'nullable|file|mimes:mp4,avi,mov,mkv,wmv,flv|max:512000',
            'video_url' => 'nullable|url',
            'video_platform' => 'nullable|in:youtube,vimeo,external,local',

            // File uploads
            'files.*' => 'nullable|file|max:10240',
        ]);

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            // Delete old video if exists
            if ($session->video_path) {
                Storage::disk('public')->delete($session->video_path);
            }

            $videoFile = $request->file('video_file');
            $path = $videoFile->store('session-videos', 'public');

            $validated['video_path'] = $path;
            $validated['video_platform'] = 'local';
            $validated['video_size'] = $videoFile->getSize();
        }

        // Filter out null values
        $validated = array_filter($validated, function($value) {
            return $value !== null;
        });

        if (!empty($validated['video_url']) && empty($validated['video_platform'])) {
            $validated['video_platform'] = 'external';
        }

        unset($validated['video_file']);

        // Create Zoom meeting if switching to live_zoom
        if ($validated['type'] === 'live_zoom' && empty($validated['zoom_meeting_id']) && empty($session->zoom_meeting_id)) {
            try {
                $meetingData = [
                    'topic' => $validated['title_ar'],
                    'type' => 2,
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
                } else {
                    return back()->withInput()->withErrors([
                        'zoom' => 'فشل إنشاء اجتماع Zoom تلقائياً.'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Zoom creation exception: ' . $e->getMessage());
                return back()->withInput()->withErrors([
                    'zoom' => 'حدث خطأ أثناء إنشاء اجتماع Zoom: ' . $e->getMessage()
                ]);
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

        // Notify enrolled students about the update
        $this->notificationService->notifySessionUpdated($session);

        return redirect()->route('teacher.my-subjects.show', $subjectId)
            ->with('success', 'تم تحديث الحصة بنجاح');
    }

    /**
     * Delete session
     */
    public function destroySession($subjectId, $sessionId)
    {
        $teacher = auth()->user();

        // Verify subject belongs to teacher
        Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        $session = Session::where('subject_id', $subjectId)->findOrFail($sessionId);

        // Delete Zoom meeting if exists
        if ($session->zoom_meeting_id) {
            try {
                $this->zoomService->deleteMeeting($session->zoom_meeting_id);
            } catch (\Exception $e) {
                Log::error('Zoom deletion exception: ' . $e->getMessage());
            }
        }

        // Delete video if exists
        if ($session->video_path) {
            Storage::disk('public')->delete($session->video_path);
        }

        // Delete associated files
        foreach ($session->files as $file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }

        $session->delete();

        return redirect()->route('teacher.my-subjects.show', $subjectId)
            ->with('success', 'تم حذف الحصة بنجاح');
    }

    /**
     * Delete session file
     */
    public function deleteSessionFile($subjectId, $sessionId, $fileId)
    {
        $teacher = auth()->user();

        Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);
        Session::where('subject_id', $subjectId)->findOrFail($sessionId);

        $file = SessionFile::where('session_id', $sessionId)->findOrFail($fileId);

        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return back()->with('success', 'تم حذف الملف بنجاح');
    }

    /**
     * Show Zoom meeting page (fullscreen)
     */
    public function showZoom($subjectId, $sessionId)
    {
        $teacher = auth()->user();

        Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        $session = Session::where('subject_id', $subjectId)
            ->with('subject')
            ->findOrFail($sessionId);

        return view('teacher.subjects.sessions.zoom', compact('session'));
    }

    /**
     * Show Zoom meeting page (embedded in dashboard)
     */
    public function showZoomEmbedded($subjectId, $sessionId)
    {
        $teacher = auth()->user();

        Subject::where('teacher_id', $teacher->id)->findOrFail($subjectId);

        $session = Session::where('subject_id', $subjectId)
            ->with('subject')
            ->findOrFail($sessionId);

        return view('teacher.subjects.sessions.zoom-embedded', compact('session'));
    }

    /**
     * Show session attendance
     */
    public function sessionAttendance($subjectId, $sessionId)
    {
        $teacher = auth()->user();

        $subject = Subject::where('teacher_id', $teacher->id)
            ->findOrFail($subjectId);

        $session = Session::where('subject_id', $subjectId)
            ->findOrFail($sessionId);

        // Get all attendance records for this session with student info
        $attendances = Attendance::where('session_id', $sessionId)
            ->with('student')
            ->orderBy('joined_at', 'desc')
            ->get();

        // Get enrolled students who haven't attended
        $attendedStudentIds = $attendances->pluck('student_id')->toArray();
        $absentStudents = $subject->enrollments()
            ->with('student')
            ->whereNotIn('student_id', $attendedStudentIds)
            ->get()
            ->pluck('student');

        // Calculate statistics
        $stats = [
            'total_enrolled' => $subject->enrollments()->count(),
            'attended' => $attendances->where('attended', true)->count(),
            'absent' => $absentStudents->count(),
            'attendance_rate' => $subject->enrollments()->count() > 0
                ? round(($attendances->where('attended', true)->count() / $subject->enrollments()->count()) * 100, 1)
                : 0,
        ];

        return view('teacher.subjects.sessions.attendance', compact('subject', 'session', 'attendances', 'absentStudents', 'stats'));
    }
}
