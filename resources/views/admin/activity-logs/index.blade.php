@extends('layouts.dashboard')

@section('title', 'سجل النشاطات')

@section('content')
@php
    // Arabic translations for actions
    $actionArabic = [
        'login'               => 'تسجيل دخول',
        'logout'              => 'تسجيل خروج',
        'register'            => 'تسجيل حساب جديد',
        'password_reset'      => 'استعادة كلمة المرور',
        'view_session'        => 'مشاهدة جلسة',
        'view_recording'      => 'مشاهدة تسجيل',
        'download_file'       => 'تحميل ملف',
        'view_subject'        => 'عرض مادة دراسية',
        'view_unit'           => 'عرض وحدة',
        'start_quiz'          => 'بدء اختبار',
        'submit_quiz'         => 'إرسال اختبار',
        'view_quiz_result'    => 'عرض نتيجة اختبار',
        'submit_assignment'   => 'إرسال واجب',
        'grade_evaluation'    => 'تصحيح تقييم',
        'join_session'        => 'الانضمام لجلسة',
        'leave_session'       => 'مغادرة جلسة',
        'mark_attendance'     => 'تسجيل الحضور',
        'enroll'              => 'التسجيل في مادة',
        'withdraw'            => 'الانسحاب من مادة',
        'complete_course'     => 'إتمام الدورة',
        'submit_survey'       => 'إرسال استبيان',
        'submit_rating'       => 'تقييم معلم',
        'create_ticket'       => 'إنشاء تذكرة دعم',
        'reply_ticket'        => 'الرد على تذكرة',
        'create_user'         => 'إنشاء مستخدم',
        'update_user'         => 'تعديل مستخدم',
        'delete_user'         => 'حذف مستخدم',
        'create_program'      => 'إنشاء مسار تعليمي',
        'update_program'      => 'تعديل مسار تعليمي',
        'delete_program'      => 'حذف مسار تعليمي',
        'create_session'      => 'إنشاء جلسة',
        'update_session'      => 'تعديل جلسة',
        'delete_session'      => 'حذف جلسة',
        'page_view'           => 'عرض صفحة',
    ];

    // Arabic translations for categories
    $categoryArabic = [
        'auth'          => 'المصادقة',
        'content'       => 'المحتوى',
        'assessment'    => 'التقييمات',
        'attendance'    => 'الحضور',
        'enrollment'    => 'التسجيل',
        'communication' => 'التواصل',
        'admin'         => 'الإدارة',
        'navigation'    => 'التصفح',
    ];

    // Category: Tailwind badge colors
    $categoryColors = [
        'auth'          => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
        'content'       => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
        'assessment'    => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
        'attendance'    => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
        'enrollment'    => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
        'communication' => 'bg-pink-100 text-pink-700 dark:bg-pink-900 dark:text-pink-300',
        'admin'         => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
        'navigation'    => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
    ];

    // Category icons
    $categoryIcons = [
        'auth'          => '🔐',
        'content'       => '📚',
        'assessment'    => '📝',
        'attendance'    => '✅',
        'enrollment'    => '🎓',
        'communication' => '💬',
        'admin'         => '⚙️',
        'navigation'    => '🧭',
    ];
@endphp

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    {{-- ── Header ─────────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary bg-opacity-10">
                <svg class="fill-primary" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M13 2.05v2.02c3.95.49 7 3.85 7 7.93s-3.05 7.44-7 7.93v2.02c5.05-.5 9-4.76 9-9.95S18.05 2.55 13 2.05zM11 2.05C5.95 2.55 2 6.81 2 12s3.95 9.45 9 9.95V19.93c-3.95-.49-7-3.85-7-7.93s3.05-7.44 7-7.93V2.05z"/>
                </svg>
            </span>
            <div>
                <h2 class="text-xl font-bold text-black dark:text-white">سجل النشاطات</h2>
                <p class="text-sm text-gray-500">تتبع جميع الأنشطة والأحداث في النظام</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.activity-logs.stats') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-meta-3 py-2 px-4 text-sm font-medium text-white hover:bg-opacity-90 transition">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                </svg>
                الإحصائيات
            </a>
            <a href="{{ route('admin.activity-logs.export', ['format' => 'csv'] + request()->all()) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-primary py-2 px-4 text-sm font-medium text-primary hover:bg-primary hover:text-white transition">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2z"/>
                </svg>
                تصدير CSV
            </a>
        </div>
    </div>

    {{-- ── Stats Cards ─────────────────────────────────────────────── --}}
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">

        <div class="rounded-xl border border-stroke bg-white p-5 shadow-sm dark:border-strokedark dark:bg-boxdark">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500">إجمالي النشاطات</p>
                    <h3 class="mt-1 text-2xl font-bold text-black dark:text-white">{{ number_format($stats['total']) }}</h3>
                    <p class="mt-1 text-xs text-gray-400">منذ بداية النظام</p>
                </div>
                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-primary bg-opacity-10">
                    <svg class="fill-primary" width="20" height="20" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm.5 5H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-stroke bg-white p-5 shadow-sm dark:border-strokedark dark:bg-boxdark">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500">آخر 24 ساعة</p>
                    <h3 class="mt-1 text-2xl font-bold text-black dark:text-white">{{ number_format($stats['last_24h']) }}</h3>
                    <p class="mt-1 text-xs text-warning">نشاط حديث</p>
                </div>
                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-warning bg-opacity-10">
                    <svg class="fill-warning" width="20" height="20" viewBox="0 0 24 24">
                        <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-stroke bg-white p-5 shadow-sm dark:border-strokedark dark:bg-boxdark">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500">آخر 7 أيام</p>
                    <h3 class="mt-1 text-2xl font-bold text-black dark:text-white">{{ number_format($stats['last_7d']) }}</h3>
                    <p class="mt-1 text-xs text-meta-3">هذا الأسبوع</p>
                </div>
                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-meta-3 bg-opacity-10">
                    <svg class="fill-meta-3" width="20" height="20" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-stroke bg-white p-5 shadow-sm dark:border-strokedark dark:bg-boxdark">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-500">مُزامن مع xAPI</p>
                    <h3 class="mt-1 text-2xl font-bold text-black dark:text-white">{{ number_format($stats['xapi_synced']) }}</h3>
                    <p class="mt-1 text-xs text-success">
                        {{ $stats['total'] > 0 ? round(($stats['xapi_synced'] / $stats['total']) * 100, 1) : 0 }}% من الإجمالي
                    </p>
                </div>
                <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-success bg-opacity-10">
                    <svg class="fill-success" width="20" height="20" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Charts ──────────────────────────────────────────────────── --}}
    <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-3">

        {{-- Timeline --}}
        <div class="lg:col-span-2 rounded-xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
            <div class="border-b border-stroke px-6 py-4 dark:border-strokedark">
                <h3 class="text-sm font-semibold text-black dark:text-white">النشاط اليومي — آخر 7 أيام</h3>
            </div>
            <div class="p-6">
                <canvas id="activityChart" height="180"></canvas>
            </div>
        </div>

        {{-- Donut --}}
        <div class="rounded-xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
            <div class="border-b border-stroke px-6 py-4 dark:border-strokedark">
                <h3 class="text-sm font-semibold text-black dark:text-white">توزيع الفئات</h3>
            </div>
            <div class="p-6">
                <canvas id="categoryChart" height="180"></canvas>
            </div>
        </div>

    </div>

    {{-- ── Top Actions ─────────────────────────────────────────────── --}}
    @if($topActions->isNotEmpty())
    <div class="mb-6 rounded-xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-6 py-4 dark:border-strokedark">
            <h3 class="text-sm font-semibold text-black dark:text-white">أكثر الأنشطة تكراراً</h3>
        </div>
        <div class="grid grid-cols-1 divide-y divide-stroke md:grid-cols-5 md:divide-x md:divide-y-0 dark:divide-strokedark">
            @foreach($topActions as $index => $action)
                @php
                    $medals = ['🥇','🥈','🥉','4️⃣','5️⃣'];
                @endphp
                <div class="flex items-center gap-3 px-5 py-4">
                    <span class="text-2xl leading-none">{{ $medals[$index] ?? ($index + 1) }}</span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-black dark:text-white">
                            {{ $actionArabic[$action->action] ?? $action->action }}
                        </p>
                        <p class="text-xs text-gray-500">{{ number_format($action->count) }} مرة</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Filters ─────────────────────────────────────────────────── --}}
    <div class="mb-6 rounded-xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-6 py-4 dark:border-strokedark flex items-center gap-2">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="text-gray-500">
                <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/>
            </svg>
            <h3 class="text-sm font-semibold text-black dark:text-white">البحث والتصفية</h3>
            @if(request()->hasAny(['action', 'category', 'date_from', 'date_to', 'search']))
                <span class="inline-flex rounded-full bg-primary bg-opacity-10 px-2 py-0.5 text-xs font-medium text-primary">فعّال</span>
            @endif
        </div>
        <div class="p-5">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}"
                  class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                {{-- Search --}}
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">البحث بالاسم أو البريد الإلكتروني</label>
                    <div class="relative">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="ابحث..."
                               class="w-full rounded-lg border border-stroke bg-transparent py-2.5 pr-9 pl-4 text-sm text-black outline-none focus:border-primary dark:border-strokedark dark:text-white transition">
                    </div>
                </div>

                {{-- Action --}}
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">الإجراء</label>
                    <select name="action"
                            class="w-full rounded-lg border border-stroke bg-transparent py-2.5 px-4 text-sm text-black outline-none focus:border-primary dark:border-strokedark dark:text-white transition">
                        <option value="">جميع الإجراءات</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>
                                {{ $actionArabic[$act] ?? $act }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Category --}}
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">الفئة</label>
                    <select name="category"
                            class="w-full rounded-lg border border-stroke bg-transparent py-2.5 px-4 text-sm text-black outline-none focus:border-primary dark:border-strokedark dark:text-white transition">
                        <option value="">جميع الفئات</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                {{ $categoryIcons[$cat] ?? '' }} {{ $categoryArabic[$cat] ?? ucfirst($cat) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date From --}}
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">من تاريخ</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full rounded-lg border border-stroke bg-transparent py-2.5 px-4 text-sm text-black outline-none focus:border-primary dark:border-strokedark dark:text-white transition">
                </div>

                {{-- Date To --}}
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-400">إلى تاريخ</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full rounded-lg border border-stroke bg-transparent py-2.5 px-4 text-sm text-black outline-none focus:border-primary dark:border-strokedark dark:text-white transition">
                </div>

                {{-- Buttons --}}
                <div class="sm:col-span-2 flex items-end gap-3">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-primary py-2.5 px-5 text-sm font-medium text-white hover:bg-opacity-90 transition">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                        </svg>
                        تطبيق
                    </button>
                    @if(request()->hasAny(['action', 'category', 'date_from', 'date_to', 'search']))
                        <a href="{{ route('admin.activity-logs.index') }}"
                           class="inline-flex items-center gap-2 rounded-lg border border-stroke py-2.5 px-5 text-sm font-medium text-black hover:bg-gray-100 dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                            </svg>
                            مسح الفلاتر
                        </a>
                    @endif
                </div>

            </form>
        </div>
    </div>

    {{-- ── Logs Table ──────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">

        <div class="border-b border-stroke px-6 py-4 dark:border-strokedark flex items-center justify-between">
            <div class="flex items-center gap-3">
                <h3 class="text-sm font-semibold text-black dark:text-white">النشاطات المسجّلة</h3>
                <span class="inline-flex items-center rounded-full bg-primary bg-opacity-10 px-2.5 py-0.5 text-xs font-medium text-primary">
                    {{ number_format($logs->total()) }} سجل
                </span>
            </div>
            @if($logs->lastPage() > 1)
                <span class="text-xs text-gray-400">
                    صفحة {{ $logs->currentPage() }} / {{ $logs->lastPage() }}
                </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 dark:bg-meta-4">
                        <th class="whitespace-nowrap py-3.5 px-5 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">المستخدم</th>
                        <th class="whitespace-nowrap py-3.5 px-5 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">النشاط</th>
                        <th class="whitespace-nowrap py-3.5 px-5 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">الفئة</th>
                        <th class="hidden whitespace-nowrap py-3.5 px-5 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 md:table-cell">الكيان المرتبط</th>
                        <th class="hidden whitespace-nowrap py-3.5 px-5 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 lg:table-cell">عنوان IP</th>
                        <th class="whitespace-nowrap py-3.5 px-5 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">الوقت</th>
                        <th class="hidden whitespace-nowrap py-3.5 px-5 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 sm:table-cell">xAPI</th>
                        <th class="py-3.5 px-5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse($logs as $log)
                        @php
                            $cat      = $log->action_category ?? 'navigation';
                            $badge    = $categoryColors[$cat]  ?? 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
                            $catIcon  = $categoryIcons[$cat]   ?? '📌';
                            $actLabel = $actionArabic[$log->action] ?? $log->action;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-meta-4 transition-colors">

                            {{-- User --}}
                            <td class="py-4 px-5">
                                @if($log->user)
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-primary bg-opacity-10 text-sm font-bold text-primary">
                                            {{ mb_strtoupper(mb_substr($log->user->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="max-w-[130px] truncate text-sm font-medium text-black dark:text-white">{{ $log->user->name }}</p>
                                            <p class="max-w-[130px] truncate text-xs text-gray-400">{{ $log->user->email }}</p>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">النظام</span>
                                @endif
                            </td>

                            {{-- Action --}}
                            <td class="py-4 px-5">
                                <span class="inline-flex items-center gap-1.5 rounded-full {{ $badge }} py-1 px-3 text-xs font-medium">
                                    {{ $catIcon }} {{ $actLabel }}
                                </span>
                            </td>

                            {{-- Category --}}
                            <td class="py-4 px-5">
                                <span class="inline-flex rounded-lg {{ $badge }} bg-opacity-60 py-1 px-2.5 text-xs font-medium">
                                    {{ $categoryArabic[$cat] ?? ucfirst($cat) }}
                                </span>
                            </td>

                            {{-- Related --}}
                            <td class="hidden py-4 px-5 md:table-cell">
                                @if($log->loggable_type)
                                    <div class="text-xs">
                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ class_basename($log->loggable_type) }}</span>
                                        <span class="text-gray-400"> #{{ $log->loggable_id }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">—</span>
                                @endif
                            </td>

                            {{-- IP --}}
                            <td class="hidden py-4 px-5 lg:table-cell">
                                <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs font-mono text-gray-600 dark:bg-meta-4 dark:text-gray-400">
                                    {{ $log->ip_address ?? '—' }}
                                </code>
                            </td>

                            {{-- Time --}}
                            <td class="py-4 px-5">
                                <p class="text-sm text-black dark:text-white">{{ $log->created_at->diffForHumans() }}</p>
                                <p class="mt-0.5 text-xs text-gray-400">{{ $log->created_at->format('d/m/Y H:i') }}</p>
                            </td>

                            {{-- xAPI --}}
                            <td class="hidden py-4 px-5 sm:table-cell">
                                @if($log->xapi_sent)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-success bg-opacity-10 px-2.5 py-1 text-xs font-medium text-success">
                                        ✓ مُرسل
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-500 dark:bg-meta-4">
                                        معلق
                                    </span>
                                @endif
                            </td>

                            {{-- View --}}
                            <td class="py-4 px-5">
                                <a href="{{ route('admin.activity-logs.show', $log) }}"
                                   title="عرض التفاصيل"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-primary bg-opacity-10 text-primary hover:bg-opacity-100 hover:text-white transition">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                    </svg>
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-meta-4">
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" class="text-gray-400">
                                            <path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 12H4V8h16v10z"/>
                                        </svg>
                                    </div>
                                    <p class="font-medium text-gray-600 dark:text-gray-400">لا توجد نشاطات مطابقة</p>
                                    <p class="text-sm text-gray-400">جرب تعديل معايير البحث أو التصفية</p>
                                    @if(request()->hasAny(['action', 'category', 'date_from', 'date_to', 'search']))
                                        <a href="{{ route('admin.activity-logs.index') }}"
                                           class="mt-1 inline-flex items-center gap-1 rounded-lg bg-primary bg-opacity-10 px-4 py-2 text-sm font-medium text-primary hover:bg-opacity-20 transition">
                                            مسح الفلاتر
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="border-t border-stroke px-6 py-4 dark:border-strokedark">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        @endif

    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const isDark = document.documentElement.classList.contains('dark');
const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
const labelColor = isDark ? '#9CA3AF' : '#6B7280';

// Activity Timeline
new Chart(document.getElementById('activityChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($activityTimeline->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m'))) !!},
        datasets: [{
            label: 'النشاطات',
            data: {!! json_encode($activityTimeline->pluck('count')) !!},
            borderColor: '#3C50E0',
            backgroundColor: 'rgba(60,80,224,0.08)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#3C50E0',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(17,24,39,0.9)',
                padding: 10,
                titleFont: { size: 12 },
                bodyFont: { size: 13 },
                callbacks: {
                    title: (items) => items[0].label,
                    label: (item) => `  ${item.raw} نشاط`,
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { color: labelColor, stepSize: 1 },
                grid: { color: gridColor }
            },
            x: {
                ticks: { color: labelColor },
                grid: { display: false }
            }
        }
    }
});

// Category Distribution
const catLabelsRaw  = {!! json_encode($categoryStats->keys()) !!};
const catLabels = catLabelsRaw.map(k => ({
    'auth':'المصادقة','content':'المحتوى','assessment':'التقييمات',
    'attendance':'الحضور','enrollment':'التسجيل','communication':'التواصل',
    'admin':'الإدارة','navigation':'التصفح'
}[k] || k));

new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: catLabels,
        datasets: [{
            data: {!! json_encode($categoryStats->values()) !!},
            backgroundColor: [
                'rgba(99,102,241,0.8)',
                'rgba(59,130,246,0.8)',
                'rgba(234,179,8,0.8)',
                'rgba(34,197,94,0.8)',
                'rgba(168,85,247,0.8)',
                'rgba(236,72,153,0.8)',
                'rgba(239,68,68,0.8)',
                'rgba(107,114,128,0.8)',
            ],
            borderWidth: 2,
            borderColor: isDark ? '#1C2434' : '#fff',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 12,
                    color: labelColor,
                    font: { size: 11 },
                    boxWidth: 12,
                    boxHeight: 12,
                }
            },
            tooltip: {
                backgroundColor: 'rgba(17,24,39,0.9)',
                padding: 10,
            }
        },
        cutout: '65%',
    }
});
</script>
@endsection
