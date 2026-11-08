@extends('layouts.dashboard')

@section('title', 'ملف المدرب — ' . $teacher->name)

@php
    $isActive = $teacher->status === 'active';
    $initials = collect(preg_split('/\s+/u', trim($teacher->name)))->filter()->take(2)
        ->map(fn ($w) => mb_substr($w, 0, 1))->join('');

    $programTypeLabels = [
        'training' => 'البرامج التدريبية',
        'english'  => 'دورات اللغة الإنجليزية',
        'course'   => 'الدورات التأهيلية',
        'diploma'  => 'الدبلومات',
    ];
    $programTypeShort = ['training' => 'تدريبي', 'english' => 'إنجليزي', 'course' => 'تأهيلي', 'diploma' => 'دبلوم'];

    // Owned classes first, then the ones he only teaches in.
    $showcaseClasses = $classes->concat($relatedClasses);
    $classFilters = [
        'all'     => ['الكل',          $showcaseClasses->count()],
        'direct'  => ['مدرب المجموعة', $classes->count()],
        'related' => ['يدرّس فيها',    $relatedClasses->count()],
        'current' => ['حالية',         $showcaseClasses->where('is_past', false)->count()],
        'past'    => ['سابقة',         $showcaseClasses->where('is_past', true)->count()],
    ];
    $reachStudents   = $showcaseClasses->where('is_past', false)->sum('students_total');
    $sessionProgress = $sessionStats['total'] ? round($sessionStats['past'] / $sessionStats['total'] * 100) : 0;

    // Heroicons outline paths.
    $icons = [
        'home'     => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'book'     => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        'academic' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
        'users'    => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        'id'       => 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2',
        'link'     => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',
        'text'     => 'M4 6h16M4 12h16M4 18h7',
        'mail'     => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'phone'    => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
        'edit'     => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
        'login'    => 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1',
        'clock'    => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'chevron'  => 'M15 19l-7-7 7-7',
    ];
    $svg = fn ($name, $size = 18) => '<svg width="' . $size . '" height="' . $size . '" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="' . $icons[$name] . '"/></svg>';

    $tabs = [
        'overview' => ['نظرة عامة',       null,                      'home'],
        'classes'  => ['المجموعات',       $showcaseClasses->count(), 'users'],
        'subjects' => ['المقررات',        $subjects->count(),        'book'],
        'programs' => ['الدورات',         $courses->count(),         'academic'],
        'sessions' => ['الجلسات القادمة', $sessionStats['upcoming'], 'calendar'],
    ];
@endphp

@push('styles')
<style>
    .tp { --tp-blue:#0071AA; --tp-blue-dark:#005a88; --tp-blue-50:#e8f4fa; --tp-blue-100:#cfe7f3;
          --tp-ink:#0f2a3d; --tp-muted:#64748b; --tp-line:#e5edf2; --tp-card:#fff; --tp-soft:#f5f9fc;
          --tp-green:#059669; --tp-green-50:#ecfdf5; --tp-red:#dc2626; --tp-red-50:#fef2f2;
          --tp-amber:#d97706; --tp-amber-50:#fffbeb;
          max-width: 80rem; margin: 0 auto; color: var(--tp-ink); }
    .dark .tp { --tp-ink:#e6eef4; --tp-muted:#94a3b8; --tp-line:#24384a; --tp-card:#13222f; --tp-soft:#0f1c27;
                --tp-blue-50:rgba(0,113,170,.18); --tp-blue-100:rgba(0,113,170,.35);
                --tp-green-50:rgba(5,150,105,.15); --tp-red-50:rgba(220,38,38,.15); --tp-amber-50:rgba(217,119,6,.15); }

    .tp a { text-decoration: none; }
    .tp-crumb { display:flex; align-items:center; gap:.5rem; font-size:.85rem; color:var(--tp-muted); margin-bottom:1rem; }
    .tp-crumb a { color:var(--tp-blue); font-weight:600; }
    .tp-flash { display:flex; gap:.6rem; align-items:center; padding:.8rem 1rem; border-radius:12px; margin-bottom:1rem; font-weight:600; font-size:.9rem; }
    .tp-flash.ok  { background:var(--tp-green-50); color:var(--tp-green); border:1px solid rgba(5,150,105,.25); }
    .tp-flash.err { background:var(--tp-red-50); color:var(--tp-red); border:1px solid rgba(220,38,38,.25); display:block; }

    /* Hero — same gradient as the sidebar */
    .tp-hero { position:relative; overflow:hidden; border-radius:20px; padding:1.75rem; color:#fff;
               background:linear-gradient(135deg,#0071AA 0%,#005a88 100%); box-shadow:0 12px 30px -12px rgba(0,90,136,.55); }
    .tp-hero::before, .tp-hero::after { content:""; position:absolute; border-radius:50%; background:rgba(255,255,255,.07); pointer-events:none; }
    .tp-hero::before { width:280px; height:280px; top:-120px; left:-80px; }
    .tp-hero::after  { width:200px; height:200px; bottom:-110px; left:35%; }
    .tp-hero-top { position:relative; display:flex; flex-wrap:wrap; gap:1.25rem; align-items:center; justify-content:space-between; }
    .tp-id { display:flex; align-items:center; gap:1.1rem; min-width:0; }
    .tp-avatar { width:84px; height:84px; flex-shrink:0; border-radius:22px; object-fit:cover; border:4px solid rgba(255,255,255,.25);
                 display:flex; align-items:center; justify-content:center; font-size:1.8rem; font-weight:800; background:rgba(255,255,255,.18); }
    .tp-name { font-size:1.6rem; font-weight:800; margin:0; line-height:1.3; }
    .tp-sub { margin-top:.2rem; font-size:.88rem; color:rgba(255,255,255,.75); }
    .tp-badge { display:inline-flex; align-items:center; gap:.35rem; padding:.2rem .7rem; border-radius:999px; font-size:.75rem; font-weight:700; vertical-align:middle; margin-inline-start:.5rem; }
    .tp-badge i { width:7px; height:7px; border-radius:50%; background:currentColor; }
    .tp-badge.on  { background:rgba(16,185,129,.22); color:#a7f3d0; }
    .tp-badge.off { background:rgba(248,113,113,.22); color:#fecaca; }
    .tp-chips { display:flex; flex-wrap:wrap; gap:.5rem; margin-top:.8rem; }
    .tp-chip { display:inline-flex; align-items:center; gap:.4rem; padding:.35rem .75rem; border-radius:10px; font-size:.82rem;
               background:rgba(255,255,255,.14); color:#fff !important; border:1px solid rgba(255,255,255,.18); }
    .tp-chip:hover { background:rgba(255,255,255,.24); }
    .tp-actions { display:flex; flex-wrap:wrap; gap:.5rem; }
    .tp-actions form { margin:0; }
    .tp-btn { display:inline-flex; align-items:center; gap:.45rem; padding:.6rem 1rem; border-radius:11px; font-size:.85rem; font-weight:700;
              border:1px solid transparent; cursor:pointer; transition:.15s; }
    .tp-btn-white { background:#fff; color:var(--tp-blue-dark) !important; }
    .tp-btn-white:hover { background:#e8f4fa; }
    .tp-btn-glass { background:rgba(255,255,255,.14); color:#fff; border-color:rgba(255,255,255,.28); }
    .tp-btn-glass:hover { background:rgba(255,255,255,.24); }
    .tp-btn-danger { background:rgba(239,68,68,.9); color:#fff; }
    .tp-btn-danger:hover { background:#dc2626; }
    .tp-btn-success { background:#10b981; color:#fff; }

    .tp-stats { position:relative; display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:.75rem; margin-top:1.5rem; }
    .tp-stat { background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.16); border-radius:14px; padding:.9rem 1rem; text-align:right; color:#fff; cursor:pointer; transition:.15s; font:inherit; }
    .tp-stat:hover { background:rgba(255,255,255,.2); transform:translateY(-2px); }
    .tp-stat-label { display:flex; align-items:center; gap:.4rem; font-size:.8rem; color:rgba(255,255,255,.8); }
    .tp-stat-num { font-size:1.75rem; font-weight:800; line-height:1.2; margin-top:.25rem; }
    .tp-stat-hint { font-size:.72rem; color:rgba(255,255,255,.7); }

    /* Tabs */
    .tp-tabs { position:sticky; top:0; z-index:20; display:flex; gap:.35rem; overflow-x:auto; margin:1.25rem 0; padding:.4rem;
               background:var(--tp-card); border:1px solid var(--tp-line); border-radius:16px; box-shadow:0 4px 14px -8px rgba(0,90,136,.25); scrollbar-width:none; }
    .tp-tabs::-webkit-scrollbar { display:none; }
    .tp-tab { flex:1 0 auto; display:inline-flex; align-items:center; justify-content:center; gap:.5rem; padding:.7rem 1rem; border-radius:12px;
              border:0; background:transparent; color:var(--tp-muted); font-weight:700; font-size:.9rem; cursor:pointer; white-space:nowrap; transition:.15s; font-family:inherit; }
    .tp-tab:hover { background:var(--tp-blue-50); color:var(--tp-blue); }
    .tp-tab.active { background:linear-gradient(135deg,#0071AA 0%,#005a88 100%); color:#fff; box-shadow:0 6px 14px -6px rgba(0,90,136,.6); }
    .tp-tab .tp-count { min-width:1.5rem; padding:.05rem .45rem; border-radius:999px; font-size:.72rem; background:var(--tp-blue-50); color:var(--tp-blue); }
    .tp-tab.active .tp-count { background:rgba(255,255,255,.22); color:#fff; }

    /* Cards & layout */
    .tp-grid-2 { display:grid; grid-template-columns:minmax(0,2fr) minmax(0,1fr); gap:1.25rem; }
    .tp-card { background:var(--tp-card); border:1px solid var(--tp-line); border-radius:18px; padding:1.4rem; box-shadow:0 2px 10px -6px rgba(15,42,61,.12); }
    .tp-card + .tp-card { margin-top:1.25rem; }
    .tp-card-head { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:.75rem; margin-bottom:1rem; }
    .tp-title { display:flex; align-items:center; gap:.6rem; font-size:1.05rem; font-weight:800; margin:0; color:var(--tp-ink); }
    .tp-title-icon { width:36px; height:36px; border-radius:11px; display:flex; align-items:center; justify-content:center; flex-shrink:0;
                     background:linear-gradient(135deg,#0071AA 0%,#005a88 100%); color:#fff; }
    .tp-desc { font-size:.85rem; color:var(--tp-muted); margin:.2rem 0 0; }
    .tp-desc b { color:var(--tp-blue); }

    .tp-dl { margin:0; }
    .tp-dl > div { display:flex; justify-content:space-between; align-items:center; gap:1rem; padding:.7rem 0; border-bottom:1px dashed var(--tp-line); font-size:.88rem; }
    .tp-dl > div:last-child { border-bottom:0; }
    .tp-dl dt { color:var(--tp-muted); }
    .tp-dl dd { margin:0; font-weight:700; text-align:left; min-width:0; }
    .tp-pill { display:inline-flex; align-items:center; gap:.3rem; padding:.18rem .6rem; border-radius:999px; font-size:.73rem; font-weight:700; white-space:nowrap; }
    .tp-pill.blue  { background:var(--tp-blue-50); color:var(--tp-blue); }
    .tp-pill.green { background:var(--tp-green-50); color:var(--tp-green); }
    .tp-pill.red   { background:var(--tp-red-50); color:var(--tp-red); }
    .tp-pill.amber { background:var(--tp-amber-50); color:var(--tp-amber); }
    .tp-pill.gray  { background:var(--tp-soft); color:var(--tp-muted); border:1px solid var(--tp-line); }
    .tp-pill .dot  { width:7px; height:7px; border-radius:50%; background:currentColor; }
    .tp-link { color:var(--tp-blue) !important; font-weight:700; }
    .tp-link:hover { text-decoration:underline; }
    .tp-mini-btn { padding:.3rem .65rem; border-radius:8px; border:1px solid var(--tp-line); background:var(--tp-soft); color:var(--tp-ink); font-size:.75rem; font-weight:700; cursor:pointer; font-family:inherit; }
    .tp-note { padding:.6rem .8rem; border-radius:10px; font-size:.8rem; background:var(--tp-amber-50); color:var(--tp-amber); }

    .tp-btn-primary { background:linear-gradient(135deg,#0071AA 0%,#005a88 100%); color:#fff !important; }
    .tp-btn-primary:hover { filter:brightness(1.08); }
    .tp-btn-primary:disabled { opacity:.5; cursor:not-allowed; filter:none; }
    .tp-btn-outline { background:var(--tp-card); color:var(--tp-blue) !important; border-color:var(--tp-blue-100); }
    .tp-btn-outline:hover { background:var(--tp-blue-50); }

    /* Lists */
    .tp-row { display:flex; align-items:center; justify-content:space-between; gap:.8rem; padding:.85rem 1rem; margin-bottom:.55rem;
              border:1px solid var(--tp-line); border-right:4px solid var(--tp-blue); border-radius:12px; background:var(--tp-card); transition:.15s; color:inherit; }
    .tp-row:hover { background:var(--tp-blue-50); }
    .tp-row-main { display:flex; align-items:center; gap:.75rem; min-width:0; }
    .tp-row-icon { width:38px; height:38px; flex-shrink:0; border-radius:10px; display:flex; align-items:center; justify-content:center; background:var(--tp-blue-50); color:var(--tp-blue); }
    .tp-row-title { font-weight:700; font-size:.92rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .tp-row-sub { font-size:.78rem; color:var(--tp-muted); margin-top:.1rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .tp-mono { font-family:ui-monospace,monospace !important; color:var(--tp-blue); }
    .tp-empty { text-align:center; padding:2.5rem 1rem; border:2px dashed var(--tp-blue-100); border-radius:14px; color:var(--tp-muted); }
    .tp-empty-icon { width:56px; height:56px; margin:0 auto .75rem; border-radius:16px; display:flex; align-items:center; justify-content:center; background:var(--tp-blue-50); color:var(--tp-blue); }
    .tp-empty p { margin:0 0 .9rem; font-weight:600; }

    /* Filters */
    .tp-filters { display:flex; flex-wrap:wrap; gap:.3rem; padding:.3rem; border-radius:12px; background:var(--tp-soft); border:1px solid var(--tp-line); }
    .tp-filter { padding:.4rem .8rem; border-radius:9px; border:0; background:transparent; font-size:.78rem; font-weight:700; color:var(--tp-muted); cursor:pointer; font-family:inherit; }
    .tp-filter.active { background:var(--tp-blue); color:#fff; }
    .tp-filter span { opacity:.7; margin-inline-start:.2rem; }

    /* Class cards */
    .tp-class-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:1rem; }
    .tp-class { position:relative; display:flex; flex-direction:column; border-radius:16px; overflow:hidden; background:var(--tp-card);
                border:1px solid var(--tp-line); color:inherit; transition:.2s; }
    .tp-class:hover { transform:translateY(-4px); box-shadow:0 16px 30px -14px rgba(0,90,136,.45); border-color:var(--tp-blue-100); }
    .tp-class-band { padding:1rem 1.1rem; color:#fff; background:linear-gradient(135deg,#0071AA 0%,#005a88 100%); }
    .tp-class.past .tp-class-band { background:linear-gradient(135deg,#64748b 0%,#475569 100%); }
    .tp-class-band-top { display:flex; align-items:flex-start; justify-content:space-between; gap:.6rem; }
    .tp-class-name { font-weight:800; font-size:1rem; margin:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .tp-class-prog { font-size:.78rem; color:rgba(255,255,255,.78); margin-top:.15rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .tp-class-state { flex-shrink:0; display:inline-flex; align-items:center; gap:.3rem; padding:.15rem .55rem; border-radius:999px; font-size:.7rem; font-weight:700; background:rgba(255,255,255,.2); }
    .tp-class-state i { width:7px; height:7px; border-radius:50%; background:#6ee7b7; }
    .tp-class.past .tp-class-state i { background:#fca5a5; }
    .tp-class-body { padding:1rem 1.1rem; display:flex; flex-direction:column; flex:1; }
    .tp-tags { display:flex; flex-wrap:wrap; gap:.35rem; }
    .tp-tags .tp-pill { max-width:11rem; overflow:hidden; text-overflow:ellipsis; }
    .tp-nums { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:.5rem; margin:.9rem 0; text-align:center; }
    .tp-nums div { background:var(--tp-soft); border:1px solid var(--tp-line); border-radius:11px; padding:.55rem .25rem; }
    .tp-nums dt { font-size:.7rem; color:var(--tp-muted); }
    .tp-nums dd { margin:.1rem 0 0; font-size:1.15rem; font-weight:800; color:var(--tp-blue); }
    .tp-nums dd small { font-size:.7rem; color:var(--tp-muted); font-weight:600; }
    .tp-bar-label { display:flex; justify-content:space-between; font-size:.72rem; color:var(--tp-muted); margin:.45rem 0 .25rem; font-weight:600; }
    .tp-bar { height:8px; border-radius:999px; background:var(--tp-blue-50); overflow:hidden; }
    .tp-bar > span { display:block; height:100%; border-radius:999px; background:linear-gradient(90deg,#005a88,#0099d6); }
    .tp-bar.thin { height:6px; }
    .tp-bar.warn > span { background:linear-gradient(90deg,#d97706,#f59e0b); }
    .tp-class-foot { margin-top:auto; padding-top:.8rem; border-top:1px solid var(--tp-line); display:flex; align-items:center; justify-content:space-between; font-size:.78rem; color:var(--tp-muted); }
    .tp-class-foot .next { display:inline-flex; align-items:center; gap:.3rem; color:var(--tp-blue); font-weight:700; }
    .tp-spacer { flex:1; min-height:.8rem; }

    /* Timeline */
    .tp-timeline { position:relative; }
    .tp-tl-item { position:relative; display:flex; gap:.9rem; padding-bottom:1rem; }
    .tp-tl-item:not(:last-child)::before { content:""; position:absolute; right:23px; top:52px; bottom:0; width:2px; background:var(--tp-blue-100); }
    .tp-date { width:48px; height:52px; flex-shrink:0; border-radius:13px; display:flex; flex-direction:column; align-items:center; justify-content:center;
               background:linear-gradient(135deg,#0071AA 0%,#005a88 100%); color:#fff; box-shadow:0 6px 14px -8px rgba(0,90,136,.7); }
    .tp-date b { font-size:1.1rem; line-height:1; }
    .tp-date small { font-size:.68rem; margin-top:.2rem; opacity:.85; }
    .tp-tl-body { min-width:0; padding-top:.15rem; flex:1; }
    .tp-tl-when { font-size:.72rem; font-weight:700; color:var(--tp-blue); margin-top:.15rem; }

    /* Assign forms */
    .tp-search { position:relative; width:100%; max-width:22rem; }
    .tp-search svg { position:absolute; right:.75rem; top:50%; transform:translateY(-50%); color:var(--tp-muted); pointer-events:none; }
    .tp-search input { width:100%; padding:.6rem 2.3rem .6rem .8rem; border-radius:11px; border:1px solid var(--tp-line); background:var(--tp-card); color:var(--tp-ink); font-size:.88rem; font-family:inherit; }
    .tp-search input:focus { outline:none; border-color:var(--tp-blue); box-shadow:0 0 0 3px var(--tp-blue-50); }
    .tp-toolbar { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:.75rem; margin-bottom:.8rem; }
    .tp-picker { max-height:28rem; overflow-y:auto; border:1px solid var(--tp-line); border-radius:13px; }
    .tp-picker fieldset { border:0; margin:0; padding:0; min-width:0; }
    .tp-picker legend { position:sticky; top:0; z-index:1; width:100%; padding:.5rem 1rem; font-size:.75rem; font-weight:800; color:var(--tp-blue-dark); background:#eaf4fa; display:block; box-sizing:border-box; }
    .dark .tp-picker legend { background:#123247; color:#cfe7f3; }
    .tp-option { display:flex; align-items:center; gap:.75rem; padding:.65rem 1rem; border-bottom:1px solid var(--tp-line); cursor:pointer; font-size:.88rem; }
    .tp-option:hover { background:var(--tp-soft); }
    .tp-option.on { background:var(--tp-blue-50); }
    .tp-option input { width:17px; height:17px; accent-color:#0071AA; flex-shrink:0; }
    .tp-option-label { flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .tp-option-warn { display:block; font-size:.74rem; color:var(--tp-red); font-weight:600; }
    .tp-option-meta { font-size:.75rem; color:var(--tp-muted); }
    .tp-footer { display:flex; flex-wrap:wrap; align-items:center; gap:.5rem; margin-top:1rem; }
    .tp-footer p { font-size:.75rem; color:var(--tp-muted); margin:0; }

    @media (max-width: 1024px) {
        .tp-grid-2 { grid-template-columns:minmax(0,1fr); }
        .tp-class-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
    }
    @media (max-width: 640px) {
        .tp-stats { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .tp-class-grid { grid-template-columns:minmax(0,1fr); }
        .tp-hero { padding:1.25rem; }
        .tp-name { font-size:1.3rem; }
        .tp-tab { flex:0 0 auto; }
    }
</style>
@endpush

@section('content')
<div class="tp" x-data="teacherProfile({{ Js::from($showcaseClasses->map(fn ($c) => ['direct' => (bool) $c->is_direct, 'past' => (bool) $c->is_past])->values()) }})" x-init="init()">

    <nav class="tp-crumb" aria-label="مسار التنقل">
        <a href="{{ route('admin.teachers.index') }}">المدربون</a>
        {!! $svg('chevron', 14) !!}
        <span>{{ $teacher->name }}</span>
    </nav>

    @if(session('success'))
        <div role="status" class="tp-flash ok">✓ {{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div role="alert" class="tp-flash err">
            تعذّر حفظ التعديلات:
            <ul style="margin:.3rem 1.2rem 0;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- ─── Hero ─────────────────────────────────────────────── --}}
    <section class="tp-hero">
        <div class="tp-hero-top">
            <div class="tp-id">
                @if($teacher->profile_photo)
                    <img src="{{ asset('storage/' . $teacher->profile_photo) }}" alt="" class="tp-avatar">
                @else
                    <div class="tp-avatar" aria-hidden="true">{{ $initials }}</div>
                @endif
                <div style="min-width:0;">
                    <h1 class="tp-name">
                        {{ $teacher->name }}
                        <span class="tp-badge {{ $isActive ? 'on' : 'off' }}"><i></i>{{ $teacher->getStatusDisplayName() }}</span>
                    </h1>
                    <p class="tp-sub">
                        {{ $teacher->getRoleDisplayName() }}@if($teacher->specialization) · {{ $teacher->specialization }}@endif
                        · انضم {{ $teacher->created_at->diffForHumans() }}
                    </p>
                    <div class="tp-chips">
                        <a href="mailto:{{ $teacher->email }}" class="tp-chip" dir="ltr">{!! $svg('mail', 15) !!} {{ $teacher->email }}</a>
                        @if($teacher->phone)
                        <a href="tel:{{ $teacher->phone }}" class="tp-chip" dir="ltr">{!! $svg('phone', 15) !!} {{ $teacher->phone }}</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="tp-actions">
                <a href="{{ route('admin.teachers.edit', $teacher) }}" class="tp-btn tp-btn-white">{!! $svg('edit', 16) !!} تعديل البيانات</a>
                @if($isActive)
                <form action="{{ route('admin.teachers.dashboard-access', $teacher) }}" method="POST">
                    @csrf
                    <button type="submit" class="tp-btn tp-btn-glass">{!! $svg('login', 16) !!} دخول لوحة المدرّس</button>
                </form>
                @endif
                <form action="{{ route('admin.teachers.toggle-status', $teacher) }}" method="POST"
                      onsubmit="return confirm({{ Js::from($isActive ? 'تعطيل حساب ' . $teacher->name . '؟ لن يتمكن من الدخول حتى يُعاد تفعيله.' : 'تفعيل حساب ' . $teacher->name . '؟') }})">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="tp-btn {{ $isActive ? 'tp-btn-danger' : 'tp-btn-success' }}">{{ $isActive ? 'تعطيل الحساب' : 'تفعيل الحساب' }}</button>
                </form>
            </div>
        </div>

        <div class="tp-stats">
            <button type="button" class="tp-stat" @click="go('classes')">
                <span class="tp-stat-label">{!! $svg('users', 15) !!} المجموعات</span>
                <div class="tp-stat-num">{{ $showcaseClasses->count() }}</div>
                <span class="tp-stat-hint">{{ $classFilters['current'][1] }} حالية · {{ $classFilters['past'][1] }} سابقة</span>
            </button>
            <button type="button" class="tp-stat" @click="go('subjects')">
                <span class="tp-stat-label">{!! $svg('book', 15) !!} المقررات</span>
                <div class="tp-stat-num">{{ $subjects->count() }}</div>
                <span class="tp-stat-hint">مقررات مسندة</span>
            </button>
            <button type="button" class="tp-stat" @click="go('programs')">
                <span class="tp-stat-label">{!! $svg('academic', 15) !!} الدورات</span>
                <div class="tp-stat-num">{{ $courses->count() }}</div>
                <span class="tp-stat-hint">دورات وبرامج</span>
            </button>
            <button type="button" class="tp-stat" @click="go('sessions')">
                <span class="tp-stat-label">{!! $svg('calendar', 15) !!} الجلسات القادمة</span>
                <div class="tp-stat-num">{{ $sessionStats['upcoming'] }}</div>
                <span class="tp-stat-hint">من {{ $sessionStats['total'] }} · {{ $sessionStats['past'] }} منتهية</span>
            </button>
        </div>
    </section>

    {{-- ─── Tabs ─────────────────────────────────────────────── --}}
    <div class="tp-tabs" role="tablist" aria-label="أقسام ملف المدرب" id="tp-tabs">
        @foreach($tabs as $key => [$label, $count, $icon])
        <button type="button" role="tab" class="tp-tab" :class="tab === '{{ $key }}' && 'active'"
                :aria-selected="tab === '{{ $key }}'" @click="go('{{ $key }}', false)">
            {!! $svg($icon, 17) !!} {{ $label }}
            @if(!is_null($count))<span class="tp-count">{{ $count }}</span>@endif
        </button>
        @endforeach
    </div>

    {{-- ─── Overview ─────────────────────────────────────────── --}}
    <div x-show="tab === 'overview'" role="tabpanel" class="tp-grid-2">
        <div>
            <section class="tp-card">
                <div class="tp-card-head">
                    <div>
                        <h2 class="tp-title"><span class="tp-title-icon">{!! $svg('users', 18) !!}</span> أحدث المجموعات</h2>
                        <p class="tp-desc">@if($reachStudents)<b>{{ $reachStudents }}</b> طالب في المجموعات الحالية @else المجموعات المرتبطة بالمدرب @endif</p>
                    </div>
                    @if($showcaseClasses->count())
                    <button type="button" class="tp-btn tp-btn-outline" @click="go('classes', false)">عرض الكل ({{ $showcaseClasses->count() }})</button>
                    @endif
                </div>
                @forelse($showcaseClasses->where('is_past', false)->take(4) as $class)
                    <a href="{{ route('admin.classes.show', $class) }}" class="tp-row">
                        <span class="tp-row-main">
                            <span class="tp-row-icon">{!! $svg('users', 17) !!}</span>
                            <span style="min-width:0;">
                                <div class="tp-row-title">{{ $class->name }}</div>
                                <div class="tp-row-sub">{{ $class->program->name_ar ?? 'بدون برنامج' }} · {{ $class->students_total }} طالب · {{ $class->progress }}% منجز</div>
                            </span>
                        </span>
                        <span class="tp-pill {{ $class->is_direct ? 'blue' : 'amber' }}">{{ $class->is_direct ? 'مدرب المجموعة' : 'يدرّس فيها' }}</span>
                    </a>
                @empty
                    <div class="tp-empty"><p>لا توجد مجموعات حالية</p></div>
                @endforelse
            </section>

            <section class="tp-card">
                <div class="tp-card-head">
                    <h2 class="tp-title"><span class="tp-title-icon">{!! $svg('calendar', 18) !!}</span> الجلسات القادمة</h2>
                    <span class="tp-pill blue">{{ $sessionProgress }}% من الجلسات منتهية</span>
                </div>
                <div class="tp-bar" style="margin-bottom:1rem;"><span style="width: {{ $sessionProgress }}%"></span></div>
                @include('admin.teachers.partials.sessions-timeline', ['items' => $upcomingSessions->take(3)])
                @if($upcomingSessions->count() > 3)
                <button type="button" class="tp-btn tp-btn-outline" @click="go('sessions', false)">كل الجلسات القادمة</button>
                @endif
            </section>
        </div>

        <aside>
            <section class="tp-card">
                <h2 class="tp-title" style="margin-bottom:.6rem;"><span class="tp-title-icon">{!! $svg('id', 18) !!}</span> البيانات الشخصية</h2>
                <dl class="tp-dl">
                    @foreach(array_filter([
                        'رقم الهوية'     => $teacher->national_id,
                        'الجنس'          => $teacher->gender ? ($teacher->gender === 'male' ? 'ذكر' : 'أنثى') : null,
                        'الجنسية'        => $teacher->nationality,
                        'تاريخ الميلاد'  => $teacher->date_of_birth?->format('Y-m-d'),
                        'التخصص'         => $teacher->specialization,
                        'تاريخ التسجيل' => $teacher->created_at->format('Y-m-d'),
                    ]) as $label => $value)
                    <div>
                        <dt>{{ $label }}</dt>
                        <dd @if($label === 'رقم الهوية' || str_contains($label, 'تاريخ')) dir="ltr" @endif>{{ $value }}</dd>
                    </div>
                    @endforeach
                </dl>
            </section>

            <section class="tp-card">
                <h2 class="tp-title" style="margin-bottom:.6rem;"><span class="tp-title-icon">{!! $svg('link', 18) !!}</span> الحساب والروابط</h2>
                <dl class="tp-dl">
                    @foreach(['البريد الإلكتروني' => $teacher->email_verified_at, 'رقم الجوال' => $teacher->phone_verified_at] as $label => $verifiedAt)
                    <div>
                        <dt>{{ $label }}</dt>
                        <dd><span class="tp-pill {{ $verifiedAt ? 'green' : 'gray' }}">{{ $verifiedAt ? 'موثّق' : 'غير موثّق' }}</span></dd>
                    </div>
                    @endforeach
                    <div style="display:block;">
                        <dt style="margin-bottom:.4rem;">رابط زووم</dt>
                        <dd style="text-align:right;">
                            @if($teacher->zoom_join_url)
                            <div style="display:flex; align-items:center; gap:.5rem;">
                                <a href="{{ $teacher->zoom_join_url }}" target="_blank" rel="noopener noreferrer" dir="ltr" class="tp-link"
                                   style="flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="{{ $teacher->zoom_join_url }}">{{ $teacher->zoom_join_url }}</a>
                                <button type="button" class="tp-mini-btn" @click="copy({{ Js::from($teacher->zoom_join_url) }}, $event)">نسخ</button>
                            </div>
                            @else
                            <div class="tp-note">لم يُضبط — لن تظهر روابط زووم لجلسات هذا المدرب</div>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt>الشهادة</dt>
                        <dd>
                            @if($teacher->certificate_url)
                                <a href="{{ $teacher->certificate_url }}" target="_blank" rel="noopener noreferrer" class="tp-link">عرض الشهادة</a>
                            @else
                                <span class="tp-pill gray">غير مرفقة</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>

            @if($teacher->bio)
            <section class="tp-card">
                <h2 class="tp-title" style="margin-bottom:.6rem;"><span class="tp-title-icon">{!! $svg('text', 18) !!}</span> نبذة</h2>
                <p style="white-space:pre-line; font-size:.88rem; line-height:1.8; color:var(--tp-muted); margin:0;">{{ $teacher->bio }}</p>
            </section>
            @endif
        </aside>
    </div>

    {{-- ─── Classes ──────────────────────────────────────────── --}}
    <section x-show="tab === 'classes'" x-cloak role="tabpanel" class="tp-card">
        <div x-show="editing !== 'classes'">
            <div class="tp-card-head">
                <div>
                    <h2 class="tp-title"><span class="tp-title-icon">{!! $svg('users', 18) !!}</span> مجموعات المدرب</h2>
                    <p class="tp-desc">المجموعات التي يتولاها والمجموعات التي يدرّس فيها جلسات أو مقررات@if($reachStudents) · <b>{{ $reachStudents }} طالب</b> في الحالية@endif</p>
                </div>
                <button type="button" class="tp-btn tp-btn-primary" @click="editing = 'classes'">تعيين مجموعات</button>
            </div>

            @if($showcaseClasses->count())
                <div class="tp-filters" role="group" aria-label="تصفية المجموعات" style="margin-bottom:1rem; width:max-content; max-width:100%;">
                    @foreach($classFilters as $key => [$label, $count])
                    <button type="button" class="tp-filter" :class="classFilter === '{{ $key }}' && 'active'"
                            :aria-pressed="classFilter === '{{ $key }}'" @click="classFilter = '{{ $key }}'">{{ $label }}<span>{{ $count }}</span></button>
                    @endforeach
                </div>
                <div class="tp-class-grid">
                    @foreach($showcaseClasses as $class)
                        @include('admin.teachers.partials.class-card', ['class' => $class])
                    @endforeach
                </div>
                <div x-show="!hasVisibleClasses()" x-cloak class="tp-empty"><p style="margin:0;">لا توجد مجموعات ضمن هذا التصنيف</p></div>
            @else
                @include('admin.teachers.partials.empty-assignment', ['text' => 'لا توجد مجموعات مرتبطة بهذا المدرب', 'action' => 'classes', 'cta' => 'تعيين مجموعات', 'icon' => $svg('users', 26)])
            @endif
        </div>

        <form x-show="editing === 'classes'" x-cloak method="POST" action="{{ route('admin.teachers.assign-classes', $teacher) }}"
              x-data="assignPanel({{ $assignedClassIds->toJson() }})" @submit="onSubmit($event, 'مجموعة', 'classes')">
            @csrf
            <h2 class="tp-title" style="margin-bottom:.8rem;"><span class="tp-title-icon">{!! $svg('users', 18) !!}</span> تعيين المجموعات</h2>
            <p class="tp-note" style="margin:0 0 .8rem;">لكل مجموعة مدرب واحد فقط — تعيين مجموعة مسندة لمدرب آخر سينقلها إلى هذا المدرب.</p>
            @include('admin.teachers.partials.assign-toolbar', ['placeholder' => 'ابحث باسم المجموعة…'])
            <div class="tp-picker">
                @forelse($allClasses as $programName => $groupClasses)
                <fieldset data-search="{{ $programName }} {{ $groupClasses->pluck('name')->implode(' ') }}" x-show="matches($el.dataset.search)">
                    <legend>{{ $programName }}</legend>
                    @foreach($groupClasses as $class)
                        @php $heldBy = $class->teacher_id && $class->teacher_id !== $teacher->id ? $class->teacher : null; @endphp
                        @include('admin.teachers.partials.assign-option', [
                            'name' => 'class_ids[]', 'id' => $class->id, 'label' => $class->name,
                            'meta' => null, 'search' => $programName . ' ' . $class->name,
                            'warning' => $heldBy ? 'مسندة حالياً إلى ' . $heldBy->name : null,
                        ])
                    @endforeach
                </fieldset>
                @empty
                <div class="tp-empty" style="border:0;"><p style="margin:0;">لا توجد مجموعات</p></div>
                @endforelse
            </div>
            @include('admin.teachers.partials.assign-footer', ['save' => 'حفظ المجموعات'])
        </form>
    </section>

    {{-- ─── Subjects ─────────────────────────────────────────── --}}
    <section x-show="tab === 'subjects'" x-cloak role="tabpanel" class="tp-card">
        <div x-show="editing !== 'subjects'">
            <div class="tp-card-head">
                <div>
                    <h2 class="tp-title"><span class="tp-title-icon">{!! $svg('book', 18) !!}</span> المقررات</h2>
                    <p class="tp-desc">المقررات التي يدرّسها المدرب</p>
                </div>
                <button type="button" class="tp-btn tp-btn-primary" @click="editing = 'subjects'">تعديل المقررات</button>
            </div>
            @forelse($subjects as $subject)
                @php $prog = $subject->program ?? $subject->term?->program; @endphp
                <div class="tp-row">
                    <span class="tp-row-main">
                        <span class="tp-row-icon">{!! $svg('book', 17) !!}</span>
                        <span style="min-width:0;">
                            <div class="tp-row-title">{{ $subject->name_ar ?? $subject->name ?? '—' }}</div>
                            <div class="tp-row-sub">{{ $prog->name_ar ?? 'غير مرتبط ببرنامج' }}@if($subject->code) · <span dir="ltr" class="tp-mono">{{ $subject->code }}</span>@endif</div>
                        </span>
                    </span>
                    @if($prog)<span class="tp-pill blue">{{ $programTypeShort[$prog->type] ?? $prog->type }}</span>@endif
                </div>
            @empty
                @include('admin.teachers.partials.empty-assignment', ['text' => 'لم يُعيَّن أي مقرر لهذا المدرب بعد', 'action' => 'subjects', 'cta' => 'تعيين مقررات', 'icon' => $svg('book', 26)])
            @endforelse
        </div>

        <form x-show="editing === 'subjects'" x-cloak method="POST" action="{{ route('admin.teachers.assign-subjects', $teacher) }}"
              x-data="assignPanel({{ $assignedSubjectIds->toJson() }})" @submit="onSubmit($event, 'مقرر', 'subjects')">
            @csrf
            <h2 class="tp-title" style="margin-bottom:.8rem;"><span class="tp-title-icon">{!! $svg('book', 18) !!}</span> تعيين المقررات</h2>
            @include('admin.teachers.partials.assign-toolbar', ['placeholder' => 'ابحث باسم المقرر أو رمزه…'])
            <div class="tp-picker">
                @foreach($programsByType as $type => $programs)
                    @foreach($programs as $prog)
                        @php $progSubjects = $prog->terms->flatMap->subjects->merge($prog->subjects)->unique('id')->sortBy('name_ar'); @endphp
                        @if($progSubjects->count())
                        <fieldset data-search="{{ $prog->name_ar }} {{ $progSubjects->map(fn ($s) => $s->name_ar . ' ' . $s->code . ' ' . $s->name_en)->implode(' ') }}"
                                  x-show="matches($el.dataset.search)">
                            <legend>{{ $prog->name_ar }} · {{ $programTypeShort[$type] ?? $type }}</legend>
                            @foreach($progSubjects as $subj)
                                @include('admin.teachers.partials.assign-option', [
                                    'name' => 'subjects[]', 'id' => $subj->id, 'label' => $subj->name_ar,
                                    'meta' => $subj->code, 'search' => $subj->name_ar . ' ' . $subj->code . ' ' . $subj->name_en,
                                ])
                            @endforeach
                        </fieldset>
                        @endif
                    @endforeach
                @endforeach

                @if($orphanSubjects->count())
                <fieldset data-search="{{ $orphanSubjects->map(fn ($s) => $s->name_ar . ' ' . $s->code)->implode(' ') }}" x-show="matches($el.dataset.search)">
                    <legend>مقررات غير مرتبطة ببرنامج</legend>
                    @foreach($orphanSubjects as $subj)
                        @include('admin.teachers.partials.assign-option', [
                            'name' => 'subjects[]', 'id' => $subj->id, 'label' => $subj->name_ar,
                            'meta' => $subj->code, 'search' => $subj->name_ar . ' ' . $subj->code,
                        ])
                    @endforeach
                </fieldset>
                @endif
            </div>
            @include('admin.teachers.partials.assign-footer', ['save' => 'حفظ المقررات'])
        </form>
    </section>

    {{-- ─── Programs / courses ───────────────────────────────── --}}
    <section x-show="tab === 'programs'" x-cloak role="tabpanel" class="tp-card">
        <div x-show="editing !== 'programs'">
            <div class="tp-card-head">
                <div>
                    <h2 class="tp-title"><span class="tp-title-icon">{!! $svg('academic', 18) !!}</span> الدورات</h2>
                    <p class="tp-desc">الدورات والبرامج المسندة للمدرب</p>
                </div>
                <button type="button" class="tp-btn tp-btn-primary" @click="editing = 'programs'">تعديل الدورات</button>
            </div>
            @forelse($courses as $program)
                <div class="tp-row">
                    <span class="tp-row-main">
                        <span class="tp-row-icon">{!! $svg('academic', 17) !!}</span>
                        <div class="tp-row-title">{{ $program->name_ar }}</div>
                    </span>
                    <span class="tp-pill blue">{{ $programTypeShort[$program->type] ?? $program->type }}</span>
                </div>
            @empty
                @include('admin.teachers.partials.empty-assignment', ['text' => 'لا توجد دورات مسندة لهذا المدرب', 'action' => 'programs', 'cta' => 'تعيين دورات', 'icon' => $svg('academic', 26)])
            @endforelse
        </div>

        <form x-show="editing === 'programs'" x-cloak method="POST" action="{{ route('admin.teachers.assign-programs', $teacher) }}"
              x-data="assignPanel({{ $assignedProgramIds->toJson() }})" @submit="onSubmit($event, 'دورة', 'programs')">
            @csrf
            <h2 class="tp-title" style="margin-bottom:.8rem;"><span class="tp-title-icon">{!! $svg('academic', 18) !!}</span> تعيين الدورات</h2>
            @include('admin.teachers.partials.assign-toolbar', ['placeholder' => 'ابحث باسم الدورة…'])
            <div class="tp-picker">
                @forelse($allPrograms as $type => $programs)
                <fieldset data-search="{{ $programs->pluck('name_ar')->implode(' ') }}" x-show="matches($el.dataset.search)">
                    <legend>{{ $programTypeLabels[$type] ?? $type }}</legend>
                    @foreach($programs as $prog)
                        @include('admin.teachers.partials.assign-option', [
                            'name' => 'program_ids[]', 'id' => $prog->id, 'label' => $prog->name_ar,
                            'meta' => $prog->status === 'active' ? 'نشط' : null, 'search' => $prog->name_ar,
                        ])
                    @endforeach
                </fieldset>
                @empty
                <div class="tp-empty" style="border:0;"><p style="margin:0;">لا توجد دورات</p></div>
                @endforelse
            </div>
            @include('admin.teachers.partials.assign-footer', ['save' => 'حفظ الدورات'])
        </form>
    </section>

    {{-- ─── Upcoming sessions ────────────────────────────────── --}}
    <section x-show="tab === 'sessions'" x-cloak role="tabpanel" class="tp-card">
        <div class="tp-card-head">
            <div>
                <h2 class="tp-title"><span class="tp-title-icon">{!! $svg('calendar', 18) !!}</span> الجلسات القادمة</h2>
                <p class="tp-desc"><b>{{ $sessionStats['upcoming'] }}</b> قادمة من أصل {{ $sessionStats['total'] }} · {{ $sessionStats['past'] }} منتهية</p>
            </div>
            <span class="tp-pill blue">{{ $sessionProgress }}% منتهية</span>
        </div>
        <div class="tp-bar" style="margin-bottom:1.2rem;"><span style="width: {{ $sessionProgress }}%"></span></div>
        @include('admin.teachers.partials.sessions-timeline', ['items' => $upcomingSessions])
        @if($sessionStats['upcoming'] > $upcomingSessions->count())
            <p class="tp-desc" style="margin-top:.5rem;">يتم عرض أقرب {{ $upcomingSessions->count() }} جلسات فقط.</p>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
// Page state: active tab, which tab is in edit mode, and the class filter.
// The tab survives the redirect after saving and is mirrored in the URL hash.
function teacherProfile(classMeta) {
    const TABS = ['overview', 'classes', 'subjects', 'programs', 'sessions'];
    return {
        tab: 'overview',
        editing: null,
        classFilter: 'all',
        classMeta: classMeta || [],

        init() {
            try {
                const saved = sessionStorage.getItem('teacher-profile-tab');
                if (saved && TABS.includes(saved)) { this.tab = saved; sessionStorage.removeItem('teacher-profile-tab'); }
            } catch (e) {}
            const hash = location.hash.slice(1);
            if (TABS.includes(hash)) this.tab = hash;
        },

        go(key, scroll = true) {
            this.tab = key;
            this.editing = null;
            try { history.replaceState(null, '', '#' + key); } catch (e) {}
            if (scroll) document.getElementById('tp-tabs')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },

        classVisible(c) {
            switch (this.classFilter) {
                case 'direct':  return c.direct;
                case 'related': return !c.direct;
                case 'current': return !c.past;
                case 'past':    return c.past;
                default:        return true;
            }
        },

        hasVisibleClasses() { return this.classMeta.some(c => this.classVisible(c)); },

        copy(text, event) {
            const btn = event.currentTarget, original = btn.textContent;
            const done = ok => { btn.textContent = ok ? 'تم ✓' : 'تعذّر'; setTimeout(() => btn.textContent = original, 1500); };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => done(true), () => done(false));
            } else {
                const t = Object.assign(document.createElement('textarea'), { value: text });
                t.style.cssText = 'position:fixed;opacity:0';
                document.body.appendChild(t); t.select();
                try { done(document.execCommand('copy')); } catch (e) { done(false); }
                t.remove();
            }
        },
    };
}

// One assign form: live search, a running "+added / −removed" summary, and a
// confirm before anything is unassigned.
function assignPanel(initialIds) {
    return {
        initial: initialIds.map(Number),
        selected: initialIds.map(Number),
        query: '',
        submitting: false,

        // أ/إ/آ ≡ ا, ة ≡ ه, ى ≡ ي, no diacritics, Arabic-Indic digits → ASCII.
        normalize(value) {
            return (value || '').toLowerCase()
                .replace(/[ـً-ْ]/g, '')
                .replace(/[آأإٱ]/g, 'ا')
                .replace(/ى/g, 'ي')
                .replace(/ة/g, 'ه')
                .replace(/[٠-٩]/g, d => String.fromCharCode(d.charCodeAt(0) - 0x0630))
                .replace(/\s+/g, ' ')
                .trim();
        },

        matches(haystack) {
            const q = this.normalize(this.query);
            if (!q) return true;
            const hay = this.normalize(haystack);
            return q.split(' ').every(term => hay.includes(term));
        },

        isChecked(id) { return this.selected.includes(id); },

        toggle(id, checked) {
            this.selected = checked
                ? [...new Set([...this.selected, id])]
                : this.selected.filter(x => x !== id);
        },

        get added()   { return this.selected.filter(id => !this.initial.includes(id)).length; },
        get removed() { return this.initial.filter(id => !this.selected.includes(id)).length; },
        get dirty()   { return this.added > 0 || this.removed > 0; },

        reset() { this.selected = [...this.initial]; this.query = ''; },

        // Returns whether to leave edit mode; the caller clears the parent's
        // `editing` so this component never reaches into outer state.
        cancel() {
            if (this.dirty && !confirm('تجاهل التغييرات غير المحفوظة؟')) return false;
            this.reset();
            return true;
        },

        onSubmit(event, noun, tabKey) {
            if (this.removed && !confirm('سيتم إلغاء تعيين ' + this.removed + ' ' + noun + ' من هذا المدرب. متابعة؟')) {
                event.preventDefault();
                return;
            }
            try { sessionStorage.setItem('teacher-profile-tab', tabKey); } catch (e) {}
            this.submitting = true;
        },
    };
}
</script>
@endpush
