@extends('layouts.dashboard')

@section('title', $subject->name)

@section('content')
<div class="mb-6">
    <!-- Breadcrumb -->
    <nav class="mb-4 text-sm">
        <ol class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('teacher.my-subjects.index') }}" class="hover:text-brand-500">موادي</a></li>
            <li>/</li>
            <li class="text-gray-900 dark:text-white">{{ $subject->name }}</li>
        </ol>
    </nav>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subject->name }}</h1>
                @if($subject->status === 'active')
                    <span class="rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-600 dark:bg-success-900 dark:text-success-200">نشط</span>
                @endif
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $subject->term->name ?? '-' }} - {{ $subject->term->program->name ?? '-' }}
                @if($subject->code)
                    <span class="mx-2">|</span>
                    <span class="font-mono">{{ $subject->code }}</span>
                @endif
            </p>
        </div>
        <a href="{{ route('teacher.my-subjects.sessions.create', $subject->id) }}"
           class="flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            إضافة حصة جديدة
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900 dark:text-green-200">
    {{ session('success') }}
</div>
@endif

<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-4 mb-6">
    <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-900">
                <svg class="h-6 w-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">الطلاب المسجلين</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subject->enrollments_count }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-900">
                <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي الحصص</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $sessions->count() }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 dark:bg-red-900">
                <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">حصص مباشرة (Zoom)</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $sessions->where('type', 'live_zoom')->count() }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 dark:bg-green-900">
                <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">حصص مسجلة</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $sessions->where('type', 'recorded_video')->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Active Live Sessions -->
@php
    $liveSessions = $sessions->filter(function($s) {
        return $s->type === 'live_zoom' && $s->zoom_meeting_id && !$s->ended_at;
    });
@endphp

@if($liveSessions->count() > 0)
<div class="mb-6">
    <div class="rounded-xl border-2 border-red-200 bg-gradient-to-r from-red-50 to-orange-50 dark:border-red-800 dark:from-red-900/20 dark:to-orange-900/20 p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="relative">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-500 text-white">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="absolute -top-1 -right-1 flex h-4 w-4">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500"></span>
                </span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">الحصص المباشرة</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">انضم إلى حصتك المباشرة الآن</p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach($liveSessions as $liveSession)
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white">{{ $liveSession->title }}</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            الحصة {{ $liveSession->session_number }}
                            @if($liveSession->scheduled_at)
                                - {{ \Carbon\Carbon::parse($liveSession->scheduled_at)->format('h:i A') }}
                            @endif
                        </p>
                    </div>
                    @if($liveSession->started_at && !$liveSession->ended_at)
                        <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700 dark:bg-green-900 dark:text-green-300">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                            جارية الآن
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">
                            مجدولة
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-4">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $liveSession->duration_minutes ?? 60 }} دقيقة
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('teacher.my-subjects.sessions.zoom-embedded', [$subject->id, $liveSession->id]) }}"
                       class="flex-1 flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-red-500 to-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:from-red-600 hover:to-red-700 transition-all shadow-sm hover:shadow-md">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        انضمام للبث
                    </a>
                    <a href="{{ route('teacher.my-subjects.sessions.zoom', [$subject->id, $liveSession->id]) }}"
                       class="flex items-center justify-center rounded-lg border border-gray-200 dark:border-gray-600 px-3 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                       title="ملء الشاشة">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                        </svg>
                    </a>
                    @if($liveSession->zoom_start_url)
                    <a href="{{ $liveSession->zoom_start_url }}" target="_blank"
                       class="flex items-center justify-center rounded-lg border border-gray-200 dark:border-gray-600 px-3 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                       title="فتح في تطبيق Zoom">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Sessions List -->
<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="border-b border-gray-200 dark:border-gray-800 px-6 py-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">الحصص</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800">
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">عنوان الحصة</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">النوع</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">الموعد</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">المدة</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">الحالة</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                <tr class="border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $session->session_number }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $session->title }}</div>
                        @if($session->unit)
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $session->unit->name ?? '' }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($session->type === 'live_zoom')
                            <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-600 dark:bg-red-900 dark:text-red-200">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                بث مباشر
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-600 dark:bg-blue-900 dark:text-blue-200">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" />
                                </svg>
                                فيديو مسجل
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                        @if($session->scheduled_at)
                            {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d') }}
                            <br>
                            <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($session->scheduled_at)->format('h:i A') }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                        @if($session->duration_minutes)
                            {{ $session->duration_minutes }} دقيقة
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($session->ended_at)
                            <span class="rounded-full bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">منتهية</span>
                        @elseif($session->started_at)
                            <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-600 dark:bg-green-900 dark:text-green-200">جارية</span>
                        @elseif($session->type === 'recorded_video' && $session->hasVideo())
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-600 dark:bg-blue-900 dark:text-blue-200">متاحة</span>
                        @else
                            <span class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-600 dark:bg-yellow-900 dark:text-yellow-200">مجدولة</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-2">
                            @if($session->type === 'live_zoom' && $session->zoom_meeting_id)
                            <a href="{{ route('teacher.my-subjects.sessions.zoom-embedded', [$subject->id, $session->id]) }}"
                               class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 dark:bg-red-900 dark:text-red-200 dark:hover:bg-red-800 transition-colors">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                بدء البث
                            </a>
                            @endif
                            <a href="{{ route('teacher.my-subjects.sessions.edit', [$subject->id, $session->id]) }}"
                               class="rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-100 dark:bg-brand-900 dark:text-brand-200 dark:hover:bg-brand-800 transition-colors">
                                تعديل
                            </a>
                            <form action="{{ route('teacher.my-subjects.sessions.destroy', [$subject->id, $session->id]) }}" method="POST" class="inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه الحصة؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="rounded-lg bg-error-50 px-3 py-1.5 text-xs font-medium text-error-600 hover:bg-error-100 dark:bg-error-900 dark:text-error-200 dark:hover:bg-error-800 transition-colors">
                                    حذف
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-4 text-sm font-medium text-gray-900 dark:text-white">لا توجد حصص</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">ابدأ بإضافة حصة جديدة</p>
                            <a href="{{ route('teacher.my-subjects.sessions.create', $subject->id) }}"
                               class="mt-4 flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                إضافة حصة
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
