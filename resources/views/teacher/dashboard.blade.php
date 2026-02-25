@extends('layouts.dashboard')

@section('title', 'لوحة المعلم')

@section('content')
<div class="mx-auto max-w-screen-xl p-4 md:p-6 2xl:p-10">

    {{-- ══════════════════════════════════════
         Hero Header
    ══════════════════════════════════════ --}}
    <div class="relative mb-8 overflow-hidden rounded-2xl px-8 py-7 text-white shadow-xl"
         style="background: linear-gradient(135deg, #1e3a5f 0%, #0071AA 55%, #0ea5e9 100%);">
        <div class="pointer-events-none absolute -top-20 -right-20 h-64 w-64 rounded-full" style="background:rgba(255,255,255,.05)"></div>
        <div class="pointer-events-none absolute -bottom-12 -left-12 h-48 w-48 rounded-full" style="background:rgba(255,255,255,.05)"></div>

        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="mb-1 text-sm font-medium" style="color:rgba(255,255,255,.7)">{{ now()->translatedFormat('l، d F Y') }}</p>
                <h1 class="text-2xl font-bold tracking-tight">مرحباً، {{ auth()->user()->name }}</h1>
                <p class="mt-1 text-sm" style="color:rgba(255,255,255,.7)">لوحة تحكم المعلم — نظرة شاملة على مواد وجلساتك</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('teacher.schedule') }}"
                   class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition"
                   style="background:rgba(255,255,255,.15)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zM7 12h5v5H7z"/>
                    </svg>
                    الجدول
                </a>
                <a href="{{ route('teacher.my-subjects.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition"
                   style="background:rgba(255,255,255,.15)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                    </svg>
                    موادي
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         Stats Row
    ══════════════════════════════════════ --}}
    <div class="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-4">
        {{-- Subjects --}}
        <div class="flex items-center gap-4 rounded-2xl border border-stroke bg-white p-5 shadow-sm dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl" style="background:linear-gradient(135deg,#0071AA,#005a88)">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="white">
                    <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-black text-black dark:text-white">{{ $stats['subjects_count'] }}</p>
                <p class="text-xs text-gray-500">المواد الدراسية</p>
            </div>
        </div>

        {{-- Students --}}
        <div class="flex items-center gap-4 rounded-2xl border border-stroke bg-white p-5 shadow-sm dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl" style="background:linear-gradient(135deg,#10b981,#059669)">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="white">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-black text-black dark:text-white">{{ $stats['total_students'] }}</p>
                <p class="text-xs text-gray-500">إجمالي الطلاب</p>
            </div>
        </div>

        {{-- Sessions --}}
        <div class="flex items-center gap-4 rounded-2xl border border-stroke bg-white p-5 shadow-sm dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="white">
                    <path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-black text-black dark:text-white">{{ $stats['total_sessions'] }}</p>
                <p class="text-xs text-gray-500">الجلسات الكلية</p>
            </div>
        </div>

        {{-- Live / Rating --}}
        @if($stats['live_sessions'] > 0)
        <div class="flex items-center gap-4 rounded-2xl border border-red-200 bg-red-50 p-5 shadow-sm dark:border-red-700 dark:bg-red-900/20">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl" style="background:linear-gradient(135deg,#ef4444,#dc2626)">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="white" class="animate-pulse">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-black text-red-600">{{ $stats['live_sessions'] }}</p>
                <p class="text-xs text-red-500">مباشر الآن</p>
            </div>
        </div>
        @else
        <div class="flex items-center gap-4 rounded-2xl border border-stroke bg-white p-5 shadow-sm dark:border-strokedark dark:bg-boxdark">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="white">
                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-black text-black dark:text-white">{{ number_format($teacherRating['overall'], 1) }}</p>
                <p class="text-xs text-gray-500">تقييمك العام</p>
            </div>
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════
         Main Grid
    ══════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- LEFT — Upcoming Sessions + Subjects (2/3) --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Upcoming Sessions --}}
            <div class="overflow-hidden rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="flex items-center justify-between border-b border-stroke px-6 py-4 dark:border-strokedark">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:linear-gradient(135deg,#0071AA,#005a88)">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                                <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zM7 12h5v5H7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-black dark:text-white">الجلسات القادمة</h3>
                            <p class="text-xs text-gray-400">{{ $upcomingSessions->count() }} جلسة مجدولة</p>
                        </div>
                    </div>
                    <a href="{{ route('teacher.schedule') }}" class="text-xs font-medium text-primary hover:underline">عرض الكل</a>
                </div>

                <div class="divide-y divide-stroke dark:divide-strokedark">
                    @forelse($upcomingSessions as $session)
                    @php
                        $dt     = \Carbon\Carbon::parse($session->scheduled_at);
                        $isToday = $dt->isToday();
                        $typeLabel = match($session->type ?? '') {
                            'live_zoom'      => 'Zoom',
                            'recorded_video' => 'مسجّل',
                            'in_person'      => 'حضوري',
                            default          => $session->type ?? '',
                        };
                    @endphp
                    <div class="flex items-center gap-4 px-6 py-4 transition hover:bg-gray-50 dark:hover:bg-meta-4">
                        {{-- Date block --}}
                        <div class="flex w-14 flex-shrink-0 flex-col items-center justify-center rounded-xl py-2 text-white"
                             style="background:{{ $isToday ? 'linear-gradient(135deg,#0071AA,#005a88)' : 'linear-gradient(135deg,#64748b,#475569)' }}">
                            <span class="text-xl font-black leading-none">{{ $dt->format('d') }}</span>
                            <span class="mt-0.5 text-[10px] uppercase">{{ $dt->translatedFormat('M') }}</span>
                            <span class="text-[10px]">{{ $dt->format('H:i') }}</span>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-1.5 mb-0.5">
                                <span class="font-semibold text-sm text-black dark:text-white truncate">{{ $session->title }}</span>
                                @if($isToday)
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold text-white" style="background:#0071AA">اليوم</span>
                                @endif
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-500 dark:bg-meta-4 dark:text-gray-300">{{ $typeLabel }}</span>
                            </div>
                            <p class="text-xs text-gray-400 truncate">{{ $session->subject->name ?? '—' }} · {{ $dt->diffForHumans() }}</p>
                        </div>

                        {{-- Action --}}
                        @if($session->zoom_join_url)
                            <a href="{{ $session->zoom_join_url }}" target="_blank"
                               class="flex-shrink-0 inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-bold text-white shadow transition"
                               style="background:linear-gradient(135deg,#22c55e,#16a34a)">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/>
                                </svg>
                                انضم
                            </a>
                        @else
                            <a href="{{ route('teacher.my-subjects.show', $session->subject_id) }}"
                               class="flex-shrink-0 inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-bold text-white shadow transition"
                               style="background:linear-gradient(135deg,#0071AA,#005a88)">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                </svg>
                                عرض
                            </a>
                        @endif
                    </div>
                    @empty
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-meta-4">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" class="text-gray-300">
                                <path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-500">لا توجد جلسات قادمة</p>
                        <a href="{{ route('teacher.schedule') }}" class="mt-2 text-xs text-primary hover:underline">إنشاء جلسة جديدة</a>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- My Subjects --}}
            <div class="overflow-hidden rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="flex items-center justify-between border-b border-stroke px-6 py-4 dark:border-strokedark">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                                <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-black dark:text-white">موادي الدراسية</h3>
                            <p class="text-xs text-gray-400">{{ $subjects->count() }} مادة مسجّلة</p>
                        </div>
                    </div>
                    <a href="{{ route('teacher.my-subjects.index') }}" class="text-xs font-medium text-primary hover:underline">عرض الكل</a>
                </div>

                @if($subjects->isEmpty())
                <div class="py-10 text-center text-sm text-gray-400">لا توجد مواد مسجّلة بعد</div>
                @else
                <div class="grid grid-cols-1 gap-px bg-stroke dark:bg-strokedark sm:grid-cols-2">
                    @foreach($subjects->take(6) as $subject)
                    @php
                        $colors = ['#0071AA','#10b981','#8b5cf6','#f59e0b','#ef4444','#06b6d4'];
                        $color  = $colors[$loop->index % count($colors)];
                    @endphp
                    <a href="{{ route('teacher.my-subjects.show', $subject->id) }}"
                       class="group flex items-center gap-4 bg-white px-5 py-4 transition hover:bg-gray-50 dark:bg-boxdark dark:hover:bg-meta-4">
                        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl text-white font-bold text-sm"
                             style="background:{{ $color }}">
                            {{ mb_substr($subject->name_ar ?? $subject->name ?? 'م', 0, 1) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-sm text-black dark:text-white group-hover:text-primary transition">
                                {{ $subject->name_ar ?? $subject->name }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $subject->enrollments_count ?? 0 }} طالب</p>
                        </div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="flex-shrink-0 text-gray-300 group-hover:text-primary transition" style="transform:rotate(180deg)">
                            <path d="M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z"/>
                        </svg>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>

        </div>

        {{-- RIGHT — Sidebar (1/3) --}}
        <div class="space-y-6">

            {{-- Mini Calendar --}}
            <div class="overflow-hidden rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="flex items-center gap-3 border-b border-stroke px-5 py-4 dark:border-strokedark">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:#0071AA">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zM7 12h5v5H7z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-black dark:text-white">التقويم</h3>
                </div>
                <div class="p-4">
                    @php
                        $today          = now();
                        $currentMonth   = $today->month;
                        $currentYear    = $today->year;
                        $firstDay       = $today->copy()->startOfMonth();
                        $daysInMonth    = $today->copy()->endOfMonth()->day;
                        $startDow       = $firstDay->dayOfWeek;
                        $sessionDates   = $upcomingSessions->pluck('scheduled_at')->map(fn($d) => $d ? $d->format('Y-m-d') : null)->filter()->toArray();
                        $arabicMonths   = ['','يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
                        $arabicDays     = ['ح','ن','ث','ر','خ','ج','س'];
                    @endphp

                    <div class="mb-3 text-center font-bold text-sm text-black dark:text-white">
                        {{ $arabicMonths[$currentMonth] }} {{ $currentYear }}
                    </div>
                    <div class="grid grid-cols-7 gap-0.5 mb-1">
                        @foreach($arabicDays as $d)
                            <div class="py-1 text-center text-xs font-semibold text-gray-400">{{ $d }}</div>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-7 gap-0.5">
                        @for($i = 0; $i < $startDow; $i++)
                            <div></div>
                        @endfor
                        @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $dateStr  = sprintf('%d-%02d-%02d', $currentYear, $currentMonth, $day);
                                $isToday  = $day === $today->day;
                                $hasSess  = in_array($dateStr, $sessionDates);
                            @endphp
                            <div class="relative flex aspect-square items-center justify-center rounded-lg text-xs
                                {{ $isToday ? 'font-black text-white' : ($hasSess ? 'font-semibold text-black dark:text-white' : 'text-gray-500 dark:text-gray-400') }}"
                                 style="{{ $isToday ? 'background:#0071AA' : '' }}">
                                {{ $day }}
                                @if($hasSess && !$isToday)
                                    <span class="absolute bottom-0.5 left-1/2 h-1 w-1 -translate-x-1/2 rounded-full" style="background:#0071AA"></span>
                                @endif
                            </div>
                        @endfor
                    </div>
                    <div class="mt-3 flex items-center justify-center gap-4 border-t border-stroke pt-3 dark:border-strokedark">
                        <div class="flex items-center gap-1.5 text-xs text-gray-400">
                            <span class="inline-block h-2.5 w-2.5 rounded" style="background:#0071AA"></span>اليوم
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-400">
                            <span class="inline-block h-1.5 w-1.5 rounded-full" style="background:#0071AA"></span>جلسة
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rating Summary --}}
            @if($teacherRating['total_ratings'] > 0)
            <div class="overflow-hidden rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="flex items-center gap-3 border-b border-stroke px-5 py-4 dark:border-strokedark">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-black dark:text-white">تقييمك</h3>
                        <p class="text-xs text-gray-400">{{ $teacherRating['total_ratings'] }} تقييم</p>
                    </div>
                </div>
                <div class="p-5">
                    <div class="mb-4 flex items-center gap-4">
                        <div class="text-4xl font-black text-black dark:text-white">{{ number_format($teacherRating['overall'], 1) }}</div>
                        <div>
                            <div class="flex gap-0.5">
                                @for($s = 1; $s <= 5; $s++)
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $s <= round($teacherRating['overall']) ? '#f59e0b' : '#e5e7eb' }}">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                @endfor
                            </div>
                            <p class="mt-1 text-xs text-gray-400">من 5 نجوم</p>
                        </div>
                    </div>
                    @if($recentFeedback->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($recentFeedback->take(2) as $fb)
                        <div class="rounded-xl bg-gray-50 p-3 dark:bg-meta-4">
                            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed line-clamp-2">"{{ $fb->comment }}"</p>
                            <p class="mt-1 text-xs text-gray-400">— {{ $fb->student->name ?? 'طالب' }}</p>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Quick Links --}}
            <div class="overflow-hidden rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke px-5 py-4 dark:border-strokedark">
                    <h3 class="font-bold text-black dark:text-white">روابط سريعة</h3>
                </div>
                <div class="p-4 space-y-2">
                    <a href="{{ route('teacher.schedule') }}"
                       class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-gray-50 dark:hover:bg-meta-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg" style="background:rgba(0,113,170,.1)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="#0071AA">
                                <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zM7 12h5v5H7z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">الجدول الدراسي</span>
                    </a>
                    <a href="{{ route('teacher.my-subjects.index') }}"
                       class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-gray-50 dark:hover:bg-meta-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg" style="background:rgba(139,92,246,.1)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="#8b5cf6">
                                <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">موادي الدراسية</span>
                    </a>
                    @if($openTicketsCount > 0)
                    <a href="{{ route('teacher.tickets.index') }}"
                       class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-gray-50 dark:hover:bg-meta-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg" style="background:rgba(239,68,68,.1)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="#ef4444">
                                <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V8l8 5 8-5v10zm-8-7L4 6h16l-8 5z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">التذاكر المفتوحة</span>
                        <span class="mr-auto inline-flex h-5 w-5 items-center justify-center rounded-full text-xs font-bold text-white" style="background:#ef4444">{{ $openTicketsCount }}</span>
                    </a>
                    @endif
                    @if($pendingSurveys > 0)
                    <div class="flex items-center gap-3 rounded-xl bg-amber-50 p-3 dark:bg-amber-900/20">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg" style="background:rgba(245,158,11,.15)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="#f59e0b">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-amber-700 dark:text-amber-400">{{ $pendingSurveys }} استبيان بانتظارك</span>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
