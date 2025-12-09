@extends('layouts.dashboard')

@section('title', 'عرض الفصل الدراسي')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.terms.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $term->name }}</h1>
        @if($term->status === 'active')
            <span class="rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-600 dark:bg-success-900 dark:text-success-200">نشط</span>
        @elseif($term->status === 'upcoming')
            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-600 dark:bg-blue-900 dark:text-blue-200">قادم</span>
        @else
            <span class="rounded-full bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">مكتمل</span>
        @endif
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.terms.edit', $term) }}"
           class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
            تعديل الفصل
        </a>
        <a href="{{ route('admin.subjects.create', ['term_id' => $term->id]) }}"
           class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
            إضافة مادة دراسية
        </a>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Term Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Basic Info -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">معلومات الفصل الدراسي</h2>
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">المسار التعليمي</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $term->program->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">رقم الفصل</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">الفصل {{ $term->term_number }}</dd>
                </div>
                @if($term->track)
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">المسار الفرعي</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $term->track->name }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">فترة الدراسة</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                        من {{ $term->start_date->format('Y/m/d') }}<br>
                        إلى {{ $term->end_date->format('Y/m/d') }}
                    </dd>
                </div>
                @if($term->registration_start_date && $term->registration_end_date)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">فترة التسجيل</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                        من {{ $term->registration_start_date->format('Y/m/d') }}
                        إلى {{ $term->registration_end_date->format('Y/m/d') }}
                    </dd>
                </div>
                @endif
            </dl>
        </div>

        <!-- Subjects List -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">المواد الدراسية ({{ $term->subjects->count() }})</h2>
            </div>

            @forelse($term->subjects as $subject)
            <div class="border-b border-gray-200 dark:border-gray-800 py-4 last:border-0">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $subject->name }}</h3>
                            <span class="rounded bg-gray-100 px-2 py-0.5 text-xs font-medium dark:bg-gray-800">
                                {{ $subject->code }}
                            </span>
                        </div>
                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <span class="flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ $subject->teacher->name ?? '-' }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                {{ $subject->sessions_count }} درس
                            </span>
                            @if($subject->total_hours)
                            <span class="flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $subject->total_hours }} ساعة
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($subject->status === 'active')
                            <span class="rounded-full bg-success-50 px-2 py-1 text-xs font-medium text-success-600 dark:bg-success-900 dark:text-success-200">نشط</span>
                        @elseif($subject->status === 'completed')
                            <span class="rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">مكتمل</span>
                        @else
                            <span class="rounded-full bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-600 dark:bg-yellow-900 dark:text-yellow-200">غير نشط</span>
                        @endif
                        <a href="{{ route('admin.subjects.show', $subject) }}"
                           class="text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                            عرض →
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <svg class="h-12 w-12 text-gray-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <p class="mt-4 text-sm font-medium text-gray-900 dark:text-white">لا توجد مواد دراسية</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">ابدأ بإضافة مادة دراسية جديدة لهذا الفصل</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Sidebar Stats -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Quick Stats -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">إحصائيات سريعة</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">عدد المواد</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $term->subjects->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">إجمالي الدروس</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $term->subjects->sum('sessions_count') }}</span>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">الجدول الزمني</h2>
            <div class="space-y-4">
                @if($term->registration_start_date && $term->registration_end_date)
                <div class="flex items-start gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                        <svg class="h-4 w-4 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">فترة التسجيل</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $term->registration_start_date->format('Y/m/d') }} - {{ $term->registration_end_date->format('Y/m/d') }}
                        </p>
                    </div>
                </div>
                @endif

                <div class="flex items-start gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-success-100 dark:bg-success-900">
                        <svg class="h-4 w-4 text-success-600 dark:text-success-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">فترة الدراسة</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $term->start_date->format('Y/m/d') }} - {{ $term->end_date->format('Y/m/d') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
