<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Session;

class SessionController extends Controller
{
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
