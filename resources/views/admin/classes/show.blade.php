@extends('layouts.dashboard')
@section('title', 'إدارة المجموعة')

@section('content')
<div x-data="{
    termModal: false,
    subjectModal: false,
    teacherModal: false,
    editSubjectModal: false,
    deleteSubjectModal: false,
    attachModal: false,
    currentTermId: null,
    currentTermName: '',
    currentSubjectId: null,
    currentSubjectName: '',
    currentTeacherIds: [],
    editSubject: {},
    classId: '{{ $class->id }}',
    openTermModal()   { this.termModal = true; },
    // In the class screen the table's "مقرر" button picks from the diploma subjects (dropdown)
    openSubjectModal(id, name) { this.openAttachModal(id, name); },
    openAttachModal(id, name)  { this.currentTermId = id; this.currentTermName = name; this.attachModal = true; },
    openTeacherModal(sid, sname, tids) {
        this.currentSubjectId = sid;
        this.currentSubjectName = sname;
        this.currentTeacherIds = (tids || []).map(String);
        this.teacherModal = true;
    },
    openEditSubject(subject) { this.editSubject = subject; this.editSubjectModal = true; },
    openDeleteSubject(id, name) { this.currentSubjectId = id; this.currentSubjectName = name; this.deleteSubjectModal = true; }
}">

{{-- ══ Header ══ --}}
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('admin.programs.show', $class->program_id) }}"
       style="display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;color:#64748b;flex-shrink:0;"
       class="hover:bg-gray-50 transition-colors">
        <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 flex-wrap">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white truncate">{{ $class->name }}</h1>
            @php $stColors = ['active'=>['#dcfce7','#16a34a'],'inactive'=>['#f1f5f9','#64748b'],'completed'=>['#dbeafe','#2563eb']]; $sc=$stColors[$class->status]??['#f1f5f9','#64748b']; @endphp
            <span style="background:{{ $sc[0] }};color:{{ $sc[1] }};border-radius:9999px;padding:.18rem .7rem;font-size:.7rem;font-weight:700;">{{ ['active'=>'نشطة','inactive'=>'غير نشطة','completed'=>'منتهية'][$class->status] ?? $class->status }}</span>
        </div>
        <p class="text-xs text-gray-400 mt-0.5">{{ $class->program->name ?? '' }} · 👥 {{ $class->students_count }} طالب{{ $class->teacher ? ' · '.$class->teacher->name : '' }}</p>
    </div>
    @if($class->program && $class->program->type === 'diploma')
    <button @click="openTermModal()"
            style="display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;background:linear-gradient(135deg,#1a3a5c,#2563eb);color:white;font-size:12px;font-weight:700;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,.3);">
        <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة ربع للمجموعة
    </button>
    @endif
</div>

{{-- ── Flash ── --}}
@if(session('success'))
<div style="display:flex;align-items:center;gap:10px;background:#f0fdf4;border:1px solid #bbf7d0;border-right:4px solid #22c55e;border-radius:12px;padding:12px 16px;margin-bottom:20px;">
    <svg style="width:16px;height:16px;color:#16a34a;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <span style="font-size:13px;font-weight:600;color:#15803d;">{{ session('success') }}</span>
</div>
@endif
@if(session('error') || $errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#b91c1c;">
    {{ session('error') ?? $errors->first() }}
</div>
@endif

{{-- ── Terms list (diploma only — courses/English have no terms) ── --}}
@if($class->program && $class->program->type === 'diploma')
<div class="space-y-5">
    @forelse($class->terms as $term)
        @include('admin.programs.partials.term-block', ['term' => $term, 'classId' => $class->id, 'showTeachers' => false])

        {{-- Inline add-subject (dropdown) under each term --}}
        @php $available = $programSubjects->whereNotIn('id', $usedSubjectIds); @endphp
        <div x-data="{ open: false }" style="margin:-10px 0 4px;">
            <button @click="open = !open" x-show="!open"
                    style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:9px;border:1.5px dashed #c4b5fd;background:#faf5ff;color:#7c3aed;font-size:12px;font-weight:700;cursor:pointer;">
                <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                إضافة مقرر للربع
            </button>

            <form x-show="open" x-cloak action="{{ route('admin.classes.attach-subject', $class->id) }}" method="POST"
                  style="display:flex;align-items:center;gap:8px;background:white;border:1px solid #e2e8f0;border-radius:12px;padding:10px 12px;box-shadow:0 2px 8px rgba(0,0,0,.04);">
                @csrf
                <input type="hidden" name="term_id" value="{{ $term->id }}">
                <select name="subject_id" required
                        style="flex:1;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:9px;outline:none;font-family:inherit;background:white;">
                    <option value="">— اختر مقرر من الدبلومة —</option>
                    @foreach($available as $s)
                        <option value="{{ $s->id }}">{{ ($s->name_ar ?: $s->name_en) }} ({{ $s->code }})</option>
                    @endforeach
                </select>
                <button type="submit" @if($available->isEmpty()) disabled @endif
                        style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#7c3aed,#8b5cf6);border:none;border-radius:9px;cursor:pointer;{{ $available->isEmpty() ? 'opacity:.5;cursor:not-allowed;' : '' }}">
                    حفظ
                </button>
                <button type="button" @click="open = false"
                        style="padding:9px 14px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:9px;cursor:pointer;">إلغاء</button>
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
    <p style="font-size:12px;color:#94a3b8;">الأرباع والمواد الدراسية خاصة بالدبلومات فقط. تُدار جلسات هذه المجموعة من صفحة الجلسات.</p>
</div>
@endif

{{-- ══ MODAL: Add Term (scoped to this class) ══ --}}
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
                        <input type="text" name="name_ar" required placeholder="الفصل التدريبي الأول"
                               style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">رقم الربع *</label>
                        <input type="number" name="term_number" required min="1" value="{{ $class->terms->count() + 1 }}"
                               style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
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

{{-- ══ MODAL: Attach existing program subject ══ --}}
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
                <p style="font-size:11px;color:#94a3b8;margin-top:8px;">المواد المُسندة لربع آخر في هذه المجموعة لا تظهر هنا. سيتم ربط المقرر بهذا الربع فقط.</p>
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

{{-- ══ MODAL: Add Subject (new, scoped to this class) ══ --}}
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
                        <input type="text" name="code" required dir="ltr" placeholder="MATH-101"
                               style="width:100%;padding:9px 12px;font-size:13px;font-family:monospace;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الساعات المعتمدة</label>
                        <input type="number" name="credits" min="1" max="12" value="3"
                               style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الاسم العربي *</label>
                    <input type="text" name="name_ar" required placeholder="مبادئ الرياضيات"
                           style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الاسم الإنجليزي</label>
                    <input type="text" name="name_en" dir="ltr" placeholder="Mathematics Fundamentals"
                           style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
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

{{-- ══ MODAL: Edit Subject ══ --}}
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
                        <input type="text" name="code" required dir="ltr" x-model="editSubject.code"
                               style="width:100%;padding:9px 12px;font-size:13px;font-family:monospace;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الساعات المعتمدة</label>
                        <input type="number" name="credits" min="1" max="12" x-model="editSubject.credits"
                               style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الاسم العربي *</label>
                    <input type="text" name="name_ar" required x-model="editSubject.name_ar"
                           style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الاسم الإنجليزي</label>
                    <input type="text" name="name_en" dir="ltr" x-model="editSubject.name_en"
                           style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;">
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9;">
                <button type="button" @click="editSubjectModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#2563eb,#3b82f6);border:none;border-radius:10px;cursor:pointer;">حفظ</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL: Delete Subject ══ --}}
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

{{-- ══ MODAL: Assign Teachers ══ --}}
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
                @foreach($teachers as $teacher)
                <label style="display:flex;align-items:center;gap:10px;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;">
                    <input type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}"
                           :checked="currentTeacherIds.includes('{{ $teacher->id }}')"
                           style="width:15px;height:15px;accent-color:#6366f1;">
                    <span style="font-size:13px;font-weight:600;color:#1e293b;">{{ $teacher->name }}</span>
                </label>
                @endforeach
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9;">
                <button type="button" @click="teacherModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#4338ca,#6366f1);border:none;border-radius:10px;cursor:pointer;">حفظ</button>
            </div>
        </form>
    </div>
</div>

</div>
@endsection
