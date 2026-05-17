@extends('layouts.front')

@section('title', $pageTitle . ' — معهد الارتقاء العالي للتدريب')

@section('styles')
<style>
    .course-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,.08);
        transition: transform .3s, box-shadow .3s;
        height: 100%;
    }
    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,.12);
    }
    .course-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
    .course-card .card-body { padding: 1.5rem; }
    .course-card .card-title { font-weight: bold; margin-bottom: .5rem; }
    .course-card .card-text  { color: rgba(56,66,80,1); font-size: .9rem; line-height: 1.6; }
    .course-card .course-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #eee;
    }
    .course-card .course-price { color: var(--main-color); font-weight: bold; }

    .course-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: .8rem;
        font-weight: 700;
    }
    [dir="ltr"] .course-badge { right: auto; left: 15px; }

    .course-card-wrapper { position: relative; }

    /* Type accent strip under hero */
    .type-strip {
        height: 4px;
    }
    .strip-developmental { background: linear-gradient(90deg,#2563eb,#60a5fa); }
    .strip-qualifying     { background: linear-gradient(90deg,#7c3aed,#a78bfa); }
    .strip-english        { background: linear-gradient(90deg,#0071AA,#0ea5e9); }
    .strip-training       { background: linear-gradient(90deg,#059669,#34d399); }

    .courses-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        max-width: 1300px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .empty-box {
        text-align: center;
        padding: 5rem 1rem;
        color: #94a3b8;
    }
    .empty-box i { font-size: 3rem; margin-bottom: 1rem; display: block; }

    @media (max-width: 768px) {
        .courses-section { padding: 1.5rem 1rem; }
        .courses-container { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')

{{-- Type strip --}}
<div class="type-strip strip-{{ $pageType }}"></div>

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
    @if($pageType === 'developmental')
        <p>دورات تطويرية مصممة لرفع كفاءتك المهنية وتطوير مهاراتك في مجالات متخصصة.</p>
    @elseif($pageType === 'qualifying')
        <p>دورات تأهيلية تمنحك المؤهلات والشهادات اللازمة للانطلاق في مسيرتك المهنية.</p>
    @else
        <p>برامج تدريبية متخصصة لتنمية المهارات المهنية والتقنية في مختلف المجالات.</p>
    @endif
</section>

{{-- Courses --}}
<section class="courses-section mb-5">
    <div class="head" style="text-align:center;margin-bottom:2rem;">
        @if($pageType === 'developmental')
            <p class="st-p" style="display:inline-block;margin-bottom:.75rem;">
                <i class="bi bi-graph-up-arrow"></i> تطويري
            </p>
            <h2 style="margin:.5rem 0;">اختر دورتك التطويرية</h2>
        @elseif($pageType === 'qualifying')
            <p class="st-p" style="display:inline-block;margin-bottom:.75rem;">
                <i class="bi bi-mortarboard-fill"></i> تأهيلي
            </p>
            <h2 style="margin:.5rem 0;">اختر دورتك التأهيلية</h2>
        @endif
        <p style="max-width:700px;margin:0 auto;line-height:1.8;color:rgba(56,66,80,1);font-size:.95rem;">
            {{ $programs->count() }} دورة متاحة — سجّل الآن واستفد من برامجنا المعتمدة.
        </p>
    </div>

    @if($programs->isEmpty())
        <div class="empty-box">
            <i class="bi bi-journal-x"></i>
            <h4>لا توجد دورات متاحة حالياً</h4>
            <p>تابعنا للاطلاع على أحدث البرامج.</p>
        </div>
    @else
    @php
        $courseImages = [
            'lms-photos/2.png','lms-photos/8.png','lms-photos/5.png',
            'lms2-photo/14.png','lms-photos/10.png','lms2-photo/6.png',
        ];
        $badgeColor = $pageType === 'developmental' ? '#2563eb' : ($pageType === 'qualifying' ? '#7c3aed' : '#059669');
        $badgeLabel = $pageType === 'developmental' ? 'تطويري' : ($pageType === 'qualifying' ? 'تأهيلي' : 'تدريبي');
    @endphp
    <div class="courses-container">
        @foreach($programs as $program)
        <div class="course-card-wrapper">
            <div class="course-card">
                <img src="{{ $program->image ? asset('storage/'.$program->image) : asset($courseImages[$loop->index % count($courseImages)]) }}"
                     alt="{{ $program->name_ar }}" />
                <span class="course-badge" style="background:{{ $badgeColor }};">{{ $badgeLabel }}</span>
                <div class="card-body">
                    <h5 class="card-title">{{ $program->name_ar }}</h5>
                    <p class="card-text">{{ Str::limit($program->description_ar, 100) }}</p>
                    <div class="course-meta">
                        @if($program->duration_months)
                            <span><i class="bi bi-clock"></i> {{ $program->duration_months }} شهر</span>
                        @endif
                        @if($program->price)
                            <span class="course-price">{{ number_format($program->price,0) }} <x-riyal /></span>
                        @endif
                    </div>
                    <a href="{{ route('register') }}" class="full-btn mt-3 w-100 d-block text-center">سجّل الآن</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</section>

{{-- Bottom CTA --}}
<section style="background:linear-gradient(135deg,#004d7a 0%,#0071aa 100%);padding:3rem clamp(1rem,3vw,3rem);color:white;">
    <div class="row align-items-center">
        <div class="col-lg-7">
            <h2 style="font-weight:800;margin-bottom:1rem;">هل أنت مستعد لبدء رحلتك التدريبية ؟</h2>
            <p style="opacity:.9;line-height:1.8;margin-bottom:1.5rem;">
                سجّل الآن في {{ $pageTitle }} واحصل على شهادة معتمدة تُعزّز مسيرتك المهنية.
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

@endsection
