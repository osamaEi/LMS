@extends('layouts.dashboard')

@section('title', 'إنشاء اختبار')

@section('content')
@php
    // Map each subject to its selectable classes for the dependent dropdown.
    $subjectClasses = $subjects->mapWithKeys(fn($s) => [$s['id'] => $s['classes']->values()]);
@endphp
<div style="direction:rtl;max-width:820px;margin:0 auto;">

    {{-- Header --}}
    <div style="background:linear-gradient(135deg,#0071AA,#004d77);border-radius:18px;padding:22px 26px;color:#fff;margin-bottom:20px;">
        <div style="font-size:19px;font-weight:800;">إنشاء اختبار جديد</div>
        <div style="font-size:13px;opacity:.85;margin-top:4px;">اختر المقرر ثم الفصل المستهدف — سيظهر الاختبار لطلاب هذا الفصل.</div>
    </div>

    @if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;border-radius:12px;padding:14px 18px;margin-bottom:16px;font-size:13px;">
        @foreach($errors->all() as $error)
            <div>• {{ $error }}</div>
        @endforeach
    </div>
    @endif

    @if($subjects->isEmpty())
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:30px;text-align:center;color:#64748b;">
        لا توجد مقررات مُسندة إليك لإنشاء اختبار.
    </div>
    @else
    <form method="POST" action="{{ route('teacher.quizzes.store-global') }}"
          x-data="{
              subjectClasses: {{ $subjectClasses->toJson() }},
              subjectId: '{{ old('subject_id') }}',
              get classes() { return this.subjectClasses[this.subjectId] || []; }
          }"
          style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:24px;display:flex;flex-direction:column;gap:18px;box-shadow:0 2px 12px rgba(0,0,0,.04);">
        @csrf

        {{-- Subject --}}
        <div>
            <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">المقرر *</label>
            <select name="subject_id" x-model="subjectId" required
                    style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;background:#fff;">
                <option value="">— اختر المقرر —</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject['id'] }}">{{ $subject['name'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Target class (depends on subject) --}}
        <div>
            <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">الفصل المستهدف</label>
            <select name="class_id"
                    style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;background:#fff;">
                <option value="">كل فصول المقرر</option>
                <template x-for="c in classes" :key="c.id">
                    <option :value="c.id" x-text="c.name"></option>
                </template>
            </select>
            <p style="font-size:11px;color:#94a3b8;margin-top:5px;">اتركه فارغًا ليظهر الاختبار لكل طلاب المقرر، أو اختر فصلًا لحصره على طلابه فقط.</p>
        </div>

        {{-- Title --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">عنوان الاختبار (عربي) *</label>
                <input type="text" name="title_ar" value="{{ old('title_ar') }}" required
                       style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">العنوان (إنجليزي)</label>
                <input type="text" name="title_en" value="{{ old('title_en') }}" dir="ltr"
                       style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
            </div>
        </div>

        {{-- Description --}}
        <div>
            <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">الوصف</label>
            <textarea name="description_ar" rows="2"
                      style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;resize:vertical;">{{ old('description_ar') }}</textarea>
        </div>

        {{-- Type + duration --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">النوع *</label>
                <select name="type" required
                        style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;background:#fff;">
                    <option value="exam" {{ old('type')==='exam'?'selected':'' }}>امتحان</option>
                    <option value="quiz" {{ old('type')==='quiz'?'selected':'' }}>اختبار قصير</option>
                    <option value="midterm" {{ old('type')==='midterm'?'selected':'' }}>اختبار نصفي</option>
                    <option value="homework" {{ old('type')==='homework'?'selected':'' }}>واجب</option>
                    <option value="paper" {{ old('type')==='paper'?'selected':'' }}>ورقة أعمال</option>
                </select>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">المدة (دقيقة)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" min="1"
                       style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
            </div>
        </div>

        {{-- Marks --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">الدرجة الكلية *</label>
                <input type="number" step="0.01" name="total_marks" value="{{ old('total_marks', 100) }}" min="1" required
                       style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">درجة النجاح *</label>
                <input type="number" step="0.01" name="pass_marks" value="{{ old('pass_marks', 50) }}" min="0" required
                       style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">عدد المحاولات *</label>
                <input type="number" name="max_attempts" value="{{ old('max_attempts', 1) }}" min="1" required
                       style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
            </div>
        </div>

        {{-- Schedule --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">يبدأ في</label>
                <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}"
                       style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">ينتهي في</label>
                <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}"
                       style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;justify-content:flex-end;gap:10px;border-top:1px solid #f1f5f9;padding-top:16px;">
            <a href="{{ route('teacher.quizzes.overview') }}"
               style="padding:10px 20px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border-radius:10px;text-decoration:none;">إلغاء</a>
            <button type="submit"
                    style="padding:10px 24px;font-size:13px;font-weight:800;color:#fff;background:linear-gradient(135deg,#0071AA,#004d77);border:none;border-radius:10px;cursor:pointer;">
                إنشاء ومتابعة لإضافة الأسئلة
            </button>
        </div>
    </form>
    @endif
</div>
@endsection
