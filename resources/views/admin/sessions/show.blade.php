@extends('layouts.dashboard')

@section('title', 'عرض الدرس')

@push('styles')
<style>
    @keyframes pulse-ring {
        0% {
            transform: scale(0.95);
            opacity: 1;
        }
        100% {
            transform: scale(1.05);
            opacity: 0;
        }
    }

    .pulse-ring {
        animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes gradient-shift {
        0%, 100% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
    }

    .gradient-animate {
        background-size: 200% 200%;
        animation: gradient-shift 3s ease infinite;
    }

    #zoom-meeting-container {
        transition: all 0.3s ease-in-out;
    }

    .zoom-sdk-wrapper {
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    /* Custom Zoom SDK styling */
    #zmmtg-root {
        width: 100% !important;
        height: 100% !important;
        min-height: 600px;
    }

    .glass-effect {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .dark .glass-effect {
        background: rgba(17, 24, 39, 0.8);
    }

    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="mb-8">
    <div class="flex items-start justify-between mb-6">
        <div class="flex items-start gap-4">
            <a href="{{ route('admin.sessions.index') }}"
               class="flex items-center justify-center h-12 w-12 rounded-xl border-2 border-gray-300 hover:bg-gray-50 hover:border-brand-500 dark:border-gray-700 dark:hover:bg-gray-800 dark:hover:border-brand-500 transition-all duration-200">
                <svg class="h-6 w-6 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 text-lg font-bold text-white shadow-lg">
                        {{ $session->session_number }}
                    </span>
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white">{{ $session->title }}</h1>
                </div>
                <div class="flex items-center gap-2 mt-3">
                    @if($session->status === 'live')
                        <span class="relative inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-red-500 to-pink-500 px-4 py-1.5 text-sm font-bold text-white shadow-lg">
                            <span class="absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75 pulse-ring"></span>
                            <span class="relative">🔴 مباشر الآن</span>
                        </span>
                    @elseif($session->status === 'scheduled')
                        <span class="rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 px-4 py-1.5 text-sm font-bold text-white shadow-md">📅 مجدول</span>
                    @elseif($session->status === 'completed')
                        <span class="rounded-full bg-gradient-to-r from-green-500 to-emerald-500 px-4 py-1.5 text-sm font-bold text-white shadow-md">✓ مكتمل</span>
                    @else
                        <span class="rounded-full bg-gradient-to-r from-gray-400 to-gray-500 px-4 py-1.5 text-sm font-bold text-white shadow-md">✕ ملغي</span>
                    @endif

                    @if($session->is_mandatory)
                        <span class="rounded-full bg-gradient-to-r from-orange-500 to-red-500 px-4 py-1.5 text-sm font-bold text-white shadow-md">⚠️ إلزامي</span>
                    @endif

                    @if($session->type === 'live_zoom')
                        <span class="rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-1.5 text-sm font-bold text-white shadow-md flex items-center gap-1.5">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                            </svg>
                            Zoom Live
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.sessions.edit', $session) }}"
               class="rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all duration-200 hover-lift">
                ✏️ تعديل
            </a>
            <form action="{{ route('admin.sessions.destroy', $session) }}" method="POST" class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit"
                        onclick="return confirm('هل أنت متأكد من حذف هذا الدرس؟')"
                        class="rounded-xl bg-gradient-to-r from-red-500 to-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all duration-200 hover-lift">
                    🗑️ حذف
                </button>
            </form>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Countdown Timer (for scheduled sessions) -->
        @if($session->status === 'scheduled' && $session->scheduled_at)
        <div class="rounded-2xl border-2 border-purple-200 bg-gradient-to-br from-purple-50 via-white to-pink-50 p-8 dark:border-purple-900 dark:from-purple-950 dark:via-gray-900 dark:to-pink-950 shadow-2xl hover-lift overflow-hidden relative">
            <!-- Animated Background -->
            <div class="absolute inset-0 bg-gradient-to-r from-purple-400/10 via-pink-400/10 to-purple-400/10 gradient-animate"></div>

            <div class="relative z-10">
                <div class="flex items-center justify-center gap-3 mb-6">
                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center shadow-lg">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white">العد التنازلي للجلسة</h2>
                </div>

                <div id="countdown" class="grid grid-cols-4 gap-4 text-center">
                    <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm rounded-xl p-4 shadow-lg hover-lift">
                        <div class="text-4xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent" id="days">00</div>
                        <div class="text-sm font-bold text-gray-600 dark:text-gray-400 mt-2">يوم</div>
                    </div>
                    <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm rounded-xl p-4 shadow-lg hover-lift">
                        <div class="text-4xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent" id="hours">00</div>
                        <div class="text-sm font-bold text-gray-600 dark:text-gray-400 mt-2">ساعة</div>
                    </div>
                    <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm rounded-xl p-4 shadow-lg hover-lift">
                        <div class="text-4xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent" id="minutes">00</div>
                        <div class="text-sm font-bold text-gray-600 dark:text-gray-400 mt-2">دقيقة</div>
                    </div>
                    <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm rounded-xl p-4 shadow-lg hover-lift">
                        <div class="text-4xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent" id="seconds">00</div>
                        <div class="text-sm font-bold text-gray-600 dark:text-gray-400 mt-2">ثانية</div>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-gray-700 dark:text-gray-300 font-medium">
                        موعد الجلسة: <span class="font-bold text-purple-600 dark:text-purple-400">{{ $session->scheduled_at->format('Y-m-d h:i A') }}</span>
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- Zoom Meeting Section (Full Width, Impressive Design) -->
        @if($session->zoom_meeting_id || $session->zoom_join_url)
        <div class="rounded-2xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 via-white to-indigo-50 p-8 dark:border-blue-900 dark:from-blue-950 dark:via-gray-900 dark:to-indigo-950 shadow-2xl hover-lift">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg">
                        <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white">اجتماع Zoom المباشر</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">انضم الآن للحصة المباشرة</p>
                    </div>
                </div>
                @if($session->status === 'live')
                <span class="relative inline-flex items-center gap-2 rounded-full bg-red-500 px-4 py-2 text-sm font-bold text-white shadow-lg">
                    <span class="absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75 pulse-ring"></span>
                    <span class="relative flex h-3 w-3 rounded-full bg-white"></span>
                    <span class="relative">LIVE</span>
                </span>
                @endif
            </div>

            @if($session->zoom_meeting_id)
            <!-- Zoom Web SDK Integration -->
            <div class="mb-6">
                <!-- Join Form -->
                <div id="join-form" class="space-y-5">
                    <div class="rounded-xl bg-white dark:bg-gray-900 p-6 shadow-lg border border-gray-200 dark:border-gray-800">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">
                            👤 اسمك في الاجتماع
                        </label>
                        <input type="text" id="display-name"
                               value="{{ auth()->user()->name ?? 'Guest' }}"
                               class="w-full rounded-xl border-2 border-gray-300 bg-white px-5 py-3.5 text-lg font-medium text-gray-900 focus:border-blue-500 focus:ring-4 focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-900 transition-all"
                               placeholder="أدخل اسمك">
                    </div>

                    <button type="button" id="join-meeting-btn"
                            class="w-full group relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-8 py-5 text-lg font-black text-white shadow-2xl hover:shadow-blue-500/50 transition-all duration-300 hover:scale-[1.02] gradient-animate">
                        <span class="relative z-10 flex items-center justify-center gap-3">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span>🚀 الانضمام إلى الاجتماع الآن</span>
                        </span>
                    </button>

                    <div class="flex items-center justify-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>سيتم فتح الاجتماع مباشرة في هذه الصفحة</p>
                    </div>
                </div>

                <!-- Zoom Web SDK Container -->
                <div id="zoom-meeting-container" style="display: none;">
                    <div class="zoom-sdk-wrapper bg-black relative">
                        <div id="zmmtg-root" class="w-full h-full"></div>
                    </div>
                    <button type="button" id="leave-meeting-btn"
                            class="mt-6 w-full flex items-center justify-center gap-3 rounded-2xl bg-gradient-to-r from-red-500 to-red-600 px-6 py-4 text-base font-bold text-white shadow-xl hover:shadow-red-500/50 transition-all duration-300 hover:scale-[1.02]">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        مغادرة الاجتماع
                    </button>
                </div>
            </div>
            @endif

            <!-- Meeting Details Card -->
            <div class="rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 p-6 shadow-2xl">
                <h3 class="text-white font-bold text-lg mb-4 flex items-center gap-2">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    معلومات الاجتماع
                </h3>

                <div class="space-y-3">
                    @if($session->zoom_meeting_id)
                    <div class="flex items-center justify-between p-4 rounded-xl bg-white/10 backdrop-blur-sm">
                        <span class="text-sm font-bold text-white/90">🔢 رقم الاجتماع</span>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-mono font-black text-white">{{ $session->zoom_meeting_id }}</span>
                            <button onclick="navigator.clipboard.writeText('{{ $session->zoom_meeting_id }}')"
                                    class="p-2 rounded-lg bg-white/20 hover:bg-white/30 transition-colors">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endif

                    @if($session->zoom_password)
                    <div class="flex items-center justify-between p-4 rounded-xl bg-white/10 backdrop-blur-sm">
                        <span class="text-sm font-bold text-white/90">🔐 كلمة المرور</span>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-mono font-black text-white">{{ $session->zoom_password }}</span>
                            <button onclick="navigator.clipboard.writeText('{{ $session->zoom_password }}')"
                                    class="p-2 rounded-lg bg-white/20 hover:bg-white/30 transition-colors">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endif

                    @if($session->zoom_join_url)
                    <div class="pt-3 mt-3 border-t border-white/20">
                        <a href="{{ $session->zoom_join_url }}" target="_blank"
                           class="flex items-center justify-center gap-3 rounded-xl bg-white px-6 py-4 text-base font-bold text-blue-600 hover:bg-gray-50 transition-all duration-200 shadow-xl hover:scale-[1.02]">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            فتح في تطبيق Zoom
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Alternative Methods -->
            <div class="mt-6 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700 p-5 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm">
                <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    طرق أخرى للانضمام
                </p>
                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                    <li class="flex items-start gap-2">
                        <span class="text-blue-600 dark:text-blue-400">1.</span>
                        <span>افتح تطبيق Zoom على جهازك</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-blue-600 dark:text-blue-400">2.</span>
                        <span>اضغط "الانضمام إلى اجتماع"</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-blue-600 dark:text-blue-400">3.</span>
                        <span>أدخل: <code class="px-2 py-1 bg-gray-200 dark:bg-gray-800 rounded font-mono text-xs">{{ $session->zoom_meeting_id }}</code></span>
                    </li>
                </ul>
            </div>
        </div>

        @push('scripts')
        <!-- Zoom Web SDK -->
        <link type="text/css" rel="stylesheet" href="https://source.zoom.us/3.8.10/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="https://source.zoom.us/3.8.10/css/react-select.css" />
        <script src="https://source.zoom.us/3.8.10/lib/vendor/react.min.js"></script>
        <script src="https://source.zoom.us/3.8.10/lib/vendor/react-dom.min.js"></script>
        <script src="https://source.zoom.us/3.8.10/lib/vendor/redux.min.js"></script>
        <script src="https://source.zoom.us/3.8.10/lib/vendor/redux-thunk.min.js"></script>
        <script src="https://source.zoom.us/3.8.10/lib/vendor/lodash.min.js"></script>
        <script src="https://source.zoom.us/zoom-meeting-3.8.10.min.js"></script>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const joinBtn = document.getElementById('join-meeting-btn');
            const leaveBtn = document.getElementById('leave-meeting-btn');
            const joinForm = document.getElementById('join-form');
            const meetingContainer = document.getElementById('zoom-meeting-container');
            const displayNameInput = document.getElementById('display-name');

            const meetingConfig = {
                meetingNumber: '{{ $session->zoom_meeting_id }}',
                password: '{{ $session->zoom_password ?? "" }}',
                userName: displayNameInput.value,
                userEmail: '{{ auth()->user()->email ?? "" }}',
                leaveUrl: window.location.href,
                role: 1, // 0 = Participant, 1 = Host (allows starting meeting)
            };

            ZoomMtg.setZoomJSLib('https://source.zoom.us/3.8.10/lib', '/av');
            ZoomMtg.preLoadWasm();
            ZoomMtg.prepareWebSDK();

            joinBtn.addEventListener('click', async function() {
                const displayName = displayNameInput.value.trim();
                if (!displayName) {
                    alert('يرجى إدخال اسمك أولاً');
                    displayNameInput.focus();
                    return;
                }

                meetingConfig.userName = displayName;
                joinBtn.disabled = true;
                const originalHTML = joinBtn.innerHTML;
                joinBtn.innerHTML = '<span class="flex items-center justify-center gap-2"><svg class="animate-spin h-6 w-6" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>جاري الانضمام...</span>';

                try {
                    const signatureResponse = await fetch('/admin/zoom/generate-signature', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            meeting_number: meetingConfig.meetingNumber,
                            role: meetingConfig.role
                        })
                    });

                    const signatureData = await signatureResponse.json();

                    if (!signatureResponse.ok || !signatureData.success) {
                        throw new Error(signatureData.message || 'فشل الحصول على التوقيع الرقمي');
                    }

                    joinForm.style.display = 'none';
                    meetingContainer.style.display = 'block';

                    ZoomMtg.init({
                        leaveUrl: meetingConfig.leaveUrl,

                        // ========== Core Features ==========
                        isSupportAV: true,           // Enable audio/video
                        disableCORP: !window.crossOriginIsolated, // CORS support

                        // ========== UI Customization ==========
                        disablePreview: true,        // Skip video preview before joining
                        disableJoinAudio: false,     // Enable audio join
                        audioPanelAlwaysOpen: false, // Don't auto-open audio panel
                        showPureSharingContent: false, // Show full interface (not minimal)
                        videoDrag: true,             // Allow dragging video
                        sharingMode: 'both',         // 'both', 'fit', 'original'
                        showMeetingHeader: true,     // Show meeting header
                        disableInvite: false,        // Allow inviting participants
                        disableCallOut: false,       // Allow call out
                        disableRecord: false,        // Allow recording

                        // ========== Communication Features ==========
                        isSupportChat: true,         // Enable chat
                        isSupportQA: true,           // Enable Q&A
                        isSupportCC: true,           // Enable closed captions
                        isSupportBreakout: true,     // Enable breakout rooms

                        // ========== Sharing Features ==========
                        screenShare: true,           // Enable screen sharing

                        // ========== Advanced Features ==========
                        isSupportNonverbal: true,    // Enable non-verbal feedback (reactions)
                        isSupportPolling: true,      // Enable polling

                        // ========== Security & Links ==========
                        isShowJoiningErrorDialog: true, // Show error dialogs

                        success: function() {
                            ZoomMtg.join({
                                signature: signatureData.signature,
                                sdkKey: '{{ config("services.zoom.sdk_key") }}',
                                meetingNumber: meetingConfig.meetingNumber,
                                userName: meetingConfig.userName,
                                userEmail: meetingConfig.userEmail,
                                passWord: meetingConfig.password,
                                success: function() {
                                    console.log('✅ Joined meeting successfully');
                                    console.log('✅ All Zoom features enabled!');
                                    console.log('✅ Chat, Q&A, Polls, Reactions, Breakout Rooms - كل شيء مفعّل!');
                                },
                                error: function(error) {
                                    console.error('❌ Join error:', error);
                                    alert('فشل الانضمام للاجتماع. يرجى استخدام رابط الانضمام المباشر.');
                                    joinForm.style.display = 'block';
                                    meetingContainer.style.display = 'none';
                                    joinBtn.disabled = false;
                                    joinBtn.innerHTML = originalHTML;
                                }
                            });
                        },
                        error: function(error) {
                            console.error('❌ Init error:', error);
                            alert('حدث خطأ في تهيئة Zoom. يرجى استخدام رابط الانضمام المباشر.');
                            joinForm.style.display = 'block';
                            meetingContainer.style.display = 'none';
                            joinBtn.disabled = false;
                            joinBtn.innerHTML = originalHTML;
                        }
                    });
                } catch (error) {
                    console.error('❌ Error:', error);
                    alert('حدث خطأ: ' + error.message);
                    joinBtn.disabled = false;
                    joinBtn.innerHTML = originalHTML;
                }
            });

            leaveBtn.addEventListener('click', function() {
                ZoomMtg.endMeeting({});
                joinForm.style.display = 'block';
                meetingContainer.style.display = 'none';
            });
        });
        </script>
        @endpush
        @endif

        <!-- Session Info Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-lg hover-lift">
            <h2 class="text-xl font-black text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                <svg class="h-6 w-6 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                معلومات الدرس
            </h2>
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="rounded-xl bg-gradient-to-br from-brand-50 to-brand-100 dark:from-brand-950 dark:to-brand-900 p-4">
                    <dt class="text-xs font-bold text-brand-600 dark:text-brand-400 mb-1">المادة الدراسية</dt>
                    <dd>
                        <a href="{{ route('admin.subjects.show', $session->subject) }}"
                           class="text-base font-black text-brand-700 hover:text-brand-800 dark:text-brand-300">
                            {{ $session->subject->name }}
                        </a>
                    </dd>
                </div>
                <div class="rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-950 dark:to-purple-900 p-4">
                    <dt class="text-xs font-bold text-purple-600 dark:text-purple-400 mb-1">الفصل الدراسي</dt>
                    <dd class="text-base font-black text-purple-900 dark:text-purple-100">{{ $session->subject->term->name ?? '-' }}</dd>
                </div>
                @if($session->scheduled_at)
                <div class="rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-950 dark:to-blue-900 p-4">
                    <dt class="text-xs font-bold text-blue-600 dark:text-blue-400 mb-1">📅 التاريخ والوقت</dt>
                    <dd class="text-base font-black text-blue-900 dark:text-blue-100">
                        {{ $session->scheduled_at->format('Y/m/d - h:i A') }}
                    </dd>
                </div>
                @endif
                @if($session->duration_minutes)
                <div class="rounded-xl bg-gradient-to-br from-green-50 to-green-100 dark:from-green-950 dark:to-green-900 p-4">
                    <dt class="text-xs font-bold text-green-600 dark:text-green-400 mb-1">⏱️ المدة</dt>
                    <dd class="text-base font-black text-green-900 dark:text-green-100">{{ $session->duration_minutes }} دقيقة</dd>
                </div>
                @endif
            </dl>

            @if($session->description)
            <div class="mt-6 pt-6 border-t-2 border-gray-200 dark:border-gray-800">
                <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-3">📝 الوصف</h3>
                <p class="text-base leading-relaxed text-gray-900 dark:text-white">{{ $session->description }}</p>
            </div>
            @endif
        </div>

        <!-- Attached Files -->
        @if($session->files->count() > 0)
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-lg hover-lift">
            <h2 class="text-xl font-black text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                <svg class="h-6 w-6 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                الملفات المرفقة ({{ $session->files->count() }})
            </h2>

            <div class="grid grid-cols-1 gap-3">
                @foreach($session->files as $file)
                <div class="flex items-center justify-between rounded-xl border-2 border-gray-200 p-4 dark:border-gray-700 hover:border-brand-500 dark:hover:border-brand-500 transition-all hover-lift bg-gradient-to-r from-white to-gray-50 dark:from-gray-900 dark:to-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-600 shadow-lg">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $file->file_name }}</p>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                {{ number_format($file->file_size / 1024, 2) }} KB
                            </p>
                        </div>
                    </div>
                    <a href="{{ Storage::url($file->file_path) }}" download
                       class="rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-4 py-2 text-sm font-bold text-white hover:shadow-lg transition-all">
                        ⬇️ تحميل
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Quick Stats -->
        <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-white to-gray-50 p-6 dark:border-gray-800 dark:from-gray-900 dark:to-gray-950 shadow-lg hover-lift">
            <h2 class="text-lg font-black text-gray-900 dark:text-white mb-5">📊 إحصائيات سريعة</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 rounded-xl bg-white dark:bg-gray-800 shadow">
                    <span class="text-sm font-bold text-gray-600 dark:text-gray-400">رقم الدرس</span>
                    <span class="text-2xl font-black text-brand-600 dark:text-brand-400">#{{ $session->session_number }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-white dark:bg-gray-800 shadow">
                    <span class="text-sm font-bold text-gray-600 dark:text-gray-400">الملفات</span>
                    <span class="text-2xl font-black text-purple-600 dark:text-purple-400">{{ $session->files->count() }}</span>
                </div>
                @if($session->duration_minutes)
                <div class="flex items-center justify-between p-3 rounded-xl bg-white dark:bg-gray-800 shadow">
                    <span class="text-sm font-bold text-gray-600 dark:text-gray-400">المدة</span>
                    <span class="text-2xl font-black text-green-600 dark:text-green-400">{{ $session->duration_minutes }}د</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Subject Info -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-lg hover-lift">
            <h2 class="text-lg font-black text-gray-900 dark:text-white mb-4">📚 المادة الدراسية</h2>
            <div class="space-y-4">
                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">اسم المادة</p>
                    <a href="{{ route('admin.subjects.show', $session->subject) }}"
                       class="text-base font-black text-brand-600 hover:text-brand-700 dark:text-brand-400">
                        {{ $session->subject->name }}
                    </a>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">كود المادة</p>
                    <p class="text-base font-black text-gray-900 dark:text-white">{{ $session->subject->code }}</p>
                </div>
                @if($session->subject->teacher)
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-2">المعلم</p>
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($session->subject->teacher->name) }}&background=6366F1&color=fff&bold=true"
                             alt="{{ $session->subject->teacher->name }}"
                             class="h-10 w-10 rounded-full shadow-lg ring-2 ring-brand-500">
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $session->subject->teacher->name }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 shadow-lg">
            <h2 class="text-lg font-black text-gray-900 dark:text-white mb-4">⚡ إجراءات سريعة</h2>
            <div class="space-y-3">
                <a href="{{ route('admin.sessions.edit', $session) }}"
                   class="block w-full rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 px-4 py-3 text-center text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all hover-lift">
                    ✏️ تعديل الدرس
                </a>
                <a href="{{ route('admin.subjects.show', $session->subject) }}"
                   class="block w-full rounded-xl border-2 border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-all">
                    📚 عرض المادة
                </a>
                <a href="{{ route('admin.sessions.index') }}"
                   class="block w-full rounded-xl border-2 border-gray-300 px-4 py-3 text-center text-sm font-bold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-all">
                    ← العودة للقائمة
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Countdown Timer
@if($session->status === 'scheduled' && $session->scheduled_at)
(function() {
    const scheduledDate = new Date('{{ $session->scheduled_at->toIso8601String() }}').getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = scheduledDate - now;

        if (distance < 0) {
            // Countdown finished
            document.getElementById('countdown').innerHTML = `
                <div class="col-span-4 text-center">
                    <div class="text-3xl font-black bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                        🎉 بدأت الجلسة! انضم الآن 🎉
                    </div>
                </div>
            `;
            return;
        }

        // Calculate time units
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Update display with leading zeros
        document.getElementById('days').textContent = String(days).padStart(2, '0');
        document.getElementById('hours').textContent = String(hours).padStart(2, '0');
        document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
        document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
    }

    // Update immediately
    updateCountdown();

    // Update every second
    setInterval(updateCountdown, 1000);
})();
@endif
</script>
@endpush

@endsection
