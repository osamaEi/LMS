<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceApology;
use App\Models\Session;
use App\Notifications\ApologyReviewedNotification;
use Illuminate\Http\Request;

class ApologyController extends Controller
{
    /**
     * Absence apologies for sessions this teacher owns.
     * Mirrors the admin page, but scoped to the teacher's own sessions/classes.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        // The teacher only ever sees apologies for sessions they teach.
        $sessionIds = Session::where('teacher_id', auth()->id())->pluck('id');

        $base = fn() => AttendanceApology::whereIn('session_id', $sessionIds);

        $query = $base()
            ->with([
                'student:id,name,email',
                'session:id,title_ar,title_en,scheduled_at,class_id',
                'reviewer:id,name',
            ])
            ->latest();

        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $apologies = $query->paginate(20)->withQueryString();

        $counts = [
            'pending'  => (clone $base())->where('status', 'pending')->count(),
            'approved' => (clone $base())->where('status', 'approved')->count(),
            'rejected' => (clone $base())->where('status', 'rejected')->count(),
        ];

        return view('teacher.apologies.index', compact('apologies', 'counts', 'status'));
    }

    public function approve(Request $request, AttendanceApology $apology)
    {
        $this->authorizeApology($apology);

        $data = $request->validate([
            'review_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $apology->update([
            'status'      => 'approved',
            'review_note' => $data['review_note'] ?? null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Mark attendance as excused (not attended, but flagged via notes)
        Attendance::updateOrCreate(
            ['student_id' => $apology->student_id, 'session_id' => $apology->session_id],
            ['attended' => false, 'notes' => 'excused']
        );

        $apology->student?->notify(new ApologyReviewedNotification($apology));

        return back()->with('success', 'تم قبول عذر الغياب وتسجيل الطالب كمعذور.');
    }

    public function reject(Request $request, AttendanceApology $apology)
    {
        $this->authorizeApology($apology);

        $data = $request->validate([
            'review_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $apology->update([
            'status'      => 'rejected',
            'review_note' => $data['review_note'] ?? null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // If previously marked excused, clear the excused flag
        $att = Attendance::where('student_id', $apology->student_id)
            ->where('session_id', $apology->session_id)
            ->first();
        if ($att && $att->notes === 'excused') {
            $att->update(['notes' => null]);
        }

        $apology->student?->notify(new ApologyReviewedNotification($apology));

        return back()->with('success', 'تم رفض عذر الغياب.');
    }

    /**
     * A teacher may only review apologies for their own sessions.
     */
    private function authorizeApology(AttendanceApology $apology): void
    {
        abort_unless(
            $apology->session && (int) $apology->session->teacher_id === (int) auth()->id(),
            403,
            'هذا العذر لا يخص محاضراتك.'
        );
    }
}
