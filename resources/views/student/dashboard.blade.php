@extends('layouts.dashboard')

@section('title', 'لوحة تحكم الطالب')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(180deg, #0071AA 0%, #005a88 100%);">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=ffffff&color=0071AA&size=80"
                         alt="{{ auth()->user()->name }}"
                         class="w-16 h-16 rounded-2xl border-2 border-white/30 shadow-lg" />
                    <span class="absolute -bottom-1 -right-1 w-5 h-5 border-2 border-white rounded-full" style="background-color: #10b981;"></span>
                </div>
                <div>
                    <p class="text-white/80 text-sm">مرحباً بك</p>
                    <h1 class="text-2xl font-bold">{{ auth()->user()->name }}</h1>
                    @if(auth()->user()->program)
                        <p class="text-white/70 text-sm mt-1">{{ auth()->user()->program->name }}</p>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('student.my-sessions') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium transition-all" style="background-color: rgba(255,255,255,0.2);">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    جلساتي
                </a>
                <a href="{{ route('student.my-program') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium transition-all" style="background-color: rgba(255,255,255,0.2);">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    برنامجي
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #0071AA;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">المواد المسجلة</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['subjects_count'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #0071AA;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #0071AA;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي الجلسات</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_sessions'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #0071AA;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #10b981;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">جلسات مكتملة</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['completed_sessions'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #10b981;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #f59e0b;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">نسبة الحضور</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $overallAttendance }}%</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #f59e0b;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Calendar Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #0071AA;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">التقويم</h3>
                </div>
            </div>
            <div class="p-4">
                @php
                    $currentDate = now();
                    $currentMonth = $currentDate->month;
                    $currentYear = $currentDate->year;
                    $firstDayOfMonth = $currentDate->copy()->startOfMonth();
                    $lastDayOfMonth = $currentDate->copy()->endOfMonth();
                    $startDayOfWeek = $firstDayOfMonth->dayOfWeek;
                    $daysInMonth = $lastDayOfMonth->day;

                    // Get sessions for this month
                    $sessionDates = $upcomingSessions->pluck('scheduled_at')->map(function($date) {
                        return $date ? $date->format('Y-m-d') : null;
                    })->filter()->toArray();

                    $arabicMonths = ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
                    $arabicDays = ['ح', 'ن', 'ث', 'ر', 'خ', 'ج', 'س'];
                @endphp

                <!-- Month Header -->
                <div class="text-center mb-4">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white">{{ $arabicMonths[$currentMonth] }} {{ $currentYear }}</h4>
                </div>

                <!-- Days Header -->
                <div class="grid grid-cols-7 gap-1 mb-2">
                    @foreach($arabicDays as $day)
                        <div class="text-center text-sm font-bold text-gray-600 dark:text-gray-300 py-2">{{ $day }}</div>
                    @endforeach
                </div>

                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 gap-1">
                    @for($i = 0; $i < $startDayOfWeek; $i++)
                        <div class="aspect-square"></div>
                    @endfor

                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $dateString = $currentYear . '-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                            $isToday = ($day == $currentDate->day);
                            $hasSession = in_array($dateString, $sessionDates);
                        @endphp
                        <div class="aspect-square flex items-center justify-center relative">
                            <span class="w-8 h-8 flex items-center justify-center rounded-full text-sm font-medium transition-all
                                @if($isToday)
                                    text-white
                                @else
                                    text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700
                                @endif"
                                style="@if($isToday) background-color: #0071AA; @endif">
                                {{ $day }}
                            </span>
                            @if($hasSession)
                                <span class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-1.5 h-1.5 rounded-full" style="background-color: #10b981;"></span>
                            @endif
                        </div>
                    @endfor
                </div>

                <!-- Legend -->
                <div class="flex items-center justify-center gap-4 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background-color: #0071AA;"></span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">اليوم</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background-color: #10b981;"></span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">جلسة قادمة</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Sessions -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #0071AA;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">الجلسات القادمة</h3>
                </div>
                <a href="{{ route('student.my-sessions') }}" class="text-sm font-medium flex items-center gap-1" style="color: #0071AA;">
                    عرض الكل
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
            </div>

            @if($upcomingSessions->count() > 0)
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($upcomingSessions as $session)
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                                    style="background-color: @if($session->type === 'live_zoom') #0071AA @elseif($session->type === 'recorded') #0071AA @else #0071AA @endif;">
                                    @if($session->type === 'live_zoom')
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-900 dark:text-white truncate">{{ $session->title }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $session->subject->name }}</p>
                                    @if($session->scheduled_at)
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs px-2 py-1 rounded-lg" style="background-color: #e6f4fa; color: #0071AA;">
                                                {{ $session->scheduled_at->format('Y-m-d') }}
                                            </span>
                                            <span class="text-xs px-2 py-1 rounded-lg" style="background-color: #d1fae5; color: #047857;">
                                                {{ $session->scheduled_at->format('H:i') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                @if($session->type === 'live_zoom' && $session->zoom_meeting_id)
                                    <a href="{{ route('student.sessions.join-zoom', $session->id) }}"
                                       class="inline-flex items-center px-4 py-2 text-white font-medium rounded-xl text-sm"
                                       style="background-color: #0071AA;">
                                        انضم
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400">لا توجد جلسات قادمة</p>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Live Sessions -->
        @if($liveSessions->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden" style="border: 2px solid #ef4444;">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700" style="background-color: #fef2f2;">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #ef4444;">
                            <span class="flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-3 w-3 rounded-full opacity-75" style="background-color: white;"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                            </span>
                        </div>
                        <h3 class="text-lg font-bold" style="color: #dc2626;">جلسات مباشرة الآن</h3>
                    </div>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($liveSessions as $session)
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white">{{ $session->title }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $session->subject->name }}</p>
                                </div>
                                <a href="{{ route('student.sessions.join-zoom', $session->id) }}"
                                   class="inline-flex items-center px-5 py-2.5 text-white font-bold rounded-xl transition-all animate-pulse"
                                   style="background-color: #ef4444;">
                                    <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    انضم الآن
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- My Subjects -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden {{ $liveSessions->count() == 0 ? 'lg:col-span-2' : '' }}">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #10b981;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">موادي</h3>
                </div>
                <a href="{{ route('student.my-program') }}" class="text-sm font-medium flex items-center gap-1" style="color: #0071AA;">
                    عرض الكل
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
            </div>

            @if($subjects->count() > 0)
                <div class="p-4 course-grid">
                    @foreach($subjects->take(4) as $index => $subject)
                        @php
                            $colors = ['blue', 'green', 'orange', 'purple'];
                            $colorClass = $colors[$index % count($colors)];
                            $progress = isset($subjectsProgress[$subject->id]) ? $subjectsProgress[$subject->id]['percentage'] : 0;

                            // Use subject color if available
                            $customStyle = '';
                            if($subject->color) {
                                $customStyle = 'background: linear-gradient(135deg, ' . $subject->color . ' 0%, ' . $subject->color . 'dd 100%);';
                                $colorClass = ''; // Clear the color class
                            }
                        @endphp
                        <div class="course-card">
                            <!-- Course Image -->
                            <div class="course-card-image {{ $colorClass }}" @if($customStyle) style="{{ $customStyle }}" @endif>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>

                            <!-- Course Content -->
                            <div class="course-card-content">
                                <h3 class="course-card-title">{{ $subject->name }}</h3>
                                @if($subject->teacher)
                                    <p class="course-card-subtitle">
                                        <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ $subject->teacher->name }}
                                    </p>
                                @endif

                                <!-- Tags -->
                                <div class="course-card-tags">
                                    <span class="course-tag registered">مسجلة</span>
                                    @if($progress >= 50)
                                        <span class="course-tag active">نشطة</span>
                                    @endif
                                </div>

                                <!-- Progress Bar -->
                                @if(isset($subjectsProgress[$subject->id]))
                                    <div class="course-progress">
                                        <div class="course-progress-label">
                                            <span class="course-progress-text">نسبة الإنجاز</span>
                                            <span class="course-progress-percent">{{ $progress }}%</span>
                                        </div>
                                        <div class="course-progress-bar">
                                            <div class="course-progress-fill" style="width: {{ $progress }}%;"></div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Action Button -->
                                <a href="{{ route('student.subjects.show', $subject->id) }}" class="course-action-button">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    عرض التفاصيل
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400">لا توجد مواد مسجلة</p>
                    <a href="{{ route('student.my-program') }}" class="inline-flex items-center mt-4 px-4 py-2 text-white font-medium rounded-xl text-sm" style="background-color: #0071AA;">
                        تسجيل في برنامج
                    </a>
                </div>
            @endif
        </div>

        <!-- Support Tickets -->
        @if($liveSessions->count() == 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #f59e0b;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">تذاكر الدعم</h3>
                        @if($openTicketsCount > 0)
                            <span class="text-xs px-2 py-0.5 rounded-full text-white" style="background-color: #f59e0b;">{{ $openTicketsCount }} مفتوحة</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('student.tickets.index') }}" class="text-sm font-medium flex items-center gap-1" style="color: #0071AA;">
                    عرض الكل
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
            </div>

            @if($myTickets->count() > 0)
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($myTickets->take(3) as $ticket)
                        <a href="{{ route('student.tickets.show', $ticket->id) }}" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white text-sm">{{ Str::limit($ticket->subject, 40) }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $ticket->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="px-2 py-1 rounded-lg text-xs font-medium"
                                    style="@if($ticket->status === 'open') background-color: #dbeafe; color: #1d4ed8; @elseif($ticket->status === 'in_progress') background-color: #fef3c7; color: #b45309; @else background-color: #d1fae5; color: #047857; @endif">
                                    @if($ticket->status === 'open') مفتوحة @elseif($ticket->status === 'in_progress') قيد المعالجة @else مغلقة @endif
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">لا توجد تذاكر</p>
                </div>
            @endif

            <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('student.tickets.create') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 text-white font-medium rounded-xl text-sm" style="background-color: #0071AA;">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    إنشاء تذكرة جديدة
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
