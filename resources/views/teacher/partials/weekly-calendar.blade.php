{{-- Weekly schedule calendar — shared by teacher dashboard & schedule page. Expects $calSessions. --}}
<style>
@keyframes calLivePulse {
    0%,100% { box-shadow:0 0 0 2px rgba(220,38,38,.25); }
    50%     { box-shadow:0 0 0 4px rgba(220,38,38,.45); }
}
.cal-live-card { animation: calLivePulse 1.6s ease-in-out infinite; }
</style>
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

<script>
const CAL_SESSIONS = @json($calSessions);
const CSRF_TOKEN = '{{ csrf_token() }}';
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

function weekStart(d){ const c=new Date(d); c.setDate(c.getDate()-c.getDay()); c.setHours(0,0,0,0); return c; }

// A session is "live" if explicitly flagged, or if now is within its scheduled window
function isSessionLive(s){
    if(s.status==='live') return true;
    if(s.status==='completed') return false;
    if(!s.scheduled_at) return false;
    const start = new Date(s.scheduled_at);
    const end   = new Date(start.getTime() + (s.duration_minutes||60)*60000);
    const now   = new Date();
    return now>=start && now<=end;
}

function sessionsOnDay(date){
    return CAL_SESSIONS.filter(s=>s.scheduled_at&&sameDay(new Date(s.scheduled_at),date))
        .sort((a,b)=>new Date(a.scheduled_at)-new Date(b.scheduled_at));
}

function openSession(s){
    const dt = s.scheduled_at ? new Date(s.scheduled_at) : null;
    document.getElementById('smTitle').textContent = s.title || ('جلسة #'+s.session_number);
    document.getElementById('sessionModal').style.display='flex';
    const statusLabel = s.status==='completed'?'مكتملة':s.status==='live'?'● مباشر':'';
    const rows = [
        dt?['📅 الموعد', dt.toLocaleDateString('ar-SA',{weekday:'long',year:'numeric',month:'long',day:'numeric'})+' — '+fmtTime(s.scheduled_at)]:null,
        dt?['⏱ المدة', (s.duration_minutes||60)+' دقيقة']:null,
        s.subject_name?['📚 المادة', s.subject_name]:null,
        s.program_name?['🎓 البرنامج', s.program_name]:null,
        s.class_name?['👥 الكلاس', s.class_name]:null,
        statusLabel?['📊 الحالة', statusLabel]:null,
    ].filter(Boolean);
    let html='<div style="display:flex;flex-direction:column;gap:10px;">';
    rows.forEach(([k,v])=>{
        html+=`<div style="display:flex;align-items:center;gap:10px;padding:8px 12px;background:#f8fafc;border-radius:9px;">
            <span style="font-size:12px;color:#64748b;min-width:90px;">${k}</span>
            <span style="font-size:13px;font-weight:600;color:#1e293b;">${v}</span>
        </div>`;
    });
    html+='<div style="display:flex;flex-direction:column;gap:8px;margin-top:8px;">';
    if(s.zoom_start_url && s.status!=='completed'){
        html+=`<a href="${s.zoom_start_url}" target="_blank" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:linear-gradient(135deg,#ef4444,#dc2626);color:white;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;">
            <svg width="16" height="16" fill="white" viewBox="0 0 24 24"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
            ▶ ابدأ الجلسة
        </a>`;
    }
    if(s.zoom_join_url && s.status!=='completed'){
        html+=`<a href="${s.zoom_join_url}" target="_blank" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:white;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;">
            <svg width="16" height="16" fill="white" viewBox="0 0 24 24"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
            انضمام للجلسة
        </a>`;
    }
    // Add / edit student join link (always available unless completed)
    if(s.status!=='completed'){
        const lbl = s.zoom_join_url ? '✓ رابط الطلاب — تعديل' : '+ إضافة رابط الطلاب';
        const btnBg = s.zoom_join_url ? 'background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0;' : 'background:#fffbeb;color:#d97706;border:1px solid #fde68a;';
        html+=`<form method="POST" action="/teacher/sessions/${s.id}/join-url" style="display:flex;flex-direction:column;gap:8px;background:#f8fafc;border-radius:10px;padding:10px;margin-top:2px;">
            <input type="hidden" name="_token" value="${CSRF_TOKEN}">
            <input type="hidden" name="_method" value="PATCH">
            <label style="font-size:11px;font-weight:700;color:#475569;">رابط انضمام الطلاب</label>
            <input type="url" name="zoom_join_url" value="${(s.zoom_join_url||'').replace(/"/g,'&quot;')}" placeholder="https://zoom.us/j/..." style="border:1.5px solid #d1fae5;border-radius:8px;padding:8px 10px;font-size:12px;outline:none;direction:ltr;text-align:left;">
            <button type="submit" style="${btnBg}border-radius:8px;padding:8px;font-size:12px;font-weight:700;cursor:pointer;">${lbl}</button>
        </form>`;
    }
    if(s.subject_id){
        html+=`<a href="/teacher/my-subjects/${s.subject_id}/sessions/${s.id}/attendance" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:#f0fdf4;color:#16a34a;border:1.5px solid #bbf7d0;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            الحضور والغياب
        </a>`;
    } else if(s.program_id){
        html+=`<a href="/teacher/my-courses/${s.program_id}/sessions/${s.id}/attendance" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:#f0fdf4;color:#16a34a;border:1.5px solid #bbf7d0;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            الحضور والغياب
        </a>`;
    }
    html+='</div></div>';
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
                const isLive = isSessionLive(s);
                const statusBg    = s.status==='completed'?'#dcfce7':isLive?'#fee2e2':'';
                const statusColor = s.status==='completed'?'#15803d':isLive?'#dc2626':'';
                const statusLabel = s.status==='completed'?'مكتملة':isLive?'● مباشر الآن':'';
                const hasLink = !!s.zoom_join_url;
                const sub = s.subject_name||s.program_name;
                const showSub = sub && !(s.title||'').includes(sub);
                // Live cards get a red animated highlight; others stay blue
                const cardStyle = isLive
                    ? 'background:linear-gradient(135deg,#fef2f2,#fee2e2);border-right:4px solid #dc2626;box-shadow:0 0 0 2px rgba(220,38,38,.25);'
                    : 'background:#eff6ff;border-right:3px solid #0071AA;';
                return `<div onclick='openSession(${JSON.stringify(s)})' class="${isLive?'cal-live-card':''}" style="${cardStyle}border-radius:6px;padding:6px 8px;margin-bottom:4px;line-height:1.35;cursor:pointer;position:relative;">
                    <div style="font-size:12px;font-weight:700;color:${isLive?'#991b1b':'#1e3a8a'};">${s.title||sub||'جلسة'}</div>
                    ${showSub?`<div style="font-size:10px;color:#64748b;">${sub}</div>`:''}
                    <div style="display:flex;gap:4px;flex-wrap:wrap;margin-top:4px;">
                        ${s.class_name?`<span style="background:#e0f2fe;color:#0071AA;font-size:10px;font-weight:600;padding:1px 6px;border-radius:20px;">${s.class_name}</span>`:''}
                        ${statusLabel?`<span style="background:${statusBg};color:${statusColor};font-size:10px;font-weight:700;padding:1px 6px;border-radius:20px;">${statusLabel}</span>`:''}
                        ${!hasLink?`<span style="background:#fffbeb;color:#d97706;font-size:10px;font-weight:600;padding:1px 6px;border-radius:20px;">+ رابط</span>`:''}
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
