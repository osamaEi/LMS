@extends('layouts.dashboard')

@section('title', $title)

@push('styles')
<style>
    .service-hero {
        border-radius: 20px;
        padding: 2.25rem 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 50px rgba(0,0,0,0.2);
    }
    .service-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 45%;
        height: 220%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.1) 0%, transparent 65%);
        pointer-events: none;
    }
    .feature-card {
        background: #fff;
        border-radius: 14px;
        border: 2px solid #f1f5f9;
        padding: 1.25rem;
        display: flex;
        align-items: flex-start;
        gap: 14px;
        transition: all 0.25s;
    }
    .dark .feature-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.08);
    }
    .feature-card:hover {
        border-color: transparent;
        box-shadow: 0 8px 30px rgba(0,0,0,0.09);
        transform: translateY(-3px);
    }
    .step-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 1rem 1.25rem;
        background: #fff;
        border-radius: 12px;
        border: 1.5px solid #f1f5f9;
    }
    .dark .step-item {
        background: #1e293b;
        border-color: rgba(255,255,255,0.08);
    }
    .cta-box {
        border-radius: 18px;
        padding: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .cta-box::before {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(255,255,255,0.05);
        border-radius: 18px;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 max-w-5xl">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:1.25rem;font-size:0.82rem">
        <a href="{{ route('student.dashboard') }}" style="color:#6b7280;text-decoration:none" class="dark:text-gray-400">لوحة التحكم</a>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        <a href="{{ route('student.links') }}" style="color:#6b7280;text-decoration:none" class="dark:text-gray-400">روابط مفيدة</a>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        <span style="color:#374151;font-weight:600" class="dark:text-white">{{ $title }}</span>
    </div>

    {{-- Hero --}}
    <div class="service-hero mb-8" style="background: {{ $icon_bg }}">
        <div class="relative z-10">
            <div style="display:flex;align-items:center;gap:18px">
                <div style="width:60px;height:60px;border-radius:16px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8">
                        @switch($service)
                            @case('portal')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            @break
                            @case('library')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            @break
                            @case('blackboard')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            @break
                            @case('calendar')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            @break
                            @case('support')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                            @break
                            @case('schedule')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            @break
                        @endswitch
                    </svg>
                </div>
                <div>
                    <p style="font-size:0.8rem;color:rgba(255,255,255,0.7);margin:0 0 4px">{{ $subtitle }}</p>
                    <h1 style="font-size:2rem;font-weight:900;color:#fff;margin:0;line-height:1.1">{{ $title }}</h1>
                </div>
            </div>
            <p style="font-size:0.95rem;color:rgba(255,255,255,0.85);margin:1.25rem 0 0;max-width:600px;line-height:1.6">{{ $description }}</p>

            {{-- CTA button in hero --}}
            @if($url)
            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
               style="display:inline-flex;align-items:center;gap:8px;margin-top:1.5rem;padding:11px 24px;background:#fff;border-radius:12px;font-size:0.9rem;font-weight:800;text-decoration:none;transition:all 0.2s;color:transparent;background-clip:text;-webkit-background-clip:text;"
               onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
                <span style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:rgba(255,255,255,1);border-radius:12px;font-size:0.9rem;font-weight:800;color:#111;text-decoration:none">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    فتح {{ $title }}
                </span>
            </a>
            @else
            <div style="display:inline-flex;align-items:center;gap:8px;margin-top:1.5rem;padding:11px 20px;background:rgba(255,255,255,0.15);border-radius:12px;font-size:0.875rem;color:rgba(255,255,255,0.8)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                لم يُضبط الرابط بعد — تواصل مع الإدارة
            </div>
            @endif
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px" class="lg:grid-cols-2">

        {{-- Features --}}
        <div>
            <h2 style="font-size:1.1rem;font-weight:800;color:#111827;margin:0 0 16px" class="dark:text-white">
                ما يمكنك فعله
            </h2>
            <div style="display:grid;gap:12px">
                @foreach($features as $feat)
                <div class="feature-card">
                    <div style="width:42px;height:42px;border-radius:12px;background:{{ $badge_bg }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $badge_color }}" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $feat['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:0.9rem;font-weight:700;color:#111827;margin:0" class="dark:text-white">{{ $feat['title'] }}</p>
                        <p style="font-size:0.8rem;color:#6b7280;margin:3px 0 0">{{ $feat['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Steps + CTA --}}
        <div style="display:flex;flex-direction:column;gap:20px">
            {{-- Steps --}}
            <div>
                <h2 style="font-size:1.1rem;font-weight:800;color:#111827;margin:0 0 16px" class="dark:text-white">
                    كيف تستخدمه؟
                </h2>
                <div style="display:flex;flex-direction:column;gap:10px">
                    @foreach($steps as $i => $step)
                    <div class="step-item">
                        <div style="width:32px;height:32px;border-radius:50%;background:{{ $icon_bg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:0.85rem;font-weight:800;color:#fff">
                            {{ $i + 1 }}
                        </div>
                        <span style="font-size:0.875rem;font-weight:600;color:#374151" class="dark:text-gray-200">{{ $step }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Big CTA --}}
            <div class="cta-box" style="background:{{ $icon_bg }}">
                <div class="relative z-10">
                    <div style="width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="white">
                            <path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z"/>
                        </svg>
                    </div>
                    <h3 style="font-size:1.05rem;font-weight:800;color:#fff;margin:0 0 6px">جاهز للبدء؟</h3>
                    <p style="font-size:0.82rem;color:rgba(255,255,255,0.8);margin:0 0 18px">افتح {{ $title }} مباشرة الآن</p>
                    @if($url)
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                       style="display:inline-flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px;background:#fff;border-radius:12px;font-size:0.9rem;font-weight:800;color:#111;text-decoration:none;transition:all 0.2s"
                       onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        فتح {{ $title }}
                    </a>
                    @else
                    <div style="padding:12px;background:rgba(255,255,255,0.15);border-radius:12px;font-size:0.83rem;color:rgba(255,255,255,0.85)">
                        الرابط غير مُضبوط بعد
                    </div>
                    @endif
                </div>
            </div>

            {{-- Internal support ticket --}}
            <div style="background:#f8fafc;border-radius:14px;border:1.5px solid #e2e8f0;padding:1.1rem 1.25rem;display:flex;align-items:center;gap:12px" class="dark:bg-boxdark dark:border-strokedark">
                <div style="width:38px;height:38px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div style="flex:1">
                    <p style="font-size:0.82rem;font-weight:700;color:#374151;margin:0" class="dark:text-white">تحتاج مساعدة؟</p>
                    <p style="font-size:0.75rem;color:#9ca3af;margin:2px 0 0">أرسل تذكرة دعم داخلية</p>
                </div>
                <a href="{{ route('student.tickets.create') }}"
                   style="font-size:0.78rem;font-weight:700;color:#0071AA;text-decoration:none;white-space:nowrap">
                    تواصل ←
                </a>
            </div>
        </div>
    </div>

    {{-- Back to links --}}
    <div style="margin-top:2rem;text-align:center">
        <a href="{{ route('student.links') }}"
           style="display:inline-flex;align-items:center;gap:6px;font-size:0.85rem;font-weight:600;color:#6b7280;text-decoration:none;transition:color 0.15s"
           onmouseover="this.style.color='#0071AA'" onmouseout="this.style.color='#6b7280'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة لجميع الروابط
        </a>
    </div>
</div>
@endsection
