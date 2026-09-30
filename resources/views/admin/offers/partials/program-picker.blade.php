{{-- Multi-select of programs / courses. Expects $programs and $selected (array of ids). --}}
@php
    $groupLabel = function ($p) {
        return match (true) {
            $p->type === 'english'                                          => '🇬🇧 برامج اللغة الإنجليزية',
            $p->type === 'course' && $p->course_type === 'developmental'    => '📘 الدورات التطويرية',
            $p->type === 'course'                                           => '📗 الدورات التأهيلية',
            $p->type === 'training'                                         => '🛠️ البرامج التأهيلية',
            default                                                         => '🎓 المسارات والدبلومات',
        };
    };
    $groups   = $programs->groupBy($groupLabel);
    $selected = array_map('intval', $selected);
@endphp

<style>
    .pp-box { border: 2px solid #e5e7eb; border-radius: 14px; overflow: hidden; }
    .dark .pp-box { border-color: #4b5563; }
    .pp-top { display: flex; gap: .5rem; align-items: center; padding: .6rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; flex-wrap: wrap; }
    .dark .pp-top { background: #111827; border-color: #4b5563; }
    .pp-search { flex: 1; min-width: 180px; padding: .5rem .8rem; border: 1.5px solid #e5e7eb; border-radius: 10px; font-size: .85rem; background: #fff; color: #374151; }
    .dark .pp-search { background: #374151; border-color: #4b5563; color: #f9fafb; }
    .pp-search:focus { outline: none; border-color: #0071AA; }
    .pp-mini { padding: .45rem .75rem; border-radius: 9px; border: none; font-size: .75rem; font-weight: 700; cursor: pointer; background: #e0f2fe; color: #0071AA; }
    .pp-mini--clear { background: #f3f4f6; color: #6b7280; }
    .pp-list { max-height: 300px; overflow-y: auto; padding: .4rem .6rem .6rem; }
    .pp-group-title { font-size: .72rem; font-weight: 800; color: #6b7280; margin: .7rem .2rem .35rem; display: flex; justify-content: space-between; align-items: center; }
    .pp-group-title button { background: none; border: none; color: #0071AA; font-size: .7rem; font-weight: 700; cursor: pointer; }
    .pp-item { display: flex; align-items: center; gap: .6rem; padding: .5rem .65rem; border-radius: 10px; cursor: pointer; transition: background .15s; }
    .pp-item:hover { background: #f3f4f6; }
    .dark .pp-item:hover { background: #374151; }
    .pp-item:has(input:checked) { background: rgba(0,113,170,.08); }
    .pp-item input { accent-color: #0071AA; width: 17px; height: 17px; flex-shrink: 0; }
    .pp-name { flex: 1; font-size: .85rem; font-weight: 600; color: #374151; }
    .dark .pp-name { color: #e5e7eb; }
    .pp-price { font-size: .75rem; color: #9ca3af; font-weight: 700; white-space: nowrap; }
    .pp-status { font-size: .65rem; background: #fee2e2; color: #b91c1c; padding: .05rem .4rem; border-radius: 5px; margin-inline-start: .3rem; }
    .pp-foot { padding: .55rem .8rem; font-size: .78rem; font-weight: 700; border-top: 1px solid #e5e7eb; background: #f9fafb; color: #0071AA; }
    .dark .pp-foot { background: #111827; border-color: #4b5563; }
    .pp-foot.is-all { color: #059669; }
</style>

<div class="pp-box" id="programPicker">
    <div class="pp-top">
        <input type="text" class="pp-search" placeholder="🔍 ابحث عن برنامج أو دورة..." oninput="ppFilter(this.value)">
        <button type="button" class="pp-mini" onclick="ppSetAll(true)">تحديد الكل</button>
        <button type="button" class="pp-mini pp-mini--clear" onclick="ppSetAll(false)">إلغاء التحديد</button>
    </div>
    <div class="pp-list">
        @foreach($groups as $label => $items)
        <div class="pp-group">
            <div class="pp-group-title">
                <span>{{ $label }} ({{ $items->count() }})</span>
                <button type="button" onclick="ppToggleGroup(this)">تحديد المجموعة</button>
            </div>
            @foreach($items as $prog)
            <label class="pp-item" data-name="{{ mb_strtolower($prog->name_ar . ' ' . $prog->name_en . ' ' . $prog->code) }}">
                <input type="checkbox" name="program_ids[]" value="{{ $prog->id }}" {{ in_array($prog->id, $selected, true) ? 'checked' : '' }} onchange="ppCount()">
                <span class="pp-name">
                    {{ $prog->name_ar }}
                    @if($prog->status !== 'active')<span class="pp-status">غير نشط</span>@endif
                </span>
                @if($prog->price > 0)<span class="pp-price">{{ number_format($prog->price, 0) }} <x-riyal /></span>@endif
            </label>
            @endforeach
        </div>
        @endforeach
    </div>
    <div class="pp-foot" id="ppFoot"></div>
</div>
<span class="f-hint">اختر برنامجاً أو أكثر — اترك الكل بدون تحديد لتطبيق العرض على جميع البرامج والدورات</span>
@error('program_ids')<span class="error-msg">{{ $message }}</span>@enderror
@error('program_ids.*')<span class="error-msg">{{ $message }}</span>@enderror

<script>
function ppBoxes(visibleOnly) {
    return [...document.querySelectorAll('#programPicker .pp-item')]
        .filter(l => !visibleOnly || l.style.display !== 'none')
        .map(l => l.querySelector('input'));
}
function ppCount() {
    const n = ppBoxes(false).filter(i => i.checked).length;
    const foot = document.getElementById('ppFoot');
    foot.classList.toggle('is-all', n === 0);
    foot.textContent = n === 0 ? '🌐 العرض سيُطبَّق على جميع البرامج والدورات' : '✅ تم اختيار ' + n + ' برنامج / دورة';
}
function ppSetAll(on) { ppBoxes(true).forEach(i => i.checked = on); ppCount(); }
function ppToggleGroup(btn) {
    const boxes = [...btn.closest('.pp-group').querySelectorAll('.pp-item')]
        .filter(l => l.style.display !== 'none').map(l => l.querySelector('input'));
    const on = boxes.some(i => !i.checked);
    boxes.forEach(i => i.checked = on);
    ppCount();
}
function ppFilter(q) {
    q = q.trim().toLowerCase();
    document.querySelectorAll('#programPicker .pp-group').forEach(g => {
        let shown = 0;
        g.querySelectorAll('.pp-item').forEach(l => {
            const ok = !q || l.dataset.name.includes(q);
            l.style.display = ok ? '' : 'none';
            if (ok) shown++;
        });
        g.style.display = shown ? '' : 'none';
    });
}
ppCount();
</script>
