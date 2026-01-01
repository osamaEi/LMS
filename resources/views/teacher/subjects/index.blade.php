@extends('layouts.dashboard')

@section('title', 'موادي')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">موادي</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">عرض وإدارة المواد الدراسية المسندة إليك</p>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900 dark:text-green-200">
    {{ session('success') }}
</div>
@endif

<!-- Statistics Cards -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-4 mb-6 w-full">
    <div class="rounded-xl shadow-lg p-5" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl" style="background: rgba(255,255,255,0.2);">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <div>
                <p class="text-sm" style="color: rgba(255,255,255,0.8);">إجمالي المواد</p>
                <p class="text-2xl font-bold text-white">{{ $subjects->count() }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-xl shadow-lg p-5" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl" style="background: rgba(255,255,255,0.2);">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm" style="color: rgba(255,255,255,0.8);">إجمالي الطلاب</p>
                <p class="text-2xl font-bold text-white">{{ $subjects->sum('enrollments_count') }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-xl shadow-lg p-5" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl" style="background: rgba(255,255,255,0.2);">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-sm" style="color: rgba(255,255,255,0.8);">إجمالي الحصص</p>
                <p class="text-2xl font-bold text-white">{{ $subjects->sum('sessions_count') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Subjects Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
    @forelse($subjects as $subject)
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden hover:shadow-lg transition-shadow">
        <!-- Subject Banner -->
        @if($subject->banner_photo)
        <div class="h-40 bg-cover bg-center" style="background-image: url('{{ asset('storage/' . $subject->banner_photo) }}')"></div>
        @else
        <div class="h-40 flex items-center justify-center" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
            <svg class="h-16 w-16" style="color: rgba(255,255,255,0.5);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
        </div>
        @endif

        <div class="p-5">
            <!-- Status Badge -->
            <div class="flex items-center justify-between mb-3">
                <span class="rounded-lg bg-gray-100 px-2 py-1 text-xs font-medium dark:bg-gray-800 text-gray-600 dark:text-gray-400">
                    {{ $subject->code }}
                </span>
                @if($subject->status === 'active')
                    <span class="rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-600 dark:bg-success-900 dark:text-success-200">نشط</span>
                @elseif($subject->status === 'completed')
                    <span class="rounded-full bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">مكتمل</span>
                @else
                    <span class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-600 dark:bg-yellow-900 dark:text-yellow-200">غير نشط</span>
                @endif
            </div>

            <!-- Subject Name -->
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $subject->name }}</h3>

            <!-- Term & Program -->
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                {{ $subject->term->name ?? '-' }} - {{ $subject->term->program->name ?? '-' }}
            </p>

            <!-- Stats -->
            <div class="flex items-center gap-4 mb-4 text-sm">
                <div class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    <span>{{ $subject->enrollments_count }} طالب</span>
                </div>
                <div class="flex items-center gap-1 text-gray-600 dark:text-gray-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span>{{ $subject->sessions_count }} حصة</span>
                </div>
            </div>

            <!-- Actions -->
            <a href="{{ route('teacher.my-subjects.show', $subject->id) }}"
               class="flex items-center justify-center gap-2 w-full rounded-lg px-4 py-2.5 text-sm font-medium text-white transition-colors"
               style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                عرض المادة والحصص
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="rounded-xl border border-gray-200 bg-white p-12 dark:border-gray-800 dark:bg-gray-900 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <p class="mt-4 text-sm font-medium text-gray-900 dark:text-white">لا توجد مواد مسندة إليك</p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">تواصل مع الإدارة لإسناد مواد دراسية</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
