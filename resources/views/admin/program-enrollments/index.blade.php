@extends('layouts.dashboard')

@section('title', 'طلبات التسجيل في البرامج')

@push('styles')
<style>
.page-wrap { max-width: 1400px; margin: 0 auto; }

/* ── Hero ── */
.page-hero {
    background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #004466 100%);
    border-radius: 24px;
    padding: 2rem 2.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    margin-bottom: 1.75rem;
}
.page-hero::before {
    content: '';
    position: absolute;
    top: -40%; right: -10%;
    width: 50%; height: 200%;
    background: radial-gradient(ellipse, rgba(255,255,255,0.1) 0%, transparent 70%);
    pointer-events: none;
}

/* ── Stat Cards ── */
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}
@media(max-width:900px) { .stats-row { grid-template-columns: repeat(2,1fr); } }
@media(max-width:500px) { .stats-row { grid-template-columns: 1fr 1fr; } }

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.25rem 1.5rem;
    display: flex; align-items: center; gap: 1rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    border: 2px solid transparent;
    cursor: pointer;
    text-decoration: none;
    transition: all .2s;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(0,0,0,0.1); }
.stat-card.active-filter { border-color: currentColor; }

.stat-icon {
    width: 50px; height: 50px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.stat-label { font-size: .78rem; color: #6b7280; font-weight: 600; margin-bottom: 2px; }
.stat-value { font-size: 1.6rem; font-weight: 800; color: #111827; line-height: 1; }

/* ── Filter Bar ── */
.filter-bar {
    background: white;
    border-radius: 16px;
    padding: 1rem 1.25rem;
    display: flex; gap: .75rem; align-items: center; flex-wrap: wrap;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    margin-bottom: 1.25rem;
}
.filter-input {
    flex: 1; min-width: 200px;
    padding: .65rem 1rem;
    border: 2px solid #e5e7eb; border-radius: 10px;
    font-size: .9rem; color: #111827;
    outline: none; transition: border-color .2s;
}
.filter-input:focus { border-color: #0071AA; }
.filter-select {
    padding: .65rem 1rem;
    border: 2px solid #e5e7eb; border-radius: 10px;
    font-size: .875rem; color: #374151;
    outline: none; background: white;
    transition: border-color .2s; cursor: pointer;
}
.filter-select:focus { border-color: #0071AA; }
.btn-search {
    padding: .65rem 1.4rem;
    background: linear-gradient(135deg, #0071AA, #005a88);
    color: white; border: none; border-radius: 10px;
    font-size: .875rem; font-weight: 700; cursor: pointer;
    display: flex; align-items: center; gap: .4rem;
    transition: opacity .2s;
}
.btn-search:hover { opacity: .9; }
.btn-clear {
    padding: .65rem 1rem;
    background: #f1f5f9; color: #6b7280;
    border: none; border-radius: 10px;
    font-size: .875rem; font-weight: 600; cursor: pointer;
    text-decoration: none; display: flex; align-items: center; gap: .3rem;
}
.btn-clear:hover { background: #e5e7eb; color: #374151; }

/* ── Table Card ── */
.table-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}
.table-wrap { overflow-x: auto; }

table.main-table {
    width: 100%; border-collapse: collapse;
}
table.main-table thead {
    background: #f8fafc;
    border-bottom: 2px solid #f1f5f9;
}
table.main-table th {
    padding: .85rem 1.25rem;
    text-align: right;
    font-size: .72rem; font-weight: 800;
    color: #6b7280; text-transform: uppercase; letter-spacing: .5px;
    white-space: nowrap;
}
table.main-table tbody tr {
    border-bottom: 1px solid #f8fafc;
    transition: background .15s;
}
table.main-table tbody tr:hover { background: #fafbff; }
table.main-table tbody tr:last-child { border-bottom: none; }
table.main-table td {
    padding: 1rem 1.25rem;
    font-size: .875rem; color: #1f2937;
    vertical-align: middle;
}

/* ── Status Badges ── */
.badge {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .3rem .85rem; border-radius: 20px;
    font-size: .72rem; font-weight: 800; white-space: nowrap;
}
.badge-pending  { background: #fef9c3; color: #92400e; }
.badge-approved { background: #dcfce7; color: #15803d; }
.badge-none     { background: #f1f5f9; color: #6b7280; }

/* ── Avatar ── */
.user-avatar {
    width: 44px; height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0071AA, #005a88);
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; color: white; font-size: .9rem;
    flex-shrink: 0; overflow: hidden;
}
.user-avatar img { width:100%; height:100%; object-fit:cover; }

/* ── Row Actions ── */
.row-actions { display: flex; gap: .5rem; flex-wrap: nowrap; }
.act-btn {
    padding: .4rem .85rem;
    border-radius: 8px; border: none;
    font-size: .75rem; font-weight: 700;
    cursor: pointer; display: flex; align-items: center; gap: .3rem;
    text-decoration: none; transition: opacity .15s; white-space: nowrap;
}
.act-btn:hover { opacity: .85; }
.act-view     { background: #eff6ff; color: #1e40af; }
.act-approve  { background: #dcfce7; color: #15803d; }
.act-reject   { background: #fee2e2; color: #991b1b; }

/* ── Bulk Bar ── */
.bulk-bar {
    display: none;
    background: #eff6ff; border: 2px solid #bfdbfe;
    border-radius: 14px; padding: .85rem 1.25rem;
    margin-bottom: 1rem;
    align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: .75rem;
}
.bulk-bar.show { display: flex; }

/* ── Empty State ── */
.empty-state {
    text-align: center; padding: 4rem 2rem;
}
.empty-state .es-icon {
    width: 80px; height: 80px; margin: 0 auto 1.25rem;
    border-radius: 20px;
    background: linear-gradient(135deg, #f1f5f9, #e5e7eb);
    display: flex; align-items: center; justify-content: center;
}

/* ── Pagination override ── */
.pagination-wrap { padding: 1rem 1.5rem; border-top: 1.5px solid #f1f5f9; }
</style>
@endpush

@section('content')
<div class="page-wrap">

    {{-- ── Hero ── --}}
    <div class="page-hero">
        <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div>
                <h1 style="font-size:1.6rem;font-weight:800;margin:0 0 .3rem;">سجل التسجيل في البرامج</h1>
                <p style="font-size:.9rem;color:rgba(255,255,255,.8);margin:0;">
                    عرض جميع طلبات التسجيل في البرامج الدراسية مع حالاتها
                </p>
            </div>
            <div style="display:flex;align-items:center;gap:.75rem;">
                <div style="background:rgba(255,255,255,0.15);border-radius:12px;padding:.6rem 1.25rem;text-align:center;">
                    <div style="font-size:1.5rem;font-weight:800;">{{ $counts['pending'] }}</div>
                    <div style="font-size:.72rem;color:rgba(255,255,255,.8);">معلق</div>
                </div>
                <div style="background:rgba(255,255,255,0.15);border-radius:12px;padding:.6rem 1.25rem;text-align:center;">
                    <div style="font-size:1.5rem;font-weight:800;">{{ $counts['approved'] }}</div>
                    <div style="font-size:.72rem;color:rgba(255,255,255,.8);">مقبول</div>
                </div>
                <div style="background:rgba(255,255,255,0.15);border-radius:12px;padding:.6rem 1.25rem;text-align:center;">
                    <div style="font-size:1.5rem;font-weight:800;">{{ $counts['none'] }}</div>
                    <div style="font-size:.72rem;color:rgba(255,255,255,.8);">مرفوض</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Status Stat Cards (clickable filters) ── --}}
    <div class="stats-row">
        @php
            $tabs = [
                ['key'=>'all',      'label'=>'جميع الطلبات',   'color'=>'#0071AA', 'bg'=>'#eff6ff', 'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                ['key'=>'pending',  'label'=>'قيد الانتظار',   'color'=>'#d97706', 'bg'=>'#fef3c7', 'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['key'=>'approved', 'label'=>'مقبولة',          'color'=>'#16a34a', 'bg'=>'#dcfce7', 'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['key'=>'none',     'label'=>'مرفوضة / ملغاة', 'color'=>'#dc2626', 'bg'=>'#fee2e2', 'icon'=>'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
        @endphp
        @foreach($tabs as $tab)
        <a href="{{ route('admin.program-enrollments.index', array_merge(request()->except('status','page'), ['status'=>$tab['key']])) }}"
           class="stat-card {{ $status === $tab['key'] ? 'active-filter' : '' }}"
           style="{{ $status === $tab['key'] ? 'border-color:'.$tab['color'].';' : '' }}color:{{ $tab['color'] }};">
            <div class="stat-icon" style="background:{{ $tab['bg'] }};">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:{{ $tab['color'] }};"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/></svg>
            </div>
            <div>
                <div class="stat-label">{{ $tab['label'] }}</div>
                <div class="stat-value" style="color:{{ $tab['color'] }};">{{ $counts[$tab['key']] }}</div>
            </div>
        </a>
        @endforeach
    </div>

    {{-- ── Filter Bar ── --}}
    <form method="GET" action="{{ route('admin.program-enrollments.index') }}" class="filter-bar">
        <input type="hidden" name="status" value="{{ $status }}">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="بحث بالاسم، البريد، الجوال، رقم الهوية..."
               class="filter-input">

        <select name="program_id" class="filter-select">
            <option value="">كل البرامج</option>
            @foreach($programs as $prog)
                <option value="{{ $prog->id }}" {{ request('program_id') == $prog->id ? 'selected' : '' }}>
                    {{ $prog->name_ar }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn-search">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            بحث
        </button>
        @if(request('search') || request('program_id'))
        <a href="{{ route('admin.program-enrollments.index', ['status'=>$status]) }}" class="btn-clear">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            مسح
        </a>
        @endif
    </form>

    {{-- ── Bulk Actions Form ── --}}
    <form id="bulk-form" method="POST" onsubmit="return false;">
        @csrf

        {{-- Bulk Bar --}}
        <div id="bulk-bar" class="bulk-bar">
            <span style="font-weight:700;color:#1e40af;font-size:.9rem;">
                تم تحديد <span id="sel-count">0</span> طالب
            </span>
            <div style="display:flex;gap:.5rem;">
                <button type="button" onclick="bulkApprove()" style="padding:.5rem 1.1rem;background:linear-gradient(135deg,#10b981,#059669);color:white;border:none;border-radius:10px;font-size:.8rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:.4rem;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    قبول المحددين
                </button>
                <button type="button" onclick="bulkReject()" style="padding:.5rem 1.1rem;background:linear-gradient(135deg,#ef4444,#dc2626);color:white;border:none;border-radius:10px;font-size:.8rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:.4rem;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    رفض المحددين
                </button>
            </div>
        </div>

        {{-- ── Table ── --}}
        @if($users->isEmpty())
        <div class="table-card">
            <div class="empty-state">
                <div class="es-icon">
                    <svg class="w-10 h-10" style="color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 style="font-size:1.1rem;font-weight:700;color:#111827;margin-bottom:.4rem;">لا توجد طلبات</h3>
                <p style="color:#6b7280;font-size:.875rem;">لا توجد بيانات تطابق الفلتر المحدد</p>
            </div>
        </div>
        @else
        <div class="table-card">
            <div class="table-wrap">
                <table class="main-table">
                    <thead>
                        <tr>
                            <th style="width:36px;">
                                <input type="checkbox" id="select-all" onchange="toggleAll(this)"
                                       style="width:16px;height:16px;cursor:pointer;accent-color:#0071AA;">
                            </th>
                            <th>الطالب</th>
                            <th>البرنامج</th>
                            <th>حالة التسجيل</th>
                            <th>حالة الحساب</th>
                            <th>تاريخ الطلب</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        @php
                            $ps = $user->program_status ?? 'none';
                            $psBg    = match($ps) { 'pending'=>'#fef9c3','approved'=>'#dcfce7', default=>'#f1f5f9' };
                            $psClr   = match($ps) { 'pending'=>'#92400e','approved'=>'#15803d', default=>'#6b7280' };
                            $psTxt   = match($ps) { 'pending'=>'⏳ قيد الانتظار','approved'=>'✓ مقبول', default=>'✕ مرفوض / لا يوجد' };
                            $acBg    = match($user->status) { 'active'=>'#dcfce7','suspended'=>'#fee2e2', default=>'#fef9c3' };
                            $acClr   = match($user->status) { 'active'=>'#15803d','suspended'=>'#dc2626', default=>'#92400e' };
                            $acTxt   = match($user->status) { 'active'=>'نشط','suspended'=>'موقوف', default=>'معلق' };
                        @endphp
                        <tr>
                            <td>
                                @if($ps === 'pending')
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                       class="row-cb" onchange="updateBulk()"
                                       style="width:16px;height:16px;cursor:pointer;accent-color:#0071AA;">
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:.75rem;">
                                    <div class="user-avatar">
                                        @if($user->profile_photo)
                                            <img src="{{ asset('storage/'.$user->profile_photo) }}" alt="{{ $user->name }}">
                                        @else
                                            {{ mb_substr($user->name, 0, 2) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div style="font-weight:700;color:#111827;">{{ $user->name }}</div>
                                        <div style="font-size:.75rem;color:#6b7280;font-family:monospace;">{{ $user->national_id ?? $user->email }}</div>
                                        @if($user->phone)
                                        <div style="font-size:.72rem;color:#9ca3af;">{{ $user->phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->program)
                                <div style="font-weight:700;color:#111827;">{{ $user->program->name_ar }}</div>
                                <div style="font-size:.72rem;color:#9ca3af;font-family:monospace;">{{ $user->program->code }}</div>
                                @else
                                <span style="color:#9ca3af;font-size:.82rem;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background:{{ $psBg }};color:{{ $psClr }};">{{ $psTxt }}</span>
                            </td>
                            <td>
                                <span class="badge" style="background:{{ $acBg }};color:{{ $acClr }};">{{ $acTxt }}</span>
                            </td>
                            <td>
                                <div style="font-size:.82rem;color:#374151;font-weight:600;">{{ $user->updated_at->format('Y/m/d') }}</div>
                                <div style="font-size:.72rem;color:#9ca3af;">{{ $user->updated_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.program-enrollments.show', $user) }}" class="act-btn act-view">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        عرض
                                    </a>
                                    <a href="{{ route('admin.students.show', $user) }}" class="act-btn" style="background:#f3f4f6;color:#374151;">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        الملف
                                    </a>
                                    @if($ps === 'pending')
                                    <form action="{{ route('admin.program-enrollments.approve', $user) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="act-btn act-approve"
                                                onclick="return confirm('قبول طلب {{ addslashes($user->name) }}؟')">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            قبول
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.program-enrollments.reject', $user) }}" method="POST" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="act-btn act-reject"
                                                onclick="return confirm('رفض طلب {{ addslashes($user->name) }}؟')">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            رفض
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($users->hasPages())
            <div class="pagination-wrap">
                {{ $users->links() }}
            </div>
            @endif
        </div>
        @endif
    </form>

    {{-- results summary --}}
    @if($users->total() > 0)
    <div style="margin-top:.75rem;font-size:.8rem;color:#9ca3af;text-align:center;">
        عرض {{ $users->firstItem() }}–{{ $users->lastItem() }} من {{ $users->total() }} نتيجة
    </div>
    @endif

</div>

{{-- ── Flash Messages ── --}}
@if(session('success'))
<div id="flash-msg" style="position:fixed;bottom:2rem;left:50%;transform:translateX(-50%);background:linear-gradient(135deg,#10b981,#059669);color:white;padding:.85rem 2rem;border-radius:14px;font-weight:700;font-size:.9rem;z-index:9999;box-shadow:0 8px 30px rgba(16,185,129,.3);">
    ✓ {{ session('success') }}
</div>
@elseif(session('error'))
<div id="flash-msg" style="position:fixed;bottom:2rem;left:50%;transform:translateX(-50%);background:linear-gradient(135deg,#ef4444,#dc2626);color:white;padding:.85rem 2rem;border-radius:14px;font-weight:700;font-size:.9rem;z-index:9999;box-shadow:0 8px 30px rgba(239,68,68,.3);">
    ✕ {{ session('error') }}
</div>
@endif

@push('scripts')
<script>
function toggleAll(cb) {
    document.querySelectorAll('.row-cb').forEach(r => r.checked = cb.checked);
    updateBulk();
}
function updateBulk() {
    const checked = document.querySelectorAll('.row-cb:checked').length;
    document.getElementById('sel-count').textContent = checked;
    document.getElementById('bulk-bar').classList.toggle('show', checked > 0);
    const all = document.querySelectorAll('.row-cb');
    document.getElementById('select-all').checked = all.length > 0 && checked === all.length;
}
function bulkApprove() {
    if (!confirm('قبول طلبات التسجيل المحددة؟')) return;
    const form = document.getElementById('bulk-form');
    form.action = '{{ route("admin.program-enrollments.bulk-approve") }}';
    form.submit();
}
function bulkReject() {
    if (!confirm('رفض طلبات التسجيل المحددة؟ سيتم إزالة البرنامج من حساباتهم.')) return;
    const form = document.getElementById('bulk-form');
    const m = document.createElement('input');
    m.type = 'hidden'; m.name = '_method'; m.value = 'DELETE';
    form.appendChild(m);
    form.action = '{{ route("admin.program-enrollments.bulk-reject") }}';
    form.submit();
}

// Auto-dismiss flash
const flash = document.getElementById('flash-msg');
if (flash) setTimeout(() => flash.style.display = 'none', 4000);
</script>
@endpush
@endsection
