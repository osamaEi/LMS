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

    {{-- Weekly view --}}
    <div id="main-view-weekly">
    @php $allCalSessions = $classSessions ?? collect(); @endphp

    <div style="background:white;border-radius:18px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:16px 20px;border-bottom:1px solid #f1f5f9;background:#fafafa;">
            <span style="font-size:15px;font-weight:700;color:#111827;">الجدول الأسبوعي</span>
            <div style="display:flex;align-items:center;gap:10px;">
                <button type="button" onclick="calPrev()" style="width:36px;height:36px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;color:#374151;font-size:18px;">&#8249;</button>
                <h3 id="calTitle" style="font-size:16px;font-weight:700;color:#111827;margin:0;min-width:220px;text-align:center;"></h3>
                <button type="button" onclick="calNext()" style="width:36px;height:36px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;color:#374151;font-size:18px;">&#8250;</button>
                <button type="button" onclick="calToday()" style="padding:8px 14px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;color:#0071AA;font-size:12px;font-weight:700;">اليوم</button>
            </div>
        </div>
        <div id="calBody" style="overflow-x:auto;"></div>
    </div>

    {{-- Session detail modal --}}
    <div id="sessionModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);align-items:center;justify-content:center;padding:16px;">
        <div style="background:white;border-radius:20px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;">
            <div style="padding:18px 20px;background:linear-gradient(135deg,#0f172a,#0071AA);display:flex;align-items:center;justify-content:space-between;">
                <h3 id="smTitle" style="color:white;font-size:15px;font-weight:700;margin:0;"></h3>
                <button onclick="document.getElementById('sessionModal').style.display='none'" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:28px;height:28px;color:white;cursor:pointer;font-size:16px;">×</button>
            </div>
            <div id="smBody" style="padding:18px;"></div>
        </div>
    </div>

    <script>
    const CAL_SESSIONS = @json($allCalSessions->values());
    const TODAY_CAL = new Date();
    // Default to week of next upcoming session if current week has no sessions
    let curCal = (function(){
        const now = new Date();
        const ws = new Date(now); ws.setDate(ws.getDate()-ws.getDay()); ws.setHours(0,0,0,0);
        const we = new Date(ws); we.setDate(we.getDate()+6); we.setHours(23,59,59,999);
        const hasThisWeek = CAL_SESSIONS.some(s=>{ const d=new Date(s.scheduled_at); return d>=ws&&d<=we; });
        if(!hasThisWeek){
            const future = CAL_SESSIONS.filter(s=>s.scheduled_at&&new Date(s.scheduled_at)>now).sort((a,b)=>new Date(a.scheduled_at)-new Date(b.scheduled_at));
            if(future.length) return new Date(future[0].scheduled_at);
        }
        return now;
    })();

    const DAY_NAMES_CAL   = ['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
    const MONTH_NAMES_CAL = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];

    const tMin = (h,m) => h*60+m;
    const PERIODS_CAL = [
        { e:tMin(9,20),  range:'9:20 - 8:10',   name:'الفترة الصباحية (1)' },
        { e:tMin(10,40), range:'10:40 - 9:30',  name:'الفترة الصباحية (2)' },
        { e:tMin(12,0),  range:'12:00 - 10:50', name:'الفترة الصباحية (3)' },
        { e:tMin(13,25), range:'1:25 - 12:20',  name:'الفترة المسائية (1)' },
        { e:tMin(14,40), range:'2:40 - 1:35',   name:'الفترة المسائية (2)' },
        { e:tMin(15,55), range:'3:55 - 2:50',   name:'الفترة المسائية (3)' },
        { e:tMin(17,15), range:'5:15 - 4:00',   name:'الفترة المسائية (4)' },
    ];
    const getPeriod = mins => { for(let p=0;p<PERIODS_CAL.length;p++) if(mins<PERIODS_CAL[p].e) return p; return PERIODS_CAL.length-1; };

    function sameDayCal(a,b){ return a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate(); }
    function fmtTimeCal(iso){ const d=new Date(iso); let h=d.getHours(),m=String(d.getMinutes()).padStart(2,'0'); return (h%12||12)+':'+m+(h<12?' ص':' م'); }
    function typeStyleCal(type){
        if(type==='live_zoom')      return {bg:'#dbeafe',color:'#1d4ed8',label:'Zoom'};
        if(type==='in_person')      return {bg:'#dcfce7',color:'#15803d',label:'حضوري'};
        if(type==='recorded_video') return {bg:'#fce7f3',color:'#be185d',label:'مسجّل'};
        return {bg:'#f3f4f6',color:'#4b5563',label:type||'—'};
    }
    function weekStartCal(d){ const c=new Date(d); c.setDate(c.getDate()-c.getDay()); c.setHours(0,0,0,0); return c; }
    function sessionsOnDayCal(date){ return CAL_SESSIONS.filter(s=>s.scheduled_at&&sameDayCal(new Date(s.scheduled_at),date)).sort((a,b)=>new Date(a.scheduled_at)-new Date(b.scheduled_at)); }

    function openSessionCal(s){
        document.getElementById('smTitle').textContent = s.title||('جلسة #'+s.session_number);
        document.getElementById('sessionModal').style.display='flex';
        const ts=typeStyleCal(s.type);
        const statusBg    = s.status==='completed'?'#dcfce7':s.status==='live'?'#fee2e2':'#dbeafe';
        const statusColor = s.status==='completed'?'#15803d':s.status==='live'?'#dc2626':'#1d4ed8';
        const statusLabel = s.status==='completed'?'مكتملة':s.status==='live'?'● مباشر':'مجدولة';
        const rows=[
            s.scheduled_at?['📅 الموعد', new Date(s.scheduled_at).toLocaleDateString('ar-SA',{weekday:'long',year:'numeric',month:'long',day:'numeric'})+' — '+fmtTimeCal(s.scheduled_at)]:null,
            s.duration_minutes?['⏱ المدة', s.duration_minutes+' دقيقة']:null,
            s.subject_name?['📚 المادة', s.subject_name]:null,
            s.teacher_name?['👤 المدرب', s.teacher_name]:null,
            ['🔖 النوع', ts.label],
            ['📊 الحالة', statusLabel],
            s.attended!==null?['✅ الحضور', s.attended?'حضرت':'غائب']:null,
        ].filter(Boolean);
        let html='<div style="display:flex;flex-direction:column;gap:10px;">';
        rows.forEach(([k,v])=>{
            html+=`<div style="display:flex;align-items:center;gap:10px;padding:8px 12px;background:#f8fafc;border-radius:9px;">
                <span style="font-size:12px;color:#64748b;min-width:90px;">${k}</span>
                <span style="font-size:13px;font-weight:600;color:#1e293b;">${v}</span>
            </div>`;
        });
        if(s.zoom_join_url&&s.status!=='completed'){
            html+=`<a href="${s.zoom_join_url}" target="_blank" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:white;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;margin-top:4px;">
                <svg width="16" height="16" fill="white" viewBox="0 0 24 24"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
                انضمام عبر Zoom
            </a>`;
        }
        if(s.status==='completed'){
            html+=`<div style="text-align:center;padding:8px;background:#f0fdf4;border-radius:10px;font-size:13px;font-weight:600;color:#15803d;">✓ انتهت هذه الجلسة</div>`;
        }
        html+='</div>';
        document.getElementById('smBody').innerHTML=html;
    }

    function renderCal(){
        const ws=weekStartCal(curCal);
        const wend=new Date(ws.getFullYear(),ws.getMonth(),ws.getDate()+6);
        document.getElementById('calTitle').textContent=ws.getDate()+' '+MONTH_NAMES_CAL[ws.getMonth()]+' — '+wend.getDate()+' '+MONTH_NAMES_CAL[wend.getMonth()]+' '+wend.getFullYear();

        const weekDays=[];
        for(let i=0;i<5;i++){ const d=new Date(ws); d.setDate(d.getDate()+i); weekDays.push(d); }

        const cellMap={};
        weekDays.forEach((d,i)=>{
            sessionsOnDayCal(d).forEach(s=>{
                const dt=new Date(s.scheduled_at); const mins=dt.getHours()*60+dt.getMinutes();
                const p=getPeriod(mins);
                (cellMap[i+'|'+p]=cellMap[i+'|'+p]||[]).push(s);
            });
        });

        let head=`<th style="padding:10px 6px;background:#0071AA;color:#fff;font-size:12px;font-weight:700;border:1px solid #fff;width:80px;">اليوم<br><span style="font-size:10px;opacity:.85;">الفترة</span></th>`;
        PERIODS_CAL.forEach(p=>{
            head+=`<th style="padding:8px 6px;background:#0071AA;color:#fff;font-size:12px;font-weight:700;border:1px solid #fff;line-height:1.6;">
                ${p.name}<br><bdi dir="ltr" style="font-size:11px;font-weight:600;opacity:.9;display:inline-block;unicode-bidi:isolate;">${p.range}</bdi>
            </th>`;
        });

        let body='';
        weekDays.forEach((d,i)=>{
            const isToday=sameDayCal(d,TODAY_CAL);
            let row=`<td style="padding:10px 6px;text-align:center;font-size:13px;font-weight:700;color:#fff;background:${isToday?'#005a88':'#0071AA'};border:1px solid #fff;line-height:1.4;">
                ${DAY_NAMES_CAL[i]}<br><span style="font-size:11px;font-weight:500;opacity:.85;">${d.getDate()} ${MONTH_NAMES_CAL[d.getMonth()]}</span>
            </td>`;
            PERIODS_CAL.forEach((p,pi)=>{
                const items=cellMap[i+'|'+pi]||[];
                const inner=items.map(s=>{
                    const ts=typeStyleCal(s.type);
                    const statusBg    = s.status==='completed'?'#dcfce7':s.status==='live'?'#fee2e2':'#eff6ff';
                    const statusColor = s.status==='completed'?'#15803d':s.status==='live'?'#dc2626':'#1e3a8a';
                    const statusLabel = s.status==='completed'?'مكتملة':s.status==='live'?'● مباشر':'مجدولة';
                    const showSub = s.subject_name && !(s.title||'').includes(s.subject_name);
                    const attendedBadge = s.attended===true?`<span style="background:#dcfce7;color:#15803d;font-size:10px;font-weight:600;padding:1px 6px;border-radius:20px;">حضرت</span>`:s.attended===false?`<span style="background:#fee2e2;color:#dc2626;font-size:10px;font-weight:600;padding:1px 6px;border-radius:20px;">غائب</span>`:'';
                    return `<div onclick='openSessionCal(${JSON.stringify(s)})' style="background:#eff6ff;border-right:3px solid #0071AA;border-radius:6px;padding:6px 8px;margin-bottom:4px;line-height:1.35;cursor:pointer;">
                        <div style="font-size:12px;font-weight:700;color:#1e3a8a;">${s.title||s.subject_name||'جلسة'}</div>
                        ${showSub?`<div style="font-size:10px;color:#64748b;">${s.subject_name}</div>`:''}
                        ${s.teacher_name?`<div style="font-size:10px;color:#64748b;">👤 ${s.teacher_name}</div>`:''}
                        <div style="display:flex;gap:4px;flex-wrap:wrap;margin-top:4px;">
                            <span style="background:${ts.bg};color:${ts.color};font-size:10px;font-weight:600;padding:1px 6px;border-radius:20px;">${ts.label}</span>
                            <span style="background:${statusBg};color:${statusColor};font-size:10px;font-weight:600;padding:1px 6px;border-radius:20px;">${statusLabel}</span>
                            ${attendedBadge}
                            ${(s.status==='live'||s.status==='scheduled')&&s.zoom_join_url
                                ?`<a href="${s.zoom_join_url}" target="_blank" onclick="event.stopPropagation()" style="background:#2563eb;color:white;font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;text-decoration:none;">📹 انضم</a>`
                                :''}
                        </div>
                    </div>`;
                }).join('');
                row+=`<td style="min-height:80px;padding:5px;vertical-align:top;border:1px solid #d6e4f0;${isToday?'background:#f8fdff;':''}">${inner}</td>`;
            });
            body+=`<tr>${row}</tr>`;
        });

        document.getElementById('calBody').innerHTML=`
            <table style="width:100%;min-width:1000px;border-collapse:collapse;table-layout:fixed;">
                <thead><tr>${head}</tr></thead>
                <tbody>${body}</tbody>
            </table>`;
    }

    function calPrev(){ curCal.setDate(curCal.getDate()-7); renderCal(); }
    function calNext(){ curCal.setDate(curCal.getDate()+7); renderCal(); }
    function calToday(){ curCal=new Date(); renderCal(); }

    renderCal();

    </script>
    </div>{{-- /main-view-weekly --}}

    <div style="display:none;">{{-- list view hidden --}}

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

    </div>{{-- /main-view-list --}}

</div>
@endsection
