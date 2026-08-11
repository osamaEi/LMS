@extends('layouts.dashboard')

@section('title', 'تقرير المتدرب')

@section('content')
@php
    $m = $report['marks'];
    $w = $m['weights'];
    $rows = $m['rows'];
    $d = $m['details'];

    $agg = fn($k) => $rows->count() > 0 ? round($rows->avg($k), 1) : 0;

    // Column order is right-to-left as written: الحضور first, المجموع last.
    $cells = [
        ['key' => 'attendance', 'label' => 'الحضور',       'val' => $agg('attendance'),    'max' => $w['attendance'],              'color' => '#0071AA'],
        ['key' => 'quizzes',    'label' => 'المشاركة',     'val' => $agg('participation'), 'max' => $w['quizzes'] + $w['homework'], 'color' => '#7c3aed'],
        ['key' => 'midterm',    'label' => 'اختبار نصفي',  'val' => $agg('midterm'),       'max' => $w['midterm'],                 'color' => '#0f766e'],
        ['key' => 'final',      'label' => 'اختبار نهائي', 'val' => $agg('final'),         'max' => $w['final'],                   'color' => '#dc2626'],
    ];
    $total    = $rows->count() > 0 ? round($rows->avg('total'), 1) : 0;
    $totalCol = $total >= 85 ? '#15803d' : ($total >= 60 ? '#0071AA' : '#b91c1c');
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
        <div class="rep-card">
            <table class="marks">
                <thead>
                    <tr>
                        @foreach($cells as $c)
                            <th>{{ $c['label'] }}<span class="mk-w">من {{ $c['max'] }}</span></th>
                        @endforeach
                        <th class="mk-total-h">المجموع<span class="mk-w">من 100</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach($cells as $c)
                            @php $pct = $c['max'] > 0 ? max(0, min(100, $c['val'] / $c['max'] * 100)) : 0; @endphp
                            <td>
                                <button type="button" class="mk-cell" data-detail="{{ $c['key'] }}" data-title="{{ $c['label'] }}">
                                    <span class="mk-val" style="color:{{ $c['color'] }};">{{ $c['val'] }}</span>
                                    <span class="mk-of">/ {{ $c['max'] }}</span>
                                    <span class="mk-bar"><i style="width:{{ $pct }}%;background:{{ $c['color'] }};"></i></span>
                                    <span class="mk-hint">عرض التفاصيل</span>
                                </button>
                            </td>
                        @endforeach
                        <td class="mk-total-c">
                            <span class="mk-total" style="color:{{ $totalCol }};">{{ $total }}</span>
                            <span class="mk-bar"><i style="width:{{ max(0, min(100, $total)) }}%;background:{{ $totalCol }};"></i></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
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
    // المشاركة merges two buckets; everything else maps 1:1.
    $modalData = [
        'attendance' => ['sections' => [['title' => 'الحضور', 'rows' => $d['attendance']]]],
        'quizzes'    => ['sections' => [
            ['title' => 'الاختبارات القصيرة', 'rows' => $d['quizzes']],
            ['title' => 'الواجبات',           'rows' => $d['homework']],
        ]],
        'midterm'    => ['sections' => [['title' => 'الاختبار النصفي', 'rows' => $d['midterm']]]],
        'final'      => ['sections' => [['title' => 'الاختبار النهائي', 'rows' => $d['final']]]],
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
                <span class="dr-main"><b>${esc(r.title)}</b><i>${esc(r.date || '—')}</i></span>
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
        return `<div class="dr">
            <span class="dr-main"><b>${esc(r.title)}</b><i>${esc(r.date || '—')}</i></span>
            <span class="dr-edit">
                <input type="number" min="0" step="0.01" name="quizzes[${r.id}][score]"
                       value="${r.score ?? ''}" placeholder="الدرجة" class="dr-num">
                <input type="number" min="0" max="100" step="0.01" name="quizzes[${r.id}][percentage]"
                       value="${r.pct ?? ''}" placeholder="%" class="dr-num">
                <span class="dr-lbl">%</span>
            </span>
        </div>`;
    }

    function open(key, label) {
        const conf = DATA[key];
        if (!conf) return;
        title.textContent = label;

        body.innerHTML = conf.sections.map(sec => {
            const rows = (sec.rows || []);
            const inner = rows.length
                ? rows.map(rowHtml).join('')
                : `<p class="dr-empty">لا توجد سجلات.</p>`;
            return `<section class="dsec"><h3>${esc(sec.title)} <em>(${rows.length})</em></h3>${inner}</section>`;
        }).join('');

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

    .dsec { margin-top:16px; }
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

    @media (max-width:640px) {
        .marks th { font-size:11px; padding:10px 4px; }
        .mk-val, .mk-total { font-size:20px; }
        .mk-cell, .mk-total-c { padding:16px 4px; }
        .mk-hint { display:none; }
    }
</style>
@endsection
