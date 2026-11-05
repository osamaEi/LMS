@extends('layouts.dashboard')

@section('title', 'المتدربون ')

@section('content')
<div style="direction:rtl;font-family:'Segoe UI',sans-serif;">

@if(session('success'))
    <div role="status" style="padding:14px 18px;margin-bottom:16px;border-radius:12px;background:#dcfce7;color:#166534;">{{ session('success') }}</div>
@endif

{{-- Header --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 55%,#0071AA 100%);border-radius:20px;padding:24px 28px;margin-bottom:24px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-60px;left:-60px;width:220px;height:220px;background:rgba(255,255,255,.05);border-radius:50%;pointer-events:none;"></div>
    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:48px;height:48px;background:rgba(255,255,255,.12);border-radius:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="22" height="22" fill="white" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <h1 style="color:white;font-size:20px;font-weight:700;margin:0;">المتدربون </h1>
                <p style="color:rgba(255,255,255,.5);font-size:12px;margin:2px 0 0;">طلاب مجموعاتك — اضغط "التقرير" لتعديل الدرجات والواجبات</p>
            </div>
        </div>
        <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:8px 16px;text-align:center;min-width:60px;">
            <div style="font-size:20px;font-weight:700;color:#fde68a;line-height:1;">{{ $totalStudents }}</div>
            <div style="font-size:11px;color:rgba(255,255,255,.5);margin-top:2px;">إجمالي الطلاب</div>
        </div>
    </div>
</div>

@if($totalStudents === 0)
<div style="background:white;border-radius:18px;border:1px solid #e5e7eb;padding:60px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,.06);">
    <svg style="width:56px;height:56px;color:#d1d5db;margin:0 auto 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    <p style="font-size:15px;font-weight:600;color:#475569;margin-bottom:4px;">لا يوجد طلاب بعد</p>
    <p style="font-size:13px;color:#94a3b8;">سيظهر الطلاب هنا بعد إسنادهم لمجموعاتك أو حضورهم جلساتك</p>
</div>
@else

@php
    $currentCount = $grouped->filter(fn ($g) => $g['class_id'] && ! $g['is_past'])->count();
    $pastCount    = $grouped->filter(fn ($g) => $g['is_past'])->count();
@endphp

<style>
    .sf-card { background:#fff;border:1px solid #e5e7eb;border-radius:18px;padding:18px;margin-bottom:18px;box-shadow:0 2px 12px rgba(0,0,0,.05); }
    .sf-top { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px; }
    .sf-search { position:relative;flex:1 1 320px;max-width:460px; }
    #student-search { width:100%;padding:12px 44px 12px 14px;border:1px solid #dbe4ee;border-radius:12px;font:inherit;font-size:13px;color:#1e293b;background:#f8fafc;transition:border-color .15s,box-shadow .15s,background .15s; }
    #student-search:focus { outline:none;border-color:#0071AA;background:#fff;box-shadow:0 0 0 3px rgba(0,113,170,.12); }

    .sf-status { display:inline-flex;gap:4px;padding:4px;background:#f1f5f9;border-radius:12px; }
    .sf-status-btn { border:0;background:transparent;color:#475569;font:inherit;font-size:12px;font-weight:700;padding:8px 14px;border-radius:9px;cursor:pointer;display:inline-flex;align-items:center;gap:6px; }
    .sf-status-btn[aria-pressed="true"] { background:#fff;color:#0f172a;box-shadow:0 1px 4px rgba(15,23,42,.1); }
    .sf-status-btn:focus-visible { outline:3px solid #38bdf8;outline-offset:2px; }
    .sf-dot { width:8px;height:8px;border-radius:50%;display:inline-block;flex-shrink:0; }
    .sf-dot-current { background:#16a34a; }
    .sf-dot-past { background:#dc2626; }
    .sf-dot-none { background:#94a3b8; }

    .sf-divider { height:1px;background:#eef2f7;margin:16px 0 12px; }
    .sf-label { font-size:11px;font-weight:700;color:#94a3b8;margin:0 2px 8px; }

    .student-group-tabs { display:flex;gap:8px;flex-wrap:wrap; }
    .student-group-tab { border:1px solid #dbe4ee;border-radius:999px;padding:8px 14px;background:#fff;color:#475569;font:inherit;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;display:inline-flex;align-items:center;gap:8px;transition:background .15s,border-color .15s,color .15s; }
    .student-group-tab[hidden] { display:none; }
    .student-group-tab:focus-visible { outline:3px solid #38bdf8;outline-offset:3px; }
    .student-group-count { font-size:11px;font-weight:700;padding:1px 8px;border-radius:999px;background:rgba(15,23,42,.06); }

    .student-group-tab.is-current { border-color:#bbf7d0;background:#f0fdf4;color:#166534; }
    .student-group-tab.is-current:hover { background:#dcfce7; }
    .student-group-tab.is-current[aria-selected="true"] { background:#16a34a;border-color:#16a34a;color:#fff; }

    .student-group-tab.is-past { border-color:#fecaca;background:#fef2f2;color:#991b1b; }
    .student-group-tab.is-past:hover { background:#fee2e2; }
    .student-group-tab.is-past[aria-selected="true"] { background:#dc2626;border-color:#dc2626;color:#fff; }

    .student-group-tab.is-none[aria-selected="true"] { background:#475569;border-color:#475569;color:#fff; }
    .student-group-tab[aria-selected="true"] .sf-dot { background:#fff; }
    .student-group-tab[aria-selected="true"] .student-group-count { background:rgba(255,255,255,.22); }

    .sf-badge { border-radius:999px;padding:3px 10px;font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:6px; }
    .sf-badge-current { background:#dcfce7;color:#166534; }
    .sf-badge-past { background:#fee2e2;color:#991b1b; }
</style>

{{-- Filter --}}
<div class="sf-card">
    <div class="sf-top">
        <div class="sf-search">
            <label for="student-search" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);">ابحث عن طالب</label>
            <svg aria-hidden="true" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="2"
                 style="position:absolute;top:50%;inset-inline-start:14px;transform:translateY(-50%);pointer-events:none;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
            </svg>
            <input type="search" id="student-search" autocomplete="off"
                   placeholder="ابحث بالاسم أو البريد أو الهوية أو الجوال…">
        </div>

        <div class="sf-status" role="group" aria-label="حالة المجموعة">
            <button type="button" class="sf-status-btn" data-status="all" aria-pressed="true">الكل</button>
            <button type="button" class="sf-status-btn" data-status="current" aria-pressed="false">
                <span class="sf-dot sf-dot-current" aria-hidden="true"></span> الحالية ({{ $currentCount }})
            </button>
            <button type="button" class="sf-status-btn" data-status="past" aria-pressed="false">
                <span class="sf-dot sf-dot-past" aria-hidden="true"></span> السابقة ({{ $pastCount }})
            </button>
        </div>
    </div>

    <p id="student-search-status" role="status" aria-live="polite"
       style="margin:8px 2px 0;font-size:12px;color:#64748b;min-height:16px;"></p>

    <div class="sf-divider"></div>
    <p class="sf-label">المجموعات</p>
    <div role="tablist" aria-label="مجموعات الطلاب" class="student-group-tabs">
        @foreach($grouped as $group)
            @php $state = ! $group['class_id'] ? 'none' : ($group['is_past'] ? 'past' : 'current'); @endphp
            <button type="button" class="student-group-tab is-{{ $state }}" role="tab" data-status="{{ $state }}"
                    id="student-group-tab-{{ $loop->index }}" aria-controls="student-group-panel-{{ $loop->index }}"
                    aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}"
                    title="{{ $state === 'past' ? 'مجموعة سابقة' : ($state === 'current' ? 'مجموعة حالية' : '') }}">
                <span class="sf-dot sf-dot-{{ $state }}" aria-hidden="true"></span>
                {{ $group['name'] }}
                <span class="student-group-count">{{ count($group['students']) }}</span>
            </button>
        @endforeach
    </div>
    <p id="student-group-empty" hidden style="margin:10px 2px 0;font-size:12px;color:#94a3b8;">لا توجد مجموعات بهذه الحالة</p>
</div>

@foreach($grouped as $group)
<div role="tabpanel" id="student-group-panel-{{ $loop->index }}" aria-labelledby="student-group-tab-{{ $loop->index }}"
     tabindex="0" @if(!$loop->first) hidden @endif
     style="background:white;border-radius:18px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);margin-bottom:20px;">
    {{-- Class header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:14px 18px;background:#f8fafc;border-bottom:1px solid #eef2f7;">
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="width:30px;height:30px;border-radius:9px;background:#e0f2fe;color:#0071AA;display:flex;align-items:center;justify-content:center;font-size:14px;">🏫</span>
            <span style="font-weight:700;color:#1e293b;font-size:14px;">{{ $group['name'] }}</span>
            @if($group['class_id'])
                @if($group['is_past'])
                    <span class="sf-badge sf-badge-past"><span class="sf-dot sf-dot-past" aria-hidden="true"></span> سابقة</span>
                @else
                    <span class="sf-badge sf-badge-current"><span class="sf-dot sf-dot-current" aria-hidden="true"></span> حالية</span>
                @endif
            @endif
        </div>
        <div style="display:flex;align-items:center;flex-wrap:wrap;gap:12px;">
            <span style="background:#eff6ff;color:#1d4ed8;border-radius:999px;padding:3px 12px;font-size:11px;font-weight:700;">{{ count($group['students']) }} طالب</span>
            @if($group['class_id'])
                <form method="POST" action="{{ route('teacher.students.groups.report.send', $group['class_id']) }}" onsubmit="const button = this.querySelector('button'); button.disabled = true; button.textContent = 'جارٍ إرسال الدرجات...';" style="margin:0;">
                    @csrf
                    <button type="submit" style="background:#0071AA;color:#fff;border:0;padding:10px 14px;border-radius:9px;font:inherit;font-size:12px;font-weight:700;cursor:pointer;">إرسال الدرجات لكل طلاب المجموعة</button>
                </form>
            @endif
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:2px solid #f1f5f9;background:#fafafa;">
                    <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;width:40px;">#</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الاسم</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">البريد الإلكتروني</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">البرنامج</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الهوية</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الجوال</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">التقرير</th>
                </tr>
            </thead>
            <tbody>
                @foreach($group['students'] as $i => $student)
                <tr class="student-row"
                    data-search="{{ mb_strtolower(trim(implode(' ', array_filter([$student->name, $student->email, $student->national_id, $student->phone, $student->program->name ?? null])))) }}"
                    style="border-bottom:1px solid #f8fafc;transition:background .1s;" onmouseover="this.style.background='#f8faff'" onmouseout="this.style.background=''">
                    <td class="student-row-index" style="padding:13px 16px;color:#cbd5e1;font-size:11px;">{{ $i + 1 }}</td>
                    <td style="padding:13px 16px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#0071AA,#004d77);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:white;font-weight:700;font-size:13px;">
                                {{ mb_substr($student->name, 0, 1) }}
                            </div>
                            <span style="font-weight:600;color:#1e293b;">{{ $student->name }}</span>
                        </div>
                    </td>
                    <td style="padding:13px 16px;color:#475569;" dir="ltr">{{ $student->email }}</td>
                    <td style="padding:13px 16px;color:#64748b;">{{ $student->program->name ?? '—' }}</td>
                    <td style="padding:13px 16px;color:#64748b;" dir="ltr">{{ $student->national_id ?? '—' }}</td>
                    <td style="padding:13px 16px;color:#64748b;" dir="ltr">{{ $student->phone ?? '—' }}</td>
                    <td style="padding:13px 16px;">
                        <a href="{{ route('teacher.students.report.show', $student) }}"
                           style="display:inline-block;background:#0071AA;color:#fff;padding:6px 14px;border-radius:9px;text-decoration:none;font-size:12px;font-weight:700;">📊 التقرير</a>
                    </td>
                </tr>
                @endforeach
                <tr class="student-empty-row" hidden>
                    <td colspan="7" style="padding:28px 16px;text-align:center;color:#94a3b8;font-size:13px;">
                        لا يوجد طالب مطابق في هذه المجموعة
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endforeach

<script>
(() => {
    const tabs = Array.from(document.querySelectorAll('.student-group-tab'));
    function selectTab(selected) {
        tabs.forEach(tab => {
            const active = tab === selected;
            tab.setAttribute('aria-selected', String(active));
            tab.tabIndex = active ? 0 : -1;
            document.getElementById(tab.getAttribute('aria-controls')).hidden = !active;
        });
    }
    // Live search across every group, not just the visible tab. Each tab keeps
    // showing how many of its students match so hits in other groups are found.
    const search = document.getElementById('student-search');
    const status = document.getElementById('student-search-status');
    const panels = tabs.map(tab => document.getElementById(tab.getAttribute('aria-controls')));

    // Normalize Arabic so "أحمد" matches "احمد" and Arabic-Indic digits match ASCII.
    const normalize = value => (value || '').toLowerCase()
        .replace(/[ـً-ْ]/g, '')
        .replace(/[آأإٱ]/g, 'ا')
        .replace(/ى/g, 'ي')
        .replace(/ة/g, 'ه')
        .replace(/[٠-٩]/g, d => String.fromCharCode(d.charCodeAt(0) - 0x0630))
        .replace(/[۰-۹]/g, d => String.fromCharCode(d.charCodeAt(0) - 0x06C0))
        .replace(/\s+/g, ' ')
        .trim();

    const rowsByPanel = panels.map(panel => Array.from(panel.querySelectorAll('.student-row')).map(row => ({
        el: row,
        haystack: normalize(row.dataset.search),
        indexCell: row.querySelector('.student-row-index'),
    })));

    function runSearch() {
        const terms = normalize(search.value).split(' ').filter(Boolean);
        let total = 0;

        rowsByPanel.forEach((rows, panelIndex) => {
            let shown = 0;
            rows.forEach(row => {
                const match = terms.every(term => row.haystack.includes(term));
                row.el.hidden = !match;
                if (match) {
                    shown++;
                    if (row.indexCell) row.indexCell.textContent = shown;
                }
            });
            total += shown;

            const emptyRow = panels[panelIndex].querySelector('.student-empty-row');
            if (emptyRow) emptyRow.hidden = !(terms.length && shown === 0);

            const count = tabs[panelIndex].querySelector('.student-group-count');
            if (count) count.textContent = shown;
        });

        status.textContent = terms.length
            ? (total ? 'عدد النتائج: ' + total : 'لا توجد نتائج مطابقة')
            : '';
    }

    search.addEventListener('input', runSearch);
    search.addEventListener('keydown', event => {
        if (event.key === 'Escape') { search.value = ''; runSearch(); }
    });

    // Status filter: all / current (green) / previous (red).
    const statusButtons = Array.from(document.querySelectorAll('.sf-status-btn'));
    const groupEmpty = document.getElementById('student-group-empty');
    function applyStatus(status) {
        statusButtons.forEach(b => b.setAttribute('aria-pressed', String(b.dataset.status === status)));
        tabs.forEach(tab => {
            tab.hidden = !(status === 'all' || tab.dataset.status === status);
        });
        const visible = tabs.filter(tab => !tab.hidden);
        groupEmpty.hidden = visible.length > 0;
        const selected = tabs.find(tab => tab.getAttribute('aria-selected') === 'true');
        if (visible.length && (!selected || selected.hidden)) {
            selectTab(visible[0]);
        } else if (!visible.length) {
            panels.forEach(panel => { panel.hidden = true; });
        } else if (selected) {
            selectTab(selected);
        }
    }
    statusButtons.forEach(b => b.addEventListener('click', () => applyStatus(b.dataset.status)));

    tabs.forEach(tab => {
        tab.addEventListener('click', () => selectTab(tab));
        tab.addEventListener('keydown', event => {
            const visible = tabs.filter(t => !t.hidden);
            const index = visible.indexOf(tab);
            let next;
            if (event.key === 'ArrowLeft') next = (index + 1) % visible.length;
            else if (event.key === 'ArrowRight') next = (index - 1 + visible.length) % visible.length;
            else if (event.key === 'Home') next = 0;
            else if (event.key === 'End') next = visible.length - 1;
            else return;
            event.preventDefault();
            selectTab(visible[next]);
            visible[next].focus();
        });
    });
})();
</script>

@endif

</div>
@endsection
