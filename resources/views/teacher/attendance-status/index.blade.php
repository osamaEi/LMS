@extends('layouts.dashboard')

@section('title', 'حالة الغياب')

@section('content')
<div class="as-wrap">

    <div class="as-head">
        <div>
            <p class="as-kicker">متابعة الحضور</p>
            <h1 class="as-title">حالة الغياب في موادك</h1>
        </div>
    </div>

    @unless($enabled)
        <p class="as-warn">⚠️ الحرمان غير مفعّل حاليًا من الإدارة — الأرقام أدناه للاطلاع فقط.</p>
    @endunless

    @if($subjects->isEmpty())
        <div class="as-empty">لا توجد مواد مسندة إليك.</div>
    @else
        <div class="as-grid">
            @foreach($subjects as $s)
                <a href="{{ route('teacher.attendance-status.show', $s['id']) }}" class="as-card">
                    <div class="as-card-top">
                        <h2 class="as-name">{{ $s['name'] }}</h2>
                        @if($s['code'])<span class="as-code">{{ $s['code'] }}</span>@endif
                    </div>

                    <p class="as-meta">
                        {{ $s['sessions'] }} محاضرة · {{ $s['students'] }} متدرب ·
                        الحد {{ $s['limit'] }}%{{ $s['custom'] ? ' (مخصص)' : '' }}
                    </p>

                    <div class="as-stats">
                        <div class="as-stat">
                            <b class="as-red">{{ $s['banned'] }}</b>
                            <span>محروم</span>
                        </div>
                        <div class="as-stat">
                            <b class="as-amber">{{ $s['at_risk'] }}</b>
                            <span>قارب الحرمان</span>
                        </div>
                        <div class="as-stat">
                            <b class="as-blue">{{ $s['students'] - $s['banned'] - $s['at_risk'] }}</b>
                            <span>آمن</span>
                        </div>
                    </div>

                    <span class="as-go">عرض المتدربين ←</span>
                </a>
            @endforeach
        </div>
    @endif
</div>

<style>
    .as-wrap { direction:rtl; font-family:'Segoe UI',sans-serif; max-width:1100px; margin:0 auto; padding:20px 16px 60px; }
    .as-kicker { color:#64748b; font-size:12px; margin:0 0 4px; }
    .as-title { color:#0f172a; font-size:24px; font-weight:800; margin:0 0 20px; }
    .as-warn { background:#fef3c7; border:1px solid #fcd34d; color:#92400e; padding:10px 14px;
               border-radius:10px; font-size:13px; font-weight:600; margin:0 0 18px; }
    .as-empty { border:1px solid #e2e8f0; border-radius:16px; padding:40px; text-align:center;
                color:#64748b; font-weight:600; background:#fff; }

    .as-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:16px; }
    .as-card { display:block; background:#fff; border:1px solid #e6ebf1; border-radius:18px;
               padding:18px 20px; text-decoration:none; transition:box-shadow .15s, transform .15s;
               box-shadow:0 6px 22px rgba(15,23,42,.06); }
    .as-card:hover { box-shadow:0 10px 28px rgba(15,23,42,.12); transform:translateY(-2px); }
    .as-card-top { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .as-name { font-size:15px; font-weight:800; color:#0f172a; margin:0; }
    .as-code { font-size:11px; color:#94a3b8; font-family:monospace; }
    .as-meta { font-size:11.5px; color:#64748b; margin:6px 0 14px; line-height:1.7; }

    .as-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; }
    .as-stat { text-align:center; background:#fafcfe; border:1px solid #eef2f7; border-radius:11px; padding:10px 4px; }
    .as-stat b { display:block; font-size:20px; font-weight:800; line-height:1.1; }
    .as-stat span { display:block; margin-top:3px; font-size:10.5px; font-weight:700; color:#64748b; }
    .as-red { color:#b91c1c; } .as-amber { color:#b45309; } .as-blue { color:#0071AA; }

    .as-go { display:block; margin-top:14px; font-size:12px; font-weight:700; color:#0071AA; }
</style>
@endsection
