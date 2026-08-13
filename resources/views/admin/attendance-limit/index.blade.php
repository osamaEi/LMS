@extends('layouts.dashboard')

@section('title', 'حد الغياب المسموح')

@section('content')
<div class="al-wrap">

    <div class="al-head">
        <div>
            <p class="al-kicker">إدارة الحضور</p>
            <h1 class="al-title">حد الغياب المسموح</h1>
        </div>
    </div>

    @if(session('success'))
        <div class="al-alert">{{ session('success') }}</div>
    @endif

    {{-- الإعدادات --}}
    <form method="POST" action="{{ route('admin.attendance-limit.update') }}" class="al-card">
        @csrf
        @method('PUT')

        <h2 class="al-h2">الإعداد</h2>

        <label class="al-toggle">
            <input type="hidden" name="enabled" value="0">
            <input type="checkbox" name="enabled" value="1" @checked($enabled)>
            <span>
                <b>تفعيل منع المتدرب عند تجاوز الحد</b>
                <i>عند التفعيل، يُمنع المتدرب من دخول محاضرات المادة التي تجاوز فيها نسبة الغياب.</i>
            </span>
        </label>

        <div class="al-field">
            <label for="percent">النسبة المسموحة للغياب</label>
            <div class="al-inline">
                <input type="number" id="percent" name="percent" min="0" max="100" step="0.5"
                       value="{{ old('percent', $percent) }}" required>
                <span class="al-suffix">%</span>
            </div>
            <p class="al-note">
                تُحسب لكل مادة على حدة: (عدد المحاضرات المغيَّبة ÷ إجمالي محاضرات المادة) × 100.
                <b>الغياب بعذر مقبول لا يُحتسب</b> ضمن النسبة.
            </p>
            @error('percent')<p class="al-err">{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="al-btn">💾 حفظ الإعدادات</button>
    </form>

    {{-- المتجاوزون --}}
    <div class="al-card">
        <div class="al-card-head">
            <h2 class="al-h2">المتدربون المتجاوزون للحد <span class="al-count">{{ $offenders->count() }}</span></h2>

            <form method="GET" action="{{ route('admin.attendance-limit.index') }}">
                <select name="subject_id" onchange="this.form.submit()" class="al-sel">
                    <option value="">كل المواد</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}" @selected($subjectId == $s->id)>
                            {{ $s->name_ar ?: $s->name_en }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if(!$enabled)
            <p class="al-warn">⚠️ المنع غير مفعّل حاليًا — القائمة أدناه للاطلاع فقط ولن يُمنع أحد.</p>
        @endif

        @if($offenders->isEmpty())
            <p class="al-empty">لا يوجد متدربون تجاوزوا الحد.</p>
        @else
            <div class="al-scroll">
            <table class="al-table">
                <thead>
                    <tr>
                        <th>المتدرب</th>
                        <th>المادة</th>
                        <th>الغياب</th>
                        <th>بعذر</th>
                        <th>النسبة</th>
                        <th>الحالة</th>
                        <th>إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($offenders as $o)
                        <tr>
                            <td>
                                <b class="al-name">{{ $o->student_name }}</b>
                                <i class="al-mail">{{ $o->student_email }}</i>
                            </td>
                            <td class="al-subj">{{ $o->subject }}</td>
                            <td class="al-num">{{ $o->absent }} / {{ $o->total }}</td>
                            <td class="al-num al-excused">{{ $o->excused }}</td>
                            <td><span class="al-pct">{{ $o->percent }}%</span></td>
                            <td>
                                @if($o->exempt)
                                    <span class="al-badge al-badge-ok" @if($o->reason) title="{{ $o->reason }}" @endif>مستثنى</span>
                                @elseif($enabled)
                                    <span class="al-badge al-badge-no">ممنوع</span>
                                @else
                                    <span class="al-badge al-badge-idle">غير مفعّل</span>
                                @endif
                            </td>
                            <td>
                                @if($o->exempt)
                                    <form method="POST" action="{{ route('admin.attendance-limit.revoke') }}"
                                          onsubmit="return confirm('إلغاء الاستثناء وإعادة منع المتدرب؟')">
                                        @csrf
                                        <input type="hidden" name="student_id" value="{{ $o->student_id }}">
                                        <input type="hidden" name="subject_id" value="{{ $o->subject_id }}">
                                        <button type="submit" class="al-mini al-mini-red">إلغاء الاستثناء</button>
                                    </form>
                                @else
                                    <button type="button" class="al-mini"
                                            data-exempt
                                            data-student="{{ $o->student_id }}"
                                            data-subject="{{ $o->subject_id }}"
                                            data-name="{{ $o->student_name }}"
                                            data-subject-name="{{ $o->subject }}">استثناء</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @endif
    </div>
</div>

{{-- مودال الاستثناء --}}
<div class="al-modal" id="alModal" hidden>
    <div class="al-modal-bd" data-close></div>
    <div class="al-modal-box">
        <form method="POST" action="{{ route('admin.attendance-limit.exempt') }}">
            @csrf
            <input type="hidden" name="student_id" id="alStudent">
            <input type="hidden" name="subject_id" id="alSubject">

            <div class="al-modal-head">
                <h2>استثناء متدرب</h2>
                <button type="button" class="al-x" data-close aria-label="إغلاق">✕</button>
            </div>

            <div class="al-modal-body">
                <p class="al-modal-txt" id="alWho"></p>
                <label for="alReason">سبب الاستثناء <span class="al-opt">(اختياري)</span></label>
                <textarea name="reason" id="alReason" rows="3" placeholder="مثال: ظرف صحي موثّق"></textarea>
            </div>

            <div class="al-modal-foot">
                <button type="button" class="al-btn-ghost" data-close>إلغاء</button>
                <button type="submit" class="al-btn">تأكيد الاستثناء</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('alModal');
    const open = (b) => {
        document.getElementById('alStudent').value = b.dataset.student;
        document.getElementById('alSubject').value = b.dataset.subject;
        document.getElementById('alWho').textContent =
            `سيُسمح لـ «${b.dataset.name}» بحضور محاضرات مادة «${b.dataset.subjectName}» رغم تجاوزه الحد.`;
        modal.hidden = false;
    };

    document.querySelectorAll('[data-exempt]').forEach(b =>
        b.addEventListener('click', () => open(b)));
    modal.querySelectorAll('[data-close]').forEach(el =>
        el.addEventListener('click', () => modal.hidden = true));
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') modal.hidden = true;
    });
})();
</script>

<style>
    .al-wrap { direction:rtl; font-family:'Segoe UI',sans-serif; max-width:1100px; margin:0 auto; padding:20px 16px 60px; }
    .al-kicker { color:#64748b; font-size:12px; margin:0 0 4px; }
    .al-title { color:#0f172a; font-size:24px; font-weight:800; margin:0 0 20px; }
    .al-alert { background:#dcfce7; border:1px solid #86efac; color:#15803d; padding:12px 16px;
                border-radius:12px; margin-bottom:16px; font-weight:600; }

    .al-card { background:#fff; border:1px solid #e6ebf1; border-radius:18px; padding:20px 22px;
               margin-bottom:20px; box-shadow:0 6px 22px rgba(15,23,42,.06); }
    .al-card-head { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; }
    .al-h2 { font-size:16px; font-weight:800; color:#0f172a; margin:0 0 16px; }
    .al-card-head .al-h2 { margin:0; }
    .al-count { display:inline-block; background:#eef4fa; color:#0071AA; font-size:12px;
                font-weight:800; border-radius:99px; padding:2px 10px; margin-right:6px; }

    .al-toggle { display:flex; align-items:flex-start; gap:10px; cursor:pointer; margin-bottom:18px; }
    .al-toggle input { margin-top:3px; width:17px; height:17px; }
    .al-toggle b { display:block; font-size:14px; color:#0f172a; }
    .al-toggle i { display:block; font-style:normal; font-size:12px; color:#64748b; margin-top:3px; }

    .al-field label { display:block; font-size:13px; font-weight:700; color:#334155; margin-bottom:6px; }
    .al-inline { display:flex; align-items:center; gap:8px; }
    .al-inline input { width:110px; border:1px solid #e2e8f0; border-radius:10px;
                       padding:9px 11px; font-size:15px; font-weight:700; font-family:inherit; }
    .al-suffix { font-size:14px; font-weight:700; color:#64748b; }
    .al-note { font-size:12px; color:#64748b; margin:8px 0 0; line-height:1.7; }
    .al-err { color:#b91c1c; font-size:12px; margin:6px 0 0; }

    .al-btn { background:#0071AA; color:#fff; border:0; border-radius:11px; padding:10px 22px;
              font-size:14px; font-weight:700; cursor:pointer; margin-top:18px; font-family:inherit; }
    .al-btn-ghost { background:#f1f5f9; color:#475569; border:0; border-radius:11px; padding:10px 18px;
                    font-size:14px; font-weight:700; cursor:pointer; font-family:inherit; }
    .al-sel { border:1px solid #e2e8f0; border-radius:10px; padding:8px 12px;
              font-size:13px; font-family:inherit; background:#fff; color:#334155; }

    .al-warn { background:#fef3c7; border:1px solid #fcd34d; color:#92400e; padding:10px 14px;
               border-radius:10px; font-size:13px; font-weight:600; margin:0 0 14px; }
    .al-empty { color:#64748b; font-size:13px; text-align:center; padding:30px 0; margin:0; }

    .al-scroll { overflow-x:auto; margin-top:14px; }
    .al-table { width:100%; border-collapse:collapse; min-width:720px; }
    .al-table th { text-align:right; font-size:12px; font-weight:700; color:#64748b;
                   padding:0 10px 10px; border-bottom:1px solid #eef2f7; white-space:nowrap; }
    .al-table td { padding:12px 10px; border-bottom:1px solid #f4f7fa; text-align:right; vertical-align:middle; }
    .al-table tr:last-child td { border-bottom:0; }
    .al-name { display:block; font-size:13px; font-weight:700; color:#0f172a; }
    .al-mail { display:block; font-style:normal; font-size:11px; color:#94a3b8; margin-top:2px; }
    .al-subj { font-size:13px; color:#334155; font-weight:600; }
    .al-num { font-size:13px; font-weight:700; color:#0f172a; white-space:nowrap; }
    .al-excused { color:#b45309; }
    .al-pct { display:inline-block; background:#fee2e2; color:#b91c1c; font-size:13px;
              font-weight:800; border-radius:8px; padding:4px 10px; }

    .al-badge { display:inline-block; font-size:11px; font-weight:800; border-radius:99px; padding:4px 10px; white-space:nowrap; }
    .al-badge-ok   { background:#dcfce7; color:#15803d; }
    .al-badge-no   { background:#fee2e2; color:#b91c1c; }
    .al-badge-idle { background:#f1f5f9; color:#64748b; }

    .al-mini { border:1px solid #0071AA; background:#fff; color:#0071AA; border-radius:9px;
               padding:6px 12px; font-size:12px; font-weight:700; cursor:pointer; font-family:inherit; white-space:nowrap; }
    .al-mini:hover { background:#f1f7fc; }
    .al-mini-red { border-color:#dc2626; color:#dc2626; }
    .al-mini-red:hover { background:#fef2f2; }

    .al-modal[hidden] { display:none; }
    .al-modal { position:fixed; inset:0; z-index:9999; display:flex; align-items:center; justify-content:center; padding:18px; }
    .al-modal-bd { position:absolute; inset:0; background:rgba(15,23,42,.55); }
    .al-modal-box { position:relative; background:#fff; border-radius:18px; width:100%; max-width:480px;
                    direction:rtl; font-family:'Segoe UI',sans-serif; overflow:hidden;
                    box-shadow:0 24px 60px rgba(2,6,23,.35); }
    .al-modal-head { display:flex; align-items:center; justify-content:space-between;
                     padding:16px 20px; border-bottom:1px solid #eef2f7; }
    .al-modal-head h2 { margin:0; font-size:16px; font-weight:800; color:#0f172a; }
    .al-x { border:0; background:#f1f5f9; color:#475569; width:30px; height:30px;
            border-radius:9px; cursor:pointer; font-size:13px; }
    .al-modal-body { padding:16px 20px; }
    .al-modal-txt { font-size:13px; color:#334155; line-height:1.8; margin:0 0 14px; }
    .al-modal-body label { display:block; font-size:13px; font-weight:700; color:#334155; margin-bottom:6px; }
    .al-opt { font-weight:600; color:#94a3b8; font-size:11px; }
    .al-modal-body textarea { width:100%; border:1px solid #e2e8f0; border-radius:10px; padding:9px 11px;
                              font-size:13px; font-family:inherit; resize:vertical; }
    .al-modal-foot { display:flex; gap:10px; padding:14px 20px; border-top:1px solid #eef2f7; background:#fafcfe; }
    .al-modal-foot .al-btn { margin-top:0; }
</style>
@endsection
