@extends('layouts.front')

@section('title', $program->name_ar . ' — معهد الارتقاء العالي للتدريب')

@section('styles')
<style>
/* ══════════════════════════════
   Page outer spacing
══════════════════════════════ */
.ps-page {
    background: #f4f7fb;
    padding: 2.5rem clamp(1.5rem, 6vw, 5rem);
}

/* ══════════════════════════════
   Program hero banner
══════════════════════════════ */
.ps-banner {
    background: linear-gradient(135deg, #0a2540 0%, #0071aa 60%, #0090d0 100%);
    padding: 3rem clamp(1.5rem, 6vw, 5rem) 2rem;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.ps-banner::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.ps-banner-inner {
    position: relative; z-index: 1;
    max-width: 1200px; margin: 0 auto;
    display: flex; align-items: flex-start; gap: 1.5rem; flex-wrap: wrap;
}
.ps-banner-icon {
    width: 72px; height: 72px; border-radius: 20px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.15); backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,.2);
    font-size: 2rem; color: #fff;
}
.ps-banner-text { flex: 1; min-width: 0; }
.ps-banner-breadcrumb {
    display: flex; align-items: center; gap: .5rem; flex-wrap: wrap;
    font-size: .78rem; color: rgba(255,255,255,.65);
    margin-bottom: .75rem;
}
.ps-banner-breadcrumb a { color: rgba(255,255,255,.75); text-decoration: none; }
.ps-banner-breadcrumb a:hover { color: #fff; }
.ps-banner-breadcrumb span { color: rgba(255,255,255,.4); }
.ps-banner-title {
    font-size: clamp(1.4rem, 3vw, 2rem);
    font-weight: 800; line-height: 1.25;
    margin-bottom: .4rem; color: #fff;
}
.ps-banner-en {
    font-size: .95rem; color: rgba(255,255,255,.6);
    margin-bottom: 1.25rem; direction: ltr;
}
.ps-banner-pills { display: flex; flex-wrap: wrap; gap: .5rem; }
.ps-banner-pill {
    display: inline-flex; align-items: center; gap: .4rem;
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.18);
    border-radius: 20px; padding: .3rem .85rem;
    font-size: .78rem; font-weight: 600; color: rgba(255,255,255,.9);
    backdrop-filter: blur(4px);
}

/* ══════════════════════════════
   Layout grid
══════════════════════════════ */
.ps-grid {
    max-width: 1200px; margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 1.75rem;
    align-items: flex-start;
}
@media(max-width:960px)  { .ps-grid { grid-template-columns: 1fr; } }

/* ══════════════════════════════
   Cards / panels
══════════════════════════════ */
.ps-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 12px rgba(0,0,0,.05);
    overflow: hidden;
    margin-bottom: 1.5rem;
}
.ps-card:last-child { margin-bottom: 0; }
.ps-card-header {
    display: flex; align-items: center; gap: .75rem;
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    background: #fafbfc;
}
.ps-card-header-icon {
    width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
    background: linear-gradient(135deg,#0071aa,#0090d0);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: .9rem;
}
.ps-card-header h3 {
    font-size: .95rem; font-weight: 700; color: #1a2540; margin: 0;
}
.ps-card-header span {
    font-size: .75rem; color: #9ca3af; margin-right: auto;
}
.ps-card-body { padding: 1.5rem; }

/* ── Description ── */
.ps-desc {
    font-size: .9rem; line-height: 2; color: #4b5563;
}

/* ── Stats row ── */
.ps-stats {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(120px,1fr));
    gap: 1rem;
}
.ps-stat {
    text-align: center;
    padding: 1.1rem .75rem;
    background: #f8fafc; border-radius: 14px;
    border: 1px solid #e5e7eb;
    transition: transform .2s, box-shadow .2s;
}
.ps-stat:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,113,170,.1); }
.ps-stat-icon {
    width: 42px; height: 42px; border-radius: 12px; margin: 0 auto .6rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
}
.ps-stat-val { font-size: 1.15rem; font-weight: 800; color: #1a2540; }
.ps-stat-lbl { font-size: .7rem; color: #9ca3af; margin-top: .15rem; }

/* ── Terms accordion ── */
.ps-terms { display: flex; flex-direction: column; gap: .75rem; }
.ps-term { border-radius: 14px; overflow: hidden; border: 1px solid #e5e7eb; background: #fff; }
.ps-term-toggle { display: none; }
.ps-term-label {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1rem 1.1rem; cursor: pointer;
    background: #f8fafc; transition: background .15s;
    user-select: none;
}
.ps-term-label:hover { background: #eef2ff; }
.ps-term-left { display: flex; align-items: center; gap: .85rem; }
.ps-term-num {
    width: 38px; height: 38px; border-radius: 11px; flex-shrink: 0;
    background: linear-gradient(135deg,#0071aa,#0090d0);
    color: #fff; font-weight: 800; font-size: .85rem;
    display: flex; align-items: center; justify-content: center;
}
.ps-term-info {}
.ps-term-name { font-size: .88rem; font-weight: 700; color: #1a2540; }
.ps-term-date { font-size: .72rem; color: #9ca3af; margin-top: .12rem; }
.ps-term-right { display: flex; align-items: center; gap: .6rem; }
.ps-term-status {
    padding: .22rem .65rem; border-radius: 6px;
    font-size: .68rem; font-weight: 700;
}
.ps-term-count {
    display: inline-flex; align-items: center; gap: .3rem;
    background: #e0f2fe; color: #0071aa;
    border-radius: 20px; padding: .2rem .65rem; font-size: .72rem; font-weight: 700;
}
.ps-term-chevron {
    color: #9ca3af; font-size: .8rem;
    transition: transform .25s;
}
.ps-term-body {
    max-height: 0; overflow: hidden;
    transition: max-height .35s ease, padding .25s;
    padding: 0 1.1rem;
}
.ps-term-toggle:checked ~ .ps-term-label .ps-term-chevron { transform: rotate(180deg); }
.ps-term-toggle:checked ~ .ps-term-body {
    max-height: 600px;
    padding: .85rem 1.1rem;
    border-top: 1px solid #f1f5f9;
}

.ps-subjects { display: flex; flex-direction: column; gap: .45rem; }
.ps-subject {
    display: flex; align-items: center; gap: .65rem;
    padding: .55rem .75rem; border-radius: 10px;
    background: #f8fafc; border: 1px solid #f1f5f9;
    font-size: .82rem; color: #374151;
    transition: background .15s;
}
.ps-subject:hover { background: #eff6ff; }
.ps-subject-icon {
    width: 28px; height: 28px; border-radius: 8px; flex-shrink: 0;
    background: #e0f2fe; color: #0071aa;
    display: flex; align-items: center; justify-content: center; font-size: .75rem;
}
.ps-subject-name { flex: 1; font-weight: 600; }
.ps-subject-code { font-size: .7rem; color: #9ca3af; }
.ps-no-subjects { font-size: .8rem; color: #9ca3af; text-align: center; padding: 1rem 0; }

/* ══════════════════════════════
   Sidebar
══════════════════════════════ */
.ps-sidebar { position: sticky; top: 80px; }

.ps-pricing-card {
    background: #fff;
    border-radius: 18px; border: 1px solid #e5e7eb;
    box-shadow: 0 4px 20px rgba(0,0,0,.07);
    overflow: hidden; margin-bottom: 1.25rem;
}
.ps-pricing-img { width: 100%; height: 190px; object-fit: cover; display: block; }
.ps-pricing-placeholder {
    height: 150px;
    background: linear-gradient(135deg,#0a2540,#0071aa);
    display: flex; align-items: center; justify-content: center;
}
.ps-pricing-placeholder i { font-size: 2.5rem; color: rgba(255,255,255,.3); }

.ps-pricing-body { padding: 1.4rem; }

.ps-price-area { text-align: center; padding: .75rem; margin-bottom: 1.25rem; }
.ps-price-big { font-size: 2.2rem; font-weight: 900; color: #0071aa; line-height: 1; }
.ps-price-sub { font-size: .75rem; color: #9ca3af; margin-top: .2rem; }
.ps-price-free {
    display: inline-flex; align-items: center; gap: .6rem;
    background: #dcfce7; color: #16a34a;
    border-radius: 14px; padding: .6rem 1.5rem; font-weight: 800; font-size: 1.05rem;
}

.ps-divider { height: 1px; background: #f1f5f9; margin: 1rem 0; }

.ps-meta-list { list-style: none; padding: 0; margin: 0 0 1.35rem; display: flex; flex-direction: column; gap: .6rem; }
.ps-meta-item {
    display: flex; align-items: center; gap: .75rem;
    font-size: .82rem; color: #4b5563; padding: .5rem .6rem;
    border-radius: 10px; background: #f8fafc;
}
.ps-meta-item-icon {
    width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0;
    background: #e0f2fe; color: #0071aa;
    display: flex; align-items: center; justify-content: center; font-size: .85rem;
}
.ps-meta-item strong { color: #1a2540; }

.ps-reg-btn {
    display: block; width: 100%; text-align: center;
    background: linear-gradient(135deg,#0071aa 0%,#0090d0 100%);
    color: #fff; border-radius: 13px;
    padding: .95rem; font-size: .95rem; font-weight: 700;
    text-decoration: none; transition: all .2s; border: none;
    box-shadow: 0 4px 14px rgba(0,113,170,.25);
}
.ps-reg-btn:hover { background: linear-gradient(135deg,#005a88,#0071aa); color:#fff; transform: translateY(-2px); box-shadow: 0 8px 22px rgba(0,113,170,.35); }
.ps-contact-link {
    display: block; text-align: center; font-size: .74rem; color: #9ca3af; margin-top: .75rem;
}
.ps-contact-link a { color: #0071aa; }

/* trust chips */
.ps-trust { display: flex; flex-wrap: wrap; gap: .5rem; }
.ps-trust-chip {
    display: inline-flex; align-items: center; gap: .35rem;
    background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0;
    border-radius: 20px; padding: .28rem .7rem; font-size: .72rem; font-weight: 600;
}

/* ══════════════════════════════
   English level hero badge
══════════════════════════════ */
.eng-lvl-badge {
    display: inline-flex; flex-direction: column; align-items: center; justify-content: center;
    width: 72px; height: 72px; border-radius: 20px;
    color: #fff; font-weight: 800;
    box-shadow: 0 4px 16px rgba(0,0,0,.25); margin-bottom: .25rem;
}
.eng-lvl-badge .num { font-size: 1.5rem; line-height: 1; }
.eng-lvl-badge .lbl { font-size: .58rem; opacity: .88; }

/* ══════════════════════════════
   CTA footer banner
══════════════════════════════ */
.ps-cta {
    background: linear-gradient(135deg,#0a2540 0%,#0071aa 100%);
    padding: 3rem clamp(1.5rem,6vw,5rem);
    color: white; position: relative; overflow: hidden;
}
.ps-cta::after {
    content:'';
    position:absolute; bottom:-40px; left:-40px;
    width:260px; height:260px; border-radius:50%;
    background:rgba(255,255,255,.04);
}
.ps-cta-inner {
    max-width:1200px; margin: 0 auto;
    display: flex; align-items: center; justify-content: space-between;
    gap: 2rem; flex-wrap: wrap; position: relative; z-index: 1;
}
.ps-cta h3 { font-size: 1.5rem; font-weight: 800; margin-bottom: .6rem; }
.ps-cta p { opacity: .88; font-size: .95rem; line-height: 1.7; margin-bottom: 1.4rem; }

@media(max-width:600px) {
    .ps-banner-icon { display: none; }
    .ps-grid { gap: 1rem; }
    .ps-stats { grid-template-columns: 1fr 1fr; }
}
</style>
@endsection

@section('content')

@php
$catColors = [
    'إدارة وأعمال'   => ['badge'=>'#eff6ff','text'=>'#1d4ed8','icon'=>'bi-briefcase',    'grad'=>'#2563eb,#60a5fa'],
    'تقنية المعلومات' => ['badge'=>'#f5f3ff','text'=>'#6d28d9','icon'=>'bi-cpu',          'grad'=>'#7c3aed,#a78bfa'],
    'تسويق ومبيعات'  => ['badge'=>'#ecfdf5','text'=>'#065f46','icon'=>'bi-megaphone',     'grad'=>'#059669,#34d399'],
    'تطوير الذات'    => ['badge'=>'#fffbeb','text'=>'#92400e','icon'=>'bi-person-check',  'grad'=>'#d97706,#fbbf24'],
    'تدريب وتعليم'   => ['badge'=>'#fef2f2','text'=>'#991b1b','icon'=>'bi-mortarboard',   'grad'=>'#dc2626,#f87171'],
    'تصميم وفنون'    => ['badge'=>'#fdf2f8','text'=>'#9d174d','icon'=>'bi-palette',       'grad'=>'#db2777,#f472b6'],
];
$defaultColor = ['badge'=>'#f1f5f9','text'=>'#475569','icon'=>'bi-collection','grad'=>'#64748b,#94a3b8'];
$col = $catColors[$program->category ?? ''] ?? $defaultColor;

$levelColors = [
    0=>['from'=>'#64748b','to'=>'#94a3b8'],1=>['from'=>'#0891b2','to'=>'#06b6d4'],
    2=>['from'=>'#0284c7','to'=>'#38bdf8'],3=>['from'=>'#059669','to'=>'#34d399'],
    4=>['from'=>'#16a34a','to'=>'#4ade80'],5=>['from'=>'#65a30d','to'=>'#a3e635'],
    6=>['from'=>'#ca8a04','to'=>'#fde047'],7=>['from'=>'#d97706','to'=>'#fbbf24'],
    8=>['from'=>'#ea580c','to'=>'#fb923c'],9=>['from'=>'#dc2626','to'=>'#f87171'],
    10=>['from'=>'#db2777','to'=>'#f472b6'],11=>['from'=>'#9333ea','to'=>'#c084fc'],
    12=>['from'=>'#7c3aed','to'=>'#a78bfa'],13=>['from'=>'#4f46e5','to'=>'#818cf8'],
    14=>['from'=>'#1d4ed8','to'=>'#60a5fa'],
];
$levelLabels = [0=>'التمهيدي',1=>'التأسيسي',2=>'المبتدئ',3=>'المستوى 1',4=>'المستوى 2',5=>'المستوى 3',
    6=>'المستوى 4',7=>'المستوى 5',8=>'المستوى 6',9=>'المستوى 7',10=>'المستوى 8',
    11=>'المستوى 9',12=>'المستوى 10',13=>'المستوى 11',14=>'المستوى 12'];
$abbrs=[0=>'PH',1=>'FN',2=>'BG',3=>'1',4=>'2',5=>'3',6=>'4',7=>'5',8=>'6',9=>'7',10=>'8',11=>'9',12=>'10',13=>'11',14=>'12'];
$lc = ($program->type === 'english' && isset($program->level)) ? ($levelColors[$program->level] ?? null) : null;

$termStatusColors = [
    'active'   => ['bg'=>'#dcfce7','text'=>'#16a34a','label'=>'نشط'],
    'upcoming' => ['bg'=>'#fef3c7','text'=>'#d97706','label'=>'قادم'],
    'completed'=> ['bg'=>'#f1f5f9','text'=>'#6b7280','label'=>'منتهي'],
    'inactive' => ['bg'=>'#fef2f2','text'=>'#dc2626','label'=>'موقوف'],
];

$typeIcons = [
    'english'       => 'bi-translate',
    'developmental' => 'bi-lightning-charge',
    'qualifying'    => 'bi-award',
    'training'      => 'bi-person-workspace',
    'diploma'       => 'bi-mortarboard',
    'course'        => 'bi-journal-bookmark',
];
$pageIcon = $typeIcons[$program->type ?? ''] ?? 'bi-mortarboard';
@endphp

{{-- ── Banner ── --}}
<div class="ps-banner">
    <div class="ps-banner-inner">

        {{-- Icon --}}
        <div class="ps-banner-icon">
            @if($lc)
            <div class="eng-lvl-badge"
                 style="background:linear-gradient(135deg,{{ $lc['from'] }},{{ $lc['to'] }});width:100%;height:100%;border-radius:18px;">
                @if($program->level <= 2)
                    <span class="lbl" style="font-size:.75rem;">{{ $abbrs[$program->level] }}</span>
                @else
                    <span class="num">{{ $program->level - 2 }}</span>
                    <span class="lbl">Level</span>
                @endif
            </div>
            @else
            <i class="bi {{ $pageIcon }}" style="font-size:1.8rem;"></i>
            @endif
        </div>

        {{-- Text --}}
        <div class="ps-banner-text">
            <div class="ps-banner-breadcrumb">
                <a href="{{ route('home') }}">الرئيسية</a>
                <span>/</span>
                <a href="{{ $backUrl }}">{{ $backLabel }}</a>
                <span>/</span>
                <span>{{ Str::limit($program->name_ar, 40) }}</span>
            </div>

            @if($program->category)
            <span style="display:inline-flex;align-items:center;gap:.35rem;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.2);border-radius:20px;padding:.25rem .75rem;font-size:.73rem;font-weight:600;color:rgba(255,255,255,.9);margin-bottom:.65rem;">
                <i class="bi {{ $col['icon'] }}"></i> {{ $program->category }}
            </span>
            @endif

            <div class="ps-banner-title">{{ $program->name_ar }}</div>
            @if($program->name_en)
            <div class="ps-banner-en">{{ $program->name_en }}</div>
            @endif

            <div class="ps-banner-pills">
                @if($program->duration_hours)
                <span class="ps-banner-pill"><i class="bi bi-clock"></i> {{ $program->duration_hours }} ساعة</span>
                @endif
                @if($program->duration_months)
                <span class="ps-banner-pill"><i class="bi bi-calendar3"></i> {{ $program->duration_months }} شهر</span>
                @endif
                @if($program->terms->isNotEmpty())
                <span class="ps-banner-pill"><i class="bi bi-layers"></i> {{ $program->terms->count() }} فصول</span>
                @endif
                @if($lc && isset($program->level))
                <span class="ps-banner-pill"><i class="bi bi-bar-chart-steps"></i> {{ $levelLabels[$program->level] ?? '' }}</span>
                @endif
                <span class="ps-banner-pill" style="background:rgba(34,197,94,.2);border-color:rgba(34,197,94,.3);color:#bbf7d0;">
                    <i class="bi bi-check-circle-fill"></i> متاح للتسجيل
                </span>
            </div>
        </div>
    </div>
</div>

{{-- ── Page body ── --}}
<div class="ps-page">
<div class="ps-grid">

    {{-- ══ Main column ══ --}}
    <div class="ps-main-col">

        {{-- Stats row --}}
        @if($program->duration_hours || $program->duration_months || $program->terms->isNotEmpty() || $program->price !== null)
        <div class="ps-card" style="margin-bottom:1.5rem;">
            <div class="ps-card-body" style="padding:1.25rem;">
                <div class="ps-stats">
                    @if($program->duration_hours)
                    <div class="ps-stat">
                        <div class="ps-stat-icon" style="background:#e0f2fe;color:#0071aa;"><i class="bi bi-clock-fill"></i></div>
                        <div class="ps-stat-val">{{ $program->duration_hours }}</div>
                        <div class="ps-stat-lbl">ساعة تدريبية</div>
                    </div>
                    @endif
                    @if($program->duration_months)
                    <div class="ps-stat">
                        <div class="ps-stat-icon" style="background:#ede9fe;color:#7c3aed;"><i class="bi bi-calendar3-fill"></i></div>
                        <div class="ps-stat-val">{{ $program->duration_months }}</div>
                        <div class="ps-stat-lbl">شهر</div>
                    </div>
                    @endif
                    @if($program->terms->isNotEmpty())
                    <div class="ps-stat">
                        <div class="ps-stat-icon" style="background:#fef3c7;color:#d97706;"><i class="bi bi-layers-fill"></i></div>
                        <div class="ps-stat-val">{{ $program->terms->count() }}</div>
                        <div class="ps-stat-lbl">فصل دراسي</div>
                    </div>
                    @endif
                    @php $totalSubjects = $program->terms->sum(fn($t) => $t->subjects->count()); @endphp
                    @if($totalSubjects > 0)
                    <div class="ps-stat">
                        <div class="ps-stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="bi bi-journal-fill"></i></div>
                        <div class="ps-stat-val">{{ $totalSubjects }}</div>
                        <div class="ps-stat-lbl">مادة دراسية</div>
                    </div>
                    @endif
                    <div class="ps-stat">
                        <div class="ps-stat-icon" style="background:#fce7f3;color:#db2777;"><i class="bi bi-patch-check-fill"></i></div>
                        <div class="ps-stat-val" style="font-size:.85rem;">معتمدة</div>
                        <div class="ps-stat-lbl">شهادة</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Description --}}
        @if($program->description_ar)
        <div class="ps-card">
            <div class="ps-card-header">
                <div class="ps-card-header-icon"><i class="bi bi-info-circle-fill"></i></div>
                <h3>نبذة عن البرنامج</h3>
            </div>
            <div class="ps-card-body">
                <p class="ps-desc">{{ $program->description_ar }}</p>
            </div>
        </div>
        @endif

        {{-- Terms accordion --}}
        @if($program->terms->isNotEmpty())
        <div class="ps-card">
            <div class="ps-card-header">
                <div class="ps-card-header-icon"><i class="bi bi-layers-fill"></i></div>
                <h3>الفصول الدراسية</h3>
                <span>{{ $program->terms->count() }} فصل</span>
            </div>
            <div class="ps-card-body">
                <div class="ps-terms">
                    @foreach($program->terms as $term)
                    @php $sc = $termStatusColors[$term->status ?? 'upcoming'] ?? $termStatusColors['upcoming']; @endphp
                    <div class="ps-term">
                        <input type="checkbox" class="ps-term-toggle" id="term-{{ $term->id }}" {{ $loop->first ? 'checked' : '' }}>
                        <label class="ps-term-label" for="term-{{ $term->id }}">
                            <div class="ps-term-left">
                                <div class="ps-term-num">{{ $term->term_number }}</div>
                                <div class="ps-term-info">
                                    <div class="ps-term-name">{{ $term->name_ar ?: ('الفصل ' . $term->term_number) }}</div>
                                    @if($term->start_date || $term->end_date)
                                    <div class="ps-term-date">
                                        <i class="bi bi-calendar2"></i>
                                        {{ $term->start_date?->format('Y/m/d') }}
                                        @if($term->end_date) — {{ $term->end_date->format('Y/m/d') }} @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="ps-term-right">
                                <span class="ps-term-status"
                                      style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};">{{ $sc['label'] }}</span>
                                <span class="ps-term-count">
                                    <i class="bi bi-journal-text"></i> {{ $term->subjects->count() }}
                                </span>
                                <i class="bi bi-chevron-down ps-term-chevron"></i>
                            </div>
                        </label>
                        <div class="ps-term-body">
                            @if($term->subjects->isNotEmpty())
                            <div class="ps-subjects">
                                @foreach($term->subjects as $subject)
                                <div class="ps-subject">
                                    <div class="ps-subject-icon"><i class="bi bi-journal-text"></i></div>
                                    <span class="ps-subject-name">{{ $subject->name_ar }}</span>
                                    @if($subject->code)
                                    <span class="ps-subject-code">{{ $subject->code }}</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="ps-no-subjects"><i class="bi bi-inbox"></i> لا توجد مواد مضافة لهذا الفصل بعد.</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- ══ Sidebar ══ --}}
    <div class="ps-sidebar">

        <div class="ps-pricing-card">

            @if($program->image)
            <img src="{{ Storage::url($program->image) }}" alt="{{ $program->name_ar }}" class="ps-pricing-img">
            @else
            <div class="ps-pricing-placeholder">
                <i class="bi {{ $pageIcon }}"></i>
            </div>
            @endif

            <div class="ps-pricing-body">

                {{-- Price --}}
                <div class="ps-price-area">
                    @if($program->price > 0)
                        <div class="ps-price-big">{{ number_format($program->price, 0) }} <x-riyal /></div>
                        <div class="ps-price-sub">رسوم التسجيل</div>
                    @else
                        <span class="ps-price-free"><i class="bi bi-gift-fill"></i> مجاني</span>
                    @endif
                </div>

                <div class="ps-divider"></div>

                {{-- Meta --}}
                <ul class="ps-meta-list">
                    @if($program->duration_hours)
                    <li class="ps-meta-item">
                        <div class="ps-meta-item-icon"><i class="bi bi-clock-fill"></i></div>
                        <span><strong>{{ $program->duration_hours }}</strong> ساعة تدريبية</span>
                    </li>
                    @endif
                    @if($program->duration_months)
                    <li class="ps-meta-item">
                        <div class="ps-meta-item-icon"><i class="bi bi-calendar3-fill"></i></div>
                        <span>المدة: <strong>{{ $program->duration_months }} شهر</strong></span>
                    </li>
                    @endif
                    @if($program->terms->isNotEmpty())
                    <li class="ps-meta-item">
                        <div class="ps-meta-item-icon"><i class="bi bi-layers-fill"></i></div>
                        <span><strong>{{ $program->terms->count() }} فصول</strong> دراسية</span>
                    </li>
                    @endif
                    @if($program->supervisor_name)
                    <li class="ps-meta-item">
                        <div class="ps-meta-item-icon"><i class="bi bi-person-workspace"></i></div>
                        <span>{{ $program->supervisor_name }}</span>
                    </li>
                    @endif
                    @if($program->code)
                    <li class="ps-meta-item">
                        <div class="ps-meta-item-icon"><i class="bi bi-hash"></i></div>
                        <span>{{ $program->code }}</span>
                    </li>
                    @endif
                    <li class="ps-meta-item">
                        <div class="ps-meta-item-icon" style="background:#e0f2fe;"><i class="bi bi-geo-alt-fill"></i></div>
                        <span>الرياض، المملكة العربية السعودية</span>
                    </li>
                </ul>

                {{-- CTA button --}}
                <a href="{{ route('register') }}" class="ps-reg-btn">
                    <i class="bi bi-pencil-square"></i>&nbsp; سجّل الآن
                </a>

                <div class="ps-divider"></div>

                {{-- Trust chips --}}
                <div class="ps-trust">
                    <span class="ps-trust-chip"><i class="bi bi-patch-check-fill"></i> شهادة معتمدة</span>
                    <span class="ps-trust-chip"><i class="bi bi-shield-check"></i> محتوى موثّق</span>
                    <span class="ps-trust-chip"><i class="bi bi-people-fill"></i> مدربون معتمدون</span>
                </div>

                <div class="ps-contact-link">
                    للاستفسار: <a href="{{ route('contact') }}">تواصل معنا</a>
                </div>
            </div>
        </div>

    </div>

</div>
</div>

{{-- ── CTA Banner ── --}}
<div class="ps-cta">
    <div class="ps-cta-inner">
        <div style="max-width:600px;">
            <h3>هل أنت مستعد للانضمام؟</h3>
            <p>سجّل الآن واستفد من برنامج <strong>{{ $program->name_ar }}</strong> واحصل على شهادة معتمدة تفتح لك أبواب المستقبل.</p>
            <a href="{{ route('register') }}" class="full-btn"
               style="display:inline-flex;align-items:center;gap:8px;font-size:.95rem;padding:12px 28px;border-radius:12px;">
                <i class="bi bi-arrow-left-circle-fill"></i> سجّل الآن
            </a>
        </div>
        <img loading="lazy" src="{{ asset('lms2-photo/4.png') }}" alt=""
             style="max-width:260px;width:100%;border-radius:18px;flex-shrink:0;"
             onerror="this.style.display='none'">
    </div>
</div>

@endsection
