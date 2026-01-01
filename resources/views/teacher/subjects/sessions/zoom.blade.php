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
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #7c3aed;
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


        /* Animated Background - hidden for performance */
        .bg-animation, .floating-shapes {
            display: none;
        }

        /* Main Container */
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

        .back-btn svg {
            width: 16px;
            height: 16px;
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

        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            50% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
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

        /* Hide sidebar */
        .sidebar {
            display: none !important;
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
            padding: 20px;
            max-width: 340px;
            width: 100%;
            text-align: center;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            margin: auto;
            position: relative;
            z-index: 10;
        }

        .zoom-logo {
            width: 44px;
            height: 44px;
            margin: 0 auto 10px;
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .zoom-logo svg {
            width: 20px;
            height: 20px;
        }

        .join-card h2 {
            color: white;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .join-card .subtitle {
            color: rgba(255, 255, 255, 0.5);
            font-size: 11px;
            margin-bottom: 12px;
        }

        .meeting-info-box {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 12px;
        }

        .meeting-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .meeting-info-row:last-child {
            border-bottom: none;
        }

        .meeting-info-row .label {
            color: rgba(255, 255, 255, 0.5);
            font-size: 10px;
        }

        .meeting-info-row .value {
            color: white;
            font-weight: 600;
            font-size: 11px;
            font-family: 'Cairo', sans-serif;
        }

        .input-group {
            margin-bottom: 10px;
        }

        .input-group label {
            display: block;
            color: rgba(255, 255, 255, 0.7);
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 4px;
            text-align: right;
        }

        .input-group input {
            width: 100%;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: white;
            font-size: 12px;
            font-family: inherit;
            transition: all 0.3s;
        }

        .input-group input:focus {
            outline: none;
            border-color: #2563eb;
            background: rgba(37, 99, 235, 0.1);
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .join-btn {
            width: 100%;
            padding: 10px 16px;
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 13px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .join-btn:hover:not(:disabled) {
            opacity: 0.9;
        }

        .join-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .join-btn svg {
            width: 16px;
            height: 16px;
        }

        .features-grid {
            display: none;
        }

        .feature-item {
            display: none;
        }

        /* Sidebar */
        .sidebar {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            overflow-y: auto;
            padding: 24px;
        }

        .sidebar-section {
            margin-bottom: 24px;
        }

        .sidebar-section h3 {
            color: white;
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-section h3 svg {
            width: 20px;
            height: 20px;
            color: #2563eb;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 20px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-row .label {
            color: rgba(255, 255, 255, 0.5);
            font-size: 13px;
        }

        .info-row .value {
            color: white;
            font-weight: 700;
            font-size: 14px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-badge.live {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        .status-badge.scheduled {
            background: rgba(37, 99, 235, 0.2);
            color: #60a5fa;
        }

        .status-badge.ended {
            background: rgba(107, 114, 128, 0.2);
            color: #9ca3af;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            border: none;
        }

        .action-btn.primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
        }

        .action-btn.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
        }

        .action-btn.success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .action-btn.success:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }

        .action-btn.secondary {
            background: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .action-btn.secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .copy-input-group {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }

        .copy-input {
            flex: 1;
            padding: 10px 14px;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: white;
            font-size: 12px;
            font-family: 'Monaco', 'Consolas', monospace;
        }

        .copy-btn {
            padding: 10px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
        }

        .copy-btn:hover {
            background: rgba(255, 255, 255, 0.2);
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
            border: 4px solid rgba(37, 99, 235, 0.2);
        }

        .loader::after {
            width: 100%;
            height: 100%;
            border: 4px solid transparent;
            border-top-color: #2563eb;
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

        /* Zoom SDK Container - hidden until meeting starts */
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

        /* Responsive */
        @media (max-width: 1024px) {
            .content-area {
                grid-template-columns: 1fr;
            }

            .sidebar {
                display: none;
            }
        }

        /* Quick Stats */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
        }

        .stat-card .number {
            font-size: 28px;
            font-weight: 900;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-card .label {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            margin-top: 4px;
        }

        /* Instructions */
        .instructions {
            background: rgba(37, 99, 235, 0.1);
            border: 1px solid rgba(37, 99, 235, 0.2);
            border-radius: 12px;
            padding: 16px;
        }

        .instructions h4 {
            color: #60a5fa;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .instructions ul {
            list-style: none;
        }

        .instructions li {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            padding: 8px 0;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .instructions li svg {
            width: 16px;
            height: 16px;
            color: #10b981;
            flex-shrink: 0;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation"></div>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="main-container">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <a href="{{ route('teacher.my-subjects.show', $session->subject_id) }}" class="back-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="20" height="20">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; margin-left: 12px; overflow: hidden; border: 2px solid rgba(255,255,255,0.2);">
                    <img src="{{ asset('images/logo/logo.png') }}" alt="Logo" style="height: 28px; object-fit: contain;" />
                </div>
                <div class="header-title">
                    <h1>{{ $session->title }}</h1>
                    <p>{{ $session->subject->name ?? '' }}</p>
                </div>
            </div>
            <div class="header-right">
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
                    <!-- Logo in center above card -->
                    <div style="position: absolute; top: 80px; left: 50%; transform: translateX(-50%); z-index: 20;">
                        <div style="width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; border: 3px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                            <img src="{{ asset('images/logo/logo.png') }}" alt="Logo" style="height: 60px; object-fit: contain;" />
                        </div>
                    </div>

                    <div class="join-card" style="margin-top: 120px;">
                        <div class="zoom-logo">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="white">
                                <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>

                        <h2>{{ $session->title }}</h2>
                        <p class="subtitle">استعد للانضمام كمضيف للحصة</p>

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
                        </div>

                        <form id="join-form" style="position: relative; z-index: 20;">
                            <div class="input-group">
                                <label>اسم العرض</label>
                                <input type="text" id="display-name" placeholder="أدخل اسمك للعرض في الاجتماع" value="{{ auth()->user()->name ?? '' }}" required style="pointer-events: auto;">
                            </div>

                            <button type="submit" class="join-btn" id="join-btn" style="pointer-events: auto; cursor: pointer;">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                بدء الحصة الآن
                            </button>
                        </form>

                        <div class="features-grid">
                            <div class="feature-item">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <span>تسجيل تلقائي</span>
                            </div>
                            <div class="feature-item">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>
                                </svg>
                                <span>محادثة مباشرة</span>
                            </div>
                            <div class="feature-item">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span>مشاركة الشاشة</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading Overlay -->
                <div class="loading-overlay" id="loading">
                    <div class="loader"></div>
                    <div class="loading-text">جاري الاتصال بالاجتماع...</div>
                    <div class="loading-subtext">يرجى الانتظار قليلاً</div>
                </div>

                <div id="zoom-meeting-container"></div>
            </div>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Quick Stats -->
                <div class="quick-stats">
                    <div class="stat-card">
                        <div class="number">{{ $session->session_number }}</div>
                        <div class="label">رقم الحصة</div>
                    </div>
                    <div class="stat-card">
                        <div class="number">{{ $session->duration_minutes ?? 60 }}</div>
                        <div class="label">دقيقة</div>
                    </div>
                </div>

                <!-- Status -->
                <div class="sidebar-section">
                    <h3>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        حالة الحصة
                    </h3>
                    <div class="info-card">
                        @if($session->ended_at)
                            <span class="status-badge ended">منتهية</span>
                        @elseif($session->started_at)
                            <span class="status-badge live"><span style="animation: blink 1s infinite;">●</span> جارية الآن</span>
                        @else
                            <span class="status-badge scheduled">مجدولة</span>
                        @endif
                    </div>
                </div>

                <!-- Session Info -->
                <div class="sidebar-section">
                    <h3>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        تفاصيل الحصة
                    </h3>
                    <div class="info-card">
                        <div class="info-row">
                            <span class="label">المادة</span>
                            <span class="value">{{ $session->subject->name ?? '-' }}</span>
                        </div>
                        @if($session->scheduled_at)
                        <div class="info-row">
                            <span class="label">التاريخ</span>
                            <span class="value">{{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">الوقت</span>
                            <span class="value">{{ \Carbon\Carbon::parse($session->scheduled_at)->format('h:i A') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Share Link -->
                <div class="sidebar-section">
                    <h3>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                        رابط للطلاب
                    </h3>
                    <div class="info-card">
                        <p style="color: rgba(255,255,255,0.6); font-size: 12px; margin-bottom: 12px;">شارك هذا الرابط مع الطلاب للانضمام</p>
                        <div class="copy-input-group">
                            <input type="text" class="copy-input" value="{{ $session->zoom_join_url }}" readonly id="join-url">
                            <button class="copy-btn" onclick="copyLink()">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="sidebar-section">
                    <h3>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        إجراءات سريعة
                    </h3>
                    <div class="action-buttons">
                        @if($session->zoom_start_url)
                        <a href="{{ $session->zoom_start_url }}" target="_blank" class="action-btn primary">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            فتح في تطبيق Zoom
                        </a>
                        @endif
                        <a href="{{ $session->zoom_join_url }}" target="_blank" class="action-btn success">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            انضمام بالرابط
                        </a>
                        <a href="{{ route('teacher.my-subjects.show', $session->subject_id) }}" class="action-btn secondary">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                            </svg>
                            العودة للمادة
                        </a>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="sidebar-section">
                    <div class="instructions">
                        <h4>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            تعليمات المضيف
                        </h4>
                        <ul>
                            <li>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                سيتم تسجيل الحصة تلقائياً على Cloud
                            </li>
                            <li>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                شارك رابط الانضمام مع طلابك
                            </li>
                            <li>
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                يمكنك مشاركة شاشتك أثناء الشرح
                            </li>
                        </ul>
                    </div>
                </div>
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
        function copyLink() {
            const input = document.getElementById('join-url');
            input.select();
            document.execCommand('copy');

            const btn = event.currentTarget;
            btn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
            setTimeout(() => {
                btn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>';
            }, 2000);
        }

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
                leaveUrl: '{{ route("teacher.my-subjects.show", $session->subject_id) }}',
                role: 1 // Host role
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

            if (config.userName && !displayName.value) {
                displayName.value = config.userName;
            }

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
                joinBtn.innerHTML = '<svg class="animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24" style="animation: spin 1s linear infinite;"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"></circle><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path></svg> جاري الاتصال...';

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfToken) throw new Error('CSRF token not found');

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

                    if (!response.ok) {
                        const errorText = await response.text();
                        throw new Error(`Server returned ${response.status}: ${errorText}`);
                    }

                    const data = await response.json();

                    if (!data.success) {
                        throw new Error(data.message || 'فشل الحصول على التوقيع');
                    }

                    preJoinScreen.classList.add('hidden');
                    loading.classList.add('active');

                    // Activate zoom container
                    const zoomRoot = document.getElementById('zmmtg-root');
                    if (zoomRoot) {
                        zoomRoot.classList.add('active');
                    }

                    console.log('Initializing ZoomMtg with signature:', data.signature.substring(0, 20) + '...');
                    console.log('SDK Key:', '{{ config("services.zoom.sdk_key") }}'.substring(0, 10) + '...');

                    ZoomMtg.init({
                        leaveUrl: config.leaveUrl,
                        disableCORP: !window.crossOriginIsolated,
                        success: function() {
                            console.log('SDK initialized, joining meeting...');

                            setTimeout(() => {
                                loading.classList.remove('active');
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
                joinBtn.disabled = false;
                joinBtn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="24" height="24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg> بدء الحصة الآن';
            }
        }
    </script>
</body>
</html>
