@extends('layouts.dashboard')

@section('title', 'برنامجي الدراسي')

@section('content')
<div class="space-y-6">
    @if($program)
        <!-- Header -->
        <div class="rounded-2xl p-6 shadow-lg text-white" style="background: linear-gradient(180deg, #0071AA 0%, #005a88 100%);">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center" style="background-color: rgba(255,255,255,0.2);">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">{{ $program->name }}</h1>
                        <p class="mt-1 opacity-90">{{ $program->description ?? 'برنامجك الدراسي الحالي' }}</p>
                        @if($track)
                            <span class="inline-flex items-center mt-2 px-3 py-1 rounded-full text-sm font-medium" style="background-color: rgba(255,255,255,0.2);">
                                <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                                المسار: {{ $track->name }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <div class="text-center px-6 py-3 rounded-xl" style="background-color: rgba(255,255,255,0.2);">
                        <p class="text-sm opacity-90">الفصل الحالي</p>
                        <p class="text-3xl font-bold">{{ $currentTermNumber ?? 1 }} / {{ $stats['total_terms'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm">التقدم في البرنامج</span>
                    <span class="text-sm font-bold">{{ $stats['progress_percentage'] ?? 0 }}%</span>
                </div>
                <div class="w-full h-3 rounded-full" style="background-color: rgba(255,255,255,0.3);">
                    <div class="h-3 rounded-full transition-all duration-500" style="width: {{ $stats['progress_percentage'] ?? 0 }}%; background-color: #10b981;"></div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Subjects -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #3b82f6;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">المواد المسجلة</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_subjects'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #3b82f6;">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Sessions -->
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

            <!-- Completed Sessions -->
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

            <!-- Attendance Rate -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5" style="border-right: 4px solid #f59e0b;">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">نسبة الحضور</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['attendance_rate'] }}%</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #f59e0b;">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Program Info & Terms Timeline -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Program Details -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #0071AA;">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">معلومات البرنامج</h2>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-400">اسم البرنامج</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $program->name }}</span>
                    </div>
                    @if($program->code)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-400">رمز البرنامج</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $program->code }}</span>
                    </div>
                    @endif
                    @if($program->duration_months)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-400">مدة البرنامج</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $program->duration_months }} شهر</span>
                    </div>
                    @endif
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-400">عدد الفصول</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $stats['total_terms'] }} فصول</span>
                    </div>
                    @if($track)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-400">المسار</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $track->name }}</span>
                    </div>
                    @endif
                    <div class="flex items-center justify-between py-3">
                        <span class="text-gray-500 dark:text-gray-400">الحالة</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold text-white" style="background-color: #10b981;">
                            نشط
                        </span>
                    </div>
                </div>
            </div>

            <!-- Terms Timeline -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #0071AA;">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">الفصول الدراسية</h2>
                    </div>
                </div>
                <div class="p-6">
                    @if($terms->count() > 0)
                        <div class="space-y-4">
                            @foreach($terms as $term)
                                @php
                                    $isCurrentTerm = ($term->term_number == $currentTermNumber);
                                    $isPastTerm = ($term->term_number < $currentTermNumber);
                                    $isFutureTerm = ($term->term_number > $currentTermNumber);
                                @endphp
                                <div class="relative flex items-start gap-4 p-4 rounded-xl transition-all
                                    @if($isCurrentTerm)
                                        border-2
                                    @else
                                        border border-gray-200 dark:border-gray-700
                                    @endif
                                "
                                style="@if($isCurrentTerm) border-color: #0071AA; background-color: rgba(139, 92, 246, 0.05); @endif">
                                    <!-- Term Number Circle -->
                                    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg"
                                        style="background-color: @if($isPastTerm) #10b981 @elseif($isCurrentTerm) #0071AA @else #d1d5db @endif;">
                                        {{ $term->term_number }}
                                    </div>

                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h3 class="font-bold text-gray-900 dark:text-white">{{ $term->name }}</h3>
                                                <div class="flex items-center gap-3 mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    @if($term->start_date && $term->end_date)
                                                        <span class="flex items-center gap-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                            </svg>
                                                            {{ $term->start_date->format('Y/m/d') }} - {{ $term->end_date->format('Y/m/d') }}
                                                        </span>
                                                    @endif
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                        </svg>
                                                        {{ $term->subjects->count() }} مادة
                                                    </span>
                                                </div>
                                            </div>
                                            <div>
                                                @if($isPastTerm)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold text-white" style="background-color: #10b981;">
                                                        <svg class="w-3 h-3 me-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                        مكتمل
                                                    </span>
                                                @elseif($isCurrentTerm)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold text-white" style="background-color: #0071AA;">
                                                        <span class="w-2 h-2 bg-white rounded-full me-1.5 animate-pulse"></span>
                                                        الفصل الحالي
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                        قادم
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <p>لا توجد فصول دراسية مسجلة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Enrolled Subjects -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #0071AA;">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">المواد المسجلة</h2>
                    </div>
                    <a href="{{ route('student.my-sessions') }}" class="text-sm font-medium flex items-center gap-1" style="color: #0071AA;">
                        عرض جميع الجلسات
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                </div>
            </div>

            @if($subjects->count() > 0)
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($subjects as $subject)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <!-- Subject Icon -->
                                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0" style="background-color: #0071AA;">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>

                                    <!-- Subject Info -->
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ $subject->name }}</h3>
                                        <div class="flex flex-wrap items-center gap-3 mt-2">
                                            @if($subject->teacher)
                                                <span class="inline-flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-300">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                    {{ $subject->teacher->name }}
                                                </span>
                                            @endif
                                            @if($subject->term)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium" style="background-color: #e6f4fa; color: #0071AA;">
                                                    {{ $subject->term->name }}
                                                </span>
                                            @endif
                                            <span class="inline-flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-300">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                                {{ $subject->sessions_count }} جلسة
                                            </span>
                                        </div>

                                        <!-- Progress Bar -->
                                        @if(isset($subjectsProgress[$subject->id]))
                                            <div class="mt-3">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">نسبة الحضور</span>
                                                    <span class="text-xs font-bold" style="color: #10b981;">{{ $subjectsProgress[$subject->id]['percentage'] }}%</span>
                                                </div>
                                                <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                                    <div class="h-2 rounded-full transition-all duration-500" style="width: {{ $subjectsProgress[$subject->id]['percentage'] }}%; background-color: #10b981;"></div>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ $subjectsProgress[$subject->id]['attended'] }} من {{ $subjectsProgress[$subject->id]['total'] }} جلسة
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('student.subjects.show', $subject->id) }}"
                                       class="inline-flex items-center px-4 py-2 text-white font-medium rounded-xl transition-all"
                                       style="background-color: #0071AA;">
                                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        عرض التفاصيل
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-16 text-center">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">لا توجد مواد مسجلة</h3>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">سيتم عرض المواد هنا بعد التسجيل</p>
                </div>
            @endif
        </div>
    @else
        <!-- No Program Assigned - Show Available Programs -->
        <div class="space-y-8">
            <!-- Welcome Header -->
            <div class="rounded-2xl p-8 text-white" style="background: linear-gradient(180deg, #2d4a6f 0%, #1e3a5f 100%);">
                <div class="max-w-3xl mx-auto text-center">
                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6" style="background-color: rgba(255,255,255,0.2);">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold mb-4">مرحباً بك في نظام التعلم!</h1>
                    <p class="text-xl opacity-90 mb-2">اختر البرنامج الدراسي المناسب لك وابدأ رحلتك التعليمية</p>
                    <p class="text-sm opacity-75">يمكنك الاختيار من بين البرامج المتاحة أدناه أو التواصل مع الدعم للمساعدة</p>
                </div>
            </div>

            @if(session('success'))
                <div class="rounded-xl p-4" style="background-color: #d1fae5;">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" style="color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span style="color: #065f46;">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-xl p-4" style="background-color: #fee2e2;">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" style="color: #dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span style="color: #991b1b;">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Available Programs Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: #0071AA;">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">البرامج الدراسية المتاحة</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">اختر البرنامج الذي يناسب أهدافك التعليمية</p>
                        </div>
                    </div>
                </div>

                @if(isset($availablePrograms) && $availablePrograms->count() > 0)
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($availablePrograms as $availableProgram)
                                <div class="rounded-2xl border-2 border-gray-200 dark:border-gray-700 hover:border-indigo-500 transition-all duration-300 overflow-hidden group">
                                    <!-- Program Header -->
                                    <div class="p-6" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
                                        <div class="flex items-center gap-3">
                                            <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background-color: rgba(255,255,255,0.2);">
                                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-lg font-bold text-white">{{ $availableProgram->name }}</h3>
                                                @if($availableProgram->code)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background-color: rgba(255,255,255,0.2); color: white;">
                                                        {{ $availableProgram->code }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Program Details -->
                                    <div class="p-6 bg-white dark:bg-gray-800">
                                        @if($availableProgram->description)
                                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-3">{{ $availableProgram->description }}</p>
                                        @endif

                                        <div class="space-y-3 mb-6">
                                            @if($availableProgram->duration_months)
                                                <div class="flex items-center gap-3 text-sm">
                                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: #dbeafe;">
                                                        <svg class="w-4 h-4" style="color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </div>
                                                    <span class="text-gray-600 dark:text-gray-300">المدة: <strong class="text-gray-900 dark:text-white">{{ $availableProgram->duration_months }} شهر</strong></span>
                                                </div>
                                            @endif

                                            <div class="flex items-center gap-3 text-sm">
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: #d1fae5;">
                                                    <svg class="w-4 h-4" style="color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                                <span class="text-gray-600 dark:text-gray-300">الفصول: <strong class="text-gray-900 dark:text-white">{{ $availableProgram->terms_count }} فصل</strong></span>
                                            </div>

                                            @if($availableProgram->price)
                                                <div class="flex items-center gap-3 text-sm">
                                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: #fef3c7;">
                                                        <svg class="w-4 h-4" style="color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </div>
                                                    <span class="text-gray-600 dark:text-gray-300">السعر: <strong class="text-gray-900 dark:text-white">{{ number_format($availableProgram->price, 2) }} ر.س</strong></span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Enroll Button -->
                                        <form action="{{ route('student.enroll-program') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="program_id" value="{{ $availableProgram->id }}">
                                            <button type="submit"
                                                    class="w-full inline-flex items-center justify-center px-6 py-3 text-white font-bold rounded-xl transition-all hover:shadow-lg"
                                                    style="background-color: #0071AA;">
                                                <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                التسجيل في هذا البرنامج
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <!-- No Programs Available -->
                    <div class="p-16 text-center">
                        <div class="w-24 h-24 rounded-2xl flex items-center justify-center mx-auto mb-6" style="background-color: #fef3c7;">
                            <svg class="w-12 h-12" style="color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">لا توجد برامج متاحة حالياً</h3>
                        <p class="mt-3 text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                            لا توجد برامج دراسية متاحة للتسجيل في الوقت الحالي. يرجى التواصل مع الإدارة للحصول على المزيد من المعلومات.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Help Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #10b981;">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">هل تحتاج مساعدة؟</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-start gap-4 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: #dbeafe;">
                                <svg class="w-6 h-6" style="color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white mb-1">تواصل مع الدعم</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">هل لديك استفسار أو تحتاج مساعدة في اختيار البرنامج المناسب؟</p>
                                <a href="{{ route('student.tickets.create') }}"
                                   class="inline-flex items-center text-sm font-medium"
                                   style="color: #0071AA;">
                                    إنشاء تذكرة دعم
                                    <svg class="w-4 h-4 ms-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background-color: #fef3c7;">
                                <svg class="w-6 h-6" style="color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white mb-1">معلومات مهمة</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    بعد التسجيل في البرنامج، ستتمكن من متابعة المواد والجلسات والحضور من لوحة التحكم.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
