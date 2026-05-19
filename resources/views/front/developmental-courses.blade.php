@extends('layouts.front')

@section('title', 'الدورات التطويرية — معهد الارتقاء العالي للتدريب')

@section('styles')
<style>
/* ── Tabs ── */
.cat-tabs-wrap {
    padding: 1.75rem clamp(1.5rem,5vw,5rem) 0;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
    position: sticky; top: 0; z-index: 10;
}
.cat-tabs { display: flex; gap: .25rem; flex-wrap: wrap; padding-right: 5.5rem; }
.cat-tab {
    padding: .6rem 1.25rem; border-radius: 8px 8px 0 0;
    font-size: .875rem; font-weight: 600; cursor: pointer;
    border: none; background: transparent;
    color: #6b7280; transition: all .2s;
    border-bottom: 3px solid transparent;
    display: flex; align-items: center; gap: .4rem;
}
.cat-tab:hover  { background: #eef2ff; color: #0071aa; }
.cat-tab.active { background: #fff; color: #0071aa; border-bottom-color: #0071aa; box-shadow: 0 -2px 8px rgba(0,0,0,.05); }
.cat-tab .tab-count {
    display: inline-flex; align-items: center; justify-content: center;
    width: 20px; height: 20px; border-radius: 50%;
    font-size: .7rem; font-weight: 700;
    background: #e0f2fe; color: #0071aa;
}
.cat-tab.active .tab-count { background: #0071aa; color: #fff; }

/* ── Cards section ── */
.dev-cards-section {
    padding: 2rem clamp(1.5rem,5vw,5rem);
    background: #f8fafc;
    min-height: 400px;
}
.dev-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
    max-width: 1300px;
    margin: 0 auto;
    direction: ltr;
}
.dev-card { direction: rtl; }
@media(max-width:991px) { .dev-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width:576px)  { .dev-grid { grid-template-columns: 1fr; } }

/* ── Card ── */
.dev-card {
    background: #fff; border-radius: 16px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    overflow: hidden; display: flex; flex-direction: column;
    transition: transform .25s, box-shadow .25s;
}
.dev-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,113,170,.12); }
.dev-card-top { height: 6px; background: linear-gradient(90deg,#2563eb,#60a5fa); }
.dev-card-body { padding: 1.25rem; flex: 1; }
.cat-badge {
    display: inline-flex; align-items: center; gap: .35rem;
    background: #eff6ff; color: #1d4ed8;
    border-radius: 6px; padding: .25rem .65rem;
    font-size: .72rem; font-weight: 700; margin-bottom: .85rem;
}
.dev-card-body h3 { font-size: .975rem; font-weight: 700; color: #1a2540; margin-bottom: .75rem; }
.dev-meta { display: flex; flex-wrap: wrap; gap: .5rem; }
.meta-chip {
    display: inline-flex; align-items: center; gap: .35rem;
    background: #f1f5f9; border-radius: 8px; padding: .3rem .65rem;
    font-size: .75rem; font-weight: 600; color: #475569;
}
.meta-chip i { color: #0071aa; font-size: .8rem; }
.dev-card-footer {
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
</style>
@endsection

@section('content')

{{-- Hero --}}
<section class="hero-section">
    <div class="breadcrumb-nav">
        <a href="{{ route('home') }}">الرئيسية</a>
        <span>></span>
        <span>الدورات</span>
        <span>></span>
        <span>{{ $pageTitle }}</span>
    </div>
    <h2>{{ $pageTitle }}</h2>
    <p>{{ $programs->count() }} دورة تطويرية متاحة في {{ $grouped->count() }} مجال متخصص — سجّل الآن واستفد من برامجنا المعتمدة.</p>
</section>

@php
$categories = $grouped->keys();
$allKey     = 'all';
$activeTab  = request('cat', $allKey);
if ($activeTab !== $allKey && !$categories->contains($activeTab)) {
    $activeTab = $allKey;
}

$catColors = [
    'إدارة وأعمال'   => ['top'=>'linear-gradient(90deg,#0071aa,#0ea5e9)',  'badge'=>'#eff6ff', 'text'=>'#1d4ed8', 'icon'=>'bi-briefcase'],
    'تقنية المعلومات' => ['top'=>'linear-gradient(90deg,#7c3aed,#a78bfa)',  'badge'=>'#f5f3ff', 'text'=>'#6d28d9', 'icon'=>'bi-cpu'],
    'تسويق ومبيعات'  => ['top'=>'linear-gradient(90deg,#059669,#34d399)',  'badge'=>'#ecfdf5', 'text'=>'#065f46', 'icon'=>'bi-megaphone'],
    'تطوير الذات'    => ['top'=>'linear-gradient(90deg,#d97706,#fbbf24)',  'badge'=>'#fffbeb', 'text'=>'#92400e', 'icon'=>'bi-person-check'],
    'تدريب وتعليم'   => ['top'=>'linear-gradient(90deg,#dc2626,#f87171)',  'badge'=>'#fef2f2', 'text'=>'#991b1b', 'icon'=>'bi-mortarboard'],
    'تصميم وفنون'    => ['top'=>'linear-gradient(90deg,#db2777,#f472b6)',  'badge'=>'#fdf2f8', 'text'=>'#9d174d', 'icon'=>'bi-palette'],
];
$defaultColor = ['top'=>'linear-gradient(90deg,#64748b,#94a3b8)', 'badge'=>'#f1f5f9', 'text'=>'#475569', 'icon'=>'bi-collection'];
@endphp

{{-- Tabs --}}
<div class="cat-tabs-wrap">
    <div class="cat-tabs">
        {{-- All tab --}}
        <button class="cat-tab {{ $activeTab === $allKey ? 'active' : '' }}"
                data-tab="{{ $allKey }}" onclick="switchTab('{{ $allKey }}')">
            الكل
            <span class="tab-count">{{ $programs->count() }}</span>
        </button>
        {{-- Category tabs --}}
        @foreach($categories as $cat)
        <button class="cat-tab {{ $activeTab === $cat ? 'active' : '' }}"
                data-tab="{{ $cat }}" onclick="switchTab('{{ $cat }}')">
            @php $col = $catColors[$cat] ?? $defaultColor; @endphp
            <i class="bi {{ $col['icon'] }}" style="font-size:.8rem;"></i>
            {{ $cat }}
            <span class="tab-count">{{ $grouped[$cat]->count() }}</span>
        </button>
        @endforeach
    </div>
</div>

{{-- Cards --}}
<div class="dev-cards-section">

    {{-- All panel --}}
    <div class="tab-panel {{ $activeTab === $allKey ? 'active' : '' }}" id="panel-all">
        <div class="dev-grid">
            @foreach($programs as $program)
            @php $col = $catColors[$program->category ?? ''] ?? $defaultColor; @endphp
            <a href="{{ route('courses.developmental.show', $program) }}" class="dev-card" style="text-decoration:none;color:inherit;">
                <div class="dev-card-top" style="background:{{ $col['top'] }};"></div>
                <div class="dev-card-body">
                    @if($program->category)
                    <span class="cat-badge" style="background:{{ $col['badge'] }};color:{{ $col['text'] }};">
                        <i class="bi {{ $col['icon'] }}"></i> {{ $program->category }}
                    </span>
                    @endif
                    <h3>{{ $program->name_ar }}</h3>
                    <div class="dev-meta">
                        @if($program->duration_hours)
                        <span class="meta-chip"><i class="bi bi-clock"></i> {{ $program->duration_hours }} ساعة</span>
                        @endif
                        @if($program->price > 0)
                        <span class="meta-chip"><i class="bi bi-tag"></i> {{ number_format($program->price,0) }} <x-riyal /></span>
                        @else
                        <span class="meta-chip" style="background:#dcfce7;color:#16a34a;"><i class="bi bi-gift" style="color:#16a34a;"></i> مجاني</span>
                        @endif
                    </div>
                </div>
                <div class="dev-card-footer">
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
    </div>

    {{-- Per-category panels --}}
    @foreach($grouped as $cat => $catPrograms)
    <div class="tab-panel {{ $activeTab === $cat ? 'active' : '' }}" id="panel-{{ Str::slug($cat) }}">
        @if($catPrograms->isEmpty())
        <div class="empty-state">
            <i class="bi bi-collection"></i>
            <p class="text-muted">لا توجد دورات في هذه الفئة بعد</p>
        </div>
        @else
        <div class="dev-grid">
            @foreach($catPrograms as $program)
            @php $col = $catColors[$cat] ?? $defaultColor; @endphp
            <a href="{{ route('courses.developmental.show', $program) }}" class="dev-card" style="text-decoration:none;color:inherit;">
                <div class="dev-card-top" style="background:{{ $col['top'] }};"></div>
                <div class="dev-card-body">
                    <span class="cat-badge" style="background:{{ $col['badge'] }};color:{{ $col['text'] }};">
                        <i class="bi {{ $col['icon'] }}"></i> {{ $cat }}
                    </span>
                    <h3>{{ $program->name_ar }}</h3>
                    <div class="dev-meta">
                        @if($program->duration_hours)
                        <span class="meta-chip"><i class="bi bi-clock"></i> {{ $program->duration_hours }} ساعة</span>
                        @endif
                        @if($program->price > 0)
                        <span class="meta-chip"><i class="bi bi-tag"></i> {{ number_format($program->price,0) }} <x-riyal /></span>
                        @else
                        <span class="meta-chip" style="background:#dcfce7;color:#16a34a;"><i class="bi bi-gift" style="color:#16a34a;"></i> مجاني</span>
                        @endif
                    </div>
                </div>
                <div class="dev-card-footer">
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

{{-- CTA --}}
<section style="background:linear-gradient(135deg,#004d7a 0%,#0071aa 100%);padding:3rem clamp(1rem,3vw,3rem);color:white;">
    <div class="row align-items-center">
        <div class="col-lg-7">
            <h2 style="font-weight:800;margin-bottom:1rem;">هل أنت مستعد لبدء رحلتك التطويرية؟</h2>
            <p style="opacity:.9;line-height:1.8;margin-bottom:1.5rem;">
                سجّل الآن في دوراتنا التطويرية واحصل على شهادة معتمدة تُعزّز مسيرتك المهنية.
            </p>
            <a href="{{ route('register') }}" class="full-btn"
               style="display:inline-flex;align-items:center;gap:8px;font-size:1rem;padding:12px 28px;border-radius:10px;">
                <i class="bi bi-arrow-left-circle-fill"></i> سجّل الآن
            </a>
        </div>
        <div class="col-lg-5 text-center mt-4 mt-lg-0">
            <img loading="lazy" src="{{ asset('lms2-photo/4.png') }}" alt="Training"
                 style="max-width:360px;width:100%;border-radius:20px;" onerror="this.style.display='none'">
        </div>
    </div>
</section>

<script>
const panelMap = @json(collect($grouped->keys())->mapWithKeys(fn($k) => [$k => \Illuminate\Support\Str::slug($k)]));

function switchTab(key) {
    document.querySelectorAll('.cat-tab').forEach(t => t.classList.toggle('active', t.dataset.tab === key));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));

    const panelId = key === 'all' ? 'panel-all' : 'panel-' + (panelMap[key] ?? key);
    const panel = document.getElementById(panelId);
    if (panel) panel.classList.add('active');

    history.replaceState(null, '', '?cat=' + encodeURIComponent(key));
}
</script>
@endsection
