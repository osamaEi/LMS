@extends('layouts.dashboard')
@section('title', 'لوحة تحكم المدير')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div style="padding:1.5rem;direction:rtl;max-width:1400px;margin:0 auto;">

{{-- ===== HERO ===== --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 40%,#0f2744 100%);border-radius:1.75rem;padding:2rem 2.5rem;margin-bottom:2rem;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-60px;left:-60px;width:280px;height:280px;border-radius:50%;background:rgba(0,113,170,.12);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-50px;right:-50px;width:240px;height:240px;border-radius:50%;background:rgba(99,102,241,.08);pointer-events:none;"></div>
    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:500px;height:500px;border-radius:50%;background:radial-gradient(ellipse,rgba(0,113,170,.06) 0%,transparent 70%);pointer-events:none;"></div>

    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1.5rem;">
        <div style="display:flex;align-items:center;gap:1.25rem;">
            <div style="width:68px;height:68px;border-radius:1.25rem;background:linear-gradient(135deg,#0071AA,#005a88);display:flex;align-items:center;justify-content:center;box-shadow:0 8px 24px rgba(0,113,170,.4);flex-shrink:0;">
                <svg style="width:34px;height:34px;color:#fff" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <div>
                <h1 style="color:#fff;font-size:1.75rem;font-weight:900;margin:0;line-height:1.2;">لوحة التحكم</h1>
                <p style="color:rgba(255,255,255,.65);font-size:.9rem;margin:.3rem 0 0;">مرحباً، <strong style="color:rgba(255,255,255,.9);">{{ auth()->user()->name }}</strong></p>
            </div>
        </div>

        {{-- Live clock + date --}}
        <div style="display:flex;align-items:center;gap:1.25rem;flex-wrap:wrap;">
            <div style="text-align:center;padding:.75rem 1.25rem;background:rgba(255,255,255,.07);border-radius:14px;border:1px solid rgba(255,255,255,.1);">
                <div id="live-time" style="font-size:1.6rem;font-weight:800;color:#fff;line-height:1;font-feature-settings:'tnum';">--:--</div>
                <div style="font-size:.72rem;color:rgba(255,255,255,.55);margin-top:3px;">{{ now()->locale('ar')->isoFormat('dddd') }}</div>
            </div>
            <div style="padding:.75rem 1.25rem;background:rgba(255,255,255,.07);border-radius:14px;border:1px solid rgba(255,255,255,.1);">
                <div style="font-size:.8rem;color:rgba(255,255,255,.9);font-weight:600;">{{ now()->locale('ar')->isoFormat('D MMMM YYYY') }}</div>
                <div style="font-size:.72rem;color:rgba(255,255,255,.55);margin-top:2px;">نظام إدارة التعلم</div>
            </div>
            @if($pendingRatingsCount > 0)
            <a href="{{ route('admin.teacher-ratings.index') }}"
               style="display:inline-flex;align-items:center;gap:7px;padding:.75rem 1.25rem;background:rgba(251,191,36,.15);border:1px solid rgba(251,191,36,.3);border-radius:14px;color:#fbbf24;font-size:.82rem;font-weight:700;text-decoration:none;">
                <svg style="width:15px;height:15px" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                {{ $pendingRatingsCount }} تقييم معلق
            </a>
            @endif
        </div>
    </div>

    {{-- Quick nav chips --}}
    <div style="position:relative;z-index:1;display:flex;flex-wrap:wrap;gap:.6rem;margin-top:1.5rem;">
        @foreach([
            ['label'=>'المعلمون','route'=>'admin.teachers.index','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label'=>'الطلاب','route'=>'admin.students.index','icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['label'=>'المواد','route'=>'admin.subjects.index','icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
            ['label'=>'الفصول','route'=>'admin.terms.index','icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['label'=>'التقارير','route'=>'admin.reports.index','icon'=>'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label'=>'الإعدادات','route'=>'admin.settings','icon'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
        ] as $item)
        <a href="{{ route($item['route']) }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:10px;color:rgba(255,255,255,.85);font-size:.8rem;font-weight:600;text-decoration:none;transition:all .2s;"
           onmouseover="this.style.background='rgba(255,255,255,.2)';this.style.color='#fff'"
           onmouseout="this.style.background='rgba(255,255,255,.1)';this.style.color='rgba(255,255,255,.85)'">
            <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
            </svg>
            {{ $item['label'] }}
        </a>
        @endforeach
    </div>
</div>

{{-- ===== ROW 1: 8 MAIN STAT CARDS ===== --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1.1rem;margin-bottom:1.5rem;">
@php
$mainCards = [
    ['label'=>'المعلمون','value'=>$stats['teachers_count'],'sub'=>null,'grad'=>'#0071AA,#0099dd','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','route'=>'admin.teachers.index'],
    ['label'=>'الطلاب','value'=>$stats['students_count'],'sub'=>null,'grad'=>'#10b981,#34d399','icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z','route'=>'admin.students.index'],
    ['label'=>'المواد الدراسية','value'=>$stats['subjects_count'],'sub'=>$stats['active_subjects'].' نشطة','grad'=>'#f59e0b,#fbbf24','icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253','route'=>'admin.subjects.index'],
    ['label'=>'الفصول الدراسية','value'=>$stats['terms_count'],'sub'=>$stats['active_terms'].' نشطة','grad'=>'#8b5cf6,#a78bfa','icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z','route'=>'admin.terms.index'],
    ['label'=>'البرامج','value'=>$stats['programs_count'],'sub'=>null,'grad'=>'#ec4899,#f472b6','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','route'=>'admin.programs.index'],
    ['label'=>'تسجيل اليوم','value'=>$stats['today_enrollments'],'sub'=>'طالب جديد','grad'=>'#14b8a6,#2dd4bf','icon'=>'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z','route'=>'admin.students.index'],
    ['label'=>'تذاكر مفتوحة','value'=>$nelcStats['open_tickets'],'sub'=>'تحتاج متابعة','grad'=>'#ef4444,#f87171','icon'=>'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z','route'=>'admin.tickets.index'],
    ['label'=>'إجمالي المستخدمين','value'=>$stats['total_users'],'sub'=>'في المنصة','grad'=>'#6366f1,#818cf8','icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z','route'=>'admin.students.index'],
];
@endphp
@foreach($mainCards as $i => $card)
<a href="{{ route($card['route']) }}" style="display:block;text-decoration:none;background:#fff;border-radius:1.1rem;padding:1.25rem;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.05);transition:all .25s;position:relative;overflow:hidden;"
   onmouseover="this.style.transform='translateY(-5px)';this.style.boxShadow='0 12px 28px rgba(0,0,0,.12)'"
   onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 8px rgba(0,0,0,.05)'">
    {{-- top color bar --}}
    <div style="position:absolute;top:0;right:0;left:0;height:3px;background:linear-gradient(90deg,{{ $card['grad'] }});"></div>
    <div style="position:absolute;bottom:-16px;left:-16px;width:70px;height:70px;border-radius:50%;background:linear-gradient(135deg,{{ $card['grad'] }});opacity:.08;"></div>

    <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,{{ $card['grad'] }});display:flex;align-items:center;justify-content:center;margin-bottom:.85rem;box-shadow:0 4px 12px rgba(0,0,0,.15);">
        <svg style="width:22px;height:22px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
        </svg>
    </div>
    <div style="font-size:2rem;font-weight:900;color:#1e293b;line-height:1;">{{ $card['value'] }}</div>
    <div style="font-size:.875rem;color:#64748b;font-weight:600;margin-top:.25rem;">{{ $card['label'] }}</div>
    @if($card['sub'])
    <div style="font-size:.75rem;color:#94a3b8;margin-top:.3rem;display:flex;align-items:center;gap:4px;">
        <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;display:inline-block;"></span>
        {{ $card['sub'] }}
    </div>
    @endif
</a>
@endforeach
</div>

{{-- ===== NELC KPI STRIP ===== --}}
<div style="background:linear-gradient(135deg,#1e3a5f 0%,#0071AA 100%);border-radius:1.1rem;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;">
    <div style="text-align:center;">
        <div style="font-size:1.6rem;font-weight:900;color:#fff;">{{ $nelcStats['satisfaction_rate'] }}%</div>
        <div style="font-size:.78rem;color:rgba(255,255,255,.75);margin-top:2px;">رضا المتدربين</div>
    </div>
    <div style="text-align:center;border-right:1px solid rgba(255,255,255,.15);">
        <div style="font-size:1.6rem;font-weight:900;color:#fff;">{{ $nelcStats['attendance_rate'] }}%</div>
        <div style="font-size:.78rem;color:rgba(255,255,255,.75);margin-top:2px;">معدل الحضور</div>
    </div>
    <div style="text-align:center;border-right:1px solid rgba(255,255,255,.15);">
        <div style="font-size:1.6rem;font-weight:900;color:#fff;">{{ $nelcStats['avg_teacher_rating'] }}</div>
        <div style="font-size:.78rem;color:rgba(255,255,255,.75);margin-top:2px;">تقييم المعلمين / 5</div>
    </div>
    <div style="text-align:center;border-right:1px solid rgba(255,255,255,.15);">
        <div style="font-size:1.6rem;font-weight:900;color:#fff;">{{ $nelcStats['avg_response_time'] }}د</div>
        <div style="font-size:.78rem;color:rgba(255,255,255,.75);margin-top:2px;">متوسط وقت الاستجابة</div>
    </div>
</div>

{{-- ===== CHARTS ROW ===== --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.5rem;">

    {{-- Users Growth --}}
    <div style="background:#fff;border-radius:1.1rem;padding:1.5rem;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.05);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;padding-bottom:1rem;border-bottom:2px solid #f8fafc;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#0071AA,#005a88);display:flex;align-items:center;justify-content:center;">
                    <svg style="width:18px;height:18px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <h3 style="font-size:1rem;font-weight:700;color:#1e293b;margin:0;">نمو المستخدمين</h3>
            </div>
            <span style="font-size:.75rem;color:#94a3b8;">آخر 6 أشهر</span>
        </div>
        <div style="height:240px;"><canvas id="usersGrowthChart"></canvas></div>
    </div>

    {{-- Satisfaction Trend --}}
    <div style="background:#fff;border-radius:1.1rem;padding:1.5rem;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.05);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;padding-bottom:1rem;border-bottom:2px solid #f8fafc;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;">
                    <svg style="width:18px;height:18px;color:#fff" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <h3 style="font-size:1rem;font-weight:700;color:#1e293b;margin:0;">مؤشر الرضا الشهري</h3>
            </div>
            <span style="font-size:.75rem;color:#94a3b8;">مقياس من 5</span>
        </div>
        <div style="height:240px;"><canvas id="satisfactionChart"></canvas></div>
    </div>
</div>

{{-- ===== BOTTOM GRID: 3 columns ===== --}}
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.25rem;margin-bottom:1.5rem;">

    {{-- Recent Teachers --}}
    <div style="background:#fff;border-radius:1.1rem;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.05);overflow:hidden;">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid #f8fafc;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#0071AA,#005a88);display:flex;align-items:center;justify-content:center;">
                    <svg style="width:15px;height:15px;color:#fff" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/></svg>
                </div>
                <h4 style="font-size:.9rem;font-weight:700;color:#1e293b;margin:0;">أحدث المعلمين</h4>
            </div>
            <a href="{{ route('admin.teachers.index') }}" style="font-size:.75rem;color:#0071AA;text-decoration:none;font-weight:600;">عرض الكل</a>
        </div>
        <div style="padding:.5rem;">
            @forelse($recentTeachers ?? [] as $teacher)
            @php $initials = collect(explode(' ',$teacher->name))->take(2)->map(fn($w)=>mb_substr($w,0,1))->join(''); @endphp
            <div style="display:flex;align-items:center;gap:10px;padding:.75rem .75rem;border-radius:10px;transition:background .15s;"
                 onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#0071AA,#005a88);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.85rem;flex-shrink:0;">{{ $initials }}</div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:.85rem;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $teacher->name }}</div>
                    <div style="font-size:.75rem;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $teacher->email }}</div>
                </div>
                <div style="font-size:.7rem;color:#cbd5e1;flex-shrink:0;">{{ $teacher->created_at->diffForHumans() }}</div>
            </div>
            @empty
            <div style="padding:2rem;text-align:center;color:#94a3b8;font-size:.85rem;">لا يوجد معلمون بعد</div>
            @endforelse
        </div>
    </div>

    {{-- Top Rated Teachers --}}
    <div style="background:#fff;border-radius:1.1rem;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.05);overflow:hidden;">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid #f8fafc;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;">
                    <svg style="width:15px;height:15px;color:#fff" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                </div>
                <h4 style="font-size:.9rem;font-weight:700;color:#1e293b;margin:0;">أعلى تقييماً</h4>
            </div>
            @if($pendingRatingsCount > 0)
            <a href="{{ route('admin.teacher-ratings.index') }}" style="font-size:.72rem;color:#ef4444;text-decoration:none;font-weight:600;background:#fef2f2;padding:3px 9px;border-radius:999px;">{{ $pendingRatingsCount }} معلق</a>
            @endif
        </div>
        <div style="padding:.5rem;">
            @forelse($topTeachers as $i => $teacher)
            @php
                $initials2 = collect(explode(' ',$teacher->name))->take(2)->map(fn($w)=>mb_substr($w,0,1))->join('');
                $starColors = ['#f59e0b','#6366f1','#10b981','#0071AA','#ec4899'];
                $starColor = $starColors[$i % count($starColors)];
            @endphp
            <div style="display:flex;align-items:center;gap:10px;padding:.75rem .75rem;border-radius:10px;transition:background .15s;"
                 onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                <div style="width:24px;height:24px;border-radius:6px;background:{{ $starColor }};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:900;font-size:.7rem;flex-shrink:0;">{{ $i+1 }}</div>
                <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,{{ $starColor }},{{ $starColor }}cc);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.8rem;flex-shrink:0;">{{ $initials2 }}</div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:.85rem;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $teacher->name }}</div>
                    <div style="font-size:.72rem;color:#94a3b8;">{{ $teacher->ratings_count }} تقييم</div>
                </div>
                <div style="font-size:.85rem;font-weight:800;color:#f59e0b;flex-shrink:0;">★ {{ number_format($teacher->avg_rating, 1) }}</div>
            </div>
            @empty
            <div style="padding:2rem;text-align:center;color:#94a3b8;font-size:.85rem;">لا توجد تقييمات بعد</div>
            @endforelse
        </div>
    </div>

    {{-- Recent Tickets --}}
    <div style="background:#fff;border-radius:1.1rem;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.05);overflow:hidden;">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid #f8fafc;display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#ef4444,#dc2626);display:flex;align-items:center;justify-content:center;">
                    <svg style="width:15px;height:15px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
                <h4 style="font-size:.9rem;font-weight:700;color:#1e293b;margin:0;">آخر التذاكر</h4>
            </div>
            <a href="{{ route('admin.tickets.index') }}" style="font-size:.75rem;color:#0071AA;text-decoration:none;font-weight:600;">عرض الكل</a>
        </div>
        <div style="padding:.5rem;">
            @php
            $ticketStatusConfig = [
                'open'        => ['label'=>'مفتوح',  'bg'=>'#fef2f2','color'=>'#dc2626'],
                'in_progress' => ['label'=>'جارٍ',   'bg'=>'#fff7ed','color'=>'#ea580c'],
                'resolved'    => ['label'=>'محلول',  'bg'=>'#f0fdf4','color'=>'#16a34a'],
                'closed'      => ['label'=>'مغلق',   'bg'=>'#f8fafc','color'=>'#64748b'],
            ];
            @endphp
            @forelse($recentTickets ?? [] as $ticket)
            @php $ts = $ticketStatusConfig[$ticket->status] ?? ['label'=>$ticket->status,'bg'=>'#f8fafc','color'=>'#64748b']; @endphp
            <div style="display:flex;align-items:flex-start;gap:10px;padding:.75rem .75rem;border-radius:10px;transition:background .15s;"
                 onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                <div style="flex:1;min-width:0;">
                    <div style="font-size:.82rem;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ticket->subject ?? $ticket->title ?? 'تذكرة دعم' }}</div>
                    <div style="font-size:.72rem;color:#94a3b8;">{{ $ticket->user->name ?? '—' }} · {{ $ticket->created_at->diffForHumans() }}</div>
                </div>
                <span style="display:inline-flex;padding:3px 9px;border-radius:999px;font-size:.7rem;font-weight:700;background:{{ $ts['bg'] }};color:{{ $ts['color'] }};flex-shrink:0;white-space:nowrap;">{{ $ts['label'] }}</span>
            </div>
            @empty
            <div style="padding:2rem;text-align:center;color:#94a3b8;font-size:.85rem;">لا توجد تذاكر بعد</div>
            @endforelse
        </div>
    </div>
</div>

{{-- ===== RECENT SUBJECTS ===== --}}
<div style="margin-bottom:1.5rem;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:38px;height:38px;border-radius:11px;background:linear-gradient(135deg,#0071AA,#005a88);display:flex;align-items:center;justify-content:center;">
                <svg style="width:19px;height:19px;color:#fff" fill="currentColor" viewBox="0 0 20 20"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/></svg>
            </div>
            <h2 style="font-size:1.1rem;font-weight:800;color:#1e293b;margin:0;">أحدث المواد الدراسية</h2>
        </div>
        <a href="{{ route('admin.subjects.index') }}" style="font-size:.82rem;color:#0071AA;text-decoration:none;font-weight:700;">عرض الكل ←</a>
    </div>

    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1.1rem;">
        @forelse($recentSubjects ?? [] as $subject)
        @php
            $subjectColors = ['#0071AA','#10b981','#f59e0b','#8b5cf6','#ec4899','#14b8a6','#ef4444','#6366f1'];
            $subjectColor = $subjectColors[crc32($subject->code ?? $subject->name_ar ?? '') % count($subjectColors)];
            $statusConfig = ['active'=>['label'=>'نشطة','bg'=>'#dcfce7','color'=>'#16a34a'],'inactive'=>['label'=>'غير نشطة','bg'=>'#f1f5f9','color'=>'#64748b'],'completed'=>['label'=>'مكتملة','bg'=>'#ede9fe','color'=>'#7c3aed']];
            $sts = $statusConfig[$subject->status] ?? ['label'=>$subject->status,'bg'=>'#f1f5f9','color'=>'#64748b'];
        @endphp
        <a href="{{ route('admin.subjects.show', $subject) }}" style="display:block;text-decoration:none;background:#fff;border-radius:1.1rem;overflow:hidden;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.05);transition:all .25s;"
           onmouseover="this.style.transform='translateY(-5px)';this.style.boxShadow='0 12px 28px rgba(0,0,0,.1)'"
           onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 8px rgba(0,0,0,.05)'">
            {{-- Banner --}}
            <div style="height:90px;background:linear-gradient(135deg,{{ $subjectColor }},{{ $subjectColor }}bb);display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,.12);"></div>
                <svg style="width:36px;height:36px;color:rgba(255,255,255,.9)" fill="currentColor" viewBox="0 0 20 20"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/></svg>
            </div>
            <div style="padding:1rem;">
                <h3 style="font-size:.9rem;font-weight:700;color:#1e293b;margin:0 0 .2rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $subject->name_ar ?? $subject->name }}</h3>
                <p style="font-size:.75rem;color:#94a3b8;margin:0 0 .75rem;">{{ $subject->code }}</p>
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="display:inline-flex;padding:3px 10px;border-radius:999px;font-size:.72rem;font-weight:700;background:{{ $sts['bg'] }};color:{{ $sts['color'] }};">{{ $sts['label'] }}</span>
                    @if($subject->teacher)
                    <span style="font-size:.72rem;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:80px;">{{ $subject->teacher->name }}</span>
                    @endif
                </div>
            </div>
        </a>
        @empty
        <div style="grid-column:1/-1;padding:3rem;text-align:center;background:#fff;border-radius:1.1rem;color:#94a3b8;">
            لا توجد مواد دراسية بعد
        </div>
        @endforelse
    </div>
</div>

{{-- ===== ACTIVITY LOG SECTION ===== --}}
<div style="margin-bottom:1.5rem;">

    {{-- Section header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:38px;height:38px;border-radius:11px;background:linear-gradient(135deg,#6366f1,#4f46e5);display:flex;align-items:center;justify-content:center;">
                <svg style="width:19px;height:19px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <h2 style="font-size:1.1rem;font-weight:800;color:#1e293b;margin:0;">سجل النشاطات</h2>
                <p style="font-size:.75rem;color:#94a3b8;margin:0;">آخر الأنشطة على المنصة في الوقت الفعلي</p>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:.75rem;">
            {{-- Mini stats --}}
            <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:#ede9fe;border-radius:999px;font-size:.78rem;font-weight:700;color:#6d28d9;">
                <svg style="width:12px;height:12px" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                اليوم: {{ $todayLogsCount }}
            </span>
            <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:#f0fdf4;border-radius:999px;font-size:.78rem;font-weight:700;color:#16a34a;">
                <svg style="width:12px;height:12px" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                xAPI: {{ $xapiSentCount }}
            </span>
            <a href="{{ route('admin.activity-logs.index') }}"
               style="display:inline-flex;align-items:center;gap:5px;font-size:.82rem;color:#6366f1;text-decoration:none;font-weight:700;">
                عرض الكل ←
            </a>
        </div>
    </div>

    {{-- Two-column layout: timeline + category breakdown --}}
    <div style="display:grid;grid-template-columns:1fr 280px;gap:1.1rem;align-items:start;">

        {{-- Timeline feed --}}
        <div style="background:#fff;border-radius:1.1rem;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.05);overflow:hidden;">
            <div style="padding:.85rem 1.25rem;background:linear-gradient(135deg,#6366f1,#4f46e5);display:flex;align-items:center;justify-content:space-between;">
                <span style="color:#fff;font-size:.88rem;font-weight:700;">آخر 12 نشاط</span>
                <span style="color:rgba(255,255,255,.75);font-size:.75rem;">{{ $totalLogsCount }} إجمالي</span>
            </div>

            @php
            $logIconMap = [
                'login'             => ['icon'=>'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1', 'bg'=>'#dbeafe','color'=>'#1d4ed8'],
                'logout'            => ['icon'=>'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1', 'bg'=>'#fef9c3','color'=>'#854d0e'],
                'register'          => ['icon'=>'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z', 'bg'=>'#dcfce7','color'=>'#16a34a'],
                'view_session'      => ['icon'=>'M15 10l4.553-2.069A1 1 0 0121 8.869v6.262a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'bg'=>'#ede9fe','color'=>'#7c3aed'],
                'download_file'     => ['icon'=>'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4', 'bg'=>'#fce7f3','color'=>'#9d174d'],
                'start_quiz'        => ['icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'bg'=>'#fef3c7','color'=>'#92400e'],
                'submit_quiz'       => ['icon'=>'M9 12l2 2 4-4M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'bg'=>'#d1fae5','color'=>'#065f46'],
                'join_session'      => ['icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'bg'=>'#cffafe','color'=>'#155e75'],
                'leave_session'     => ['icon'=>'M17 16l4-4m0 0l-4-4m4 4H7', 'bg'=>'#fee2e2','color'=>'#991b1b'],
                'enroll'            => ['icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'bg'=>'#e0e7ff','color'=>'#3730a3'],
                'submit_survey'     => ['icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'bg'=>'#fdf4ff','color'=>'#86198f'],
                'submit_rating'     => ['icon'=>'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', 'bg'=>'#fef9c3','color'=>'#a16207'],
                'create_ticket'     => ['icon'=>'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z', 'bg'=>'#fee2e2','color'=>'#dc2626'],
                'mark_attendance'   => ['icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'bg'=>'#dcfce7','color'=>'#15803d'],
                'page_view'         => ['icon'=>'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z', 'bg'=>'#f1f5f9','color'=>'#64748b'],
            ];
            $defaultIcon = ['icon'=>'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'bg'=>'#f1f5f9','color'=>'#64748b'];

            $roleLabels = ['super_admin'=>'المدير العام','admin'=>'مدير','teacher'=>'معلم','student'=>'طالب'];
            @endphp

            <div style="padding:.5rem;">
                @forelse($recentLogs as $log)
                @php
                    $iconCfg   = $logIconMap[$log->action] ?? $defaultIcon;
                    $initials  = $log->user ? collect(explode(' ', $log->user->name))->take(2)->map(fn($w)=>mb_substr($w,0,1))->join('') : '?';
                    $roleLabel = $roleLabels[$log->user->role ?? ''] ?? ($log->user->role ?? '—');
                    $isNew     = $log->created_at->gt(now()->subMinutes(30));
                @endphp
                <div style="display:flex;align-items:flex-start;gap:12px;padding:.85rem .75rem;border-radius:10px;transition:background .15s;position:relative;"
                     onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">

                    {{-- Action icon --}}
                    <div style="width:36px;height:36px;border-radius:10px;background:{{ $iconCfg['bg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        <svg style="width:17px;height:17px;color:{{ $iconCfg['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconCfg['icon'] }}"/>
                        </svg>
                    </div>

                    {{-- Content --}}
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                            <span style="font-size:.85rem;font-weight:700;color:#1e293b;">{{ $log->getDescription() }}</span>
                            @if($isNew)
                            <span style="display:inline-flex;width:7px;height:7px;border-radius:50%;background:#22c55e;animation:pulse 2s infinite;flex-shrink:0;"></span>
                            @endif
                        </div>
                        <div style="display:flex;align-items:center;gap:.6rem;margin-top:3px;flex-wrap:wrap;">
                            {{-- User avatar + name --}}
                            <div style="display:flex;align-items:center;gap:5px;">
                                <div style="width:20px;height:20px;border-radius:5px;background:linear-gradient(135deg,#6366f1,#4f46e5);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.6rem;font-weight:800;flex-shrink:0;">{{ $initials }}</div>
                                <span style="font-size:.77rem;color:#374151;font-weight:600;">{{ $log->user->name ?? 'مجهول' }}</span>
                            </div>
                            <span style="width:3px;height:3px;border-radius:50%;background:#cbd5e1;display:inline-block;"></span>
                            <span style="font-size:.72rem;color:#94a3b8;background:#f8fafc;padding:1px 7px;border-radius:999px;">{{ $roleLabel }}</span>
                            @if($log->action_category)
                            <span style="width:3px;height:3px;border-radius:50%;background:#cbd5e1;display:inline-block;"></span>
                            <span style="font-size:.72rem;color:#6366f1;font-weight:600;">{{ $log->action_category }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Time + xAPI --}}
                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;">
                        <span style="font-size:.7rem;color:#94a3b8;white-space:nowrap;">{{ $log->created_at->diffForHumans() }}</span>
                        @if($log->xapi_sent)
                        <span style="display:inline-flex;align-items:center;gap:3px;font-size:.65rem;color:#16a34a;background:#dcfce7;padding:1px 6px;border-radius:999px;font-weight:700;">
                            <svg style="width:9px;height:9px" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            xAPI
                        </span>
                        @endif
                    </div>

                    {{-- Vertical connector line --}}
                    @if(!$loop->last)
                    <div style="position:absolute;bottom:0;right:29px;width:1px;height:12px;background:linear-gradient(to bottom,#e2e8f0,transparent);"></div>
                    @endif
                </div>
                @empty
                <div style="padding:3rem;text-align:center;color:#94a3b8;">
                    <svg style="width:40px;height:40px;margin:0 auto .75rem;opacity:.4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p style="font-size:.875rem;">لا توجد سجلات بعد</p>
                </div>
                @endforelse
            </div>

            @if($recentLogs->count() > 0)
            <div style="padding:.85rem 1.25rem;border-top:1px solid #f1f5f9;text-align:center;">
                <a href="{{ route('admin.activity-logs.index') }}"
                   style="font-size:.82rem;color:#6366f1;text-decoration:none;font-weight:700;display:inline-flex;align-items:center;gap:5px;">
                    <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    عرض جميع السجلات ({{ $totalLogsCount }})
                </a>
            </div>
            @endif
        </div>

        {{-- Category breakdown sidebar --}}
        <div style="display:flex;flex-direction:column;gap:1rem;">

            {{-- Category counts --}}
            <div style="background:#fff;border-radius:1.1rem;border:1px solid #f1f5f9;box-shadow:0 2px 8px rgba(0,0,0,.05);overflow:hidden;">
                <div style="padding:.85rem 1.25rem;border-bottom:1px solid #f8fafc;background:linear-gradient(135deg,#6366f1,#4f46e5);">
                    <h4 style="font-size:.875rem;font-weight:700;color:#fff;margin:0;">حسب التصنيف</h4>
                </div>
                <div style="padding:.75rem;">
                    @php
                    use App\Models\ActivityLog as AL;
                    $categories = [
                        ['key'=>AL::CATEGORY_AUTH,          'label'=>'المصادقة',    'bg'=>'#dbeafe','color'=>'#1d4ed8','icon'=>'M11 16l-4-4m0 0l4-4m-4 4h14'],
                        ['key'=>AL::CATEGORY_CONTENT,       'label'=>'المحتوى',     'bg'=>'#ede9fe','color'=>'#7c3aed','icon'=>'M15 10l4.553-2.069A1 1 0 0121 8.869v6.262'],
                        ['key'=>AL::CATEGORY_ASSESSMENT,    'label'=>'التقييم',     'bg'=>'#fef9c3','color'=>'#854d0e','icon'=>'M9 12l2 2 4-4'],
                        ['key'=>AL::CATEGORY_ATTENDANCE,    'label'=>'الحضور',      'bg'=>'#dcfce7','color'=>'#15803d','icon'=>'M9 5H7a2 2 0 00-2 2v12'],
                        ['key'=>AL::CATEGORY_ENROLLMENT,    'label'=>'التسجيل',     'bg'=>'#e0e7ff','color'=>'#3730a3','icon'=>'M12 6.253v13'],
                        ['key'=>AL::CATEGORY_COMMUNICATION, 'label'=>'التواصل',     'bg'=>'#fce7f3','color'=>'#9d174d','icon'=>'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6'],
                        ['key'=>AL::CATEGORY_ADMIN,         'label'=>'الإدارة',     'bg'=>'#fee2e2','color'=>'#991b1b','icon'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0'],
                        ['key'=>AL::CATEGORY_NAVIGATION,    'label'=>'التصفح',      'bg'=>'#f1f5f9','color'=>'#64748b','icon'=>'M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                    ];
                    $catCounts = \App\Models\ActivityLog::select('action_category', \Illuminate\Support\Facades\DB::raw('count(*) as cnt'))
                        ->whereNotNull('action_category')
                        ->groupBy('action_category')
                        ->pluck('cnt', 'action_category');
                    $maxCat = $catCounts->max() ?: 1;
                    @endphp
                    @foreach($categories as $cat)
                    @php $cnt = $catCounts[$cat['key']] ?? 0; @endphp
                    <div style="margin-bottom:.6rem;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:3px;">
                            <div style="display:flex;align-items:center;gap:6px;">
                                <div style="width:22px;height:22px;border-radius:6px;background:{{ $cat['bg'] }};display:flex;align-items:center;justify-content:center;">
                                    <svg style="width:11px;height:11px;color:{{ $cat['color'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cat['icon'] }}"/>
                                    </svg>
                                </div>
                                <span style="font-size:.78rem;color:#374151;font-weight:600;">{{ $cat['label'] }}</span>
                            </div>
                            <span style="font-size:.75rem;font-weight:800;color:{{ $cat['color'] }};">{{ $cnt }}</span>
                        </div>
                        <div style="height:5px;background:#f1f5f9;border-radius:999px;overflow:hidden;">
                            <div style="height:100%;width:{{ $maxCat > 0 ? round(($cnt/$maxCat)*100) : 0 }}%;background:{{ $cat['color'] }};border-radius:999px;transition:width 1s ease;"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Quick link to full logs --}}
            <a href="{{ route('admin.activity-logs.index') }}"
               style="display:flex;align-items:center;justify-content:center;gap:8px;padding:1rem;background:linear-gradient(135deg,#6366f1,#4f46e5);border-radius:1.1rem;color:#fff;font-size:.875rem;font-weight:700;text-decoration:none;box-shadow:0 4px 12px rgba(99,102,241,.3);transition:all .2s;"
               onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 20px rgba(99,102,241,.4)'"
               onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(99,102,241,.3)'">
                <svg style="width:17px;height:17px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                سجل النشاطات الكامل
            </a>
        </div>

    </div>
</div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Live clock
    function updateTime() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2,'0');
        const m = String(now.getMinutes()).padStart(2,'0');
        const el = document.getElementById('live-time');
        if (el) el.textContent = h + ':' + m;
    }
    updateTime();
    setInterval(updateTime, 10000);

    Chart.defaults.font.family = 'Cairo, sans-serif';
    Chart.defaults.color = '#64748b';

    // Users Growth
    const studentsData  = @json($studentsPerMonth ?? []);
    const teachersData  = @json($teachersPerMonth ?? []);
    const allMonths = [...new Set([...Object.keys(studentsData), ...Object.keys(teachersData)])].sort();

    const ctx1 = document.getElementById('usersGrowthChart').getContext('2d');
    const grad1 = ctx1.createLinearGradient(0, 0, 0, 240);
    grad1.addColorStop(0, 'rgba(16,185,129,.25)');
    grad1.addColorStop(1, 'rgba(16,185,129,0)');
    const grad2 = ctx1.createLinearGradient(0, 0, 0, 240);
    grad2.addColorStop(0, 'rgba(0,113,170,.25)');
    grad2.addColorStop(1, 'rgba(0,113,170,0)');

    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: allMonths,
            datasets: [
                { label:'الطلاب', data: allMonths.map(m => studentsData[m]||0), borderColor:'#10b981', backgroundColor: grad1, borderWidth:2.5, tension:.4, fill:true, pointBackgroundColor:'#10b981', pointBorderColor:'#fff', pointBorderWidth:2, pointRadius:4, pointHoverRadius:6 },
                { label:'المعلمون', data: allMonths.map(m => teachersData[m]||0), borderColor:'#0071AA', backgroundColor: grad2, borderWidth:2.5, tension:.4, fill:true, pointBackgroundColor:'#0071AA', pointBorderColor:'#fff', pointBorderWidth:2, pointRadius:4, pointHoverRadius:6 }
            ]
        },
        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'top', align:'end', labels:{ usePointStyle:true, padding:16, font:{size:11,weight:'600'} } }, tooltip:{ backgroundColor:'rgba(15,23,42,.95)', titleFont:{size:13,weight:'600'}, bodyFont:{size:12}, padding:10, cornerRadius:8 } }, scales:{ x:{ grid:{display:false}, ticks:{font:{size:10}} }, y:{ beginAtZero:true, grid:{color:'rgba(0,0,0,.04)'}, ticks:{font:{size:10},stepSize:1} } }, interaction:{ intersect:false, mode:'index' } }
    });

    // Satisfaction Trend
    const satisfactionData = @json($satisfactionTrend ?? []);
    const satisfactionMonths = Object.keys(satisfactionData);
    const ctx2 = document.getElementById('satisfactionChart').getContext('2d');
    const grad3 = ctx2.createLinearGradient(0, 0, 0, 240);
    grad3.addColorStop(0, 'rgba(245,158,11,.25)');
    grad3.addColorStop(1, 'rgba(245,158,11,0)');

    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: satisfactionMonths.length ? satisfactionMonths : allMonths,
            datasets: [{
                label: 'معدل الرضا',
                data: satisfactionMonths.length ? Object.values(satisfactionData) : allMonths.map(()=>0),
                borderColor: '#f59e0b',
                backgroundColor: grad3,
                borderWidth: 2.5,
                tension: .4,
                fill: true,
                pointBackgroundColor: '#f59e0b',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false }, tooltip:{ backgroundColor:'rgba(15,23,42,.95)', titleFont:{size:13,weight:'600'}, bodyFont:{size:12}, padding:10, cornerRadius:8 } }, scales:{ x:{ grid:{display:false}, ticks:{font:{size:10}} }, y:{ beginAtZero:true, max:5, grid:{color:'rgba(0,0,0,.04)'}, ticks:{font:{size:10},stepSize:1} } } }
    });
});
</script>
@endpush
@endsection
