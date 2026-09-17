<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceApology;
use App\Models\Session;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApologyController extends Controller
{
    /**
     * GET /api/v1/student/apologies
     * List the student's absence apologies. Optional ?status=pending|approved|rejected
     */
    public function index(Request $request)
    {
        $studentId = auth()->id();

        $query = AttendanceApology::where('student_id', $studentId)
            ->with(['session:id,title_ar,title_en,scheduled_at', 'reviewer:id,name'])
            ->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $apologies = $query->paginate(15);

        return response()->json([
            'success' => true,
            'status'  => $status ?? 'all',
            'stats'   => [
                'pending'  => AttendanceApology::where('student_id', $studentId)->where('status', 'pending')->count(),
                'approved' => AttendanceApology::where('student_id', $studentId)->where('status', 'approved')->count(),
                'rejected' => AttendanceApology::where('student_id', $studentId)->where('status', 'rejected')->count(),
            ],
            'data'    => $apologies->through(fn($apology) => $this->format($apology)),
        ]);
    }

    /**
     * GET /api/v1/student/apologies/{id}
     */
    public function show($id)
    {
        $apology = AttendanceApology::where('student_id', auth()->id())
            ->with(['session:id,title_ar,title_en,scheduled_at', 'reviewer:id,name'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $this->format($apology),
        ]);
    }

    /**
     * POST /api/v1/student/sessions/{sessionId}/apology
     * Submit an absence apology for a session (multipart/form-data)
     *
     * Fields: reason (required), attachment (optional file)
     */
    public function store(Request $request, $sessionId)
    {
        $student = auth()->user();

        $data = $request->validate([
            'reason'     => ['required', 'string', 'min:5', 'max:1000'],
            // Either a real upload or a URL string pointing at an already
            // hosted file.
            'attachment' => $request->hasFile('attachment')
                ? ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096']
                : ['nullable', 'string', 'max:2048'],
        ], [
            'reason.required'    => 'يرجى كتابة سبب الغياب.',
            'reason.min'         => 'السبب قصير جداً.',
            'reason.max'         => 'السبب طويل جداً.',
            'attachment.mimes'   => 'يجب أن يكون المرفق بصيغة PDF أو JPG أو PNG',
            'attachment.max'     => 'حجم المرفق لا يتجاوز 4 ميجابايت',
        ]);

        $session = Session::findOrFail($sessionId);

        // Access is program membership, not an attendance row: an apology is for
        // a session the student did NOT attend, and an attendance row may only be
        // created when they join or when an admin rosters the class. Gating on it
        // would reject exactly the cases an apology exists for.
        $hasAccess = Attendance::where('student_id', $student->id)
            ->where('session_id', $session->id)
            ->exists();

        if (!$hasAccess && $session->subject_id) {
            $programIds = $student->allProgramIds();

            $hasAccess = Subject::where('id', $session->subject_id)
                ->where(fn($q) => $q->whereIn('program_id', $programIds)
                    ->orWhereHas('term', fn($tq) => $tq->whereIn('program_id', $programIds)))
                ->exists();
        }

        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك تقديم عذر لمحاضرة غير مسجل بها.',
            ], 403);
        }

        // One apology per session.
        $existing = AttendanceApology::where('student_id', $student->id)
            ->where('session_id', $session->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'لقد قمت بتقديم عذر لهذه المحاضرة مسبقاً (الحالة: ' . $existing->statusLabelAr() . ').',
                'data'    => $this->format($existing),
            ], 422);
        }

        // Can't apologize for a session the student already attended.
        $alreadyAttended = Attendance::where('student_id', $student->id)
            ->where('session_id', $session->id)
            ->where('attended', true)
            ->exists();

        if ($alreadyAttended) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تقديم عذر لمحاضرة قمت بحضورها.',
            ], 422);
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('apologies', 'public');
        } elseif (!empty($data['attachment'])) {
            $attachmentPath = $data['attachment'];
        }

        $apology = AttendanceApology::create([
            'student_id'      => $student->id,
            'session_id'      => $session->id,
            'reason'          => $data['reason'],
            'attachment_path' => $attachmentPath,
            'status'          => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال عذر الغياب بنجاح، وسيتم مراجعته من قبل الإدارة.',
            'data'    => $this->format($apology->load('session:id,title_ar,title_en,scheduled_at')),
        ], 201);
    }

    /**
     * DELETE /api/v1/student/apologies/{id}
     * Withdraw an apology that is still pending review.
     */
    public function destroy($id)
    {
        $apology = AttendanceApology::where('student_id', auth()->id())->findOrFail($id);

        if (!$apology->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف عذر تمت مراجعته.',
            ], 422);
        }

        // External URLs are not ours to delete.
        if ($apology->attachment_path && !filter_var($apology->attachment_path, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($apology->attachment_path);
        }

        $apology->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف العذر بنجاح',
        ]);
    }

    protected function format(AttendanceApology $apology): array
    {
        return [
            'id'           => $apology->id,
            'reason'       => $apology->reason,
            'status'       => $apology->status,
            'status_label' => $apology->statusLabelAr(),
            'attachment'   => $apology->attachment_url,
            'review_note'  => $apology->review_note,
            'reviewed_by'  => $apology->reviewer?->name,
            'reviewed_at'  => $apology->reviewed_at?->toIso8601String(),
            'created_at'   => $apology->created_at?->toIso8601String(),
            'session'      => $apology->relationLoaded('session') && $apology->session ? [
                'id'           => $apology->session->id,
                'title'        => $apology->session->title,
                'scheduled_at' => $apology->session->scheduled_at?->toIso8601String(),
            ] : null,
        ];
    }
}
