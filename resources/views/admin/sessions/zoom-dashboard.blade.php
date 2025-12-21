@extends('layouts.dashboard')

@section('title', $session->title . ' - Zoom Live')

@push('styles')
<link rel="stylesheet" href="https://source.zoom.us/3.8.10/css/bootstrap.css">
<link rel="stylesheet" href="https://source.zoom.us/3.8.10/css/react-select.css">

<style>
    /* Remove default dashboard padding for full height */
    main.bg-white {
        padding: 0 !important;
    }

    main.bg-white > div {
        padding: 0 !important;
        max-width: 100% !important;
        margin: 0 !important;
    }

    .zoom-dashboard-container {
        display: flex;
        height: calc(100vh - 73px); /* Full height minus header */
        overflow: hidden;
        padding-top: 60px; /* Space above live video */
    }

    .zoom-sidebar {
        width: 320px;
        background: white;
        border-left: 1px solid #e5e7eb;
        overflow-y: auto;
        padding: 20px;
        flex-shrink: 0;
    }

    .zoom-video-area {
        flex: 1;
        background: #000;
        position: relative;
        overflow: hidden;
    }

    .join-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 100;
    }

    .join-overlay.hidden {
        display: none !important;
    }

    .join-card {
        background: white;
        border-radius: 16px;
        padding: 40px;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .join-card h2 {
        font-size: 24px;
        font-weight: 800;
        color: #1a202c;
        text-align: center;
        margin-bottom: 20px;
    }

    .join-card input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 15px;
        margin-bottom: 16px;
    }

    .join-card input:focus {
        outline: none;
        border-color: #3182ce;
    }

    .join-card button {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #3182ce 0%, #2c5aa0 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .join-card button:hover:not(:disabled) {
        transform: translateY(-2px);
    }

    .join-card button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        display: none;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        z-index: 99;
    }

    .loading-overlay.active {
        display: flex;
    }

    .spinner {
        width: 50px;
        height: 50px;
        border: 4px solid rgba(255,255,255,0.2);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 20px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .loading-text {
        color: white;
        font-size: 16px;
    }

    /* Zoom SDK container */
    #zmmtg-root {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        z-index: 50 !important;
        background-color: transparent !important;
    }

    /* Zoom preview flow padding */
    .preview-new-flow {
        padding-top: 131px !important;
    }

    /* Sidebar styles */
    .info-card {
        background: #f9fafb;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .info-card h3 {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 12px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        color: #6b7280;
        font-size: 14px;
    }

    .info-value {
        color: #1f2937;
        font-size: 14px;
        font-weight: 600;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-live {
        background: #fee2e2;
        color: #dc2626;
    }

    .badge-scheduled {
        background: #dbeafe;
        color: #2563eb;
    }

    .badge-completed {
        background: #d1fae5;
        color: #059669;
    }

    .action-btn {
        width: 100%;
        padding: 10px;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 8px;
        transition: background 0.2s;
    }

    .action-btn:hover {
        background: #2563eb;
    }

    .action-btn.secondary {
        background: #6b7280;
    }

    .action-btn.secondary:hover {
        background: #4b5563;
    }
</style>
@endpush

@section('content')
<div class="zoom-dashboard-container">
    <!-- Zoom Video Area -->
    <div class="zoom-video-area">
        <!-- Join Overlay -->
        <div class="join-overlay" id="join-overlay">
            <div class="join-card">
                <h2>{{ $session->title }}</h2>
                <div style="text-align: center; color: #718096; font-size: 14px; margin-bottom: 20px;">
                    Meeting ID: {{ $session->zoom_meeting_id }}
                </div>
                <form id="join-form">
                    <input
                        type="text"
                        id="display-name"
                        placeholder="أدخل اسمك"
                        value="{{ auth()->user()->name ?? '' }}"
                        required
                        autofocus
                    >
                    <button type="submit" id="join-btn">الانضمام الآن</button>
                </form>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div class="loading-overlay" id="loading">
            <div class="spinner"></div>
            <div class="loading-text">جاري الاتصال بالاجتماع...</div>
        </div>

        <!-- Zoom Meeting Container -->
        <div id="zoom-meeting-container"></div>
    </div>

    <!-- Sidebar -->
    <div class="zoom-sidebar">
        <h2 style="font-size: 20px; font-weight: 800; color: #1f2937; margin-bottom: 20px;">معلومات الجلسة</h2>

        <!-- Status -->
        <div class="info-card">
            <h3>الحالة</h3>
            <div style="margin-top: 8px;">
                @if($session->status === 'live')
                    <span class="badge badge-live">🔴 مباشر الآن</span>
                @elseif($session->status === 'scheduled')
                    <span class="badge badge-scheduled">📅 مجدول</span>
                @elseif($session->status === 'completed')
                    <span class="badge badge-completed">✓ مكتمل</span>
                @endif
            </div>
        </div>

        <!-- Session Info -->
        <div class="info-card">
            <h3>تفاصيل الدرس</h3>
            <div class="info-row">
                <span class="info-label">العنوان</span>
                <span class="info-value">{{ $session->title }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">الرقم</span>
                <span class="info-value">{{ $session->session_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">المدة</span>
                <span class="info-value">{{ $session->duration }} دقيقة</span>
            </div>
            @if($session->scheduled_at)
            <div class="info-row">
                <span class="info-label">الموعد</span>
                <span class="info-value" style="font-size: 12px;">
                    {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y-m-d H:i') }}
                </span>
            </div>
            @endif
        </div>

        <!-- Subject Info -->
        @if($session->subject)
        <div class="info-card">
            <h3>المادة</h3>
            <div class="info-row">
                <span class="info-label">المادة</span>
                <span class="info-value">{{ $session->subject->name }}</span>
            </div>
            @if($session->subject->term)
            <div class="info-row">
                <span class="info-label">الفصل</span>
                <span class="info-value">{{ $session->subject->term->name }}</span>
            </div>
            @endif
            @if($session->subject->term && $session->subject->term->program)
            <div class="info-row">
                <span class="info-label">البرنامج</span>
                <span class="info-value">{{ $session->subject->term->program->name }}</span>
            </div>
            @endif
        </div>
        @endif

        <!-- Zoom Info -->
        <div class="info-card">
            <h3>معلومات Zoom</h3>
            <div class="info-row">
                <span class="info-label">Meeting ID</span>
                <span class="info-value" style="font-family: monospace; font-size: 12px;">
                    {{ $session->zoom_meeting_id }}
                </span>
            </div>
            @if($session->zoom_password)
            <div class="info-row">
                <span class="info-label">Password</span>
                <span class="info-value" style="font-family: monospace; font-size: 12px;">
                    {{ $session->zoom_password }}
                </span>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div style="margin-top: 20px;">
            <a href="{{ $session->zoom_join_url }}" target="_blank">
                <button class="action-btn">فتح في تطبيق Zoom</button>
            </a>
            <a href="{{ route('admin.sessions.show', $session) }}">
                <button class="action-btn secondary">عرض تفاصيل الجلسة</button>
            </a>
        </div>

        <!-- Description -->
        @if($session->description)
        <div class="info-card" style="margin-top: 20px;">
            <h3>الوصف</h3>
            <div style="color: #4b5563; font-size: 14px; line-height: 1.6; white-space: pre-line;">
                {{ $session->description }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://source.zoom.us/3.8.10/lib/vendor/react.min.js"></script>
<script src="https://source.zoom.us/3.8.10/lib/vendor/react-dom.min.js"></script>
<script src="https://source.zoom.us/3.8.10/lib/vendor/redux.min.js"></script>
<script src="https://source.zoom.us/3.8.10/lib/vendor/redux-thunk.min.js"></script>
<script src="https://source.zoom.us/3.8.10/lib/vendor/lodash.min.js"></script>
<script src="https://source.zoom.us/zoom-meeting-3.8.10.min.js"></script>

<script>
    window.onerror = function(msg, url, lineNo, columnNo, error) {
        console.error('JavaScript Error:', {
            message: msg,
            url: url,
            line: lineNo,
            column: columnNo,
            error: error
        });
        return false;
    };
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📄 DOM fully loaded');
        initializeZoom();
    });

    function initializeZoom() {
        const config = {
            meetingNumber: '{{ $session->zoom_meeting_id }}',
            password: '{{ $session->zoom_password ?? "" }}',
            userName: '{{ auth()->user()->name ?? "" }}',
            userEmail: '{{ auth()->user()->email ?? "" }}',
            leaveUrl: '{{ route("admin.sessions.show", $session) }}',
            role: 1
        };

        console.log('🚀 Initializing Zoom SDK...');

        if (typeof ZoomMtg === 'undefined') {
            console.error('❌ Zoom SDK not loaded!');
            alert('خطأ: لم يتم تحميل Zoom SDK. يرجى إعادة تحميل الصفحة.');
            return;
        }

        console.log('✅ Zoom SDK loaded successfully');

        ZoomMtg.setZoomJSLib('https://source.zoom.us/3.8.10/lib', '/av');
        ZoomMtg.preLoadWasm();
        ZoomMtg.prepareWebSDK();
        ZoomMtg.i18n.load('en-US');
        ZoomMtg.i18n.reload('en-US');

        const joinOverlay = document.getElementById('join-overlay');
        const joinForm = document.getElementById('join-form');
        const joinBtn = document.getElementById('join-btn');
        const displayName = document.getElementById('display-name');
        const loading = document.getElementById('loading');

        if (config.userName && !displayName.value) {
            displayName.value = config.userName;
        }

        console.log('✅ Event listener attached to join form');

        joinForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            console.log('🎯 Join button clicked!');

            const name = displayName.value.trim();
            if (!name) {
                alert('يرجى إدخال اسمك');
                return;
            }

            config.userName = name;
            joinBtn.disabled = true;
            joinBtn.textContent = 'جاري الانضمام...';

            try {
                console.log('📤 Sending signature request...');

                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    throw new Error('CSRF token not found');
                }

                const response = await fetch('/admin/zoom/generate-signature', {
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

                console.log('📥 Response status:', response.status);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server error:', errorText);
                    throw new Error(`Server returned ${response.status}: ${errorText}`);
                }

                const data = await response.json();
                console.log('📦 Response data:', data);

                if (!data.success) {
                    throw new Error(data.message || 'فشل الحصول على التوقيع');
                }

                joinOverlay.classList.add('hidden');
                loading.classList.add('active');

                console.log('Initializing Zoom SDK...');

                ZoomMtg.init({
                    leaveUrl: config.leaveUrl,
                    patchJsMedia: true,
                    leaveOnPageUnload: true,
                    isSupportAV: true,
                    success: function() {
                        console.log('✅ SDK initialized successfully, joining meeting...');

                        setTimeout(() => {
                            loading.classList.remove('active');
                            console.log('✅ Loading hidden - Zoom should be visible now');
                        }, 2000);

                        ZoomMtg.join({
                            signature: data.signature,
                            sdkKey: '{{ config("services.zoom.sdk_key") }}',
                            meetingNumber: config.meetingNumber,
                            userName: config.userName,
                            userEmail: config.userEmail,
                            passWord: config.password,
                            tk: '',

                            success: function(res) {
                                console.log('✅ Join meeting success!', res);
                            },

                            error: function(error) {
                                console.error('❌ Join error:', error);
                                alert('فشل الانضمام للاجتماع: ' + (error.errorMessage || error.reason || 'خطأ غير معروف'));
                                reset();
                            }
                        });
                    },

                    error: function(error) {
                        console.error('❌ Init error:', error);
                        alert('خطأ في تهيئة Zoom SDK: ' + (error.errorMessage || error.reason || 'خطأ غير معروف'));
                        reset();
                    }
                });

            } catch (error) {
                console.error('Error:', error);
                alert('حدث خطأ: ' + error.message);
                reset();
            }
        });

        function reset() {
            loading.classList.remove('active');
            joinOverlay.classList.remove('hidden');
            joinBtn.disabled = false;
            joinBtn.textContent = 'الانضمام الآن';
        }
    }
</script>
@endpush
