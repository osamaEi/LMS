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
        </div>
    </div>
</div>

{{-- Calendar card --}}
<div style="background:white;border-radius:18px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9;background:#fafafa;">
        <button onclick="prevMonth()" style="width:36px;height:36px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#374151;font-size:16px;">&#8249;</button>
        <div style="text-align:center;">
            <h2 id="calTitle" style="font-size:18px;font-weight:700;color:#111827;margin:0;"></h2>
            <button onclick="goToToday()" style="font-size:11px;color:#0071AA;background:none;border:none;cursor:pointer;padding:2px 8px;margin-top:2px;font-family:inherit;">اليوم</button>
        </div>
        <button onclick="nextMonth()" style="width:36px;height:36px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#374151;font-size:16px;">&#8250;</button>
    </div>
    <div id="calHeaders" style="display:grid;grid-template-columns:repeat(7,1fr);"></div>
    <div id="calGrid"    style="display:grid;grid-template-columns:repeat(7,1fr);"></div>
</div>

{{-- Day detail panel --}}
<div id="dayPanel" style="display:none;background:white;border-radius:16px;border:1px solid #e5e7eb;margin-top:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);overflow:hidden;">
    <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;background:#fafafa;">
        <h3 id="dayPanelTitle" style="font-size:15px;font-weight:700;color:#111827;margin:0;"></h3>
        <button onclick="document.getElementById('dayPanel').style.display='none'" style="width:28px;height:28px;border-radius:7px;border:none;background:#f1f5f9;cursor:pointer;color:#6b7280;font-size:16px;display:flex;align-items:center;justify-content:center;">×</button>
    </div>
    <div id="dayPanelContent" style="padding:16px;display:flex;flex-direction:column;gap:10px;"></div>
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
            <span id="selectedCount" style="font-size:13px;color:#6b7280;">0 طالب محدد</span>
            <div style="display:flex;gap:8px;">
                <button onclick="closeAssign()" style="padding:10px 18px;border-radius:10px;background:#f3f4f6;color:#374151;border:none;cursor:pointer;font-size:13px;font-weight:600;">إلغاء</button>
                <button onclick="submitAssign()" style="padding:10px 20px;border-radius:10px;background:linear-gradient(135deg,#0071AA,#005a88);color:white;border:none;cursor:pointer;font-size:13px;font-weight:700;box-shadow:0 3px 10px rgba(0,113,170,.35);">حفظ الإسناد</button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes livePulse { 0%,100%{opacity:1} 50%{opacity:.35} }
.cal-cell:hover { background:#f8faff !important; }
.cal-event:hover { opacity:.82; }
.student-row:hover { background:#f0f9ff !important; }
</style>

<script>
const sessions = @json($calSessions);
const CSRF     = '{{ csrf_token() }}';

const DAY_NAMES   = ['أحد','اثن','ثلا','أرب','خمس','جمع','سبت'];
const MONTH_NAMES = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
const TODAY = new Date();
let curYear  = TODAY.getFullYear();
let curMonth = TODAY.getMonth();

function sessionsOnDay(y, m, d) {
    return sessions.filter(s => {
        if (!s.scheduled_at) return false;
        const dt = new Date(s.scheduled_at);
        return dt.getFullYear() === y && dt.getMonth() === m && dt.getDate() === d;
    }).sort((a, b) => new Date(a.scheduled_at) - new Date(b.scheduled_at));
}

function typeStyle(type) {
    if (type === 'live_zoom')      return { bg: '#dbeafe', color: '#1d4ed8', label: 'Zoom' };
    if (type === 'in_person')      return { bg: '#dcfce7', color: '#15803d', label: 'حضوري' };
    if (type === 'recorded_video') return { bg: '#fce7f3', color: '#be185d', label: 'مسجّل' };
    return { bg: '#f3f4f6', color: '#4b5563', label: type || '—' };
}

function fmt12(iso) {
    const d = new Date(iso);
    const h = d.getHours(), m = d.getMinutes();
    const hh = h % 12 || 12;
    const mm = String(m).padStart(2,'0');
    return hh + ':' + mm + (h < 12 ? ' ص' : ' م');
}

function renderCalendar() {
    document.getElementById('calTitle').textContent = MONTH_NAMES[curMonth] + ' ' + curYear;

    document.getElementById('calHeaders').innerHTML = DAY_NAMES.map(d =>
        `<div style="padding:10px 4px;text-align:center;font-size:11px;font-weight:700;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb;">${d}</div>`
    ).join('');

    const firstDow    = new Date(curYear, curMonth, 1).getDay();
    const daysInMonth = new Date(curYear, curMonth + 1, 0).getDate();
    const prevDays    = new Date(curYear, curMonth, 0).getDate();

    let cells = [];
    for (let i = firstDow - 1; i >= 0; i--)
        cells.push({ d: prevDays - i, m: curMonth - 1, y: curYear, cur: false });
    for (let d = 1; d <= daysInMonth; d++)
        cells.push({ d, m: curMonth, y: curYear, cur: true });
    let fill = 42 - cells.length;
    for (let d = 1; d <= fill; d++)
        cells.push({ d, m: curMonth + 1, y: curYear, cur: false });

    document.getElementById('calGrid').innerHTML = cells.map(cell => {
        const isToday = cell.cur
            && cell.y === TODAY.getFullYear()
            && cell.m === TODAY.getMonth()
            && cell.d === TODAY.getDate();

        const daySes = cell.cur ? sessionsOnDay(cell.y, cell.m, cell.d) : [];
        const visible = daySes.slice(0, 3);
        const more    = daySes.length - visible.length;

        const chips = visible.map(s => {
            const ts   = typeStyle(s.type);
            const time = fmt12(s.scheduled_at);
            const label = (s.title || s.subject_name || s.program_name || ts.label).substring(0, 18);
            return `<div class="cal-event"
                         onclick="event.stopPropagation();showDay(${cell.y},${cell.m},${cell.d})"
                         style="background:${ts.bg};color:${ts.color};border-right:2px solid ${ts.color};font-size:10px;font-weight:600;padding:2px 5px;border-radius:3px;margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;cursor:pointer;">
                        ${time} ${label}
                    </div>`;
        }).join('');

        const moreEl = more > 0
            ? `<div style="font-size:10px;color:#6b7280;text-align:center;cursor:pointer;" onclick="showDay(${cell.y},${cell.m},${cell.d})">+${more} أخرى</div>`
            : '';

        const cellBg = isToday ? '#eff6ff' : (cell.cur ? 'white' : '#fafafa');
        const numStyle = isToday
            ? `display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;background:#0071AA;color:white;border-radius:50%;font-weight:700;font-size:13px;`
            : `font-size:13px;font-weight:${cell.cur?'500':'400'};color:${cell.cur?'#374151':'#cbd5e1'};`;

        return `<div class="cal-cell" onclick="showDay(${cell.y},${cell.m},${cell.d})"
                     style="min-height:96px;padding:6px;border-left:1px solid #f1f5f9;border-bottom:1px solid #f1f5f9;background:${cellBg};cursor:pointer;">
                    <div style="margin-bottom:4px;text-align:left;">
                        <span style="${numStyle}">${cell.d}</span>
                    </div>
                    ${chips}${moreEl}
                </div>`;
    }).join('');
}

function showDay(y, m, d) {
    const daySes = sessionsOnDay(y, m, d);
    const panel  = document.getElementById('dayPanel');
    if (!daySes.length) { panel.style.display = 'none'; return; }

    const dateLabel = new Intl.DateTimeFormat('ar-SA', { weekday:'long', year:'numeric', month:'long', day:'numeric' })
        .format(new Date(y, m, d));

    document.getElementById('dayPanelTitle').textContent = dateLabel + ' — ' + daySes.length + ' جلسة';

    document.getElementById('dayPanelContent').innerHTML = daySes.map(s => {
        const ts   = typeStyle(s.type);
        const time = fmt12(s.scheduled_at);
        const statusBg    = s.status === 'completed' ? '#dcfce7' : s.status === 'live' ? '#fee2e2' : '#dbeafe';
        const statusColor = s.status === 'completed' ? '#15803d' : s.status === 'live' ? '#dc2626' : '#1d4ed8';
        const statusLabel = s.status === 'completed' ? 'مكتملة' : s.status === 'live' ? '● مباشر' : 'مجدولة';

        return `
        <div style="background:#fafafa;border-radius:12px;border:1px solid #e5e7eb;padding:14px;display:flex;align-items:flex-start;gap:12px;">
            <div style="width:48px;text-align:center;flex-shrink:0;background:white;border-radius:10px;border:1px solid #e5e7eb;padding:8px 4px;">
                <div style="font-size:18px;font-weight:800;color:#111827;line-height:1;">${time.split(':')[0]}</div>
                <div style="font-size:12px;color:#6b7280;">${time.split(':')[1]}</div>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;margin-bottom:6px;">
                    <h4 style="margin:0;font-size:14px;font-weight:700;color:#111827;">${s.title}${s.session_number ? ' <span style="font-size:11px;color:#9ca3af;">#'+s.session_number+'</span>' : ''}</h4>
                    <span style="background:${statusBg};color:${statusColor};font-size:11px;font-weight:600;padding:2px 9px;border-radius:20px;">${statusLabel}</span>
                </div>
                <p style="margin:0 0 8px;font-size:12px;color:#6b7280;">
                    ${s.is_course ? '🎓' : '📚'} ${s.subject_name || '—'}
                    ${s.program_name && !s.is_course ? '<span style="color:#9ca3af;"> · ' + s.program_name + '</span>' : ''}
                    ${s.teacher_name ? ' · 👤 ' + s.teacher_name : ''}
                    ${s.duration_minutes ? ' · ' + s.duration_minutes + ' دقيقة' : ''}
                </p>
                <div style="display:flex;gap:7px;flex-wrap:wrap;align-items:center;">
                    <span style="background:${ts.bg};color:${ts.color};font-size:11px;font-weight:600;padding:2px 9px;border-radius:20px;">${ts.label}</span>
                    <span style="background:${s.is_course ? '#fef3c7' : '#ede9fe'};color:${s.is_course ? '#92400e' : '#5b21b6'};font-size:11px;font-weight:600;padding:2px 9px;border-radius:20px;">${s.is_course ? 'دورة' : 'مقرر'}</span>
                    <span style="background:#f3f4f6;color:#6b7280;font-size:11px;padding:2px 9px;border-radius:20px;">👥 ${s.attendance_count} طالب</span>
                    <button onclick="openAssign(${s.id})"
                            style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;background:linear-gradient(135deg,#0071AA,#005a88);color:white;border:none;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;">
                        ➕ إسناد الطلاب
                    </button>
                </div>
            </div>
        </div>`;
    }).join('');

    panel.style.display = 'block';
    panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function prevMonth()  { curMonth--; if (curMonth < 0)  { curMonth = 11; curYear--; } renderCalendar(); }
function nextMonth()  { curMonth++; if (curMonth > 11) { curMonth = 0;  curYear++; } renderCalendar(); }
function goToToday()  { curYear = TODAY.getFullYear(); curMonth = TODAY.getMonth(); renderCalendar(); }

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
    document.getElementById('selectedCount').textContent = n + ' طالب محدد';
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
        toast.textContent = `✓ تم إسناد ${data.inserted} طالب جديد للجلسة`;
        toast.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:99999;background:#15803d;color:white;padding:12px 20px;border-radius:12px;font-size:13px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.2);';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3500);
    });
}

renderCalendar();
</script>
@endsection
