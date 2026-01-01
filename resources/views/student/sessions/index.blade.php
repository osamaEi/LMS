@extends('layouts.dashboard')

@section('title', 'جلساتي')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="rounded-2xl p-6 shadow-lg" style="background: linear-gradient(180deg, #2d4a6f 0%, #1e3a5f 100%);">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background-color: rgba(255,255,255,0.2);">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">جلساتي</h1>
                <p class="mt-1" style="color: rgba(255,255,255,0.8);">جميع الجلسات والمحاضرات المتاحة لك</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Sessions -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #3b82f6;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي الجلسات</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalSessions }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #3b82f6;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completed Sessions -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #10b981;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">جلسات مكتملة</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $completedSessions }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #10b981;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Zoom Sessions -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #06b6d4;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">جلسات Zoom</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $zoomSessions }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #06b6d4;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Upcoming Sessions -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #f59e0b;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">جلسات قادمة</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalSessions - $completedSessions }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #f59e0b;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5">
        <form method="GET" action="{{ route('student.my-sessions') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="subject_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المادة</label>
                <select name="subject_id" id="subject_id" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">جميع المواد</option>
                    @foreach($enrolledSubjects as $subject)
                        <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نوع الجلسة</label>
                <select name="type" id="type" class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">جميع الأنواع</option>
                    <option value="live_zoom" {{ $type == 'live_zoom' ? 'selected' : '' }}>Zoom مباشر</option>
                    <option value="recorded" {{ $type == 'recorded' ? 'selected' : '' }}>مسجل</option>
                    <option value="in_person" {{ $type == 'in_person' ? 'selected' : '' }}>حضوري</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-6 py-2.5 text-white font-medium rounded-xl transition-all flex items-center gap-2" style="background-color: #4f46e5;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    تصفية
                </button>
            </div>
        </form>
    </div>

    <!-- Sessions List -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #4f46e5;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">قائمة الجلسات</h2>
            </div>
        </div>

        @if($sessions->count() > 0)
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($sessions as $session)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                            <div class="flex items-start gap-4">
                                <!-- Session Icon -->
                                <div class="relative flex-shrink-0">
                                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                                        style="background-color: @if($session->type === 'live_zoom') #06b6d4 @elseif($session->type === 'recorded') #8b5cf6 @else #f59e0b @endif;">
                                        @if($session->type === 'live_zoom')
                                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                        @elseif($session->type === 'recorded')
                                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @else
                                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    @if($session->started_at && !$session->ended_at)
                                        <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75" style="background-color: #f87171;"></span>
                                            <span class="relative inline-flex rounded-full h-4 w-4 border-2 border-white dark:border-gray-800" style="background-color: #ef4444;"></span>
                                        </span>
                                    @endif
                                </div>

                                <!-- Session Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ $session->title }}</h3>
                                        @if($session->started_at && !$session->ended_at)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold text-white" style="background-color: #ef4444;">
                                                <span class="w-1.5 h-1.5 bg-white rounded-full me-1.5 animate-pulse"></span>
                                                مباشر الآن
                                            </span>
                                        @elseif($session->ended_at)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium" style="background-color: #d1fae5; color: #047857;">
                                                <svg class="w-3 h-3 me-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                مكتمل
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium" style="background-color: #e0e7ff; color: #4338ca;">
                                            {{ $session->subject->name }}
                                        </span>
                                        @if($session->unit)
                                            <span class="text-gray-400 dark:text-gray-500">•</span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $session->unit->title }}</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 mt-3">
                                        @if($session->scheduled_at)
                                            <span class="inline-flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-lg">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                {{ $session->scheduled_at->format('Y-m-d') }}
                                            </span>
                                            <span class="inline-flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-lg">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $session->scheduled_at->format('H:i') }}
                                            </span>
                                        @endif
                                        @if($session->duration_minutes)
                                            <span class="inline-flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 px-3 py-1.5 rounded-lg">
                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                </svg>
                                                {{ $session->duration_minutes }} دقيقة
                                            </span>
                                        @endif
                                        @if($session->type === 'live_zoom')
                                            <span class="px-3 py-1.5 rounded-lg text-xs font-semibold" style="background-color: #cffafe; color: #0e7490;">Zoom</span>
                                        @elseif($session->type === 'recorded')
                                            <span class="px-3 py-1.5 rounded-lg text-xs font-semibold" style="background-color: #ede9fe; color: #6d28d9;">مسجل</span>
                                        @else
                                            <span class="px-3 py-1.5 rounded-lg text-xs font-semibold" style="background-color: #fef3c7; color: #b45309;">حضوري</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <!-- Attendance Status -->
                                @if(isset($attendances[$session->id]))
                                    @if($attendances[$session->id]->attended)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold text-white" style="background-color: #10b981;">
                                            <svg class="w-3.5 h-3.5 me-1.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            حضرت
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold text-white" style="background-color: #ef4444;">
                                            <svg class="w-3.5 h-3.5 me-1.5" fill="currentColor" viewBox="0 0 20 20">
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
                                               class="inline-flex items-center px-5 py-2.5 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl"
                                               style="background-color: #ef4444;">
                                                <svg class="w-5 h-5 me-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                                انضم الآن
                                            </a>
                                        @elseif(!$session->ended_at && $session->zoom_meeting_id)
                                            {{-- Session has Zoom but not started yet --}}
                                            @if($session->scheduled_at && $session->scheduled_at > now())
                                                <span class="text-sm font-semibold px-4 py-2 rounded-xl"
                                                    style="@if($session->scheduled_at->isToday()) background-color: #cffafe; color: #0e7490; @elseif($session->scheduled_at->isTomorrow()) background-color: #fef3c7; color: #b45309; @else background-color: #f3f4f6; color: #4b5563; @endif">
                                                    @if($session->scheduled_at->isToday())
                                                        اليوم {{ $session->scheduled_at->format('H:i') }}
                                                    @elseif($session->scheduled_at->isTomorrow())
                                                        غداً {{ $session->scheduled_at->format('H:i') }}
                                                    @else
                                                        {{ $session->scheduled_at->diffForHumans() }}
                                                    @endif
                                                </span>
                                            @endif
                                            <a href="{{ route('student.sessions.join-zoom', $session->id) }}"
                                               class="inline-flex items-center px-5 py-2.5 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl"
                                               style="background-color: #06b6d4;">
                                                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                                انضم للجلسة
                                            </a>
                                        @elseif($session->ended_at && $session->zoom_recording_url)
                                            {{-- Session ended but has recording --}}
                                            <a href="{{ $session->zoom_recording_url }}" target="_blank"
                                               class="inline-flex items-center px-5 py-2.5 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl"
                                               style="background-color: #8b5cf6;">
                                                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                مشاهدة التسجيل
                                            </a>
                                        @endif

                                        {{-- External Zoom Link --}}
                                        @if($session->zoom_join_url && !$session->ended_at)
                                            <a href="{{ $session->zoom_join_url }}" target="_blank"
                                               class="inline-flex items-center px-4 py-2.5 text-white font-semibold rounded-xl transition-all"
                                               style="background-color: #2563eb;"
                                               title="فتح في تطبيق Zoom">
                                                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                                فتح Zoom
                                            </a>
                                        @endif
                                    @endif

                                    <!-- Watch Recording Button for recorded sessions -->
                                    @if($session->type === 'recorded' && $session->video_url)
                                        <a href="{{ $session->video_url }}" target="_blank"
                                           class="inline-flex items-center px-5 py-2.5 text-white font-bold rounded-xl transition-all shadow-lg hover:shadow-xl"
                                           style="background-color: #8b5cf6;">
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
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    {{ $sessions->withQueryString()->links() }}
                </div>
            @endif
        @else
            <div class="p-16 text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">لا توجد جلسات</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-sm mx-auto">سيتم عرض الجلسات هنا عند توفرها. تابع صفحتك للحصول على التحديثات.</p>
            </div>
        @endif
    </div>
</div>
@endsection
