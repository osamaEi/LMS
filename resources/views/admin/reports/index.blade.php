@extends('layouts.dashboard')

@section('title', 'التقارير والإحصاءات')

@section('content')
<div style="direction:rtl; font-family:'Segoe UI',sans-serif;">

    {{-- Hero Section --}}
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 40%,#0f172a 100%);border-radius:20px;padding:36px 32px;margin-bottom:28px;position:relative;overflow:hidden;">
        {{-- Background decorations --}}
        <div style="position:absolute;top:-60px;left:-60px;width:240px;height:240px;background:linear-gradient(135deg,#6366f1,#8b5cf6);opacity:0.12;border-radius:50%;"></div>
        <div style="position:absolute;bottom:-80px;right:10%;width:300px;height:300px;background:linear-gradient(135deg,#0ea5e9,#06b6d4);opacity:0.08;border-radius:50%;"></div>
        <div style="position:absolute;top:20px;left:40%;width:120px;height:120px;background:#f59e0b;opacity:0.06;border-radius:50%;"></div>

        <div style="position:relative;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:20px;">
            <div>
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:10px;">
                    <div style="width:52px;height:52px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(99,102,241,0.4);">
                        <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 style="color:white;font-size:26px;font-weight:700;margin:0;letter-spacing:-0.5px;">التقارير والتحليلات</h1>
                        <p style="color:rgba(255,255,255,0.55);font-size:14px;margin:3px 0 0;">لوحة متابعة شاملة · التوافق مع متطلبات النظام</p>
                    </div>
                </div>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="{{ route('admin.activity-logs.index') }}"
                   style="display:inline-flex;align-items:center;gap:7px;padding:10px 18px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:10px;color:rgba(255,255,255,0.85);font-size:13px;font-weight:500;text-decoration:none;backdrop-filter:blur(8px);transition:all .2s;"
                   onmouseover="this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    سجلات النشاط
                </a>
                <a href="{{ route('admin.xapi.index') }}"
                   style="display:inline-flex;align-items:center;gap:7px;padding:10px 18px;background:rgba(99,102,241,0.25);border:1px solid rgba(99,102,241,0.4);border-radius:10px;color:#a5b4fc;font-size:13px;font-weight:500;text-decoration:none;backdrop-filter:blur(8px);transition:all .2s;"
                   onmouseover="this.style.background='rgba(99,102,241,0.4)'" onmouseout="this.style.background='rgba(99,102,241,0.25)'">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                    </svg>
                    xAPI Dashboard
                </a>
            </div>
        </div>

        {{-- Stats Row --}}
        <div style="position:relative;z-index:1;display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-top:28px;">
            @php
                $heroStats = [
                    ['label'=>'الطلاب','value'=>number_format($stats['total_students']),'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','grad'=>'#6366f1,#8b5cf6'],
                    ['label'=>'البرامج','value'=>number_format($stats['total_programs']),'icon'=>'M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z','grad'=>'#059669,#10b981'],
                    ['label'=>'الجلسات','value'=>number_format($stats['total_sessions']),'icon'=>'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z','grad'=>'#d97706,#f59e0b'],
                    ['label'=>'الأنشطة','value'=>number_format($stats['total_activities']),'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','grad'=>'#0891b2,#06b6d4'],
                    ['label'=>'التسجيلات','value'=>number_format($stats['active_enrollments']),'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','grad'=>'#dc2626,#ef4444'],
                    ['label'=>'xAPI معلق','value'=>number_format($stats['pending_xapi']),'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','grad'=>'#7c3aed,#a78bfa'],
                ];
            @endphp
            @foreach($heroStats as $hs)
            <div style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);border-radius:14px;padding:14px 12px;text-align:center;backdrop-filter:blur(8px);">
                <div style="width:34px;height:34px;background:linear-gradient(135deg,{{ $hs['grad'] }});border-radius:9px;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;">
                    <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $hs['icon'] }}"/>
                    </svg>
                </div>
                <div style="color:white;font-size:20px;font-weight:700;line-height:1;">{{ $hs['value'] }}</div>
                <div style="color:rgba(255,255,255,0.55);font-size:11px;margin-top:3px;">{{ $hs['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Reports Grid Title --}}
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
        <div style="width:4px;height:24px;background:linear-gradient(180deg,#6366f1,#8b5cf6);border-radius:4px;"></div>
        <h2 style="font-size:18px;font-weight:700;color:#111827;margin:0;">التقارير المتاحة</h2>
        <div style="flex:1;height:1px;background:linear-gradient(90deg,#e5e7eb,transparent);margin-right:8px;"></div>
    </div>

    {{-- Report Cards Grid --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-bottom:24px;">

        @php
        $reportCards = [
            [
                'title'  => 'تقرير الامتثال NELC',
                'desc'   => 'تقرير شامل لمتطلبات اعتماد NELC يشمل تتبع النشاط، xAPI، إمكانية الوصول، والمقاييس المطلوبة.',
                'grad'   => 'linear-gradient(135deg,#4f46e5,#7c3aed)',
                'light'  => '#f0f0ff',
                'border' => '#c4b5fd',
                'text'   => '#4f46e5',
                'icon'   => 'M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z',
                'tags'   => [['label'=>'أمان','bg'=>'#ede9fe','text'=>'#7c3aed'],['label'=>'تحليلات','bg'=>'#dcfce7','text'=>'#15803d'],['label'=>'وصول','bg'=>'#fef3c7','text'=>'#92400e']],
                'viewRoute' => ['admin.reports.nelc-compliance', null],
                'exportRoute' => ['admin.reports.export', ['type'=>'nelc','format'=>'pdf']],
                'exportLabel' => 'تصدير PDF',
                'exportBg' => '#ede9fe',
                'exportText' => '#4f46e5',
            ],
            [
                'title'  => 'تقرير تقدم الطلاب',
                'desc'   => 'تتبع التسجيل، الحضور، درجات الاختبارات، والتقدم العام للطلاب عبر جميع البرامج والمواد.',
                'grad'   => 'linear-gradient(135deg,#059669,#10b981)',
                'light'  => '#f0fdf4',
                'border' => '#86efac',
                'text'   => '#059669',
                'icon'   => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                'tags'   => [['label'=>'متابعة التقدم','bg'=>'#dcfce7','text'=>'#15803d'],['label'=>'الأداء','bg'=>'#f0fdf4','text'=>'#059669']],
                'viewRoute' => ['admin.reports.student-progress', null],
                'exportRoute' => ['admin.reports.export', ['type'=>'student-progress','format'=>'csv']],
                'exportLabel' => 'تصدير CSV',
                'exportBg' => '#dcfce7',
                'exportText' => '#15803d',
            ],
            [
                'title'  => 'تقرير الحضور',
                'desc'   => 'سجلات تفصيلية للحضور في الجلسات المباشرة ومشاهدات الفيديو المسجلة عبر جميع البرامج.',
                'grad'   => 'linear-gradient(135deg,#d97706,#f59e0b)',
                'light'  => '#fffbeb',
                'border' => '#fcd34d',
                'text'   => '#d97706',
                'icon'   => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                'tags'   => [['label'=>'الحضور','bg'=>'#fef3c7','text'=>'#92400e'],['label'=>'مباشر ومسجل','bg'=>'#fee2e2','text'=>'#dc2626']],
                'viewRoute' => ['admin.reports.attendance', null],
                'exportRoute' => ['admin.reports.export', ['type'=>'attendance','format'=>'csv']],
                'exportLabel' => 'تصدير CSV',
                'exportBg' => '#fef3c7',
                'exportText' => '#92400e',
            ],
            [
                'title'  => 'تقرير الدرجات والتقييم',
                'desc'   => 'نظرة شاملة على درجات الطلاب، نتائج الاختبارات، أداء التقييمات، وتحليلات التقييم.',
                'grad'   => 'linear-gradient(135deg,#0891b2,#0284c7)',
                'light'  => '#f0f9ff',
                'border' => '#7dd3fc',
                'text'   => '#0284c7',
                'icon'   => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                'tags'   => [['label'=>'التقييمات','bg'=>'#e0f2fe','text'=>'#0369a1'],['label'=>'الدرجات','bg'=>'#dcfce7','text'=>'#15803d']],
                'viewRoute' => ['admin.reports.grades', null],
                'exportRoute' => ['admin.reports.export', ['type'=>'grades','format'=>'csv']],
                'exportLabel' => 'تصدير CSV',
                'exportBg' => '#e0f2fe',
                'exportText' => '#0369a1',
            ],
            [
                'title'  => 'أداء المعلمين',
                'desc'   => 'تقييم تقييمات المعلمين، ملاحظات الطلاب، مقاييس فاعلية التدريس، وتحليلات الأداء.',
                'grad'   => 'linear-gradient(135deg,#dc2626,#ec4899)',
                'light'  => '#fff1f2',
                'border' => '#fca5a5',
                'text'   => '#dc2626',
                'icon'   => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
                'tags'   => [['label'=>'التقييمات','bg'=>'#fee2e2','text'=>'#dc2626'],['label'=>'التغذية الراجعة','bg'=>'#fef3c7','text'=>'#92400e']],
                'viewRoute' => ['admin.reports.teacher-performance', null],
                'exportRoute' => ['admin.reports.export', ['type'=>'teacher-performance','format'=>'csv']],
                'exportLabel' => 'تصدير CSV',
                'exportBg' => '#fee2e2',
                'exportText' => '#dc2626',
            ],
            [
                'title'  => 'سجلات النشاط',
                'desc'   => 'تتبع شامل للأنشطة يشمل عمليات تسجيل الدخول، الوصول للمحتوى، التقييمات، وإرسال xAPI.',
                'grad'   => 'linear-gradient(135deg,#7c3aed,#4f46e5)',
                'light'  => '#f5f3ff',
                'border' => '#c4b5fd',
                'text'   => '#7c3aed',
                'icon'   => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                'tags'   => [['label'=>'التتبع','bg'=>'#ede9fe','text'=>'#7c3aed'],['label'=>'سجل التدقيق','bg'=>'#dbeafe','text'=>'#1d4ed8']],
                'viewRoute' => ['admin.activity-logs.index', null],
                'exportRoute' => ['admin.activity-logs.stats', null],
                'exportLabel' => 'عرض التحليلات',
                'exportBg' => '#ede9fe',
                'exportText' => '#7c3aed',
            ],
        ];
        @endphp

        @foreach($reportCards as $card)
        <div style="background:white;border-radius:16px;border:1px solid {{ $card['border'] }};overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.06);transition:all .2s;position:relative;"
             onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.12)'"
             onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 10px rgba(0,0,0,0.06)'">
            {{-- Colored top strip --}}
            <div style="height:4px;background:{{ $card['grad'] }};"></div>

            <div style="padding:22px;">
                {{-- Icon + Title --}}
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                    <div style="width:46px;height:46px;background:{{ $card['grad'] }};border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.15);flex-shrink:0;">
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/>
                        </svg>
                    </div>
                    <h3 style="font-size:15px;font-weight:700;color:#111827;margin:0;line-height:1.3;">{{ $card['title'] }}</h3>
                </div>

                {{-- Description --}}
                <p style="font-size:13px;color:#6b7280;line-height:1.65;margin:0 0 14px;min-height:52px;">{{ $card['desc'] }}</p>

                {{-- Tags --}}
                <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:16px;">
                    @foreach($card['tags'] as $tag)
                    <span style="display:inline-block;background:{{ $tag['bg'] }};color:{{ $tag['text'] }};padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;">{{ $tag['label'] }}</span>
                    @endforeach
                </div>

                {{-- Action Buttons --}}
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <a href="{{ route($card['viewRoute'][0], $card['viewRoute'][1]) }}"
                       style="display:flex;align-items:center;justify-content:center;gap:7px;padding:10px;background:{{ $card['grad'] }};color:white;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;box-shadow:0 2px 8px rgba(0,0,0,0.15);">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        عرض التقرير
                    </a>
                    <a href="{{ route($card['exportRoute'][0], $card['exportRoute'][1]) }}"
                       style="display:flex;align-items:center;justify-content:center;gap:7px;padding:9px;background:{{ $card['exportBg'] }};color:{{ $card['exportText'] }};border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        {{ $card['exportLabel'] }}
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Quick Export Section --}}
    <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;margin-bottom:20px;box-shadow:0 1px 6px rgba(0,0,0,0.06);">
        <div style="padding:18px 24px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:10px;background:linear-gradient(90deg,#fafafa,#ffffff);">
            <div style="width:36px;height:36px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
            </div>
            <div>
                <h2 style="font-size:15px;font-weight:700;color:#111827;margin:0;">تصدير سريع</h2>
                <p style="font-size:12px;color:#9ca3af;margin:2px 0 0;">إنشاء تقارير شاملة بتنسيقات متعددة</p>
            </div>
        </div>
        <div style="padding:20px 24px;">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;">
                <a href="{{ route('admin.reports.export', ['type' => 'all', 'format' => 'pdf']) }}"
                   style="display:flex;align-items:center;gap:12px;padding:16px;background:#fff1f2;border:1.5px solid #fca5a5;border-radius:12px;text-decoration:none;transition:all .15s;"
                   onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fff1f2'">
                    <div style="width:42px;height:42px;background:linear-gradient(135deg,#dc2626,#ef4444);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 3px 10px rgba(220,38,38,0.3);">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:700;color:#dc2626;">تصدير PDF</div>
                        <div style="font-size:12px;color:#9ca3af;margin-top:1px;">جميع التقارير</div>
                    </div>
                </a>
                <a href="{{ route('admin.reports.export', ['type' => 'all', 'format' => 'csv']) }}"
                   style="display:flex;align-items:center;gap:12px;padding:16px;background:#f0fdf4;border:1.5px solid #86efac;border-radius:12px;text-decoration:none;transition:all .15s;"
                   onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                    <div style="width:42px;height:42px;background:linear-gradient(135deg,#059669,#10b981);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 3px 10px rgba(5,150,105,0.3);">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:700;color:#059669;">تصدير CSV</div>
                        <div style="font-size:12px;color:#9ca3af;margin-top:1px;">جميع التقارير</div>
                    </div>
                </a>
                <a href="{{ route('admin.reports.export', ['type' => 'all', 'format' => 'json']) }}"
                   style="display:flex;align-items:center;gap:12px;padding:16px;background:#f0f9ff;border:1.5px solid #7dd3fc;border-radius:12px;text-decoration:none;transition:all .15s;"
                   onmouseover="this.style.background='#e0f2fe'" onmouseout="this.style.background='#f0f9ff'">
                    <div style="width:42px;height:42px;background:linear-gradient(135deg,#0891b2,#0284c7);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 3px 10px rgba(8,145,178,0.3);">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size:14px;font-weight:700;color:#0284c7;">تصدير JSON</div>
                        <div style="font-size:12px;color:#9ca3af;margin-top:1px;">جميع التقارير</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- NELC Info Banner --}}
    <div style="background:linear-gradient(135deg,#1e1b4b,#312e81);border-radius:16px;padding:24px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-20px;left:-20px;width:150px;height:150px;background:rgba(99,102,241,0.15);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-30px;right:10%;width:180px;height:180px;background:rgba(139,92,246,0.1);border-radius:50%;"></div>
        <div style="position:relative;z-index:1;display:flex;align-items:flex-start;gap:16px;">
            <div style="width:46px;height:46px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 16px rgba(99,102,241,0.4);">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div style="flex:1;">
                <h4 style="color:white;font-size:16px;font-weight:700;margin:0 0 8px;">حول تقارير NELC والامتثال</h4>
                <p style="color:rgba(255,255,255,0.65);font-size:13px;line-height:1.7;margin:0 0 14px;">
                    هذه التقارير مصممة لتلبية معايير امتثال <strong style="color:#a5b4fc;">NELC (المركز الوطني للتعليم الإلكتروني)</strong> للاعتماد الرسمي في المملكة العربية السعودية. تتضمن جميع البيانات دعمًا ثنائي اللغة (عربي/إنجليزي)، تتبع النشاط الشامل، تكامل xAPI، ومعايير إمكانية الوصول المطلوبة لتكامل منصة FutureX.
                </p>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach(['✓ توافق xAPI','✓ تتبع النشاط','✓ معايير الوصول','✓ دعم ثنائي اللغة'] as $badge)
                    <span style="display:inline-block;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);color:rgba(255,255,255,0.85);padding:4px 12px;border-radius:20px;font-size:12px;font-weight:500;">{{ $badge }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
