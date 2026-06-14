{{-- MODAL: Edit Session (reschedule) --}}
<div id="editSessModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);align-items:center;justify-content:center;padding:1rem;">
    <div style="background:white;border-radius:18px;width:100%;max-width:420px;box-shadow:0 30px 60px rgba(0,0,0,.2);overflow:hidden;">
        <div style="background:linear-gradient(135deg,#0071AA,#004d77);padding:18px 22px;display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:15px;font-weight:700;color:white;margin:0;">تعديل موعد الجلسة</h3>
            <button onclick="closeEditSession()" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:30px;height:30px;color:white;cursor:pointer;font-size:16px;">×</button>
        </div>
        <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
            <input type="hidden" id="editSessId">
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">التاريخ *</label>
                <input type="date" id="editSessDate" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
            </div>
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الفترة (الوقت) *</label>
                <select id="editSessTime" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:white;">
                    <option value="">— اختر الفترة —</option>
                    <option value="08:10">الفترة الصباحية (1) — 8:10</option>
                    <option value="09:30">الفترة الصباحية (2) — 9:30</option>
                    <option value="10:50">الفترة الصباحية (3) — 10:50</option>
                    <option value="12:20">الفترة المسائية (1) — 12:20</option>
                    <option value="13:35">الفترة المسائية (2) — 1:35</option>
                    <option value="14:50">الفترة المسائية (3) — 2:50</option>
                    <option value="16:00">الفترة المسائية (4) — 4:00</option>
                </select>
            </div>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9;">
            <button onclick="closeEditSession()" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
            <button onclick="submitEditSession()" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#0071AA,#004d77);border:none;border-radius:10px;cursor:pointer;">حفظ</button>
        </div>
    </div>
</div>

{{-- MODAL: Generate Sessions --}}
<template x-teleport="body">
<div x-show="sessionModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="sessionModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:520px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <div style="background:linear-gradient(135deg,#0071AA,#004d77);border-radius:18px 18px 0 0;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <h3 style="font-size:15px;font-weight:700;color:white;margin:0;">إنشاء جلسات للمجموعة</h3>
            <button @click="sessionModal=false" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:white;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.classes.generate-sessions', $class->id) }}" method="POST" style="display:flex;flex-direction:column;overflow:hidden;flex:1;">
            @csrf
            @php
                $termEndMap = $classSubjects->mapWithKeys(fn($cs) => [$cs->id => optional($cs->term?->end_date)->format('Y-m-d')]);
                $classEnd   = $class->end_date?->format('Y-m-d');
                $subjectTeachersMap = $classSubjects->mapWithKeys(function ($cs) {
                    $teachers = $cs->teachers->isNotEmpty() ? $cs->teachers : ($cs->teacher ? collect([$cs->teacher]) : collect());
                    return [$cs->id => $teachers->map(fn($t) => ['id' => $t->id, 'name' => $t->name])->values()];
                });
            @endphp
            <div x-data="{
                    subjectId: '',
                    termEnds: {{ $termEndMap->toJson() }},
                    subjectTeachers: {{ $subjectTeachersMap->toJson() }},
                    classEnd: '{{ $classEnd }}',
                    isDiploma: {{ ($class->program && $class->program->type === 'diploma') ? 'true' : 'false' }},
                    get endDate() { return this.isDiploma ? (this.termEnds[this.subjectId] || '') : this.classEnd; },
                    get teacherOptions() { return this.isDiploma ? (this.subjectTeachers[this.subjectId] || []) : @js($teachers->map(fn($t)=>['id'=>$t->id,'name'=>$t->name])->values()); }
                }" style="padding:20px;display:flex;flex-direction:column;gap:14px;overflow-y:auto;flex:1;">

                @if($class->program && $class->program->type === 'diploma')
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">المقرر *</label>
                    <select name="subject_id" x-model="subjectId" required style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:white;">
                        <option value="">— اختر مقرر —</option>
                        @foreach($classSubjects as $cs)
                            <option value="{{ $cs->id }}">{{ ($cs->name_ar ?: $cs->name_en) }} ({{ $cs->code }})</option>
                        @endforeach
                    </select>
                    @if($classSubjects->isEmpty())
                    <p style="font-size:11px;color:#dc2626;margin-top:6px;">أضف مواد لأرباع المجموعة أولاً.</p>
                    @endif
                </div>
                @endif

                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">المدرب *</label>
                    <select name="teacher_id" required style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:white;">
                        <option value="">— اختر المدرب —</option>
                        <template x-for="t in teacherOptions" :key="t.id">
                            <option :value="t.id" x-text="t.name"></option>
                        </template>
                    </select>
                    <template x-if="isDiploma && subjectId && teacherOptions.length === 0">
                        <p style="font-size:11px;color:#dc2626;margin-top:6px;">لا يوجد مدرب معيّن لهذا المقرر — عيّن مدربًا للمقرر أولاً.</p>
                    </template>
                    <template x-if="!isDiploma && teacherOptions.length === 0">
                        <p style="font-size:11px;color:#dc2626;margin-top:6px;">لا يوجد مدربون مسجّلون لهذه الدورة — سجّل مدربًا للبرنامج أولاً.</p>
                    </template>
                </div>

                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">أيام الأسبوع *</label>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;">
                        @foreach(['الأحد'=>0,'الإثنين'=>1,'الثلاثاء'=>2,'الأربعاء'=>3,'الخميس'=>4,'الجمعة'=>5,'السبت'=>6] as $dName=>$dVal)
                        <label style="display:inline-flex;align-items:center;gap:5px;padding:6px 10px;border:1.5px solid #e2e8f0;border-radius:8px;cursor:pointer;font-size:12px;font-weight:600;color:#475569;">
                            <input type="checkbox" name="days[]" value="{{ $dVal }}" style="accent-color:#0071AA;">
                            {{ $dName }}
                        </label>
                        @endforeach
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">تبدأ من *</label>
                        <input type="date" name="start_date" required value="{{ $class->start_date?->format('Y-m-d') }}" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الفترة (الوقت) *</label>
                        <select name="time" required style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:white;">
                            <option value="">— اختر الفترة —</option>
                            <option value="08:10">الفترة الصباحية (1) — 8:10</option>
                            <option value="09:30">الفترة الصباحية (2) — 9:30</option>
                            <option value="10:50">الفترة الصباحية (3) — 10:50</option>
                            <option value="12:20">الفترة المسائية (1) — 12:20</option>
                            <option value="13:35">الفترة المسائية (2) — 1:35</option>
                            <option value="14:50">الفترة المسائية (3) — 2:50</option>
                            <option value="16:00">الفترة المسائية (4) — 4:00</option>
                        </select>
                    </div>
                </div>

                <div x-show="endDate" style="display:flex;align-items:center;gap:8px;background:#eff6ff;border:1px solid #dbeafe;border-radius:10px;padding:9px 12px;font-size:12px;color:#1e40af;">
                    <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>الجلسات أسبوعية وتنتهي تلقائيًا بنهاية {{ $class->program && $class->program->type === 'diploma' ? 'الربع' : 'المجموعة' }}: <strong x-text="endDate"></strong></span>
                </div>

                <div x-show="!endDate" x-cloak>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">تنتهي في *</label>
                    <input type="date" name="end_date" :required="!endDate" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    <p style="font-size:11px;color:#b45309;margin-top:6px;">@if($class->program && $class->program->type === 'diploma')لا يوجد تاريخ نهاية للربع — حدّد تاريخ نهاية الجلسات.@else لا يوجد تاريخ نهاية للمجموعة — حدّد تاريخ نهاية الجلسات.@endif</p>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9;flex-shrink:0;">
                <button type="button" @click="sessionModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#0071AA,#004d77);border:none;border-radius:10px;cursor:pointer;">إنشاء الجلسات</button>
            </div>
        </form>
    </div>
</div>
</template>
