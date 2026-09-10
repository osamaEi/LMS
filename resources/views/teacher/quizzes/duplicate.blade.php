@extends('layouts.dashboard')
@section('title', 'إنشاء اختبار جديد من اختبار سابق')
@push('styles')
@include('teacher.quizzes.partials.duplicate-styles')
@endpush
@section('content')
<div class="qd">
    <a class="qd-back" href="{{ route('teacher.quizzes.overview') }}"><span aria-hidden="true">→</span> العودة للاختبارات</a>
    <header class="qd-hero">
        <div class="qd-icon" aria-hidden="true"><svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><rect x="8" y="8" width="12" height="13" rx="2"/><path stroke-linecap="round" d="M16 4V3a1 1 0 0 0-1-1H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h1"/></svg></div>
        <div><span class="qd-hero-label">إعداد اختبار جديد</span><h1>ابدأ من اختبارك السابق</h1><p>خصّص البيانات والأسئلة لتناسب مجموعتك، ثم احفظ الاختبار الجديد.</p></div>
    </header>
    <nav class="qd-nav" aria-label="أقسام نموذج الاختبار">
        <a href="#quiz-destination"><span>01</span> المجموعة</a>
        <a href="#quiz-settings"><span>02</span> بيانات الاختبار</a>
        <a href="#quiz-schedule"><span>03</span> المواعيد</a>
        <a href="#quiz-questions"><span>04</span> الأسئلة</a>
    </nav>
    @if($errors->any())
        <div class="qd-error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
    @endif
    <div class="qd-layout">
    <div class="qd-panel">
    @if($classes->isEmpty())
        <div class="qd-empty"><h2>لا توجد مجموعات أخرى متاحة</h2><p>ستظهر هنا المجموعات الأخرى المسندة إليك لاختيار المجموعة المستهدفة.</p><a class="qd-back" href="{{ route('teacher.quizzes.overview') }}">العودة للاختبارات</a></div>
    @else
        <form method="POST" enctype="multipart/form-data" action="{{ route('teacher.quizzes.duplicate.store', $quiz) }}" onsubmit="const button=this.querySelector('[type=submit]');button.disabled=true;button.textContent='جارٍ إنشاء الاختبار…';">
            @csrf
            <section class="qd-section" id="quiz-destination">
            <div class="qd-heading"><span class="qd-step">1</span><h2>اختر المجموعة المستهدفة</h2></div>
            <label class="qd-label" for="destination">المجموعة والمقرر / البرنامج</label>
            <select class="qd-field" id="destination" name="destination" required aria-describedby="destination-help" @if($errors->has('destination')) aria-invalid="true" @endif>
                <option value="">اختر المجموعة والمقرر</option>
                @foreach($classes as $class)
                    <optgroup label="{{ $class['name'] }}">
                        @foreach($class['subjects'] as $subject)
                            @php $value = $class['id'] . ':subject:' . $subject['id']; @endphp
                            <option value="{{ $value }}" @selected(old('destination') === $value)>{{ $class['name'] }} — {{ $subject['name'] }}</option>
                        @endforeach
                        @if($class['program'])
                            @php $value = $class['id'] . ':program:' . $class['program']['id']; @endphp
                            <option value="{{ $value }}" @selected(old('destination') === $value)>{{ $class['name'] }} — {{ $class['program']['name'] }}</option>
                        @endif
                    </optgroup>
                @endforeach
            </select>
            <p class="qd-help" id="destination-help">سيظهر الاختبار لطلاب المجموعة التي تختارها.</p>
            </section>
            <section class="qd-section" id="quiz-settings">
            <div class="qd-heading"><span class="qd-step">2</span><h2>بيانات الاختبار الجديد</h2></div>
            <div class="qd-dates">
                <div><label class="qd-label" for="title_ar">اسم الاختبار بالعربية</label><input class="qd-field" id="title_ar" name="title_ar" maxlength="255" required value="{{ old('title_ar', $quiz->title_ar) }}"></div>
                <div><label class="qd-label" for="title_en">اسم الاختبار بالإنجليزية <span class="qd-optional">اختياري</span></label><input class="qd-field" dir="ltr" id="title_en" name="title_en" maxlength="255" value="{{ old('title_en', $quiz->title_en) }}"></div>
            </div>
            <label class="qd-label" for="description_ar" style="margin-top:16px">الوصف بالعربية</label>
            <textarea class="qd-field" id="description_ar" name="description_ar" rows="3">{{ old('description_ar', $quiz->description_ar) }}</textarea>
            <label class="qd-label" for="description_en" style="margin-top:16px">الوصف بالإنجليزية</label>
            <textarea class="qd-field" dir="ltr" id="description_en" name="description_en" rows="3">{{ old('description_en', $quiz->description_en) }}</textarea>
            <label class="qd-label" for="type">نوع الاختبار</label>
            <select class="qd-field" id="type" name="type" required>
                @foreach(['quiz' => 'اختبار قصير', 'midterm' => 'اختبار نصفي', 'exam' => 'امتحان', 'homework' => 'واجب', 'paper' => 'ورقة أعمال'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('type', $quiz->type) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="qd-dates" style="margin-top:16px;">
                <div><label class="qd-label" for="total_marks">الدرجة الكلية</label><input class="qd-field" type="number" id="total_marks" name="total_marks" min="1" step="0.01" required value="{{ old('total_marks', $quiz->total_marks) }}"></div>
                <div><label class="qd-label" for="duration_minutes">مدة الاختبار بالدقائق <span class="qd-optional">اختياري</span></label><input class="qd-field" type="number" id="duration_minutes" name="duration_minutes" min="1" step="1" placeholder="بدون مدة محددة" value="{{ old('duration_minutes', $quiz->duration_minutes) }}"></div>
            </div>
            <p class="qd-help">تُطبّق هذه الإعدادات على النسخة الجديدة فقط. يمكنك ترك المدة فارغة لاختبار بدون حد زمني.</p>
            <div class="qd-dates" style="margin-top:16px">
                <div><label class="qd-label" for="pass_marks">درجة النجاح</label><input class="qd-field" type="number" id="pass_marks" name="pass_marks" min="0" step="0.01" required value="{{ old('pass_marks', $quiz->pass_marks) }}"></div>
                <div><label class="qd-label" for="max_attempts">عدد المحاولات</label><input class="qd-field" type="number" id="max_attempts" name="max_attempts" min="1" required value="{{ old('max_attempts', $quiz->max_attempts) }}"></div>
            </div>
            <fieldset class="qd-preferences"><legend>إعدادات العرض والإتاحة</legend>
            @foreach(['shuffle_questions' => 'ترتيب عشوائي للأسئلة', 'shuffle_answers' => 'ترتيب عشوائي للإجابات', 'show_results' => 'عرض النتائج', 'show_correct_answers' => 'عرض الإجابات الصحيحة', 'is_active' => 'تفعيل الاختبار'] as $field => $label)
                <input type="hidden" name="{{ $field }}" value="0">
                <label class="qd-toggle"><span>{{ $label }}</span><input type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $field === 'is_active' ? true : $quiz->$field))><span class="qd-switch" aria-hidden="true"></span></label>
            @endforeach
            </fieldset>
            </section>
            <section class="qd-section" id="quiz-schedule">
            <div class="qd-heading"><span class="qd-step">3</span><h2>حدّد مواعيد الاختبار <span class="qd-optional">اختياري</span></h2></div>
            <div class="qd-dates">
                <div><label class="qd-label" for="starts_at">موعد البداية الجديد</label><input class="qd-field" dir="ltr" type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at') }}" aria-describedby="schedule-help"></div>
                <div><label class="qd-label" for="ends_at">موعد النهاية الجديد</label><input class="qd-field" dir="ltr" type="datetime-local" id="ends_at" name="ends_at" value="{{ old('ends_at') }}" aria-describedby="schedule-help"></div>
            </div>
            <p class="qd-note" id="schedule-help">عند تفعيل الاختبار وترك المواعيد فارغة، يتاح فورًا دون موعد انتهاء.</p>
            </section>
            @include('teacher.quizzes.partials.duplicate-questions')
            <div class="qd-actions"><div class="qd-save-note"><strong>جاهز لحفظ الاختبار؟</strong><span>سيُحفظ كاختبار مستقل للمجموعة المختارة.</span></div><button class="qd-submit" type="submit">حفظ اختبار جديد <span aria-hidden="true">←</span></button><a class="qd-cancel" href="{{ route('teacher.quizzes.overview') }}">إلغاء</a></div>
        </form>
    @endif
    </div>
    <aside class="qd-panel qd-summary" aria-label="ملخص الاختبار">
        <div class="qd-eyebrow">إعدادات الاختبار الأصلي</div>
        <div class="qd-title">{{ $quiz->title_ar }}</div>
        <div class="qd-stat"><span>عدد الأسئلة</span><strong>{{ $quiz->questions->count() }} سؤال</strong></div>
        <div class="qd-stat"><span>نوع الاختبار</span><strong>{{ $quiz->type_label }}</strong></div>
        <div class="qd-stat"><span>الدرجة الكلية</span><strong>{{ $quiz->total_marks }} درجة</strong></div>
        <div class="qd-stat"><span>مدة الاختبار</span><strong>{{ $quiz->duration_minutes ? $quiz->duration_minutes . ' دقيقة' : 'غير محددة' }}</strong></div>
        <p class="qd-help">تمت تعبئة النموذج ببيانات الاختبار السابق. يمكنك تعديل الاسم والإعدادات وإضافة الأسئلة وتعديلها وحذفها قبل حفظ الاختبار الجديد.</p>
        <p class="qd-summary-note">لكل مجموعة محاولات ونتائج مستقلة. تظل نتائج المجموعة الأصلية محفوظة.</p>
    </aside>
    </div>
</div>
@endsection
