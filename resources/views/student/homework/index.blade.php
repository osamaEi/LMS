@extends('layouts.dashboard')

@section('title', 'الواجبات المنزلية')

@section('content')
<div class="mx-auto max-w-screen-xl p-4 md:p-6 2xl:p-10">

    {{-- Header --}}
    <div class="relative mb-8 overflow-hidden rounded-2xl px-8 py-7 text-white shadow-xl"
         style="background:linear-gradient(135deg,#f59e0b 0%,#d97706 55%,#b45309 100%)">
        <div class="pointer-events-none absolute -top-20 -right-20 h-64 w-64 rounded-full" style="background:rgba(255,255,255,.06)"></div>
        <div class="relative flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">الواجبات المنزلية</h1>
                <p class="mt-1 text-sm" style="color:rgba(255,255,255,.75)">جميع الواجبات المرتبطة بجلساتك</p>
            </div>
            <div class="rounded-xl px-5 py-3 text-center" style="background:rgba(255,255,255,.15)">
                <div class="text-2xl font-bold">{{ $homeworks->count() }}</div>
                <div class="text-xs" style="color:rgba(255,255,255,.75)">واجب</div>
            </div>
        </div>
    </div>

    @if($homeworks->isEmpty())
    <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-stroke py-20 text-center dark:border-strokedark">
        <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full" style="background:#fef3c7">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="#f59e0b">
                <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
            </svg>
        </div>
        <p class="font-semibold text-gray-500">لا توجد واجبات حتى الآن</p>
        <p class="mt-1 text-sm text-gray-400">ستظهر هنا الواجبات التي يضيفها المدرسون بعد كل جلسة</p>
    </div>
    @else
    <div class="space-y-4">
        @foreach($homeworks as $hw)
        @php
            $isOverdue  = $hw->due_date && $hw->due_date->isPast();
            $isDueSoon  = $hw->due_date && !$isOverdue && $hw->due_date->diffInDays(now()) <= 3;
        @endphp
        <div class="overflow-hidden rounded-2xl border bg-white shadow-sm dark:bg-boxdark"
             style="border-color:{{ $isOverdue ? '#fca5a5' : ($isDueSoon ? '#fde68a' : '#e5e7eb') }}">
            <div class="flex items-stretch">
                {{-- Color stripe --}}
                <div class="w-1.5 flex-shrink-0"
                     style="background:{{ $isOverdue ? '#ef4444' : ($isDueSoon ? '#f59e0b' : '#0071AA') }}"></div>

                <div class="flex-1 p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex-1">
                            {{-- Session / Subject info --}}
                            <div class="mb-2 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                <span class="flex items-center gap-1">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 3L1 9l4 2.18V17h2v-4.68L9 13.4V17c0 2.21 1.34 4 3 4s3-1.79 3-4v-3.6l2-.92V17h2v-5.82L23 9 12 3zm0 2.19L19.26 9 12 12.57 4.74 9 12 5.19z"/>
                                    </svg>
                                    {{ $hw->session->subject->name_ar ?? '—' }}
                                </span>
                                <span>·</span>
                                <span>{{ $hw->session->title ?? ('جلسة #' . $hw->session->session_number) }}</span>
                            </div>

                            {{-- Title --}}
                            <h3 class="text-base font-bold text-black dark:text-white">
                                {{ $hw->title_ar ?: $hw->title_en ?: 'واجب بدون عنوان' }}
                            </h3>

                            {{-- Description --}}
                            @if($hw->description_ar || $hw->description_en)
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line leading-relaxed">
                                {{ $hw->description_ar ?: $hw->description_en }}
                            </p>
                            @endif

                            {{-- Meta --}}
                            <div class="mt-3 flex flex-wrap gap-3 text-xs">
                                @if($hw->due_date)
                                <span class="flex items-center gap-1 font-medium"
                                      style="color:{{ $isOverdue ? '#dc2626' : ($isDueSoon ? '#b45309' : '#6b7280') }}">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/>
                                    </svg>
                                    موعد التسليم: {{ $hw->due_date->format('Y/m/d') }}
                                    @if($isOverdue)
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold text-white" style="background:#ef4444">متأخر</span>
                                    @elseif($isDueSoon)
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold text-white" style="background:#f59e0b">قريباً</span>
                                    @endif
                                </span>
                                @endif
                                <span class="flex items-center gap-1 text-gray-400">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm4.24 16L11 14.61V7h1.5v6.86l4.62 2.75-1.38 2.39z"/>
                                    </svg>
                                    أُضيف {{ $hw->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        {{-- File download --}}
                        @if($hw->file_path)
                        <div class="flex-shrink-0">
                            <a href="{{ asset('storage/' . $hw->file_path) }}" target="_blank" download
                               class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold text-white shadow transition hover:opacity-90"
                               style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"/>
                                </svg>
                                تحميل الواجب
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
