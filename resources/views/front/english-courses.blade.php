@extends('layouts.front')

@section('title', 'برامج اللغة الإنجليزية - معهد الارتقاء العالي للتدريب')

@section('styles')
<style>
/* ── Hero ── */
.eng-hero {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #0ea5e9 100%);
    padding: 3.5rem clamp(1.5rem, 5vw, 5rem);
    color: #fff;
    position: relative;
    overflow: hidden;
}
.eng-hero::before {
    content: '';
    position: absolute;
    top: -60px; left: -60px;
    width: 260px; height: 260px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
}
.eng-hero::after {
    content: '';
    position: absolute;
    bottom: -80px; right: -80px;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
}
.eng-hero .inner { position: relative; z-index: 1; }
.eng-hero .badge-label {
    display: inline-flex; align-items: center; gap: .5rem;
    background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25);
    border-radius: 30px; padding: .35rem 1rem;
    font-size: .8rem; font-weight: 600; margin-bottom: 1rem;
    backdrop-filter: blur(6px);
}
.eng-hero h1 { font-size: clamp(1.6rem,3vw,2.4rem); font-weight: 800; margin-bottom: .75rem; }
.eng-hero p  { font-size: 1rem; opacity: .85; max-width: 560px; line-height: 1.8; margin-bottom: 0; }
.eng-hero .hero-stats {
    display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 1.75rem;
}
.eng-hero .stat-pill {
    background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.2);
    border-radius: 12px; padding: .6rem 1.2rem; text-align: center;
    backdrop-filter: blur(6px); min-width: 90px;
}
.eng-hero .stat-pill strong { display: block; font-size: 1.5rem; font-weight: 800; }
.eng-hero .stat-pill span  { font-size: .75rem; opacity: .85; }

/* ── Level Track ── */
.level-track {
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    padding: 1.25rem clamp(1.5rem,5vw,5rem);
    overflow-x: auto;
}
.level-track-inner {
    display: flex; align-items: center; gap: 0;
    min-width: max-content;
}
.track-step {
    display: flex; flex-direction: column; align-items: center; gap: .4rem;
    cursor: pointer;
}
.track-step .circle {
    width: 38px; height: 38px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: .72rem; font-weight: 700;
    border: 2px solid #d1d5db; color: #9ca3af;
    background: #f9fafb; transition: all .25s;
}
.track-step .circle.active {
    background: #1d4ed8; border-color: #1d4ed8; color: #fff;
    box-shadow: 0 4px 12px rgba(29,78,216,.35);
}
.track-step .lbl {
    font-size: .65rem; font-weight: 600; color: #9ca3af;
    white-space: nowrap; transition: color .25s;
}
.track-step.active-step .circle { background: #1d4ed8; border-color: #1d4ed8; color: #fff; box-shadow: 0 4px 12px rgba(29,78,216,.35); }
.track-step.active-step .lbl   { color: #1d4ed8; }
.track-connector {
    width: 20px; height: 2px; background: #e5e7eb; margin-bottom: 18px; flex-shrink: 0;
}

/* ── Tabs ── */
.eng-tabs-wrap {
    padding: 1.75rem clamp(1.5rem,5vw,5rem) 0;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}
.eng-tabs {
    display: flex; gap: .25rem; flex-wrap: wrap;
}
.eng-tab {
    padding: .6rem 1.25rem; border-radius: 8px 8px 0 0;
    font-size: .875rem; font-weight: 600; cursor: pointer;
    border: none; background: transparent;
    color: #6b7280; transition: all .2s;
    border-bottom: 3px solid transparent;
}
.eng-tab:hover  { background: #eef2ff; color: #1d4ed8; }
.eng-tab.active { background: #fff; color: #1d4ed8; border-bottom-color: #1d4ed8; box-shadow: 0 -2px 8px rgba(0,0,0,.05); }
.eng-tab .tab-count {
    display: inline-flex; align-items: center; justify-content: center;
    width: 20px; height: 20px; border-radius: 50%;
    font-size: .7rem; font-weight: 700; margin-right: .4rem;
    background: #e0e7ff; color: #1d4ed8;
}
.eng-tab.active .tab-count { background: #1d4ed8; color: #fff; }

/* ── Cards Grid ── */
.eng-cards-section {
    padding: 2rem clamp(1.5rem,5vw,5rem);
    background: #f8fafc;
    min-height: 350px;
}
.eng-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(280px,1fr)); gap: 1.25rem; }

.eng-card {
    background: #fff; border-radius: 16px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    overflow: hidden; display: flex; flex-direction: column;
    transition: transform .25s, box-shadow .25s;
}
.eng-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(29,78,216,.12); }

.eng-card-top {
    height: 6px; background: linear-gradient(90deg,#1e3a8a,#0ea5e9);
}
.eng-card-body { padding: 1.25rem 1.25rem .75rem; flex: 1; }

.level-badge {
    display: inline-flex; flex-direction: column; align-items: center; justify-content: center;
    width: 52px; height: 52px; border-radius: 14px;
    background: linear-gradient(135deg,#1e3a8a,#1d4ed8);
    color: #fff; font-weight: 800; margin-bottom: .85rem;
    box-shadow: 0 4px 12px rgba(29,78,216,.3);
}
.level-badge .lvl-num  { font-size: 1.2rem; line-height: 1; }
.level-badge .lvl-word { font-size: .52rem; opacity: .8; line-height: 1; }

.eng-card-body h3 {
    font-size: .975rem; font-weight: 700; color: #1a2540; margin-bottom: .25rem;
}
.eng-card-body .en-name {
    font-size: .78rem; color: #9ca3af; margin-bottom: 1rem; direction: ltr; text-align: left;
}
.eng-card-meta { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 0; }
.meta-chip {
    display: inline-flex; align-items: center; gap: .35rem;
    background: #f1f5f9; border-radius: 8px; padding: .3rem .65rem;
    font-size: .75rem; font-weight: 600; color: #475569;
}
.meta-chip i { color: #1d4ed8; font-size: .8rem; }

.eng-card-footer {
    padding: .85rem 1.25rem;
    border-top: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
}
.eng-card-footer .status-dot {
    display: flex; align-items: center; gap: .4rem;
    font-size: .75rem; font-weight: 600;
}
.status-dot .dot { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; animation: pls 2s infinite; }
@keyframes pls { 0%,100%{opacity:1} 50%{opacity:.4} }
.btn-enroll {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .45rem 1rem; border-radius: 8px;
    background: linear-gradient(135deg,#1d4ed8,#0ea5e9);
    color: #fff; font-size: .8rem; font-weight: 700;
    border: none; cursor: pointer; text-decoration: none;
    transition: box-shadow .2s, transform .2s;
}
.btn-enroll:hover { box-shadow: 0 4px 14px rgba(29,78,216,.35); transform: translateY(-1px); color: #fff; }

/* hidden tab content */
.tab-panel { display: none; }
.tab-panel.active { display: block; }

/* empty state */
.empty-state { text-align: center; padding: 4rem 1rem; }
.empty-state i { font-size: 3rem; color: #d1d5db; margin-bottom: 1rem; display: block; }

@media(max-width:576px) {
    .eng-hero .hero-stats { gap: .75rem; }
    .eng-grid { grid-template-columns: 1fr; }
    .track-connector { width: 12px; }
}
</style>
@endsection

@section('content')

@php
$groups = [
    'all'        => ['label' => 'جميع المستويات', 'levels' => range(0,14)],
    'foundation' => ['label' => 'مستويات التمهيد', 'levels' => [0,1,2]],
    'core-a'     => ['label' => 'المستويات 1 — 6',  'levels' => [3,4,5,6,7,8]],
    'core-b'     => ['label' => 'المستويات 7 — 12', 'levels' => [9,10,11,12,13,14]],
];
$levelLabels = [
    0=>'التمهيدي',1=>'التأسيسي',2=>'المبتدئ',
    3=>'المستوى 1',4=>'المستوى 2',5=>'المستوى 3',6=>'المستوى 4',
    7=>'المستوى 5',8=>'المستوى 6',9=>'المستوى 7',10=>'المستوى 8',
    11=>'المستوى 9',12=>'المستوى 10',13=>'المستوى 11',14=>'المستوى 12',
];
$abbrs = [
    0=>'PH',1=>'FN',2=>'BG',
    3=>'1',4=>'2',5=>'3',6=>'4',7=>'5',8=>'6',
    9=>'7',10=>'8',11=>'9',12=>'10',13=>'11',14=>'12',
];

$activeTab = request('tab', 'all');
if (!array_key_exists($activeTab, $groups)) $activeTab = 'all';

$byLevel = $programs->keyBy('level');
@endphp

{{-- ── HERO ── --}}
<section class="eng-hero">
    <div class="inner">
        <div class="breadcrumb-nav mb-3" style="opacity:.8;font-size:.85rem;">
            <a href="{{ route('home') }}" style="color:rgba(255,255,255,.8);text-decoration:none;">الرئيسية</a>
            <span style="margin:0 .4rem;opacity:.5;">/</span>
            <span>اللغة الإنجليزية</span>
        </div>
        <div class="badge-label">
            <i class="bi bi-translate"></i>
            برامج اللغة الإنجليزية
        </div>
        <h1>تعلّم الإنجليزية من الصفر حتى الاحتراف</h1>
        <p>{{ $programs->count() }} مستوى متدرّج مُصمَّم لجميع الفئات — من التمهيدي حتى المستوى الثاني عشر. كل مستوى مدته شهر واحد بـ 40 ساعة تدريبية.</p>
        <div class="hero-stats">
            <div class="stat-pill">
                <strong>{{ $programs->count() }}</strong>
                <span>مستوى</span>
            </div>
            <div class="stat-pill">
                <strong>40</strong>
                <span>ساعة / مستوى</span>
            </div>
            <div class="stat-pill">
                <strong>شهر</strong>
                <span>مدة كل مستوى</span>
            </div>
        </div>
    </div>
</section>

{{-- ── LEVEL TRACK ── --}}
<div class="level-track">
    <div class="level-track-inner">
        @foreach(range(0,14) as $lv)
        <div class="track-step" id="track-{{ $lv }}" onclick="jumpToLevel({{ $lv }})">
            <div class="circle" id="circle-{{ $lv }}">{{ $abbrs[$lv] }}</div>
            <span class="lbl">{{ $lv <= 2 ? $levelLabels[$lv] : 'M'.($lv-2) }}</span>
        </div>
        @if($lv < 14)
        <div class="track-connector"></div>
        @endif
        @endforeach
    </div>
</div>

{{-- ── TABS ── --}}
<div class="eng-tabs-wrap">
    <div class="eng-tabs">
        @foreach($groups as $key => $group)
        @php $cnt = collect($group['levels'])->filter(fn($lv) => $byLevel->has($lv))->count(); @endphp
        <button class="eng-tab {{ $activeTab === $key ? 'active' : '' }}"
                data-tab="{{ $key }}" onclick="switchTab('{{ $key }}')">
            <span class="tab-count">{{ $cnt }}</span>
            {{ $group['label'] }}
        </button>
        @endforeach
    </div>
</div>

{{-- ── CARDS ── --}}
<div class="eng-cards-section">
    @foreach($groups as $key => $group)
    <div class="tab-panel {{ $activeTab === $key ? 'active' : '' }}" id="panel-{{ $key }}">
        @php $levelList = collect($group['levels'])->filter(fn($lv) => $byLevel->has($lv))->values(); @endphp
        @if($levelList->isEmpty())
            <div class="empty-state">
                <i class="bi bi-translate"></i>
                <p class="text-muted">لا توجد مستويات في هذه الفئة بعد</p>
            </div>
        @else
        <div class="eng-grid">
            @foreach($levelList as $lv)
            @php $prog = $byLevel[$lv]; @endphp
            <div class="eng-card" data-level="{{ $lv }}">
                <div class="eng-card-top"></div>
                <div class="eng-card-body">
                    <div class="level-badge">
                        @if($lv <= 2)
                            <span class="lvl-word">{{ ['PH','FN','BG'][$lv] }}</span>
                        @else
                            <span class="lvl-num">{{ $lv - 2 }}</span>
                            <span class="lvl-word">Level</span>
                        @endif
                    </div>
                    <h3>{{ $prog->name_ar }}</h3>
                    @if($prog->name_en)
                    <p class="en-name">{{ $prog->name_en }}</p>
                    @endif
                    <div class="eng-card-meta">
                        <span class="meta-chip"><i class="bi bi-clock"></i> {{ $prog->duration_months ?? 1 }} شهر</span>
                        <span class="meta-chip"><i class="bi bi-journal-bookmark"></i> 40 ساعة</span>
                        @if($prog->price > 0)
                        <span class="meta-chip"><i class="bi bi-tag"></i> {{ number_format($prog->price,0) }} ر.س</span>
                        @else
                        <span class="meta-chip" style="background:#dcfce7;color:#16a34a;"><i class="bi bi-gift" style="color:#16a34a;"></i> مجاني</span>
                        @endif
                    </div>
                </div>
                <div class="eng-card-footer">
                    <div class="status-dot">
                        <span class="dot"></span>
                        <span style="color:#16a34a;">متاح للتسجيل</span>
                    </div>
                    <a href="{{ route('register') }}" class="btn-enroll">
                        <i class="bi bi-pencil-square"></i> سجّل الآن
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endforeach
</div>

<script>
const tabGroups = @json(collect($groups)->map(fn($g) => $g['levels']));

function switchTab(key) {
    document.querySelectorAll('.eng-tab').forEach(t => t.classList.toggle('active', t.dataset.tab === key));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.toggle('active', p.id === 'panel-' + key));
    history.replaceState(null,'', '?tab=' + key);
    highlightTrack([]);
}

function highlightTrack(levels) {
    for (let i = 0; i <= 14; i++) {
        const c = document.getElementById('circle-' + i);
        const s = document.getElementById('track-' + i);
        if (!c) continue;
        const on = levels.length === 0 || levels.includes(i);
        c.classList.toggle('active', on);
        s.classList.toggle('active-step', on);
    }
}

function jumpToLevel(lv) {
    // find which tab contains this level
    for (const [key, lvls] of Object.entries(tabGroups)) {
        if (lvls.includes(lv)) {
            switchTab('all');
            setTimeout(() => {
                const card = document.querySelector('.eng-card[data-level="' + lv + '"]');
                if (card) card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
            return;
        }
    }
}

// highlight active tab levels on load
document.addEventListener('DOMContentLoaded', () => {
    const activeKey = document.querySelector('.eng-tab.active')?.dataset.tab;
    if (activeKey && tabGroups[activeKey]) highlightTrack(tabGroups[activeKey]);
});

document.querySelectorAll('.eng-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        const key = btn.dataset.tab;
        if (tabGroups[key]) highlightTrack(tabGroups[key]);
    });
});
</script>
@endsection
