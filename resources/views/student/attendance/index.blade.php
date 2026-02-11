@extends('layouts.dashboard')

@section('title', 'سجل الحضور')

@push('styles')
<style>
    .att-page { max-width: 1200px; margin: 0 auto; }

    /* Header */
    .att-header {
        background: linear-gradient(135deg, #0071AA 0%, #004d77 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .att-header::before {
        content: '';
        position: absolute;
        top: -40%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }
    .att-header::after {
        content: '';
        position: absolute;
        bottom: -50%;
        right: -5%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        border-radius: 50%;
    }

    /* Stats Strip */
    .stats-strip {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
    }
    @media (max-width: 768px) {
        .stats-strip { grid-template-columns: repeat(2, 1fr); }
        .stats-strip .stat-item:last-child { grid-column: span 2; }
    }
    .stat-item {
        background: #fff;
        border-radius: 18px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .dark .stat-item { background: #1f2937; }
    .stat-item:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .stat-item .stat-icon-wrap {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-item .stat-num {
        font-size: 1.75rem;
        font-weight: 800;
        line-height: 1;
        color: #111827;
    }
    .dark .stat-item .stat-num { color: #f9fafb; }
    .stat-item .stat-txt {
        font-size: 0.78rem;
        color: #6b7280;
        margin-top: 0.2rem;
        font-weight: 500;
    }
    .dark .stat-item .stat-txt { color: #9ca3af; }

    /* Main Layout */
    .att-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 1.5rem;
        align-items: start;
    }
    @media (max-width: 1024px) {
        .att-layout { grid-template-columns: 1fr; }
    }

    /* Card */
    .att-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .dark .att-card { background: #1f2937; }
    .att-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .dark .att-card-header { border-color: #374151; }

    /* Filter */
    .att-filter {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
    }
    .dark .att-filter { background: #111827; border-color: #374151; }
    .att-filter select {
        flex: 1;
        padding: 0.6rem 1rem;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        font-weight: 600;
        font-size: 0.875rem;
        background: #fff;
        transition: border-color 0.2s;
    }
    .dark .att-filter select { background: #1f2937; border-color: #4b5563; color: #f9fafb; }
    .att-filter select:focus { border-color: #0071AA; outline: none; }

    /* Table */
    .att-table { width: 100%; border-collapse: collapse; }
    .att-table thead th {
        padding: 0.85rem 1rem;
        text-align: right;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        background: #f9fafb;
        border-bottom: 2px solid #f1f5f9;
        white-space: nowrap;
    }
    .dark .att-table thead th { background: #111827; color: #9ca3af; border-color: #374151; }
    .att-table thead th.center { text-align: center; }
    .att-table tbody tr {
        border-bottom: 1px solid #f8fafc;
        transition: background 0.15s;
    }
    .dark .att-table tbody tr { border-color: #1f2937; }
    .att-table tbody tr:hover { background: #f8fafc; }
    .dark .att-table tbody tr:hover { background: #111827; }
    .att-table tbody tr:last-child { border-bottom: none; }
    .att-table td {
        padding: 1rem;
        vertical-align: middle;
        font-size: 0.875rem;
    }
    .att-table td.center { text-align: center; }

    /* Row Number */
    .row-num {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.8rem;
        color: #fff;
    }

    /* Badges */
    .badge-present {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.75rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        background: #ecfdf5;
        color: #059669;
    }
    .dark .badge-present { background: rgba(16,185,129,0.1); color: #34d399; }
    .badge-absent {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.75rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        background: #fef2f2;
        color: #dc2626;
    }
    .dark .badge-absent { background: rgba(239,68,68,0.1); color: #f87171; }
    .badge-upcoming {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.75rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        background: #eff6ff;
        color: #2563eb;
    }
    .dark .badge-upcoming { background: rgba(37,99,235,0.1); color: #60a5fa; }

    .dur-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.7rem;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 700;
        background: #f0f9ff;
        color: #0369a1;
    }
    .dark .dur-chip { background: rgba(3,105,161,0.1); color: #38bdf8; }

    /* Progress Mini */
    .progress-mini {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        justify-content: center;
    }
    .progress-mini .bar {
        width: 48px;
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        overflow: hidden;
    }
    .dark .progress-mini .bar { background: #374151; }
    .progress-mini .fill { height: 100%; border-radius: 3px; }

    /* Donut Chart */
    .donut-wrap {
        position: relative;
        width: 140px;
        height: 140px;
        margin: 0 auto;
    }
    .donut-wrap svg { width: 100%; height: 100%; transform: rotate(-90deg); }
    .donut-wrap .donut-bg { fill: none; stroke: #f3f4f6; stroke-width: 12; }
    .dark .donut-wrap .donut-bg { stroke: #374151; }
    .donut-wrap .donut-fg { fill: none; stroke-width: 12; stroke-linecap: round; transition: stroke-dashoffset 1s ease; }
    .donut-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }
    .donut-center .donut-val { font-size: 1.75rem; font-weight: 800; line-height: 1; }
    .donut-center .donut-lbl { font-size: 0.7rem; color: #9ca3af; margin-top: 0.15rem; }

    /* Time card */
    .time-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
    }
    .time-box {
        text-align: center;
        padding: 1rem;
        border-radius: 14px;
        background: #f9fafb;
    }
    .dark .time-box { background: #111827; }
    .time-box .time-val { font-size: 1.5rem; font-weight: 800; line-height: 1; }
    .time-box .time-lbl { font-size: 0.7rem; color: #9ca3af; margin-top: 0.3rem; }

    /* Mobile cards */
    .mobile-att-card {
        display: none;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .dark .mobile-att-card { border-color: #374151; }
    @media (max-width: 768px) {
        .desktop-table { display: none !important; }
        .mobile-att-card { display: block; }
    }
    @media (min-width: 769px) {
        .mobile-att-card { display: none !important; }
    }
</style>
@endpush

@section('content')
<div class="att-page space-y-6">

    @if(!auth()->user()->program_id)
        <!-- Empty State for No Program -->
        <div class="min-h-screen flex items-center justify-center px-4">
            <div class="max-w-2xl w-full">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden">
                    <!-- Gradient Header -->
                    <div class="bg-gradient-to-r from-blue-600 via-cyan-600 to-teal-600 p-8 text-center relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-32 -mt-32"></div>
                        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white opacity-5 rounded-full -ml-24 -mb-24"></div>

                        <div class="relative z-10">
                            <div class="w-24 h-24 mx-auto mb-4 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <h1 class="text-3xl font-bold mb-2">سجل الحضور والغياب</h1>
                            <p class="text-blue-100">تتبع حضورك في الجلسات التعليمية</p>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-12 text-center">
                        <div class="w-32 h-32 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>

                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">لم يتم تسجيلك في برنامج دراسي</h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                            للوصول إلى سجل الحضور والغياب، يجب أن تكون مسجلاً في برنامج دراسي وملتحقاً بمواد دراسية
                        </p>

                        <!-- Info Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                            <div class="bg-blue-50 dark:bg-blue-900 dark:bg-opacity-20 rounded-xl p-4">
                                <div class="w-10 h-10 mx-auto mb-2 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">اختر برنامجك</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">سجل في البرنامج الدراسي المناسب</p>
                            </div>

                            <div class="bg-green-50 dark:bg-green-900 dark:bg-opacity-20 rounded-xl p-4">
                                <div class="w-10 h-10 mx-auto mb-2 bg-green-100 dark:bg-green-800 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">ابدأ الدراسة</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">احضر الجلسات التعليمية</p>
                            </div>

                            <div class="bg-purple-50 dark:bg-purple-900 dark:bg-opacity-20 rounded-xl p-4">
                                <div class="w-10 h-10 mx-auto mb-2 bg-purple-100 dark:bg-purple-800 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1">تتبع تقدمك</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">راقب سجل حضورك</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('student.my-program') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 rounded-xl font-bold transition-all shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                التسجيل في برنامج
                            </a>
                            <a href="{{ route('student.dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-xl font-bold transition-all">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                العودة للرئيسية
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else

    <!-- Header -->
    <div class="att-header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 relative z-10">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.12);">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight">سجل الحضور والغياب</h1>
                    <p class="text-sm opacity-70 mt-0.5">{{ auth()->user()->name }} @if(auth()->user()->studentId) &mdash; {{ auth()->user()->studentId }} @endif</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('student.dashboard') }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all" style="background: rgba(255,255,255,0.12);">لوحة التحكم</a>
                <a href="{{ route('student.my-sessions') }}" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all" style="background: rgba(255,255,255,0.12);">جلساتي</a>
            </div>
        </div>
    </div>

    <!-- Stats Strip -->
    <div class="stats-strip">
        <div class="stat-item">
            <div class="stat-icon-wrap" style="background: linear-gradient(135deg, #0071AA, #005588);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <div class="stat-num">{{ $totalSessions }}</div>
                <div class="stat-txt">إجمالي الجلسات</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon-wrap" style="background: linear-gradient(135deg, #10b981, #059669);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <div class="stat-num">{{ $attendedSessions }}</div>
                <div class="stat-txt">حاضر</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon-wrap" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div>
                <div class="stat-num">{{ $totalSessions - $attendedSessions }}</div>
                <div class="stat-txt">غائب</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon-wrap" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <div class="stat-num">{{ $attendanceRate }}<span style="font-size: 1rem;">%</span></div>
                <div class="stat-txt">نسبة الحضور</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon-wrap" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="stat-num">{{ number_format($totalMinutes) }}</div>
                <div class="stat-txt">دقيقة إجمالية</div>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="att-layout">
        <!-- Records -->
        <div class="att-card">
            <!-- Filter -->
            <form method="GET" action="{{ route('student.attendance') }}">
                <div class="att-filter">
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <select name="subject_id" onchange="this.form.submit()">
                        <option value="">جميع المواد</option>
                        @foreach($enrolledSubjects as $subject)
                            <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>

            <!-- Desktop Table -->
            <div class="desktop-table">
                <table class="att-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>الجلسة</th>
                            <th>المادة</th>
                            <th class="center">التاريخ</th>
                            <th class="center">الحالة</th>
                            <th class="center">المدة</th>
                            <th class="center">المشاهدة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $index => $attendance)
                            <tr>
                                <td>
                                    <span class="row-num" style="background: {{ $attendance->attended ? '#10b981' : '#ef4444' }};">
                                        {{ $attendances->firstItem() + $index }}
                                    </span>
                                </td>
                                <td>
                                    <div class="font-bold text-gray-900 dark:text-white" style="font-size: 0.875rem;">{{ $attendance->session->title }}</div>
                                    @if($attendance->session->unit)
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $attendance->session->unit->title }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-sm font-semibold" style="color: #0071AA;">{{ $attendance->session->subject->name }}</span>
                                </td>
                                <td class="center">
                                    <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                        {{ $attendance->session->scheduled_at?->format('d/m/Y') ?? $attendance->created_at->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        @if($attendance->joined_at)
                                            {{ $attendance->joined_at->format('H:i') }}@if($attendance->left_at) &larr; {{ $attendance->left_at->format('H:i') }}@endif
                                        @else
                                            {{ $attendance->session->scheduled_at?->format('H:i') ?? '--:--' }}
                                        @endif
                                    </div>
                                </td>
                                <td class="center">
                                    @if($attendance->attended)
                                        <span class="badge-present">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            حاضر
                                        </span>
                                    @elseif($attendance->session->scheduled_at && $attendance->session->scheduled_at->isFuture())
                                        <span class="badge-upcoming">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            قادمة
                                        </span>
                                    @else
                                        <span class="badge-absent">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            غائب
                                        </span>
                                    @endif
                                </td>
                                <td class="center">
                                    @if($attendance->duration_minutes)
                                        <span class="dur-chip">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $attendance->duration_minutes }} د
                                        </span>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600">&mdash;</span>
                                    @endif
                                </td>
                                <td class="center">
                                    @if($attendance->watch_percentage)
                                        <div class="progress-mini">
                                            <div class="bar">
                                                <div class="fill" style="width: {{ min($attendance->watch_percentage, 100) }}%; background: {{ $attendance->watch_percentage >= 80 ? '#10b981' : ($attendance->watch_percentage >= 50 ? '#f59e0b' : '#ef4444') }};"></div>
                                            </div>
                                            <span class="text-xs font-bold" style="color: {{ $attendance->watch_percentage >= 80 ? '#10b981' : ($attendance->watch_percentage >= 50 ? '#f59e0b' : '#ef4444') }};">{{ round($attendance->watch_percentage) }}%</span>
                                        </div>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center" style="padding: 4rem 1rem;">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </div>
                                    <p class="text-gray-400 font-medium">لا يوجد سجلات حضور</p>
                                    <p class="text-xs text-gray-300 mt-1">ستظهر هنا عند بدء الجلسات</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            @foreach($attendances as $index => $attendance)
                <div class="mobile-att-card">
                    <div class="flex items-start gap-3">
                        <span class="row-num flex-shrink-0" style="background: {{ $attendance->attended ? '#10b981' : '#ef4444' }}; margin-top: 2px;">
                            {{ $attendances->firstItem() + $index }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-gray-900 dark:text-white text-sm truncate">{{ $attendance->session->title }}</div>
                            <div class="text-xs mt-0.5" style="color: #0071AA; font-weight: 600;">{{ $attendance->session->subject->name }}</div>
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                @if($attendance->attended)
                                    <span class="badge-present">حاضر</span>
                                @elseif($attendance->session->scheduled_at && $attendance->session->scheduled_at->isFuture())
                                    <span class="badge-upcoming">قادمة</span>
                                @else
                                    <span class="badge-absent">غائب</span>
                                @endif
                                @if($attendance->duration_minutes)
                                    <span class="dur-chip">{{ $attendance->duration_minutes }} د</span>
                                @endif
                                <span class="text-xs text-gray-400">{{ $attendance->session->scheduled_at?->format('d/m/Y H:i') ?? $attendance->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($attendances->isEmpty())
                <div class="mobile-att-card text-center" style="padding: 3rem 1rem;">
                    <p class="text-gray-400">لا يوجد سجلات حضور</p>
                </div>
            @endif

            <!-- Pagination -->
            @if($attendances->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
                    {{ $attendances->withQueryString()->links() }}
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-5">
            <!-- Donut Chart -->
            <div class="att-card">
                <div class="att-card-header">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: #ecfdf5;">
                        <svg class="w-4 h-4" style="color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">معدل الحضور</span>
                </div>
                <div class="p-5">
                    @php
                        $r = 54;
                        $circ = 2 * 3.14159265 * $r;
                        $dashoffset = $circ - ($attendanceRate / 100) * $circ;
                        $rateColor = $attendanceRate >= 75 ? '#10b981' : ($attendanceRate >= 50 ? '#f59e0b' : '#ef4444');
                    @endphp
                    <div class="donut-wrap">
                        <svg viewBox="0 0 120 120">
                            <circle class="donut-bg" cx="60" cy="60" r="{{ $r }}"/>
                            <circle class="donut-fg" cx="60" cy="60" r="{{ $r }}" stroke="{{ $rateColor }}" stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $dashoffset }}"/>
                        </svg>
                        <div class="donut-center">
                            <div class="donut-val" style="color: {{ $rateColor }};">{{ $attendanceRate }}%</div>
                            <div class="donut-lbl">نسبة الحضور</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mt-5">
                        <div class="text-center p-2.5 rounded-xl" style="background: #ecfdf5;">
                            <div class="text-lg font-extrabold" style="color: #059669;">{{ $attendedSessions }}</div>
                            <div class="text-xs font-semibold" style="color: #059669;">حاضر</div>
                        </div>
                        <div class="text-center p-2.5 rounded-xl" style="background: #fef2f2;">
                            <div class="text-lg font-extrabold" style="color: #dc2626;">{{ $totalSessions - $attendedSessions }}</div>
                            <div class="text-xs font-semibold" style="color: #dc2626;">غائب</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Study Time -->
            <div class="att-card">
                <div class="att-card-header">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: #f3e8ff;">
                        <svg class="w-4 h-4" style="color: #7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">وقت الدراسة</span>
                </div>
                <div class="p-5">
                    <div class="time-grid">
                        <div class="time-box">
                            <div class="time-val" style="color: #7c3aed;">{{ $totalMinutes >= 60 ? floor($totalMinutes / 60) : 0 }}</div>
                            <div class="time-lbl">ساعة</div>
                        </div>
                        <div class="time-box">
                            <div class="time-val" style="color: #7c3aed;">{{ $totalMinutes % 60 }}</div>
                            <div class="time-lbl">دقيقة</div>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <span class="text-xs font-semibold text-gray-400">الإجمالي: {{ number_format($totalMinutes) }} دقيقة</span>
                    </div>
                </div>
            </div>

            <!-- Student Info -->
            <div class="att-card">
                <div class="att-card-header">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: #eff6ff;">
                        <svg class="w-4 h-4" style="color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">معلومات الطالب</span>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0071AA&color=ffffff&size=96&bold=true"
                             alt="{{ auth()->user()->name }}"
                             class="w-12 h-12 rounded-xl shadow" />
                        <div class="min-w-0">
                            <div class="font-bold text-gray-900 dark:text-white text-sm truncate">{{ auth()->user()->name }}</div>
                            @if(auth()->user()->email)
                                <div class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="space-y-2">
                        @if(auth()->user()->studentId)
                        <div class="flex justify-between text-xs p-2.5 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <span class="text-gray-500">رقم الطالب</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ auth()->user()->studentId }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-xs p-2.5 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <span class="text-gray-500">تاريخ الالتحاق</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ auth()->user()->created_at->format('d/m/Y') }}</span>
                        </div>
                        @if(auth()->user()->program)
                        <div class="flex justify-between text-xs p-2.5 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <span class="text-gray-500">البرنامج</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ auth()->user()->program->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
