@php
    $initialQuestions = old('questions', $quiz->questions->map(fn ($question) => array_merge(
        $question->only(['type', 'question_ar', 'question_en', 'explanation_ar', 'explanation_en', 'marks']),
        ['source_id' => $question->id, 'options' => $question->options->map(fn ($option) => [
            'option_ar' => $option->option_ar, 'option_en' => $option->option_en,
            'is_correct' => (int) $option->is_correct,
        ])->values()->all()]
    ))->values()->all());
@endphp
<section class="qd-section" id="quiz-questions" x-data="{
    questions: {{ Illuminate\Support\Js::from(array_values($initialQuestions)) }},
    sourceImages: {{ Illuminate\Support\Js::from($quiz->questions->filter(fn ($q) => $q->image)->mapWithKeys(fn ($q) => [$q->id => Storage::disk('public')->url($q->image)])) }},
    nextKey: 0,
    init() { this.questions.forEach(q => { q.key = this.nextKey++; q.options = q.options || []; q.remove_image = q.remove_image || 0; q.preview = Number(q.remove_image) ? '' : (this.sourceImages[q.source_id] || ''); }); },
    previewImage(q, input) {
        const file = input.files[0];
        input.setCustomValidity(file &amp;&amp; file.size > 2097152 ? 'الحد الأقصى للصورة 2 ميجابايت' : '');
        if (q.preview &amp;&amp; q.preview.startsWith('blob:')) URL.revokeObjectURL(q.preview);
        q.preview = file ? URL.createObjectURL(file) : (Number(q.remove_image) ? '' : (this.sourceImages[q.source_id] || ''));
    },
    addQuestion() { this.questions.push({key: this.nextKey++, source_id: '', type: 'multiple_choice', question_ar: '', question_en: '', explanation_ar: '', explanation_en: '', marks: 1, options: [{option_ar: '', option_en: '', is_correct: 1}, {option_ar: '', option_en: '', is_correct: 0}]}); },
    changeType(q) {
        if (q.type === 'true_false') q.options = [{option_ar: 'صح', option_en: 'True', is_correct: 1}, {option_ar: 'خطأ', option_en: 'False', is_correct: 0}];
        else if (q.type === 'multiple_choice' && q.options.length === 0) q.options = [{option_ar: '', option_en: '', is_correct: 1}, {option_ar: '', option_en: '', is_correct: 0}];
        else if (['essay', 'short_answer'].includes(q.type)) q.options = [];
    }
}">
    <div class="qd-heading"><span class="qd-step">4</span><h2>أسئلة الاختبار الجديد</h2><span class="qd-question-count" x-cloak x-text="questions.length + ' سؤال'"></span></div>
    <p class="qd-help">أضف نصًا أو صورة للسؤال، وحدد الدرجة والإجابة الصحيحة.</p>
    <template x-for="(question, index) in questions" :key="question.key">
        <div class="qd-question">
            <div class="qd-question-head">
                <h2 x-text="'السؤال ' + (index + 1)"></h2>
                <button type="button" class="qd-danger" @click="questions.splice(index, 1)">حذف السؤال</button>
            </div>
            <div class="qd-question-body">
            <input type="hidden" :name="'questions[' + index + '][source_id]'" :value="question.source_id">
            <div class="qd-dates">
                <label class="qd-label">نوع السؤال
                    <select class="qd-field" :name="'questions[' + index + '][type]'" x-model="question.type" @change="changeType(question)">
                        <option value="multiple_choice">اختيار من متعدد</option>
                        <option value="true_false">صح وخطأ</option>
                        <option value="short_answer">إجابة قصيرة</option>
                        <option value="essay">مقالي</option>
                    </select>
                </label>
                <label class="qd-label">الدرجة<input class="qd-field" type="number" min="0.01" step="0.01" required :name="'questions[' + index + '][marks]'" x-model="question.marks"></label>
            </div>
            <label class="qd-label">السؤال بالعربية<textarea class="qd-field" rows="3" placeholder="اكتب السؤال، أو ارفع صورته بالأسفل" :name="'questions[' + index + '][question_ar]'" x-model="question.question_ar"></textarea></label>
            <div class="qd-upload">
                <label class="qd-label">صورة السؤال
                    <input class="qd-field" type="file" accept="image/jpeg,image/png,image/gif,image/webp" :name="'questions[' + index + '][image]'" @change="previewImage(question, $event.target)">
                </label>
                <p class="qd-help">JPG، PNG، GIF، WEBP — حتى 2 ميجابايت. يمكن استخدام الصورة بدل نص السؤال.</p>
                <template x-if="question.preview"><img :src="question.preview" alt="معاينة صورة السؤال" class="qd-image-preview"></template>
                <template x-if="sourceImages[question.source_id]">
                    <label class="qd-label" style="margin-top:12px"><input type="checkbox" :name="'questions[' + index + '][remove_image]'" value="1" :checked="Number(question.remove_image) === 1" @change="question.remove_image = $event.target.checked ? 1 : 0; previewImage(question, $el.closest('.qd-upload').querySelector('input[type=file]'))"> حذف الصورة الأصلية من الاختبار الجديد</label>
                </template>
            </div>
            <label class="qd-label">السؤال بالإنجليزية<textarea class="qd-field" rows="2" :name="'questions[' + index + '][question_en]'" x-model="question.question_en"></textarea></label>
            <template x-if="['multiple_choice', 'true_false'].includes(question.type)">
                <div class="qd-options">
                    <template x-for="(option, optionIndex) in question.options" :key="optionIndex">
                        <div class="qd-option" :class="{ 'qd-option-correct': Number(option.is_correct) === 1 }">
                            <div class="qd-option-grid">
                            <label class="qd-label"><span x-text="'الاختيار ' + (optionIndex + 1)"></span><input class="qd-field" required :name="'questions[' + index + '][options][' + optionIndex + '][option_ar]'" x-model="option.option_ar"></label>
                            <label class="qd-label">الاختيار بالإنجليزية<input class="qd-field" :name="'questions[' + index + '][options][' + optionIndex + '][option_en]'" x-model="option.option_en"></label>
                            </div>
                            <div class="qd-option-footer">
                            <label class="qd-label">الإجابة
                                <select class="qd-field" :name="'questions[' + index + '][options][' + optionIndex + '][is_correct]'" x-model="option.is_correct">
                                    <option value="0">غير صحيحة</option><option value="1">صحيحة</option>
                                </select>
                            </label>
                            <button type="button" class="qd-danger" x-show="question.type === 'multiple_choice' && question.options.length > 2" @click="question.options.splice(optionIndex, 1)">حذف الاختيار</button>
                            </div>
                        </div>
                    </template>
                    <button type="button" class="qd-add" x-show="question.type === 'multiple_choice'" @click="question.options.push({option_ar: '', option_en: '', is_correct: 0})">+ إضافة اختيار</button>
                </div>
            </template>
            <label class="qd-label">شرح الإجابة بالعربية<textarea class="qd-field" rows="2" :name="'questions[' + index + '][explanation_ar]'" x-model="question.explanation_ar"></textarea></label>
            <label class="qd-label">شرح الإجابة بالإنجليزية<textarea class="qd-field" rows="2" :name="'questions[' + index + '][explanation_en]'" x-model="question.explanation_en"></textarea></label>
            </div>
        </div>
    </template>
    <p class="qd-help" x-show="questions.length === 0">أضف سؤالًا واحدًا على الأقل لحفظ الاختبار.</p>
    <button type="button" class="qd-add" style="margin-top:20px" @click="addQuestion()">+ إضافة سؤال جديد</button>
</section>
