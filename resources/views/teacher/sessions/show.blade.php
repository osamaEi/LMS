@extends('layouts.dashboard')

@section('title', $session->title_ar ?? $session->title)

@push('styles')
<style>
    .live-pulse { animation: livePulse 2s cubic-bezier(.4,0,.6,1) infinite; }
    @keyframes livePulse { 0%,100%{opacity:1} 50%{opacity:.4} }
    .info-row:last-child { border-bottom: none !important; }
</style>
@endpush

@section('content')
@php
    $dt        = $session->scheduled_at ? \Carbon\Carbon::parse($session->scheduled_at) : null;
    $isLive    = $session->status === 'live';
    $isDone    = $session->status === 'completed';
    $isCancelled = $session->status === 'cancelled';
    $typeLabel = match($session->type ?? '') {
        'live_zoom'      => 'Zoom مباشر',
        'recorded_video' => 'فيديو مسجّل',
        'in_person'      => 'حضوري',
        default          => $session->type ?? '—',
    };
    $statusBg    = $isLive ? '#ef4444' : ($isDone ? '#10b981' : ($isCancelled ? '#6b7280' : '#0071AA'));
    $statusLabel = $isLive ? 'مباشر الآن' : ($isDone ? 'مكتملة' : ($isCancelled ? 'ملغاة' : 'مجدولة'));
@endphp

<div class="mx-auto max-w-screen-xl p-4 md:p-6 2xl:p-10">

    {{-- Breadcrumb --}}
    <div class="mb-5 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('teacher.my-subjects.show', $session->subject_id) }}"
           class="inline-flex items-center gap-1.5 font-medium transition hover:opacity-80" style="color:#0071AA">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
            </svg>
            {{ $session->subject->name_ar ?? $session->subject->name ?? 'المادة' }}
        </a>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="text-gray-300" style="transform:rotate(180deg)">
            <path d="M15.41 16.09l-4.58-4.59 4.58-4.59L14 5.5l-6 6 6 6z"/>
        </svg>
        <span class="truncate text-gray-400">{{ $session->title_ar ?? $session->title }}</span>
    </div>

    {{-- ══════════════════════════════════════
         Hero Header
    ══════════════════════════════════════ --}}
    <div class="relative mb-8 overflow-hidden rounded-2xl px-8 py-7 text-white shadow-xl"
         style="background:linear-gradient(135deg,#1e3a5f 0%,#0071AA 60%,#0ea5e9 100%)">
        <div class="pointer-events-none absolute -top-20 -right-20 h-64 w-64 rounded-full" style="background:rgba(255,255,255,.05)"></div>
        <div class="pointer-events-none absolute -bottom-12 -left-12 h-48 w-48 rounded-full" style="background:rgba(255,255,255,.05)"></div>

        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex-1 min-w-0">
                <div class="mb-2 flex flex-wrap items-center gap-2">
                    {{-- Status badge --}}
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold"
                          style="background:rgba(255,255,255,.2)">
                        @if($isLive)
                            <span class="h-2 w-2 rounded-full bg-white live-pulse"></span>
                        @else
                            <span class="h-2 w-2 rounded-full" style="background:{{ $statusBg === '#0071AA' ? '#7dd3fc' : 'white' }}"></span>
                        @endif
                        {{ $statusLabel }}
                    </span>
                    {{-- Type --}}
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
                          style="background:rgba(255,255,255,.15)">
                        {{ $typeLabel }}
                    </span>
                    @if($session->session_number)
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
                          style="background:rgba(255,255,255,.15)">
                        جلسة #{{ $session->session_number }}
                    </span>
                    @endif
                </div>
                <h1 class="text-2xl font-black tracking-tight sm:text-3xl">{{ $session->title_ar ?? $session->title }}</h1>
                @if($session->title_en && $session->title_en !== ($session->title_ar ?? ''))
                    <p class="mt-1 text-sm" style="color:rgba(255,255,255,.65)">{{ $session->title_en }}</p>
                @endif
                <p class="mt-2 flex items-center gap-1.5 text-sm" style="color:rgba(255,255,255,.7)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3L1 9l4 2.18V17h2v-4.68L9 13.4V17c0 2.21 1.34 4 3 4s3-1.79 3-4v-3.6l2-.92V17h2v-5.82L23 9 12 3z"/>
                    </svg>
                    {{ $session->subject->name_ar ?? $session->subject->name ?? '—' }}
                    @if($session->subject->term->program->name ?? null)
                        <span style="color:rgba(255,255,255,.4)">·</span>
                        {{ $session->subject->term->program->name }}
                    @endif
                </p>
            </div>

            {{-- Date / time block --}}
            @if($dt)
            <div class="flex-shrink-0 rounded-2xl px-6 py-4 text-center" style="background:rgba(255,255,255,.15)">
                <div class="text-3xl font-black leading-none">{{ $dt->format('d') }}</div>
                <div class="mt-0.5 text-sm font-semibold" style="color:rgba(255,255,255,.8)">{{ $dt->translatedFormat('F') }}</div>
                <div class="mt-2 text-lg font-bold">{{ $dt->format('H:i') }}</div>
                <div class="mt-0.5 text-xs" style="color:rgba(255,255,255,.6)">{{ $dt->diffForHumans() }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════
         Zoom Join Card (if applicable)
    ══════════════════════════════════════ --}}
    @if($session->type === 'live_zoom' && $session->zoom_meeting_id)
    <div class="mb-8 overflow-hidden rounded-2xl shadow-xl"
         style="background:linear-gradient(135deg,#065f46 0%,#059669 55%,#34d399 100%)">
        <div class="relative px-8 py-7 text-white">
            <div class="pointer-events-none absolute -top-16 -left-16 h-48 w-48 rounded-full" style="background:rgba(255,255,255,.06)"></div>

            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex-1">
                    <div class="mb-3 flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl" style="background:rgba(255,255,255,.2)">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="white">
                                <path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold">جلسة Zoom مباشرة</h2>
                            <p class="text-sm" style="color:rgba(255,255,255,.75)">انضم كمضيف وابدأ التدريس</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <div class="rounded-xl px-4 py-3" style="background:rgba(255,255,255,.15)">
                            <p class="text-[11px] font-medium mb-0.5" style="color:rgba(255,255,255,.7)">معرّف الاجتماع</p>
                            <p class="font-mono font-black text-base">{{ $session->zoom_meeting_id }}</p>
                        </div>
                        @if($session->zoom_password)
                        <div class="rounded-xl px-4 py-3" style="background:rgba(255,255,255,.15)">
                            <p class="text-[11px] font-medium mb-0.5" style="color:rgba(255,255,255,.7)">كلمة المرور</p>
                            <p class="font-mono font-black text-base">{{ $session->zoom_password }}</p>
                        </div>
                        @endif
                        @if($session->duration_minutes)
                        <div class="rounded-xl px-4 py-3" style="background:rgba(255,255,255,.15)">
                            <p class="text-[11px] font-medium mb-0.5" style="color:rgba(255,255,255,.7)">المدة</p>
                            <p class="font-black text-base">{{ $session->duration_minutes }} دقيقة</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="flex flex-shrink-0 flex-col gap-3">
                    <a href="{{ route('teacher.my-subjects.sessions.zoom', ['subjectId' => $session->subject_id, 'sessionId' => $session->id]) }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3.5 text-sm font-black shadow-lg transition hover:opacity-90"
                       style="background:white;color:#059669">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/>
                        </svg>
                        بدء / انضمام للحصة
                    </a>
                    @if($session->zoom_start_url)
                    <a href="{{ $session->zoom_start_url }}" target="_blank"
                       class="inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3 text-sm font-bold transition hover:opacity-80"
                       style="background:rgba(255,255,255,.2);color:white">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/>
                        </svg>
                        فتح في التطبيق
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════
         Main Grid
    ══════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- LEFT — Details + Files --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Session Details --}}
            <div class="overflow-hidden rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="flex items-center gap-3 border-b border-stroke px-6 py-4 dark:border-strokedark">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:linear-gradient(135deg,#0071AA,#005a88)">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="white">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-black dark:text-white">تفاصيل الجلسة</h3>
                </div>
                <div class="divide-y divide-stroke dark:divide-strokedark px-6">
                    @if($dt)
                    <div class="info-row flex items-center justify-between py-4">
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="color:#0071AA">
                                <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zM7 12h5v5H7z"/>
                            </svg>
                            التاريخ
                        </div>
                        <span class="font-bold text-sm text-black dark:text-white">
                            {{ $dt->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}
                        </span>
                    </div>
                    <div class="info-row flex items-center justify-between py-4">
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="color:#0071AA">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm.5 5v5.25l4.5 2.67-.75 1.23L11 13V7h1.5z"/>
                            </svg>
                            الوقت
                        </div>
                        <span class="font-bold text-sm text-black dark:text-white">{{ $dt->format('H:i') }}</span>
                    </div>
                    @endif
                    @if($session->duration_minutes)
                    <div class="info-row flex items-center justify-between py-4">
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="color:#8b5cf6">
                                <path d="M13 2.05V4.07c3.94.49 7 3.85 7 7.93s-3.06 7.44-7 7.93v2.02c5.06-.5 9-4.76 9-9.95S18.06 2.55 13 2.05zM11 2.05c-2.01.2-3.84 1-5.32 2.27L7.1 5.74C8.22 4.84 9.57 4.2 11 4.07V2.05zM5.74 7.11L4.27 5.63C3 7.12 2.2 8.97 2.05 11h2.02c.14-1.43.77-2.73 1.67-3.89zM4.07 13H2.05c.16 2.03.95 3.87 2.23 5.37l1.47-1.47c-.9-1.16-1.53-2.46-1.68-3.9zm1.69 6.76C7.17 20.98 9 21.77 11 21.94v-2.02c-1.42-.14-2.72-.78-3.9-1.68l-1.34 1.52z"/>
                            </svg>
                            المدة
                        </div>
                        <span class="font-bold text-sm text-black dark:text-white">{{ $session->duration_minutes }} دقيقة</span>
                    </div>
                    @endif
                    <div class="info-row flex items-center justify-between py-4">
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="color:#f59e0b">
                                <path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/>
                            </svg>
                            النوع
                        </div>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold text-white"
                              style="background:{{ $session->type === 'live_zoom' ? '#0071AA' : '#8b5cf6' }}">
                            {{ $typeLabel }}
                        </span>
                    </div>
                    <div class="info-row flex items-center justify-between py-4">
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="color:#10b981">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            الحالة
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold text-white"
                              style="background:{{ $statusBg }}">
                            @if($isLive)<span class="h-1.5 w-1.5 rounded-full bg-white live-pulse"></span>@endif
                            {{ $statusLabel }}
                        </span>
                    </div>
                    @if($session->description_ar || $session->description)
                    <div class="info-row py-4">
                        <p class="mb-2 flex items-center gap-1.5 text-sm text-gray-500">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="color:#0071AA">
                                <path d="M14 17H4v2h10v-2zm6-8H4v2h16V9zM4 15h16v-2H4v2zM4 5v2h16V5H4z"/>
                            </svg>
                            الوصف
                        </p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                            {{ $session->description_ar ?? $session->description }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Files --}}
            @if($session->files && $session->files->count() > 0)
            <div class="overflow-hidden rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="flex items-center justify-between border-b border-stroke px-6 py-4 dark:border-strokedark">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:linear-gradient(135deg,#ef4444,#dc2626)">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="white">
                                <path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm10 5.5h1v-3h-1v3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-black dark:text-white">الملفات المرفقة</h3>
                            <p class="text-xs text-gray-400">{{ $session->files->count() }} ملف</p>
                        </div>
                    </div>
                </div>
                <div class="divide-y divide-stroke dark:divide-strokedark">
                    @foreach($session->files as $file)
                    @php
                        $ext = strtolower(pathinfo($file->file_path ?? '', PATHINFO_EXTENSION));
                        $fileColor = match(true) {
                            in_array($ext, ['pdf'])            => '#ef4444',
                            in_array($ext, ['jpg','jpeg','png','gif','webp']) => '#8b5cf6',
                            in_array($ext, ['doc','docx'])     => '#3b82f6',
                            in_array($ext, ['xls','xlsx'])     => '#10b981',
                            in_array($ext, ['ppt','pptx'])     => '#f97316',
                            default                            => '#6b7280',
                        };
                        $fileIcon = in_array($ext, ['pdf']) ? 'pdf' : (in_array($ext, ['jpg','jpeg','png','gif','webp']) ? 'img' : 'doc');
                        $sizeKb = $file->file_size ? round($file->file_size / 1024, 1) : null;
                        $sizeLabel = $sizeKb ? ($sizeKb >= 1024 ? round($sizeKb/1024, 1).' MB' : $sizeKb.' KB') : '';
                    @endphp
                    <div class="flex items-center gap-4 px-6 py-4 transition hover:bg-gray-50 dark:hover:bg-meta-4">
                        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl text-white font-bold text-xs uppercase"
                             style="background:{{ $fileColor }}">
                            {{ $ext ?: 'F' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-black dark:text-white truncate">{{ $file->title ?? basename($file->file_path ?? '') }}</p>
                            @if($sizeLabel)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $sizeLabel }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold text-white shadow transition hover:opacity-90"
                               style="background:linear-gradient(135deg,#0071AA,#005a88)">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                </svg>
                                عرض
                            </a>
                            <a href="{{ asset('storage/' . $file->file_path) }}" download
                               class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold text-white shadow transition hover:opacity-90"
                               style="background:linear-gradient(135deg,#10b981,#059669)">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                                </svg>
                                تحميل
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT — Sidebar --}}
        <div class="space-y-5">

            {{-- Quick Actions --}}
            <div class="overflow-hidden rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke px-5 py-4 dark:border-strokedark">
                    <h3 class="font-bold text-black dark:text-white">إجراءات سريعة</h3>
                </div>
                <div class="space-y-2 p-4">
                    @if($session->zoom_start_url)
                    <a href="{{ $session->zoom_start_url }}" target="_blank"
                       class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-bold text-white shadow transition hover:opacity-90"
                       style="background:linear-gradient(135deg,#22c55e,#16a34a)">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/>
                        </svg>
                        ابدأ في تطبيق Zoom
                    </a>
                    @endif

                    <a href="{{ route('teacher.my-subjects.sessions.attendance', ['subjectId' => $session->subject_id, 'sessionId' => $session->id]) }}"
                       class="flex items-center gap-3 rounded-xl border border-stroke px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-strokedark dark:text-gray-300 dark:hover:bg-meta-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg flex-shrink-0" style="background:rgba(139,92,246,.1)">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="#8b5cf6">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        الحضور والغياب
                    </a>

                    <a href="{{ route('teacher.my-subjects.sessions.edit', ['subjectId' => $session->subject_id, 'sessionId' => $session->id]) }}"
                       class="flex items-center gap-3 rounded-xl border border-stroke px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-strokedark dark:text-gray-300 dark:hover:bg-meta-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg flex-shrink-0" style="background:rgba(245,158,11,.1)">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="#f59e0b">
                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                            </svg>
                        </div>
                        تعديل الجلسة
                    </a>

                    <a href="{{ route('teacher.my-subjects.show', $session->subject_id) }}"
                       class="flex items-center gap-3 rounded-xl border border-stroke px-4 py-3 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-strokedark dark:text-gray-300 dark:hover:bg-meta-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg flex-shrink-0" style="background:rgba(0,113,170,.1)">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="#0071AA">
                                <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                            </svg>
                        </div>
                        صفحة المادة
                    </a>
                </div>
            </div>

            {{-- Share Join Link --}}
            @if($session->zoom_join_url)
            <div class="overflow-hidden rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke px-5 py-4 dark:border-strokedark">
                    <h3 class="font-bold text-black dark:text-white">رابط الطلاب</h3>
                    <p class="text-xs text-gray-400 mt-0.5">شارك هذا الرابط مع طلابك</p>
                </div>
                <div class="p-4">
                    <div class="flex gap-2">
                        <input type="text" value="{{ $session->zoom_join_url }}" readonly id="join-url"
                               class="flex-1 rounded-xl border border-stroke bg-gray-50 px-3 py-2.5 text-xs font-mono text-gray-600 outline-none dark:border-strokedark dark:bg-meta-4 dark:text-gray-300">
                        <button onclick="copyJoinLink()" id="copy-btn"
                                class="flex-shrink-0 flex h-10 w-10 items-center justify-center rounded-xl text-white shadow transition hover:opacity-90"
                                style="background:linear-gradient(135deg,#0071AA,#005a88)">
                            <svg id="copy-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
                            </svg>
                        </button>
                    </div>
                    <p id="copy-msg" class="mt-2 hidden text-center text-xs font-medium" style="color:#10b981">تم النسخ!</p>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<script>
function copyJoinLink() {
    const input = document.getElementById('join-url');
    const msg   = document.getElementById('copy-msg');
    const btn   = document.getElementById('copy-btn');
    navigator.clipboard.writeText(input.value).then(() => {
        btn.style.background = 'linear-gradient(135deg,#10b981,#059669)';
        document.getElementById('copy-icon').innerHTML = '<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>';
        msg.classList.remove('hidden');
        setTimeout(() => {
            btn.style.background = 'linear-gradient(135deg,#0071AA,#005a88)';
            document.getElementById('copy-icon').innerHTML = '<path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>';
            msg.classList.add('hidden');
        }, 2500);
    }).catch(() => {
        input.select();
        document.execCommand('copy');
    });
}
</script>
@endsection
