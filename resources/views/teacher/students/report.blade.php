@extends('layouts.dashboard')

@section('title', 'تقرير المتدرب')

@section('content')
@php
    $m = $report['marks'];
    $w = $m['weights'];
    $rows = $m['rows'];
    $d = $m['details'];

    // Column order is right-to-left as written: الحضور first, المجموع last.
    $columns = [
        ['key' => 'attendance', 'field' => 'attendance',    'label' => 'الحضور',       'max' => $w['attendance'],               'color' => '#0071AA'],
        ['key' => 'quizzes',    'field' => 'participation', 'label' => 'المشاركة',     'max' => $w['quizzes'] + $w['homework'], 'color' => '#7c3aed'],
        ['key' => 'midterm',    'field' => 'midterm',       'label' => 'اختبار نصفي',  'max' => $w['midterm'],                  'color' => '#0f766e'],
        ['key' => 'final',      'field' => 'final',         'label' => 'اختبار نهائي', 'max' => $w['final'],                    'color' => '#dc2626'],
    ];

    $colorFor = fn($t) => $t >= 85 ? '#15803d' : ($t >= 60 ? '#0071AA' : '#b91c1c');
@endphp

<div class="rep-wrap">

    <div class="rep-head">
        <div>
            <p class="rep-kicker">تقرير المتدرب — موادك فقط</p>
            <h1 class="rep-name">{{ $student->name }}</h1>
            @if($rows->isNotEmpty())
                <p class="rep-subjects">
                    @foreach($rows as $r)<span class="rep-chip">{{ $r['name'] }}</span>@endforeach
                </p>
            @endif
        </div>
        <a href="{{ route('teacher.students.index') }}" class="rep-back">← قائمة المتدربين</a>
    </div>

    @if(session('success'))
        <div class="rep-alert">{{ session('success') }}</div>
    @endif

    @if($rows->isEmpty())
        <div class="rep-empty">لا توجد مواد لحساب توزيع الدرجات.</div>
    @else
        @foreach($rows as $r)
            @php
                $subTotal = (float) $r['total'];
                $subCol   = $colorFor($subTotal);
                [$letter, $label] = \App\Models\Enrollment::gradeBand($subTotal);
            @endphp

            <div class="rep-card mk-card">
                <h2 class="mk-subject">{{ $r['name'] }}</h2>

                <table class="marks">
                    <thead>
                        <tr>
                            @foreach($columns as $c)
                                <th>{{ $c['label'] }}<span class="mk-w">من {{ $c['max'] }}</span></th>
                            @endforeach
                            <th class="mk-total-h">المجموع<span class="mk-w">من 100</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach($columns as $c)
                                @php
                                    $val = $r[$c['field']] ?? 0;
                                    $pct = $c['max'] > 0 ? max(0, min(100, $val / $c['max'] * 100)) : 0;
                                @endphp
                                <td>
                                    <button type="button" class="mk-cell"
                                            data-detail="{{ $c['key'] }}" data-title="{{ $c['label'] }}">
                                        <span class="mk-val" style="color:{{ $c['color'] }};">{{ $val }}</span>
                                        <span class="mk-of">/ {{ $c['max'] }}</span>
                                        <span class="mk-bar"><i style="width:{{ $pct }}%;background:{{ $c['color'] }};"></i></span>
                                        <span class="mk-hint">عرض التفاصيل</span>
                                    </button>
                                </td>
                            @endforeach
                            <td class="mk-total-c">
                                <button type="button" class="mk-cell" data-detail="total" data-title="المجموع">
                                    <span class="mk-total-line">
                                        <span class="mk-total" style="color:{{ $subCol }};">{{ $r['total'] }}</span>
                                        <span class="g-letter" style="background:{{ $subCol }};">{{ $letter }}</span>
                                    </span>
                                    <span class="mk-grade">{{ $label }}</span>
                                    <span class="mk-bar"><i style="width:{{ max(0, min(100, $subTotal)) }}%;background:{{ $subCol }};"></i></span>
                                    <span class="mk-hint">تعديل الدرجة النهائية</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    @endif
</div>

{{-- Detail / edit modal --}}
<div class="rep-modal" id="repModal" hidden>
    <div class="rep-modal-backdrop" data-close></div>
    <div class="rep-modal-box" role="dialog" aria-modal="true" aria-labelledby="repModalTitle">
        <form method="POST" action="{{ route('teacher.students.report.update', $student) }}">
            @csrf
            @method('PATCH')
            <div class="rep-modal-head">
                <h2 id="repModalTitle">التفاصيل</h2>
                <button type="button" class="rep-x" data-close aria-label="إغلاق">✕</button>
            </div>
            <div class="rep-modal-body" id="repModalBody"></div>
            <div class="rep-modal-foot">
                <button type="button" class="rep-btn-ghost" data-close>إلغاء</button>
                <button type="submit" class="rep-btn">💾 حفظ التعديلات</button>
            </div>
        </form>
    </div>
</div>

@php
    /**
     * One pinned input per subject. The saved manual mark wins; otherwise the
     * grade is seeded automatically from the student's actual attempt, so the
     * teacher only has to edit it when it needs changing.
     */
    $pinnedRows = function ($saved, $defaultMax, $attempts = null) use ($rows) {
        $attempts = collect($attempts ?? []);

        return $rows->map(function ($r) use ($saved, $defaultMax, $attempts) {
            $existing = collect($saved)->firstWhere('subject_id', $r['subject_id']);

            // Latest attempt on this subject, used only when nothing was saved.
            $attempt = $attempts->first(fn($a) => ($a['subject_id'] ?? null) === $r['subject_id']
                && ($a['score'] ?? null) !== null);

            return [
                'subject_id' => $r['subject_id'],
                'name'       => $r['name'],
                'grade'      => $existing['grade'] ?? $attempt['score'] ?? null,
                'max_grade'  => $existing['max_grade'] ?? $attempt['total_marks'] ?? $defaultMax,
                'auto'       => !$existing && $attempt !== null,
            ];
        })->values();
    };

    // المشاركة merges two buckets; everything else maps 1:1.
    $modalData = [
        'attendance' => [
            'stats' => [
                ['label' => 'حضور',      'val' => $report['attendance']['attended'], 'color' => '#0071AA'],
                ['label' => 'غياب',      'val' => $report['attendance']['absent'],   'color' => '#b91c1c'],
                ['label' => 'غياب بعذر', 'val' => $report['attendance']['excused'],  'color' => '#15803d'],
            ],
            'sections' => [['title' => 'الحضور', 'rows' => $d['attendance']]],
        ],
        'quizzes'    => [
            // البنود الثابتة — تظهر أسفل السجلات.
            'pinned_after' => [
                [
                    'title' => 'مشاركة',
                    'field' => 'participation_score',
                    'note'  => 'درجة المشاركة داخل المحاضرات',
                    'rows'  => $pinnedRows($d['participation_score'], 10),
                ],
                [
                    'title' => 'مشروع',
                    'field' => 'project_score',
                    'note'  => 'درجة المشروع',
                    'rows'  => $pinnedRows($d['project_score'], 10),
                ],
            ],
            'sections' => [
                ['title' => 'الاختبارات القصيرة', 'rows' => $d['quizzes']],
                ['title' => 'الواجبات',           'rows' => $d['homework']],
                ['title' => 'بنود إضافية',        'rows' => $d['manual'], 'addable' => true],
            ],
            'subjects' => $rows->map(fn($r) => ['id' => $r['subject_id'], 'name' => $r['name']])->values(),
        ],
        'midterm'    => [
            'pinned' => [
                'title' => 'اختبار نصفي',
                'field' => 'midterm_score',
                'note'  => 'الدرجة المرصودة يدويًا',
                'rows'  => $pinnedRows($d['midterm_score'], 20, $d['midterm']),
            ],
            'sections' => [],
        ],
        'final'      => [
            'pinned' => [
                'title' => 'اختبار نهائي',
                'field' => 'final_score',
                'note'  => 'الدرجة المرصودة يدويًا',
                'rows'  => $pinnedRows($d['final_score'], 40, $d['final']),
            ],
            'sections' => [
                // Older free-form manual rows stay editable, but no new ones are added.
                ['title' => 'درجات يدوية سابقة', 'rows' => $d['manual_final'], 'legacy' => true],
            ],
            'subjects' => $rows->map(fn($r) => ['id' => $r['subject_id'], 'name' => $r['name']])->values(),
        ],
        'total'      => [
            'totals' => $rows->map(function ($r) {
                // التقدير محسوب من مجموع المادة نفسها، لا من المعدل العام.
                [$letter, $label] = \App\Models\Enrollment::gradeBand((float) $r['total']);

                return [
                    'subject_id'   => $r['subject_id'],
                    'name'         => $r['name'],
                    'computed'     => $r['computed'],
                    'override'     => $r['override'],
                    'total'        => $r['total'],
                    'letter'       => $letter,
                    'label'        => $label,
                ];
            })->values(),
        ],
    ];
@endphp

<script>
(function () {
    const DATA  = @json($modalData);
    const modal = document.getElementById('repModal');
    const body  = document.getElementById('repModalBody');
    const title = document.getElementById('repModalTitle');

    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, c =>
        ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c]));

    // One editable line per record; input names mirror the PATCH payload shape.
    function rowHtml(r) {
        if (r.kind === 'attendance') {
            return `<label class="dr">
                <span class="dr-main">
                    <b>${esc(r.title)}${r.excused ? ' <em class="dr-badge">بعذر</em>' : ''}</b>
                    <i>${esc(r.date || '—')}</i>
                </span>
                <span class="dr-edit">
                    <input type="hidden" name="attendance[${r.id}][attended]" value="0">
                    <input type="checkbox" name="attendance[${r.id}][attended]" value="1" ${r.attended ? 'checked' : ''}>
                    <span class="dr-lbl">حاضر</span>
                </span>
            </label>`;
        }
        if (r.kind === 'homework') {
            return `<div class="dr">
                <span class="dr-main"><b>${esc(r.title)}</b><i>${esc(r.date || '—')}</i></span>
                <span class="dr-edit">
                    <input type="number" min="0" step="1" name="homework[${r.id}][grade]"
                           value="${r.grade ?? ''}" placeholder="الدرجة" class="dr-num">
                    <span class="dr-lbl">/ ${esc(r.max_grade ?? '—')}</span>
                    <input type="text" name="homework[${r.id}][feedback]"
                           value="${esc(r.feedback ?? '')}" placeholder="ملاحظة" class="dr-txt">
                </span>
            </div>`;
        }
        if (r.kind === 'manual') {
            return `<div class="dr" data-manual>
                <span class="dr-main">
                    <input type="text" name="participation[${r.id}][title]" value="${esc(r.title)}"
                           placeholder="اسم البند" class="dr-key">
                </span>
                <span class="dr-edit">
                    <input type="number" min="0" step="0.01" name="participation[${r.id}][grade]"
                           value="${r.grade ?? ''}" placeholder="الدرجة" class="dr-num">
                    <span class="dr-lbl">من</span>
                    <input type="number" min="1" step="1" name="participation[${r.id}][max_grade]"
                           value="${r.max_grade ?? 10}" class="dr-num dr-num-sm">
                    <label class="dr-del">
                        <input type="checkbox" name="participation[${r.id}][delete]" value="1">
                        <span>حذف</span>
                    </label>
                </span>
            </div>`;
        }
        // Quiz-type rows: grade only — the percentage is derived, not entered.
        return `<div class="dr">
            <span class="dr-main"><b>${esc(r.title)}</b><i>${esc(r.date || '—')}</i></span>
            <span class="dr-edit">
                <input type="number" min="0" step="0.01" name="quizzes[${r.id}][score]"
                       value="${r.score ?? ''}" placeholder="الدرجة" class="dr-num">
                <span class="dr-lbl">من ${esc(r.total ?? '—')}</span>
            </span>
        </div>`;
    }

    // Blank row for a brand-new manual item. Index only needs to be unique.
    let newIdx = 0;
    function newManualHtml(subjects, kind) {
        const i = newIdx++;
        const opts = subjects.map(s => `<option value="${s.id}">${esc(s.name)}</option>`).join('');
        return `<div class="dr" data-new>
            <span class="dr-main">
                <input type="text" name="new_participation[${i}][title]"
                       placeholder="${kind === 'final' ? 'اسم الاختبار' : 'اسم البند'}" class="dr-key">
                <input type="hidden" name="new_participation[${i}][kind]" value="${kind || 'participation'}">
                ${subjects.length > 1
                    ? `<select name="new_participation[${i}][subject_id]" class="dr-sel">${opts}</select>`
                    : `<input type="hidden" name="new_participation[${i}][subject_id]" value="${subjects[0]?.id ?? ''}">`}
            </span>
            <span class="dr-edit">
                <input type="number" min="0" step="0.01" name="new_participation[${i}][grade]"
                       placeholder="الدرجة" class="dr-num">
                <span class="dr-lbl">من</span>
                <input type="number" min="1" step="1" name="new_participation[${i}][max_grade]"
                       value="${kind === 'final' ? 40 : 10}" class="dr-num dr-num-sm">
                <button type="button" class="dr-rm" title="إزالة">✕</button>
            </span>
        </div>`;
    }

    function open(key, label) {
        const conf = DATA[key];
        if (!conf) return;
        title.textContent = label;

        const subjects = conf.subjects || [];

        // المجموع: override the computed total per subject instead of listing records.
        if (conf.totals) {
            body.innerHTML = `<section class="dsec">
                <h3>الدرجة النهائية <em>(من 100)</em></h3>
                <p class="dr-note">اترك الخانة فارغة ليُحتسب المجموع تلقائيًا من توزيع الدرجات.</p>
                ${conf.totals.map(t => `<div class="dr">
                    <span class="dr-main">
                        <b>${esc(t.name)}
                            <em class="dr-grade">${esc(t.letter)} — ${esc(t.label)}</em>
                        </b>
                        <i>الدرجة: ${t.total} من 100 · المحسوب تلقائيًا: ${t.computed}</i>
                    </span>
                    <span class="dr-edit">
                        <input type="number" min="0" max="100" step="0.01"
                               name="totals[${t.subject_id}][final_grade]"
                               value="${t.override ?? ''}" placeholder="يدوي" class="dr-num">
                        <span class="dr-lbl">من 100</span>
                        ${t.override !== null ? `<label class="dr-del">
                            <input type="checkbox" name="totals[${t.subject_id}][clear]" value="1">
                            <span>إلغاء</span>
                        </label>` : ''}
                    </span>
                </div>`).join('')}
            </section>`;
            modal.hidden = false;
            document.body.style.overflow = 'hidden';
            return;
        }

        const stats = (conf.stats || []).length
            ? `<div class="dstats">${conf.stats.map(s => `<div class="dstat">
                    <b style="color:${s.color};">${s.val}</b><span>${esc(s.label)}</span>
                </div>`).join('')}</div>`
            : '';

        // Pinned grade box — one input per subject. `pinned` renders above the
        // records, `pinned_after` below them.
        const pinBlock = (pin) => (pin && pin.rows.length)
            ? `<section class="dsec dsec-pin">
                <h3>${esc(pin.title)}</h3>
                ${pin.rows.map(p => `<div class="dr">
                    <span class="dr-main">
                        <b>${esc(p.name)}</b>
                        <i>${p.auto ? 'محسوبة تلقائيًا من الاختبار — يمكن تعديلها' : esc(pin.note)}</i>
                    </span>
                    <span class="dr-edit">
                        <input type="number" min="0" step="0.01"
                               name="${pin.field}[${p.subject_id}][grade]"
                               value="${p.grade ?? ''}" placeholder="الدرجة" class="dr-num">
                        <span class="dr-lbl">من</span>
                        <input type="number" min="1" step="1"
                               name="${pin.field}[${p.subject_id}][max_grade]"
                               value="${p.max_grade}" class="dr-num dr-num-sm">
                    </span>
                </div>`).join('')}
            </section>`
            : '';

        const pinned = pinBlock(conf.pinned);
        const pinnedAfter = (conf.pinned_after || []).map(pinBlock).join('');

        body.innerHTML = stats + pinned + conf.sections.map((sec, i) => {
            const rows = (sec.rows || []);
            // Legacy sections only exist to keep old records editable — drop them
            // entirely once there is nothing left in them.
            if (sec.legacy && !rows.length) return '';
            const inner = rows.length
                ? rows.map(rowHtml).join('')
                : `<p class="dr-empty">لا توجد سجلات.</p>`;
            const add = sec.addable && subjects.length
                ? `<div class="dr-slot"></div>
                   <button type="button" class="dr-add" data-kind="${sec.kind || 'participation'}">+ إضافة بند</button>`
                : '';
            return `<section class="dsec"><h3>${esc(sec.title)} <em>(${rows.length})</em></h3>${inner}${add}</section>`;
        }).join('') + pinnedAfter;

        body.querySelectorAll('.dr-add').forEach(btn => {
            btn.addEventListener('click', () => {
                const slot = btn.previousElementSibling;
                slot.insertAdjacentHTML('beforeend', newManualHtml(subjects, btn.dataset.kind));
                slot.lastElementChild.querySelector('.dr-key').focus();
            });
        });
        body.addEventListener('click', e => {
            const rm = e.target.closest('.dr-rm');
            if (rm) rm.closest('[data-new]').remove();
        });

        modal.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    function close() {
        modal.hidden = true;
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.mk-cell').forEach(btn => {
        btn.addEventListener('click', () => open(btn.dataset.detail, btn.dataset.title));
    });
    modal.querySelectorAll('[data-close]').forEach(el => el.addEventListener('click', close));
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.hidden) close(); });
})();
</script>

<style>
    .rep-wrap { direction:rtl; font-family:'Segoe UI',sans-serif; max-width:1100px; margin:0 auto; padding:20px 16px 60px; }

    .rep-head { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:14px; margin-bottom:22px; }
    .rep-kicker { color:#64748b; font-size:12px; margin:0 0 4px; }
    .rep-name { color:#0f172a; font-size:24px; font-weight:800; margin:0; }
    .rep-subjects { margin:10px 0 0; display:flex; flex-wrap:wrap; gap:6px; }
    .rep-chip { background:#eef4fa; color:#0071AA; font-size:11px; font-weight:700; border-radius:999px; padding:4px 11px; }
    .rep-back { background:#f1f5f9; color:#334155; padding:9px 16px; border-radius:11px; text-decoration:none; font-size:13px; font-weight:700; }
    .rep-alert { background:#dcfce7; border:1px solid #86efac; color:#15803d; padding:12px 16px; border-radius:12px; margin-bottom:16px; font-weight:600; }
    .rep-empty { border:1px solid #e2e8f0; border-radius:16px; padding:40px; text-align:center; color:#64748b; font-weight:600; background:#fff; }

    .rep-card { background:#fff; border:1px solid #e6ebf1; border-radius:18px; overflow:hidden;
                box-shadow:0 6px 22px rgba(15,23,42,.06); }
    .marks { width:100%; border-collapse:collapse; table-layout:fixed; }
    .marks th { background:#f7f9fc; border-bottom:1px solid #e6ebf1; padding:14px 10px;
                font-size:14px; font-weight:800; color:#0f172a; text-align:center; }
    .marks th + th, .marks td + td { border-right:1px solid #eef2f7; }
    .mk-w { display:block; margin-top:4px; font-size:11px; font-weight:600; color:#94a3b8; }
    .marks td { padding:0; text-align:center; vertical-align:middle; }
    .mk-total-h { background:#eef4fa; }
    .mk-total-c { background:#fafcfe; padding:22px 10px; }

    .mk-cell { display:block; width:100%; border:0; background:transparent; cursor:pointer;
               padding:22px 10px; font-family:inherit; transition:background .15s; }
    .mk-cell:hover { background:#f6faff; }
    .mk-cell:focus-visible { outline:2px solid #0071AA; outline-offset:-3px; }
    .mk-val { font-size:30px; font-weight:800; }
    .mk-of { font-size:12px; color:#94a3b8; margin-right:3px; }
    .mk-total { font-size:32px; font-weight:800; }
    .mk-bar { display:block; height:5px; border-radius:99px; background:#eef2f7; margin:10px auto 0; max-width:120px; overflow:hidden; }
    .mk-bar i { display:block; height:100%; border-radius:99px; }
    .mk-hint { display:block; margin-top:8px; font-size:10px; font-weight:700; color:#b6c2d1; }
    .mk-cell:hover .mk-hint { color:#0071AA; }

    .mk-card { margin-bottom:18px; }
    .mk-subject { font-size:15px; font-weight:800; color:#0f172a;
                  margin:0; padding:16px 18px; border-bottom:1px solid #eef2f7; }
    .mk-total-line { display:flex; align-items:center; justify-content:center; gap:8px; }
    .mk-grade { display:block; margin-top:4px; font-size:11px; font-weight:700; color:#475569; }
    .g-letter { display:inline-block; min-width:32px; text-align:center; color:#fff;
                font-size:12px; font-weight:800; border-radius:8px; padding:3px 8px; }
    .dr-grade { font-style:normal; font-size:11px; font-weight:700; color:#0071AA;
                background:#eef4fa; border-radius:99px; padding:2px 8px; margin-right:6px; }

    .rep-modal[hidden] { display:none; }
    .rep-modal { position:fixed; inset:0; z-index:9999; display:flex; align-items:center; justify-content:center; padding:18px; }
    .rep-modal-backdrop { position:absolute; inset:0; background:rgba(15,23,42,.55); }
    .rep-modal-box { position:relative; background:#fff; border-radius:18px; width:100%; max-width:680px;
                     max-height:86vh; display:flex; flex-direction:column; direction:rtl;
                     font-family:'Segoe UI',sans-serif; box-shadow:0 24px 60px rgba(2,6,23,.35); overflow:hidden; }
    .rep-modal-box form { display:flex; flex-direction:column; min-height:0; }
    .rep-modal-head { display:flex; align-items:center; justify-content:space-between;
                      padding:16px 20px; border-bottom:1px solid #eef2f7; }
    .rep-modal-head h2 { margin:0; font-size:17px; font-weight:800; color:#0f172a; }
    .rep-x { border:0; background:#f1f5f9; color:#475569; width:30px; height:30px; border-radius:9px; cursor:pointer; font-size:13px; }
    .rep-modal-body { padding:8px 20px 16px; overflow-y:auto; }
    .rep-modal-foot { display:flex; gap:10px; justify-content:flex-start;
                      padding:14px 20px; border-top:1px solid #eef2f7; background:#fafcfe; }
    .rep-btn { background:#0071AA; color:#fff; border:0; border-radius:11px; padding:10px 20px; font-size:14px; font-weight:700; cursor:pointer; }
    .rep-btn-ghost { background:#f1f5f9; color:#475569; border:0; border-radius:11px; padding:10px 18px; font-size:14px; font-weight:700; cursor:pointer; }

    .dstats { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-top:16px; }
    .dstat { border:1px solid #eef2f7; border-radius:12px; padding:12px 8px; text-align:center; background:#fafcfe; }
    .dstat b { display:block; font-size:24px; font-weight:800; line-height:1.1; }
    .dstat span { display:block; margin-top:4px; font-size:11px; font-weight:700; color:#64748b; }
    .dr-badge { font-style:normal; font-size:10px; font-weight:700; color:#15803d;
                background:#dcfce7; border-radius:99px; padding:2px 7px; margin-right:6px; }

    .dsec { margin-top:16px; }
    .dsec-pin { background:#f6faff; border:1px solid #dbeafe; border-radius:14px; padding:12px 12px 4px; }
    .dsec-pin h3 { color:#0071AA; }
    .dsec-pin .dr { background:#fff; }
    .dsec h3 { font-size:13px; font-weight:800; color:#334155; margin:0 0 8px; }
    .dsec h3 em { font-style:normal; color:#94a3b8; font-weight:600; }
    .dr { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
          border:1px solid #eef2f7; border-radius:12px; padding:10px 12px; margin-bottom:8px; }
    .dr-main { display:flex; flex-direction:column; gap:2px; min-width:150px; flex:1; }
    .dr-main b { font-size:13px; font-weight:700; color:#0f172a; }
    .dr-main i { font-style:normal; font-size:11px; color:#94a3b8; }
    .dr-edit { display:flex; align-items:center; gap:7px; flex-wrap:wrap; }
    .dr-lbl { font-size:11px; color:#64748b; font-weight:600; }
    .dr-num { width:78px; border:1px solid #e2e8f0; border-radius:9px; padding:7px 9px; font-size:13px; font-family:inherit; }
    .dr-txt { width:150px; border:1px solid #e2e8f0; border-radius:9px; padding:7px 9px; font-size:13px; font-family:inherit; }
    .dr-empty { font-size:12px; color:#94a3b8; padding:8px 2px; margin:0; }
    .dr-note { font-size:11px; color:#94a3b8; margin:0 0 10px; }
    .dr-key { width:100%; min-width:130px; border:1px solid #e2e8f0; border-radius:9px;
              padding:7px 9px; font-size:13px; font-weight:700; font-family:inherit; color:#0f172a; }
    .dr-sel { margin-top:6px; width:100%; border:1px solid #e2e8f0; border-radius:9px;
              padding:6px 8px; font-size:12px; font-family:inherit; color:#475569; background:#fff; }
    .dr-num-sm { width:60px; }
    .dr-del { display:inline-flex; align-items:center; gap:4px; font-size:11px; color:#b91c1c; font-weight:700; cursor:pointer; }
    .dr-rm { border:0; background:#fee2e2; color:#b91c1c; width:28px; height:28px;
             border-radius:8px; cursor:pointer; font-size:12px; font-weight:700; }
    .dr-add { margin-top:4px; width:100%; border:1px dashed #cbd5e1; background:#fafcfe; color:#0071AA;
              border-radius:11px; padding:9px; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit; }
    .dr-add:hover { background:#f1f7fc; border-color:#0071AA; }
    [data-new] { background:#fafcfe; border-style:dashed; }

    @media (max-width:640px) {
        .marks th { font-size:11px; padding:10px 4px; }
        .mk-val, .mk-total { font-size:20px; }
        .mk-cell, .mk-total-c { padding:16px 4px; }
        .mk-hint { display:none; }
    }
</style>
@endsection
