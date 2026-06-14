@extends('layouts.dashboard')
@section('title', 'الحضور والغياب')

@php
    $allSubjectSessions = $subjects->flatMap(fn($s) => $s->sessions);
    $allProgramSessions = $programs->flatMap(fn($p) => $p->sessions);

    $totalSessions  = $allSubjectSessions->count() + $allProgramSessions->count();
    $totalAttended  = $allSubjectSessions->sum('attended_count') + $allProgramSessions->sum('attended_count');
    $totalEnrolled  = $subjects->sum('enrollments_count') + $programs->sum('enrolled_count');

    $subjectsPast   = $allSubjectSessions->count();
    $programsPast   = $allProgramSessions->count();
@endphp

@section('content')
<div style="direction:rtl;max-width:1100px;margin:0 auto;">

{{-- Hero --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 55%,#0071AA 100%);border-radius:20px;padding:28px 32px;margin-bottom:24px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-50px;left:-50px;width:200px;height:200px;background:rgba(255,255,255,.04);border-radius:50%;pointer-events:none;"></div>
    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:50px;height:50px;background:rgba(255,255,255,.15);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="24" height="24" fill="white" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
            </div>
            <div>
                <h1 style="color:white;font-size:20px;font-weight:800;margin:0;">سجلات الحضور والغياب</h1>
                <p style="color:rgba(255,255,255,.6);font-size:12px;margin:4px 0 0;">جميع المواد والدورات المسندة إليك</p>
            </div>
        </div>
        {{-- Overall stats --}}
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            @foreach([
                [$totalSessions,  'جلسة منعقدة',    'rgba(255,255,255,.75)'],
                [$totalAttended,  'حضور مسجّل',     '#86efac'],
                [$totalEnrolled,  'إجمالي المسجلين','#fde68a'],
            ] as [$v,$l,$c])
            <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:8px 18px;text-align:center;min-width:72px;">
                <div style="font-size:20px;font-weight:800;color:{{ $c }};line-height:1.1;">{{ $v }}</div>
                <div style="font-size:11px;color:rgba(255,255,255,.55);margin-top:2px;">{{ $l }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Tabs --}}
<div style="display:flex;gap:0;border:1.5px solid #e5e7eb;border-radius:12px;overflow:hidden;margin-bottom:20px;background:white;">
    <button id="tab-subjects" onclick="switchTab('subjects')"
            style="flex:1;padding:12px;font-size:13px;font-weight:700;border:none;cursor:pointer;background:linear-gradient(135deg,#0071AA,#005a88);color:white;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:7px;">
        <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zm-7.5 9.49L12 17l7.5-4.51V9.09L12 13.53 4.5 9.09v3.4z"/></svg>
        مقررات الدبلومات
        @if($subjects->isNotEmpty())
        <span style="background:rgba(255,255,255,.25);border-radius:20px;padding:1px 8px;font-size:11px;">{{ $subjects->count() }}</span>
        @endif
    </button>
    <button id="tab-programs" onclick="switchTab('programs')"
            style="flex:1;padding:12px;font-size:13px;font-weight:700;border:none;cursor:pointer;background:transparent;color:#6b7280;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:7px;">
        <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        الدورات والبرامج
        @if($programs->isNotEmpty())
        <span style="background:#e5e7eb;border-radius:20px;padding:1px 8px;font-size:11px;color:#374151;">{{ $programs->count() }}</span>
        @endif
    </button>
</div>

{{-- ══ SUBJECTS TAB ══ --}}
<div id="pane-subjects">
    @if($subjects->isEmpty())
    <div style="background:white;border:1.5px solid #f1f5f9;border-radius:16px;padding:48px;text-align:center;">
        <div style="width:56px;height:56px;background:#f1f5f9;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <svg width="24" height="24" fill="none" stroke="#9ca3af" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <p style="font-size:.9rem;font-weight:700;color:#374151;margin:0 0 4px;">لا توجد مقررات دبلومات مسندة إليك</p>
        <p style="font-size:.8rem;color:#9ca3af;margin:0;">تواصل مع الإدارة لإسناد مقررات</p>
    </div>
    @else
    <div style="display:flex;flex-direction:column;gap:16px;">
        @foreach($subjects as $subject)
        @php
            $pastSes       = $subject->sessions;
            $totalEnrolled = $subject->enrollments_count;
            $totalAtt      = $pastSes->sum('attended_count');
            $overallRate   = ($totalSes = $pastSes->count()) && $totalEnrolled
                             ? round($totalAtt / ($totalSes * $totalEnrolled) * 100) : 0;
            $rateColor = $overallRate >= 75 ? '#16a34a' : ($overallRate >= 50 ? '#d97706' : '#dc2626');
        @endphp
        <div style="background:white;border:1.5px solid #f1f5f9;border-radius:16px;overflow:hidden;">
            {{-- Subject header --}}
            <div style="padding:14px 20px;background:linear-gradient(135deg,rgba(0,113,170,.06),rgba(0,113,170,.02));border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;background:linear-gradient(135deg,#0071AA,#005a88);border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-size:16px;font-weight:800;flex-shrink:0;">
                        {{ mb_substr($subject->name_ar ?? 'م', 0, 1) }}
                    </div>
                    <div>
                        @php $className = $subject->programClass->name ?? $subject->term->programClass->name ?? null; @endphp
                        <h3 style="font-size:.95rem;font-weight:800;color:#111827;margin:0;">
                            {{ $subject->name_ar }}
                            @if($className)<span style="font-size:.72rem;font-weight:700;color:#0071AA;background:#e0f2fe;padding:2px 8px;border-radius:20px;margin-right:4px;">{{ $className }}</span>@endif
                        </h3>
                        <p style="font-size:.75rem;color:#6b7280;margin:2px 0 0;">
                            {{ $totalEnrolled }} طالب مسجّل &nbsp;·&nbsp; {{ $pastSes->count() }} جلسة منعقدة
                            &nbsp;·&nbsp; معدل حضور إجمالي:
                            <strong style="color:{{ $rateColor }}">{{ $overallRate }}%</strong>
                        </p>
                    </div>
                </div>
                <a href="{{ route('teacher.my-subjects.show', $subject->id) }}"
                   style="font-size:.75rem;font-weight:700;color:#0071AA;text-decoration:none;">عرض المقرر ←</a>
            </div>

            @if($pastSes->isEmpty())
            <div style="padding:32px;text-align:center;font-size:.85rem;color:#9ca3af;">لا توجد جلسات منعقدة بعد</div>
            @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:.85rem;">
                    <thead>
                        <tr style="background:#f9fafb;">
                            <th style="padding:10px 16px;text-align:right;font-size:.72rem;font-weight:700;color:#6b7280;white-space:nowrap;">#</th>
                            <th style="padding:10px 16px;text-align:right;font-size:.72rem;font-weight:700;color:#6b7280;white-space:nowrap;">العنوان</th>
                            <th style="padding:10px 16px;text-align:right;font-size:.72rem;font-weight:700;color:#6b7280;white-space:nowrap;">التاريخ</th>
                            <th style="padding:10px 16px;text-align:right;font-size:.72rem;font-weight:700;color:#6b7280;white-space:nowrap;">الحضور</th>
                            <th style="padding:10px 16px;text-align:right;font-size:.72rem;font-weight:700;color:#6b7280;white-space:nowrap;">النسبة</th>
                            <th style="padding:10px 16px;text-align:right;font-size:.72rem;font-weight:700;color:#6b7280;white-space:nowrap;">إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pastSes as $ses)
                        @php
                            $att      = $ses->attended_count;
                            $absent   = max(0, $totalEnrolled - $att);
                            $rate     = $totalEnrolled > 0 ? round($att / $totalEnrolled * 100) : 0;
                            $rc       = $rate >= 75 ? '#16a34a' : ($rate >= 50 ? '#d97706' : '#dc2626');
                            $rb       = $rate >= 75 ? '#dcfce7' : ($rate >= 50 ? '#fef3c7' : '#fee2e2');
                        @endphp
                        <tr style="border-top:1px solid #f9fafb;">
                            <td style="padding:12px 16px;color:#9ca3af;font-weight:700;">{{ $ses->session_number }}</td>
                            <td style="padding:12px 16px;">
                                <p style="font-weight:700;color:#111827;margin:0;">{{ $ses->title ?: 'جلسة '.$ses->session_number }}</p>
                                <p style="font-size:.72rem;color:#9ca3af;margin:2px 0 0;">
                                    {{ $ses->type === 'live_zoom' ? 'Zoom مباشر' : 'مسجّلة' }}
                                </p>
                            </td>
                            <td style="padding:12px 16px;color:#374151;white-space:nowrap;">
                                {{ $ses->scheduled_at ? \Carbon\Carbon::parse($ses->scheduled_at)->format('Y/m/d H:i') : '—' }}
                            </td>
                            <td style="padding:12px 16px;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="width:80px;height:6px;border-radius:99px;background:#f1f5f9;overflow:hidden;">
                                        <div style="height:100%;border-radius:99px;width:{{ $rate }}%;background:{{ $rc }};"></div>
                                    </div>
                                    <span style="font-size:.8rem;font-weight:700;color:#111827;">{{ $att }} / {{ $totalEnrolled }}</span>
                                    <span style="font-size:.72rem;color:#9ca3af;">غائب: {{ $absent }}</span>
                                </div>
                            </td>
                            <td style="padding:12px 16px;">
                                <span style="background:{{ $rb }};color:{{ $rc }};font-size:.75rem;font-weight:700;padding:3px 10px;border-radius:20px;">{{ $rate }}%</span>
                            </td>
                            <td style="padding:12px 16px;">
                                <a href="{{ route('teacher.my-subjects.sessions.attendance', [$subject->id, $ses->id]) }}"
                                   style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;background:linear-gradient(135deg,#0071AA,#005a88);color:white;border-radius:8px;font-size:.75rem;font-weight:700;text-decoration:none;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    عرض / تسجيل
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- ══ PROGRAMS TAB ══ --}}
<div id="pane-programs" style="display:none;">
    @if($programs->isEmpty())
    <div style="background:white;border:1.5px solid #f1f5f9;border-radius:16px;padding:48px;text-align:center;">
        <div style="width:56px;height:56px;background:#f1f5f9;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <svg width="24" height="24" fill="none" stroke="#9ca3af" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        </div>
        <p style="font-size:.9rem;font-weight:700;color:#374151;margin:0 0 4px;">لا توجد دورات مسندة إليك</p>
        <p style="font-size:.8rem;color:#9ca3af;margin:0;">تواصل مع الإدارة لإسناد دورات</p>
    </div>
    @else
    @php
        $typeLabels = ['training' => 'تدريبي', 'english' => 'إنجليزي', 'course' => 'دورة'];
        $typeColors = [
            'training' => ['from' => '#0071AA', 'to' => '#005a88'],
            'english'  => ['from' => '#d97706', 'to' => '#b45309'],
            'course'   => ['from' => '#10b981', 'to' => '#059669'],
        ];
    @endphp
    <div style="display:flex;flex-direction:column;gap:16px;">
        @foreach($programs as $program)
        @php
            $pastSes       = $program->sessions;
            $totalEnrolled = $program->enrolled_count;
            $totalAtt      = $pastSes->sum('attended_count');
            $overallRate   = ($totalSes = $pastSes->count()) && $totalEnrolled
                             ? round($totalAtt / ($totalSes * $totalEnrolled) * 100) : 0;
            $rateColor = $overallRate >= 75 ? '#16a34a' : ($overallRate >= 50 ? '#d97706' : '#dc2626');
            $pFrom = $typeColors[$program->type]['from'] ?? '#0071AA';
            $pTo   = $typeColors[$program->type]['to'] ?? '#005a88';
            $pLabel= $typeLabels[$program->type] ?? $program->type;
        @endphp
        <div style="background:white;border:1.5px solid #f1f5f9;border-radius:16px;overflow:hidden;">
            {{-- Program header --}}
            <div style="padding:14px 20px;background:linear-gradient(135deg,rgba(0,0,0,.03),rgba(0,0,0,.01));border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;background:linear-gradient(135deg,{{ $pFrom }},{{ $pTo }});border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-size:16px;font-weight:800;flex-shrink:0;">
                        {{ mb_substr($program->name_ar, 0, 1) }}
                    </div>
                    <div>
                        <div style="display:flex;align-items:center;gap:7px;flex-wrap:wrap;">
                            <h3 style="font-size:.95rem;font-weight:800;color:#111827;margin:0;">{{ $program->name_ar }}</h3>
                            <span style="font-size:.7rem;font-weight:700;padding:2px 9px;border-radius:20px;background:linear-gradient(135deg,{{ $pFrom }},{{ $pTo }});color:white;">{{ $pLabel }}</span>
                            @foreach($program->classes as $cls)
                            <span style="font-size:.7rem;font-weight:700;padding:2px 9px;border-radius:20px;background:#e0f2fe;color:#0071AA;">{{ $cls->name }}</span>
                            @endforeach
                        </div>
                        <p style="font-size:.75rem;color:#6b7280;margin:2px 0 0;">
                            {{ $totalEnrolled }} متدرب مسجّل &nbsp;·&nbsp; {{ $pastSes->count() }} جلسة منعقدة
                            &nbsp;·&nbsp; معدل حضور إجمالي:
                            <strong style="color:{{ $rateColor }}">{{ $overallRate }}%</strong>
                        </p>
                    </div>
                </div>
                <a href="{{ route('teacher.my-courses.show', $program->id) }}"
                   style="font-size:.75rem;font-weight:700;color:{{ $pFrom }};text-decoration:none;">عرض الدورة ←</a>
            </div>

            @if($pastSes->isEmpty())
            <div style="padding:32px;text-align:center;font-size:.85rem;color:#9ca3af;">لا توجد جلسات منعقدة بعد</div>
            @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:.85rem;">
                    <thead>
                        <tr style="background:#f9fafb;">
                            <th style="padding:10px 16px;text-align:right;font-size:.72rem;font-weight:700;color:#6b7280;white-space:nowrap;">#</th>
                            <th style="padding:10px 16px;text-align:right;font-size:.72rem;font-weight:700;color:#6b7280;white-space:nowrap;">العنوان</th>
                            <th style="padding:10px 16px;text-align:right;font-size:.72rem;font-weight:700;color:#6b7280;white-space:nowrap;">التاريخ</th>
                            <th style="padding:10px 16px;text-align:right;font-size:.72rem;font-weight:700;color:#6b7280;white-space:nowrap;">الحضور</th>
                            <th style="padding:10px 16px;text-align:right;font-size:.72rem;font-weight:700;color:#6b7280;white-space:nowrap;">النسبة</th>
                            <th style="padding:10px 16px;text-align:right;font-size:.72rem;font-weight:700;color:#6b7280;white-space:nowrap;">إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pastSes as $ses)
                        @php
                            $att    = $ses->attended_count;
                            $absent = max(0, $totalEnrolled - $att);
                            $rate   = $totalEnrolled > 0 ? round($att / $totalEnrolled * 100) : 0;
                            $rc     = $rate >= 75 ? '#16a34a' : ($rate >= 50 ? '#d97706' : '#dc2626');
                            $rb     = $rate >= 75 ? '#dcfce7' : ($rate >= 50 ? '#fef3c7' : '#fee2e2');
                        @endphp
                        <tr style="border-top:1px solid #f9fafb;">
                            <td style="padding:12px 16px;color:#9ca3af;font-weight:700;">{{ $ses->session_number }}</td>
                            <td style="padding:12px 16px;">
                                <p style="font-weight:700;color:#111827;margin:0;">{{ $ses->title ?: 'جلسة '.$ses->session_number }}</p>
                                <p style="font-size:.72rem;color:#9ca3af;margin:2px 0 0;">
                                    {{ $ses->type === 'live_zoom' ? 'Zoom مباشر' : 'مسجّلة' }}
                                </p>
                            </td>
                            <td style="padding:12px 16px;color:#374151;white-space:nowrap;">
                                {{ $ses->scheduled_at ? \Carbon\Carbon::parse($ses->scheduled_at)->format('Y/m/d H:i') : '—' }}
                            </td>
                            <td style="padding:12px 16px;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="width:80px;height:6px;border-radius:99px;background:#f1f5f9;overflow:hidden;">
                                        <div style="height:100%;border-radius:99px;width:{{ $rate }}%;background:{{ $rc }};"></div>
                                    </div>
                                    <span style="font-size:.8rem;font-weight:700;color:#111827;">{{ $att }} / {{ $totalEnrolled }}</span>
                                    <span style="font-size:.72rem;color:#9ca3af;">غائب: {{ $absent }}</span>
                                </div>
                            </td>
                            <td style="padding:12px 16px;">
                                <span style="background:{{ $rb }};color:{{ $rc }};font-size:.75rem;font-weight:700;padding:3px 10px;border-radius:20px;">{{ $rate }}%</span>
                            </td>
                            <td style="padding:12px 16px;">
                                <a href="{{ route('teacher.my-courses.sessions.attendance', [$program->id, $ses->id]) }}"
                                   style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;background:linear-gradient(135deg,{{ $pFrom }},{{ $pTo }});color:white;border-radius:8px;font-size:.75rem;font-weight:700;text-decoration:none;">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    عرض / تسجيل
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

</div>

<script>
function switchTab(tab) {
    const isSubjects = tab === 'subjects';

    document.getElementById('pane-subjects').style.display = isSubjects ? 'block' : 'none';
    document.getElementById('pane-programs').style.display = isSubjects ? 'none' : 'block';

    const activeStyle   = 'flex:1;padding:12px;font-size:13px;font-weight:700;border:none;cursor:pointer;background:linear-gradient(135deg,#0071AA,#005a88);color:white;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:7px;';
    const inactiveStyle = 'flex:1;padding:12px;font-size:13px;font-weight:700;border:none;cursor:pointer;background:transparent;color:#6b7280;transition:all .15s;display:flex;align-items:center;justify-content:center;gap:7px;';

    document.getElementById('tab-subjects').style.cssText = isSubjects ? activeStyle : inactiveStyle;
    document.getElementById('tab-programs').style.cssText = isSubjects ? inactiveStyle : activeStyle;

    // update badge color
    const subjBadge = document.querySelector('#tab-subjects span');
    const progBadge = document.querySelector('#tab-programs span');
    if (subjBadge) subjBadge.style.background = isSubjects ? 'rgba(255,255,255,.25)' : '#e5e7eb';
    if (subjBadge) subjBadge.style.color       = isSubjects ? 'white' : '#374151';
    if (progBadge) progBadge.style.background  = isSubjects ? '#e5e7eb' : 'rgba(255,255,255,.25)';
    if (progBadge) progBadge.style.color        = isSubjects ? '#374151' : 'white';
}
</script>
@endsection
