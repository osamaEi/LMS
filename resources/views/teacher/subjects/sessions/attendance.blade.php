@extends('layouts.dashboard')

@section('title', 'حضور الحصة - ' . $session->title)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="text-sm">
        <ol class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('teacher.my-subjects.index') }}" class="hover:text-brand-500">موادي</a></li>
            <li>/</li>
            <li><a href="{{ route('teacher.my-subjects.show', $subject->id) }}" class="hover:text-brand-500">{{ $subject->name }}</a></li>
            <li>/</li>
            <li class="text-gray-900 dark:text-white">حضور الحصة {{ $session->session_number }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(180deg, #0071AA 0%, #0071AA 100%);">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background-color: rgba(255,255,255,0.2);">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">{{ $session->title }}</h1>
                    <p class="mt-1 opacity-90">الحصة {{ $session->session_number }} - {{ $subject->name }}</p>
                    @if($session->scheduled_at)
                        <p class="text-sm opacity-75 mt-1">
                            <svg class="inline w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d - h:i A') }}
                        </p>
                    @endif
                </div>
            </div>
            <a href="{{ route('teacher.my-subjects.show', $subject->id) }}"
               class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium transition-all" style="background-color: rgba(255,255,255,0.2);">
                <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                العودة للمادة
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #3b82f6;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">إجمالي المسجلين</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_enrolled'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #3b82f6;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #10b981;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">الحاضرون</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['attended'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #10b981;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #ef4444;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">الغائبون</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['absent'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #ef4444;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #0071AA;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">نسبة الحضور</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['attendance_rate'] }}%</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #0071AA;">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Attended Students -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #10b981;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">الطلاب الحاضرون</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $attendances->where('attended', true)->count() }} طالب</p>
                </div>
            </div>
        </div>

        @if($attendances->where('attended', true)->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">الطالب</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">وقت الانضمام</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">وقت المغادرة</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">المدة</th>
                        @if($session->type === 'recorded_video')
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">نسبة المشاهدة</th>
                        @endif
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances->where('attended', true) as $index => $attendance)
                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($attendance->student->name ?? 'N/A') }}&background=4f46e5&color=fff&size=40"
                                     alt="{{ $attendance->student->name ?? 'N/A' }}"
                                     class="w-10 h-10 rounded-full" />
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $attendance->student->name ?? 'غير معروف' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $attendance->student->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            @if($attendance->joined_at)
                                {{ \Carbon\Carbon::parse($attendance->joined_at)->format('h:i A') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            @if($attendance->left_at)
                                {{ \Carbon\Carbon::parse($attendance->left_at)->format('h:i A') }}
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                            @if($attendance->duration_minutes)
                                {{ $attendance->duration_minutes }} دقيقة
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        @if($session->type === 'recorded_video')
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden" style="max-width: 100px;">
                                    <div class="h-full rounded-full" style="width: {{ $attendance->watch_percentage ?? 0 }}%; background-color: #10b981;"></div>
                                </div>
                                <span class="text-gray-900 dark:text-white">{{ $attendance->watch_percentage ?? 0 }}%</span>
                            </div>
                        </td>
                        @endif
                        <td class="px-6 py-4 text-sm">
                            @if($attendance->video_completed || ($attendance->duration_minutes && $attendance->duration_minutes >= ($session->duration_minutes * 0.8)))
                                <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium" style="background-color: #d1fae5; color: #065f46;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    حضور كامل
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium" style="background-color: #fef3c7; color: #92400e;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    حضور جزئي
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background-color: #fef3c7;">
                <svg class="w-8 h-8" style="color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">لا يوجد حضور بعد</h3>
            <p class="mt-2 text-gray-500 dark:text-gray-400">لم يحضر أي طالب هذه الحصة حتى الآن</p>
        </div>
        @endif
    </div>

    <!-- Absent Students -->
    @if($absentStudents->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #ef4444;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">الطلاب الغائبون</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $absentStudents->count() }} طالب</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($absentStudents as $student)
                <div class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name ?? 'N/A') }}&background=ef4444&color=fff&size=40"
                         alt="{{ $student->name ?? 'N/A' }}"
                         class="w-10 h-10 rounded-full" />
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $student->name ?? 'غير معروف' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $student->email ?? '' }}</p>
                    </div>
                    <span class="ms-auto inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium" style="background-color: #fee2e2; color: #991b1b;">
                        غائب
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
