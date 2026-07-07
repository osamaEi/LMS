<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<style>
    * { font-family: 'DejaVu Sans', sans-serif; }
    body { direction: rtl; color: #111827; font-size: 12px; line-height: 1.7; }
    .header { text-align: center; border-bottom: 2px solid #0071AA; padding-bottom: 10px; margin-bottom: 16px; }
    .header h1 { font-size: 18px; margin: 0 0 4px; color: #0f172a; }
    .meta { font-size: 11px; color: #6b7280; }
    .meta span { margin: 0 8px; }
    .info-row { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 12px; margin-bottom: 16px; font-size: 11px; color: #374151; }
    .student-line { margin: 10px 0 18px; font-size: 12px; }
    .q { margin-bottom: 14px; page-break-inside: avoid; }
    .q-head { font-weight: bold; margin-bottom: 6px; }
    .q-marks { color: #0071AA; font-weight: normal; font-size: 11px; }
    .opt { margin: 3px 0 3px 18px; padding: 3px 8px; border: 1px solid #e5e7eb; border-radius: 5px; }
    .opt.correct { background: #dcfce7; border-color: #86efac; font-weight: bold; }
    .tf { display: inline-block; margin-left: 18px; padding: 3px 14px; border: 1px solid #d1d5db; border-radius: 5px; }
    .tf.correct { background: #dcfce7; border-color: #86efac; font-weight: bold; }
    .answer-space { border-bottom: 1px dotted #9ca3af; height: 22px; margin: 4px 0; }
    .badge { font-size: 10px; background: #eff6ff; color: #1d4ed8; padding: 1px 8px; border-radius: 10px; }
</style>
</head>
<body>

<div class="header">
    <h1>{{ $quiz->title_ar ?: $quiz->title_en }}</h1>
    <div class="meta">
        <span>{{ $subject->name_ar ?? $subject->name_en }}</span>
        @if($subject->code)<span>({{ $subject->code }})</span>@endif
        <span>|</span>
        <span>{{ ['quiz'=>'اختبار قصير','midterm'=>'نصفي','exam'=>'نهائي','homework'=>'واجب','paper'=>'ورقة'][$quiz->type] ?? $quiz->type }}</span>
    </div>
</div>

<div class="info-row">
    الدرجة الكلية: <strong>{{ $quiz->total_marks }}</strong> &nbsp;·&nbsp;
    درجة النجاح: <strong>{{ $quiz->pass_marks }}</strong>
    @if($quiz->duration_minutes) &nbsp;·&nbsp; المدة: <strong>{{ $quiz->duration_minutes }} دقيقة</strong> @endif
    &nbsp;·&nbsp; عدد الأسئلة: <strong>{{ $quiz->questions->count() }}</strong>
    @if($withAnswers) &nbsp;·&nbsp; <span class="badge">نموذج الإجابة</span> @endif
</div>

@unless($withAnswers)
<div class="student-line">
    الاسم: ______________________________ &nbsp;&nbsp;&nbsp; رقم المتدرب: __________________
</div>
@endunless

@if($quiz->description_ar)
<p style="font-size:11px;color:#4b5563;margin-bottom:14px;">{{ $quiz->description_ar }}</p>
@endif

@foreach($quiz->questions as $i => $question)
<div class="q">
    <div class="q-head">
        {{ $i + 1 }}. {{ $question->question_ar ?: $question->question_en }}
        <span class="q-marks">({{ $question->marks }} درجة)</span>
    </div>

    @if($question->type === 'multiple_choice')
        @foreach($question->options as $opt)
        <div class="opt {{ $withAnswers && $opt->is_correct ? 'correct' : '' }}">
            {{ $withAnswers && $opt->is_correct ? '✓ ' : '' }}{{ $opt->option_ar ?: $opt->option_en }}
        </div>
        @endforeach
    @elseif($question->type === 'true_false')
        @foreach($question->options as $opt)
        <span class="tf {{ $withAnswers && $opt->is_correct ? 'correct' : '' }}">
            {{ $withAnswers && $opt->is_correct ? '✓ ' : '' }}{{ $opt->option_ar ?: $opt->option_en }}
        </span>
        @endforeach
    @else
        {{-- short_answer / essay --}}
        @if($withAnswers && $question->explanation_ar)
            <div style="margin:4px 18px;color:#15803d;font-size:11px;">الإجابة النموذجية: {{ $question->explanation_ar }}</div>
        @else
            <div class="answer-space"></div>
            <div class="answer-space"></div>
        @endif
    @endif
</div>
@endforeach

</body>
</html>
