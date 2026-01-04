<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $session->title }} - Zoom Live</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Cairo', sans-serif !important; }

        :root {
            --primary: #0071AA;
            --primary-dark: #005a88;
            --secondary: #0071AA;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #1e293b;
            --light: #f8fafc;
        }

        body {
            font-family: 'Cairo', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            overflow-x: hidden;
            color: #ffffff;
        }

        .main-container {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .back-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .header-title h1 {
            color: white;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 0;
        }

        .header-title p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 11px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .live-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            background: #ef4444;
            padding: 4px 10px;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            font-size: 11px;
        }

        .live-dot {
            width: 6px;
            height: 6px;
            background: white;
            border-radius: 50%;
            animation: blink 1s infinite;
        }

        .attendance-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            background: #10b981;
            padding: 4px 10px;
            border-radius: 12px;
            color: white;
            font-weight: 600;
            font-size: 11px;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Content Area */
        .content-area {
            flex: 1;
            display: block;
        }

        /* Video Area */
        .video-area {
            position: relative;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 50px);
        }

        /* Pre-Join Screen */
        .pre-join-screen {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            z-index: 100;
            overflow-y: auto;
            padding: 20px;
        }

        .pre-join-screen.hidden {
            display: none;
        }

        .join-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(40px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 24px;
            max-width: 380px;
            width: 100%;
            text-align: center;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            margin: auto;
        }

        .zoom-logo {
            width: 56px;
            height: 56px;
            margin: 0 auto 12px;
            background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .zoom-logo svg {
            width: 28px;
            height: 28px;
        }

        .join-card h2 {
            color: white;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .join-card .subtitle {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            margin-bottom: 16px;
        }

        .meeting-info-box {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 16px;
        }

        .meeting-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .meeting-info-row:last-child {
            border-bottom: none;
        }

        .meeting-info-row .label {
            color: rgba(255, 255, 255, 0.5);
            font-size: 11px;
        }

        .meeting-info-row .value {
            color: white;
            font-weight: 600;
            font-size: 12px;
        }

        .input-group {
            margin-bottom: 12px;
        }

        .input-group label {
            display: block;
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 6px;
            text-align: right;
        }

        .input-group input {
            width: 100%;
            padding: 10px 14px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: white;
            font-size: 13px;
            font-family: inherit;
            transition: all 0.3s;
        }

        .input-group input:focus {
            outline: none;
            border-color: #0071AA;
            background: rgba(6, 182, 212, 0.1);
        }

        .join-btn {
            width: 100%;
            padding: 12px 18px;
            background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .join-btn:hover:not(:disabled) {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .join-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .external-btn {
            width: 100%;
            padding: 12px 18px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            color: white;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .external-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .or-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 16px 0;
            color: rgba(255, 255, 255, 0.3);
            font-size: 12px;
        }

        .or-divider::before,
        .or-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        /* Loading Overlay */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.95);
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 99;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loader {
            width: 60px;
            height: 60px;
            position: relative;
        }

        .loader::before,
        .loader::after {
            content: '';
            position: absolute;
            border-radius: 50%;
        }

        .loader::before {
            width: 100%;
            height: 100%;
            border: 4px solid rgba(6, 182, 212, 0.2);
        }

        .loader::after {
            width: 100%;
            height: 100%;
            border: 4px solid transparent;
            border-top-color: #0071AA;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-text {
            color: white;
            font-size: 16px;
            font-weight: 600;
            margin-top: 24px;
        }

        .loading-subtext {
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
            margin-top: 8px;
        }

        /* Zoom SDK Container */
        #zmmtg-root {
            display: none !important;
        }

        #zmmtg-root.active {
            display: block !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            z-index: 9999 !important;
            background-color: #000 !important;
        }

        /* Attendance Info */
        .attendance-info {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .attendance-info svg {
            width: 20px;
            height: 20px;
            color: #10b981;
        }

        .attendance-info .text {
            flex: 1;
        }

        .attendance-info .text p {
            color: #10b981;
            font-size: 12px;
            font-weight: 600;
        }

        .attendance-info .text span {
            color: rgba(16, 185, 129, 0.7);
            font-size: 10px;
        }

        /* Leave Button */
        .leave-btn {
            position: fixed;
            top: 60px;
            left: 16px;
            padding: 8px 16px;
            background: rgba(239, 68, 68, 0.9);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 12px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            z-index: 10000;
            transition: all 0.3s;
        }

        .leave-btn:hover {
            background: #ef4444;
        }

        @media (max-width: 640px) {
            .join-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <a href="{{ route('student.upcoming-sessions') }}" class="back-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div class="header-title">
                    <h1>{{ $session->title }}</h1>
                    <p>{{ $session->subject->name ?? '' }}</p>
                </div>
            </div>
            <div class="header-right">
                <div class="attendance-badge">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    تم تسجيل الحضور
                </div>
                @if($session->started_at && !$session->ended_at)
                    <div class="live-badge">
                        <span class="live-dot"></span>
                        مباشر الآن
                    </div>
                @endif
            </div>
        </header>

        <!-- Content -->
        <div class="content-area">
            <!-- Video Area -->
            <div class="video-area">
                <!-- Pre-Join Screen -->
                <div class="pre-join-screen" id="pre-join-screen">
                    <div class="join-card">
                        <div class="zoom-logo">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="white">
                                <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>

                        <h2>{{ $session->title }}</h2>
                        <p class="subtitle">{{ $session->subject->name ?? '' }}</p>

                        <!-- Attendance Confirmation -->
                        <div class="attendance-info">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text">
                                <p>تم تسجيل حضورك</p>
                                <span>{{ $attendance->joined_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>

                        <div class="meeting-info-box">
                            <div class="meeting-info-row">
                                <span class="label">معرف الاجتماع</span>
                                <span class="value">{{ $session->zoom_meeting_id }}</span>
                            </div>
                            @if($session->zoom_password)
                            <div class="meeting-info-row">
                                <span class="label">كلمة المرور</span>
                                <span class="value">{{ $session->zoom_password }}</span>
                            </div>
                            @endif
                            @if($session->scheduled_at)
                            <div class="meeting-info-row">
                                <span class="label">الموعد</span>
                                <span class="value">{{ \Carbon\Carbon::parse($session->scheduled_at)->format('h:i A') }}</span>
                            </div>
                            @endif
                            @if($session->duration_minutes)
                            <div class="meeting-info-row">
                                <span class="label">المدة</span>
                                <span class="value">{{ $session->duration_minutes }} دقيقة</span>
                            </div>
                            @endif
                        </div>

                        <form id="join-form">
                            <div class="input-group">
                                <label>اسم العرض</label>
                                <input type="text" id="display-name" placeholder="أدخل اسمك للعرض في الاجتماع" value="{{ auth()->user()->name ?? '' }}" required>
                            </div>

                            <button type="submit" class="join-btn" id="join-btn">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                انضم للجلسة الآن
                            </button>
                        </form>

                        <div class="or-divider">أو</div>

                        <a href="{{ $session->zoom_join_url }}" target="_blank" class="external-btn">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            فتح في تطبيق Zoom
                        </a>
                    </div>
                </div>

                <!-- Loading Overlay -->
                <div class="loading-overlay" id="loading">
                    <div class="loader"></div>
                    <div class="loading-text">جاري الاتصال بالاجتماع...</div>
                    <div class="loading-subtext">يرجى الانتظار قليلا</div>
                </div>

                <div id="zoom-meeting-container"></div>
            </div>
        </div>
    </div>

    <!-- Leave Button (shown when in meeting) -->
    <form action="{{ route('student.sessions.leave-zoom', $session->id) }}" method="POST" id="leave-form" style="display: none;">
        @csrf
        <button type="submit" class="leave-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            مغادرة الجلسة
        </button>
    </form>

    <script src="https://source.zoom.us/3.1.6/lib/vendor/react.min.js"></script>
    <script src="https://source.zoom.us/3.1.6/lib/vendor/react-dom.min.js"></script>
    <script src="https://source.zoom.us/3.1.6/lib/vendor/redux.min.js"></script>
    <script src="https://source.zoom.us/3.1.6/lib/vendor/redux-thunk.min.js"></script>
    <script src="https://source.zoom.us/3.1.6/lib/vendor/lodash.min.js"></script>
    <script src="https://source.zoom.us/zoom-meeting-3.1.6.min.js"></script>

    <script>
        window.onerror = function(msg, url, lineNo, columnNo, error) {
            console.error('JavaScript Error:', { message: msg, url: url, line: lineNo, column: columnNo, error: error });
            return false;
        };

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing Zoom SDK...');
            initializeZoom();
        });

        function initializeZoom() {
            const config = {
                meetingNumber: '{{ $session->zoom_meeting_id }}',
                password: '{{ $session->zoom_password ?? "" }}',
                userName: '{{ auth()->user()->name ?? "" }}',
                userEmail: '{{ auth()->user()->email ?? "" }}',
                leaveUrl: '{{ route("student.upcoming-sessions") }}',
                role: 0, // Participant role
                signature: '{{ $signature ?? "" }}',
                sdkKey: '{{ config("services.zoom.sdk_key") }}'
            };

            if (typeof ZoomMtg === 'undefined') {
                console.error('Zoom SDK not loaded!');
                alert('خطأ: لم يتم تحميل Zoom SDK. يرجى إعادة تحميل الصفحة.');
                return;
            }

            console.log('Zoom SDK loaded successfully');

            ZoomMtg.setZoomJSLib('https://source.zoom.us/3.1.6/lib', '/av');
            ZoomMtg.preLoadWasm();
            ZoomMtg.prepareWebSDK();
            ZoomMtg.i18n.load('en-US');
            ZoomMtg.i18n.reload('en-US');

            const preJoinScreen = document.getElementById('pre-join-screen');
            const joinForm = document.getElementById('join-form');
            const joinBtn = document.getElementById('join-btn');
            const displayName = document.getElementById('display-name');
            const loading = document.getElementById('loading');
            const leaveForm = document.getElementById('leave-form');

            joinForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                console.log('Join button clicked');

                const name = displayName.value.trim();
                if (!name) {
                    alert('يرجى إدخال اسمك');
                    return;
                }

                config.userName = name;
                joinBtn.disabled = true;
                joinBtn.innerHTML = '<svg class="animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20" style="animation: spin 1s linear infinite;"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"></circle><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path></svg> جاري الاتصال...';

                try {
                    preJoinScreen.classList.add('hidden');
                    loading.classList.add('active');

                    // Show leave button
                    leaveForm.style.display = 'block';

                    // Activate zoom container
                    const zoomRoot = document.getElementById('zmmtg-root');
                    if (zoomRoot) {
                        zoomRoot.classList.add('active');
                    }

                    console.log('Initializing ZoomMtg...');

                    ZoomMtg.init({
                        leaveUrl: config.leaveUrl,
                        disableCORP: !window.crossOriginIsolated,
                        success: function() {
                            console.log('SDK initialized, joining meeting...');

                            setTimeout(() => {
                                loading.classList.remove('active');
                            }, 2000);

                            ZoomMtg.join({
                                signature: config.signature,
                                sdkKey: config.sdkKey,
                                meetingNumber: config.meetingNumber,
                                userName: config.userName,
                                userEmail: config.userEmail,
                                passWord: config.password,
                                tk: '',

                                success: function(res) {
                                    console.log('Join meeting success!', res);
                                },

                                error: function(error) {
                                    console.error('Join error:', error);
                                    alert('فشل الانضمام للاجتماع: ' + (error.errorMessage || error.reason || JSON.stringify(error)));
                                    reset();
                                }
                            });
                        },

                        error: function(error) {
                            console.error('Init error:', error);
                            alert('خطأ في تهيئة Zoom SDK: ' + (error.errorMessage || error.reason || JSON.stringify(error)));
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
                preJoinScreen.classList.remove('hidden');
                leaveForm.style.display = 'none';
                joinBtn.disabled = false;
                joinBtn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg> انضم للجلسة الآن';
            }
        }
    </script>
</body>
</html>
