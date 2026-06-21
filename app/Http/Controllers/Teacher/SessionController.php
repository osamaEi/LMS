<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function updateJoinUrl(Request $request, Session $session)
    {
        $teacher = auth()->user();

        // Authorize
        if ($session->subject_id) {
            $session->loadMissing('subject');
            if (!$session->subject || !$session->subject->isAssignedToTeacher($teacher->id)) {
                abort(403);
            }
        } elseif ($session->program_id) {
            $allowed = $teacher->teachingPrograms()
                ->whereIn('type', ['training', 'english', 'course'])
                ->where('id', $session->program_id)
                ->exists();
            if (!$allowed) abort(403);
        } else {
            abort(403);
        }

        $request->validate(['zoom_join_url' => 'nullable|url|max:500']);

        $session->update(['zoom_join_url' => $request->input('zoom_join_url') ?: null]);

        return redirect()->back()->with('success', 'تم حفظ رابط الانضمام بنجاح ✓');
    }

    /**
     * Teacher starts the session — records started_at (so students may join), then
     * opens the host start link.
     */
    public function startSession(Request $request, Session $session)
    {
        $teacher = auth()->user();

        // Authorize (same rule as updateJoinUrl)
        if ($session->subject_id) {
            $session->loadMissing('subject');
            if (!$session->subject || !$session->subject->isAssignedToTeacher($teacher->id)) {
                abort(403);
            }
        } elseif ($session->program_id) {
            $allowed = $teacher->teachingPrograms()
                ->whereIn('type', ['training', 'english', 'course'])
                ->where('id', $session->program_id)
                ->exists();
            if (!$allowed) abort(403);
        } else {
            abort(403);
        }

        // Mark as started (only once) so students are allowed to join.
        if (!$session->started_at) {
            $session->update(['started_at' => now()]);
        }

        // Open the host start link (fallback to join link).
        $url = $session->zoom_start_url ?: $session->zoom_join_url;
        if ($url) {
            return redirect($url);
        }

        return redirect()->back()->with('error', 'لا يوجد رابط للجلسة');
    }

    public function show(Session $session)
    {
        $teacher = auth()->user();

        // Authorize: session must belong to this teacher via subject or program
        if ($session->subject_id) {
            $session->loadMissing('subject');
            if (!$session->subject || !$session->subject->isAssignedToTeacher($teacher->id)) {
                abort(403);
            }
        } elseif ($session->program_id) {
            $allowed = $teacher->teachingPrograms()
                ->whereIn('type', ['training', 'english', 'course'])
                ->where('id', $session->program_id)
                ->exists();
            if (!$allowed) abort(403);
        } else {
            abort(403);
        }

        $session->load([
            'subject.term.program',
            'program',
            'files',
            'homework.submissions.student',
        ]);

        return view('teacher.sessions.show', compact('session'));
    }
}
