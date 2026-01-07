@extends('layouts.dashboard')

@section('title', 'إدارة الجلسات')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    :root {
        --primary: #0071AA;
        --primary-dark: #005a88;
        --primary-light: #e6f4fa;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #f3f4f6;
        transition: all 0.3s ease;
    }

    .dark .stat-card {
        background: #1f2937;
        border-color: #374151;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,113,170,0.15);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
    }

    .dark .stat-value {
        color: #f9fafb;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #6b7280;
    }

    /* Calendar Container */
    .calendar-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        border: 1px solid #f3f4f6;
    }

    .dark .calendar-container {
        background: #1f2937;
        border-color: #374151;
    }

    .calendar-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .calendar-body {
        padding: 1.5rem;
    }

    /* FullCalendar Styles */
    .fc {
        font-family: inherit !important;
    }

    .fc-theme-standard td,
    .fc-theme-standard th,
    .fc-theme-standard .fc-scrollgrid {
        border-color: #e5e7eb !important;
    }

    .dark .fc-theme-standard td,
    .dark .fc-theme-standard th,
    .dark .fc-theme-standard .fc-scrollgrid {
        border-color: #374151 !important;
    }

    .fc .fc-toolbar {
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem !important;
    }

    .fc .fc-toolbar-title {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #1f2937 !important;
    }

    .dark .fc .fc-toolbar-title {
        color: #f9fafb !important;
    }

    .fc .fc-button {
        background: white !important;
        color: #374151 !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 10px !important;
        padding: 10px 16px !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
        box-shadow: none !important;
        transition: all 0.2s ease !important;
    }

    .dark .fc .fc-button {
        background: #374151 !important;
        color: #e5e7eb !important;
        border-color: #4b5563 !important;
    }

    .fc .fc-button:hover {
        background: var(--primary) !important;
        color: white !important;
        border-color: var(--primary) !important;
    }

    .fc .fc-button-primary:not(:disabled).fc-button-active {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%) !important;
        color: white !important;
        border-color: var(--primary) !important;
    }

    .fc .fc-col-header-cell {
        background: #f9fafb !important;
        padding: 12px 0 !important;
    }

    .dark .fc .fc-col-header-cell {
        background: #111827 !important;
    }

    .fc .fc-col-header-cell-cushion {
        color: #374151 !important;
        font-weight: 600 !important;
        text-decoration: none !important;
    }

    .dark .fc .fc-col-header-cell-cushion {
        color: #d1d5db !important;
    }

    .fc .fc-daygrid-day {
        min-height: 100px !important;
    }

    .fc .fc-daygrid-day-number {
        font-weight: 600 !important;
        padding: 8px !important;
        color: #374151 !important;
    }

    .dark .fc .fc-daygrid-day-number {
        color: #d1d5db !important;
    }

    .fc .fc-day-today {
        background: rgba(0, 113, 170, 0.08) !important;
    }

    .fc .fc-day-today .fc-daygrid-day-number {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%) !important;
        color: white !important;
        border-radius: 8px !important;
        width: 32px !important;
        height: 32px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .fc .fc-event {
        border-radius: 6px !important;
        border: none !important;
        padding: 2px 6px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        margin-bottom: 2px !important;
        transition: all 0.2s ease !important;
    }

    .fc .fc-event:hover {
        transform: scale(1.02) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }

    .fc .fc-event.event-zoom {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 100%) !important;
    }

    .fc .fc-event.event-video {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
    }

    .fc .fc-event.event-live {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        animation: pulse 2s infinite;
    }

    .fc .fc-event.event-completed {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    /* Sessions List */
    .sessions-list {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid #f3f4f6;
        overflow: hidden;
    }

    .dark .sessions-list {
        background: #1f2937;
        border-color: #374151;
    }

    .list-header {
        padding: 1.25rem;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .dark .list-header {
        border-color: #374151;
    }

    .session-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.2s ease;
    }

    .dark .session-item {
        border-color: #374151;
    }

    .session-item:hover {
        background: #f9fafb;
    }

    .dark .session-item:hover {
        background: #111827;
    }

    .session-item:last-child {
        border-bottom: none;
    }

    .session-time {
        width: 70px;
        text-align: center;
        flex-shrink: 0;
    }

    .session-time-value {
        font-size: 1rem;
        font-weight: 700;
        color: var(--primary);
    }

    .session-time-period {
        font-size: 0.7rem;
        color: #6b7280;
        text-transform: uppercase;
    }

    .session-indicator {
        width: 4px;
        height: 50px;
        border-radius: 4px;
        flex-shrink: 0;
    }

    .session-info {
        flex: 1;
        min-width: 0;
    }

    .session-title {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.95rem;
        margin-bottom: 4px;
    }

    .dark .session-title {
        color: #f9fafb;
    }

    .session-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        font-size: 0.8rem;
        color: #6b7280;
    }

    .session-meta-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .session-actions {
        display: flex;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        color: #6b7280;
    }

    .action-btn:hover {
        background: #f3f4f6;
    }

    .dark .action-btn:hover {
        background: #374151;
    }

    .action-btn.view:hover { color: #0071AA; }
    .action-btn.edit:hover { color: #f59e0b; }
    .action-btn.delete:hover { color: #ef4444; background: #fef2f2; }
    .dark .action-btn.delete:hover { background: rgba(239, 68, 68, 0.2); }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .status-live {
        background: #fef2f2;
        color: #dc2626;
        animation: pulse 2s infinite;
    }

    .status-scheduled {
        background: #eff6ff;
        color: #2563eb;
    }

    .status-completed {
        background: #f0fdf4;
        color: #16a34a;
    }

    /* Add Button */
    .add-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 113, 170, 0.3);
    }

    .add-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 113, 170, 0.4);
    }

    /* View Toggle */
    .view-toggle {
        display: flex;
        gap: 4px;
        padding: 4px;
        background: #f3f4f6;
        border-radius: 10px;
    }

    .dark .view-toggle {
        background: #374151;
    }

    .view-btn {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        color: #6b7280;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .view-btn:hover {
        color: #374151;
    }

    .view-btn.active {
        background: white;
        color: var(--primary);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .dark .view-btn.active {
        background: #1f2937;
    }

    /* Legend */
    .legend {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 12px;
    }

    .dark .legend {
        background: #111827;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        color: #6b7280;
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 4px;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        background: #f3f4f6;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dark .empty-icon {
        background: #374151;
    }

    /* Filter Bar */
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }

    .dark .filter-bar {
        background: #111827;
    }

    .filter-select {
        padding: 0.5rem 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.875rem;
        background: white;
        min-width: 150px;
    }

    .dark .filter-select {
        background: #1f2937;
        border-color: #374151;
        color: #f9fafb;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إدارة الجلسات</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">عرض وإدارة جميع جلسات Zoom</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="view-toggle">
                <button class="view-btn active" data-view="calendar">
                    <svg class="w-4 h-4 inline-block ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    التقويم
                </button>
                <button class="view-btn" data-view="list">
                    <svg class="w-4 h-4 inline-block ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    القائمة
                </button>
            </div>
            <a href="{{ route('admin.sessions.create') }}" class="add-btn">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                إضافة جلسة
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span class="text-green-700 dark:text-green-300 font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        <span class="text-red-700 dark:text-red-300 font-medium">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $sessions->total() }}</div>
                <div class="stat-label">إجمالي الجلسات</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $sessions->where('status', 'live')->count() }}</div>
                <div class="stat-label">مباشر الآن</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $sessions->where('status', 'scheduled')->count() }}</div>
                <div class="stat-label">مجدولة</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">{{ $sessions->where('status', 'completed')->count() }}</div>
                <div class="stat-label">مكتملة</div>
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    <div id="calendarView" class="calendar-container">
        <div class="calendar-header">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">تقويم الجلسات</h2>
                    <p class="text-white/70 text-sm">انقر على أي جلسة لعرض التفاصيل</p>
                </div>
            </div>
            <div class="legend" style="background: rgba(255,255,255,0.15); padding: 0.75rem 1rem;">
                <div class="legend-item" style="color: white;">
                    <div class="legend-dot" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);"></div>
                    <span>Zoom</span>
                </div>
                <div class="legend-item" style="color: white;">
                    <div class="legend-dot" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);"></div>
                    <span>مباشر</span>
                </div>
                <div class="legend-item" style="color: white;">
                    <div class="legend-dot" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"></div>
                    <span>مكتمل</span>
                </div>
            </div>
        </div>
        <div class="calendar-body">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- List View -->
    <div id="listView" class="sessions-list" style="display: none;">
        <div class="list-header">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">قائمة الجلسات</h3>
            <span class="text-sm text-gray-500">{{ $sessions->total() }} جلسة</span>
        </div>

        @forelse($sessions as $session)
        <div class="session-item">
            <div class="session-time">
                @if($session->scheduled_at)
                <div class="session-time-value">{{ $session->scheduled_at->format('h:i') }}</div>
                <div class="session-time-period">{{ $session->scheduled_at->format('A') == 'AM' ? 'ص' : 'م' }}</div>
                @else
                <div class="session-time-value">--:--</div>
                @endif
            </div>

            <div class="session-indicator" style="background: linear-gradient(135deg,
                @if($session->status === 'live') #ef4444, #dc2626
                @elseif($session->status === 'completed') #10b981, #059669
                @else #0071AA, #005a88
                @endif);"></div>

            <div class="session-info">
                <div class="session-title">{{ $session->title }}</div>
                <div class="session-meta">
                    <span class="session-meta-item">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        {{ $session->subject->name ?? '-' }}
                    </span>
                    @if($session->scheduled_at)
                    <span class="session-meta-item">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $session->scheduled_at->format('Y/m/d') }}
                    </span>
                    @endif
                    <span class="session-meta-item">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $session->duration_minutes ?? 60 }} دقيقة
                    </span>
                </div>
            </div>

            <div>
                @if($session->status === 'live')
                    <span class="status-badge status-live">
                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                        مباشر
                    </span>
                @elseif($session->status === 'scheduled')
                    <span class="status-badge status-scheduled">مجدول</span>
                @elseif($session->status === 'completed')
                    <span class="status-badge status-completed">مكتمل</span>
                @endif
            </div>

            <div class="session-actions">
                <a href="{{ route('admin.sessions.show', $session) }}" class="action-btn view" title="عرض">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </a>
                <a href="{{ route('admin.sessions.edit', $session) }}" class="action-btn edit" title="تعديل">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </a>
                <form action="{{ route('admin.sessions.destroy', $session) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الجلسة؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn delete" title="حذف">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon">
                <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">لا توجد جلسات</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">ابدأ بإضافة جلسة جديدة</p>
            <a href="{{ route('admin.sessions.create') }}" class="add-btn">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                إضافة جلسة
            </a>
        </div>
        @endforelse

        @if($sessions->hasPages())
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $sessions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/ar.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sessions data from server
    const sessionsData = @json($sessions->items());

    // Convert to calendar events
    const events = sessionsData.map(session => {
        let className = 'event-zoom';
        if (session.status === 'live') className = 'event-live';
        else if (session.status === 'completed') className = 'event-completed';
        else if (session.type === 'recorded_video') className = 'event-video';

        return {
            id: session.id,
            title: session.title_ar || session.title,
            start: session.scheduled_at,
            className: className,
            extendedProps: {
                subject: session.subject?.name || '',
                duration: session.duration_minutes,
                status: session.status,
                type: session.type
            }
        };
    }).filter(e => e.start);

    // Initialize Calendar
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'ar',
        direction: 'rtl',
        headerToolbar: {
            right: 'prev,next today',
            center: 'title',
            left: 'dayGridMonth,dayGridWeek,listWeek'
        },
        height: 'auto',
        events: events,
        eventClick: function(info) {
            window.location.href = '/admin/sessions/' + info.event.id;
        },
        eventDidMount: function(info) {
            // Add tooltip
            info.el.title = info.event.extendedProps.subject + ' - ' + (info.event.extendedProps.duration || 60) + ' دقيقة';
        }
    });
    calendar.render();

    // View Toggle
    const viewBtns = document.querySelectorAll('.view-btn');
    const calendarView = document.getElementById('calendarView');
    const listView = document.getElementById('listView');

    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            viewBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            if (this.dataset.view === 'calendar') {
                calendarView.style.display = 'block';
                listView.style.display = 'none';
                calendar.render();
            } else {
                calendarView.style.display = 'none';
                listView.style.display = 'block';
            }
        });
    });
});
</script>
@endpush
