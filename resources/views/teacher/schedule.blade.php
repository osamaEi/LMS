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

{{-- Weekly Calendar --}}
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

</div>

@push('scripts')
<script>
const CAL_SESSIONS = @json($calSessions);
const TODAY = new Date();
let cur = new Date();

const DAY_NAMES   = ['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
const MONTH_NAMES = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];

const t = (h,m) => h*60+m;
const PERIODS = [
    { s:t(8,10),  e:t(9,20),  range:'9:20 - 8:10',   name:'الفترة الصباحية (1)' },
    { s:t(9,30),  e:t(10,40), range:'10:40 - 9:30',  name:'الفترة الصباحية (2)' },
    { s:t(10,50), e:t(12,0),  range:'12:00 - 10:50', name:'الفترة الصباحية (3)' },
    { s:t(12,20), e:t(13,25), range:'1:25 - 12:20',  name:'الفترة المسائية (1)' },
    { s:t(13,35), e:t(14,40), range:'2:40 - 1:35',   name:'الفترة المسائية (2)' },
    { s:t(14,50), e:t(15,55), range:'3:55 - 2:50',   name:'الفترة المسائية (3)' },
    { s:t(16,0),  e:t(17,15), range:'5:15 - 4:00',   name:'الفترة المسائية (4)' },
];
const periodIndex = mins => { for(let p=0;p<PERIODS.length;p++) if(mins<PERIODS[p].e) return p; return PERIODS.length-1; };

function sameDay(a,b){ return a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate(); }
function fmtTime(iso){ const d=new Date(iso); let h=d.getHours(),m=String(d.getMinutes()).padStart(2,'0'); return (h%12||12)+':'+m+(h<12?' ص':' م'); }

function typeStyle(type){
    if(type==='live_zoom')      return {bg:'#dbeafe',color:'#1d4ed8',label:'Zoom'};
    if(type==='in_person')      return {bg:'#dcfce7',color:'#15803d',label:'حضوري'};
    if(type==='recorded_video') return {bg:'#fce7f3',color:'#be185d',label:'مسجّل'};
    return {bg:'#f3f4f6',color:'#4b5563',label:type||'—'};
}

function weekStart(d){ const c=new Date(d); c.setDate(c.getDate()-c.getDay()); c.setHours(0,0,0,0); return c; }

function sessionsOnDay(date){
    return CAL_SESSIONS.filter(s=>s.scheduled_at&&sameDay(new Date(s.scheduled_at),date))
        .sort((a,b)=>new Date(a.scheduled_at)-new Date(b.scheduled_at));
}

function openSession(s){
    const dt = s.scheduled_at ? new Date(s.scheduled_at) : null;
    document.getElementById('smTitle').textContent = s.title || ('جلسة #'+s.session_number);
    document.getElementById('sessionModal').style.display='flex';
    const ts = typeStyle(s.type);
    const statusBg    = s.status==='completed'?'#dcfce7':s.status==='live'?'#fee2e2':'#dbeafe';
    const statusColor = s.status==='completed'?'#15803d':s.status==='live'?'#dc2626':'#1d4ed8';
    const statusLabel = s.status==='completed'?'مكتملة':s.status==='live'?'● مباشر':'مجدولة';
    const rows = [
        dt?['📅 الموعد', dt.toLocaleDateString('ar-SA',{weekday:'long',year:'numeric',month:'long',day:'numeric'})+' — '+fmtTime(s.scheduled_at)]:null,
        dt?['⏱ المدة', (s.duration_minutes||60)+' دقيقة']:null,
        s.subject_name?['📚 المادة', s.subject_name]:null,
        s.program_name?['🎓 البرنامج', s.program_name]:null,
        ['🔖 النوع', ts.label],
        ['📊 الحالة', statusLabel],
    ].filter(Boolean);
    let html='<div style="display:flex;flex-direction:column;gap:10px;">';
    rows.forEach(([k,v])=>{
        html+=`<div style="display:flex;align-items:center;gap:10px;padding:8px 12px;background:#f8fafc;border-radius:9px;">
            <span style="font-size:12px;color:#64748b;min-width:90px;">${k}</span>
            <span style="font-size:13px;font-weight:600;color:#1e293b;">${v}</span>
        </div>`;
    });
    if(s.zoom_join_url && s.status!=='completed'){
        html+=`<a href="${s.zoom_join_url}" target="_blank" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:white;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;margin-top:4px;">
            <svg width="16" height="16" fill="white" viewBox="0 0 24 24"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
            انضمام عبر Zoom
        </a>`;
    }
    html+='</div>';
    document.getElementById('smBody').innerHTML=html;
}

function renderCalendar(){
    const ws = weekStart(cur);
    const wend = new Date(ws.getFullYear(), ws.getMonth(), ws.getDate()+6);
    document.getElementById('calTitle').textContent =
        ws.getDate()+' '+MONTH_NAMES[ws.getMonth()]+' — '+wend.getDate()+' '+MONTH_NAMES[wend.getMonth()]+' '+wend.getFullYear();

    const weekDays = [];
    for(let i=0;i<5;i++){ const d=new Date(ws); d.setDate(d.getDate()+i); weekDays.push(d); }

    const cellMap = {};
    weekDays.forEach((d,i)=>{
        sessionsOnDay(d).forEach(s=>{
            const dt=new Date(s.scheduled_at); const mins=dt.getHours()*60+dt.getMinutes();
            const p=periodIndex(mins);
            (cellMap[i+'|'+p]=cellMap[i+'|'+p]||[]).push(s);
        });
    });

    let head = `<th style="padding:10px 6px;background:#0071AA;color:#fff;font-size:12px;font-weight:700;border:1px solid #fff;width:80px;">اليوم<br><span style="font-size:10px;opacity:.85;">الفترة</span></th>`;
    PERIODS.forEach(p=>{
        head+=`<th style="padding:8px 6px;background:#0071AA;color:#fff;font-size:12px;font-weight:700;border:1px solid #fff;line-height:1.6;">
            ${p.name}<br><bdi dir="ltr" style="font-size:11px;font-weight:600;opacity:.9;display:inline-block;unicode-bidi:isolate;">${p.range}</bdi>
        </th>`;
    });

    let body='';
    weekDays.forEach((d,i)=>{
        const isToday=sameDay(d,TODAY);
        let row=`<td style="padding:10px 6px;text-align:center;font-size:13px;font-weight:700;color:#fff;background:${isToday?'#005a88':'#0071AA'};border:1px solid #fff;line-height:1.4;">
            ${DAY_NAMES[i]}<br><span style="font-size:11px;font-weight:500;opacity:.85;">${d.getDate()} ${MONTH_NAMES[d.getMonth()]}</span>
        </td>`;
        PERIODS.forEach((p,pi)=>{
            const items=cellMap[i+'|'+pi]||[];
            const inner=items.map(s=>{
                const ts=typeStyle(s.type);
                const statusBg    = s.status==='completed'?'#dcfce7':s.status==='live'?'#fee2e2':'#eff6ff';
                const statusColor = s.status==='completed'?'#15803d':s.status==='live'?'#dc2626':'#1e3a8a';
                const statusLabel = s.status==='completed'?'مكتملة':s.status==='live'?'● مباشر':'مجدولة';
                const sub = s.subject_name||s.program_name;
                const showSub = sub && !(s.title||'').includes(sub);
                return `<div onclick='openSession(${JSON.stringify(s)})' style="background:#eff6ff;border-right:3px solid #0071AA;border-radius:6px;padding:6px 8px;margin-bottom:4px;line-height:1.35;cursor:pointer;">
                    <div style="font-size:12px;font-weight:700;color:#1e3a8a;">${s.title||sub||'جلسة'}</div>
                    ${showSub?`<div style="font-size:10px;color:#64748b;">${sub}</div>`:''}
                    <div style="display:flex;gap:4px;flex-wrap:wrap;margin-top:4px;">
                        <span style="background:${ts.bg};color:${ts.color};font-size:10px;font-weight:600;padding:1px 6px;border-radius:20px;">${ts.label}</span>
                        <span style="background:${statusBg};color:${statusColor};font-size:10px;font-weight:600;padding:1px 6px;border-radius:20px;">${statusLabel}</span>
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

function calPrev(){ cur.setDate(cur.getDate()-7); renderCalendar(); }
function calNext(){ cur.setDate(cur.getDate()+7); renderCalendar(); }
function calToday(){ cur=new Date(); renderCalendar(); }

renderCalendar();
</script>
@endpush
@endsection
