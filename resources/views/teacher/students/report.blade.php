@extends('layouts.dashboard')

@section('title', 'تقرير المتدرب')

@section('content')
@php $s = $report['summary']; @endphp
<div style="direction:rtl;font-family:'Segoe UI',sans-serif;" class="mx-auto max-w-screen-xl p-4 md:p-6">

    {{-- Hero --}}
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 55%,#0071AA 100%);border-radius:20px;padding:24px 28px;margin-bottom:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;">
            <div>
                <p style="color:rgba(255,255,255,.5);font-size:12px;margin:0 0 2px;">تقرير المتدرب — موادك فقط</p>
                <h1 style="color:white;font-size:20px;font-weight:700;margin:0;">{{ $student->name }}</h1>
            </div>
            <a href="{{ route('teacher.students.index') }}" style="background:rgba(255,255,255,.14);color:#fff;padding:9px 16px;border-radius:11px;text-decoration:none;font-size:13px;font-weight:700;">← قائمة المتدربين</a>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;border:1px solid #86efac;color:#15803d;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-weight:600;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div style="background:#fee2e2;border:1px solid #fca5a5;color:#b91c1c;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-weight:600;">{{ $errors->first() }}</div>
    @endif

    {{-- Summary tiles --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:20px;">
        @php
            $tiles = [
                ['نسبة الحضور', $s['attendance_rate'].'%', '#0071AA'],
                ['الجلسات', $s['attended_sessions'].' / '.$s['total_sessions'], '#0f766e'],
                ['الاختبارات', $s['quizzes_count'], '#7c3aed'],
                ['متوسط الاختبارات', ($s['avg_quiz'] !== null ? $s['avg_quiz'].'%' : '—'), '#c026d3'],
                ['الواجبات', $s['homework_count'], '#ca8a04'],
                ['المواد', $s['subjects_count'], '#dc2626'],
            ];
        @endphp
        @foreach($tiles as [$label, $val, $color])
            <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;padding:14px 16px;box-shadow:0 1px 6px rgba(0,0,0,.04);">
                <div style="font-size:12px;color:#64748b;margin-bottom:6px;">{{ $label }}</div>
                <div style="font-size:22px;font-weight:800;color:{{ $color }};">{{ $val }}</div>
            </div>
        @endforeach
    </div>

    {{-- Marks distribution --}}
    @php $m = $report['marks']; $w = $m['weights']; @endphp
    <x-report-card title="توزيع الدرجات" icon="🧮">
        @if($m['rows']->isEmpty())
            <x-report-empty>لا توجد مواد لحساب توزيع الدرجات.</x-report-empty>
        @else
            <div style="overflow-x:auto;border:1px solid #e2e8f0;border-radius:14px;">
                <table class="marks-table" style="width:100%;border-collapse:collapse;min-width:720px;">
                    <thead>
                        <tr>
                            <th rowspan="2" class="mk-h mk-h-name">المادة</th>
                            <th rowspan="2" class="mk-h">الحضور<span class="mk-w">{{ $w['attendance'] }}</span></th>
                            <th colspan="2" class="mk-h mk-h-group">المشاركة<span class="mk-w">{{ $w['quizzes'] + $w['homework'] }}</span></th>
                            <th rowspan="2" class="mk-h">اختبار نصفي<span class="mk-w">{{ $w['midterm'] }}</span></th>
                            <th rowspan="2" class="mk-h">اختبار نهائي<span class="mk-w">{{ $w['final'] }}</span></th>
                            <th rowspan="2" class="mk-h mk-h-total">المجموع<span class="mk-w">100</span></th>
                        </tr>
                        <tr>
                            <th class="mk-h mk-h-sub">الاختبارات القصيرة<span class="mk-w">{{ $w['quizzes'] }}</span></th>
                            <th class="mk-h mk-h-sub">الواجبات<span class="mk-w">{{ $w['homework'] }}</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($m['rows'] as $row)
                            @php
                                $cells = [
                                    ['attendance', $w['attendance'], '#0071AA'],
                                    ['quizzes',    $w['quizzes'],    '#7c3aed'],
                                    ['homework',   $w['homework'],   '#ca8a04'],
                                    ['midterm',    $w['midterm'],    '#0f766e'],
                                    ['final',      $w['final'],      '#dc2626'],
                                ];
                                $totalPct = max(0, min(100, $row['total']));
                                $totalCol = $totalPct >= 85 ? '#15803d' : ($totalPct >= 60 ? '#0071AA' : '#b91c1c');
                            @endphp
                            <tr>
                                <td class="mk-c mk-c-name">
                                    <span style="font-weight:600;">{{ $row['name'] }}</span>
                                    @if($row['code'])<span class="mk-code">{{ $row['code'] }}</span>@endif
                                </td>
                                @foreach($cells as [$key, $max, $color])
                                    @php $pct = $max > 0 ? max(0, min(100, $row[$key] / $max * 100)) : 0; @endphp
                                    <td class="mk-c">
                                        <span class="mk-val" style="color:{{ $color }};">{{ $row[$key] }}</span><span class="mk-max">/{{ $max }}</span>
                                        <span class="mk-bar"><i style="width:{{ $pct }}%;background:{{ $color }};"></i></span>
                                    </td>
                                @endforeach
                                <td class="mk-c mk-c-total">
                                    <span class="mk-total" style="color:{{ $totalCol }};">{{ $row['total'] }}</span>
                                    <span class="mk-bar"><i style="width:{{ $totalPct }}%;background:{{ $totalCol }};"></i></span>
                                </td>
                            </tr>
                        @endforeach
                        @if($m['rows']->count() > 1)
                            <tr class="mk-foot">
                                <td colspan="6" class="mk-c" style="text-align:left;font-weight:700;">المعدل العام</td>
                                <td class="mk-c mk-c-total"><span class="mk-total" style="color:#0071AA;">{{ $m['total'] }}</span></td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <style>
                .marks-table th, .marks-table td { border:1px solid #e8edf3; }
                .marks-table thead tr:first-child th { border-top:none; }
                .marks-table th:first-child, .marks-table td:first-child { border-right:none; }
                .marks-table th:last-child,  .marks-table td:last-child  { border-left:none; }
                .mk-h { background:#f1f5f9; color:#334155; font-size:12px; font-weight:700;
                        padding:10px 8px; text-align:center; line-height:1.5; white-space:nowrap; }
                .mk-h-group { background:#e8eef6; }
                .mk-h-sub   { background:#f6f9fc; font-weight:600; }
                .mk-h-name  { text-align:right; padding-right:14px; }
                .mk-h-total { background:#0071AA; color:#fff; }
                .mk-h-total .mk-w { background:rgba(255,255,255,.2); color:#fff; }
                .mk-w  { display:block; margin-top:3px; font-weight:600; font-size:10px;
                         color:#64748b; background:#fff; border-radius:6px; padding:1px 0; }
                .mk-c  { padding:10px 8px; text-align:center; vertical-align:middle; }
                .marks-table tbody tr:nth-child(even) td { background:#fcfdfe; }
                .marks-table tbody tr:hover td { background:#f4f9fd; }
                .mk-c-name { text-align:right; padding-right:14px; font-size:13px; color:#0f172a; }
                .mk-code { display:inline-block; margin-right:6px; font-size:10px; color:#64748b;
                           background:#f1f5f9; border-radius:5px; padding:1px 6px; }
                .mk-val   { font-size:15px; font-weight:800; }
                .mk-max   { font-size:11px; color:#94a3b8; margin-right:1px; }
                .mk-total { font-size:17px; font-weight:800; }
                .mk-bar   { display:block; height:4px; border-radius:99px; background:#eef2f7; margin-top:5px; overflow:hidden; }
                .mk-bar i { display:block; height:100%; border-radius:99px; }
                .mk-foot td { background:#f1f5f9 !important; }
            </style>
        @endif
    </x-report-card>

    <form method="POST" action="{{ route('teacher.students.report.update', $student) }}">
        @csrf
        @method('PATCH')

        {{-- Subjects / final grades --}}
        <x-report-card title="المواد والدرجة النهائية" icon="📚">
            @if($report['subjects']->isEmpty())
                <x-report-empty>لا توجد مواد مسجلة ضمن موادك.</x-report-empty>
            @else
                <x-report-table :head="['المادة','الكود','الحالة','الدرجة النهائية (0-100)','التقدير']">
                    @foreach($report['subjects'] as $row)
                        <tr>
                            <td style="font-weight:600;">{{ $row['name'] }}</td>
                            <td>{{ $row['code'] ?? '—' }}</td>
                            <td>{{ $row['status'] }}</td>
                            <td>
                                <input type="number" step="0.01" min="0" max="100"
                                       name="subjects[{{ $row['enrollment_id'] }}][final_grade]"
                                       value="{{ $row['final_grade'] }}"
                                       style="width:110px;border:1px solid #e2e8f0;border-radius:8px;padding:6px 8px;font-size:13px;">
                            </td>
                            <td style="font-weight:700;color:#0071AA;">{{ $row['grade_letter'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                </x-report-table>
            @endif
        </x-report-card>

        {{-- Exams --}}
        <x-report-card title="الاختبارات" icon="📝">
            @if($report['quizzes']->isEmpty())
                <x-report-empty>لا توجد محاولات اختبار.</x-report-empty>
            @else
                <x-report-table :head="['الاختبار','التاريخ','الدرجة','النسبة %','ناجح','معتمد']">
                    @foreach($report['quizzes'] as $row)
                        <tr>
                            <td style="font-weight:600;">{{ $row['title'] }}</td>
                            <td>{{ $row['submitted_at'] ?? '—' }}</td>
                            <td>
                                <input type="number" step="0.01" min="0"
                                       name="quizzes[{{ $row['attempt_id'] }}][score]"
                                       value="{{ $row['score'] }}"
                                       style="width:90px;border:1px solid #e2e8f0;border-radius:8px;padding:6px 8px;font-size:13px;">
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" max="100"
                                       name="quizzes[{{ $row['attempt_id'] }}][percentage]"
                                       value="{{ $row['percentage'] }}"
                                       style="width:90px;border:1px solid #e2e8f0;border-radius:8px;padding:6px 8px;font-size:13px;">
                            </td>
                            <td>{!! $row['passed'] ? '<span style="color:#15803d;font-weight:700;">نعم</span>' : '<span style="color:#b91c1c;font-weight:700;">لا</span>' !!}</td>
                            <td>{{ $row['released'] ? '✓' : '—' }}</td>
                        </tr>
                    @endforeach
                </x-report-table>
            @endif
        </x-report-card>

        {{-- Homework --}}
        <x-report-card title="الواجبات" icon="📋">
            @if($report['homework']->isEmpty())
                <x-report-empty>لا توجد واجبات مُسلَّمة.</x-report-empty>
            @else
                <x-report-table :head="['الواجب','التاريخ','الدرجة','من','ملاحظة المعلم']">
                    @foreach($report['homework'] as $row)
                        <tr>
                            <td style="font-weight:600;">{{ $row['title'] }}</td>
                            <td>{{ $row['submitted_at'] ?? '—' }}</td>
                            <td>
                                <input type="number" min="0"
                                       name="homework[{{ $row['submission_id'] }}][grade]"
                                       value="{{ $row['grade'] }}"
                                       style="width:80px;border:1px solid #e2e8f0;border-radius:8px;padding:6px 8px;font-size:13px;">
                            </td>
                            <td>{{ $row['max_grade'] ?? '—' }}</td>
                            <td>
                                <input type="text"
                                       name="homework[{{ $row['submission_id'] }}][feedback]"
                                       value="{{ $row['feedback'] }}"
                                       placeholder="ملاحظة"
                                       style="width:100%;min-width:160px;border:1px solid #e2e8f0;border-radius:8px;padding:6px 8px;font-size:13px;">
                            </td>
                        </tr>
                    @endforeach
                </x-report-table>
            @endif
        </x-report-card>

        {{-- Attendance --}}
        <x-report-card title="الحضور" icon="📅">
            @if($report['attendance']['rows']->isEmpty())
                <x-report-empty>لا توجد سجلات حضور.</x-report-empty>
            @else
                <x-report-table :head="['الجلسة','التاريخ','حضر؟']">
                    @foreach($report['attendance']['rows'] as $row)
                        <tr>
                            <td style="font-weight:600;">{{ $row['session'] }}</td>
                            <td>{{ $row['scheduled_at'] ?? '—' }}</td>
                            <td>
                                <input type="hidden" name="attendance[{{ $row['attendance_id'] }}][attended]" value="0">
                                <label style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;">
                                    <input type="checkbox" name="attendance[{{ $row['attendance_id'] }}][attended]" value="1" {{ $row['attended'] ? 'checked' : '' }}>
                                    <span style="font-size:12px;color:#475569;">حاضر</span>
                                </label>
                            </td>
                        </tr>
                    @endforeach
                </x-report-table>
            @endif
        </x-report-card>

        <div style="position:sticky;bottom:0;background:linear-gradient(to top,#fff 70%,transparent);padding:14px 0;margin-top:8px;">
            <button type="submit" style="background:#0071AA;color:#fff;border:none;border-radius:11px;padding:11px 22px;font-size:14px;font-weight:700;cursor:pointer;">💾 حفظ التعديلات</button>
        </div>
    </form>
</div>
@endsection
