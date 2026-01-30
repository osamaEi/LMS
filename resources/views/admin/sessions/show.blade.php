@extends('layouts.dashboard')

@section('title', $session->title)

@push('styles')
<style>
    :root {
        --primary: #0071AA;
        --primary-dark: #005a88;
        --primary-light: #e6f4fa;
    }

    .session-header-gradient {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    }

    .card-modern {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid #f3f4f6;
        transition: all 0.3s ease;
    }

    .card-modern:hover {
        box-shadow: 0 8px 30px rgba(0,113,170,0.12);
        transform: translateY(-2px);
    }

    .dark .card-modern {
        background: #1f2937;
        border-color: #374151;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-live {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        animation: pulse 2s infinite;
    }

    .status-scheduled {
        background: #e6f4fa;
        color: #0071AA;
    }

    .status-completed {
        background: #d1fae5;
        color: #047857;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 14px;
        transition: all 0.2s ease;
    }

    .dark .info-item {
        background: #111827;
    }

    .info-item:hover {
        background: #f3f4f6;
    }

    .dark .info-item:hover {
        background: #1f2937;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .action-btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
    }

    .action-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,113,170,0.3);
    }

    .action-btn-secondary {
        background: white;
        color: #374151;
        border: 2px solid #e5e7eb;
    }

    .dark .action-btn-secondary {
        background: #374151;
        color: #d1d5db;
        border-color: #4b5563;
    }

    .action-btn-danger {
        background: #fef2f2;
        color: #dc2626;
        border: 2px solid #fecaca;
    }

    .action-btn-danger:hover {
        background: #fee2e2;
    }

    .file-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 14px;
        border: 2px solid transparent;
        transition: all 0.2s ease;
    }

    .dark .file-card {
        background: #111827;
    }

    .file-card:hover {
        border-color: var(--primary);
        background: white;
    }

    .dark .file-card:hover {
        background: #1f2937;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .section-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@section('content')
<div class="space-y-6 pb-12">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900 dark:hover:text-white transition">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
            </svg>
        </a>
        <span>/</span>
        <a href="{{ route('admin.sessions.index') }}" class="hover:text-gray-900 dark:hover:text-white transition">
            الجلسات
        </a>
        <span>/</span>
        <span class="text-gray-900 dark:text-white font-medium">{{ Str::limit($session->title, 30) }}</span>
    </nav>

    <!-- Header Card -->
    <div class="card-modern overflow-hidden">
        <div class="session-header-gradient p-8 text-white">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                <div class="flex-1">
                    <!-- Status & Type Badges -->
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        @if($session->status === 'live')
                            <span class="status-badge status-live">
                                <span class="w-2 h-2 bg-white rounded-full"></span>
                                مباشر الآن
                            </span>
                        @elseif($session->status === 'scheduled')
                            <span class="status-badge status-scheduled">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                مجدول
                            </span>
                        @elseif($session->status === 'completed')
                            <span class="status-badge status-completed">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                مكتمل
                            </span>
                        @endif

                        @if($session->type === 'live_zoom')
                            <span class="status-badge" style="background: rgba(255,255,255,0.2);">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                </svg>
                                Zoom
                            </span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="text-3xl lg:text-4xl font-bold mb-4">{{ $session->title }}</h1>

                    <!-- Meta Info -->
                    <div class="flex flex-wrap items-center gap-6 text-blue-100">
                        @if($session->subject)
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                                </svg>
                                {{ $session->subject->name }}
                            </span>
                        @endif

                        @if($session->scheduled_at)
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                {{ $session->scheduled_at->format('Y-m-d H:i') }}
                            </span>
                        @endif

                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            {{ $session->duration_minutes ?? 60 }} دقيقة
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.sessions.edit', $session) }}" class="action-btn action-btn-secondary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        تعديل
                    </a>
                    <form action="{{ route('admin.sessions.destroy', $session) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الجلسة؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn action-btn-danger">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Zoom Meeting Card -->
    @if($session->type === 'live_zoom' && $session->zoom_meeting_id)
    <div class="card-modern p-6">
        <div class="section-title">
            <div class="section-icon" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">اجتماع Zoom</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Meeting ID: {{ $session->zoom_meeting_id }}</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.sessions.zoom', $session) }}" class="action-btn action-btn-primary">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 110-2h4a1 1 0 011 1v4a1 1 0 11-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 112 0v1.586l2.293-2.293a1 1 0 011.414 1.414L6.414 15H8a1 1 0 110 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 110-2h1.586l-2.293-2.293a1 1 0 011.414-1.414L15 13.586V12a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                شاشة كاملة
            </a>
            <a href="{{ route('admin.sessions.zoom-dashboard', $session) }}" class="action-btn action-btn-secondary">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                </svg>
                مع لوحة التحكم
            </a>
            @if($session->zoom_join_url)
            <a href="{{ $session->zoom_join_url }}" target="_blank" class="action-btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                فتح في Zoom
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Grid Layout for Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Description -->
            @if($session->description)
            <div class="card-modern p-6">
                <div class="section-title">
                    <div class="section-icon" style="background: #e6f4fa;">
                        <svg class="w-6 h-6" style="color: #0071AA;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">الوصف</h2>
                </div>
                <div class="text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $session->description }}</div>
            </div>
            @endif

            <!-- Video Player -->
            @if(($session->type === 'video' || $session->type === 'recorded_video') && ($session->video_path || $session->video_url))
            <div class="card-modern overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <div class="section-title mb-0">
                        <div class="section-icon" style="background: #fef2f2;">
                            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">فيديو الجلسة</h2>
                    </div>
                </div>
                <div class="aspect-video bg-gray-900">
                    <video controls class="w-full h-full">
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
            <div class="card-modern p-6">
                <div class="section-title">
                    <div class="section-icon" style="background: #f3e8ff;">
                        <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">الملفات المرفقة</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $sessionFiles->count() }} ملف</p>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach($sessionFiles as $file)
                    <div class="file-card">
                        <div class="w-12 h-12 bg-white dark:bg-gray-600 rounded-xl flex items-center justify-center shadow-sm flex-shrink-0">
                            @php
                                $extension = pathinfo($file->original_name ?? $file->file_path, PATHINFO_EXTENSION);
                                $iconColor = match(strtolower($extension)) {
                                    'pdf' => 'text-red-500',
                                    'doc', 'docx' => 'text-blue-500',
                                    'xls', 'xlsx' => 'text-green-500',
                                    'ppt', 'pptx' => 'text-orange-500',
                                    default => 'text-gray-500'
                                };
                            @endphp
                            <svg class="w-6 h-6 {{ $iconColor }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white truncate">{{ $file->original_name ?? 'ملف' }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ strtoupper($extension) }}</p>
                        </div>
                        <a href="{{ asset('storage/' . $file->file_path) }}" download class="action-btn action-btn-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            تحميل
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">

            <!-- Quick Info -->
            <div class="card-modern p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">معلومات سريعة</h3>
                <div class="space-y-3">
                    @if($session->scheduled_at)
                    <div class="info-item">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400">التاريخ</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $session->scheduled_at->format('Y-m-d') }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="info-item">
                        <svg class="w-5 h-5 text-purple-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400">المدة</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $session->duration_minutes ?? 60 }} دقيقة</p>
                        </div>
                    </div>

                    @if($session->subject)
                    <div class="info-item">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400">المادة</p>
                            <p class="font-medium text-gray-900 dark:text-white truncate">{{ $session->subject->name }}</p>
                        </div>
                    </div>
                    @endif

                    @if($session->session_number)
                    <div class="info-item">
                        <svg class="w-5 h-5 text-orange-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 dark:text-gray-400">رقم الجلسة</p>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $session->session_number }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Stats -->
            @if($session->attendances)
            <div class="card-modern p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">الإحصائيات</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">معدل الحضور</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $session->getAttendanceRate() }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-gradient-to-r from-blue-600 to-blue-400 h-2 rounded-full transition-all duration-300" style="width: {{ $session->getAttendanceRate() }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Countdown timer script can go here if needed
</script>
@endpush
