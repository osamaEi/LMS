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
    .teacher-card {
        background: #fff;
        border-radius: 20px;
        border: 1.5px solid #f1f5f9;
        overflow: hidden;
        transition: all 0.25s;
        display: flex;
        flex-direction: column;
    }
    .dark .teacher-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.08);
    }
    .teacher-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        border-color: #6366f1;
    }
    .teacher-card:hover .card-actions {
        opacity: 1;
        transform: translateY(0);
    }
    .card-actions {
        opacity: 0;
        transform: translateY(6px);
        transition: all 0.2s;
    }
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 7px 14px;
        border-radius: 10px;
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.15s;
        border: none;
        cursor: pointer;
    }
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
        width: 72px;
        height: 72px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 900;
        color: #fff;
        flex-shrink: 0;
        border: 3px solid rgba(255,255,255,0.3);
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
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
                        إضافة أستاذ
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
                   placeholder="ابحث باسم الأستاذ، البريد، رقم الهوية...">
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
            {{ $teachers->total() }} أستاذ
            @if($search)
                <span style="color:#6366f1">· بحث: "{{ $search }}"</span>
            @endif
        </div>
    </div>

    {{-- Cards Grid --}}
    @if($teachers->count())
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:18px;margin-bottom:24px">
        @foreach($teachers as $teacher)
        @php
            $colors = ['#6366f1','#8b5cf6','#0071AA','#0891b2','#059669','#d97706','#dc2626','#db2777'];
            $color  = $colors[$teacher->id % count($colors)];
            $initials = collect(explode(' ', $teacher->name))->take(2)->map(fn($w) => mb_substr($w, 0, 1))->join('');
        @endphp
        <div class="teacher-card">
            {{-- Top color accent --}}
            <div style="height:5px;background:linear-gradient(90deg,{{ $color }},{{ $color }}88)"></div>

            <div style="padding:20px">
                {{-- Avatar + info --}}
                <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:16px">
                    <div class="avatar-initials" style="background:linear-gradient(135deg,{{ $color }},{{ $color }}bb)">
                        {{ $initials }}
                    </div>
                    <div style="flex:1;min-width:0">
                        <h3 style="font-size:1rem;font-weight:800;color:#111827;margin:0 0 3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" class="dark:text-white">
                            {{ $teacher->name }}
                        </h3>
                        <p style="font-size:0.78rem;color:#6b7280;margin:0 0 8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                            {{ $teacher->email }}
                        </p>
                        {{-- Subjects count badge --}}
                        @php $assignedCount = $teacher->assignedSubjects->merge($teacher->subjects)->unique('id')->count(); @endphp
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:0.72rem;font-weight:700;background:{{ $color }}18;color:{{ $color }}">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                            </svg>
                            {{ $assignedCount }} {{ $assignedCount === 1 ? 'مادة' : 'مقررات' }}
                        </span>
                    </div>
                </div>

                {{-- Assigned subjects list (pivot + legacy teacher_id, same as /my-subjects) --}}
                @php $allAssigned = $teacher->assignedSubjects->merge($teacher->subjects)->unique('id'); @endphp
                @if($allAssigned->count())
                <div style="margin-bottom:14px">
                    <p style="font-size:0.72rem;font-weight:700;color:#9ca3af;margin:0 0 7px;text-transform:uppercase;letter-spacing:.05em">المقررات المعيّنة</p>
                    <div style="display:flex;flex-direction:column;gap:5px">
                        @foreach($allAssigned->take(4) as $subj)
                        <div style="display:flex;align-items:center;gap:8px;padding:7px 10px;background:#f8faff;border-radius:9px;border-right:3px solid {{ $color }}" class="dark:bg-white/5">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="{{ $color }}">
                                <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                            </svg>
                            <span style="font-size:0.8rem;font-weight:600;color:#111827;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" class="dark:text-white">
                                {{ $subj->name_ar }}
                            </span>
                            <span style="font-size:0.68rem;color:#9ca3af;flex-shrink:0">{{ $subj->code }}</span>
                        </div>
                        @endforeach
                        @if($allAssigned->count() > 4)
                        <div style="text-align:center;padding:5px;font-size:0.72rem;color:#6b7280">
                            + {{ $allAssigned->count() - 4 }} مقررات أخرى
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <div style="margin-bottom:14px;padding:10px 12px;background:#fafafa;border-radius:10px;border:1.5px dashed #e5e7eb;text-align:center">
                    <span style="font-size:0.78rem;color:#9ca3af">لم يتم تعيين مقررات بعد</span>
                </div>
                @endif

                {{-- Assigned programs --}}
                @if($teacher->teachingPrograms->count())
                @php
                $progTypeColors = ['training'=>'#6366f1','english'=>'#0891b2','course'=>'#059669','diploma'=>'#d97706'];
                $progTypeLabels = ['training'=>'تدريبي','english'=>'إنجليزي','course'=>'تأهيلي','diploma'=>'دبلوم'];
                @endphp
                <div style="margin-bottom:14px">
                    <p style="font-size:0.72rem;font-weight:700;color:#9ca3af;margin:0 0 7px;text-transform:uppercase;letter-spacing:.05em">الدورات المعيّنة</p>
                    <div style="display:flex;flex-direction:column;gap:5px">
                        @foreach($teacher->teachingPrograms as $prog)
                        @php $pc = $progTypeColors[$prog->type] ?? '#6b7280'; $pl = $progTypeLabels[$prog->type] ?? $prog->type; @endphp
                        <div style="display:flex;align-items:center;gap:8px;padding:7px 10px;background:#f0fdf4;border-radius:9px;border-right:3px solid {{ $pc }}">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="{{ $pc }}" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                            </svg>
                            <span style="font-size:0.8rem;font-weight:600;color:#111827;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" class="dark:text-white">
                                {{ $prog->name_ar }}
                            </span>
                            <span style="font-size:0.65rem;padding:2px 6px;border-radius:999px;background:{{ $pc }}18;color:{{ $pc }};font-weight:700;flex-shrink:0">{{ $pl }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Meta info --}}
                <div style="display:flex;flex-direction:column;gap:6px;padding:12px;background:#f8faff;border-radius:12px;margin-bottom:14px" class="dark:bg-white/5">
                    @if($teacher->phone)
                    <div style="display:flex;align-items:center;gap:8px;font-size:0.78rem;color:#6b7280">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="{{ $color }}" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span>{{ $teacher->phone }}</span>
                    </div>
                    @endif
                    @if($teacher->national_id)
                    <div style="display:flex;align-items:center;gap:8px;font-size:0.78rem;color:#6b7280">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="{{ $color }}" stroke-width="2">
                            <rect x="3" y="6" width="18" height="13" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M8 13h.01M12 10h4M12 13h4"/>
                        </svg>
                        <span style="font-family:monospace;letter-spacing:0.05em">{{ $teacher->national_id }}</span>
                    </div>
                    @endif
                    <div style="display:flex;align-items:center;gap:8px;font-size:0.78rem;color:#6b7280">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="{{ $color }}" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span>انضم {{ $teacher->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                {{-- Status badge --}}
                @php $isActive = $teacher->status === 'active'; @endphp
                <div style="margin-bottom:12px">
                    <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:0.72rem;font-weight:700;
                          background:{{ $isActive ? '#dcfce7' : '#fee2e2' }};color:{{ $isActive ? '#16a34a' : '#dc2626' }}">
                        <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block"></span>
                        {{ $isActive ? 'نشط' : 'معطّل' }}
                    </span>
                </div>

                {{-- Actions --}}
                <div class="card-actions" style="display:flex;gap:8px;flex-wrap:wrap">
                    <a href="{{ route('admin.teachers.show', $teacher) }}"
                       class="action-btn"
                       style="flex:1;background:#ede9fe;color:#6d28d9;justify-content:center">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        عرض
                    </a>
                    <a href="{{ route('admin.teachers.edit', $teacher) }}"
                       class="action-btn"
                       style="flex:1;background:#fef3c7;color:#b45309;justify-content:center">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        تعديل
                    </a>

                    {{-- Assign programs (direct) --}}
                    <button type="button" class="action-btn"
                            style="flex:1;background:#f0fdf4;color:#15803d;justify-content:center"
                            onclick="openProgramModal({{ $teacher->id }}, '{{ addslashes($teacher->name) }}', {{ $teacher->teachingPrograms->pluck('id')->toJson() }})">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        دورات
                    </button>

                    {{-- Assign subjects --}}
                    <button type="button" class="action-btn"
                            style="flex:1;background:#e0f2fe;color:#0369a1;justify-content:center"
                            onclick="openAssignModal({{ $teacher->id }}, '{{ addslashes($teacher->name) }}', {{ $teacher->assignedSubjects->pluck('id')->toJson() }})">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        مقررات
                    </button>

                    {{-- Toggle active/inactive --}}
                    <form action="{{ route('admin.teachers.toggle-status', $teacher) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="action-btn"
                                style="background:{{ $isActive ? '#fef9c3' : '#dcfce7' }};color:{{ $isActive ? '#92400e' : '#15803d' }}"
                                title="{{ $isActive ? 'تعطيل الحساب' : 'تفعيل الحساب' }}">
                            @if($isActive)
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            @else
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @endif
                        </button>
                    </form>

                    <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST"
                          onsubmit="return confirm('هل أنت متأكد من حذف المدرب{{ addslashes($teacher->name) }}؟ لا يمكن التراجع عن هذا الإجراء.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn" style="background:#fee2e2;color:#dc2626">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($teachers->hasPages())
    <div style="display:flex;justify-content:center;padding:8px 0">
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
            @if($search) جرّب كلمات بحث مختلفة @else ابدأ بإضافة أول مدربفي المنصة @endif
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
            إضافة أول أستاذ
        </a>
        @endif
    </div>
    @endif

</div>

{{-- Assign Subjects Modal --}}
<div id="assign-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;padding:16px" onclick="if(event.target===this)closeAssignModal()">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:520px;overflow:hidden;box-shadow:0 24px 80px rgba(0,0,0,0.25)" class="dark:bg-slate-800">
        {{-- Modal header --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid #f1f5f9">
            <div>
                <h3 style="font-size:1rem;font-weight:800;color:#111827;margin:0" class="dark:text-white" id="modal-title">تعيين المقررات</h3>
                <p style="font-size:0.78rem;color:#6b7280;margin:3px 0 0" id="modal-subtitle">اختر المقررات التي يدرّسها هذا الأستاذ</p>
            </div>
            <button onclick="closeAssignModal()" style="width:32px;height:32px;border-radius:8px;border:none;background:#f1f5f9;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#6b7280">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Search inside modal --}}
        <div style="padding:14px 22px 0">
            <input type="text" id="modal-search" placeholder="ابحث في المقررات..."
                   oninput="filterModalSubjects(this.value)"
                   style="width:100%;padding:8px 12px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:0.875rem;color:#111827;box-sizing:border-box;outline:none">
        </div>

        {{-- Programs grouped by type --}}
        <div style="max-height:380px;overflow-y:auto;padding:14px 22px" id="modal-subjects-list">
            @php
            $typeConfig = [
                'training' => ['label' => 'البرامج التدريبية',       'color' => '#6366f1', 'bg' => '#ede9fe'],
                'english'  => ['label' => 'دورات اللغة الإنجليزية',  'color' => '#0891b2', 'bg' => '#e0f2fe'],
                'course'   => ['label' => 'الدورات التأهيلية',        'color' => '#059669', 'bg' => '#dcfce7'],
            ];
            @endphp
            @forelse($programsByType as $type => $programs)
            @php $cfg = $typeConfig[$type] ?? ['label' => $type, 'color' => '#6b7280', 'bg' => '#f3f4f6']; @endphp
            <div class="type-section" data-type="{{ $type }}" style="margin-bottom:14px">
                <div style="display:flex;align-items:center;gap:6px;padding:5px 10px;border-radius:8px;background:{{ $cfg['bg'] }};margin-bottom:6px">
                    <span style="font-size:0.72rem;font-weight:800;color:{{ $cfg['color'] }};text-transform:uppercase;letter-spacing:.06em">{{ $cfg['label'] }}</span>
                </div>
                @foreach($programs as $prog)
                @php $subjects = $prog->terms->flatMap->subjects->unique('id'); @endphp
                @if($subjects->count())
                <div class="prog-block" data-prog="{{ $prog->id }}" style="margin-bottom:4px">
                    <div class="prog-hdr-row" style="display:flex;align-items:center;gap:10px;padding:7px 10px;border-radius:9px;cursor:pointer;border:1.5px solid #f1f5f9;transition:background 0.1s"
                         onmouseover="this.style.background='#f8faff'" onmouseout="this.style.background=''"
                         onclick="toggleProgBlock(this)">
                        <input type="checkbox" class="prog-all-cb" data-prog="{{ $prog->id }}"
                               onclick="event.stopPropagation()" onchange="toggleProgramSubjects(this)"
                               style="width:15px;height:15px;accent-color:{{ $cfg['color'] }};cursor:pointer;flex-shrink:0">
                        <span style="flex:1;font-size:0.84rem;font-weight:700;color:#111827;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" class="dark:text-white">{{ $prog->name_ar }}</span>
                        <span style="font-size:0.7rem;color:#9ca3af;flex-shrink:0;background:#f3f4f6;padding:2px 7px;border-radius:999px">{{ $subjects->count() }}</span>
                        <svg class="prog-arrow" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2.5" style="flex-shrink:0;transition:transform 0.2s;transform:rotate(180deg)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    <div class="prog-subjects-div" style="display:block;padding:4px 4px 4px 24px">
                        @foreach($subjects as $subj)
                        <label class="subj-label" id="subj-row-{{ $subj->id }}"
                               style="display:flex;align-items:center;gap:10px;padding:6px 10px;border-radius:8px;cursor:pointer;margin-bottom:2px;transition:background 0.1s"
                               onmouseover="this.style.background='#f8faff'" onmouseout="this.style.background='transparent'">
                            <input type="checkbox" class="assign-cb" value="{{ $subj->id }}" data-prog="{{ $prog->id }}"
                                   style="width:14px;height:14px;accent-color:#0071AA;cursor:pointer;flex-shrink:0">
                            <div style="flex:1;min-width:0">
                                <p style="font-size:0.82rem;font-weight:600;color:#111827;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" class="dark:text-white">{{ $subj->name_ar }}</p>
                                <p style="font-size:0.7rem;color:#9ca3af;margin:1px 0 0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $subj->code }}{{ $subj->name_en ? ' · ' . $subj->name_en : '' }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @empty
            <p style="text-align:center;color:#9ca3af;font-size:0.875rem;padding:24px 0">لا توجد برامج أو مقررات</p>
            @endforelse
        </div>

        {{-- Footer --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 22px;border-top:1px solid #f1f5f9;gap:10px">
            <span style="font-size:0.78rem;color:#6b7280" id="modal-selected-count">0 مقرر محدد</span>
            <div style="display:flex;gap:8px">
                <button onclick="closeAssignModal()" style="padding:8px 18px;border-radius:10px;border:1.5px solid #e5e7eb;background:#fff;color:#374151;font-size:0.875rem;font-weight:700;cursor:pointer">
                    إلغاء
                </button>
                <button onclick="submitAssign()" style="padding:8px 22px;border-radius:10px;border:none;background:#0071AA;color:#fff;font-size:0.875rem;font-weight:700;cursor:pointer">
                    حفظ
                </button>
            </div>
        </div>

        {{-- Hidden form --}}
        <form id="assign-form" method="POST" style="display:none">
            @csrf
            <div id="assign-inputs"></div>
        </form>
    </div>
</div>

{{-- Assign Programs Modal --}}
<div id="prog-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;padding:16px" onclick="if(event.target===this)closeProgramModal()">
    <div style="background:#fff;border-radius:18px;width:100%;max-width:480px;overflow:hidden;box-shadow:0 24px 80px rgba(0,0,0,0.25)" class="dark:bg-slate-800">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid #f1f5f9">
            <div>
                <h3 style="font-size:1rem;font-weight:800;color:#111827;margin:0" class="dark:text-white" id="prog-modal-title">تعيين الدورات</h3>
                <p style="font-size:0.78rem;color:#6b7280;margin:3px 0 0">اختر الدورات التي يدرّبها هذا الأستاذ</p>
            </div>
            <button onclick="closeProgramModal()" style="width:32px;height:32px;border-radius:8px;border:none;background:#f1f5f9;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#6b7280">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div style="max-height:400px;overflow-y:auto;padding:16px 22px" id="prog-modal-list">
            @php
            $progTypeConfig = [
                'training' => ['label' => 'البرامج التدريبية',       'color' => '#6366f1', 'bg' => '#ede9fe'],
                'english'  => ['label' => 'دورات اللغة الإنجليزية',  'color' => '#0891b2', 'bg' => '#e0f2fe'],
                'course'   => ['label' => 'الدورات التأهيلية',        'color' => '#059669', 'bg' => '#dcfce7'],
                'diploma'  => ['label' => 'الدبلومات',                'color' => '#d97706', 'bg' => '#fef3c7'],
            ];
            @endphp
            @forelse($allPrograms as $type => $programs)
            @php $pcfg = $progTypeConfig[$type] ?? ['label' => $type, 'color' => '#6b7280', 'bg' => '#f3f4f6']; @endphp
            <div style="margin-bottom:14px">
                <div style="padding:5px 10px;border-radius:8px;background:{{ $pcfg['bg'] }};margin-bottom:6px">
                    <span style="font-size:0.72rem;font-weight:800;color:{{ $pcfg['color'] }};letter-spacing:.06em">{{ $pcfg['label'] }}</span>
                </div>
                @foreach($programs as $prog)
                <label style="display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;cursor:pointer;margin-bottom:4px;border:1.5px solid #f1f5f9;transition:border-color 0.15s"
                       onmouseover="this.style.borderColor='{{ $pcfg['color'] }}33'" onmouseout="this.style.borderColor='#f1f5f9'">
                    <input type="checkbox" class="prog-top-cb" value="{{ $prog->id }}"
                           style="width:16px;height:16px;accent-color:{{ $pcfg['color'] }};cursor:pointer;flex-shrink:0">
                    <span style="flex:1;font-size:0.875rem;font-weight:700;color:#111827" class="dark:text-white">{{ $prog->name_ar }}</span>
                    @if($prog->status === 'active')
                    <span style="font-size:0.68rem;padding:2px 8px;border-radius:999px;background:#dcfce7;color:#16a34a;font-weight:700;flex-shrink:0">نشط</span>
                    @endif
                </label>
                @endforeach
            </div>
            @empty
            <p style="text-align:center;color:#9ca3af;padding:24px 0">لا توجد دورات</p>
            @endforelse
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 22px;border-top:1px solid #f1f5f9;gap:10px">
            <span style="font-size:0.78rem;color:#6b7280" id="prog-selected-count">0 دورة محددة</span>
            <div style="display:flex;gap:8px">
                <button onclick="closeProgramModal()" style="padding:8px 18px;border-radius:10px;border:1.5px solid #e5e7eb;background:#fff;color:#374151;font-size:0.875rem;font-weight:700;cursor:pointer">
                    إلغاء
                </button>
                <button onclick="submitProgramAssign()" style="padding:8px 22px;border-radius:10px;border:none;background:#059669;color:#fff;font-size:0.875rem;font-weight:700;cursor:pointer">
                    حفظ
                </button>
            </div>
        </div>

        <form id="prog-assign-form" method="POST" style="display:none">
            @csrf
            <div id="prog-assign-inputs"></div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentTeacherId = null;

function openAssignModal(teacherId, teacherName, assignedIds) {
    currentTeacherId = teacherId;
    document.getElementById('modal-title').textContent = 'تعيين المقررات — ' + teacherName;
    document.getElementById('modal-search').value = '';

    // Reset visibility from any previous search
    document.querySelectorAll('.subj-label').forEach(l => l.style.display = 'flex');
    document.querySelectorAll('.prog-block, .type-section').forEach(el => el.style.display = '');

    // Set subject checkboxes
    document.querySelectorAll('.assign-cb').forEach(cb => {
        cb.checked = assignedIds.includes(parseInt(cb.value));
    });

    // Expand all programs and sync their header checkboxes
    document.querySelectorAll('.prog-block').forEach(block => {
        const progId = block.dataset.prog;
        const subjDiv = block.querySelector('.prog-subjects-div');
        const arrow = block.querySelector('.prog-arrow');
        if (subjDiv) subjDiv.style.display = 'block';
        if (arrow) arrow.style.transform = 'rotate(180deg)';
        syncProgHeader(progId);
    });

    updateCount();
    document.getElementById('assign-modal').style.display = 'flex';
}

function closeAssignModal() {
    document.getElementById('assign-modal').style.display = 'none';
    currentTeacherId = null;
}

function toggleProgBlock(hdrEl) {
    const block = hdrEl.closest('.prog-block');
    const subjDiv = block.querySelector('.prog-subjects-div');
    const arrow = hdrEl.querySelector('.prog-arrow');
    const open = !subjDiv.style.display || subjDiv.style.display === 'none';
    subjDiv.style.display = open ? 'block' : 'none';
    if (arrow) arrow.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
}

function toggleProgramSubjects(progCb) {
    const progId = progCb.dataset.prog;
    document.querySelectorAll('.assign-cb[data-prog="' + progId + '"]').forEach(cb => {
        cb.checked = progCb.checked;
    });
    progCb.indeterminate = false;
    updateCount();
}

function syncProgHeader(progId) {
    const cbs = Array.from(document.querySelectorAll('.assign-cb[data-prog="' + progId + '"]'));
    const progAllCb = document.querySelector('.prog-all-cb[data-prog="' + progId + '"]');
    if (!progAllCb || !cbs.length) return;
    const n = cbs.filter(c => c.checked).length;
    progAllCb.checked = n === cbs.length;
    progAllCb.indeterminate = n > 0 && n < cbs.length;
}

function filterModalSubjects(q) {
    q = q.toLowerCase().trim();

    if (!q) {
        document.querySelectorAll('.subj-label').forEach(l => l.style.display = 'flex');
        document.querySelectorAll('.prog-block, .type-section').forEach(el => el.style.display = '');
        document.querySelectorAll('.prog-subjects-div').forEach(d => d.style.display = 'block');
        document.querySelectorAll('.prog-arrow').forEach(a => a.style.transform = 'rotate(180deg)');
        return;
    }

    document.querySelectorAll('.subj-label').forEach(label => {
        label.style.display = label.textContent.toLowerCase().includes(q) ? 'flex' : 'none';
    });

    document.querySelectorAll('.prog-block').forEach(block => {
        const any = Array.from(block.querySelectorAll('.subj-label')).some(l => l.style.display !== 'none');
        block.style.display = any ? '' : 'none';
        if (any) {
            const subjDiv = block.querySelector('.prog-subjects-div');
            const arrow = block.querySelector('.prog-arrow');
            if (subjDiv) subjDiv.style.display = 'block';
            if (arrow) arrow.style.transform = 'rotate(180deg)';
        }
    });

    document.querySelectorAll('.type-section').forEach(section => {
        const any = Array.from(section.querySelectorAll('.prog-block')).some(b => b.style.display !== 'none');
        section.style.display = any ? '' : 'none';
    });
}

function updateCount() {
    const n = document.querySelectorAll('.assign-cb:checked').length;
    document.getElementById('modal-selected-count').textContent = n + ' مقرر محدد';
}

document.addEventListener('change', e => {
    if (e.target.classList.contains('assign-cb')) {
        syncProgHeader(e.target.dataset.prog);
        updateCount();
    }
});

function submitAssign() {
    const form = document.getElementById('assign-form');
    form.action = '/admin/teachers/' + currentTeacherId + '/assign-subjects';
    const container = document.getElementById('assign-inputs');
    container.innerHTML = '';
    document.querySelectorAll('.assign-cb:checked').forEach(cb => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'subjects[]';
        inp.value = cb.value;
        container.appendChild(inp);
    });
    form.submit();
}

// ─── Program-level assign modal ──────────────────────────────────────────────
let currentProgTeacherId = null;

function openProgramModal(teacherId, teacherName, assignedProgIds) {
    currentProgTeacherId = teacherId;
    document.getElementById('prog-modal-title').textContent = 'تعيين الدورات — ' + teacherName;

    document.querySelectorAll('.prog-top-cb').forEach(cb => {
        cb.checked = assignedProgIds.includes(parseInt(cb.value));
    });
    updateProgCount();
    document.getElementById('prog-modal').style.display = 'flex';
}

function closeProgramModal() {
    document.getElementById('prog-modal').style.display = 'none';
    currentProgTeacherId = null;
}

function updateProgCount() {
    const n = document.querySelectorAll('.prog-top-cb:checked').length;
    document.getElementById('prog-selected-count').textContent = n + ' دورة محددة';
}

document.addEventListener('change', e => {
    if (e.target.classList.contains('prog-top-cb')) updateProgCount();
});

function submitProgramAssign() {
    const form = document.getElementById('prog-assign-form');
    form.action = '/admin/teachers/' + currentProgTeacherId + '/assign-programs';
    const container = document.getElementById('prog-assign-inputs');
    container.innerHTML = '';
    document.querySelectorAll('.prog-top-cb:checked').forEach(cb => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'program_ids[]';
        inp.value = cb.value;
        container.appendChild(inp);
    });
    form.submit();
}
</script>
@endpush

@endsection
