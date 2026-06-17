@extends('layouts.dashboard')

@section('title', __('Dashboard Overview'))

@section('content')
<div class="mx-auto max-w-screen-xl p-4 md:p-6 2xl:p-10">

    {{-- ══════════════════════════════════════
         Hero Header
    ══════════════════════════════════════ --}}
    <div class="relative mb-8 overflow-hidden rounded-2xl px-8 py-7 text-white shadow-xl"
         style="background: linear-gradient(135deg, #1e3a5f 0%, #0071AA 55%, #0ea5e9 100%);">
        <div class="pointer-events-none absolute -top-20 -right-20 h-64 w-64 rounded-full" style="background:rgba(255,255,255,.05)"></div>
        <div class="pointer-events-none absolute -bottom-12 -left-12 h-48 w-48 rounded-full" style="background:rgba(255,255,255,.05)"></div>

        <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="mb-1 text-sm font-medium" style="color:rgba(255,255,255,.7)">{{ now()->translatedFormat('l، d F Y') }}</p>
                <h1 class="text-2xl font-bold tracking-tight">
                    مرحباً {{ auth()->user()->gender === 'female' ? 'بالمدربة' : 'بالمدرب' }} {{ auth()->user()->name }}
                </h1>
                <p class="mt-1 text-sm" style="color:rgba(255,255,255,.7)">إدارة دوراتك والمتدربون  بكل سهولة من لوحة التحكم</p>
            </div>
            <div class="flex gap-3">
             
                <a href="{{ route('teacher.my-subjects.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition"
                   style="background:rgba(255,255,255,.15)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                    </svg>
                    {{ __('My Subjects') }}
                </a>
            </div>
        </div>
    </div>



    {{-- ══════════════════════════════════════
         Sessions Panel (upcoming / live / past)
    ══════════════════════════════════════ --}}
    @php
        $liveCount     = $liveSessions->count();
        $upcomingCount = $upcomingSessions->count();
        $pastCount     = $pastSessions->count();
        $defaultTab    = $liveCount > 0 ? 'live' : 'upcoming';
    @endphp

    {{-- Link modal — رابط واحد فقط للطلاب --}}
    <div id="linkModal" style="display:none;position:fixed;inset:0;z-index:50;background:rgba(0,0,0,.45);align-items:center;justify-content:center;padding:1rem;">
        <div style="width:100%;max-width:420px;background:#fff;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:fadeInUp .2s ease;" class="dark:bg-boxdark">
            {{-- Header --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:1.1rem 1.5rem;border-bottom:1px solid #f1f5f9;">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <div style="width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,#0071AA,#004d77);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="white"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
                    </div>
                    <div>
                        <p style="font-size:.88rem;font-weight:700;color:#111827;margin:0;">رابط المحاضرة للطلاب</p>
                        <p id="modalSessionTitle" style="font-size:.72rem;color:#9ca3af;margin:0;"></p>
                    </div>
                </div>
                <button onclick="closeLinkModal()" style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:.25rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                </button>
            </div>
            {{-- Body --}}
            <form id="linkModalForm" method="POST" style="padding:1.25rem 1.5rem;">
                @csrf
                @method('PATCH')

                <label style="display:block;font-size:.78rem;font-weight:600;color:#374151;margin-bottom:.6rem;">
                    رابط الانضمام
                    <span style="font-weight:400;color:#10b981;">— سيظهر للطلاب مباشرة</span>
                </label>
                <input type="url" name="zoom_join_url" id="modalJoinUrl"
                       placeholder="https://zoom.us/j/123456789"
                       style="width:100%;border:2px solid #d1fae5;border-radius:12px;padding:.75rem 1rem;font-size:.875rem;color:#111827;background:#f0fdf4;box-sizing:border-box;outline:none;transition:border-color .15s;"
                       onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#d1fae5'">

                <p style="margin:.6rem 0 1.25rem;font-size:.72rem;color:#6b7280;display:flex;align-items:center;gap:.4rem;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="#10b981"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                    الطلاب سيرون هذا الرابط في صفحة المادة تحت المحاضرة
                </p>

                <div style="display:flex;gap:.6rem;justify-content:flex-end;">
                    <button type="button" onclick="closeLinkModal()"
                            style="padding:.55rem 1.1rem;border-radius:10px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;font-size:.8rem;font-weight:600;cursor:pointer;">
                        إلغاء
                    </button>
                    <button type="submit"
                            style="padding:.55rem 1.25rem;border-radius:10px;border:none;background:linear-gradient(135deg,#0071AA,#004d77);color:#fff;font-size:.8rem;font-weight:700;cursor:pointer;box-shadow:0 2px 8px rgba(0,113,170,.3);">
                        حفظ الرابط
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="mb-8 overflow-hidden rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark"
         x-data="{ tab: '{{ $defaultTab }}' }">

        {{-- Tab header --}}
        <div class="flex items-center justify-between border-b border-stroke px-6 py-4 dark:border-strokedark flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:linear-gradient(135deg,#0071AA,#004d77)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                        <path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-black dark:text-white">المحاضرات</h3>
                    <p class="text-xs text-gray-400">{{ $upcomingCount + $liveCount + $pastCount }} محاضرة إجمالاً</p>
                </div>
            </div>
            {{-- Filter tabs --}}
            <div style="display:flex;gap:.375rem;background:#f3f4f6;padding:.375rem;border-radius:14px;">
                @if($liveCount > 0)
                <button @click="tab='live'"
                        :style="tab==='live' ? 'background:#ef4444;color:#fff;box-shadow:0 2px 8px rgba(239,68,68,.4);' : 'color:#6b7280;background:transparent;'"
                        style="padding:.4rem .9rem;border-radius:10px;font-size:.8rem;font-weight:600;border:none;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:.4rem;">
                    <span class="animate-pulse" style="width:7px;height:7px;background:currentColor;border-radius:50%;display:inline-block;opacity:.8;"></span>
                    الحالية
                    <span style="background:rgba(239,68,68,.15);color:#ef4444;border-radius:6px;padding:.1rem .45rem;font-size:.7rem;font-weight:700;">{{ $liveCount }}</span>
                </button>
                @endif
                <!-- <button @click="tab='upcoming'"
                        :style="tab==='upcoming' ? 'background:#0071AA;color:#fff;box-shadow:0 2px 8px rgba(0,113,170,.3);' : 'color:#6b7280;background:transparent;'"
                        style="padding:.4rem .9rem;border-radius:10px;font-size:.8rem;font-weight:600;border:none;cursor:pointer;transition:all .2s;">
                    القادمة
                    <span style="margin-right:.3rem;background:#e0f2fe;color:#0071AA;border-radius:6px;padding:.1rem .45rem;font-size:.7rem;font-weight:700;">{{ $upcomingCount }}</span>
                </button>
                <button @click="tab='past'"
                        :style="tab==='past' ? 'background:#0071AA;color:#fff;box-shadow:0 2px 8px rgba(0,113,170,.3);' : 'color:#6b7280;background:transparent;'"
                        style="padding:.4rem .9rem;border-radius:10px;font-size:.8rem;font-weight:600;border:none;cursor:pointer;transition:all .2s;">
                    السابقة
                    <span style="margin-right:.3rem;background:#f3f4f6;color:#6b7280;border-radius:6px;padding:.1rem .45rem;font-size:.7rem;font-weight:700;">{{ $pastCount }}</span>
                </button> -->
            </div>
        </div>

        {{-- LIVE --}}
        @if($liveCount > 0)
        <div x-show="tab==='live'" x-cloak>
            <div class="divide-y divide-stroke dark:divide-strokedark">
                @foreach($liveSessions as $session)
                @php $dt = \Carbon\Carbon::parse($session->scheduled_at); @endphp
                <div class="flex items-center gap-4 px-6 py-4" style="border-right:4px solid #ef4444;background:linear-gradient(90deg,rgba(239,68,68,.04) 0%,transparent 60%);">
                    {{-- Live badge --}}
                    <div class="flex-shrink-0 flex flex-col items-center gap-1">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl text-white text-xs font-black" style="background:linear-gradient(135deg,#ef4444,#dc2626);">
                            <span class="animate-pulse">●</span>
                        </div>
                        <span class="text-[10px] font-bold text-red-500">LIVE</span>
                    </div>
                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm text-black dark:text-white truncate">{{ $session->title }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $session->subject->name_ar ?? $session->subject->name ?? '—' }}</p>
                        @php
                            $ldt    = \Carbon\Carbon::parse($session->scheduled_at);
                            $lEnd   = $ldt->copy()->addMinutes($session->duration_minutes ?? 60);
                            $lPer   = $ldt->hour < 12 ? 'صباحية' : 'مسائية';
                        @endphp
                        <p class="text-xs text-red-400 mt-0.5 font-medium">
                            من {{ $ldt->format('h:i') }} إلى {{ $lEnd->format('h:i') }}
                            <span style="font-size:.6rem;font-weight:700;padding:.05rem .35rem;border-radius:20px;background:{{ $ldt->hour < 12 ? '#fef9c3' : '#e0e7ff' }};color:{{ $ldt->hour < 12 ? '#a16207' : '#4338ca' }};">{{ $lPer }}</span>
                        </p>
                        @if($session->zoom_join_url)
                        <div class="mt-1.5 flex items-center gap-1.5">
                            <svg class="text-green-500 flex-shrink-0" width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
                            <span class="text-xs text-green-600 truncate font-medium">رابط الطلاب متاح</span>
                        </div>
                        @endif
                    </div>
                    {{-- Actions --}}
                    <div class="flex-shrink-0 flex flex-col gap-1.5">
                        @if($session->zoom_join_url)
                        <a href="{{ $session->zoom_join_url }}" target="_blank"
                           class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-bold text-white"
                           style="background:linear-gradient(135deg,#ef4444,#dc2626)">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
                            متابعة
                        </a>
                        @endif
                        <button class="open-link-modal inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-semibold"
                                style="{{ $session->zoom_join_url ? 'background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0;' : 'background:#fffbeb;color:#d97706;border:1px solid #fde68a;' }}"
                                data-id="{{ $session->id }}" data-title="{{ $session->title }}" data-join="{{ $session->zoom_join_url ?? '' }}">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
                            {{ $session->zoom_join_url ? '✓ رابط الطلاب' : '+ رابط الطلاب' }}
                        </button>
                        @if($session->subject_id)
                        <a href="{{ route('teacher.my-subjects.sessions.attendance', [$session->subject_id, $session->id]) }}"
                           class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-semibold"
                           style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                            الحضور
                        </a>
                        @elseif($session->program_id)
                        <a href="{{ route('teacher.my-courses.sessions.attendance', [$session->program_id, $session->id]) }}"
                           class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-xs font-semibold"
                           style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                            الحضور
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- UPCOMING — weekly schedule calendar (same as /teacher/schedule) --}}
        <div x-show="tab==='upcoming'" x-cloak>
            <div style="padding:1rem 1.25rem;">
                @include('teacher.partials.weekly-calendar')
            </div>
        </div>

        {{-- PAST --}}
        <div x-show="tab==='past'" x-cloak>
            @if($pastSessions->isEmpty())
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:3.5rem 1rem;text-align:center;">
                <p style="font-size:.875rem;color:#9ca3af;margin:0;">لا توجد محاضرات سابقة</p>
            </div>
            @else
            <div style="padding:1rem 1.25rem;display:flex;flex-direction:column;gap:.5rem;">
                @foreach($pastSessions as $session)
                @php
                    $dt     = \Carbon\Carbon::parse($session->scheduled_at);
                    $ended  = $session->ended_at ? \Carbon\Carbon::parse($session->ended_at) : null;
                    $typeIcon = match($session->type ?? '') {
                        'live_zoom'      => '📹',
                        'recorded_video' => '🎬',
                        'in_person'      => '🏫',
                        default          => '📖',
                    };
                    $isEnded = !empty($session->ended_at);
                @endphp
                <div style="display:flex;align-items:center;gap:.875rem;padding:.75rem 1rem;border-radius:14px;border:1px solid #f1f5f9;background:#fafafa;opacity:.85;transition:opacity .15s,box-shadow .15s;"
                     onmouseenter="this.style.opacity='1';this.style.boxShadow='0 2px 10px rgba(0,0,0,.06)'"
                     onmouseleave="this.style.opacity='.85';this.style.boxShadow='none'">

                    {{-- Date pill --}}
                    <div style="flex-shrink:0;width:46px;text-align:center;border-radius:12px;padding:.45rem .2rem;background:#e5e7eb;color:#6b7280;">
                        <div style="font-size:1.05rem;font-weight:900;line-height:1;">{{ $dt->format('d') }}</div>
                        <div style="font-size:.6rem;font-weight:600;text-transform:uppercase;opacity:.7;margin-top:.1rem;">{{ $dt->translatedFormat('M') }}</div>
                    </div>

                    {{-- Info --}}
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;flex-wrap:wrap;gap:.4rem;margin-bottom:.15rem;">
                            <span style="font-size:.8rem;font-weight:600;color:#374151;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;">{{ $typeIcon }} {{ $session->title }}</span>
                            @if($isEnded)
                                <span style="background:#dcfce7;color:#16a34a;font-size:.62rem;font-weight:700;padding:.1rem .45rem;border-radius:20px;">✓ منتهية</span>
                            @endif
                        </div>
                        <div style="font-size:.7rem;color:#9ca3af;">
                            @php
                                $pSubName = $session->subject->name_ar ?? $session->subject->name ?? null;
                                $pProgName = $session->subject?->program->name_ar
                                           ?? $session->subject?->program->name
                                           ?? $session->program?->name_ar
                                           ?? $session->program?->name
                                           ?? null;
                                $pLabel = $pSubName ?? $pProgName ?? '—';
                                $pSub   = $pSubName ? $pProgName : null;
                            @endphp
                            {{ $pLabel }}
                            @if($pSub)
                                · <span style="color:#0071AA;font-weight:600;">{{ $pSub }}</span>
                            @endif
                            · {{ $dt->format('d/m/Y') }}
                            @php
                                $pEnd    = $ended ?? $dt->copy()->addMinutes($session->duration_minutes ?? 60);
                                $pPeriod = $dt->hour < 12 ? 'صباحية' : 'مسائية';
                            @endphp
                            · {{ $dt->format('h:i') }}–{{ $pEnd->format('h:i') }}
                            <span style="font-size:.58rem;font-weight:700;padding:.05rem .3rem;border-radius:20px;background:{{ $dt->hour < 12 ? '#fef9c3' : '#e0e7ff' }};color:{{ $dt->hour < 12 ? '#a16207' : '#4338ca' }};">{{ $pPeriod }}</span>
                        </div>
                    </div>

                    {{-- Action --}}
                    <div style="flex-shrink:0;display:flex;flex-direction:column;gap:.35rem;">
                        <a href="{{ route('teacher.sessions.show', $session->id) }}"
                           style="display:inline-flex;align-items:center;gap:.3rem;padding:.4rem .8rem;border-radius:10px;background:#f1f5f9;color:#374151;font-size:.72rem;font-weight:600;text-decoration:none;border:1px solid #e2e8f0;"
                           onmouseenter="this.style.background='#0071AA';this.style.color='#fff';this.style.borderColor='#0071AA'"
                           onmouseleave="this.style.background='#f1f5f9';this.style.color='#374151';this.style.borderColor='#e2e8f0'">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
                            عرض
                        </a>
                        @if($session->subject_id)
                        <a href="{{ route('teacher.my-subjects.sessions.attendance', [$session->subject_id, $session->id]) }}"
                           style="display:inline-flex;align-items:center;gap:.3rem;padding:.4rem .8rem;border-radius:10px;background:#f0fdf4;color:#16a34a;font-size:.72rem;font-weight:600;text-decoration:none;border:1px solid #bbf7d0;"
                           onmouseenter="this.style.background='#16a34a';this.style.color='#fff';this.style.borderColor='#16a34a'"
                           onmouseleave="this.style.background='#f0fdf4';this.style.color='#16a34a';this.style.borderColor='#bbf7d0'">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                            الحضور
                        </a>
                        @elseif($session->program_id)
                        <a href="{{ route('teacher.my-courses.sessions.attendance', [$session->program_id, $session->id]) }}"
                           style="display:inline-flex;align-items:center;gap:.3rem;padding:.4rem .8rem;border-radius:10px;background:#f0fdf4;color:#16a34a;font-size:.72rem;font-weight:600;text-decoration:none;border:1px solid #bbf7d0;"
                           onmouseenter="this.style.background='#16a34a';this.style.color='#fff';this.style.borderColor='#16a34a'"
                           onmouseleave="this.style.background='#f0fdf4';this.style.color='#16a34a';this.style.borderColor='#bbf7d0'">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                            الحضور
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    function openLinkModal(id, title, joinUrl) {
        var modal = document.getElementById('linkModal');
        document.getElementById('modalSessionTitle').textContent = title;
        document.getElementById('modalJoinUrl').value = joinUrl || '';
        document.getElementById('linkModalForm').action = '/teacher/sessions/' + id + '/join-url';
        modal.style.display = 'flex';
        setTimeout(function(){ document.getElementById('modalJoinUrl').focus(); }, 100);
    }
    function closeLinkModal() {
        document.getElementById('linkModal').style.display = 'none';
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.open-link-modal');
            if (!btn) return;
            openLinkModal(btn.dataset.id, btn.dataset.title, btn.dataset.join);
        });
        document.getElementById('linkModal').addEventListener('click', function(e) {
            if (e.target === this) closeLinkModal();
        });
    });
    </script>
    <style>
    @keyframes fadeInUp {
        from { opacity:0; transform:translateY(16px); }
        to   { opacity:1; transform:translateY(0); }
    }
    [x-cloak] { display:none !important; }

    /* ── Sessions cards ─────────────────────── */
    .sess-card {
        display:flex;align-items:center;gap:.875rem;
        padding:.875rem 1rem;border-radius:16px;
        border:1.5px solid #f1f5f9;background:#fafafa;
        transition:box-shadow .15s;
    }
    .sess-card.sess-today {
        border-color:#bae6fd;
        background:linear-gradient(135deg,#f0f9ff,#e0f2fe);
    }
    .sess-date {
        flex-shrink:0;width:52px;text-align:center;
        border-radius:14px;padding:.5rem .25rem;
        background:#e2e8f0;color:#64748b;
    }
    .sess-date.sess-date-today {
        background:linear-gradient(135deg,#0071AA,#004d77);color:#fff;
    }
    .sess-date-day   { font-size:1.2rem;font-weight:900;line-height:1; }
    .sess-date-month { font-size:.65rem;font-weight:600;text-transform:uppercase;opacity:.8;margin-top:.1rem; }
    .sess-date-time  { font-size:.7rem;font-weight:700;margin-top:.15rem; }
    .sess-info       { flex:1;min-width:0; }
    .sess-title-row  { display:flex;align-items:center;flex-wrap:wrap;gap:.4rem;margin-bottom:.2rem; }
    .sess-title      { font-size:.82rem;font-weight:700;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px; }
    .sess-badge      { font-size:.65rem;font-weight:700;padding:.15rem .5rem;border-radius:20px; }
    .badge-today     { background:#0071AA;color:#fff; }
    .badge-tomorrow  { background:#e0f2fe;color:#0369a1; }
    .sess-subject    { font-size:.72rem;color:#6b7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
    .sess-program    { font-size:.65rem;color:#0071AA;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:.1rem; }
    .sess-time-rel   { margin-top:.3rem;font-size:.68rem;color:#9ca3af; }
    .sess-actions    { flex-shrink:0;display:flex;flex-direction:column;gap:.4rem;align-items:flex-end; }
    .sess-start-btn  {
        display:inline-flex;align-items:center;gap:.3rem;padding:.4rem .8rem;
        border-radius:10px;background:linear-gradient(135deg,#ef4444,#dc2626);
        color:#fff;font-size:.72rem;font-weight:700;text-decoration:none;
    }
    .sess-link-btn {
        display:inline-flex;align-items:center;gap:.3rem;padding:.4rem .8rem;
        border-radius:10px;font-size:.72rem;font-weight:600;cursor:pointer;
    }
    .sess-link-btn.has-link { border:1.5px solid #86efac;background:#f0fdf4;color:#16a34a; }
    .sess-link-btn.no-link  { border:1.5px solid #fde68a;background:#fffbeb;color:#d97706; }
    </style>
    @endpush

    {{-- ══════════════════════════════════════
         Main Grid
    ══════════════════════════════════════ --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- LEFT — Upcoming Sessions + Subjects (2/3) --}}
        <div class="space-y-6 lg:col-span-2">

     

    

            {{-- Attendance Section --}}
            <div class="overflow-hidden rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="flex items-center justify-between border-b border-stroke px-6 py-4 dark:border-strokedark">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:linear-gradient(135deg,#10b981,#059669)">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-black dark:text-white">{{ __('Student Attendance') }}</h3>
                            <p class="text-xs text-gray-400">{{ __('Recent sessions') }}</p>
                        </div>
                    </div>
                </div>

                @if($recentSessionsWithAttendance->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-meta-4">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" class="text-gray-300">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500">{{ __('No completed sessions yet') }}</p>
                </div>
                @else
                <div class="divide-y divide-stroke dark:divide-strokedark">
                    @foreach($recentSessionsWithAttendance as $session)
                    @php
                        $attendedCount = $session->attendances->count();
                        $totalEnrolled = $session->enrolled_count;
                        $rate = $totalEnrolled > 0 ? round(($attendedCount / $totalEnrolled) * 100) : 0;
                        $rateColor = $rate >= 75 ? '#10b981' : ($rate >= 50 ? '#f59e0b' : '#ef4444');
                    @endphp
                    <div class="px-6 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-semibold text-sm text-black dark:text-white truncate">{{ $session->title }}</span>
                                    <span class="text-xs text-gray-400">{{ $session->subject->name_ar ?? $session->subject->name ?? '—' }}</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $session->scheduled_at ? \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d H:i') : '—' }}
                                </p>
                                {{-- Attendance bar --}}
                                <div class="mt-2 flex items-center gap-2">
                                    <div class="flex-1 h-1.5 rounded-full bg-gray-100 dark:bg-meta-4 overflow-hidden">
                                        <div class="h-full rounded-full transition-all" style="width:{{ $rate }}%; background:{{ $rateColor }}"></div>
                                    </div>
                                    <span class="text-xs font-bold flex-shrink-0" style="color:{{ $rateColor }}">
                                        {{ $attendedCount }}/{{ $totalEnrolled }}
                                    </span>
                                </div>
                                {{-- Attended students avatars --}}
                                @if($session->attendances->isNotEmpty())
                                <div class="mt-2 flex items-center gap-1 flex-wrap">
                                    @foreach($session->attendances->take(5) as $att)
                                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-white text-xs font-bold ring-2 ring-white dark:ring-boxdark"
                                          style="background:linear-gradient(135deg,#0071AA,#005a88)"
                                          title="{{ $att->student->name ?? '' }}">
                                        {{ mb_substr($att->student->name ?? '؟', 0, 1) }}
                                    </span>
                                    @endforeach
                                    @if($session->attendances->count() > 5)
                                    <span class="text-xs text-gray-400">+{{ $session->attendances->count() - 5 }} آخرون</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                            {{-- Actions --}}
                            <div class="flex flex-col gap-1.5 flex-shrink-0">
                                <a href="{{ route('teacher.my-subjects.sessions.attendance', [$session->subject_id, $session->id]) }}"
                                   class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-white transition"
                                   style="background:linear-gradient(135deg,#0071AA,#005a88)">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                    </svg>
                                    {{ __('View') }}
                                </a>
                                <a href="{{ route('teacher.my-subjects.sessions.attendance', [$session->subject_id, $session->id]) }}#add-attendance"
                                   class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1.5 text-xs font-semibold transition"
                                   style="background:rgba(16,185,129,.1); color:#059669;">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                                    </svg>
                                    {{ __('Add Attendance') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

        </div>

        {{-- RIGHT — Sidebar (1/3) --}}
        <div class="space-y-6">

            {{-- Mini Calendar --}}
            <div class="overflow-hidden rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="flex items-center gap-3 border-b border-stroke px-5 py-4 dark:border-strokedark">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:#0071AA">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zM7 12h5v5H7z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-black dark:text-white">{{ __('Calendar') }}</h3>
                </div>
                <div class="p-4">
                    @php
                        $today          = now();
                        $currentMonth   = $today->month;
                        $currentYear    = $today->year;
                        $firstDay       = $today->copy()->startOfMonth();
                        $daysInMonth    = $today->copy()->endOfMonth()->day;
                        $startDow       = $firstDay->dayOfWeek;
                        $sessionDates   = $upcomingSessions->pluck('scheduled_at')->map(fn($d) => $d ? $d->format('Y-m-d') : null)->filter()->toArray();
                        $arabicMonths   = ['','يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
                        $arabicDays     = ['ح','ن','ث','ر','خ','ج','س'];
                    @endphp

                    <div class="mb-3 text-center font-bold text-sm text-black dark:text-white">
                        {{ $arabicMonths[$currentMonth] }} {{ $currentYear }}
                    </div>
                    <div class="grid grid-cols-7 gap-0.5 mb-1">
                        @foreach($arabicDays as $d)
                            <div class="py-1 text-center text-xs font-semibold text-gray-400">{{ $d }}</div>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-7 gap-0.5">
                        @for($i = 0; $i < $startDow; $i++)
                            <div></div>
                        @endfor
                        @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $dateStr  = sprintf('%d-%02d-%02d', $currentYear, $currentMonth, $day);
                                $isToday  = $day === $today->day;
                                $hasSess  = in_array($dateStr, $sessionDates);
                            @endphp
                            <div class="relative flex aspect-square items-center justify-center rounded-lg text-xs
                                {{ $isToday ? 'font-black text-white' : ($hasSess ? 'font-semibold text-black dark:text-white' : 'text-gray-500 dark:text-gray-400') }}"
                                 style="{{ $isToday ? 'background:#0071AA' : '' }}">
                                {{ $day }}
                                @if($hasSess && !$isToday)
                                    <span class="absolute bottom-0.5 left-1/2 h-1 w-1 -translate-x-1/2 rounded-full" style="background:#0071AA"></span>
                                @endif
                            </div>
                        @endfor
                    </div>
                    <div class="mt-3 flex items-center justify-center gap-4 border-t border-stroke pt-3 dark:border-strokedark">
                        <div class="flex items-center gap-1.5 text-xs text-gray-400">
                            <span class="inline-block h-2.5 w-2.5 rounded" style="background:#0071AA"></span>{{ __('Today') }}
                        </div>
                        <div class="flex items-center gap-1.5 text-xs text-gray-400">
                            <span class="inline-block h-1.5 w-1.5 rounded-full" style="background:#0071AA"></span>{{ __('Session') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Rating Summary --}}
            @if($teacherRating['total_ratings'] > 0)
            <div class="overflow-hidden rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="flex items-center gap-3 border-b border-stroke px-5 py-4 dark:border-strokedark">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-black dark:text-white">{{ __('Your Rating') }}</h3>
                        <p class="text-xs text-gray-400">{{ $teacherRating['total_ratings'] }} {{ __('Your Rating') }}</p>
                    </div>
                </div>
                <div class="p-5">
                    <div class="mb-4 flex items-center gap-4">
                        <div class="text-4xl font-black text-black dark:text-white">{{ number_format($teacherRating['overall'], 1) }}</div>
                        <div>
                            <div class="flex gap-0.5">
                                @for($s = 1; $s <= 5; $s++)
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $s <= round($teacherRating['overall']) ? '#f59e0b' : '#e5e7eb' }}">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                @endfor
                            </div>
                            <p class="mt-1 text-xs text-gray-400">{{ __('out of 5 stars') }}</p>
                        </div>
                    </div>
                    @if($recentFeedback->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($recentFeedback->take(2) as $fb)
                        <div class="rounded-xl bg-gray-50 p-3 dark:bg-meta-4">
                            <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed line-clamp-2">"{{ $fb->comment }}"</p>
                            <p class="mt-1 text-xs text-gray-400">— {{ $fb->student->name ?? 'طالب' }}</p>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Quick Links --}}
            <div class="overflow-hidden rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke px-5 py-4 dark:border-strokedark">
                    <h3 class="font-bold text-black dark:text-white">{{ __('Quick Links') }}</h3>
                </div>
                <div class="p-4 space-y-2">
                    <a href="{{ route('teacher.schedule') }}"
                       class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-gray-50 dark:hover:bg-meta-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg" style="background:rgba(0,113,170,.1)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="#0071AA">
                                <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zM7 12h5v5H7z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Academic Schedule') }}</span>
                    </a>
                    <a href="{{ route('teacher.my-subjects.index') }}"
                       class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-gray-50 dark:hover:bg-meta-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg" style="background:rgba(139,92,246,.1)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="#8b5cf6">
                                <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('My Subjects') }}</span>
                    </a>
                    @if($openTicketsCount > 0)
                    <a href="{{ route('teacher.tickets.index') }}"
                       class="flex items-center gap-3 rounded-xl p-3 transition hover:bg-gray-50 dark:hover:bg-meta-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg" style="background:rgba(239,68,68,.1)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="#ef4444">
                                <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V8l8 5 8-5v10zm-8-7L4 6h16l-8 5z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Open Tickets') }}</span>
                        <span class="mr-auto inline-flex h-5 w-5 items-center justify-center rounded-full text-xs font-bold text-white" style="background:#ef4444">{{ $openTicketsCount }}</span>
                    </a>
                    @endif
                    @if($pendingSurveys > 0)
                    <div class="flex items-center gap-3 rounded-xl bg-amber-50 p-3 dark:bg-amber-900/20">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg" style="background:rgba(245,158,11,.15)">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="#f59e0b">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-amber-700 dark:text-amber-400">{{ $pendingSurveys }} استبيان بانتظارك</span>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
