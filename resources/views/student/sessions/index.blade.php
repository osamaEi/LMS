@extends('layouts.dashboard')

@section('title', 'جلساتي')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<style>
    /* ===== Page Header ===== */
    .page-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #004266 100%);
        border-radius: 24px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 50px rgba(0, 113, 170, 0.3);
    }
    .page-header::before {
        content: '';
        position: absolute;
        top: -100%;
        right: -20%;
        width: 80%;
        height: 300%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.1) 0%, transparent 60%);
    }
    .page-header::after {
        content: '';
        position: absolute;
        bottom: -50px;
        left: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }

    /* ===== Stats ===== */
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .dark .stat-card { background: #1e293b; border-color: rgba(255,255,255,0.1); }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,0.1); }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: var(--stat-color);
        border-radius: 20px 20px 0 0;
    }
    .stat-number { font-size: 2rem; font-weight: 800; line-height: 1; }
    .stat-label { font-size: 0.85rem; color: #64748b; margin-top: 0.25rem; }
    .dark .stat-label { color: #94a3b8; }
    .stat-icon {
        width: 48px; height: 48px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
    }

    /* ===== View Toggle ===== */
    .view-toggle {
        display: inline-flex;
        background: #f1f5f9;
        border-radius: 14px;
        padding: 4px;
        gap: 4px;
    }
    .dark .view-toggle { background: #334155; }
    .view-toggle-btn {
        padding: 0.5rem 1.25rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        border: none;
        background: transparent;
        color: #64748b;
        transition: all 0.3s ease;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .dark .view-toggle-btn { color: #94a3b8; }
    .view-toggle-btn.active {
        background: white;
        color: #0071AA;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .dark .view-toggle-btn.active { background: #1e293b; color: #38bdf8; }
    .view-toggle-btn:hover:not(.active) { color: #0071AA; }

    /* ===== Calendar ===== */
    .calendar-wrapper {
        background: white;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }
    .dark .calendar-wrapper { background: #1e293b; border-color: rgba(255,255,255,0.1); }
    .fc { direction: rtl; font-family: 'Cairo', sans-serif; }
    .fc-toolbar { padding: 1.25rem 1.5rem !important; margin-bottom: 0 !important; background: #f8fafc; border-bottom: 2px solid #e2e8f0; flex-wrap: wrap; gap: 0.75rem; }
    .dark .fc-toolbar { background: #0f172a; border-color: #334155; }
    .fc-toolbar-title { font-size: 1.4rem !important; font-weight: 800 !important; color: #1e293b !important; }
    .dark .fc-toolbar-title { color: #e2e8f0 !important; }
    .fc .fc-button {
        background: white !important; border: 2px solid #e2e8f0 !important;
        color: #475569 !important; padding: 0.5rem 1rem; font-size: 0.8rem;
        border-radius: 10px !important; font-weight: 600;
        transition: all 0.3s ease; box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    .dark .fc .fc-button { background: #334155 !important; border-color: #475569 !important; color: #e2e8f0 !important; }
    .fc .fc-button:hover { background: #f1f5f9 !important; color: #0071AA !important; border-color: #0071AA !important; transform: translateY(-1px); }
    .fc .fc-button-active, .fc .fc-button-primary:not(:disabled).fc-button-active {
        background: #0071AA !important; color: white !important; border-color: #0071AA !important;
        box-shadow: 0 4px 12px rgba(0,113,170,0.3);
    }
    .fc .fc-today-button { background: linear-gradient(135deg, #10b981, #059669) !important; border: none !important; color: white !important; }
    .fc .fc-today-button:disabled { background: #e2e8f0 !important; color: #94a3b8 !important; opacity: 0.6; box-shadow: none; }
    .fc .fc-prev-button, .fc .fc-next-button { width: 38px; height: 38px; border-radius: 10px !important; color: #0071AA !important; }
    .fc-col-header { background: #f1f5f9; }
    .dark .fc-col-header { background: #334155; }
    .fc-col-header-cell { padding: 0.75rem 0 !important; font-weight: 700; font-size: 0.85rem; color: #0071AA; border-color: #e2e8f0 !important; }
    .dark .fc-col-header-cell { color: #94a3b8; border-color: #334155 !important; }
    .fc-daygrid-day { min-height: 100px; transition: all 0.2s ease; }
    .fc-daygrid-day:hover { background: #f0f9ff; }
    .dark .fc-daygrid-day:hover { background: #334155; }
    .fc-daygrid-day-number { font-weight: 700; font-size: 0.9rem; color: #475569; padding: 6px 10px !important; }
    .dark .fc-daygrid-day-number { color: #e2e8f0; }
    .fc-day-today { background: #dbeafe !important; }
    .dark .fc-day-today { background: #1e3a5f !important; }
    .fc-day-today .fc-daygrid-day-number { background: #0071AA; color: white !important; border-radius: 8px; }
    .fc-event { border-radius: 8px !important; padding: 4px 8px !important; margin: 2px 4px !important; cursor: pointer; border: none !important; font-size: 0.75rem; box-shadow: 0 2px 6px rgba(0,0,0,0.1); transition: all 0.2s ease; }
    .fc-event:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.15); }
    .fc-scrollgrid { border: none !important; }
    .fc-scrollgrid td, .fc-scrollgrid th { border-color: #e2e8f0 !important; }
    .dark .fc-scrollgrid td, .dark .fc-scrollgrid th { border-color: #334155 !important; }
    .fc-day-other { background: #f8fafc; }
    .dark .fc-day-other { background: #0f172a; }
    .fc-daygrid-more-link { color: #0071AA; font-weight: 700; font-size: 0.7rem; }
    .fc-timegrid-slot { height: 3rem !important; }

    /* ===== Session List Card ===== */
    .session-item {
        background: white;
        border-radius: 16px;
        border: 2px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .dark .session-item { background: #1e293b; border-color: rgba(255,255,255,0.1); }
    .session-item:hover { border-color: #0071AA; box-shadow: 0 8px 25px rgba(0,113,170,0.1); transform: translateX(-3px); }
    .session-num {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        color: white; font-weight: 800; font-size: 1rem; flex-shrink: 0;
    }
    .session-meta-tag {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 0.25rem 0.75rem; border-radius: 8px;
        font-size: 0.75rem; font-weight: 600;
    }
    .file-tag {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 0.3rem 0.75rem; border-radius: 8px;
        font-size: 0.7rem; font-weight: 600;
        background: #fef2f2; color: #dc2626;
        transition: all 0.2s ease; text-decoration: none;
    }
    .dark .file-tag { background: rgba(220,38,38,0.15); color: #fca5a5; }
    .file-tag:hover { background: #fee2e2; transform: translateY(-1px); }

    /* ===== Filter ===== */
    .filter-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }
    .dark .filter-card { background: #1e293b; border-color: rgba(255,255,255,0.1); }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr) !important; }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr !important; }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="page-header">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white">جلساتي</h1>
                    <p class="text-white/70 text-sm">متابعة جميع الجلسات والمحاضرات التعليمية</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-5 py-3 rounded-2xl flex items-center gap-3" style="background: rgba(255,255,255,0.12); backdrop-filter: blur(10px);">
                    <div class="text-center">
                        <div class="text-white font-bold text-xl">{{ $completedSessions }}/{{ $totalSessions }}</div>
                        <div class="text-white/60 text-xs">نسبة الإنجاز</div>
                    </div>
                    <div class="w-12 h-12 rounded-full border-3 flex items-center justify-center" style="border: 3px solid rgba(255,255,255,0.3);">
                        <span class="text-white font-bold text-sm">{{ $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100) : 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
        <div class="stat-card" style="--stat-color: #3b82f6;">
            <div class="flex items-center justify-between">
                <div>
                    <div class="stat-number text-gray-900 dark:text-white">{{ $totalSessions }}</div>
                    <div class="stat-label">إجمالي الجلسات</div>
                </div>
                <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>
        <div class="stat-card" style="--stat-color: #10b981;">
            <div class="flex items-center justify-between">
                <div>
                    <div class="stat-number text-gray-900 dark:text-white">{{ $completedSessions }}</div>
                    <div class="stat-label">مكتملة</div>
                </div>
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="stat-card" style="--stat-color: #0071AA;">
            <div class="flex items-center justify-between">
                <div>
                    <div class="stat-number text-gray-900 dark:text-white">{{ $zoomSessions }}</div>
                    <div class="stat-label">Zoom مباشر</div>
                </div>
                <div class="stat-icon" style="background: linear-gradient(135deg, #0071AA, #005a88);">
                    <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor"><path d="M4 4h10a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm14 2.5l4-2v11l-4-2v-7z"/></svg>
                </div>
            </div>
        </div>
        <div class="stat-card" style="--stat-color: #f59e0b;">
            <div class="flex items-center justify-between">
                <div>
                    <div class="stat-number text-gray-900 dark:text-white">{{ $totalSessions - $completedSessions }}</div>
                    <div class="stat-label">قادمة</div>
                </div>
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- View Toggle + Filters --}}
    <div class="filter-card">
        <div class="p-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            {{-- View Toggle --}}
            <div class="view-toggle">
                <button type="button" class="view-toggle-btn active" onclick="switchView('list')" id="btn-list">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    قائمة
                </button>
                <button type="button" class="view-toggle-btn" onclick="switchView('calendar')" id="btn-calendar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    تقويم
                </button>
            </div>

            {{-- Filters --}}
            <form method="GET" action="{{ route('student.my-sessions') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <select name="subject_id" class="rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm font-medium px-4 py-2.5 focus:ring-2 focus:ring-offset-0 focus:border-[#0071AA] min-w-[160px]">
                    <option value="">جميع المواد</option>
                    @foreach($enrolledSubjects as $subject)
                        <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                    @endforeach
                </select>
                <select name="type" class="rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm font-medium px-4 py-2.5 focus:ring-2 focus:ring-offset-0 focus:border-[#0071AA] min-w-[140px]">
                    <option value="">جميع الأنواع</option>
                    <option value="live_zoom" {{ $type == 'live_zoom' ? 'selected' : '' }}>Zoom مباشر</option>
                    <option value="recorded_video" {{ $type == 'recorded_video' ? 'selected' : '' }}>فيديو مسجل</option>
                </select>
                <button type="submit" class="px-5 py-2.5 text-white font-bold rounded-xl text-sm flex items-center justify-center gap-2" style="background: linear-gradient(135deg, #0071AA, #005a88);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    بحث
                </button>
                @if($subjectId || $type)
                <a href="{{ route('student.my-sessions') }}" class="px-4 py-2.5 text-gray-600 dark:text-gray-300 font-medium rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm text-center">
                    إلغاء
                </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Calendar View --}}
    <div id="calendar-view" style="display: none;">
        <div class="calendar-wrapper">
            <div id="calendar"></div>
        </div>
    </div>

    {{-- List View --}}
    <div id="list-view">
        @if($sessions->count() > 0)
            <div class="space-y-3">
                @foreach($sessions as $session)
                <div class="session-item">
                    {{-- Session Number --}}
                    <div class="session-num" style="background: linear-gradient(135deg, {{ $session->ended_at ? '#10b981, #059669' : ($session->started_at ? '#ef4444, #dc2626' : '#3b82f6, #2563eb') }});">
                        {{ $session->session_number }}
                    </div>

                    {{-- Session Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <h3 class="font-bold text-gray-900 dark:text-white text-sm">{{ $session->title }}</h3>
                            @if($session->started_at && !$session->ended_at)
                                <span class="session-meta-tag bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 animate-pulse">مباشر</span>
                            @elseif($session->ended_at)
                                <span class="session-meta-tag bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">مكتمل</span>
                            @else
                                <span class="session-meta-tag bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">مجدول</span>
                            @endif
                            <span class="session-meta-tag bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                {{ $session->type === 'live_zoom' ? 'Zoom' : 'فيديو' }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap mb-2">
                            <a href="{{ route('student.subjects.show', $session->subject_id) }}" class="session-meta-tag hover:shadow-md transition-all" style="background: #e6f4fa; color: #0071AA;">
                                {{ $session->subject->name }}
                            </a>
                            @if($session->scheduled_at)
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $session->scheduled_at->translatedFormat('D d M Y') }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $session->scheduled_at->format('h:i A') }}
                            </span>
                            @endif
                            @if($session->duration_minutes)
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $session->duration_minutes }} دقيقة</span>
                            @endif
                        </div>

                        {{-- Files --}}
                        @if($session->files && $session->files->count() > 0)
                        <div class="flex items-center gap-2 flex-wrap">
                            @foreach($session->files as $file)
                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="file-tag">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                {{ Str::limit($file->title, 20) }}
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 flex-shrink-0 flex-wrap">
                        {{-- Attendance --}}
                        @if(isset($attendances[$session->id]))
                            @if($attendances[$session->id]->attended)
                                <span class="session-meta-tag text-white" style="background: linear-gradient(135deg, #10b981, #059669);">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    حضرت
                                </span>
                            @else
                                <span class="session-meta-tag text-white" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    غائب
                                </span>
                            @endif
                        @endif

                        {{-- Zoom Actions --}}
                        @if($session->type === 'live_zoom')
                            @if($session->started_at && !$session->ended_at)
                                <a href="{{ route('student.sessions.join-zoom', $session->id) }}"
                                   class="px-4 py-2 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 animate-pulse transition-all hover:shadow-lg"
                                   style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    انضم الآن
                                </a>
                            @elseif(!$session->ended_at && $session->zoom_meeting_id && $session->scheduled_at)
                                @if($session->scheduled_at > now())
                                    <span class="session-meta-tag bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                        {{ $session->scheduled_at->diffForHumans() }}
                                    </span>
                                @elseif($session->scheduled_at->copy()->addHours(2) >= now())
                                    <a href="{{ route('student.sessions.join-zoom', $session->id) }}"
                                       class="px-4 py-2 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 transition-all hover:shadow-lg"
                                       style="background: linear-gradient(135deg, #0071AA, #005a88);">
                                        انضم للجلسة
                                    </a>
                                @endif
                            @endif
                        @endif

                        @if($session->type === 'recorded_video' && $session->hasVideo())
                            <a href="{{ $session->getVideoUrl() }}" target="_blank"
                               class="px-4 py-2 text-white font-bold rounded-xl text-xs flex items-center gap-1.5 transition-all hover:shadow-lg"
                               style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                                مشاهدة
                            </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($sessions->hasPages())
            <div class="mt-6">
                {{ $sessions->withQueryString()->links() }}
            </div>
            @endif
        @else
            <div class="filter-card p-16 text-center">
                <div class="w-20 h-20 mx-auto mb-6 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #f1f5f9, #e2e8f0);">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد جلسات حالياً</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm max-w-md mx-auto mb-6">سيتم عرض الجلسات والمحاضرات هنا عند توفرها.</p>
                <a href="{{ route('student.dashboard') }}" class="inline-flex items-center px-5 py-2.5 text-white font-bold rounded-xl text-sm" style="background: linear-gradient(135deg, #0071AA, #005a88);">
                    العودة للرئيسية
                </a>
            </div>
        @endif
    </div>
</div>

{{-- Session Detail Modal (for calendar clicks) --}}
<div id="sessionModal" style="position:fixed;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(8px);z-index:9999;display:none;align-items:center;justify-content:center;" onclick="if(event.target===this)closeSessionModal()">
    <div style="background:white;border-radius:24px;max-width:420px;width:95%;overflow:hidden;box-shadow:0 25px 80px rgba(0,0,0,0.3);transform:scale(0.95);transition:transform 0.3s ease;" id="sessionModalContent" class="dark:bg-gray-800">
        <div id="sessionModalHeader" style="padding:1.5rem;text-align:center;color:white;">
            <button type="button" onclick="closeSessionModal()" style="position:absolute;top:1rem;left:1rem;width:36px;height:36px;background:rgba(255,255,255,0.15);border:none;border-radius:10px;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center;position:relative;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div id="modalStatus" style="margin-bottom:0.5rem;"></div>
            <h3 id="modalTitle" style="font-size:1.2rem;font-weight:800;margin-bottom:0.25rem;"></h3>
            <p id="modalSubject" style="opacity:0.8;font-size:0.85rem;"></p>
        </div>
        <div style="padding:1.25rem;display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
            <div style="background:#f8fafc;border-radius:14px;padding:1rem;text-align:center;" class="dark:bg-gray-700">
                <div style="font-size:0.7rem;color:#64748b;margin-bottom:0.25rem;" class="dark:text-gray-400">رقم الجلسة</div>
                <div id="modalNumber" style="font-weight:800;color:#1e293b;font-size:1.1rem;" class="dark:text-white"></div>
            </div>
            <div style="background:#f8fafc;border-radius:14px;padding:1rem;text-align:center;" class="dark:bg-gray-700">
                <div style="font-size:0.7rem;color:#64748b;margin-bottom:0.25rem;" class="dark:text-gray-400">النوع</div>
                <div id="modalType" style="font-weight:700;color:#1e293b;font-size:0.85rem;" class="dark:text-white"></div>
            </div>
            <div style="background:#f8fafc;border-radius:14px;padding:1rem;text-align:center;grid-column:span 2;" class="dark:bg-gray-700">
                <div style="font-size:0.7rem;color:#64748b;margin-bottom:0.25rem;" class="dark:text-gray-400">التاريخ والوقت</div>
                <div id="modalDate" style="font-weight:700;color:#1e293b;font-size:0.85rem;" class="dark:text-white"></div>
            </div>
        </div>
        <div style="padding:0 1.25rem 1.25rem;">
            <a id="modalLink" href="#" style="display:flex;align-items:center;justify-content:center;gap:0.5rem;padding:0.85rem;border-radius:14px;background:linear-gradient(135deg,#10b981,#059669);color:white;font-weight:700;font-size:0.9rem;text-decoration:none;box-shadow:0 6px 20px rgba(16,185,129,0.3);transition:all 0.3s ease;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                عرض المادة
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
let calendar;
const calendarEvents = @json($calendarEvents);

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'اليوم',
            month: 'شهر',
            week: 'أسبوع',
            day: 'يوم',
            list: 'قائمة'
        },
        locale: 'ar',
        firstDay: 6,
        direction: 'rtl',
        height: 'auto',
        events: calendarEvents,
        dayMaxEvents: 3,
        moreLinkText: n => `+${n} المزيد`,
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            const props = info.event.extendedProps;
            openSessionModal({
                title: info.event.title,
                subject: props.subject,
                subject_id: props.subject_id,
                status: props.status,
                type: props.type,
                session_number: props.session_number,
                start: info.event.start,
                url: info.event.url
            });
        },
        eventContent: function(arg) {
            const props = arg.event.extendedProps;
            const icon = props.type === 'live_zoom' ? '🎥' : '📹';
            return {
                html: `<div style="padding:2px 6px;overflow:hidden;">
                    <div style="font-weight:700;font-size:0.7rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${icon} ${arg.event.title}</div>
                    <div style="font-size:0.65rem;opacity:0.85;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${props.subject}</div>
                </div>`
            };
        }
    });
});

function switchView(view) {
    document.getElementById('list-view').style.display = view === 'list' ? 'block' : 'none';
    document.getElementById('calendar-view').style.display = view === 'calendar' ? 'block' : 'none';
    document.getElementById('btn-list').classList.toggle('active', view === 'list');
    document.getElementById('btn-calendar').classList.toggle('active', view === 'calendar');
    if (view === 'calendar' && calendar) {
        calendar.render();
    }
}

function openSessionModal(session) {
    const statusColors = {
        'مباشر': 'linear-gradient(135deg, #ef4444, #dc2626)',
        'مكتملة': 'linear-gradient(135deg, #10b981, #059669)',
        'مجدولة': 'linear-gradient(135deg, #3b82f6, #2563eb)'
    };
    const header = document.getElementById('sessionModalHeader');
    header.style.background = statusColors[session.status] || statusColors['مجدولة'];
    document.getElementById('modalStatus').innerHTML = `<span style="background:rgba(255,255,255,0.2);padding:0.25rem 0.75rem;border-radius:20px;font-size:0.75rem;font-weight:600;">${session.status}</span>`;
    document.getElementById('modalTitle').textContent = session.title;
    document.getElementById('modalSubject').textContent = session.subject;
    document.getElementById('modalNumber').textContent = '#' + session.session_number;
    document.getElementById('modalType').textContent = session.type === 'live_zoom' ? 'Zoom مباشر' : 'فيديو مسجل';
    const dateStr = session.start ? new Date(session.start).toLocaleString('ar-SA', {
        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
    }) : 'غير محدد';
    document.getElementById('modalDate').textContent = dateStr;
    document.getElementById('modalLink').href = session.url;
    const modal = document.getElementById('sessionModal');
    modal.style.display = 'flex';
    setTimeout(() => {
        document.getElementById('sessionModalContent').style.transform = 'scale(1)';
    }, 10);
}

function closeSessionModal() {
    document.getElementById('sessionModalContent').style.transform = 'scale(0.95)';
    setTimeout(() => {
        document.getElementById('sessionModal').style.display = 'none';
    }, 200);
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeSessionModal();
});
</script>
@endpush
