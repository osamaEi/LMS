@extends('layouts.dashboard')

@section('title', 'المتدربون ')

@section('content')
<div style="direction:rtl;font-family:'Segoe UI',sans-serif;">

{{-- Header --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 55%,#0071AA 100%);border-radius:20px;padding:24px 28px;margin-bottom:24px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-60px;left:-60px;width:220px;height:220px;background:rgba(255,255,255,.05);border-radius:50%;pointer-events:none;"></div>
    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:48px;height:48px;background:rgba(255,255,255,.12);border-radius:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="22" height="22" fill="white" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <h1 style="color:white;font-size:20px;font-weight:700;margin:0;">المتدربون </h1>
                <p style="color:rgba(255,255,255,.5);font-size:12px;margin:2px 0 0;">الطلاب الذين حضروا جلساتك</p>
            </div>
        </div>
        <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:8px 16px;text-align:center;min-width:60px;">
            <div style="font-size:20px;font-weight:700;color:#fde68a;line-height:1;">{{ $totalStudents }}</div>
            <div style="font-size:11px;color:rgba(255,255,255,.5);margin-top:2px;">إجمالي الطلاب</div>
        </div>
    </div>
</div>

@if($students->isEmpty())
<div style="background:white;border-radius:18px;border:1px solid #e5e7eb;padding:60px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,.06);">
    <svg style="width:56px;height:56px;color:#d1d5db;margin:0 auto 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    <p style="font-size:15px;font-weight:600;color:#475569;margin-bottom:4px;">لا يوجد طلاب بعد</p>
    <p style="font-size:13px;color:#94a3b8;">سيظهر الطلاب هنا بعد حضورهم جلساتك</p>
</div>
@else
<div style="background:white;border-radius:18px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:2px solid #f1f5f9;background:#fafafa;">
                    <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;width:40px;">#</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الاسم</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">البريد الإلكتروني</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">البرنامج</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الهوية</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الجوال</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $i => $student)
                <tr style="border-bottom:1px solid #f8fafc;transition:background .1s;" onmouseover="this.style.background='#f8faff'" onmouseout="this.style.background=''">
                    <td style="padding:13px 16px;color:#cbd5e1;font-size:11px;">{{ $i + 1 }}</td>
                    <td style="padding:13px 16px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#0071AA,#004d77);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:white;font-weight:700;font-size:13px;">
                                {{ mb_substr($student->name, 0, 1) }}
                            </div>
                            <span style="font-weight:600;color:#1e293b;">{{ $student->name }}</span>
                        </div>
                    </td>
                    <td style="padding:13px 16px;color:#475569;" dir="ltr">{{ $student->email }}</td>
                    <td style="padding:13px 16px;color:#64748b;">{{ $student->program->name ?? '—' }}</td>
                    <td style="padding:13px 16px;color:#64748b;" dir="ltr">{{ $student->national_id ?? '—' }}</td>
                    <td style="padding:13px 16px;color:#64748b;" dir="ltr">{{ $student->phone ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

</div>
@endsection
