@extends('layouts.dashboard')
@section('title', 'سجل النشاطات')

@section('content')
@php
$actionArabic = [
    'login'=>'تسجيل دخول','logout'=>'تسجيل خروج','register'=>'تسجيل حساب جديد',
    'password_reset'=>'استعادة كلمة المرور','view_session'=>'مشاهدة جلسة',
    'view_recording'=>'مشاهدة تسجيل','download_file'=>'تحميل ملف',
    'view_subject'=>'عرض مادة','view_unit'=>'عرض وحدة','start_quiz'=>'بدء اختبار',
    'submit_quiz'=>'إرسال اختبار','view_quiz_result'=>'عرض نتيجة','submit_assignment'=>'إرسال واجب',
    'grade_evaluation'=>'تصحيح تقييم','join_session'=>'الانضمام لجلسة','leave_session'=>'مغادرة جلسة',
    'mark_attendance'=>'تسجيل الحضور','enroll'=>'التسجيل في مادة','withdraw'=>'الانسحاب',
    'complete_course'=>'إتمام الدورة','submit_survey'=>'إرسال استبيان','submit_rating'=>'تقييم معلم',
    'create_ticket'=>'إنشاء تذكرة','reply_ticket'=>'الرد على تذكرة','create_user'=>'إنشاء مستخدم',
    'update_user'=>'تعديل مستخدم','delete_user'=>'حذف مستخدم','create_program'=>'إنشاء مسار',
    'update_program'=>'تعديل مسار','delete_program'=>'حذف مسار','create_session'=>'إنشاء جلسة',
    'update_session'=>'تعديل جلسة','delete_session'=>'حذف جلسة','page_view'=>'عرض صفحة',
];
$categoryArabic = [
    'auth'=>'المصادقة','content'=>'المحتوى','assessment'=>'التقييم','attendance'=>'الحضور',
    'enrollment'=>'التسجيل','communication'=>'التواصل','admin'=>'الإدارة','navigation'=>'التصفح',
];
$categoryStyle = [
    'auth'          => ['bg'=>'#dbeafe','color'=>'#1d4ed8'],
    'content'       => ['bg'=>'#ede9fe','color'=>'#7c3aed'],
    'assessment'    => ['bg'=>'#fef9c3','color'=>'#854d0e'],
    'attendance'    => ['bg'=>'#dcfce7','color'=>'#15803d'],
    'enrollment'    => ['bg'=>'#e0e7ff','color'=>'#3730a3'],
    'communication' => ['bg'=>'#fce7f3','color'=>'#9d174d'],
    'admin'         => ['bg'=>'#fee2e2','color'=>'#991b1b'],
    'navigation'    => ['bg'=>'#f1f5f9','color'=>'#64748b'],
];
$actionIconMap = [
    'login'          => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1',
    'logout'         => 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
    'register'       => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
    'view_session'   => 'M15 10l4.553-2.069A1 1 0 0121 8.869v6.262a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
    'download_file'  => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4',
    'start_quiz'     => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
    'submit_quiz'    => 'M9 12l2 2 4-4M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
    'join_session'   => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    'enroll'         => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
    'submit_rating'  => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
    'create_ticket'  => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z',
    'mark_attendance'=> 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
];
$defaultIcon = 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
@endphp

<div style="padding:1.5rem;direction:rtl;max-width:1300px;margin:0 auto;">

{{-- ===== HERO ===== --}}
<div style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 50%,#4338ca 100%);border-radius:1.5rem;padding:2rem 2.5rem;margin-bottom:1.75rem;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-50px;left:-50px;width:250px;height:250px;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-40px;right:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.04);pointer-events:none;"></div>

    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1.25rem;">
        <div style="display:flex;align-items:center;gap:1.25rem;">
            <div style="width:60px;height:60px;border-radius:1.1rem;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:30px;height:30px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <h1 style="color:#fff;font-size:1.6rem;font-weight:900;margin:0;">سجل النشاطات</h1>
                <p style="color:rgba(255,255,255,.65);font-size:.875rem;margin:.25rem 0 0;">تتبع جميع الأنشطة والأحداث على المنصة في الوقت الفعلي</p>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
            {{-- Stat chips --}}
            <span style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:rgba(255,255,255,.12);border-radius:10px;color:#fff;font-size:.82rem;font-weight:700;">
                <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                {{ number_format($stats['total']) }} إجمالي
            </span>
            <span style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:rgba(34,197,94,.18);border-radius:10px;color:#86efac;font-size:.82rem;font-weight:700;">
                <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ number_format($stats['xapi_synced']) }} xAPI
            </span>
            <a href="{{ route('admin.activity-logs.stats') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:rgba(255,255,255,.15);border-radius:10px;color:#fff;font-size:.82rem;font-weight:700;text-decoration:none;transition:background .2s;"
               onmouseover="this.style.background='rgba(255,255,255,.25)'" onmouseout="this.style.background='rgba(255,255,255,.15)'">
                <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                الإحصائيات
            </a>
            <a href="{{ route('admin.activity-logs.export', ['format'=>'csv'] + request()->all()) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:rgba(34,197,94,.2);border:1px solid rgba(34,197,94,.35);border-radius:10px;color:#86efac;font-size:.82rem;font-weight:700;text-decoration:none;transition:background .2s;"
               onmouseover="this.style.background='rgba(34,197,94,.3)'" onmouseout="this.style.background='rgba(34,197,94,.2)'">
                <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                CSV
            </a>
        </div>
    </div>
</div>

{{-- ===== STAT CARDS ===== --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1.1rem;margin-bottom:1.5rem;">
@php
$statCards = [
    ['label'=>'إجمالي النشاطات','value'=>number_format($stats['total']),'sub'=>'منذ بداية النظام','grad'=>'#6366f1,#4f46e5','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
    ['label'=>'آخر 24 ساعة','value'=>number_format($stats['last_24h']),'sub'=>'نشاط حديث','grad'=>'#f59e0b,#d97706','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    ['label'=>'آخر 7 أيام','value'=>number_format($stats['last_7d']),'sub'=>'هذا الأسبوع','grad'=>'#10b981,#059669','icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
    ['label'=>'مُزامن xAPI','value'=>number_format($stats['xapi_synced']),'sub'=>($stats['total']>0?round(($stats['xapi_synced']/$stats['total'])*100,1):0).'% من الإجمالي','grad'=>'#0071AA,#005a88','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
];
@endphp
@foreach($statCards as $card)
<div style="background:#fff;border-radius:1.1rem;padding:1.25rem;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.05);position:relative;overflow:hidden;">
    <div style="position:absolute;top:0;right:0;left:0;height:3px;background:linear-gradient(90deg,{{ $card['grad'] }});"></div>
    <div style="position:absolute;bottom:-16px;left:-16px;width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,{{ $card['grad'] }});opacity:.07;"></div>
    <div style="width:42px;height:42px;border-radius:11px;background:linear-gradient(135deg,{{ $card['grad'] }});display:flex;align-items:center;justify-content:center;margin-bottom:.85rem;">
        <svg style="width:20px;height:20px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
        </svg>
    </div>
    <div style="font-size:2rem;font-weight:900;color:#1e293b;">{{ $card['value'] }}</div>
    <div style="font-size:.875rem;color:#64748b;font-weight:600;margin-top:.2rem;">{{ $card['label'] }}</div>
    <div style="font-size:.75rem;color:#94a3b8;margin-top:.2rem;">{{ $card['sub'] }}</div>
</div>
@endforeach
</div>

{{-- ===== CHARTS ===== --}}
<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.1rem;margin-bottom:1.5rem;">
    <div style="background:#fff;border-radius:1.1rem;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.05);overflow:hidden;">
        <div style="padding:.85rem 1.25rem;border-bottom:1px solid #f8fafc;display:flex;align-items:center;gap:8px;">
            <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#6366f1,#4f46e5);display:flex;align-items:center;justify-content:center;">
                <svg style="width:14px;height:14px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <span style="font-size:.9rem;font-weight:700;color:#1e293b;">النشاط اليومي — آخر 7 أيام</span>
        </div>
        <div style="padding:1.25rem;height:220px;"><canvas id="activityChart"></canvas></div>
    </div>
    <div style="background:#fff;border-radius:1.1rem;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.05);overflow:hidden;">
        <div style="padding:.85rem 1.25rem;border-bottom:1px solid #f8fafc;display:flex;align-items:center;gap:8px;">
            <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;">
                <svg style="width:14px;height:14px;color:#fff" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/><path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/></svg>
            </div>
            <span style="font-size:.9rem;font-weight:700;color:#1e293b;">توزيع الفئات</span>
        </div>
        <div style="padding:1rem;height:220px;"><canvas id="categoryChart"></canvas></div>
    </div>
</div>

{{-- ===== TOP ACTIONS ===== --}}
@if($topActions->isNotEmpty())
<div style="background:#fff;border-radius:1.1rem;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.05);overflow:hidden;margin-bottom:1.5rem;">
    <div style="padding:.85rem 1.25rem;border-bottom:1px solid #f8fafc;display:flex;align-items:center;gap:8px;">
        <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;">
            <svg style="width:14px;height:14px;color:#fff" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
        </div>
        <span style="font-size:.9rem;font-weight:700;color:#1e293b;">أكثر الأنشطة تكراراً</span>
    </div>
    <div style="display:grid;grid-template-columns:repeat(5,1fr);">
        @php $medals = ['🥇','🥈','🥉','4️⃣','5️⃣']; @endphp
        @foreach($topActions as $i => $action)
        <div style="padding:1rem 1.25rem;{{ !$loop->last ? 'border-left:1px solid #f1f5f9;' : '' }}display:flex;align-items:center;gap:10px;">
            <span style="font-size:1.5rem;flex-shrink:0;">{{ $medals[$i] ?? ($i+1) }}</span>
            <div style="min-width:0;">
                <div style="font-size:.82rem;font-weight:700;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $actionArabic[$action->action] ?? $action->action }}</div>
                <div style="font-size:.75rem;color:#94a3b8;margin-top:2px;">{{ number_format($action->count) }} مرة</div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ===== FILTERS ===== --}}
<div style="background:#fff;border-radius:1.1rem;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.05);overflow:hidden;margin-bottom:1.5rem;">
    <div style="padding:.85rem 1.25rem;border-bottom:1px solid #f8fafc;display:flex;align-items:center;gap:8px;">
        <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#64748b,#475569);display:flex;align-items:center;justify-content:center;">
            <svg style="width:14px;height:14px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
        </div>
        <span style="font-size:.9rem;font-weight:700;color:#1e293b;">البحث والتصفية</span>
        @if(request()->hasAny(['action','category','date_from','date_to','search']))
        <span style="display:inline-flex;padding:2px 10px;border-radius:999px;background:#ede9fe;color:#6d28d9;font-size:.72rem;font-weight:700;">فعّال</span>
        @endif
    </div>
    <form method="GET" action="{{ route('admin.activity-logs.index') }}"
          style="padding:1.25rem;display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr auto;gap:.85rem;align-items:end;">

        <div>
            <label style="display:block;font-size:.78rem;font-weight:600;color:#64748b;margin-bottom:.4rem;">البحث بالاسم أو البريد</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث..."
                   style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:9px 14px;font-size:.875rem;color:#374151;outline:none;background:#f8fafc;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#6366f1';this.style.background='#fff'"
                   onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc'">
        </div>

        <div>
            <label style="display:block;font-size:.78rem;font-weight:600;color:#64748b;margin-bottom:.4rem;">الإجراء</label>
            <select name="action"
                    style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:9px 14px;font-size:.875rem;color:#374151;background:#f8fafc;outline:none;cursor:pointer;"
                    onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
                <option value="">جميع الإجراءات</option>
                @foreach($actions as $act)
                <option value="{{ $act }}" {{ request('action')==$act?'selected':'' }}>{{ $actionArabic[$act] ?? $act }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display:block;font-size:.78rem;font-weight:600;color:#64748b;margin-bottom:.4rem;">الفئة</label>
            <select name="category"
                    style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:9px 14px;font-size:.875rem;color:#374151;background:#f8fafc;outline:none;cursor:pointer;"
                    onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
                <option value="">جميع الفئات</option>
                @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ request('category')==$cat?'selected':'' }}>{{ $categoryArabic[$cat] ?? $cat }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display:block;font-size:.78rem;font-weight:600;color:#64748b;margin-bottom:.4rem;">من تاريخ</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:9px 14px;font-size:.875rem;color:#374151;background:#f8fafc;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <div>
            <label style="display:block;font-size:.78rem;font-weight:600;color:#64748b;margin-bottom:.4rem;">إلى تاريخ</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:9px 14px;font-size:.875rem;color:#374151;background:#f8fafc;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <div style="display:flex;gap:.5rem;">
            <button type="submit"
                    style="padding:9px 18px;background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;border:none;border-radius:10px;font-size:.875rem;font-weight:700;cursor:pointer;white-space:nowrap;box-shadow:0 3px 8px rgba(99,102,241,.3);">
                تطبيق
            </button>
            @if(request()->hasAny(['action','category','date_from','date_to','search']))
            <a href="{{ route('admin.activity-logs.index') }}"
               style="padding:9px 14px;border:1.5px solid #e2e8f0;color:#64748b;border-radius:10px;font-size:.875rem;font-weight:600;text-decoration:none;display:flex;align-items:center;white-space:nowrap;">
                مسح
            </a>
            @endif
        </div>
    </form>
</div>

{{-- ===== LOGS TABLE ===== --}}
<div style="background:#fff;border-radius:1.1rem;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.05);overflow:hidden;">
    <div style="padding:.85rem 1.25rem;border-bottom:1px solid #f8fafc;display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:8px;">
            <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#6366f1,#4f46e5);display:flex;align-items:center;justify-content:center;">
                <svg style="width:14px;height:14px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            </div>
            <span style="font-size:.9rem;font-weight:700;color:#1e293b;">النشاطات المسجّلة</span>
            <span style="display:inline-flex;padding:2px 10px;border-radius:999px;background:#ede9fe;color:#6d28d9;font-size:.75rem;font-weight:700;">{{ number_format($logs->total()) }} سجل</span>
        </div>
        @if($logs->lastPage() > 1)
        <span style="font-size:.75rem;color:#94a3b8;">صفحة {{ $logs->currentPage() }} / {{ $logs->lastPage() }}</span>
        @endif
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:11px 16px;text-align:right;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em;white-space:nowrap;">المستخدم</th>
                    <th style="padding:11px 16px;text-align:right;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em;white-space:nowrap;">النشاط</th>
                    <th style="padding:11px 16px;text-align:right;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em;white-space:nowrap;">الفئة</th>
                    <th style="padding:11px 16px;text-align:right;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em;white-space:nowrap;">عنوان IP</th>
                    <th style="padding:11px 16px;text-align:right;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em;white-space:nowrap;">الوقت</th>
                    <th style="padding:11px 16px;text-align:right;font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em;white-space:nowrap;">xAPI</th>
                    <th style="padding:11px 16px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                    $cat      = $log->action_category ?? 'navigation';
                    $catStyle = $categoryStyle[$cat] ?? ['bg'=>'#f1f5f9','color'=>'#64748b'];
                    $actLabel = $actionArabic[$log->action] ?? $log->action;
                    $icon     = $actionIconMap[$log->action] ?? $defaultIcon;
                    $initials = $log->user ? collect(explode(' ',$log->user->name))->take(2)->map(fn($w)=>mb_substr($w,0,1))->join('') : '?';
                    $isNew    = $log->created_at->gt(now()->subMinutes(60));
                @endphp
                <tr style="border-top:1px solid #f8fafc;transition:background .15s;"
                    onmouseover="this.style.background='#fafbff'" onmouseout="this.style.background='transparent'">

                    {{-- User --}}
                    <td style="padding:13px 16px;">
                        @if($log->user)
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#6366f1,#4f46e5);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.8rem;flex-shrink:0;">{{ $initials }}</div>
                            <div style="min-width:0;">
                                <div style="font-size:.85rem;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:130px;">{{ $log->user->name }}</div>
                                <div style="font-size:.72rem;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:130px;">{{ $log->user->email }}</div>
                            </div>
                        </div>
                        @else
                        <span style="font-size:.85rem;color:#94a3b8;">النظام</span>
                        @endif
                    </td>

                    {{-- Action --}}
                    <td style="padding:13px 16px;">
                        <div style="display:flex;align-items:center;gap:7px;">
                            <div style="width:28px;height:28px;border-radius:7px;background:{{ $catStyle['bg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg style="width:13px;height:13px;color:{{ $catStyle['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                                </svg>
                            </div>
                            <span style="font-size:.82rem;font-weight:600;color:#374151;white-space:nowrap;">{{ $actLabel }}</span>
                            @if($isNew)
                            <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;display:inline-block;flex-shrink:0;"></span>
                            @endif
                        </div>
                    </td>

                    {{-- Category --}}
                    <td style="padding:13px 16px;">
                        <span style="display:inline-flex;padding:3px 10px;border-radius:999px;font-size:.72rem;font-weight:700;background:{{ $catStyle['bg'] }};color:{{ $catStyle['color'] }};white-space:nowrap;">
                            {{ $categoryArabic[$cat] ?? $cat }}
                        </span>
                    </td>

                    {{-- IP --}}
                    <td style="padding:13px 16px;">
                        <code style="background:#f1f5f9;padding:3px 8px;border-radius:6px;font-size:.75rem;color:#64748b;font-family:monospace;">{{ $log->ip_address ?? '—' }}</code>
                    </td>

                    {{-- Time --}}
                    <td style="padding:13px 16px;">
                        <div style="font-size:.82rem;color:#374151;white-space:nowrap;">{{ $log->created_at->diffForHumans() }}</div>
                        <div style="font-size:.72rem;color:#94a3b8;margin-top:1px;">{{ $log->created_at->format('d/m/Y H:i') }}</div>
                    </td>

                    {{-- xAPI --}}
                    <td style="padding:13px 16px;">
                        @if($log->xapi_sent)
                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:999px;background:#dcfce7;color:#16a34a;font-size:.72rem;font-weight:700;">
                            <svg style="width:10px;height:10px" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            مُرسل
                        </span>
                        @else
                        <span style="display:inline-flex;padding:3px 10px;border-radius:999px;background:#f1f5f9;color:#94a3b8;font-size:.72rem;font-weight:600;">معلق</span>
                        @endif
                    </td>

                    {{-- View --}}
                    <td style="padding:13px 16px;">
                        <a href="{{ route('admin.activity-logs.show', $log) }}"
                           style="display:inline-flex;width:32px;height:32px;align-items:center;justify-content:center;border-radius:8px;background:#ede9fe;color:#6d28d9;text-decoration:none;transition:all .2s;"
                           onmouseover="this.style.background='#6366f1';this.style.color='#fff'"
                           onmouseout="this.style.background='#ede9fe';this.style.color='#6d28d9'">
                            <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding:4rem;text-align:center;">
                        <div style="display:flex;flex-direction:column;align-items:center;gap:.75rem;">
                            <div style="width:64px;height:64px;border-radius:50%;background:#ede9fe;display:flex;align-items:center;justify-content:center;">
                                <svg style="width:30px;height:30px;color:#a78bfa" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <p style="font-size:.95rem;font-weight:700;color:#374151;margin:0;">لا توجد نشاطات مطابقة</p>
                            <p style="font-size:.82rem;color:#94a3b8;margin:0;">جرب تعديل معايير البحث أو التصفية</p>
                            @if(request()->hasAny(['action','category','date_from','date_to','search']))
                            <a href="{{ route('admin.activity-logs.index') }}"
                               style="margin-top:.25rem;padding:8px 18px;background:#ede9fe;color:#6d28d9;border-radius:10px;font-size:.82rem;font-weight:700;text-decoration:none;">
                                مسح الفلاتر
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div style="padding:1rem 1.25rem;border-top:1px solid #f8fafc;">
        {{ $logs->appends(request()->query())->links() }}
    </div>
    @endif
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
Chart.defaults.font.family = 'Cairo, sans-serif';
Chart.defaults.color = '#64748b';

new Chart(document.getElementById('activityChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($activityTimeline->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))) !!},
        datasets: [{
            label: 'النشاطات',
            data: {!! json_encode($activityTimeline->pluck('count')) !!},
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99,102,241,.1)',
            tension: 0.4, fill: true,
            pointBackgroundColor: '#6366f1', pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend:{ display:false }, tooltip:{ backgroundColor:'rgba(15,23,42,.9)', padding:10, cornerRadius:8 } },
        scales: { x:{ grid:{ display:false }, ticks:{ font:{ size:11 } } }, y:{ beginAtZero:true, grid:{ color:'rgba(0,0,0,.04)' }, ticks:{ font:{ size:11 }, stepSize:1 } } }
    }
});

const catLabelsRaw = {!! json_encode($categoryStats->keys()) !!};
const catMap = {'auth':'المصادقة','content':'المحتوى','assessment':'التقييم','attendance':'الحضور','enrollment':'التسجيل','communication':'التواصل','admin':'الإدارة','navigation':'التصفح'};
new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: catLabelsRaw.map(k => catMap[k] || k),
        datasets: [{
            data: {!! json_encode($categoryStats->values()) !!},
            backgroundColor: ['rgba(99,102,241,.8)','rgba(59,130,246,.8)','rgba(234,179,8,.8)','rgba(34,197,94,.8)','rgba(168,85,247,.8)','rgba(236,72,153,.8)','rgba(239,68,68,.8)','rgba(107,114,128,.8)'],
            borderWidth: 2, borderColor: '#fff',
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend:{ position:'bottom', labels:{ padding:10, font:{size:10}, boxWidth:10, boxHeight:10 } }, tooltip:{ backgroundColor:'rgba(15,23,42,.9)', padding:8, cornerRadius:8 } },
        cutout: '62%',
    }
});
</script>
@endsection
