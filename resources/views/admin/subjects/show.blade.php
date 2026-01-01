@extends('layouts.dashboard')

@section('title', 'عرض المادة الدراسية')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.subjects.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subject->name }}</h1>
        @if($subject->status === 'active')
            <span class="rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-600 dark:bg-success-900 dark:text-success-200">نشط</span>
        @elseif($subject->status === 'completed')
            <span class="rounded-full bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">مكتمل</span>
        @else
            <span class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-600 dark:bg-yellow-900 dark:text-yellow-200">غير نشط</span>
        @endif
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.subjects.edit', $subject) }}"
           class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
            تعديل المادة
        </a>
        <a href="{{ route('admin.sessions.create', ['subject_id' => $subject->id]) }}"
           class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
            إضافة درس
        </a>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Subject Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Basic Info -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            @if($subject->banner_photo)
            <div class="h-48 rounded-t-xl overflow-hidden">
                <img src="{{ Storage::url($subject->banner_photo) }}" alt="{{ $subject->name }}" class="w-full h-full object-cover">
            </div>
            @endif
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">معلومات المادة</h2>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">كود المادة</dt>
                        <dd class="mt-1">
                            <span class="rounded-lg bg-gray-100 px-2 py-1 text-sm font-medium dark:bg-gray-800">
                                {{ $subject->code }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">المعلم</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $subject->teacher->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">الفصل الدراسي</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $subject->term->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">المسار التعليمي</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $subject->term->program->name ?? '-' }}</dd>
                    </div>
                    @if($subject->credits)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">الساعات المعتمدة</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $subject->credits }} ساعة</dd>
                    </div>
                    @endif
                </dl>

                @if($subject->description)
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-800">
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">الوصف</h3>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $subject->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Sessions List -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">الدروس والمحاضرات ({{ $subject->sessions->count() }})</h2>
            </div>

            @forelse($subject->sessions as $session)
            <div class="border-b border-gray-200 dark:border-gray-800 py-4 last:border-0">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand-100 text-xs font-medium text-brand-600 dark:bg-brand-900 dark:text-brand-300">
                                {{ $session->session_number }}
                            </span>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $session->title }}</h3>
                            @if($session->is_mandatory)
                            <span class="rounded bg-error-50 px-2 py-0.5 text-xs font-medium text-error-600 dark:bg-error-900 dark:text-error-200">
                                إلزامي
                            </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <span class="flex items-center gap-1">
                                @if($session->type === 'live_zoom')
                                    <svg class="h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                                    </svg>
                                    Zoom
                                @else
                                    <svg class="h-4 w-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                                    </svg>
                                    فيديو
                                @endif
                            </span>
                            @if($session->duration_minutes)
                            <span class="flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $session->duration_minutes }} دقيقة
                            </span>
                            @endif
                            @if($session->scheduled_at)
                            <span class="flex items-center gap-1">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $session->scheduled_at->format('Y/m/d') }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($session->status === 'live')
                            <span class="rounded-full bg-error-50 px-2 py-1 text-xs font-medium text-error-600 dark:bg-error-900 dark:text-error-200 animate-pulse">مباشر</span>
                        @elseif($session->status === 'scheduled')
                            <span class="rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-600 dark:bg-blue-900 dark:text-blue-200">مجدول</span>
                        @elseif($session->status === 'completed')
                            <span class="rounded-full bg-success-50 px-2 py-1 text-xs font-medium text-success-600 dark:bg-success-900 dark:text-success-200">مكتمل</span>
                        @else
                            <span class="rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">ملغي</span>
                        @endif
                        <a href="{{ route('admin.sessions.show', $session) }}"
                           class="text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                            عرض →
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <svg class="h-12 w-12 text-gray-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <p class="mt-4 text-sm font-medium text-gray-900 dark:text-white">لا توجد دروس</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">ابدأ بإضافة درس جديد لهذه المادة</p>
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
                    <span class="text-sm text-gray-500 dark:text-gray-400">عدد الدروس</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $subject->sessions->count() }}</span>
                </div>
                @if($subject->credits)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">الساعات المعتمدة</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $subject->credits }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Teacher Info -->
        @if($subject->teacher)
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">معلومات المعلم</h2>
            <div class="flex items-center gap-3">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($subject->teacher->name) }}&background=6366F1&color=fff"
                     alt="{{ $subject->teacher->name }}"
                     class="h-12 w-12 rounded-full">
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $subject->teacher->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $subject->teacher->email }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
