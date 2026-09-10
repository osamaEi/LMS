@php
    $initialQuestions = old('questions', $quiz->questions->map(fn ($question) => array_merge(
        $question->only(['type', 'question_ar', 'question_en', 'explanation_ar', 'explanation_en', 'marks']),
        ['source_id' => $question->id, 'options' => $question->options->map(fn ($option) => [
            'option_ar' => $option->option_ar, 'option_en' => $option->option_en,
            'is_correct' => (int) $option->is_correct,
        ])->values()->all()]
    ))->values()->all());
@endphp
<section class="qd-section" x-data="{
    questions: {{ Illuminate\Support\Js::from(array_values($initialQuestions)) }},
    nextKey: 0,
    init() { this.questions.forEach(q => { q.key = this.nextKey++; q.options = q.options || []; }); },
    addQuestion() { this.questions.push({key: this.nextKey++, source_id: '', type: 'multiple_choice', question_ar: '', question_en: '', explanation_ar: '', explanation_en: '', marks: 1, options: [{option_ar: '', option_en: '', is_correct: 1}, {option_ar: '', option_en: '', is_correct: 0}]}); },
    changeType(q) {
        if (q.type === 'true_false') q.options = [{option_ar: 'صح', option_en: 'True', is_correct: 1}, {option_ar: 'خطأ', option_en: 'False', is_correct: 0}];
        else if (q.type === 'multiple_choice' && q.options.length === 0) q.options = [{option_ar: '', option_en: '', is_correct: 1}, {option_ar: '', option_en: '', is_correct: 0}];
        else if (['essay', 'short_answer'].includes(q.type)) q.options = [];
    }
}">
    <div class="qd-heading"><span class="qd-step">4</span><h2>أسئلة الاختبار الجديد</h2></div>
    <p class="qd-help">يمكنك تعديل الأسئلة والإجابات أو حذفها وإضافة أسئلة جديدة قبل الحفظ. صور الأسئلة الأصلية تُنسخ مع الأسئلة المحتفظ بها.</p>
    <template x-for="(question, index) in questions" :key="question.key">
        <div style="border:1px solid #e2e8f0;border-radius:12px;padding:16px;margin-top:16px">
            <div class="qd-heading" style="justify-content:space-between">
                <h2 x-text="'السؤال ' + (index + 1)"></h2>
                <button type="button" class="qd-cancel" style="color:#b91c1c" @click="questions.splice(index, 1)">حذف السؤال</button>
            </div>
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
            <label class="qd-label">السؤال بالعربية<textarea class="qd-field" required rows="3" :name="'questions[' + index + '][question_ar]'" x-model="question.question_ar"></textarea></label>
            <label class="qd-label">السؤال بالإنجليزية<textarea class="qd-field" rows="2" :name="'questions[' + index + '][question_en]'" x-model="question.question_en"></textarea></label>
            <template x-if="['multiple_choice', 'true_false'].includes(question.type)">
                <div>
                    <template x-for="(option, optionIndex) in question.options" :key="optionIndex">
                        <div style="border-top:1px solid #e2e8f0;padding-top:12px;margin-top:12px">
                            <label class="qd-label"><span x-text="'الاختيار ' + (optionIndex + 1)"></span><input class="qd-field" required :name="'questions[' + index + '][options][' + optionIndex + '][option_ar]'" x-model="option.option_ar"></label>
                            <label class="qd-label">الاختيار بالإنجليزية<input class="qd-field" :name="'questions[' + index + '][options][' + optionIndex + '][option_en]'" x-model="option.option_en"></label>
                            <label class="qd-label">الإجابة
                                <select class="qd-field" :name="'questions[' + index + '][options][' + optionIndex + '][is_correct]'" x-model="option.is_correct">
                                    <option value="0">غير صحيحة</option><option value="1">صحيحة</option>
                                </select>
                            </label>
                            <button type="button" class="qd-cancel" x-show="question.type === 'multiple_choice' && question.options.length > 2" @click="question.options.splice(optionIndex, 1)">حذف الاختيار</button>
                        </div>
                    </template>
                    <button type="button" class="qd-cancel" style="margin:12px 0" x-show="question.type === 'multiple_choice'" @click="question.options.push({option_ar: '', option_en: '', is_correct: 0})">+ إضافة اختيار</button>
                </div>
            </template>
            <label class="qd-label">شرح الإجابة بالعربية<textarea class="qd-field" rows="2" :name="'questions[' + index + '][explanation_ar]'" x-model="question.explanation_ar"></textarea></label>
            <label class="qd-label">شرح الإجابة بالإنجليزية<textarea class="qd-field" rows="2" :name="'questions[' + index + '][explanation_en]'" x-model="question.explanation_en"></textarea></label>
        </div>
    </template>
    <p class="qd-help" x-show="questions.length === 0">أضف سؤالًا واحدًا على الأقل لحفظ الاختبار.</p>
    <button type="button" class="qd-submit" style="margin-top:16px" @click="addQuestion()">+ إضافة سؤال</button>
</section>
