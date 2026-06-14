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
    sessionModal: false,
    currentTermId: null,
    currentTermName: '',
    currentSubjectId: null,
    currentSubjectName: '',
    currentTeacherIds: [],
    currentTeacherList: [],
    editSubject: {},
    classId: '{{ $class->id }}',
    openTermModal()   { this.termModal = true; },
    openSubjectModal(id, name) { this.openAttachModal(id, name); },
    openAttachModal(id, name)  { this.currentTermId = id; this.currentTermName = name; this.attachModal = true; },
    openTeacherModal(sid, sname, tids, candidates) {
        this.currentSubjectId = sid;
        this.currentSubjectName = sname;
        this.currentTeacherIds = (tids || []).map(String);
        this.currentTeacherList = candidates || [];
        this.teacherModal = true;
    },
    openEditSubject(subject) { this.editSubject = subject; this.editSubjectModal = true; },
    openDeleteSubject(id, name) { this.currentSubjectId = id; this.currentSubjectName = name; this.deleteSubjectModal = true; }
}">

{{-- Header --}}
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
        <p class="text-xs text-gray-400 mt-0.5">{{ $class->program->name ?? '' }} · 👥 {{ $studentsCount }} طالب{{ $class->teacher ? ' · '.$class->teacher->name : '' }}</p>
    </div>
    @if($class->program && $class->program->type === 'diploma')
    <button @click="openTermModal()"
            style="display:flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;background:linear-gradient(135deg,#1a3a5c,#2563eb);color:white;font-size:12px;font-weight:700;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,.3);">
        <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة ربع للمجموعة
    </button>
    @endif
</div>

{{-- Flash --}}
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

@php $isDiplomaClass = $class->program && $class->program->type === 'diploma'; @endphp

{{-- Tabs --}}
<div style="display:flex;align-items:center;gap:4px;background:#f1f5f9;border-radius:12px;padding:4px;margin-bottom:20px;width:fit-content;">
    <button onclick="switchClassTab('students')" id="ctab-btn-students"
        style="padding:8px 18px;border-radius:9px;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:all .15s;background:white;color:#1e293b;box-shadow:0 1px 4px rgba(0,0,0,.08);">
        الطلاب
        <span style="background:#dbeafe;color:#2563eb;border-radius:9999px;padding:.1rem .5rem;font-size:.65rem;margin-right:4px;font-weight:700;">{{ $studentsCount }}</span>
    </button>
    <button onclick="switchClassTab('sessions')" id="ctab-btn-sessions"
        style="padding:8px 18px;border-radius:9px;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:all .15s;background:transparent;color:#64748b;">
        الجلسات
        <span style="background:#cffafe;color:#0891b2;border-radius:9999px;padding:.1rem .5rem;font-size:.65rem;margin-right:4px;font-weight:700;">{{ $sessions->count() }}</span>
    </button>
    @if($isDiplomaClass)
    <button onclick="switchClassTab('terms')" id="ctab-btn-terms"
        style="padding:8px 18px;border-radius:9px;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:all .15s;background:transparent;color:#64748b;">
        الأرباع والمواد
        <span style="background:#ede9fe;color:#7c3aed;border-radius:9999px;padding:.1rem .5rem;font-size:.65rem;margin-right:4px;font-weight:700;">{{ $class->terms->count() }}</span>
    </button>
    @endif
</div>

{{-- Tab contents --}}
@include('admin.classes.partials._tab-students')
@include('admin.classes.partials._tab-terms')
@include('admin.classes.partials._tab-sessions')

{{-- Tab switcher script --}}
<script>
function switchClassTab(tab){
    ['students','sessions','terms'].forEach(t=>{
        const pane=document.getElementById('ctab-'+t);
        const btn=document.getElementById('ctab-btn-'+t);
        if(pane) pane.style.display = (t===tab)?'block':'none';
        if(btn){
            const on=(t===tab);
            btn.style.background = on?'white':'transparent';
            btn.style.color = on?'#1e293b':'#64748b';
            btn.style.boxShadow = on?'0 1px 4px rgba(0,0,0,.08)':'none';
        }
    });
}
(function(){
    const h = (location.hash || '').replace('#','');
    if (['students','sessions','terms'].includes(h) && document.getElementById('ctab-btn-'+h)) {
        switchClassTab(h);
    }
})();
</script>

{{-- Modals --}}
@include('admin.classes.partials._modals-session')
@include('admin.classes.partials._modals-subject')

</div>

@include('admin.classes.partials._modal-students')

@endsection
