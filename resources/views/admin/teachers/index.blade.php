@extends('layouts.dashboard')

@section('title', 'إدارة المدربون ')

@push('styles')
<style>
    .teachers-hero {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 22px;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.25);
    }
    .teachers-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -5%;
        width: 45%;
        height: 220%;
        background: radial-gradient(ellipse, rgba(99,102,241,0.18) 0%, transparent 70%);
        pointer-events: none;
    }
    .teachers-hero::after {
        content: '';
        position: absolute;
        bottom: -60%;
        left: 2%;
        width: 35%;
        height: 220%;
        background: radial-gradient(ellipse, rgba(139,92,246,0.1) 0%, transparent 70%);
        pointer-events: none;
    }
    .hero-stat {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.12);
        border-radius: 16px;
        padding: 14px 20px;
        flex: 1;
        min-width: 110px;
    }
    .table-card {
        background: #fff;
        border-radius: 18px;
        border: 1.5px solid #f1f5f9;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        margin-bottom: 8px;
    }
    .dark .table-card { background: #1e293b; border-color: rgba(255,255,255,0.08); }
    .teachers-table { width: 100%; min-width: 980px; border-collapse: collapse; font-size: 0.85rem; }
    .teachers-table th {
        padding: 12px 14px;
        text-align: right;
        font-size: 0.72rem;
        font-weight: 700;
        color: #94a3b8;
        background: #f8fafc;
        border-bottom: 2px solid #f1f5f9;
        white-space: nowrap;
    }
    .teachers-table td { padding: 12px 14px; vertical-align: middle; border-bottom: 1px solid #f8fafc; }
    .teachers-table tbody tr { transition: background 0.1s; }
    .teachers-table tbody tr:hover { background: #f8faff; }
    .dark .teachers-table th { background: rgba(255,255,255,0.04); border-bottom-color: rgba(255,255,255,0.08); }
    .dark .teachers-table td { border-bottom-color: rgba(255,255,255,0.05); }
    .dark .teachers-table tbody tr:hover { background: rgba(255,255,255,0.03); }
    .teacher-name {
        display: block;
        font-weight: 800;
        color: #111827;
        font-size: 0.875rem;
        text-decoration: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 220px;
    }
    .teacher-name:hover { color: #4f46e5; }
    .dark .teacher-name { color: #fff; }
    .cell-ltr { direction: ltr; text-align: right; }
    .chip {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 700;
        white-space: nowrap;
    }
    .row-actions { display: flex; gap: 6px; flex-wrap: nowrap; }
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 6px 11px;
        border-radius: 9px;
        font-size: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        transition: filter 0.15s;
        border: none;
        cursor: pointer;
        white-space: nowrap;
    }
    .action-btn:hover { filter: brightness(0.95); }
    .search-input {
        width: 100%;
        padding: 10px 16px 10px 42px;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        font-size: 0.875rem;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
        background: #fff;
        color: #111827;
    }
    .dark .search-input {
        background: #1e293b;
        border-color: rgba(255,255,255,0.1);
        color: #fff;
    }
    .search-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
    }
    .avatar-initials {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 800;
        color: #fff;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">

    {{-- Hero --}}
    <div class="teachers-hero mb-6">
        <div class="relative z-10">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:1.5rem">
                <div style="display:flex;align-items:center;gap:16px">
                    <div style="width:58px;height:58px;border-radius:16px;background:linear-gradient(135deg,#6366f1,#4f46e5);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 8px 24px rgba(99,102,241,0.4)">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="white">
                            <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 style="font-size:1.7rem;font-weight:900;color:#fff;margin:0;line-height:1.2">إدارة المدربون </h1>
                        <p style="font-size:0.875rem;color:rgba(255,255,255,0.65);margin:5px 0 0">عرض وإدارة جميع المدربون  في المنصة</p>
                    </div>
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap">
                    <a href="{{ route('admin.teachers.export') }}"
                       style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:rgba(255,255,255,0.1);border:1.5px solid rgba(255,255,255,0.25);color:#fff;font-weight:700;border-radius:14px;text-decoration:none;font-size:0.875rem;transition:all 0.2s"
                       onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        تصدير Excel
                    </a>
                    <a href="{{ route('admin.teachers.create') }}"
                       style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:#fff;color:#4f46e5;font-weight:800;border-radius:14px;text-decoration:none;font-size:0.875rem;transition:all 0.2s;box-shadow:0 4px 14px rgba(0,0,0,0.15)"
                       onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='translateY(0)'">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        إضافة مدرب
                    </a>
                </div>
            </div>

            {{-- Stats --}}
            <div style="display:flex;gap:10px;flex-wrap:wrap">
                <div class="hero-stat">
                    <div style="font-size:1.8rem;font-weight:900;color:#fff;line-height:1">{{ $stats['total'] }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.65);margin-top:4px">إجمالي المدربون </div>
                </div>
                <div class="hero-stat">
                    <div style="font-size:1.8rem;font-weight:900;color:#a5b4fc;line-height:1">{{ $stats['with_subjects'] }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.65);margin-top:4px">لديهم مقرارت</div>
                </div>
                <div class="hero-stat">
                    <div style="font-size:1.8rem;font-weight:900;color:#34d399;line-height:1">{{ $stats['this_month'] }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.65);margin-top:4px">انضموا هذا الشهر</div>
                </div>
                <div class="hero-stat">
                    <div style="font-size:1.8rem;font-weight:900;color:#fbbf24;line-height:1">{{ $stats['total'] - $stats['with_subjects'] }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.65);margin-top:4px">بدون مقرارت</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div style="margin-bottom:16px;padding:14px 18px;background:#f0fdf4;border:1px solid #86efac;border-radius:14px;color:#16a34a;font-weight:600;display:flex;align-items:center;gap:10px">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="#16a34a"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Search + Count --}}
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap">
        <form method="GET" action="{{ route('admin.teachers.index') }}" style="flex:1;min-width:200px;max-width:400px;position:relative">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" style="position:absolute;right:14px;top:50%;transform:translateY(-50%)">
                <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
            </svg>
            <input type="text" name="search" value="{{ $search }}"
                   class="search-input"
                   placeholder="ابحث باسم المدرب، البريد، رقم الهوية...">
        </form>
        @if($search)
        <a href="{{ route('admin.teachers.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:10px;background:#fef2f2;color:#dc2626;font-size:0.8rem;font-weight:700;text-decoration:none">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            إلغاء البحث
        </a>
        @endif
        <div style="margin-right:auto;font-size:0.8rem;color:#6b7280">
            {{ $teachers->total() }} مدرب
            @if($search)
                <span style="color:#6366f1">· بحث: "{{ $search }}"</span>
            @endif
        </div>
    </div>

    {{-- Teachers Table --}}
    @if($teachers->count())
    <div class="table-card">
        <div style="overflow-x:auto">
            <table class="teachers-table">
                <thead>
                    <tr>
                        <th style="width:44px">#</th>
                        <th>المدرب</th>
                        <th>التواصل</th>
                        <th>المقررات</th>
                        <th>الدورات</th>
                        <th style="width:90px">الحالة</th>
                        <th style="width:1%">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $teacher)
                    @php
                        $colors = ['#6366f1','#8b5cf6','#0071AA','#0891b2','#059669','#d97706','#dc2626','#db2777'];
                        $color  = $colors[$teacher->id % count($colors)];
                        $initials = collect(explode(' ', $teacher->name))->take(2)->map(fn($w) => mb_substr($w, 0, 1))->join('');
                        // Pivot assignments plus the legacy subjects.teacher_id column.
                        $allAssigned = $teacher->assignedSubjects->merge($teacher->subjects)->unique('id');
                        $isActive = $teacher->status === 'active';
                        $progTypeColors = ['training'=>'#6366f1','english'=>'#0891b2','course'=>'#059669','diploma'=>'#d97706'];
                    @endphp
                    <tr>
                        <td style="color:#cbd5e1;font-size:0.72rem">
                            {{ $loop->iteration + ($teachers->currentPage() - 1) * $teachers->perPage() }}
                        </td>

                        {{-- Teacher --}}
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                <div class="avatar-initials" style="background:linear-gradient(135deg,{{ $color }},{{ $color }}bb)">
                                    {{ $initials }}
                                </div>
                                <div style="min-width:0">
                                    <a href="{{ route('admin.teachers.show', $teacher) }}" class="teacher-name">{{ $teacher->name }}</a>
                                    <div style="font-size:0.72rem;color:#9ca3af;margin-top:2px">
                                        انضم {{ $teacher->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Contact --}}
                        <td>
                            <div class="cell-ltr" style="font-size:0.78rem;color:#475569;max-width:210px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                                 title="{{ $teacher->email }}">{{ $teacher->email }}</div>
                            @if($teacher->phone)
                            <div class="cell-ltr" style="font-size:0.75rem;color:#94a3b8;margin-top:2px">{{ $teacher->phone }}</div>
                            @endif
                            @if($teacher->national_id)
                            <div class="cell-ltr" style="font-size:0.72rem;color:#cbd5e1;font-family:monospace;margin-top:2px">{{ $teacher->national_id }}</div>
                            @endif
                        </td>

                        {{-- Subjects --}}
                        <td>
                            @if($allAssigned->count())
                            <div style="display:flex;flex-wrap:wrap;gap:4px;max-width:250px">
                                @foreach($allAssigned->take(3) as $subj)
                                <span class="chip" style="background:{{ $color }}14;color:{{ $color }}" title="{{ $subj->name_ar }}">
                                    {{ Str::limit($subj->name_ar, 20) }}
                                </span>
                                @endforeach
                                @if($allAssigned->count() > 3)
                                <span class="chip" style="background:#f1f5f9;color:#64748b"
                                      title="{{ $allAssigned->skip(3)->pluck('name_ar')->implode('، ') }}">
                                    +{{ $allAssigned->count() - 3 }}
                                </span>
                                @endif
                            </div>
                            @else
                            <span style="font-size:0.75rem;color:#cbd5e1">—</span>
                            @endif
                        </td>

                        {{-- Programs --}}
                        <td>
                            @if($teacher->teachingPrograms->count())
                            <div style="display:flex;flex-wrap:wrap;gap:4px;max-width:200px">
                                @foreach($teacher->teachingPrograms->take(2) as $prog)
                                @php $pc = $progTypeColors[$prog->type] ?? '#6b7280'; @endphp
                                <span class="chip" style="background:{{ $pc }}14;color:{{ $pc }}" title="{{ $prog->name_ar }}">
                                    {{ Str::limit($prog->name_ar, 18) }}
                                </span>
                                @endforeach
                                @if($teacher->teachingPrograms->count() > 2)
                                <span class="chip" style="background:#f1f5f9;color:#64748b"
                                      title="{{ $teacher->teachingPrograms->skip(2)->pluck('name_ar')->implode('، ') }}">
                                    +{{ $teacher->teachingPrograms->count() - 2 }}
                                </span>
                                @endif
                            </div>
                            @else
                            <span style="font-size:0.75rem;color:#cbd5e1">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:0.72rem;font-weight:700;
                                  background:{{ $isActive ? '#dcfce7' : '#fee2e2' }};color:{{ $isActive ? '#16a34a' : '#dc2626' }}">
                                <span style="width:6px;height:6px;border-radius:50%;background:currentColor"></span>
                                {{ $isActive ? 'نشط' : 'معطّل' }}
                            </span>
                        </td>

                        {{-- Actions. Assigning subjects/courses/classes lives on the
                             teacher page now, so this column only links there. --}}
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('admin.teachers.show', $teacher) }}" class="action-btn"
                                   style="background:#ede9fe;color:#6d28d9">عرض وتعيين</a>
                                <a href="{{ route('admin.teachers.edit', $teacher) }}" class="action-btn"
                                   style="background:#fef3c7;color:#b45309">تعديل</a>

                                @if($isActive)
                                <form action="{{ route('admin.teachers.dashboard-access', $teacher) }}" method="POST" style="margin:0">
                                    @csrf
                                    <button type="submit" class="action-btn" style="background:#d1fae5;color:#065f46"
                                            title="دخول لوحة المدرّس {{ $teacher->name }}">لوحة المدرّس</button>
                                </form>
                                @endif

                                <form action="{{ route('admin.teachers.toggle-status', $teacher) }}" method="POST" style="margin:0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="action-btn"
                                            style="background:{{ $isActive ? '#fef9c3' : '#dcfce7' }};color:{{ $isActive ? '#92400e' : '#15803d' }}"
                                            title="{{ $isActive ? 'تعطيل حساب ' . $teacher->name : 'تفعيل حساب ' . $teacher->name }}">
                                        {{ $isActive ? 'تعطيل' : 'تفعيل' }}
                                    </button>
                                </form>

                                <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" style="margin:0"
                                      onsubmit="return confirm('هل أنت متأكد من حذف المدرب ' + {{ Js::from($teacher->name) }} + '؟ لا يمكن التراجع عن هذا الإجراء.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn" style="background:#fee2e2;color:#dc2626"
                                            title="حذف {{ $teacher->name }}">حذف</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($teachers->hasPages())
    <div style="display:flex;justify-content:center;padding:16px 0">
        {{ $teachers->links() }}
    </div>
    @endif


    @else
    {{-- Empty State --}}
    <div style="background:#fff;border-radius:20px;border:1.5px solid #f1f5f9;padding:80px 40px;text-align:center" class="dark:bg-slate-800 dark:border-white/10">
        <div style="width:90px;height:90px;border-radius:24px;background:linear-gradient(135deg,#ede9fe,#ddd6fe);display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
            </svg>
        </div>
        <h3 style="font-size:1.15rem;font-weight:800;color:#111827;margin:0 0 8px" class="dark:text-white">
            @if($search) لا توجد نتائج لـ "{{ $search }}" @else لا يوجد أساتذة مسجلون @endif
        </h3>
        <p style="font-size:0.875rem;color:#9ca3af;margin:0 0 24px">
            @if($search) جرّب كلمات بحث مختلفة @else ابدأ بإضافة أول مدرب في المنصة @endif
        </p>
        @if($search)
        <a href="{{ route('admin.teachers.index') }}"
           style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:#6366f1;color:#fff;font-weight:700;border-radius:12px;text-decoration:none;font-size:0.875rem">
            عرض جميع المدربون 
        </a>
        @else
        <a href="{{ route('admin.teachers.create') }}"
           style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:#6366f1;color:#fff;font-weight:700;border-radius:12px;text-decoration:none;font-size:0.875rem;box-shadow:0 8px 24px rgba(99,102,241,0.3)">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            إضافة أول مدرب
        </a>
        @endif
    </div>
    @endif

</div>
@endsection
