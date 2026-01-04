@extends('layouts.dashboard')

@section('title', 'جلساتي')

@push('styles')
<style>
    .sessions-page {
        --primary: #0071AA;
        --primary-dark: #005a88;
        --primary-light: #e6f4fa;
    }

    /* Animated gradient background */
    .hero-gradient {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #003d5c 100%);
        position: relative;
        overflow: hidden;
    }

    .hero-gradient::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        animation: slide 20s linear infinite;
    }

    @keyframes slide {
        0% { transform: translateX(0); }
        100% { transform: translateX(-60px); }
    }

    /* Floating shapes */
    .floating-shape {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        animation: float 6s ease-in-out infinite;
    }

    .floating-shape:nth-child(1) {
        width: 80px;
        height: 80px;
        top: 10%;
        right: 10%;
        animation-delay: 0s;
    }

    .floating-shape:nth-child(2) {
        width: 60px;
        height: 60px;
        top: 60%;
        right: 20%;
        animation-delay: 2s;
    }

    .floating-shape:nth-child(3) {
        width: 40px;
        height: 40px;
        top: 30%;
        left: 15%;
        animation-delay: 4s;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(10deg); }
    }

    /* Stats card hover effect */
    .stat-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--accent-color);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -15px rgba(0, 113, 170, 0.2);
    }

    .stat-card:hover::before {
        transform: scaleX(1);
    }

    /* Session card styles */
    .session-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .session-card::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        opacity: 0;
        transition: opacity 0.3s ease;
        background: linear-gradient(135deg, rgba(0, 113, 170, 0.03) 0%, transparent 100%);
    }

    .session-card:hover {
        transform: translateX(-4px);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
    }

    .session-card:hover::after {
        opacity: 1;
    }

    /* Live indicator pulse */
    .live-pulse {
        animation: livePulse 2s ease-in-out infinite;
    }

    @keyframes livePulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        50% { box-shadow: 0 0 0 12px rgba(239, 68, 68, 0); }
    }

    /* Button shine effect */
    .btn-shine {
        position: relative;
        overflow: hidden;
    }

    .btn-shine::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s ease;
    }

    .btn-shine:hover::before {
        left: 100%;
    }

    /* Filter section */
    .filter-section {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    }

    .dark .filter-section {
        background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
    }

    /* Timeline connector */
    .timeline-dot {
        position: relative;
    }

    .timeline-dot::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        width: 2px;
        height: 20px;
        background: linear-gradient(to bottom, #e5e7eb, transparent);
    }

    .session-card:last-child .timeline-dot::after {
        display: none;
    }

    /* Glassmorphism effect */
    .glass {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .dark .glass {
        background: rgba(31, 41, 55, 0.7);
    }

    /* Progress ring */
    .progress-ring {
        transform: rotate(-90deg);
    }

    .progress-ring__circle {
        transition: stroke-dashoffset 0.5s ease;
    }

    /* Tab active indicator */
    .tab-indicator {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Smooth scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endpush

@section('content')
<div class="sessions-page space-y-6">
    <!-- Hero Header -->
    <div class="hero-gradient rounded-3xl p-8 shadow-xl relative">
        <!-- Floating shapes -->
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>

        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center glass">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">جلساتي</h1>
                        <p class="mt-1 text-white/70">متابعة جميع الجلسات والمحاضرات التعليمية</p>
                    </div>
                </div>

                <!-- Quick Stats in Header -->
                <div class="flex items-center gap-4">
                    @if($totalSessions - $completedSessions > 0)
                    <div class="px-5 py-3 rounded-2xl glass flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white/60 text-xs">جلسات قادمة</p>
                            <p class="text-white font-bold text-lg">{{ $totalSessions - $completedSessions }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="px-5 py-3 rounded-2xl glass flex items-center gap-3">
                        <div class="relative w-12 h-12">
                            <svg class="progress-ring w-12 h-12">
                                <circle class="text-white/20" stroke="currentColor" stroke-width="3" fill="transparent" r="20" cx="24" cy="24"/>
                                <circle class="progress-ring__circle text-emerald-400" stroke="currentColor" stroke-width="3" fill="transparent" r="20" cx="24" cy="24"
                                    stroke-dasharray="{{ 125.6 }}"
                                    stroke-dashoffset="{{ $totalSessions > 0 ? 125.6 - (125.6 * $completedSessions / $totalSessions) : 125.6 }}"
                                    stroke-linecap="round"/>
                            </svg>
                            <span class="absolute inset-0 flex items-center justify-center text-white text-xs font-bold">
                                {{ $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100) : 0 }}%
                            </span>
                        </div>
                        <div>
                            <p class="text-white/60 text-xs">نسبة الإنجاز</p>
                            <p class="text-white font-bold">{{ $completedSessions }}/{{ $totalSessions }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Sessions -->
        <div class="stat-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6" style="--accent-color: #3b82f6;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي الجلسات</p>
                    <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalSessions }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">جميع الجلسات المتاحة</p>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completed Sessions -->
        <div class="stat-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6" style="--accent-color: #10b981;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">جلسات مكتملة</p>
                    <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">{{ $completedSessions }}</p>
                    <div class="flex items-center gap-1 mt-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" style="background-color: #d1fae5; color: #047857;">
                            <svg class="w-3 h-3 me-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            تم الحضور
                        </span>
                    </div>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Zoom Sessions -->
        <div class="stat-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6" style="--accent-color: #0071AA;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">جلسات Zoom</p>
                    <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">{{ $zoomSessions }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">جلسات مباشرة</p>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
                    <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 4h10a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm14 2.5l4-2v11l-4-2v-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Upcoming Sessions -->
        <div class="stat-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6" style="--accent-color: #f59e0b;">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">جلسات قادمة</p>
                    <p class="text-4xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalSessions - $completedSessions }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">في انتظارك</p>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filter-section bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #e6f4fa;">
                    <svg class="w-5 h-5" style="color: #0071AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white">تصفية الجلسات</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">اختر المادة ونوع الجلسة للبحث</p>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('student.my-sessions') }}" class="p-5">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            المادة
                        </span>
                    </label>
                    <select name="subject_id" id="subject_id" class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-offset-0 focus:border-[#0071AA] shadow-sm transition-all">
                        <option value="">جميع المواد</option>
                        @foreach($enrolledSubjects as $subject)
                            <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2m0 2v2m10-2V2m0 2v2M3 10h18M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                            </svg>
                            نوع الجلسة
                        </span>
                    </label>
                    <select name="type" id="type" class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-offset-0 focus:border-[#0071AA] shadow-sm transition-all">
                        <option value="">جميع الأنواع</option>
                        <option value="live_zoom" {{ $type == 'live_zoom' ? 'selected' : '' }}>🎥 Zoom مباشر</option>
                        <option value="recorded" {{ $type == 'recorded' ? 'selected' : '' }}>📹 مسجل</option>
                        <option value="in_person" {{ $type == 'in_person' ? 'selected' : '' }}>👥 حضوري</option>
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit" class="btn-shine px-6 py-2.5 text-white font-semibold rounded-xl transition-all flex items-center gap-2 shadow-lg hover:shadow-xl" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        بحث
                    </button>
                    @if($subjectId || $type)
                    <a href="{{ route('student.my-sessions') }}" class="px-4 py-2.5 text-gray-600 dark:text-gray-300 font-medium rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        إلغاء
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Sessions List -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <!-- Section Header -->
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">قائمة الجلسات</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $sessions->total() }} جلسة متاحة</p>
                    </div>
                </div>

                @if($sessions->count() > 0)
                <div class="hidden sm:flex items-center gap-2 text-sm">
                    <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400">
                        <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                        مباشر
                    </span>
                    <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        مكتمل
                    </span>
                    <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/>
                        </svg>
                        قادم
                    </span>
                </div>
                @endif
            </div>
        </div>

        @if($sessions->count() > 0)
            <div class="divide-y divide-gray-50 dark:divide-gray-700/50">
                @foreach($sessions as $session)
                    <div class="session-card p-6 hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-all duration-300">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-5">
                            <!-- Session Icon with Timeline -->
                            <div class="flex-shrink-0 timeline-dot">
                                <div class="relative">
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg transition-transform hover:scale-105"
                                        style="background: linear-gradient(135deg,
                                            @if($session->type === 'live_zoom') #0071AA, #005a88
                                            @elseif($session->type === 'recorded') #8b5cf6, #6d28d9
                                            @else #f59e0b, #d97706 @endif);">
                                        @if($session->type === 'live_zoom')
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        @elseif($session->type === 'recorded')
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @else
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        @endif
                                    </div>

                                    <!-- Live indicator -->
                                    @if($session->started_at && !$session->ended_at)
                                        <span class="absolute -top-1 -right-1 flex h-5 w-5 live-pulse">
                                            <span class="absolute inline-flex h-full w-full rounded-full bg-red-500"></span>
                                            <span class="relative inline-flex rounded-full h-5 w-5 bg-red-500 border-2 border-white dark:border-gray-800 items-center justify-center">
                                                <span class="w-2 h-2 bg-white rounded-full"></span>
                                            </span>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Session Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-3 mb-2">
                                    <h3 class="font-bold text-gray-900 dark:text-white text-lg hover:text-[#0071AA] transition-colors">{{ $session->title }}</h3>

                                    <!-- Status Badge -->
                                    @if($session->started_at && !$session->ended_at)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold text-white bg-gradient-to-r from-red-500 to-red-600 shadow-sm">
                                            <span class="w-1.5 h-1.5 bg-white rounded-full me-1.5 animate-pulse"></span>
                                            مباشر الآن
                                        </span>
                                    @elseif($session->ended_at)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                            <svg class="w-3.5 h-3.5 me-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            مكتمل
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">
                                            <svg class="w-3.5 h-3.5 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/>
                                            </svg>
                                            قادم
                                        </span>
                                    @endif
                                </div>

                                <!-- Subject & Unit -->
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold" style="background-color: #e6f4fa; color: #0071AA;">
                                        <svg class="w-3.5 h-3.5 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        {{ $session->subject->name }}
                                    </span>
                                    @if($session->unit)
                                        <span class="text-gray-300 dark:text-gray-600">•</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $session->unit->title }}</span>
                                    @endif
                                </div>

                                <!-- Meta Info -->
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($session->scheduled_at)
                                        <span class="inline-flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-lg">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $session->scheduled_at->format('Y-m-d') }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-lg">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $session->scheduled_at->format('H:i') }}
                                        </span>
                                    @endif
                                    @if($session->duration_minutes)
                                        <span class="inline-flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-lg">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                            {{ $session->duration_minutes }} دقيقة
                                        </span>
                                    @endif

                                    <!-- Session Type Badge -->
                                    @if($session->type === 'live_zoom')
                                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold" style="background: linear-gradient(135deg, #0071AA20 0%, #005a8820 100%); color: #0071AA;">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M4 4h10a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm14 2.5l4-2v11l-4-2v-7z"/>
                                                </svg>
                                                Zoom
                                            </span>
                                        </span>
                                    @elseif($session->type === 'recorded')
                                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold" style="background: linear-gradient(135deg, #8b5cf620 0%, #6d28d920 100%); color: #7c3aed;">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                </svg>
                                                مسجل
                                            </span>
                                        </span>
                                    @else
                                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold" style="background: linear-gradient(135deg, #f59e0b20 0%, #d9770620 100%); color: #b45309;">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7"/>
                                                </svg>
                                                حضوري
                                            </span>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions Column -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 lg:flex-shrink-0">
                                <!-- Attendance Status -->
                                @if(isset($attendances[$session->id]))
                                    @if($attendances[$session->id]->attended)
                                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold text-white shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #047857 100%);">
                                            <svg class="w-4 h-4 me-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            حضرت
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold text-white shadow-sm" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                                            <svg class="w-4 h-4 me-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                            غائب
                                        </span>
                                    @endif
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($session->type === 'live_zoom')
                                        @if($session->started_at && !$session->ended_at)
                                            {{-- Session is LIVE now --}}
                                            <a href="{{ route('student.sessions.join-zoom', $session->id) }}"
                                               class="btn-shine inline-flex items-center px-6 py-3 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                               style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                                                <svg class="w-5 h-5 me-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                                انضم الآن
                                            </a>
                                        @elseif(!$session->ended_at && $session->zoom_meeting_id)
                                            @if($session->scheduled_at && $session->scheduled_at > now())
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-bold px-4 py-2 rounded-xl"
                                                        style="@if($session->scheduled_at->isToday()) background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 100%); color: #0e7490; @elseif($session->scheduled_at->isTomorrow()) background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #b45309; @else background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); color: #4b5563; @endif">
                                                        @if($session->scheduled_at->isToday())
                                                            <span class="flex items-center gap-1.5">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/>
                                                                </svg>
                                                                اليوم {{ $session->scheduled_at->format('H:i') }}
                                                            </span>
                                                        @elseif($session->scheduled_at->isTomorrow())
                                                            <span class="flex items-center gap-1.5">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                                </svg>
                                                                غداً {{ $session->scheduled_at->format('H:i') }}
                                                            </span>
                                                        @else
                                                            {{ $session->scheduled_at->diffForHumans() }}
                                                        @endif
                                                    </span>
                                                </div>
                                            @elseif($session->scheduled_at && $session->scheduled_at->copy()->addHours(2) < now())
                                                <span class="inline-flex items-center text-sm font-bold px-4 py-2 rounded-xl" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b;">
                                                    <svg class="w-4 h-4 me-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    انتهى وقت الدخول
                                                </span>
                                            @else
                                                <a href="{{ route('student.sessions.join-zoom', $session->id) }}"
                                                   class="btn-shine inline-flex items-center px-6 py-3 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                                   style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
                                                    <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                    انضم للجلسة
                                                </a>
                                            @endif
                                        @elseif($session->ended_at && $session->zoom_recording_url)
                                            <a href="{{ $session->zoom_recording_url }}" target="_blank"
                                               class="btn-shine inline-flex items-center px-6 py-3 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                               style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
                                                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                مشاهدة التسجيل
                                            </a>
                                        @endif

                                        {{-- External Zoom Link --}}
                                        @if($session->zoom_join_url && !$session->ended_at)
                                            @if(!$session->scheduled_at || ($session->scheduled_at <= now() && $session->scheduled_at->copy()->addHours(2) >= now()))
                                                <a href="{{ $session->zoom_join_url }}" target="_blank"
                                                   class="inline-flex items-center px-4 py-2.5 text-white font-semibold rounded-xl transition-all hover:shadow-lg"
                                                   style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);"
                                                   title="فتح في تطبيق Zoom">
                                                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                    </svg>
                                                    فتح Zoom
                                                </a>
                                            @endif
                                        @endif
                                    @endif

                                    @if($session->type === 'recorded' && $session->video_url)
                                        <a href="{{ $session->video_url }}" target="_blank"
                                           class="btn-shine inline-flex items-center px-6 py-3 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                           style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
                                            <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            مشاهدة الفيديو
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($sessions->hasPages())
                <div class="px-6 py-5 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    {{ $sessions->withQueryString()->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="p-16 text-center">
                <div class="relative w-32 h-32 mx-auto mb-8">
                    <div class="absolute inset-0 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 animate-pulse"></div>
                    <div class="absolute inset-4 rounded-full bg-white dark:bg-gray-800 flex items-center justify-center shadow-inner">
                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">لا توجد جلسات حالياً</h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6">
                    سيتم عرض الجلسات والمحاضرات هنا عند توفرها. تابع صفحتك للحصول على آخر التحديثات من معلميك.
                </p>
                <a href="{{ route('student.dashboard') }}" class="inline-flex items-center px-6 py-3 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
                    <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    العودة للرئيسية
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
