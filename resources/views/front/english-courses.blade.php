@extends('layouts.front')

@section('title', 'برامج اللغة الإنجليزية - معهد الارتقاء العالي للتدريب')

@section('styles')
<style>
.level-track {
    background: #fff;
    border-bottom: 1px solid #e5e7eb;
    padding: 1.25rem clamp(1.5rem,5vw,5rem);
    overflow-x: auto;
}
.level-track-inner {
    display: flex; align-items: center; gap: 0;
    min-width: max-content;
    margin: 0 auto;
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
.track-step .circle.active { color: #fff; }
.track-step .lbl {
    font-size: .65rem; font-weight: 600; color: #9ca3af;
    white-space: nowrap; transition: color .25s;
}
.track-step.active-step .lbl { color: #374151; }
.track-connector {
    width: 20px; height: 2px; background: #e5e7eb; margin-bottom: 18px; flex-shrink: 0;
}

.eng-tabs-wrap {
    padding: 1.75rem clamp(1.5rem,5vw,5rem) 0;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}
.eng-tabs { display: flex; gap: .25rem; flex-wrap: wrap; padding-right: 1.5rem; }
.eng-tab {
    padding: .6rem 1.25rem; border-radius: 8px 8px 0 0;
    font-size: .875rem; font-weight: 600; cursor: pointer;
    border: none; background: transparent;
    color: #6b7280; transition: all .2s;
    border-bottom: 3px solid transparent;
}
.eng-tab:hover  { background: #eef2ff; color: #0071aa; }
.eng-tab.active { background: #fff; color: #0071aa; border-bottom-color: #0071aa; box-shadow: 0 -2px 8px rgba(0,0,0,.05); }
.eng-tab .tab-count {
    display: inline-flex; align-items: center; justify-content: center;
    width: 20px; height: 20px; border-radius: 50%;
    font-size: .7rem; font-weight: 700; margin-right: .4rem;
    background: #e0f2fe; color: #0071aa; vertical-align: middle;
}
.eng-tab.active .tab-count { background: #0071aa; color: #fff; }

.eng-cards-section {
    padding: 2rem clamp(1.5rem,5vw,5rem);
    background: #f8fafc;
    min-height: 350px;
}
.eng-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
    direction: ltr;
}
.eng-card { direction: rtl; }
@media(max-width:991px) { .eng-grid { grid-template-columns: repeat(2, 1fr); } }

.eng-card {
    background: #fff; border-radius: 16px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    overflow: hidden; display: flex; flex-direction: column;
    transition: transform .25s, box-shadow .25s;
}
.eng-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,113,170,.12); }

.eng-card-top { height: 7px; }
.eng-card-body { padding: 1.25rem 1.25rem .75rem; flex: 1; }

.level-badge {
    display: inline-flex; flex-direction: column; align-items: center; justify-content: center;
    width: 52px; height: 52px; border-radius: 14px;
    color: #fff; font-weight: 800; margin-bottom: .85rem;
}
.level-badge .lvl-num  { font-size: 1.2rem; line-height: 1; }
.level-badge .lvl-word { font-size: .52rem; opacity: .85; line-height: 1; }

.eng-card-body h3 { font-size: .975rem; font-weight: 700; color: #1a2540; margin-bottom: .25rem; }
.eng-card-body .en-name { font-size: .78rem; color: #9ca3af; margin-bottom: 1rem; direction: ltr; text-align: left; }
.eng-card-meta { display: flex; flex-wrap: wrap; gap: .5rem; }
.meta-chip {
    display: inline-flex; align-items: center; gap: .35rem;
    background: #f1f5f9; border-radius: 8px; padding: .3rem .65rem;
    font-size: .75rem; font-weight: 600; color: #475569;
}
.meta-chip i { color: #0071aa; font-size: .8rem; }

.eng-card-footer {
    padding: .85rem 1.25rem;
    border-top: 1px solid #f1f5f9;
    display: flex; align-items: center; justify-content: space-between;
}
.status-dot {
    display: flex; align-items: center; gap: .4rem;
    font-size: .75rem; font-weight: 600;
}
.status-dot .dot { width: 7px; height: 7px; border-radius: 50%; background: #22c55e; animation: pls 2s infinite; }
@keyframes pls { 0%,100%{opacity:1} 50%{opacity:.4} }

.tab-panel { display: none; }
.tab-panel.active { display: block; }
.empty-state { text-align: center; padding: 4rem 1rem; }
.empty-state i { font-size: 3rem; color: #d1d5db; margin-bottom: 1rem; display: block; }

@media(max-width:576px) {
    .eng-grid { grid-template-columns: 1fr; }
    .track-connector { width: 12px; }
}
</style>
@endsection

@section('content')

@php
$groups = [
    'all'        => ['label' => 'جميع المستويات', 'levels' => range(0,14)],
    'foundation' => ['label' => 'مستويات التمهيدي', 'levels' => [0,1,2]],
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
$levelColors = [
    0  => ['from'=>'#64748b','to'=>'#94a3b8','shadow'=>'rgba(100,116,139,.35)'],
    1  => ['from'=>'#0891b2','to'=>'#06b6d4','shadow'=>'rgba(8,145,178,.35)'],
    2  => ['from'=>'#0284c7','to'=>'#38bdf8','shadow'=>'rgba(2,132,199,.35)'],
    3  => ['from'=>'#059669','to'=>'#34d399','shadow'=>'rgba(5,150,105,.35)'],
    4  => ['from'=>'#16a34a','to'=>'#4ade80','shadow'=>'rgba(22,163,74,.35)'],
    5  => ['from'=>'#65a30d','to'=>'#a3e635','shadow'=>'rgba(101,163,13,.35)'],
    6  => ['from'=>'#ca8a04','to'=>'#fde047','shadow'=>'rgba(202,138,4,.35)'],
    7  => ['from'=>'#d97706','to'=>'#fbbf24','shadow'=>'rgba(217,119,6,.35)'],
    8  => ['from'=>'#ea580c','to'=>'#fb923c','shadow'=>'rgba(234,88,12,.35)'],
    9  => ['from'=>'#dc2626','to'=>'#f87171','shadow'=>'rgba(220,38,38,.35)'],
    10 => ['from'=>'#db2777','to'=>'#f472b6','shadow'=>'rgba(219,39,119,.35)'],
    11 => ['from'=>'#9333ea','to'=>'#c084fc','shadow'=>'rgba(147,51,234,.35)'],
    12 => ['from'=>'#7c3aed','to'=>'#a78bfa','shadow'=>'rgba(124,58,237,.35)'],
    13 => ['from'=>'#4f46e5','to'=>'#818cf8','shadow'=>'rgba(79,70,229,.35)'],
    14 => ['from'=>'#1d4ed8','to'=>'#60a5fa','shadow'=>'rgba(29,78,216,.35)'],
];
$activeTab = request('tab', 'all');
if (!array_key_exists($activeTab, $groups)) $activeTab = 'all';
$byLevel = $programs->keyBy('level');
@endphp

{{-- Hero --}}
<section class="hero-section">
    <div class="breadcrumb-nav">
        <a href="{{ route('home') }}">الرئيسية</a>
        <span>></span>
        <span>الدورات</span>
        <span>></span>
        <span>اللغة الإنجليزية</span>
    </div>
    <h2>برامج اللغة الإنجليزية</h2>
    <p>{{ $programs->count() }} مستوى متدرّج من التمهيدي حتى المستوى الثاني عشر — مدة كل مستوى شهر واحد بـ 40 ساعة تدريبية.</p>
</section>

{{-- Level Track --}}
<div class="level-track">
    <div class="level-track-inner">
        @foreach(range(0,14) as $lv)
        @php $c = $levelColors[$lv]; @endphp
        <div class="track-step" id="track-{{ $lv }}" onclick="jumpToLevel({{ $lv }})"
             data-from="{{ $c['from'] }}" data-to="{{ $c['to'] }}" data-shadow="{{ $c['shadow'] }}">
            <div class="circle" id="circle-{{ $lv }}">{{ $abbrs[$lv] }}</div>
            <span class="lbl">{{ $lv <= 2 ? $levelLabels[$lv] : 'L'.($lv-2) }}</span>
        </div>
        @if($lv < 14)
        <div class="track-connector"></div>
        @endif
        @endforeach
    </div>
</div>

{{-- Tabs --}}
<div class="eng-tabs-wrap">
    <div class="eng-tabs">
        @foreach($groups as $key => $group)
        @php $cnt = collect($group['levels'])->filter(fn($lv) => $byLevel->has($lv))->count(); @endphp
        <button class="eng-tab {{ $activeTab === $key ? 'active' : '' }}"
                data-tab="{{ $key }}" onclick="switchTab('{{ $key }}')">
            {{ $group['label'] }}
            <span class="tab-count">{{ $cnt }}</span>
        </button>
        @endforeach
    </div>
</div>

{{-- Cards --}}
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
            @php $prog = $byLevel[$lv]; $c = $levelColors[$lv]; @endphp
            <a href="{{ route('english-courses.show', $prog) }}" class="eng-card" data-level="{{ $lv }}" style="text-decoration:none;color:inherit;">
                <div class="eng-card-top" style="background:linear-gradient(90deg,{{ $c['from'] }},{{ $c['to'] }});"></div>
                <div class="eng-card-body">
                    <div class="level-badge"
                         style="background:linear-gradient(135deg,{{ $c['from'] }},{{ $c['to'] }});box-shadow:0 4px 12px {{ $c['shadow'] }};">
                        @if($lv <= 2)
                            <span class="lvl-word" style="font-size:.6rem;">{{ ['PH','FN','BG'][$lv] }}</span>
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
                        <span class="meta-chip"><i class="bi bi-tag"></i> {{ number_format($prog->price,0) }} <x-riyal /></span>
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
                    <span class="full-btn" style="font-size:.8rem;padding:.45rem 1rem;border-radius:8px;">
                        <i class="bi bi-eye"></i> التفاصيل
                    </span>
                </div>
            </a>
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
        const circle = document.getElementById('circle-' + i);
        const step   = document.getElementById('track-' + i);
        if (!circle) continue;
        const on = levels.length === 0 || levels.includes(i);
        circle.classList.toggle('active', on);
        step.classList.toggle('active-step', on);
        if (on) {
            circle.style.background  = `linear-gradient(135deg,${step.dataset.from},${step.dataset.to})`;
            circle.style.borderColor = step.dataset.from;
            circle.style.boxShadow   = `0 4px 12px ${step.dataset.shadow}`;
        } else {
            circle.style.background  = '';
            circle.style.borderColor = '';
            circle.style.boxShadow   = '';
        }
    }
}

function jumpToLevel(lv) {
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

document.addEventListener('DOMContentLoaded', () => {
    const activeKey = document.querySelector('.eng-tab.active')?.dataset.tab;
    if (activeKey && tabGroups[activeKey]) highlightTrack(tabGroups[activeKey]);
});

document.querySelectorAll('.eng-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        if (tabGroups[btn.dataset.tab]) highlightTrack(tabGroups[btn.dataset.tab]);
    });
});
</script>
@endsection
