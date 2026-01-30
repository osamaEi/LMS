<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function show(Session $session)
    {
        // Load relationships
        $session->load(['subject.term.program', 'files']);

        // Check if the teacher owns this session's subject
        if ($session->subject->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this session.');
        }

        return view('teacher.sessions.show', compact('session'));
    }
}
