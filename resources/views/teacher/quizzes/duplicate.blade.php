@extends('layouts.dashboard')
@section('title', 'إعادة اختبار لمجموعة أخرى')
@section('content')
<div style="direction:rtl;max-width:820px;margin:auto;background:white;padding:24px;border-radius:16px;">
    <h1 style="font-size:22px;font-weight:700;margin-bottom:12px;">إعادة اختبار لمجموعة أخرى</h1>
    <p style="margin-bottom:20px;">{{ $quiz->title_ar }} — سيتم نسخ الأسئلة والإجابات والدرجات والإعدادات. تبقى نتائج المجموعة الأصلية مستقلة.</p>
    @foreach($errors->all() as $error)
        <p role="alert" style="color:#b91c1c;">{{ $error }}</p>
    @endforeach
    @if($classes->isEmpty())
        <p>لا توجد مجموعات أخرى متاحة لك.</p>
    @else
        <form method="POST" action="{{ route('teacher.quizzes.duplicate.store', $quiz) }}">
            @csrf
            <label for="destination">المجموعة والمقرر / البرنامج</label>
            <select id="destination" name="destination" required style="display:block;width:100%;margin:8px 0 20px;border:1px solid #cbd5e1;border-radius:8px;padding:10px;">
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
            <div style="display:flex;gap:20px;flex-wrap:wrap;margin-bottom:16px;">
                <div><label for="starts_at">موعد البداية الجديد</label><input style="display:block;" type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at') }}"></div>
                <div><label for="ends_at">موعد النهاية الجديد</label><input style="display:block;" type="datetime-local" id="ends_at" name="ends_at" value="{{ old('ends_at') }}"></div>
            </div>
            <p style="color:#64748b;margin-bottom:20px;">عند ترك المواعيد فارغة، يتاح الاختبار فورًا دون موعد انتهاء.</p>
            <button type="submit" style="background:#0071AA;color:white;padding:10px 20px;border-radius:8px;">إعادة الاختبار للمجموعة</button>
        </form>
    @endif
    <a style="display:inline-block;margin-top:20px;" href="{{ route('teacher.quizzes.overview') }}">العودة للاختبارات</a>
</div>
@endsection
