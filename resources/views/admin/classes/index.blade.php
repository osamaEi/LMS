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
    <select name="status" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;">
        <option value="">كل الحالات</option>
        <option value="active"    {{ request('status')=='active'?'selected':'' }}>نشطة</option>
        <option value="inactive"  {{ request('status')=='inactive'?'selected':'' }}>غير نشطة</option>
        <option value="completed" {{ request('status')=='completed'?'selected':'' }}>منتهية</option>
    </select>
    <button type="submit" style="padding:8px 16px;border-radius:9px;background:#1e293b;color:white;font-size:13px;font-weight:600;border:none;cursor:pointer;">بحث</button>
    @if(request()->hasAny(['search','program_id','status']))
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
                <th style="padding:12px 16px;text-align:right;font-weight:700;color:#374151;">المدرب</th>
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
                <td style="padding:12px 16px;color:#64748b;">{{ $cls->teacher?->name ?? '—' }}</td>
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
                        <a href="{{ route('admin.programs.show', $cls->program_id) }}#classes" style="padding:5px 10px;font-size:11px;color:#7c3aed;background:#f5f3ff;border:1px solid #e9d5ff;border-radius:7px;text-decoration:none;font-weight:600;">عرض</a>
                        <button onclick="confirmDelete({{ $cls->id }})" style="padding:5px 10px;font-size:11px;color:#dc2626;background:#fff1f2;border:1px solid #fecaca;border-radius:7px;cursor:pointer;">حذف</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="padding:48px;text-align:center;color:#94a3b8;font-size:13px;">لا توجد مجموعات</td>
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
        <h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:0;">إضافة مجموعة جديدة</h3>
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
            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">المدرب/المشرف</label>
            <select id="m-teacher" style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;box-sizing:border-box;">
                <option value="">— بدون —</option>
                @foreach($teachers as $t)
                <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>
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
        <button onclick="submitCreate()" style="padding:9px 18px;font-size:13px;font-weight:700;color:white;background:linear-gradient(135deg,#7c3aed,#a855f7);border:none;border-radius:10px;cursor:pointer;">حفظ</button>
    </div>
</div>
</div>

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';
function openCreateModal() { document.getElementById('createModal').style.display = 'flex'; }
function confirmDelete(id) {
    if (!confirm('حذف المجموعة وإلغاء إسناد الطلاب؟')) return;
    fetch(`/admin/classes/${id}`, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
}
function submitCreate() {
    const program_id = document.getElementById('m-program').value;
    const name       = document.getElementById('m-name').value.trim();
    if (!program_id) { alert('اختر برنامجاً'); return; }
    if (!name)       { alert('اسم المجموعة مطلوب'); return; }
    fetch('/admin/classes', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({
            program_id,
            name,
            teacher_id:   document.getElementById('m-teacher').value || null,
            start_date:   document.getElementById('m-start').value || null,
            end_date:     document.getElementById('m-end').value || null,
            max_students: document.getElementById('m-max').value || null,
            status:       document.getElementById('m-status').value,
        })
    }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
}
</script>
@endpush
@endsection
