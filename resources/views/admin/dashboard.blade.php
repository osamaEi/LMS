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
                <p style="color:rgba(255,255,255,.65);font-size:.9rem;margin:.3rem 0 0;">مرحباً بك في أكاديمية الارتقاء، <strong style="color:rgba(255,255,255,.9);">{{ auth()->user()->name }}</strong></p>
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
            ['label'=>'المدربون','route'=>'admin.teachers.index','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label'=>' المتدربون ','route'=>'admin.students.index','icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['label'=>'المقررات ','route'=>'admin.subjects.index','icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
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
    ['label'=>'المدربون','value'=>$stats['teachers_count'],'sub'=>null,'grad'=>'#0071AA,#0099dd','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','route'=>'admin.teachers.index'],
    ['label'=>' المتدربون ','value'=>$stats['students_count'],'sub'=>null,'grad'=>'#10b981,#34d399','icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z','route'=>'admin.students.index'],
    ['label'=>'المقررات  التدريبية ','value'=>$stats['subjects_count'],'sub'=>$stats['active_subjects'].' نشطة','grad'=>'#f59e0b,#fbbf24','icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253','route'=>'admin.subjects.index'],
    ['label'=>'الفصول التدريبية ','value'=>$stats['terms_count'],'sub'=>$stats['active_terms'].' نشطة','grad'=>'#8b5cf6,#a78bfa','icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z','route'=>'admin.terms.index'],
    ['label'=>'البرامج','value'=>$stats['programs_count'],'sub'=>null,'grad'=>'#ec4899,#f472b6','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','route'=>'admin.programs.index'],
    ['label'=>'تسجيل اليوم','value'=>$stats['today_enrollments'],'sub'=>' متدرب جديد','grad'=>'#14b8a6,#2dd4bf','icon'=>'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z','route'=>'admin.students.index'],
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
                { label:' المتدربون ', data: allMonths.map(m => studentsData[m]||0), borderColor:'#10b981', backgroundColor: grad1, borderWidth:2.5, tension:.4, fill:true, pointBackgroundColor:'#10b981', pointBorderColor:'#fff', pointBorderWidth:2, pointRadius:4, pointHoverRadius:6 },
                { label:'المدربون', data: allMonths.map(m => teachersData[m]||0), borderColor:'#0071AA', backgroundColor: grad2, borderWidth:2.5, tension:.4, fill:true, pointBackgroundColor:'#0071AA', pointBorderColor:'#fff', pointBorderWidth:2, pointRadius:4, pointHoverRadius:6 }
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
