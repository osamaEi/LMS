@extends('layouts.dashboard')

@section('title', 'لوحة تحكم الطالب')

@push('styles')
<style>
    .dash-page { max-width: 1240px; margin: 0 auto; }

    /* Header */
    .dash-header {
        background: linear-gradient(135deg, #0071AA 0%, #004d77 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .dash-header::before {
        content: '';
        position: absolute;
        top: -60%;
        left: -8%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(255,255,255,0.07) 0%, transparent 70%);
        border-radius: 50%;
    }
    .dash-header::after {
        content: '';
        position: absolute;
        bottom: -70%;
        right: -3%;
        width: 280px;
        height: 280px;
        background: radial-gradient(circle, rgba(255,255,255,0.04) 0%, transparent 70%);
        border-radius: 50%;
    }

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
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 relative z-10">
            <div class="flex items-center gap-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=ffffff&color=0071AA&size=96&bold=true"
                     alt="{{ auth()->user()->name }}"
                     class="w-14 h-14 rounded-2xl border-2 border-white/20 shadow-lg" />
                <div>
                    <p class="text-sm opacity-65">مرحباً بك</p>
                    <h1 class="text-2xl font-extrabold tracking-tight">{{ auth()->user()->name }}</h1>
                    @if(auth()->user()->program)
                        <p class="text-sm opacity-55 mt-0.5">{{ auth()->user()->program->name }}</p>
                    @endif
                </div>
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('student.my-sessions') }}" class="px-4 py-2 rounded-xl text-sm font-semibold" style="background: rgba(255,255,255,0.12);">جلساتي</a>
                <a href="{{ route('student.attendance') }}" class="px-4 py-2 rounded-xl text-sm font-semibold" style="background: rgba(255,255,255,0.12);">سجل الحضور</a>
                <a href="{{ route('student.my-program') }}" class="px-4 py-2 rounded-xl text-sm font-semibold" style="background: rgba(255,255,255,0.12);">برنامجي</a>
            </div>
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

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-box">
            <div class="s-icon" style="background: linear-gradient(135deg, #0071AA, #005588);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
                <div class="s-val">{{ $stats['subjects_count'] }}</div>
                <div class="s-lbl">المواد المسجلة</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="s-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <div class="s-val">{{ $stats['total_sessions'] }}</div>
                <div class="s-lbl">إجمالي الجلسات</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="s-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="s-val">{{ $stats['completed_sessions'] }}</div>
                <div class="s-lbl">جلسات مكتملة</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="s-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div>
                <div class="s-val">{{ $overallAttendance }}<span style="font-size: 1rem;">%</span></div>
                <div class="s-lbl">نسبة الحضور</div>
            </div>
        </div>
    </div>

    <!-- Row 1: Upcoming Sessions + Calendar -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Upcoming Sessions -->
        <div class="lg:col-span-2 d-card">
            <div class="d-card-head">
                <div class="icon-wrap" style="background: #eff6ff;">
                    <svg class="w-4.5 h-4.5" style="color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">الجلسات القادمة</span>
                <a href="{{ route('student.my-sessions') }}" class="link-all" style="background: #eff6ff; color: #2563eb;">عرض الكل</a>
            </div>
            @if($upcomingSessions->count() > 0)
                @foreach($upcomingSessions as $session)
                    <div class="sess-item">
                        <div class="sess-icon" style="background: {{ $session->type === 'live_zoom' ? 'linear-gradient(135deg, #2563eb, #1d4ed8)' : 'linear-gradient(135deg, #8b5cf6, #7c3aed)' }};">
                            @if($session->type === 'live_zoom')
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            @else
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-sm text-gray-900 dark:text-white truncate">{{ $session->title }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $session->subject->name }}</div>
                        </div>
                        <div class="text-left flex-shrink-0">
                            @if($session->scheduled_at)
                                <div class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $session->scheduled_at->format('d/m') }}</div>
                                <div class="text-xs text-gray-400">{{ $session->scheduled_at->format('H:i') }}</div>
                            @endif
                        </div>
                        @if($session->type === 'live_zoom' && $session->zoom_meeting_id)
                            <a href="{{ route('student.sessions.join-zoom', $session->id) }}"
                               class="px-3 py-1.5 text-white text-xs font-bold rounded-lg flex-shrink-0" style="background: #0071AA;">
                                انضم
                            </a>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="py-12 text-center">
                    <div class="w-14 h-14 bg-gray-100 dark:bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-gray-400 text-sm font-medium">لا توجد جلسات قادمة</p>
                </div>
            @endif
        </div>

        <!-- Calendar -->
        <div class="d-card">
            <div class="d-card-head">
                <div class="icon-wrap" style="background: #fef3c7;">
                    <svg class="w-4.5 h-4.5" style="color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white">التقويم</span>
            </div>
            <div class="p-4">
                @php
                    $now = now();
                    $cMonth = $now->month;
                    $cYear = $now->year;
                    $firstDay = $now->copy()->startOfMonth();
                    $daysInM = $now->copy()->endOfMonth()->day;
                    $startDow = $firstDay->dayOfWeek;
                    $sessDates = $upcomingSessions->pluck('scheduled_at')->map(fn($d) => $d?->format('Y-m-d'))->filter()->toArray();
                    $arMonths = ['','يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
                    $arDays = ['ح','ن','ث','ر','خ','ج','س'];
                @endphp
                <div class="text-center mb-3">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $arMonths[$cMonth] }} {{ $cYear }}</span>
                </div>
                <div class="cal-grid mb-1">
                    @foreach($arDays as $d)<div class="cal-head">{{ $d }}</div>@endforeach
                </div>
                <div class="cal-grid">
                    @for($i = 0; $i < $startDow; $i++)<div></div>@endfor
                    @for($dy = 1; $dy <= $daysInM; $dy++)
                        @php
                            $ds = $cYear.'-'.str_pad($cMonth,2,'0',STR_PAD_LEFT).'-'.str_pad($dy,2,'0',STR_PAD_LEFT);
                            $isT = ($dy == $now->day);
                            $hasS = in_array($ds, $sessDates);
                        @endphp
                        <div class="cal-cell {{ $isT ? 'today' : '' }} {{ $hasS ? 'has-session' : '' }}">{{ $dy }}</div>
                    @endfor
                </div>
                <div class="flex items-center justify-center gap-4 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full" style="background: #0071AA;"></span>
                        <span class="text-[0.7rem] text-gray-400">اليوم</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full" style="background: #10b981;"></span>
                        <span class="text-[0.7rem] text-gray-400">جلسة</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Subjects Progress + Attendance Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Subjects Progress -->
        <div class="d-card">
            <div class="d-card-head">
                <div class="icon-wrap" style="background: #ecfdf5;">
                    <svg class="w-4.5 h-4.5" style="color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">تقدم المواد</span>
                <a href="{{ route('student.attendance') }}" class="link-all" style="background: #ecfdf5; color: #059669;">سجل الحضور</a>
            </div>
            <div class="p-5 space-y-4">
                @forelse($subjectsProgress as $id => $prog)
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <a href="{{ route('student.subjects.show', $id) }}" class="text-sm font-bold text-gray-900 dark:text-white hover:underline" style="color: #0071AA;">{{ $prog['name'] }}</a>
                            <span class="text-xs font-bold" style="color: {{ $prog['percentage'] >= 75 ? '#059669' : ($prog['percentage'] >= 50 ? '#d97706' : '#dc2626') }};">{{ $prog['percentage'] }}%</span>
                        </div>
                        <div class="prog-track">
                            <div class="prog-fill" style="width: {{ $prog['percentage'] }}%; background: {{ $prog['percentage'] >= 75 ? '#10b981' : ($prog['percentage'] >= 50 ? '#f59e0b' : '#ef4444') }};"></div>
                        </div>
                        <div class="flex justify-between mt-1">
                            <span class="text-[0.7rem] text-gray-400">{{ $prog['attended'] }} / {{ $prog['total'] }} جلسة</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <p class="text-gray-400 text-sm">لا توجد مواد مسجلة</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Monthly Attendance Chart -->
        <div class="d-card">
            <div class="d-card-head">
                <div class="icon-wrap" style="background: #f3e8ff;">
                    <svg class="w-4.5 h-4.5" style="color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white">الحضور الشهري</span>
            </div>
            <div class="p-5">
                @if($monthlyAttendance->count() > 0)
                    <div class="flex items-end gap-3 justify-center" style="height: 160px;">
                        @php
                            $arShortMonths = ['01'=>'يناير','02'=>'فبراير','03'=>'مارس','04'=>'أبريل','05'=>'مايو','06'=>'يونيو','07'=>'يوليو','08'=>'أغسطس','09'=>'سبتمبر','10'=>'أكتوبر','11'=>'نوفمبر','12'=>'ديسمبر'];
                        @endphp
                        @foreach($monthlyAttendance as $m)
                            @php
                                $mKey = explode('-', $m->month)[1];
                                $barH = max($m->rate * 1.4, 8);
                                $barColor = $m->rate >= 75 ? '#10b981' : ($m->rate >= 50 ? '#f59e0b' : '#ef4444');
                            @endphp
                            <div class="flex flex-col items-center gap-1 flex-1">
                                <span class="text-[0.65rem] font-bold" style="color: {{ $barColor }};">{{ $m->rate }}%</span>
                                <div style="width: 100%; max-width: 36px; height: {{ $barH }}px; background: {{ $barColor }}; border-radius: 6px 6px 2px 2px; opacity: 0.85;"></div>
                                <span class="text-[0.6rem] text-gray-400 mt-0.5">{{ $arShortMonths[$mKey] ?? $mKey }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700 text-center">
                        <span class="text-xs text-gray-400">آخر 6 أشهر — المعدل العام: <strong style="color: #0071AA;">{{ $overallAttendance }}%</strong></span>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-400 text-sm">لا توجد بيانات حضور شهرية بعد</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Row 3: Surveys + Tickets + Ratings -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Pending Surveys -->
        <div class="d-card">
            <div class="d-card-head">
                <div class="icon-wrap" style="background: #fef3c7;">
                    <svg class="w-4.5 h-4.5" style="color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">الاستبيانات</span>
                @if($pendingSurveys->count() > 0)
                    <span class="px-2 py-0.5 text-xs font-bold text-white rounded-full" style="background: #f59e0b;">{{ $pendingSurveys->count() }}</span>
                @endif
            </div>
            @if($pendingSurveys->count() > 0)
                @foreach($pendingSurveys as $survey)
                    <div class="survey-item">
                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $survey->title }}</div>
                        @if($survey->subject)
                            <div class="text-xs text-gray-400 mt-0.5">{{ $survey->subject->name }}</div>
                        @endif
                        <div class="mt-2">
                            <a href="{{ route('student.surveys.show', $survey->id) }}" class="px-3 py-1.5 text-xs font-bold rounded-lg text-white" style="background: #f59e0b;">
                                أجب الآن
                            </a>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="py-10 text-center">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <p class="text-xs text-gray-400">لا توجد استبيانات معلقة</p>
                </div>
            @endif
        </div>

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
            </div>
            @if($myTickets->count() > 0)
                @foreach($myTickets as $ticket)
                    <div class="ticket-row">
                        <div class="ticket-dot" style="background: {{ $ticket->status === 'open' ? '#f59e0b' : ($ticket->status === 'in_progress' ? '#3b82f6' : ($ticket->status === 'resolved' ? '#10b981' : '#9ca3af')) }};"></div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-gray-900 dark:text-white truncate text-sm">{{ $ticket->subject ?? $ticket->title ?? 'تذكرة #'.$ticket->id }}</div>
                        </div>
                        <span class="text-[0.65rem] text-gray-400 flex-shrink-0">{{ $ticket->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            @else
                <div class="py-10 text-center">
                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                    </div>
                    <p class="text-xs text-gray-400">لا توجد تذاكر</p>
                </div>
            @endif
        </div>

        <!-- Rate Teachers -->
        <div class="d-card">
            <div class="d-card-head">
                <div class="icon-wrap" style="background: #fdf2f8;">
                    <svg class="w-4.5 h-4.5" style="color: #be185d;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">تقييم المعلمين</span>
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
