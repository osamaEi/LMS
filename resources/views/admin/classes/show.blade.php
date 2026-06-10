@extends('layouts.dashboard')
@section('title', 'إدارة المجموعة')

@section('content')
<div x-data="{
    termModal: false,
    subjectModal: false,
    teacherModal: false,
    editSubjectModal: false,
    deleteSubjectModal: false,
    attachModal: false,
    sessionModal: false,
    currentTermId: null,
    currentTermName: '',
    currentSubjectId: null,
    currentSubjectName: '',
    currentTeacherIds: [],
    editSubject: {},
    classId: '{{ $class->id }}',
    openTermModal()   { this.termModal = true; },
    openSubjectModal(id, name) { this.openAttachModal(id, name); },
    openAttachModal(id, name)  { this.currentTermId = id; this.currentTermName = name; this.attachModal = true; },
    openTeacherModal(sid, sname, tids) {
        this.currentSubjectId = sid;
        this.currentSubjectName = sname;
        this.currentTeacherIds = (tids || []).map(String);
        this.teacherModal = true;
    },
    openEditSubject(subject) { this.editSubject = subject; this.editSubjectModal = true; },
    openDeleteSubject(id, name) { this.currentSubjectId = id; this.currentSubjectName = name; this.deleteSubjectModal = true; }
}">

{{-- ══ Header ══ --}}
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('admin.programs.show', $class->program_id) }}"
       style="display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;color:#64748b;flex-shrink:0;"
       class="hover:bg-gray-50 transition-colors">
        <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 flex-wrap">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white truncate">{{ $class->name }}</h1>
            @php $stColors = ['active'=>['#dcfce7','#16a34a'],'inactive'=>['#f1f5f9','#64748b'],'completed'=>['#dbeafe','#2563eb']]; $sc=$stColors[$class->status]??['#f1f5f9','#64748b']; @endphp
            <span style="background:{{ $sc[0] }};color:{{ $sc[1] }};border-radius:9999px;padding:.18rem .7rem;font-size:.7rem;font-weight:700;">{{ ['active'=>'نشطة','inactive'=>'غير نشطة','completed'=>'منتهية'][$class->status] ?? $class->status }}</span>
        </div>
        <p class="text-xs text-gray-400 mt-0.5">{{ $class->program->name ?? '' }} · 👥 {{ $studentsCount }} طالب{{ $class->teacher ? ' · '.$class->teacher->name : '' }}</p>
    </div>
    @if($class->program && $class->program->type === 'diploma')
    <button @click="openTermModal()"
            style="display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;background:linear-gradient(135deg,#1a3a5c,#2563eb);color:white;font-size:12px;font-weight:700;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,.3);">
        <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة ربع للمجموعة
    </button>
    @endif
</div>

{{-- ── Flash ── --}}
@if(session('success'))
<div style="display:flex;align-items:center;gap:10px;background:#f0fdf4;border:1px solid #bbf7d0;border-right:4px solid #22c55e;border-radius:12px;padding:12px 16px;margin-bottom:20px;">
    <svg style="width:16px;height:16px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <span style="font-size:13px;font-weight:600;color:#15803d;">{{ session('success') }}</span>
</div>
@endif
@if(session('error') || $errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#b91c1c;">
    {{ session('error') ?? $errors->first() }}
</div>
@endif

@php $isDiplomaClass = $class->program && $class->program->type === 'diploma'; @endphp

{{-- ── Tabs ── --}}
<div style="display:flex;align-items:center;gap:4px;background:#f1f5f9;border-radius:12px;padding:4px;margin-bottom:20px;width:fit-content;">
    <button onclick="switchClassTab('students')" id="ctab-btn-students"
        style="padding:8px 18px;border-radius:9px;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:all .15s;background:white;color:#1e293b;box-shadow:0 1px 4px rgba(0,0,0,.08);">
        الطلاب
        <span style="background:#dbeafe;color:#2563eb;border-radius:9999px;padding:.1rem .5rem;font-size:.65rem;margin-right:4px;font-weight:700;">{{ $studentsCount }}</span>
    </button>
    <button onclick="switchClassTab('sessions')" id="ctab-btn-sessions"
        style="padding:8px 18px;border-radius:9px;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:all .15s;background:transparent;color:#64748b;">
        الجلسات
        <span style="background:#cffafe;color:#0891b2;border-radius:9999px;padding:.1rem .5rem;font-size:.65rem;margin-right:4px;font-weight:700;">{{ $sessions->count() }}</span>
    </button>
    @if($isDiplomaClass)
    <button onclick="switchClassTab('terms')" id="ctab-btn-terms"
        style="padding:8px 18px;border-radius:9px;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:all .15s;background:transparent;color:#64748b;">
        الأرباع والمواد
        <span style="background:#ede9fe;color:#7c3aed;border-radius:9999px;padding:.1rem .5rem;font-size:.65rem;margin-right:4px;font-weight:700;">{{ $class->terms->count() }}</span>
    </button>
    @endif
</div>

{{-- ════ TAB: Students ════ --}}
<div id="ctab-students">
    <div style="background:white;border-radius:18px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9;background:#fafafa;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:15px;font-weight:700;color:#111827;">طلاب المجموعة</span>
                <span style="background:#dbeafe;color:#2563eb;border-radius:9999px;padding:.12rem .55rem;font-size:.68rem;font-weight:700;">{{ $studentsCount }}</span>
            </div>
            <button onclick="openStudentsModal({{ $class->id }}, '{{ addslashes($class->name) }}')"
                    style="display:flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;background:linear-gradient(135deg,#0071AA,#004d77);color:white;font-size:12px;font-weight:700;border:none;cursor:pointer;">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                إدارة الطلاب
            </button>
        </div>
        @if($class->students->isNotEmpty())
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="border-bottom:2px solid #f1f5f9;background:#fafafa;">
                        <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;width:40px;">#</th>
                        <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الاسم</th>
                        <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">البريد</th>
                        <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الهوية</th>
                        <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الجوال</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($class->students as $i => $student)
                    <tr style="border-bottom:1px solid #f8fafc;">
                        <td style="padding:12px 16px;color:#cbd5e1;font-size:11px;">{{ $i + 1 }}</td>
                        <td style="padding:12px 16px;font-weight:600;color:#1e293b;">{{ $student->name }}</td>
                        <td style="padding:12px 16px;color:#475569;" dir="ltr">{{ $student->email }}</td>
                        <td style="padding:12px 16px;color:#64748b;" dir="ltr">{{ $student->national_id ?? '—' }}</td>
                        <td style="padding:12px 16px;color:#64748b;" dir="ltr">{{ $student->phone ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding:48px;text-align:center;">
            <p style="font-size:13px;color:#94a3b8;margin-bottom:12px;">لا يوجد طلاب في هذه المجموعة بعد</p>
            <button onclick="openStudentsModal({{ $class->id }}, '{{ addslashes($class->name) }}')"
                    style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:9px;background:linear-gradient(135deg,#0071AA,#004d77);color:white;font-size:12px;font-weight:700;border:none;cursor:pointer;">
                إضافة طلاب
            </button>
        </div>
        @endif
    </div>
</div>

{{-- ════ TAB: Terms ════ --}}
<div id="ctab-terms" style="display:none;">
{{-- ── Terms list (diploma only — courses/English have no terms) ── --}}
@if($class->program && $class->program->type === 'diploma')
<div class="space-y-5">
    @forelse($class->terms as $term)
        @include('admin.programs.partials.term-block', ['term' => $term, 'classId' => $class->id, 'showTeachers' => false])

        {{-- Inline add-subject (dropdown) under each term --}}
        @php $available = $programSubjects->whereNotIn('id', $usedSubjectIds); @endphp
        <div x-data="{ open: false }" style="margin:-10px 0 4px;">
            <button @click="open = !open" x-show="!open"
                    style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:9px;border:1.5px dashed #c4b5fd;background:#faf5ff;color:#7c3aed;font-size:12px;font-weight:700;cursor:pointer;">
                <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                إضافة مقرر للربع
            </button>

            <form x-show="open" x-cloak action="{{ route('admin.classes.attach-subject', $class->id) }}" method="POST"
                  style="background:white;border:1px solid #e2e8f0;border-radius:12px;padding:12px;box-shadow:0 2px 8px rgba(0,0,0,.04);">
                @csrf
                <input type="hidden" name="term_id" value="{{ $term->id }}">
                <div style="font-size:12px;font-weight:700;color:#475569;margin-bottom:8px;">اختر المقررات من الدبلومة</div>
                <div style="display:flex;flex-direction:column;gap:6px;max-height:220px;overflow-y:auto;margin-bottom:10px;">
                    @foreach($available as $s)
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 10px;border:1px solid #f1f5f9;border-radius:8px;cursor:pointer;font-size:13px;color:#1e293b;">
                        <input type="checkbox" name="subject_ids[]" value="{{ $s->id }}" style="width:15px;height:15px;accent-color:#7c3aed;flex-shrink:0;">
                        <span>{{ ($s->name_ar ?: $s->name_en) }} <span style="color:#94a3b8;font-size:11px;">({{ $s->code }})</span></span>
                    </label>
                    @endforeach
                </div>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" @click="open = false"
                            style="padding:9px 14px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:9px;cursor:pointer;">إلغاء</button>
                    <button type="submit" @if($available->isEmpty()) disabled @endif
                            style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#7c3aed,#8b5cf6);border:none;border-radius:9px;cursor:pointer;{{ $available->isEmpty() ? 'opacity:.5;cursor:not-allowed;' : '' }}">
                        حفظ المحدد
                    </button>
                </div>
            </form>
            @if($available->isEmpty())
            <p x-show="open" x-cloak style="font-size:12px;color:#dc2626;margin-top:6px;">كل مواد الدبلومة مُسندة بالفعل لأرباع هذه المجموعة.</p>
            @endif
        </div>
    @empty
    <div style="background:white;border-radius:18px;border:1px solid #e2e8f0;padding:60px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,.04);">
        <div style="width:64px;height:64px;border-radius:18px;background:linear-gradient(135deg,#1a3a5c,#2563eb);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg style="width:28px;height:28px;color:white;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </div>
        <p style="font-size:15px;font-weight:600;color:#475569;margin-bottom:6px;">لا توجد أرباع خاصة بهذه المجموعة</p>
        <p style="font-size:12px;color:#94a3b8;margin-bottom:20px;">ابدأ بإضافة أول ربع تدريبي للمجموعة</p>
        <button @click="openTermModal()"
                style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:10px;background:linear-gradient(135deg,#1a3a5c,#2563eb);color:white;font-size:13px;font-weight:700;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,.3);">
            <svg style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            إضافة ربع دراسي
        </button>
    </div>
    @endforelse
</div>
@else
<div style="background:white;border-radius:18px;border:1px solid #e2e8f0;padding:48px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,.04);">
    <div style="width:56px;height:56px;border-radius:16px;background:#f0f9ff;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
        <svg style="width:26px;height:26px;color:#0071AA;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    </div>
    <p style="font-size:14px;font-weight:600;color:#475569;margin-bottom:4px;">هذه المجموعة لا تحتوي على أرباع</p>
    <p style="font-size:12px;color:#94a3b8;">الأرباع والمواد خاصة بالدبلومات فقط. أنشئ جلسات هذه المجموعة من تبويب الجلسات.</p>
</div>
@endif
</div>{{-- /ctab-terms --}}

{{-- ════ TAB: Sessions ════ --}}
<div id="ctab-sessions" style="display:none;">
{{-- ══ الجدول والجلسات ══ --}}
@php
    $sessionsJs = $sessions->map(fn($s) => [
        'id'       => $s->id,
        'title'    => $s->title_ar ?: 'جلسة',
        'subject'  => $s->subject->name_ar ?? null,
        'at'       => $s->scheduled_at ? \Carbon\Carbon::parse($s->scheduled_at)->format('Y-m-d H:i:s') : null,
        'duration' => $s->duration_minutes,
        'status'   => $s->status,
        'number'   => $s->session_number,
    ])->filter(fn($s) => $s['at'])->values();
@endphp
<div style="margin-top:24px;background:white;border-radius:18px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);">
    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:16px 20px;border-bottom:1px solid #f1f5f9;background:#fafafa;">
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-size:15px;font-weight:700;color:#111827;">الجدول والجلسات</span>
            <span style="background:#eef2ff;color:#4338ca;border-radius:9999px;padding:.12rem .55rem;font-size:.68rem;font-weight:700;">{{ $sessions->count() }}</span>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            {{-- View switcher --}}
            <div style="display:inline-flex;background:#f1f5f9;border-radius:10px;padding:3px;">
                <button type="button" id="vbtn-list"  onclick="calSetView('list')"  style="padding:7px 16px;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit;background:white;color:#0071AA;box-shadow:0 1px 3px rgba(0,0,0,.1);">قائمة</button>
                <button type="button" id="vbtn-week"  onclick="calSetView('week')"  style="padding:7px 16px;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit;background:transparent;color:#64748b;">أسبوعي</button>
                <button type="button" id="vbtn-month" onclick="calSetView('month')" style="padding:7px 16px;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit;background:transparent;color:#64748b;">شهري</button>
            </div>
            <button @click="sessionModal = true"
                    style="display:flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;background:linear-gradient(135deg,#0071AA,#004d77);color:white;font-size:12px;font-weight:700;border:none;cursor:pointer;">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                إنشاء جلسات
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;padding:12px 20px;border-bottom:1px solid #f1f5f9;background:white;">
        <input id="calSearch" type="text" placeholder="بحث بالعنوان..." oninput="calApplyFilters()"
               style="flex:1;min-width:160px;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:12px;font-family:inherit;">
        @if($class->program && $class->program->type === 'diploma')
        <select id="calSubject" onchange="calApplyFilters()" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:12px;font-family:inherit;background:white;">
            <option value="">كل المقررات</option>
            @foreach($classSubjects as $cs)
                <option value="{{ $cs->name_ar ?: $cs->name_en }}">{{ $cs->name_ar ?: $cs->name_en }}</option>
            @endforeach
        </select>
        @endif
        <select id="calStatus" onchange="calApplyFilters()" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:12px;font-family:inherit;background:white;">
            <option value="">كل الحالات</option>
            <option value="scheduled">مجدولة</option>
            <option value="live">مباشرة</option>
            <option value="completed">مكتملة</option>
            <option value="cancelled">ملغاة</option>
        </select>
        <button type="button" onclick="calResetFilters()" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:9px;background:white;cursor:pointer;color:#64748b;font-size:12px;font-weight:600;font-family:inherit;">إعادة تعيين</button>
        <span id="calFilterCount" style="font-size:11px;color:#94a3b8;"></span>
    </div>

    {{-- Period nav (hidden for list) --}}
    <div id="calNav" style="display:none;align-items:center;justify-content:center;gap:16px;padding:14px;border-bottom:1px solid #f1f5f9;background:white;">
        <button type="button" onclick="calPrev()" style="width:36px;height:36px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;color:#374151;font-size:18px;">&#8249;</button>
        <h3 id="calTitle" style="font-size:16px;font-weight:700;color:#111827;margin:0;min-width:200px;text-align:center;"></h3>
        <button type="button" onclick="calNext()" style="width:36px;height:36px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;color:#374151;font-size:18px;">&#8250;</button>
        <button type="button" onclick="calToday()" style="padding:8px 14px;border-radius:10px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;color:#0071AA;font-size:12px;font-weight:700;font-family:inherit;">اليوم</button>
    </div>

    <div id="calBody"></div>
</div>

<script>
(function(){
    const SESSIONS = {!! $sessionsJs->toJson() !!};
    const TODAY = new Date();
    const DAY_NAMES = ['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
    const MONTH_NAMES = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
    const STATUS = {
        scheduled:['#dbeafe','#1d4ed8','مجدولة'],
        live:['#fee2e2','#dc2626','● مباشر'],
        completed:['#dcfce7','#15803d','مكتملة'],
        cancelled:['#fee2e2','#dc2626','ملغاة'],
    };
    let view = 'list';
    let cur  = new Date();
    let filters = { q: '', subject: '', status: '' };

    const parse = at => new Date(at.replace(' ','T'));
    const fmtTime = d => { let h=d.getHours(), m=String(d.getMinutes()).padStart(2,'0'); let ap=h>=12?'م':'ص'; let hh=h%12||12; return hh+':'+m+' '+ap; };
    const sameDay = (a,b)=> a.getFullYear()===b.getFullYear()&&a.getMonth()===b.getMonth()&&a.getDate()===b.getDate();
    const st = s => STATUS[s] || ['#f1f5f9','#64748b',s];

    // Apply the active filters to the session list
    function filtered(){
        return SESSIONS.filter(s => {
            if (filters.q && !((s.title||'').toLowerCase().includes(filters.q.toLowerCase()))) return false;
            if (filters.subject && (s.subject||'') !== filters.subject) return false;
            if (filters.status && (s.status||'') !== filters.status) return false;
            return true;
        });
    }
    const onDay = date => filtered().filter(s=>sameDay(parse(s.at),date)).sort((a,b)=>parse(a.at)-parse(b.at));

    window.calApplyFilters = function(){
        filters.q       = document.getElementById('calSearch')?.value || '';
        filters.subject = document.getElementById('calSubject')?.value || '';
        filters.status  = document.getElementById('calStatus')?.value || '';
        render();
    };
    window.calResetFilters = function(){
        filters = { q:'', subject:'', status:'' };
        const s1=document.getElementById('calSearch'); if(s1) s1.value='';
        const s2=document.getElementById('calSubject'); if(s2) s2.value='';
        const s3=document.getElementById('calStatus'); if(s3) s3.value='';
        render();
    };

    window.calSetView = function(v){
        view = v;
        ['list','week','month'].forEach(x=>{
            const b=document.getElementById('vbtn-'+x);
            const on = x===v;
            b.style.background = on?'white':'transparent';
            b.style.color = on?'#0071AA':'#64748b';
            b.style.boxShadow = on?'0 1px 3px rgba(0,0,0,.1)':'none';
        });
        document.getElementById('calNav').style.display = v==='list'?'none':'flex';
        render();
    };
    window.calPrev = function(){ if(view==='week') cur.setDate(cur.getDate()-7); else cur.setMonth(cur.getMonth()-1); render(); };
    window.calNext = function(){ if(view==='week') cur.setDate(cur.getDate()+7); else cur.setMonth(cur.getMonth()+1); render(); };
    window.calToday = function(){ cur = new Date(); render(); };

    function emptyState(){
        return `<div style="padding:48px 20px;text-align:center;color:#94a3b8;font-size:13px;">لا توجد جلسات في هذه الفترة</div>`;
    }

    function renderList(){
        const data = filtered();
        if(!SESSIONS.length) return `<div style="padding:48px;text-align:center;color:#94a3b8;font-size:13px;">لا توجد جلسات لهذه المجموعة بعد</div>`;
        if(!data.length) return `<div style="padding:48px;text-align:center;color:#94a3b8;font-size:13px;">لا توجد جلسات مطابقة للفلتر</div>`;
        const rows = [...data].sort((a,b)=>parse(a.at)-parse(b.at)).map((s,i)=>{
            const d=parse(s.at); const c=st(s.status);
            const date = d.toLocaleDateString('ar-EG',{year:'numeric',month:'2-digit',day:'2-digit'});
            return `<tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:12px 16px;color:#cbd5e1;font-size:11px;">${s.number ?? i+1}</td>
                <td style="padding:12px 16px;font-weight:600;color:#1e293b;">${s.title}</td>
                <td style="padding:12px 16px;color:#475569;" dir="ltr">${date} · ${fmtTime(d)}</td>
                <td style="padding:12px 16px;text-align:center;color:#64748b;">${s.duration} د</td>
                <td style="padding:12px 16px;text-align:center;"><span style="background:${c[0]};color:${c[1]};border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">${c[2]}</span></td>
            </tr>`;
        }).join('');
        return `<div style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead><tr style="border-bottom:2px solid #f1f5f9;background:#fafafa;">
                <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;width:40px;">#</th>
                <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">العنوان</th>
                <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الموعد</th>
                <th style="padding:11px 16px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;width:70px;">المدة</th>
                <th style="padding:11px 16px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;width:100px;">الحالة</th>
            </tr></thead><tbody>${rows}</tbody></table></div>`;
    }

    function weekStart(){ const d=new Date(cur); d.setDate(d.getDate()-d.getDay()); d.setHours(0,0,0,0); return d; }

    function renderWeek(){
        const ws=weekStart();
        const wend=new Date(ws.getFullYear(),ws.getMonth(),ws.getDate()+6);
        document.getElementById('calTitle').textContent =
            ws.getDate()+' '+MONTH_NAMES[ws.getMonth()]+' — '+wend.getDate()+' '+MONTH_NAMES[wend.getMonth()];

        // Show Sun → Thu (5 academic days)
        const weekDays = [];
        for(let i=0;i<5;i++){ const d=new Date(ws); d.setDate(d.getDate()+i); weekDays.push(d); }

        // Fixed academic periods (start/end in minutes from midnight) + labels
        const t = (h,m)=> h*60+m;
        const PERIODS = [
            { s:t(8,10),  e:t(9,20),  range:'من الساعة (8:10) إلى الساعة (9:20)',  name:'الفترة الصباحية (1)' },
            { s:t(9,30),  e:t(10,40), range:'من الساعة (9:30) إلى الساعة (10:40)', name:'الفترة الصباحية (2)' },
            { s:t(10,50), e:t(12,0),  range:'من الساعة (10:50) إلى الساعة (12:00)',name:'الفترة الصباحية (3)' },
            { s:t(12,20), e:t(13,25), range:'من الساعة (12:20) إلى الساعة (1:25)', name:'الفترة المسائية (1)' },
            { s:t(13,35), e:t(14,40), range:'من الساعة (1:35) إلى الساعة (2:40)',  name:'الفترة المسائية (2)' },
            { s:t(14,50), e:t(15,55), range:'من الساعة (2:50) إلى الساعة (3:55)',  name:'الفترة المسائية (3)' },
            { s:t(16,0),  e:t(17,15), range:'من الساعة (4:00) إلى الساعة (5:15)',  name:'الفترة المسائية (4)' },
        ];
        const periodIndex = mins => {
            for(let p=0;p<PERIODS.length;p++){ if(mins < PERIODS[p].e) return p; }
            return PERIODS.length-1;
        };

        // Bucket each session into its day + period
        const cellMap = {}; // key: dayIndex + '|' + periodIndex
        weekDays.forEach((d,i)=>{
            onDay(d).forEach(s=>{
                const dt=parse(s.at); const mins=dt.getHours()*60+dt.getMinutes();
                const p=periodIndex(mins);
                (cellMap[i+'|'+p] = cellMap[i+'|'+p] || []).push(s);
            });
        });

        // Header: day + period columns (time range + period name)
        let head = `<th style="padding:10px 6px;background:#0071AA;color:#fff;font-size:12px;font-weight:700;border:1px solid #fff;width:80px;">اليوم<br><span style="font-size:10px;opacity:.85;">الفترة</span></th>`;
        PERIODS.forEach(p=>{
            head += `<th style="padding:8px 6px;background:#0071AA;color:#fff;font-size:11px;font-weight:700;border:1px solid #fff;line-height:1.5;">
                ${p.range}<br><span style="font-size:11px;font-weight:600;opacity:.9;">${p.name}</span>
            </th>`;
        });

        // Body rows (one per day)
        let body='';
        weekDays.forEach((d,i)=>{
            const today = sameDay(d,TODAY);
            let row = `<td style="padding:10px 6px;text-align:center;font-size:13px;font-weight:700;color:#fff;background:${today?'#005a88':'#0071AA'};border:1px solid #fff;line-height:1.4;">
                ${DAY_NAMES[i]}
            </td>`;
            PERIODS.forEach((p,pi)=>{
                const items = cellMap[i+'|'+pi] || [];
                const inner = items.map(s=>{
                    return `<div style="background:#eff6ff;border-right:3px solid #0071AA;border-radius:6px;padding:6px 8px;margin-bottom:4px;line-height:1.35;">
                        <div style="font-size:12px;font-weight:700;color:#1e3a8a;">${s.title}</div>
                        ${s.subject?`<div style="font-size:10px;color:#64748b;">${s.subject}</div>`:''}
                    </div>`;
                }).join('');
                row += `<td style="height:80px;padding:5px;vertical-align:top;border:1px solid #d6e4f0;${today?'background:#f8fdff;':''}">${inner}</td>`;
            });
            body += `<tr>${row}</tr>`;
        });

        return `<div style="padding:14px;overflow-x:auto;">
            <table style="width:100%;min-width:1000px;border-collapse:collapse;table-layout:fixed;">
                <thead><tr>${head}</tr></thead>
                <tbody>${body}</tbody>
            </table>
        </div>`;
    }

    function renderMonth(){
        const y=cur.getFullYear(), m=cur.getMonth();
        document.getElementById('calTitle').textContent = MONTH_NAMES[m]+' '+y;
        const firstDow=new Date(y,m,1).getDay();
        const dim=new Date(y,m+1,0).getDate();
        const prevDim=new Date(y,m,0).getDate();
        let cells=[];
        for(let i=firstDow-1;i>=0;i--) cells.push({d:prevDim-i,cur:false});
        for(let d=1;d<=dim;d++) cells.push({d,cur:true});
        let fill=42-cells.length; for(let d=1;d<=fill;d++) cells.push({d,cur:false});

        const headers = DAY_NAMES.map(d=>`<div style="padding:10px 4px;text-align:center;font-size:11px;font-weight:700;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb;">${d}</div>`).join('');

        const grid = cells.map(cell=>{
            if(!cell.cur) return `<div style="min-height:104px;padding:6px;border-left:1px solid #f1f5f9;border-bottom:1px solid #f1f5f9;background:#fafafa;"><span style="font-size:13px;color:#d1d5db;">${cell.d}</span></div>`;
            const date=new Date(y,m,cell.d);
            const isToday=sameDay(date,TODAY);
            const items=onDay(date);
            const chips=items.slice(0,3).map(s=>`<div style="background:#eff6ff;color:#1e3a8a;border-right:2px solid #0071AA;font-size:10px;font-weight:600;padding:2px 5px;border-radius:3px;margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${fmtTime(parse(s.at))} ${s.title}</div>`).join('');
            const more=items.length-3>0?`<div style="font-size:10px;color:#7c3aed;font-weight:700;">+${items.length-3} أخرى</div>`:'';
            const num=isToday?`display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;background:#0071AA;color:white;border-radius:50%;font-weight:700;font-size:13px;`:`font-size:13px;font-weight:500;color:#374151;`;
            return `<div style="min-height:104px;padding:6px;border-left:1px solid #f1f5f9;border-bottom:1px solid #f1f5f9;background:${isToday?'#eff6ff':'white'};">
                <div style="margin-bottom:4px;text-align:left;"><span style="${num}">${cell.d}</span></div>
                ${chips}${more}
            </div>`;
        }).join('');

        return `<div style="display:grid;grid-template-columns:repeat(7,1fr);">${headers}</div>
                <div style="display:grid;grid-template-columns:repeat(7,1fr);">${grid}</div>`;
    }

    function render(){
        const body=document.getElementById('calBody');
        if(view==='list')      body.innerHTML=renderList();
        else if(view==='week') body.innerHTML=renderWeek();
        else                   body.innerHTML=renderMonth();
        // Filter count indicator
        const cntEl=document.getElementById('calFilterCount');
        if(cntEl){
            const n=filtered().length, total=SESSIONS.length;
            cntEl.textContent = (n===total) ? `${total} جلسة` : `${n} من ${total}`;
        }
    }

    render();
})();
</script>
</div>{{-- /ctab-sessions --}}

{{-- Tab switcher --}}
<script>
function switchClassTab(tab){
    ['students','sessions','terms'].forEach(t=>{
        const pane=document.getElementById('ctab-'+t);
        const btn=document.getElementById('ctab-btn-'+t);
        if(pane) pane.style.display = (t===tab)?'block':'none';
        if(btn){
            const on=(t===tab);
            btn.style.background = on?'white':'transparent';
            btn.style.color = on?'#1e293b':'#64748b';
            btn.style.boxShadow = on?'0 1px 4px rgba(0,0,0,.08)':'none';
        }
    });
}

// Open the tab named in the URL hash (e.g. #terms after adding a term/subject)
(function(){
    const h = (location.hash || '').replace('#','');
    if (['students','sessions','terms'].includes(h) && document.getElementById('ctab-btn-'+h)) {
        switchClassTab(h);
    }
})();
</script>

{{-- ══ MODAL: Generate Sessions ══ --}}
<template x-teleport="body">
<div x-show="sessionModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="sessionModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:520px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <div style="background:linear-gradient(135deg,#0071AA,#004d77);border-radius:18px 18px 0 0;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <h3 style="font-size:15px;font-weight:700;color:white;margin:0;">إنشاء جلسات للمجموعة</h3>
            <button @click="sessionModal=false" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:white;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.classes.generate-sessions', $class->id) }}" method="POST" style="display:flex;flex-direction:column;overflow:hidden;flex:1;">
            @csrf
            @php
                // subject_id => term end date (for the auto-end note)
                $termEndMap = $classSubjects->mapWithKeys(fn($cs) => [$cs->id => optional($cs->term?->end_date)->format('Y-m-d')]);
                $classEnd = $class->end_date?->format('Y-m-d');
                // subject_id => assigned teachers [{id,name}] (subject.teacher + subject.teachers pivot)
                $subjectTeachersMap = $classSubjects->mapWithKeys(function ($cs) {
                    $teachers = $cs->teachers->isNotEmpty()
                        ? $cs->teachers
                        : ($cs->teacher ? collect([$cs->teacher]) : collect());
                    return [$cs->id => $teachers->map(fn($t) => ['id' => $t->id, 'name' => $t->name])->values()];
                });
            @endphp
            <div x-data="{
                    subjectId: '',
                    termEnds: {{ $termEndMap->toJson() }},
                    subjectTeachers: {{ $subjectTeachersMap->toJson() }},
                    classEnd: '{{ $classEnd }}',
                    isDiploma: {{ ($class->program && $class->program->type === 'diploma') ? 'true' : 'false' }},
                    get endDate() { return this.isDiploma ? (this.termEnds[this.subjectId] || '') : this.classEnd; },
                    get teacherOptions() { return this.isDiploma ? (this.subjectTeachers[this.subjectId] || []) : @js($teachers->map(fn($t)=>['id'=>$t->id,'name'=>$t->name])->values()); }
                }" style="padding:20px;display:flex;flex-direction:column;gap:14px;overflow-y:auto;flex:1;">

                @if($class->program && $class->program->type === 'diploma')
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">المقرر *</label>
                    <select name="subject_id" x-model="subjectId" required style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:white;">
                        <option value="">— اختر مقرر —</option>
                        @foreach($classSubjects as $cs)
                            <option value="{{ $cs->id }}">{{ ($cs->name_ar ?: $cs->name_en) }} ({{ $cs->code }})</option>
                        @endforeach
                    </select>
                    @if($classSubjects->isEmpty())
                    <p style="font-size:11px;color:#dc2626;margin-top:6px;">أضف مواد لأرباع المجموعة أولاً.</p>
                    @endif
                </div>
                @endif

                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">المدرب *</label>
                    <select name="teacher_id" required style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:white;">
                        <option value="">— اختر المدرب —</option>
                        <template x-for="t in teacherOptions" :key="t.id">
                            <option :value="t.id" x-text="t.name"></option>
                        </template>
                    </select>
                    <template x-if="isDiploma && subjectId && teacherOptions.length === 0">
                        <p style="font-size:11px;color:#dc2626;margin-top:6px;">لا يوجد مدرب معيّن لهذا المقرر — عيّن مدربًا للمقرر أولاً.</p>
                    </template>
                    <template x-if="!isDiploma && teacherOptions.length === 0">
                        <p style="font-size:11px;color:#dc2626;margin-top:6px;">لا يوجد مدربون مسجّلون لهذه الدورة — سجّل مدربًا للبرنامج أولاً.</p>
                    </template>
                </div>

                {{-- Weekly days --}}
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">أيام الأسبوع *</label>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;">
                        @foreach(['الأحد'=>0,'الإثنين'=>1,'الثلاثاء'=>2,'الأربعاء'=>3,'الخميس'=>4,'الجمعة'=>5,'السبت'=>6] as $dName=>$dVal)
                        <label style="display:inline-flex;align-items:center;gap:5px;padding:6px 10px;border:1.5px solid #e2e8f0;border-radius:8px;cursor:pointer;font-size:12px;font-weight:600;color:#475569;">
                            <input type="checkbox" name="days[]" value="{{ $dVal }}" style="accent-color:#0071AA;">
                            {{ $dName }}
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Start date + time --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">تبدأ من *</label>
                        <input type="date" name="start_date" required value="{{ $class->start_date?->format('Y-m-d') }}" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الوقت *</label>
                        <input type="time" name="time" required style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                </div>

                {{-- Auto end note (when an end date is available) --}}
                <div x-show="endDate" style="display:flex;align-items:center;gap:8px;background:#eff6ff;border:1px solid #dbeafe;border-radius:10px;padding:9px 12px;font-size:12px;color:#1e40af;">
                    <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>الجلسات أسبوعية وتنتهي تلقائيًا بنهاية {{ $class->program && $class->program->type === 'diploma' ? 'الربع' : 'المجموعة' }}: <strong x-text="endDate"></strong></span>
                </div>

                {{-- Manual end date (when no auto end date is available) --}}
                <div x-show="!endDate" x-cloak>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">تنتهي في *</label>
                    <input type="date" name="end_date" :required="!endDate" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    <p style="font-size:11px;color:#b45309;margin-top:6px;">@if($class->program && $class->program->type === 'diploma')لا يوجد تاريخ نهاية للربع — حدّد تاريخ نهاية الجلسات.@else لا يوجد تاريخ نهاية للمجموعة — حدّد تاريخ نهاية الجلسات.@endif</p>
                </div>

            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9;flex-shrink:0;">
                <button type="button" @click="sessionModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#0071AA,#004d77);border:none;border-radius:10px;cursor:pointer;">إنشاء الجلسات</button>
            </div>
        </form>
    </div>
</div>
</template>

{{-- ══ MODAL: Add Term (scoped to this class) ══ --}}
<template x-teleport="body">
<div x-show="termModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="termModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:460px;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <div style="background:linear-gradient(135deg,#1a3a5c,#2563eb);border-radius:18px 18px 0 0;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:15px;font-weight:700;color:white;margin:0;">إضافة ربع دراسي للمجموعة</h3>
            <button @click="termModal=false" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:white;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.terms.store') }}" method="POST">
            @csrf
            <input type="hidden" name="program_id" value="{{ $class->program_id }}">
            <input type="hidden" name="class_id" value="{{ $class->id }}">
            <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">اسم الربع *</label>
                        <input type="text" name="name_ar" required placeholder="الفصل التدريبي الأول"
                               style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">رقم الربع *</label>
                        <input type="number" name="term_number" required min="1" value="{{ $class->terms->count() + 1 }}"
                               style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">تاريخ البداية</label>
                        <input type="date" name="start_date" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">تاريخ النهاية</label>
                        <input type="date" name="end_date" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الحالة</label>
                    <select name="status" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:white;">
                        <option value="upcoming">قادم</option>
                        <option value="active">نشط</option>
                        <option value="completed">مكتمل</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9;">
                <button type="button" @click="termModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#1a3a5c,#2563eb);border:none;border-radius:10px;cursor:pointer;">إضافة الربع</button>
            </div>
        </form>
    </div>
</div>
</template>

{{-- ══ MODAL: Attach existing program subject ══ --}}
<template x-teleport="body">
<div x-show="attachModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="attachModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:460px;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <div style="background:linear-gradient(135deg,#7c3aed,#8b5cf6);border-radius:18px 18px 0 0;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <h3 style="font-size:15px;font-weight:700;color:white;margin:0;">اختيار مقرر من البرنامج</h3>
                <p style="font-size:11px;color:rgba(255,255,255,.7);margin:2px 0 0;" x-text="currentTermName"></p>
            </div>
            <button @click="attachModal=false" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:white;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.classes.attach-subject', $class->id) }}" method="POST">
            @csrf
            <input type="hidden" name="term_id" :value="currentTermId">
            <div style="padding:20px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">المقرر</label>
                <select name="subject_id" required style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:white;">
                    <option value="">— اختر مقرر —</option>
                    @foreach($programSubjects->whereNotIn('id', $usedSubjectIds) as $s)
                        <option value="{{ $s->id }}">{{ ($s->name_ar ?: $s->name_en) }} ({{ $s->code }})</option>
                    @endforeach
                </select>
                <p style="font-size:11px;color:#94a3b8;margin-top:8px;">المواد المُسندة لربع آخر في هذه المجموعة لا تظهر هنا. سيتم ربط المقرر بهذا الربع فقط.</p>
                @if($programSubjects->whereNotIn('id', $usedSubjectIds)->isEmpty())
                <p style="font-size:12px;color:#dc2626;margin-top:6px;">كل مواد الدبلومة مُسندة بالفعل لأرباع هذه المجموعة.</p>
                @endif
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9;">
                <button type="button" @click="attachModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#7c3aed,#8b5cf6);border:none;border-radius:10px;cursor:pointer;">إضافة المقرر</button>
            </div>
        </form>
    </div>
</div>
</template>

{{-- ══ MODAL: Add Subject (new, scoped to this class) ══ --}}
<template x-teleport="body">
<div x-show="subjectModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="subjectModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:500px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <div style="background:linear-gradient(135deg,#7c3aed,#8b5cf6);border-radius:18px 18px 0 0;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div>
                <h3 style="font-size:15px;font-weight:700;color:white;margin:0;">إضافة مقرر جديد</h3>
                <p style="font-size:11px;color:rgba(255,255,255,.7);margin:2px 0 0;" x-text="currentTermName"></p>
            </div>
            <button @click="subjectModal=false" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:white;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.subjects.store') }}" method="POST" style="display:flex;flex-direction:column;overflow:hidden;flex:1;">
            @csrf
            <input type="hidden" name="program_id" value="{{ $class->program_id }}">
            <input type="hidden" name="class_id" value="{{ $class->id }}">
            <input type="hidden" name="term_id" :value="currentTermId">
            <div style="padding:20px;display:flex;flex-direction:column;gap:14px;overflow-y:auto;flex:1;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">كود المقرر *</label>
                        <input type="text" name="code" required dir="ltr" placeholder="MATH-101"
                               style="width:100%;padding:9px 12px;font-size:13px;font-family:monospace;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الساعات المعتمدة</label>
                        <input type="number" name="credits" min="1" max="12" value="3"
                               style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الاسم العربي *</label>
                    <input type="text" name="name_ar" required placeholder="مبادئ الرياضيات"
                           style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الاسم الإنجليزي</label>
                    <input type="text" name="name_en" dir="ltr" placeholder="Mathematics Fundamentals"
                           style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">المدرب (اختياري)</label>
                        <select name="teacher_id" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:white;">
                            <option value="">— بدون مدرب —</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الحالة</label>
                        <select name="status" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:white;">
                            <option value="inactive" selected>مقفل</option>
                            <option value="active">نشط</option>
                        </select>
                    </div>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9;flex-shrink:0;">
                <button type="button" @click="subjectModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#7c3aed,#8b5cf6);border:none;border-radius:10px;cursor:pointer;">إضافة المقرر</button>
            </div>
        </form>
    </div>
</div>
</template>

{{-- ══ MODAL: Edit Subject ══ --}}
<template x-teleport="body">
<div x-show="editSubjectModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="editSubjectModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:460px;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <div style="background:linear-gradient(135deg,#2563eb,#3b82f6);border-radius:18px 18px 0 0;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:15px;font-weight:700;color:white;margin:0;">تعديل المقرر</h3>
            <button @click="editSubjectModal=false" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:white;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form :action="'/admin/subjects/' + editSubject.id" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="program_id" value="{{ $class->program_id }}">
            <input type="hidden" name="class_id" value="{{ $class->id }}">
            <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">كود المقرر *</label>
                        <input type="text" name="code" required dir="ltr" x-model="editSubject.code"
                               style="width:100%;padding:9px 12px;font-size:13px;font-family:monospace;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الساعات المعتمدة</label>
                        <input type="number" name="credits" min="1" max="12" x-model="editSubject.credits"
                               style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الاسم العربي *</label>
                    <input type="text" name="name_ar" required x-model="editSubject.name_ar"
                           style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الاسم الإنجليزي</label>
                    <input type="text" name="name_en" dir="ltr" x-model="editSubject.name_en"
                           style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9;">
                <button type="button" @click="editSubjectModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#2563eb,#3b82f6);border:none;border-radius:10px;cursor:pointer;">حفظ</button>
            </div>
        </form>
    </div>
</div>
</template>

{{-- ══ MODAL: Delete Subject ══ --}}
<template x-teleport="body">
<div x-show="deleteSubjectModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="deleteSubjectModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:400px;padding:24px;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <p style="font-size:14px;color:#1e293b;margin-bottom:18px;">هل تريد حذف المقرر <strong x-text="currentSubjectName"></strong>؟</p>
        <form :action="'/admin/subjects/' + currentSubjectId" method="POST" style="display:flex;justify-content:flex-end;gap:8px;">
            @csrf @method('DELETE')
            <button type="button" @click="deleteSubjectModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
            <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:#ef4444;border:none;border-radius:10px;cursor:pointer;">حذف</button>
        </form>
    </div>
</div>
</template>

{{-- ══ MODAL: Assign Teachers ══ --}}
<template x-teleport="body">
<div x-show="teacherModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="teacherModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:440px;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <div style="background:linear-gradient(135deg,#4338ca,#6366f1);border-radius:18px 18px 0 0;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <h3 style="font-size:15px;font-weight:700;color:white;margin:0;">تعيين المدربين</h3>
                <p style="font-size:11px;color:rgba(255,255,255,.7);margin:2px 0 0;" x-text="currentSubjectName"></p>
            </div>
            <button @click="teacherModal=false" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:white;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form :action="'/admin/subjects/' + currentSubjectId + '/assign-teachers'" method="POST">
            @csrf
            <div style="padding:20px;display:flex;flex-direction:column;gap:10px;max-height:50vh;overflow-y:auto;">
                @foreach($teachers as $teacher)
                <label style="display:flex;align-items:center;gap:10px;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;">
                    <input type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}"
                           :checked="currentTeacherIds.includes('{{ $teacher->id }}')"
                           style="width:15px;height:15px;accent-color:#6366f1;">
                    <span style="font-size:13px;font-weight:600;color:#1e293b;">{{ $teacher->name }}</span>
                </label>
                @endforeach
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9;">
                <button type="button" @click="teacherModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#4338ca,#6366f1);border:none;border-radius:10px;cursor:pointer;">حفظ</button>
            </div>
        </form>
    </div>
</div>
</template>

</div>

{{-- ══ Students Manage Modal (reused from index) ══ --}}
<div id="studentsModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1100;align-items:center;justify-content:center;">
<div style="background:white;border-radius:18px;width:100%;max-width:580px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,.25);overflow:hidden;">
    <div style="padding:18px 22px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;background:linear-gradient(135deg,#0071AA,#0ea5e9);">
        <div>
            <div style="color:#fff;font-size:15px;font-weight:800;" id="sm-title">طلاب المجموعة</div>
            <div style="color:rgba(255,255,255,.7);font-size:12px;margin-top:2px;" id="sm-subtitle"></div>
        </div>
        <button onclick="document.getElementById('studentsModal').style.display='none'" style="background:rgba(255,255,255,.2);border:none;border-radius:8px;width:32px;height:32px;color:#fff;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
    </div>
    <div style="display:flex;border-bottom:1px solid #e2e8f0;flex-shrink:0;">
        <button id="tab-current" onclick="showTab('current')" style="flex:1;padding:11px;font-size:13px;font-weight:700;border:none;cursor:pointer;background:#f0f9ff;color:#0369a1;border-bottom:2px solid #0369a1;">الطلاب الحاليون</button>
        <button id="tab-add" onclick="showTab('add')" style="flex:1;padding:11px;font-size:13px;font-weight:600;border:none;cursor:pointer;background:#f8fafc;color:#64748b;border-bottom:2px solid transparent;">إضافة طلاب</button>
    </div>
    <div id="pane-current" style="flex:1;overflow-y:auto;padding:16px;">
        <div id="sm-loading" style="text-align:center;padding:32px;color:#94a3b8;font-size:13px;">جاري التحميل...</div>
        <div id="sm-list" style="display:none;"></div>
        <div id="sm-empty" style="display:none;text-align:center;padding:32px;color:#94a3b8;font-size:13px;">لا يوجد طلاب في هذه المجموعة</div>
    </div>
    <div id="pane-add" style="display:none;flex:1;overflow-y:auto;padding:16px;">
        <input id="sm-search" placeholder="بحث بالاسم أو الرقم الوطني..." oninput="filterAvailable()" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;margin-bottom:12px;box-sizing:border-box;">
        <div id="sm-available-loading" style="text-align:center;padding:32px;color:#94a3b8;font-size:13px;">جاري التحميل...</div>
        <div id="sm-available-list" style="display:none;"></div>
        <div id="sm-available-empty" style="display:none;text-align:center;padding:24px;color:#94a3b8;font-size:13px;">لا يوجد طلاب متاحون للإضافة</div>
        <div id="sm-add-actions" style="display:none;padding-top:12px;border-top:1px solid #f1f5f9;margin-top:12px;">
            <button onclick="assignSelected()" style="padding:9px 20px;background:linear-gradient(135deg,#0071AA,#0ea5e9);color:#fff;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;">إضافة المحددين</button>
        </div>
    </div>
</div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';
let _classId = {{ $class->id }};
let _allAvailable = [];
let _selectedIds = new Set();

function showTab(tab) {
    document.getElementById('pane-current').style.display = tab === 'current' ? 'block' : 'none';
    document.getElementById('pane-add').style.display     = tab === 'add'     ? 'block' : 'none';
    document.getElementById('tab-current').style.cssText  = tab === 'current'
        ? 'flex:1;padding:11px;font-size:13px;font-weight:700;border:none;cursor:pointer;background:#f0f9ff;color:#0369a1;border-bottom:2px solid #0369a1;'
        : 'flex:1;padding:11px;font-size:13px;font-weight:600;border:none;cursor:pointer;background:#f8fafc;color:#64748b;border-bottom:2px solid transparent;';
    document.getElementById('tab-add').style.cssText = tab === 'add'
        ? 'flex:1;padding:11px;font-size:13px;font-weight:700;border:none;cursor:pointer;background:#f0f9ff;color:#0369a1;border-bottom:2px solid #0369a1;'
        : 'flex:1;padding:11px;font-size:13px;font-weight:600;border:none;cursor:pointer;background:#f8fafc;color:#64748b;border-bottom:2px solid transparent;';
}
function openStudentsModal(classId, className) {
    _classId = classId; _selectedIds = new Set();
    document.getElementById('sm-subtitle').textContent = className;
    document.getElementById('sm-search').value = '';
    showTab('current');
    document.getElementById('studentsModal').style.display = 'flex';
    loadCurrentStudents(); loadAvailableStudents();
}
function loadCurrentStudents() {
    document.getElementById('sm-loading').style.display = 'block';
    document.getElementById('sm-list').style.display = 'none';
    document.getElementById('sm-empty').style.display = 'none';
    fetch(`/admin/classes/${_classId}/students`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json()).then(d => {
            document.getElementById('sm-loading').style.display = 'none';
            if (!d.students.length) { document.getElementById('sm-empty').style.display = 'block'; return; }
            const list = document.getElementById('sm-list');
            list.innerHTML = d.students.map(s => `
                <div id="student-row-${s.id}" style="display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;border:1px solid #f1f5f9;margin-bottom:8px;">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#a855f7);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;flex-shrink:0;">${s.name.charAt(0)}</div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:700;color:#1e293b;">${s.name}</div>
                        <div style="font-size:11px;color:#94a3b8;">${s.national_id || s.email || ''}</div>
                    </div>
                    <button onclick="removeStudent(${s.id})" style="padding:5px 12px;font-size:11px;color:#dc2626;background:#fff1f2;border:1px solid #fecaca;border-radius:7px;cursor:pointer;font-weight:600;flex-shrink:0;">إزالة</button>
                </div>`).join('');
            list.style.display = 'block';
        });
}
function removeStudent(studentId) {
    if (!confirm('إزالة الطالب من المجموعة؟')) return;
    fetch(`/admin/classes/${_classId}/remove-student`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ student_id: studentId })
    }).then(r => r.json()).then(d => {
        if (d.success) { location.reload(); }
    });
}
function loadAvailableStudents() {
    document.getElementById('sm-available-loading').style.display = 'block';
    document.getElementById('sm-available-list').style.display = 'none';
    document.getElementById('sm-available-empty').style.display = 'none';
    document.getElementById('sm-add-actions').style.display = 'none';
    fetch(`/admin/classes/${_classId}/available-students`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json()).then(d => {
            document.getElementById('sm-available-loading').style.display = 'none';
            _allAvailable = d.students.filter(s => !s.class_id || s.class_id != _classId);
            renderAvailable(_allAvailable);
        });
}
function renderAvailable(students) {
    if (!students.length) {
        document.getElementById('sm-available-empty').style.display = 'block';
        document.getElementById('sm-available-list').style.display = 'none';
        document.getElementById('sm-add-actions').style.display = 'none';
        return;
    }
    document.getElementById('sm-available-empty').style.display = 'none';
    const list = document.getElementById('sm-available-list');
    list.innerHTML = students.map(s => `
        <label style="display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;border:1px solid #f1f5f9;margin-bottom:8px;cursor:pointer;" onchange="updateAddBtn()">
            <input type="checkbox" value="${s.id}" class="avail-cb" ${_selectedIds.has(s.id)?'checked':''} style="width:15px;height:15px;accent-color:#0071AA;flex-shrink:0;">
            <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#0369a1,#0ea5e9);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;flex-shrink:0;">${s.name.charAt(0)}</div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:700;color:#1e293b;">${s.name}</div>
                <div style="font-size:11px;color:#94a3b8;">${s.national_id || s.email || ''}</div>
            </div>
            ${s.class_id ? '<span style="font-size:10px;color:#f59e0b;background:#fef3c7;padding:2px 7px;border-radius:20px;flex-shrink:0;">في مجموعة أخرى</span>' : ''}
        </label>`).join('');
    list.style.display = 'block';
    updateAddBtn();
}
function filterAvailable() {
    const q = document.getElementById('sm-search').value.toLowerCase();
    renderAvailable(_allAvailable.filter(s => s.name.toLowerCase().includes(q) || (s.national_id||'').includes(q)));
}
function updateAddBtn() {
    _selectedIds = new Set([...document.querySelectorAll('.avail-cb:checked')].map(c => parseInt(c.value)));
    document.getElementById('sm-add-actions').style.display = _selectedIds.size ? 'block' : 'none';
}
function assignSelected() {
    if (!_selectedIds.size) return;
    fetch(`/admin/classes/${_classId}/assign-students`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ student_ids: [..._selectedIds] })
    }).then(r => r.json()).then(d => {
        if (d.success) { location.reload(); }
    });
}
</script>
@endsection
