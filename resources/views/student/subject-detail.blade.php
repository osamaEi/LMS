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
    .session-card {
        background: white;
        border-radius: 16px;
        border: 2px solid #f1f5f9;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .dark .session-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.1);
    }
    .session-card:hover {
        border-color: #0071AA;
        box-shadow: 0 8px 30px rgba(0, 113, 170, 0.1);
    }
    .session-toggle {
        cursor: pointer;
        width: 100%;
        text-align: right;
        background: none;
        border: none;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .session-toggle:hover {
        background: #f8fafc;
    }
    .dark .session-toggle:hover {
        background: #334155;
    }
    .session-details {
        display: none;
        padding: 0 1.5rem 1.5rem;
        border-top: 1px solid #f1f5f9;
    }
    .dark .session-details {
        border-color: rgba(255,255,255,0.1);
    }
    .session-card.open .session-details {
        display: block;
    }
    .session-card.open .toggle-icon {
        transform: rotate(180deg);
    }
    .toggle-icon {
        transition: transform 0.3s ease;
    }
    .file-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: #f8fafc;
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    .dark .file-item {
        background: #0f172a;
    }
    .file-item:hover {
        background: #eff6ff;
        transform: translateX(-4px);
    }
    .dark .file-item:hover {
        background: #334155;
    }
    .stat-mini {
        text-align: center;
        padding: 1rem;
        border-radius: 14px;
    }
    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">

    {{-- Subject Header --}}
    <div class="subject-header mb-6">
        <div class="relative z-10">
            <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-1 text-white/70 hover:text-white text-sm mb-4 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                العودة للوحة التحكم
            </a>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">{{ $subject->name }}</h1>
                    <p class="text-white/70">{{ $subject->term->program->name ?? '' }} - {{ $subject->term->name ?? '' }}</p>
                    @if($subject->code)
                        <span class="inline-block mt-2 px-3 py-1 bg-white/15 rounded-lg text-white/90 text-sm font-medium">{{ $subject->code }}</span>
                    @endif
                </div>
                <a href="{{ route('student.quizzes.index', $subject->id) }}"
                   class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold text-white transition-all hover:scale-105"
                   style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 6px 20px rgba(16,185,129,0.4);">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    الاختبارات
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content: Sessions --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">الجلسات ({{ $sessions->count() }})</h2>
            </div>

            @if($sessions->count() > 0)
                @foreach($sessions as $session)
                <div class="session-card" id="session-card-{{ $session->id }}">
                    <button type="button" class="session-toggle" onclick="toggleSession({{ $session->id }})">
                        {{-- Session Number Badge --}}
                        <div style="width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: white; font-weight: 800; font-size: 0.9rem;
                            background: {{ $session->ended_at ? 'linear-gradient(135deg, #10b981, #059669)' : ($session->started_at ? 'linear-gradient(135deg, #ef4444, #dc2626)' : 'linear-gradient(135deg, #3b82f6, #2563eb)') }};">
                            {{ $session->session_number }}
                        </div>

                        {{-- Session Info --}}
                        <div class="flex-1 text-right">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <h3 class="font-bold text-gray-900 dark:text-white text-sm md:text-base">{{ $session->title }}</h3>
                                @if($session->ended_at)
                                    <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded-full font-medium dark:bg-green-900 dark:text-green-300">مكتمل</span>
                                @elseif($session->started_at)
                                    <span class="text-xs px-2 py-0.5 bg-red-100 text-red-700 rounded-full font-medium animate-pulse dark:bg-red-900 dark:text-red-300">مباشر</span>
                                @else
                                    <span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full font-medium dark:bg-blue-900 dark:text-blue-300">مجدول</span>
                                @endif
                                <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full dark:bg-gray-700 dark:text-gray-300">
                                    {{ $session->type === 'live_zoom' ? 'Zoom' : 'فيديو' }}
                                </span>
                            </div>
                            @if($session->scheduled_at)
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($session->scheduled_at)->translatedFormat('l، d F Y - h:i A') }}
                            </div>
                            @endif
                        </div>

                        {{-- Toggle Arrow --}}
                        <svg class="w-5 h-5 text-gray-400 toggle-icon flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Expandable Details --}}
                    <div class="session-details">
                        <div class="pt-4 space-y-4">
                            {{-- Description --}}
                            @if($session->description)
                            <div>
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">الوصف</div>
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $session->description }}</p>
                            </div>
                            @endif

                            {{-- Session Info Grid --}}
                            <div class="grid grid-cols-2 gap-3">
                                @if($session->duration_minutes)
                                <div class="bg-amber-50 dark:bg-amber-900/30 rounded-xl p-3 text-center">
                                    <div class="text-xs text-amber-600 dark:text-amber-400 mb-1">المدة</div>
                                    <div class="font-bold text-amber-800 dark:text-amber-200">{{ $session->duration_minutes }} دقيقة</div>
                                </div>
                                @endif
                                @if($session->scheduled_at)
                                <div class="bg-blue-50 dark:bg-blue-900/30 rounded-xl p-3 text-center">
                                    <div class="text-xs text-blue-600 dark:text-blue-400 mb-1">الموعد</div>
                                    <div class="font-bold text-blue-800 dark:text-blue-200 text-sm">{{ \Carbon\Carbon::parse($session->scheduled_at)->diffForHumans() }}</div>
                                </div>
                                @endif
                            </div>

                            {{-- Attendance Status --}}
                            @if(isset($attendances[$session->id]))
                                @php $att = $attendances[$session->id]; @endphp
                                <div class="flex items-center gap-2 p-3 rounded-xl {{ $att->attended ? 'bg-green-50 dark:bg-green-900/30' : 'bg-red-50 dark:bg-red-900/30' }}">
                                    @if($att->attended)
                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        <span class="text-sm font-medium text-green-700 dark:text-green-300">تم تسجيل حضورك</span>
                                    @else
                                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                        <span class="text-sm font-medium text-red-700 dark:text-red-300">غائب</span>
                                    @endif
                                </div>
                            @endif

                            {{-- Session Files --}}
                            @if($session->files && $session->files->count() > 0)
                            <div>
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-2 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    الملفات المرفقة ({{ $session->files->count() }})
                                </div>
                                <div class="space-y-2">
                                    @foreach($session->files as $file)
                                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="file-item">
                                        <div style="width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
                                            background: linear-gradient(135deg, #ef4444, #dc2626);">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $file->title }}</div>
                                            @if($file->file_size)
                                            <div class="text-xs text-gray-500">{{ number_format($file->file_size / 1024, 0) }} KB</div>
                                            @endif
                                        </div>
                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Recording --}}
                            @if($session->type === 'live_zoom' && $session->video_url)
                            <div>
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-2 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    التسجيل
                                </div>
                                <div class="bg-black rounded-xl overflow-hidden">
                                    <video class="w-full" controls controlsList="nodownload" preload="metadata">
                                        <source src="{{ $session->getVideoUrl() }}" type="video/mp4">
                                        متصفحك لا يدعم تشغيل الفيديو
                                    </video>
                                </div>
                            </div>
                            @elseif($session->type === 'recorded_video' && $session->hasVideo())
                            <div>
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-2 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    الفيديو
                                </div>
                                <div class="bg-black rounded-xl overflow-hidden">
                                    <video class="w-full" controls controlsList="nodownload" preload="metadata">
                                        <source src="{{ $session->getVideoUrl() }}" type="video/mp4">
                                        متصفحك لا يدعم تشغيل الفيديو
                                    </video>
                                </div>
                            </div>
                            @endif

                            {{-- Join Zoom Button --}}
                            @if($session->type === 'live_zoom' && $session->started_at && !$session->ended_at && $session->zoom_join_url)
                            <a href="{{ route('student.sessions.join-zoom', $session->id) }}"
                               class="flex items-center justify-center gap-2 w-full py-3 rounded-xl text-white font-bold text-sm transition-all hover:scale-[1.02]"
                               style="background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 6px 20px rgba(239,68,68,0.4);">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                انضم الآن
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="info-card p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">لا توجد جلسات لهذه المادة بعد</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Teacher Info --}}
            <div class="info-card">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="font-bold text-gray-900 dark:text-white">المعلم</h2>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg"
                             style="background: linear-gradient(135deg, #0071AA, #005a88);">
                            {{ mb_substr($subject->teacher->name ?? 'غ', 0, 1) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white">{{ $subject->teacher->name ?? 'غير محدد' }}</div>
                            @if($subject->teacher)
                            <div class="text-xs text-gray-500">{{ $subject->teacher->email }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Subject Info --}}
            <div class="info-card">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="font-bold text-gray-900 dark:text-white">معلومات المادة</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">البرنامج</div>
                        <div class="font-medium text-gray-900 dark:text-white text-sm">{{ $subject->term->program->name ?? 'غير محدد' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">الفصل الدراسي</div>
                        <div class="font-medium text-gray-900 dark:text-white text-sm">{{ $subject->term->name ?? 'غير محدد' }}</div>
                    </div>
                    @if($subject->credits)
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">الساعات المعتمدة</div>
                        <div class="font-medium text-gray-900 dark:text-white text-sm">{{ $subject->credits }}</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Statistics --}}
            <div class="info-card">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="font-bold text-gray-900 dark:text-white">الإحصائيات</h2>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="stat-mini" style="background: linear-gradient(135deg, #eff6ff, #dbeafe);">
                            <div class="text-2xl font-bold text-blue-700">{{ $sessions->count() }}</div>
                            <div class="text-xs text-blue-600 mt-1">إجمالي الجلسات</div>
                        </div>
                        <div class="stat-mini" style="background: linear-gradient(135deg, #ecfdf5, #d1fae5);">
                            <div class="text-2xl font-bold text-green-700">{{ $sessions->where('ended_at', '!=', null)->count() }}</div>
                            <div class="text-xs text-green-600 mt-1">مكتملة</div>
                        </div>
                    </div>

                    @php
                        $totalAttended = $attendances->where('attended', true)->count();
                        $totalSessions = $sessions->where('ended_at', '!=', null)->count();
                        $attendanceRate = $totalSessions > 0 ? round(($totalAttended / $totalSessions) * 100) : 0;
                        $completionRate = $sessions->count() > 0 ? round(($sessions->where('ended_at', '!=', null)->count() / $sessions->count()) * 100) : 0;
                    @endphp

                    <div class="space-y-3">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-gray-600 dark:text-gray-400">نسبة الإنجاز</span>
                                <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $completionRate }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full transition-all" style="width: {{ $completionRate }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-gray-600 dark:text-gray-400">نسبة الحضور</span>
                                <span class="text-xs font-bold text-gray-900 dark:text-white">{{ $attendanceRate }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all" style="width: {{ $attendanceRate }}%; background: linear-gradient(135deg, #0071AA, #005a88);"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleSession(id) {
    const card = document.getElementById('session-card-' + id);
    card.classList.toggle('open');
}
</script>
@endpush
