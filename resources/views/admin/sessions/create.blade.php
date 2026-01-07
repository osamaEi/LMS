@extends('layouts.dashboard')

@section('title', 'جدولة جلسات Zoom')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    :root {
        --primary: #0071AA;
        --primary-dark: #005a88;
        --primary-light: #e6f4fa;
    }

    /* Main Container */
    .scheduler-container {
        min-height: calc(100vh - 200px);
    }

    /* Top Stats Bar */
    .stats-bar {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        transition: all 0.3s ease;
        border: 1px solid #f3f4f6;
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
        font-size: 0.875rem;
        color: #6b7280;
    }

    /* Calendar Container */
    .calendar-wrapper {
        background: white;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        border: 1px solid #f3f4f6;
    }

    .dark .calendar-wrapper {
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

    .calendar-title-section {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .calendar-icon-box {
        width: 56px;
        height: 56px;
        background: rgba(255,255,255,0.2);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
    }

    .calendar-body {
        padding: 1.5rem;
    }

    /* FullCalendar Overrides */
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

    .fc .fc-button-group {
        gap: 4px;
    }

    .fc .fc-button {
        background: white !important;
        color: #374151 !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 10px !important;
        padding: 10px 16px !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
        text-transform: none !important;
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

    .fc .fc-button-primary:not(:disabled).fc-button-active,
    .fc .fc-button-primary:not(:disabled):active {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%) !important;
        color: white !important;
        border-color: var(--primary) !important;
    }

    .fc .fc-col-header-cell {
        background: #f9fafb !important;
        padding: 14px 0 !important;
        font-weight: 600;
    }

    .dark .fc .fc-col-header-cell {
        background: #111827 !important;
    }

    .fc .fc-col-header-cell-cushion {
        color: #374151 !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        font-size: 0.875rem;
    }

    .dark .fc .fc-col-header-cell-cushion {
        color: #d1d5db !important;
    }

    .fc .fc-daygrid-day {
        transition: all 0.2s ease !important;
        cursor: pointer !important;
        min-height: 110px !important;
    }

    .fc .fc-daygrid-day:hover {
        background: var(--primary-light) !important;
    }

    .dark .fc .fc-daygrid-day:hover {
        background: rgba(0, 113, 170, 0.15) !important;
    }

    .fc .fc-daygrid-day.fc-day-today {
        background: rgba(0, 113, 170, 0.08) !important;
    }

    .fc .fc-daygrid-day-number {
        font-weight: 600 !important;
        padding: 10px !important;
        color: #374151 !important;
        font-size: 0.9rem;
    }

    .dark .fc .fc-daygrid-day-number {
        color: #d1d5db !important;
    }

    .fc .fc-day-today .fc-daygrid-day-number {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%) !important;
        color: white !important;
        border-radius: 10px !important;
        width: 36px !important;
        height: 36px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .fc .fc-daygrid-day-frame {
        min-height: 100% !important;
    }

    .fc .fc-daygrid-day-events {
        padding: 0 8px 8px !important;
    }

    .fc .fc-event {
        border-radius: 8px !important;
        border: none !important;
        padding: 4px 8px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        margin-bottom: 4px !important;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%) !important;
        transition: all 0.2s ease !important;
    }

    .fc .fc-event:hover {
        transform: scale(1.02) !important;
        box-shadow: 0 4px 12px rgba(0, 113, 170, 0.3) !important;
    }

    .fc .fc-event-title {
        font-weight: 600 !important;
    }

    .fc .fc-day-past {
        opacity: 0.5;
    }

    .fc .fc-day-past:hover {
        background: transparent !important;
        cursor: not-allowed !important;
    }

    /* Sidebar Panels */
    .sidebar-panel {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        border: 1px solid #f3f4f6;
    }

    .dark .sidebar-panel {
        background: #1f2937;
        border-color: #374151;
    }

    .panel-header {
        padding: 1.25rem;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .dark .panel-header {
        border-color: #374151;
    }

    .panel-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .panel-body {
        padding: 1.25rem;
    }

    /* Session List Item */
    .session-list-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem;
        background: #f9fafb;
        border-radius: 12px;
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }

    .dark .session-list-item {
        background: #111827;
    }

    .session-list-item:hover {
        border-color: var(--primary);
        box-shadow: 0 4px 12px rgba(0,113,170,0.1);
    }

    .session-number {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .session-info {
        flex: 1;
        min-width: 0;
    }

    .session-title {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.875rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .dark .session-title {
        color: #f9fafb;
    }

    .session-meta {
        font-size: 0.75rem;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .session-delete {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .session-delete:hover {
        background: #fef2f2;
        color: #ef4444;
    }

    .dark .session-delete:hover {
        background: rgba(239, 68, 68, 0.2);
    }

    /* Quick Tips */
    .tip-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.75rem 0;
    }

    .tip-number {
        width: 24px;
        height: 24px;
        background: var(--primary-light);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-weight: 700;
        font-size: 0.75rem;
        flex-shrink: 0;
    }

    .dark .tip-number {
        background: rgba(0, 113, 170, 0.2);
    }

    .tip-text {
        font-size: 0.875rem;
        color: #4b5563;
        line-height: 1.5;
    }

    .dark .tip-text {
        color: #9ca3af;
    }

    /* Feature Badge */
    .feature-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        background: #f0fdf4;
        border-radius: 8px;
        font-size: 0.75rem;
        color: #15803d;
        font-weight: 500;
    }

    .dark .feature-badge {
        background: rgba(34, 197, 94, 0.1);
        color: #4ade80;
    }

    /* Submit Button */
    .submit-btn {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        border: none;
        border-radius: 14px;
        font-weight: 700;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 113, 170, 0.3);
    }

    .submit-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 113, 170, 0.4);
    }

    .submit-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Modal */
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

    /* Form Elements */
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

    /* Recurrence Toggle */
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

    /* Day Selector */
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

    /* Options Panel */
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

    /* Action Buttons */
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

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 2rem 1rem;
        color: #9ca3af;
    }

    .empty-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        background: #f3f4f6;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dark .empty-icon {
        background: #111827;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .calendar-header {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>
@endpush

@section('content')
<div class="scheduler-container space-y-6">
    <!-- Stats Bar -->
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="stat-value" id="totalSessionsCount">0</div>
                <div class="stat-label">جلسات مضافة</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-value" id="totalHoursCount">0</div>
                <div class="stat-label">ساعة تعليمية</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M4 4h10a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm14 2.5l4-2v11l-4-2v-7z"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">Zoom</div>
                <div class="stat-label">جلسات مباشرة</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <div class="stat-value">تلقائي</div>
                <div class="stat-label">إنشاء الروابط</div>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-red-700 dark:text-red-300 mb-1">يرجى تصحيح الأخطاء:</p>
                <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Calendar Section -->
        <div class="xl:col-span-3">
            <div class="calendar-wrapper">
                <div class="calendar-header">
                    <div class="calendar-title-section">
                        <a href="{{ route('admin.sessions.index') }}" class="calendar-icon-box hover:bg-white/30 transition-all">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-white">جدولة جلسات Zoom</h1>
                            <p class="text-white/70 text-sm mt-1">انقر على أي يوم لإضافة جلسة جديدة</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="feature-badge" style="background: rgba(255,255,255,0.2); color: white;">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            إنشاء Zoom تلقائي
                        </div>
                    </div>
                </div>
                <div class="calendar-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Tips -->
            <div class="sidebar-panel">
                <div class="panel-header">
                    <div class="panel-icon" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">طريقة الاستخدام</h3>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="tip-item">
                        <div class="tip-number">1</div>
                        <div class="tip-text">انقر على اليوم المطلوب في التقويم</div>
                    </div>
                    <div class="tip-item">
                        <div class="tip-number">2</div>
                        <div class="tip-text">أدخل بيانات الجلسة (المادة، العنوان، الوقت)</div>
                    </div>
                    <div class="tip-item">
                        <div class="tip-number">3</div>
                        <div class="tip-text">اختر التكرار أسبوعي أو شهري (اختياري)</div>
                    </div>
                    <div class="tip-item">
                        <div class="tip-number">4</div>
                        <div class="tip-text">اضغط "حفظ الجلسات" لإنشائها</div>
                    </div>
                </div>
            </div>

            <!-- Pending Sessions -->
            <div class="sidebar-panel">
                <div class="panel-header">
                    <div class="panel-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900 dark:text-white">الجلسات المضافة</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400" id="pendingCountLabel">0 جلسات</p>
                    </div>
                </div>
                <div class="panel-body">
                    <div id="pendingSessionsList">
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-sm">انقر على التقويم لإضافة جلسات</p>
                        </div>
                    </div>

                    <button type="button" id="submitAllBtn" class="submit-btn mt-4" disabled>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        حفظ جميع الجلسات
                    </button>
                </div>
            </div>

            <!-- Zoom Features -->
            <div class="sidebar-panel">
                <div class="panel-header">
                    <div class="panel-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M4 4h10a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm14 2.5l4-2v11l-4-2v-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">مميزات Zoom</h3>
                    </div>
                </div>
                <div class="panel-body space-y-2">
                    <div class="feature-badge">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        إنشاء رابط تلقائي
                    </div>
                    <div class="feature-badge">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        تسجيل سحابي
                    </div>
                    <div class="feature-badge">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        محادثة مباشرة
                    </div>
                </div>
            </div>
        </div>
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

            <!-- Time & Duration -->
            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">وقت البدء <span class="text-red-500">*</span></label>
                    <input type="time" id="modal_start_time" class="form-input text-center" value="10:00" required>
                </div>
                <div class="form-group">
                    <label class="form-label">المدة</label>
                    <select id="modal_duration" class="form-select">
                        <option value="30">30 دقيقة</option>
                        <option value="45">45 دقيقة</option>
                        <option value="60" selected>60 دقيقة</option>
                        <option value="90">90 دقيقة</option>
                        <option value="120">ساعتان</option>
                    </select>
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
            <button type="button" onclick="addSessionToCalendar()" class="btn-primary">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                إضافة للتقويم
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
    let calendar;
    let selectedDate = null;
    let pendingSessions = [];
    let selectedDays = [];
    let currentRecurrence = 'none';

    // Initialize Calendar
    const calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'ar',
        direction: 'rtl',
        headerToolbar: {
            right: 'prev,next today',
            center: 'title',
            left: 'dayGridMonth,dayGridWeek'
        },
        height: 'auto',
        selectable: true,
        dayMaxEvents: 3,
        events: [],
        dateClick: function(info) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (info.date < today) return;
            openModal(info.dateStr);
        },
        eventClick: function(info) {
            const eventId = info.event.id;
            pendingSessions = pendingSessions.filter(s => s.id !== eventId);
            info.event.remove();
            updateUI();
        }
    });
    calendar.render();

    // Modal Functions
    window.openModal = function(dateStr) {
        selectedDate = dateStr;
        document.getElementById('modal_scheduled_date').value = dateStr;

        const dateObj = new Date(dateStr);
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('selectedDateText').textContent = dateObj.toLocaleDateString('ar-SA', options);

        // Auto-select the day
        const dayOfWeek = dateObj.getDay();
        selectedDays = [dayOfWeek];
        updateDayButtons();

        document.getElementById('sessionModal').classList.add('active');
    };

    window.closeModal = function() {
        document.getElementById('sessionModal').classList.remove('active');
        resetModal();
    };

    function resetModal() {
        document.getElementById('modal_subject_id').value = '';
        document.getElementById('modal_start_time').value = '10:00';
        document.getElementById('modal_duration').value = '60';
        currentRecurrence = 'none';
        selectedDays = [];

        document.querySelectorAll('.recurrence-option').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.value === 'none');
        });

        document.getElementById('weeklyOptions').style.display = 'none';
        document.getElementById('monthlyOptions').style.display = 'none';
        updateDayButtons();
    }

    // Recurrence Toggle
    document.querySelectorAll('.recurrence-option').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.recurrence-option').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentRecurrence = this.dataset.value;

            document.getElementById('weeklyOptions').style.display = currentRecurrence === 'weekly' ? 'block' : 'none';
            document.getElementById('monthlyOptions').style.display = currentRecurrence === 'monthly' ? 'block' : 'none';
        });
    });

    // Day Buttons
    document.querySelectorAll('.day-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const day = parseInt(this.dataset.day);
            if (selectedDays.includes(day)) {
                selectedDays = selectedDays.filter(d => d !== day);
            } else {
                selectedDays.push(day);
            }
            updateDayButtons();
        });
    });

    function updateDayButtons() {
        document.querySelectorAll('.day-btn').forEach(btn => {
            const day = parseInt(btn.dataset.day);
            btn.classList.toggle('active', selectedDays.includes(day));
        });
    }

    // Add Session
    window.addSessionToCalendar = function() {
        const subjectId = document.getElementById('modal_subject_id').value;
        const startTime = document.getElementById('modal_start_time').value;
        const duration = document.getElementById('modal_duration').value;

        if (!subjectId || !startTime) {
            alert('يرجى اختيار المادة ووقت البدء');
            return;
        }

        const subjectSelect = document.getElementById('modal_subject_id');
        const subjectName = subjectSelect.options[subjectSelect.selectedIndex].text;

        // Auto-generate session number based on existing sessions for this subject
        const existingSessionsForSubject = pendingSessions.filter(s => s.subject_id === subjectId).length;
        const sessionNumber = existingSessionsForSubject + 1;

        const sessions = generateSessions(selectedDate, currentRecurrence);

        sessions.forEach((sessionDate, index) => {
            const id = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            const dateTime = sessionDate + 'T' + startTime;
            const currentSessionNumber = sessionNumber + index;

            // Auto-generate title from subject name and session number
            const dateObj = new Date(sessionDate);
            const titleAr = `جلسة ${currentSessionNumber} - ${subjectName.split('(')[0].trim()}`;
            const titleEn = `Session ${currentSessionNumber} - ${subjectName.split('(')[0].trim()}`;

            calendar.addEvent({
                id: id,
                title: titleAr,
                start: dateTime,
                extendedProps: {
                    subjectId: subjectId,
                    subjectName: subjectName,
                    titleAr: titleAr,
                    titleEn: titleEn,
                    duration: duration,
                    sessionNumber: currentSessionNumber
                }
            });

            pendingSessions.push({
                id: id,
                subject_id: subjectId,
                subjectName: subjectName,
                title_ar: titleAr,
                title_en: titleEn,
                scheduled_at: dateTime,
                duration_minutes: parseInt(duration),
                session_number: currentSessionNumber,
                type: 'live_zoom'
            });
        });

        updateUI();
        closeModal();
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

    function updateUI() {
        // Update stats
        document.getElementById('totalSessionsCount').textContent = pendingSessions.length;
        const totalMinutes = pendingSessions.reduce((sum, s) => sum + s.duration_minutes, 0);
        document.getElementById('totalHoursCount').textContent = Math.round(totalMinutes / 60 * 10) / 10;

        // Update pending count
        document.getElementById('pendingCountLabel').textContent = pendingSessions.length + ' جلسات';

        // Update submit button
        document.getElementById('submitAllBtn').disabled = pendingSessions.length === 0;

        // Update sessions list
        const container = document.getElementById('pendingSessionsList');

        if (pendingSessions.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm">انقر على التقويم لإضافة جلسات</p>
                </div>
            `;
            return;
        }

        let html = '<div style="max-height: 300px; overflow-y: auto;">';
        pendingSessions.slice(0, 10).forEach((session, index) => {
            const dateObj = new Date(session.scheduled_at);
            const dateStr = dateObj.toLocaleDateString('ar-SA', { weekday: 'short', month: 'short', day: 'numeric' });
            const timeStr = dateObj.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit' });

            html += `
                <div class="session-list-item">
                    <div class="session-number">${index + 1}</div>
                    <div class="session-info">
                        <div class="session-title">${session.title_ar}</div>
                        <div class="session-meta">
                            <span>${dateStr}</span>
                            <span>•</span>
                            <span>${timeStr}</span>
                        </div>
                    </div>
                    <button type="button" onclick="removeSession('${session.id}')" class="session-delete">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;
        });

        if (pendingSessions.length > 10) {
            html += `<p class="text-center text-sm text-gray-500 py-2">و ${pendingSessions.length - 10} جلسات أخرى...</p>`;
        }
        html += '</div>';

        container.innerHTML = html;
    }

    window.removeSession = function(id) {
        pendingSessions = pendingSessions.filter(s => s.id !== id);
        const event = calendar.getEventById(id);
        if (event) event.remove();
        updateUI();
    };

    // Submit All
    document.getElementById('submitAllBtn').addEventListener('click', function() {
        if (pendingSessions.length === 0) return;

        this.disabled = true;
        this.innerHTML = `
            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            جاري الحفظ...
        `;

        document.getElementById('batchSessionsInput').value = JSON.stringify(pendingSessions);
        document.getElementById('batchForm').submit();
    });

    // Keyboard & Click handlers
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    document.getElementById('sessionModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
});
</script>
@endpush
