@extends('layouts.dashboard')

@section('title', 'الملف الشخصي للطالب')

@section('content')
@php
    $completedEnrollments = $student->enrollments->where('status', 'completed')->whereNotNull('final_grade');
    $averageGrade = $completedEnrollments->count() > 0 ? $completedEnrollments->avg('final_grade') : 0;
@endphp

<!-- Header with Back Button -->
<div class="mb-6">
    <a href="{{ route('admin.students.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        العودة لقائمة الطلاب
    </a>
</div>

<!-- Main Profile Card -->
<div class="rounded-2xl overflow-hidden bg-white dark:bg-gray-800 shadow-xl mb-6">
    <!-- Cover & Avatar Section -->
    <div class="h-40 bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-600 relative">
        <div class="absolute inset-0 bg-black/10"></div>
        <!-- Pattern Overlay -->
        <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

        <!-- Status Badge -->
        <div class="absolute top-4 left-4">
            <span class="rounded-full px-4 py-1.5 text-sm font-medium backdrop-blur-sm
                @if($student->status === 'active') bg-green-500/80 text-white
                @elseif($student->status === 'pending') bg-yellow-500/80 text-white
                @elseif($student->status === 'suspended') bg-red-500/80 text-white
                @else bg-gray-500/80 text-white
                @endif">
                {{ $student->getStatusDisplayName() }}
            </span>
        </div>

        <!-- Edit Button -->
        <div class="absolute top-4 right-4">
            <a href="{{ route('admin.students.edit', $student) }}"
               class="flex items-center gap-2 rounded-lg bg-white/20 backdrop-blur-sm px-4 py-2 text-sm font-medium text-white hover:bg-white/30 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                تعديل
            </a>
        </div>
    </div>

    <!-- Profile Info Section -->
    <div class="relative px-6 pb-6">
        <!-- Avatar -->
        <div class="flex flex-col sm:flex-row sm:items-end gap-4 -mt-16 mb-4">
            <div class="h-32 w-32 rounded-2xl border-4 border-white dark:border-gray-800 bg-white shadow-xl overflow-hidden">
                @if($student->profile_photo)
                    <img src="{{ asset('storage/' . $student->profile_photo) }}" alt="{{ $student->name }}" class="h-full w-full object-cover">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&size=200&background=10b981&color=fff&bold=true" alt="{{ $student->name }}" class="h-full w-full object-cover">
                @endif
            </div>
            <div class="sm:pb-2">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $student->name }}</h1>
                <p class="text-gray-500 dark:text-gray-400">{{ $student->email }}</p>
            </div>
        </div>

        <!-- Quick Info Chips -->
        <div class="flex flex-wrap gap-3 mb-6">
            @if($student->national_id)
            <div class="flex items-center gap-2 rounded-full bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                </svg>
                <span class="text-gray-700 dark:text-gray-300">{{ $student->national_id }}</span>
            </div>
            @endif
            @if($student->phone)
            <div class="flex items-center gap-2 rounded-full bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                <span class="text-gray-700 dark:text-gray-300">{{ $student->phone }}</span>
            </div>
            @endif
            @if($student->program)
            <div class="flex items-center gap-2 rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-4 py-2 text-sm">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span class="text-emerald-700 dark:text-emerald-300">{{ $student->program->name }}</span>
            </div>
            @endif
            <div class="flex items-center gap-2 rounded-full bg-gray-100 dark:bg-gray-700 px-4 py-2 text-sm">
                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-gray-700 dark:text-gray-300">انضم {{ $student->created_at->diffForHumans() }}</span>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold">{{ $student->enrollments->count() }}</div>
                        <div class="text-sm text-blue-100">إجمالي المقررات</div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold">{{ $student->enrollments->where('status', 'completed')->count() }}</div>
                        <div class="text-sm text-emerald-100">مكتملة</div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold">{{ $student->enrollments->where('status', 'active')->count() }}</div>
                        <div class="text-sm text-amber-100">نشطة</div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold">{{ $averageGrade > 0 ? number_format($averageGrade, 1) : '-' }}</div>
                        <div class="text-sm text-purple-100">المعدل</div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Personal Info Card -->
        <div class="rounded-xl bg-white dark:bg-gray-800 shadow-lg overflow-hidden">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    المعلومات الشخصية
                </h3>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">الاسم الكامل</div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $student->name }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                        <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">البريد الإلكتروني</div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $student->email }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">رقم الهوية</div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $student->national_id ?? 'غير محدد' }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">رقم الهاتف</div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $student->phone ?? 'غير محدد' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrolled Subjects Card -->
        <div class="rounded-xl bg-white dark:bg-gray-800 shadow-lg overflow-hidden">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    المقررات المسجلة
                </h3>
                <span class="bg-brand-100 dark:bg-brand-900/30 text-brand-700 dark:text-brand-300 text-sm font-medium px-3 py-1 rounded-full">
                    {{ $student->enrollments->count() }} مقرر
                </span>
            </div>
            @if($student->enrollments->count() > 0)
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($student->enrollments as $enrollment)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-gray-900 dark:text-white">
                                {{ $enrollment->subject->name ?? 'غير محدد' }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $enrollment->subject->code ?? '' }}
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            @if($enrollment->progress)
                            <div class="hidden sm:block w-24">
                                <div class="flex items-center gap-2">
                                    <div class="h-2 flex-1 rounded-full bg-gray-200 dark:bg-gray-600 overflow-hidden">
                                        <div class="h-full rounded-full bg-brand-500" style="width: {{ $enrollment->progress }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-500">{{ $enrollment->progress }}%</span>
                                </div>
                            </div>
                            @endif
                            <span class="rounded-full px-3 py-1 text-xs font-medium
                                @if($enrollment->status === 'active') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                                @elseif($enrollment->status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                                @endif">
                                @if($enrollment->status === 'active') نشط
                                @elseif($enrollment->status === 'completed') مكتمل
                                @else {{ $enrollment->status }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-8 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400">لا توجد مقررات مسجلة حالياً</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Verification Status -->
        <div class="rounded-xl bg-white dark:bg-gray-800 shadow-lg overflow-hidden">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    حالة التحقق
                </h3>
            </div>
            <div class="p-4 space-y-3">
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg {{ $student->email_verified_at ? 'bg-green-100 dark:bg-green-900/30' : 'bg-yellow-100 dark:bg-yellow-900/30' }} flex items-center justify-center">
                            <svg class="w-4 h-4 {{ $student->email_verified_at ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">البريد الإلكتروني</span>
                    </div>
                    @if($student->email_verified_at)
                        <span class="text-xs font-medium text-green-600 dark:text-green-400">موثق</span>
                    @else
                        <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400">غير موثق</span>
                    @endif
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg {{ $student->phone_verified_at ? 'bg-green-100 dark:bg-green-900/30' : 'bg-yellow-100 dark:bg-yellow-900/30' }} flex items-center justify-center">
                            <svg class="w-4 h-4 {{ $student->phone_verified_at ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">رقم الهاتف</span>
                    </div>
                    @if($student->phone_verified_at)
                        <span class="text-xs font-medium text-green-600 dark:text-green-400">موثق</span>
                    @else
                        <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400">غير موثق</span>
                    @endif
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg {{ $student->nafath_verified_at ? 'bg-green-100 dark:bg-green-900/30' : 'bg-yellow-100 dark:bg-yellow-900/30' }} flex items-center justify-center">
                            <svg class="w-4 h-4 {{ $student->nafath_verified_at ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">نفاذ</span>
                    </div>
                    @if($student->nafath_verified_at)
                        <span class="text-xs font-medium text-green-600 dark:text-green-400">موثق</span>
                    @else
                        <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400">غير موثق</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="rounded-xl bg-white dark:bg-gray-800 shadow-lg overflow-hidden">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">إجراءات سريعة</h3>
            </div>
            <div class="p-4 space-y-2">
                <a href="{{ route('admin.students.edit', $student) }}"
                   class="w-full flex items-center gap-3 rounded-xl p-3 text-sm font-medium bg-brand-50 text-brand-700 hover:bg-brand-100 dark:bg-brand-900/20 dark:text-brand-300 dark:hover:bg-brand-900/30 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    تعديل البيانات
                </a>
                <button class="w-full flex items-center gap-3 rounded-xl p-3 text-sm font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-300 dark:hover:bg-blue-900/30 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    إرسال رسالة
                </button>
                <button class="w-full flex items-center gap-3 rounded-xl p-3 text-sm font-medium bg-purple-50 text-purple-700 hover:bg-purple-100 dark:bg-purple-900/20 dark:text-purple-300 dark:hover:bg-purple-900/30 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    السجل الأكاديمي
                </button>
            </div>
        </div>

        <!-- Account Info -->
        <div class="rounded-xl bg-white dark:bg-gray-800 shadow-lg overflow-hidden">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">معلومات الحساب</h3>
            </div>
            <div class="p-4 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">تاريخ الإنشاء</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->created_at->format('Y/m/d') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">آخر تحديث</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->updated_at->diffForHumans() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">اكتمال الملف</span>
                    @if($student->profile_completed_at)
                        <span class="text-xs font-medium px-2 py-1 rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">مكتمل</span>
                    @else
                        <span class="text-xs font-medium px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">غير مكتمل</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
