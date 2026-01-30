@extends('layouts.dashboard')

@section('title', 'حضور الحصة - ' . $session->title)

@push('styles')
<style>
    /* Attendance Page Styles */
    .attendance-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #004466 100%);
        border-radius: 24px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }

    .attendance-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 60%;
        height: 200%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .attendance-header::after {
        content: '';
        position: absolute;
        bottom: -40%;
        left: -10%;
        width: 40%;
        height: 160%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.06) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Stats Cards */
    .att-stat-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0, 0, 0, 0.04);
    }

    .att-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--stat-color), var(--stat-color-light));
    }

    .att-stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.1);
    }

    .att-stat-card .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--stat-color), var(--stat-color-light));
        box-shadow: 0 6px 16px var(--stat-shadow);
    }

    .att-stat-card .stat-number {
        font-size: 2.25rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--stat-color), var(--stat-color-dark));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.2;
    }

    .att-stat-card .decorative {
        position: absolute;
        bottom: -15px;
        left: -15px;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--stat-color), transparent);
        opacity: 0.08;
    }

    /* Circular Progress */
    .circular-progress-wrapper {
        position: relative;
        width: 100px;
        height: 100px;
    }

    .circular-progress-wrapper svg {
        transform: rotate(-90deg);
        width: 100px;
        height: 100px;
    }

    .circular-progress-wrapper .progress-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.375rem;
        font-weight: 800;
        color: #0071AA;
    }

    /* Modern Table */
    .modern-table-wrapper {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(0, 0, 0, 0.04);
    }

    .modern-table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5rem;
        border-bottom: 2px solid #f1f5f9;
    }

    .modern-table-header .title-section {
        display: flex;
        align-items: center;
        gap: 0.875rem;
    }

    .modern-table-header .title-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .modern-table thead th {
        background: linear-gradient(180deg, #f8fafc, #f1f5f9);
        padding: 1rem 1.5rem;
        text-align: right;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #e2e8f0;
    }

    .modern-table tbody td {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9375rem;
    }

    .modern-table tbody tr {
        transition: all 0.2s ease;
    }

    .modern-table tbody tr:hover {
        background: linear-gradient(135deg, rgba(0, 113, 170, 0.03), rgba(0, 113, 170, 0.01));
    }

    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Student Avatar */
    .student-avatar {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        border: 3px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .modern-table tbody tr:hover .student-avatar {
        border-color: #0071AA;
        transform: scale(1.08);
    }

    /* Badge Styles */
    .attendance-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.4rem 0.875rem;
        border-radius: 20px;
        font-size: 0.8125rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .attendance-badge.full {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
    }

    .attendance-badge.partial {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
    }

    .attendance-badge.absent {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
    }

    /* Absent Student Card */
    .absent-card {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        border: 1px solid rgba(239, 68, 68, 0.12);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .absent-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #ef4444, #dc2626);
        border-radius: 0 4px 4px 0;
    }

    .absent-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(239, 68, 68, 0.12);
        border-color: rgba(239, 68, 68, 0.25);
    }

    .absent-card .absent-avatar {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        border: 3px solid #fecaca;
        transition: all 0.3s ease;
    }

    .absent-card:hover .absent-avatar {
        border-color: #ef4444;
        transform: scale(1.05);
    }

    /* Search Input */
    .search-input-wrapper {
        position: relative;
    }

    .search-input-wrapper svg {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }

    .search-input-wrapper input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
        background: #f8fafc;
        direction: rtl;
    }

    .search-input-wrapper input:focus {
        border-color: #0071AA;
        box-shadow: 0 0 0 4px rgba(0, 113, 170, 0.1);
        outline: none;
        background: white;
    }

    /* Progress Mini Bar */
    .progress-mini {
        height: 6px;
        border-radius: 10px;
        background: #f1f5f9;
        overflow: hidden;
        width: 80px;
    }

    .progress-mini-fill {
        height: 100%;
        border-radius: 10px;
        background: linear-gradient(90deg, #10b981, #34d399);
        transition: width 0.6s ease;
    }

    /* Time Badge */
    .time-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        border-radius: 10px;
        font-size: 0.8125rem;
        font-weight: 500;
        background: #f1f5f9;
        color: #475569;
    }

    /* Breadcrumb Modern */
    .breadcrumb-modern {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        background: white;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(0, 0, 0, 0.04);
    }

    .breadcrumb-modern a {
        color: #64748b;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .breadcrumb-modern a:hover {
        color: #0071AA;
    }

    .breadcrumb-modern .separator {
        color: #cbd5e1;
    }

    .breadcrumb-modern .current {
        color: #1e293b;
        font-weight: 600;
    }

    /* Animations */
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .slide-up {
        animation: slideUp 0.5s ease forwards;
    }

    .slide-up-d1 { animation-delay: 0.1s; opacity: 0; }
    .slide-up-d2 { animation-delay: 0.2s; opacity: 0; }
    .slide-up-d3 { animation-delay: 0.3s; opacity: 0; }
    .slide-up-d4 { animation-delay: 0.4s; opacity: 0; }

    /* Dark mode support */
    .dark .att-stat-card,
    .dark .modern-table-wrapper,
    .dark .absent-card {
        background: #1e293b;
        border-color: rgba(255, 255, 255, 0.08);
    }

    .dark .modern-table thead th {
        background: linear-gradient(180deg, #1e293b, #0f172a);
        color: #94a3b8;
        border-color: #334155;
    }

    .dark .modern-table tbody td {
        border-color: #334155;
    }

    .dark .modern-table tbody tr:hover {
        background: linear-gradient(135deg, rgba(0, 113, 170, 0.08), rgba(0, 113, 170, 0.04));
    }

    .dark .breadcrumb-modern {
        background: #1e293b;
        border-color: rgba(255, 255, 255, 0.08);
    }

    .dark .search-input-wrapper input {
        background: #0f172a;
        border-color: #334155;
        color: #e2e8f0;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="breadcrumb-modern text-sm slide-up">
        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <a href="{{ route('teacher.my-subjects.index') }}">موادي</a>
        <span class="separator">/</span>
        <a href="{{ route('teacher.my-subjects.show', $subject->id) }}">{{ $subject->name }}</a>
        <span class="separator">/</span>
        <span class="current">حضور الحصة {{ $session->session_number }}</span>
    </nav>

    <!-- Header -->
    <div class="attendance-header slide-up">
        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ $session->title }}</h1>
                        <p class="mt-1 text-white/80 text-sm">الحصة {{ $session->session_number }} - {{ $subject->name }}</p>
                        @if($session->scheduled_at)
                            <div class="flex items-center gap-4 mt-3">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/15 text-white/90 text-sm backdrop-blur-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d') }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/15 text-white/90 text-sm backdrop-blur-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($session->scheduled_at)->format('h:i A') }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
                <a href="{{ route('teacher.my-subjects.show', $subject->id) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium bg-white/15 hover:bg-white/25 text-white transition-all backdrop-blur-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    العودة للمادة
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Enrolled -->
        <div class="att-stat-card slide-up slide-up-d1" style="--stat-color: #3b82f6; --stat-color-light: #60a5fa; --stat-color-dark: #2563eb; --stat-shadow: rgba(59, 130, 246, 0.25);">
            <div class="flex items-start justify-between mb-3">
                <div class="stat-icon">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-number">{{ $stats['total_enrolled'] }}</div>
            <p class="text-sm font-medium text-gray-500 mt-1">إجمالي المسجلين</p>
            <div class="decorative"></div>
        </div>

        <!-- Attended -->
        <div class="att-stat-card slide-up slide-up-d2" style="--stat-color: #10b981; --stat-color-light: #34d399; --stat-color-dark: #059669; --stat-shadow: rgba(16, 185, 129, 0.25);">
            <div class="flex items-start justify-between mb-3">
                <div class="stat-icon">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-number">{{ $stats['attended'] }}</div>
            <p class="text-sm font-medium text-gray-500 mt-1">الحاضرون</p>
            <div class="decorative"></div>
        </div>

        <!-- Absent -->
        <div class="att-stat-card slide-up slide-up-d3" style="--stat-color: #ef4444; --stat-color-light: #f87171; --stat-color-dark: #dc2626; --stat-shadow: rgba(239, 68, 68, 0.25);">
            <div class="flex items-start justify-between mb-3">
                <div class="stat-icon">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
            <div class="stat-number">{{ $stats['absent'] }}</div>
            <p class="text-sm font-medium text-gray-500 mt-1">الغائبون</p>
            <div class="decorative"></div>
        </div>

        <!-- Attendance Rate with Circular Progress -->
        <div class="att-stat-card slide-up slide-up-d4" style="--stat-color: #0071AA; --stat-color-light: #0099dd; --stat-color-dark: #005a88; --stat-shadow: rgba(0, 113, 170, 0.25);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-2">نسبة الحضور</p>
                    <div class="stat-number">{{ $stats['attendance_rate'] }}%</div>
                </div>
                <div class="circular-progress-wrapper">
                    <svg viewBox="0 0 100 100">
                        <defs>
                            <linearGradient id="progressGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#0071AA" />
                                <stop offset="100%" style="stop-color:#0099dd" />
                            </linearGradient>
                        </defs>
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#f1f5f9" stroke-width="8" />
                        <circle cx="50" cy="50" r="40" fill="none" stroke="url(#progressGrad)" stroke-width="8" stroke-linecap="round"
                                stroke-dasharray="{{ 2 * 3.14159 * 40 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 40 * (1 - $stats['attendance_rate'] / 100) }}" />
                    </svg>
                </div>
            </div>
            <div class="decorative"></div>
        </div>
    </div>

    <!-- Attended Students Table -->
    <div class="modern-table-wrapper slide-up" x-data="{ search: '' }">
        <div class="modern-table-header">
            <div class="title-section">
                <div class="title-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">الطلاب الحاضرون</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $attendances->where('attended', true)->count() }} طالب من أصل {{ $stats['total_enrolled'] }}</p>
                </div>
            </div>
            <div class="search-input-wrapper" style="width: 260px;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" x-model="search" placeholder="ابحث عن طالب..." />
            </div>
        </div>

        @if($attendances->where('attended', true)->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full modern-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الطالب</th>
                        <th>وقت الانضمام</th>
                        <th>وقت المغادرة</th>
                        <th>المدة</th>
                        @if($session->type === 'recorded_video')
                        <th>نسبة المشاهدة</th>
                        @endif
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances->where('attended', true) as $index => $attendance)
                    <tr x-show="search === '' || '{{ strtolower($attendance->student->name ?? '') }}'.includes(search.toLowerCase()) || '{{ strtolower($attendance->student->email ?? '') }}'.includes(search.toLowerCase())">
                        <td class="text-gray-500 font-medium">{{ $index + 1 }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($attendance->student->name ?? 'N/A') }}&background=0071AA&color=fff&size=44&rounded=true&bold=true"
                                     alt="{{ $attendance->student->name ?? 'N/A' }}"
                                     class="student-avatar" />
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $attendance->student->name ?? 'غير معروف' }}</p>
                                    <p class="text-xs text-gray-400">{{ $attendance->student->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($attendance->joined_at)
                                <span class="time-badge">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($attendance->joined_at)->format('h:i A') }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td>
                            @if($attendance->left_at)
                                <span class="time-badge">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($attendance->left_at)->format('h:i A') }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td>
                            @if($attendance->duration_minutes)
                                <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $attendance->duration_minutes }}</span>
                                <span class="text-gray-400 text-sm">دقيقة</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        @if($session->type === 'recorded_video')
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="progress-mini">
                                    <div class="progress-mini-fill" style="width: {{ $attendance->watch_percentage ?? 0 }}%;"></div>
                                </div>
                                <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $attendance->watch_percentage ?? 0 }}%</span>
                            </div>
                        </td>
                        @endif
                        <td>
                            @if($attendance->video_completed || ($attendance->duration_minutes && $attendance->duration_minutes >= ($session->duration_minutes * 0.8)))
                                <span class="attendance-badge full">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    حضور كامل
                                </span>
                            @else
                                <span class="attendance-badge partial">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    حضور جزئي
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-16 text-center">
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-5" style="background: linear-gradient(135deg, #fef3c7, #fde68a);">
                <svg class="w-10 h-10" style="color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">لا يوجد حضور بعد</h3>
            <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-sm mx-auto">لم يحضر أي طالب هذه الحصة حتى الآن. سيظهر الحضور تلقائياً عند انضمام الطلاب</p>
        </div>
        @endif
    </div>

    <!-- Absent Students -->
    @if($absentStudents->count() > 0)
    <div class="modern-table-wrapper slide-up">
        <div class="modern-table-header">
            <div class="title-section">
                <div class="title-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">الطلاب الغائبون</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $absentStudents->count() }} طالب لم يحضروا هذه الحصة</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($absentStudents as $student)
                <div class="absent-card">
                    <div class="flex items-center gap-3.5">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name ?? 'N/A') }}&background=ef4444&color=fff&size=48&rounded=true&bold=true"
                             alt="{{ $student->name ?? 'N/A' }}"
                             class="absent-avatar" />
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $student->name ?? 'غير معروف' }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $student->email ?? '' }}</p>
                        </div>
                        <span class="attendance-badge absent flex-shrink-0">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            غائب
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
