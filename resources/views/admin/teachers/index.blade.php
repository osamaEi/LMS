@extends('layouts.dashboard')

@section('title', 'إدارة الأساتذة')

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
                        <h1 style="font-size:1.7rem;font-weight:900;color:#fff;margin:0;line-height:1.2">إدارة الأساتذة</h1>
                        <p style="font-size:0.875rem;color:rgba(255,255,255,0.65);margin:5px 0 0">عرض وإدارة جميع الأساتذة في المنصة</p>
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
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.65);margin-top:4px">إجمالي الأساتذة</div>
                </div>
                <div class="hero-stat">
                    <div style="font-size:1.8rem;font-weight:900;color:#a5b4fc;line-height:1">{{ $stats['with_subjects'] }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.65);margin-top:4px">لديهم مواد</div>
                </div>
                <div class="hero-stat">
                    <div style="font-size:1.8rem;font-weight:900;color:#34d399;line-height:1">{{ $stats['this_month'] }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.65);margin-top:4px">انضموا هذا الشهر</div>
                </div>
                <div class="hero-stat">
                    <div style="font-size:1.8rem;font-weight:900;color:#fbbf24;line-height:1">{{ $stats['total'] - $stats['with_subjects'] }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.65);margin-top:4px">بدون مواد</div>
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
                        {{-- Subjects badge --}}
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:0.72rem;font-weight:700;background:{{ $color }}18;color:{{ $color }}">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                            </svg>
                            {{ $teacher->subjects_count }} {{ $teacher->subjects_count === 1 ? 'مادة' : 'مواد' }}
                        </span>
                    </div>
                </div>

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

                {{-- Actions --}}
                <div class="card-actions" style="display:flex;gap:8px">
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
                    <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST"
                          onsubmit="return confirm('هل أنت متأكد من حذف الأستاذ {{ addslashes($teacher->name) }}؟ لا يمكن التراجع عن هذا الإجراء.')">
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
            @if($search) جرّب كلمات بحث مختلفة @else ابدأ بإضافة أول أستاذ في المنصة @endif
        </p>
        @if($search)
        <a href="{{ route('admin.teachers.index') }}"
           style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:#6366f1;color:#fff;font-weight:700;border-radius:12px;text-decoration:none;font-size:0.875rem">
            عرض جميع الأساتذة
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
@endsection
