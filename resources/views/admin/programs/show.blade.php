@extends('layouts.dashboard')
@section('title', 'عرض البرنامج')

@section('content')
<div x-data="{
    termModal: false,
    subjectModal: false,
    teacherModal: false,
    editSubjectModal: false,
    deleteSubjectModal: false,
    currentTermId: null,
    currentTermName: '',
    currentTermClassId: '',
    currentSubjectId: null,
    currentSubjectName: '',
    currentTeacherIds: [],
    editSubject: {},
    openTermModal(classId)   { this.currentTermClassId = classId || ''; this.termModal = true; },
    openSubjectModal(id, name, classId) { this.currentTermId = id; this.currentTermName = name; this.currentTermClassId = classId || ''; this.subjectModal = true; },
    openTeacherModal(sid, sname, tids) {
        this.currentSubjectId = sid;
        this.currentSubjectName = sname;
        this.currentTeacherIds = (tids || []).map(String);
        this.teacherModal = true;
    },
    openEditSubject(subject) {
        this.editSubject = subject;
        this.editSubjectModal = true;
    },
    openDeleteSubject(id, name) {
        this.currentSubjectId = id;
        this.currentSubjectName = name;
        this.deleteSubjectModal = true;
    }
}">

{{-- ══ Header ══ --}}
<div class="flex items-center gap-4 mb-6">
    <a href="{{ isset($backRoute) ? route($backRoute) : route('admin.programs.index') }}"
       style="display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;color:#64748b;flex-shrink:0;"
       class="hover:bg-gray-50 transition-colors">
        <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 flex-wrap">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white truncate">{{ $program->name }}</h1>
            @if($program->status === 'active')
                <span style="background:#dcfce7;color:#16a34a;border-radius:9999px;padding:.18rem .7rem;font-size:.7rem;font-weight:700;">● نشط</span>
            @else
                <span style="background:#f1f5f9;color:#64748b;border-radius:9999px;padding:.18rem .7rem;font-size:.7rem;font-weight:700;">○ غير نشط</span>
            @endif
        </div>
        <p class="text-xs text-gray-400 mt-0.5 font-mono" dir="ltr">{{ $program->code }}</p>
    </div>
    @php $editRoute = isset($backRoute) ? str_replace('.index','.edit',$backRoute) : 'admin.programs.edit'; @endphp
    <a href="{{ route($editRoute, $program) }}"
       style="display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;font-size:12px;font-weight:600;color:#374151;text-decoration:none;"
       class="hover:bg-gray-50 transition-colors">
        <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        تعديل البرنامج
    </a>
    @if($program->type === 'diploma')
    <button @click="openTermModal()"
            style="display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;background:linear-gradient(135deg,#1a3a5c,#2563eb);color:white;font-size:12px;font-weight:700;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,.3);">
        <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة ربع
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

{{-- ── Tabs ── --}}
@if(!($showClassesOnly ?? false))
<div style="display:flex;align-items:center;gap:4px;background:#f1f5f9;border-radius:12px;padding:4px;margin-bottom:20px;width:fit-content;">
    @if($program->type === 'diploma')
    @php $programSubjects = $programSubjects ?? collect(); @endphp
    <button onclick="switchTab('terms')" id="tab-btn-terms"
        style="padding:8px 18px;border-radius:9px;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:all .15s;background:white;color:#1e293b;box-shadow:0 1px 4px rgba(0,0,0,.08);">
        المواد الدراسية
        <span style="background:#e2e8f0;color:#64748b;border-radius:9999px;padding:.1rem .5rem;font-size:.65rem;margin-right:4px;font-weight:700;">{{ $programSubjects->count() }}</span>
    </button>
    @endif
    <button onclick="switchTab('classes')" id="tab-btn-classes"
        style="padding:8px 18px;border-radius:9px;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:all .15s;{{ $program->type !== 'diploma' ? 'background:white;color:#1e293b;box-shadow:0 1px 4px rgba(0,0,0,.08);' : 'background:transparent;color:#64748b;' }}">
        المجموعات
        <span id="classes-count-badge" style="background:#e9d5ff;color:#7c3aed;border-radius:9999px;padding:.1rem .5rem;font-size:.65rem;margin-right:4px;font-weight:700;">{{ $classes->count() }}</span>
    </button>
</div>
@endif

{{-- ── Tab: Terms ── --}}
<div id="tab-terms" class="space-y-5" @if(($showClassesOnly ?? false) || $program->type !== 'diploma') style="display:none;" @endif>
    @if($program->type === 'diploma')
    @php $programSubjects = $programSubjects ?? collect(); @endphp
    <div style="background:white;border-radius:18px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.04);">
        {{-- Header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-bottom:1px solid #e2e8f0;">
            <span style="font-size:14px;font-weight:700;color:#1e293b;">المواد الدراسية
                <span style="font-size:11px;color:#94a3b8;margin-right:8px;">· {{ $programSubjects->count() }} مقرر · {{ $programSubjects->sum('credits') }} ساعة</span>
            </span>
            <button @click="openSubjectModal(null, 'مادة جديدة', '')"
                    style="display:flex;align-items:center;gap:5px;padding:7px 12px;border-radius:9px;background:linear-gradient(135deg,#7c3aed,#8b5cf6);color:white;font-size:11px;font-weight:700;border:none;cursor:pointer;">
                <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                إضافة مقرر
            </button>
        </div>

        @if($programSubjects->isNotEmpty())
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="border-bottom:2px solid #f1f5f9;">
                        <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;width:36px;">#</th>
                        <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الكود</th>
                        <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">المقرر</th>
                        <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;" class="hidden md:table-cell">English</th>
                        <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;width:60px;">س.م</th>
                        <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">المدرب/ون</th>
                        <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;width:80px;">الحالة</th>
                        <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;width:90px;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programSubjects as $idx => $subject)
                    @php
                        $allTeachers = $subject->teachers->isNotEmpty()
                            ? $subject->teachers
                            : ($subject->teacher ? collect([$subject->teacher]) : collect());
                    @endphp
                    <tr style="border-bottom:1px solid #f8fafc;transition:background .15s;" onmouseover="this.style.background='#fafbff'" onmouseout="this.style.background=''">
                        <td style="padding:12px 16px;color:#cbd5e1;font-size:11px;">{{ $idx + 1 }}</td>
                        <td style="padding:12px 16px;" dir="ltr">
                            <span style="font-family:monospace;font-size:11px;font-weight:700;color:#2563eb;background:#eff6ff;padding:2px 8px;border-radius:6px;">{{ $subject->code }}</span>
                        </td>
                        <td style="padding:12px 16px;">
                            <span style="font-weight:600;color:#1e293b;">{{ $subject->name_ar ?: $subject->name_en }}</span>
                        </td>
                        <td style="padding:12px 16px;color:#94a3b8;font-size:12px;" dir="ltr" class="hidden md:table-cell">{{ $subject->name_en }}</td>
                        <td style="padding:12px 16px;text-align:center;">
                            <span style="font-size:12px;font-weight:700;color:#475569;background:#f1f5f9;padding:2px 8px;border-radius:9999px;">{{ $subject->credits ?? '—' }}</span>
                        </td>

                        {{-- Teachers cell --}}
                        <td style="padding:12px 16px;">
                            <button @click="openTeacherModal({{ $subject->id }}, '{{ addslashes($subject->name_ar ?: $subject->name_en) }}', {{ json_encode($allTeachers->pluck('id')->all()) }})"
                                    style="display:flex;align-items:center;gap:6px;background:none;border:none;cursor:pointer;padding:0;text-align:right;">
                                @if($allTeachers->isNotEmpty())
                                    <div style="display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
                                        @foreach($allTeachers->take(2) as $t)
                                        <span style="display:inline-flex;align-items:center;gap:4px;background:#f0f4ff;border:1px solid #c7d2fe;border-radius:9999px;padding:3px 8px;font-size:11px;font-weight:600;color:#3730a3;">
                                            <svg style="width:10px;height:10px;color:#6366f1;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            {{ $t->name }}
                                        </span>
                                        @endforeach
                                        @if($allTeachers->count() > 2)
                                        <span style="background:#e0e7ff;color:#4338ca;border-radius:9999px;padding:3px 7px;font-size:10px;font-weight:700;">+{{ $allTeachers->count() - 2 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:4px;color:#94a3b8;font-size:12px;border:1.5px dashed #e2e8f0;border-radius:9999px;padding:3px 10px;">
                                        <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        تعيين مدرب
                                    </span>
                                @endif
                            </button>
                        </td>

                        {{-- Status --}}
                        <td style="padding:12px 16px;text-align:center;">
                            @php $isActive = $subject->status === 'active'; @endphp
                            <div x-data="{ open: false }" class="relative inline-block">
                                <button @click="open = !open" @click.outside="open = false"
                                        style="display:inline-flex;align-items:center;gap:4px;border-radius:9999px;padding:4px 10px;font-size:11px;font-weight:700;border:none;cursor:pointer;{{ $isActive ? 'background:#dcfce7;color:#16a34a;' : 'background:#f1f5f9;color:#64748b;' }}">
                                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $isActive ? '#22c55e' : '#cbd5e1' }};display:inline-block;"></span>
                                    {{ $isActive ? 'نشط' : 'مقفل' }}
                                    <svg style="width:10px;height:10px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" x-cloak
                                     style="position:absolute;left:0;margin-top:4px;width:110px;background:white;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.12);border:1px solid #e2e8f0;z-index:50;overflow:hidden;">
                                    @if(!$isActive)
                                    <form method="POST" action="{{ route('admin.subjects.toggle-status', $subject) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" style="width:100%;text-align:right;padding:8px 12px;font-size:12px;font-weight:600;color:#16a34a;background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:6px;">
                                            <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;flex-shrink:0;"></span> تنشيط
                                        </button>
                                    </form>
                                    @else
                                    <form method="POST" action="{{ route('admin.subjects.toggle-status', $subject) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="inactive">
                                        <button type="submit" style="width:100%;text-align:right;padding:8px 12px;font-size:12px;font-weight:600;color:#64748b;background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:6px;">
                                            <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            قفل
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td style="padding:12px 16px;text-align:center;">
                            <div style="display:flex;align-items:center;justify-content:center;gap:4px;">
                                <button @click="openEditSubject({
                                            id: {{ $subject->id }},
                                            code: '{{ addslashes($subject->code) }}',
                                            name_ar: '{{ addslashes($subject->name_ar) }}',
                                            name_en: '{{ addslashes($subject->name_en) }}',
                                            credits: '{{ $subject->credits }}',
                                            status: '{{ $subject->status }}'
                                        })"
                                        style="display:flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:#f0f4ff;color:#2563eb;border:none;cursor:pointer;"
                                        title="تعديل المقرر">
                                    <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button @click="openDeleteSubject({{ $subject->id }}, '{{ addslashes($subject->name_ar ?: $subject->name_en) }}')"
                                        style="display:flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:#fff1f2;color:#ef4444;border:none;cursor:pointer;"
                                        title="حذف المقرر">
                                    <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding:48px;text-align:center;">
            <div style="width:56px;height:56px;border-radius:16px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                <svg style="width:24px;height:24px;color:#cbd5e1;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <p style="font-size:14px;font-weight:600;color:#475569;margin-bottom:12px;">لا توجد مواد دراسية بعد</p>
            <button @click="openSubjectModal(null, 'مادة جديدة', '')"
                    style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:9px;background:linear-gradient(135deg,#7c3aed,#8b5cf6);color:white;font-size:12px;font-weight:700;border:none;cursor:pointer;">
                <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                إضافة مقرر
            </button>
        </div>
        @endif
    </div>
    @endif
</div>

{{-- ══════════════════════════════════════════
     MODAL: Add Term
══════════════════════════════════════════ --}}
<div x-show="termModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="termModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:460px;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <div style="background:linear-gradient(135deg,#1a3a5c,#2563eb);border-radius:18px 18px 0 0;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:34px;height:34px;border-radius:10px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;">
                    <svg style="width:16px;height:16px;color:white;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 style="font-size:15px;font-weight:700;color:white;margin:0;">إضافة ربع دراسي</h3>
            </div>
            <button @click="termModal=false" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:white;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.terms.store') }}" method="POST">
            @csrf
            <input type="hidden" name="program_id" value="{{ $program->id }}">
            <input type="hidden" name="class_id" :value="currentTermClassId">
            <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">اسم الربع *</label>
                        <input type="text" name="name_ar" required placeholder="الفصل التدريبي الأول"
                               style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;"
                               onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">رقم الربع *</label>
                        <input type="number" name="term_number" required min="1" value="{{ $program->terms->count() + 1 }}"
                               style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;"
                               onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">تاريخ البداية</label>
                        <input type="date" name="start_date"
                               style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;"
                               onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">تاريخ النهاية</label>
                        <input type="date" name="end_date"
                               style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;"
                               onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
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
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#1a3a5c,#2563eb);border:none;border-radius:10px;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,.3);">إضافة الربع</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL: Add Subject
══════════════════════════════════════════ --}}
<div x-show="subjectModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="subjectModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:500px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <div style="background:linear-gradient(135deg,#7c3aed,#8b5cf6);border-radius:18px 18px 0 0;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:34px;height:34px;border-radius:10px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;">
                    <svg style="width:16px;height:16px;color:white;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <h3 style="font-size:15px;font-weight:700;color:white;margin:0;">إضافة مقرر دراسي</h3>
                    <p style="font-size:11px;color:rgba(255,255,255,.7);margin:2px 0 0;" x-text="currentTermName"></p>
                </div>
            </div>
            <button @click="subjectModal=false" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:white;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="{{ route('admin.subjects.store') }}" method="POST" style="display:flex;flex-direction:column;overflow:hidden;flex:1;">
            @csrf
            <input type="hidden" name="program_id" value="{{ $program->id }}">
            <input type="hidden" name="class_id" :value="currentTermClassId">
            <input type="hidden" name="term_id" :value="currentTermId">
            <div style="padding:20px;display:flex;flex-direction:column;gap:14px;overflow-y:auto;flex:1;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">كود المقرر *</label>
                        <input type="text" name="code" required dir="ltr" placeholder="MATH-101"
                               style="width:100%;padding:9px 12px;font-size:13px;font-family:monospace;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;"
                               onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الساعات المعتمدة</label>
                        <input type="number" name="credits" min="1" max="12" value="3"
                               style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;"
                               onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الاسم العربي *</label>
                    <input type="text" name="name_ar" required placeholder="مبادئ الرياضيات"
                           style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;"
                           onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الاسم الإنجليزي</label>
                    <input type="text" name="name_en" dir="ltr" placeholder="Mathematics Fundamentals"
                           style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;"
                           onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">المدرب (اختياري)</label>
                        <select name="teacher_id" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:white;">
                            <option value="">— بدون مدرب —</option>
                            @foreach($teachers ?? [] as $teacher)
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
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#7c3aed,#8b5cf6);border:none;border-radius:10px;cursor:pointer;box-shadow:0 4px 12px rgba(124,58,237,.3);">إضافة المقرر</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL: Edit Subject
══════════════════════════════════════════ --}}
<div x-show="editSubjectModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="editSubjectModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:500px;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <div style="background:linear-gradient(135deg,#0071AA,#2563eb);border-radius:18px 18px 0 0;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:34px;height:34px;border-radius:10px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;">
                    <svg style="width:16px;height:16px;color:white;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h3 style="font-size:15px;font-weight:700;color:white;margin:0;">تعديل المقرر</h3>
                    <p style="font-size:11px;color:rgba(255,255,255,.7);margin:2px 0 0;" x-text="editSubject.name_ar"></p>
                </div>
            </div>
            <button @click="editSubjectModal=false" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:white;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form :action="'/admin/subjects/' + editSubject.id" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="program_id" value="{{ $program->id }}">
            <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">كود المقرر *</label>
                        <input type="text" name="code" required dir="ltr" :value="editSubject.code"
                               style="width:100%;padding:9px 12px;font-size:13px;font-family:monospace;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;"
                               onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الساعات المعتمدة</label>
                        <input type="number" name="credits" min="1" max="12" :value="editSubject.credits"
                               style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;"
                               onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الاسم العربي *</label>
                    <input type="text" name="name_ar" required :value="editSubject.name_ar"
                           style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;"
                           onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الاسم الإنجليزي</label>
                    <input type="text" name="name_en" dir="ltr" :value="editSubject.name_en"
                           style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;"
                           onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:700;color:#475569;margin-bottom:6px;">الحالة</label>
                    <select name="status" style="width:100%;padding:9px 12px;font-size:13px;border:1.5px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:white;">
                        <option value="active" :selected="editSubject.status === 'active'">نشط</option>
                        <option value="inactive" :selected="editSubject.status === 'inactive'">غير نشط</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9;">
                <button type="button" @click="editSubjectModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#0071AA,#2563eb);border:none;border-radius:10px;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,.3);">حفظ التعديلات</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL: Delete Subject
══════════════════════════════════════════ --}}
<div x-show="deleteSubjectModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="deleteSubjectModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:400px;box-shadow:0 30px 60px rgba(0,0,0,.2);text-align:center;padding:32px 28px;">
        <div style="width:56px;height:56px;border-radius:16px;background:#fff1f2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <svg style="width:26px;height:26px;color:#ef4444;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:0 0 8px;">حذف المقرر</h3>
        <p style="font-size:13px;color:#64748b;margin:0 0 6px;">هل أنت متأكد من حذف المقرر:</p>
        <p style="font-size:14px;font-weight:700;color:#ef4444;margin:0 0 20px;" x-text="'«' + currentSubjectName + '»'"></p>
        <p style="font-size:12px;color:#94a3b8;margin:0 0 24px;">لا يمكن التراجع عن هذا الإجراء</p>
        <form :action="'/admin/subjects/' + currentSubjectId" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <div style="display:flex;gap:10px;justify-content:center;">
                <button type="button" @click="deleteSubjectModal=false" style="padding:10px 22px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
                <button type="submit" style="padding:10px 22px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#ef4444,#dc2626);border:none;border-radius:10px;cursor:pointer;box-shadow:0 4px 12px rgba(239,68,68,.3);">نعم، احذف</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════
     MODAL: Assign Teachers (multi)
══════════════════════════════════════════ --}}
<div x-show="teacherModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
    <div @click="teacherModal=false" style="position:absolute;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(2px);"></div>
    <div @click.stop style="position:relative;background:white;border-radius:18px;width:100%;max-width:420px;box-shadow:0 30px 60px rgba(0,0,0,.2);">
        <div style="background:linear-gradient(135deg,#1a3a5c,#2563eb);border-radius:18px 18px 0 0;padding:18px 22px;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:34px;height:34px;border-radius:10px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;">
                    <svg style="width:16px;height:16px;color:white;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h3 style="font-size:15px;font-weight:700;color:white;margin:0;">تعيين المدربين</h3>
                    <p style="font-size:11px;color:rgba(255,255,255,.7);margin:2px 0 0;" x-text="currentSubjectName"></p>
                </div>
            </div>
            <button @click="teacherModal=false" style="background:rgba(255,255,255,.15);border:none;border-radius:8px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:white;">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" :action="'/admin/subjects/' + currentSubjectId + '/assign-teachers'">
            @csrf
            <div style="padding:16px 20px;max-height:280px;overflow-y:auto;">
                <p style="font-size:11px;color:#94a3b8;margin:0 0 12px;">اختر مدرباً واحداً أو أكثر</p>
                <div style="display:flex;flex-direction:column;gap:4px;">
                    @forelse($teachers as $teacher)
                    <label style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;cursor:pointer;border:1.5px solid #f1f5f9;transition:all .15s;"
                           onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                        <input type="checkbox"
                               name="teacher_ids[]"
                               value="{{ $teacher->id }}"
                               x-init="$el.checked = currentTeacherIds.includes('{{ $teacher->id }}')"
                               style="width:16px;height:16px;accent-color:#2563eb;flex-shrink:0;cursor:pointer;">
                        <div style="width:30px;height:30px;border-radius:8px;background:linear-gradient(135deg,#1a3a5c,#2563eb);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span style="font-size:11px;font-weight:700;color:white;">{{ mb_substr($teacher->name, 0, 1) }}</span>
                        </div>
                        <span style="font-size:13px;font-weight:600;color:#1e293b;">{{ $teacher->name }}</span>
                    </label>
                    @empty
                    <div style="text-align:center;padding:24px;color:#94a3b8;font-size:13px;">لا يوجد مدربون في النظام</div>
                    @endforelse
                </div>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;border-top:1px solid #f1f5f9;">
                <button type="button" @click="teacherModal=false" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
                <button type="submit" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#1a3a5c,#2563eb);border:none;border-radius:10px;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,.3);">حفظ التعيين</button>
            </div>
        </form>
    </div>
</div>

</div>
</div>{{-- /tab-terms --}}

{{-- ══ Tab: Classes ══ --}}
<div id="tab-classes" style="display:{{ (($showClassesOnly ?? false) || $program->type !== 'diploma') ? 'block' : 'none' }};">
<div style="background:#fff;border-radius:20px;border:1px solid #e2e8f0;box-shadow:0 2px 12px rgba(0,0,0,.04);padding:20px;">
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
    <div style="font-size:15px;font-weight:700;color:#1e293b;">المجموعات الدراسية
        <span style="font-size:12px;color:#64748b;font-weight:500;margin-right:6px;">({{ $classes->count() }} مجموعة)</span>
    </div>
    <button onclick="openClassModal()" style="display:flex;align-items:center;gap:6px;padding:9px 16px;border-radius:10px;background:linear-gradient(135deg,#0071AA,#004d77);color:white;font-size:13px;font-weight:700;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(0,113,170,.3);">
        <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة مجموعة
    </button>
</div>

<div id="classesList" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px;">
    @forelse($classes as $cls)
    <div id="cls-card-{{ $cls->id }}" style="background:white;border-radius:14px;border:1px solid #e2e8f0;padding:16px;box-shadow:0 2px 8px rgba(0,0,0,.04);">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px;">
            <div>
                <div style="font-size:14px;font-weight:700;color:#1e293b;">{{ $cls->name }}</div>
                @if($cls->teacher)
                <div style="font-size:11px;color:#64748b;margin-top:2px;">{{ $cls->teacher->name }}</div>
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:6px;">
                @php $stColors = ['active'=>['#dcfce7','#16a34a'],'inactive'=>['#f1f5f9','#64748b'],'completed'=>['#dbeafe','#2563eb']]; $sc=$stColors[$cls->status]??['#f1f5f9','#64748b']; @endphp
                <span style="background:{{ $sc[0] }};color:{{ $sc[1] }};border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">{{ ['active'=>'نشطة','inactive'=>'غير نشطة','completed'=>'منتهية'][$cls->status] ?? $cls->status }}</span>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:16px;font-size:12px;color:#64748b;margin-bottom:14px;">
            <span>👥 <strong id="cls-count-{{ $cls->id }}" style="color:#1e293b;">{{ $cls->students_count }}</strong> طالب</span>
            @if($cls->max_students)
            <span>/ {{ $cls->max_students }} الحد الأقصى</span>
            @endif
            @if($cls->start_date)
            <span>📅 {{ $cls->start_date->format('Y/m/d') }}</span>
            @endif
        </div>
        <div style="display:flex;gap:8px;">
            <button onclick="openClassStudents({{ $cls->id }}, '{{ addslashes($cls->name) }}')" style="flex:1;padding:7px;font-size:12px;font-weight:600;color:#7c3aed;background:#f5f3ff;border:1px solid #e9d5ff;border-radius:8px;cursor:pointer;">
                عرض الطلاب
            </button>
            <button onclick="openEditClass({{ $cls->id }}, {{ json_encode(['name'=>$cls->name,'teacher_id'=>$cls->teacher_id,'start_date'=>$cls->start_date?->format('Y-m-d'),'end_date'=>$cls->end_date?->format('Y-m-d'),'max_students'=>$cls->max_students,'status'=>$cls->status]) }})" style="padding:7px 10px;font-size:12px;color:#2563eb;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;cursor:pointer;">
                ✏️
            </button>
            <button onclick="deleteClass({{ $cls->id }})" style="padding:7px 10px;font-size:12px;color:#dc2626;background:#fff1f2;border:1px solid #fecaca;border-radius:8px;cursor:pointer;">
                🗑️
            </button>
        </div>

        {{-- Open dedicated class screen --}}
        <a href="{{ route('admin.classes.show', $cls->id) }}"
           style="display:flex;align-items:center;justify-content:space-between;margin-top:14px;border-top:1px dashed #e2e8f0;padding-top:12px;text-decoration:none;">
            <span style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#7c3aed;">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                @if($program->type === 'diploma')
                    إدارة الأرباع والمواد
                    <span style="background:#f3e8ff;color:#7c3aed;border-radius:9999px;padding:.05rem .45rem;font-size:.62rem;font-weight:700;">{{ $cls->terms->count() }}</span>
                @else
                    عرض المجموعة
                @endif
            </span>
            <svg style="width:14px;height:14px;color:#94a3b8;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </a>
    </div>
    @empty
    <div id="noClasses" style="grid-column:1/-1;text-align:center;padding:48px 20px;">
        <div style="width:56px;height:56px;border-radius:16px;background:#f0f9ff;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
            <svg style="width:26px;height:26px;color:#0071AA;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <p style="color:#94a3b8;font-size:13px;font-weight:600;">لا توجد مجموعات بعد</p>
        <p style="color:#cbd5e1;font-size:12px;margin-top:4px;">اضغط "إضافة مجموعة" لإنشاء أول مجموعة</p>
    </div>
    @endforelse
</div>{{-- /classesList --}}
</div>{{-- /card wrapper --}}
</div>{{-- /tab-classes --}}

{{-- Class Create/Edit Modal --}}
<div id="classModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center;">
<div style="background:white;border-radius:18px;padding:24px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
        <h3 id="classModalTitle" style="font-size:16px;font-weight:700;color:#1e293b;margin:0;">إضافة مجموعة</h3>
        <button onclick="closeClassModal()" style="background:none;border:none;font-size:20px;color:#94a3b8;cursor:pointer;">×</button>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div style="grid-column:1/-1;">
            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">اسم المجموعة *</label>
            <input id="cls-name" type="text" placeholder="مثال: المجموعة أ - 2025" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
        </div>
        <div style="grid-column:1/-1;">
            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">المدرب/المشرف</label>
            <select id="cls-teacher" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
                <option value="">— بدون مدرب —</option>
                @foreach($teachers as $t)
                <option value="{{ $t->id }}" {{ isset($programTeacherId) && $programTeacherId == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">تاريخ البدء</label>
            <input id="cls-start" type="date" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
        </div>
        <div>
            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">تاريخ الانتهاء</label>
            <input id="cls-end" type="date" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
        </div>
        <div>
            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">الحد الأقصى للطلاب</label>
            <input id="cls-max" type="number" min="1" placeholder="غير محدد" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
        </div>
        <div>
            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">الحالة</label>
            <select id="cls-status" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
                <option value="active">نشطة</option>
                <option value="inactive">غير نشطة</option>
                <option value="completed">منتهية</option>
            </select>
        </div>
    </div>
    <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:18px;">
        <button onclick="closeClassModal()" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
        <button onclick="submitClass()" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#7c3aed,#a855f7);border:none;border-radius:10px;cursor:pointer;box-shadow:0 4px 12px rgba(124,58,237,.3);">حفظ</button>
    </div>
</div>
</div>

{{-- Class Students Modal --}}
<div id="classStudentsModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center;">
<div style="background:white;border-radius:18px;padding:24px;width:100%;max-width:560px;max-height:80vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.2);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;flex-shrink:0;">
        <h3 id="csModalTitle" style="font-size:15px;font-weight:700;color:#1e293b;margin:0;">طلاب المجموعة</h3>
        <button onclick="closeStudentsModal()" style="background:none;border:none;font-size:20px;color:#94a3b8;cursor:pointer;">×</button>
    </div>
    <div style="display:flex;gap:8px;margin-bottom:12px;flex-shrink:0;">
        <button onclick="showTab('assigned')" id="tab-assigned" style="flex:1;padding:7px;font-size:12px;font-weight:700;border-radius:8px;border:1.5px solid #7c3aed;background:#7c3aed;color:white;cursor:pointer;">الطلاب المسندون</button>
        <button onclick="showTab('available')" id="tab-available" style="flex:1;padding:7px;font-size:12px;font-weight:700;border-radius:8px;border:1.5px solid #e2e8f0;background:white;color:#64748b;cursor:pointer;">إسناد طلاب</button>
    </div>
    <div style="overflow-y:auto;flex:1;">
        <div id="panel-assigned">
            <div id="assignedList" style="display:flex;flex-direction:column;gap:6px;"></div>
        </div>
        <div id="panel-available" style="display:none;">
            <div style="margin-bottom:8px;">
                <input id="searchAvailable" type="text" placeholder="بحث..." oninput="filterAvailable()" style="width:100%;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
            </div>
            <div id="availableList" style="display:flex;flex-direction:column;gap:6px;"></div>
            <div style="margin-top:12px;display:flex;justify-content:flex-end;">
                <button onclick="assignSelected()" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#7c3aed,#a855f7);border:none;border-radius:10px;cursor:pointer;">إسناد المحددين</button>
            </div>
        </div>
    </div>
</div>
</div>{{-- /tab-classes --}}

@push('scripts')
<script>
const CSRF   = '{{ csrf_token() }}';
const PROG_ID = {{ $program->id }};
const DEFAULT_TEACHER_ID = '{{ $programTeacherId ?? '' }}';
let currentClassId = null;
let availableStudentsData = [];

function openClassModal() {
    currentClassId = null;
    document.getElementById('classModalTitle').textContent = 'إضافة مجموعة';
    ['cls-name','cls-start','cls-end','cls-max'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('cls-teacher').value = DEFAULT_TEACHER_ID;
    document.getElementById('cls-status').value = 'active';
    document.getElementById('classModal').style.display = 'flex';
}

function openEditClass(id, data) {
    currentClassId = id;
    document.getElementById('classModalTitle').textContent = 'تعديل المجموعة';
    document.getElementById('cls-name').value    = data.name || '';
    document.getElementById('cls-teacher').value = data.teacher_id || '';
    document.getElementById('cls-start').value   = data.start_date || '';
    document.getElementById('cls-end').value     = data.end_date || '';
    document.getElementById('cls-max').value     = data.max_students || '';
    document.getElementById('cls-status').value  = data.status || 'active';
    document.getElementById('classModal').style.display = 'flex';
}

function closeClassModal() { document.getElementById('classModal').style.display = 'none'; }

function submitClass() {
    const name = document.getElementById('cls-name').value.trim();
    if (!name) { alert('اسم المجموعة مطلوب'); return; }
    const payload = {
        program_id:   PROG_ID,
        name,
        teacher_id:   document.getElementById('cls-teacher').value || null,
        start_date:   document.getElementById('cls-start').value || null,
        end_date:     document.getElementById('cls-end').value || null,
        max_students: document.getElementById('cls-max').value || null,
        status:       document.getElementById('cls-status').value,
    };

    const url    = currentClassId ? `/admin/classes/${currentClassId}` : '/admin/classes';
    const method = currentClassId ? 'PUT' : 'POST';

    fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(payload)
    }).then(r => r.json()).then(data => {
        if (data.success) { closeClassModal(); location.reload(); }
        else { alert(data.message || 'حدث خطأ'); }
    });
}

function deleteClass(id) {
    if (!confirm('حذف المجموعة؟ سيتم إلغاء إسناد جميع الطلاب منها.')) return;
    fetch(`/admin/classes/${id}`, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
}

// Students modal
function openClassStudents(id, name) {
    currentClassId = id;
    document.getElementById('csModalTitle').textContent = 'طلاب: ' + name;
    showTab('assigned');
    document.getElementById('classStudentsModal').style.display = 'flex';
    loadAssigned();
}

function closeStudentsModal() {
    document.getElementById('classStudentsModal').style.display = 'none';
}

function showTab(tab) {
    document.getElementById('panel-assigned').style.display  = tab === 'assigned'  ? 'block' : 'none';
    document.getElementById('panel-available').style.display = tab === 'available' ? 'block' : 'none';
    document.getElementById('tab-assigned').style.background  = tab === 'assigned'  ? '#7c3aed' : 'white';
    document.getElementById('tab-assigned').style.color       = tab === 'assigned'  ? 'white'   : '#64748b';
    document.getElementById('tab-assigned').style.borderColor = tab === 'assigned'  ? '#7c3aed' : '#e2e8f0';
    document.getElementById('tab-available').style.background  = tab === 'available' ? '#7c3aed' : 'white';
    document.getElementById('tab-available').style.color       = tab === 'available' ? 'white'   : '#64748b';
    document.getElementById('tab-available').style.borderColor = tab === 'available' ? '#7c3aed' : '#e2e8f0';
    if (tab === 'available') loadAvailable();
}

function loadAssigned() {
    const el = document.getElementById('assignedList');
    el.innerHTML = '<div style="text-align:center;padding:20px;color:#94a3b8;font-size:13px;">جار التحميل...</div>';
    fetch(`/admin/classes/${currentClassId}/students`, { headers: { 'Accept': 'application/json' } })
    .then(r => r.json()).then(data => {
        if (!data.students.length) {
            el.innerHTML = '<div style="text-align:center;padding:24px;color:#94a3b8;font-size:13px;">لا يوجد طلاب مسندون بعد</div>';
            return;
        }
        el.innerHTML = data.students.map(s => `
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:#f8fafc;border-radius:9px;border:1px solid #e2e8f0;">
                <div>
                    <div style="font-size:13px;font-weight:600;color:#1e293b;">${s.name}</div>
                    <div style="font-size:11px;color:#94a3b8;">${s.email}</div>
                </div>
                <button onclick="removeStudent(${s.id})" style="font-size:11px;color:#dc2626;background:#fff1f2;border:1px solid #fecaca;border-radius:7px;padding:4px 10px;cursor:pointer;">إزالة</button>
            </div>`).join('');
    });
}

function loadAvailable() {
    const el = document.getElementById('availableList');
    el.innerHTML = '<div style="text-align:center;padding:20px;color:#94a3b8;font-size:13px;">جار التحميل...</div>';
    fetch(`/admin/classes/${currentClassId}/available-students`, { headers: { 'Accept': 'application/json' } })
    .then(r => r.json()).then(data => {
        availableStudentsData = data.students;
        renderAvailable(data.students);
    });
}

function renderAvailable(students) {
    const el = document.getElementById('availableList');
    if (!students.length) {
        el.innerHTML = '<div style="text-align:center;padding:24px;color:#94a3b8;font-size:13px;">لا يوجد طلاب متاحون</div>';
        return;
    }
    el.innerHTML = students.map(s => `
        <label style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:${s.class_id==currentClassId?'#f5f3ff':'#f8fafc'};border-radius:9px;border:1px solid ${s.class_id==currentClassId?'#e9d5ff':'#e2e8f0'};cursor:pointer;">
            <input type="checkbox" value="${s.id}" ${s.class_id==currentClassId?'checked disabled':''} style="width:15px;height:15px;accent-color:#7c3aed;">
            <div>
                <div style="font-size:13px;font-weight:600;color:#1e293b;">${s.name}</div>
                <div style="font-size:11px;color:#94a3b8;">${s.email}${s.class_id==currentClassId?' · مسند بالفعل':''}</div>
            </div>
        </label>`).join('');
}

function filterAvailable() {
    const q = document.getElementById('searchAvailable').value.toLowerCase();
    renderAvailable(availableStudentsData.filter(s => s.name.toLowerCase().includes(q) || s.email.toLowerCase().includes(q)));
}

function assignSelected() {
    const ids = [...document.querySelectorAll('#availableList input[type=checkbox]:checked:not(:disabled)')].map(c => c.value);
    if (!ids.length) { alert('اختر طالباً على الأقل'); return; }
    fetch(`/admin/classes/${currentClassId}/assign-students`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ student_ids: ids })
    }).then(r => r.json()).then(d => {
        if (d.success) {
            const countEl = document.getElementById(`cls-count-${currentClassId}`);
            if (countEl) countEl.textContent = parseInt(countEl.textContent) + (d.assigned || 0);
            showTab('assigned');
            loadAssigned();
        }
    });
}

function removeStudent(studentId) {
    if (!confirm('إزالة الطالب من المجموعة؟')) return;
    fetch(`/admin/classes/${currentClassId}/remove-student`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ student_id: studentId })
    }).then(r => r.json()).then(d => {
        if (d.success) {
            const countEl = document.getElementById(`cls-count-${currentClassId}`);
            if (countEl) countEl.textContent = Math.max(0, parseInt(countEl.textContent) - 1);
            loadAssigned();
        }
    });
}

// ── Tab switching ──
function switchTab(tab) {
    const isTerms = tab === 'terms';
    document.getElementById('tab-terms').style.display   = isTerms ? 'block' : 'none';
    document.getElementById('tab-classes').style.display = isTerms ? 'none'  : 'block';

    const btnTerms   = document.getElementById('tab-btn-terms');
    const btnClasses = document.getElementById('tab-btn-classes');

    btnTerms.style.background  = isTerms ? 'white'       : 'transparent';
    btnTerms.style.color       = isTerms ? '#1e293b'     : '#64748b';
    btnTerms.style.boxShadow   = isTerms ? '0 1px 4px rgba(0,0,0,.08)' : 'none';

    btnClasses.style.background = isTerms ? 'transparent' : 'white';
    btnClasses.style.color      = isTerms ? '#64748b'     : '#7c3aed';
    btnClasses.style.boxShadow  = isTerms ? 'none'        : '0 1px 4px rgba(0,0,0,.08)';
}

// Activate tab from URL hash
if (location.hash === '#classes') switchTab('classes');
</script>
@endpush

</div>
@endsection
