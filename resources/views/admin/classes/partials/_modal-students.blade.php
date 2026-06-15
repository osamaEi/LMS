{{-- MODAL: Manage Students --}}
<div id="studentsModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1100;align-items:center;justify-content:center;">
<div style="background:white;border-radius:18px;width:100%;max-width:580px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,.25);overflow:hidden;">
    <div style="padding:18px 22px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;background:linear-gradient(135deg,#0071AA,#0ea5e9);">
        <div>
            <div style="color:#fff;font-size:15px;font-weight:800;" id="sm-title">طلاب المجموعة</div>
            <div style="color:rgba(255,255,255,.7);font-size:12px;margin-top:2px;" id="sm-subtitle"></div>
        </div>
        <button onclick="document.getElementById('studentsModal').style.display='none'" style="background:rgba(255,255,255,.2);border:none;border-radius:8px;width:32px;height:32px;color:#fff;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;">×</button>
    </div>
    <div style="display:flex;border-bottom:1px solid #e2e8f0;flex-shrink:0;">
        <button id="tab-current" onclick="showTab('current')" style="flex:1;padding:11px;font-size:13px;font-weight:700;border:none;cursor:pointer;background:#f0f9ff;color:#0369a1;border-bottom:2px solid #0369a1;">الطلاب الحاليون</button>
        <button id="tab-add" onclick="showTab('add')" style="flex:1;padding:11px;font-size:13px;font-weight:600;border:none;cursor:pointer;background:#f8fafc;color:#64748b;border-bottom:2px solid transparent;">إضافة طلاب</button>
    </div>
    <div id="pane-current" style="flex:1;overflow-y:auto;padding:16px;">
        <div id="sm-loading" style="text-align:center;padding:32px;color:#94a3b8;font-size:13px;">جاري التحميل...</div>
        <div id="sm-list" style="display:none;"></div>
        <div id="sm-empty" style="display:none;text-align:center;padding:32px;color:#94a3b8;font-size:13px;">لا يوجد طلاب في هذه المجموعة</div>
    </div>
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

<script>
const CSRF = '{{ csrf_token() }}';
let _classId = {{ $class->id }};
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
    _classId = classId; _selectedIds = new Set();
    document.getElementById('sm-subtitle').textContent = className;
    document.getElementById('sm-search').value = '';
    showTab('current');
    document.getElementById('studentsModal').style.display = 'flex';
    loadCurrentStudents(); loadAvailableStudents();
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
    }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
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
    renderAvailable(_allAvailable.filter(s => s.name.toLowerCase().includes(q) || (s.national_id||'').includes(q)));
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
    }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
}
</script>
