@extends('layouts.dashboard')
@section('title', 'المجموعات الدراسية')

@section('content')
<div style="max-width:1200px;margin:0 auto;">

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#1e293b;margin:0;">المجموعات الدراسية</h1>
        <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">إدارة مجموعات جميع البرامج</p>
    </div>
    <button onclick="openCreateModal()" style="display:flex;align-items:center;gap:6px;padding:9px 16px;border-radius:10px;background:linear-gradient(135deg,#7c3aed,#a855f7);color:white;font-size:13px;font-weight:700;border:none;cursor:pointer;box-shadow:0 4px 12px rgba(124,58,237,.3);">
        <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة مجموعة
    </button>
</div>

{{-- Filters --}}
<form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;">
    <input name="search" value="{{ request('search') }}" placeholder="بحث بالاسم..." style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;min-width:200px;">
    <select name="program_id" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;">
        <option value="">كل البرامج</option>
        @foreach($programs as $p)
        <option value="{{ $p->id }}" {{ request('program_id')==$p->id?'selected':'' }}>{{ $p->name_ar }}</option>
        @endforeach
    </select>
    <select name="type" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;">
        <option value="">كل الأنواع</option>
        <option value="diploma"  {{ request('type')=='diploma'?'selected':'' }}>دبلومة</option>
        <option value="course"   {{ request('type')=='course'?'selected':'' }}>دورة</option>
        <option value="english"  {{ request('type')=='english'?'selected':'' }}>إنجليزي</option>
        <option value="training" {{ request('type')=='training'?'selected':'' }}>تدريب</option>
    </select>
    <select name="status" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;">
        <option value="">كل الحالات</option>
        <option value="active"    {{ request('status')=='active'?'selected':'' }}>نشطة</option>
        <option value="inactive"  {{ request('status')=='inactive'?'selected':'' }}>غير نشطة</option>
        <option value="completed" {{ request('status')=='completed'?'selected':'' }}>منتهية</option>
    </select>
    <button type="submit" style="padding:8px 16px;border-radius:9px;background:#1e293b;color:white;font-size:13px;font-weight:600;border:none;cursor:pointer;">بحث</button>
    @if(request()->hasAny(['search','program_id','type','status']))
    <a href="{{ route('admin.classes.index') }}" style="padding:8px 14px;border-radius:9px;background:#f1f5f9;color:#64748b;font-size:13px;font-weight:600;text-decoration:none;">مسح</a>
    @endif
</form>

{{-- Table --}}
<div style="background:white;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.04);">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                <th style="padding:12px 16px;text-align:right;font-weight:700;color:#374151;">#</th>
                <th style="padding:12px 16px;text-align:right;font-weight:700;color:#374151;">اسم المجموعة</th>
                <th style="padding:12px 16px;text-align:right;font-weight:700;color:#374151;">البرنامج</th>
                <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">النوع</th>
                <th style="padding:12px 16px;text-align:right;font-weight:700;color:#374151;">المشرف</th>
                <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">الطلاب</th>
                <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">الحالة</th>
                <th style="padding:12px 16px;text-align:right;font-weight:700;color:#374151;">الفترة</th>
                <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($classes as $cls)
            @php $stColors = ['active'=>['#dcfce7','#16a34a'],'inactive'=>['#f1f5f9','#64748b'],'completed'=>['#dbeafe','#2563eb']]; $sc=$stColors[$cls->status]??['#f1f5f9','#64748b']; @endphp
            <tr style="border-bottom:1px solid #f1f5f9;" class="hover:bg-gray-50">
                <td style="padding:12px 16px;color:#94a3b8;">{{ $cls->id }}</td>
                <td style="padding:12px 16px;font-weight:600;color:#1e293b;">{{ $cls->name }}</td>
                <td style="padding:12px 16px;color:#64748b;">{{ $cls->program?->name_ar ?? '—' }}</td>
                <td style="padding:12px 16px;text-align:center;">
                    @php
                        $typeMap = [
                            'diploma'  => ['دبلومة', '#ede9fe', '#7c3aed'],
                            'course'   => ['دورة', '#dcfce7', '#16a34a'],
                            'english'  => ['إنجليزي', '#dbeafe', '#2563eb'],
                            'training' => ['تدريب', '#fef3c7', '#d97706'],
                        ];
                        $t = $typeMap[$cls->program?->type] ?? ['—', '#f1f5f9', '#64748b'];
                    @endphp
                    <span style="background:{{ $t[1] }};color:{{ $t[2] }};border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">{{ $t[0] }}</span>
                </td>
                <td style="padding:12px 16px;color:#64748b;">{{ $cls->supervisor_name ?: ($cls->teacher?->name ?? '—') }}</td>
                <td style="padding:12px 16px;text-align:center;">
                    <span style="font-weight:700;color:#7c3aed;">{{ $cls->students_count }}</span>
                    @if($cls->max_students)<span style="color:#94a3b8;font-size:11px;"> / {{ $cls->max_students }}</span>@endif
                </td>
                <td style="padding:12px 16px;text-align:center;">
                    <span style="background:{{ $sc[0] }};color:{{ $sc[1] }};border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">{{ ['active'=>'نشطة','inactive'=>'غير نشطة','completed'=>'منتهية'][$cls->status] ?? $cls->status }}</span>
                </td>
                <td style="padding:12px 16px;color:#64748b;font-size:12px;">
                    {{ $cls->start_date?->format('Y/m/d') ?? '—' }}
                    @if($cls->end_date) → {{ $cls->end_date->format('Y/m/d') }} @endif
                </td>
                <td style="padding:12px 16px;text-align:center;">
                    <div style="display:flex;gap:6px;justify-content:center;">
                        <a href="{{ route('admin.classes.show', $cls->id) }}" style="padding:5px 10px;font-size:11px;color:#7c3aed;background:#f5f3ff;border:1px solid #e9d5ff;border-radius:7px;cursor:pointer;font-weight:600;text-decoration:none;">عرض</a>
                        <button onclick="openStudentsModal({{ $cls->id }}, '{{ addslashes($cls->name) }}')" style="padding:5px 10px;font-size:11px;color:#0369a1;background:#e0f2fe;border:1px solid #bae6fd;border-radius:7px;cursor:pointer;font-weight:600;">الطلاب</button>
                        @php
                            $clsEdit = [
                                'id'           => $cls->id,
                                'program_id'   => $cls->program_id,
                                'name'         => $cls->name,
                                'supervisor_name' => $cls->supervisor_name,
                                'max_students' => $cls->max_students,
                                'status'       => $cls->status,
                                'start_date'   => optional($cls->start_date)->format('Y-m-d'),
                                'end_date'     => optional($cls->end_date)->format('Y-m-d'),
                            ];
                        @endphp
                        <button onclick='openEditClass(@json($clsEdit))' style="padding:5px 10px;font-size:11px;color:#d97706;background:#fffbeb;border:1px solid #fde68a;border-radius:7px;cursor:pointer;font-weight:600;">تعديل</button>
                        <button onclick="confirmDelete({{ $cls->id }})" style="padding:5px 10px;font-size:11px;color:#dc2626;background:#fff1f2;border:1px solid #fecaca;border-radius:7px;cursor:pointer;">حذف</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="padding:48px;text-align:center;color:#94a3b8;font-size:13px;">لا توجد مجموعات</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($classes->hasPages())
<div style="margin-top:16px;">{{ $classes->links() }}</div>
@endif

</div>

{{-- Create Modal --}}
<div id="createModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center;">
<div style="background:white;border-radius:18px;padding:24px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
        <h3 id="createModalTitle" style="font-size:16px;font-weight:700;color:#1e293b;margin:0;">إضافة مجموعة جديدة</h3>
        <button onclick="document.getElementById('createModal').style.display='none'" style="background:none;border:none;font-size:20px;color:#94a3b8;cursor:pointer;">×</button>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div style="grid-column:1/-1;">
            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">البرنامج *</label>
            <select id="m-program" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
                <option value="">اختر برنامجاً</option>
                @foreach($programs as $p)
                <option value="{{ $p->id }}">{{ $p->name_ar }}</option>
                @endforeach
            </select>
        </div>
        <div style="grid-column:1/-1;">
            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">اسم المجموعة *</label>
            <input id="m-name" type="text" placeholder="مثال: المجموعة أ" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
        </div>
        <div style="grid-column:1/-1;">
            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">اسم المشرف</label>
            <input id="m-supervisor" type="text" placeholder="اسم المشرف" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
        </div>
        <div>
            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">تاريخ البدء</label>
            <input id="m-start" type="date" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
        </div>
        <div>
            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">تاريخ الانتهاء</label>
            <input id="m-end" type="date" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
        </div>
        <div>
            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">الحد الأقصى</label>
            <input id="m-max" type="number" min="1" placeholder="غير محدد" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
        </div>
        <div>
            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">الحالة</label>
            <select id="m-status" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
                <option value="active">نشطة</option>
                <option value="inactive">غير نشطة</option>
                <option value="completed">منتهية</option>
            </select>
        </div>
    </div>
    <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:18px;">
        <button onclick="document.getElementById('createModal').style.display='none'" style="padding:9px 18px;font-size:13px;font-weight:600;color:#475569;background:#f1f5f9;border:none;border-radius:10px;cursor:pointer;">إلغاء</button>
        <button id="createModalSubmit" onclick="submitCreate()" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#7c3aed,#a855f7);border:none;border-radius:10px;cursor:pointer;">حفظ</button>
    </div>
</div>
</div>

{{-- Students Modal --}}
<div id="studentsModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1100;align-items:center;justify-content:center;">
<div style="background:white;border-radius:18px;width:100%;max-width:580px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,.25);overflow:hidden;">

    {{-- Header --}}
    <div style="padding:18px 22px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;background:linear-gradient(135deg,#0071AA,#0ea5e9);">
        <div>
            <div style="color:#fff;font-size:15px;font-weight:800;" id="sm-title">طلاب المجموعة</div>
            <div style="color:rgba(255,255,255,.7);font-size:12px;margin-top:2px;" id="sm-subtitle"></div>
        </div>
        <button onclick="document.getElementById('studentsModal').style.display='none'" style="background:rgba(255,255,255,.2);border:none;border-radius:8px;width:32px;height:32px;color:#fff;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
    </div>

    {{-- Tabs --}}
    <div style="display:flex;border-bottom:1px solid #e2e8f0;flex-shrink:0;">
        <button id="tab-current" onclick="showTab('current')" style="flex:1;padding:11px;font-size:13px;font-weight:700;border:none;cursor:pointer;background:#f0f9ff;color:#0369a1;border-bottom:2px solid #0369a1;">الطلاب الحاليون</button>
        <button id="tab-add" onclick="showTab('add')" style="flex:1;padding:11px;font-size:13px;font-weight:600;border:none;cursor:pointer;background:#f8fafc;color:#64748b;border-bottom:2px solid transparent;">إضافة طلاب</button>
    </div>

    {{-- Current students tab --}}
    <div id="pane-current" style="flex:1;overflow-y:auto;padding:16px;">
        <div id="sm-loading" style="text-align:center;padding:32px;color:#94a3b8;font-size:13px;">جاري التحميل...</div>
        <div id="sm-list" style="display:none;"></div>
        <div id="sm-empty" style="display:none;text-align:center;padding:32px;color:#94a3b8;font-size:13px;">لا يوجد طلاب في هذه المجموعة</div>
    </div>

    {{-- Add students tab --}}
    <div id="pane-add" style="display:none;flex:1;overflow-y:auto;padding:16px;">
        <input id="sm-search" placeholder="بحث بالاسم أو الرقم الوطني..." oninput="filterAvailable()" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;margin-bottom:12px;box-sizing:border-box;">
        <div id="sm-available-loading" style="text-align:center;padding:32px;color:#94a3b8;font-size:13px;">جاري التحميل...</div>
        <div id="sm-available-list" style="display:none;"></div>
        <div id="sm-available-empty" style="display:none;text-align:center;padding:24px;color:#94a3b8;font-size:13px;">لا يوجد طلاب متاحون للإضافة</div>
        <div id="sm-add-actions" style="display:none;padding-top:12px;border-top:1px solid #f1f5f9;margin-top:12px;">
            <button onclick="assignSelected()" style="padding:9px 20px;background:linear-gradient(135deg,#0071AA,#0ea5e9);color:#fff;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;">إضافة المحددين</button>
        </div>
    </div>

</div>
</div>

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
let _editClassId = null;

function openCreateModal() {
    _editClassId = null;
    document.getElementById('createModalTitle').textContent = 'إضافة مجموعة جديدة';
    document.getElementById('createModalSubmit').textContent = 'حفظ';
    document.getElementById('m-program').value = '';
    document.getElementById('m-program').disabled = false;
    document.getElementById('m-name').value = '';
    document.getElementById('m-supervisor').value = '';
    document.getElementById('m-start').value = '';
    document.getElementById('m-end').value = '';
    document.getElementById('m-max').value = '';
    document.getElementById('m-status').value = 'active';
    document.getElementById('createModal').style.display = 'flex';
}

function openEditClass(c) {
    _editClassId = c.id;
    document.getElementById('createModalTitle').textContent = 'تعديل المجموعة';
    document.getElementById('createModalSubmit').textContent = 'تحديث';
    document.getElementById('m-program').value = c.program_id ?? '';
    document.getElementById('m-program').disabled = true; // program can't change after creation
    document.getElementById('m-name').value = c.name ?? '';
    document.getElementById('m-supervisor').value = c.supervisor_name ?? '';
    document.getElementById('m-start').value = c.start_date ?? '';
    document.getElementById('m-end').value = c.end_date ?? '';
    document.getElementById('m-max').value = c.max_students ?? '';
    document.getElementById('m-status').value = c.status ?? 'active';
    document.getElementById('createModal').style.display = 'flex';
}
function confirmDelete(id) {
    if (!confirm('حذف المجموعة وإلغاء إسناد الطلاب؟')) return;
    fetch(`/admin/classes/${id}`, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
}
// ── Students Modal ──────────────────────────────────────────────────────
let _classId = null;
let _allAvailable = [];
let _selectedIds = new Set();

function showTab(tab) {
    document.getElementById('pane-current').style.display = tab === 'current' ? 'block' : 'none';
    document.getElementById('pane-add').style.display     = tab === 'add'     ? 'block' : 'none';
    document.getElementById('tab-current').style.cssText  = tab === 'current'
        ? 'flex:1;padding:11px;font-size:13px;font-weight:700;border:none;cursor:pointer;background:#f0f9ff;color:#0369a1;border-bottom:2px solid #0369a1;'
        : 'flex:1;padding:11px;font-size:13px;font-weight:600;border:none;cursor:pointer;background:#f8fafc;color:#64748b;border-bottom:2px solid transparent;';
    document.getElementById('tab-add').style.cssText = tab === 'add'
        ? 'flex:1;padding:11px;font-size:13px;font-weight:700;border:none;cursor:pointer;background:#f0f9ff;color:#0369a1;border-bottom:2px solid #0369a1;'
        : 'flex:1;padding:11px;font-size:13px;font-weight:600;border:none;cursor:pointer;background:#f8fafc;color:#64748b;border-bottom:2px solid transparent;';
}

function openStudentsModal(classId, className) {
    _classId = classId;
    _selectedIds = new Set();
    document.getElementById('sm-title').textContent = 'طلاب المجموعة';
    document.getElementById('sm-subtitle').textContent = className;
    document.getElementById('sm-search').value = '';
    showTab('current');
    document.getElementById('studentsModal').style.display = 'flex';
    loadCurrentStudents();
    loadAvailableStudents();
}

function loadCurrentStudents() {
    document.getElementById('sm-loading').style.display = 'block';
    document.getElementById('sm-list').style.display = 'none';
    document.getElementById('sm-empty').style.display = 'none';
    fetch(`/admin/classes/${_classId}/students`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json()).then(d => {
            document.getElementById('sm-loading').style.display = 'none';
            if (!d.students.length) { document.getElementById('sm-empty').style.display = 'block'; return; }
            const list = document.getElementById('sm-list');
            list.innerHTML = d.students.map(s => `
                <div id="student-row-${s.id}" style="display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;border:1px solid #f1f5f9;margin-bottom:8px;">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#a855f7);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;flex-shrink:0;">${s.name.charAt(0)}</div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:700;color:#1e293b;">${s.name}</div>
                        <div style="font-size:11px;color:#94a3b8;">${s.national_id || s.email || ''}</div>
                    </div>
                    <button onclick="removeStudent(${s.id})" style="padding:5px 12px;font-size:11px;color:#dc2626;background:#fff1f2;border:1px solid #fecaca;border-radius:7px;cursor:pointer;font-weight:600;flex-shrink:0;">إزالة</button>
                </div>`).join('');
            list.style.display = 'block';
        });
}

function removeStudent(studentId) {
    if (!confirm('إزالة المتدرب من المجموعة؟')) return;
    fetch(`/admin/classes/${_classId}/remove-student`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ student_id: studentId })
    }).then(r => r.json()).then(d => {
        if (d.success) {
            const row = document.getElementById(`student-row-${studentId}`);
            if (row) row.remove();
            const list = document.getElementById('sm-list');
            if (list && !list.children.length) {
                list.style.display = 'none';
                document.getElementById('sm-empty').style.display = 'block';
            }
            loadAvailableStudents();
        }
    });
}

function loadAvailableStudents() {
    document.getElementById('sm-available-loading').style.display = 'block';
    document.getElementById('sm-available-list').style.display = 'none';
    document.getElementById('sm-available-empty').style.display = 'none';
    document.getElementById('sm-add-actions').style.display = 'none';
    fetch(`/admin/classes/${_classId}/available-students`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json()).then(d => {
            document.getElementById('sm-available-loading').style.display = 'none';
            _allAvailable = d.students.filter(s => !s.class_id || s.class_id != _classId);
            renderAvailable(_allAvailable);
        });
}

function renderAvailable(students) {
    if (!students.length) {
        document.getElementById('sm-available-empty').style.display = 'block';
        document.getElementById('sm-available-list').style.display = 'none';
        document.getElementById('sm-add-actions').style.display = 'none';
        return;
    }
    document.getElementById('sm-available-empty').style.display = 'none';
    const list = document.getElementById('sm-available-list');
    list.innerHTML = students.map(s => `
        <label style="display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;border:1px solid #f1f5f9;margin-bottom:8px;cursor:pointer;" onchange="updateAddBtn()">
            <input type="checkbox" value="${s.id}" class="avail-cb" ${_selectedIds.has(s.id)?'checked':''} style="width:15px;height:15px;accent-color:#0071AA;flex-shrink:0;">
            <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#0369a1,#0ea5e9);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;flex-shrink:0;">${s.name.charAt(0)}</div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:700;color:#1e293b;">${s.name}</div>
                <div style="font-size:11px;color:#94a3b8;">${s.national_id || s.email || ''}</div>
            </div>
            ${s.class_id ? '<span style="font-size:10px;color:#f59e0b;background:#fef3c7;padding:2px 7px;border-radius:20px;flex-shrink:0;">في مجموعة أخرى</span>' : ''}
        </label>`).join('');
    list.style.display = 'block';
    updateAddBtn();
}

function filterAvailable() {
    const q = document.getElementById('sm-search').value.toLowerCase();
    const filtered = _allAvailable.filter(s => s.name.toLowerCase().includes(q) || (s.national_id||'').includes(q));
    renderAvailable(filtered);
}

function updateAddBtn() {
    _selectedIds = new Set([...document.querySelectorAll('.avail-cb:checked')].map(c => parseInt(c.value)));
    document.getElementById('sm-add-actions').style.display = _selectedIds.size ? 'block' : 'none';
}

function assignSelected() {
    if (!_selectedIds.size) return;
    fetch(`/admin/classes/${_classId}/assign-students`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ student_ids: [..._selectedIds] })
    }).then(r => r.json()).then(d => {
        if (d.success) {
            _selectedIds = new Set();
            loadCurrentStudents();
            loadAvailableStudents();
            showTab('current');
        }
    });
}
// ────────────────────────────────────────────────────────────────────────────

function submitCreate() {
    const program_id = document.getElementById('m-program').value;
    const name       = document.getElementById('m-name').value.trim();
    if (!program_id) { alert('اختر برنامجاً'); return; }
    if (!name)       { alert('اسم المجموعة مطلوب'); return; }

    const payload = {
        program_id,
        name,
        supervisor_name: document.getElementById('m-supervisor').value || null,
        start_date:   document.getElementById('m-start').value || null,
        end_date:     document.getElementById('m-end').value || null,
        max_students: document.getElementById('m-max').value || null,
        status:       document.getElementById('m-status').value,
    };

    const isEdit = !!_editClassId;
    const url    = isEdit ? `/admin/classes/${_editClassId}` : '/admin/classes';
    if (isEdit) payload._method = 'PUT';

    fetch(url, {
        method: 'POST', // method spoofing via _method for PUT
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(payload)
    }).then(r => r.json()).then(d => { if (d.success) location.reload(); else alert(d.message || 'حدث خطأ'); });
}
</script>
@endpush
@endsection
