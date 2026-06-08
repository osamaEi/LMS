@extends('layouts.dashboard')
@section('title', 'برنامجي التدريبي ')

@push('styles')
<style>
.prog-page { padding: 1.5rem; max-width: 1200px; margin: 0 auto; }

/* Hero Header */
.prog-hero {
    background: linear-gradient(135deg, #0071AA 0%, #004d77 100%);
    border-radius: 20px;
    padding: 2rem 2.25rem;
    color: #fff;
    position: relative;
    overflow: hidden;
    margin-bottom: 1.5rem;
}
.prog-hero::before {
    content: '';
    position: absolute;
    top: -40%;
    left: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.04);
    border-radius: 50%;
}
.prog-hero::after {
    content: '';
    position: absolute;
    bottom: -30%;
    right: -5%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.03);
    border-radius: 50%;
}
.hero-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 1.5rem; position: relative; z-index: 1; flex-wrap: wrap; }
.hero-info { flex: 1; min-width: 0; }
.hero-name { font-size: 1.6rem; font-weight: 800; margin-bottom: 0.35rem; }
.hero-desc { opacity: 0.85; font-size: 0.9rem; line-height: 1.6; margin-bottom: 0.75rem; }
.hero-track {
    display: inline-flex; align-items: center; gap: 0.4rem;
    background: rgba(255,255,255,0.15); padding: 0.35rem 0.85rem;
    border-radius: 999px; font-size: 0.8rem; font-weight: 600;
}
.hero-term-box {
    background: rgba(255,255,255,0.15);
    border-radius: 16px;
    padding: 1rem 1.75rem;
    text-align: center;
    flex-shrink: 0;
    backdrop-filter: blur(4px);
}
.hero-term-num { font-size: 2.25rem; font-weight: 900; line-height: 1; }
.hero-term-label { font-size: 0.75rem; opacity: 0.8; margin-top: 0.25rem; }
.hero-progress { position: relative; z-index: 1; margin-top: 1.75rem; }
.hero-progress-bar {
    width: 100%; height: 8px; border-radius: 999px;
    background: rgba(255,255,255,0.2); overflow: hidden;
}
.hero-progress-fill {
    height: 100%; border-radius: 999px;
    background: linear-gradient(90deg, #34d399, #10b981);
    transition: width 0.8s ease;
}
.hero-progress-labels {
    display: flex; justify-content: space-between;
    font-size: 0.8rem; margin-bottom: 0.5rem; opacity: 0.9;
}

/* Stats Row */
.stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1.5rem; }
.stat-card {
    background: #fff; border-radius: 16px; padding: 1.25rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    display: flex; align-items: center; gap: 1rem;
}
.stat-icon {
    width: 48px; height: 48px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.stat-icon svg { width: 24px; height: 24px; color: #fff; }
.stat-val { font-size: 1.75rem; font-weight: 800; color: #1e293b; line-height: 1; }
.stat-label { font-size: 0.78rem; color: #94a3b8; margin-top: 0.2rem; }

/* Section Card */
.sec-card {
    background: #fff; border-radius: 18px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    overflow: hidden; margin-bottom: 1.5rem;
}
.sec-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
}
.sec-title { font-size: 1.05rem; font-weight: 800; color: #1e293b; display: flex; align-items: center; gap: 0.6rem; }
.sec-title-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
}
.sec-title-icon svg { width: 18px; height: 18px; color: #fff; }
.sec-link { font-size: 0.82rem; font-weight: 700; color: #0071AA; text-decoration: none; display: flex; align-items: center; gap: 0.3rem; }
.sec-link:hover { text-decoration: underline; }

/* Timeline */
.timeline { padding: 1.5rem; }
.tl-item {
    display: flex; gap: 1.25rem; position: relative;
    padding-bottom: 1.5rem;
}
.tl-item:last-child { padding-bottom: 0; }
.tl-item:not(:last-child)::after {
    content: '';
    position: absolute;
    right: 23px; /* center of 48px circle */
    top: 48px;
    bottom: 0;
    width: 3px;
    background: #e2e8f0;
}
.tl-item.past:not(:last-child)::after { background: #10b981; }
.tl-item.current:not(:last-child)::after {
    background: linear-gradient(to bottom, #0071AA 50%, #e2e8f0 50%);
}
.tl-circle {
    width: 48px; height: 48px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 1.1rem; color: #fff; flex-shrink: 0;
    position: relative; z-index: 1;
}
.tl-circle.past { background: linear-gradient(135deg, #10b981, #059669); }
.tl-circle.current { background: linear-gradient(135deg, #0071AA, #005a88); box-shadow: 0 0 0 4px rgba(0,113,170,0.2); }
.tl-circle.future { background: #e2e8f0; color: #94a3b8; }
.tl-body { flex: 1; min-width: 0; }
.tl-row { display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; flex-wrap: wrap; }
.tl-name { font-weight: 700; font-size: 0.95rem; color: #1e293b; }
.tl-meta { display: flex; align-items: center; gap: 0.75rem; margin-top: 0.35rem; font-size: 0.8rem; color: #94a3b8; }
.tl-meta svg { width: 14px; height: 14px; flex-shrink: 0; }
.tl-badge {
    font-size: 0.72rem; font-weight: 700; padding: 0.3rem 0.75rem;
    border-radius: 999px; display: inline-flex; align-items: center; gap: 0.3rem;
}
.tl-badge.past { background: #d1fae5; color: #065f46; }
.tl-badge.current { background: #0071AA; color: #fff; }
.tl-badge.future { background: #f1f5f9; color: #64748b; }
.tl-subjects {
    display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.6rem;
}
.tl-subj-chip {
    font-size: 0.72rem; padding: 0.25rem 0.6rem;
    border-radius: 8px; background: #f8fafc; color: #475569;
    border: 1px solid #e2e8f0; font-weight: 600;
}

/* Subject Cards Grid */
.subj-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1rem; padding: 1.5rem; }
.subj-card {
    border: 1px solid #f1f5f9;
    border-radius: 16px;
    overflow: hidden;
    transition: box-shadow 0.2s, transform 0.15s;
}
.subj-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.08); transform: translateY(-2px); }
.subj-top {
    height: 6px; width: 100%;
}
.subj-body { padding: 1.25rem; }
.subj-name { font-size: 1rem; font-weight: 800; color: #1e293b; margin-bottom: 0.5rem; }
.subj-chips { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.85rem; }
.subj-chip {
    display: inline-flex; align-items: center; gap: 0.3rem;
    font-size: 0.75rem; color: #64748b; background: #f8fafc;
    padding: 0.3rem 0.65rem; border-radius: 8px;
}
.subj-chip svg { width: 13px; height: 13px; }
.subj-progress { margin-bottom: 0.85rem; }
.subj-progress-header { display: flex; justify-content: space-between; margin-bottom: 0.35rem; }
.subj-progress-label { font-size: 0.75rem; color: #94a3b8; }
.subj-progress-pct { font-size: 0.75rem; font-weight: 800; color: #10b981; }
.subj-progress-track { height: 6px; background: #f1f5f9; border-radius: 999px; overflow: hidden; }
.subj-progress-fill { height: 100%; border-radius: 999px; background: linear-gradient(90deg, #34d399, #10b981); transition: width 0.6s; }
.subj-progress-text { font-size: 0.72rem; color: #94a3b8; margin-top: 0.25rem; }
.subj-btn {
    display: flex; align-items: center; justify-content: center; gap: 0.4rem;
    width: 100%; padding: 0.65rem; border-radius: 12px;
    background: linear-gradient(135deg, #0071AA, #005a88);
    color: #fff; font-size: 0.85rem; font-weight: 700;
    text-decoration: none; transition: opacity 0.2s;
}
.subj-btn:hover { opacity: 0.9; }
.subj-btn svg { width: 16px; height: 16px; }

/* Program Info Sidebar */
.info-list { padding: 1.25rem 1.5rem; }
.info-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0.85rem 0;
    border-bottom: 1px solid #f8fafc;
    font-size: 0.88rem;
}
.info-row:last-child { border-bottom: none; }
.info-key { color: #94a3b8; }
.info-val { font-weight: 700; color: #1e293b; }
.info-badge {
    font-size: 0.72rem; font-weight: 700; padding: 0.25rem 0.75rem;
    border-radius: 999px; background: #d1fae5; color: #065f46;
}

/* Two Column Layout */
.two-col { display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; margin-bottom: 1.5rem; }

/* Empty / No Program */
.enroll-hero {
    background: linear-gradient(135deg, #0071AA 0%, #004d77 100%);
    border-radius: 24px; padding: 3rem 2rem; text-align: center;
    color: #fff; position: relative; overflow: hidden; margin-bottom: 1.5rem;
}
.enroll-hero::before {
    content: ''; position: absolute; top: -60px; right: -60px;
    width: 200px; height: 200px; border-radius: 50%;
    background: rgba(255,255,255,0.05);
}
.enroll-icon {
    width: 80px; height: 80px; border-radius: 24px; margin: 0 auto 1.5rem;
    background: rgba(255,255,255,0.15);
    display: flex; align-items: center; justify-content: center;
}
.enroll-icon svg { width: 40px; height: 40px; }
.enroll-title { font-size: 1.75rem; font-weight: 900; margin-bottom: 0.5rem; }
.enroll-sub { opacity: 0.85; font-size: 0.95rem; max-width: 500px; margin: 0 auto; }

.prog-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.25rem; }
.prog-card {
    background: #fff; border-radius: 20px; overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    border: 2px solid transparent;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.prog-card:hover { border-color: #0071AA; box-shadow: 0 8px 24px rgba(0,113,170,0.12); }
.prog-card-header {
    background: linear-gradient(135deg, #0071AA, #005a88);
    padding: 1.5rem; color: #fff;
}
.prog-card-name { font-size: 1.15rem; font-weight: 800; }
.prog-card-code {
    display: inline-block; margin-top: 0.35rem;
    background: rgba(255,255,255,0.2); padding: 0.2rem 0.6rem;
    border-radius: 6px; font-size: 0.75rem; font-weight: 600;
}
.prog-card-body { padding: 1.5rem; }
.prog-card-desc { font-size: 0.85rem; color: #64748b; line-height: 1.6; margin-bottom: 1rem; }
.prog-card-meta { display: flex; flex-direction: column; gap: 0.65rem; margin-bottom: 1.25rem; }
.prog-card-meta-item {
    display: flex; align-items: center; gap: 0.6rem; font-size: 0.85rem; color: #475569;
}
.prog-card-meta-icon {
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.prog-card-meta-icon svg { width: 16px; height: 16px; }
.enroll-btn {
    width: 100%; padding: 0.85rem; border: none; border-radius: 14px;
    background: linear-gradient(135deg, #0071AA, #005a88);
    color: #fff; font-size: 0.95rem; font-weight: 800; cursor: pointer;
    transition: opacity 0.2s;
}
.enroll-btn:hover { opacity: 0.9; }

.alert-box {
    padding: 0.85rem 1.25rem; border-radius: 12px; font-size: 0.88rem;
    font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.6rem;
}
.alert-success { background: #ecfdf5; color: #065f46; }
.alert-error { background: #fef2f2; color: #991b1b; }

.help-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; padding: 1.5rem; }
.help-card {
    display: flex; align-items: flex-start; gap: 1rem;
    padding: 1.25rem; border-radius: 14px; border: 1px solid #f1f5f9;
}
.help-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.help-icon svg { width: 22px; height: 22px; }
.help-title { font-weight: 700; font-size: 0.9rem; color: #1e293b; margin-bottom: 0.25rem; }
.help-desc { font-size: 0.8rem; color: #94a3b8; line-height: 1.5; }
.help-link { font-size: 0.82rem; font-weight: 700; color: #0071AA; text-decoration: none; margin-top: 0.5rem; display: inline-block; }

.no-prog-empty { text-align: center; padding: 4rem 1rem; }
.no-prog-empty svg { width: 56px; height: 56px; color: #f59e0b; margin: 0 auto 1rem; }

/* Dark */
.dark .stat-card, .dark .sec-card, .dark .prog-card, .dark .help-card { background: #1e293b; }
.dark .stat-val, .dark .sec-title, .dark .tl-name, .dark .subj-name, .dark .info-val, .dark .help-title { color: #f1f5f9; }
.dark .info-row { border-color: #334155; }
.dark .tl-item:not(:last-child)::after { background: #334155; }
.dark .tl-item.past:not(:last-child)::after { background: #10b981; }
.dark .tl-circle.future { background: #334155; color: #64748b; }
.dark .tl-subj-chip { background: #0f172a; border-color: #334155; color: #94a3b8; }
.dark .subj-card { border-color: #334155; }
.dark .subj-chip { background: #0f172a; color: #94a3b8; }
.dark .sec-header { border-color: #334155; }

@media (max-width: 1024px) {
    .two-col { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
    .stats-row { grid-template-columns: repeat(2, 1fr); }
    .subj-grid { grid-template-columns: 1fr; }
    .help-grid { grid-template-columns: 1fr; }
    .prog-cards { grid-template-columns: 1fr; }
    .hero-top { flex-direction: column; }
    .hero-term-box { align-self: flex-start; }
}
@media (max-width: 480px) {
    .prog-page { padding: 1rem; }
    .prog-hero { padding: 1.5rem; }
    .hero-name { font-size: 1.3rem; }
    .tl-item { gap: 0.75rem; }
    .tl-circle { width: 40px; height: 40px; font-size: 0.95rem; }
    .tl-item:not(:last-child)::after { right: 19px; }
}
</style>
@endpush

@section('content')
<div class="prog-page">

@if(!empty($programsData) && count($programsData) > 0)

{{-- ══════════════════════════════════════════════════════════════
     PROGRAM TABS — one tab per enrolled program
══════════════════════════════════════════════════════════════ --}}
@php
    $tabKeys = array_keys($programsData);
    $firstTabKey = $tabKeys[0] ?? null;
@endphp

{{-- Tab bar --}}
<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:0;border-bottom:2px solid #e5e7eb;padding-bottom:0;">
    @foreach($programsData as $progId => $pd)
    @php
        $prog = $pd['program'];
        $tabColor = $prog->type === 'diploma' ? '#0071AA'
                  : ($prog->type === 'english' ? '#059669' : '#7c3aed');
        $typeLabel = match($prog->type) {
            'diploma'  => 'دبلومة',
            'english'  => 'إنجليزي',
            'course'   => 'دورة',
            'training' => 'تدريب',
            default    => $prog->type,
        };
    @endphp
    <button onclick="switchProgTab({{ $progId }})" id="progtab-{{ $progId }}"
            data-color="{{ $tabColor }}"
            style="padding:10px 20px;border:none;border-bottom:3px solid transparent;background:transparent;cursor:pointer;
                   font-size:.88rem;font-weight:700;font-family:inherit;color:#6b7280;
                   transition:all .2s;border-radius:10px 10px 0 0;margin-bottom:-2px;display:flex;align-items:center;gap:8px;">
        <span style="width:8px;height:8px;border-radius:50%;background:{{ $tabColor }};display:inline-block;flex-shrink:0;"></span>
        {{ $prog->name_ar ?? $prog->name }}
        <span style="background:#f1f5f9;color:#6b7280;font-size:.7rem;font-weight:700;padding:1px 7px;border-radius:20px;">{{ $typeLabel }}</span>
    </button>
    @endforeach
</div>

{{-- Tab panels --}}
@foreach($programsData as $progId => $pd)
@php
    $prog                = $pd['program'];
    $pdStats             = $pd['stats'];
    $pdTerms             = $pd['terms'];
    $pdCurrentTerm       = $pd['currentTerm'];
    $pdCurrentTermIndex  = $pd['currentTermIndex'];
    $pdCurrentTermSubj   = $pd['currentTermSubjects'];
    $pdSubjects          = $pd['subjects'];
    $pdSubjProgress      = $pd['subjectsProgress'];
    $pdSessions          = $pd['programSessions'];
    $tabColor = $prog->type === 'diploma' ? '#0071AA'
              : ($prog->type === 'english' ? '#059669' : '#7c3aed');
    $gradBg = $prog->type === 'diploma'
            ? 'linear-gradient(135deg,#0f172a 0%,#1e3a5f 55%,#0071AA 100%)'
            : ($prog->type === 'english'
                ? 'linear-gradient(135deg,#022c22,#065f46,#059669)'
                : 'linear-gradient(135deg,#1e1b4b,#3730a3,#7c3aed)');
@endphp
<div id="progpanel-{{ $progId }}" style="display:none;padding-top:20px;">

@if($pd['isDiploma'])
{{-- ── DIPLOMA panel ── --}}

<div class="prog-hero" style="background:{{ $gradBg }};">
    <div class="hero-top">
        <div class="hero-info">
            <h1 class="hero-name">{{ $prog->name_ar ?? $prog->name }}</h1>
            <p class="hero-desc">{{ $prog->description ?? 'برنامجك التدريبي الحالي' }}</p>
        </div>
        <div class="hero-term-box">
            <div class="hero-term-num">{{ $pdCurrentTermIndex }}<span style="font-size:1rem;opacity:.7;"> / {{ $pdStats['total_terms'] }}</span></div>
            <div class="hero-term-label">الفصل الحالي</div>
        </div>
    </div>
    <div class="hero-progress">
        <div class="hero-progress-labels">
            <span>التقدم في البرنامج</span>
            <span style="font-weight:800;">{{ $pdStats['progress_percentage'] ?? 0 }}%</span>
        </div>
        <div class="hero-progress-bar">
            <div class="hero-progress-fill" style="width:{{ $pdStats['progress_percentage'] ?? 0 }}%;"></div>
        </div>
    </div>
</div>

{{-- ── Program Info + Terms Timeline ── --}}
<div class="two-col">
    <div class="sec-card">
        <div class="sec-header">
            <div class="sec-title">
                <div class="sec-title-icon" style="background:linear-gradient(135deg,{{ $tabColor }},{{ $tabColor }}cc);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                معلومات البرنامج
            </div>
        </div>
        <div class="info-list">
            <div class="info-row"><span class="info-key">اسم البرنامج</span><span class="info-val">{{ $prog->name_ar ?? $prog->name }}</span></div>
            @if($prog->code)<div class="info-row"><span class="info-key">رمز البرنامج</span><span class="info-val">{{ $prog->code }}</span></div>@endif
            @if($prog->duration_months)<div class="info-row"><span class="info-key">مدة البرنامج</span><span class="info-val">{{ $prog->duration_months }} شهر</span></div>@endif
            <div class="info-row"><span class="info-key">عدد الفصول</span><span class="info-val">{{ $pdStats['total_terms'] ?? $pdTerms->count() }} فصول</span></div>
            <div class="info-row"><span class="info-key">إجمالي المقررات</span><span class="info-val">{{ $pdStats['total_subjects'] ?? $pdSubjects->count() }}</span></div>
            <div class="info-row"><span class="info-key">نسبة الحضور</span><span class="info-val">{{ $pdStats['attendance_rate'] ?? 0 }}%</span></div>
            <div class="info-row"><span class="info-key">الحالة</span><span class="info-badge">نشط</span></div>
        </div>
    </div>

    <div class="sec-card">
        <div class="sec-header">
            <div class="sec-title">
                <div class="sec-title-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                الفصول التدريبية
            </div>
        </div>
        <div class="timeline">
            @if($pdTerms->count() > 0)
                @foreach($pdTerms as $termLoop)
                @php
                    $isCT = $pdCurrentTerm && $termLoop->id === $pdCurrentTerm->id;
                    $isPT = !$isCT && (($termLoop->end_date && $termLoop->end_date < now()) ||
                            ($pdCurrentTerm && $pdTerms->search(fn($t)=>$t->id===$termLoop->id) < $pdTerms->search(fn($t)=>$t->id===$pdCurrentTerm->id)));
                    $state = $isPT ? 'past' : ($isCT ? 'current' : 'future');
                @endphp
                <div class="tl-item {{ $state }}">
                    <div class="tl-circle {{ $state }}">
                        @if($isPT)<svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        @else{{ $termLoop->term_number }}@endif
                    </div>
                    <div class="tl-body">
                        <div class="tl-row">
                            <div>
                                <div class="tl-name">{{ $termLoop->name_ar ?? $termLoop->name }}</div>
                                <div class="tl-meta">
                                    @if($termLoop->start_date && $termLoop->end_date)
                                    <span style="display:flex;align-items:center;gap:3px;">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $termLoop->start_date->format('Y/m/d') }} - {{ $termLoop->end_date->format('Y/m/d') }}
                                    </span>
                                    @endif
                                    <span>{{ $termLoop->subjects->count() }} مادة</span>
                                </div>
                            </div>
                            <span class="tl-badge {{ $state }}">
                                @if($isPT)<svg style="width:12px;height:12px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> مكتمل
                                @elseif($isCT)<span style="width:7px;height:7px;background:#fff;border-radius:50%;display:inline-block;animation:pulse 2s infinite;"></span> الحالي
                                @else قادم @endif
                            </span>
                        </div>
                        @if($isCT && $termLoop->subjects->count() > 0)
                        <div class="tl-subjects">
                            @foreach($termLoop->subjects as $s)<span class="tl-subj-chip">{{ $s->name_ar ?? $s->name }}</span>@endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <div style="text-align:center;padding:2rem;color:#94a3b8;">لا توجد فصول</div>
            @endif
        </div>
    </div>
</div>

{{-- ── Current Term Subjects ── --}}
<div class="sec-card">
    <div class="sec-header">
        <div class="sec-title">
            <div class="sec-title-icon" style="background:linear-gradient(135deg,{{ $tabColor }},{{ $tabColor }}cc);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            مقررات الفصل الحالي
            @if($pdCurrentTerm)
            <span style="background:#e0f2fe;color:#0369a1;font-size:.72rem;font-weight:700;padding:.2rem .65rem;border-radius:999px;margin-right:.25rem;">{{ $pdCurrentTerm->name_ar ?? $pdCurrentTerm->name }}</span>
            @endif
        </div>
        <a href="{{ route('student.my-sessions') }}" class="sec-link">جميع الجلسات <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
    </div>
    @if($pdCurrentTermSubj->count() > 0)
    <div class="subj-grid">
        @foreach($pdCurrentTermSubj as $subject)
        @php $color=$subject->color??$tabColor; $progress=$pdSubjProgress[$subject->id]??null; @endphp
        <div class="subj-card">
            <div class="subj-top" style="background:{{ $color }};"></div>
            <div class="subj-body">
                <div class="subj-name">{{ $subject->name_ar ?? $subject->name }}</div>
                <div class="subj-chips">
                    @if($subject->teacher)<span class="subj-chip"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>{{ $subject->teacher->name }}</span>@endif
                    <span class="subj-chip"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>{{ $subject->sessions->count() }} جلسة</span>
                </div>
                @if($progress)
                <div class="subj-progress">
                    <div class="subj-progress-header"><span class="subj-progress-label">نسبة الحضور</span><span class="subj-progress-pct">{{ $progress['percentage'] }}%</span></div>
                    <div class="subj-progress-track"><div class="subj-progress-fill" style="width:{{ $progress['percentage'] }}%;"></div></div>
                    <div class="subj-progress-text">{{ $progress['attended'] }} من {{ $progress['total'] }} جلسة</div>
                </div>
                @endif
                <a href="{{ route('student.subjects.show', $subject->id) }}" class="subj-btn"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>عرض التفاصيل</a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div style="text-align:center;padding:3rem 1rem;color:#94a3b8;">لا توجد مقررات في هذا الفصل</div>
    @endif
</div>

@else
{{-- ── NON-DIPLOMA panel ── --}}
<div style="background:{{ $gradBg }};border-radius:20px;padding:28px 32px;margin-bottom:24px;position:relative;overflow:hidden;">
    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <div>
            <h1 style="color:white;font-size:1.4rem;font-weight:900;margin:0 0 4px;">{{ $prog->name_ar ?? $prog->name }}</h1>
            <p style="color:rgba(255,255,255,.65);font-size:.85rem;margin:0;">{{ $prog->description ?? 'برنامجك التدريبي' }}</p>
            @if($prog->duration_hours)<span style="display:inline-flex;align-items:center;gap:5px;background:rgba(255,255,255,.12);border-radius:20px;padding:4px 12px;font-size:.78rem;color:rgba(255,255,255,.8);margin-top:8px;">{{ $prog->duration_hours }} ساعة تدريبية</span>@endif
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            @foreach([[$pdStats['total_sessions'],'إجمالي الجلسات','rgba(255,255,255,.75)'],[$pdStats['completed_sessions'],'مكتملة','#86efac'],[$pdStats['attendance_rate'].'%','نسبة الحضور','#fde68a']] as [$v,$l,$c])
            <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:8px 16px;text-align:center;min-width:70px;">
                <div style="font-size:1.3rem;font-weight:800;color:{{ $c }};line-height:1.1;">{{ $v }}</div>
                <div style="font-size:.7rem;color:rgba(255,255,255,.55);margin-top:2px;">{{ $l }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div style="background:white;border-radius:18px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.05);margin-bottom:20px;">
    <div style="padding:18px 22px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px;">
        <div style="width:36px;height:36px;background:linear-gradient(135deg,{{ $tabColor }},{{ $tabColor }}cc);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" fill="white" viewBox="0 0 24 24"><path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
        </div>
        <h2 style="font-size:1rem;font-weight:800;color:#111827;margin:0;">المحاضرات والجلسات</h2>
        <span style="background:{{ $tabColor }};color:white;font-size:.72rem;font-weight:700;padding:2px 9px;border-radius:20px;">{{ $pdSessions->count() }}</span>
    </div>
    @if($pdSessions->isEmpty())
    <div style="text-align:center;padding:48px 20px;color:#6b7280;">لا توجد جلسات مضافة بعد</div>
    @else
    <div>
        @foreach($pdSessions as $sess)
        @php $isComp=!is_null($sess->ended_at);$isLive=!is_null($sess->started_at)&&is_null($sess->ended_at); @endphp
        <div style="border-bottom:1px solid #f8fafc;padding:14px 22px;display:flex;align-items:center;justify-content:space-between;gap:12px;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
            <div style="display:flex;align-items:center;gap:12px;min-width:0;flex:1;">
                <div style="width:36px;height:36px;border-radius:10px;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:{{ $isLive?'linear-gradient(135deg,#22c55e,#16a34a)':($isComp?'#f0fdf4':'#f8fafc') }};">
                    @if($isLive)<svg width="16" height="16" fill="white" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/></svg>
                    @elseif($isComp)<svg width="16" height="16" fill="none" stroke="#16a34a" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    @else<span style="font-size:.78rem;font-weight:800;color:#9ca3af;">{{ $sess->session_number??'—' }}</span>@endif
                </div>
                <div style="min-width:0;">
                    <h4 style="font-size:.9rem;font-weight:700;color:#111827;margin:0;">{{ $sess->title_ar?:$sess->title_en??('جلسة #'.($sess->session_number??'—')) }}</h4>
                    <div style="display:flex;align-items:center;gap:10px;margin-top:3px;flex-wrap:wrap;">
                        @if($sess->scheduled_at)<span style="font-size:.72rem;color:#9ca3af;">{{ \Carbon\Carbon::parse($sess->scheduled_at)->format('Y/m/d · H:i') }}</span>@endif
                        @if($sess->files->count()>0)<span style="font-size:.72rem;color:#0071AA;background:#eff6ff;padding:1px 7px;border-radius:20px;">{{ $sess->files->count() }} ملف</span>@endif
                        @if($sess->homework)<span style="font-size:.72rem;color:#d97706;background:#fef3c7;padding:1px 7px;border-radius:20px;">واجب</span>@endif
                    </div>
                </div>
            </div>
            <a href="{{ route('student.sessions.show', $sess) }}" style="flex-shrink:0;display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:linear-gradient(135deg,{{ $tabColor }},{{ $tabColor }}cc);color:white;border-radius:9px;font-size:.78rem;font-weight:700;text-decoration:none;">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                عرض
            </a>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endif

</div>{{-- end panel --}}
@endforeach{{-- end programsData loop --}}

{{-- Tab switch JS --}}
<script>
function switchProgTab(id) {
    document.querySelectorAll('[id^="progpanel-"]').forEach(p => p.style.display = 'none');
    document.querySelectorAll('[id^="progtab-"]').forEach(btn => {
        btn.style.color = '#6b7280';
        btn.style.borderBottomColor = 'transparent';
        btn.style.background = 'transparent';
    });
    var panel = document.getElementById('progpanel-' + id);
    var tab   = document.getElementById('progtab-'  + id);
    if (panel) panel.style.display = 'block';
    if (tab) {
        var c = tab.dataset.color;
        tab.style.color = c;
        tab.style.borderBottomColor = c;
        tab.style.background = c + '12';
    }
}
// Activate first tab
@if($firstTabKey)switchProgTab({{ $firstTabKey }});@endif
</script>

@else
        {{-- ===== No Program - Enrollment Flow ===== --}}
        <div class="enroll-hero">
            <div class="enroll-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#fff;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h1 class="enroll-title">مرحبا بك في نظام التعلم!</h1>
            <p class="enroll-sub">اختر البرنامج التدريبي المناسب لك وابدأ رحلتك التعليمية</p>
        </div>

        @if(session('success'))
            <div class="alert-box alert-success">
                <svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert-box alert-error">
                <svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @php
            $diplomaPrograms = $diplomaPrograms ?? collect();
            $coursePrograms  = $coursePrograms  ?? collect();
            $englishPrograms = $englishPrograms ?? collect();

            // Pick default tab = first non-empty group
            $defaultTab = $diplomaPrograms->isNotEmpty() ? 'diploma'
                        : ($coursePrograms->isNotEmpty()  ? 'course'
                        : ($englishPrograms->isNotEmpty() ? 'english' : 'diploma'));
        @endphp

        @if($diplomaPrograms->isEmpty() && $coursePrograms->isEmpty() && $englishPrograms->isEmpty())
            <div class="sec-card">
                <div class="no-prog-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <h3 style="font-size:1.25rem;font-weight:800;color:#1e293b;">لا توجد برامج متاحة حاليا</h3>
                    <p style="font-size:0.88rem;color:#94a3b8;max-width:400px;margin:0.5rem auto 0;">يرجى التواصل مع الإدارة للحصول على المزيد من المعلومات</p>
                </div>
            </div>
        @else
        {{-- ── Tabbed programs card ── --}}
        <div class="sec-card" style="margin-bottom:1.5rem;">

            {{-- Tab bar --}}
            <div style="padding:1.25rem 1.5rem 0;border-bottom:1px solid #f1f5f9;display:flex;gap:4px;flex-wrap:wrap;">
                @if($diplomaPrograms->isNotEmpty())
                <button onclick="switchTab('diploma')" id="tab-diploma"
                        style="padding:10px 22px;border-radius:10px 10px 0 0;border:none;cursor:pointer;font-size:.88rem;font-weight:700;font-family:inherit;transition:all .2s;">
                    🎓 الدبلومات
                    <span style="background:#eff6ff;color:#1d4ed8;font-size:.7rem;font-weight:700;padding:1px 7px;border-radius:20px;margin-right:4px;">{{ $diplomaPrograms->count() }}</span>
                </button>
                @endif
                @if($coursePrograms->isNotEmpty())
                <button onclick="switchTab('course')" id="tab-course"
                        style="padding:10px 22px;border-radius:10px 10px 0 0;border:none;cursor:pointer;font-size:.88rem;font-weight:700;font-family:inherit;transition:all .2s;">
                    📋 الدورات
                    <span style="background:#f5f3ff;color:#5b21b6;font-size:.7rem;font-weight:700;padding:1px 7px;border-radius:20px;margin-right:4px;">{{ $coursePrograms->count() }}</span>
                </button>
                @endif
                @if($englishPrograms->isNotEmpty())
                <button onclick="switchTab('english')" id="tab-english"
                        style="padding:10px 22px;border-radius:10px 10px 0 0;border:none;cursor:pointer;font-size:.88rem;font-weight:700;font-family:inherit;transition:all .2s;">
                    🇬🇧 الإنجليزية
                    <span style="background:#ecfdf5;color:#065f46;font-size:.7rem;font-weight:700;padding:1px 7px;border-radius:20px;margin-right:4px;">{{ $englishPrograms->count() }}</span>
                </button>
                @endif
            </div>

            {{-- Tab panels --}}
            @php
                $tabDefs = [
                    ['diploma',  $diplomaPrograms,  'linear-gradient(135deg,#0071AA,#005a88)',  false],
                    ['course',   $coursePrograms,   'linear-gradient(135deg,#7c3aed,#5b21b6)',  true],
                    ['english',  $englishPrograms,  'linear-gradient(135deg,#059669,#047857)',  false],
                ];
            @endphp

            @foreach($tabDefs as [$tabKey, $group, $gradient, $showHours])
            <div id="panel-{{ $tabKey }}" style="padding:1.5rem;display:none;">
                <div class="prog-cards">
                    @foreach($group as $availableProgram)
                    <div class="prog-card">
                        <div class="prog-card-header" style="background:{{ $gradient }};">
                            <div class="prog-card-name">{{ $availableProgram->name }}</div>
                            @if($availableProgram->code)
                                <span class="prog-card-code">{{ $availableProgram->code }}</span>
                            @endif
                        </div>
                        <div class="prog-card-body">
                            @if($availableProgram->description)
                                <p class="prog-card-desc">{{ Str::limit($availableProgram->description, 120) }}</p>
                            @endif
                            <div class="prog-card-meta">
                                @if($showHours && $availableProgram->duration_hours)
                                    <div class="prog-card-meta-item">
                                        <div class="prog-card-meta-icon" style="background:#dbeafe;">
                                            <svg style="color:#3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        المدة: <strong>{{ $availableProgram->duration_hours }} ساعة</strong>
                                    </div>
                                @elseif(!$showHours && $availableProgram->duration_months)
                                    <div class="prog-card-meta-item">
                                        <div class="prog-card-meta-icon" style="background:#dbeafe;">
                                            <svg style="color:#3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        المدة: <strong>{{ $availableProgram->duration_months }} شهر</strong>
                                    </div>
                                @endif
                                @if($availableProgram->terms_count)
                                    <div class="prog-card-meta-item">
                                        <div class="prog-card-meta-icon" style="background:#d1fae5;">
                                            <svg style="color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        الفصول: <strong>{{ $availableProgram->terms_count }} فصل</strong>
                                    </div>
                                @endif
                                @if($availableProgram->price)
                                    <div class="prog-card-meta-item">
                                        <div class="prog-card-meta-icon" style="background:#fef3c7;">
                                            <svg style="color:#f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        السعر: <strong>{{ number_format($availableProgram->price, 2) }} <x-riyal /></strong>
                                    </div>
                                @endif
                            </div>
                            <form action="{{ route('student.enroll-program') }}" method="POST">
                                @csrf
                                <input type="hidden" name="program_id" value="{{ $availableProgram->id }}">
                                @if($availableProgram->price && $availableProgram->price > 0)
                                    <button type="submit" class="enroll-btn" style="background:{{ $gradient }};">
                                        التسجيل والدفع - {{ number_format($availableProgram->price, 2) }} <x-riyal />
                                    </button>
                                @else
                                    <button type="submit" class="enroll-btn" style="background:{{ $gradient }};">التسجيل في هذا البرنامج</button>
                                @endif
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

        </div>

        <script>
        const TAB_COLORS = {
            diploma: { active: '#0071AA', bg: '#eff6ff' },
            course:  { active: '#7c3aed', bg: '#f5f3ff' },
            english: { active: '#059669', bg: '#ecfdf5' },
        };
        function switchTab(key) {
            ['diploma','course','english'].forEach(k => {
                const btn   = document.getElementById('tab-' + k);
                const panel = document.getElementById('panel-' + k);
                if (!btn || !panel) return;
                const isActive = k === key;
                panel.style.display    = isActive ? 'block' : 'none';
                btn.style.background   = isActive ? TAB_COLORS[k].bg  : 'transparent';
                btn.style.color        = isActive ? TAB_COLORS[k].active : '#6b7280';
                btn.style.borderBottom = isActive ? ('2px solid ' + TAB_COLORS[k].active) : '2px solid transparent';
                btn.style.marginBottom = isActive ? '-1px' : '0';
            });
        }
        switchTab('{{ $defaultTab }}');
        </script>
        @endif

        {{-- Help Section --}}
        <div class="sec-card">
            <div class="sec-header">
                <div class="sec-title">
                    <div class="sec-title-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    هل تحتاج مساعدة؟
                </div>
            </div>
            <div class="help-grid">
                <div class="help-card">
                    <div class="help-icon" style="background:#dbeafe;">
                        <svg style="color:#3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <div>
                        <div class="help-title">تواصل مع الدعم</div>
                        <div class="help-desc">هل لديك استفسار أو تحتاج مساعدة في اختيار البرنامج المناسب؟</div>
                        <a href="{{ route('student.tickets.create') }}" class="help-link">إنشاء تذكرة دعم</a>
                    </div>
                </div>
                <div class="help-card">
                    <div class="help-icon" style="background:#fef3c7;">
                        <svg style="color:#f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <div class="help-title">معلومات مهمة</div>
                        <div class="help-desc">بعد التسجيل في البرنامج، ستتمكن من متابعة المقررات  والجلسات والحضور من لوحة التحكم.</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
