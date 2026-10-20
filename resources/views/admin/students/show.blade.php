@extends('layouts.dashboard')

@section('title', 'الملف الشخصي للطالب')

@push('styles')
<style>
    .sp { direction:rtl; max-width:1100px; margin:0 auto; color:#0f172a; }
    .sp a { text-decoration:none; }
    .sp-back { display:inline-flex; align-items:center; gap:6px; color:#64748b; font-size:13px; margin-bottom:14px; }
    .sp-back:hover { color:#0071AA; }

    .sp-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; }
    .sp-card + .sp-card { margin-top:16px; }
    .sp-card-h { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:14px 18px; border-bottom:1px solid #f1f5f9; }
    .sp-card-h h3 { margin:0; font-size:14px; font-weight:800; }
    .sp-card-b { padding:16px 18px; }

    .sp-head { display:flex; align-items:center; gap:16px; flex-wrap:wrap; padding:18px; }
    .sp-avatar { width:64px; height:64px; border-radius:50%; overflow:hidden; flex-shrink:0; background:#0071AA; color:#fff;
                 display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:800; }
    .sp-avatar img { width:100%; height:100%; object-fit:cover; }
    .sp-name { margin:0; font-size:20px; font-weight:800; }
    .sp-sub { margin-top:4px; font-size:13px; color:#64748b; display:flex; flex-wrap:wrap; gap:4px 12px; }
    .sp-code { font-family:monospace; font-weight:700; color:#0071AA; }
    .sp-actions { margin-inline-start:auto; display:flex; flex-wrap:wrap; gap:8px; }

    .sp-btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:9px 16px; border-radius:10px;
              font-size:13px; font-weight:700; border:1px solid #e2e8f0; background:#fff; color:#334155; cursor:pointer; font-family:inherit; }
    .sp-btn:hover { background:#f8fafc; }
    .sp-btn-primary { background:#0071AA; border-color:#0071AA; color:#fff; }
    .sp-btn-primary:hover { background:#005a88; }
    .sp-btn-green { background:#16a34a; border-color:#16a34a; color:#fff; }
    .sp-btn-red { background:#fff; border-color:#fecaca; color:#dc2626; }
    .sp-btn-sm { padding:6px 12px; font-size:12px; }
    .sp-btn[disabled] { opacity:.5; cursor:not-allowed; }

    .sp-pill { display:inline-block; padding:2px 10px; border-radius:999px; font-size:11px; font-weight:700; background:#f1f5f9; color:#475569; }
    .sp-pill.green { background:#dcfce7; color:#15803d; }
    .sp-pill.red { background:#fee2e2; color:#b91c1c; }
    .sp-pill.amber { background:#fef3c7; color:#b45309; }
    .sp-pill.blue { background:#e0f2fe; color:#0071AA; }

    .sp-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin:16px 0; }
    .sp-stat { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:14px; text-align:center; }
    .sp-stat b { display:block; font-size:22px; font-weight:800; color:#0071AA; }
    .sp-stat span { font-size:12px; color:#64748b; }

    .sp-tabs { display:flex; gap:4px; overflow-x:auto; border-bottom:1px solid #e5e7eb; margin-bottom:16px; }
    .sp-tab { padding:10px 14px; border:0; background:none; font-family:inherit; font-size:13px; font-weight:700; color:#64748b;
              border-bottom:2px solid transparent; cursor:pointer; white-space:nowrap; }
    .sp-tab.active { color:#0071AA; border-bottom-color:#0071AA; }
    .sp-tab .sp-pill { margin-inline-start:4px; }
    .sp-panel { display:none; }
    .sp-panel.active { display:block; }

    .sp-grid { display:grid; grid-template-columns:2fr 1fr; gap:16px; align-items:start; }
    .sp-dl { display:grid; grid-template-columns:repeat(2,1fr); gap:12px 20px; margin:0; }
    .sp-dl dt { font-size:12px; color:#94a3b8; }
    .sp-dl dd { margin:2px 0 0; font-size:14px; font-weight:600; overflow-wrap:anywhere; }
    .sp-rows > div { display:flex; justify-content:space-between; gap:10px; padding:8px 0; border-bottom:1px solid #f1f5f9; font-size:13px; }
    .sp-rows > div:last-child { border-bottom:0; }
    .sp-rows span:first-child { color:#64748b; }
    .sp-rows span:last-child { font-weight:600; text-align:left; overflow-wrap:anywhere; }

    .sp-list-item { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 12px; border:1px solid #f1f5f9; border-radius:10px; }
    .sp-list-item + .sp-list-item { margin-top:8px; }
    .sp-list-item small { display:block; color:#94a3b8; font-size:12px; margin-top:2px; }

    .sp-table { width:100%; border-collapse:collapse; font-size:13px; }
    .sp-table th { text-align:right; padding:10px 12px; background:#f8fafc; color:#64748b; font-size:12px; font-weight:700; border-bottom:1px solid #e5e7eb; white-space:nowrap; }
    .sp-table td { padding:10px 12px; border-bottom:1px solid #f1f5f9; }
    .sp-scroll { overflow-x:auto; }

    .sp-input { width:100%; box-sizing:border-box; border:1px solid #e2e8f0; border-radius:10px; padding:9px 12px; font-size:13px; font-family:inherit; background:#fff; }
    .sp-input:focus { outline:none; border-color:#0071AA; }
    .sp-label { display:block; font-size:13px; font-weight:700; margin-bottom:6px; }
    .sp-empty { text-align:center; padding:28px; color:#94a3b8; font-size:13px; }
    .sp-alert { padding:12px 14px; border-radius:10px; font-size:13px; margin-bottom:14px; }
    .sp-alert.ok { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
    .sp-alert.err { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }

    .sp-docs { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:16px; }
    .sp-doc-img { height:200px; background:#f8fafc; display:flex; align-items:center; justify-content:center; overflow:hidden; border-bottom:1px solid #f1f5f9; }
    .sp-doc-img img { max-width:100%; max-height:100%; object-fit:contain; cursor:zoom-in; }

    .sp-overlay { display:none; position:fixed; inset:0; z-index:9990; background:rgba(15,23,42,.7); align-items:center; justify-content:center; padding:16px; }
    .sp-modal { background:#fff; border-radius:14px; padding:20px; width:100%; max-width:440px; }
    .sp-toast { display:none; position:fixed; bottom:24px; left:50%; transform:translateX(-50%); padding:10px 20px; border-radius:10px; color:#fff; font-size:13px; font-weight:700; z-index:99999; }

    @media (max-width: 900px) {
        .sp-grid { grid-template-columns:1fr; }
        .sp-stats { grid-template-columns:repeat(2,1fr); }
    }
    @media (max-width: 560px) {
        .sp-dl { grid-template-columns:1fr; }
        .sp-actions { margin-inline-start:0; width:100%; }
    }
</style>
@endpush

@section('content')
@include('admin.partials.consent-records', ['consentUser' => $student])
@php
    $completedEnrollments = $student->enrollments->where('status', 'completed')->whereNotNull('final_grade');
    $averageGrade = $completedEnrollments->count() > 0 ? $completedEnrollments->avg('final_grade') : 0;

    $totalAttendances = $student->attendances->count();
    $presentCount = $student->attendances->where('attended', true)->count();
    $absentCount = $student->attendances->where('attended', false)->count();
    $attendancePercentage = $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100) : 0;

    $openTicketsCount = $student->tickets->whereIn('status', ['open', 'in_progress'])->count();
    $pendingDocs = $student->documents ? $student->documents->where('status', 'pending')->count() : 0;

    $statusPill = ['active' => 'green', 'pending' => 'amber', 'inactive' => '', 'suspended' => 'red'][$student->status] ?? '';
    $canEnterDashboard = $student->status === 'active' && !session()->has('student_dashboard_admin_id');

    // Open the notification tab again after sending / a validation error.
    $initialTab = (session('notif_success') || $errors->any()) ? 'notification' : 'personal';
@endphp

<div class="sp">
    <a href="{{ route('admin.students.index') }}" class="sp-back">→ العودة لقائمة المتدربين</a>

    @if(session('success'))<div class="sp-alert ok">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="sp-alert err">{{ session('error') }}</div>@endif

    {{-- Header --}}
    <div class="sp-card sp-head">
        <div class="sp-avatar">
            @if($student->profile_photo)
                <img src="{{ asset('storage/' . $student->profile_photo) }}" alt="{{ $student->name }}">
            @else
                {{ mb_substr($student->name, 0, 1) }}
            @endif
        </div>
        <div>
            <h1 class="sp-name">{{ $student->name }} <span class="sp-pill {{ $statusPill }}">{{ $student->getStatusDisplayName() }}</span></h1>
            <div class="sp-sub">
                @if($student->student_code)<span class="sp-code">{{ $student->student_code }}</span>@endif
                <span dir="ltr">{{ $student->email }}</span>
                @if($student->phone)<span dir="ltr">{{ $student->phone }}</span>@endif
                @if($student->program)<span>{{ $student->program->name }}</span>@endif
            </div>
        </div>
        <div class="sp-actions">
            @if($canEnterDashboard)
                <form action="{{ route('admin.students.dashboard-access', $student) }}" method="POST">
                    @csrf
                    <button type="submit" class="sp-btn sp-btn-primary">دخول لوحة الطالب</button>
                </form>
            @else
                <button type="button" class="sp-btn sp-btn-primary" disabled
                        title="{{ $student->status !== 'active' ? 'متاح للحسابات المفعّلة فقط' : 'أنت تتصفح حسابًا آخر حاليًا' }}">دخول لوحة الطالب</button>
            @endif
            <a href="{{ route('admin.students.report.show', $student) }}" class="sp-btn">التقرير الأكاديمي</a>
            <a href="{{ route('admin.students.edit', $student) }}" class="sp-btn">تعديل</a>
        </div>
    </div>

    {{-- Numbers --}}
    <div class="sp-stats">
        <div class="sp-stat"><b>{{ $student->enrollments->count() }}</b><span>إجمالي المقررات</span></div>
        <div class="sp-stat"><b>{{ $student->enrollments->where('status', 'active')->count() }}</b><span>مقررات نشطة</span></div>
        <div class="sp-stat"><b>{{ $averageGrade > 0 ? number_format($averageGrade, 1) : '-' }}</b><span>المعدل التراكمي</span></div>
        <div class="sp-stat"><b>{{ $totalAttendances > 0 ? $attendancePercentage . '%' : '-' }}</b><span>نسبة الحضور</span></div>
    </div>

    {{-- Tabs --}}
    <div class="sp-tabs" role="tablist">
        @foreach([
            'personal'     => ['المعلومات', null],
            'grades'       => ['الدرجات', $student->enrollments->count() ?: null],
            'attendance'   => ['الحضور', null],
            'payments'     => ['المدفوعات', null],
            'documents'    => ['المستندات', $pendingDocs ?: null],
            'tickets'      => ['التذاكر', $openTicketsCount ?: null],
            'notification' => ['إرسال إشعار', null],
        ] as $key => [$label, $badge])
            <button type="button" class="sp-tab {{ $initialTab === $key ? 'active' : '' }}" data-tab="{{ $key }}" role="tab">
                {{ $label }}@if($badge)<span class="sp-pill {{ $key === 'documents' ? 'amber' : 'blue' }}">{{ $badge }}</span>@endif
            </button>
        @endforeach
    </div>

    {{-- Personal --}}
    <div class="sp-panel {{ $initialTab === 'personal' ? 'active' : '' }}" id="tab-personal">
        <div class="sp-grid">
            <div>
                <div class="sp-card">
                    <div class="sp-card-h"><h3>البيانات الشخصية</h3></div>
                    <div class="sp-card-b">
                        <dl class="sp-dl">
                            <div><dt>الاسم الكامل</dt><dd>{{ $student->name }}</dd></div>
                            <div><dt>كود المتدرب</dt><dd class="sp-code">{{ $student->student_code ?? '—' }}</dd></div>
                            <div><dt>البريد الإلكتروني</dt><dd dir="ltr" style="text-align:right">{{ $student->email }}</dd></div>
                            <div><dt>رقم الهوية</dt><dd>{{ $student->national_id ?? 'غير محدد' }}</dd></div>
                            <div><dt>رقم الهاتف</dt><dd dir="ltr" style="text-align:right">{{ $student->phone ?? 'غير محدد' }}</dd></div>
                            <div><dt>تاريخ الميلاد</dt><dd>{{ $student->date_of_birth ? $student->date_of_birth->format('Y/m/d') : 'غير محدد' }}</dd></div>
                            <div><dt>الجنس</dt><dd>{{ $student->gender === 'male' ? 'ذكر' : ($student->gender === 'female' ? 'أنثى' : 'غير محدد') }}</dd></div>
                            <div><dt>الجنسية</dt><dd>{{ $student->nationality ?? 'غير محدد' }}</dd></div>
                            <div><dt>نوع المؤهل</dt><dd>{{ $student->specialization ?? 'غير محدد' }}</dd></div>
                            <div><dt>المؤهل التعليمي</dt><dd>{{ $student->specialization_type ?? 'غير محدد' }}</dd></div>
                            <div><dt>تاريخ التخرج</dt><dd>{{ $student->date_of_graduation ? $student->date_of_graduation->format('Y') : 'غير محدد' }}</dd></div>
                        </dl>
                    </div>
                </div>

                <div class="sp-card">
                    <div class="sp-card-h"><h3>المجموعات</h3></div>
                    <div class="sp-card-b">
                        @php
                            $clsTypeMap = ['diploma' => 'دبلومة', 'course' => 'دورة', 'english' => 'إنجليزي', 'training' => 'تدريب'];
                        @endphp
                        @forelse($studentClasses as $cls)
                            <a href="{{ route('admin.classes.show', $cls->id) }}" class="sp-list-item" style="color:inherit">
                                <div>
                                    <b>{{ $cls->name }}</b>
                                    <small>{{ $cls->program?->name_ar ?? '—' }} · {{ $cls->students_count }} طالب @if($cls->teacher) · {{ $cls->teacher->name }} @endif</small>
                                </div>
                                <span class="sp-pill">{{ $clsTypeMap[$cls->program?->type] ?? '—' }}</span>
                            </a>
                        @empty
                            <p class="sp-empty">المتدرب غير مسند لأي مجموعة بعد</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div>
                {{-- Program --}}
                <div class="sp-card">
                    <div class="sp-card-h"><h3>البرنامج التدريبي</h3></div>
                    <div class="sp-card-b">
                        @if($student->program && $student->program_status === 'pending')
                            <div class="sp-alert" style="background:#fffbeb;border:1px solid #fde68a;color:#92400e">
                                طلب تسجيل بانتظار الموافقة:<br><b>{{ $student->program->name }}</b>
                                @if($student->program->code)<small> ({{ $student->program->code }})</small>@endif
                            </div>
                            <div style="display:flex;gap:8px">
                                <form action="{{ route('admin.program-enrollments.approve', $student) }}" method="POST" style="flex:1">
                                    @csrf
                                    <button type="submit" class="sp-btn sp-btn-green" style="width:100%">قبول</button>
                                </form>
                                <form action="{{ route('admin.program-enrollments.reject', $student) }}" method="POST" style="flex:1"
                                      onsubmit="return confirm('هل أنت متأكد من رفض طلب التسجيل؟');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="sp-btn sp-btn-red" style="width:100%">رفض</button>
                                </form>
                            </div>
                        @else
                            @php
                                $extraPrograms = $student->programs()->get();
                                $allAssigned   = collect();
                                if ($student->program) $allAssigned->push(['program' => $student->program, 'primary' => true]);
                                foreach ($extraPrograms as $ep) {
                                    if ($ep->id !== $student->program_id) $allAssigned->push(['program' => $ep, 'primary' => false]);
                                }
                            @endphp

                            @forelse($allAssigned as $entry)
                                @php $prog = $entry['program']; @endphp
                                <div class="sp-list-item">
                                    <div>
                                        <b>{{ $prog->name_ar }}</b>
                                        @if($entry['primary'])<span class="sp-pill blue">أساسي</span>@endif
                                        <small>{{ $prog->code }} · المدة: {{ $prog->duration_label }}</small>
                                    </div>
                                    <form action="{{ route('admin.students.remove-program', $student) }}" method="POST" onsubmit="return confirm('إزالة البرنامج؟')">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="program_id" value="{{ $prog->id }}">
                                        <button type="submit" class="sp-btn sp-btn-red sp-btn-sm" title="إزالة">×</button>
                                    </form>
                                </div>
                            @empty
                                <p class="sp-empty" style="padding:8px 0 14px">لم يتم تعيين برنامج لهذا المتدرب</p>
                            @endforelse

                            <form action="{{ route('admin.students.assign-program', $student) }}" method="POST" style="margin-top:12px">
                                @csrf
                                <select name="program_id" required class="sp-input" style="margin-bottom:8px">
                                    <option value="">{{ $allAssigned->isEmpty() ? 'اختر البرنامج...' : '+ إضافة برنامج آخر...' }}</option>
                                    @foreach($programs as $prog)
                                        @if($prog->id !== $student->program_id && !$extraPrograms->contains('id', $prog->id))
                                            <option value="{{ $prog->id }}">{{ $prog->name_ar }} ({{ $prog->code }})</option>
                                        @endif
                                    @endforeach
                                </select>
                                <button type="submit" class="sp-btn sp-btn-primary" style="width:100%">{{ $allAssigned->isEmpty() ? 'تعيين البرنامج' : 'إضافة برنامج' }}</button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Account --}}
                <div class="sp-card">
                    <div class="sp-card-h"><h3>الحساب</h3></div>
                    <div class="sp-card-b sp-rows">
                        <div><span>البريد الإلكتروني</span><span class="sp-pill {{ $student->email_verified_at ? 'green' : 'red' }}">{{ $student->email_verified_at ? 'موثق' : 'غير موثق' }}</span></div>
                        <div><span>رقم الهاتف</span><span class="sp-pill {{ $student->phone_verified_at ? 'green' : 'red' }}">{{ $student->phone_verified_at ? 'موثق' : 'غير موثق' }}</span></div>
                        <div><span>اكتمال الملف</span><span class="sp-pill {{ $student->profile_completed_at ? 'green' : 'amber' }}">{{ $student->profile_completed_at ? 'مكتمل' : 'غير مكتمل' }}</span></div>
                        <div><span>تاريخ الإنشاء</span><span>{{ $student->created_at->format('Y/m/d') }}</span></div>
                        <div><span>آخر تحديث</span><span>{{ $student->updated_at->diffForHumans() }}</span></div>
                        <div><span>IP التسجيل</span><span dir="ltr">{{ $student->registration_ip ?? 'غير مسجل' }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grades --}}
    <div class="sp-panel {{ $initialTab === 'grades' ? 'active' : '' }}" id="tab-grades">
        <div class="sp-card">
            @if($student->enrollments->count() > 0)
                <div class="sp-scroll">
                    <table class="sp-table">
                        <thead>
                            <tr><th>المقرر</th><th>الكود</th><th>الفصل</th><th>الدرجة النهائية</th><th>التقدير</th><th>الحالة</th></tr>
                        </thead>
                        <tbody>
                            @foreach($student->enrollments as $enrollment)
                                @php
                                    $finalGrade = $enrollment->final_grade;
                                    $gradeLetter = $enrollment->grade_letter ?? ($finalGrade ? $enrollment->calculateGradeLetter() : null);
                                    $statusMap = ['active' => ['نشط', 'blue'], 'completed' => ['مكتمل', 'green'], 'withdrawn' => ['منسحب', ''], 'failed' => ['راسب', 'red']];
                                    [$stLabel, $stColor] = $statusMap[$enrollment->status] ?? [$enrollment->status, ''];
                                @endphp
                                <tr>
                                    <td><b>{{ $enrollment->subject->name ?? 'غير محدد' }}</b></td>
                                    <td>{{ $enrollment->subject->code ?? '-' }}</td>
                                    <td>{{ $enrollment->subject->term->name ?? '-' }}</td>
                                    <td><b>{{ $finalGrade ? number_format($finalGrade, 1) : '-' }}</b></td>
                                    <td>{{ $gradeLetter ?? '-' }}</td>
                                    <td><span class="sp-pill {{ $stColor }}">{{ $stLabel }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="sp-empty">لا توجد درجات مسجلة</p>
            @endif
        </div>
    </div>

    {{-- Attendance --}}
    <div class="sp-panel {{ $initialTab === 'attendance' ? 'active' : '' }}" id="tab-attendance">
        <div class="sp-stats" style="grid-template-columns:repeat(3,1fr);margin-top:0">
            <div class="sp-stat"><b>{{ $attendancePercentage }}%</b><span>نسبة الحضور</span></div>
            <div class="sp-stat"><b style="color:#15803d">{{ $presentCount }}</b><span>حضور</span></div>
            <div class="sp-stat"><b style="color:#b91c1c">{{ $absentCount }}</b><span>غياب</span></div>
        </div>
        <div class="sp-card">
            <div class="sp-card-h"><h3>آخر السجلات</h3></div>
            <div class="sp-card-b">
                @forelse($student->attendances->sortByDesc('created_at')->take(10) as $attendance)
                    <div class="sp-list-item">
                        <div>
                            <b>{{ $attendance->session->subject->name ?? 'جلسة' }}</b>
                            <small>{{ $attendance->session->title ?? 'جلسة دراسية' }} · {{ $attendance->created_at->format('Y/m/d H:i') }}</small>
                        </div>
                        <span class="sp-pill {{ $attendance->attended ? 'green' : 'red' }}">{{ $attendance->attended ? 'حاضر' : 'غائب' }}</span>
                    </div>
                @empty
                    <p class="sp-empty">لا توجد سجلات حضور</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Payments --}}
    <div class="sp-panel {{ $initialTab === 'payments' ? 'active' : '' }}" id="tab-payments">
        <div class="sp-stats" style="grid-template-columns:repeat(3,1fr);margin-top:0">
            <div class="sp-stat"><b>{{ number_format($totalPayments, 2) }} <x-riyal /></b><span>إجمالي الرسوم</span></div>
            <div class="sp-stat"><b style="color:#15803d">{{ number_format($totalPaid, 2) }} <x-riyal /></b><span>المدفوع</span></div>
            <div class="sp-stat"><b style="color:#b91c1c">{{ number_format($totalRemaining, 2) }} <x-riyal /></b><span>المتبقي</span></div>
        </div>
        <div class="sp-card">
            <div class="sp-card-h">
                <h3>سجل المدفوعات</h3>
                <a href="{{ route('admin.payments.create') }}?user_id={{ $student->id }}" class="sp-btn sp-btn-primary sp-btn-sm">+ إنشاء دفعة</a>
            </div>
            @if($student->payments->count() > 0)
                <div class="sp-scroll">
                    <table class="sp-table">
                        <thead>
                            <tr><th>#</th><th>البرنامج</th><th>نوع الدفع</th><th>الإجمالي</th><th>المدفوع</th><th>المتبقي</th><th>الحالة</th><th></th></tr>
                        </thead>
                        <tbody>
                            @foreach($student->payments as $payment)
                                @php
                                    [$pLabel, $pColor] = [
                                        'completed' => ['مكتملة', 'green'], 'partial' => ['جزئية', 'blue'],
                                        'pending' => ['قيد الانتظار', ''], 'cancelled' => ['ملغاة', 'red'],
                                    ][$payment->status] ?? [$payment->status, ''];
                                @endphp
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td><b>{{ $payment->program->name_ar ?? '—' }}</b></td>
                                    <td>{{ $payment->payment_type === 'full' ? 'دفعة كاملة' : 'تقسيط' }}</td>
                                    <td>{{ number_format($payment->total_amount, 2) }}</td>
                                    <td style="color:#15803d">{{ number_format($payment->paid_amount, 2) }}</td>
                                    <td style="color:#b91c1c">{{ number_format($payment->remaining_amount, 2) }}</td>
                                    <td><span class="sp-pill {{ $pColor }}">{{ $pLabel }}</span></td>
                                    <td><a href="{{ route('admin.payments.show', $payment) }}" class="sp-btn sp-btn-sm">عرض</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="sp-empty">لا توجد دفعات مسجلة لهذا المتدرب</p>
            @endif
        </div>
    </div>

    {{-- Documents --}}
    <div class="sp-panel {{ $initialTab === 'documents' ? 'active' : '' }}" id="tab-documents">
        @if($student->documents && $student->documents->count() > 0)
            @php $docsKeyed = $student->documents->keyBy('document_type'); @endphp
            <div class="sp-docs">
                @foreach(['national_id_front' => 'الوجه الأمامي للهوية الوطنية', 'national_id_back' => 'الوجه الخلفي للهوية الوطنية', 'certificate' => 'الشهادة'] as $type => $label)
                    @php
                        $doc = $docsKeyed->get($type);
                        $sb = $doc ? match($doc->status) {
                            'approved' => ['green', 'مقبول'], 'rejected' => ['red', 'مرفوض'], default => ['amber', 'قيد المراجعة'],
                        } : null;
                    @endphp
                    <div class="sp-card" id="card-{{ $type }}" style="overflow:hidden">
                        <div class="sp-card-h">
                            <h3>{{ $label }}</h3>
                            @if($sb)<span class="sp-pill {{ $sb[0] }}" id="status-badge-{{ $type }}">{{ $sb[1] }}</span>@endif
                        </div>
                        <div class="sp-doc-img">
                            @if($doc && $doc->file_path)
                                @php $isImg = in_array(strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']); @endphp
                                @if($isImg)
                                    <img src="{{ asset('storage/' . $doc->file_path) }}" alt="{{ $label }}"
                                         onclick="openDocLightbox(this.src, '{{ $label }}')">
                                @else
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="sp-btn">فتح ملف PDF</a>
                                @endif
                            @else
                                <span class="sp-empty">لم يتم الرفع بعد</span>
                            @endif
                        </div>
                        @if($doc)
                            <div class="sp-card-b">
                                <div style="font-size:12px;color:#64748b;margin-bottom:10px">
                                    {{ $doc->file_size_human }} · {{ $doc->created_at->format('Y/m/d') }}
                                    @if($doc->reviewed_at) · راجعه: {{ $doc->reviewer?->name ?? 'الإدارة' }} @endif
                                </div>
                                <div style="display:flex;gap:8px;flex-wrap:wrap">
                                    <a href="{{ asset('storage/' . $doc->file_path) }}" download="{{ $doc->original_name }}" class="sp-btn sp-btn-sm">تنزيل</a>
                                    <button type="button" class="sp-btn sp-btn-green sp-btn-sm doc-approve-btn" data-doc-id="{{ $doc->id }}" data-action="approve"
                                            onclick="reviewDocument({{ $doc->id }}, '{{ $type }}', 'approve')"
                                            style="{{ $doc->status === 'approved' ? 'display:none' : '' }}">قبول</button>
                                    <button type="button" class="sp-btn sp-btn-red sp-btn-sm doc-reject-btn" data-doc-id="{{ $doc->id }}" data-action="reject"
                                            onclick="openRejectModal({{ $doc->id }}, '{{ $type }}')"
                                            style="{{ $doc->status === 'rejected' ? 'display:none' : '' }}">رفض</button>
                                </div>
                                @if($doc->rejection_reason && $doc->status === 'rejected')
                                    <div class="sp-alert err doc-reject-reason-box" style="margin:10px 0 0">سبب الرفض: {{ $doc->rejection_reason }}</div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="sp-card"><p class="sp-empty">لم يتم رفع أي مستندات بعد</p></div>
        @endif
    </div>

    {{-- Tickets --}}
    <div class="sp-panel {{ $initialTab === 'tickets' ? 'active' : '' }}" id="tab-tickets">
        <div class="sp-card">
            <div class="sp-card-b">
                @forelse($student->tickets->sortByDesc('created_at') as $ticket)
                    <div class="sp-list-item">
                        <div>
                            <b>#{{ substr($ticket->ticket_number, -4) }} — {{ $ticket->subject }}</b>
                            <small>{{ Str::limit($ticket->description, 100) }}</small>
                            <small>{{ $ticket->getPriorityLabel() }} · {{ $ticket->created_at->diffForHumans() }}</small>
                        </div>
                        <span class="sp-pill {{ in_array($ticket->status, ['open', 'in_progress']) ? 'amber' : 'green' }}">{{ $ticket->getStatusLabel() }}</span>
                    </div>
                @empty
                    <p class="sp-empty">لا توجد تذاكر دعم</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Notification --}}
    <div class="sp-panel {{ $initialTab === 'notification' ? 'active' : '' }}" id="tab-notification">
        <div class="sp-card">
            <div class="sp-card-h"><h3>إرسال إشعار إلى {{ $student->name }}</h3></div>
            <div class="sp-card-b">
                @if(session('notif_success'))<div class="sp-alert ok">{{ session('notif_success') }}</div>@endif
                @if($errors->any())
                    <div class="sp-alert err">@foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach</div>
                @endif

                <form action="{{ route('notifications.send') }}" method="POST" style="display:flex;flex-direction:column;gap:14px">
                    @csrf
                    <input type="hidden" name="send_mode" value="individual">
                    <input type="hidden" name="user_ids[]" value="{{ $student->id }}">

                    <div>
                        <label class="sp-label">عنوان الإشعار <span style="color:#dc2626">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required maxlength="255" class="sp-input"
                               placeholder="مثال: تنبيه مهم، رسالة من الإدارة...">
                    </div>
                    <div>
                        <label class="sp-label">نص الإشعار <span style="color:#dc2626">*</span></label>
                        <textarea name="body" required maxlength="1000" rows="4" class="sp-input" style="resize:vertical"
                                  placeholder="اكتب تفاصيل الإشعار هنا...">{{ old('body') }}</textarea>
                    </div>
                    <div>
                        <label class="sp-label">رابط الإجراء <span style="font-weight:400;color:#94a3b8">(اختياري)</span></label>
                        <input type="url" name="action_url" value="{{ old('action_url') }}" placeholder="https://..." class="sp-input" dir="ltr">
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center">
                        <span style="font-size:12px;color:#64748b">قوالب سريعة:</span>
                        @foreach([
                            ['label' => 'تذكير بالحضور',  'title' => 'تذكير بحضور الجلسة',     'body' => 'نود تذكيرك بأن لديك جلسة قادمة، يرجى الحضور في الوقت المحدد.'],
                            ['label' => 'تحديث البرنامج', 'title' => 'تحديث مهم بشأن برنامجك', 'body' => 'نود إعلامك بوجود تحديثات مهمة تخص برنامجك التدريبي .'],
                            ['label' => 'إشعار دفع',      'title' => 'تذكير بموعد السداد',      'body' => 'يرجى إتمام عملية السداد في أقرب وقت ممكن لتجنب تعليق الحساب.'],
                            ['label' => 'تهنئة',          'title' => 'تهانينا على تميزك!',     'body' => 'نهنئك على أدائك المتميز ونتمنى لك مزيداً من التوفيق والنجاح.'],
                        ] as $tpl)
                            <button type="button" class="sp-btn sp-btn-sm"
                                    onclick="applyTemplate('{{ addslashes($tpl['title']) }}','{{ addslashes($tpl['body']) }}')">{{ $tpl['label'] }}</button>
                        @endforeach
                    </div>
                    <button type="submit" class="sp-btn sp-btn-primary">إرسال الإشعار للطالب</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Document lightbox --}}
<div id="doc-lightbox" class="sp-overlay" onclick="closeDocLightbox()">
    <div style="text-align:center" onclick="event.stopPropagation()">
        <img id="lightbox-img" src="" alt="" style="max-width:90vw;max-height:80vh;border-radius:12px">
        <div id="lightbox-label" style="color:#fff;margin-top:10px;font-weight:700"></div>
        <button type="button" class="sp-btn" style="margin-top:10px" onclick="closeDocLightbox()">إغلاق</button>
    </div>
</div>

{{-- Reject document modal --}}
<div id="reject-modal" class="sp-overlay">
    <div class="sp-modal" onclick="event.stopPropagation()">
        <h3 style="margin:0;font-size:16px;font-weight:800">رفض الوثيقة</h3>
        <p id="reject-modal-subtitle" style="margin:4px 0 14px;font-size:13px;color:#64748b"></p>
        <label class="sp-label" for="reject-reason-input">سبب الرفض (اختياري)</label>
        <textarea id="reject-reason-input" rows="3" class="sp-input" style="resize:none" placeholder="اكتب سبب رفض الوثيقة..."></textarea>
        <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:14px">
            <button type="button" class="sp-btn" onclick="closeRejectModal()">إلغاء</button>
            <button type="button" class="sp-btn sp-btn-red" id="reject-submit-btn" onclick="submitReject()">تأكيد الرفض</button>
        </div>
    </div>
</div>

<div id="doc-toast" class="sp-toast"></div>

@push('scripts')
<script>
/* ── Tabs ── */
document.querySelectorAll('.sp-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.sp-tab').forEach(b => b.classList.toggle('active', b === btn));
        document.querySelectorAll('.sp-panel').forEach(p => p.classList.toggle('active', p.id === 'tab-' + btn.dataset.tab));
    });
});

/* ── Notification quick templates ── */
function applyTemplate(title, body) {
    const form = document.querySelector('#tab-notification form');
    form.querySelector('[name="title"]').value = title;
    form.querySelector('[name="body"]').value  = body;
}

/* ── Lightbox ── */
function openDocLightbox(src, label) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox-label').textContent = label || '';
    document.getElementById('doc-lightbox').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeDocLightbox() {
    document.getElementById('doc-lightbox').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeDocLightbox(); closeRejectModal(); } });

/* ── Reject modal ── */
let _rejectDocId = null, _rejectDocType = null;
const DOC_LABELS = { national_id_front: 'الوجه الأمامي للهوية الوطنية', national_id_back: 'الوجه الخلفي للهوية الوطنية', certificate: 'الشهادة' };

function openRejectModal(docId, docType) {
    _rejectDocId = docId; _rejectDocType = docType;
    document.getElementById('reject-modal-subtitle').textContent = DOC_LABELS[docType] || docType;
    document.getElementById('reject-reason-input').value = '';
    document.getElementById('reject-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('reject-reason-input').focus(), 50);
}
function closeRejectModal() {
    document.getElementById('reject-modal').style.display = 'none';
    document.body.style.overflow = '';
}
function submitReject() {
    const reason = document.getElementById('reject-reason-input').value.trim();
    closeRejectModal();
    reviewDocument(_rejectDocId, _rejectDocType, 'reject', reason);
}

/* ── Approve / reject via AJAX ── */
function reviewDocument(docId, docType, action, reason) {
    const url = `/admin/students/{{ $student->id }}/documents/${docId}/${action === 'approve' ? 'approve' : 'reject'}`;
    const body = new URLSearchParams();
    body.append('_token', '{{ csrf_token() }}');
    if (action === 'reject' && reason) body.append('reason', reason);

    const btn = document.querySelector(`[data-doc-id="${docId}"][data-action="${action}"]`);
    if (btn) btn.disabled = true;

    fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' }, body })
        .then(r => r.json())
        .then(data => {
            if (btn) btn.disabled = false;
            if (data.success) {
                showDocToast(data.message, action === 'approve' ? '#16a34a' : '#dc2626');
                updateCardStatus(docType, data.status, reason);
            } else {
                showDocToast(data.message || 'حدث خطأ', '#dc2626');
            }
        })
        .catch(() => {
            if (btn) btn.disabled = false;
            showDocToast('فشل الاتصال بالخادم', '#dc2626');
        });
}

function updateCardStatus(docType, status, reason) {
    const card = document.getElementById('card-' + docType);
    if (!card) return;

    const badge = document.getElementById('status-badge-' + docType);
    if (badge) {
        const map = { approved: ['green', 'مقبول'], rejected: ['red', 'مرفوض'], pending: ['amber', 'قيد المراجعة'] };
        const [color, text] = map[status] || map.pending;
        badge.className = 'sp-pill ' + color;
        badge.textContent = text;
    }

    const approveBtn = card.querySelector('.doc-approve-btn');
    const rejectBtn  = card.querySelector('.doc-reject-btn');
    if (approveBtn) approveBtn.style.display = status === 'approved' ? 'none' : '';
    if (rejectBtn)  rejectBtn.style.display  = status === 'rejected' ? 'none' : '';

    let reasonBox = card.querySelector('.doc-reject-reason-box');
    if (status === 'rejected' && reason) {
        if (!reasonBox) {
            reasonBox = document.createElement('div');
            reasonBox.className = 'sp-alert err doc-reject-reason-box';
            reasonBox.style.margin = '10px 0 0';
            card.querySelector('.sp-card-b').appendChild(reasonBox);
        }
        reasonBox.textContent = 'سبب الرفض: ' + reason;
    } else if (reasonBox && status !== 'rejected') {
        reasonBox.remove();
    }
}

function showDocToast(msg, bg) {
    const t = document.getElementById('doc-toast');
    t.textContent = msg;
    t.style.background = bg || '#16a34a';
    t.style.display = 'block';
    setTimeout(() => { t.style.display = 'none'; }, 3500);
}
</script>
@endpush
@endsection
