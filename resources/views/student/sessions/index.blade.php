@extends('layouts.dashboard')

@section('title', 'جلساتي')

@push('styles')
<style>
    .sessions-page { max-width: 1400px; margin: 0 auto; }

    /* Header */
    .sessions-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #004266 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .sessions-header::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }
    .sessions-header::after {
        content: '';
        position: absolute;
        bottom: -50%;
        left: -5%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        border-radius: 50%;
    }
    .header-title {
        font-size: 1.75rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }
    .header-subtitle {
        font-size: 0.95rem;
        opacity: 0.85;
        position: relative;
        z-index: 1;
    }
    .header-stats {
        display: flex;
        gap: 1rem;
        margin-top: 1.25rem;
        position: relative;
        z-index: 1;
        flex-wrap: wrap;
    }
    .header-stat {
        background: rgba(255,255,255,0.12);
        backdrop-filter: blur(10px);
        padding: 0.75rem 1.25rem;
        border-radius: 14px;
        text-align: center;
    }
    .header-stat-value {
        font-size: 1.5rem;
        font-weight: 800;
    }
    .header-stat-label {
        font-size: 0.75rem;
        opacity: 0.8;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .stat-card {
        background: #fff;
        border-radius: 18px;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s;
    }
    .dark .stat-card { background: #1f2937; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon svg { width: 24px; height: 24px; color: #fff; }
    .stat-value { font-size: 1.5rem; font-weight: 800; color: #111827; line-height: 1; }
    .dark .stat-value { color: #f9fafb; }
    .stat-label { font-size: 0.8rem; color: #6b7280; font-weight: 500; margin-top: 0.15rem; }
    .dark .stat-label { color: #9ca3af; }

    /* Filter Card */
    .filter-card {
        background: #fff;
        border-radius: 18px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .dark .filter-card { background: #1f2937; }
    .filter-select {
        padding: 0.625rem 1rem;
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        background: #fff;
        min-width: 160px;
        outline: none;
        transition: border-color 0.2s;
    }
    .dark .filter-select { background: #374151; border-color: #4b5563; color: #f3f4f6; }
    .filter-select:focus { border-color: #0071AA; }
    .filter-btn {
        padding: 0.625rem 1.5rem;
        border-radius: 12px;
        border: none;
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
        text-decoration: none;
    }
    .filter-btn-primary {
        background: linear-gradient(135deg, #0071AA, #005a88);
        color: #fff;
    }
    .filter-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,113,170,0.3); }
    .filter-btn-secondary {
        background: #f3f4f6;
        color: #6b7280;
    }
    .dark .filter-btn-secondary { background: #374151; color: #9ca3af; }
    .filter-btn-secondary:hover { background: #e5e7eb; }

    /* Subject Section */
    .subject-section {
        margin-bottom: 2rem;
    }
    .subject-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: 1rem 1.5rem;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        border-right: 4px solid;
    }
    .dark .subject-header { background: #1f2937; }
    .subject-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .subject-icon svg { width: 24px; height: 24px; color: #fff; }
    .subject-name {
        font-size: 1.1rem;
        font-weight: 800;
        color: #111827;
    }
    .dark .subject-name { color: #f9fafb; }
    .subject-meta {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 0.15rem;
    }
    .dark .subject-meta { color: #9ca3af; }
    .subject-badge {
        margin-right: auto;
        padding: 0.375rem 0.875rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        background: #e6f4fa;
        color: #0071AA;
    }
    .dark .subject-badge { background: rgba(0,113,170,0.2); }

    /* Session Card */
    .sessions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1rem;
    }
    .session-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        overflow: hidden;
        transition: all 0.2s;
        border: 2px solid transparent;
    }
    .dark .session-card { background: #1f2937; }
    .session-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        border-color: #0071AA;
    }
    .session-card-top {
        padding: 1.25rem 1.25rem 0.75rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }
    .session-num {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 800;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .session-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.25rem;
    }
    .dark .session-title { color: #f9fafb; }
    .session-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 0.25rem 0.625rem;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 700;
    }
    .badge-live {
        background: #fee2e2;
        color: #dc2626;
        animation: pulse 2s infinite;
    }
    .dark .badge-live { background: rgba(220,38,38,0.2); color: #fca5a5; }
    .badge-completed {
        background: #d1fae5;
        color: #059669;
    }
    .dark .badge-completed { background: rgba(16,185,129,0.2); color: #6ee7b7; }
    .badge-scheduled {
        background: #dbeafe;
        color: #2563eb;
    }
    .dark .badge-scheduled { background: rgba(37,99,235,0.2); color: #93c5fd; }
    .badge-zoom {
        background: #e6f4fa;
        color: #0071AA;
    }
    .dark .badge-zoom { background: rgba(0,113,170,0.2); color: #7dd3fc; }
    .badge-video {
        background: #f3e8ff;
        color: #7c3aed;
    }
    .dark .badge-video { background: rgba(124,58,237,0.2); color: #c4b5fd; }
    .badge-attended {
        background: #d1fae5;
        color: #059669;
    }
    .dark .badge-attended { background: rgba(16,185,129,0.2); color: #6ee7b7; }
    .badge-absent {
        background: #fee2e2;
        color: #dc2626;
    }
    .dark .badge-absent { background: rgba(220,38,38,0.2); color: #fca5a5; }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    /* Session Card Bottom */
    .session-card-meta {
        padding: 0.75rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        border-top: 1px solid #f3f4f6;
    }
    .dark .session-card-meta { border-color: #374151; }
    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.75rem;
        color: #6b7280;
    }
    .dark .meta-item { color: #9ca3af; }
    .meta-item svg { width: 14px; height: 14px; flex-shrink: 0; }

    .session-card-actions {
        padding: 0.75rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
        border-top: 1px solid #f3f4f6;
    }
    .dark .session-card-actions { border-color: #374151; }

    /* Action Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.75rem;
        transition: all 0.15s;
        cursor: pointer;
        border: none;
        text-decoration: none;
    }
    .btn-join {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
    }
    .btn-join:hover { box-shadow: 0 4px 12px rgba(239,68,68,0.3); transform: translateY(-1px); }
    .btn-watch {
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        color: #fff;
    }
    .btn-watch:hover { box-shadow: 0 4px 12px rgba(139,92,246,0.3); transform: translateY(-1px); }
    .btn-view-subject {
        background: #e6f4fa;
        color: #0071AA;
    }
    .btn-view-subject:hover { background: #cce9f5; transform: translateY(-1px); }

    /* File Tags */
    .file-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 0.25rem 0.625rem;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 600;
        background: #fef2f2;
        color: #dc2626;
        text-decoration: none;
        transition: all 0.2s;
    }
    .dark .file-tag { background: rgba(220,38,38,0.15); color: #fca5a5; }
    .file-tag:hover { background: #fee2e2; transform: translateY(-1px); }
    .file-tag svg { width: 12px; height: 12px; }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .dark .empty-state { background: #1f2937; }
    .empty-state svg { width: 80px; height: 80px; margin: 0 auto 1.5rem; color: #d1d5db; }
    .dark .empty-state svg { color: #4b5563; }
    .empty-state h3 { font-size: 1.25rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem; }
    .dark .empty-state h3 { color: #f9fafb; }
    .empty-state p { color: #6b7280; font-size: 0.9rem; }
    .dark .empty-state p { color: #9ca3af; }

    /* Responsive */
    @media (max-width: 768px) {
        .sessions-grid { grid-template-columns: 1fr; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .header-stats { flex-direction: column; }
    }
</style>
@endpush

@section('content')
<div class="sessions-page">
    <!-- Header -->
    <div class="sessions-header">
        <div style="position: relative; z-index: 1;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px);">
                    <svg style="width: 28px; height: 28px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="header-title">جلساتي</h1>
                    <p class="header-subtitle">
                        @if($termFilter === 'current' && $currentTerm)
                            جلسات الفصل الحالي &mdash; {{ $currentTerm->name }}
                        @else
                            جميع جلسات ومحاضرات برنامجك الدراسي
                        @endif
                    </p>
                </div>
            </div>
            <div class="header-stats">
                <div class="header-stat">
                    <div class="header-stat-value">{{ $totalSessions }}</div>
                    <div class="header-stat-label">إجمالي الجلسات</div>
                </div>
                <div class="header-stat">
                    <div class="header-stat-value">{{ $completedSessions }}</div>
                    <div class="header-stat-label">مكتملة</div>
                </div>
                <div class="header-stat">
                    <div class="header-stat-value">{{ $zoomSessions }}</div>
                    <div class="header-stat-label">Zoom مباشر</div>
                </div>
                <div class="header-stat">
                    <div class="header-stat-value">{{ $liveSessions }}</div>
                    <div class="header-stat-label">مباشر الآن</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Term Tabs -->
    @if($currentTerm)
    <div style="display: flex; gap: 0.375rem; background: #f3f4f6; padding: 0.375rem; border-radius: 16px; margin-bottom: 1.25rem;">
        <a href="{{ route('student.my-sessions', ['term_filter' => 'current']) }}"
           style="flex: 1; text-align: center; padding: 0.625rem 1.25rem; border-radius: 12px; font-size: 0.875rem; font-weight: 700; text-decoration: none; transition: all 0.2s;
                  {{ $termFilter === 'current' ? 'background: linear-gradient(135deg,#0071AA,#005a88); color:#fff; box-shadow:0 2px 8px rgba(0,113,170,0.25);' : 'color:#6b7280;' }}">
            <svg style="width: 15px; height: 15px; display: inline; margin-left: 0.375rem; vertical-align: -2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            الفصل الحالي
            <span style="margin-right: 0.375rem; font-size: 0.75rem; opacity: 0.85;">— {{ $currentTerm->name }}</span>
        </a>
        <a href="{{ route('student.my-sessions', ['term_filter' => 'all']) }}"
           style="flex: 1; text-align: center; padding: 0.625rem 1.25rem; border-radius: 12px; font-size: 0.875rem; font-weight: 700; text-decoration: none; transition: all 0.2s;
                  {{ $termFilter === 'all' ? 'background: #fff; color:#374151; box-shadow:0 1px 4px rgba(0,0,0,0.1);' : 'color:#6b7280;' }}">
            <svg style="width: 15px; height: 15px; display: inline; margin-left: 0.375rem; vertical-align: -2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            جميع الفصول
        </a>
    </div>
    @endif

    <!-- Filters -->
    <form method="GET" action="{{ route('student.my-sessions') }}" class="filter-card">
        @if($termFilter === 'all')
            <input type="hidden" name="term_filter" value="all">
        @endif
        <svg style="width: 20px; height: 20px; color: #6b7280; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
        </svg>
        <select name="subject_id" class="filter-select">
            <option value="">جميع المواد</option>
            @foreach($filterSubjects as $subject)
                <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
            @endforeach
        </select>
        <select name="type" class="filter-select">
            <option value="">جميع الأنواع</option>
            <option value="live_zoom" {{ $type == 'live_zoom' ? 'selected' : '' }}>Zoom مباشر</option>
            <option value="recorded_video" {{ $type == 'recorded_video' ? 'selected' : '' }}>فيديو مسجل</option>
        </select>
        <button type="submit" class="filter-btn filter-btn-primary">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            بحث
        </button>
        @if($subjectId || $type)
        <a href="{{ route('student.my-sessions', $termFilter === 'all' ? ['term_filter' => 'all'] : []) }}" class="filter-btn filter-btn-secondary">
            إلغاء الفلتر
        </a>
        @endif
    </form>

    <!-- Sessions By Subject -->
    @if($sessions->count() > 0)
        @php
            $colors = ['#0071AA', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444', '#ec4899', '#06b6d4', '#84cc16'];
            $colorIndex = 0;
        @endphp

        @foreach($sessionsBySubject as $subjectIdGroup => $subjectSessions)
            @php
                $subject = $subjectSessions->first()->subject;
                $color = $subject->color ?? $colors[$colorIndex % count($colors)];
                $colorIndex++;
                $subjectTotal = $subjectSessions->count();
                $subjectCompleted = $subjectSessions->whereNotNull('ended_at')->count();
            @endphp

            <div class="subject-section">
                <!-- Subject Header -->
                <div class="subject-header" style="border-color: {{ $color }};">
                    <div class="subject-icon" style="background: linear-gradient(135deg, {{ $color }}, {{ $color }}dd);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div style="flex: 1;">
                        <div class="subject-name">{{ $subject->name }}</div>
                        <div class="subject-meta">
                            @if($subject->term)
                                {{ $subject->term->name }}
                            @endif
                            @if($subject->teacher)
                                &bull; {{ $subject->teacher->name }}
                            @endif
                        </div>
                    </div>
                    <span class="subject-badge">{{ $subjectCompleted }}/{{ $subjectTotal }} جلسة</span>
                </div>

                <!-- Sessions Grid -->
                <div class="sessions-grid">
                    @foreach($subjectSessions as $session)
                    <div class="session-card">
                        <!-- Top -->
                        <div class="session-card-top">
                            <div class="session-num" style="background: linear-gradient(135deg, {{ $session->ended_at ? '#10b981, #059669' : ($session->started_at && !$session->ended_at ? '#ef4444, #dc2626' : $color . ',' . $color . 'cc') }});">
                                {{ $session->session_number }}
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div class="session-title">{{ $session->title }}</div>
                                <div class="session-info">
                                    @if($session->started_at && !$session->ended_at)
                                        <span class="badge badge-live">
                                            <svg style="width: 10px; height: 10px;" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                                            مباشر
                                        </span>
                                    @elseif($session->ended_at)
                                        <span class="badge badge-completed">مكتمل</span>
                                    @else
                                        <span class="badge badge-scheduled">مجدول</span>
                                    @endif

                                    @if($session->type === 'live_zoom')
                                        <span class="badge badge-zoom">Zoom</span>
                                    @else
                                        <span class="badge badge-video">فيديو</span>
                                    @endif

                                    @if(isset($attendances[$session->id]))
                                        @if($attendances[$session->id]->attended)
                                            <span class="badge badge-attended">
                                                <svg style="width: 10px; height: 10px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                حضرت
                                            </span>
                                        @else
                                            <span class="badge badge-absent">
                                                <svg style="width: 10px; height: 10px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                                غائب
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Meta -->
                        <div class="session-card-meta">
                            @if($session->scheduled_at)
                            <div class="meta-item">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $session->scheduled_at->translatedFormat('D d M Y') }}
                            </div>
                            <div class="meta-item">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $session->scheduled_at->format('h:i A') }}
                            </div>
                            @endif
                            @if($session->duration_minutes)
                            <div class="meta-item">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                {{ $session->duration_minutes }} دقيقة
                            </div>
                            @endif
                        </div>

                        <!-- Files -->
                        @if($session->files && $session->files->count() > 0)
                        <div style="padding: 0.5rem 1.25rem; display: flex; gap: 0.5rem; flex-wrap: wrap; border-top: 1px solid #f3f4f6;">
                            @foreach($session->files as $file)
                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="file-tag">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                {{ Str::limit($file->title, 20) }}
                            </a>
                            @endforeach
                        </div>
                        @endif

                        <!-- Actions -->
                        <div class="session-card-actions">
                            @if($session->type === 'live_zoom' && $session->zoom_meeting_id)
                                @if($session->started_at && !$session->ended_at)
                                    {{-- Live now - join via SDK --}}
                                    <a href="{{ route('student.sessions.join-zoom', $session->id) }}" class="btn btn-join">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        انضم الآن
                                    </a>
                                @elseif(!$session->ended_at)
                                    {{-- Not ended - join via SDK --}}
                                    <a href="{{ route('student.sessions.join-zoom', $session->id) }}" class="btn btn-join" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        انضم عبر Zoom
                                    </a>
                                    @if($session->scheduled_at && $session->scheduled_at > now())
                                        <span class="badge" style="background: #fef3c7; color: #92400e; padding: 0.375rem 0.75rem;">
                                            {{ $session->scheduled_at->diffForHumans() }}
                                        </span>
                                    @endif
                                @endif
                            @endif

                            @if($session->type === 'recorded_video' && $session->hasVideo())
                                <a href="{{ $session->getVideoUrl() }}" target="_blank" class="btn btn-watch">
                                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                                    مشاهدة
                                </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <h3>لا توجد جلسات حالياً</h3>
            <p>سيتم عرض جلسات ومحاضرات برنامجك الدراسي هنا عند توفرها</p>
        </div>
    @endif
</div>
@endsection
