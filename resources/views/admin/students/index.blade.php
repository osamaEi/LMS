@extends('layouts.dashboard')

@section('title', 'إدارة الطلاب')

@push('styles')
<style>
    .students-hero {
        background: linear-gradient(135deg, #065f46 0%, #047857 50%, #059669 100%);
        border-radius: 22px;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(5,150,105,0.35);
        margin-bottom: 1.5rem;
    }
    .students-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -5%;
        width: 45%;
        height: 220%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }
    .students-hero::after {
        content: '';
        position: absolute;
        bottom: -60%;
        left: 5%;
        width: 35%;
        height: 220%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.06) 0%, transparent 70%);
        pointer-events: none;
    }
    .hero-stat {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 16px;
        padding: 14px 20px;
        flex: 1;
        min-width: 110px;
        text-decoration: none;
        display: block;
        transition: all 0.2s;
        cursor: pointer;
    }
    .hero-stat:hover {
        background: rgba(255,255,255,0.18);
        transform: translateY(-2px);
        text-decoration: none;
    }
    .hero-stat.active-filter {
        background: rgba(255,255,255,0.25);
        border-color: rgba(255,255,255,0.5);
        box-shadow: 0 0 0 2px rgba(255,255,255,0.3);
    }
    .search-wrap {
        position: relative;
    }
    .search-input {
        width: 100%;
        padding: 11px 16px 11px 44px;
        border: 1.5px solid #e5e7eb;
        border-radius: 13px;
        font-size: 0.875rem;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
        background: #fff;
        color: #111827;
        direction: rtl;
    }
    .dark .search-input {
        background: #1e293b;
        border-color: rgba(255,255,255,0.1);
        color: #fff;
    }
    .search-input:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5,150,105,0.12);
    }
    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.15s;
        border: 1.5px solid #e5e7eb;
        background: #fff;
        color: #6b7280;
        white-space: nowrap;
    }
    .dark .filter-chip {
        background: #1e293b;
        border-color: rgba(255,255,255,0.1);
        color: rgba(255,255,255,0.6);
    }
    .filter-chip:hover { border-color: #059669; color: #059669; text-decoration: none; }
    .filter-chip.active { background: #059669; border-color: #059669; color: #fff; }
    .filter-chip .chip-count {
        background: rgba(0,0,0,0.12);
        border-radius: 999px;
        padding: 1px 7px;
        font-size: 0.72rem;
        font-weight: 800;
    }
    .filter-chip.active .chip-count { background: rgba(255,255,255,0.25); }

    /* Table */
    .table-card {
        background: #fff;
        border-radius: 20px;
        border: 1.5px solid #f1f5f9;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.05);
    }
    .dark .table-card { background: #1e293b; border-color: rgba(255,255,255,0.08); }

    .students-table { width: 100%; border-collapse: collapse; }

    .students-table thead {
        background: #f8faff;
        border-bottom: 2px solid #e5e7eb;
    }
    .dark .students-table thead {
        background: rgba(255,255,255,0.04);
        border-color: rgba(255,255,255,0.08);
    }
    .students-table th {
        padding: 14px 20px;
        text-align: right;
        font-size: 0.72rem;
        font-weight: 800;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        white-space: nowrap;
    }
    .dark .students-table th { color: #9ca3af; }

    .students-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.12s;
        position: relative;
    }
    .dark .students-table tbody tr { border-color: rgba(255,255,255,0.06); }
    .students-table tbody tr:last-child { border-bottom: none; }
    .students-table tbody tr:hover { background: #f8faff; }
    .dark .students-table tbody tr:hover { background: rgba(255,255,255,0.03); }

    .students-table td {
        padding: 16px 20px;
        font-size: 0.875rem;
        color: #374151;
        vertical-align: middle;
    }
    .dark .students-table td { color: #d1d5db; }

    .student-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 900;
        color: #fff;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
    .program-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.72rem;
        font-weight: 700;
        background: #ecfdf5;
        color: #065f46;
        max-width: 160px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .dark .program-badge { background: rgba(5,150,105,0.15); color: #6ee7b7; }
    .row-actions { display: flex; gap: 6px; align-items: center; }
    .act-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 12px;
        border-radius: 9px;
        font-size: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.15s;
        border: none;
        cursor: pointer;
        white-space: nowrap;
    }
    .act-view   { background: #ede9fe; color: #6d28d9; }
    .act-view:hover   { background: #ddd6fe; }
    .act-on     { background: #d1fae5; color: #065f46; }
    .act-on:hover     { background: #a7f3d0; }
    .act-off    { background: #fef3c7; color: #92400e; }
    .act-off:hover    { background: #fde68a; }
    .act-del    { background: #fee2e2; color: #991b1b; }
    .act-del:hover    { background: #fecaca; }
</style>
@endpush

@section('content')
<div style="max-width:1400px;margin:0 auto">

    {{-- Hero --}}
    <div class="students-hero">
        <div style="position:relative;z-index:1">
            {{-- Top row --}}
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:1.5rem">
                <div style="display:flex;align-items:center;gap:16px">
                    <div style="width:58px;height:58px;border-radius:16px;background:rgba(255,255,255,0.18);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="white">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 style="font-size:1.7rem;font-weight:900;color:#fff;margin:0;line-height:1.2">إدارة الطلاب</h1>
                        <p style="font-size:0.875rem;color:rgba(255,255,255,0.75);margin:5px 0 0">عرض وإدارة جميع الطلاب المسجلين في المنصة</p>
                    </div>
                </div>
                <a href="{{ route('admin.students.export') }}"
                   style="display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.35);color:#fff;font-weight:700;border-radius:14px;text-decoration:none;font-size:0.875rem;transition:all 0.2s"
                   onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    تصدير Excel
                </a>
            </div>

            {{-- Stats --}}
            <div style="display:flex;gap:10px;flex-wrap:wrap">
                <a href="{{ route('admin.students.index') }}" class="hero-stat {{ !$statusFilter ? 'active-filter' : '' }}">
                    <div style="font-size:1.8rem;font-weight:900;color:#fff;line-height:1">{{ $stats['total'] }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.75);margin-top:4px">إجمالي الطلاب</div>
                </a>
                <a href="{{ route('admin.students.index', ['status' => 'active']) }}" class="hero-stat {{ $statusFilter === 'active' ? 'active-filter' : '' }}">
                    <div style="font-size:1.8rem;font-weight:900;color:#6ee7b7;line-height:1">{{ $stats['active'] }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.75);margin-top:4px">نشطون</div>
                </a>
                <a href="{{ route('admin.students.index', ['status' => 'pending']) }}" class="hero-stat {{ $statusFilter === 'pending' ? 'active-filter' : '' }}">
                    <div style="font-size:1.8rem;font-weight:900;color:#fde68a;line-height:1">{{ $stats['pending'] }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.75);margin-top:4px">معلقون</div>
                </a>
                <a href="{{ route('admin.students.index', ['status' => 'inactive']) }}" class="hero-stat {{ $statusFilter === 'inactive' ? 'active-filter' : '' }}">
                    <div style="font-size:1.8rem;font-weight:900;color:#fca5a5;line-height:1">{{ $stats['inactive'] }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.75);margin-top:4px">غير نشطين</div>
                </a>
                <div class="hero-stat" style="cursor:default">
                    <div style="font-size:1.8rem;font-weight:900;color:#93c5fd;line-height:1">{{ $stats['this_month'] }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.75);margin-top:4px">هذا الشهر</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div style="margin-bottom:16px;padding:14px 18px;background:#f0fdf4;border:1px solid #86efac;border-radius:14px;color:#15803d;font-weight:600;display:flex;align-items:center;gap:10px">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="#15803d"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Toolbar --}}
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap">
        {{-- Search --}}
        <form method="GET" action="{{ route('admin.students.index') }}" style="flex:1;min-width:220px;max-width:380px" class="search-wrap">
            @if($statusFilter)
                <input type="hidden" name="status" value="{{ $statusFilter }}">
            @endif
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"
                 style="position:absolute;right:14px;top:50%;transform:translateY(-50%);pointer-events:none">
                <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
            </svg>
            <input type="text" name="search" value="{{ $search }}"
                   class="search-input"
                   placeholder="ابحث بالاسم، البريد، رقم الهوية...">
        </form>

        {{-- Status chips --}}
        @php
            $chips = [
                ['value' => null,       'label' => 'الكل',       'count' => $stats['total']],
                ['value' => 'active',   'label' => 'نشط',        'count' => $stats['active']],
                ['value' => 'pending',  'label' => 'معلق',        'count' => $stats['pending']],
                ['value' => 'inactive', 'label' => 'غير نشط',    'count' => $stats['inactive']],
            ];
        @endphp
        @foreach($chips as $chip)
        @php
            $isActive = ($statusFilter === $chip['value']) || ($chip['value'] === null && !$statusFilter);
            $href = $chip['value']
                ? route('admin.students.index', array_filter(['status' => $chip['value'], 'search' => $search]))
                : route('admin.students.index', array_filter(['search' => $search]));
        @endphp
        <a href="{{ $href }}" class="filter-chip {{ $isActive ? 'active' : '' }}">
            {{ $chip['label'] }}
            <span class="chip-count">{{ $chip['count'] }}</span>
        </a>
        @endforeach

        @if($search)
        <a href="{{ route('admin.students.index', $statusFilter ? ['status' => $statusFilter] : []) }}"
           style="display:inline-flex;align-items:center;gap:5px;padding:7px 12px;border-radius:9px;background:#fee2e2;color:#dc2626;font-size:0.78rem;font-weight:700;text-decoration:none">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            مسح البحث
        </a>
        @endif

        <span style="margin-right:auto;font-size:0.78rem;color:#9ca3af">
            {{ $students->total() }} طالب
        </span>
    </div>

    {{-- Table --}}
    @if($students->isEmpty())
    <div class="table-card" style="padding:80px 40px;text-align:center">
        <div style="width:84px;height:84px;border-radius:22px;background:linear-gradient(135deg,#d1fae5,#a7f3d0);display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
            <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h3 style="font-size:1.1rem;font-weight:800;color:#111827;margin:0 0 8px" class="dark:text-white">
            @if($search) لا توجد نتائج لـ "{{ $search }}" @else لا يوجد طلاب مسجلون @endif
        </h3>
        <p style="font-size:0.875rem;color:#9ca3af;margin:0">
            @if($search) جرّب كلمات بحث مختلفة @else لم يتم تسجيل أي طلاب بعد @endif
        </p>
    </div>
    @else
    <div class="table-card">
        <div style="overflow-x:auto">
            <table class="students-table">
                <thead>
                    <tr>
                        <th style="width:44px">#</th>
                        <th>الطالب</th>
                        <th>البريد الإلكتروني</th>
                        <th>رقم الهوية</th>
                        <th>رقم الهاتف</th>
                        <th>البرنامج</th>
                        <th>الحالة</th>
                        <th>تاريخ التسجيل</th>
                        <th style="text-align:center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $i => $student)
                    @php
                        $colors = ['#059669','#0071AA','#6366f1','#8b5cf6','#0891b2','#d97706','#dc2626','#db2777'];
                        $color  = $colors[$student->id % count($colors)];
                        $initials = collect(explode(' ', $student->name))->take(2)->map(fn($w) => mb_substr($w, 0, 1))->join('');
                        $statusConf = match($student->status ?? 'inactive') {
                            'active'    => ['label' => 'نشط',      'bg' => '#d1fae5', 'color' => '#065f46', 'dot' => '#059669'],
                            'pending'   => ['label' => 'معلق',      'bg' => '#fef3c7', 'color' => '#92400e', 'dot' => '#f59e0b'],
                            'suspended' => ['label' => 'موقوف',     'bg' => '#fee2e2', 'color' => '#991b1b', 'dot' => '#ef4444'],
                            default     => ['label' => 'غير نشط',   'bg' => '#f3f4f6', 'color' => '#4b5563', 'dot' => '#9ca3af'],
                        };
                    @endphp
                    <tr>
                        {{-- Row # --}}
                        <td style="color:#9ca3af;font-size:0.78rem;font-weight:600;font-family:monospace">
                            {{ $students->firstItem() + $i }}
                        </td>

                        {{-- Student --}}
                        <td>
                            <div style="display:flex;align-items:center;gap:12px">
                                <div class="student-avatar" style="background:linear-gradient(135deg,{{ $color }},{{ $color }}bb)">
                                    {{ $initials }}
                                </div>
                                <div style="min-width:0">
                                    <div style="font-weight:800;color:#111827;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:160px" class="dark:text-white">
                                        {{ $student->name }}
                                    </div>
                                    <div style="font-size:0.72rem;color:#9ca3af;margin-top:1px">
                                        {{ $student->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Email --}}
                        <td>
                            <span style="font-size:0.82rem;color:#6b7280" class="dark:text-gray-400">{{ $student->email }}</span>
                        </td>

                        {{-- National ID --}}
                        <td>
                            <span style="font-size:0.82rem;color:#374151;font-family:monospace;letter-spacing:0.04em" class="dark:text-gray-300">
                                {{ $student->national_id ?? '—' }}
                            </span>
                        </td>

                        {{-- Phone --}}
                        <td>
                            <span style="font-size:0.82rem;color:#6b7280" class="dark:text-gray-400">
                                {{ $student->phone ?? '—' }}
                            </span>
                        </td>

                        {{-- Program --}}
                        <td>
                            @if($student->program)
                            <span class="program-badge">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                                </svg>
                                {{ Str::limit($student->program->name, 20) }}
                            </span>
                            @else
                            <span style="font-size:0.78rem;color:#9ca3af">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="status-badge" style="background:{{ $statusConf['bg'] }};color:{{ $statusConf['color'] }}">
                                <span class="status-dot" style="background:{{ $statusConf['dot'] }}"></span>
                                {{ $statusConf['label'] }}
                            </span>
                        </td>

                        {{-- Date --}}
                        <td>
                            <span style="font-size:0.8rem;color:#6b7280" class="dark:text-gray-400">
                                {{ $student->created_at->format('d/m/Y') }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="row-actions" style="justify-content:center">
                                <a href="{{ route('admin.students.show', $student) }}" class="act-btn act-view">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    عرض
                                </a>

                                <form action="{{ route('admin.students.toggle-status', $student) }}" method="POST" style="display:inline">
                                    @csrf
                                    @if($student->status === 'active')
                                    <button type="submit"
                                            class="act-btn act-off"
                                            onclick="return confirm('إلغاء تفعيل حساب {{ addslashes($student->name) }}؟')">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        </svg>
                                        إيقاف
                                    </button>
                                    @else
                                    <button type="submit"
                                            class="act-btn act-on"
                                            onclick="return confirm('تفعيل حساب {{ addslashes($student->name) }}؟')">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        تفعيل
                                    </button>
                                    @endif
                                </form>

                                <form action="{{ route('admin.students.destroy', $student) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="act-btn act-del"
                                            onclick="return confirm('حذف الطالب {{ addslashes($student->name) }} نهائياً؟')">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($students->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f1f5f9" class="dark:border-white/08">
            {{ $students->links() }}
        </div>
        @endif
    </div>
    @endif

</div>
@endsection
