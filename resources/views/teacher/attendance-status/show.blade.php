@extends('layouts.dashboard')

@section('title', 'حالة الغياب — ' . ($subject->name_ar ?: $subject->name_en))

@section('content')
@php
    $allowed = $students->first()->allowed ?? (int) floor($totalSessions * $limit / 100);
@endphp

<div class="as-wrap">

    <div class="as-head">
        <div>
            <p class="as-kicker">متابعة الحضور</p>
            <h1 class="as-title">{{ $subject->name_ar ?: $subject->name_en }}</h1>
        </div>
        <a href="{{ route('teacher.attendance-status.index') }}" class="as-back">← كل المواد</a>
    </div>

    @unless($enabled)
        <p class="as-warn">⚠️ الحرمان غير مفعّل حاليًا من الإدارة — الأرقام أدناه للاطلاع فقط.</p>
    @endunless

    <div class="as-summary">
        <div class="as-sum"><b>{{ $totalSessions }}</b><span>إجمالي المحاضرات</span></div>
        <div class="as-sum"><b>{{ $limit }}%</b><span>الحد المسموح للغياب</span></div>
        <div class="as-sum"><b>{{ $allowed }}</b><span>محاضرات مسموح غيابها</span></div>
        <div class="as-sum"><b class="as-red">{{ $students->where('banned', true)->count() }}</b><span>محروم</span></div>
    </div>

    @if($students->isEmpty())
        <div class="as-empty">لا يوجد متدربون مسجّلون في محاضرات هذه المادة.</div>
    @else
        <div class="as-card as-table-card">
            <div class="as-scroll">
                <table class="as-table">
                    <thead>
                        <tr>
                            <th>المتدرب</th>
                            <th>حضور</th>
                            <th>غياب</th>
                            <th>بعذر</th>
                            <th>النسبة</th>
                            <th>المتبقي له</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $s)
                            <tr class="{{ $s->banned ? 'as-row-banned' : ($s->remaining <= 2 && !$s->exempt ? 'as-row-risk' : '') }}">
                                <td>
                                    <b class="as-sname">{{ $s->name }}</b>
                                    <i class="as-mail">{{ $s->email }}</i>
                                </td>
                                <td class="as-num as-blue">{{ $s->attended }}</td>
                                <td class="as-num as-red">{{ $s->absent }}</td>
                                <td class="as-num as-green">{{ $s->excused }}</td>
                                <td>
                                    <span class="as-pct {{ $s->exceeded ? 'as-pct-over' : '' }}">{{ $s->percent }}%</span>
                                </td>
                                <td>
                                    @if($s->exceeded)
                                        <span class="as-rem as-rem-zero">تجاوز الحد</span>
                                    @else
                                        <span class="as-rem">
                                            {{ $s->remaining }}
                                            <i>محاضرة</i>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($s->exempt)
                                        <span class="as-badge as-badge-ok">مستثنى</span>
                                    @elseif($s->banned)
                                        <span class="as-badge as-badge-no">محروم</span>
                                    @elseif($s->remaining <= 2)
                                        <span class="as-badge as-badge-warn">قارب الحرمان</span>
                                    @else
                                        <span class="as-badge as-badge-idle">منتظم</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <p class="as-note">
            «المتبقي له» = عدد المحاضرات التي ما زال بإمكانه التغيّب عنها قبل الحرمان.
            الغياب بعذر مقبول لا يُحتسب. رفع الحرمان يتم من الإدارة.
        </p>
    @endif
</div>

<style>
    .as-wrap { direction:rtl; font-family:'Segoe UI',sans-serif; max-width:1100px; margin:0 auto; padding:20px 16px 60px; }
    .as-head { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:14px; }
    .as-kicker { color:#64748b; font-size:12px; margin:0 0 4px; }
    .as-title { color:#0f172a; font-size:24px; font-weight:800; margin:0 0 20px; }
    .as-back { background:#f1f5f9; color:#334155; padding:9px 16px; border-radius:11px;
               text-decoration:none; font-size:13px; font-weight:700; }
    .as-warn { background:#fef3c7; border:1px solid #fcd34d; color:#92400e; padding:10px 14px;
               border-radius:10px; font-size:13px; font-weight:600; margin:0 0 18px; }
    .as-empty { border:1px solid #e2e8f0; border-radius:16px; padding:40px; text-align:center;
                color:#64748b; font-weight:600; background:#fff; }

    .as-summary { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:12px; margin-bottom:18px; }
    .as-sum { background:#fff; border:1px solid #e6ebf1; border-radius:14px; padding:14px 10px; text-align:center;
              box-shadow:0 4px 14px rgba(15,23,42,.05); }
    .as-sum b { display:block; font-size:22px; font-weight:800; color:#0f172a; line-height:1.1; }
    .as-sum span { display:block; margin-top:4px; font-size:11px; font-weight:700; color:#64748b; }

    .as-card { background:#fff; border:1px solid #e6ebf1; border-radius:18px; overflow:hidden;
               box-shadow:0 6px 22px rgba(15,23,42,.06); }
    .as-scroll { overflow-x:auto; }
    .as-table { width:100%; border-collapse:collapse; min-width:700px; }
    .as-table th { text-align:right; font-size:12px; font-weight:700; color:#64748b; background:#f7f9fc;
                   padding:12px 12px; border-bottom:1px solid #eef2f7; white-space:nowrap; }
    .as-table td { padding:12px; border-bottom:1px solid #f4f7fa; text-align:right; vertical-align:middle; }
    .as-table tr:last-child td { border-bottom:0; }
    .as-row-banned { background:#fef7f7; }
    .as-row-risk { background:#fffdf5; }

    .as-sname { display:block; font-size:13px; font-weight:700; color:#0f172a; }
    .as-mail { display:block; font-style:normal; font-size:11px; color:#94a3b8; margin-top:2px; }
    .as-num { font-size:15px; font-weight:800; }
    .as-red { color:#b91c1c; } .as-green { color:#15803d; } .as-blue { color:#0071AA; }

    .as-pct { display:inline-block; background:#f1f5f9; color:#475569; font-size:13px;
              font-weight:800; border-radius:8px; padding:4px 10px; }
    .as-pct-over { background:#fee2e2; color:#b91c1c; }

    .as-rem { font-size:16px; font-weight:800; color:#0f172a; white-space:nowrap; }
    .as-rem i { font-style:normal; font-size:11px; font-weight:600; color:#94a3b8; margin-right:3px; }
    .as-rem-zero { font-size:12px; font-weight:800; color:#b91c1c; }

    .as-badge { display:inline-block; font-size:11px; font-weight:800; border-radius:99px;
                padding:4px 11px; white-space:nowrap; }
    .as-badge-ok   { background:#dcfce7; color:#15803d; }
    .as-badge-no   { background:#fee2e2; color:#b91c1c; }
    .as-badge-warn { background:#fef3c7; color:#b45309; }
    .as-badge-idle { background:#f1f5f9; color:#64748b; }

    .as-note { font-size:11.5px; color:#94a3b8; margin:12px 2px 0; line-height:1.8; }
</style>
@endsection
