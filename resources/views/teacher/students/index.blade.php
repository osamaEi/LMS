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

<style>
    .student-group-tab { border:1px solid #dbe4ee;border-radius:12px;padding:10px 16px;background:#fff;color:#475569;font:inherit;cursor:pointer;white-space:nowrap; }
    .student-group-tab[aria-selected="true"] { background:#0071AA;border-color:#0071AA;color:#fff; }
    .student-group-tab:focus-visible { outline:3px solid #38bdf8;outline-offset:3px; }
    #student-search:focus { outline:none;border-color:#0071AA;box-shadow:0 0 0 3px rgba(0,113,170,.12); }
</style>

{{-- Search --}}
<div style="margin-bottom:16px;">
    <label for="student-search" class="sr-only" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);">ابحث عن طالب</label>
    <div style="position:relative;max-width:420px;">
        <svg aria-hidden="true" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="2"
             style="position:absolute;top:50%;inset-inline-start:14px;transform:translateY(-50%);pointer-events:none;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
        </svg>
        <input type="search" id="student-search" autocomplete="off"
               placeholder="ابحث بالاسم أو البريد أو الهوية أو الجوال…"
               style="width:100%;padding:11px 42px 11px 14px;border:1px solid #dbe4ee;border-radius:12px;font:inherit;font-size:13px;color:#1e293b;background:#fff;">
    </div>
    <p id="student-search-status" role="status" aria-live="polite"
       style="margin:8px 2px 0;font-size:12px;color:#64748b;min-height:16px;"></p>
</div>
<div role="tablist" aria-label="مجموعات الطلاب" style="display:flex;gap:8px;overflow-x:auto;padding:4px;margin-bottom:16px;">
    @foreach($grouped as $group)
        <button type="button" class="student-group-tab" role="tab"
                id="student-group-tab-{{ $loop->index }}" aria-controls="student-group-panel-{{ $loop->index }}"
                aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}">
            {{ $group['name'] }} <span class="student-group-count" style="font-size:12px;margin-inline-start:6px;">({{ count($group['students']) }})</span>
        </button>
    @endforeach
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
            if (count) count.textContent = '(' + shown + ')';
        });

        status.textContent = terms.length
            ? (total ? 'عدد النتائج: ' + total : 'لا توجد نتائج مطابقة')
            : '';
    }

    search.addEventListener('input', runSearch);
    search.addEventListener('keydown', event => {
        if (event.key === 'Escape') { search.value = ''; runSearch(); }
    });

    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => selectTab(tab));
        tab.addEventListener('keydown', event => {
            let next;
            if (event.key === 'ArrowLeft') next = (index + 1) % tabs.length;
            else if (event.key === 'ArrowRight') next = (index - 1 + tabs.length) % tabs.length;
            else if (event.key === 'Home') next = 0;
            else if (event.key === 'End') next = tabs.length - 1;
            else return;
            event.preventDefault();
            selectTab(tabs[next]);
            tabs[next].focus();
        });
    });
})();
</script>

@endif

</div>
@endsection
