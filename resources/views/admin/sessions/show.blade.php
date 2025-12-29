@extends('layouts.dashboard')

@section('title', $session->title)

@push('styles')
<style>
    .session-hero {
        background: linear-gradient(135deg, #0071aa 0%, #005a88 50%, #004266 100%);
        position: relative;
        overflow: hidden;
    }
    .session-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 500px;
        height: 500px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .session-hero::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255,255,255,0.03);
        border-radius: 50%;
    }
    .glass-card {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
    }
    .status-pulse {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .countdown-item {
        background: linear-gradient(145deg, #f8fafc, #e2e8f0);
        box-shadow: 5px 5px 15px #d1d5db, -5px -5px 15px #ffffff;
    }
    .info-card {
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }
    .info-card:hover {
        border-color: #0071aa;
        transform: translateY(-2px);
        box-shadow: 0 10px 40px rgba(0,113,170,0.1);
    }
    .zoom-action-btn {
        transition: all 0.3s ease;
    }
    .zoom-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .file-item {
        transition: all 0.2s ease;
    }
    .file-item:hover {
        background: #f0f9ff;
        border-color: #0071aa;
    }
    .breadcrumb-item {
        position: relative;
    }
    .breadcrumb-item:not(:last-child)::after {
        content: '/';
        margin: 0 12px;
        color: #9ca3af;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">

    <!-- Hero Section -->
    <div class="session-hero text-white">
        <div class="relative z-10">
            <!-- Top Bar with Breadcrumb and Actions -->
            <div class="px-6 lg:px-12 py-4 border-b border-white/10">
                <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-4">
                    <!-- Breadcrumb -->
                    <nav class="flex items-center text-sm text-blue-100">
                        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-item hover:text-white transition">
                            <svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                            الرئيسية
                        </a>
                        <a href="{{ route('admin.sessions.index') }}" class="breadcrumb-item hover:text-white transition">الدروس</a>
                        <span class="text-white font-medium">{{ Str::limit($session->title, 30) }}</span>
                    </nav>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.sessions.edit', $session) }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-white font-medium transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            تعديل
                        </a>
                        <form action="{{ route('admin.sessions.destroy', $session) }}" method="POST" class="inline"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الدرس؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-500/20 hover:bg-red-500/30 border border-red-400/30 rounded-xl text-white font-medium transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                حذف
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Main Header Content -->
            <div class="px-6 lg:px-12 py-8 lg:py-12">
                <div class="max-w-7xl mx-auto">
                    <!-- Status Badges -->
                    <div class="flex flex-wrap items-center gap-3 mb-6">
                    @if($session->status === 'live')
                        <span class="status-pulse inline-flex items-center gap-2 px-4 py-2 bg-red-500 rounded-full text-sm font-bold">
                            <span class="w-2 h-2 bg-white rounded-full"></span>
                            مباشر الآن
                        </span>
                    @elseif($session->status === 'scheduled')
                        <span class="inline-flex items-center gap-2 px-4 py-2 glass-card rounded-full text-sm font-medium">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            مجدول
                        </span>
                    @elseif($session->status === 'completed')
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-500/20 border border-green-400/30 rounded-full text-sm font-medium text-green-100">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            مكتمل
                        </span>
                    @elseif($session->status === 'cancelled')
                        <span class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500/20 rounded-full text-sm font-medium">
                            ملغي
                        </span>
                    @endif

                    @if($session->is_mandatory)
                        <span class="inline-flex items-center gap-1 px-3 py-2 bg-orange-500/20 border border-orange-400/30 rounded-full text-xs font-medium text-orange-100">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            إلزامي
                        </span>
                    @endif
                    </div>

                    <!-- Title -->
                    <h1 class="text-3xl lg:text-4xl font-bold mb-6">{{ $session->title }}</h1>

                    <!-- Meta Info -->
                    <div class="flex flex-wrap items-center gap-x-8 gap-y-4 text-blue-100">
                        @if($session->subject)
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                                </svg>
                                {{ $session->subject->name }}
                            </span>
                        @endif

                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            {{ $session->duration_minutes ?? $session->duration ?? 0 }} دقيقة
                        </span>

                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                            </svg>
                            @if($session->type === 'live_zoom')
                                بث مباشر Zoom
                            @elseif($session->type === 'recorded_video' || $session->type === 'video')
                                فيديو مسجل
                            @else
                                {{ $session->type }}
                            @endif
                        </span>

                        @if($session->session_number)
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                الدرس رقم {{ $session->session_number }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-6 lg:px-12 pb-12">

        <!-- Zoom Join Card -->
        @if($session->type === 'live_zoom' && $session->zoom_meeting_id)
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-2xl shadow-2xl overflow-hidden">
                <div class="p-8">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M4 4h10v10H4V4m12 0h4v4h-4V4m0 6h4v4h-4v-4m0 6h4v4h-4v-4m-6 0h4v4h-4v-4m-6 0h4v4H4v-4"/>
                                </svg>
                            </div>
                            <div class="text-white">
                                <h3 class="text-xl font-bold mb-1">اجتماع Zoom</h3>
                                <p class="text-blue-100 text-sm">Meeting ID: <span class="font-mono">{{ $session->zoom_meeting_id }}</span></p>
                                @if($session->zoom_password)
                                    <p class="text-blue-100 text-sm">Password: <span class="font-mono">{{ $session->zoom_password }}</span></p>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3 justify-end">
                            <a href="{{ route('admin.sessions.zoom', $session) }}"
                               class="zoom-action-btn inline-flex items-center gap-2 px-6 py-3 bg-white text-blue-700 rounded-xl font-bold shadow-lg hover:bg-blue-50">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 110-2h4a1 1 0 011 1v4a1 1 0 11-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 112 0v1.586l2.293-2.293a1 1 0 011.414 1.414L6.414 15H8a1 1 0 110 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 110-2h1.586l-2.293-2.293a1 1 0 011.414-1.414L15 13.586V12a1 1 0 011-1z" clip-rule="evenodd"/>
                                </svg>
                                شاشة كاملة
                            </a>
                            <a href="{{ route('admin.sessions.zoom-dashboard', $session) }}"
                               class="zoom-action-btn inline-flex items-center gap-2 px-6 py-3 bg-white text-blue-700 rounded-xl font-bold shadow-lg hover:bg-blue-50">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                </svg>
                                مع لوحة التحكم
                            </a>
                            @if($session->zoom_join_url)
                            <a href="{{ $session->zoom_join_url }}" target="_blank"
                               class="zoom-action-btn inline-flex items-center gap-2 px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                فتح في Zoom
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Countdown Timer -->
        @if($session->status === 'scheduled' && $session->scheduled_at && $session->scheduled_at->isFuture())
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">الوقت المتبقي للبدء</h3>
                    <p class="text-gray-500 dark:text-gray-400">{{ $session->scheduled_at->format('l، d F Y - h:i A') }}</p>
                </div>
                <div id="countdown-timer" class="grid grid-cols-4 gap-4 max-w-xl mx-auto">
                    <div class="countdown-item rounded-2xl p-4 text-center">
                        <div class="text-4xl font-bold text-blue-600 dark:text-blue-400" id="days">00</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">يوم</div>
                    </div>
                    <div class="countdown-item rounded-2xl p-4 text-center">
                        <div class="text-4xl font-bold text-purple-600 dark:text-purple-400" id="hours">00</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">ساعة</div>
                    </div>
                    <div class="countdown-item rounded-2xl p-4 text-center">
                        <div class="text-4xl font-bold text-pink-600 dark:text-pink-400" id="minutes">00</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">دقيقة</div>
                    </div>
                    <div class="countdown-item rounded-2xl p-4 text-center">
                        <div class="text-4xl font-bold text-orange-600 dark:text-orange-400" id="seconds">00</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">ثانية</div>
                    </div>
                </div>
                <div id="countdown-message" class="hidden">
                    <div class="bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl p-6 text-center">
                        <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-green-700 dark:text-green-300 mb-2">حان وقت البدء!</h4>
                        <p class="text-green-600 dark:text-green-400">الجلسة جاهزة للانطلاق الآن</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Main Content Column -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Description -->
                @if($session->description)
                <div class="info-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">وصف الدرس</h3>
                    </div>
                    <div class="text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $session->description }}</div>
                </div>
                @endif

                <!-- Video Player -->
                @if(($session->type === 'video' || $session->type === 'recorded_video') && ($session->video_path || $session->video_url))
                <div class="info-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">فيديو الدرس</h3>
                                @if($session->video_duration)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">المدة: {{ floor($session->video_duration / 60) }}:{{ str_pad($session->video_duration % 60, 2, '0', STR_PAD_LEFT) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="aspect-video bg-gray-900">
                        <video controls class="w-full h-full" poster="">
                            <source src="{{ $session->getVideoUrl() }}" type="video/mp4">
                            متصفحك لا يدعم تشغيل الفيديو
                        </video>
                    </div>
                </div>
                @endif

                <!-- Files Section -->
                @php
                    $sessionFiles = $session->files()->get();
                @endphp
                @if($sessionFiles && $sessionFiles->count() > 0)
                <div class="info-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">الملفات المرفقة</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $sessionFiles->count() }} ملف</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @foreach($sessionFiles as $file)
                        <div class="file-item flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-600">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white dark:bg-gray-600 rounded-xl flex items-center justify-center shadow-sm">
                                    @php
                                        $extension = pathinfo($file->original_name ?? $file->file_path, PATHINFO_EXTENSION);
                                        $iconColor = match(strtolower($extension)) {
                                            'pdf' => 'text-red-500',
                                            'doc', 'docx' => 'text-blue-500',
                                            'xls', 'xlsx' => 'text-green-500',
                                            'ppt', 'pptx' => 'text-orange-500',
                                            'zip', 'rar' => 'text-yellow-500',
                                            default => 'text-gray-500'
                                        };
                                    @endphp
                                    <svg class="w-6 h-6 {{ $iconColor }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $file->original_name ?? $file->title ?? 'ملف' }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ strtoupper($extension) }}
                                        @if($file->file_size)
                                            • {{ number_format($file->file_size / 1024, 1) }} KB
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    تحميل
                                </a>
                                <form action="{{ route('admin.sessions.files.delete', $file->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الملف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-2 px-3 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 rounded-lg text-sm font-medium transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Attendance Stats -->
                @if($session->status === 'completed' || $session->status === 'live')
                @php
                    $attendanceRate = $session->getAttendanceRate();
                    $totalAttended = $session->attendances()->where('attended', true)->count();
                    $totalAbsent = $session->attendances()->where('attended', false)->count();
                @endphp
                <div class="info-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">إحصائيات الحضور</h3>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 text-center">
                            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $attendanceRate }}%</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">نسبة الحضور</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 text-center">
                            <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $totalAttended }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">حضور</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 text-center">
                            <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $totalAbsent }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">غياب</div>
                        </div>
                    </div>
                    @if($attendanceRate > 0)
                    <div class="mt-4">
                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-green-500 to-emerald-500 rounded-full transition-all duration-500"
                                 style="width: {{ $attendanceRate }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

            </div>

            <!-- Sidebar -->
            <div class="space-y-6">

                <!-- Quick Info Card -->
                <div class="info-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">معلومات الدرس</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">رقم الدرس</p>
                                <p class="font-semibold text-gray-900 dark:text-white">#{{ $session->session_number ?? $session->id }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">المدة</p>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $session->duration_minutes ?? $session->duration ?? 0 }} دقيقة</p>
                            </div>
                        </div>

                        @if($session->scheduled_at)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">الموعد</p>
                                <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $session->scheduled_at->format('Y/m/d') }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $session->scheduled_at->format('h:i A') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($session->started_at)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">بدأ في</p>
                                <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $session->started_at->format('Y/m/d h:i A') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($session->ended_at)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">انتهى في</p>
                                <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $session->ended_at->format('Y/m/d h:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Subject & Program Info -->
                @if($session->subject)
                <div class="info-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">المادة والبرنامج</h3>
                    <div class="space-y-4">
                        <div class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-100 dark:border-blue-800">
                            <p class="text-xs text-blue-600 dark:text-blue-400 font-medium mb-1">المادة</p>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $session->subject->name }}</p>
                        </div>

                        @if($session->subject->term)
                        <div class="p-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl border border-purple-100 dark:border-purple-800">
                            <p class="text-xs text-purple-600 dark:text-purple-400 font-medium mb-1">الفصل الدراسي</p>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $session->subject->term->name }}</p>
                        </div>
                        @endif

                        @if($session->subject->term && $session->subject->term->program)
                        <div class="p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-100 dark:border-green-800">
                            <p class="text-xs text-green-600 dark:text-green-400 font-medium mb-1">البرنامج</p>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $session->subject->term->program->name }}</p>
                        </div>
                        @endif

                        @if($session->unit)
                        <div class="p-4 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-xl border border-orange-100 dark:border-orange-800">
                            <p class="text-xs text-orange-600 dark:text-orange-400 font-medium mb-1">الوحدة</p>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $session->unit->title }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Quick Actions -->
                <div class="info-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">إجراءات سريعة</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.sessions.edit', $session) }}"
                           class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            تعديل الدرس
                        </a>

                        @if($session->type === 'live_zoom' && $session->zoom_join_url)
                        <a href="{{ $session->zoom_join_url }}" target="_blank"
                           class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                            </svg>
                            انضمام للاجتماع
                        </a>
                        @endif

                        <a href="{{ route('admin.sessions.index') }}"
                           class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-xl font-medium transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                            </svg>
                            العودة للقائمة
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@if($session->status === 'scheduled' && $session->scheduled_at && $session->scheduled_at->isFuture())
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scheduledAt = new Date('{{ $session->scheduled_at->toIso8601String() }}').getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = scheduledAt - now;

        if (distance < 0) {
            document.getElementById('countdown-timer').classList.add('hidden');
            document.getElementById('countdown-message').classList.remove('hidden');
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById('days').textContent = days.toString().padStart(2, '0');
        document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
        document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
        document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
});
</script>
@endif
@endsection
