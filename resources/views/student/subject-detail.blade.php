@extends('layouts.dashboard')

@section('title', $subject->name)

@push('styles')
<style>
    .subject-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #004266 100%);
        border-radius: 20px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 50px rgba(0, 113, 170, 0.3);
    }
    .subject-header::before {
        content: '';
        position: absolute;
        top: -100%;
        right: -20%;
        width: 80%;
        height: 300%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.1) 0%, transparent 60%);
    }
    .info-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }
    .dark .info-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.1);
    }
    .file-card {
        background: white;
        border-radius: 14px;
        border: 2px solid #f1f5f9;
        padding: 1.1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .dark .file-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.08);
    }
    .file-card:hover {
        border-color: #0071AA;
        box-shadow: 0 6px 24px rgba(0,113,170,0.12);
        transform: translateY(-2px);
    }
    .tab-btn {
        padding: 0.6rem 1.5rem;
        font-size: 0.9rem;
        font-weight: 600;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        background: transparent;
        color: #6b7280;
    }
    .tab-btn.active {
        background: #0071AA;
        color: white;
        box-shadow: 0 4px 12px rgba(0,113,170,0.3);
    }
    .tab-badge-on  { background: rgba(255,255,255,0.3); }
    .tab-badge-off { background: #f1f5f9; color: #6b7280; }
    .tab-badge-hw  { background: #fef3c7; color: #d97706; }
    .session-card {
        background: white;
        border-radius: 16px;
        border: 1.5px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
        transition: all 0.2s;
    }
    .dark .session-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.08);
    }
    .session-card:hover {
        border-color: #0071AA40;
        box-shadow: 0 6px 24px rgba(0,113,170,0.08);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl"
     x-data="{ tab: 'sessions' }">

    {{-- Subject Header --}}
    <div class="subject-header mb-6">
        <div class="relative z-10 text-white">
            <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-1 text-white/70 hover:text-white text-sm mb-4 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                العودة للوحة التحكم
            </a>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">{{ $subject->name }}</h1>
                    <p class="text-white/70">{{ $subject->term->program->name ?? '' }} {{ $subject->term->name ? '- ' . $subject->term->name : '' }}</p>
                    @if($subject->code)
                        <span class="inline-block mt-2 px-3 py-1 bg-white/15 rounded-lg text-white/90 text-sm font-medium" dir="ltr">{{ $subject->code }}</span>
                    @endif
                </div>
                <div class="flex gap-3 flex-wrap">
                    <a href="{{ route('student.quizzes.index', $subject->id) }}"
                       class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold text-white transition-all hover:scale-105"
                       style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 6px 20px rgba(16,185,129,0.4);">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        الاختبارات
                    </a>
                    <a href="{{ route('student.homework.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold text-white transition-all hover:scale-105"
                       style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 6px 20px rgba(245,158,11,0.4);">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        الواجبات
                    </a>
                </div>
            </div>

            {{-- Stats --}}
            <div class="flex flex-wrap gap-3 mt-5">
                <div class="rounded-xl px-4 py-2 text-center" style="background:rgba(255,255,255,0.12)">
                    <div class="text-xl font-bold">{{ $sessions->count() }}</div>
                    <div class="text-xs opacity-80">حصة</div>
                </div>
                <div class="rounded-xl px-4 py-2 text-center" style="background:rgba(255,255,255,0.12)">
                    <div class="text-xl font-bold">{{ $attendances->where('attended', true)->count() }}</div>
                    <div class="text-xs opacity-80">حضرت</div>
                </div>
                <div class="rounded-xl px-4 py-2 text-center" style="background:rgba(255,255,255,0.12)">
                    <div class="text-xl font-bold">{{ $homeworks->count() }}</div>
                    <div class="text-xs opacity-80">واجب</div>
                </div>
                @php
                    $allSessionFiles = $sessions->flatMap(fn($s) => $s->files ?? collect());
                    $subjectFiles    = $subject->files ?? collect();
                    $totalFiles      = $allSessionFiles->count() + $subjectFiles->count();
                @endphp
                <div class="rounded-xl px-4 py-2 text-center" style="background:rgba(255,255,255,0.12)">
                    <div class="text-xl font-bold">{{ $totalFiles }}</div>
                    <div class="text-xs opacity-80">ملف</div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    {{-- Tabs --}}
    <div class="mb-5 flex items-center gap-2 flex-wrap" style="background:white;border-radius:14px;padding:6px;box-shadow:0 2px 12px rgba(0,0,0,0.06);border:1px solid #f1f5f9" class="dark:bg-gray-900">
        <button @click="tab='sessions'"
                :class="tab==='sessions' ? 'active' : ''"
                class="tab-btn flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            ال محاضرات  
            <span class="rounded-full px-1.5 py-0.5 text-xs font-bold tab-badge-off"
                  :class="tab==='sessions' ? 'tab-badge-on' : 'tab-badge-off'">{{ $sessions->count() }}</span>
        </button>
        <button @click="tab='files'"
                :class="tab==='files' ? 'active' : ''"
                class="tab-btn flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
            </svg>
            الملفات
            <span class="rounded-full px-1.5 py-0.5 text-xs font-bold tab-badge-off"
                  :class="tab==='files' ? 'tab-badge-on' : 'tab-badge-off'">{{ $totalFiles }}</span>
        </button>
        <button @click="tab='homework'"
                :class="tab==='homework' ? 'active' : ''"
                class="tab-btn flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            الواجبات
            @if($homeworks->count() > 0)
            <span class="rounded-full px-1.5 py-0.5 text-xs font-bold tab-badge-hw"
                  :class="tab==='homework' ? 'tab-badge-on' : 'tab-badge-hw'">{{ $homeworks->count() }}</span>
            @endif
        </button>
    </div>

    {{-- ═══════ Sessions Tab ═══════ --}}
    <div x-show="tab==='sessions'">
        @if($sessions->isEmpty())
            <div class="info-card" style="padding:4rem 2rem;text-align:center">
                <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#eff6ff,#dbeafe);display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem">
                    <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#93c5fd" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p style="font-size:1rem;font-weight:700;color:#374151;margin:0">لا توجد محاضرات بعد</p>
                <p style="font-size:0.85rem;color:#9ca3af;margin:6px 0 0">سيتم إضافة المحاضرات من قِبَل المعلم</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($sessions as $session)
                    @php
                        $attended   = isset($attendances[$session->id]) && $attendances[$session->id]->attended;
                        $isLive     = $session->started_at && !$session->ended_at;
                        $isComplete = (bool)$session->ended_at;
                        $isPending  = !$session->started_at;

                        if ($isLive)         { $statusLabel = 'مباشر الآن'; $statusBg = '#fef2f2'; $statusColor = '#dc2626'; }
                        elseif ($isComplete) { $statusLabel = 'مكتملة';     $statusBg = '#dcfce7'; $statusColor = '#16a34a'; }
                        else                 { $statusLabel = 'قادمة';       $statusBg = '#fefce8'; $statusColor = '#ca8a04'; }

                        $isZoom     = $session->type === 'live_zoom';
                        $isRecorded = $session->type === 'recorded_video';
                        $hasVideo   = $session->video_url || $session->video_path;
                        $hasZoomJoin = $isZoom && $session->zoom_join_url;
                    @endphp
                    <div class="session-card">
                        <div class="flex items-start gap-4">

                            {{-- Number badge --}}
                            <div class="flex-shrink-0 flex flex-col items-center gap-1">
                                <div style="width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:1rem;
                                    background:{{ $isLive ? 'linear-gradient(135deg,#ef4444,#dc2626)' : ($isComplete ? 'linear-gradient(135deg,#10b981,#059669)' : 'linear-gradient(135deg,#0071AA,#005a88)') }}">
                                    {{ $session->session_number }}
                                </div>
                                @if($attended)
                                    <span title="حضرت هذه الحصة">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#16a34a"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
                                    </span>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <h3 class="font-bold text-gray-900 dark:text-white text-base">{{ $session->title }}</h3>
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                          style="background:{{ $statusBg }};color:{{ $statusColor }}">{{ $statusLabel }}</span>
                                    @if($isLive)
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold text-white animate-pulse"
                                              style="background:#dc2626">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white inline-block"></span>
                                            LIVE
                                        </span>
                                    @endif
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                                          style="background:{{ $isZoom ? '#eff6ff' : '#f5f3ff' }};color:{{ $isZoom ? '#1d4ed8' : '#6d28d9' }}">
                                        {{ $isZoom ? 'زووم مباشر' : 'مسجل' }}
                                    </span>
                                </div>

                                @if($session->description_ar || $session->description_en)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2 leading-relaxed">
                                        {{ $session->description_ar ?: $session->description_en }}
                                    </p>
                                @endif

                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                                    @if($session->scheduled_at)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d — H:i') }}
                                        </span>
                                    @endif
                                    @if($session->duration_minutes)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $session->duration_minutes }} دقيقة
                                        </span>
                                    @endif
                                    @if($session->files->count())
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            {{ $session->files->count() }} ملف
                                        </span>
                                    @endif
                                    @if($session->homework)
                                        <span class="flex items-center gap-1" style="color:#d97706">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            واجب
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Action buttons --}}
                            <div class="flex flex-col gap-2 flex-shrink-0">
                                @if($hasZoomJoin)
                                    <a href="{{ route('student.sessions.join-zoom', $session->id) }}"
                                       class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold text-white transition hover:opacity-90"
                                       style="background: linear-gradient(135deg,#0071AA,#005a88); box-shadow: 0 4px 12px rgba(0,113,170,0.3);">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        دخول الزووم
                                    </a>
                                @endif
                                @if($isRecorded && $hasVideo)
                                    @php $videoHref = $session->video_url ?: asset('storage/'.$session->video_path); @endphp
                                    <a href="{{ $videoHref }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-bold text-white transition hover:opacity-90"
                                       style="background: linear-gradient(135deg,#6d28d9,#5b21b6);">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        مشاهدة
                                    </a>
                                @endif
                                @if($session->files->count())
                                    <button @click="tab='files'"
                                            class="inline-flex items-center gap-1.5 rounded-xl px-4 py-2 text-xs font-semibold transition"
                                            style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                        الملفات
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ═══════ Files Tab ═══════ --}}
    <div x-show="tab==='files'">
        @php
            $extMeta = [
                'pdf'  => ['bg' => '#fee2e2', 'icon_bg' => '#ef4444', 'label' => 'PDF'],
                'doc'  => ['bg' => '#dbeafe', 'icon_bg' => '#2563eb', 'label' => 'Word'],
                'docx' => ['bg' => '#dbeafe', 'icon_bg' => '#2563eb', 'label' => 'Word'],
                'xls'  => ['bg' => '#d1fae5', 'icon_bg' => '#059669', 'label' => 'Excel'],
                'xlsx' => ['bg' => '#d1fae5', 'icon_bg' => '#059669', 'label' => 'Excel'],
                'ppt'  => ['bg' => '#ffedd5', 'icon_bg' => '#ea580c', 'label' => 'PowerPoint'],
                'pptx' => ['bg' => '#ffedd5', 'icon_bg' => '#ea580c', 'label' => 'PowerPoint'],
                'mp4'  => ['bg' => '#ede9fe', 'icon_bg' => '#7c3aed', 'label' => 'فيديو'],
                'mp3'  => ['bg' => '#fae8ff', 'icon_bg' => '#a21caf', 'label' => 'صوت'],
                'zip'  => ['bg' => '#fef9c3', 'icon_bg' => '#ca8a04', 'label' => 'ZIP'],
                'jpg'  => ['bg' => '#e0f2fe', 'icon_bg' => '#0284c7', 'label' => 'صورة'],
                'jpeg' => ['bg' => '#e0f2fe', 'icon_bg' => '#0284c7', 'label' => 'صورة'],
                'png'  => ['bg' => '#e0f2fe', 'icon_bg' => '#0284c7', 'label' => 'صورة'],
            ];
            $defaultMeta = ['bg' => '#f1f5f9', 'icon_bg' => '#64748b', 'label' => 'ملف'];
        @endphp

        {{-- Subject-level files (SubjectFile) --}}
        @if($subjectFiles->count() > 0)
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#16a34a,#15803d);display:flex;align-items:center;justify-content:center">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span style="font-size:0.95rem;font-weight:700;color:#374151" class="dark:text-white">ملفات المقرر العامة</span>
                    <span style="font-size:0.78rem;color:#6b7280;background:#f1f5f9;padding:2px 10px;border-radius:999px">{{ $subjectFiles->count() }}</span>
                </div>
                <div class="space-y-2">
                    @foreach($subjectFiles as $file)
                    @php
                        $ext = strtolower($file->file_type ?? pathinfo($file->file_path ?? '', PATHINFO_EXTENSION));
                        $meta = $extMeta[$ext] ?? $defaultMeta;
                        $sizeKb = $file->file_size ? number_format($file->file_size / 1024, 0) : null;
                    @endphp
                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="file-card">
                        <div style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:{{ $meta['bg'] }}">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="{{ $meta['icon_bg'] }}">
                                <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                            </svg>
                        </div>
                        <div style="flex:1;min-width:0">
                            <div style="font-size:0.95rem;font-weight:700;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" class="dark:text-white">{{ $file->title }}</div>
                            <div style="display:flex;align-items:center;gap:8px;margin-top:3px">
                                <span style="font-size:0.75rem;font-weight:600;padding:1px 8px;border-radius:999px;background:{{ $meta['bg'] }};color:{{ $meta['icon_bg'] }}">{{ strtoupper($ext) ?: $meta['label'] }}</span>
                                @if($sizeKb)<span style="font-size:0.75rem;color:#9ca3af">{{ $sizeKb }} KB</span>@endif
                            </div>
                        </div>
                        <div style="flex-shrink:0;width:36px;height:36px;border-radius:10px;background:#f1f5f9;display:flex;align-items:center;justify-content:center">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Session files grouped --}}
        @php
            $filesBySession = [];
            foreach ($sessions as $session) {
                if ($session->files && $session->files->count() > 0) {
                    $filesBySession[] = ['session' => $session, 'files' => $session->files];
                }
            }
        @endphp

        @if(count($filesBySession) === 0 && $subjectFiles->count() === 0)
            <div class="info-card" style="padding:4rem 2rem;text-align:center">
                <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#eff6ff,#dbeafe);display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem">
                    <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#93c5fd" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                </div>
                <p style="font-size:1rem;font-weight:700;color:#374151;margin:0">لا توجد ملفات بعد</p>
                <p style="font-size:0.85rem;color:#9ca3af;margin:6px 0 0">سيتم رفع الملفات هنا من قِبَل المعلم</p>
            </div>
        @else
            @if(count($filesBySession) > 0)
                <div class="flex items-center gap-2 mb-3">
                    <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#0071AA,#005a88);display:flex;align-items:center;justify-content:center">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                    </div>
                    <span style="font-size:0.95rem;font-weight:700;color:#374151" class="dark:text-white">ملفات ال محاضرات  </span>
                </div>
                <div id="filesList" class="space-y-5">
                    @foreach($filesBySession as $group)
                    @php $sessionF = $group['session']; @endphp
                    <div class="file-group">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                            <div style="width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:0.8rem;flex-shrink:0;
                                background:{{ $sessionF->ended_at ? 'linear-gradient(135deg,#10b981,#059669)' : ($sessionF->started_at ? 'linear-gradient(135deg,#ef4444,#dc2626)' : 'linear-gradient(135deg,#3b82f6,#2563eb)') }}">
                                {{ $sessionF->session_number }}
                            </div>
                            <span style="font-size:0.95rem;font-weight:700;color:#374151" class="dark:text-white">{{ $sessionF->title }}</span>
                            <span style="font-size:0.78rem;color:#6b7280;background:#f1f5f9;padding:2px 10px;border-radius:999px">{{ $group['files']->count() }}</span>
                        </div>
                        <div class="space-y-2">
                            @foreach($group['files'] as $file)
                            @php
                                $ext = strtolower(pathinfo($file->file_path ?? $file->title ?? '', PATHINFO_EXTENSION));
                                $meta = $extMeta[$ext] ?? $defaultMeta;
                                $sizeKb = $file->file_size ? number_format($file->file_size / 1024, 0) : null;
                                $fileName = $file->title ?: basename($file->file_path ?? 'ملف');
                            @endphp
                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="file-card">
                                <div style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:{{ $meta['bg'] }}">
                                    @if(in_array($ext, ['mp4','avi','mov','mkv']))
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="{{ $meta['icon_bg'] }}"><path d="M8 5v14l11-7z"/></svg>
                                    @elseif(in_array($ext, ['jpg','jpeg','png','gif','webp']))
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="{{ $meta['icon_bg'] }}"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                                    @elseif(in_array($ext, ['mp3','wav','ogg']))
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="{{ $meta['icon_bg'] }}"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
                                    @else
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="{{ $meta['icon_bg'] }}"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                                    @endif
                                </div>
                                <div style="flex:1;min-width:0">
                                    <div style="font-size:0.95rem;font-weight:700;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" class="dark:text-white">{{ $fileName }}</div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-top:3px">
                                        <span style="font-size:0.75rem;font-weight:600;padding:1px 8px;border-radius:999px;background:{{ $meta['bg'] }};color:{{ $meta['icon_bg'] }}">{{ strtoupper($ext) ?: $meta['label'] }}</span>
                                        @if($sizeKb)<span style="font-size:0.75rem;color:#9ca3af">{{ $sizeKb }} KB</span>@endif
                                    </div>
                                </div>
                                <div style="flex-shrink:0;width:36px;height:36px;border-radius:10px;background:#f1f5f9;display:flex;align-items:center;justify-content:center">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>

    {{-- ═══════ Homework Tab ═══════ --}}
    <div x-show="tab==='homework'">
        @if($homeworks->isEmpty())
            <div class="info-card" style="padding:4rem 2rem;text-align:center">
                <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#fef3c7,#fde68a);display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem">
                    <svg width="38" height="38" viewBox="0 0 24 24" fill="#f59e0b">
                        <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                </div>
                <p style="font-size:1rem;font-weight:700;color:#374151;margin:0">لا توجد واجبات بعد</p>
                <p style="font-size:0.85rem;color:#9ca3af;margin:6px 0 0">ستظهر هنا الواجبات التي يضيفها المعلم</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($homeworks as $hw)
                @php
                    $isOverdue = $hw->due_date && $hw->due_date->isPast();
                    $isDueSoon = $hw->due_date && !$isOverdue && $hw->due_date->diffInDays(now()) <= 3;
                @endphp
                <div class="overflow-hidden rounded-2xl border bg-white shadow-sm dark:bg-gray-900"
                     style="border-color:{{ $isOverdue ? '#fca5a5' : ($isDueSoon ? '#fde68a' : '#e5e7eb') }}">
                    <div class="flex items-stretch">
                        <div class="w-1.5 flex-shrink-0"
                             style="background:{{ $isOverdue ? '#ef4444' : ($isDueSoon ? '#f59e0b' : '#0071AA') }}"></div>
                        <div class="flex-1 p-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="flex-1">
                                    <div class="mb-2 flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            الحصة {{ $hw->session->session_number }}: {{ $hw->session->title }}
                                        </span>
                                    </div>
                                    <h3 class="text-base font-bold text-black dark:text-white">
                                        {{ $hw->title_ar ?: $hw->title_en ?: 'واجب بدون عنوان' }}
                                    </h3>
                                    @if($hw->description_ar || $hw->description_en)
                                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line leading-relaxed">
                                        {{ $hw->description_ar ?: $hw->description_en }}
                                    </p>
                                    @endif
                                    <div class="mt-3 flex flex-wrap gap-3 text-xs">
                                        @if($hw->due_date)
                                        <span class="flex items-center gap-1 font-medium"
                                              style="color:{{ $isOverdue ? '#dc2626' : ($isDueSoon ? '#b45309' : '#6b7280') }}">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/>
                                            </svg>
                                            موعد التسليم: {{ $hw->due_date->format('Y/m/d') }}
                                            @if($isOverdue)
                                                <span class="rounded-full px-2 py-0.5 text-[10px] font-bold text-white" style="background:#ef4444">متأخر</span>
                                            @elseif($isDueSoon)
                                                <span class="rounded-full px-2 py-0.5 text-[10px] font-bold text-white" style="background:#f59e0b">قريباً</span>
                                            @endif
                                        </span>
                                        @endif
                                        <span class="flex items-center gap-1 text-gray-400">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm4.24 16L11 14.61V7h1.5v6.86l4.62 2.75-1.38 2.39z"/>
                                            </svg>
                                            أُضيف {{ $hw->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                @if($hw->file_path)
                                <div class="flex-shrink-0">
                                    <a href="{{ asset('storage/' . $hw->file_path) }}" target="_blank" download
                                       class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold text-white shadow transition hover:opacity-90"
                                       style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"/>
                                        </svg>
                                        تحميل الواجب
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>{{-- end x-data --}}
@endsection
