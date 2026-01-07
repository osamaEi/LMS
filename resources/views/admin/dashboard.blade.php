@extends('layouts.dashboard')

@section('title', 'لوحة تحكم المدير')

@push('styles')
<style>
    /* Modern Dashboard Styles */
    .dashboard-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #004466 100%);
        border-radius: 24px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 60%;
        height: 200%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .dashboard-header::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 40%;
        height: 150%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.05) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0, 113, 170, 0.1);
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 113, 170, 0.15);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--card-color), var(--card-color-light));
    }

    .stat-card .icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--card-color), var(--card-color-light));
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        margin-bottom: 1rem;
    }

    .stat-card .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--card-color), var(--card-color-dark));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.2;
    }

    .stat-card .stat-label {
        font-size: 0.95rem;
        color: #64748b;
        font-weight: 500;
        margin-top: 0.25rem;
    }

    .stat-card .stat-sub {
        font-size: 0.8rem;
        color: #94a3b8;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .stat-card .decorative-circle {
        position: absolute;
        bottom: -20px;
        left: -20px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--card-color), transparent);
        opacity: 0.1;
    }

    /* Card Colors */
    .stat-card.teachers {
        --card-color: #0071AA;
        --card-color-light: #0099dd;
        --card-color-dark: #005580;
    }

    .stat-card.students {
        --card-color: #10b981;
        --card-color-light: #34d399;
        --card-color-dark: #059669;
    }

    .stat-card.subjects {
        --card-color: #f59e0b;
        --card-color-light: #fbbf24;
        --card-color-dark: #d97706;
    }

    .stat-card.terms {
        --card-color: #8b5cf6;
        --card-color-light: #a78bfa;
        --card-color-dark: #7c3aed;
    }

    /* Chart Cards */
    .chart-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 113, 170, 0.1);
        transition: all 0.3s ease;
    }

    .chart-card:hover {
        box-shadow: 0 10px 30px rgba(0, 113, 170, 0.12);
    }

    .chart-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f1f5f9;
    }

    .chart-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .chart-title .icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0071AA, #005a88);
    }

    /* Info Cards */
    .info-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 113, 170, 0.1);
        height: 100%;
    }

    .info-card-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f1f5f9;
    }

    .info-card-header .icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .info-card-header h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
    }

    /* Teacher Item */
    .teacher-item {
        display: flex;
        align-items: center;
        padding: 0.875rem;
        border-radius: 14px;
        transition: all 0.3s ease;
        margin-bottom: 0.5rem;
    }

    .teacher-item:hover {
        background: linear-gradient(135deg, rgba(0, 113, 170, 0.05), rgba(0, 113, 170, 0.02));
        transform: translateX(-4px);
    }

    .teacher-item img {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        border: 3px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .teacher-item:hover img {
        border-color: #0071AA;
        transform: scale(1.05);
    }

    .teacher-item .info {
        margin-right: 0.875rem;
        flex: 1;
    }

    .teacher-item .name {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.95rem;
    }

    .teacher-item .email {
        font-size: 0.8rem;
        color: #94a3b8;
    }

    /* Terms Status */
    .term-status-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        border-radius: 14px;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
    }

    .term-status-item:hover {
        transform: translateX(-4px);
    }

    .term-status-item.active {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .term-status-item.upcoming {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05));
        border: 1px solid rgba(59, 130, 246, 0.2);
    }

    .term-status-item.completed {
        background: linear-gradient(135deg, rgba(107, 114, 128, 0.1), rgba(107, 114, 128, 0.05));
        border: 1px solid rgba(107, 114, 128, 0.2);
    }

    .term-status-item .status-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .term-status-item.active .status-icon {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .term-status-item.upcoming .status-icon {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .term-status-item.completed .status-icon {
        background: linear-gradient(135deg, #6b7280, #4b5563);
    }

    .term-status-item .count {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1e293b;
    }

    /* System Stats */
    .system-stat-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        border-radius: 14px;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
    }

    .system-stat-item:hover {
        background: linear-gradient(135deg, rgba(0, 113, 170, 0.08), rgba(0, 113, 170, 0.04));
        transform: translateX(-4px);
    }

    .system-stat-item .label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #475569;
        font-weight: 500;
    }

    .system-stat-item .label .icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0071AA, #005a88);
    }

    .system-stat-item .value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0071AA;
    }

    /* Subject Cards */
    .subjects-section {
        margin-top: 2rem;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .section-header .icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0071AA, #005a88);
    }

    .section-header h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
    }

    .subject-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 113, 170, 0.1);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .subject-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 113, 170, 0.15);
    }

    .subject-card .image-wrapper {
        height: 140px;
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #004466 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .subject-card .image-wrapper::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -30%;
        width: 80%;
        height: 150%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.15) 0%, transparent 60%);
    }

    .subject-card .image-wrapper svg {
        width: 56px;
        height: 56px;
        color: rgba(255, 255, 255, 0.9);
        position: relative;
        z-index: 1;
    }

    .subject-card .content {
        padding: 1.25rem;
    }

    .subject-card .name {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }

    .subject-card .code {
        font-size: 0.8rem;
        color: #94a3b8;
        margin-bottom: 0.75rem;
    }

    .subject-card .meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }

    .subject-card .status {
        font-size: 0.75rem;
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-weight: 600;
    }

    .subject-card .status.active {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.1));
        color: #059669;
    }

    .subject-card .status.inactive {
        background: linear-gradient(135deg, rgba(107, 114, 128, 0.15), rgba(107, 114, 128, 0.1));
        color: #4b5563;
    }

    .subject-card .term {
        font-size: 0.75rem;
        color: #64748b;
    }

    .subject-card .teacher {
        font-size: 0.8rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .subject-card .teacher svg {
        width: 16px;
        height: 16px;
        color: #0071AA;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #94a3b8;
    }

    .empty-state svg {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        opacity: 0.5;
    }

    /* Welcome Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeInUp {
        animation: fadeInUp 0.6s ease forwards;
    }

    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }
</style>
@endpush

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Dashboard Header -->
<div class="dashboard-header animate-fadeInUp">
    <div class="relative z-10">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-white">لوحة التحكم</h1>
                <p class="text-white/80 mt-1">مرحباً بك، {{ auth()->user()->name }}</p>
            </div>
        </div>
        <div class="flex items-center gap-6 mt-6">
            <div class="flex items-center gap-2 text-white/90">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>{{ now()->format('l، d F Y') }}</span>
            </div>
            <div class="flex items-center gap-2 text-white/90">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span id="current-time">{{ now()->format('h:i A') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <!-- Teachers -->
    <div class="stat-card teachers animate-fadeInUp delay-100" style="opacity: 0;">
        <div class="icon-wrapper">
            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
            </svg>
        </div>
        <div class="stat-value">{{ $stats['teachers_count'] ?? 0 }}</div>
        <div class="stat-label">المعلمين</div>
        <div class="decorative-circle"></div>
    </div>

    <!-- Students -->
    <div class="stat-card students animate-fadeInUp delay-200" style="opacity: 0;">
        <div class="icon-wrapper">
            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
            </svg>
        </div>
        <div class="stat-value">{{ $stats['students_count'] ?? 0 }}</div>
        <div class="stat-label">الطلاب</div>
        <div class="decorative-circle"></div>
    </div>

    <!-- Subjects -->
    <div class="stat-card subjects animate-fadeInUp delay-300" style="opacity: 0;">
        <div class="icon-wrapper">
            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
            </svg>
        </div>
        <div class="stat-value">{{ $stats['subjects_count'] ?? 0 }}</div>
        <div class="stat-label">المواد الدراسية</div>
        <div class="stat-sub">
            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ $stats['active_subjects'] ?? 0 }} نشطة</span>
        </div>
        <div class="decorative-circle"></div>
    </div>

    <!-- Terms -->
    <div class="stat-card terms animate-fadeInUp delay-400" style="opacity: 0;">
        <div class="icon-wrapper">
            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="stat-value">{{ $stats['terms_count'] ?? 0 }}</div>
        <div class="stat-label">الفصول الدراسية</div>
        <div class="stat-sub">
            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ $stats['active_terms'] ?? 0 }} نشطة</span>
        </div>
        <div class="decorative-circle"></div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Users Growth Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title">
                <div class="icon">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <span>نمو المستخدمين</span>
            </div>
        </div>
        <div style="height: 280px;">
            <canvas id="usersGrowthChart"></canvas>
        </div>
    </div>

    <!-- Enrollments Chart -->
    <div class="chart-card">
        <div class="chart-header">
            <div class="chart-title">
                <div class="icon">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span>التسجيلات الشهرية</span>
            </div>
        </div>
        <div style="height: 280px;">
            <canvas id="enrollmentsChart"></canvas>
        </div>
    </div>
</div>

<!-- Bottom Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Recent Teachers -->
    <div class="info-card">
        <div class="info-card-header">
            <div class="icon" style="background: linear-gradient(135deg, #0071AA, #005a88);">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                </svg>
            </div>
            <h3>أحدث المعلمين</h3>
        </div>
        <div>
            @forelse($recentTeachers ?? [] as $teacher)
            <div class="teacher-item">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->name) }}&background=0071AA&color=fff&size=96"
                     alt="{{ $teacher->name }}">
                <div class="info">
                    <p class="name">{{ $teacher->name }}</p>
                    <p class="email">{{ $teacher->email }}</p>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p>لا يوجد معلمين</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Terms Status -->
    <div class="info-card">
        <div class="info-card-header">
            <div class="icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h3>حالة الفصول الدراسية</h3>
        </div>
        <div>
            @php
                $termLabels = ['active' => 'نشط', 'upcoming' => 'قادم', 'completed' => 'مكتمل'];
            @endphp
            @foreach($termsStatus ?? [] as $status => $count)
            <div class="term-status-item {{ $status }}">
                <div class="flex items-center gap-3">
                    <div class="status-icon">
                        @if($status === 'active')
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        @elseif($status === 'upcoming')
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        @else
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        @endif
                    </div>
                    <span class="font-medium text-gray-700">{{ $termLabels[$status] ?? $status }}</span>
                </div>
                <div class="count">{{ $count }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- System Stats -->
    <div class="info-card">
        <div class="info-card-header">
            <div class="icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <h3>إحصائيات النظام</h3>
        </div>
        <div>
            <div class="system-stat-item">
                <div class="label">
                    <div class="icon">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                    </div>
                    <span>إجمالي المستخدمين</span>
                </div>
                <div class="value">{{ $stats['total_users'] ?? 0 }}</div>
            </div>
            <div class="system-stat-item">
                <div class="label">
                    <div class="icon">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span>تسجيل اليوم</span>
                </div>
                <div class="value">{{ $stats['today_enrollments'] ?? 0 }}</div>
            </div>
            <div class="system-stat-item">
                <div class="label">
                    <div class="icon">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                        </svg>
                    </div>
                    <span>البرامج التعليمية</span>
                </div>
                <div class="value">{{ $stats['programs_count'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Subjects -->
<div class="subjects-section">
    <div class="section-header">
        <div class="icon-wrapper">
            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
            </svg>
        </div>
        <h2>أحدث المواد الدراسية</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($recentSubjects ?? [] as $subject)
        <div class="subject-card">
            <div class="image-wrapper">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                </svg>
            </div>
            <div class="content">
                <h3 class="name">{{ $subject->name }}</h3>
                <p class="code">{{ $subject->code }}</p>
                <div class="meta">
                    <span class="status {{ $subject->status === 'active' ? 'active' : 'inactive' }}">
                        {{ $subject->status === 'active' ? 'نشطة' : 'غير نشطة' }}
                    </span>
                    <span class="term">{{ $subject->term->name ?? '' }}</span>
                </div>
                <div class="teacher">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>{{ $subject->teacher->name ?? 'غير محدد' }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-4">
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <p>لا توجد مواد دراسية</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update current time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('ar-SA', { hour: '2-digit', minute: '2-digit', hour12: true });
        document.getElementById('current-time').textContent = timeString;
    }
    setInterval(updateTime, 1000);

    // Chart.js global defaults
    Chart.defaults.font.family = 'Cairo, sans-serif';
    Chart.defaults.color = '#64748b';

    // Users Growth Chart
    const usersGrowthCtx = document.getElementById('usersGrowthChart').getContext('2d');
    const studentsData = @json($studentsPerMonth ?? []);
    const teachersData = @json($teachersPerMonth ?? []);
    const allMonths = [...new Set([...Object.keys(studentsData), ...Object.keys(teachersData)])].sort();

    const gradientStudents = usersGrowthCtx.createLinearGradient(0, 0, 0, 280);
    gradientStudents.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
    gradientStudents.addColorStop(1, 'rgba(16, 185, 129, 0)');

    const gradientTeachers = usersGrowthCtx.createLinearGradient(0, 0, 0, 280);
    gradientTeachers.addColorStop(0, 'rgba(0, 113, 170, 0.3)');
    gradientTeachers.addColorStop(1, 'rgba(0, 113, 170, 0)');

    new Chart(usersGrowthCtx, {
        type: 'line',
        data: {
            labels: allMonths,
            datasets: [{
                label: 'الطلاب',
                data: allMonths.map(month => studentsData[month] || 0),
                borderColor: '#10b981',
                backgroundColor: gradientStudents,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }, {
                label: 'المعلمين',
                data: allMonths.map(month => teachersData[month] || 0),
                borderColor: '#0071AA',
                backgroundColor: gradientTeachers,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#0071AA',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 12, weight: '600' }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(30, 41, 59, 0.95)',
                    titleFont: { size: 14, weight: '600' },
                    bodyFont: { size: 13 },
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: true
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    ticks: { font: { size: 11 }, stepSize: 1 }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Enrollments Chart
    const enrollmentsCtx = document.getElementById('enrollmentsChart').getContext('2d');

    const gradientBars = enrollmentsCtx.createLinearGradient(0, 0, 0, 280);
    gradientBars.addColorStop(0, 'rgba(0, 113, 170, 0.9)');
    gradientBars.addColorStop(1, 'rgba(0, 113, 170, 0.4)');

    new Chart(enrollmentsCtx, {
        type: 'bar',
        data: {
            labels: allMonths,
            datasets: [{
                label: 'التسجيلات',
                data: allMonths.map(month => (studentsData[month] || 0) + (teachersData[month] || 0)),
                backgroundColor: gradientBars,
                borderColor: '#0071AA',
                borderWidth: 0,
                borderRadius: 8,
                borderSkipped: false,
                hoverBackgroundColor: '#005a88'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 12, weight: '600' }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(30, 41, 59, 0.95)',
                    titleFont: { size: 14, weight: '600' },
                    bodyFont: { size: 13 },
                    padding: 12,
                    cornerRadius: 10
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    ticks: { font: { size: 11 }, stepSize: 1 }
                }
            }
        }
    });
});
</script>
@endpush
@endsection
