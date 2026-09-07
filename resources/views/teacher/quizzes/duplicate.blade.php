@extends('layouts.dashboard')
@section('title', 'إعادة اختبار لمجموعة أخرى')
@push('styles')
<style>
.qd{direction:rtl;max-width:960px;margin:0 auto 32px;color:#1e293b;font-family:'Cairo',sans-serif}
.qd-back{display:inline-flex;align-items:center;gap:8px;color:#64748b;font-size:13px;text-decoration:none;margin-bottom:18px}
.qd-hero{display:flex;align-items:center;gap:16px;padding:26px;border:1px solid #bae6fd;border-radius:20px;background:linear-gradient(120deg,#f0f9ff,#fff);margin-bottom:22px}
.qd-icon{display:grid;place-items:center;width:54px;height:54px;flex-shrink:0;border-radius:16px;background:#0071aa;color:white}
.qd h1{font-size:23px;font-weight:800;margin:0 0 7px}.qd p{line-height:1.9}.qd-hero p{margin:0;font-size:13px;color:#64748b}
.qd-layout{display:grid;grid-template-columns:minmax(0,1fr) 270px;gap:20px;align-items:start}
.qd-panel{background:white;border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;box-shadow:0 4px 20px #0f172a04}
.qd-section{padding:24px}.qd-section+.qd-section{border-top:1px solid #f1f5f9}
.qd-heading{display:flex;align-items:center;gap:10px;margin-bottom:18px}.qd-step{display:grid;place-items:center;width:29px;height:29px;border-radius:9px;background:#e0f2fe;color:#0071aa;font-size:13px;font-weight:800;flex-shrink:0}
.qd h2{margin:0;font-size:15px;font-weight:800}.qd-label{display:block;font-size:12px;font-weight:700;margin-bottom:9px}.qd-optional{color:#94a3b8;font-weight:400;font-size:11px}
.qd-field{box-sizing:border-box;width:100%;min-width:0;min-height:48px;padding:11px 12px;background:#f8fafc;border:1px solid #cbd5e1;border-radius:10px;color:#334155;font:inherit;font-size:13px;transition:border-color .15s,box-shadow .15s}
.qd-field:focus{outline:none;border-color:#0071aa;box-shadow:0 0 0 3px #e0f2fe;background:#fff}
.qd-help{font-size:12px;color:#64748b;margin:10px 0 0}.qd-dates{display:grid;grid-template-columns:1fr 1fr;gap:14px}.qd-dates>div{min-width:0}
.qd-note{padding:12px 14px;background:#f0f9ff;border-radius:10px;color:#0369a1;font-size:12px;margin:16px 0 0}
.qd-actions{display:flex;align-items:center;gap:16px;padding:20px 24px;border-top:1px solid #e2e8f0;background:#fafcff}
.qd-submit{display:flex;align-items:center;justify-content:center;gap:8px;min-height:46px;padding:11px 22px;border:0;border-radius:10px;background:#0071aa;color:white;font:700 13px 'Cairo',sans-serif;cursor:pointer;box-shadow:0 4px 10px #0071aa20}.qd-submit:hover{background:#005b89}.qd-submit:disabled{opacity:.65;cursor:wait}
.qd-cancel{color:#64748b;font-size:13px;text-decoration:none}.qd-summary{padding:22px}.qd-eyebrow{font-size:11px;font-weight:700;color:#0071aa;margin-bottom:10px}.qd-title{font-size:17px;font-weight:800;line-height:1.8;overflow-wrap:anywhere;margin-bottom:18px}
.qd-stat{display:flex;justify-content:space-between;gap:12px;padding:12px 0;font-size:12px;border-bottom:1px solid #f1f5f9}.qd-stat span{color:#64748b}.qd-stat strong{font-weight:700}.qd-summary-note{background:#f0fdf4;color:#15803d;border-radius:10px;padding:12px;font-size:12px;margin:18px 0 0}
.qd-error{padding:14px 18px;border:1px solid #fecaca;background:#fef2f2;color:#b91c1c;border-radius:12px;margin-bottom:18px;font-size:13px}.qd-error p{margin:0}.qd-empty{text-align:center;padding:44px 24px}.qd-empty p{color:#64748b;font-size:13px}
@media(max-width:760px){.qd-layout{grid-template-columns:1fr}.qd-summary{padding:20px}.qd-hero{padding:20px}.qd h1{font-size:19px}}
@media(max-width:480px){.qd-dates{grid-template-columns:1fr}.qd-section{padding:20px 16px}.qd-actions{padding:18px 16px;flex-direction:column}.qd-submit{width:100%}.qd-hero{align-items:flex-start;gap:12px}.qd-icon{width:42px;height:42px;border-radius:12px}.qd h1{font-size:17px}}
</style>
@endpush
@section('content')
<div class="qd">
    <a class="qd-back" href="{{ route('teacher.quizzes.overview') }}"><span aria-hidden="true">→</span> العودة للاختبارات</a>
    <header class="qd-hero">
        <div class="qd-icon" aria-hidden="true"><svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><rect x="8" y="8" width="12" height="13" rx="2"/><path stroke-linecap="round" d="M16 4V3a1 1 0 0 0-1-1H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h1"/></svg></div>
        <div><h1>إعادة اختبار لمجموعة أخرى</h1><p>استخدم اختبارك مرة أخرى، وحدّد المجموعة والمواعيد المناسبة لها.</p></div>
    </header>
    @if($errors->any())
        <div class="qd-error" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
    @endif
    <div class="qd-layout">
    <div class="qd-panel">
    @if($classes->isEmpty())
        <div class="qd-empty"><h2>لا توجد مجموعات أخرى متاحة</h2><p>ستظهر هنا المجموعات الأخرى المسندة إليك لاختيار المجموعة المستهدفة.</p><a class="qd-back" href="{{ route('teacher.quizzes.overview') }}">العودة للاختبارات</a></div>
    @else
        <form method="POST" action="{{ route('teacher.quizzes.duplicate.store', $quiz) }}" onsubmit="const button=this.querySelector('[type=submit]');button.disabled=true;button.textContent='جارٍ إنشاء نسخة الاختبار…';">
            @csrf
            <section class="qd-section">
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
            <section class="qd-section">
            <div class="qd-heading"><span class="qd-step">2</span><h2>حدّد مواعيد الاختبار <span class="qd-optional">اختياري</span></h2></div>
            <div class="qd-dates">
                <div><label class="qd-label" for="starts_at">موعد البداية الجديد</label><input class="qd-field" dir="ltr" type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at') }}" aria-describedby="schedule-help"></div>
                <div><label class="qd-label" for="ends_at">موعد النهاية الجديد</label><input class="qd-field" dir="ltr" type="datetime-local" id="ends_at" name="ends_at" value="{{ old('ends_at') }}" aria-describedby="schedule-help"></div>
            </div>
            <p class="qd-note" id="schedule-help">عند ترك المواعيد فارغة، يتاح الاختبار فورًا دون موعد انتهاء.</p>
            </section>
            <div class="qd-actions"><button class="qd-submit" type="submit">إنشاء نسخة للمجموعة</button><a class="qd-cancel" href="{{ route('teacher.quizzes.overview') }}">إلغاء</a></div>
        </form>
    @endif
    </div>
    <aside class="qd-panel qd-summary" aria-label="ملخص الاختبار">
        <div class="qd-eyebrow">الاختبار الذي ستتم إعادته</div>
        <div class="qd-title">{{ $quiz->title_ar }}</div>
        <div class="qd-stat"><span>نوع الاختبار</span><strong>{{ $quiz->type_label }}</strong></div>
        <div class="qd-stat"><span>الدرجة الكلية</span><strong>{{ $quiz->total_marks }} درجة</strong></div>
        <div class="qd-stat"><span>مدة الاختبار</span><strong>{{ $quiz->duration_minutes ? $quiz->duration_minutes . ' دقيقة' : 'غير محددة' }}</strong></div>
        <p class="qd-help">تتضمن النسخة نفس الأسئلة والإجابات والدرجات وإعدادات الاختبار الأصلي.</p>
        <p class="qd-summary-note">لكل مجموعة محاولات ونتائج مستقلة. تظل نتائج المجموعة الأصلية محفوظة.</p>
    </aside>
    </div>
</div>
@endsection
