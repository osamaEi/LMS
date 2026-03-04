@extends('layouts.dashboard')

@section('title', 'الربع الدراسي — ' . $term->name)

@push('styles')
<style>
@keyframes fadeUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
@keyframes slideIn { from{opacity:0;transform:translateX(40px)} to{opacity:1;transform:translateX(0)} }
.fu{animation:fadeUp .5s cubic-bezier(.4,0,.2,1) both}
.d1{animation-delay:.05s}.d2{animation-delay:.1s}.d3{animation-delay:.15s}.d4{animation-delay:.2s}

/* ── Hero ──────────────────────────────────────────────────────── */
.term-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0071AA 100%);
    border-radius: 24px;
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,113,170,.3);
}
.term-hero .blob { position:absolute; border-radius:50%; pointer-events:none; }

/* ── Stat pills ─────────────────────────────────────────────────── */
.stat-pill {
    display:flex; align-items:center; gap:.75rem;
    padding:.9rem 1.25rem;
    background:rgba(255,255,255,.1);
    border:1px solid rgba(255,255,255,.15);
    border-radius:16px; backdrop-filter:blur(8px);
}
.stat-pill .val { font-size:1.5rem; font-weight:900; color:#fff; line-height:1; }
.stat-pill .lbl { font-size:.7rem; color:rgba(255,255,255,.65); margin-top:2px; }
.stat-pill .ico { width:38px; height:38px; border-radius:10px; background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; }

/* ── Timeline bar ───────────────────────────────────────────────── */
.progress-track { height:6px; border-radius:99px; background:rgba(255,255,255,.15); overflow:hidden; }
.progress-fill  { height:100%; border-radius:99px; background:linear-gradient(90deg,#34d399,#10b981); transition:width 1s ease; }

/* ── Cards ──────────────────────────────────────────────────────── */
.card {
    background:#fff; border-radius:20px;
    border:1px solid rgba(0,0,0,.06);
    box-shadow:0 2px 16px rgba(0,0,0,.05);
    overflow:hidden;
}
.dark .card { background:#1e293b; border-color:rgba(255,255,255,.07); }
.card-header {
    padding:1.25rem 1.5rem;
    border-bottom:1px solid #f1f5f9;
    display:flex; align-items:center; justify-content:space-between; gap:1rem;
}
.dark .card-header { border-color:#334155; }

/* ── Subject row ────────────────────────────────────────────────── */
.sub-row { padding:1.1rem 1.5rem; border-bottom:1px solid #f8fafc; transition:background .15s; display:flex; align-items:center; gap:1rem; }
.sub-row:last-child { border-bottom:none; }
.sub-row:hover { background:#f0fdfa; }
.dark .sub-row { border-color:#1e293b; }
.dark .sub-row:hover { background:rgba(0,113,170,.07); }

.sub-avatar {
    width:44px; height:44px; border-radius:13px;
    display:flex; align-items:center; justify-content:center;
    font-size:.8rem; font-weight:800; color:#fff; flex-shrink:0;
}

/* ── Drawer overlay ─────────────────────────────────────────────── */
.drawer-overlay {
    position:fixed; inset:0; background:rgba(0,0,0,.45);
    backdrop-filter:blur(3px); z-index:40;
    transition:opacity .25s;
}
.drawer-overlay.hidden { opacity:0; pointer-events:none; }

.drawer-panel {
    position:fixed; top:0; left:0; bottom:0; width:520px; max-width:95vw;
    background:#fff; box-shadow:-8px 0 40px rgba(0,0,0,.15);
    z-index:50; display:flex; flex-direction:column;
    transform:translateX(-100%); transition:transform .3s cubic-bezier(.4,0,.2,1);
}
.dark .drawer-panel { background:#0f172a; }
.drawer-panel.open { transform:translateX(0); }

.drawer-search input {
    width:100%; padding:.7rem 1rem .7rem 2.8rem;
    border:1.5px solid #e2e8f0; border-radius:12px;
    font-size:.875rem; background:#f8fafc; outline:none;
    transition:border-color .2s;
}
.drawer-search input:focus { border-color:#0071AA; }
.dark .drawer-search input { background:#1e293b; border-color:#334155; color:#e2e8f0; }

/* ── Checkbox subject card ──────────────────────────────────────── */
.sub-check-item {
    display:flex; align-items:center; gap:.9rem;
    padding:.75rem 1rem; border-radius:12px; cursor:pointer;
    border:1.5px solid #f1f5f9; transition:all .18s;
    margin-bottom:.5rem;
}
.sub-check-item:hover { border-color:#0071AA; background:#f0f9ff; }
.dark .sub-check-item { border-color:#1e293b; }
.dark .sub-check-item:hover { background:rgba(0,113,170,.08); }
.sub-check-item.checked { border-color:#10b981; background:#f0fdf4; }
.dark .sub-check-item.checked { background:rgba(16,185,129,.07); }
.sub-check-item input[type=checkbox] { width:18px; height:18px; accent-color:#10b981; cursor:pointer; flex-shrink:0; }

/* ── Status badge ───────────────────────────────────────────────── */
.badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:700; }
.badge-active   { background:#d1fae5; color:#065f46; }
.badge-upcoming { background:#dbeafe; color:#1e40af; }
.badge-done     { background:#f3f4f6; color:#6b7280; }
</style>
@endpush

@section('content')
@php
    $now = now();
    $start = $term->start_date;
    $end   = $term->end_date;
    $totalDays = max(1, $start->diffInDays($end));
    $elapsed   = max(0, min($totalDays, $start->diffInDays($now)));
    $pct = round(($elapsed / $totalDays) * 100);

    $statusStyle = match($term->status) {
        'active'    => ['label' => 'نشط',   'cls' => 'badge-active'],
        'upcoming'  => ['label' => 'قادم',  'cls' => 'badge-upcoming'],
        default     => ['label' => 'مكتمل', 'cls' => 'badge-done'],
    };

    $subjectColors = ['#6366f1','#0891b2','#059669','#d97706','#dc2626','#7c3aed','#0284c7','#db2777'];
@endphp

<div class="space-y-6" style="direction:rtl">

{{-- ── Hero ─────────────────────────────────────────────────────── --}}
<div class="term-hero fu">
    <div class="blob" style="top:-80px;right:-60px;width:280px;height:280px;background:radial-gradient(circle,rgba(255,255,255,.1) 0%,transparent 70%)"></div>
    <div class="blob" style="bottom:-100px;left:-40px;width:320px;height:320px;background:radial-gradient(circle,rgba(0,168,232,.2) 0%,transparent 70%)"></div>

    <div class="relative z-10">
        {{-- Top row --}}
        <div class="flex items-start justify-between flex-wrap gap-4 mb-6">
            <div>
                <div class="flex items-center gap-3 mb-2 flex-wrap">
                    <a href="{{ route('admin.terms.index') }}"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-xl transition"
                       style="background:rgba(255,255,255,.12); color:rgba(255,255,255,.8);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        الأرباع
                    </a>
                    <span class="badge {{ $statusStyle['cls'] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $term->status === 'active' ? 'bg-green-500' : ($term->status === 'upcoming' ? 'bg-blue-500' : 'bg-gray-400') }}"></span>
                        {{ $statusStyle['label'] }}
                    </span>
                </div>
                <h1 class="text-3xl font-black text-white tracking-tight mb-1">{{ $term->name }}</h1>
                <p class="text-white/60 text-sm">{{ $term->program->name_ar ?? 'بدون دبلوم' }} — الربع رقم {{ $term->term_number }}</p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <button onclick="openDrawer()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white transition"
                        style="background:rgba(16,185,129,.3); border:1px solid rgba(16,185,129,.5);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    تعيين المواد
                </button>
                <a href="{{ route('admin.terms.edit', $term) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white transition"
                   style="background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    تعديل
                </a>
            </div>
        </div>

        {{-- Stat pills --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="stat-pill">
                <div class="ico"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg></div>
                <div><div class="val">{{ $term->subjects->count() }}</div><div class="lbl">مادة دراسية</div></div>
            </div>
            <div class="stat-pill">
                <div class="ico"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>
                <div><div class="val">{{ $term->subjects->sum('sessions_count') }}</div><div class="lbl">درس إجمالي</div></div>
            </div>
            <div class="stat-pill">
                <div class="ico"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                <div><div class="val">{{ $totalDays }}</div><div class="lbl">يوم إجمالي</div></div>
            </div>
            <div class="stat-pill">
                <div class="ico"><svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                <div><div class="val">{{ $term->subjects->sum('files_count') }}</div><div class="lbl">ملف مرفق</div></div>
            </div>
        </div>

        {{-- Timeline progress --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-white/70 text-xs font-semibold">مدة الربع الدراسي</span>
                <span class="text-white text-xs font-bold">{{ $pct }}% مكتمل</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" style="width:{{ $pct }}%"></div>
            </div>
            <div class="flex justify-between mt-1.5">
                <span class="text-white/50 text-xs">{{ $start->format('Y/m/d') }}</span>
                <span class="text-white/50 text-xs">{{ $end->format('Y/m/d') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Flash --}}
@if(session('success'))
<div class="flex items-center gap-2.5 rounded-xl px-4 py-3 text-sm font-semibold text-green-700 border border-green-200 fu d1"
     style="background:#f0fdf4">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- ── Main grid ───────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Subjects list (2/3) ──────────────────────────────────────── --}}
    <div class="lg:col-span-2 fu d2">
        <div class="card">
            <div class="card-header">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#0071AA,#0891b2)">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-900 dark:text-white">المواد الدراسية</h2>
                        <p class="text-xs text-gray-400">{{ $term->subjects->count() }} مادة مرتبطة بهذا الربع</p>
                    </div>
                </div>
                <button onclick="openDrawer()"
                        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-bold text-teal-700 transition"
                        style="background:#f0fdfa; border:1px solid #99f6e4;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    تعيين / تعديل
                </button>
            </div>

            @forelse($term->subjects as $i => $subject)
            @php
                $clr = $subjectColors[$i % count($subjectColors)];
                $initials = collect(explode(' ', $subject->name_ar ?? 'م'))->take(2)->map(fn($w) => mb_substr($w,0,1))->join('');
            @endphp
            <div class="sub-row">
                {{-- Color avatar --}}
                <div class="sub-avatar" style="background:{{ $clr }}20; color:{{ $clr }}; border:2px solid {{ $clr }}30;">
                    {{ $initials }}
                </div>
                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $subject->name_ar }}</span>
                        <span class="text-xs font-mono px-2 py-0.5 rounded-md" style="background:{{ $clr }}15; color:{{ $clr }}">{{ $subject->code }}</span>
                        @if($subject->status === 'active')
                        <span class="badge badge-active">نشط</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-4 mt-1 text-xs text-gray-400 flex-wrap">
                        @if($subject->teacher)
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $subject->teacher->name }}
                        </span>
                        @else
                        <span class="text-amber-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            بدون معلم
                        </span>
                        @endif
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            {{ $subject->sessions_count }} درس
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            {{ $subject->files_count }} ملف
                        </span>
                    </div>
                </div>
                {{-- Action --}}
                <a href="{{ route('admin.subjects.show', $subject) }}"
                   class="flex-shrink-0 inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg transition"
                   style="background:#ede9fe; color:#7c3aed;"
                   onmouseover="this.style.background='#ddd6fe'" onmouseout="this.style.background='#ede9fe'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    عرض
                </a>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-4" style="background:#f0fdfa">
                    <svg class="w-10 h-10" style="color:#5eead4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white">لا توجد مواد مرتبطة</h3>
                <p class="text-sm text-gray-400 mt-1 mb-4">ابدأ بتعيين المواد لهذا الربع</p>
                <button onclick="openDrawer()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg"
                        style="background:linear-gradient(135deg,#0071AA,#0891b2)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    تعيين المواد
                </button>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── Sidebar (1/3) ──────────────────────────────────────────── --}}
    <div class="space-y-5 fu d3">

        {{-- Term info card --}}
        <div class="card">
            <div class="card-header">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#6366f1,#7c3aed)">
                        <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white text-sm">معلومات الربع</h3>
                </div>
            </div>
            <div class="p-5 space-y-3.5">
                @php
                    $infoRows = [
                        ['label' => 'الدبلوم',     'value' => $term->program->name_ar ?? '—', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                        ['label' => 'رقم الربع',   'value' => 'الربع ' . $term->term_number,  'icon' => 'M7 20l4-16m2 16l4-16M6 9h14M4 15h14'],
                        ['label' => 'بداية الدراسة','value' => $start->format('Y/m/d'),        'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['label' => 'نهاية الدراسة','value' => $end->format('Y/m/d'),          'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ];
                @endphp
                @foreach($infoRows as $row)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#f1f5f9">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $row['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">{{ $row['label'] }}</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $row['value'] }}</p>
                    </div>
                </div>
                @endforeach

                @if($term->registration_start_date)
                <div class="pt-2 border-t border-gray-100 dark:border-gray-700">
                    <p class="text-xs text-gray-400 mb-1">فترة التسجيل</p>
                    <p class="text-sm font-semibold text-blue-600">
                        {{ $term->registration_start_date->format('Y/m/d') }} — {{ $term->registration_end_date?->format('Y/m/d') }}
                    </p>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="card">
            <div class="card-header">
                <h3 class="font-bold text-gray-900 dark:text-white text-sm">إجراءات سريعة</h3>
            </div>
            <div class="p-4 space-y-2">
                <button onclick="openDrawer()"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-teal-700 transition"
                        style="background:#f0fdfa; border:1px solid #ccfbf1;"
                        onmouseover="this.style.background='#ccfbf1'" onmouseout="this.style.background='#f0fdfa'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    تعيين المواد الدراسية
                </button>
                <a href="{{ route('admin.terms.edit', $term) }}"
                   class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-amber-700 transition"
                   style="background:#fffbeb; border:1px solid #fde68a;"
                   onmouseover="this.style.background='#fde68a'" onmouseout="this.style.background='#fffbeb'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    تعديل بيانات الربع
                </a>
                <a href="{{ route('admin.subjects.create', ['program_id' => $term->program_id]) }}"
                   class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-purple-700 transition"
                   style="background:#f5f3ff; border:1px solid #ddd6fe;"
                   onmouseover="this.style.background='#ddd6fe'" onmouseout="this.style.background='#f5f3ff'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    إنشاء مادة جديدة
                </a>
            </div>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- ── Assign Subjects Drawer ──────────────────────────────────────── --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}

{{-- Overlay --}}
<div id="drawer-overlay" class="drawer-overlay hidden" onclick="closeDrawer()"></div>

{{-- Drawer panel --}}
<div id="drawer-panel" class="drawer-panel" x-data="subjectDrawer()" x-init="init()">
    {{-- Header --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#0071AA,#0891b2)">
                <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white">تعيين المواد الدراسية</h2>
                <p class="text-xs text-gray-400" x-text="selected.length + ' مادة محددة'"></p>
            </div>
        </div>
        <button onclick="closeDrawer()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Search + select-all --}}
    <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex-shrink-0 space-y-2.5">
        <div class="drawer-search relative">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);pointer-events:none">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" x-model="search" placeholder="ابحث بالاسم أو الكود أو الدبلوم..." class="pr-9"/>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-xs text-gray-400" x-text="filtered.length + ' مادة ظاهرة'"></span>
            <div class="flex gap-2">
                <button type="button" @click="selectAll()"
                        class="text-xs font-semibold text-teal-600 hover:text-teal-700 px-2 py-1 rounded-lg hover:bg-teal-50 transition">تحديد الكل</button>
                <button type="button" @click="clearAll()"
                        class="text-xs font-semibold text-red-500 hover:text-red-600 px-2 py-1 rounded-lg hover:bg-red-50 transition">إلغاء الكل</button>
            </div>
        </div>
    </div>

    {{-- Subject list --}}
    <div class="flex-1 overflow-y-auto px-5 py-3">
        <template x-for="sub in filtered" :key="sub.id">
            <label class="sub-check-item" :class="isSelected(sub.id) ? 'checked' : ''">
                <input type="checkbox" :checked="isSelected(sub.id)" @change="toggle(sub.id)">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="sub.name_ar"></span>
                        <span class="text-xs font-mono px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300" x-text="sub.code"></span>
                    </div>
                    <span class="text-xs text-gray-400 mt-0.5 block" x-text="sub.program || 'بدون دبلوم'"></span>
                </div>
                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition"
                     :class="isSelected(sub.id) ? 'bg-green-500 border-green-500' : 'border-gray-300'">
                    <svg x-show="isSelected(sub.id)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </label>
        </template>
        <div x-show="filtered.length === 0" class="text-center py-12 text-gray-400 text-sm">
            لا توجد مواد تطابق البحث
        </div>
    </div>

    {{-- Footer --}}
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 flex-shrink-0">
        <form action="{{ route('admin.terms.subjects.sync', $term) }}" method="POST" id="sync-form">
            @csrf
            <div id="hidden-inputs"></div>
        </form>
        <div class="flex items-center gap-3">
            <button type="button" onclick="closeDrawer()"
                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition dark:bg-gray-700 dark:text-gray-300">
                إلغاء
            </button>
            <button type="button" @click="submit()"
                    :disabled="selected.length === 0"
                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg transition"
                    :class="selected.length === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:shadow-xl hover:-translate-y-0.5'"
                    style="background:linear-gradient(135deg,#10b981,#059669)">
                <span x-text="'حفظ (' + selected.length + ')'"></span>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Drawer open/close
function openDrawer() {
    document.getElementById('drawer-panel').classList.add('open');
    document.getElementById('drawer-overlay').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeDrawer() {
    document.getElementById('drawer-panel').classList.remove('open');
    document.getElementById('drawer-overlay').classList.add('hidden');
    document.body.style.overflow = '';
}

// Alpine component
function subjectDrawer() {
    return {
        search: '',
        selected: @json($assignedIds),
        allSubjects: @json($allSubjectsForJs),
        get filtered() {
            const q = this.search.toLowerCase().trim();
            if (!q) return this.allSubjects;
            return this.allSubjects.filter(s =>
                (s.name_ar && s.name_ar.toLowerCase().includes(q)) ||
                (s.code    && s.code.toLowerCase().includes(q)) ||
                (s.program && s.program.toLowerCase().includes(q))
            );
        },
        isSelected(id) { return this.selected.includes(id); },
        toggle(id) {
            if (this.isSelected(id)) this.selected = this.selected.filter(x => x !== id);
            else this.selected.push(id);
        },
        selectAll() { this.selected = [...new Set([...this.selected, ...this.filtered.map(s => s.id)])]; },
        clearAll()  { const filteredIds = this.filtered.map(s => s.id); this.selected = this.selected.filter(id => !filteredIds.includes(id)); },
        submit() {
            const container = document.getElementById('hidden-inputs');
            container.innerHTML = '';
            this.selected.forEach(id => {
                const inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = 'subject_ids[]'; inp.value = id;
                container.appendChild(inp);
            });
            document.getElementById('sync-form').submit();
        },
        init() {}
    };
}
</script>
@endpush

</div>
@endsection
