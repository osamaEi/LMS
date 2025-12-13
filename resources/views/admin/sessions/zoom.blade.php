<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $session->title }} - Zoom</title>

    <link rel="stylesheet" href="https://source.zoom.us/3.8.10/css/bootstrap.css">
    <link rel="stylesheet" href="https://source.zoom.us/3.8.10/css/react-select.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        body.meeting-active {
            background: #000;
            overflow: hidden;
        }

        .join-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            max-width: 450px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #2d8cff 0%, #1a5fff 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        h1 {
            font-size: 24px;
            font-weight: 800;
            color: #1a202c;
            text-align: center;
            margin-bottom: 8px;
        }

        .info {
            text-align: center;
            color: #718096;
            font-size: 14px;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            transition: border 0.2s;
        }

        input:focus {
            outline: none;
            border-color: #3182ce;
        }

        button {
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

        button:hover:not(:disabled) {
            transform: translateY(-2px);
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 16px;
            color: #718096;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            color: #2d3748;
        }

        #zoom-meeting-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
        }

        #zoom-meeting-container.active {
            display: block !important;
        }

        /* Force Zoom SDK elements to be visible - AGGRESSIVE OVERRIDES */
        #zmmtg-root {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: 100% !important;
            height: 100% !important;
            min-height: 100vh !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            background-color: #232323 !important;
            z-index: 9999 !important;
        }

        /* Ensure all Zoom child elements are visible */
        #zmmtg-root *,
        #zmmtg-root > * {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Zoom meeting app container */
        .meeting-app,
        .meeting-client,
        .meeting-client-inner,
        #wc-container,
        #wc-content,
        .zmwebsdk,
        [class*="meeting-client"] {
            display: block !important;
            visibility: visible !important;
            width: 100% !important;
            height: 100% !important;
            min-height: 100vh !important;
            position: relative !important;
        }

        /* Zoom video container */
        .ReactModalPortal,
        .zoom-video-wrap,
        .video-container,
        .video-avatar-container,
        .video-layout-container,
        [class*="video"] {
            display: block !important;
            visibility: visible !important;
            width: 100% !important;
            height: 100% !important;
        }

        /* Zoom footer/controls */
        .meeting-app-footer,
        .footer-button-container,
        [class*="footer"] {
            display: flex !important;
            visibility: visible !important;
        }

        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 9998;
        }

        .loading.active {
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

        .loading-text {
            color: white;
            font-size: 16px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .leave-btn {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #dc2626;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4);
            z-index: 10000;
            display: none;
        }

        .leave-btn.active {
            display: block;
        }

        .leave-btn:hover {
            background: #b91c1c;
        }
    </style>
</head>
<body>

    <div class="join-card" id="join-screen">
        <div class="logo">
            <svg width="32" height="32" viewBox="0 0 90 20" fill="white">
                <path d="M4.94506 20L3.95604 19.95C2.31347 19.8406 1.13603 18.6476 1.03846 16.9991L0.989011 16L12.8571 4H3.95604L2.96583 3.95C1.34815 3.85556 0.177592 2.62595 0.0494498 0.999056L0 7.42403e-06L14.8352 0.000912409L15.8241 0.0499992C17.4625 0.137543 18.6634 1.34167 18.7418 3.00124L18.7912 4L6.92308 16H15.8242L16.8132 16.05C18.4453 16.1531 19.5984 17.3544 19.7308 19.0009L19.7802 20H4.94506Z"/>
            </svg>
        </div>

        <h1>{{ $session->title }}</h1>
        <div class="info">Meeting ID: {{ $session->zoom_meeting_id }}</div>

        <form id="join-form">
            <div class="form-group">
                <label for="display-name">الاسم</label>
                <input type="text" id="display-name" placeholder="أدخل اسمك" required autofocus>
            </div>
            <button type="submit" id="join-btn">الانضمام الآن</button>
        </form>

        <a href="{{ route('admin.sessions.show', $session) }}" class="back-link">← العودة</a>
    </div>

    <div class="loading" id="loading">
        <div class="spinner"></div>
        <div class="loading-text">جاري الاتصال بالاجتماع...</div>
    </div>

    <div id="zoom-meeting-container"></div>

    <button class="leave-btn" id="leave-btn" onclick="ZoomMtg.leaveMeeting({})">
        مغادرة الاجتماع
    </button>

    <script src="https://source.zoom.us/3.8.10/lib/vendor/react.min.js"></script>
    <script src="https://source.zoom.us/3.8.10/lib/vendor/react-dom.min.js"></script>
    <script src="https://source.zoom.us/3.8.10/lib/vendor/redux.min.js"></script>
    <script src="https://source.zoom.us/3.8.10/lib/vendor/redux-thunk.min.js"></script>
    <script src="https://source.zoom.us/3.8.10/lib/vendor/lodash.min.js"></script>
    <script src="https://source.zoom.us/zoom-meeting-3.8.10.min.js"></script>

    <script>
        const config = {
            meetingNumber: '{{ $session->zoom_meeting_id }}',
            password: '{{ $session->zoom_password ?? "" }}',
            userName: '',
            userEmail: '{{ auth()->user()->email ?? "" }}',
            leaveUrl: '{{ route("admin.sessions.show", $session) }}',
            role: 1
        };

        ZoomMtg.setZoomJSLib('https://source.zoom.us/3.8.10/lib', '/av');
        ZoomMtg.preLoadWasm();
        ZoomMtg.prepareWebSDK();
        ZoomMtg.i18n.load('en-US');
        ZoomMtg.i18n.reload('en-US');

        const joinScreen = document.getElementById('join-screen');
        const joinForm = document.getElementById('join-form');
        const joinBtn = document.getElementById('join-btn');
        const displayName = document.getElementById('display-name');
        const loading = document.getElementById('loading');
        const meetingContainer = document.getElementById('zoom-meeting-container');
        const leaveBtn = document.getElementById('leave-btn');

        joinForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const name = displayName.value.trim();
            if (!name) {
                alert('يرجى إدخال اسمك');
                return;
            }

            config.userName = name;
            joinBtn.disabled = true;
            joinBtn.textContent = 'جاري الانضمام...';

            try {
                const response = await fetch('/admin/zoom/generate-signature', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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

                joinScreen.style.display = 'none';
                loading.classList.add('active');

                console.log('Initializing Zoom SDK...');

                ZoomMtg.init({
                    leaveUrl: config.leaveUrl,
                    success: function() {
                        console.log('SDK initialized, joining meeting...');

                        ZoomMtg.join({
                            signature: data.signature,
                            sdkKey: '{{ config("services.zoom.sdk_key") }}',
                            meetingNumber: config.meetingNumber,
                            userName: config.userName,
                            userEmail: config.userEmail,
                            passWord: config.password,
                            tk: '',

                            success: function() {
                                console.log('✅ Joined meeting successfully');

                                // Debug: Check what Zoom created
                                setTimeout(() => {
                                    const zmmtgRoot = document.getElementById('zmmtg-root');
                                    console.log('🔍 Debugging Zoom elements:');
                                    console.log('- #zmmtg-root exists:', !!zmmtgRoot);
                                    console.log('- #zmmtg-root display:', zmmtgRoot ? window.getComputedStyle(zmmtgRoot).display : 'not found');
                                    console.log('- #zmmtg-root children:', zmmtgRoot ? zmmtgRoot.children.length : 0);
                                    console.log('- First child:', zmmtgRoot && zmmtgRoot.children[0] ? zmmtgRoot.children[0].className : 'none');

                                    loading.classList.remove('active');
                                    meetingContainer.classList.add('active');
                                    leaveBtn.classList.add('active');
                                    document.body.classList.add('meeting-active');

                                    console.log('✅ Meeting container activated');
                                }, 1000);
                            },

                            error: function(error) {
                                console.error('Join error:', error);
                                alert('فشل الانضمام: ' + (error.reason || 'خطأ غير معروف'));
                                reset();
                            }
                        });
                    },

                    error: function(error) {
                        console.error('Init error:', error);
                        alert('خطأ في التهيئة: ' + (error.reason || 'خطأ غير معروف'));
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
            joinScreen.style.display = 'block';
            joinBtn.disabled = false;
            joinBtn.textContent = 'الانضمام الآن';
            document.body.classList.remove('meeting-active');
            meetingContainer.classList.remove('active');
            leaveBtn.classList.remove('active');
        }
    </script>
</body>
</html>
