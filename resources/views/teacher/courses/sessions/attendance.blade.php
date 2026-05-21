@extends('layouts.dashboard')

@section('title', 'حضور المحاضرة')

@section('content')
@php
    $rate      = $stats['attendance_rate'];
    $attended  = $stats['attended'];
    $absent    = $stats['absent'];
    $total     = $stats['total_enrolled'];
    $rateColor = $rate >= 75 ? '#16a34a' : ($rate >= 50 ? '#d97706' : '#dc2626');
@endphp

<div style="direction:rtl;max-width:900px;margin:0 auto;padding:0 4px">

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-right:4px solid #22c55e;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#15803d;font-size:.875rem;font-weight:600;display:flex;align-items:center;gap:8px">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- Header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:20px;flex-wrap:wrap">
    <div>
        <div style="font-size:.8rem;color:#6b7280;margin-bottom:4px">
            <a href="{{ route('teacher.my-courses.show', $program->id) }}" style="color:#6b7280;text-decoration:none">{{ $program->name_ar }}</a>
            <span style="margin:0 6px">›</span>
            <span>المحاضرة {{ $session->session_number }}</span>
        </div>
        <h1 style="font-size:1.25rem;font-weight:800;color:#111827;margin:0">سجل الحضور</h1>
        @if($session->scheduled_at)
        <p style="font-size:.8rem;color:#6b7280;margin:4px 0 0">
            {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d · H:i') }}
            @if($session->duration_minutes) · {{ $session->duration_minutes }} دقيقة @endif
        </p>
        @endif
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        <a href="{{ route('teacher.my-courses.show', $program->id) }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.8rem;font-weight:600;color:#374151;text-decoration:none;background:#fff">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            رجوع
        </a>
        <button onclick="window.print()"
                style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.8rem;font-weight:600;color:#374151;background:#fff;cursor:pointer">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            طباعة
        </button>
    </div>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px">
    @foreach([
        [$total,      'إجمالي المسجلين', '#2563eb', '#dbeafe'],
        [$attended,   'حاضر',            '#16a34a', '#dcfce7'],
        [$absent,     'غائب',            '#dc2626', '#fee2e2'],
        [$rate.'%',   'نسبة الحضور',    $rateColor, '#f9fafb'],
    ] as [$val, $label, $color, $bg])
    <div style="background:#fff;border:1.5px solid #f1f5f9;border-radius:12px;padding:14px 16px;text-align:center">
        <div style="font-size:1.5rem;font-weight:900;color:{{ $color }}">{{ $val }}</div>
        <div style="font-size:.75rem;color:#6b7280;margin-top:2px">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- Attended --}}
<div style="background:#fff;border:1.5px solid #f1f5f9;border-radius:14px;overflow:hidden;margin-bottom:20px">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px">
        <div style="width:8px;height:8px;border-radius:50%;background:#16a34a"></div>
        <h2 style="font-size:.9rem;font-weight:700;color:#111827;margin:0">الحاضرون ({{ $attended }})</h2>
    </div>
    @if($attendances->where('attended', true)->count())
    <table style="width:100%;border-collapse:collapse;font-size:.85rem">
        <thead>
            <tr style="background:#f9fafb">
                <th style="padding:10px 18px;text-align:right;font-size:.75rem;font-weight:700;color:#6b7280">#</th>
                <th style="padding:10px 18px;text-align:right;font-size:.75rem;font-weight:700;color:#6b7280">المتدرب</th>
                <th style="padding:10px 18px;text-align:right;font-size:.75rem;font-weight:700;color:#6b7280">وقت الانضمام</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances->where('attended', true) as $i => $att)
            <tr style="border-top:1px solid #f9fafb">
                <td style="padding:12px 18px;color:#9ca3af;font-weight:700">{{ $i + 1 }}</td>
                <td style="padding:12px 18px">
                    <p style="font-weight:700;color:#111827;margin:0">{{ $att->student->name ?? 'غير معروف' }}</p>
                    <p style="font-size:.75rem;color:#9ca3af;margin:2px 0 0">{{ $att->student->email ?? '' }}</p>
                </td>
                <td style="padding:12px 18px;color:#374151">
                    {{ $att->joined_at ? \Carbon\Carbon::parse($att->joined_at)->format('h:i A') : '—' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="padding:40px;text-align:center;color:#9ca3af;font-size:.875rem">لا يوجد حضور مسجّل بعد</div>
    @endif
</div>

{{-- Add Attendance --}}
@if($absentStudents->count())
<div style="background:#fff;border:1.5px solid #f1f5f9;border-radius:14px;overflow:hidden">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div style="display:flex;align-items:center;gap:10px">
            <div style="width:8px;height:8px;border-radius:50%;background:#dc2626"></div>
            <h2 style="font-size:.9rem;font-weight:700;color:#111827;margin:0">
                تسجيل الحضور يدوياً — {{ $absentStudents->count() }} غائب
            </h2>
        </div>
        <label style="font-size:.78rem;font-weight:600;color:#374151;cursor:pointer;display:flex;align-items:center;gap:6px">
            <input type="checkbox" id="select-all" onchange="document.querySelectorAll('.att-cb').forEach(cb => cb.checked = this.checked)">
            تحديد الكل
        </label>
    </div>
    <form action="{{ route('teacher.my-courses.sessions.attendance.save', [$program->id, $session->id]) }}" method="POST">
        @csrf
        <table style="width:100%;border-collapse:collapse;font-size:.85rem">
            @foreach($absentStudents as $student)
            <tr style="border-top:1px solid #f9fafb">
                <td style="padding:10px 18px;width:40px">
                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="att-cb" style="width:16px;height:16px;cursor:pointer">
                </td>
                <td style="padding:10px 18px">
                    <p style="font-weight:700;color:#111827;margin:0">{{ $student->name ?? 'غير معروف' }}</p>
                    <p style="font-size:.75rem;color:#9ca3af;margin:2px 0 0">{{ $student->email ?? '' }}</p>
                </td>
            </tr>
            @endforeach
        </table>
        <div style="padding:14px 16px;border-top:1px solid #f1f5f9;text-align:left">
            <button type="submit"
                    style="padding:10px 24px;border-radius:8px;border:none;font-size:.875rem;font-weight:700;color:#fff;background:linear-gradient(135deg,#16a34a,#15803d);cursor:pointer">
                حفظ الحضور
            </button>
        </div>
    </form>
</div>
@endif

</div>
@endsection
