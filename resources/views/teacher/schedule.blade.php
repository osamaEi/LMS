@extends('layouts.dashboard')
@section('title', 'الجدول الأكاديمي')

@php
$calSessions = $sessions->map(fn($s) => [
    'id'               => $s->id,
    'title'            => $s->title_ar ?: ($s->subject->name_ar ?? $s->program->name_ar ?? 'جلسة'),
    'subject_name'     => $s->subject->name_ar ?? '',
    'program_name'     => $s->program->name_ar ?? $s->subject?->program?->name_ar ?? '',
    'scheduled_at'     => $s->scheduled_at ? \Carbon\Carbon::parse($s->scheduled_at)->toIso8601String() : null,
    'duration_minutes' => $s->duration_minutes ?? 60,
    'type'             => $s->type ?? '',
    'status'           => (string)($s->status ?? ''),
    'session_number'   => $s->session_number,
    'zoom_join_url'    => $s->zoom_join_url,
])->filter(fn($s) => $s['scheduled_at'])->values();
@endphp

@section('content')
<div style="direction:rtl;font-family:'Segoe UI',sans-serif;">

{{-- Hero --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 55%,#0071AA 100%);border-radius:20px;padding:24px 28px;margin-bottom:24px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-60px;left:-60px;width:220px;height:220px;background:rgba(255,255,255,.05);border-radius:50%;pointer-events:none;"></div>
    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:48px;height:48px;background:rgba(255,255,255,.12);border-radius:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="22" height="22" fill="white" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zM7 12h5v5H7z"/></svg>
            </div>
            <div>
                <p style="color:rgba(255,255,255,.5);font-size:12px;margin:0 0 2px;">{{ now()->translatedFormat('l، d F Y') }}</p>
                <h1 style="color:white;font-size:20px;font-weight:700;margin:0;">الجدول الأكاديمي</h1>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            @foreach([
                [$stats['total'],     'الكل',    'rgba(255,255,255,.8)'],
                [$stats['upcoming'],  'قادمة',   '#fde68a'],
                [$stats['live'],      'مباشرة',  '#86efac'],
                [$stats['completed'], 'مكتملة',  '#a5b4fc'],
            ] as [$v,$l,$c])
            <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:8px 16px;text-align:center;min-width:60px;">
                <div style="font-size:20px;font-weight:700;color:{{ $c }};line-height:1;">{{ $v }}</div>
                <div style="font-size:11px;color:rgba(255,255,255,.5);margin-top:2px;">{{ $l }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Calendar --}}
<div style="background:white;border-radius:18px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);">

    {{-- Toolbar --}}
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid #f1f5f9;background:#fafafa;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:center;gap:8px;">
            <button onclick="navPrev()" style="width:34px;height:34px;border-radius:9px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:16px;color:#374151;">&#8249;</button>
            <button onclick="goToToday()" style="padding:0 14px;height:34px;border-radius:9px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;font-size:12px;font-weight:600;color:#374151;">اليوم</button>
            <button onclick="navNext()" style="width:34px;height:34px;border-radius:9px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:16px;color:#374151;">&#8250;</button>
        </div>
        <h2 id="calTitle" style="font-size:16px;font-weight:700;color:#111827;margin:0;flex:1;text-align:center;"></h2>
        <div style="display:flex;background:#f1f5f9;border-radius:10px;padding:3px;gap:2px;">
            <button id="v-month" onclick="setView('month')" style="padding:6px 14px;border-radius:8px;border:none;font-size:12px;font-weight:600;cursor:pointer;background:linear-gradient(135deg,#0071AA,#005a88);color:white;">شهر</button>
            <button id="v-week"  onclick="setView('week')"  style="padding:6px 14px;border-radius:8px;border:none;font-size:12px;font-weight:600;cursor:pointer;background:transparent;color:#6b7280;">أسبوع</button>
            <button id="v-day"   onclick="setView('day')"   style="padding:6px 14px;border-radius:8px;border:none;font-size:12px;font-weight:600;cursor:pointer;background:transparent;color:#6b7280;">يوم</button>
        </div>
    </div>

    {{-- Month view --}}
    <div id="view-month">
        <div id="calHeaders" style="display:grid;grid-template-columns:repeat(7,1fr);"></div>
        <div id="calGrid"    style="display:grid;grid-template-columns:repeat(7,1fr);"></div>
    </div>

    {{-- Week view --}}
    <div id="view-week" style="display:none;">
        <div id="weekHeaders" style="display:grid;padding-right:48px;"></div>
        <div style="overflow-y:auto;max-height:600px;">
            <div style="display:flex;position:relative;">
                <div id="timeLabels" style="width:48px;flex-shrink:0;"></div>
                <div id="weekGrid"   style="flex:1;position:relative;"></div>
            </div>
        </div>
    </div>

    {{-- Day view --}}
    <div id="view-day" style="display:none;">
        <div style="overflow-y:auto;max-height:600px;">
            <div style="display:flex;position:relative;">
                <div id="dayTimeLabels" style="width:48px;flex-shrink:0;"></div>
                <div id="dayGrid"       style="flex:1;position:relative;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Day panel --}}
<div id="dayPanel" style="display:none;background:white;border-radius:16px;border:1px solid #e5e7eb;margin-top:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);overflow:hidden;">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;background:#fafafa;">
        <h3 id="dayPanelTitle" style="font-size:14px;font-weight:700;color:#111827;margin:0;"></h3>
        <button onclick="document.getElementById('dayPanel').style.display='none'" style="width:26px;height:26px;border-radius:7px;border:none;background:#f1f5f9;cursor:pointer;color:#6b7280;font-size:16px;">×</button>
    </div>
    <div id="dayPanelContent" style="padding:14px;display:flex;flex-direction:column;gap:8px;"></div>
</div>

{{-- Session detail modal --}}
<div id="sessionModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);align-items:center;justify-content:center;padding:16px;">
    <div style="background:white;border-radius:20px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.25);overflow:hidden;">
        <div id="smHeader" style="padding:18px 20px;background:linear-gradient(135deg,#0f172a,#0071AA);display:flex;align-items:center;justify-content:space-between;">
            <h3 id="smTitle" style="color:white;font-size:15px;font-weight:700;margin:0;"></h3>
            <button onclick="document.getElementById('sessionModal').style.display='none'" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:28px;height:28px;color:white;cursor:pointer;font-size:16px;">×</button>
        </div>
        <div id="smBody" style="padding:18px;"></div>
    </div>
</div>

</div>

@push('scripts')
<script>
const CAL_SESSIONS = @json($calSessions);
const DAYS_AR = ['الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
const MONTHS_AR = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];

let currentView = 'month';
let currentDate = new Date();

// ── Helpers ──
function sameDay(a,b){ return a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate(); }
function startOfWeek(d){ const c=new Date(d); c.setDate(c.getDate()-c.getDay()); return c; }
function fmtTime(dt){ return dt.getHours().toString().padStart(2,'0')+':'+dt.getMinutes().toString().padStart(2,'0'); }

function statusLabel(s){
    const m={'live':'🔴 مباشرة','completed':'✅ مكتملة','scheduled':'📅 مجدولة','cancelled':'❌ ملغاة'};
    return m[s]||s;
}
function typeLabel(t){
    return t==='live_zoom'?'🎥 زووم':t==='recorded_video'?'🎬 مسجلة':'📚 محاضرة';
}

// ── Navigation ──
function navPrev(){
    if(currentView==='month'){ currentDate.setMonth(currentDate.getMonth()-1); }
    else if(currentView==='week'){ currentDate.setDate(currentDate.getDate()-7); }
    else { currentDate.setDate(currentDate.getDate()-1); }
    render();
}
function navNext(){
    if(currentView==='month'){ currentDate.setMonth(currentDate.getMonth()+1); }
    else if(currentView==='week'){ currentDate.setDate(currentDate.getDate()+7); }
    else { currentDate.setDate(currentDate.getDate()+1); }
    render();
}
function goToToday(){ currentDate = new Date(); render(); }
function setView(v){
    currentView=v;
    ['month','week','day'].forEach(x=>{
        document.getElementById('view-'+x).style.display = x===v?'block':'none';
        const btn=document.getElementById('v-'+x);
        btn.style.background = x===v?'linear-gradient(135deg,#0071AA,#005a88)':'transparent';
        btn.style.color      = x===v?'white':'#6b7280';
    });
    render();
}

// ── Session detail modal ──
function openSession(s){
    const dt = s.scheduled_at ? new Date(s.scheduled_at) : null;
    document.getElementById('smTitle').textContent = s.title || ('جلسة #'+s.session_number);
    document.getElementById('sessionModal').style.display='flex';

    const rows = [
        dt ? ['📅 الموعد', dt.toLocaleDateString('ar-SA',{weekday:'long',year:'numeric',month:'long',day:'numeric'})+' - '+fmtTime(dt)] : null,
        dt ? ['⏱ المدة', (s.duration_minutes||60)+' دقيقة'] : null,
        s.subject_name ? ['📚 المادة', s.subject_name] : null,
        s.program_name ? ['🎓 البرنامج', s.program_name] : null,
        ['🔖 النوع', typeLabel(s.type)],
        ['📊 الحالة', statusLabel(s.status)],
    ].filter(Boolean);

    let html = '<div style="display:flex;flex-direction:column;gap:10px;">';
    rows.forEach(([k,v])=>{
        html+=`<div style="display:flex;align-items:center;gap:10px;padding:8px 12px;background:#f8fafc;border-radius:9px;">
            <span style="font-size:12px;color:#64748b;min-width:90px;">${k}</span>
            <span style="font-size:13px;font-weight:600;color:#1e293b;">${v}</span>
        </div>`;
    });

    if(s.zoom_join_url && s.status !== 'completed'){
        html+=`<a href="${s.zoom_join_url}" target="_blank" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:white;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;margin-top:4px;">
            <svg width="16" height="16" fill="white" viewBox="0 0 24 24"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
            انضمام عبر Zoom
        </a>`;
    }

    html += '</div>';
    document.getElementById('smBody').innerHTML = html;
}

function sessionChip(s, compact=false){
    const colors = {
        live:      ['#fee2e2','#dc2626'],
        completed: ['#dcfce7','#16a34a'],
        scheduled: ['#dbeafe','#2563eb'],
        cancelled: ['#f1f5f9','#94a3b8'],
    };
    const [bg,cl] = colors[s.status] || ['#f1f5f9','#64748b'];
    const label = s.title || ('جلسة #'+s.session_number);
    const dt = s.scheduled_at ? new Date(s.scheduled_at) : null;
    const time = dt ? fmtTime(dt) : '';

    return `<div onclick='openSession(${JSON.stringify(s)})' style="background:${bg};border-right:3px solid ${cl};border-radius:6px;padding:${compact?'3px 6px':'5px 8px'};cursor:pointer;margin-bottom:2px;overflow:hidden;">
        <div style="font-size:${compact?'10':'11'}px;font-weight:700;color:${cl};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${time} ${label}</div>
        ${!compact&&s.subject_name?`<div style="font-size:10px;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${s.subject_name||s.program_name}</div>`:''}
    </div>`;
}

// ── Month view ──
function renderMonth(){
    const today = new Date();
    const year  = currentDate.getFullYear();
    const month = currentDate.getMonth();
    document.getElementById('calTitle').textContent = MONTHS_AR[month]+' '+year;

    const headers = document.getElementById('calHeaders');
    headers.innerHTML = DAYS_AR.map(d=>`<div style="padding:10px 4px;text-align:center;font-size:11px;font-weight:700;color:#6b7280;border-bottom:1px solid #f1f5f9;">${d}</div>`).join('');

    const first = new Date(year,month,1).getDay();
    const days  = new Date(year,month+1,0).getDate();
    const grid  = document.getElementById('calGrid');
    grid.innerHTML='';

    for(let i=0;i<first;i++) grid.innerHTML+=`<div style="min-height:90px;border:1px solid #f8fafc;"></div>`;

    for(let d=1;d<=days;d++){
        const date = new Date(year,month,d);
        const isToday = sameDay(date,today);
        const daySessions = CAL_SESSIONS.filter(s=>sameDay(new Date(s.scheduled_at),date));

        const cell = document.createElement('div');
        cell.style.cssText=`min-height:90px;border:1px solid #f1f5f9;padding:6px;background:${isToday?'#eff6ff':'white'};cursor:pointer;transition:background .15s;`;
        cell.onmouseenter=()=>{ if(!isToday) cell.style.background='#f8fafc'; };
        cell.onmouseleave=()=>{ if(!isToday) cell.style.background='white'; };

        let html=`<div style="font-size:12px;font-weight:700;color:${isToday?'#2563eb':'#374151'};margin-bottom:4px;${isToday?'background:#2563eb;color:white;width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;':''}">${d}</div>`;

        const visible = daySessions.slice(0,2);
        visible.forEach(s=>{ html+=sessionChip(s,true); });
        if(daySessions.length>2){
            html+=`<div onclick="showDayPanel(new Date(${date.getTime()}))" style="font-size:10px;color:#2563eb;font-weight:600;cursor:pointer;">+${daySessions.length-2} أخرى</div>`;
        }
        cell.innerHTML=html;
        if(daySessions.length) cell.onclick=()=>showDayPanel(date);
        grid.appendChild(cell);
    }
}

function showDayPanel(date){
    const sessions = CAL_SESSIONS.filter(s=>sameDay(new Date(s.scheduled_at),date));
    document.getElementById('dayPanelTitle').textContent = DAYS_AR[date.getDay()]+' '+date.getDate()+' '+MONTHS_AR[date.getMonth()]+' '+date.getFullYear();
    document.getElementById('dayPanelContent').innerHTML = sessions.length
        ? sessions.map(s=>sessionChip(s,false)).join('')
        : '<p style="color:#94a3b8;font-size:13px;text-align:center;padding:20px 0;">لا توجد جلسات</p>';
    document.getElementById('dayPanel').style.display='block';
}

// ── Week view ──
function renderWeek(){
    const start = startOfWeek(currentDate);
    const days  = Array.from({length:7},(_,i)=>{ const d=new Date(start); d.setDate(d.getDate()+i); return d; });
    document.getElementById('calTitle').textContent = DAYS_AR[days[0].getDay()]+' '+days[0].getDate()+' - '+DAYS_AR[days[6].getDay()]+' '+days[6].getDate()+' '+MONTHS_AR[days[0].getMonth()];

    const today = new Date();
    const headers = document.getElementById('weekHeaders');
    headers.style.gridTemplateColumns='repeat(7,1fr)';
    headers.innerHTML = days.map(d=>`
        <div style="padding:8px 4px;text-align:center;border-bottom:1px solid #f1f5f9;${sameDay(d,today)?'color:#2563eb;font-weight:800;':'color:#374151;'}">
            <div style="font-size:10px;color:#94a3b8;">${DAYS_AR[d.getDay()]}</div>
            <div style="font-size:16px;font-weight:700;">${d.getDate()}</div>
        </div>`).join('');

    const timeLabels = document.getElementById('timeLabels');
    const weekGrid   = document.getElementById('weekGrid');
    const PX_PER_HOUR = 60;
    const HOURS = 24;
    timeLabels.style.cssText='width:48px;flex-shrink:0;position:relative;';
    timeLabels.innerHTML='';
    for(let h=0;h<HOURS;h++){
        timeLabels.innerHTML+=`<div style="height:${PX_PER_HOUR}px;font-size:10px;color:#94a3b8;padding-top:2px;text-align:center;">${h.toString().padStart(2,'0')}:00</div>`;
    }

    weekGrid.style.cssText=`display:grid;grid-template-columns:repeat(7,1fr);position:relative;height:${HOURS*PX_PER_HOUR}px;`;
    weekGrid.innerHTML='';
    for(let h=0;h<HOURS;h++){
        for(let col=0;col<7;col++){
            const cell=document.createElement('div');
            cell.style.cssText=`border-top:1px solid #f1f5f9;border-right:1px solid #f1f5f9;height:${PX_PER_HOUR}px;`;
            weekGrid.appendChild(cell);
        }
    }

    days.forEach((day,colIdx)=>{
        CAL_SESSIONS.filter(s=>sameDay(new Date(s.scheduled_at),day)).forEach(s=>{
            const dt=new Date(s.scheduled_at);
            const top=(dt.getHours()*60+dt.getMinutes())/60*PX_PER_HOUR;
            const h=(s.duration_minutes||60)/60*PX_PER_HOUR;
            const colors={live:['#fee2e2','#dc2626'],completed:['#dcfce7','#16a34a'],scheduled:['#dbeafe','#2563eb'],cancelled:['#f1f5f9','#94a3b8']};
            const [bg,cl]=colors[s.status]||['#f1f5f9','#64748b'];
            const el=document.createElement('div');
            el.style.cssText=`position:absolute;top:${top}px;left:calc(${colIdx}/7*100%);width:calc(100%/7 - 2px);height:${Math.max(h,20)}px;background:${bg};border-right:3px solid ${cl};border-radius:5px;padding:2px 4px;overflow:hidden;cursor:pointer;z-index:1;`;
            el.innerHTML=`<div style="font-size:10px;font-weight:700;color:${cl};">${fmtTime(dt)} ${s.title||'جلسة #'+s.session_number}</div>`;
            el.onclick=()=>openSession(s);
            weekGrid.appendChild(el);
        });
    });
}

// ── Day view ──
function renderDay(){
    const today = new Date();
    document.getElementById('calTitle').textContent = DAYS_AR[currentDate.getDay()]+' '+currentDate.getDate()+' '+MONTHS_AR[currentDate.getMonth()]+' '+currentDate.getFullYear();
    const PX_PER_HOUR=80;
    const HOURS=24;

    const timeLabels=document.getElementById('dayTimeLabels');
    const dayGrid=document.getElementById('dayGrid');
    timeLabels.style.cssText='width:48px;flex-shrink:0;';
    timeLabels.innerHTML='';
    for(let h=0;h<HOURS;h++){
        timeLabels.innerHTML+=`<div style="height:${PX_PER_HOUR}px;font-size:10px;color:#94a3b8;padding-top:2px;text-align:center;">${h.toString().padStart(2,'0')}:00</div>`;
    }

    dayGrid.style.cssText=`position:relative;height:${HOURS*PX_PER_HOUR}px;`;
    dayGrid.innerHTML='';
    for(let h=0;h<HOURS;h++){
        const line=document.createElement('div');
        line.style.cssText=`position:absolute;top:${h*PX_PER_HOUR}px;left:0;right:0;border-top:1px solid #f1f5f9;`;
        dayGrid.appendChild(line);
    }

    CAL_SESSIONS.filter(s=>sameDay(new Date(s.scheduled_at),currentDate)).forEach(s=>{
        const dt=new Date(s.scheduled_at);
        const top=(dt.getHours()*60+dt.getMinutes())/60*PX_PER_HOUR;
        const h=(s.duration_minutes||60)/60*PX_PER_HOUR;
        const colors={live:['#fee2e2','#dc2626'],completed:['#dcfce7','#16a34a'],scheduled:['#dbeafe','#2563eb'],cancelled:['#f1f5f9','#94a3b8']};
        const [bg,cl]=colors[s.status]||['#f1f5f9','#64748b'];
        const el=document.createElement('div');
        el.style.cssText=`position:absolute;top:${top}px;left:4px;right:4px;height:${Math.max(h,30)}px;background:${bg};border-right:4px solid ${cl};border-radius:8px;padding:6px 10px;overflow:hidden;cursor:pointer;`;
        el.innerHTML=`<div style="font-size:12px;font-weight:700;color:${cl};">${fmtTime(dt)} — ${s.title||'جلسة #'+s.session_number}</div>
            ${s.subject_name?`<div style="font-size:11px;color:#64748b;">${s.subject_name||s.program_name}</div>`:''}`;
        el.onclick=()=>openSession(s);
        dayGrid.appendChild(el);
    });
}

// ── Main render ──
function render(){
    document.getElementById('dayPanel').style.display='none';
    if(currentView==='month') renderMonth();
    else if(currentView==='week') renderWeek();
    else renderDay();
}

render();
</script>
@endpush
@endsection
