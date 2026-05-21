@extends('layouts.dashboard')
@section('title', __('Academic Schedule'))

@php
$calSessions = $sessions->map(fn($s) => [
    'id'               => $s->id,
    'title'            => $s->title ?: ($s->title_ar ?: ''),
    'subject_name'     => $s->subject->name_ar ?? '',
    'scheduled_at'     => $s->scheduled_at ? \Carbon\Carbon::parse($s->scheduled_at)->toIso8601String() : null,
    'duration_minutes' => $s->duration_minutes ?? 60,
    'type'             => $s->type ?? '',
    'status'           => $s->status ?? '',
    'session_number'   => $s->session_number,
    'zoom_start_url'   => $s->zoom_start_url,
    'zoom_join_url'    => $s->zoom_join_url,
])->filter(fn($s) => $s['scheduled_at'])->values();
@endphp

@section('content')
<div style="direction:rtl;font-family:'Segoe UI',sans-serif;">

{{-- Alerts --}}
@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-right:4px solid #22c55e;border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span style="color:#15803d;font-size:14px;font-weight:500;">{{ session('success') }}</span>
</div>
@endif
@if($errors->any())
<div style="background:#fff1f2;border:1px solid #fecaca;border-right:4px solid #ef4444;border-radius:12px;padding:14px 18px;margin-bottom:20px;">
    <ul style="margin:0;padding-right:18px;color:#dc2626;font-size:13px;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
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
                <h1 style="color:white;font-size:20px;font-weight:700;margin:0;">الجدول الأكاديمي</h1>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            @foreach([
                [$stats['total'],     'الكل',    'rgba(255,255,255,0.75)'],
                [$stats['upcoming'],  'قادمة',   '#fde68a'],
                [$stats['completed'], 'مكتملة',  '#86efac'],
                [$past->count(),      'ماضية',   'rgba(255,255,255,0.55)'],
            ] as [$v,$l,$c])
            <div style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.12);border-radius:12px;padding:8px 16px;text-align:center;min-width:64px;">
                <div style="font-size:20px;font-weight:700;color:{{ $c }};line-height:1;">{{ $v }}</div>
                <div style="font-size:11px;color:rgba(255,255,255,0.55);margin-top:2px;">{{ $l }}</div>
            </div>
            @endforeach
            {{-- Monthly schedule trigger --}}
            <button onclick="openMonthlyModal()"
                    style="display:flex;align-items:center;gap:8px;padding:10px 18px;background:linear-gradient(135deg,#15803d,#166534);color:white;border:none;border-radius:12px;font-size:13px;font-weight:700;cursor:pointer;box-shadow:0 3px 14px rgba(21,128,61,.45);">
                <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 9h-3v3H9v-3H6v-2h3V8h2v3h3v2z"/></svg>
                جدولة الشهر كاملاً
            </button>
        </div>
    </div>
</div>

{{-- Calendar --}}
<div>
        <div style="background:white;border-radius:18px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);">

            {{-- Calendar toolbar --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid #f1f5f9;background:#fafafa;flex-wrap:wrap;gap:10px;">
                {{-- Prev / Title / Next --}}
                <div style="display:flex;align-items:center;gap:8px;">
                    <button id="btnPrev" onclick="navPrev()"
                            style="width:34px;height:34px;border-radius:9px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#374151;font-size:16px;"
                            onmouseover="this.style.borderColor='#0071AA';this.style.color='#0071AA'"
                            onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">&#8249;</button>
                    <button onclick="goToToday()"
                            style="padding:0 14px;height:34px;border-radius:9px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;font-size:12px;font-weight:600;color:#374151;"
                            onmouseover="this.style.borderColor='#0071AA';this.style.color='#0071AA'"
                            onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">اليوم</button>
                    <button id="btnNext" onclick="navNext()"
                            style="width:34px;height:34px;border-radius:9px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#374151;font-size:16px;"
                            onmouseover="this.style.borderColor='#0071AA';this.style.color='#0071AA'"
                            onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">&#8250;</button>
                </div>

                <h2 id="calTitle" style="font-size:16px;font-weight:700;color:#111827;margin:0;flex:1;text-align:center;"></h2>

                {{-- View toggles --}}
                <div style="display:flex;background:#f1f5f9;border-radius:10px;padding:3px;gap:2px;">
                    <button id="v-month" onclick="setView('month')"
                            style="padding:6px 14px;border-radius:8px;border:none;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;background:linear-gradient(135deg,#0071AA,#005a88);color:white;">شهر</button>
                    <button id="v-week"  onclick="setView('week')"
                            style="padding:6px 14px;border-radius:8px;border:none;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#6b7280;">أسبوع</button>
                    <button id="v-day"   onclick="setView('day')"
                            style="padding:6px 14px;border-radius:8px;border:none;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;background:transparent;color:#6b7280;">يوم</button>
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
                <div style="overflow-y:auto;max-height:620px;" id="weekScroll">
                    <div style="display:flex;position:relative;">
                        <div id="timeLabels" style="width:48px;flex-shrink:0;"></div>
                        <div id="weekGrid"   style="flex:1;position:relative;"></div>
                    </div>
                </div>
            </div>

            {{-- Day view --}}
            <div id="view-day" style="display:none;">
                <div style="overflow-y:auto;max-height:620px;" id="dayScroll">
                    <div style="display:flex;position:relative;">
                        <div id="dayTimeLabels" style="width:48px;flex-shrink:0;"></div>
                        <div id="dayGrid"        style="flex:1;position:relative;"></div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Day-detail panel (month view click) --}}
        <div id="dayPanel" style="display:none;background:white;border-radius:16px;border:1px solid #e5e7eb;margin-top:16px;box-shadow:0 2px 12px rgba(0,0,0,.06);overflow:hidden;">
            <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;background:#fafafa;">
                <h3 id="dayPanelTitle" style="font-size:15px;font-weight:700;color:#111827;margin:0;"></h3>
                <button onclick="document.getElementById('dayPanel').style.display='none'"
                        style="width:28px;height:28px;border-radius:7px;border:none;background:#f1f5f9;cursor:pointer;color:#6b7280;font-size:16px;display:flex;align-items:center;justify-content:center;">×</button>
            </div>
            <div id="dayPanelContent" style="padding:16px;display:flex;flex-direction:column;gap:10px;"></div>
        </div>
    </div>

</div>
</div>

{{-- ══════════════════════════════════════ --}}
{{-- Monthly Schedule Modal --}}
{{-- ══════════════════════════════════════ --}}
<div id="monthlyModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.55);align-items:center;justify-content:center;padding:16px;">
    <div style="background:white;border-radius:22px;width:100%;max-width:520px;max-height:92vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 28px 70px rgba(0,0,0,.28);">
        {{-- Modal header --}}
        <div style="padding:22px 26px 18px;background:linear-gradient(135deg,#15803d,#166534);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;background:rgba(255,255,255,.2);border-radius:11px;display:flex;align-items:center;justify-content:center;">
                    <svg width="19" height="19" fill="white" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 9h-3v3H9v-3H6v-2h3V8h2v3h3v2z"/></svg>
                </div>
                <div>
                    <h3 style="color:white;font-size:16px;font-weight:700;margin:0;">جدولة الشهر كاملاً</h3>
                    <p style="color:rgba(255,255,255,.7);font-size:12px;margin:3px 0 0;">حدد الأيام المتكررة وسيتم إنشاء جلسات تلقائياً</p>
                </div>
            </div>
            <button onclick="closeMonthlyModal()"
                    style="width:32px;height:32px;background:rgba(255,255,255,.2);border:none;border-radius:8px;cursor:pointer;color:white;font-size:20px;display:flex;align-items:center;justify-content:center;line-height:1;">×</button>
        </div>

        {{-- Modal body --}}
        <div style="flex:1;overflow-y:auto;padding:22px 26px;">
            <form id="monthlyForm" action="{{ route('teacher.schedule.monthly.store') }}" method="POST">
                @csrf
                {{-- Subject --}}
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:5px;">المقرر <span style="color:#ef4444;">*</span></label>
                    <select name="subject_id" required
                            style="width:100%;border-radius:10px;border:1.5px solid #e5e7eb;background:white;padding:10px 13px;font-size:13px;color:#111827;outline:none;font-family:inherit;"
                            onfocus="this.style.borderColor='#15803d'" onblur="this.style.borderColor='#e5e7eb'">
                        <option value="">— اختر المقرر —</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name_ar }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Year + Month --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:5px;">السنة</label>
                        <select name="year" style="width:100%;border-radius:10px;border:1.5px solid #e5e7eb;background:white;padding:10px 13px;font-size:13px;outline:none;font-family:inherit;"
                                onfocus="this.style.borderColor='#15803d'" onblur="this.style.borderColor='#e5e7eb'">
                            @for($y = now()->year; $y <= now()->year + 2; $y++)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:5px;">الشهر</label>
                        <select name="month" style="width:100%;border-radius:10px;border:1.5px solid #e5e7eb;background:white;padding:10px 13px;font-size:13px;outline:none;font-family:inherit;"
                                onfocus="this.style.borderColor='#15803d'" onblur="this.style.borderColor='#e5e7eb'">
                            @foreach(['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'] as $mi => $mn)
                            <option value="{{ $mi + 1 }}" {{ ($mi + 1) == now()->month ? 'selected' : '' }}>{{ $mn }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Days of week --}}
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:8px;">أيام الأسبوع المتكررة <span style="color:#ef4444;">*</span></label>
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:7px;">
                        @foreach([0=>'الأحد',1=>'الاثنين',2=>'الثلاثاء',3=>'الأربعاء',4=>'الخميس',5=>'الجمعة',6=>'السبت'] as $dow => $dayName)
                        <label id="day-lbl-{{ $dow }}"
                               style="display:flex;flex-direction:column;align-items:center;gap:4px;padding:9px 6px;border-radius:10px;border:1.5px solid #e5e7eb;cursor:pointer;font-size:11px;font-weight:600;color:#6b7280;background:white;text-align:center;transition:all .15s;"
                               onclick="toggleDayLabel(this)">
                            <input type="checkbox" name="days[]" value="{{ $dow }}" style="display:none;">
                            {{ $dayName }}
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Time + Duration --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:5px;">وقت البدء <span style="color:#ef4444;">*</span></label>
                        <input type="time" name="time" value="09:00" required
                               style="width:100%;border-radius:10px;border:1.5px solid #e5e7eb;background:white;padding:10px 13px;font-size:13px;color:#111827;outline:none;box-sizing:border-box;"
                               onfocus="this.style.borderColor='#15803d'" onblur="this.style.borderColor='#e5e7eb'">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:5px;">المدة (دقيقة)</label>
                        <input type="number" name="duration_minutes" value="60" min="15" max="480" step="15"
                               style="width:100%;border-radius:10px;border:1.5px solid #e5e7eb;background:white;padding:10px 13px;font-size:13px;color:#111827;outline:none;box-sizing:border-box;"
                               onfocus="this.style.borderColor='#15803d'" onblur="this.style.borderColor='#e5e7eb'">
                    </div>
                </div>

                {{-- Preview count hint --}}
                <div id="previewHint" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:10px 14px;margin-bottom:18px;font-size:12px;color:#15803d;display:none;"></div>

                {{-- Submit --}}
                <button type="submit"
                        style="width:100%;display:flex;align-items:center;justify-content:center;gap:9px;padding:13px;background:linear-gradient(135deg,#15803d,#166534);color:white;border:none;border-radius:11px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(21,128,61,0.4);">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 9h-3v3H9v-3H6v-2h3V8h2v3h3v2z"/></svg>
                    إنشاء الجدول الشهري
                </button>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes livePulse{0%,100%{opacity:1}50%{opacity:.35}}
.cal-cell{transition:background .12s;}
.cal-cell:hover{background:#f8faff !important;}
.cal-event{transition:opacity .12s;}
.cal-event:hover{opacity:.82;}
.tg-event{transition:box-shadow .12s,transform .12s;}
.tg-event:hover{box-shadow:0 4px 16px rgba(0,0,0,.18)!important;transform:translateY(-1px);}
</style>

<script>
const sessions    = @json($calSessions);
const CSRF = '{{ csrf_token() }}';

const DAY_NAMES   = ['أحد','اثن','ثلا','أرب','خمس','جمع','سبت'];
const DAY_FULL    = ['الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
const MONTH_NAMES = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];

const TODAY    = new Date();
let curView    = 'month';
let curYear    = TODAY.getFullYear();
let curMonth   = TODAY.getMonth();
let curWeekStart = getWeekStart(TODAY); // Sunday of current week
let curDay     = new Date(TODAY.getFullYear(), TODAY.getMonth(), TODAY.getDate());

// ── Helpers ──────────────────────────────────
function getWeekStart(d) {
    const dt = new Date(d);
    dt.setDate(dt.getDate() - dt.getDay()); // Sunday
    return new Date(dt.getFullYear(), dt.getMonth(), dt.getDate());
}
function addDays(d, n) { const r = new Date(d); r.setDate(r.getDate() + n); return r; }
function sameDay(a, b)  { return a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate(); }

function sessionsOnDay(d) {
    return sessions.filter(s => {
        if (!s.scheduled_at) return false;
        return sameDay(new Date(s.scheduled_at), d);
    }).sort((a,b) => new Date(a.scheduled_at) - new Date(b.scheduled_at));
}

function typeStyle(type) {
    if (type === 'live_zoom')      return { bg:'#dbeafe', color:'#1d4ed8', border:'#93c5fd', label:'Zoom' };
    if (type === 'in_person')      return { bg:'#dcfce7', color:'#15803d', border:'#86efac', label:'حضوري' };
    if (type === 'recorded_video') return { bg:'#fce7f3', color:'#be185d', border:'#f9a8d4', label:'مسجّل' };
    return { bg:'#f3f4f6', color:'#4b5563', border:'#d1d5db', label: type || '—' };
}

function fmt12(iso) {
    const d = new Date(iso), h = d.getHours(), m = d.getMinutes();
    return (h%12||12) + ':' + String(m).padStart(2,'0') + (h<12?' ص':' م');
}

// ── View switching ───────────────────────────
function setView(v) {
    curView = v;
    ['month','week','day'].forEach(x => {
        document.getElementById('view-'+x).style.display = x===v ? 'block' : 'none';
        const btn = document.getElementById('v-'+x);
        if (x===v) { btn.style.background='linear-gradient(135deg,#0071AA,#005a88)'; btn.style.color='white'; }
        else        { btn.style.background='transparent'; btn.style.color='#6b7280'; }
    });
    document.getElementById('dayPanel').style.display = 'none';
    render();
}

function navPrev() {
    if (curView==='month')      { curMonth--; if(curMonth<0){curMonth=11;curYear--;} }
    else if (curView==='week')  { curWeekStart = addDays(curWeekStart,-7); }
    else                        { curDay = addDays(curDay,-1); }
    render();
}
function navNext() {
    if (curView==='month')      { curMonth++; if(curMonth>11){curMonth=0;curYear++;} }
    else if (curView==='week')  { curWeekStart = addDays(curWeekStart,7); }
    else                        { curDay = addDays(curDay,1); }
    render();
}
function goToToday() {
    curYear=TODAY.getFullYear(); curMonth=TODAY.getMonth();
    curWeekStart=getWeekStart(TODAY);
    curDay=new Date(TODAY.getFullYear(),TODAY.getMonth(),TODAY.getDate());
    render();
}

function render() {
    if (curView==='month') renderMonth();
    else if (curView==='week') renderWeek();
    else renderDay();
}

// ── MONTH VIEW ───────────────────────────────
function renderMonth() {
    document.getElementById('calTitle').textContent = MONTH_NAMES[curMonth] + ' ' + curYear;

    document.getElementById('calHeaders').innerHTML = DAY_NAMES.map(d =>
        `<div style="padding:9px 4px;text-align:center;font-size:11px;font-weight:700;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb;">${d}</div>`
    ).join('');

    const firstDow    = new Date(curYear,curMonth,1).getDay();
    const daysInMonth = new Date(curYear,curMonth+1,0).getDate();
    const prevDays    = new Date(curYear,curMonth,0).getDate();

    let cells=[];
    for(let i=firstDow-1;i>=0;i--) cells.push({d:prevDays-i,dt:new Date(curYear,curMonth-1,prevDays-i),cur:false});
    for(let d=1;d<=daysInMonth;d++) cells.push({d,dt:new Date(curYear,curMonth,d),cur:true});
    let fill=42-cells.length;
    for(let d=1;d<=fill;d++) cells.push({d,dt:new Date(curYear,curMonth+1,d),cur:false});

    document.getElementById('calGrid').innerHTML = cells.map(cell => {
        const isTd = sameDay(cell.dt,TODAY);
        const daySes = cell.cur ? sessionsOnDay(cell.dt) : [];
        const visible = daySes.slice(0,3), more=daySes.length-visible.length;

        const chips = visible.map(s => {
            const ts=typeStyle(s.type), time=fmt12(s.scheduled_at);
            const label=(s.title||s.subject_name||ts.label).substring(0,18);
            return `<div class="cal-event" onclick="event.stopPropagation();showDayPanel(${cell.dt.getFullYear()},${cell.dt.getMonth()},${cell.dt.getDate()})"
                         style="background:${ts.bg};color:${ts.color};border-right:2px solid ${ts.color};font-size:10px;font-weight:600;padding:2px 5px;border-radius:3px;margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;cursor:pointer;">${time} ${label}</div>`;
        }).join('');
        const moreEl = more>0 ? `<div style="font-size:10px;color:#6b7280;text-align:center;cursor:pointer;" onclick="showDayPanel(${cell.dt.getFullYear()},${cell.dt.getMonth()},${cell.dt.getDate()})">+${more} أخرى</div>` : '';

        const cellBg = isTd ? '#eff6ff' : (cell.cur?'white':'#fafafa');
        const numSt  = isTd ? 'display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;background:#0071AA;color:white;border-radius:50%;font-weight:700;font-size:12px;'
                            : `font-size:12px;font-weight:${cell.cur?'500':'400'};color:${cell.cur?'#374151':'#cbd5e1'};`;
        return `<div class="cal-cell" onclick="showDayPanel(${cell.dt.getFullYear()},${cell.dt.getMonth()},${cell.dt.getDate()})"
                     style="min-height:90px;padding:5px;border-left:1px solid #f1f5f9;border-bottom:1px solid #f1f5f9;background:${cellBg};cursor:pointer;">
                    <div style="margin-bottom:3px;text-align:left;"><span style="${numSt}">${cell.d}</span></div>
                    ${chips}${moreEl}
                </div>`;
    }).join('');
}

// ── WEEK VIEW ────────────────────────────────
const TG_START = 7;   // 7am
const TG_END   = 22;  // 10pm
const PX_PER_HR = 60; // 60px per hour

function renderTimeLabels(containerId) {
    let html = `<div style="height:${PX_PER_HR/2}px;"></div>`; // top offset
    for(let h=TG_START;h<=TG_END;h++) {
        html += `<div style="height:${PX_PER_HR}px;display:flex;align-items:flex-start;padding-top:0;justify-content:flex-end;padding-left:6px;padding-right:6px;">
                    <span style="font-size:10px;color:#9ca3af;font-weight:500;white-space:nowrap;">${h%12||12}${h<12?'ص':'م'}</span>
                 </div>`;
    }
    document.getElementById(containerId).innerHTML = html;
}

function renderTimeGrid(containerId, days) {
    const hours = TG_END - TG_START + 1;
    const totalH = hours * PX_PER_HR;

    // Build background grid lines
    let bg = '';
    for(let h=0;h<=hours;h++) {
        const y = h * PX_PER_HR;
        bg += `<div style="position:absolute;top:${y}px;left:0;right:0;height:1px;background:${h===0?'#e5e7eb':'#f1f5f9'};"></div>`;
    }

    // Columns
    const colW = 100 / days.length;
    const colHtml = days.map((day,ci) => {
        const isTd = sameDay(day,TODAY);
        const daySes = sessionsOnDay(day);

        const events = daySes.map(s => {
            const dt       = new Date(s.scheduled_at);
            const startMin = (dt.getHours() - TG_START) * 60 + dt.getMinutes();
            const dur      = Math.max(s.duration_minutes || 60, 15);
            const top      = startMin;   // 1px per minute
            const height   = Math.max(dur, 30);
            const ts       = typeStyle(s.type);
            const time     = fmt12(s.scheduled_at);
            const label    = (s.title || s.subject_name || ts.label).substring(0,24);

            if(startMin < 0 || top > (TG_END-TG_START)*60) return '';
            return `<div class="tg-event" onclick="showDayPanel(${day.getFullYear()},${day.getMonth()},${day.getDate()})"
                         style="position:absolute;top:${top}px;left:2px;right:2px;height:${height}px;
                                background:${ts.bg};color:${ts.color};border-right:3px solid ${ts.color};
                                border-radius:6px;padding:3px 6px;font-size:10px;font-weight:600;
                                overflow:hidden;cursor:pointer;z-index:2;box-shadow:0 1px 4px rgba(0,0,0,.08);">
                        <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${time}</div>
                        <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;opacity:.85;">${label}</div>
                    </div>`;
        }).join('');

        const todayBg = isTd ? 'rgba(0,113,170,.04)' : 'transparent';
        return `<div style="position:absolute;top:0;bottom:0;left:${ci*colW}%;width:${colW}%;background:${todayBg};border-left:1px solid #f1f5f9;">
                    <div style="position:relative;height:${totalH}px;">
                        ${events}
                    </div>
                </div>`;
    }).join('');

    // Now marker
    const nowDate = new Date();
    const inRange = nowDate >= days[0] && nowDate <= addDays(days[days.length-1],1);
    const nowLine = inRange ? (() => {
        const nowMin = (nowDate.getHours()-TG_START)*60 + nowDate.getMinutes();
        if(nowMin<0 || nowMin>(TG_END-TG_START)*60) return '';
        // find which column
        const ci = days.findIndex(d => sameDay(d,nowDate));
        if(ci<0) return '';
        const leftPct = ci*colW + '%';
        const widthPct = colW + '%';
        return `<div style="position:absolute;top:${nowMin}px;left:${leftPct};width:${widthPct};height:2px;background:#ef4444;z-index:10;">
                    <div style="position:absolute;right:-1px;top:-4px;width:8px;height:8px;background:#ef4444;border-radius:50%;"></div>
                </div>`;
    })() : '';

    document.getElementById(containerId).innerHTML =
        `<div style="position:relative;height:${totalH}px;">${bg}${colHtml}${nowLine}</div>`;

    // Scroll to 8am on render
    const scroll = containerId === 'weekGrid' ? document.getElementById('weekScroll') : document.getElementById('dayScroll');
    if(scroll) { setTimeout(()=>{ scroll.scrollTop = (8-TG_START)*PX_PER_HR - 20; },50); }
}

function renderWeek() {
    const days = [];
    for(let i=0;i<7;i++) days.push(addDays(curWeekStart,i));

    // Title
    const wEnd = addDays(curWeekStart,6);
    document.getElementById('calTitle').textContent =
        MONTH_NAMES[curWeekStart.getMonth()] + ' ' + curWeekStart.getDate() +
        ' — ' + MONTH_NAMES[wEnd.getMonth()] + ' ' + wEnd.getDate() + '، ' + wEnd.getFullYear();

    // Headers
    document.getElementById('weekHeaders').style.gridTemplateColumns = '48px repeat(7,1fr)';
    document.getElementById('weekHeaders').innerHTML =
        '<div></div>' +
        days.map(d => {
            const isTd = sameDay(d,TODAY);
            const numSt = isTd ? 'display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;background:#0071AA;color:white;border-radius:50%;font-weight:800;'
                                : 'font-weight:700;color:#374151;';
            return `<div style="padding:10px 4px;text-align:center;border-bottom:1px solid #e5e7eb;background:${isTd?'#eff6ff':'#f9fafb'};">
                        <div style="font-size:11px;color:#9ca3af;margin-bottom:2px;">${DAY_NAMES[d.getDay()]}</div>
                        <span style="${numSt};font-size:13px;">${d.getDate()}</span>
                    </div>`;
        }).join('');

    renderTimeLabels('timeLabels');
    renderTimeGrid('weekGrid', days);
}

function renderDay() {
    document.getElementById('calTitle').textContent =
        DAY_FULL[curDay.getDay()] + ' ' + curDay.getDate() + ' ' + MONTH_NAMES[curDay.getMonth()] + ' ' + curDay.getFullYear();

    renderTimeLabels('dayTimeLabels');
    renderTimeGrid('dayGrid', [curDay]);
}

// ── Day panel ────────────────────────────────
function showDayPanel(y,m,d) {
    const dt = new Date(y,m,d);
    const daySes = sessionsOnDay(dt);
    const panel  = document.getElementById('dayPanel');
    if(!daySes.length){ panel.style.display='none'; return; }

    const dateLabel = new Intl.DateTimeFormat('ar-SA',{weekday:'long',year:'numeric',month:'long',day:'numeric'}).format(dt);
    document.getElementById('dayPanelTitle').textContent = dateLabel + ' — ' + daySes.length + ' محاضرة';

    document.getElementById('dayPanelContent').innerHTML = daySes.map(s => {
        const ts=typeStyle(s.type), time=fmt12(s.scheduled_at);
        const stBg    = s.status==='completed'?'#dcfce7':s.status==='live'?'#fee2e2':'#dbeafe';
        const stColor = s.status==='completed'?'#15803d':s.status==='live'?'#dc2626':'#1d4ed8';
        const stLabel = s.status==='completed'?'مكتملة':s.status==='live'?'● مباشر':'مجدولة';
        return `<div style="background:#fafafa;border-radius:12px;border:1px solid #e5e7eb;padding:14px;display:flex;gap:12px;">
            <div style="width:46px;text-align:center;flex-shrink:0;background:white;border-radius:9px;border:1px solid #e5e7eb;padding:7px 4px;">
                <div style="font-size:17px;font-weight:800;color:#111827;line-height:1;">${time.split(':')[0]}</div>
                <div style="font-size:11px;color:#6b7280;">${time.split(':')[1]}</div>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:5px;margin-bottom:5px;">
                    <h4 style="margin:0;font-size:13px;font-weight:700;color:#111827;">${s.title||ts.label}${s.session_number?' <span style="font-size:11px;color:#9ca3af;">#'+s.session_number+'</span>':''}</h4>
                    <span style="background:${stBg};color:${stColor};font-size:11px;font-weight:600;padding:2px 9px;border-radius:20px;">${stLabel}</span>
                </div>
                <p style="margin:0 0 7px;font-size:12px;color:#6b7280;">📚 ${s.subject_name||'—'}${s.duration_minutes?' · '+s.duration_minutes+' دقيقة':''}</p>
                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    <span style="background:${ts.bg};color:${ts.color};font-size:11px;font-weight:600;padding:2px 9px;border-radius:20px;">${ts.label}</span>
                    ${s.zoom_start_url?`<a href="${s.zoom_start_url}" target="_blank" style="padding:4px 11px;background:linear-gradient(135deg,#ef4444,#dc2626);color:white;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;">▶ ابدأ الجلسة</a>`:''}
                    ${s.zoom_join_url?`<a href="${s.zoom_join_url}" target="_blank" style="padding:4px 11px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:white;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;">دخول Zoom</a>`:''}
                </div>
            </div>
        </div>`;
    }).join('');

    panel.style.display='block';
    panel.scrollIntoView({behavior:'smooth',block:'nearest'});
}

// ── Monthly modal ────────────────────────────
function openMonthlyModal() {
    document.getElementById('monthlyModal').style.display='flex';
    document.body.style.overflow='hidden';
}
function closeMonthlyModal() {
    document.getElementById('monthlyModal').style.display='none';
    document.body.style.overflow='';
}
document.addEventListener('keydown', e => { if(e.key==='Escape') closeMonthlyModal(); });
document.getElementById('monthlyModal').addEventListener('click', function(e) {
    if(e.target===this) closeMonthlyModal();
});

// Toggle day-of-week label style
function toggleDayLabel(lbl) {
    const cb = lbl.querySelector('input');
    cb.checked = !cb.checked;
    if(cb.checked) {
        lbl.style.background='#f0fdf4'; lbl.style.borderColor='#15803d'; lbl.style.color='#15803d';
    } else {
        lbl.style.background='white'; lbl.style.borderColor='#e5e7eb'; lbl.style.color='#6b7280';
    }
    updatePreviewHint();
}

function updatePreviewHint() {
    const yearEl  = document.querySelector('#monthlyForm select[name=year]');
    const monthEl = document.querySelector('#monthlyForm select[name=month]');
    const checked = document.querySelectorAll('#monthlyForm input[name="days[]"]:checked');
    if(!yearEl||!monthEl||!checked.length) { document.getElementById('previewHint').style.display='none'; return; }

    const y=parseInt(yearEl.value), m=parseInt(monthEl.value)-1;
    const days=[...checked].map(c=>parseInt(c.value));
    const start=new Date(y,m,1), end=new Date(y,m+1,0);
    let count=0, cur=new Date(start);
    while(cur<=end){if(days.includes(cur.getDay()))count++;cur.setDate(cur.getDate()+1);}
    const hint=document.getElementById('previewHint');
    hint.textContent='✓ سيتم إنشاء '+count+' جلسة في شهر '+MONTH_NAMES[m]+' '+y;
    hint.style.display='block';
}

// Wire up year/month selects to update hint
document.querySelector('#monthlyForm select[name=year]').addEventListener('change',updatePreviewHint);
document.querySelector('#monthlyForm select[name=month]').addEventListener('change',updatePreviewHint);

// Open modal if validation errors exist (form was re-submitted)
@if($errors->any())
openMonthlyModal();
@endif

render();
</script>
@endsection
