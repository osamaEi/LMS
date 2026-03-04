@extends('layouts.dashboard')

@section('title', 'حضور الحصة - ' . $session->title)

@push('styles')
<style>
@keyframes fadeUp   { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }
@keyframes scaleIn  { from { opacity:0; transform:scale(.9); }        to { opacity:1; transform:scale(1); }      }
@keyframes spin-slow { from { transform:rotate(0deg); } to { transform:rotate(360deg); } }
@keyframes pulse-ring { 0%,100% { opacity:.4; transform:scale(1); } 50% { opacity:.15; transform:scale(1.15); } }
@keyframes countUp  { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

.fade-up  { animation: fadeUp  .55s cubic-bezier(.4,0,.2,1) both; }
.scale-in { animation: scaleIn .45s cubic-bezier(.4,0,.2,1) both; }
.d1{animation-delay:.07s} .d2{animation-delay:.14s} .d3{animation-delay:.21s} .d4{animation-delay:.28s} .d5{animation-delay:.35s} .d6{animation-delay:.42s}

/* ── Hero ──────────────────────────────────────────────────────── */
.att-hero {
    background: linear-gradient(135deg, #0a1628 0%, #0071AA 55%, #00a8e8 100%);
    border-radius: 28px;
    padding: 2.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 24px 64px rgba(0,113,170,.35);
}
.att-hero .blob1 {
    position:absolute; top:-80px; right:-80px;
    width:320px; height:320px; border-radius:50%;
    background: radial-gradient(circle, rgba(255,255,255,.12) 0%, transparent 70%);
    pointer-events:none;
}
.att-hero .blob2 {
    position:absolute; bottom:-100px; left:-60px;
    width:280px; height:280px; border-radius:50%;
    background: radial-gradient(circle, rgba(0,168,232,.25) 0%, transparent 70%);
    pointer-events:none;
}
.att-hero .blob3 {
    position:absolute; top:50%; left:40%;
    width:200px; height:200px; border-radius:50%;
    background: radial-gradient(circle, rgba(255,255,255,.05) 0%, transparent 70%);
    pointer-events:none;
    animation: pulse-ring 4s ease-in-out infinite;
}

/* ── Gauge ─────────────────────────────────────────────────────── */
.gauge-wrap { position:relative; width:140px; height:140px; flex-shrink:0; }
.gauge-wrap svg { transform:rotate(-90deg); width:140px; height:140px; }
.gauge-center {
    position:absolute; top:50%; left:50%;
    transform:translate(-50%,-50%);
    text-align:center; pointer-events:none;
}
.gauge-pct  { font-size:1.9rem; font-weight:900; color:#fff; line-height:1; }
.gauge-lbl  { font-size:.65rem; color:rgba(255,255,255,.65); margin-top:2px; letter-spacing:.04em; }

/* ── Hero stat pills ────────────────────────────────────────────── */
.hero-pill {
    display:flex; align-items:center; gap:.6rem;
    padding:.65rem 1.1rem;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.18);
    border-radius:14px;
    backdrop-filter:blur(8px);
    min-width:110px;
}
.hero-pill .pill-val { font-size:1.35rem; font-weight:900; color:#fff; line-height:1; }
.hero-pill .pill-lbl { font-size:.7rem; color:rgba(255,255,255,.7); margin-top:1px; }
.hero-pill .pill-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }

/* ── Cards ──────────────────────────────────────────────────────── */
.att-card {
    background:#fff;
    border-radius:22px;
    overflow:hidden;
    border:1px solid rgba(0,0,0,.05);
    box-shadow:0 4px 24px rgba(0,0,0,.06);
}
.dark .att-card { background:#1e293b; border-color:rgba(255,255,255,.07); }

.att-card-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:1.25rem 1.5rem;
    border-bottom:1px solid #f1f5f9;
}
.dark .att-card-header { border-color:#334155; }

/* ── Table ──────────────────────────────────────────────────────── */
.att-table { width:100%; }
.att-table thead th {
    padding:.875rem 1.5rem;
    text-align:right;
    font-size:.75rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase;
    color:#64748b;
    background:linear-gradient(180deg,#f8fafc,#f1f5f9);
    border-bottom:2px solid #e2e8f0;
}
.att-table tbody td { padding:1rem 1.5rem; border-bottom:1px solid #f8fafc; font-size:.9rem; vertical-align:middle; }
.att-table tbody tr:last-child td { border-bottom:none; }
.att-table tbody tr { transition:background .18s; }
.att-table tbody tr:hover { background:rgba(0,113,170,.03); }
.dark .att-table thead th { background:linear-gradient(180deg,#1e293b,#0f172a); color:#94a3b8; border-color:#334155; }
.dark .att-table tbody td { border-color:#1e293b; }
.dark .att-table tbody tr:hover { background:rgba(0,113,170,.07); }

.stu-avatar {
    width:42px; height:42px; border-radius:12px;
    border:2.5px solid #e2e8f0;
    transition:all .25s ease;
    object-fit:cover;
}
.att-table tbody tr:hover .stu-avatar { border-color:#0071AA; transform:scale(1.08); }

.rank-badge {
    width:28px; height:28px; border-radius:8px;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:.75rem; font-weight:800; color:#64748b;
    background:#f1f5f9;
}
.rank-badge.gold   { background:linear-gradient(135deg,#fbbf24,#f59e0b); color:#fff; }
.rank-badge.silver { background:linear-gradient(135deg,#94a3b8,#64748b); color:#fff; }
.rank-badge.bronze { background:linear-gradient(135deg,#c2855a,#9a6345); color:#fff; }

.time-chip {
    display:inline-flex; align-items:center; gap:.35rem;
    padding:.3rem .7rem; border-radius:9px;
    font-size:.8rem; font-weight:500;
    background:#f1f5f9; color:#475569;
}
.dark .time-chip { background:#0f172a; color:#94a3b8; }

.dur-chip {
    display:inline-flex; align-items:center; gap:.3rem;
    font-size:.85rem; font-weight:700; color:#0071AA;
}

.pct-bar { height:5px; border-radius:8px; background:#f1f5f9; overflow:hidden; width:72px; }
.pct-fill { height:100%; border-radius:8px; background:linear-gradient(90deg,#10b981,#34d399); transition:width .5s ease; }

.badge-full    { display:inline-flex; align-items:center; gap:.35rem; padding:.35rem .85rem; border-radius:20px; font-size:.78rem; font-weight:700; background:linear-gradient(135deg,#d1fae5,#a7f3d0); color:#065f46; }
.badge-partial { display:inline-flex; align-items:center; gap:.35rem; padding:.35rem .85rem; border-radius:20px; font-size:.78rem; font-weight:700; background:linear-gradient(135deg,#fef3c7,#fde68a); color:#92400e; }
.badge-absent  { display:inline-flex; align-items:center; gap:.35rem; padding:.35rem .85rem; border-radius:20px; font-size:.78rem; font-weight:700; background:linear-gradient(135deg,#fee2e2,#fecaca); color:#991b1b; }

/* ── Search ─────────────────────────────────────────────────────── */
.search-wrap { position:relative; }
.search-wrap svg { position:absolute; right:.85rem; top:50%; transform:translateY(-50%); color:#94a3b8; pointer-events:none; width:17px; height:17px; }
.search-wrap input {
    padding:.6rem .85rem .6rem 2.5rem;
    border:1.5px solid #e2e8f0; border-radius:12px;
    font-size:.875rem; background:#f8fafc; direction:rtl; width:220px;
    transition:all .25s;
}
.search-wrap input:focus { border-color:#0071AA; box-shadow:0 0 0 3px rgba(0,113,170,.1); outline:none; background:#fff; }
.dark .search-wrap input { background:#0f172a; border-color:#334155; color:#e2e8f0; }

/* ── Absent / selector cards ────────────────────────────────────── */
@keyframes pop { 0%{transform:scale(1)} 40%{transform:scale(1.06)} 100%{transform:scale(1.025)} }

.sel-card {
    border-radius:16px; padding:1rem 1.1rem;
    border:2px solid rgba(239,68,68,.18);
    background:#fff; cursor:pointer;
    transition:all .22s cubic-bezier(.4,0,.2,1);
    position:relative; overflow:hidden;
    user-select:none;
    box-shadow: 0 2px 10px rgba(239,68,68,.08);
}
.sel-card::after {
    content:''; position:absolute; top:0; right:0; width:5px; height:100%;
    background:linear-gradient(180deg,#ef4444,#dc2626);
    border-radius:0 14px 14px 0;
    transition:all .22s;
}

/* ── SELECTED state ── much more visible */
.sel-card.selected {
    border-color:#10b981;
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    box-shadow: 0 0 0 3px rgba(16,185,129,.25), 0 8px 24px rgba(16,185,129,.18);
    transform: scale(1.025);
    animation: pop .25s cubic-bezier(.4,0,.2,1);
}
.sel-card.selected::after { background:linear-gradient(180deg,#10b981,#059669); width:6px; }

.sel-card:hover:not(.selected) { transform:translateY(-2px); box-shadow:0 8px 20px rgba(239,68,68,.12); border-color:rgba(239,68,68,.35); }
.dark .sel-card { background:#1e293b; border-color:rgba(239,68,68,.25); }
.dark .sel-card.selected { background:linear-gradient(135deg,rgba(16,185,129,.15),rgba(16,185,129,.08)); border-color:#10b981; }

.sel-avatar { width:46px; height:46px; border-radius:13px; border:2.5px solid #fecaca; transition:all .22s; }
.sel-card.selected .sel-avatar { border-color:#10b981; box-shadow:0 0 0 3px rgba(16,185,129,.2); transform:scale(1.05); }

/* ── Check ring ─────────────────────────────────────────────────── */
.check-ring {
    width:32px; height:32px; border-radius:50%; border:2px solid #e2e8f0;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0; transition:all .22s;
    background:#f8fafc;
}
.sel-card.selected .check-ring {
    background: linear-gradient(135deg,#10b981,#059669);
    border-color:#10b981;
    box-shadow: 0 0 0 4px rgba(16,185,129,.2);
    transform: scale(1.1);
}
.check-ring svg { opacity:0; transition:opacity .18s, transform .18s; transform:scale(.6); }
.sel-card.selected .check-ring svg { opacity:1; transform:scale(1); }

/* ── Breadcrumb ─────────────────────────────────────────────────── */
.bc { display:flex; align-items:center; gap:.5rem; padding:.65rem 1.1rem; background:#fff; border-radius:13px; border:1px solid rgba(0,0,0,.05); box-shadow:0 2px 8px rgba(0,0,0,.04); font-size:.85rem; }
.dark .bc { background:#1e293b; border-color:rgba(255,255,255,.08); }
.bc a { color:#64748b; font-weight:500; transition:color .2s; }
.bc a:hover { color:#0071AA; }
.bc .sep { color:#cbd5e1; font-size:.75rem; }
.bc .cur { color:#0f172a; font-weight:600; }
.dark .bc .cur { color:#f1f5f9; }
</style>
@endpush

@section('content')
@php
    $rate = $stats['attendance_rate'];
    $circumference = 2 * 3.14159 * 52;
    $offset = $circumference * (1 - $rate / 100);
    $rateColor = $rate >= 75 ? '#10b981' : ($rate >= 50 ? '#f59e0b' : '#ef4444');
@endphp

<div class="space-y-6 p-1">

{{-- ── Breadcrumb ───────────────────────────────────────────────── --}}
<nav class="bc fade-up">
    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    <a href="{{ route('teacher.my-subjects.index') }}">موادي</a>
    <span class="sep">›</span>
    <a href="{{ route('teacher.my-subjects.show', $subject->id) }}">{{ $subject->name_ar ?? $subject->name }}</a>
    <span class="sep">›</span>
    <span class="cur">سجل الحضور — الحصة {{ $session->session_number }}</span>
</nav>

{{-- ── Hero ─────────────────────────────────────────────────────── --}}
<div class="att-hero fade-up d1">
    <div class="blob1"></div>
    <div class="blob2"></div>
    <div class="blob3"></div>

    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center gap-8">

        {{-- Left: session info + pills --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold"
                      style="background:rgba(255,255,255,.15); color:rgba(255,255,255,.9)">
                    <span class="w-2 h-2 rounded-full {{ $session->type === 'live_zoom' ? 'bg-red-400 animate-pulse' : 'bg-sky-300' }}"></span>
                    {{ $session->type === 'live_zoom' ? 'Zoom مباشر' : 'مسجّل' }}
                </span>
                @if($session->scheduled_at)
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium" style="background:rgba(255,255,255,.12); color:rgba(255,255,255,.8)">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d · H:i') }}
                </span>
                @endif
            </div>

            <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight mb-1">{{ $session->title }}</h1>
            <p class="text-white/65 text-sm mb-6">{{ $subject->name_ar ?? $subject->name }} — الحصة رقم {{ $session->session_number }}</p>

            {{-- Pills --}}
            <div class="flex flex-wrap gap-3">
                <div class="hero-pill">
                    <div class="pill-dot" style="background:#60a5fa"></div>
                    <div>
                        <div class="pill-val">{{ $stats['total_enrolled'] }}</div>
                        <div class="pill-lbl">إجمالي المسجلين</div>
                    </div>
                </div>
                <div class="hero-pill">
                    <div class="pill-dot" style="background:#34d399"></div>
                    <div>
                        <div class="pill-val">{{ $stats['attended'] }}</div>
                        <div class="pill-lbl">الحاضرون</div>
                    </div>
                </div>
                <div class="hero-pill">
                    <div class="pill-dot" style="background:#f87171"></div>
                    <div>
                        <div class="pill-val">{{ $stats['absent'] }}</div>
                        <div class="pill-lbl">الغائبون</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Center: gauge --}}
        <div class="flex flex-col items-center gap-3">
            <div class="gauge-wrap scale-in d2">
                <svg viewBox="0 0 120 120">
                    <defs>
                        <linearGradient id="gaugeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:{{ $rateColor }}" />
                            <stop offset="100%" style="stop-color:#fff;stop-opacity:.5" />
                        </linearGradient>
                        <filter id="glow">
                            <feGaussianBlur stdDeviation="3" result="blur"/>
                            <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                        </filter>
                    </defs>
                    {{-- Track --}}
                    <circle cx="60" cy="60" r="52" fill="none" stroke="rgba(255,255,255,.12)" stroke-width="10"/>
                    {{-- Progress --}}
                    <circle cx="60" cy="60" r="52" fill="none"
                            stroke="{{ $rateColor }}"
                            stroke-width="10"
                            stroke-linecap="round"
                            stroke-dasharray="{{ $circumference }}"
                            stroke-dashoffset="{{ $offset }}"
                            filter="url(#glow)"
                            style="transition:stroke-dashoffset 1s ease;"/>
                </svg>
                <div class="gauge-center">
                    <div class="gauge-pct">{{ $rate }}%</div>
                    <div class="gauge-lbl">نسبة الحضور</div>
                </div>
            </div>
            <div class="text-xs font-semibold px-3 py-1 rounded-full"
                 style="background:rgba(255,255,255,.15); color:rgba(255,255,255,.9)">
                @if($rate >= 75) ممتاز @elseif($rate >= 50) متوسط @else منخفض @endif
            </div>
        </div>

        {{-- Right: back button --}}
        <div class="flex flex-col gap-2 lg:self-start">
            <a href="{{ route('teacher.my-subjects.show', $subject->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:bg-white/20"
               style="background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                العودة للمادة
            </a>
            @if($absentStudents->count() > 0)
            <a href="#add-attendance"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:bg-white/20"
               style="background:rgba(16,185,129,.25); border:1px solid rgba(16,185,129,.35)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                إضافة حضور
            </a>
            @endif
        </div>
    </div>
</div>

{{-- ── Attended Students ────────────────────────────────────────── --}}
<div class="att-card fade-up d2" x-data="{ search: '' }">
    <div class="att-card-header">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#10b981,#059669)">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white">الطلاب الحاضرون</h2>
                <p class="text-xs text-gray-400">{{ $attendances->where('attended', true)->count() }} من أصل {{ $stats['total_enrolled'] }} طالب</p>
            </div>
        </div>
        <div class="search-wrap">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" x-model="search" placeholder="ابحث عن طالب..." />
        </div>
    </div>

    @if($attendances->where('attended', true)->count() > 0)
    <div class="overflow-x-auto">
        <table class="att-table">
            <thead>
                <tr>
                    <th style="width:52px">#</th>
                    <th>الطالب</th>
                    <th>وقت الانضمام</th>
                    <th>وقت المغادرة</th>
                    <th>المدة</th>
                    @if($session->type === 'recorded_video')<th>المشاهدة</th>@endif
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances->where('attended', true) as $i => $att)
                @php $name = $att->student->name ?? ''; $email = $att->student->email ?? ''; @endphp
                <tr x-show="search===''||'{{ strtolower($name) }}'.includes(search.toLowerCase())||'{{ strtolower($email) }}'.includes(search.toLowerCase())">
                    <td>
                        <span class="rank-badge {{ $i===0 ? 'gold' : ($i===1 ? 'silver' : ($i===2 ? 'bronze' : '')) }}">
                            {{ $i + 1 }}
                        </span>
                    </td>
                    <td>
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($name ?: 'N') }}&background=0071AA&color=fff&size=84&bold=true&rounded=true"
                                 alt="{{ $name }}" class="stu-avatar" />
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $name ?: 'غير معروف' }}</p>
                                <p class="text-xs text-gray-400">{{ $email }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($att->joined_at)
                        <span class="time-chip">
                            <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                            {{ \Carbon\Carbon::parse($att->joined_at)->format('h:i A') }}
                        </span>
                        @else<span class="text-gray-300 dark:text-gray-600">—</span>@endif
                    </td>
                    <td>
                        @if($att->left_at)
                        <span class="time-chip">
                            <svg class="w-3 h-3 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                            {{ \Carbon\Carbon::parse($att->left_at)->format('h:i A') }}
                        </span>
                        @else<span class="text-gray-300 dark:text-gray-600">—</span>@endif
                    </td>
                    <td>
                        @if($att->duration_minutes)
                        <span class="dur-chip">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $att->duration_minutes }} د
                        </span>
                        @else<span class="text-gray-300 dark:text-gray-600">—</span>@endif
                    </td>
                    @if($session->type === 'recorded_video')
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="pct-bar"><div class="pct-fill" style="width:{{ $att->watch_percentage ?? 0 }}%"></div></div>
                            <span class="text-xs font-bold text-gray-600 dark:text-gray-300">{{ $att->watch_percentage ?? 0 }}%</span>
                        </div>
                    </td>
                    @endif
                    <td>
                        @if($att->video_completed || ($att->duration_minutes && $att->duration_minutes >= ($session->duration_minutes * 0.8)))
                            <span class="badge-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                حضور كامل
                            </span>
                        @else
                            <span class="badge-partial">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                حضور جزئي
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="flex flex-col items-center justify-center py-16 text-center">
        <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-5"
             style="background:linear-gradient(135deg,#fef3c7,#fde68a)">
            <svg class="w-10 h-10" style="color:#f59e0b" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white">لا يوجد حضور مسجّل بعد</h3>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-xs mx-auto">سيظهر الحضور تلقائياً عند انضمام الطلاب، أو يمكنك تسجيله يدوياً أدناه</p>
        @if($absentStudents->count() > 0)
        <a href="#add-attendance"
           class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg hover:shadow-xl transition"
           style="background:linear-gradient(135deg,#10b981,#059669)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            إضافة حضور يدوي
        </a>
        @endif
    </div>
    @endif
</div>

{{-- ── Absent + Add Attendance ─────────────────────────────────── --}}
@if($absentStudents->count() > 0)
<div class="att-card fade-up d3" id="add-attendance"
     x-data="{ selected: [], allIds: {{ json_encode($absentStudents->pluck('id')->values()) }} }">

    {{-- Header --}}
    <div class="att-card-header">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#ef4444,#dc2626)">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white">الطلاب الغائبون</h2>
                <p class="text-xs text-gray-400">{{ $absentStudents->count() }} طالب — انقر على البطاقة لتحديدها وتسجيل الحضور</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs font-semibold px-3 py-1.5 rounded-full"
                  style="background:rgba(16,185,129,.1); color:#059669"
                  x-text="selected.length + ' محدد'"></span>
            <label class="flex items-center gap-1.5 text-sm font-medium text-gray-500 dark:text-gray-400 cursor-pointer">
                <input type="checkbox" class="rounded"
                       @change="selected = $event.target.checked ? [...allIds] : []">
                <span class="text-xs">الكل</span>
            </label>
        </div>
    </div>

    @if(session('success'))
    <div class="mx-5 mt-4 flex items-center gap-2.5 rounded-xl px-4 py-3 text-sm font-medium text-green-700 border border-green-200"
         style="background:#f0fdf4">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('teacher.my-subjects.sessions.attendance.save', [$subject->id, $session->id]) }}"
          method="POST">
        @csrf
        <div class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                @foreach($absentStudents as $student)
                <label class="sel-card" :class="selected.includes({{ $student->id }}) ? 'selected' : ''">
                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                           class="sr-only" x-model="selected" :value="{{ $student->id }}">
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name ?? 'N') }}&background=ef4444&color=fff&size=92&bold=true&rounded=true"
                             alt="{{ $student->name }}"
                             class="sel-avatar"
                             :style="selected.includes({{ $student->id }}) ? 'border-color:#6ee7b7' : ''" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $student->name ?? 'غير معروف' }}</p>
                            <p class="text-xs text-gray-400 truncate mt-0.5">{{ $student->email ?? '' }}</p>
                            <p class="text-xs mt-1" :class="selected.includes({{ $student->id }}) ? 'text-green-600 font-semibold' : 'text-red-400'"
                               x-text="selected.includes({{ $student->id }}) ? '✓ سيُسجَّل حضوره' : 'غائب'"></p>
                        </div>
                        <div class="check-ring">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between gap-4 border-t border-gray-100 dark:border-gray-700 px-5 py-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                انقر على بطاقات الطلاب لتحديدهم ثم اضغط تسجيل
            </p>
            <button type="submit"
                    :disabled="selected.length === 0"
                    class="inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-bold text-white shadow-lg transition"
                    :class="selected.length === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:shadow-xl hover:-translate-y-0.5'"
                    style="background:linear-gradient(135deg,#10b981,#059669)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                تسجيل حضور
                <span class="bg-white/25 rounded-lg px-2 py-0.5 text-xs" x-show="selected.length > 0" x-text="'(' + selected.length + ')'"></span>
            </button>
        </div>
    </form>
</div>
@endif

</div>
@endsection
