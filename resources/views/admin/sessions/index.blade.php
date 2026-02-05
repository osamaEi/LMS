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

    /* Modal Styles */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .modal-backdrop.active {
        display: flex;
    }

    .modal-container {
        background: white;
        border-radius: 24px;
        width: 100%;
        max-width: 560px;
        max-height: 90vh;
        overflow: hidden;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        animation: modalSlide 0.3s ease;
    }

    .dark .modal-container {
        background: #1f2937;
    }

    @keyframes modalSlide {
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-close {
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
    }

    .modal-close:hover {
        background: rgba(255,255,255,0.3);
    }

    .modal-body {
        padding: 1.5rem;
        max-height: 60vh;
        overflow-y: auto;
    }

    .modal-footer {
        padding: 1.25rem 1.5rem;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    .dark .modal-footer {
        background: #111827;
        border-color: #374151;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .dark .form-label {
        color: #d1d5db;
    }

    .form-input, .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        background: white;
        color: #1f2937;
    }

    .dark .form-input, .dark .form-select {
        background: #111827;
        border-color: #374151;
        color: #f9fafb;
    }

    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(0, 113, 170, 0.1);
    }

    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: left 12px center;
        background-size: 20px;
        padding-left: 40px;
    }

    .recurrence-toggle {
        display: flex;
        gap: 0.5rem;
        padding: 4px;
        background: #f3f4f6;
        border-radius: 12px;
    }

    .dark .recurrence-toggle {
        background: #111827;
    }

    .recurrence-option {
        flex: 1;
        padding: 0.625rem 1rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #6b7280;
        background: transparent;
        border: none;
    }

    .recurrence-option:hover {
        color: #374151;
    }

    .dark .recurrence-option:hover {
        color: #d1d5db;
    }

    .recurrence-option.active {
        background: white;
        color: var(--primary);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .dark .recurrence-option.active {
        background: #374151;
        color: #60a5fa;
    }

    .day-selector {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .day-btn {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        background: white;
        color: #6b7280;
        font-weight: 600;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dark .day-btn {
        background: #111827;
        border-color: #374151;
        color: #9ca3af;
    }

    .day-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .day-btn.active {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-color: transparent;
        color: white;
    }

    .options-panel {
        background: #f9fafb;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
        animation: fadeIn 0.2s ease;
    }

    .dark .options-panel {
        background: #111827;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .btn-secondary {
        padding: 0.75rem 1.5rem;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .dark .btn-secondary {
        background: #374151;
        border-color: #4b5563;
        color: #d1d5db;
    }

    .btn-secondary:hover {
        background: #f3f4f6;
    }

    .dark .btn-secondary:hover {
        background: #4b5563;
    }

    .btn-primary {
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border: none;
        border-radius: 12px;
        font-weight: 600;
        color: white;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 113, 170, 0.3);
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
            <button onclick="showCreateModal()" class="add-btn">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                إضافة جلسة
            </button>
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
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Session Templates -->
                <select id="sessionTemplate" onchange="applyTemplate(this.value)"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer border-2 border-white/30 bg-white/20 text-white focus:outline-none focus:ring-2 focus:ring-white/50">
                    <option value="">اختر قالب...</option>
                    <option value="weekly_morning">أسبوعي صباحاً (10:00)</option>
                    <option value="weekly_afternoon">أسبوعي ظهراً (14:00)</option>
                    <option value="weekly_evening">أسبوعي مساءً (17:00)</option>
                    <option value="biweekly_morning">كل أسبوعين صباحاً (10:00)</option>
                    <option value="daily_morning">يومي صباحاً (10:00)</option>
                </select>

                <!-- Create Session Button -->
                <button onclick="showCreateModal()"
                        class="px-6 py-2 rounded-lg text-sm font-medium bg-white/20 hover:bg-white/30 text-white border-2 border-white/30 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    إضافة جلسة
                </button>
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
            <button onclick="showCreateModal()" class="add-btn">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                إضافة جلسة
            </button>
        </div>
        @endforelse

        @if($sessions->hasPages())
        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
            {{ $sessions->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Add Session Modal -->
<div class="modal-backdrop" id="sessionModal">
    <div class="modal-container">
        <!-- Modal Header -->
        <div class="modal-header">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 4h10a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm14 2.5l4-2v11l-4-2v-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">إضافة جلسة Zoom</h3>
                    <p class="text-white/70 text-sm" id="selectedDateText">اختر التاريخ</p>
                </div>
            </div>
            <button type="button" onclick="closeModal()" class="modal-close">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
            <input type="hidden" id="modal_scheduled_date">

            <!-- Subject -->
            <div class="form-group">
                <label class="form-label">المادة الدراسية <span class="text-red-500">*</span></label>
                <select id="modal_subject_id" class="form-select" required>
                    <option value="">اختر المادة</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Start Date & End Date -->
            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">تاريخ البدء <span class="text-red-500">*</span></label>
                    <input type="date" id="modal_start_date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الانتهاء <span class="text-red-500">*</span></label>
                    <input type="date" id="modal_end_date" class="form-input" required>
                </div>
            </div>

            <!-- Time & Duration -->
            <div class="grid grid-cols-3 gap-4">
                <div class="form-group">
                    <label class="form-label">وقت البدء <span class="text-red-500">*</span></label>
                    <input type="time" id="modal_start_time" class="form-input text-center" value="10:00" required onchange="calculateModalDuration()">
                </div>
                <div class="form-group">
                    <label class="form-label">وقت الانتهاء <span class="text-red-500">*</span></label>
                    <input type="time" id="modal_end_time" class="form-input text-center" value="11:00" required onchange="calculateModalDuration()">
                </div>
                <div class="form-group">
                    <label class="form-label">المدة (محسوبة تلقائياً)</label>
                    <div id="modal_calculated_duration" class="form-input bg-gray-100 text-center flex items-center justify-center font-bold text-emerald-600">60 دقيقة</div>
                    <input type="hidden" id="modal_duration" value="60">
                </div>
            </div>

            <!-- Recurrence -->
            <div class="form-group">
                <label class="form-label">التكرار</label>
                <div class="recurrence-toggle">
                    <button type="button" class="recurrence-option active" data-value="none">مرة واحدة</button>
                    <button type="button" class="recurrence-option" data-value="weekly">أسبوعي</button>
                    <button type="button" class="recurrence-option" data-value="monthly">شهري</button>
                </div>
            </div>

            <!-- Weekly Options -->
            <div id="weeklyOptions" class="options-panel" style="display: none;">
                <div class="form-group mb-4">
                    <label class="form-label">أيام التكرار</label>
                    <div class="day-selector">
                        <button type="button" class="day-btn" data-day="0">أحد</button>
                        <button type="button" class="day-btn" data-day="1">إثن</button>
                        <button type="button" class="day-btn" data-day="2">ثلا</button>
                        <button type="button" class="day-btn" data-day="3">أرب</button>
                        <button type="button" class="day-btn" data-day="4">خمي</button>
                        <button type="button" class="day-btn" data-day="5">جمع</button>
                        <button type="button" class="day-btn" data-day="6">سبت</button>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">عدد الأسابيع</label>
                    <select id="modal_weeks" class="form-select" style="max-width: 200px;">
                        @for($i = 2; $i <= 16; $i++)
                            <option value="{{ $i }}" {{ $i == 4 ? 'selected' : '' }}>{{ $i }} أسابيع</option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- Monthly Options -->
            <div id="monthlyOptions" class="options-panel" style="display: none;">
                <div class="form-group mb-0">
                    <label class="form-label">عدد الأشهر</label>
                    <select id="modal_months" class="form-select" style="max-width: 200px;">
                        @for($i = 2; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == 3 ? 'selected' : '' }}>{{ $i }} أشهر</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
            <button type="button" onclick="closeModal()" class="btn-secondary">إلغاء</button>
            <button type="button" onclick="createSession()" class="btn-primary">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                إنشاء الجلسة
            </button>
        </div>
    </div>
</div>

<!-- Hidden Form -->
<form id="batchForm" action="{{ route('admin.sessions.store-batch') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="sessions" id="batchSessionsInput">
</form>
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

        // Use subject color if available
        const subjectColor = session.subject?.color || '#0071AA';

        return {
            id: session.id,
            title: session.title_ar || session.title,
            start: session.scheduled_at,
            className: className,
            backgroundColor: subjectColor,
            borderColor: subjectColor,
            extendedProps: {
                subject: session.subject?.name || '',
                duration: session.duration_minutes,
                status: session.status,
                type: session.type,
                subjectColor: subjectColor
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
        editable: true, // Enable drag-and-drop
        selectable: true,
        events: events,
        dateClick: function(info) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (info.date < today) return;
            openModal(info.dateStr);
        },
        eventClick: function(info) {
            // Only navigate if not dragging
            if (!info.jsEvent.defaultPrevented) {
                window.location.href = '/admin/sessions/' + info.event.id;
            }
        },
        eventDrop: function(info) {
            // Handle session rescheduling
            const sessionId = info.event.id;
            const newDate = info.event.start;

            if (confirm(`هل تريد إعادة جدولة "${info.event.title}" إلى ${newDate.toLocaleString('ar-SA', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}؟`)) {
                // Send AJAX request to update session date
                fetch(`/admin/sessions/${sessionId}/reschedule`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        scheduled_at: newDate.toISOString()
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showNotification('تم إعادة جدولة الجلسة بنجاح', 'success');
                    } else {
                        info.revert(); // Revert if failed
                        showNotification('فشل إعادة جدولة الجلسة', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    info.revert(); // Revert on error
                    showNotification('حدث خطأ أثناء إعادة جدولة الجلسة', 'error');
                });
            } else {
                info.revert(); // Revert if cancelled
            }
        },
        eventDidMount: function(info) {
            // Add tooltip
            info.el.title = info.event.extendedProps.subject + ' - ' + (info.event.extendedProps.duration || 60) + ' دقيقة';
        }
    });
    calendar.render();

    // Simple notification function
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 left-1/2 transform -translate-x-1/2 px-6 py-3 rounded-lg shadow-lg z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white font-medium`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

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

    // Modal Variables
    let selectedDate = null;
    let currentRecurrence = 'none';
    let selectedDays = [];

    // Check for subject_id in URL and auto-open modal
    const urlParams = new URLSearchParams(window.location.search);
    const subjectId = urlParams.get('subject_id');
    if (subjectId) {
        // Pre-select subject and open modal
        setTimeout(() => {
            document.getElementById('modal_subject_id').value = subjectId;
            showCreateModal();
        }, 500);
    }

    // Show modal function
    window.showCreateModal = function() {
        const today = new Date().toISOString().split('T')[0];
        openModal(today);
    };

    // Calculate duration from start and end time
    window.calculateModalDuration = function() {
        const startTime = document.getElementById('modal_start_time').value;
        const endTime = document.getElementById('modal_end_time').value;
        const durationDisplay = document.getElementById('modal_calculated_duration');
        const durationInput = document.getElementById('modal_duration');

        if (startTime && endTime) {
            const [startHour, startMin] = startTime.split(':').map(Number);
            const [endHour, endMin] = endTime.split(':').map(Number);

            let totalMinutes = (endHour * 60 + endMin) - (startHour * 60 + startMin);

            if (totalMinutes < 0) {
                durationDisplay.textContent = 'وقت الانتهاء يجب أن يكون بعد وقت البدء';
                durationDisplay.classList.remove('text-emerald-600');
                durationDisplay.classList.add('text-red-500');
                durationInput.value = '';
                return;
            }

            durationDisplay.classList.remove('text-red-500');
            durationDisplay.classList.add('text-emerald-600');

            if (totalMinutes >= 60) {
                const hours = Math.floor(totalMinutes / 60);
                const mins = totalMinutes % 60;
                if (mins > 0) {
                    durationDisplay.textContent = hours + ' ساعة و ' + mins + ' دقيقة';
                } else {
                    durationDisplay.textContent = hours + (hours === 1 ? ' ساعة' : (hours === 2 ? ' ساعتان' : ' ساعات'));
                }
            } else {
                durationDisplay.textContent = totalMinutes + ' دقيقة';
            }

            durationInput.value = totalMinutes;
        }
    };

    // Modal Functions
    window.openModal = function(dateStr) {
        selectedDate = dateStr;
        document.getElementById('modal_scheduled_date').value = dateStr;
        document.getElementById('modal_start_date').value = dateStr;
        document.getElementById('modal_end_date').value = dateStr;

        const dateObj = new Date(dateStr);
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('selectedDateText').textContent = dateObj.toLocaleDateString('ar-SA', options);

        // Auto-select the day
        const dayOfWeek = dateObj.getDay();
        selectedDays = [dayOfWeek];
        updateDayButtons();

        // Calculate initial duration
        calculateModalDuration();

        document.getElementById('sessionModal').classList.add('active');
    };

    window.closeModal = function() {
        document.getElementById('sessionModal').classList.remove('active');
        resetModal();
    };

    function resetModal() {
        document.getElementById('modal_subject_id').value = '';
        document.getElementById('modal_start_time').value = '10:00';
        document.getElementById('modal_end_time').value = '11:00';
        document.getElementById('modal_duration').value = '60';
        document.getElementById('modal_calculated_duration').textContent = '60 دقيقة';
        document.getElementById('modal_calculated_duration').classList.remove('text-red-500');
        document.getElementById('modal_calculated_duration').classList.add('text-emerald-600');
        document.getElementById('modal_start_date').value = '';
        document.getElementById('modal_end_date').value = '';
        currentRecurrence = 'none';
        selectedDays = [];

        // Reset recurrence buttons
        document.querySelectorAll('.recurrence-option').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.value === 'none') btn.classList.add('active');
        });

        // Hide options panels
        document.getElementById('weeklyOptions').style.display = 'none';
        document.getElementById('monthlyOptions').style.display = 'none';

        // Reset day buttons
        document.querySelectorAll('.day-btn').forEach(btn => btn.classList.remove('active'));
    }

    // Recurrence handlers
    document.querySelectorAll('.recurrence-option').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.recurrence-option').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            currentRecurrence = this.dataset.value;

            document.getElementById('weeklyOptions').style.display = currentRecurrence === 'weekly' ? 'block' : 'none';
            document.getElementById('monthlyOptions').style.display = currentRecurrence === 'monthly' ? 'block' : 'none';
        });
    });

    // Day selector handlers
    document.querySelectorAll('.day-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const day = parseInt(this.dataset.day);

            if (selectedDays.includes(day)) {
                selectedDays = selectedDays.filter(d => d !== day);
                this.classList.remove('active');
            } else {
                selectedDays.push(day);
                this.classList.add('active');
            }
        });
    });

    function updateDayButtons() {
        document.querySelectorAll('.day-btn').forEach(btn => {
            const day = parseInt(btn.dataset.day);
            if (selectedDays.includes(day)) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    // Create session
    window.createSession = function() {
        const subjectId = document.getElementById('modal_subject_id').value;
        const startDate = document.getElementById('modal_start_date').value;
        const endDate = document.getElementById('modal_end_date').value;
        const startTime = document.getElementById('modal_start_time').value;
        const endTime = document.getElementById('modal_end_time').value;
        const duration = parseInt(document.getElementById('modal_duration').value);

        if (!subjectId) {
            alert('الرجاء اختيار المادة');
            return;
        }

        if (!startDate || !endDate) {
            alert('الرجاء تحديد تاريخ البدء وتاريخ الانتهاء');
            return;
        }

        if (new Date(endDate) < new Date(startDate)) {
            alert('تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء');
            return;
        }

        if (!duration || duration <= 0) {
            alert('وقت الانتهاء يجب أن يكون بعد وقت البدء');
            return;
        }

        const dates = generateSessions(startDate, currentRecurrence);
        const sessions = [];

        // Get subject info for calendar display
        const subjectSelect = document.getElementById('modal_subject_id');
        const subjectText = subjectSelect.options[subjectSelect.selectedIndex].text;
        const subjects = @json($subjects);
        const selectedSubject = subjects.find(s => s.id == subjectId);

        dates.forEach((date, index) => {
            const datetime = date + ' ' + startTime;

            sessions.push({
                subject_id: parseInt(subjectId),
                title_ar: `جلسة ${index + 1}`,
                title_en: `Session ${index + 1}`,
                scheduled_at: datetime,
                start_date: startDate,
                end_date: endDate,
                start_time: startTime,
                end_time: endTime,
                duration_minutes: duration,
                type: 'live_zoom'
            });
        });

        // Use AJAX to submit the form
        console.log('Sending sessions:', sessions);

        fetch('{{ route('admin.sessions.store-batch') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ sessions: sessions })
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                // Add new sessions to calendar
                if (data.sessions) {
                    data.sessions.forEach(session => {
                        let className = 'event-zoom';
                        const subjectColor = selectedSubject?.color || '#0071AA';

                        calendar.addEvent({
                            id: session.id,
                            title: session.title_ar || session.title,
                            start: session.scheduled_at,
                            className: className,
                            backgroundColor: subjectColor,
                            borderColor: subjectColor,
                            extendedProps: {
                                subject: selectedSubject?.name || '',
                                duration: session.duration_minutes,
                                status: session.status,
                                type: session.type,
                                subjectColor: subjectColor
                            }
                        });
                    });
                }

                showNotification(data.message || 'تم إنشاء الجلسات بنجاح', 'success');
                closeModal();

                // Reload page after a short delay to update stats
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showNotification(data.message || 'فشل إنشاء الجلسات', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('حدث خطأ أثناء إنشاء الجلسات', 'error');
        });
    };

    function generateSessions(baseDate, recurrenceType) {
        const sessions = [];
        const date = new Date(baseDate);

        if (recurrenceType === 'none') {
            sessions.push(baseDate);
        } else if (recurrenceType === 'weekly') {
            const weeks = parseInt(document.getElementById('modal_weeks').value) || 4;
            const days = selectedDays.length > 0 ? selectedDays : [date.getDay()];

            for (let w = 0; w < weeks; w++) {
                days.forEach(day => {
                    const sessionDate = new Date(date);
                    const currentDay = sessionDate.getDay();
                    let daysUntil = day - currentDay;
                    if (daysUntil < 0) daysUntil += 7;
                    sessionDate.setDate(sessionDate.getDate() + daysUntil + (w * 7));

                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    if (sessionDate >= today) {
                        sessions.push(sessionDate.toISOString().split('T')[0]);
                    }
                });
            }
        } else if (recurrenceType === 'monthly') {
            const months = parseInt(document.getElementById('modal_months').value) || 3;

            for (let m = 0; m < months; m++) {
                const sessionDate = new Date(date);
                sessionDate.setMonth(sessionDate.getMonth() + m);
                sessions.push(sessionDate.toISOString().split('T')[0]);
            }
        }

        return [...new Set(sessions)].sort();
    }

    // Apply session template
    window.applyTemplate = function(templateValue) {
        if (!templateValue) return;

        const subject = document.getElementById('modal_subject_id').value;
        if (!subject) {
            alert('الرجاء اختيار المادة أولاً');
            document.getElementById('sessionTemplate').value = '';
            return;
        }

        // Template configurations
        const templates = {
            'weekly_morning': {
                time: '10:00',
                recurrence: 'weekly',
                weeks: 8,
                days: [0, 2, 4]
            },
            'weekly_afternoon': {
                time: '14:00',
                recurrence: 'weekly',
                weeks: 8,
                days: [1, 3]
            },
            'weekly_evening': {
                time: '17:00',
                recurrence: 'weekly',
                weeks: 8,
                days: [0, 2]
            },
            'biweekly_morning': {
                time: '10:00',
                recurrence: 'weekly',
                weeks: 8,
                days: [0]
            },
            'daily_morning': {
                time: '10:00',
                recurrence: 'weekly',
                weeks: 4,
                days: [0, 1, 2, 3, 4]
            }
        };

        const template = templates[templateValue];
        if (!template) return;

        // Set time
        document.getElementById('modal_start_time').value = template.time;

        // Set recurrence
        currentRecurrence = template.recurrence;
        selectedDays = template.days;

        if (template.recurrence === 'weekly') {
            document.getElementById('modal_weeks').value = template.weeks;
        }

        // Update UI
        document.querySelectorAll('.recurrence-option').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.value === template.recurrence) btn.classList.add('active');
        });

        document.getElementById('weeklyOptions').style.display = template.recurrence === 'weekly' ? 'block' : 'none';
        updateDayButtons();

        showNotification('تم تطبيق القالب بنجاح! الرجاء النقر على التاريخ المطلوب', 'success');

        // Reset template selection
        document.getElementById('sessionTemplate').value = '';
    };

    // Keyboard handlers
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    document.getElementById('sessionModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
});
</script>
@endpush
