@extends('layouts.dashboard')

@section('title', 'تعديل الاختبار')

@section('content')
@php
    // Map each class to its selectable targets (subjects + program), like create.
    $classTargets = ($classes ?? collect())->mapWithKeys(function ($c) {
        $targets = collect();
        foreach ($c['subjects'] as $s) {
            $targets->push(['value' => 'subject:' . $s['id'], 'label' => $s['name']]);
        }
        if (!empty($c['program'])) {
            $targets->push(['value' => 'program:' . $c['program']['id'], 'label' => $c['program']['name'] . ' (برنامج)']);
        }
        return [$c['id'] => $targets->values()];
    });
@endphp
<div style="direction:rtl;max-width:820px;margin:0 auto;">

    {{-- Header --}}
    <div style="background:linear-gradient(135deg,#0071AA,#004d77);border-radius:18px;padding:22px 26px;color:#fff;margin-bottom:20px;">
        <div style="font-size:19px;font-weight:800;">تعديل الاختبار</div>
        <div style="font-size:13px;opacity:.85;margin-top:4px;">{{ $program->name ?? '' }} — يمكنك تعديل بيانات الاختبار وفصله المستهدف.</div>
    </div>

    @if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;border-radius:12px;padding:14px 18px;margin-bottom:16px;font-size:13px;">
        @foreach($errors->all() as $error)
            <div>• {{ $error }}</div>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('teacher.quizzes.program.update', [$program->id, $quiz->id]) }}"
          x-data="{
              classTargets: {{ $classTargets->toJson() }},
              classId: '{{ old('class_id', $quiz->class_id) }}',
              target: '{{ old('target', $currentTarget ?? '') }}',
              get targets() { return this.classTargets[this.classId] || []; },
              onClassChange() { if (!this.targets.some(t => t.value === this.target)) this.target = ''; }
          }"
          style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:24px;display:flex;flex-direction:column;gap:18px;box-shadow:0 2px 12px rgba(0,0,0,.04);">
        @csrf
        @method('PUT')

        {{-- Class (chosen first) --}}
        <div>
            <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">الفصل المستهدف</label>
            <select name="class_id" x-model="classId" @change="onClassChange()"
                    style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;background:#fff;">
                <option value="">— اختر الفصل —</option>
                @foreach(($classes ?? []) as $class)
                    <option value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                @endforeach
            </select>
            <p style="font-size:11px;color:#94a3b8;margin-top:5px;">اتركه كما هو للإبقاء على الفصل الحالي، أو غيّره لنقل الاختبار لفصل آخر.</p>
        </div>

        {{-- Target: subject or program --}}
        <div>
            <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">المقرر / البرنامج</label>
            <select name="target" x-model="target" :disabled="!classId"
                    style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;background:#fff;">
                <option value="">— اختر المقرر أو البرنامج —</option>
                <template x-for="t in targets" :key="t.value">
                    <option :value="t.value" x-text="t.label" :selected="t.value === target"></option>
                </template>
            </select>
        </div>

        {{-- Title --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">عنوان الاختبار (عربي) *</label>
                <input type="text" name="title_ar" value="{{ old('title_ar', $quiz->title_ar) }}" required
                       style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">العنوان (إنجليزي)</label>
                <input type="text" name="title_en" value="{{ old('title_en', $quiz->title_en) }}" dir="ltr"
                       style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
            </div>
        </div>

        {{-- Description --}}
        <div>
            <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">الوصف</label>
            <textarea name="description_ar" rows="2"
                      style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;resize:vertical;">{{ old('description_ar', $quiz->description_ar) }}</textarea>
        </div>

        {{-- Type + duration --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">النوع *</label>
                <select name="type" required
                        style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;background:#fff;">
                    @php $t = old('type', $quiz->type); @endphp
                    <option value="exam" {{ $t==='exam'?'selected':'' }}>امتحان</option>
                    <option value="quiz" {{ $t==='quiz'?'selected':'' }}>اختبار قصير</option>
                    <option value="midterm" {{ $t==='midterm'?'selected':'' }}>اختبار نصفي</option>
                    <option value="homework" {{ $t==='homework'?'selected':'' }}>واجب</option>
                    <option value="paper" {{ $t==='paper'?'selected':'' }}>ورقة أعمال</option>
                </select>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">المدة (دقيقة)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $quiz->duration_minutes) }}" min="1"
                       style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
            </div>
        </div>

        {{-- Marks --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">الدرجة الكلية *</label>
                <input type="number" step="0.5" name="total_marks" value="{{ old('total_marks', $quiz->total_marks) }}" min="1" required
                       style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">درجة النجاح *</label>
                <input type="number" step="0.5" name="pass_marks" value="{{ old('pass_marks', $quiz->pass_marks) }}" min="0" required
                       style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">عدد المحاولات *</label>
                <input type="number" name="max_attempts" value="{{ old('max_attempts', $quiz->max_attempts) }}" min="1" required
                       style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
            </div>
        </div>

        {{-- Schedule --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">يبدأ في</label>
                <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $quiz->starts_at ? $quiz->starts_at->format('Y-m-d\TH:i') : '') }}"
                       style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;">ينتهي في</label>
                <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $quiz->ends_at ? $quiz->ends_at->format('Y-m-d\TH:i') : '') }}"
                       style="width:100%;padding:10px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
            </div>
        </div>

        {{-- Options (checkboxes) --}}
        <div style="border-top:1px solid #f1f5f9;padding-top:16px;">
            <div style="font-size:13px;font-weight:800;color:#334155;margin-bottom:12px;">خيارات الاختبار</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                @php
                    $opts = [
                        ['shuffle_questions', 'ترتيب عشوائي للأسئلة', $quiz->shuffle_questions],
                        ['shuffle_answers', 'ترتيب عشوائي للإجابات', $quiz->shuffle_answers],
                        ['show_results', 'عرض النتيجة بعد التسليم', $quiz->show_results],
                        ['show_correct_answers', 'عرض الإجابات الصحيحة', $quiz->show_correct_answers],
                        ['is_active', 'تفعيل الاختبار', $quiz->is_active],
                    ];
                @endphp
                @foreach($opts as [$name, $label, $current])
                <label style="display:flex;align-items:center;gap:9px;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;font-size:13px;color:#475569;">
                    <input type="checkbox" name="{{ $name }}" value="1" {{ old($name, $current) ? 'checked' : '' }}
                           style="width:16px;height:16px;accent-color:#0071AA;">
                    {{ $label }}
                </label>
                @endforeach
            </div>
        </div>

        {{-- Actions --}}
        <div style="display:flex;justify-content:flex-end;gap:10px;border-top:1px solid #f1f5f9;padding-top:16px;">
            <a href="{{ route('teacher.quizzes.program.show', [$program->id, $quiz->id]) }}"
               style="padding:10px 20px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border-radius:10px;text-decoration:none;">إلغاء</a>
            <button type="submit"
                    style="padding:10px 24px;font-size:13px;font-weight:800;color:#fff;background:linear-gradient(135deg,#0071AA,#004d77);border:none;border-radius:10px;cursor:pointer;">
                حفظ التغييرات
            </button>
        </div>
    </form>
</div>
@endsection
