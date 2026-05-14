@extends('layouts.dashboard')

@section('title', 'دورات اللغة الإنجليزية')

@section('content')

@php
$palette = [
    0  => ['bg'=>'#0ea5e9','light'=>'#e0f2fe','text'=>'#0369a1','label'=>'تمهيدي',    'abbr'=>'PH'],
    1  => ['bg'=>'#3b82f6','light'=>'#dbeafe','text'=>'#1d4ed8','label'=>'تأسيسي',    'abbr'=>'FN'],
    2  => ['bg'=>'#6366f1','light'=>'#e0e7ff','text'=>'#4338ca','label'=>'مبتدئ',     'abbr'=>'BG'],
    3  => ['bg'=>'#8b5cf6','light'=>'#ede9fe','text'=>'#6d28d9','label'=>'مستوى 1',  'abbr'=>'L1'],
    4  => ['bg'=>'#a855f7','light'=>'#fae8ff','text'=>'#7e22ce','label'=>'مستوى 2',  'abbr'=>'L2'],
    5  => ['bg'=>'#d946ef','light'=>'#fdf4ff','text'=>'#a21caf','label'=>'مستوى 3',  'abbr'=>'L3'],
    6  => ['bg'=>'#ec4899','light'=>'#fce7f3','text'=>'#be185d','label'=>'مستوى 4',  'abbr'=>'L4'],
    7  => ['bg'=>'#f43f5e','light'=>'#ffe4e6','text'=>'#be123c','label'=>'مستوى 5',  'abbr'=>'L5'],
    8  => ['bg'=>'#f97316','light'=>'#ffedd5','text'=>'#c2410c','label'=>'مستوى 6',  'abbr'=>'L6'],
    9  => ['bg'=>'#f59e0b','light'=>'#fef3c7','text'=>'#b45309','label'=>'مستوى 7',  'abbr'=>'L7'],
    10 => ['bg'=>'#84cc16','light'=>'#ecfccb','text'=>'#4d7c0f','label'=>'مستوى 8',  'abbr'=>'L8'],
    11 => ['bg'=>'#22c55e','light'=>'#dcfce7','text'=>'#15803d','label'=>'مستوى 9',  'abbr'=>'L9'],
    12 => ['bg'=>'#10b981','light'=>'#d1fae5','text'=>'#047857','label'=>'مستوى 10', 'abbr'=>'L10'],
    13 => ['bg'=>'#14b8a6','light'=>'#ccfbf1','text'=>'#0f766e','label'=>'مستوى 11', 'abbr'=>'L11'],
    14 => ['bg'=>'#06b6d4','light'=>'#cffafe','text'=>'#0e7490','label'=>'مستوى 12', 'abbr'=>'L12'],
];
@endphp

{{-- ══ HERO BANNER ══════════════════════════════════════════════════ --}}
<div class="relative overflow-hidden rounded-2xl mb-6" style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 45%,#0ea5e9 100%);">
    <div class="absolute -top-12 -left-12 w-48 h-48 rounded-full" style="background:rgba(255,255,255,.07);"></div>
    <div class="absolute -bottom-16 -right-16 w-64 h-64 rounded-full" style="background:rgba(255,255,255,.06);"></div>
    <div class="absolute top-1/2 left-1/3 w-32 h-32 rounded-full -translate-y-1/2" style="background:rgba(255,255,255,.04);"></div>

    <div class="relative px-7 py-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(255,255,255,.18);backdrop-filter:blur(8px);">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                </svg>
            </div>
            <div>
                <p class="text-blue-200 text-xs font-bold uppercase tracking-widest mb-0.5">إدارة البرامج</p>
                <h1 class="text-white text-xl font-black leading-tight">برامج اللغة الإنجليزية</h1>
                <p class="text-blue-100 text-sm opacity-80 mt-0.5">{{ $stats['total'] }} مستوى • من التمهيدي إلى الثاني عشر</p>
            </div>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            {{-- mini stats --}}
            <div class="flex items-center gap-3 rounded-xl px-4 py-2.5" style="background:rgba(255,255,255,.13);backdrop-filter:blur(8px);">
                <div class="text-center"><p class="text-white font-black text-lg leading-none">{{ $stats['total'] }}</p><p class="text-blue-200 text-[10px] mt-0.5">إجمالي</p></div>
                <div class="w-px h-7 bg-white/20"></div>
                <div class="text-center"><p class="font-black text-lg leading-none" style="color:#86efac;">{{ $stats['active'] }}</p><p class="text-blue-200 text-[10px] mt-0.5">نشط</p></div>
                <div class="w-px h-7 bg-white/20"></div>
                <div class="text-center"><p class="font-black text-lg leading-none" style="color:#fca5a5;">{{ $stats['inactive'] }}</p><p class="text-blue-200 text-[10px] mt-0.5">معطّل</p></div>
            </div>
            <a href="{{ route('admin.english.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all hover:scale-105 hover:shadow-lg"
               style="background:#fff;color:#1d4ed8;">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                إضافة مستوى
            </a>
        </div>
    </div>
</div>

@if(session('success'))
<div class="mb-5 rounded-xl px-4 py-3 flex items-center gap-3" style="background:#f0fdf4;border:1px solid #bbf7d0;">
    <svg class="w-4 h-4 flex-shrink-0" style="color:#16a34a;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <p class="text-sm font-semibold" style="color:#15803d;">{{ session('success') }}</p>
</div>
@endif

{{-- ══ COLOR SPECTRUM TRACK ══════════════════════════════════════════ --}}
<div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 mb-5 overflow-hidden">
    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4">مسار التعلّم</p>
    <div class="flex items-end gap-1 overflow-x-auto pb-1">
        @foreach($palette as $lv => $c)
        @php $active = $programs->where('level', $lv)->where('status','active')->count() > 0; @endphp
        <div class="flex flex-col items-center gap-1.5 flex-shrink-0 cursor-pointer group/step">
            <span class="text-[8px] font-bold whitespace-nowrap opacity-0 group-hover/step:opacity-100 transition-opacity"
                  style="color:{{ $c['bg'] }};">{{ $c['label'] }}</span>
            <div class="w-8 h-8 rounded-xl flex items-center justify-center text-[10px] font-black transition-all
                        {{ $active ? 'text-white shadow-lg' : 'opacity-30' }}"
                 style="{{ $active ? 'background:'.$c['bg'].';box-shadow:0 4px 12px '.$c['bg'].'55;' : 'background:'.$c['light'].';color:'.$c['bg'].';' }}">
                {{ $c['abbr'] }}
            </div>
            {{-- height bar --}}
            <div class="w-1.5 rounded-full transition-all"
                 style="height:{{ $active ? '28px' : '8px' }};background:{{ $active ? $c['bg'] : '#e5e7eb' }};opacity:{{ $active ? '1' : '0.4' }};"></div>
        </div>
        @if($lv < 14)
        <div class="w-3 h-0.5 mb-5 flex-shrink-0 rounded-full" style="background:linear-gradient(90deg,{{ $c['bg'] }}66,{{ $palette[$lv+1]['bg'] }}66);"></div>
        @endif
        @endforeach
    </div>
</div>

{{-- ══ COLORED TABLE ══════════════════════════════════════════════════ --}}
<div class="rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 overflow-hidden">

    {{-- header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
        <div class="flex items-center gap-2">
            {{-- rainbow stripe --}}
            <div class="flex gap-0.5 rounded-full overflow-hidden h-4 w-16">
                @foreach([0,2,5,8,11,14] as $lv)
                <div class="flex-1 h-full" style="background:{{ $palette[$lv]['bg'] }};"></div>
                @endforeach
            </div>
            <h2 class="text-sm font-bold text-gray-800 dark:text-gray-100">قائمة المستويات</h2>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full" style="background:#dbeafe;color:#1d4ed8;">
            {{ $programs->total() }} مستوى
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800" style="background:#f8fafc;">
                    <th class="px-5 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest w-44">المستوى</th>
                    <th class="px-5 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">الدورة</th>
                    <th class="px-5 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest w-28">المدة</th>
                    <th class="px-5 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest w-24">الرسوم</th>
                    <th class="px-5 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest w-24">الحالة</th>
                    <th class="px-5 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest w-24">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($programs as $program)
                @php
                    $lv = $program->level ?? 0;
                    $c  = $palette[$lv] ?? $palette[0];
                @endphp

                {{-- group header row --}}
                @if($lv === 0)
                <tr><td colspan="6" class="px-5 pt-4 pb-1.5">
                    <div class="flex items-center gap-2">
                        <span class="text-[9px] font-black uppercase tracking-widest" style="color:#0ea5e9;">مستويات التمهيدي</span>
                        <div class="flex-1 h-px" style="background:linear-gradient(90deg,#bae6fd,transparent);"></div>
                    </div>
                </td></tr>
                @elseif($lv === 3)
                <tr><td colspan="6" class="px-5 pt-5 pb-1.5">
                    <div class="flex items-center gap-2">
                        <span class="text-[9px] font-black uppercase tracking-widest" style="color:#8b5cf6;">المستويات الأساسية</span>
                        <div class="flex-1 h-px" style="background:linear-gradient(90deg,#ddd6fe,transparent);"></div>
                    </div>
                </td></tr>
                @endif

                <tr class="border-b border-gray-50 dark:border-gray-800/50 group transition-colors"
                    style="border-right: 3px solid {{ $c['bg'] }};"
                    onmouseover="this.style.background='{{ $c['light'] }}22'"
                    onmouseout="this.style.background=''">

                    {{-- level badge --}}
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black text-white flex-shrink-0 shadow-md"
                                 style="background:{{ $c['bg'] }};box-shadow:0 3px 10px {{ $c['bg'] }}55;">
                                {{ $c['abbr'] }}
                            </div>
                            <div>
                                <span class="text-xs font-bold leading-none" style="color:{{ $c['text'] }};">{{ $c['label'] }}</span>
                                <p class="text-[10px] font-mono text-gray-300 dark:text-gray-600 mt-0.5">{{ $program->code }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- names --}}
                    <td class="px-5 py-3">
                        <p class="font-bold text-gray-900 dark:text-white text-sm leading-snug">{{ $program->name_ar }}</p>
                        @if($program->name_en)
                        <p class="text-xs text-gray-400 mt-0.5" dir="ltr">{{ $program->name_en }}</p>
                        @endif
                    </td>

                    {{-- duration --}}
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-lg"
                              style="background:{{ $c['light'] }};color:{{ $c['text'] }};">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $program->duration_months ?? 1 }}م · 40س
                        </span>
                    </td>

                    {{-- price --}}
                    <td class="px-5 py-3">
                        @if($program->price > 0)
                            <span class="text-sm font-bold text-gray-800 dark:text-white">{{ number_format($program->price,0) }} <span class="text-xs font-normal text-gray-400"><x-riyal /></span></span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-1 rounded-lg" style="background:#dcfce7;color:#15803d;">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                مجاني
                            </span>
                        @endif
                    </td>

                    {{-- status --}}
                    <td class="px-5 py-3">
                        @if($program->status === 'active')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold" style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 flex-shrink-0" style="animation:pulse 2s infinite;"></span>نشط
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400" style="border:1px solid #e5e7eb;">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400 flex-shrink-0"></span>معطّل
                        </span>
                        @endif
                    </td>

                    {{-- actions --}}
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.english.edit', $program) }}"
                               class="w-8 h-8 rounded-lg flex items-center justify-center text-white transition-all hover:scale-110 shadow-sm"
                               style="background:{{ $c['bg'] }};" title="تعديل">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.english.destroy', $program) }}" method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المستوى؟')">
                                @csrf @method('DELETE')
                                <button type="submit" title="حذف"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-white transition-all hover:scale-110 shadow-sm"
                                        style="background:#ef4444;">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-20 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center" style="background:#dbeafe;">
                            <svg class="w-8 h-8" style="color:#3b82f6;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-500">لا توجد دورات بعد</p>
                        <a href="{{ route('admin.english.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold text-white" style="background:#2563eb;">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            إضافة مستوى
                        </a>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($programs->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
        {{ $programs->links() }}
    </div>
    @endif
</div>

<style>
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
</style>

@endsection
