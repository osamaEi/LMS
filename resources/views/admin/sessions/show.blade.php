@extends('layouts.dashboard')

@section('title', $session->title)

@push('styles')
<style>
    .session-page { max-width: 1200px; margin: 0 auto; }

    /* Header */
    .session-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .session-header::before {
        content: '';
        position: absolute;
        top: -40%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }
    .session-header::after {
        content: '';
        position: absolute;
        bottom: -50%;
        right: -5%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        border-radius: 50%;
    }
    .session-header .z { position: relative; z-index: 1; }

    /* Cards */
    .card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .dark .card { background: #1f2937; }
    .card-body { padding: 1.75rem; }

    /* Status Badges */
    .badge { display: inline-flex; align-items: center; gap: 6px; padding: 0.5rem 1rem; border-radius: 999px; font-size: 0.875rem; font-weight: 700; }
    .badge-live { background: rgba(239, 68, 68, 0.9); color: #fff; }
    .badge-scheduled { background: rgba(255,255,255,0.2); color: #fff; }
    .badge-completed { background: rgba(16, 185, 129, 0.9); color: #fff; }
    .badge-zoom { background: rgba(255,255,255,0.2); color: #fff; }
    .badge-dot { width: 8px; height: 8px; border-radius: 50%; background: #fff; }

    @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
    .badge-live .badge-dot { animation: pulse-dot 1.5s ease-in-out infinite; }

    /* Meta Items */
    .meta-row { display: flex; flex-wrap: wrap; gap: 1.5rem; color: rgba(255,255,255,0.85); font-size: 0.9375rem; }
    .meta-item { display: flex; align-items: center; gap: 0.5rem; }
    .meta-item svg { width: 20px; height: 20px; }

    /* Action Buttons */
    .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.25rem; border-radius: 12px; font-weight: 600; font-size: 0.875rem; transition: all 0.15s; cursor: pointer; border: none; text-decoration: none; }
    .btn svg { width: 18px; height: 18px; }
    .btn-white { background: rgba(255,255,255,0.2); color: #fff; border: 1px solid rgba(255,255,255,0.25); }
    .btn-white:hover { background: rgba(255,255,255,0.3); transform: translateY(-1px); }
    .btn-primary { background: linear-gradient(135deg, #0071AA 0%, #005a88 100%); color: #fff; }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0, 113, 170, 0.3); }
    .btn-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; }
    .btn-success:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
    .btn-danger { background: #fee2e2; color: #991b1b; }
    .btn-danger:hover { background: #fecaca; transform: translateY(-1px); }
    .btn-edit { background: #fef3c7; color: #92400e; }
    .btn-edit:hover { background: #fde68a; transform: translateY(-1px); }
    .btn-outline { background: #fff; color: #374151; border: 2px solid #e5e7eb; }
    .dark .btn-outline { background: #374151; color: #d1d5db; border-color: #4b5563; }
    .btn-outline:hover { background: #f3f4f6; transform: translateY(-1px); }

    /* Info Items */
    .info-item {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 1rem; background: #f9fafb; border-radius: 14px; transition: all 0.2s;
    }
    .dark .info-item { background: #111827; }
    .info-item:hover { background: #f3f4f6; }
    .dark .info-item:hover { background: #1f2937; }
    .info-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .info-icon svg { width: 20px; height: 20px; color: #fff; }
    .info-label { font-size: 0.8125rem; color: #6b7280; font-weight: 500; margin-bottom: 0.125rem; }
    .dark .info-label { color: #9ca3af; }
    .info-value { font-weight: 700; color: #111827; font-size: 0.9375rem; }
    .dark .info-value { color: #f9fafb; }

    /* Section Title */
    .section-title {
        display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;
    }
    .section-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .section-icon svg { width: 22px; height: 22px; color: #fff; }
    .section-name { font-size: 1.125rem; font-weight: 700; color: #111827; }
    .dark .section-name { color: #f9fafb; }
    .section-sub { font-size: 0.8125rem; color: #6b7280; }
    .dark .section-sub { color: #9ca3af; }

    /* File Card */
    .file-card {
        display: flex; align-items: center; gap: 1rem;
        padding: 1rem; background: #f9fafb; border-radius: 14px;
        border: 2px solid transparent; transition: all 0.2s;
    }
    .dark .file-card { background: #111827; }
    .file-card:hover { border-color: #0071AA; background: #fff; }
    .dark .file-card:hover { background: #1f2937; }
    .file-icon { width: 48px; height: 48px; border-radius: 12px; background: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
    .dark .file-icon { background: #374151; }
    .file-name { font-weight: 600; color: #111827; margin-bottom: 0.125rem; }
    .dark .file-name { color: #f9fafb; }
    .file-ext { font-size: 0.8125rem; color: #6b7280; }
    .dark .file-ext { color: #9ca3af; }

    /* Breadcrumb */
    .breadcrumb { display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: #6b7280; margin-bottom: 1.5rem; }
    .dark .breadcrumb { color: #9ca3af; }
    .breadcrumb a { color: #6b7280; text-decoration: none; transition: color 0.15s; }
    .dark .breadcrumb a { color: #9ca3af; }
    .breadcrumb a:hover { color: #111827; }
    .dark .breadcrumb a:hover { color: #f9fafb; }
    .breadcrumb .current { color: #111827; font-weight: 600; }
    .dark .breadcrumb .current { color: #f9fafb; }

    /* Progress Bar */
    .progress-bar { width: 100%; height: 8px; background: #e5e7eb; border-radius: 999px; overflow: hidden; }
    .dark .progress-bar { background: #374151; }
    .progress-fill { height: 100%; border-radius: 999px; background: linear-gradient(90deg, #0071AA, #38bdf8); transition: width 0.5s; }

    /* Grid */
    .grid-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; }
    @media (max-width: 1024px) { .grid-layout { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="session-page">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">
            <svg style="width: 16px; height: 16px;" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
            </svg>
        </a>
        <span>/</span>
        <a href="{{ route('admin.sessions.index') }}">الجلسات</a>
        <span>/</span>
        <span class="current">{{ Str::limit($session->title, 30) }}</span>
    </div>

    <!-- Header -->
    <div class="session-header">
        <div class="z">
            <div style="display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div style="flex: 1; min-width: 0;">
                    <!-- Status Badges -->
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem;">
                        @if($session->status === 'live')
                            <span class="badge badge-live"><span class="badge-dot"></span> مباشر الآن</span>
                        @elseif($session->status === 'scheduled')
                            <span class="badge badge-scheduled">مجدول</span>
                        @elseif($session->status === 'completed')
                            <span class="badge badge-completed">مكتمل</span>
                        @endif

                        @if($session->type === 'live_zoom')
                            <span class="badge badge-zoom">Zoom</span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 1rem;">{{ $session->title }}</h1>

                    <!-- Meta -->
                    <div class="meta-row">
                        @if($session->subject)
                            <span class="meta-item">
                                <svg fill="currentColor" viewBox="0 0 20 20"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/></svg>
                                {{ $session->subject->name }}
                            </span>
                        @endif
                        @if($session->scheduled_at)
                            <span class="meta-item">
                                <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                                {{ $session->scheduled_at->format('Y-m-d H:i') }}
                            </span>
                        @endif
                        <span class="meta-item">
                            <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                            {{ $session->duration_minutes ?? 60 }} دقيقة
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    <a href="{{ route('admin.sessions.edit', $session) }}" class="btn btn-white">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        تعديل
                    </a>
                    <a href="{{ route('admin.sessions.index') }}" class="btn btn-white">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/></svg>
                        العودة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Zoom Meeting Card -->
    @if($session->type === 'live_zoom' && $session->zoom_meeting_id)
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="card-body">
            <div class="section-title">
                <div class="section-icon" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
                    <svg fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/></svg>
                </div>
                <div>
                    <div class="section-name">اجتماع Zoom</div>
                    <div class="section-sub">Meeting ID: {{ $session->zoom_meeting_id }}</div>
                </div>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                <a href="{{ route('admin.sessions.zoom', $session) }}" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    شاشة كاملة
                </a>
                <a href="{{ route('admin.sessions.zoom-dashboard', $session) }}" class="btn btn-outline">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    مع لوحة التحكم
                </a>
                @if($session->zoom_join_url)
                <a href="{{ $session->zoom_join_url }}" target="_blank" class="btn btn-success">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    فتح في Zoom
                </a>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Grid Layout -->
    <div class="grid-layout">
        <!-- Main Content -->
        <div>
            <!-- Description -->
            @if($session->description)
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-body">
                    <div class="section-title">
                        <div class="section-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                            <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="section-name">الوصف</div>
                    </div>
                    <div style="color: #4b5563; line-height: 1.75; white-space: pre-line;" class="dark:text-gray-300">{{ $session->description }}</div>
                </div>
            </div>
            @endif

            <!-- Video Player -->
            @if(($session->type === 'video' || $session->type === 'recorded_video') && ($session->video_path || $session->video_url))
            <div class="card" style="margin-bottom: 1.5rem;">
                <div style="padding: 1.25rem 1.75rem; border-bottom: 2px solid #f3f4f6;">
                    <div class="section-title" style="margin-bottom: 0;">
                        <div class="section-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                            <svg fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/></svg>
                        </div>
                        <div class="section-name">فيديو الجلسة</div>
                    </div>
                </div>
                <div style="aspect-ratio: 16/9; background: #111827;">
                    <video controls style="width: 100%; height: 100%;">
                        <source src="{{ $session->getVideoUrl() }}" type="video/mp4">
                        متصفحك لا يدعم تشغيل الفيديو
                    </video>
                </div>
            </div>
            @endif

            <!-- Files -->
            @php
                $sessionFiles = $session->files ?? collect();
            @endphp
            @if($sessionFiles->count() > 0)
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-body">
                    <div class="section-title">
                        <div class="section-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                            <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <div class="section-name">الملفات المرفقة</div>
                            <div class="section-sub">{{ $sessionFiles->count() }} ملف</div>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @foreach($sessionFiles as $file)
                        @php
                            $extension = pathinfo($file->original_name ?? $file->file_path, PATHINFO_EXTENSION);
                            $iconBg = match(strtolower($extension)) {
                                'pdf' => '#ef4444',
                                'doc', 'docx' => '#3b82f6',
                                'xls', 'xlsx' => '#10b981',
                                'ppt', 'pptx' => '#f59e0b',
                                default => '#6b7280'
                            };
                        @endphp
                        <div class="file-card">
                            <div class="file-icon">
                                <svg style="width: 24px; height: 24px; color: {{ $iconBg }};" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div class="file-name" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $file->original_name ?? 'ملف' }}</div>
                                <div class="file-ext">{{ strtoupper($extension) }}</div>
                            </div>
                            <a href="{{ asset('storage/' . $file->file_path) }}" download class="btn btn-primary">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                تحميل
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Delete Action -->
            <div class="card">
                <div class="card-body" style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div style="font-weight: 700; color: #991b1b; margin-bottom: 0.25rem;">حذف الجلسة</div>
                        <div style="font-size: 0.875rem; color: #6b7280;">سيتم حذف الجلسة نهائياً ولا يمكن استرجاعها</div>
                    </div>
                    <form action="{{ route('admin.sessions.destroy', $session) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الجلسة؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Quick Info -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-body">
                    <div style="font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 1.25rem;" class="dark:text-gray-100">معلومات سريعة</div>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @if($session->scheduled_at)
                        <div class="info-item">
                            <div class="info-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                                <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                            </div>
                            <div>
                                <div class="info-label">التاريخ</div>
                                <div class="info-value">{{ $session->scheduled_at->format('Y-m-d') }}</div>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                                <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                            </div>
                            <div>
                                <div class="info-label">الوقت</div>
                                <div class="info-value">{{ $session->scheduled_at->format('h:i A') }}</div>
                            </div>
                        </div>
                        @endif

                        <div class="info-item">
                            <div class="info-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                                <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                            </div>
                            <div>
                                <div class="info-label">المدة</div>
                                <div class="info-value">{{ $session->duration_minutes ?? 60 }} دقيقة</div>
                            </div>
                        </div>

                        @if($session->subject)
                        <div class="info-item">
                            <div class="info-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <svg fill="currentColor" viewBox="0 0 20 20"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/></svg>
                            </div>
                            <div>
                                <div class="info-label">المادة</div>
                                <div class="info-value">{{ $session->subject->name }}</div>
                            </div>
                        </div>
                        @endif

                        @if($session->session_number)
                        <div class="info-item">
                            <div class="info-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                                <svg fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </div>
                            <div>
                                <div class="info-label">رقم الجلسة</div>
                                <div class="info-value">{{ $session->session_number }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Attendance Stats -->
            @if($session->attendances)
            <div class="card">
                <div class="card-body">
                    <div style="font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 1.25rem;" class="dark:text-gray-100">الإحصائيات</div>
                    <div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span class="info-label">معدل الحضور</span>
                            <span style="font-weight: 700; color: #0071AA;">{{ $session->getAttendanceRate() }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $session->getAttendanceRate() }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
