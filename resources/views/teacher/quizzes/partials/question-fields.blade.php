@php
    // $q is the Question being edited, or null when adding a new one.
    // $useOld: only the add form repopulates from old() after a validation error,
    // so edit forms for OTHER questions don't all get overwritten with that input.
    $useOld = $useOld ?? false;
    $field = fn($key, $current) => $useOld ? old($key, $current) : $current;

    $qType = $field('type', $q->type ?? 'multiple_choice');
    $opts  = $q && $q->options->count()
        ? $q->options
        : collect();
    $tfCorrect = null;
    if ($q && $q->type === 'true_false') {
        $tfCorrect = optional($q->options->firstWhere('is_correct', true))->option_en === 'True' ? 'true' : 'false';
    }
@endphp

<div class="inline-form-grid">
    <div class="field">
        <label>نوع السؤال *</label>
        <select name="type" class="js-qtype" required>
            @foreach(['multiple_choice'=>'اختيار من متعدد','true_false'=>'صح / خطأ','short_answer'=>'إجابة قصيرة','essay'=>'مقالي'] as $val=>$lbl)
                <option value="{{ $val }}" @selected($qType===$val)>{{ $lbl }}</option>
            @endforeach
        </select>
    </div>
    <div class="field">
        <label>الدرجة *</label>
        <input type="number" name="marks" step="0.5" min="0" value="{{ $field('marks', $q->marks ?? 1) }}" required>
    </div>
    <div class="field full">
        <label>نص السؤال (عربي) *</label>
        <textarea name="question_ar" required>{{ $field('question_ar', $q->question_ar ?? '') }}</textarea>
    </div>
    <div class="field full">
        <label>نص السؤال (إنجليزي)</label>
        <textarea name="question_en" dir="ltr">{{ $field('question_en', $q->question_en ?? '') }}</textarea>
    </div>
    <div class="field full">
        <label>الشرح (يظهر بعد التصحيح)</label>
        <textarea name="explanation_ar">{{ $field('explanation_ar', $q->explanation_ar ?? '') }}</textarea>
    </div>

    {{-- Image --}}
    <div class="field full">
        <label>صورة السؤال (اختياري)</label>
        @if($q && $q->image)
            <div style="margin-bottom:0.5rem;">
                <img src="{{ \Illuminate\Support\Facades\Storage::url($q->image) }}" style="max-height:120px;border-radius:8px;border:1px solid #e5e7eb;">
                <label style="display:inline-flex;align-items:center;gap:0.3rem;font-size:0.8rem;color:#dc2626;margin-top:0.3rem;">
                    <input type="checkbox" name="remove_image" value="1"> حذف الصورة الحالية
                </label>
            </div>
        @endif
        <input type="file" name="image" accept="image/*">
    </div>
</div>

{{-- Multiple choice options --}}
<div class="js-mc-fields" style="margin-top:0.875rem;{{ $qType==='multiple_choice' ? '' : 'display:none;' }}">
    <label style="display:block;font-size:0.78rem;font-weight:600;color:#374151;margin-bottom:0.5rem;">الخيارات (حدّد الإجابة الصحيحة) *</label>
    <div class="js-opts">
        @php
            $mcOpts = $qType === 'multiple_choice' && $opts->count()
                ? $opts
                : collect([(object)['option_ar'=>'','is_correct'=>false],(object)['option_ar'=>'','is_correct'=>false]]);
        @endphp
        @foreach($mcOpts as $i => $opt)
            <div class="opt-row">
                <input type="text" name="options[{{ $i }}][text_ar]" value="{{ $opt->option_ar ?? '' }}" placeholder="نص الخيار">
                <label class="opt-correct">
                    <input type="checkbox" name="options[{{ $i }}][is_correct]" value="1" @checked($opt->is_correct ?? false)> صحيحة
                </label>
                <button type="button" class="opt-remove" onclick="removeOption(this)">×</button>
            </div>
        @endforeach
    </div>
    <button type="button" class="btn-add-opt" onclick="addOption(this)">+ إضافة خيار</button>
</div>

{{-- True / false --}}
<div class="js-tf-fields" style="margin-top:0.875rem;{{ $qType==='true_false' ? '' : 'display:none;' }}">
    <label style="display:block;font-size:0.78rem;font-weight:600;color:#374151;margin-bottom:0.5rem;">الإجابة الصحيحة *</label>
    <div class="checkbox-row">
        <label><input type="radio" name="correct_answer" value="true" @checked($tfCorrect==='true')> صح</label>
        <label><input type="radio" name="correct_answer" value="false" @checked($tfCorrect==='false')> خطأ</label>
    </div>
</div>
