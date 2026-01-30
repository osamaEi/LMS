@extends('layouts.dashboard')

@section('title', 'الجدول الدراسي')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<style>
    /* ==================== Modern Calendar Styles ==================== */
    .calendar-wrapper {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0, 113, 170, 0.1), 0 8px 25px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        border: 1px solid rgba(0, 113, 170, 0.08);
    }

    /* Calendar Container */
    .fc {
        direction: rtl;
        font-family: 'Cairo', sans-serif;
    }

    /* Header Toolbar */
    .fc-toolbar {
        padding: 1.5rem 2rem !important;
        margin-bottom: 0 !important;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .fc-toolbar-title {
        font-size: 1.75rem !important;
        font-weight: 800 !important;
        color: #1e293b !important;
        letter-spacing: -0.5px;
    }

    /* Navigation Buttons - Inactive */
    .fc .fc-button {
        background: white !important;
        border: 2px solid #e2e8f0 !important;
        color: #475569 !important;
        padding: 0.6rem 1.2rem;
        font-size: 0.875rem;
        border-radius: 12px !important;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .fc .fc-button:hover {
        background: #f1f5f9 !important;
        color: #0071AA !important;
        border-color: #0071AA !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    /* Active View Button */
    .fc .fc-button-active,
    .fc .fc-button-primary:not(:disabled).fc-button-active {
        background: #0071AA !important;
        color: white !important;
        border-color: #0071AA !important;
        box-shadow: 0 4px 15px rgba(0, 113, 170, 0.4);
        font-weight: 700;
    }

    .fc .fc-button-active:hover,
    .fc .fc-button-primary:not(:disabled).fc-button-active:hover {
        background: #005a88 !important;
        border-color: #005a88 !important;
    }

    .fc .fc-today-button {
        background: linear-gradient(135deg, #10b981, #059669) !important;
        border: none !important;
        color: white !important;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
    }

    .fc .fc-today-button:hover {
        background: linear-gradient(135deg, #34d399, #10b981) !important;
        color: white !important;
        transform: translateY(-2px);
    }

    .fc .fc-today-button:disabled {
        background: #e2e8f0 !important;
        color: #94a3b8 !important;
        opacity: 0.6;
        box-shadow: none;
    }

    .fc .fc-prev-button,
    .fc .fc-next-button {
        background: white !important;
        border: 2px solid #e2e8f0 !important;
        color: #0071AA !important;
        width: 42px;
        height: 42px;
        border-radius: 12px !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .fc .fc-prev-button:hover,
    .fc .fc-next-button:hover {
        background: #0071AA !important;
        border-color: #0071AA !important;
        color: white !important;
        transform: translateY(-2px) scale(1.05);
    }

    /* Column Headers */
    .fc-col-header {
        background: linear-gradient(180deg, #f1f5f9 0%, #e2e8f0 100%);
    }

    .fc-col-header-cell {
        padding: 1rem 0 !important;
        font-weight: 700;
        font-size: 0.9rem;
        color: #0071AA;
        border-color: #e2e8f0 !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Day Cells */
    .fc-daygrid-day {
        min-height: 120px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .fc-daygrid-day:hover {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        z-index: 1;
    }

    .fc-daygrid-day:hover::after {
        content: '+';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 2rem;
        color: #0071AA;
        opacity: 0.3;
        font-weight: 300;
        pointer-events: none;
    }

    .fc-daygrid-day-number {
        font-weight: 700;
        font-size: 1rem;
        color: #475569;
        padding: 8px 12px !important;
        border-radius: 12px;
        margin: 4px;
        transition: all 0.3s ease;
    }

    .fc-daygrid-day:hover .fc-daygrid-day-number {
        background: linear-gradient(135deg, #0071AA, #005a88);
        color: white !important;
        transform: scale(1.1);
        box-shadow: 0 4px 15px rgba(0, 113, 170, 0.3);
    }

    /* Today */
    .fc-day-today {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 50%, #93c5fd 100%) !important;
    }

    .fc-day-today .fc-daygrid-day-number {
        background: linear-gradient(135deg, #0071AA, #005a88);
        color: white !important;
        box-shadow: 0 4px 15px rgba(0, 113, 170, 0.4);
        animation: pulse-today 2s infinite;
    }

    @keyframes pulse-today {
        0%, 100% { box-shadow: 0 4px 15px rgba(0, 113, 170, 0.4); }
        50% { box-shadow: 0 4px 25px rgba(0, 113, 170, 0.6); }
    }

    /* Events */
    .fc-event {
        border-radius: 10px !important;
        padding: 6px 10px !important;
        margin: 3px 6px !important;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none !important;
        font-size: 0.8rem;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
    }

    .fc-event::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(180deg, rgba(255,255,255,0.2) 0%, transparent 50%);
        pointer-events: none;
    }

    .fc-event:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        z-index: 10;
    }

    /* Grid Borders */
    .fc-scrollgrid {
        border: none !important;
    }

    .fc-scrollgrid td,
    .fc-scrollgrid th {
        border-color: #e2e8f0 !important;
    }

    /* Other Days */
    .fc-day-other {
        background: #f8fafc;
    }

    .fc-day-other .fc-daygrid-day-number {
        color: #cbd5e1;
    }

    /* More Link */
    .fc-daygrid-more-link {
        color: #0071AA;
        font-weight: 700;
        font-size: 0.75rem;
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        padding: 4px 8px;
        border-radius: 8px;
        margin: 2px 4px;
    }

    /* ==================== Stats Cards ==================== */
    .stat-card {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        padding: 1.5rem;
        color: white;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 50px rgba(0,0,0,0.2);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -30%;
        width: 100%;
        height: 200%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.15) 0%, transparent 60%);
        pointer-events: none;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        bottom: -20px;
        left: -20px;
        width: 100px;
        height: 100px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    /* ==================== Modal Styles ==================== */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-container {
        background: white;
        border-radius: 28px;
        max-width: 480px;
        width: 95%;
        max-height: 90vh;
        overflow: hidden;
        transform: scale(0.9) translateY(30px);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
    }

    .modal-overlay.active .modal-container {
        transform: scale(1) translateY(0);
    }

    .modal-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #004266 100%);
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }

    .modal-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 80%;
        height: 200%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.1) 0%, transparent 60%);
    }

    .modal-header-icon {
        width: 64px;
        height: 64px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: white;
        margin-bottom: 0.25rem;
    }

    .modal-subtitle {
        color: rgba(255,255,255,0.8);
        font-size: 0.9rem;
    }

    .modal-close {
        position: absolute;
        top: 1.5rem;
        left: 1.5rem;
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.15);
        border: none;
        border-radius: 12px;
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-close:hover {
        background: rgba(255,255,255,0.25);
        transform: rotate(90deg);
    }

    .modal-body {
        padding: 2rem;
        max-height: 50vh;
        overflow-y: auto;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }

    .form-label-icon {
        width: 28px;
        height: 28px;
        background: linear-gradient(135deg, #0071AA, #005a88);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .form-control {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        font-size: 1rem;
        font-family: 'Cairo', sans-serif;
        transition: all 0.3s ease;
        background: #f8fafc;
    }

    .form-control:focus {
        outline: none;
        border-color: #0071AA;
        background: white;
        box-shadow: 0 0 0 4px rgba(0, 113, 170, 0.1);
    }

    .form-control option {
        padding: 0.75rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .modal-footer {
        padding: 1.5rem 2rem 2rem;
        display: flex;
        gap: 1rem;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }

    .btn {
        flex: 1;
        padding: 1rem 1.5rem;
        border-radius: 14px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(16, 185, 129, 0.4);
    }

    .btn-secondary {
        background: #e2e8f0;
        color: #475569;
    }

    .btn-secondary:hover {
        background: #cbd5e1;
        transform: translateY(-2px);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
    }

    .btn-danger:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(239, 68, 68, 0.4);
    }

    .btn-edit {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
    }

    .btn-edit:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(245, 158, 11, 0.4);
    }

    /* Session Detail Items */
    .session-detail {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f8fafc;
        border-radius: 14px;
        margin-bottom: 0.75rem;
    }

    .session-detail-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }

    .session-detail-content {
        flex: 1;
    }

    .session-detail-label {
        font-size: 0.8rem;
        color: #64748b;
        margin-bottom: 0.25rem;
    }

    .session-detail-value {
        font-weight: 700;
        color: #1e293b;
        font-size: 1rem;
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .status-badge::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
        animation: pulse-dot 2s infinite;
    }

    @keyframes pulse-dot {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.5); opacity: 0.5; }
    }

    /* Quick Actions Card */
    .quick-actions-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 113, 170, 0.08);
    }

    .quick-action-btn {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.25rem;
        border-radius: 14px;
        font-weight: 700;
        color: white;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin-bottom: 0.75rem;
        border: none;
        cursor: pointer;
        font-size: 0.95rem;
    }

    .quick-action-btn:last-child {
        margin-bottom: 0;
    }

    .quick-action-btn:hover {
        transform: translateX(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .quick-action-icon {
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Legend Card */
    .legend-card {
        background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
        border-radius: 20px;
        padding: 1.5rem;
        color: white;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 0;
    }

    .legend-dot {
        width: 14px;
        height: 14px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }

    .legend-text {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #004266 100%);
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
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

    /* Dark mode */
    .dark .calendar-wrapper {
        background: linear-gradient(145deg, #1e293b 0%, #0f172a 100%);
        border-color: rgba(255,255,255,0.1);
    }

    .dark .fc-col-header {
        background: linear-gradient(180deg, #334155 0%, #1e293b 100%);
    }

    .dark .fc-col-header-cell {
        color: #94a3b8;
        border-color: #334155 !important;
    }

    .dark .fc-daygrid-day-number {
        color: #e2e8f0;
    }

    .dark .fc-scrollgrid td {
        border-color: #334155 !important;
    }

    .dark .fc-day-other {
        background: #0f172a;
    }

    .dark .quick-actions-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.1);
    }

    .dark .form-control {
        background: #1e293b;
        border-color: #334155;
        color: white;
    }

    .dark .modal-container {
        background: #1e293b;
    }

    .dark .modal-body {
        background: #1e293b;
    }

    .dark .modal-footer {
        background: #0f172a;
        border-color: #334155;
    }

    .dark .session-detail {
        background: #0f172a;
    }

    .dark .session-detail-value {
        color: #e2e8f0;
    }

    .dark .form-label {
        color: #e2e8f0;
    }

    /* Responsive Stats Grid */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush

@section('content')
<div class="py-6">
    <div class="mx-auto px-4 max-w-7xl">

        <!-- Page Header -->
        <div class="page-header">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 relative z-10">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">الجدول الدراسي</h1>
                    <p class="text-white/80">إدارة جلساتك التعليمية بسهولة من خلال التقويم التفاعلي</p>
                </div>
                <button onclick="openCreateModal()" class="inline-flex items-center gap-3 px-6 py-3 rounded-xl font-bold text-white transition-all duration-300 hover:scale-105 hover:shadow-xl" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    إضافة جلسة جديدة
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
            <div class="stat-card" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
                <div class="stat-icon">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="stat-number">{{ count($sessions) }}</div>
                <div class="stat-label">إجمالي الجلسات</div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="stat-icon">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="stat-number">{{ count($sessions->where('start', '>=', now()->startOfMonth())) }}</div>
                <div class="stat-label">هذا الشهر</div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="stat-icon">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="stat-number">{{ count($sessions->where('start', '>=', now())) }}</div>
                <div class="stat-label">قادمة</div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                <div class="stat-icon">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="stat-number">{{ count($subjects) }}</div>
                <div class="stat-label">المواد</div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Calendar -->
            <div class="lg:col-span-9">
                <div class="calendar-wrapper">
                    <div id="calendar"></div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Quick Actions -->
                <div class="quick-actions-card">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">إجراءات سريعة</h3>
                    <button onclick="openCreateModal()" class="quick-action-btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <span class="quick-action-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </span>
                        إضافة جلسة
                    </button>
                    <a href="{{ route('teacher.my-subjects.index') }}" class="quick-action-btn" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
                        <span class="quick-action-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </span>
                        عرض المواد
                    </a>
                </div>

                <!-- Legend -->
                <div class="legend-card">
                    <h3 class="text-lg font-bold mb-4">دليل الألوان</h3>
                    <div class="legend-item">
                        <div class="legend-dot" style="background: linear-gradient(135deg, #3b82f6, #2563eb);"></div>
                        <span class="legend-text">مجدولة (قادمة)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-dot" style="background: linear-gradient(135deg, #ef4444, #dc2626);"></div>
                        <span class="legend-text">مباشر (جارية الآن)</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-dot" style="background: linear-gradient(135deg, #10b981, #059669);"></div>
                        <span class="legend-text">مكتملة</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-dot" style="background: linear-gradient(135deg, #6b7280, #4b5563);"></div>
                        <span class="legend-text">أخرى</span>
                    </div>
                </div>

                <!-- Tips -->
                <div class="quick-actions-card" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: none;">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-amber-900 mb-1">نصيحة</h4>
                            <p class="text-sm text-amber-800">انقر على أي يوم لإضافة جلسة، أو انقر على جلسة لعرض التفاصيل</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Session Modal -->
<div id="createModal" class="modal-overlay" onclick="if(event.target === this) closeCreateModal()">
    <div class="modal-container">
        <div class="modal-header">
            <button type="button" class="modal-close" onclick="closeCreateModal()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div class="modal-header-icon">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <h3 class="modal-title">إضافة جلسة جديدة</h3>
            <p class="modal-subtitle">أضف جلسة تعليمية جديدة إلى جدولك</p>
        </div>

        <form id="createSessionForm" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">
                        <span class="form-label-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </span>
                        المادة الدراسية
                    </label>
                    <select name="subject_id" id="createSubjectId" required class="form-control">
                        <option value="">اختر المادة...</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" data-name="{{ $subject->name }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <span class="form-label-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        التاريخ والوقت
                    </label>
                    <input type="datetime-local" name="scheduled_at" id="createScheduledAt" required class="form-control">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            <span class="form-label-icon">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            المدة
                        </label>
                        <select name="duration_minutes" id="createDuration" class="form-control">
                            <option value="30">30 دقيقة</option>
                            <option value="45">45 دقيقة</option>
                            <option value="60" selected>60 دقيقة</option>
                            <option value="90">90 دقيقة</option>
                            <option value="120">ساعتان</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <span class="form-label-icon">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            النوع
                        </label>
                        <select name="type" id="createType" class="form-control">
                            <option value="live_zoom">Zoom مباشر</option>
                            <option value="recorded_video">فيديو مسجل</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <span class="form-label-icon">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                        </span>
                        الوصف (اختياري)
                    </label>
                    <textarea name="description_ar" id="createDescription" rows="3" class="form-control" placeholder="أضف وصفاً للجلسة..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeCreateModal()" class="btn btn-secondary">إلغاء</button>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    إنشاء الجلسة
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Session Modal -->
<div id="viewModal" class="modal-overlay" onclick="if(event.target === this) closeViewModal()">
    <div class="modal-container" style="max-width: 520px;">
        <div class="modal-header" id="viewModalHeader" style="padding: 1.75rem 2rem; text-align: center;">
            <button type="button" class="modal-close" onclick="closeViewModal()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div id="viewModalStatus" style="margin-bottom: 0.75rem;"></div>
            <h3 class="modal-title" id="viewModalTitle" style="font-size: 1.35rem; margin-bottom: 0.5rem;">تفاصيل الجلسة</h3>
            <p class="modal-subtitle" id="viewModalSubtitle" style="font-size: 0.95rem;"></p>
        </div>

        <div class="modal-body" style="padding: 1.5rem 2rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div style="background: linear-gradient(135deg, #eff6ff, #dbeafe); border-radius: 16px; padding: 1.25rem; text-align: center;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #0071AA, #005a88); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">المادة</div>
                    <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;" id="viewSubject"></div>
                </div>
                <div style="background: linear-gradient(135deg, #f5f3ff, #ede9fe); border-radius: 16px; padding: 1.25rem; text-align: center;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                        </svg>
                    </div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">رقم الجلسة</div>
                    <div style="font-weight: 800; color: #1e293b; font-size: 1.25rem;" id="viewSessionNumber"></div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div style="background: linear-gradient(135deg, #fefce8, #fef3c7); border-radius: 16px; padding: 1.25rem; text-align: center;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">التاريخ والوقت</div>
                    <div style="font-weight: 700; color: #1e293b; font-size: 0.85rem; line-height: 1.5;" id="viewDateTime"></div>
                </div>
                <div style="background: linear-gradient(135deg, #fdf2f8, #fce7f3); border-radius: 16px; padding: 1.25rem; text-align: center;">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #ec4899, #be185d); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">نوع الجلسة</div>
                    <div style="font-weight: 700; color: #1e293b; font-size: 0.95rem;" id="viewType"></div>
                </div>
            </div>
        </div>

        <div class="modal-footer" style="flex-direction: column; gap: 0.75rem;">
            <a id="viewZoomBtn" href="#" target="_blank" class="btn" style="width: 100%; display: none; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff; text-decoration: none; justify-content: center; align-items: center; gap: 0.5rem; padding: 0.75rem 1rem; border-radius: 12px; font-weight: 700; font-size: 0.95rem;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                انضمام إلى Zoom
            </a>
            <a id="viewDetailsBtn" href="#" class="btn btn-primary" style="width: 100%;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                عرض التفاصيل الكاملة
            </a>
            <div style="display: flex; gap: 0.75rem; width: 100%;">
                <a id="editSessionBtn" href="#" class="btn btn-edit" style="flex: 1;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    تعديل
                </a>
                <button type="button" onclick="openDeleteModal()" class="btn btn-danger" style="flex: 1;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    حذف
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay" onclick="if(event.target === this) closeDeleteModal()">
    <div class="modal-container" style="max-width: 400px;">
        <div class="modal-header" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
            <button type="button" class="modal-close" onclick="closeDeleteModal()">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div class="modal-header-icon" style="background: rgba(255,255,255,0.2);">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="modal-title">تأكيد الحذف</h3>
            <p class="modal-subtitle">هل أنت متأكد من حذف هذه الجلسة؟</p>
        </div>

        <div class="modal-body" style="text-align: center; padding: 2rem;">
            <p class="text-gray-600 dark:text-gray-400">لا يمكن التراجع عن هذا الإجراء. سيتم حذف الجلسة نهائياً مع جميع البيانات المرتبطة بها.</p>
        </div>

        <div class="modal-footer">
            <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">إلغاء</button>
            <form id="deleteForm" method="POST" style="flex: 1;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" style="width: 100%;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    نعم، احذف الجلسة
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
    let calendar;
    let currentSessionData = null;
    const sessions = @json($sessions);

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            buttonText: {
                today: 'اليوم',
                month: 'شهر',
                week: 'أسبوع',
                list: 'قائمة'
            },
            locale: 'ar',
            firstDay: 6,
            direction: 'rtl',
            height: 'auto',
            events: sessions,
            dateClick: function(info) {
                openCreateModal(info.dateStr);
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                const props = info.event.extendedProps;
                currentSessionData = {
                    id: info.event.id,
                    title: info.event.title,
                    subject: props.subject,
                    subject_id: props.subject_id,
                    status: props.status,
                    type: props.type,
                    session_number: props.session_number,
                    start: info.event.start,
                    url: info.event.url,
                    zoom_start_url: props.zoom_start_url,
                    zoom_join_url: props.zoom_join_url
                };
                openViewModal(currentSessionData);
            },
            eventContent: function(arg) {
                const session = arg.event.extendedProps;
                const icon = session.type === 'live_zoom' ? '🎥' : '📹';
                return {
                    html: `
                        <div style="padding: 4px 6px; overflow: hidden;">
                            <div style="font-weight: 700; font-size: 0.75rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                ${icon} ${arg.event.title}
                            </div>
                            <div style="font-size: 0.7rem; opacity: 0.9; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                ${session.subject}
                            </div>
                        </div>
                    `
                };
            },
            dayMaxEvents: 3,
            moreLinkText: n => `+${n} المزيد`
        });

        calendar.render();

        // Form submission
        document.getElementById('createSessionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const subjectId = document.getElementById('createSubjectId').value;
            const subjectOption = document.getElementById('createSubjectId').selectedOptions[0];

            if (!subjectId) {
                alert('يرجى اختيار المادة الدراسية');
                return;
            }

            const subjectName = subjectOption.dataset.name;
            const subjectSessions = sessions.filter(s => s.extendedProps.subject_id == subjectId);
            const nextSessionNumber = subjectSessions.length > 0
                ? Math.max(...subjectSessions.map(s => s.extendedProps.session_number)) + 1
                : 1;

            addHiddenField(this, 'title_ar', `جلسة ${nextSessionNumber} - ${subjectName}`);
            addHiddenField(this, 'title_en', `Session ${nextSessionNumber} - ${subjectName}`);
            addHiddenField(this, 'session_number', nextSessionNumber);

            this.action = `/teacher/my-subjects/${subjectId}/sessions`;
            this.submit();
        });
    });

    function addHiddenField(form, name, value) {
        let input = form.querySelector(`input[name="${name}"]`);
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            form.appendChild(input);
        }
        input.value = value;
    }

    function openCreateModal(date = null) {
        document.getElementById('createModal').classList.add('active');
        if (date) {
            document.getElementById('createScheduledAt').value = date + 'T10:00';
        }
        document.body.style.overflow = 'hidden';
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.remove('active');
        document.getElementById('createSessionForm').reset();
        document.body.style.overflow = '';
    }

    function openViewModal(session) {
        const statusConfig = {
            'مباشر': { bg: 'linear-gradient(135deg, #ef4444, #dc2626)', badge: 'background: #fef2f2; color: #dc2626;' },
            'مكتملة': { bg: 'linear-gradient(135deg, #10b981, #059669)', badge: 'background: #ecfdf5; color: #059669;' },
            'مجدولة': { bg: 'linear-gradient(135deg, #3b82f6, #2563eb)', badge: 'background: #eff6ff; color: #2563eb;' },
            'أخرى': { bg: 'linear-gradient(135deg, #6b7280, #4b5563)', badge: 'background: #f3f4f6; color: #4b5563;' }
        };

        const config = statusConfig[session.status] || statusConfig['أخرى'];

        document.getElementById('viewModalHeader').style.background = config.bg;
        document.getElementById('viewModalTitle').textContent = session.title;
        document.getElementById('viewModalSubtitle').textContent = session.subject;
        document.getElementById('viewModalStatus').innerHTML = `<span class="status-badge" style="${config.badge}; font-size: 0.8rem; padding: 0.35rem 1rem;">${session.status}</span>`;
        document.getElementById('viewSubject').textContent = session.subject;
        document.getElementById('viewSessionNumber').textContent = `#${session.session_number}`;
        document.getElementById('viewType').textContent = session.type === 'live_zoom' ? '🎥 Zoom مباشر' : '📹 فيديو مسجل';

        // Show/hide Zoom button
        const zoomBtn = document.getElementById('viewZoomBtn');
        const zoomUrl = session.zoom_start_url || session.zoom_join_url;
        if (session.type === 'live_zoom' && zoomUrl) {
            zoomBtn.href = zoomUrl;
            zoomBtn.style.display = 'flex';
        } else {
            zoomBtn.style.display = 'none';
        }

        const dateStr = session.start ? new Date(session.start).toLocaleString('ar-SA', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }) : 'غير محدد';
        document.getElementById('viewDateTime').textContent = dateStr;

        document.getElementById('viewDetailsBtn').href = session.url;
        document.getElementById('editSessionBtn').href = `/teacher/my-subjects/${session.subject_id}/sessions/${session.id}/edit`;

        document.getElementById('viewModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeViewModal() {
        document.getElementById('viewModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    function openDeleteModal() {
        if (!currentSessionData) return;
        document.getElementById('deleteForm').action = `/teacher/my-subjects/${currentSessionData.subject_id}/sessions/${currentSessionData.id}`;
        document.getElementById('deleteModal').classList.add('active');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
    }

    // Escape key handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeViewModal();
            closeDeleteModal();
        }
    });
</script>
@endpush
