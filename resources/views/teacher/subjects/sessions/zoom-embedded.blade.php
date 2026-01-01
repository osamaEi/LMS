@extends('layouts.dashboard')

@section('title', $session->title . ' - بث مباشر')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
<style>
    .zoom-page, .zoom-page * { font-family: 'Cairo', sans-serif !important; }
</style>
@endpush

@section('content')
<div class="zoom-page">
    <!-- Simple Breadcrumb -->
    <nav class="mb-4 text-xs">
        <ol class="flex items-center gap-1 text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('teacher.my-subjects.index') }}" class="hover:text-brand-500">موادي</a></li>
            <li>/</li>
            <li><a href="{{ route('teacher.my-subjects.show', $session->subject_id) }}" class="hover:text-brand-500">{{ $session->subject->name ?? '' }}</a></li>
            <li>/</li>
            <li class="text-gray-900 dark:text-white">{{ $session->title }}</li>
        </ol>
    </nav>

    <!-- Compact Zoom Container -->
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden" style="background: #1a1f2e;">
        <!-- Compact Header -->
        <div class="flex items-center justify-between px-4 py-2 border-b border-white/10" style="background: rgba(255,255,255,0.03);">
            <div class="flex items-center gap-3">
                <h2 class="text-sm font-bold text-white">{{ $session->title }}</h2>
                @if($session->started_at && !$session->ended_at)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold text-white bg-red-500">
                    <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                    مباشر
                </span>
                @endif
            </div>
            <a href="{{ route('teacher.my-subjects.sessions.zoom', [$session->subject_id, $session->id]) }}"
               class="flex items-center gap-1 px-3 py-1 rounded text-white text-xs" style="background: rgba(255,255,255,0.1);">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                </svg>
                ملء الشاشة
            </a>
        </div>

        <!-- Video Area Only -->
        <div class="relative bg-black flex items-center justify-center" style="min-height: 400px;">
            <!-- Compact Pre-Join Card -->
            <div id="pre-join-card" class="text-center p-5 rounded-xl max-w-sm w-full mx-3" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                <!-- Icon -->
                <div class="w-12 h-12 mx-auto mb-3 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #2563eb, #7c3aed);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="white">
                        <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>

                <h3 class="text-base font-bold text-white mb-1">{{ $session->title }}</h3>
                <p class="text-xs text-white/50 mb-4">انضم كمضيف</p>

                <!-- Meeting Info -->
                <div class="rounded-lg p-3 mb-4 text-right text-xs" style="background: rgba(255,255,255,0.05);">
                    <div class="flex justify-between items-center">
                        <span class="font-mono text-white">{{ $session->zoom_meeting_id }}</span>
                        <span class="text-white/40">ID</span>
                    </div>
                    @if($session->zoom_password)
                    <div class="flex justify-between items-center mt-2 pt-2 border-t border-white/5">
                        <span class="font-mono text-white">{{ $session->zoom_password }}</span>
                        <span class="text-white/40">كلمة المرور</span>
                    </div>
                    @endif
                </div>

                <!-- Join Form -->
                <form id="join-form">
                    <div class="mb-3 text-right">
                        <label class="block text-xs text-white/60 mb-1">اسم العرض</label>
                        <input type="text" id="display-name" value="{{ auth()->user()->name ?? '' }}" required
                               class="w-full px-3 py-2 rounded-lg text-white text-sm focus:outline-none"
                               style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1);"
                               placeholder="اسمك">
                    </div>
                    <button type="submit" id="join-btn"
                            class="w-full py-2.5 rounded-lg text-white font-bold text-sm flex items-center justify-center gap-2"
                            style="background: linear-gradient(135deg, #2563eb, #7c3aed);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        بدء الحصة
                    </button>
                </form>
            </div>

            <!-- Loading State -->
            <div id="loading" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-gray-900/95">
                <div class="w-8 h-8 border-3 border-blue-500/20 border-t-blue-500 rounded-full animate-spin"></div>
                <p class="text-white text-sm mt-3">جاري الاتصال...</p>
            </div>

            <div id="zoom-meeting-container"></div>
        </div>
    </div>
</div>

<script src="https://source.zoom.us/3.1.6/lib/vendor/react.min.js"></script>
<script src="https://source.zoom.us/3.1.6/lib/vendor/react-dom.min.js"></script>
<script src="https://source.zoom.us/3.1.6/lib/vendor/redux.min.js"></script>
<script src="https://source.zoom.us/3.1.6/lib/vendor/redux-thunk.min.js"></script>
<script src="https://source.zoom.us/3.1.6/lib/vendor/lodash.min.js"></script>
<script src="https://source.zoom.us/zoom-meeting-3.1.6.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeZoom();
    });

    function initializeZoom() {
        const config = {
            meetingNumber: '{{ $session->zoom_meeting_id }}',
            password: '{{ $session->zoom_password ?? "" }}',
            userName: '{{ auth()->user()->name ?? "" }}',
            userEmail: '{{ auth()->user()->email ?? "" }}',
            leaveUrl: '{{ route("teacher.my-subjects.show", $session->subject_id) }}',
            role: 1
        };

        if (typeof ZoomMtg === 'undefined') {
            console.error('Zoom SDK not loaded!');
            return;
        }

        ZoomMtg.setZoomJSLib('https://source.zoom.us/3.1.6/lib', '/av');
        ZoomMtg.preLoadWasm();
        ZoomMtg.prepareWebSDK();
        ZoomMtg.i18n.load('en-US');
        ZoomMtg.i18n.reload('en-US');

        const preJoinCard = document.getElementById('pre-join-card');
        const joinForm = document.getElementById('join-form');
        const joinBtn = document.getElementById('join-btn');
        const displayName = document.getElementById('display-name');
        const loading = document.getElementById('loading');

        joinForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const name = displayName.value.trim();
            if (!name) {
                alert('يرجى إدخال اسمك');
                return;
            }

            config.userName = name;
            joinBtn.disabled = true;
            joinBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> جاري...';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                const response = await fetch('/teacher/zoom/generate-signature', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        meeting_number: config.meetingNumber,
                        role: config.role
                    })
                });

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'فشل الحصول على التوقيع');
                }

                preJoinCard.classList.add('hidden');
                loading.classList.remove('hidden');

                ZoomMtg.init({
                    leaveUrl: config.leaveUrl,
                    disableCORP: !window.crossOriginIsolated,
                    success: function() {
                        setTimeout(() => loading.classList.add('hidden'), 2000);

                        ZoomMtg.join({
                            signature: data.signature,
                            sdkKey: '{{ config("services.zoom.sdk_key") }}',
                            meetingNumber: config.meetingNumber,
                            userName: config.userName,
                            userEmail: config.userEmail,
                            passWord: config.password,
                            tk: '',
                            success: function(res) {
                                console.log('Join success!', res);
                            },
                            error: function(error) {
                                console.error('Join error:', error);
                                alert('فشل الانضمام: ' + (error.errorMessage || error.reason || JSON.stringify(error)));
                                resetForm();
                            }
                        });
                    },
                    error: function(error) {
                        console.error('Init error:', error);
                        alert('خطأ: ' + (error.errorMessage || error.reason || JSON.stringify(error)));
                        resetForm();
                    }
                });

            } catch (error) {
                console.error('Error:', error);
                alert('حدث خطأ: ' + error.message);
                resetForm();
            }
        });

        function resetForm() {
            loading.classList.add('hidden');
            preJoinCard.classList.remove('hidden');
            joinBtn.disabled = false;
            joinBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg> بدء الحصة';
        }
    }
</script>
@endsection
