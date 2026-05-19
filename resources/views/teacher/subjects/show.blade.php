@extends('layouts.dashboard')

@section('title', $subject->name)

@push('styles')
<style>
    .subject-hero {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #003f6b 100%);
        border-radius: 20px;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        margin-bottom: 1.5rem;
        box-shadow: 0 12px 40px rgba(0,113,170,.25);
    }
    .subject-hero::before {
        content: '';
        position: absolute;
        top: -60%;
        right: -10%;
        width: 320px; height: 320px;
        background: radial-gradient(circle, rgba(255,255,255,.08) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .hero-stat {
        background: rgba(255,255,255,.12);
        border-radius: 14px;
        padding: .6rem 1.1rem;
        text-align: center;
        min-width: 80px;
    }
    .hero-stat-val { font-size: 1.5rem; font-weight: 800; line-height: 1; }
    .hero-stat-lbl { font-size: .7rem; opacity: .75; margin-top: 2px; }

    .tab-bar {
        display: flex;
        align-items: center;
        gap: .35rem;
        background: #fff;
        border-radius: 14px;
        padding: 5px;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
        border: 1px solid #f1f5f9;
        margin-bottom: 1.25rem;
        flex-wrap: wrap;
    }
    .dark .tab-bar { background: #1e293b; border-color: rgba(255,255,255,.08); }

    .tab-btn {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .55rem 1.2rem;
        border-radius: 10px;
        font-size: .875rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all .2s;
        color: #6b7280;
        background: transparent;
    }
    .tab-btn.active {
        background: linear-gradient(135deg, #0071AA, #005a88);
        color: #fff;
        box-shadow: 0 4px 12px rgba(0,113,170,.3);
    }
    .tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        padding: 0 5px;
        border-radius: 999px;
        font-size: .7rem;
        font-weight: 700;
    }
    .tab-count-off { background: #f1f5f9; color: #6b7280; }
    .tab-count-on  { background: rgba(255,255,255,.25); color: #fff; }

    .section-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #f1f5f9;
        overflow: hidden;
        box-shadow: 0 1px 6px rgba(0,0,0,.05);
    }
    .dark .section-card { background: #1e293b; border-color: rgba(255,255,255,.08); }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .dark .section-header { border-color: rgba(255,255,255,.08); }

    .row-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f9fafb;
        transition: background .15s;
    }
    .dark .row-item { border-color: rgba(255,255,255,.04); }
    .row-item:last-child { border-bottom: none; }
    .row-item:hover { background: #f9fafb; }
    .dark .row-item:hover { background: rgba(255,255,255,.02); }

    .num-badge {
        width: 42px; height: 42px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 800; font-size: .9rem;
        flex-shrink: 0;
        background: linear-gradient(135deg, #0071AA, #005a88);
    }
    .file-badge {
        width: 42px; height: 42px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 700; font-size: .7rem;
        flex-shrink: 0;
    }

    .pill {
        display: inline-flex; align-items: center;
        padding: .2rem .65rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .action-btn {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .4rem .85rem;
        border-radius: 8px;
        font-size: .75rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all .2s;
        text-decoration: none;
    }
    .empty-box {
        display: flex; flex-direction: column; align-items: center;
        justify-content: center; padding: 4rem 1.5rem; text-align: center;
    }
    .empty-icon {
        width: 72px; height: 72px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: #f1f5f9;
        margin: 0 auto 1rem;
    }
</style>
@endpush

@section('content')
<div x-data="{ tab: 'sessions', uploadModal: false, createModal: false }">

{{-- ─── Hero Header ─── --}}
<div class="subject-hero">
    <div class="relative z-10">
        {{-- Breadcrumb --}}
        <nav class="mb-4">
            <ol class="flex items-center gap-2 text-white/60 text-sm">
                <li><a href="{{ route('teacher.my-subjects.index') }}" class="hover:text-white transition">مقرراتي</a></li>
                <li>/</li>
                <li class="text-white">{{ $subject->name }}</li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
            <div class="flex-1">
                <div class="flex items-center gap-3 flex-wrap mb-1">
                    <h1 class="text-2xl font-extrabold">{{ $subject->name }}</h1>
                    @if($subject->status === 'active')
                        <span class="pill" style="background:rgba(16,185,129,.25);color:#a7f3d0">نشط</span>
                    @else
                        <span class="pill" style="background:rgba(255,255,255,.15);color:rgba(255,255,255,.7)">غير نشط</span>
                    @endif
                    @if($subject->code)
                        <span class="pill" style="background:rgba(255,255,255,.15);color:#fff" dir="ltr">{{ $subject->code }}</span>
                    @endif
                </div>
                <p class="text-white/70 text-sm">
                    {{ $subject->term->name ?? '' }}
                    @if($subject->term?->program?->name)
                        <span class="mx-1 opacity-50">—</span>{{ $subject->term->program->name }}
                    @endif
                </p>

                {{-- Stats --}}
                <div class="flex flex-wrap gap-3 mt-4">
                    <div class="hero-stat">
                        <div class="hero-stat-val">{{ $subject->enrollments_count }}</div>
                        <div class="hero-stat-lbl">طالب</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-val">{{ $sessions->count() }}</div>
                        <div class="hero-stat-lbl">حصة</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-val">{{ $subject->files->count() }}</div>
                        <div class="hero-stat-lbl">ملف مقرر</div>
                    </div>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="flex gap-2 flex-wrap">
                <button @click="uploadModal = true"
                        class="action-btn"
                        style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.25)">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    رفع ملف
                </button>
                <button @click="createModal = true"
                        class="action-btn"
                        style="background:rgba(16,185,129,.9);color:#fff;box-shadow:0 4px 12px rgba(16,185,129,.35)">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    إنشاء حصة
                </button>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="mb-4 rounded-xl p-4 text-sm font-medium" style="background:#dcfce7;color:#166534">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="mb-4 rounded-xl p-4 text-sm" style="background:#fee2e2;color:#991b1b">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

{{-- ─── Tab Bar ─── --}}
<div class="tab-bar">
    <button @click="tab='sessions'" :class="tab==='sessions' ? 'active' : ''" class="tab-btn">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
        ال محاضرات  
        <span class="tab-count" :class="tab==='sessions' ? 'tab-count-on' : 'tab-count-off'">{{ $sessions->count() }}</span>
    </button>
    <button @click="tab='subject-files'" :class="tab==='subject-files' ? 'active' : ''" class="tab-btn">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        ملفات المقرر
        <span class="tab-count" :class="tab==='subject-files' ? 'tab-count-on' : 'tab-count-off'">{{ $subject->files->count() }}</span>
    </button>
</div>

{{-- ═══ Sessions Tab ═══ --}}
<div x-show="tab==='sessions'">
    <div class="section-card">
        <div class="section-header">
            <div class="flex items-center gap-3">
                <div style="width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#0071AA,#005a88)">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white">المحاضرات والمحاضرات</h2>
                    <p class="text-xs text-gray-500">{{ $sessions->count() }}  محاضرة مضافة</p>
                </div>
            </div>
            <button @click="createModal = true"
                    class="action-btn"
                    style="background:linear-gradient(135deg,#0071AA,#005a88);color:#fff">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                إضافة حصة
            </button>
        </div>

        @if($sessions->isEmpty())
            <div class="empty-box">
                <div class="empty-icon">
                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-600 dark:text-gray-400">لا توجد محاضرات بعد</p>
                <button @click="createModal = true"
                        class="action-btn mt-3"
                        style="background:linear-gradient(135deg,#0071AA,#005a88);color:#fff">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    إنشاء أول حصة
                </button>
            </div>
        @else
            @foreach($sessions as $session)
                @php
                    $isLive     = $session->started_at && !$session->ended_at;
                    $isComplete = (bool)$session->ended_at;
                    $isZoom     = $session->type === 'live_zoom';

                    if ($isLive)         { $sb = '#fef2f2'; $sc = '#dc2626'; $sl = 'مباشر'; }
                    elseif ($isComplete) { $sb = '#dcfce7'; $sc = '#16a34a'; $sl = 'مكتملة'; }
                    else                 { $sb = '#fefce8'; $sc = '#ca8a04'; $sl = 'قادمة'; }

                    $numBg = $isLive ? 'linear-gradient(135deg,#ef4444,#dc2626)'
                           : ($isComplete ? 'linear-gradient(135deg,#10b981,#059669)'
                           : 'linear-gradient(135deg,#0071AA,#005a88)');
                @endphp
                <div class="row-item">
                    {{-- Number --}}
                    <div class="num-badge" style="background:{{ $numBg }}">{{ $session->session_number }}</div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-1.5 mb-0.5">
                            <span class="font-semibold text-gray-900 dark:text-white text-sm">{{ $session->title }}</span>
                            <span class="pill" style="background:{{ $sb }};color:{{ $sc }}">{{ $sl }}</span>
                            <span class="pill" style="background:{{ $isZoom ? '#eff6ff' : '#f5f3ff' }};color:{{ $isZoom ? '#1d4ed8' : '#6d28d9' }}">
                                {{ $isZoom ? 'زووم' : 'مسجل' }}
                            </span>
                            @if($isLive)
                                <span class="pill animate-pulse" style="background:#dc2626;color:#fff">● LIVE</span>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-gray-500">
                            @if($session->scheduled_at)
                                <span class="flex items-center gap-1">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d H:i') }}
                                </span>
                            @endif
                            @if($session->duration_minutes)
                                <span>{{ $session->duration_minutes }} دقيقة</span>
                            @endif
                            @if($session->files->count())
                                <span class="flex items-center gap-1" style="color:#2563eb">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    {{ $session->files->count() }} ملف
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-shrink-0 items-center gap-1.5 flex-wrap">
                        @if($isZoom && $session->zoom_join_url)
                            <a href="{{ route('teacher.my-subjects.sessions.zoom-embedded', [$subject->id, $session->id]) }}"
                               class="action-btn" style="background:#0071AA;color:#fff">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                دخول
                            </a>
                        @endif
                        <a href="{{ route('teacher.my-subjects.sessions.attendance', [$subject->id, $session->id]) }}"
                           class="action-btn" style="background:#f5f3ff;color:#6d28d9">
                            الحضور
                        </a>
                        <a href="{{ route('teacher.my-subjects.sessions.edit', [$subject->id, $session->id]) }}"
                           class="action-btn" style="background:#f1f5f9;color:#374151">
                            تعديل
                        </a>
                        <form action="{{ route('teacher.my-subjects.sessions.destroy', [$subject->id, $session->id]) }}"
                              method="POST" class="inline"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذه الحصة؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn" style="background:#fee2e2;color:#dc2626">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

{{-- ═══ Subject Files Tab ═══ --}}
<div x-show="tab==='subject-files'">
    <div class="section-card">
        <div class="section-header">
            <div class="flex items-center gap-3">
                <div style="width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#16a34a,#15803d)">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900 dark:text-white">ملفات المقرر</h2>
                    <p class="text-xs text-gray-500">{{ $subject->files->count() }} ملف</p>
                </div>
            </div>
            <button @click="uploadModal = true"
                    class="action-btn"
                    style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                رفع ملف
            </button>
        </div>

        @if($subject->files->isEmpty())
            <div class="empty-box">
                <div class="empty-icon">
                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="font-semibold text-gray-600 dark:text-gray-400">لا توجد ملفات بعد</p>
                <button @click="uploadModal = true" class="action-btn mt-3"
                        style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff">
                    رفع ملف جديد
                </button>
            </div>
        @else
            @foreach($subject->files as $sFile)
                @php
                    $ext2 = strtolower($sFile->file_type ?? pathinfo($sFile->file_path ?? '', PATHINFO_EXTENSION));
                    if ($ext2==='pdf')                                    { $c2='#ef4444';$l2='PDF'; }
                    elseif(in_array($ext2,['doc','docx']))                { $c2='#2563eb';$l2='DOC'; }
                    elseif(in_array($ext2,['xls','xlsx','csv']))          { $c2='#16a34a';$l2='XLS'; }
                    elseif(in_array($ext2,['ppt','pptx']))                { $c2='#ea580c';$l2='PPT'; }
                    elseif(in_array($ext2,['jpg','jpeg','png','gif','webp'])) { $c2='#9333ea';$l2=strtoupper($ext2); }
                    else                                                  { $c2='#6b7280';$l2=strtoupper($ext2)?:'FILE'; }
                @endphp
                <div class="row-item">
                    <div class="file-badge" style="background:{{ $c2 }}">{{ $l2 }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="truncate font-semibold text-gray-900 dark:text-white text-sm">{{ $sFile->title }}</p>
                        <div class="flex flex-wrap items-center gap-x-3 text-xs text-gray-500 mt-0.5">
                            @if($sFile->file_size)
                                <span>{{ number_format($sFile->file_size/1024,1) }} KB</span>
                            @endif
                            @if($sFile->description)
                                <span class="truncate max-w-xs">{{ $sFile->description }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-shrink-0 items-center gap-1.5">
                        @if($sFile->file_path)
                            <a href="{{ asset('storage/'.$sFile->file_path) }}" target="_blank"
                               class="action-btn" style="background:#eff6ff;color:#1d4ed8">عرض</a>
                            <a href="{{ asset('storage/'.$sFile->file_path) }}" download
                               class="action-btn" style="background:#dcfce7;color:#15803d">تحميل</a>
                        @endif
                        <form action="{{ route('teacher.files.destroy', $sFile) }}" method="POST" class="inline"
                              onsubmit="return confirm('هل أنت متأكد؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn" style="background:#fee2e2;color:#dc2626">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

{{-- ─── Upload Modal ─── --}}
<div x-show="uploadModal" x-cloak
     style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.55);"
     @keydown.escape.window="uploadModal = false">
    <div class="rounded-2xl bg-white dark:bg-gray-900 shadow-2xl w-full max-w-md mx-4 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">رفع ملف للمقرر</h3>
            <button @click="uploadModal = false" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('teacher.files.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">عنوان الملف <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="مثال: محاضرة الوحدة الأولى">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الملف <span class="text-red-500">*</span></label>
                    <input type="file" name="file" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.png,.jpg,.jpeg">
                    <p class="mt-1 text-xs text-gray-400">PDF, Word, Excel, PowerPoint, صور — حد أقصى 50MB</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">وصف (اختياري)</label>
                    <textarea name="description" rows="2"
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="وصف مختصر للملف"></textarea>
                </div>
            </div>
            <div class="mt-5 flex gap-3">
                <button type="submit"
                        class="flex-1 rounded-lg py-2.5 text-sm font-semibold text-white transition"
                        style="background:linear-gradient(135deg,#16a34a,#15803d)">
                    رفع الملف
                </button>
                <button type="button" @click="uploadModal = false"
                        class="rounded-lg border border-gray-300 dark:border-gray-700 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ─── Create Session Modal ─── --}}
<div x-show="createModal" x-cloak
     style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.6);"
     @keydown.escape.window="createModal = false">
    <div style="background:#fff;border-radius:20px;box-shadow:0 24px 80px rgba(0,0,0,.22);width:100%;max-width:420px;margin:1rem;overflow:hidden;"
         class="dark:bg-gray-900">

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#0071AA 0%,#005a88 60%,#003f6b 100%);padding:1.25rem 1.5rem;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-50%;right:-5%;width:180px;height:180px;background:radial-gradient(circle,rgba(255,255,255,.1) 0%,transparent 70%);border-radius:50%;pointer-events:none;"></div>
            <div class="relative flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div style="width:40px;height:40px;border-radius:11px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;">
                        <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="color:#fff;font-size:1rem;font-weight:800;line-height:1.2;">إنشاء  محاضرة جديدة</h3>
                        <p style="color:rgba(255,255,255,.7);font-size:.75rem;margin-top:2px;">{{ $subject->name }}</p>
                    </div>
                </div>
                <button @click="createModal = false"
                        style="width:34px;height:34px;border-radius:9px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);display:flex;align-items:center;justify-content:center;cursor:pointer;color:#fff;">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body --}}
        <form id="cm_form"
              action="{{ route('teacher.my-subjects.sessions.store', $subject->id) }}"
              method="POST">
            @csrf
            <div style="padding:1.5rem;">
                <label style="display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.5rem;" class="dark:text-gray-300">
                    موعد الحصة
                </label>
                <input type="datetime-local" name="scheduled_at" required
                       style="width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:.65rem .9rem;font-size:.9rem;color:#111827;outline:none;box-sizing:border-box;transition:border .2s;"
                       class="dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                       onfocus="this.style.borderColor='#0071AA'" onblur="this.style.borderColor='#e5e7eb'">
            </div>

            {{-- Footer --}}
            <div style="padding:.9rem 1.5rem 1.25rem;display:flex;gap:.65rem;justify-content:flex-end;">
                <button type="button" @click="createModal = false"
                        style="border:1.5px solid #e5e7eb;border-radius:10px;padding:.55rem 1.1rem;font-size:.875rem;font-weight:600;color:#374151;cursor:pointer;background:#fff;"
                        class="dark:border-gray-700 dark:text-gray-300 dark:bg-gray-800">
                    إلغاء
                </button>
                <button type="submit"
                        style="border-radius:10px;padding:.55rem 1.4rem;font-size:.875rem;font-weight:700;color:#fff;cursor:pointer;background:linear-gradient(135deg,#0071AA,#005a88);border:none;box-shadow:0 4px 12px rgba(0,113,170,.3);">
                    إنشاء
                </button>
            </div>
        </form>
    </div>
</div>

</div>{{-- end x-data --}}
@endsection
