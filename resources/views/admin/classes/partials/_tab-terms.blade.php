{{-- TAB: Terms & Subjects (diploma only) --}}
<div id="ctab-terms" style="display:none;">
@if($class->program && $class->program->type === 'diploma')
<div class="space-y-5">
    @forelse($class->terms as $term)
        @include('admin.programs.partials.term-block', ['term' => $term, 'classId' => $class->id, 'showTeachers' => false])

        @php $available = $programSubjects->whereNotIn('id', $usedSubjectIds); @endphp
        <div x-data="{ open: false }" style="margin:-10px 0 4px;">
            <button @click="open = !open" x-show="!open"
                    style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:9px;border:1.5px dashed #c4b5fd;background:#faf5ff;color:#7c3aed;font-size:12px;font-weight:700;cursor:pointer;">
                <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                إضافة مقرر للربع
            </button>
            <form x-show="open" x-cloak action="{{ route('admin.classes.attach-subject', $class->id) }}" method="POST"
                  style="background:white;border:1px solid #e2e8f0;border-radius:12px;padding:12px;box-shadow:0 2px 8px rgba(0,0,0,.04);">
                @csrf
                <input type="hidden" name="term_id" value="{{ $term->id }}">
                <div style="font-size:12px;font-weight:700;color:#475569;margin-bottom:8px;">اختر المقررات من الدبلومة</div>
                <div style="display:flex;flex-direction:column;gap:6px;max-height:220px;overflow-y:auto;margin-bottom:10px;">
                    @foreach($available as $s)
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 10px;border:1px solid #f1f5f9;border-radius:8px;cursor:pointer;font-size:13px;color:#1e293b;">
                        <input type="checkbox" name="subject_ids[]" value="{{ $s->id }}" style="width:15px;height:15px;accent-color:#7c3aed;flex-shrink:0;">
                        <span>{{ ($s->name_ar ?: $s->name_en) }} <span style="color:#94a3b8;font-size:11px;">({{ $s->code }})</span></span>
                    </label>
                    @endforeach
                </div>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" @click="open = false"
                            style="padding:9px 14px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:9px;cursor:pointer;">إلغاء</button>
                    <button type="submit" @if($available->isEmpty()) disabled @endif
                            style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#7c3aed,#8b5cf6);border:none;border-radius:9px;cursor:pointer;{{ $available->isEmpty() ? 'opacity:.5;cursor:not-allowed;' : '' }}">
                        حفظ المحدد
                    </button>
                </div>
            </form>
            @if($available->isEmpty())
            <p x-show="open" x-cloak style="font-size:12px;color:#dc2626;margin-top:6px;">كل مواد الدبلومة مُسندة بالفعل لأرباع هذه المجموعة.</p>
            @endif
        </div>
    @empty
    <div style="background:white;border-radius:18px;border:1px solid #e2e8f0;padding:60px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,.04);">
        <div style="width:64px;height:64px;border-radius:18px;background:linear-gradient(135deg,#1a3a5c,#2563eb);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg style="width:28px;height:28px;color:white;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        </div>
        <p style="font-size:15px;font-weight:600;color:#475569;margin-bottom:6px;">لا توجد أرباع خاصة بهذه المجموعة</p>
        <p style="font-size:12px;color:#94a3b8;margin-bottom:20px;">ابدأ بإضافة أول ربع تدريبي للمجموعة</p>
        <button @click="openTermModal()"
                style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:10px;background:linear-gradient(135deg,#1a3a5c,#2563eb);color:white;font-size:13px;font-weight:700;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,.3);">
            <svg style="width:15px;height:15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            إضافة ربع دراسي
        </button>
    </div>
    @endforelse
</div>
@else
<div style="background:white;border-radius:18px;border:1px solid #e2e8f0;padding:48px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,.04);">
    <div style="width:56px;height:56px;border-radius:16px;background:#f0f9ff;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
        <svg style="width:26px;height:26px;color:#0071AA;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    </div>
    <p style="font-size:14px;font-weight:600;color:#475569;margin-bottom:4px;">هذه المجموعة لا تحتوي على أرباع</p>
    <p style="font-size:12px;color:#94a3b8;">الأرباع والمواد خاصة بالدبلومات فقط. أنشئ جلسات هذه المجموعة من تبويب الجلسات.</p>
</div>
@endif
</div>{{-- /ctab-terms --}}
