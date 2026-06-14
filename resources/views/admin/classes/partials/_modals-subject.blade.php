{{-- MODAL: Add Term --}}
<template x-teleport="body">
<div x-show="termModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="termModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:460px;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <div style="background:linear-gradient(135deg,#1a3a5c,#2563eb);border-radius:18px 18px 0 0;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:15px;font-weight:700;color:white;margin:0;">إضافة ربع دراسي للمجموعة</h3>
            <button @click="termModal=false" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:white;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.terms.store') }}" method="POST">
            @csrf
            <input type="hidden" name="program_id" value="{{ $class->program_id }}">
            <input type="hidden" name="class_id" value="{{ $class->id }}">
            <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">اسم الربع *</label>
                        <input type="text" name="name_ar" required placeholder="الفصل التدريبي الأول" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">رقم الربع *</label>
                        <input type="number" name="term_number" required min="1" value="{{ $class->terms->count() + 1 }}" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">تاريخ البداية</label>
                        <input type="date" name="start_date" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">تاريخ النهاية</label>
                        <input type="date" name="end_date" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الحالة</label>
                    <select name="status" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:white;">
                        <option value="upcoming">قادم</option>
                        <option value="active">نشط</option>
                        <option value="completed">مكتمل</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9;">
                <button type="button" @click="termModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#1a3a5c,#2563eb);border:none;border-radius:10px;cursor:pointer;">إضافة الربع</button>
            </div>
        </form>
    </div>
</div>
</template>

{{-- MODAL: Attach existing program subject --}}
<template x-teleport="body">
<div x-show="attachModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="attachModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:460px;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <div style="background:linear-gradient(135deg,#7c3aed,#8b5cf6);border-radius:18px 18px 0 0;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <h3 style="font-size:15px;font-weight:700;color:white;margin:0;">اختيار مقرر من البرنامج</h3>
                <p style="font-size:11px;color:rgba(255,255,255,.7);margin:2px 0 0;" x-text="currentTermName"></p>
            </div>
            <button @click="attachModal=false" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:white;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.classes.attach-subject', $class->id) }}" method="POST">
            @csrf
            <input type="hidden" name="term_id" :value="currentTermId">
            <div style="padding:20px;">
                <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">المقرر</label>
                <select name="subject_id" required style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:white;">
                    <option value="">— اختر مقرر —</option>
                    @foreach($programSubjects->whereNotIn('id', $usedSubjectIds) as $s)
                        <option value="{{ $s->id }}">{{ ($s->name_ar ?: $s->name_en) }} ({{ $s->code }})</option>
                    @endforeach
                </select>
                <p style="font-size:11px;color:#94a3b8;margin-top:8px;">المواد المُسندة لربع آخر في هذه المجموعة لا تظهر هنا.</p>
                @if($programSubjects->whereNotIn('id', $usedSubjectIds)->isEmpty())
                <p style="font-size:12px;color:#dc2626;margin-top:6px;">كل مواد الدبلومة مُسندة بالفعل لأرباع هذه المجموعة.</p>
                @endif
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9;">
                <button type="button" @click="attachModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#7c3aed,#8b5cf6);border:none;border-radius:10px;cursor:pointer;">إضافة المقرر</button>
            </div>
        </form>
    </div>
</div>
</template>

{{-- MODAL: Add Subject (new) --}}
<template x-teleport="body">
<div x-show="subjectModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="subjectModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:500px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <div style="background:linear-gradient(135deg,#7c3aed,#8b5cf6);border-radius:18px 18px 0 0;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div>
                <h3 style="font-size:15px;font-weight:700;color:white;margin:0;">إضافة مقرر جديد</h3>
                <p style="font-size:11px;color:rgba(255,255,255,.7);margin:2px 0 0;" x-text="currentTermName"></p>
            </div>
            <button @click="subjectModal=false" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:white;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.subjects.store') }}" method="POST" style="display:flex;flex-direction:column;overflow:hidden;flex:1;">
            @csrf
            <input type="hidden" name="program_id" value="{{ $class->program_id }}">
            <input type="hidden" name="class_id" value="{{ $class->id }}">
            <input type="hidden" name="term_id" :value="currentTermId">
            <div style="padding:20px;display:flex;flex-direction:column;gap:14px;overflow-y:auto;flex:1;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">كود المقرر *</label>
                        <input type="text" name="code" required dir="ltr" placeholder="MATH-101" style="width:100%;padding:9px 12px;font-size:13px;font-family:monospace;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الساعات المعتمدة</label>
                        <input type="number" name="credits" min="1" max="12" value="3" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الاسم العربي *</label>
                    <input type="text" name="name_ar" required placeholder="مبادئ الرياضيات" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الاسم الإنجليزي</label>
                    <input type="text" name="name_en" dir="ltr" placeholder="Mathematics Fundamentals" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">المدرب (اختياري)</label>
                        <select name="teacher_id" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:white;">
                            <option value="">— بدون مدرب —</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الحالة</label>
                        <select name="status" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:white;">
                            <option value="inactive" selected>مقفل</option>
                            <option value="active">نشط</option>
                        </select>
                    </div>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9;flex-shrink:0;">
                <button type="button" @click="subjectModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#7c3aed,#8b5cf6);border:none;border-radius:10px;cursor:pointer;">إضافة المقرر</button>
            </div>
        </form>
    </div>
</div>
</template>

{{-- MODAL: Edit Subject --}}
<template x-teleport="body">
<div x-show="editSubjectModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="editSubjectModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:460px;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <div style="background:linear-gradient(135deg,#2563eb,#3b82f6);border-radius:18px 18px 0 0;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:15px;font-weight:700;color:white;margin:0;">تعديل المقرر</h3>
            <button @click="editSubjectModal=false" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:white;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form :action="'/admin/subjects/' + editSubject.id" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="program_id" value="{{ $class->program_id }}">
            <input type="hidden" name="class_id" value="{{ $class->id }}">
            <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">كود المقرر *</label>
                        <input type="text" name="code" required dir="ltr" x-model="editSubject.code" style="width:100%;padding:9px 12px;font-size:13px;font-family:monospace;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الساعات المعتمدة</label>
                        <input type="number" name="credits" min="1" max="12" x-model="editSubject.credits" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الاسم العربي *</label>
                    <input type="text" name="name_ar" required x-model="editSubject.name_ar" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الاسم الإنجليزي</label>
                    <input type="text" name="name_en" dir="ltr" x-model="editSubject.name_en" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9;">
                <button type="button" @click="editSubjectModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#2563eb,#3b82f6);border:none;border-radius:10px;cursor:pointer;">حفظ</button>
            </div>
        </form>
    </div>
</div>
</template>

{{-- MODAL: Delete Subject --}}
<template x-teleport="body">
<div x-show="deleteSubjectModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="deleteSubjectModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:400px;padding:24px;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <p style="font-size:14px;color:#1e293b;margin-bottom:18px;">هل تريد حذف المقرر <strong x-text="currentSubjectName"></strong>؟</p>
        <form :action="'/admin/subjects/' + currentSubjectId" method="POST" style="display:flex;justify-content:flex-end;gap:8px;">
            @csrf @method('DELETE')
            <button type="button" @click="deleteSubjectModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
            <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:#ef4444;border:none;border-radius:10px;cursor:pointer;">حذف</button>
        </form>
    </div>
</div>
</template>

{{-- MODAL: Assign Teachers --}}
<template x-teleport="body">
<div x-show="teacherModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="teacherModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:440px;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <div style="background:linear-gradient(135deg,#4338ca,#6366f1);border-radius:18px 18px 0 0;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <h3 style="font-size:15px;font-weight:700;color:white;margin:0;">تعيين المدربين</h3>
                <p style="font-size:11px;color:rgba(255,255,255,.7);margin:2px 0 0;" x-text="currentSubjectName"></p>
            </div>
            <button @click="teacherModal=false" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:white;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form :action="'/admin/subjects/' + currentSubjectId + '/assign-teachers'" method="POST">
            @csrf
            <div style="padding:20px;display:flex;flex-direction:column;gap:10px;max-height:50vh;overflow-y:auto;">
                <template x-for="t in currentTeacherList" :key="t.id">
                    <label style="display:flex;align-items:center;gap:10px;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;">
                        <input type="checkbox" name="teacher_ids[]" :value="t.id"
                               :checked="currentTeacherIds.includes(String(t.id))"
                               style="width:15px;height:15px;accent-color:#6366f1;">
                        <span style="font-size:13px;font-weight:600;color:#1e293b;" x-text="t.name"></span>
                    </label>
                </template>
                <p x-show="!currentTeacherList.length" style="text-align:center;font-size:13px;color:#94a3b8;padding:12px 0;">لا يوجد مدربون مسندون لهذا المقرر</p>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9;">
                <button type="button" @click="teacherModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#4338ca,#6366f1);border:none;border-radius:10px;cursor:pointer;">حفظ</button>
            </div>
        </form>
    </div>
</div>
</template>
