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
                    <p class="header-subtitle">جميع جلسات ومحاضرات برامجك التدريبية</p>
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

    @php
        $progTabColors = ['#0071AA','#10b981','#8b5cf6','#f59e0b','#ef4444','#ec4899','#06b6d4'];
        $subjColors    = ['#0071AA','#10b981','#8b5cf6','#f59e0b','#ef4444','#ec4899','#06b6d4','#84cc16'];
        $pci = 0;
        $firstProgId = array_key_first($programsSessionData ?? []);
    @endphp

    @if(!empty($programsSessionData) && count($programsSessionData) > 0)

    {{-- Program tabs bar (only shown if more than 1 program) --}}
    @if(count($programsSessionData) > 1)
    <div style="display:flex;gap:8px;background:#fff;border-radius:18px;box-shadow:0 1px 3px rgba(0,0,0,.05);margin-bottom:16px;padding:12px 16px;flex-wrap:wrap;overflow-x:auto;">
        @foreach($programsSessionData as $progId => $pd)
        @php
            $ptc = $progTabColors[$pci % count($progTabColors)];
            $pci++;
            $ptypeBadge = match($pd['program']->type) {
                'diploma'  => 'دبلومة',
                'course'   => 'دورة',
                'english'  => 'إنجليزي',
                default    => 'تدريب',
            };
        @endphp
        <button onclick="switchProgSessionTab({{ $progId }})" id="progsess-tab-{{ $progId }}" data-color="{{ $ptc }}"
            style="padding:8px 18px;border:none;border-radius:999px;cursor:pointer;font-size:.85rem;font-weight:700;font-family:inherit;transition:all .18s;white-space:nowrap;background:#f3f4f6;color:#6b7280;">
            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:{{ $ptc }};margin-left:6px;"></span>
            {{ $pd['program']->name }}
            <span style="font-size:.7rem;opacity:.75;margin-right:4px;">({{ $ptypeBadge }})</span>
            <span style="font-size:.72rem;opacity:.8;margin-right:4px;">{{ $pd['totalSessions'] }}</span>
        </button>
        @endforeach
    </div>
    @endif

    {{-- Program panels --}}
    @php $pci = 0; @endphp
    @foreach($programsSessionData as $progId => $pd)
    @php
        $ptc = $progTabColors[$pci % count($progTabColors)];
        $pci++;
        $sci = 0;
    @endphp
    <div id="progsess-panel-{{ $progId }}" class="progsess-panel" style="{{ $loop->first ? 'display:block' : 'display:none' }};">

        @if($pd['isDiploma'])
        {{-- Diploma: subject tabs --}}
        @php
            $subjectGroups = [];
            foreach($pd['sessionsBySubject'] as $sid => $subSessions) {
                $subj = $subSessions->first()->subject ?? null;
                if (!$subj) continue;
                $subjectGroups[$sid] = [
                    'subject'   => $subj,
                    'sessions'  => $subSessions,
                    'color'     => $subj->color ?? $subjColors[$sci % count($subjColors)],
                    'total'     => $subSessions->count(),
                    'completed' => $subSessions->whereNotNull('ended_at')->count(),
                ];
                $sci++;
            }
        @endphp

        @if(count($subjectGroups) > 0)
        {{-- Subject tabs bar --}}
        <div style="display:flex;gap:8px;background:#fff;border-radius:18px;box-shadow:0 1px 3px rgba(0,0,0,.05);margin-bottom:20px;padding:12px 16px;flex-wrap:wrap;overflow-x:auto;">
            <button onclick="switchSubjTab_{{ $progId }}('all')" id="subjtab-{{ $progId }}-all" data-color="{{ $ptc }}"
                style="padding:8px 18px;border:none;border-radius:999px;cursor:pointer;font-size:.85rem;font-weight:700;font-family:inherit;transition:all .18s;white-space:nowrap;background:{{ $ptc }};color:white;">
                الكل
                <span style="font-size:.72rem;opacity:.85;margin-right:4px;">{{ $pd['totalSessions'] }}</span>
            </button>
            @foreach($subjectGroups as $sid => $sg)
            <button onclick="switchSubjTab_{{ $progId }}({{ $sid }})" id="subjtab-{{ $progId }}-{{ $sid }}" data-color="{{ $sg['color'] }}"
                style="padding:8px 18px;border:none;border-radius:999px;cursor:pointer;font-size:.85rem;font-weight:700;font-family:inherit;transition:all .18s;white-space:nowrap;background:#f3f4f6;color:#6b7280;">
                {{ $sg['subject']->name_ar ?? $sg['subject']->name }}
                <span style="font-size:.72rem;opacity:.8;margin-right:4px;">{{ $sg['completed'] }}/{{ $sg['total'] }}</span>
            </button>
            @endforeach
        </div>

        {{-- Per-subject panels --}}
        @foreach($subjectGroups as $sid => $sg)
        @php $color = $sg['color']; @endphp
        <div id="subjpanel-{{ $progId }}-{{ $sid }}" class="subj-panel-{{ $progId }}" style="display:none;">
            <div class="subject-section">
                <div class="subject-header" style="border-color:{{ $color }};">
                    <div class="subject-icon" style="background:linear-gradient(135deg,{{ $color }},{{ $color }}cc);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div style="flex:1;">
                        <div class="subject-name">{{ $sg['subject']->name_ar ?? $sg['subject']->name }}</div>
                        <div class="subject-meta">
                            @if($sg['subject']->term) {{ $sg['subject']->term->name_ar ?? $sg['subject']->term->name }} @endif
                            @if($sg['subject']->teacher) &bull; {{ $sg['subject']->teacher->name }} @endif
                        </div>
                    </div>
                    <span class="subject-badge">{{ $sg['completed'] }}/{{ $sg['total'] }} جلسة</span>
                </div>
                <div class="sessions-grid">
                    @foreach($sg['sessions'] as $session)
                    @include('student.sessions._session_card', ['session'=>$session,'color'=>$color,'attendances'=>$pd['attendances']])
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach

        {{-- "All" panel for this program --}}
        <div id="subjpanel-{{ $progId }}-all" style="display:block;">
            @foreach($subjectGroups as $sid => $sg)
            @php $color = $sg['color']; @endphp
            <div class="subject-section">
                <div class="subject-header" style="border-color:{{ $color }};">
                    <div class="subject-icon" style="background:linear-gradient(135deg,{{ $color }},{{ $color }}cc);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div style="flex:1;">
                        <div class="subject-name">{{ $sg['subject']->name_ar ?? $sg['subject']->name }}</div>
                        <div class="subject-meta">
                            @if($sg['subject']->term) {{ $sg['subject']->term->name_ar ?? $sg['subject']->term->name }} @endif
                            @if($sg['subject']->teacher) &bull; {{ $sg['subject']->teacher->name }} @endif
                        </div>
                    </div>
                    <span class="subject-badge">{{ $sg['completed'] }}/{{ $sg['total'] }} جلسة</span>
                </div>
                <div class="sessions-grid">
                    @foreach($sg['sessions'] as $session)
                    @include('student.sessions._session_card', ['session'=>$session,'color'=>$color,'attendances'=>$pd['attendances']])
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <script>
        function switchSubjTab_{{ $progId }}(id) {
            document.querySelectorAll('.subj-panel-{{ $progId }}').forEach(p => p.style.display='none');
            var allPanel = document.getElementById('subjpanel-{{ $progId }}-all');
            if (id === 'all') {
                if(allPanel) allPanel.style.display = 'block';
            } else {
                if(allPanel) allPanel.style.display = 'none';
                var p = document.getElementById('subjpanel-{{ $progId }}-' + id);
                if(p) p.style.display = 'block';
            }
            document.querySelectorAll('[id^="subjtab-{{ $progId }}-"]').forEach(btn => {
                btn.style.background = '#f3f4f6';
                btn.style.color = '#6b7280';
            });
            var activeBtn = document.getElementById('subjtab-{{ $progId }}-' + id);
            if (activeBtn) {
                var c = activeBtn.dataset.color || '{{ $ptc }}';
                activeBtn.style.background = c;
                activeBtn.style.color = '#fff';
            }
        }
        switchSubjTab_{{ $progId }}('all');
        </script>

        @else
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            <h3>لا توجد جلسات لهذا البرنامج</h3>
            <p>سيتم عرض الجلسات هنا عند توفرها</p>
        </div>
        @endif

        @else
        {{-- Non-diploma: flat session list --}}
        @if($pd['sessions']->count() > 0)
        <div style="display:flex;flex-direction:column;gap:10px;">
            @foreach($pd['sessions'] as $session)
            @php
                $isLive      = $session->started_at && !$session->ended_at;
                $isCompleted = !is_null($session->ended_at);
                $att         = $pd['attendances'][$session->id] ?? null;
            @endphp
            <div class="session-card" style="border-radius:16px;">
                <div class="session-card-top">
                    <div class="session-num" style="background: linear-gradient(135deg,{{ $isLive ? '#ef4444,#dc2626' : ($isCompleted ? '#10b981,#059669' : $ptc.','.$ptc.'cc') }});">
                        {{ $session->session_number ?? '—' }}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div class="session-title">{{ $session->title_ar ?: $session->title_en ?: 'جلسة بدون عنوان' }}</div>
                        <div class="session-info">
                            @if($isLive)
                                <span class="badge badge-live"><svg style="width:10px;height:10px;" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg> مباشر</span>
                            @elseif($isCompleted)
                                <span class="badge badge-completed">مكتمل</span>
                            @else
                                <span class="badge badge-scheduled">مجدول</span>
                            @endif
                            @if($session->type === 'live_zoom') <span class="badge badge-zoom">Zoom</span>
                            @else <span class="badge badge-video">فيديو</span> @endif
                            @if($att)
                                @if($att->attended)
                                    <span class="badge badge-attended">حضرت</span>
                                @else
                                    <span class="badge badge-absent">غائب</span>
                                @endif
                            @endif
                            @if($session->files && $session->files->count() > 0)
                                <span class="badge" style="background:#f3e8ff;color:#7c3aed;">{{ $session->files->count() }} ملف</span>
                            @endif
                            @if($session->homework)
                                <span class="badge" style="background:#fef3c7;color:#d97706;">واجب</span>
                            @endif
                        </div>
                    </div>
                    @php $joinUrl = $session->zoom_link ?? $session->zoom_join_url ?? null; @endphp
                    @if($joinUrl && !$session->ended_at)
                    <a href="{{ $joinUrl }}" target="_blank" class="btn btn-join" style="white-space:nowrap;flex-shrink:0;{{ !$isLive ? 'background:linear-gradient(135deg,#2563eb,#1d4ed8);' : '' }}">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        انضم للحصة
                    </a>
                    @endif
                </div>
                @if($session->scheduled_at || $session->duration_minutes)
                <div class="session-card-meta">
                    @if($session->scheduled_at)
                    <div class="meta-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d · H:i') }}
                    </div>
                    @endif
                    @if($session->duration_minutes)
                    <div class="meta-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $session->duration_minutes }} دقيقة
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            <h3>لا توجد جلسات حالياً</h3>
            <p>سيتم عرض جلسات ومحاضرات برنامجك التدريبي هنا عند توفرها</p>
        </div>
        @endif
        @endif {{-- end isDiploma --}}

    </div>
    @endforeach {{-- end programsSessionData loop --}}

    <script>
    function switchProgSessionTab(id) {
        document.querySelectorAll('.progsess-panel').forEach(p => p.style.display='none');
        var panel = document.getElementById('progsess-panel-' + id);
        if(panel) panel.style.display = 'block';
        document.querySelectorAll('[id^="progsess-tab-"]').forEach(btn => {
            btn.style.background = '#f3f4f6';
            btn.style.color = '#6b7280';
        });
        var tab = document.getElementById('progsess-tab-' + id);
        if(tab) {
            tab.style.background = tab.dataset.color;
            tab.style.color = '#fff';
        }
    }
    @if($firstProgId) switchProgSessionTab({{ $firstProgId }}); @endif
    </script>

    @else
    <div class="empty-state">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
        <h3>لا توجد برامج مسجّل فيها</h3>
        <p>يرجى التواصل مع الإدارة لتسجيلك في برنامج تدريبي</p>
    </div>
    @endif

</div>
@endsection
