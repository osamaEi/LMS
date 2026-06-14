<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Program;
use App\Models\StudentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $statusFilter = $request->get('status');

        $query = User::where('role', 'student')->with('program');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $students = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'      => User::where('role', 'student')->count(),
            'active'     => User::where('role', 'student')->where('status', 'active')->count(),
            'pending'    => User::where('role', 'student')->where('status', 'pending')->count(),
            'inactive'   => User::where('role', 'student')->whereIn('status', ['inactive', 'suspended'])->count(),
            'this_month' => User::where('role', 'student')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
        ];

        return view('admin.students.index', compact('students', 'stats', 'search', 'statusFilter'));
    }

    public function create()
    {
        $programs = Program::where('status', 'active')->orderBy('name_ar')->get(['id', 'name_ar', 'name_en', 'type']);
        return view('admin.students.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'student_code'        => 'nullable|string|max:50|unique:users,student_code',
            'email'               => 'required|email|unique:users,email',
            'national_id'         => 'required|digits:10|unique:users,national_id',
            'phone'               => 'nullable|string|max:20|unique:users,phone',
            'gender'              => 'nullable|in:male,female',
            'date_of_birth'       => 'nullable|date',
            'nationality'         => 'nullable|string|max:100',
            'status'              => 'nullable|in:active,pending,inactive,suspended',
            'specialization'      => 'nullable|string|max:255',
            'specialization_type' => 'nullable|string|max:255',
            'date_of_graduation'  => 'nullable|digits:4|integer|between:1950,2100',
            'bio'                 => 'nullable|string|max:500',
            'profile_photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password'            => 'required|string|min:8|confirmed',
            'program_ids'         => 'nullable|array',
            'program_ids.*'       => 'exists:programs,id',
        ], [
            'name.required'            => 'الاسم مطلوب.',
            'name.max'                 => 'الاسم لا يتجاوز 255 حرفاً.',
            'email.required'           => 'البريد الإلكتروني مطلوب.',
            'email.email'              => 'البريد الإلكتروني غير صحيح.',
            'email.unique'             => 'البريد الإلكتروني مستخدم مسبقاً.',
            'national_id.required'     => 'رقم الهوية مطلوب.',
            'national_id.digits'       => 'رقم الهوية يجب أن يكون 10 أرقام.',
            'national_id.unique'       => 'رقم الهوية مستخدم مسبقاً.',
            'student_code.unique'      => 'كود المتدرب مستخدم مسبقاً.',
            'phone.unique'             => 'رقم الهاتف مستخدم مسبقاً.',
            'password.required'        => 'كلمة المرور مطلوبة.',
            'password.min'             => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed'       => 'تأكيد كلمة المرور غير متطابق.',
            'profile_photo.image'      => 'الصورة يجب أن تكون ملف صورة.',
            'profile_photo.mimes'      => 'الصورة يجب أن تكون JPG أو PNG.',
            'profile_photo.max'        => 'حجم الصورة لا يتجاوز 2 ميجابايت.',
            'date_of_birth.date'              => 'تاريخ الميلاد غير صحيح.',
            'date_of_graduation.digits'       => 'أدخل سنة التخرج بأربعة أرقام.',
            'date_of_graduation.between'      => 'سنة التخرج يجب أن تكون بين 1950 و2100.',
            'program_ids.*.exists'     => 'أحد البرامج المختارة غير موجود.',
        ]);

        $programIds = $validated['program_ids'] ?? [];
        unset($validated['program_ids']);

        if (!empty($validated['date_of_graduation'])) {
            $validated['date_of_graduation'] = $validated['date_of_graduation'] . '-01-01';
        }

        $validated['role'] = 'student';
        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = $validated['status'] ?? 'active';

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('uploads/images', 'public');
        }

        // First chosen program becomes the primary program_id (legacy field)
        if (!empty($programIds)) {
            $validated['program_id'] = $programIds[0];
            $validated['program_status'] = 'approved';
        }

        $student = User::create($validated);

        // Attach all chosen programs via the student_programs pivot
        foreach ($programIds as $pid) {
            $student->programs()->syncWithoutDetaching([
                $pid => ['status' => 'approved', 'enrolled_at' => now()],
            ]);
        }

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'تم إضافة المتدرب بنجاح');
    }

    public function show(User $student)
    {
        // Eager load relationships for better performance
        $student->load([
            'program',
            'track',
            'programs',
            'enrollments.subject',
            'payments.program',
            'documents',
        ]);

        // All classes the student belongs to: legacy class_id + per-program pivot class_id
        $classIds = collect();
        if ($student->class_id) {
            $classIds->push($student->class_id);
        }
        $classIds = $classIds
            ->merge($student->programs->pluck('pivot.class_id')->filter())
            ->unique()
            ->values();

        $studentClasses = $classIds->isNotEmpty()
            ? \App\Models\ProgramClass::whereIn('id', $classIds)
                ->with(['program:id,name_ar,name_en,type', 'teacher:id,name'])
                ->withCount('students')
                ->get()
            : collect();

        // Get all active programs for assignment dropdown
        $programs = Program::where('status', 'active')->get();

        // Get payment statistics
        $totalPayments = $student->payments->sum('total_amount');
        $totalPaid = $student->payments->sum('paid_amount');
        $totalRemaining = $student->payments->sum('remaining_amount');

        return view('admin.students.show', compact('student', 'programs', 'studentClasses', 'totalPayments', 'totalPaid', 'totalRemaining'));
    }

    public function edit(User $student)
    {
        $programs = Program::where('status', 'active')->orderBy('name_ar')->get(['id', 'name_ar', 'name_en', 'type']);
        $student->load('programs');
        $assignedProgramIds = $student->allProgramIds()->all();
        return view('admin.students.edit', compact('student', 'programs', 'assignedProgramIds'));
    }

    public function update(Request $request, User $student)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|unique:users,email,' . $student->id,
            'national_id'         => 'required|digits:10|unique:users,national_id,' . $student->id,
            'phone'               => 'nullable|string|max:20|unique:users,phone,' . $student->id,
            'gender'              => 'nullable|in:male,female',
            'date_of_birth'       => 'nullable|date',
            'nationality'         => 'nullable|string|max:100',
            'status'              => 'nullable|in:active,pending,inactive,suspended',
            'specialization'      => 'nullable|string|max:255',
            'specialization_type' => 'nullable|string|max:255',
            'date_of_graduation'  => 'nullable|date',
            'bio'                 => 'nullable|string|max:500',
            'profile_photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password'            => 'nullable|string|min:8|confirmed',
            'program_ids'         => 'nullable|array',
            'program_ids.*'       => 'exists:programs,id',
        ], [
            'email.unique'       => 'البريد الإلكتروني مستخدم مسبقاً.',
            'national_id.unique' => 'رقم الهوية مستخدم مسبقاً.',
            'national_id.digits' => 'رقم الهوية يجب أن يكون 10 أرقام.',
            'phone.unique'       => 'رقم الهاتف مستخدم مسبقاً.',
        ]);

        $programIds = $validated['program_ids'] ?? null;
        unset($validated['program_ids']);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('profile_photo')) {
            if ($student->profile_photo) {
                Storage::disk('public')->delete($student->profile_photo);
            }
            $validated['profile_photo'] = $request->file('profile_photo')->store('uploads/images', 'public');
        } else {
            unset($validated['profile_photo']);
        }

        $student->update($validated);

        // Sync assigned programs (only when the field was submitted)
        if ($programIds !== null) {
            // Preserve existing pivot class_id where possible
            $existing = $student->programs()->pluck('student_programs.class_id', 'programs.id');
            $syncData = [];
            foreach ($programIds as $pid) {
                $syncData[$pid] = [
                    'status'   => 'approved',
                    'class_id' => $existing[$pid] ?? null,
                ];
            }
            $student->programs()->sync($syncData);

            // Keep legacy primary program_id pointing at one of the assigned programs
            if (!empty($programIds) && !in_array($student->program_id, $programIds)) {
                $student->update(['program_id' => $programIds[0], 'program_status' => 'approved']);
            } elseif (empty($programIds)) {
                $student->update(['program_id' => null]);
            }
        }

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'تم تحديث بيانات المتدرب بنجاح');
    }

    public function destroy(User $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'تم حذف ال متدرب بنجاح');
    }

    public function assignProgram(Request $request, User $student)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
        ]);

        $program = Program::with('terms.subjects')->findOrFail($validated['program_id']);

        $isPrimary = is_null($student->program_id);

        // If no primary program yet → set as primary on users table
        if ($isPrimary) {
            $student->update([
                'program_id'          => $program->id,
                'program_status'      => 'approved',
                'current_term_number' => 1,
                'status'              => 'active',
            ]);
        } else {
            // Already has a primary → add to student_programs pivot
            $student->programs()->syncWithoutDetaching([
                $program->id => [
                    'status'              => 'approved',
                    'current_term_number' => 1,
                    'enrolled_at'         => now()->toDateString(),
                ],
            ]);
            // Ensure student is active
            if ($student->status !== 'active') {
                $student->update(['status' => 'active']);
            }
        }

        // Auto-enroll in all program subjects
        $enrolledCount = 0;
        foreach ($program->terms as $term) {
            foreach ($term->subjects as $subject) {
                $exists = \App\Models\Enrollment::where('student_id', $student->id)
                    ->where('subject_id', $subject->id)->exists();
                if (!$exists) {
                    \App\Models\Enrollment::create([
                        'student_id'  => $student->id,
                        'subject_id'  => $subject->id,
                        'enrolled_at' => now(),
                        'status'      => 'active',
                    ]);
                    $enrolledCount++;
                }
            }
        }

        $label = $isPrimary ? 'البرنامج الأساسي' : 'برنامج إضافي';
        return redirect()->route('admin.students.show', $student)
            ->with('success', "تم إضافة {$label} ({$program->name_ar}) وتسجيل الطالب في {$enrolledCount} مادة");
    }

    public function removeProgram(Request $request, User $student)
    {
        $programId = $request->input('program_id', $student->program_id);

        // If removing primary program
        if ($student->program_id == $programId) {
            // Promote next program from pivot to primary if exists
            $next = $student->programs()->where('programs.id', '!=', $programId)->first();
            $student->update([
                'program_id'     => $next?->id,
                'program_status' => $next ? 'approved' : null,
            ]);
            if ($next) {
                $student->programs()->detach($next->id);
            }
        } else {
            // Remove from pivot only
            $student->programs()->detach($programId);
        }

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'تم إزالة البرنامج من المتدرب بنجاح');
    }

    public function toggleStatus(User $student)
    {
        $newStatus = $student->status === 'active' ? 'inactive' : 'active';
        $student->update(['status' => $newStatus]);

        $message = $newStatus === 'active'
            ? 'تم تفعيل حساب ال متدرب بنجاح'
            : 'تم إلغاء تفعيل حساب المتدرب ';

        return redirect()->route('admin.students.index')
            ->with('success', $message);
    }

    public function export()
    {
        $students = User::where('role', 'student')->latest()->get();

        $filename = 'students_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['#', 'الاسم', 'البريد الإلكتروني', 'رقم الهوية', 'رقم الهاتف', 'الحالة', 'تاريخ التسجيل']);

            $statusMap = ['active' => 'نشط', 'inactive' => 'غير نشط', 'pending' => 'معلق', 'suspended' => 'موقوف'];

            foreach ($students as $i => $student) {
                fputcsv($file, [
                    $i + 1,
                    $student->name,
                    $student->email,
                    $student->national_id ?? '',
                    $student->phone ?? '',
                    $statusMap[$student->status] ?? $student->status,
                    $student->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function approveDocument(Request $request, User $student, StudentDocument $document)
    {
        abort_if($document->user_id !== $student->id, 403);
        $document->approve(auth()->id());
        return response()->json(['success' => true, 'message' => 'تم قبول الوثيقة بنجاح', 'status' => 'approved']);
    }

    public function rejectDocument(Request $request, User $student, StudentDocument $document)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);
        abort_if($document->user_id !== $student->id, 403);
        $document->reject(auth()->id(), $request->reason ?? 'تم الرفض من قبل الإدارة');
        return response()->json(['success' => true, 'message' => 'تم رفض الوثيقة', 'status' => 'rejected']);
    }
}
