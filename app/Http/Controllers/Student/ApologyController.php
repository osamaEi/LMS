<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AttendanceApology;
use App\Models\Session;
use App\Models\User;
use App\Notifications\ApologySubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ApologyController extends Controller
{
    public function index()
    {
        $base = fn() => AttendanceApology::where('student_id', auth()->id())->inOpenPeriod();

        $apologies = $base()
            ->with(['session:id,title_ar,title_en,scheduled_at', 'reviewer:id,name'])
            ->latest()
            ->paginate(15);

        $counts = [
            'pending'  => $base()->where('status', 'pending')->count(),
            'approved' => $base()->where('status', 'approved')->count(),
            'rejected' => $base()->where('status', 'rejected')->count(),
        ];

        return view('student.apologies.index', compact('apologies', 'counts'));
    }

    public function store(Request $request, Session $session)
    {
        $student = auth()->user();

        $data = $request->validate([
            'reason'     => ['required', 'string', 'min:5', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
        ], [
            'reason.required' => 'يرجى كتابة سبب الغياب.',
            'reason.min'      => 'السبب قصير جداً.',
        ]);

        if ($session->apologiesClosed()) {
            return back()->with('error', 'انتهى الربع التدريبي أو الدورة لهذه المحاضرة، ولم يعد تقديم الأعذار متاحاً.');
        }

        // Prevent duplicate apology for the same session
        $existing = AttendanceApology::where('student_id', $student->id)
            ->where('session_id', $session->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'لقد قمت بتقديم عذر لهذه المحاضرة مسبقاً (الحالة: ' . $existing->statusLabelAr() . ').');
        }

        // Can't apologize for a session the student already attended
        $alreadyAttended = \App\Models\Attendance::where('student_id', $student->id)
            ->where('session_id', $session->id)
            ->where('attended', true)
            ->exists();

        if ($alreadyAttended) {
            return back()->with('error', 'لا يمكن تقديم عذر لمحاضرة قمت بحضورها.');
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('apologies', 'public');
        }

        $apology = AttendanceApology::create([
            'student_id'      => $student->id,
            'session_id'      => $session->id,
            'reason'          => $data['reason'],
            'attachment_path' => $attachmentPath,
            'status'          => 'pending',
        ]);

        // Notify all admins (database + email)
        // $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        // if ($admins->isNotEmpty()) {
        //     Notification::send($admins, new ApologySubmittedNotification($apology));
        // }

        return back()->with('success', 'تم إرسال عذر الغياب بنجاح، وسيتم مراجعته من قبل الإدارة.');
    }
}
