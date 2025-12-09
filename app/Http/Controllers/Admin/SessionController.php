<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\SessionFile;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SessionController extends Controller
{
    public function index()
    {
        $sessions = Session::with(['subject.term.program'])
            ->latest('scheduled_at')
            ->paginate(15);

        return view('admin.sessions.index', compact('sessions'));
    }

    public function create()
    {
        $subjects = Subject::with('term.program')->where('status', 'active')->get();

        return view('admin.sessions.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
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

            'status' => 'required|in:scheduled,live,completed,cancelled',
            'is_mandatory' => 'boolean',

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

        $session = Session::create($validated);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $path = $file->store('session-files', 'public');

                SessionFile::create([
                    'session_id' => $session->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
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

            'status' => 'required|in:scheduled,live,completed,cancelled',
            'is_mandatory' => 'boolean',

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

        $session->update($validated);

        // Handle new file uploads
        if ($request->hasFile('files')) {
            $currentMaxOrder = $session->files()->max('order') ?? 0;

            foreach ($request->file('files') as $index => $file) {
                $path = $file->store('session-files', 'public');

                SessionFile::create([
                    'session_id' => $session->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
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
}
