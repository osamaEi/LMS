@extends('layouts.dashboard')

@section('title', 'الدرجات والتقييمات')

@push('styles')
<style>
    .grades-page { max-width: 1160px; margin: 0 auto; color: #172b42; --grade-border: #e4ebf2; --grade-muted: #64748b; --grade-surface: #fff; }
    .grades-page * { box-sizing: border-box; }
    .grades-hero { padding: 32px; border-radius: 24px; background: radial-gradient(ellipse at top left, #1689b5 0%, transparent 55%), linear-gradient(120deg, #00547f, #07344e); color: #fff; }
    .grades-topline { display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; }
    .grades-eyebrow { color: #bae6fd; font-size: 12px; margin: 0 0 8px; }
    .grades-hero h1 { font-size: clamp(23px, 4vw, 30px); font-weight: 800; margin: 0; }
    .grades-hero p { line-height: 1.8; }
    .grades-intro { font-size: 14px; color: #d4e8f3; margin: 8px 0 0; }
    .grades-back { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border: 1px solid #ffffff40; border-radius: 12px; color: #fff; text-decoration: none; font-size: 13px; background: #ffffff0d; }
    .grades-back:hover { background: #ffffff20; }
    .grades-back:focus-visible { outline: 3px solid #bae6fd; outline-offset: 4px; }
    .grades-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-top: 26px; }
    .grades-stat { padding: 18px; background: #ffffff0d; border: 1px solid #ffffff24; border-radius: 16px; }
    .grades-stat strong { display: block; font-size: 28px; font-weight: 800; font-variant-numeric: tabular-nums; }
    .grades-stat span { display: block; font-size: 12px; color: #d4e8f3; margin-top: 4px; }
    .grades-section-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin: 28px 0 16px; }
    .grades-section-head h2 { font-size: 19px; font-weight: 800; margin: 0 0 5px; }
    .grades-section-head p { color: var(--grade-muted); font-size: 13px; margin: 0; line-height: 1.7; }
    .grades-count { flex-shrink: 0; background: var(--grade-surface); border: 1px solid var(--grade-border); padding: 7px 12px; border-radius: 10px; font-size: 12px; }
    .grades-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
    .course-grade { background: var(--grade-surface); border: 1px solid var(--grade-border); border-radius: 20px; padding: 22px; box-shadow: 0 4px 16px #12324a04; min-width: 0; }
    .course-grade-head { display: flex; align-items: flex-start; gap: 12px; }
    .course-grade-icon { width: 44px; height: 44px; flex-shrink: 0; display: grid; place-items: center; border-radius: 13px; background: #eaf5fb; color: #0071aa; }
    .course-grade h3 { font-size: 16px; line-height: 1.7; font-weight: 800; margin: 0; overflow-wrap: anywhere; }
    .course-grade-teacher { color: var(--grade-muted); font-size: 12px; margin: 3px 0 0; }
    .course-grade-score { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin: 22px 0 16px; }
    .course-grade-number { font-size: 40px; line-height: 1.15; font-weight: 800; letter-spacing: -1px; font-variant-numeric: tabular-nums; }
    .course-grade-number small { font-size: 15px; font-weight: 500; color: var(--grade-muted); letter-spacing: 0; }
    .course-grade-label { font-size: 12px; color: var(--grade-muted); margin-top: 6px; }
    .course-grade-badge { padding: 7px 12px; border-radius: 9px; font-size: 12px; font-weight: 700; white-space: nowrap; }
    .grade-excellent { background: #dcfce7; color: #166534; }
    .grade-good { background: #dbeafe; color: #1e40af; }
    .grade-average { background: #fef3c7; color: #92400e; }
    .grade-poor { background: #fee2e2; color: #991b1b; }
    .course-grade-track { height: 7px; border-radius: 8px; background: #eaf0f5; overflow: hidden; }
    .course-grade-track span { display: block; height: 100%; border-radius: inherit; background: #0071aa; }
    .course-grade-footer { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px; margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--grade-border); font-size: 11px; color: var(--grade-muted); }
    .course-grade-source { color: #0071aa; font-weight: 700; }
    .grades-note { display: flex; align-items: flex-start; gap: 12px; background: #edf6fb; border: 1px solid #d6eaf5; border-radius: 16px; padding: 18px 20px; margin-top: 22px; }
    .grades-note svg { flex-shrink: 0; color: #0071aa; margin-top: 2px; }
    .grades-note h2 { font-size: 13px; font-weight: 800; margin: 0 0 5px; }
    .grades-note p { color: var(--grade-muted); font-size: 12px; line-height: 1.9; margin: 0; }
    .grades-empty { text-align: center; padding: 55px 24px; border: 1px dashed var(--grade-border); border-radius: 20px; background: var(--grade-surface); }
    .grades-empty .course-grade-icon { width: 64px; height: 64px; margin: 0 auto 18px; }
    .grades-empty h2 { font-size: 20px; font-weight: 800; margin-bottom: 8px; }
    .grades-empty p { font-size: 14px; color: var(--grade-muted); max-width: 420px; margin: auto; line-height: 1.9; }
    .dark .grades-page { color: #e5edf5; --grade-border: #334155; --grade-muted: #a6b5c7; --grade-surface: #1f2937; }
    .dark .course-grade-icon, .dark .grades-note { background: #173449; border-color: #28506a; }
    .dark .course-grade-icon, .dark .course-grade-source, .dark .grades-note svg { color: #7dd3fc; }
    .dark .course-grade-track { background: #334155; }
    .dark .course-grade-track span { background: #38bdf8; }
    @media (max-width: 640px) {
        .grades-hero { padding: 22px 18px; border-radius: 20px; }
        .grades-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; margin-top: 20px; }
        .grades-stat { padding: 14px; }
        .grades-stat strong { font-size: 24px; }
        .grades-grid { grid-template-columns: 1fr; }
        .course-grade { padding: 18px; }
        .grades-section-head { align-items: flex-start; }
        .grades-note { padding: 16px; }
    }
</style>
@endpush

@section('content')
<div class="grades-page" dir="rtl">
    <header class="grades-hero">
        <div class="grades-topline">
            <div>
                <p class="grades-eyebrow">سجلك الأكاديمي</p>
                <h1>الدرجات والتقييمات</h1>
                <p class="grades-intro">درجاتك في مكان واحد، لمتابعة تقدمك في كل مقرر.</p>
            </div>
            <a href="{{ route('student.dashboard') }}" class="grades-back">
                العودة للوحة التحكم <span aria-hidden="true">←</span>
            </a>
        </div>
        @if(!empty($subjectGrades))
        <div class="grades-stats">
            <div class="grades-stat"><strong>{{ number_format($avgPercentage ?? 0, 1) }}<small>%</small></strong><span>متوسط الدرجات المعروضة</span></div>
            <div class="grades-stat"><strong>{{ count($subjectGrades) }}</strong><span>مقررات لها درجات</span></div>
            <div class="grades-stat"><strong>{{ $totalQuizzes }}</strong><span>محاولات اختبار مسلّمة</span></div>
            <div class="grades-stat"><strong>{{ $totalEvaluations }}</strong><span>تقييمات مصحّحة</span></div>
        </div>
        @endif
    </header>

    <div class="grades-section-head">
        <div><h2>درجات المقررات</h2><p>اطّلع على درجتك وتقديرك في كل مقرر.</p></div>
        <span class="grades-count">{{ count($subjectGrades) }} مقرر</span>
    </div>

    @if(empty($subjectGrades))
    <div class="grades-empty">
        <div class="course-grade-icon" aria-hidden="true">—</div>
        <h2>درجاتك ستظهر هنا</h2>
        <p>عندما يسجّل المدرب درجتك النهائية أو تُصحّح تقييماتك، ستجد درجات المقررات في هذه الصفحة.</p>
    </div>
    @else
    <div class="grades-grid">
        @foreach($subjectGrades as $data)
        @php
            $isFinal = $data['final_grade'] !== null;
            $score = rtrim(rtrim(number_format($data['percentage'], 2, '.', ''), '0'), '.');
            $badgeClass = match (true) {
                $data['percentage'] >= 90 => 'grade-excellent',
                $data['percentage'] >= 75 => 'grade-good',
                $data['percentage'] >= 60 => 'grade-average',
                default => 'grade-poor',
            };
        @endphp
        <article class="course-grade">
            <div class="course-grade-head">
                <div class="course-grade-icon">
                    <svg width="23" height="23" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <h3>{{ $data['subject']->name_ar ?? $data['subject']->name }}</h3>
                    @if($data['subject']->teacher)
                    <p class="course-grade-teacher">المدرب: {{ $data['subject']->teacher->name }}</p>
                    @endif
                </div>
            </div>
            <div class="course-grade-score">
                <div>
                    <div class="course-grade-number"><bdi>{{ $score }} <small>{{ $isFinal ? '/ 100' : '%' }}</small></bdi></div>
                    <div class="course-grade-label">{{ $isFinal ? 'الدرجة النهائية من 100' : 'نسبة التقييمات والاختبارات' }}</div>
                </div>
                <span class="course-grade-badge {{ $badgeClass }}">{{ $data['grade_label'] }}</span>
            </div>
            <div class="course-grade-track" aria-hidden="true"><span style="width: {{ max(0, min(100, $data['percentage'])) }}%;"></span></div>
            <div class="course-grade-footer">
                <span class="course-grade-source">{{ $isFinal ? 'درجة نهائية مسجّلة' : 'نسبة محسوبة' }}</span>
                <span>{{ $data['evaluations']->count() }} تقييم · {{ $data['attempts']->count() }} محاولة اختبار</span>
            </div>
        </article>
        @endforeach
    </div>
    @endif

    <aside class="grades-note">
        <svg width="21" height="21" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 11v5m0-9v.1"/></svg>
        <div>
            <h2>كيف تُعرض درجاتك؟</h2>
            <p>تظهر الدرجة النهائية عند تسجيلها من المدرب. قبل ذلك، تظهر النسبة المحسوبة من التقييمات والاختبارات المتاحة. للاستفسار عن درجتك، تواصل مع مدرب المقرر.</p>
        </div>
    </aside>
</div>
@endsection
