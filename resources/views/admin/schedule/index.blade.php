@extends('layouts.dashboard')
@section('title', 'إدارة الجدول')

@section('content')
<div style="direction:rtl;font-family:'Segoe UI',sans-serif;">

@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-right:4px solid #22c55e;border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span style="color:#15803d;font-size:14px;font-weight:500;">{{ session('success') }}</span>
</div>
@endif

{{-- Hero --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 55%,#0071AA 100%);border-radius:20px;padding:28px;margin-bottom:24px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-60px;left:-60px;width:220px;height:220px;background:rgba(255,255,255,.05);border-radius:50%;pointer-events:none;"></div>
    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:50px;height:50px;background:linear-gradient(135deg,#0071AA,#005a88);border-radius:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 16px rgba(0,113,170,.4);">
                <svg width="24" height="24" fill="white" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zM7 12h5v5H7z"/></svg>
            </div>
            <div>
                <p style="color:rgba(255,255,255,0.5);font-size:12px;margin:0 0 2px;">{{ now()->translatedFormat('l، d F Y') }}</p>
                <h1 style="color:white;font-size:20px;font-weight:700;margin:0;">إدارة الجدول الدراسي</h1>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            @foreach([
                [$stats['total'],     'الكل',    'rgba(255,255,255,0.75)'],
                [$stats['upcoming'],  'قادمة',   '#fde68a'],
                [$stats['live'],      'مباشر',   '#fca5a5'],
                [$stats['completed'], 'مكتملة',  '#86efac'],
            ] as [$v,$l,$c])
            <div style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.12);border-radius:12px;padding:8px 16px;text-align:center;min-width:64px;">
                <div style="font-size:20px;font-weight:700;color:{{ $c }};line-height:1;">{{ $v }}</div>
                <div style="font-size:11px;color:rgba(255,255,255,0.55);margin-top:2px;">{{ $l }}</div>
            </div>
            @endforeach
            <a href="{{ route('admin.classes.index') }}" style="display:flex;align-items:center;gap:8px;padding:10px 18px;background:rgba(255,255,255,.15);color:white;border:1px solid rgba(255,255,255,.25);border-radius:12px;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                إنشاء الجلسات من المجموعات
            </a>
        </div>
    </div>
</div>

{{-- Weekly Calendar --}}
<div style="background:white;border-radius:18px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);">
    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:16px 20px;border-bottom:1px solid #f1f5f9;background:#fafafa;">
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-size:15px;font-weight:700;color:#111827;">الجدول الأسبوعي</span>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <button type="button" onclick="calPrev()" style="width:36px;height:36px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;color:#374151;font-size:18px;">&#8249;</button>
            <h3 id="calTitle" style="font-size:16px;font-weight:700;color:#111827;margin:0;min-width:200px;text-align:center;"></h3>
            <button type="button" onclick="calNext()" style="width:36px;height:36px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;color:#374151;font-size:18px;">&#8250;</button>
            <button type="button" onclick="calToday()" style="padding:8px 14px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;color:#0071AA;font-size:12px;font-weight:700;">اليوم</button>
        </div>
    </div>
    <div id="calBody" style="overflow-x:auto;"></div>
</div>


</div>

{{-- Assign-students modal --}}
<div id="assignModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);align-items:center;justify-content:center;">
    <div style="background:white;border-radius:20px;width:100%;max-width:500px;max-height:90vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,.25);">
        <div style="padding:20px 24px;background:linear-gradient(135deg,#0071AA,#005a88);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <h3 style="color:white;font-size:15px;font-weight:700;margin:0;">إسناد الطلاب للجلسة</h3>
                <p id="assignSessionTitle" style="color:rgba(255,255,255,.7);font-size:12px;margin:3px 0 0;"></p>
            </div>
            <button onclick="closeAssign()" style="width:32px;height:32px;background:rgba(255,255,255,.2);border:none;border-radius:8px;cursor:pointer;color:white;font-size:18px;">×</button>
        </div>

        <div style="padding:16px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:10px;">
            <input id="studentSearch" type="text" placeholder="بحث عن طالب..." oninput="filterStudents()"
                   style="flex:1;border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 14px;font-size:13px;outline:none;font-family:inherit;"
                   onfocus="this.style.borderColor='#0071AA'" onblur="this.style.borderColor='#e5e7eb'">
            <button onclick="selectAll()" style="padding:9px 14px;border-radius:10px;background:#eff6ff;color:#2563eb;border:none;cursor:pointer;font-size:12px;font-weight:600;white-space:nowrap;">تحديد الكل</button>
        </div>

        <div id="studentList" style="flex:1;overflow-y:auto;padding:12px 24px;display:flex;flex-direction:column;gap:6px;min-height:120px;max-height:340px;"></div>

        <div style="padding:16px 24px;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;gap:10px;">
            <span id="selectedCount" style="font-size:13px;color:#6b7280;">0 متدرب محدد</span>
            <div style="display:flex;gap:8px;">
                <button onclick="closeAssign()" style="padding:10px 18px;border-radius:10px;background:#f3f4f6;color:#374151;border:none;cursor:pointer;font-size:13px;font-weight:600;">إلغاء</button>
                <button onclick="submitAssign()" style="padding:10px 20px;border-radius:10px;background:linear-gradient(135deg,#0071AA,#005a88);color:white;border:none;cursor:pointer;font-size:13px;font-weight:700;box-shadow:0 3px 10px rgba(0,113,170,.35);">حفظ الإسناد</button>
            </div>
        </div>
    </div>
</div>

<style>
.student-row:hover { background:#f0f9ff !important; }
</style>

<script>
const sessions = @json($calSessions);
const CSRF     = '{{ csrf_token() }}';

const DAY_NAMES   = ['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
const MONTH_NAMES = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
const TODAY = new Date();
let cur = new Date();

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

function sessionsOnDay(date){
    return sessions.filter(s=>{
        if(!s.scheduled_at) return false;
        return sameDay(new Date(s.scheduled_at), date);
    }).sort((a,b)=>new Date(a.scheduled_at)-new Date(b.scheduled_at));
}

function typeStyle(type){
    if(type==='live_zoom')      return {bg:'#dbeafe',color:'#1d4ed8',label:'Zoom'};
    if(type==='in_person')      return {bg:'#dcfce7',color:'#15803d',label:'حضوري'};
    if(type==='recorded_video') return {bg:'#fce7f3',color:'#be185d',label:'مسجّل'};
    return {bg:'#f3f4f6',color:'#4b5563',label:type||'—'};
}

function weekStart(d){ const c=new Date(d); c.setDate(c.getDate()-c.getDay()); c.setHours(0,0,0,0); return c; }

function renderCalendar(){
    const ws = weekStart(cur);
    const wend = new Date(ws.getFullYear(), ws.getMonth(), ws.getDate()+6);
    document.getElementById('calTitle').textContent =
        ws.getDate()+' '+MONTH_NAMES[ws.getMonth()]+' — '+wend.getDate()+' '+MONTH_NAMES[wend.getMonth()]+' '+wend.getFullYear();

    // 5 academic days Sun→Thu
    const weekDays = [];
    for(let i=0;i<5;i++){ const d=new Date(ws); d.setDate(d.getDate()+i); weekDays.push(d); }

    // Bucket sessions
    const cellMap = {};
    weekDays.forEach((d,i)=>{
        sessionsOnDay(d).forEach(s=>{
            const dt=new Date(s.scheduled_at); const mins=dt.getHours()*60+dt.getMinutes();
            const p=periodIndex(mins);
            (cellMap[i+'|'+p] = cellMap[i+'|'+p]||[]).push(s);
        });
    });

    // Header row
    let head = `<th style="padding:10px 6px;background:#0071AA;color:#fff;font-size:12px;font-weight:700;border:1px solid #fff;width:80px;">اليوم<br><span style="font-size:10px;opacity:.85;">الفترة</span></th>`;
    PERIODS.forEach(p=>{
        head += `<th style="padding:8px 6px;background:#0071AA;color:#fff;font-size:12px;font-weight:700;border:1px solid #fff;line-height:1.6;">
            ${p.name}<br><bdi dir="ltr" style="font-size:11px;font-weight:600;opacity:.9;display:inline-block;unicode-bidi:isolate;">${p.range}</bdi>
        </th>`;
    });

    // Body rows
    let body='';
    weekDays.forEach((d,i)=>{
        const isToday = sameDay(d, TODAY);
        let row = `<td style="padding:10px 6px;text-align:center;font-size:13px;font-weight:700;color:#fff;background:${isToday?'#005a88':'#0071AA'};border:1px solid #fff;line-height:1.4;">
            ${DAY_NAMES[i]}<br><span style="font-size:11px;font-weight:500;opacity:.85;">${d.getDate()} ${MONTH_NAMES[d.getMonth()]}</span>
        </td>`;
        PERIODS.forEach((p,pi)=>{
            const items = cellMap[i+'|'+pi]||[];
            const inner = items.map(s=>{
                const ts = typeStyle(s.type);
                const statusBg    = s.status==='completed'?'#dcfce7':s.status==='live'?'#fee2e2':'#eff6ff';
                const statusColor = s.status==='completed'?'#15803d':s.status==='live'?'#dc2626':'#1e3a8a';
                const statusLabel = s.status==='completed'?'مكتملة':s.status==='live'?'● مباشر':'مجدولة';
                const showSub = s.subject_name && !(s.title||'').includes(s.subject_name);
                return `<div style="background:#eff6ff;border-right:3px solid #0071AA;border-radius:6px;padding:6px 8px;margin-bottom:4px;line-height:1.35;">
                    <div style="font-size:12px;font-weight:700;color:#1e3a8a;">${s.title||s.subject_name||'جلسة'}</div>
                    ${showSub?`<div style="font-size:10px;color:#64748b;">${s.subject_name}</div>`:''}
                    ${s.teacher_name?`<div style="font-size:10px;color:#64748b;">👤 ${s.teacher_name}</div>`:''}
                    <div style="display:flex;gap:4px;flex-wrap:wrap;margin-top:4px;">
                        <span style="background:${ts.bg};color:${ts.color};font-size:10px;font-weight:600;padding:1px 6px;border-radius:20px;">${ts.label}</span>
                        <span style="background:${statusBg};color:${statusColor};font-size:10px;font-weight:600;padding:1px 6px;border-radius:20px;">${statusLabel}</span>
                        <span style="background:#f3f4f6;color:#6b7280;font-size:10px;padding:1px 6px;border-radius:20px;">👥 ${s.attendance_count}</span>
                    </div>
                </div>`;
            }).join('');
            row += `<td style="min-height:80px;padding:5px;vertical-align:top;border:1px solid #d6e4f0;${isToday?'background:#f8fdff;':''}">${inner}</td>`;
        });
        body += `<tr>${row}</tr>`;
    });

    document.getElementById('calBody').innerHTML = `
        <table style="width:100%;min-width:1000px;border-collapse:collapse;table-layout:fixed;">
            <thead><tr>${head}</tr></thead>
            <tbody>${body}</tbody>
        </table>`;
}

function calPrev(){ cur.setDate(cur.getDate()-7); renderCalendar(); }
function calNext(){ cur.setDate(cur.getDate()+7); renderCalendar(); }
function calToday(){ cur=new Date(); renderCalendar(); }

// ── Assign modal ──
let currentSessionId = null;
let allStudents      = [];

function openAssign(sessionId) {
    currentSessionId = sessionId;
    const sessionData = sessions.find(x => x.id === sessionId);
    document.getElementById('assignSessionTitle').textContent = sessionData ? sessionData.title : 'الجلسة';
    document.getElementById('studentList').innerHTML = '<div style="text-align:center;padding:24px;color:#9ca3af;">جاري التحميل...</div>';
    document.getElementById('studentSearch').value = '';
    document.getElementById('assignModal').style.display = 'flex';

    fetch(`/admin/schedule/sessions/${sessionId}/students`, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(data => {
        allStudents = data.students || [];
        renderStudents(allStudents);
    });
}

function renderStudents(list) {
    const el = document.getElementById('studentList');
    if (!list.length) {
        el.innerHTML = '<div style="text-align:center;padding:24px;color:#9ca3af;">لا يوجد طلاب مسجّلون في هذا المقرر</div>';
        return;
    }
    el.innerHTML = list.map(s => `
        <label class="student-row" style="display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;cursor:pointer;border:1px solid #f1f5f9;background:white;">
            <input type="checkbox" value="${s.id}" ${s.assigned ? 'checked' : ''} onchange="updateCount()"
                   style="width:16px;height:16px;accent-color:#0071AA;cursor:pointer;flex-shrink:0;">
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:600;color:#111827;">${s.name}</div>
                <div style="font-size:11px;color:#9ca3af;">${s.email}</div>
            </div>
            ${s.assigned ? '<span style="font-size:10px;background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:20px;font-weight:600;">محدد</span>' : ''}
        </label>
    `).join('');
    updateCount();
}

function filterStudents() {
    const q = document.getElementById('studentSearch').value.toLowerCase();
    renderStudents(allStudents.filter(s => s.name.toLowerCase().includes(q) || s.email.toLowerCase().includes(q)));
}

function selectAll() {
    document.querySelectorAll('#studentList input[type=checkbox]').forEach(cb => cb.checked = true);
    updateCount();
}

function updateCount() {
    const n = document.querySelectorAll('#studentList input[type=checkbox]:checked').length;
    document.getElementById('selectedCount').textContent = n + ' متدرب محدد';
}

function closeAssign() {
    document.getElementById('assignModal').style.display = 'none';
    currentSessionId = null;
}

function submitAssign() {
    const ids = [...document.querySelectorAll('#studentList input[type=checkbox]:checked')].map(cb => cb.value);
    if (!ids.length) { alert('اختر طالباً واحداً على الأقل'); return; }

    fetch(`/admin/schedule/sessions/${currentSessionId}/assign`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ student_ids: ids })
    })
    .then(r => r.json())
    .then(data => {
        closeAssign();
        // Update attendance_count in local data
        const s = sessions.find(s => s.id === currentSessionId);
        if (s) s.attendance_count = (s.attendance_count || 0) + (data.inserted || 0);
        // Show brief success toast
        const toast = document.createElement('div');
        toast.textContent = `✓ تم إسناد ${data.inserted} متدرب جديد للجلسة`;
        toast.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:99999;background:#15803d;color:white;padding:12px 20px;border-radius:12px;font-size:13px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.2);';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3500);
    });
}

renderCalendar();

// ════════════════════════════════════
// Generate Schedule Modal
// ══════════════════════════════════════
let allPrograms = [], allSubjects = [];

async function openGenerateModal() {
    document.getElementById('genModal').style.display = 'flex';
    // reset
    document.getElementById('genEntitySearch').value = '';
    document.getElementById('genEntityId').value = '';
    document.getElementById('genEntityList').innerHTML = '';
    document.getElementById('genEntityDropdown').style.display = 'none';
    document.getElementById('genClassRow').style.display = 'none';
    document.getElementById('genDatesRow').style.display = 'none';
    document.getElementById('genResult').style.display = 'none';

    if (!allPrograms.length) {
        const r = await fetch('/admin/schedule/programs', { headers: { Accept: 'application/json' } });
        const d = await r.json();
        allPrograms = d.programs;
        allSubjects = d.subjects;
    }
    renderEntityList('program', '');
}

function closeGenerateModal() {
    document.getElementById('genModal').style.display = 'none';
}

// Close dropdown when clicking outside
document.addEventListener('click', e => {
    const wrap = document.getElementById('genEntitySearch')?.closest('div');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('genEntityDropdown').style.display = 'none';
    }
});

function switchEntityType(type) {
    document.getElementById('genEntityType').value = type;
    document.getElementById('tabProgram').style.background = type==='program' ? '#7c3aed' : 'transparent';
    document.getElementById('tabProgram').style.color      = type==='program' ? 'white' : '#64748b';
    document.getElementById('tabSubject').style.background = type==='subject' ? '#7c3aed' : 'transparent';
    document.getElementById('tabSubject').style.color      = type==='subject' ? 'white' : '#64748b';
    // update label
    document.getElementById('genEntityLabel').textContent =
        type === 'program' ? 'اختر البرنامج / الدورة *' : 'اختر المادة / المقرر *';
    // clear search and selection
    document.getElementById('genEntitySearch').value = '';
    document.getElementById('genEntityId').value = '';
    document.getElementById('genEntityDropdown').style.display = 'none';
    document.getElementById('genClassRow').style.display = 'none';
    document.getElementById('genDatesRow').style.display = 'none';
    document.getElementById('genClassId').innerHTML = '<option value="">— اختر المجموعة —</option>';
    renderEntityList(type, '');
}

function filterEntityList() {
    const type = document.getElementById('genEntityType').value;
    const q    = document.getElementById('genEntitySearch').value;
    document.getElementById('genEntityDropdown').style.display = 'block';
    renderEntityList(type, q);
}

function renderEntityList(type, q) {
    const list = (type === 'program' ? allPrograms : allSubjects)
        .filter(e => !q || e.name_ar.includes(q));
    const container = document.getElementById('genEntityList');
    if (!list.length) {
        container.innerHTML = '<div style="padding:10px 14px;font-size:12px;color:#9ca3af;">لا توجد نتائج</div>';
        return;
    }
    container.innerHTML = list.map(e => `
        <div onclick="selectEntity(${e.id}, '${e.name_ar.replace(/'/g,"\\'")}', ${e.program_id ?? 'null'}, ${e.duration_months ?? 'null'})"
             style="padding:9px 14px;font-size:13px;cursor:pointer;border-bottom:1px solid #f1f5f9;color:#1e293b;"
             onmouseover="this.style.background='#f8faff'" onmouseout="this.style.background='white'">
            ${e.name_ar}${e.type ? ` <span style="font-size:11px;color:#9ca3af;">(${e.type})</span>` : ''}
            ${e.program_name ? ` <span style="font-size:11px;color:#a855f7;">— ${e.program_name}</span>` : ''}
        </div>
    `).join('');
}

async function selectEntity(id, name, programId, durationMonths) {
    document.getElementById('genEntitySearch').value = name;
    document.getElementById('genEntityId').value = id;
    document.getElementById('genEntityDropdown').style.display = 'none';

    const type = document.getElementById('genEntityType').value;
    const pid  = type === 'subject' ? programId : id;

    if (!pid) { alert('هذه المادة غير مرتبطة ببرنامج'); return; }

    // Reset class selection
    document.getElementById('genClassId').value = '';
    document.getElementById('genDatesRow').style.display = 'none';

    // Show loading
    const cards = document.getElementById('genClassCards');
    const empty = document.getElementById('genClassEmpty');
    cards.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:20px;color:#9ca3af;font-size:13px;">جار التحميل...</div>`;
    document.getElementById('genClassRow').style.display = 'block';
    empty.style.display = 'none';

    const r = await fetch(`/admin/schedule/classes?program_id=${pid}`, { headers: { Accept: 'application/json' } });
    const d = await r.json();

    document.getElementById('genClassCount').textContent = d.classes.length ? `(${d.classes.length})` : '';

    if (!d.classes.length) {
        cards.innerHTML = '';
        empty.style.display = 'block';
        return;
    }

    empty.style.display = 'none';
    cards.innerHTML = d.classes.map(c => {
        const start = c.start_date ? c.start_date.slice(0,10) : '—';
        const end   = c.end_date   ? c.end_date.slice(0,10)   : '—';
        const teacher = c.teacher?.name ?? 'بدون مدرب';
        return `
        <div onclick="selectClass(${c.id}, '${(c.start_date||'').slice(0,10)}', '${(c.end_date||'').slice(0,10)}')"
             id="genClass-${c.id}"
             style="border:2px solid #e2e8f0;border-radius:14px;padding:14px;cursor:pointer;transition:all .15s;background:white;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                <span style="font-size:14px;font-weight:700;color:#1e293b;">${c.name}</span>
                <span style="background:#f3f4f6;color:#374151;font-size:11px;font-weight:700;padding:3px 8px;border-radius:20px;">${c.students_count} طالب</span>
            </div>
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                <span style="font-size:12px;color:#6b7280;">${teacher}</span>
            </div>
            <div style="display:flex;align-items:center;gap:6px;">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                <span style="font-size:11px;color:#9ca3af;">${start} → ${end}</span>
            </div>
        </div>`;
    }).join('');

    // Auto-fill dates from program duration_months
    if (type === 'program' && durationMonths) {
        const start = new Date();
        const end   = new Date(start);
        end.setMonth(end.getMonth() + durationMonths);
        document.getElementById('genStartDate').value = start.toISOString().slice(0,10);
        document.getElementById('genEndDate').value   = end.toISOString().slice(0,10);
    }
}

function selectClass(id, startDate, endDate) {
    // Deselect all cards
    document.querySelectorAll('[id^="genClass-"]').forEach(el => {
        el.style.border      = '2px solid #e2e8f0';
        el.style.background  = 'white';
        el.style.boxShadow   = 'none';
    });
    // Highlight selected
    const card = document.getElementById('genClass-' + id);
    card.style.border     = '2px solid #7c3aed';
    card.style.background = '#faf5ff';
    card.style.boxShadow  = '0 0 0 4px rgba(124,58,237,.1)';

    document.getElementById('genClassId').value = id;

    // Auto-fill dates from class dates
    if (startDate) document.getElementById('genStartDate').value = startDate;
    if (endDate)   document.getElementById('genEndDate').value   = endDate;

    document.getElementById('genDatesRow').style.display = 'flex';
}


async function submitGenerate() {
    const btn = document.getElementById('genSubmitBtn');
    btn.disabled = true;
    btn.textContent = 'جار الإنشاء...';

    const days = [...document.querySelectorAll('#genDays input:checked')].map(c => parseInt(c.value));
    if (!days.length) { alert('اختر يوماً واحداً على الأقل'); btn.disabled=false; btn.textContent='إنشاء الجدول'; return; }

    const payload = {
        entity_type:      document.getElementById('genEntityType').value,
        entity_id:        document.getElementById('genEntityId').value,
        class_id:         document.getElementById('genClassId').value,
        days,
        time:             document.getElementById('genTime').value,
        duration_minutes: document.getElementById('genDuration').value,
        start_date:       document.getElementById('genStartDate').value,
        end_date:         document.getElementById('genEndDate').value,
        session_type:     document.getElementById('genSessionType').value,
    };

    if (!payload.entity_id || !payload.class_id || !payload.start_date || !payload.end_date || !payload.time) {
        alert('يرجى تعبئة جميع الحقول المطلوبة'); btn.disabled=false; btn.textContent='إنشاء الجدول'; return;
    }

    const r = await fetch('/admin/schedule/generate', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(payload),
    });
    const d = await r.json();

    btn.disabled = false;
    btn.textContent = 'إنشاء الجدول';

    const res = document.getElementById('genResult');
    res.style.display = 'block';
    if (d.success) {
        res.style.background = '#f0fdf4';
        res.style.border = '1px solid #bbf7d0';
        res.style.color = '#15803d';
        res.innerHTML = `✓ ${d.message}`;
        // Reload page after 2s to reflect new sessions
        setTimeout(() => location.reload(), 2000);
    } else {
        res.style.background = '#fff1f2';
        res.style.border = '1px solid #fecaca';
        res.style.color = '#dc2626';
        res.innerHTML = `✗ ${d.message}`;
    }
}
</script>

{{-- Generate Schedule Modal --}}
<div id="genModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.55);align-items:center;justify-content:center;padding:12px;">
<div style="background:white;border-radius:20px;width:100%;max-width:860px;height:90vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,.3);">

    {{-- Header --}}
    <div style="padding:20px 24px;background:linear-gradient(135deg,#4c1d95,#7c3aed);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <div>
            <h3 style="color:white;font-size:15px;font-weight:700;margin:0;">إنشاء جدول أسبوعي متكرر</h3>
            <p style="color:rgba(255,255,255,.7);font-size:12px;margin:3px 0 0;">تحديد الدورة + المجموعة + الأيام → ينشئ الجلسات تلقائياً</p>
        </div>
        <button onclick="closeGenerateModal()" style="width:32px;height:32px;background:rgba(255,255,255,.2);border:none;border-radius:8px;cursor:pointer;color:white;font-size:18px;">×</button>
    </div>

    {{-- Body --}}
    <div style="overflow-y:auto;flex:1;padding:20px 24px;display:flex;flex-direction:column;gap:14px;">

        {{-- Entity type tabs --}}
        <div>
            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">نوع الجدول</label>
            <div style="display:flex;background:#f1f5f9;border-radius:10px;padding:3px;gap:2px;width:fit-content;">
                <button id="tabProgram" onclick="switchEntityType('program')" style="padding:7px 18px;border-radius:8px;border:none;font-size:12px;font-weight:700;cursor:pointer;background:#7c3aed;color:white;">برنامج / دورة</button>
                <button id="tabSubject" onclick="switchEntityType('subject')" style="padding:7px 18px;border-radius:8px;border:none;font-size:12px;font-weight:700;cursor:pointer;background:transparent;color:#64748b;">مقرر / مادة</button>
            </div>
            <input type="hidden" id="genEntityType" value="program">
        </div>

        {{-- Entity search + select --}}
        <div>
            <label id="genEntityLabel" style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">اختر البرنامج / الدورة *</label>
            <div style="position:relative;">
                <input id="genEntitySearch" type="text" placeholder="ابحث..." oninput="filterEntityList()" autocomplete="off"
                    style="width:100%;padding:9px 12px 9px 36px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;"
                    onfocus="document.getElementById('genEntityDropdown').style.display='block'"
                >
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="hidden" id="genEntityId">
                <div id="genEntityDropdown" style="display:none;position:absolute;top:100%;right:0;left:0;z-index:999;background:white;border:1.5px solid #e2e8f0;border-top:none;border-radius:0 0 9px 9px;max-height:200px;overflow-y:auto;box-shadow:0 8px 24px rgba(0,0,0,.1);">
                    <div id="genEntityList"></div>
                </div>
            </div>
        </div>

        {{-- Class cards --}}
        <div id="genClassRow" style="display:none;">
            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">المجموعة * <span id="genClassCount" style="color:#9ca3af;font-weight:400;"></span></label>
            <input type="hidden" id="genClassId">
            <div id="genClassCards" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;"></div>
            <p id="genClassEmpty" style="display:none;color:#9ca3af;font-size:13px;text-align:center;padding:16px;background:#f9fafb;border-radius:10px;">لا توجد مجموعات لهذا البرنامج — أنشئ مجموعة أولاً</p>
        </div>

        {{-- Dates & settings (shown after class selected) --}}
        <div id="genDatesRow" style="display:none;flex-direction:column;gap:14px;">

            {{-- Days of week --}}
            <div>
                <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:8px;">أيام الأسبوع *</label>
                <div id="genDays" style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach([0=>'الأحد',1=>'الاثنين',2=>'الثلاثاء',3=>'الأربعاء',4=>'الخميس',5=>'الجمعة',6=>'السبت'] as $val=>$name)
                    <label style="display:flex;align-items:center;gap:5px;padding:6px 12px;border:1.5px solid #e2e8f0;border-radius:8px;cursor:pointer;font-size:12px;font-weight:600;color:#374151;user-select:none;">
                        <input type="checkbox" value="{{ $val }}" style="accent-color:#7c3aed;">
                        {{ $name }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">وقت الجلسة *</label>
                    <input id="genTime" type="time" value="09:00" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">المدة (دقيقة) *</label>
                    <input id="genDuration" type="number" value="60" min="15" max="480" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">من تاريخ *</label>
                    <input id="genStartDate" type="date" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
                </div>
                <div>
                    <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:5px;">إلى تاريخ *</label>
                    <input id="genEndDate" type="date" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
                </div>
            </div>

            <input type="hidden" id="genSessionType" value="live_zoom">

            {{-- Result --}}
            <div id="genResult" style="display:none;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;"></div>
        </div>

    </div>

    {{-- Footer --}}
    <div style="padding:14px 24px;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end;gap:8px;flex-shrink:0;">
        <button onclick="closeGenerateModal()" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
        <button id="genSubmitBtn" onclick="submitGenerate()" style="padding:9px 20px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#7c3aed,#a855f7);border:none;border-radius:10px;cursor:pointer;box-shadow:0 4px 12px rgba(124,58,237,.3);">إنشاء الجدول</button>
    </div>
</div>
</div>

@endsection
