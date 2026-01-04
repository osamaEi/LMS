@extends('layouts.dashboard')

@section('title', 'سجل الحضور')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header with Profile -->
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
                    <p class="text-white/80 text-sm">سجل الحضور</p>
                    <h1 class="text-2xl font-bold">{{ auth()->user()->name }}</h1>
                    @if(auth()->user()->studentId)
                        <p class="text-white/70 text-sm mt-1">رقم الطالب: {{ auth()->user()->studentId }}</p>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('student.dashboard') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium transition-all" style="background-color: rgba(255,255,255,0.2);">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    لوحة التحكم
                </a>
                <a href="{{ route('student.my-sessions') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium transition-all" style="background-color: rgba(255,255,255,0.2);">
                    <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    جلساتي
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #0071AA;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي الجلسات</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalSessions }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #0071AA;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #10b981;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">الجلسات المحضورة</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $attendedSessions }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #10b981;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #ef4444;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">الجلسات الغائبة</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalSessions - $attendedSessions }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #ef4444;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #f59e0b;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">نسبة الحضور</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $attendanceRate }}%</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #f59e0b;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Weekly Schedule Table -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #0071AA;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">جدول هذا الأسبوع</h3>
                </div>
                <a href="{{ route('student.my-sessions') }}" class="text-sm font-medium flex items-center gap-1 px-4 py-2 rounded-xl" style="background-color: #e6f4fa; color: #0071AA;">
                    عرض الحضور الكامل
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50">
                            <th class="px-6 py-4 text-right text-sm font-bold text-gray-700 dark:text-gray-300">اليوم</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-gray-700 dark:text-gray-300">المادة / الدورة</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-gray-700 dark:text-gray-300">نوع الجلسة</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-gray-700 dark:text-gray-300">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @php
                            $arabicDays = [
                                'Sunday' => 'الأحد',
                                'Monday' => 'الاثنين',
                                'Tuesday' => 'الثلاثاء',
                                'Wednesday' => 'الأربعاء',
                                'Thursday' => 'الخميس',
                                'Friday' => 'الجمعة',
                                'Saturday' => 'السبت'
                            ];
                        @endphp
                        @forelse($attendances->take(7) as $attendance)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $arabicDays[$attendance->session->scheduled_at?->format('l') ?? $attendance->created_at->format('l')] ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $attendance->session->subject->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $attendance->session->title }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($attendance->session->type === 'live_zoom')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium" style="background-color: #dbeafe; color: #1d4ed8;">
                                            أونلاين
                                        </span>
                                    @elseif($attendance->session->type === 'recorded')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium" style="background-color: #e0e7ff; color: #4338ca;">
                                            مسجّلة
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium" style="background-color: #fef3c7; color: #b45309;">
                                            حضوري
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($attendance->attended)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium" style="background-color: #d1fae5; color: #047857;">
                                            <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            تم الحضور
                                        </span>
                                    @elseif($attendance->session->scheduled_at && $attendance->session->scheduled_at->isFuture())
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium" style="background-color: #e6f4fa; color: #0071AA;">
                                            <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            لم يبدأ بعد
                                        </span>
                                    @elseif($attendance->video_completed)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium" style="background-color: #d1fae5; color: #047857;">
                                            <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                            </svg>
                                            شاهدة الآن
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium" style="background-color: #fee2e2; color: #dc2626;">
                                            <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            غياب
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400">لا يوجد سجلات حضور</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sidebar - Profile Info & Filter -->
        <div class="space-y-6">
            <!-- Student Profile Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 text-center border-b border-gray-100 dark:border-gray-700">
                    <div class="relative inline-block">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0071AA&color=ffffff&size=120"
                             alt="{{ auth()->user()->name }}"
                             class="w-24 h-24 rounded-2xl mx-auto shadow-lg" />
                        <span class="absolute -bottom-1 -right-1 w-6 h-6 border-3 border-white rounded-full" style="background-color: #10b981;"></span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-4">{{ auth()->user()->name }}</h3>
                </div>
                <div class="p-4 space-y-3">
                    @if(auth()->user()->studentId)
                    <div class="flex items-center gap-3 text-sm">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                        <span class="text-gray-500 dark:text-gray-400">رقم الهوية</span>
                        <span class="text-gray-900 dark:text-white font-medium me-auto">{{ auth()->user()->studentId }}</span>
                    </div>
                    @endif
                    <div class="flex items-center gap-3 text-sm">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-gray-500 dark:text-gray-400">تاريخ الالتحاق</span>
                        <span class="text-gray-900 dark:text-white font-medium me-auto">{{ auth()->user()->created_at->format('d M Y') }}</span>
                    </div>
                    @if(auth()->user()->email)
                    <div class="flex items-center gap-3 text-sm">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-gray-500 dark:text-gray-400">البريد الإلكتروني</span>
                        <span class="text-gray-900 dark:text-white font-medium me-auto text-xs">{{ auth()->user()->email }}</span>
                    </div>
                    @endif
                    @if(auth()->user()->program)
                    <div class="flex items-center gap-3 text-sm">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="text-gray-500 dark:text-gray-400">البرنامج المنضم له</span>
                        <span class="text-gray-900 dark:text-white font-medium me-auto">{{ auth()->user()->program->name }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Filter by Subject -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #0071AA;">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">تصفية حسب المادة</h3>
                    </div>
                </div>
                <div class="p-4">
                    <form method="GET" action="{{ route('student.attendance') }}" class="space-y-4">
                        <div>
                            <select name="subject_id" id="subject_id" class="w-full rounded-xl border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">جميع المواد</option>
                                @foreach($enrolledSubjects as $subject)
                                    <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full px-4 py-2.5 text-white font-medium rounded-xl transition-all" style="background-color: #0071AA;">
                            <svg class="w-4 h-4 inline me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            تصفية
                        </button>
                    </form>
                </div>
            </div>

            <!-- Attendance Progress -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #10b981;">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">معدل الحضور</h3>
                    </div>
                </div>
                <div class="p-5">
                    <div class="relative pt-1">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">نسبة الحضور الكلية</span>
                            <span class="text-lg font-bold" style="color: {{ $attendanceRate >= 75 ? '#10b981' : ($attendanceRate >= 50 ? '#f59e0b' : '#ef4444') }};">{{ $attendanceRate }}%</span>
                        </div>
                        <div class="w-full h-3 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-3 rounded-full transition-all duration-500" style="width: {{ $attendanceRate }}%; background-color: {{ $attendanceRate >= 75 ? '#10b981' : ($attendanceRate >= 50 ? '#f59e0b' : '#ef4444') }};"></div>
                        </div>
                        <div class="mt-4 flex items-center justify-center gap-4">
                            <div class="text-center">
                                <span class="block text-2xl font-bold text-gray-900 dark:text-white">{{ $attendedSessions }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">حاضر</span>
                            </div>
                            <div class="w-px h-8 bg-gray-200 dark:bg-gray-700"></div>
                            <div class="text-center">
                                <span class="block text-2xl font-bold text-gray-900 dark:text-white">{{ $totalSessions - $attendedSessions }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">غائب</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Attendance History -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #0071AA;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">سجل الحضور الكامل</h3>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700/50">
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-700 dark:text-gray-300">الجلسة</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-700 dark:text-gray-300">المادة</th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-gray-700 dark:text-gray-300">التاريخ</th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-gray-700 dark:text-gray-300">الحالة</th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-gray-700 dark:text-gray-300">المدة</th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-gray-700 dark:text-gray-300">نسبة المشاهدة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($attendances as $attendance)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $attendance->session->title }}
                                </div>
                                @if($attendance->session->unit)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                        {{ $attendance->session->unit->title }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $attendance->session->subject->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $attendance->session->scheduled_at?->format('Y-m-d') ?? $attendance->created_at->format('Y-m-d') }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $attendance->joined_at?->format('H:i') ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($attendance->attended)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium" style="background-color: #d1fae5; color: #047857;">
                                        <svg class="w-4 h-4 me-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        حاضر
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium" style="background-color: #fee2e2; color: #dc2626;">
                                        <svg class="w-4 h-4 me-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        غائب
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    @if($attendance->duration_minutes)
                                        {{ $attendance->duration_minutes }} دقيقة
                                    @else
                                        -
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($attendance->watch_percentage)
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-16 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-2 rounded-full" style="width: {{ min($attendance->watch_percentage, 100) }}%; background-color: #10b981;"></div>
                                        </div>
                                        <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $attendance->watch_percentage }}%</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">لا يوجد سجلات حضور</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($attendances->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
