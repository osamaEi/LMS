@extends('layouts.dashboard')

@section('title', 'حضور الحصة - ' . $session->title)

@push('styles')
<style>
/* ════════════════════════════════════════════════════════════
   Keyframes
════════════════════════════════════════════════════════════ */
@keyframes fadeUp      { from { opacity:0; transform:translateY(28px); } to { opacity:1; transform:translateY(0); } }
@keyframes fadeRight   { from { opacity:0; transform:translateX(-20px); } to { opacity:1; transform:translateX(0); } }
@keyframes scaleIn     { from { opacity:0; transform:scale(.88); }  to { opacity:1; transform:scale(1); } }
@keyframes float       { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
@keyframes pulse-ring  { 0%,100%{opacity:.4;transform:scale(1)} 50%{opacity:.15;transform:scale(1.18)} }
@keyframes shimmer     { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes spin-slow   { to{transform:rotate(360deg)} }
@keyframes pop         { 0%{transform:scale(1)} 40%{transform:scale(1.07)} 100%{transform:scale(1.03)} }
@keyframes countUp     { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
@keyframes drawLine    { from{stroke-dashoffset:400} to{stroke-dashoffset:0} }
@keyframes glow-pulse  { 0%,100%{box-shadow:0 0 20px rgba(0,113,170,.4)} 50%{box-shadow:0 0 40px rgba(0,113,170,.7)} }
@keyframes confetti    { 0%{transform:translateY(-10px) rotate(0deg);opacity:1} 100%{transform:translateY(100px) rotate(720deg);opacity:0} }

.fade-up    { animation: fadeUp   .6s cubic-bezier(.4,0,.2,1) both; }
.fade-right { animation: fadeRight .5s cubic-bezier(.4,0,.2,1) both; }
.scale-in   { animation: scaleIn  .5s cubic-bezier(.4,0,.2,1) both; }
.d1{animation-delay:.05s}.d2{animation-delay:.12s}.d3{animation-delay:.19s}
.d4{animation-delay:.26s}.d5{animation-delay:.33s}.d6{animation-delay:.40s}

/* ════════════════════════════════════════════════════════════
   Hero
════════════════════════════════════════════════════════════ */
.att-hero {
    background: linear-gradient(135deg, #020c1b 0%, #0a2240 30%, #0071AA 70%, #00c6e0 100%);
    border-radius: 32px;
    padding: 2.75rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 32px 80px rgba(0,113,170,.4), 0 0 0 1px rgba(255,255,255,.08);
}
.att-hero::before {
    content:'';
    position:absolute; inset:0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events:none;
}
.hero-blob {
    position:absolute; border-radius:50%; pointer-events:none;
}
.hero-blob-1 {
    top:-100px; right:-100px; width:380px; height:380px;
    background: radial-gradient(circle, rgba(0,198,224,.2) 0%, transparent 70%);
    animation: pulse-ring 6s ease-in-out infinite;
}
.hero-blob-2 {
    bottom:-120px; left:-80px; width:320px; height:320px;
    background: radial-gradient(circle, rgba(0,113,170,.3) 0%, transparent 70%);
    animation: pulse-ring 8s ease-in-out infinite 2s;
}
.hero-blob-3 {
    top:30%; left:35%; width:180px; height:180px;
    background: radial-gradient(circle, rgba(255,255,255,.06) 0%, transparent 70%);
    animation: float 5s ease-in-out infinite;
}
/* floating particles */
.particle {
    position:absolute; border-radius:50%; pointer-events:none;
    background:rgba(255,255,255,.15);
    animation: float linear infinite;
}

/* ════════════════════════════════════════════════════════════
   Gauge
════════════════════════════════════════════════════════════ */
.gauge-outer {
    position:relative; width:160px; height:160px; flex-shrink:0;
    filter: drop-shadow(0 8px 24px rgba(0,0,0,.3));
}
.gauge-outer svg { transform:rotate(-90deg); width:160px; height:160px; }
.gauge-center {
    position:absolute; top:50%; left:50%;
    transform:translate(-50%,-50%);
    text-align:center; pointer-events:none;
}
.gauge-pct   { font-size:2.2rem; font-weight:900; color:#fff; line-height:1; letter-spacing:-.02em; }
.gauge-lbl   { font-size:.65rem; color:rgba(255,255,255,.6); margin-top:3px; letter-spacing:.08em; text-transform:uppercase; }
.gauge-ring  { transition: stroke-dashoffset 1.4s cubic-bezier(.4,0,.2,1); }

/* ════════════════════════════════════════════════════════════
   Hero Pills
════════════════════════════════════════════════════════════ */
.hero-pill {
    display:flex; align-items:center; gap:.75rem;
    padding:.75rem 1.25rem;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.15);
    border-radius:16px;
    backdrop-filter:blur(12px);
    min-width:120px;
    transition: all .25s;
}
.hero-pill:hover { background:rgba(255,255,255,.14); transform:translateY(-2px); }
.hero-pill .pill-icon { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.hero-pill .pill-val  { font-size:1.5rem; font-weight:900; color:#fff; line-height:1; }
.hero-pill .pill-lbl  { font-size:.7rem; color:rgba(255,255,255,.65); margin-top:2px; }

/* ════════════════════════════════════════════════════════════
   Stat Mini Cards (below hero)
════════════════════════════════════════════════════════════ */
.stat-mini {
    background:#fff;
    border-radius:20px;
    padding:1.25rem 1.5rem;
    border:1px solid rgba(0,0,0,.05);
    box-shadow:0 4px 20px rgba(0,0,0,.05);
    display:flex; align-items:center; gap:1rem;
    transition: all .3s cubic-bezier(.4,0,.2,1);
    position:relative; overflow:hidden;
}
.stat-mini::after {
    content:''; position:absolute; bottom:0; left:0; right:0;
    height:3px; border-radius:0 0 20px 20px;
}
.stat-mini.blue::after  { background:linear-gradient(90deg,#0071AA,#00c6e0); }
.stat-mini.green::after { background:linear-gradient(90deg,#10b981,#34d399); }
.stat-mini.red::after   { background:linear-gradient(90deg,#ef4444,#f87171); }
.stat-mini.amber::after { background:linear-gradient(90deg,#f59e0b,#fbbf24); }
.stat-mini:hover { transform:translateY(-4px); box-shadow:0 12px 32px rgba(0,0,0,.1); }
.dark .stat-mini { background:#1e293b; border-color:rgba(255,255,255,.07); }
.stat-mini .sm-icon {
    width:52px; height:52px; border-radius:16px;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.stat-mini .sm-val  { font-size:1.75rem; font-weight:900; line-height:1; }
.stat-mini .sm-lbl  { font-size:.78rem; font-weight:600; color:#64748b; margin-top:3px; }
.dark .stat-mini .sm-lbl { color:#94a3b8; }

/* ════════════════════════════════════════════════════════════
   Cards
════════════════════════════════════════════════════════════ */
.att-card {
    background:#fff;
    border-radius:24px;
    overflow:hidden;
    border:1px solid rgba(0,0,0,.05);
    box-shadow:0 4px 24px rgba(0,0,0,.06);
}
.dark .att-card { background:#1e293b; border-color:rgba(255,255,255,.07); }

.att-card-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:1.35rem 1.6rem;
    border-bottom:1px solid #f1f5f9;
    background:linear-gradient(180deg,#fafbfc,#fff);
}
.dark .att-card-header { border-color:#334155; background:linear-gradient(180deg,#1e293b,#1a2744); }

/* ════════════════════════════════════════════════════════════
   Table
════════════════════════════════════════════════════════════ */
.att-table { width:100%; border-collapse:collapse; }
.att-table thead th {
    padding:.9rem 1.6rem;
    text-align:right;
    font-size:.72rem; font-weight:800; letter-spacing:.08em; text-transform:uppercase;
    color:#94a3b8;
    background:linear-gradient(180deg,#f8fafc,#f1f5f9);
    border-bottom:2px solid #e2e8f0;
    white-space:nowrap;
}
.att-table tbody td {
    padding:1.05rem 1.6rem;
    border-bottom:1px solid #f8fafc;
    font-size:.9rem;
    vertical-align:middle;
}
.att-table tbody tr:last-child td { border-bottom:none; }
.att-table tbody tr {
    transition: background .2s, transform .15s;
    cursor:default;
}
.att-table tbody tr:hover { background:rgba(0,113,170,.035); }
.dark .att-table thead th { background:linear-gradient(180deg,#0f172a,#1e293b); color:#475569; border-color:#334155; }
.dark .att-table tbody td { border-color:#0f172a; }
.dark .att-table tbody tr:hover { background:rgba(0,113,170,.08); }

/* Row accent line */
.att-table tbody tr td:first-child { position:relative; }
.att-table tbody tr:hover td:first-child::before {
    content:''; position:absolute; left:0; top:20%; bottom:20%;
    width:3px; border-radius:0 3px 3px 0;
    background:linear-gradient(180deg,#0071AA,#00c6e0);
}

.stu-avatar {
    width:44px; height:44px; border-radius:13px;
    border:2.5px solid #e2e8f0;
    transition:all .25s ease;
    object-fit:cover;
}
.att-table tbody tr:hover .stu-avatar { border-color:#0071AA; transform:scale(1.1) rotate(-2deg); }

.rank-badge {
    width:30px; height:30px; border-radius:9px;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:.75rem; font-weight:900; color:#94a3b8;
    background:#f1f5f9;
}
.rank-badge.gold   { background:linear-gradient(135deg,#fbbf24,#f59e0b); color:#fff; box-shadow:0 4px 12px rgba(251,191,36,.4); }
.rank-badge.silver { background:linear-gradient(135deg,#94a3b8,#64748b); color:#fff; box-shadow:0 4px 12px rgba(100,116,139,.3); }
.rank-badge.bronze { background:linear-gradient(135deg,#c2855a,#9a6345); color:#fff; box-shadow:0 4px 12px rgba(194,133,90,.3); }

.time-chip {
    display:inline-flex; align-items:center; gap:.35rem;
    padding:.32rem .75rem; border-radius:10px;
    font-size:.8rem; font-weight:600;
    background:#f1f5f9; color:#475569;
    white-space:nowrap;
}
.dark .time-chip { background:#0f172a; color:#94a3b8; }

.dur-chip {
    display:inline-flex; align-items:center; gap:.3rem;
    font-size:.88rem; font-weight:800; color:#0071AA;
}

.pct-bar { height:6px; border-radius:8px; background:#f1f5f9; overflow:hidden; width:80px; }
.pct-fill { height:100%; border-radius:8px; background:linear-gradient(90deg,#10b981,#34d399); transition:width .6s cubic-bezier(.4,0,.2,1); }

.badge-full    { display:inline-flex; align-items:center; gap:.35rem; padding:.38rem .9rem; border-radius:20px; font-size:.78rem; font-weight:800; background:linear-gradient(135deg,#d1fae5,#a7f3d0); color:#065f46; border:1px solid rgba(16,185,129,.2); }
.badge-partial { display:inline-flex; align-items:center; gap:.35rem; padding:.38rem .9rem; border-radius:20px; font-size:.78rem; font-weight:800; background:linear-gradient(135deg,#fef3c7,#fde68a); color:#92400e; border:1px solid rgba(245,158,11,.2); }
.badge-absent  { display:inline-flex; align-items:center; gap:.35rem; padding:.38rem .9rem; border-radius:20px; font-size:.78rem; font-weight:800; background:linear-gradient(135deg,#fee2e2,#fecaca); color:#991b1b; border:1px solid rgba(239,68,68,.2); }

/* ════════════════════════════════════════════════════════════
   Search
════════════════════════════════════════════════════════════ */
.search-wrap { position:relative; }
.search-wrap .s-icon { position:absolute; right:.9rem; top:50%; transform:translateY(-50%); color:#94a3b8; pointer-events:none; width:16px; height:16px; }
.search-wrap input {
    padding:.62rem .9rem .62rem 2.8rem;
    border:1.5px solid #e2e8f0; border-radius:13px;
    font-size:.875rem; background:#f8fafc; direction:rtl; width:230px;
    transition:all .28s; color:#1e293b;
}
.search-wrap input:focus { border-color:#0071AA; box-shadow:0 0 0 4px rgba(0,113,170,.1); outline:none; background:#fff; }
.dark .search-wrap input { background:#0f172a; border-color:#334155; color:#e2e8f0; }

/* ════════════════════════════════════════════════════════════
   Absent Selector Cards
════════════════════════════════════════════════════════════ */
@keyframes pop { 0%{transform:scale(1)} 40%{transform:scale(1.07)} 100%{transform:scale(1.03)} }

.sel-card {
    border-radius:18px; padding:1.1rem 1.2rem;
    border:2px solid rgba(239,68,68,.15);
    background:linear-gradient(135deg,#fff,#fafafa);
    cursor:pointer;
    transition:all .24s cubic-bezier(.4,0,.2,1);
    position:relative; overflow:hidden;
    user-select:none;
    box-shadow: 0 2px 12px rgba(239,68,68,.07);
}
.sel-card .accent-bar {
    position:absolute; top:0; right:0; width:4px; height:100%;
    background:linear-gradient(180deg,#ef4444,#dc2626);
    border-radius:0 16px 16px 0;
    transition:all .24s;
}
.sel-card.selected {
    border-color:#10b981;
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 80%);
    box-shadow: 0 0 0 3px rgba(16,185,129,.22), 0 10px 28px rgba(16,185,129,.2);
    transform: scale(1.028) translateY(-1px);
    animation: pop .26s cubic-bezier(.4,0,.2,1);
}
.sel-card.selected .accent-bar { background:linear-gradient(180deg,#10b981,#059669); width:5px; }
.sel-card:hover:not(.selected) { transform:translateY(-3px); box-shadow:0 10px 24px rgba(239,68,68,.14); border-color:rgba(239,68,68,.3); }
.dark .sel-card { background:linear-gradient(135deg,#1e293b,#1a2744); border-color:rgba(239,68,68,.2); }
.dark .sel-card.selected { background:linear-gradient(135deg,rgba(16,185,129,.16),rgba(16,185,129,.08)); border-color:#10b981; }

.sel-avatar { width:48px; height:48px; border-radius:14px; border:2.5px solid #fecaca; transition:all .24s; }
.sel-card.selected .sel-avatar { border-color:#10b981; box-shadow:0 0 0 4px rgba(16,185,129,.2); transform:scale(1.06); }

.check-ring {
    width:34px; height:34px; border-radius:50%; border:2.5px solid #e2e8f0;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0; transition:all .24s;
    background:#f8fafc;
}
.sel-card.selected .check-ring {
    background: linear-gradient(135deg,#10b981,#059669);
    border-color:#10b981;
    box-shadow: 0 0 0 5px rgba(16,185,129,.2);
    transform: scale(1.12);
}
.check-ring svg { opacity:0; transition:opacity .2s, transform .2s; transform:scale(.5); }
.sel-card.selected .check-ring svg { opacity:1; transform:scale(1); }

/* ════════════════════════════════════════════════════════════
   Breadcrumb
════════════════════════════════════════════════════════════ */
.bc {
    display:flex; align-items:center; gap:.55rem;
    padding:.7rem 1.2rem;
    background:#fff; border-radius:14px;
    border:1px solid rgba(0,0,0,.05);
    box-shadow:0 2px 10px rgba(0,0,0,.04);
    font-size:.85rem; flex-wrap:wrap;
}
.dark .bc { background:#1e293b; border-color:rgba(255,255,255,.08); }
.bc a { color:#64748b; font-weight:500; transition:color .2s; }
.bc a:hover { color:#0071AA; }
.bc .sep { color:#cbd5e1; font-size:.75rem; }
.bc .cur { color:#0f172a; font-weight:700; }
.dark .bc .cur { color:#f1f5f9; }

/* ════════════════════════════════════════════════════════════
   Export Button
════════════════════════════════════════════════════════════ */
.export-btn {
    display:inline-flex; align-items:center; gap:.5rem;
    padding:.55rem 1.1rem; border-radius:12px;
    font-size:.8rem; font-weight:700;
    border:1.5px solid #e2e8f0; background:#fff; color:#64748b;
    transition:all .22s; cursor:pointer;
}
.export-btn:hover { border-color:#0071AA; color:#0071AA; background:#eff8ff; transform:translateY(-1px); box-shadow:0 4px 12px rgba(0,113,170,.12); }
.dark .export-btn { background:#1e293b; border-color:#334155; color:#94a3b8; }
.dark .export-btn:hover { border-color:#0071AA; color:#38bdf8; background:rgba(0,113,170,.1); }

/* ════════════════════════════════════════════════════════════
   Session Info Strip
════════════════════════════════════════════════════════════ */
.info-strip {
    display:flex; align-items:center; flex-wrap:wrap; gap:.75rem;
    padding:1rem 1.6rem;
    background:linear-gradient(90deg, rgba(0,113,170,.04), rgba(0,198,224,.04));
    border-bottom:1px solid rgba(0,113,170,.08);
}
.info-strip-item {
    display:flex; align-items:center; gap:.45rem;
    font-size:.8rem; font-weight:600; color:#475569;
}
.dark .info-strip-item { color:#94a3b8; }
.info-strip-item svg { color:#0071AA; width:14px; height:14px; }

/* ════════════════════════════════════════════════════════════
   Empty state
════════════════════════════════════════════════════════════ */
.empty-state {
    display:flex; flex-direction:column; align-items:center;
    justify-content:center; padding:4rem 2rem; text-align:center;
}
.empty-icon-wrap {
    width:88px; height:88px; border-radius:28px;
    display:flex; align-items:center; justify-content:center; margin-bottom:1.5rem;
    background:linear-gradient(135deg,#fef3c7,#fde68a);
    box-shadow:0 8px 24px rgba(251,191,36,.25);
    animation: float 4s ease-in-out infinite;
}

/* ════════════════════════════════════════════════════════════
   Attendance Timeline visualization
════════════════════════════════════════════════════════════ */
.mini-bar-container {
    display:flex; align-items:flex-end; gap:3px; height:40px; padding:0 4px;
}
.mini-bar {
    flex:1; border-radius:3px 3px 0 0; min-height:4px;
    transition:height .5s cubic-bezier(.4,0,.2,1);
    background:linear-gradient(180deg,#0071AA,#00c6e0);
    opacity:.7;
}
.mini-bar.active { opacity:1; background:linear-gradient(180deg,#10b981,#34d399); }

/* ════════════════════════════════════════════════════════════
   Print
════════════════════════════════════════════════════════════ */
@media print {
    .att-hero .hero-blob, nav.bc, .export-btn, #add-attendance, button { display:none !important; }
    .att-card { box-shadow:none; border:1px solid #e2e8f0; page-break-inside:avoid; }
}
</style>
@endpush

@section('content')
@php
    $rate = $stats['attendance_rate'];
    $circumference = 2 * 3.14159 * 60;
    $offset = $circumference * (1 - $rate / 100);
    $rateColor  = $rate >= 75 ? '#10b981' : ($rate >= 50 ? '#f59e0b' : '#ef4444');
    $rateLabel  = $rate >= 75 ? 'ممتاز' : ($rate >= 50 ? 'متوسط' : 'منخفض');
    $rateBg     = $rate >= 75 ? 'rgba(16,185,129,.25)' : ($rate >= 50 ? 'rgba(245,158,11,.25)' : 'rgba(239,68,68,.25)');
    $attended   = $stats['attended'];
    $absent     = $stats['absent'];
    $total      = $stats['total_enrolled'];
    $partial    = $attendances->where('attended', true)->filter(fn($a) => !($a->video_completed || ($a->duration_minutes && $a->duration_minutes >= ($session->duration_minutes * 0.8))))->count();
    $fullAtt    = $attended - $partial;
@endphp

<div class="space-y-5 p-1" dir="rtl">

{{-- ══════════════════════════════════════════════════════════
     Breadcrumb
══════════════════════════════════════════════════════════ --}}
<nav class="bc fade-up">
    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
    <a href="{{ route('teacher.my-subjects.index') }}">موادي</a>
    <span class="sep">›</span>
    <a href="{{ route('teacher.my-subjects.show', $subject->id) }}">{{ $subject->name_ar ?? $subject->name }}</a>
    <span class="sep">›</span>
    <span class="cur">سجل الحضور — الحصة {{ $session->session_number }}</span>
</nav>

{{-- ══════════════════════════════════════════════════════════
     Hero Banner
══════════════════════════════════════════════════════════ --}}
<div class="att-hero fade-up d1">
    <div class="hero-blob hero-blob-1"></div>
    <div class="hero-blob hero-blob-2"></div>
    <div class="hero-blob hero-blob-3"></div>

    {{-- Floating particles --}}
    <div class="particle" style="width:6px;height:6px;top:15%;left:20%;animation-duration:7s;animation-delay:0s"></div>
    <div class="particle" style="width:4px;height:4px;top:60%;left:10%;animation-duration:5s;animation-delay:1s"></div>
    <div class="particle" style="width:5px;height:5px;top:30%;left:60%;animation-duration:9s;animation-delay:2s"></div>
    <div class="particle" style="width:3px;height:3px;top:75%;left:75%;animation-duration:6s;animation-delay:.5s"></div>

    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center gap-8">

        {{-- Session Info --}}
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold"
                      style="background:rgba(255,255,255,.12); color:rgba(255,255,255,.9); border:1px solid rgba(255,255,255,.2)">
                    <span class="w-2 h-2 rounded-full {{ $session->type === 'live_zoom' ? 'bg-red-400 animate-pulse' : 'bg-sky-300' }}"></span>
                    {{ $session->type === 'live_zoom' ? 'Zoom مباشر' : 'مسجّل' }}
                </span>
                @if($session->scheduled_at)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
                      style="background:rgba(255,255,255,.1); color:rgba(255,255,255,.8); border:1px solid rgba(255,255,255,.15)">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d · H:i') }}
                </span>
                @endif
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold"
                      style="background:{{ $rateBg }}; color:{{ $rateColor }}; border:1px solid {{ $rateColor }}50">
                    {{ $rateLabel }}
                </span>
            </div>

            <h1 class="text-2xl md:text-3xl lg:text-4xl font-black text-white tracking-tight mb-1 leading-tight">
                {{ $session->title }}
            </h1>
            <p class="text-white/60 text-sm mb-7 font-medium">
                {{ $subject->name_ar ?? $subject->name }}
                <span class="mx-2 opacity-40">·</span>
                الحصة رقم {{ $session->session_number }}
                @if($session->duration_minutes)
                <span class="mx-2 opacity-40">·</span>
                {{ $session->duration_minutes }} دقيقة
                @endif
            </p>

            {{-- Stat Pills --}}
            <div class="flex flex-wrap gap-3">
                <div class="hero-pill scale-in d2">
                    <div class="pill-icon" style="background:rgba(96,165,250,.2)">
                        <svg class="w-5 h-5" style="color:#93c5fd" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <div class="pill-val">{{ $total }}</div>
                        <div class="pill-lbl">إجمالي الطلاب</div>
                    </div>
                </div>
                <div class="hero-pill scale-in d3">
                    <div class="pill-icon" style="background:rgba(52,211,153,.2)">
                        <svg class="w-5 h-5" style="color:#6ee7b7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="pill-val">{{ $attended }}</div>
                        <div class="pill-lbl">الحاضرون</div>
                    </div>
                </div>
                <div class="hero-pill scale-in d4">
                    <div class="pill-icon" style="background:rgba(248,113,113,.2)">
                        <svg class="w-5 h-5" style="color:#fca5a5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="pill-val">{{ $absent }}</div>
                        <div class="pill-lbl">الغائبون</div>
                    </div>
                </div>
                @if($partial > 0)
                <div class="hero-pill scale-in d5">
                    <div class="pill-icon" style="background:rgba(251,191,36,.2)">
                        <svg class="w-5 h-5" style="color:#fde68a" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="pill-val">{{ $partial }}</div>
                        <div class="pill-lbl">حضور جزئي</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Gauge --}}
        <div class="flex flex-col items-center gap-4 scale-in d3">
            <div class="gauge-outer">
                <svg viewBox="0 0 140 140">
                    <defs>
                        <linearGradient id="gaugeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:{{ $rateColor }}" />
                            <stop offset="100%" style="stop-color:{{ $rateColor }};stop-opacity:.6" />
                        </linearGradient>
                        <filter id="glow">
                            <feGaussianBlur stdDeviation="4" result="blur"/>
                            <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
                        </filter>
                        <filter id="shadow">
                            <feDropShadow dx="0" dy="0" stdDeviation="6" flood-color="{{ $rateColor }}" flood-opacity=".6"/>
                        </filter>
                    </defs>
                    {{-- Track --}}
                    <circle cx="70" cy="70" r="60" fill="none" stroke="rgba(255,255,255,.08)" stroke-width="12"/>
                    {{-- Glow track --}}
                    <circle cx="70" cy="70" r="60" fill="none" stroke="rgba(255,255,255,.04)" stroke-width="18"/>
                    {{-- Progress ring --}}
                    <circle cx="70" cy="70" r="60" fill="none"
                            stroke="url(#gaugeGrad)"
                            stroke-width="12"
                            stroke-linecap="round"
                            stroke-dasharray="{{ $circumference }}"
                            stroke-dashoffset="{{ $offset }}"
                            class="gauge-ring"
                            filter="url(#glow)"/>
                    {{-- Inner ring --}}
                    <circle cx="70" cy="70" r="46" fill="none" stroke="rgba(255,255,255,.05)" stroke-width="1"/>
                </svg>
                <div class="gauge-center">
                    <div class="gauge-pct">{{ $rate }}%</div>
                    <div class="gauge-lbl">نسبة الحضور</div>
                </div>
            </div>
            <div class="px-4 py-1.5 rounded-full text-xs font-bold"
                 style="background:{{ $rateBg }}; color:{{ $rateColor }}; border:1.5px solid {{ $rateColor }}40">
                {{ $rateLabel }}
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col gap-2.5 lg:self-start">
            <a href="{{ route('teacher.my-subjects.show', $subject->id) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition hover:bg-white/20"
               style="background:rgba(255,255,255,.1); border:1px solid rgba(255,255,255,.2)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                العودة للمادة
            </a>
            @if($absentStudents->count() > 0)
            <a href="#add-attendance"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition hover:brightness-110"
               style="background:linear-gradient(135deg,rgba(16,185,129,.5),rgba(5,150,105,.5)); border:1px solid rgba(16,185,129,.5)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                إضافة حضور
                <span class="bg-white/25 text-white text-xs font-black px-2 py-0.5 rounded-lg">{{ $absentStudents->count() }}</span>
            </a>
            @endif
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition hover:bg-white/20"
                    style="background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.15)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                طباعة
            </button>
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     Mini Stats Row
══════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 fade-up d2">
    {{-- Total --}}
    <div class="stat-mini blue">
        <div class="sm-icon" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe)">
            <svg class="w-6 h-6" style="color:#2563eb" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <div>
            <div class="sm-val" style="color:#1d4ed8">{{ $total }}</div>
            <div class="sm-lbl">إجمالي المسجلين</div>
        </div>
    </div>
    {{-- Attended --}}
    <div class="stat-mini green">
        <div class="sm-icon" style="background:linear-gradient(135deg,#d1fae5,#a7f3d0)">
            <svg class="w-6 h-6" style="color:#059669" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="sm-val" style="color:#065f46">{{ $attended }}</div>
            <div class="sm-lbl">طالب حاضر</div>
        </div>
    </div>
    {{-- Absent --}}
    <div class="stat-mini red">
        <div class="sm-icon" style="background:linear-gradient(135deg,#fee2e2,#fecaca)">
            <svg class="w-6 h-6" style="color:#dc2626" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="sm-val" style="color:#991b1b">{{ $absent }}</div>
            <div class="sm-lbl">طالب غائب</div>
        </div>
    </div>
    {{-- Rate --}}
    <div class="stat-mini amber">
        <div class="sm-icon" style="background:linear-gradient(135deg,#fef3c7,#fde68a)">
            <svg class="w-6 h-6" style="color:#d97706" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <div>
            <div class="sm-val" style="color:{{ $rateColor }}">{{ $rate }}%</div>
            <div class="sm-lbl">نسبة الحضور</div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     Attended Students Table
══════════════════════════════════════════════════════════ --}}
<div class="att-card fade-up d3" x-data="{ search: '' }">

    {{-- Card Header --}}
    <div class="att-card-header">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center"
                 style="background:linear-gradient(135deg,#10b981,#059669); box-shadow:0 4px 14px rgba(16,185,129,.35)">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-gray-900 dark:text-white text-base">الطلاب الحاضرون</h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    <span class="font-bold text-emerald-500">{{ $attendances->where('attended', true)->count() }}</span>
                    من أصل
                    <span class="font-bold text-gray-600 dark:text-gray-300">{{ $total }}</span>
                    طالب مسجّل
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <button onclick="window.print()" class="export-btn">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                تصدير
            </button>
            <div class="search-wrap">
                <svg class="s-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="search" placeholder="ابحث عن طالب..." />
            </div>
        </div>
    </div>

    {{-- Session Info Strip --}}
    <div class="info-strip">
        @if($session->scheduled_at)
        <div class="info-strip-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            {{ \Carbon\Carbon::parse($session->scheduled_at)->format('l، j F Y') }}
        </div>
        @endif
        @if($session->duration_minutes)
        <div class="info-strip-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            مدة الحصة: {{ $session->duration_minutes }} دقيقة
        </div>
        @endif
        <div class="info-strip-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            نسبة الحضور: <strong style="color:{{ $rateColor }}">{{ $rate }}%</strong>
        </div>
        <div class="info-strip-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
            الحضور الكامل: <strong class="text-emerald-600">{{ $fullAtt }}</strong>
            &nbsp;|&nbsp; الجزئي: <strong class="text-amber-600">{{ $partial }}</strong>
        </div>
    </div>

    @if($attendances->where('attended', true)->count() > 0)
    <div class="overflow-x-auto">
        <table class="att-table">
            <thead>
                <tr>
                    <th style="width:56px">#</th>
                    <th>الطالب</th>
                    <th>وقت الانضمام</th>
                    <th>وقت المغادرة</th>
                    <th>المدة</th>
                    @if($session->type === 'recorded_video')
                    <th>نسبة المشاهدة</th>
                    @endif
                    <th>حالة الحضور</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances->where('attended', true) as $i => $att)
                @php
                    $name  = $att->student->name ?? '';
                    $email = $att->student->email ?? '';
                    $isFull = $att->video_completed || ($att->duration_minutes && $att->duration_minutes >= ($session->duration_minutes * 0.8));
                @endphp
                <tr x-show="search===''||'{{ strtolower($name) }}'.includes(search.toLowerCase())||'{{ strtolower($email) }}'.includes(search.toLowerCase())">
                    {{-- Rank --}}
                    <td>
                        <span class="rank-badge {{ $i===0 ? 'gold' : ($i===1 ? 'silver' : ($i===2 ? 'bronze' : '')) }}">
                            {{ $i + 1 }}
                        </span>
                    </td>
                    {{-- Student --}}
                    <td>
                        <div class="flex items-center gap-3.5">
                            <div class="relative">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($name ?: 'N') }}&background=0071AA&color=fff&size=88&bold=true&rounded=true"
                                     alt="{{ $name }}" class="stu-avatar" />
                                <span class="absolute -bottom-0.5 -left-0.5 w-3 h-3 rounded-full border-2 border-white {{ $isFull ? 'bg-emerald-400' : 'bg-amber-400' }}"></span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white text-sm leading-tight">{{ $name ?: 'غير معروف' }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $email }}</p>
                            </div>
                        </div>
                    </td>
                    {{-- Join --}}
                    <td>
                        @if($att->joined_at)
                        <span class="time-chip">
                            <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                            {{ \Carbon\Carbon::parse($att->joined_at)->format('h:i A') }}
                        </span>
                        @else<span class="text-gray-300 dark:text-gray-600 text-sm">—</span>@endif
                    </td>
                    {{-- Left --}}
                    <td>
                        @if($att->left_at)
                        <span class="time-chip">
                            <svg class="w-3 h-3 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                            {{ \Carbon\Carbon::parse($att->left_at)->format('h:i A') }}
                        </span>
                        @else<span class="text-gray-300 dark:text-gray-600 text-sm">—</span>@endif
                    </td>
                    {{-- Duration --}}
                    <td>
                        @if($att->duration_minutes)
                        <span class="dur-chip">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $att->duration_minutes }} د
                        </span>
                        @else<span class="text-gray-300 dark:text-gray-600 text-sm">—</span>@endif
                    </td>
                    {{-- Watch % --}}
                    @if($session->type === 'recorded_video')
                    <td>
                        <div class="flex items-center gap-2.5">
                            <div class="pct-bar">
                                <div class="pct-fill" style="width:{{ $att->watch_percentage ?? 0 }}%"></div>
                            </div>
                            <span class="text-xs font-bold" style="color:#0071AA">{{ $att->watch_percentage ?? 0 }}%</span>
                        </div>
                    </td>
                    @endif
                    {{-- Status badge --}}
                    <td>
                        @if($isFull)
                            <span class="badge-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
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
    <div class="empty-state">
        <div class="empty-icon-wrap">
            <svg class="w-11 h-11" style="color:#f59e0b" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white">لا يوجد حضور مسجّل بعد</h3>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto leading-relaxed">
            سيظهر الحضور تلقائياً عند انضمام الطلاب، أو يمكنك تسجيله يدوياً أدناه
        </p>
        @if($absentStudents->count() > 0)
        <a href="#add-attendance"
           class="mt-5 inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all"
           style="background:linear-gradient(135deg,#10b981,#059669)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            سجّل الحضور يدوياً
        </a>
        @endif
    </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════
     Add Attendance (Absent Students)
══════════════════════════════════════════════════════════ --}}
@if($absentStudents->count() > 0)
@php $totalAbsent = $absentStudents->count(); @endphp
<div class="att-card fade-up d4" id="add-attendance"
     x-data="{
         selected: [],
         allIds: {{ json_encode($absentStudents->pluck('id')->values()) }},
         get allSelected() { return this.selected.length === this.allIds.length && this.allIds.length > 0; },
         toggleAll() { this.selected = this.allSelected ? [] : [...this.allIds]; },
         get pct() { return this.allIds.length > 0 ? Math.round((this.selected.length / this.allIds.length) * 100) : 0; }
     }">

    {{-- Header --}}
    <div class="att-card-header" style="flex-direction:column; align-items:stretch; gap:.9rem">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:linear-gradient(135deg,#ef4444,#dc2626); box-shadow:0 4px 14px rgba(239,68,68,.35)">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-gray-900 dark:text-white text-base">تسجيل الحضور اليدوي</h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        <span class="font-bold text-red-500">{{ $totalAbsent }}</span> طالب غائب — انقر على بطاقة الطالب لتحديده
                    </p>
                </div>
            </div>

            {{-- Toggle All --}}
            <button type="button" @click="toggleAll()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all duration-200"
                    :class="allSelected
                        ? 'text-red-600 bg-red-50 border border-red-200 hover:bg-red-100 dark:bg-red-900/20 dark:border-red-800'
                        : 'text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:border-emerald-800'">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          x-show="!allSelected"
                          d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          x-show="allSelected" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span x-text="allSelected ? 'إلغاء تحديد الكل' : 'تحديد الكل'"></span>
            </button>
        </div>

        {{-- Progress Bar --}}
        <div class="flex items-center gap-3">
            <div class="flex-1 h-2.5 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500"
                     style="background:linear-gradient(90deg,#10b981,#34d399)"
                     :style="'width:' + pct + '%'">
                </div>
            </div>
            <span class="text-xs font-bold w-36 text-right flex-shrink-0"
                  :class="selected.length > 0 ? 'text-emerald-600' : 'text-gray-400'"
                  x-text="selected.length > 0 ? selected.length + ' / {{ $totalAbsent }} محدد (' + pct + '%)' : 'لم يُحدَّد أحد بعد'">
            </span>
        </div>
    </div>

    {{-- Success flash --}}
    @if(session('success'))
    <div class="mx-5 mt-4 flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-emerald-700 border border-emerald-200"
         style="background:linear-gradient(135deg,#f0fdf4,#dcfce7)">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
             style="background:#10b981">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('teacher.my-subjects.sessions.attendance.save', [$subject->id, $session->id]) }}"
          method="POST" id="attendance-form">
        @csrf

        <div class="p-5 pb-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                @foreach($absentStudents as $idx => $student)
                <label class="sel-card" :class="selected.includes({{ $student->id }}) ? 'selected' : ''"
                       style="animation: fadeUp .4s cubic-bezier(.4,0,.2,1) {{ $idx * 0.04 }}s both">
                    <div class="accent-bar"></div>
                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                           class="sr-only" x-model="selected" :value="{{ $student->id }}">

                    {{-- Selected badge --}}
                    <div class="absolute top-2.5 left-2.5 z-10 transition-all duration-200"
                         :class="selected.includes({{ $student->id }}) ? 'opacity-100 scale-100' : 'opacity-0 scale-75'">
                        <span class="inline-flex items-center gap-1 text-xs font-black text-white px-2.5 py-0.5 rounded-full"
                              style="background:linear-gradient(135deg,#10b981,#059669); box-shadow:0 2px 8px rgba(16,185,129,.4)">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                            حاضر
                        </span>
                    </div>

                    <div class="flex items-center gap-3.5">
                        {{-- Avatar --}}
                        <div class="relative flex-shrink-0">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name ?? 'N') }}&background=64748b&color=fff&size=96&bold=true&rounded=true"
                                 alt="{{ $student->name }}"
                                 class="sel-avatar" />
                            <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white transition-all duration-200"
                                  :class="selected.includes({{ $student->id }}) ? 'bg-emerald-400' : 'bg-red-400'"></span>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate leading-tight">
                                {{ $student->name ?? 'غير معروف' }}
                            </p>
                            <p class="text-xs text-gray-400 truncate mt-0.5">{{ $student->email ?? '' }}</p>
                            <div class="mt-1.5">
                                <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-0.5 rounded-full transition-all duration-200"
                                      :class="selected.includes({{ $student->id }})
                                          ? 'bg-emerald-100 text-emerald-700 border border-emerald-200'
                                          : 'bg-red-50 text-red-500 border border-red-100'"
                                      x-text="selected.includes({{ $student->id }}) ? '✓ سيُسجَّل حضوره' : '✕ غائب'">
                                </span>
                            </div>
                        </div>

                        {{-- Check Ring --}}
                        <div class="check-ring flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Sticky Footer --}}
        <div class="sticky bottom-0 z-20 flex items-center justify-between gap-4 px-5 py-4
                    border-t border-gray-100 dark:border-gray-700
                    bg-white/95 dark:bg-slate-800/95 backdrop-blur-sm rounded-b-2xl">

            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-200"
                     :class="selected.length > 0 ? 'bg-emerald-100' : 'bg-gray-100'">
                    <svg class="w-4 h-4 transition-colors duration-200"
                         :class="selected.length > 0 ? 'text-emerald-600' : 'text-gray-400'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-400 dark:text-gray-500" x-show="selected.length === 0">انقر على بطاقات الطلاب لتحديد الحاضرين</p>
                    <p class="text-sm font-bold text-emerald-600" x-show="selected.length > 0"
                       x-text="'تم تحديد ' + selected.length + ' طالب للتسجيل'"></p>
                </div>
            </div>

            <button type="submit" form="attendance-form"
                    :disabled="selected.length === 0"
                    class="inline-flex items-center gap-2.5 rounded-xl px-8 py-3 text-sm font-black text-white transition-all duration-300"
                    :class="selected.length === 0
                        ? 'opacity-40 cursor-not-allowed'
                        : 'shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0'"
                    style="background:linear-gradient(135deg,#10b981,#059669)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                حفظ الحضور
                <span class="bg-white/25 rounded-lg px-2.5 py-0.5 text-xs font-black transition-all"
                      x-show="selected.length > 0" x-text="selected.length"></span>
            </button>
        </div>
    </form>
</div>
@endif

</div>
@endsection
