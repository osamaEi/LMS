<dl class="quiz-metrics" aria-label="معلومات الاختبار">
    <div class="quiz-metric"><dt>نوع الاختبار</dt><dd>{{ $quiz->type_label }}</dd></div>
    <div class="quiz-metric"><dt>عدد الأسئلة</dt><dd>{{ $quiz->questions_count }} سؤال</dd></div>
    <div class="quiz-metric"><dt>المدة المتاحة</dt><dd>{{ $quiz->duration_minutes ? $quiz->duration_minutes . ' دقيقة' : 'بدون وقت محدد' }}</dd></div>
    <div class="quiz-metric"><dt>المحاولات المتبقية</dt><dd>{{ max(0, $quiz->max_attempts - $attempts->count()) }} من {{ $quiz->max_attempts }}</dd></div>
</dl>
@if($attempts->contains(fn ($attempt) => $attempt->isCompleted() && ! $attempt->isReleased()))
    <div class="quiz-review-note" role="status">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div><strong>تم التسليم · بانتظار اعتماد المعلم</strong><p>إجاباتك محفوظة. ستظهر درجة المحاولة بعد مراجعتها واعتمادها من المعلم.</p></div>
    </div>
@endif
