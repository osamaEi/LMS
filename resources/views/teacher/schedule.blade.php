@extends('layouts.dashboard')

@section('title', 'الجدول الدراسي')

@push('styles')
<style>
    .session-card { transition: transform .2s ease, box-shadow .2s ease; }
    .session-card:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(0,0,0,.08); }
    .live-pulse { animation: livePulse 2s cubic-bezier(.4,0,.6,1) infinite; }
    @keyframes livePulse { 0%,100%{opacity:1} 50%{opacity:.4} }
    .day-pill-line::before {
        content:''; position:absolute; top:50%; left:0; right:0;
        height:1px; background:linear-gradient(90deg,transparent,#e2e8f0 15%,#e2e8f0 85%,transparent);
    }
    .session-row-enter { animation: rowSlide .25s ease; }
    @keyframes rowSlide { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')
<div class="mx-auto max-w-screen-xl p-4 md:p-6 2xl:p-10">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 rounded-xl border px-5 py-4"
         style="background:#f0fdf4;border-color:#bbf7d0;color:#15803d">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="flex-shrink-0">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
        </svg>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif
    @if($errors->any())
    <div class="mb-6 rounded-xl border px-5 py-4" style="background:#fef2f2;border-color:#fecaca;color:#dc2626">
        <p class="font-semibold mb-1">يرجى تصحيح الأخطاء التالية:</p>
        <ul class="list-disc list-inside text-sm space-y-0.5">
            @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
    </div>
    @endif

    {{-- ══════════════════════════════════════
         Hero Header
    ══════════════════════════════════════ --}}
    <div class="relative mb-8 overflow-hidden rounded-2xl px-8 py-7 text-white shadow-xl"
         style="background:linear-gradient(135deg,#1e3a5f 0%,#0071AA 55%,#0ea5e9 100%)">
        <div class="pointer-events-none absolute -top-20 -right-20 h-64 w-64 rounded-full" style="background:rgba(255,255,255,.05)"></div>
        <div class="pointer-events-none absolute -bottom-12 -left-12 h-48 w-48 rounded-full" style="background:rgba(255,255,255,.05)"></div>

        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="mb-1 text-sm font-medium" style="color:rgba(255,255,255,.7)">{{ now()->translatedFormat('l، d F Y') }}</p>
                <h1 class="text-3xl font-bold tracking-tight">الجدول الدراسي</h1>
                <p class="mt-1 text-sm" style="color:rgba(255,255,255,.7)">نظرة شاملة على جميع جلساتك ومحاضراتك</p>
            </div>

            <div class="grid grid-cols-4 gap-2 sm:gap-3">
                <div class="rounded-xl px-4 py-3 text-center" style="background:rgba(255,255,255,.12)">
                    <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                    <div class="text-xs mt-0.5" style="color:rgba(255,255,255,.7)">إجمالي</div>
                </div>
                <div class="rounded-xl px-4 py-3 text-center" style="background:rgba(255,255,255,.12)">
                    <div class="text-2xl font-bold" style="color:#fde68a">{{ $stats['upcoming'] }}</div>
                    <div class="text-xs mt-0.5" style="color:rgba(255,255,255,.7)">قادمة</div>
                </div>
                @if($stats['live'] > 0)
                <div class="rounded-xl px-4 py-3 text-center" style="background:rgba(239,68,68,.7)">
                    <div class="text-2xl font-bold live-pulse">{{ $stats['live'] }}</div>
                    <div class="text-xs mt-0.5" style="color:rgba(255,255,255,.8)">مباشر</div>
                </div>
                @else
                <div class="rounded-xl px-4 py-3 text-center" style="background:rgba(255,255,255,.12)">
                    <div class="text-2xl font-bold" style="color:#86efac">{{ $stats['completed'] }}</div>
                    <div class="text-xs mt-0.5" style="color:rgba(255,255,255,.7)">مكتملة</div>
                </div>
                @endif
                <div class="rounded-xl px-4 py-3 text-center" style="background:rgba(255,255,255,.12)">
                    <div class="text-2xl font-bold" style="color:rgba(255,255,255,.85)">{{ $past->count() }}</div>
                    <div class="text-xs mt-0.5" style="color:rgba(255,255,255,.7)">سابقة</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ══════════════════════════════════════
             LEFT — Upcoming Sessions (2/3)
        ══════════════════════════════════════ --}}
        <div class="lg:col-span-2">

            {{-- Section header --}}
            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:linear-gradient(135deg,#0071AA,#005a88)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                        <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zM7 12h5v5H7z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-black dark:text-white">الجلسات القادمة</h2>
            </div>

            @if($groupedUpcoming->isEmpty())
            <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-stroke py-16 text-center dark:border-strokedark">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full" style="background:#eff6ff">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="#93c5fd">
                        <path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-500">لا توجد جلسات قادمة</p>
                <p class="mt-1 text-sm text-gray-400">أضف جلسات من نموذج الإنشاء ←</p>
            </div>
            @else
                @foreach($groupedUpcoming as $dayLabel => $daySessions)
                    {{-- Day pill --}}
                    <div class="day-pill-line relative mb-4 mt-6 first:mt-0 flex items-center justify-center">
                        <span class="relative z-10 inline-flex items-center gap-2 rounded-full border px-4 py-1.5 text-sm font-semibold shadow-sm bg-white dark:bg-boxdark"
                              style="{{ $dayLabel === 'اليوم' ? 'border-color:#bfdbfe;color:#1d4ed8;background:#eff6ff' : ($dayLabel === 'غداً' ? 'border-color:#e9d5ff;color:#7c3aed' : 'border-color:#e5e7eb;color:#6b7280') }}">
                            <span class="h-2 w-2 rounded-full live-pulse"
                                  style="background:{{ $dayLabel === 'اليوم' ? '#3b82f6' : ($dayLabel === 'غداً' ? '#8b5cf6' : '#d1d5db') }}"></span>
                            {{ $dayLabel }}
                        </span>
                    </div>

                    <div class="mb-2 space-y-3">
                        @foreach($daySessions as $session)
                            @php
                                $dt        = \Carbon\Carbon::parse($session->scheduled_at);
                                $isLive    = $session->status === 'live';
                                $isToday   = $dt->isToday();
                                $typeLabel = match($session->type ?? '') {
                                    'live_zoom'      => 'Zoom',
                                    'recorded_video' => 'مسجّل',
                                    'in_person'      => 'حضوري',
                                    default          => ucfirst($session->type ?? ''),
                                };
                                $accentColor = $isLive ? '#ef4444' : ($isToday ? '#0071AA' : '#d1d5db');
                                $borderColor = $isLive ? '#fca5a5' : ($isToday ? '#bfdbfe' : '#e5e7eb');
                            @endphp

                            <div class="session-card flex items-stretch overflow-hidden rounded-2xl border bg-white shadow-sm dark:bg-boxdark"
                                 style="border-color:{{ $borderColor }}">

                                {{-- Accent stripe --}}
                                <div class="w-1.5 flex-shrink-0" style="background:{{ $accentColor }}"></div>

                                {{-- Time --}}
                                <div class="flex w-[72px] flex-shrink-0 flex-col items-center justify-center border-l border-stroke px-2 py-4 dark:border-strokedark">
                                    <span class="text-2xl font-black leading-none text-black dark:text-white">{{ $dt->format('H') }}</span>
                                    <span class="mt-0.5 text-sm font-semibold text-gray-400">{{ $dt->format('i') }}</span>
                                    <span class="mt-1 text-[10px] uppercase text-gray-300">{{ $dt->format('A') === 'AM' ? 'ص' : 'م' }}</span>
                                </div>

                                {{-- Body --}}
                                <div class="flex flex-1 min-w-0 flex-col justify-center gap-2 px-4 py-4">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-black dark:text-white">{{ $session->title }}</h3>
                                            <p class="mt-0.5 flex items-center gap-1 text-sm text-gray-500">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" class="flex-shrink-0">
                                                    <path d="M12 3L1 9l4 2.18V17h2v-4.68L9 13.4V17c0 2.21 1.34 4 3 4s3-1.79 3-4v-3.6l2-.92V17h2v-5.82L23 9 12 3zm0 2.19L19.26 9 12 12.57 4.74 9 12 5.19z"/>
                                                </svg>
                                                {{ $session->subject->name ?? '—' }}
                                            </p>
                                        </div>
                                        <div class="flex flex-wrap gap-1.5">
                                            @if($isLive)
                                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-bold text-white"
                                                      style="background:#ef4444">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-white live-pulse"></span> مباشر
                                                </span>
                                            @elseif($isToday)
                                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"
                                                      style="background:#dbeafe;color:#1d4ed8">اليوم</span>
                                            @endif
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs"
                                                  style="background:#f3f4f6;color:#4b5563">{{ $typeLabel }}</span>
                                            @if($session->session_number)
                                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium"
                                                      style="background:#eef2ff;color:#4f46e5">#{{ $session->session_number }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-400">{{ $dt->diffForHumans() }} · {{ $session->duration_minutes ?? 60 }} دقيقة</p>
                                </div>

                                {{-- Actions --}}
                                <div class="flex flex-shrink-0 flex-col items-center justify-center gap-2 border-l border-stroke px-4 py-4 dark:border-strokedark">
                                    @if($session->zoom_join_url)
                                        {{-- Join via browser --}}
                                        <a href="{{ $session->zoom_join_url }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold text-white shadow transition hover:opacity-90"
                                           style="background:linear-gradient(135deg,#22c55e,#16a34a)">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/>
                                            </svg>
                                            انضم
                                        </a>
                                        {{-- Open in Zoom App --}}
                                        <button type="button"
                                                onclick="openZoomApp('{{ $session->zoom_join_url }}')"
                                                class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold text-white shadow transition hover:opacity-90"
                                                style="background:linear-gradient(135deg,#2563eb,#1d4ed8)">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"/>
                                            </svg>
                                            التطبيق
                                        </button>
                                    @else
                                        <a href="{{ route('teacher.my-subjects.show', $session->subject_id) }}"
                                           class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2.5 text-xs font-bold text-white shadow-md transition hover:opacity-90"
                                           style="background:linear-gradient(135deg,#0071AA,#005a88)">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                            </svg>
                                            عرض المادة
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @endif
        </div>

        {{-- ══════════════════════════════════════
             RIGHT — Create Sessions Form (1/3)
        ══════════════════════════════════════ --}}
        <div class="lg:col-span-1">
            <div class="sticky top-6 overflow-hidden rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">

                {{-- Panel header --}}
                <div class="flex items-center gap-3 border-b border-stroke px-5 py-4 dark:border-strokedark"
                     style="background:linear-gradient(135deg,#0071AA,#005a88)">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(255,255,255,.2)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-sm">إنشاء جلسات جديدة</h3>
                        <p class="text-xs" style="color:rgba(255,255,255,.7)">أضف عدة جلسات دفعة واحدة</p>
                    </div>
                </div>

                {{-- Form --}}
                <form action="{{ route('teacher.schedule.sessions.store') }}" method="POST" id="bulkForm">
                    @csrf
                    <div class="p-5">

                        <div id="sessionRows" class="space-y-4 mb-4">
                            <div class="session-row session-row-enter rounded-xl border border-stroke bg-gray-50 p-4 dark:border-strokedark dark:bg-meta-4" data-index="0">
                                <div class="mb-3 flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-600 dark:text-gray-400 row-label">جلسة 1</span>
                                    <button type="button" onclick="removeRow(this)"
                                            class="hidden remove-btn flex h-6 w-6 items-center justify-center rounded-full transition"
                                            style="background:#fee2e2;color:#ef4444">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                                    </button>
                                </div>

                                <div class="mb-3">
                                    <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">المادة <span style="color:#ef4444">*</span></label>
                                    <select name="sessions[0][subject_id]" required
                                            class="w-full rounded-lg border border-stroke bg-white py-2 px-3 text-sm text-black outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
                                        <option value="">— اختر المادة —</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">عنوان الجلسة (عربي) <span style="color:#ef4444">*</span></label>
                                    <input type="text" name="sessions[0][title_ar]" required placeholder="مثال: مقدمة في البرمجة"
                                           class="w-full rounded-lg border border-stroke bg-white py-2 px-3 text-sm text-black outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
                                </div>

                                <div class="mb-3">
                                    <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">عنوان الجلسة (إنجليزي) <span style="color:#ef4444">*</span></label>
                                    <input type="text" name="sessions[0][title_en]" required placeholder="e.g. Introduction to Programming"
                                           class="w-full rounded-lg border border-stroke bg-white py-2 px-3 text-sm text-black outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
                                </div>

                                <div class="mb-3">
                                    <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">النوع <span style="color:#ef4444">*</span></label>
                                    <select name="sessions[0][type]" required
                                            class="w-full rounded-lg border border-stroke bg-white py-2 px-3 text-sm text-black outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
                                        <option value="live_zoom">Zoom مباشر</option>
                                        <option value="recorded_video">مسجّل</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">التاريخ والوقت <span style="color:#ef4444">*</span></label>
                                        <input type="datetime-local" name="sessions[0][scheduled_at]" required
                                               min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                                               class="w-full rounded-lg border border-stroke bg-white py-2 px-3 text-xs text-black outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">المدة (دقيقة)</label>
                                        <input type="number" name="sessions[0][duration_minutes]" value="60" min="15" max="480" step="15"
                                               class="w-full rounded-lg border border-stroke bg-white py-2 px-3 text-sm text-black outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Add row --}}
                        <button type="button" onclick="addRow()"
                                class="mb-5 flex w-full items-center justify-center gap-2 rounded-xl border-2 border-dashed border-stroke py-3 text-sm font-medium text-gray-500 transition hover:text-primary dark:border-strokedark"
                                style="border-style:dashed">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                            </svg>
                            إضافة جلسة أخرى
                        </button>

                        {{-- Submit --}}
                        <button type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-xl py-3 text-sm font-bold text-white shadow-md transition hover:opacity-90"
                                style="background:linear-gradient(135deg,#0071AA,#005a88)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                            </svg>
                            حفظ الجلسات
                        </button>

                        <p class="mt-3 text-center text-xs text-gray-400">
                            رابط Zoom يُضاف لاحقاً من صفحة المادة
                        </p>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
let rowCount = 1;
const subjectOptions = `{!! $subjects->map(fn($s) => '<option value="'.$s->id.'">'.e($s->name_ar).'</option>')->join('') !!}`;

function addRow() {
    const idx = rowCount++;
    const html = `
    <div class="session-row session-row-enter rounded-xl border border-stroke bg-gray-50 p-4 dark:border-strokedark dark:bg-meta-4" data-index="${idx}">
        <div class="mb-3 flex items-center justify-between">
            <span class="text-xs font-bold text-gray-600 dark:text-gray-400 row-label">جلسة ${rowCount}</span>
            <button type="button" onclick="removeRow(this)"
                    class="remove-btn flex h-6 w-6 items-center justify-center rounded-full transition"
                    style="background:#fee2e2;color:#ef4444">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            </button>
        </div>
        <div class="mb-3">
            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">المادة <span style="color:#ef4444">*</span></label>
            <select name="sessions[${idx}][subject_id]" required class="w-full rounded-lg border border-stroke bg-white py-2 px-3 text-sm text-black outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
                <option value="">— اختر المادة —</option>${subjectOptions}
            </select>
        </div>
        <div class="mb-3">
            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">عنوان الجلسة (عربي) <span style="color:#ef4444">*</span></label>
            <input type="text" name="sessions[${idx}][title_ar]" required placeholder="مثال: مقدمة في البرمجة"
                   class="w-full rounded-lg border border-stroke bg-white py-2 px-3 text-sm text-black outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
        </div>
        <div class="mb-3">
            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">عنوان الجلسة (إنجليزي) <span style="color:#ef4444">*</span></label>
            <input type="text" name="sessions[${idx}][title_en]" required placeholder="e.g. Introduction to Programming"
                   class="w-full rounded-lg border border-stroke bg-white py-2 px-3 text-sm text-black outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
        </div>
        <div class="mb-3">
            <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">النوع <span style="color:#ef4444">*</span></label>
            <select name="sessions[${idx}][type]" required class="w-full rounded-lg border border-stroke bg-white py-2 px-3 text-sm text-black outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
                <option value="live_zoom">Zoom مباشر</option>
                <option value="recorded_video">مسجّل</option>
            </select>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">التاريخ والوقت <span style="color:#ef4444">*</span></label>
                <input type="datetime-local" name="sessions[${idx}][scheduled_at]" required
                       min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                       class="w-full rounded-lg border border-stroke bg-white py-2 px-3 text-xs text-black outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">المدة (دقيقة)</label>
                <input type="number" name="sessions[${idx}][duration_minutes]" value="60" min="15" max="480" step="15"
                       class="w-full rounded-lg border border-stroke bg-white py-2 px-3 text-sm text-black outline-none focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white">
            </div>
        </div>
    </div>`;
    document.getElementById('sessionRows').insertAdjacentHTML('beforeend', html);
    updateRemoveButtons();
}

function removeRow(btn) {
    btn.closest('.session-row').remove();
    document.querySelectorAll('#sessionRows .row-label').forEach((el, i) => {
        el.textContent = `جلسة ${i + 1}`;
    });
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('#sessionRows .session-row');
    rows.forEach(row => {
        const btn = row.querySelector('.remove-btn');
        if (btn) btn.classList.toggle('hidden', rows.length === 1);
    });
}

// Open Zoom session in the desktop/mobile app directly
function openZoomApp(joinUrl) {
    try {
        // Extract meeting ID and password from the join URL
        // e.g. https://zoom.us/j/12345678?pwd=XXXXX
        const url   = new URL(joinUrl);
        const parts = url.pathname.split('/');
        const confno = parts[parts.length - 1];
        const pwd    = url.searchParams.get('pwd') || '';
        const deep   = 'zoommtg://zoom.us/join?confno=' + confno + (pwd ? '&pwd=' + pwd : '') + '&zc=0';
        window.location.href = deep;
        // Fallback: if app not installed, open web after short delay
        setTimeout(() => { window.open(joinUrl, '_blank'); }, 1500);
    } catch(e) {
        window.open(joinUrl, '_blank');
    }
}
</script>
@endsection
