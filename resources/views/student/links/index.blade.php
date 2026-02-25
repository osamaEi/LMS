@extends('layouts.dashboard')

@section('title', 'روابط مفيدة')

@push('styles')
<style>
    .links-hero {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #003f66 100%);
        border-radius: 20px;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 50px rgba(0,113,170,0.3);
    }
    .links-hero::before {
        content: '';
        position: absolute;
        top: -60%;
        right: -10%;
        width: 50%;
        height: 250%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.08) 0%, transparent 65%);
        pointer-events: none;
    }
    .links-hero::after {
        content: '';
        position: absolute;
        bottom: -40%;
        left: 5%;
        width: 30%;
        height: 200%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.05) 0%, transparent 65%);
        pointer-events: none;
    }
    .link-card {
        background: #fff;
        border-radius: 18px;
        border: 2px solid #f1f5f9;
        overflow: hidden;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        display: block;
    }
    .dark .link-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.08);
    }
    .link-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.12);
        border-color: transparent;
        text-decoration: none;
    }
    .link-card-inner {
        padding: 1.75rem;
    }
    .link-icon-wrap {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.25rem;
        transition: transform 0.3s ease;
    }
    .link-card:hover .link-icon-wrap {
        transform: scale(1.1) rotate(-3deg);
    }
    .link-footer {
        padding: 0.9rem 1.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top: 1px solid #f1f5f9;
        transition: background 0.2s;
    }
    .dark .link-footer {
        border-color: rgba(255,255,255,0.08);
    }
    .link-card:hover .link-footer {
        background: #f8faff;
    }
    .dark .link-card:hover .link-footer {
        background: rgba(255,255,255,0.04);
    }
    .open-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 8px;
        transition: all 0.2s;
        text-decoration: none;
    }
    .help-banner {
        background: linear-gradient(135deg, #0f766e, #0d9488);
        border-radius: 18px;
        padding: 1.75rem 2rem;
        box-shadow: 0 8px 30px rgba(13,148,136,0.25);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">

    {{-- Hero Header --}}
    <div class="links-hero mb-8">
        <div class="relative z-10">
            <a href="{{ route('student.dashboard') }}"
               style="display:inline-flex;align-items:center;gap:6px;color:rgba(255,255,255,0.7);font-size:0.875rem;text-decoration:none;margin-bottom:1rem;transition:color 0.15s"
               onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.7)'">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                العودة للوحة التحكم
            </a>

            <div style="display:flex;align-items:center;gap:16px;margin-bottom:0.5rem">
                <div style="width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,0.18);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="white">
                        <path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/>
                    </svg>
                </div>
                <div>
                    <h1 style="font-size:1.75rem;font-weight:900;color:#fff;margin:0;line-height:1.2">روابط مفيدة</h1>
                    <p style="font-size:0.9rem;color:rgba(255,255,255,0.75);margin:4px 0 0">وصول سريع لجميع الأنظمة والخدمات الأكاديمية</p>
                </div>
            </div>

            {{-- Stats row --}}
            <div style="display:flex;gap:12px;margin-top:1.25rem;flex-wrap:wrap">
                @foreach([['6', 'خدمة متاحة'], ['فوري', 'وصول سريع'], ['24/7', 'دعم مستمر']] as $stat)
                <div style="background:rgba(255,255,255,0.15);border-radius:10px;padding:6px 16px;display:flex;align-items:center;gap:8px">
                    <span style="font-size:1rem;font-weight:800;color:#fff">{{ $stat[0] }}</span>
                    <span style="font-size:0.78rem;color:rgba(255,255,255,0.8)">{{ $stat[1] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Links Grid --}}
    @php
        $linkDefs = [
            [
                'service'     => 'portal',
                'key'         => 'student_portal_url',
                'title'       => 'البوابة الإلكترونية',
                'desc'        => 'الوصول الشامل لخدمات الطالب، السجل الأكاديمي، والوثائق الرسمية',
                'badge'       => 'بوابة رسمية',
                'icon_bg'     => 'linear-gradient(135deg,#3b82f6,#2563eb)',
                'card_top'    => '#eff6ff',
                'badge_bg'    => '#dbeafe',
                'badge_color' => '#1d4ed8',
                'btn_bg'      => '#2563eb',
                'icon_svg'    => '<path d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>',
            ],
            [
                'service'     => 'library',
                'key'         => 'library_url',
                'title'       => 'المكتبة الرقمية',
                'desc'        => 'تصفح الكتب والمراجع العلمية والأبحاث الأكاديمية الرقمية',
                'badge'       => 'مكتبة',
                'icon_bg'     => 'linear-gradient(135deg,#8b5cf6,#7c3aed)',
                'card_top'    => '#f5f3ff',
                'badge_bg'    => '#ede9fe',
                'badge_color' => '#6d28d9',
                'btn_bg'      => '#7c3aed',
                'icon_svg'    => '<path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
            ],
            [
                'service'     => 'blackboard',
                'key'         => 'blackboard_url',
                'title'       => 'نظام البلاك بورد',
                'desc'        => 'منصة إدارة التعلم، الواجبات، المحاضرات، والتواصل مع الأساتذة',
                'badge'       => 'LMS',
                'icon_bg'     => 'linear-gradient(135deg,#374151,#111827)',
                'card_top'    => '#f9fafb',
                'badge_bg'    => '#f3f4f6',
                'badge_color' => '#374151',
                'btn_bg'      => '#374151',
                'icon_svg'    => '<path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
            ],
            [
                'service'     => 'calendar',
                'key'         => 'calendar_url',
                'title'       => 'التقويم الأكاديمي',
                'desc'        => 'مواعيد الاختبارات، الإجازات، الأحداث الجامعية والمناسبات الأكاديمية',
                'badge'       => 'تقويم',
                'icon_bg'     => 'linear-gradient(135deg,#0891b2,#0e7490)',
                'card_top'    => '#ecfeff',
                'badge_bg'    => '#cffafe',
                'badge_color' => '#0e7490',
                'btn_bg'      => '#0891b2',
                'icon_svg'    => '<path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
            ],
            [
                'service'     => 'support',
                'key'         => 'support_url',
                'title'       => 'الدعم الفني',
                'desc'        => 'التواصل مع فريق الدعم الفني لحل المشكلات التقنية والاستفسارات',
                'badge'       => 'دعم',
                'icon_bg'     => 'linear-gradient(135deg,#16a34a,#15803d)',
                'card_top'    => '#f0fdf4',
                'badge_bg'    => '#dcfce7',
                'badge_color' => '#15803d',
                'btn_bg'      => '#16a34a',
                'icon_svg'    => '<path d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>',
            ],
            [
                'service'     => 'schedule',
                'key'         => 'schedule_url',
                'title'       => 'الجدول الدراسي',
                'desc'        => 'عرض جدولك الدراسي الأسبوعي، أوقات المحاضرات والقاعات',
                'badge'       => 'جدول',
                'icon_bg'     => 'linear-gradient(135deg,#f59e0b,#d97706)',
                'card_top'    => '#fffbeb',
                'badge_bg'    => '#fef3c7',
                'badge_color' => '#b45309',
                'btn_bg'      => '#d97706',
                'icon_svg'    => '<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>',
            ],
        ];
    @endphp

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:20px;margin-bottom:2rem">
        @foreach($linkDefs as $def)
        @php
            $url = \App\Models\Setting::where('key', $def['key'])->value('value') ?? null;
            $hasUrl = !empty($url);
        @endphp
        <a href="{{ route('student.links.show', $def['service']) }}"
           class="link-card">

            {{-- Colored top strip --}}
            <div style="height:5px;background:{{ $def['icon_bg'] }}"></div>

            <div class="link-card-inner">
                {{-- Icon + Badge --}}
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1rem">
                    <div class="link-icon-wrap" style="background:{{ $def['icon_bg'] }};box-shadow:0 8px 24px rgba(0,0,0,0.15)">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8">
                            {!! $def['icon_svg'] !!}
                        </svg>
                    </div>
                    <span style="font-size:0.72rem;font-weight:700;padding:3px 10px;border-radius:999px;background:{{ $def['badge_bg'] }};color:{{ $def['badge_color'] }}">
                        {{ $def['badge'] }}
                    </span>
                </div>

                {{-- Title + Desc --}}
                <h3 style="font-size:1.05rem;font-weight:800;color:#111827;margin:0 0 6px;line-height:1.3" class="dark:text-white">
                    {{ $def['title'] }}
                </h3>
                <p style="font-size:0.82rem;color:#6b7280;margin:0;line-height:1.55">
                    {{ $def['desc'] }}
                </p>

                {{-- Status --}}
                <div style="display:flex;align-items:center;gap:6px;margin-top:14px">
                    @if($hasUrl)
                    <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;animation:pulse 2s infinite"></span>
                    <span style="font-size:0.75rem;color:#16a34a;font-weight:600">متاح</span>
                    @else
                    <span style="width:8px;height:8px;border-radius:50%;background:#9ca3af;display:inline-block"></span>
                    <span style="font-size:0.75rem;color:#9ca3af;font-weight:600">غير مُفعَّل</span>
                    @endif
                </div>
            </div>

            {{-- Footer --}}
            <div class="link-footer">
                <span style="font-size:0.78rem;color:#9ca3af">
                    @if($hasUrl)
                        {{ parse_url($url, PHP_URL_HOST) ?? $url }}
                    @else
                        لم يُضبط الرابط بعد
                    @endif
                </span>
                <span class="open-btn" style="background:{{ $def['btn_bg'] }};color:#fff">
                    عرض التفاصيل
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </span>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Help Banner --}}
    <div class="help-banner">
        <div style="display:flex;flex-direction:column;gap:16px" class="md:flex-row md:items-center md:justify-between">
            <div style="display:flex;align-items:center;gap:14px">
                <div style="width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="white">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size:1.05rem;font-weight:800;color:#fff;margin:0">هل تحتاج مساعدة؟</h3>
                    <p style="font-size:0.83rem;color:rgba(255,255,255,0.8);margin:4px 0 0">فريق الدعم الفني جاهز لمساعدتك على مدار الساعة</p>
                </div>
            </div>
            <a href="{{ route('student.tickets.create') }}"
               style="display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:#fff;color:#0d9488;font-size:0.875rem;font-weight:700;border-radius:12px;text-decoration:none;transition:all 0.2s;white-space:nowrap;flex-shrink:0"
               onmouseover="this.style.background='#f0fdfa'" onmouseout="this.style.background='#fff'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                تواصل مع الدعم
            </a>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}
</style>
@endsection
