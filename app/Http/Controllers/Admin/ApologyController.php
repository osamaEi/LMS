<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceApology;
use App\Notifications\ApologyReviewedNotification;
use Illuminate\Http\Request;

class ApologyController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $filters = $request->validate([
            'q'          => ['nullable', 'string', 'max:100'],
            'class_id'   => ['nullable', 'integer'],
            'program_id' => ['nullable', 'integer'],
            'teacher_id' => ['nullable', 'integer'],
            'date_from'  => ['nullable', 'date'],
            'date_to'    => ['nullable', 'date'],
        ]);

        // Search + filters, shared by the list and the tab counts so the
        // numbers always match what is shown.
        $filtered = function () use ($filters) {
            // Excuses of ended/deactivated quarters and courses are hidden.
            $query = AttendanceApology::inOpenPeriod();

            if ($term = trim($filters['q'] ?? '')) {
                $like = '%' . addcslashes($term, '%_\\') . '%';
                $query->where(fn ($q) => $q
                    ->where('reason', 'like', $like)
                    ->orWhereHas('student', fn ($s) => $s
                        ->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('student_code', 'like', $like)
                        ->orWhere('phone', 'like', $like))
                    ->orWhereHas('session', fn ($s) => $s
                        ->where('title_ar', 'like', $like)
                        ->orWhere('title_en', 'like', $like)));
            }

            $onSession = fn (string $column, $value) => $value
                ? $query->whereHas('session', fn ($s) => $s->where($column, $value))
                : null;
            $onSession('class_id', $filters['class_id'] ?? null);
            $onSession('program_id', $filters['program_id'] ?? null);
            $onSession('teacher_id', $filters['teacher_id'] ?? null);

            if (!empty($filters['date_from'])) {
                $query->whereHas('session', fn ($s) => $s->whereDate('scheduled_at', '>=', $filters['date_from']));
            }
            if (!empty($filters['date_to'])) {
                $query->whereHas('session', fn ($s) => $s->whereDate('scheduled_at', '<=', $filters['date_to']));
            }

            return $query;
        };

        $query = $filtered()
            ->with([
                'student:id,name,email,student_code',
                'session:id,title_ar,title_en,scheduled_at,class_id,program_id,teacher_id',
                'session.programClass:id,name',
                'session.teacher:id,name',
                'reviewer:id,name',
            ])
            ->latest();

        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        $apologies = $query->paginate(20)->withQueryString();

        $counts = $filtered()->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')->pluck('total', 'status');
        $counts = [
            'pending'  => (int) ($counts['pending'] ?? 0),
            'approved' => (int) ($counts['approved'] ?? 0),
            'rejected' => (int) ($counts['rejected'] ?? 0),
        ];

        // Dropdown options: only groups / programs / teachers that have excuses.
        $sessionIds = AttendanceApology::inOpenPeriod()->distinct()->pluck('session_id');
        $used = \App\Models\Session::whereIn('id', $sessionIds)
            ->get(['class_id', 'program_id', 'teacher_id']);

        $classes = \App\Models\ProgramClass::whereIn('id', $used->pluck('class_id')->filter()->unique())
            ->orderBy('name')->get(['id', 'name']);
        $programs = \App\Models\Program::whereIn('id', $used->pluck('program_id')->filter()->unique())
            ->orderBy('name_ar')->get(['id', 'name_ar', 'name_en']);
        $teachers = \App\Models\User::whereIn('id', $used->pluck('teacher_id')->filter()->unique())
            ->orderBy('name')->get(['id', 'name']);

        return view('admin.apologies.index', compact(
            'apologies', 'counts', 'status', 'filters', 'classes', 'programs', 'teachers'
        ));
    }

    public function approve(Request $request, AttendanceApology $apology)
    {
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
}
