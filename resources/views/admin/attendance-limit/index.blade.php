@extends('layouts.dashboard')

@section('title', 'حد الغياب المسموح')

@section('content')
@php
    // كل الروابط داخل الصفحة تحافظ على التبويب والنطاق الحالي.
    $link = function (array $extra = []) use ($tab, $kind, $allPrograms, $programId, $search, $status) {
        $params = array_merge([
            'tab'        => $tab,
            'kind'       => $kind,
            'program_id' => $allPrograms ? '' : $programId,
            'q'          => $search ?: null,
            'status'     => $status,
        ], $extra);

        // route() drops empty strings, but "" is meaningful here — it means
        // "all programs" — so build the query string by hand.
        $params = array_filter($params, fn($v) => $v !== null);

        return route('admin.attendance-limit.index') . '?' . http_build_query($params);
    };

    $tabs = [
        'settings'  => ['الإعداد العام', null],
        'subjects'  => ['حدود المقررات', null],
        'offenders' => ['المتجاوزون', $tab === 'offenders' ? $offenders->count() : null],
    ];

    $scopeLabel = $kind === 'course' ? 'الدورة' : 'الدبلومة';
@endphp

<div class="al-wrap">

    <div class="al-head">
        <div>
            <p class="al-kicker">إدارة الحضور</p>
            <h1 class="al-title">حد الغياب المسموح</h1>
        </div>

        <span class="al-state {{ $enabled ? 'al-state-on' : 'al-state-off' }}">
            {{ $enabled ? '● الحرمان مفعّل' : '○ الحرمان غير مفعّل' }}
        </span>
    </div>

    @if(session('success'))
        <div class="al-alert">{{ session('success') }}</div>
    @endif

    {{-- ═══ التبويبات ═══ --}}
    <div class="al-tabs">
        @foreach($tabs as $key => [$label, $count])
            <a href="{{ $link(['tab' => $key]) }}" class="al-tab {{ $tab === $key ? 'al-tab-on' : '' }}">
                {{ $label }}
                @if($count !== null)<span class="al-tab-n">{{ $count }}</span>@endif
            </a>
        @endforeach
    </div>

    {{-- ═══ نطاق البرنامج — مشترك بين تبويبي المقررات والمتجاوزين ═══ --}}
    @if($tab !== 'settings')
        <div class="al-card al-scope">
            <div class="al-kinds">
                <a href="{{ $link(['kind' => 'diploma', 'program_id' => null]) }}"
                   class="al-kind {{ $kind === 'diploma' ? 'al-kind-on' : '' }}">الدبلومات</a>
                <a href="{{ $link(['kind' => 'course', 'program_id' => null]) }}"
                   class="al-kind {{ $kind === 'course' ? 'al-kind-on' : '' }}">الدورات</a>
            </div>

            @if($programs->isEmpty())
                <p class="al-empty-sm">لا توجد {{ $kind === 'course' ? 'دورات' : 'دبلومات' }}.</p>
            @else
                <form method="GET" action="{{ route('admin.attendance-limit.index') }}" class="al-picker">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <input type="hidden" name="kind" value="{{ $kind }}">
                    @if($search)<input type="hidden" name="q" value="{{ $search }}">@endif
                    @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif

                    <label for="program_id">{{ $scopeLabel }}</label>
                    <select name="program_id" id="program_id" onchange="this.form.submit()" class="al-sel">
                        @if($tab === 'offenders')
                            <option value="" @selected($allPrograms)>كل {{ $kind === 'course' ? 'الدورات' : 'الدبلومات' }}</option>
                        @endif
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}" @selected(!$allPrograms && $programId == $p->id)>
                                {{ $p->name_ar ?: $p->name_en }}{{ $p->code ? ' — ' . $p->code : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>
    @endif

    {{-- ═══════════════ تبويب: الإعداد العام ═══════════════ --}}
    @if($tab === 'settings')
        <form method="POST" action="{{ route('admin.attendance-limit.update') }}" class="al-card">
            @csrf
            @method('PUT')

            <h2 class="al-h2">الإعداد العام</h2>

            <label class="al-toggle">
                <input type="hidden" name="enabled" value="0">
                <input type="checkbox" name="enabled" value="1" @checked($enabled)>
                <span>
                    <b>تفعيل منع المتدرب عند تجاوز الحد</b>
                    <i>عند التفعيل، يُمنع المتدرب من دخول محاضرات المقرر الذي تجاوز فيه نسبة الغياب.</i>
                </span>
            </label>

            <div class="al-field">
                <label for="percent">النسبة المسموحة للغياب</label>
                <div class="al-inline">
                    <input type="number" id="percent" name="percent" min="0" max="100" step="0.5"
                           value="{{ old('percent', $percent) }}" required>
                    <span class="al-suffix">%</span>
                </div>
                @error('percent')<p class="al-err">{{ $message }}</p>@enderror
            </div>

            <div class="al-how">
                <b>طريقة الاحتساب</b>
                <ul>
                    <li>النسبة تُحسب لكل مقرر على حدة: (عدد المحاضرات المغيَّبة ÷ إجمالي محاضرات المقرر) × 100.</li>
                    <li><b>الغياب بعذر مقبول لا يُحتسب</b> ضمن النسبة.</li>
                    <li>يمكن تخصيص نسبة مختلفة لكل مقرر من تبويب «حدود المقررات».</li>
                    <li>يمكن استثناء متدرب بعينه من تبويب «المتجاوزون».</li>
                </ul>
            </div>

            <button type="submit" class="al-btn">💾 حفظ الإعدادات</button>
        </form>
    @endif

    {{-- ═══════════════ تبويب: حدود المقررات ═══════════════ --}}
    @if($tab === 'subjects')
        <form method="POST" action="{{ route('admin.attendance-limit.update-subjects') }}" class="al-card">
            @csrf
            @method('PUT')

            <div class="al-card-head">
                <h2 class="al-h2">مقررات {{ $scopeLabel }} المختارة</h2>
                <span class="al-hint">الخانة الفارغة تتبع الحد العام ({{ $percent }}%)</span>
            </div>

            @if($subjects->isEmpty())
                <p class="al-empty">لا توجد مقررات في هذا البرنامج.</p>
            @else
                <div class="al-scroll">
                    <table class="al-table">
                        <thead>
                            <tr>
                                <th>المقرر</th>
                                <th>الرمز</th>
                                <th>الحد المخصص</th>
                                <th>المطبَّق فعليًا</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjects as $s)
                                <tr>
                                    <td class="al-subj">{{ $s->name_ar ?: $s->name_en }}</td>
                                    <td class="al-code">{{ $s->code ?: '—' }}</td>
                                    <td>
                                        <div class="al-inline">
                                            <input type="number" name="limits[{{ $s->id }}]" min="0" max="100" step="0.5"
                                                   value="{{ $s->absence_limit_percent !== null ? (float) $s->absence_limit_percent : '' }}"
                                                   placeholder="عام" class="al-mini-input">
                                            <span class="al-suffix">%</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($s->absence_limit_percent !== null)
                                            <span class="al-badge al-badge-custom">{{ (float) $s->absence_limit_percent }}% مخصص</span>
                                        @else
                                            <span class="al-badge al-badge-idle">{{ $percent }}% عام</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="submit" class="al-btn">💾 حفظ حدود المقررات</button>
            @endif
        </form>
    @endif

    {{-- ═══════════════ تبويب: المتجاوزون ═══════════════ --}}
    @if($tab === 'offenders')

        {{-- بحث + فلتر الحالة + تصدير --}}
        <div class="al-card al-filters">
            <form method="GET" action="{{ route('admin.attendance-limit.index') }}" class="al-filter-form">
                <input type="hidden" name="tab" value="offenders">
                <input type="hidden" name="kind" value="{{ $kind }}">
                <input type="hidden" name="program_id" value="{{ $allPrograms ? '' : $programId }}">

                <input type="search" name="q" value="{{ $search }}" class="al-search"
                       placeholder="🔍 بحث بالاسم أو البريد…">

                <select name="status" onchange="this.form.submit()" class="al-sel">
                    <option value="">كل الحالات</option>
                    <option value="banned" @selected($status === 'banned')>ممنوع فقط</option>
                    <option value="exempt" @selected($status === 'exempt')>مستثنى فقط</option>
                </select>

                <button type="submit" class="al-btn-sm">بحث</button>

                @if($search || $status)
                    <a href="{{ $link(['q' => null, 'status' => null]) }}" class="al-clear">✕ إزالة الفلاتر</a>
                @endif
            </form>

            <a href="{{ route('admin.attendance-limit.export', request()->query()) }}" class="al-btn-out">
                ⬇ تصدير CSV
            </a>
        </div>

        @unless($enabled)
            <p class="al-warn">⚠️ المنع غير مفعّل حاليًا — القائمة أدناه للاطلاع فقط ولن يُمنع أحد.</p>
        @endunless

        <div class="al-card">
            <div class="al-card-head">
                <h2 class="al-h2">
                    المتدربون المتجاوزون للحد
                    <span class="al-count">{{ $offenders->count() }}</span>
                </h2>
                <span class="al-hint">
                    ممنوع: {{ $offenders->where('exempt', false)->count() }} ·
                    مستثنى: {{ $offenders->where('exempt', true)->count() }}
                </span>
            </div>

            @if($offenders->isEmpty())
                <p class="al-empty">
                    {{ $search || $status ? 'لا توجد نتائج مطابقة للفلاتر.' : 'لا يوجد متدربون تجاوزوا الحد.' }}
                </p>
            @else
                <div class="al-scroll">
                    <table class="al-table">
                        <thead>
                            <tr>
                                <th>المتدرب</th>
                                <th>المقرر</th>
                                <th>الغياب (بدون عذر)</th>
                                <th>غياب بعذر</th>
                                <th>النسبة</th>
                                <th>الحد</th>
                                <th>الحالة</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offenders as $o)
                                <tr class="{{ $o->exempt ? '' : 'al-row-banned' }}">
                                    <td>
                                        <b class="al-name">{{ $o->student_name }}</b>
                                        <i class="al-mail">{{ $o->student_email }}</i>
                                    </td>
                                    <td class="al-subj">{{ $o->subject }}</td>
                                    <td>
                                        <b class="al-absent">{{ $o->absent }}</b>
                                        <i class="al-of">من {{ $o->total }} محاضرة</i>
                                    </td>
                                    <td class="al-num al-excused">{{ $o->excused }}</td>
                                    <td><span class="al-pct">{{ $o->percent }}%</span></td>
                                    <td>
                                        <span class="al-badge {{ $o->custom ? 'al-badge-custom' : 'al-badge-idle' }}">
                                            {{ $o->limit }}%{{ $o->custom ? ' مخصص' : '' }}
                                        </span>
                                    </td>
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

        {{-- قائمة المستثنين --}}
        <div class="al-card">
            <div class="al-card-head">
                <h2 class="al-h2">الاستثناءات الممنوحة <span class="al-count">{{ $exemptions->count() }}</span></h2>
                <span class="al-hint">تشمل من لم يتجاوز الحد بعد</span>
            </div>

            @if($exemptions->isEmpty())
                <p class="al-empty">لا توجد استثناءات في هذا النطاق.</p>
            @else
                <div class="al-scroll">
                    <table class="al-table">
                        <thead>
                            <tr>
                                <th>المتدرب</th>
                                <th>المقرر</th>
                                <th>السبب</th>
                                <th>مُنح بواسطة</th>
                                <th>التاريخ</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exemptions as $e)
                                <tr>
                                    <td>
                                        <b class="al-name">{{ $e->student_name }}</b>
                                        <i class="al-mail">{{ $e->student_email }}</i>
                                    </td>
                                    <td class="al-subj">{{ $e->subject }}</td>
                                    <td class="al-reason">{{ $e->reason ?: '—' }}</td>
                                    <td class="al-by">{{ $e->granted_by_name ?: '—' }}</td>
                                    <td class="al-date">
                                        {{ $e->updated_at ? \Carbon\Carbon::parse($e->updated_at)->format('Y/m/d') : '—' }}
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.attendance-limit.revoke') }}"
                                              onsubmit="return confirm('إلغاء الاستثناء؟')">
                                            @csrf
                                            <input type="hidden" name="student_id" value="{{ $e->student_id }}">
                                            <input type="hidden" name="subject_id" value="{{ $e->subject_id }}">
                                            <button type="submit" class="al-mini al-mini-red">إلغاء</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
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
    if (!modal) return;

    const open = (b) => {
        document.getElementById('alStudent').value = b.dataset.student;
        document.getElementById('alSubject').value = b.dataset.subject;
        document.getElementById('alWho').textContent =
            `سيُسمح لـ «${b.dataset.name}» بحضور محاضرات مقرر «${b.dataset.subjectName}» رغم تجاوزه الحد.`;
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
    .al-wrap { direction:rtl; font-family:'Segoe UI',sans-serif; max-width:1150px; margin:0 auto; padding:20px 16px 60px; }
    .al-head { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; }
    .al-kicker { color:#64748b; font-size:12px; margin:0 0 4px; }
    .al-title { color:#0f172a; font-size:24px; font-weight:800; margin:0 0 20px; }
    .al-state { font-size:12px; font-weight:800; border-radius:99px; padding:6px 14px; }
    .al-state-on  { background:#dcfce7; color:#15803d; }
    .al-state-off { background:#f1f5f9; color:#64748b; }
    .al-alert { background:#dcfce7; border:1px solid #86efac; color:#15803d; padding:12px 16px;
                border-radius:12px; margin-bottom:16px; font-weight:600; }

    /* التبويبات */
    .al-tabs { display:flex; gap:4px; border-bottom:2px solid #eef2f7; margin-bottom:20px; flex-wrap:wrap; }
    .al-tab { text-decoration:none; color:#64748b; padding:11px 20px; font-size:14px; font-weight:700;
              border-bottom:3px solid transparent; margin-bottom:-2px; transition:color .15s; }
    .al-tab:hover { color:#0071AA; }
    .al-tab-on { color:#0071AA; border-bottom-color:#0071AA; }
    .al-tab-n { display:inline-block; background:#eef4fa; color:#0071AA; font-size:11px;
                font-weight:800; border-radius:99px; padding:1px 8px; margin-right:5px; }

    .al-card { background:#fff; border:1px solid #e6ebf1; border-radius:18px; padding:20px 22px;
               margin-bottom:18px; box-shadow:0 6px 22px rgba(15,23,42,.06); }
    .al-card-head { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:6px; }
    .al-h2 { font-size:16px; font-weight:800; color:#0f172a; margin:0; }
    .al-hint { font-size:11.5px; color:#94a3b8; font-weight:600; }
    .al-count { display:inline-block; background:#eef4fa; color:#0071AA; font-size:12px;
                font-weight:800; border-radius:99px; padding:2px 10px; margin-right:6px; }

    /* نطاق البرنامج */
    .al-scope { padding:16px 22px; }
    .al-kinds { display:flex; gap:8px; margin-bottom:14px; }
    .al-kind { text-decoration:none; background:#f1f5f9; color:#475569; border-radius:10px;
               padding:8px 18px; font-size:13px; font-weight:700; transition:background .15s; }
    .al-kind:hover { background:#e2e8f0; }
    .al-kind-on { background:#0071AA; color:#fff; }
    .al-kind-on:hover { background:#0071AA; }
    .al-picker { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
    .al-picker label { font-size:13px; font-weight:700; color:#334155; margin:0; }
    .al-picker .al-sel { min-width:280px; }
    .al-empty-sm { font-size:13px; color:#94a3b8; margin:0; }

    /* الإعداد */
    .al-toggle { display:flex; align-items:flex-start; gap:10px; cursor:pointer; margin:16px 0 18px; }
    .al-toggle input { margin-top:3px; width:17px; height:17px; }
    .al-toggle b { display:block; font-size:14px; color:#0f172a; }
    .al-toggle i { display:block; font-style:normal; font-size:12px; color:#64748b; margin-top:3px; }
    .al-field label { display:block; font-size:13px; font-weight:700; color:#334155; margin-bottom:6px; }
    .al-inline { display:flex; align-items:center; gap:8px; }
    .al-inline input { width:110px; border:1px solid #e2e8f0; border-radius:10px;
                       padding:9px 11px; font-size:15px; font-weight:700; font-family:inherit; }
    .al-suffix { font-size:14px; font-weight:700; color:#64748b; }
    .al-err { color:#b91c1c; font-size:12px; margin:6px 0 0; }

    .al-how { background:#f7fafd; border:1px solid #e6ebf1; border-radius:12px; padding:14px 16px; margin-top:18px; }
    .al-how b { display:block; font-size:12.5px; color:#0f172a; margin-bottom:8px; }
    .al-how ul { margin:0; padding-right:18px; }
    .al-how li { font-size:12px; color:#475569; line-height:2; }

    /* الفلاتر */
    .al-filters { display:flex; align-items:center; justify-content:space-between;
                  gap:12px; flex-wrap:wrap; padding:14px 18px; }
    .al-filter-form { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .al-search { border:1px solid #e2e8f0; border-radius:10px; padding:8px 12px;
                 font-size:13px; font-family:inherit; min-width:230px; }
    .al-clear { font-size:12px; font-weight:700; color:#b91c1c; text-decoration:none; }
    .al-btn-out { background:#fff; border:1px solid #0071AA; color:#0071AA; border-radius:10px;
                  padding:8px 16px; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap; }
    .al-btn-out:hover { background:#f1f7fc; }

    /* أزرار */
    .al-btn { background:#0071AA; color:#fff; border:0; border-radius:11px; padding:10px 22px;
              font-size:14px; font-weight:700; cursor:pointer; margin-top:18px; font-family:inherit; }
    .al-btn-sm { background:#0071AA; color:#fff; border:0; border-radius:10px; padding:8px 18px;
                 font-size:13px; font-weight:700; cursor:pointer; font-family:inherit; }
    .al-btn-ghost { background:#f1f5f9; color:#475569; border:0; border-radius:11px; padding:10px 18px;
                    font-size:14px; font-weight:700; cursor:pointer; font-family:inherit; }
    .al-sel { border:1px solid #e2e8f0; border-radius:10px; padding:8px 12px;
              font-size:13px; font-family:inherit; background:#fff; color:#334155; }

    .al-warn { background:#fef3c7; border:1px solid #fcd34d; color:#92400e; padding:10px 14px;
               border-radius:10px; font-size:13px; font-weight:600; margin:0 0 18px; }
    .al-empty { color:#64748b; font-size:13px; text-align:center; padding:30px 0; margin:0; }

    /* الجداول */
    .al-scroll { overflow-x:auto; margin-top:14px; }
    .al-table { width:100%; border-collapse:collapse; min-width:760px; }
    .al-table th { text-align:right; font-size:12px; font-weight:700; color:#64748b;
                   padding:0 10px 10px; border-bottom:1px solid #eef2f7; white-space:nowrap; }
    .al-table td { padding:12px 10px; border-bottom:1px solid #f4f7fa; text-align:right; vertical-align:middle; }
    .al-table tr:last-child td { border-bottom:0; }
    .al-row-banned { background:#fefafa; }

    .al-name { display:block; font-size:13px; font-weight:700; color:#0f172a; }
    .al-mail { display:block; font-style:normal; font-size:11px; color:#94a3b8; margin-top:2px; }
    .al-subj { font-size:13px; color:#334155; font-weight:600; }
    .al-code { font-size:12px; color:#94a3b8; font-family:monospace; }
    .al-num { font-size:15px; font-weight:800; color:#0f172a; }
    .al-excused { color:#15803d; }
    .al-absent { display:block; font-size:16px; font-weight:800; color:#b91c1c; }
    .al-of { display:block; font-style:normal; font-size:11px; font-weight:600; color:#94a3b8; margin-top:2px; }
    .al-pct { display:inline-block; background:#fee2e2; color:#b91c1c; font-size:13px;
              font-weight:800; border-radius:8px; padding:4px 10px; }
    .al-reason { font-size:12px; color:#475569; max-width:240px; }
    .al-by { font-size:12px; color:#64748b; }
    .al-date { font-size:12px; color:#94a3b8; white-space:nowrap; }

    .al-badge { display:inline-block; font-size:11px; font-weight:800; border-radius:99px;
                padding:4px 10px; white-space:nowrap; }
    .al-badge-ok     { background:#dcfce7; color:#15803d; }
    .al-badge-no     { background:#fee2e2; color:#b91c1c; }
    .al-badge-idle   { background:#f1f5f9; color:#64748b; }
    .al-badge-custom { background:#eef4fa; color:#0071AA; }

    .al-mini-input { width:82px; border:1px solid #e2e8f0; border-radius:9px;
                     padding:7px 9px; font-size:13px; font-weight:700; font-family:inherit; }
    .al-mini { border:1px solid #0071AA; background:#fff; color:#0071AA; border-radius:9px;
               padding:6px 12px; font-size:12px; font-weight:700; cursor:pointer;
               font-family:inherit; white-space:nowrap; }
    .al-mini:hover { background:#f1f7fc; }
    .al-mini-red { border-color:#dc2626; color:#dc2626; }
    .al-mini-red:hover { background:#fef2f2; }

    /* المودال */
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
