@extends('layouts.dashboard')

@section('title', 'لوحة تحكم المتدرب ')

@push('styles')
<style>
    .dash-page { max-width: 1240px; margin: 0 auto; }

    /* Header */
    .dash-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 40%, #003d5c 100%);
        border-radius: 24px;
        padding: 2.25rem 2.5rem 0;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .dash-header::before {
        content: '';
        position: absolute;
        top: -40%; left: -5%;
        width: 420px; height: 420px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 65%);
        border-radius: 50%;
        pointer-events: none;
    }
    .dash-header::after {
        content: '';
        position: absolute;
        bottom: -60%; right: -2%;
        width: 320px; height: 320px;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 65%);
        border-radius: 50%;
        pointer-events: none;
    }
    .dash-header .hdr-glow {
        position: absolute;
        top: 30%; left: 50%;
        transform: translate(-50%, -50%);
        width: 600px; height: 200px;
        background: radial-gradient(ellipse, rgba(255,255,255,0.04) 0%, transparent 70%);
        pointer-events: none;
    }
    /* Avatar ring */
    .hdr-avatar {
        width: 72px; height: 72px;
        border-radius: 20px;
        border: 2.5px solid rgba(255,255,255,0.25);
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        flex-shrink: 0;
    }
    /* Code badge */
    .hdr-code {
        display: inline-flex; align-items: center; gap: .4rem;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 8px;
        padding: .25rem .75rem;
        font-size: .72rem; font-weight: 800;
        letter-spacing: .06em; color: #fff;
        backdrop-filter: blur(8px);
    }
    .hdr-code .code-dot {
        width: 6px; height: 6px; border-radius: 50%;
        background: #4ade80;
        animation: blink 1.4s ease-in-out infinite;
    }
    /* Bottom chips strip */
    .hdr-chips {
        display: flex; align-items: center; gap: 0;
        margin-top: 1.75rem;
        border-top: 1px solid rgba(255,255,255,0.1);
    }
    .hdr-chip {
        flex: 1; display: flex; flex-direction: column; align-items: center;
        padding: .9rem .5rem;
        border-left: 1px solid rgba(255,255,255,0.08);
        transition: background .2s;
        text-decoration: none; color: #fff;
    }
    .hdr-chip:first-child { border-left: none; }
    .hdr-chip:hover { background: rgba(255,255,255,0.06); }
    .hdr-chip .chip-val { font-size: 1.25rem; font-weight: 900; line-height: 1; }
    .hdr-chip .chip-lbl { font-size: .7rem; opacity: .6; margin-top: .25rem; font-weight: 600; }
    /* Nav buttons */
    .hdr-nav {
        display: flex; align-items: center; gap: .6rem; flex-wrap: wrap;
    }
    .hdr-btn {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .55rem 1.1rem; border-radius: 12px;
        font-size: .82rem; font-weight: 700;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.15);
        color: #fff; text-decoration: none;
        transition: background .2s;
    }
    .hdr-btn:hover { background: rgba(255,255,255,0.2); color: #fff; }
    .hdr-btn svg { flex-shrink: 0; }

    /* Card */
    .d-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .dark .d-card { background: #1f2937; }
    .d-card-head {
        padding: 1.15rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .dark .d-card-head { border-color: #374151; }
    .d-card-head .icon-wrap {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Stats */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }
    @media (max-width: 768px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }
    .stat-box {
        background: #fff;
        border-radius: 18px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: transform 0.2s;
    }
    .dark .stat-box { background: #1f2937; }
    .stat-box:hover { transform: translateY(-2px); }
    .stat-box .s-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-box .s-val { font-size: 1.75rem; font-weight: 800; line-height: 1; color: #111827; }
    .dark .stat-box .s-val { color: #f9fafb; }
    .stat-box .s-lbl { font-size: 0.78rem; color: #6b7280; margin-top: 0.15rem; font-weight: 500; }
    .dark .stat-box .s-lbl { color: #9ca3af; }

    /* Live Banner */
    .live-banner {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border-radius: 18px;
        padding: 1.25rem 1.5rem;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        animation: livePulse 2s infinite;
    }
    @keyframes livePulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.4); }
        50% { box-shadow: 0 0 0 8px rgba(239,68,68,0); }
    }
    .live-dot {
        width: 10px;
        height: 10px;
        background: #fff;
        border-radius: 50%;
        animation: blink 1s infinite;
    }
    @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }

    /* Session item */
    .sess-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f8fafc;
        transition: background 0.15s;
    }
    .dark .sess-item { border-color: #1f2937; }
    .sess-item:hover { background: #f8fafc; }
    .dark .sess-item:hover { background: #111827; }
    .sess-item:last-child { border-bottom: none; }
    .sess-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Progress bar */
    .prog-track {
        height: 8px;
        background: #f3f4f6;
        border-radius: 4px;
        overflow: hidden;
    }
    .dark .prog-track { background: #374151; }
    .prog-fill { height: 100%; border-radius: 4px; transition: width 0.6s ease; }

    /* Calendar mini */
    .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; }
    .cal-cell {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 8px;
        color: #374151;
        position: relative;
        cursor: default;
    }
    .dark .cal-cell { color: #d1d5db; }
    .cal-cell.today { background: #0071AA; color: #fff; font-weight: 800; }
    .cal-cell.has-session::after {
        content: '';
        position: absolute;
        bottom: 3px;
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #10b981;
    }
    .cal-cell.today.has-session::after { background: #fff; }
    .cal-head { font-size: 0.7rem; font-weight: 700; color: #9ca3af; text-align: center; padding: 0.35rem 0; }

    /* Badge */
    .type-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.2rem 0.6rem;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 700;
    }

    /* Ticket item */
    .ticket-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1.25rem;
        border-bottom: 1px solid #f8fafc;
        font-size: 0.85rem;
        min-width: 0;
    }
    .dark .ticket-row { border-color: #1f2937; }
    .ticket-row:last-child { border-bottom: none; }
    .ticket-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

    /* Survey card */
    .survey-item {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f8fafc;
    }
    .dark .survey-item { border-color: #1f2937; }
    .survey-item:last-child { border-bottom: none; }

    /* Link subtle */
    .link-all {
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.35rem 0.85rem;
        border-radius: 8px;
        transition: background 0.15s;
        text-decoration: none;
    }
</style>
@endpush

@section('content')
<div class="dash-page space-y-6">
    <!-- Header -->
    <div class="dash-header">
        <div class="hdr-glow"></div>

        {{-- Top row: avatar + info | nav buttons --}}
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5 relative z-10">

            {{-- Left: avatar + identity --}}
            <div class="flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=ffffff&color=0071AA&size=144&bold=true"
                     alt="{{ auth()->user()->name }}"
                     class="hdr-avatar" />
                <div>
                    <p class="text-xs font-semibold mb-1" style="opacity:.55;">مرحباً {{ auth()->user()->gender === 'female' ? 'متدربة' : 'متدرب' }} في معهد الارتقاء 👋</p>
                    <h1 class="text-3xl font-black tracking-tight leading-none mb-2">{{ auth()->user()->name }}</h1>
                    <div class="flex items-center gap-2 flex-wrap">
                        @if(auth()->user()->student_code)
                        <div class="hdr-code">
                            <span class="code-dot"></span>
                            {{ auth()->user()->student_code }}
                        </div>
                        @endif
                        @if(auth()->user()->program)
                        <span class="text-xs font-semibold" style="opacity:.65;">
                            {{ auth()->user()->program->name }}
                        </span>
                        @endif
                        @if(auth()->user()->level)
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:rgba(255,255,255,0.18);">
                            {{ auth()->user()->level }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right: nav buttons --}}
           
        </div>

        {{-- Bottom chips strip --}}
        <div class="hdr-chips relative z-10">
            <div class="hdr-chip">
                <span class="chip-val">{{ $stats['subjects_count'] }}</span>
                <span class="chip-lbl">المقررات</span>
            </div>
            <div class="hdr-chip">
                <span class="chip-val">{{ $stats['total_sessions'] }}</span>
                <span class="chip-lbl">الجلسات</span>
            </div>
            <div class="hdr-chip">
                <span class="chip-val">{{ $stats['completed_sessions'] }}</span>
                <span class="chip-lbl">مكتملة</span>
            </div>
            <div class="hdr-chip">
                <span class="chip-val">{{ $overallAttendance }}<small style="font-size:.75rem;">%</small></span>
                <span class="chip-lbl">نسبة الحضور</span>
            </div>
            @if(auth()->user()->national_id)
            <div class="hdr-chip" style="border-left:1px solid rgba(255,255,255,0.08);">
                <span class="chip-val" style="font-size:.9rem;letter-spacing:.04em;">{{ auth()->user()->national_id }}</span>
                <span class="chip-lbl">رقم الهوية</span>
            </div>
            @endif
            <a href="{{ route('student.payments.index') }}" class="hdr-chip">
                <span class="chip-val" style="font-size:.95rem;">💳</span>
                <span class="chip-lbl">المدفوعات</span>
            </a>
        </div>
    </div>

    <!-- Live Sessions Banner -->
    @if($liveSessions->count() > 0)
        @foreach($liveSessions as $session)
            <div class="live-banner">
                <div class="flex items-center gap-3">
                    <div class="live-dot"></div>
                    <div>
                        <div class="font-bold text-base">{{ $session->title }}</div>
                        <div class="text-sm opacity-80">{{ $session->subject->name }} — جلسة مباشرة الآن</div>
                    </div>
                </div>
                <a href="{{ route('student.sessions.join-zoom', $session->id) }}"
                   class="px-5 py-2.5 bg-white text-red-600 font-bold rounded-xl text-sm flex items-center gap-2 flex-shrink-0 hover:bg-red-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    انضم الآن
                </a>
            </div>
        @endforeach
    @endif

    <!-- Upcoming Sessions Panel -->
    @if($upcomingSessions->count() > 0 || $liveSessions->count() > 0)
    <div class="d-card" style="margin-bottom:1.25rem;">
        <div class="d-card-head">
            <div class="icon-wrap" style="background:linear-gradient(135deg,#0071AA,#004d77);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="white"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
            </div>
            <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">محاضراتي القادمة</span>
            <a href="{{ route('student.my-sessions') }}"
               style="font-size:.75rem;font-weight:700;color:#0071AA;text-decoration:none;padding:.3rem .75rem;background:#f0f9ff;border:1px solid #bae6fd;border-radius:20px;">
                عرض الكل
            </a>
        </div>
        <div style="padding:.5rem 0;">
            @foreach($upcomingSessions->take(5) as $session)
            @php
                $dt = \Carbon\Carbon::parse($session->scheduled_at);
                $isToday = $dt->isToday();
                $isTomorrow = $dt->isTomorrow();
            @endphp
            <div style="display:flex;align-items:center;gap:.875rem;padding:.75rem 1.25rem;border-bottom:1px solid #f8fafc;transition:background .15s;"
                 onmouseenter="this.style.background='#f8fafc'" onmouseleave="this.style.background='transparent'">

                {{-- Date pill --}}
                <div style="flex-shrink:0;width:48px;text-align:center;border-radius:12px;padding:.45rem .2rem;background:{{ $isToday ? 'linear-gradient(135deg,#0071AA,#004d77)' : '#f1f5f9' }};color:{{ $isToday ? '#fff' : '#64748b' }};">
                    <div style="font-size:1.05rem;font-weight:900;line-height:1;">{{ $dt->format('d') }}</div>
                    <div style="font-size:.6rem;font-weight:600;text-transform:uppercase;opacity:.8;margin-top:.1rem;">{{ $dt->translatedFormat('M') }}</div>
                    <div style="font-size:.65rem;font-weight:700;margin-top:.1rem;">{{ $dt->format('H:i') }}</div>
                </div>

                {{-- Info --}}
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;margin-bottom:.1rem;">
                        <span style="font-size:.83rem;font-weight:700;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:220px;">
                            📹 {{ $session->title }}
                        </span>
                        @if($isToday)
                            <span style="background:#0071AA;color:#fff;font-size:.62rem;font-weight:700;padding:.1rem .45rem;border-radius:20px;">اليوم</span>
                        @elseif($isTomorrow)
                            <span style="background:#e0f2fe;color:#0369a1;font-size:.62rem;font-weight:700;padding:.1rem .45rem;border-radius:20px;">غداً</span>
                        @endif
                    </div>
                    <div style="font-size:.72rem;color:#6b7280;">
                        {{ $session->subject->name_ar ?? $session->subject->name ?? $session->program->name_ar ?? $session->program->name ?? '—' }}
                    </div>
                    <div style="font-size:.68rem;color:#94a3b8;margin-top:.1rem;">{{ $dt->diffForHumans() }}</div>
                </div>

                {{-- Join link --}}
                @if($session->zoom_join_url)
                <a href="{{ $session->zoom_join_url }}" target="_blank"
                   style="flex-shrink:0;display:inline-flex;align-items:center;gap:.35rem;padding:.45rem .9rem;border-radius:10px;background:linear-gradient(135deg,#0071AA,#004d77);color:#fff;font-size:.72rem;font-weight:700;text-decoration:none;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
                    انضم
                </a>
                @else
                <span style="flex-shrink:0;padding:.4rem .75rem;border-radius:10px;background:#f9fafb;border:1px solid #e5e7eb;color:#9ca3af;font-size:.7rem;font-weight:600;">
                    قريباً
                </span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Stats -->


    <!-- Student Profile Info -->
    <div class="d-card">
        <div class="d-card-head">
            <div class="icon-wrap" style="background: #f0fdf4;">
                <svg class="w-4.5 h-4.5" style="color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">بياناتي الشخصية</span>
            <span class="px-2.5 py-1 text-xs font-bold rounded-lg"
                  style="background: {{ auth()->user()->status === 'active' ? '#dcfce7' : (auth()->user()->status === 'pending' ? '#fef9c3' : '#fee2e2') }};
                         color:      {{ auth()->user()->status === 'active' ? '#16a34a' : (auth()->user()->status === 'pending' ? '#92400e' : '#dc2626') }};">
                {{ auth()->user()->getStatusDisplayName() }}
            </span>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-6 gap-y-4">

                {{-- National ID --}}
                <div class="flex items-start gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5" style="background: #eff6ff;">
                        <svg class="w-4 h-4" style="color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                    </div>
                    <div>
                        <div class="text-[0.7rem] text-gray-400 font-medium">رقم الهوية</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">{{ auth()->user()->national_id ?? '—' }}</div>
                    </div>
                </div>

                {{-- Date of Birth --}}
                <div class="flex items-start gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5" style="background: #fef3c7;">
                        <svg class="w-4 h-4" style="color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-[0.7rem] text-gray-400 font-medium">تاريخ الميلاد</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">{{ auth()->user()->date_of_birth?->format('Y-m-d') ?? '—' }}</div>
                    </div>
                </div>

                {{-- Gender --}}
                <div class="flex items-start gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5" style="background: #fdf2f8;">
                        <svg class="w-4 h-4" style="color: #be185d;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <div class="text-[0.7rem] text-gray-400 font-medium">الجنس</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">
                            {{ auth()->user()->gender === 'male' ? 'ذكر' : (auth()->user()->gender === 'female' ? 'أنثى' : '—') }}
                        </div>
                    </div>
                </div>

                {{-- Phone --}}
                <div class="flex items-start gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5" style="background: #ecfdf5;">
                        <svg class="w-4 h-4" style="color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <div class="text-[0.7rem] text-gray-400 font-medium">الجوال</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white mt-0.5" dir="ltr">{{ auth()->user()->phone ?? '—' }}</div>
                    </div>
                </div>

                {{-- Email --}}
                <div class="flex items-start gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5" style="background: #f3e8ff;">
                        <svg class="w-4 h-4" style="color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-[0.7rem] text-gray-400 font-medium">البريد الإلكتروني</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white mt-0.5 truncate max-w-[160px]" dir="ltr">{{ auth()->user()->email ?? '—' }}</div>
                    </div>
                </div>

                {{-- Specialization --}}
                <div class="flex items-start gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5" style="background: #eff6ff;">
                        <svg class="w-4 h-4" style="color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <div class="text-[0.7rem] text-gray-400 font-medium">نوع المؤهل</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">{{ auth()->user()->specialization ?? '—' }}</div>
                    </div>
                </div>

                {{-- Specialization Type --}}
                <div class="flex items-start gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5" style="background: #fef9c3;">
                        <svg class="w-4 h-4" style="color: #a16207;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <div class="text-[0.7rem] text-gray-400 font-medium">المؤهل التعليمي </div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">{{ auth()->user()->specialization_type ?? '—' }}</div>
                    </div>
                </div>

                {{-- Graduation Date --}}
                <div class="flex items-start gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5" style="background: #fef2f2;">
                        <svg class="w-4 h-4" style="color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                    </div>
                    <div>
                        <div class="text-[0.7rem] text-gray-400 font-medium">تاريخ التخرج</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">{{ auth()->user()->date_of_graduation?->format('Y-m-d') ?? '—' }}</div>
                    </div>
                </div>

                {{-- Nationality --}}
                <div class="flex items-start gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5" style="background: #ecfdf5;">
                        <svg class="w-4 h-4" style="color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21V5a2 2 0 012-2h14a2 2 0 012 2v16M3 21h18M12 3v18M3 12h18"/></svg>
                    </div>
                    <div>
                        <div class="text-[0.7rem] text-gray-400 font-medium">الجنسية</div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white mt-0.5">{{ auth()->user()->nationality ?? '—' }}</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

  

  

    <!-- Row 3: Tickets + Ratings -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
     

        <!-- My Tickets -->
        <div class="d-card">
            <div class="d-card-head">
                <div class="icon-wrap" style="background: #fef2f2;">
                    <svg class="w-4.5 h-4.5" style="color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">التذاكر</span>
                @if($openTicketsCount > 0)
                    <span class="px-2 py-0.5 text-xs font-bold text-white rounded-full" style="background: #ef4444;">{{ $openTicketsCount }}</span>
                @endif
                <a href="{{ route('student.tickets.create') }}" class="text-xs font-bold px-3 py-1.5 rounded-lg text-white" style="background:#dc2626;">+ تذكرة جديدة</a>
            </div>
            @if($myTickets->count() > 0)
                @foreach($myTickets as $ticket)
                    <a href="{{ route('student.tickets.show', $ticket->id) }}" class="ticket-row hover:bg-red-50 dark:hover:bg-gray-800" style="text-decoration:none;color:inherit;">
                        <div class="ticket-dot" style="background: {{ $ticket->status === 'open' ? '#f59e0b' : ($ticket->status === 'in_progress' ? '#3b82f6' : ($ticket->status === 'resolved' ? '#10b981' : '#9ca3af')) }};"></div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-gray-900 dark:text-white truncate text-sm">{{ $ticket->subject ?? $ticket->title ?? 'تذكرة #'.$ticket->id }}</div>
                            <div class="text-[0.68rem] text-gray-400 mt-0.5">
                                @php
                                    $statusLabels = ['open'=>'مفتوحة','in_progress'=>'قيد المعالجة','resolved'=>'محلولة','closed'=>'مغلقة'];
                                    $statusColors = ['open'=>'#f59e0b','in_progress'=>'#3b82f6','resolved'=>'#10b981','closed'=>'#9ca3af'];
                                @endphp
                                <span style="color:{{ $statusColors[$ticket->status] ?? '#9ca3af' }};">{{ $statusLabels[$ticket->status] ?? $ticket->status }}</span>
                                · {{ $ticket->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                @endforeach
            @else
                <div class="py-10 text-center">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                    </div>
                    <p class="text-xs text-gray-400 mb-3">لا توجد تذاكر حتى الآن</p>
                    <a href="{{ route('student.tickets.create') }}" class="text-xs font-bold px-4 py-2 rounded-lg text-white" style="background:#dc2626;">افتح تذكرة</a>
                </div>
            @endif
        </div>

        <!-- Rate Teachers -->
        <div class="d-card">
            <div class="d-card-head">
                <div class="icon-wrap" style="background: #fdf2f8;">
                    <svg class="w-4.5 h-4.5" style="color: #be185d;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">تقييم المدربون </span>
                @if($ratableTeachers->count() > 0)
                    <span class="px-2 py-0.5 text-xs font-bold text-white rounded-full" style="background: #ec4899;">{{ $ratableTeachers->count() }}</span>
                @endif
            </div>
            @if($ratableTeachers->count() > 0)
                @foreach($ratableTeachers as $subj)
                    <div class="sess-item" style="padding: 0.85rem 1.25rem;">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($subj->teacher->name ?? 'T') }}&background=ec4899&color=fff&size=64&bold=true"
                             class="w-9 h-9 rounded-lg" />
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $subj->teacher->name ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $subj->name }}</div>
                        </div>
                        <a href="{{ route('student.teacher-ratings.create', $subj->id) }}" class="px-3 py-1.5 text-xs font-bold rounded-lg text-white flex-shrink-0" style="background: #ec4899;">
                            قيّم
                        </a>
                    </div>
                @endforeach
            @elseif($myRatings->count() > 0)
                @foreach($myRatings->take(3) as $rating)
                    <div class="sess-item" style="padding: 0.85rem 1.25rem;">
                        <div class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5" fill="{{ $i <= $rating->rating ? '#f59e0b' : '#e5e7eb' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $rating->teacher->name ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $rating->subject->name ?? '-' }}</div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="py-10 text-center">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                    <p class="text-xs text-gray-400">لا توجد تقييمات معلقة</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
