<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'national_id' => 'required|string|unique:users,national_id',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['role'] = 'student';
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'تم إضافة الطالب بنجاح');
    }

    public function show(User $student)
    {
        // Eager load relationships for better performance
        $student->load([
            'program',
            'track',
            'enrollments.subject',
            'payments.program'
        ]);

        // Get all active programs for assignment dropdown
        $programs = Program::where('status', 'active')->get();

        // Get payment statistics
        $totalPayments = $student->payments->sum('total_amount');
        $totalPaid = $student->payments->sum('paid_amount');
        $totalRemaining = $student->payments->sum('remaining_amount');

        return view('admin.students.show', compact('student', 'programs', 'totalPayments', 'totalPaid', 'totalRemaining'));
    }

    public function edit(User $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, User $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'national_id' => 'required|string|unique:users,national_id,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $student->update($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'تم تحديث بيانات الطالب بنجاح');
    }

    public function destroy(User $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'تم حذف الطالب بنجاح');
    }

    public function assignProgram(Request $request, User $student)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
        ]);

        $program = Program::with('terms.subjects')->findOrFail($validated['program_id']);

        // Update student with program
        $student->update([
            'program_id' => $program->id,
            'program_status' => 'approved', // Auto-approve when admin assigns
            'current_term_number' => 1, // Start at first term
            'status' => 'active', // Activate student account
        ]);

        // Auto-enroll student in all subjects of the program
        $enrolledCount = 0;
        foreach ($program->terms as $term) {
            foreach ($term->subjects as $subject) {
                // Check if enrollment already exists
                $exists = \App\Models\Enrollment::where('student_id', $student->id)
                    ->where('subject_id', $subject->id)
                    ->exists();

                if (!$exists) {
                    \App\Models\Enrollment::create([
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'enrolled_at' => now(),
                        'status' => 'active',
                    ]);
                    $enrolledCount++;
                }
            }
        }

        return redirect()->route('admin.students.show', $student)
            ->with('success', "تم تعيين البرنامج للطالب بنجاح وتسجيله في {$enrolledCount} مادة");
    }

    public function removeProgram(User $student)
    {
        $student->update([
            'program_id' => null,
        ]);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'تم إزالة البرنامج من الطالب بنجاح');
    }

    public function toggleStatus(User $student)
    {
        $newStatus = $student->status === 'active' ? 'inactive' : 'active';
        $student->update(['status' => $newStatus]);

        $message = $newStatus === 'active'
            ? 'تم تفعيل حساب الطالب بنجاح'
            : 'تم إلغاء تفعيل حساب الطالب';

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
}
