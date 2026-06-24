<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SessionController extends Controller
{
    /**
     * Returns true when $teacher is allowed to manage $session (owns the subject
     * or teaches the program). Sessions with neither are never manageable.
     */
    private function teacherOwnsSession(Session $session, $teacher): bool
    {
        if ($session->subject_id) {
            $session->loadMissing('subject');
            return $session->subject && $session->subject->isAssignedToTeacher($teacher->id);
        }

        if ($session->program_id) {
            return $teacher->teachingPrograms()
                ->whereIn('type', ['training', 'english', 'course'])
                ->where('id', $session->program_id)
                ->exists();
        }

        return false;
    }

    /**
     * Normalises a pasted Zoom link: trims surrounding whitespace and prepends
     * https:// when the teacher pasted a link without a scheme (e.g. "zoom.us/j/123").
     */
    private function normalizeJoinUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }
        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . ltrim($url, '/');
        }
        return $url;
    }

    public function updateJoinUrl(Request $request, Session $session)
    {
        $teacher = auth()->user();

        // if (!$this->teacherOwnsSession($session, $teacher)) {
        //     abort(403, 'هذه الجلسة غير مسندة لك، تواصل مع الإدارة.');
        // }

        // Normalise BEFORE validating so a scheme-less paste ("zoom.us/j/123")
        // does not trip the strict `url` rule and surface as "invalid".
        $request->merge([
            'zoom_join_url' => $this->normalizeJoinUrl($request->input('zoom_join_url')),
        ]);

        $request->validate(['zoom_join_url' => 'nullable|url|max:500']);

        $joinUrl = $request->input('zoom_join_url') ?: null;

        $payload = ['zoom_join_url' => $joinUrl];

        // Saving a join link also marks the session as started (once) so students
        // may join immediately without a separate "start" step.
        if ($joinUrl && !$session->started_at) {
            $payload['started_at'] = now();
        }

        $session->update($payload);

        Log::info('Teacher updated session join link', [
            'teacher_id'   => $teacher->id,
            'teacher_name' => $teacher->name,
            'session_id'   => $session->id,
            'session_title'=> $session->title_ar,
            'class_id'     => $session->class_id,
            'subject_id'   => $session->subject_id,
            'program_id'   => $session->program_id,
            'join_url'     => $joinUrl,
            'auto_started' => isset($payload['started_at']),
            'ip'           => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'تم حفظ رابط الانضمام بنجاح ✓');
    }

    /**
     * Teacher starts the session — records started_at (so students may join), then
     * opens the host start link.
     */
    public function startSession(Request $request, Session $session)
    {
        $teacher = auth()->user();

        // if (!$this->teacherOwnsSession($session, $teacher)) {
        //     abort(403, 'هذه الجلسة غير مسندة لك، تواصل مع الإدارة.');
        // }

        // Mark as started (only once) so students are allowed to join.
        $wasAlreadyStarted = (bool) $session->started_at;
        if (!$session->started_at) {
            $session->update(['started_at' => now()]);
        }

        Log::info('Teacher started session', [
            'teacher_id'    => $teacher->id,
            'teacher_name'  => $teacher->name,
            'session_id'    => $session->id,
            'session_title' => $session->title_ar,
            'class_id'      => $session->class_id,
            'subject_id'    => $session->subject_id,
            'program_id'    => $session->program_id,
            'already_started' => $wasAlreadyStarted,
            'started_at'    => optional($session->started_at)->toDateTimeString(),
            'ip'            => $request->ip(),
        ]);

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

        if (!$this->teacherOwnsSession($session, $teacher)) {
            abort(403, 'هذه الجلسة غير مسندة لك، تواصل مع الإدارة.');
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
