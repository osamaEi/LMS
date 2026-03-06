@extends('layouts.dashboard')

@section('title', 'الواجبات المنزلية')

@section('content')
<div class="mx-auto max-w-screen-xl p-4 md:p-6 2xl:p-10">

    {{-- Flash --}}
    @if(session('success'))
    <div class="mb-5 flex items-center gap-3 rounded-xl border px-5 py-4"
         style="background:#f0fdf4;border-color:#bbf7d0;color:#15803d">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Header --}}
    <div class="relative mb-8 overflow-hidden rounded-2xl px-8 py-7 text-white shadow-xl"
         style="background:linear-gradient(135deg,#f59e0b 0%,#d97706 55%,#b45309 100%)">
        <div class="pointer-events-none absolute -top-20 -right-20 h-64 w-64 rounded-full" style="background:rgba(255,255,255,.06)"></div>
        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">الواجبات المنزلية</h1>
                <p class="mt-1 text-sm" style="color:rgba(255,255,255,.75)">أضف وأدر الواجبات لكل جلسة</p>
            </div>
            <div class="flex gap-3">
                <div class="rounded-xl px-5 py-3 text-center" style="background:rgba(255,255,255,.15)">
                    <div class="text-2xl font-bold">{{ $withHomework->count() }}</div>
                    <div class="text-xs" style="color:rgba(255,255,255,.75)">مع واجب</div>
                </div>
                <div class="rounded-xl px-5 py-3 text-center" style="background:rgba(255,255,255,.15)">
                    <div class="text-2xl font-bold">{{ $withoutHomework->count() }}</div>
                    <div class="text-xs" style="color:rgba(255,255,255,.75)">بدون واجب</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">

        {{-- ══ Sessions WITH homework ══ --}}
        <div>
            <div class="mb-4 flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg" style="background:linear-gradient(135deg,#10b981,#059669)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                </div>
                <h2 class="font-bold text-black dark:text-white">جلسات لديها واجب</h2>
                <span class="rounded-full px-2.5 py-0.5 text-xs font-bold text-white" style="background:#10b981">{{ $withHomework->count() }}</span>
            </div>

            @if($withHomework->isEmpty())
            <div class="rounded-2xl border-2 border-dashed border-stroke py-10 text-center dark:border-strokedark">
                <p class="text-sm text-gray-400">لا توجد جلسات مع واجب بعد</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($withHomework as $session)
                <div class="overflow-hidden rounded-xl border bg-white shadow-sm dark:bg-boxdark" style="border-color:#bbf7d0">
                    <div class="flex items-stretch">
                        <div class="w-1 flex-shrink-0" style="background:#10b981"></div>
                        <div class="flex-1 p-4">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-400 mb-0.5">{{ $session->subject->name_ar ?? '—' }}</p>
                                    <h4 class="font-semibold text-sm text-black dark:text-white truncate">
                                        {{ $session->title_ar ?: $session->title_en ?: ('جلسة #' . $session->session_number) }}
                                    </h4>
                                    <p class="mt-1 text-xs font-medium" style="color:#d97706">
                                        📋 {{ $session->homework->title_ar ?: $session->homework->title_en ?: 'واجب بدون عنوان' }}
                                    </p>
                                    @if($session->homework->due_date)
                                    <p class="mt-0.5 text-xs text-gray-400">
                                        موعد التسليم: {{ $session->homework->due_date->format('Y/m/d') }}
                                    </p>
                                    @endif
                                </div>
                                <div class="flex gap-1.5 flex-shrink-0">
                                    <a href="{{ route('teacher.sessions.show', $session) }}"
                                       class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-bold text-white"
                                       style="background:#0071AA">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                        تعديل
                                    </a>
                                    <form action="{{ route('teacher.sessions.homework.destroy', $session) }}" method="POST"
                                          onsubmit="return confirm('حذف الواجب؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-bold text-white"
                                                style="background:#ef4444">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ══ Sessions WITHOUT homework ══ --}}
        <div>
            <div class="mb-4 flex items-center gap-2">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                </div>
                <h2 class="font-bold text-black dark:text-white">جلسات بدون واجب</h2>
                <span class="rounded-full px-2.5 py-0.5 text-xs font-bold text-white" style="background:#f59e0b">{{ $withoutHomework->count() }}</span>
            </div>

            @if($withoutHomework->isEmpty())
            <div class="rounded-2xl border-2 border-dashed border-stroke py-10 text-center dark:border-strokedark">
                <p class="text-sm text-gray-400">جميع الجلسات لديها واجب</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($withoutHomework as $session)
                <div class="overflow-hidden rounded-xl border bg-white shadow-sm dark:bg-boxdark" style="border-color:#e5e7eb">
                    <div class="flex items-stretch">
                        <div class="w-1 flex-shrink-0" style="background:#d1d5db"></div>
                        <div class="flex-1 p-4">
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-xs text-gray-400 mb-0.5">{{ $session->subject->name_ar ?? '—' }}</p>
                                    <h4 class="font-semibold text-sm text-black dark:text-white truncate">
                                        {{ $session->title_ar ?: $session->title_en ?: ('جلسة #' . $session->session_number) }}
                                    </h4>
                                    @if($session->scheduled_at)
                                    <p class="mt-0.5 text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d H:i') }}
                                    </p>
                                    @endif
                                </div>
                                <a href="{{ route('teacher.sessions.show', $session) }}"
                                   class="flex-shrink-0 inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-bold text-white transition hover:opacity-90"
                                   style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                                    إضافة واجب
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
