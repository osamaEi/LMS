@extends('layouts.dashboard')

@section('title', 'أعذاري')

@section('content')
<div style="direction:rtl;font-family:'Segoe UI',sans-serif;" class="mx-auto max-w-screen-xl p-4 md:p-6">

    {{-- Hero --}}
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 55%,#0071AA 100%);border-radius:20px;padding:24px 28px;margin-bottom:24px;position:relative;overflow:hidden;">
        <div style="display:flex;align-items:center;gap:14px;position:relative;z-index:1;">
            <div style="width:48px;height:48px;background:rgba(255,255,255,.12);border-radius:13px;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="white" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
            </div>
            <div>
                <p style="color:rgba(255,255,255,.5);font-size:12px;margin:0 0 2px;">{{ now()->translatedFormat('l، d F Y') }}</p>
                <h1 style="color:white;font-size:20px;font-weight:700;margin:0;">أعذار الغياب التي قدمتها</h1>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;border:1px solid #86efac;color:#15803d;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-weight:600;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div style="background:#fee2e2;border:1px solid #fca5a5;color:#b91c1c;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-weight:600;">{{ session('error') }}</div>
    @endif

    {{-- Summary counters --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;">
        @foreach([
            ['قيد المراجعة', $counts['pending'],  '#d97706', '#fef3c7'],
            ['مقبولة',       $counts['approved'], '#16a34a', '#dcfce7'],
            ['مرفوضة',       $counts['rejected'], '#dc2626', '#fee2e2'],
        ] as [$label, $val, $color, $bg])
        <div style="background:{{ $bg }};border-radius:14px;padding:16px;text-align:center;">
            <div style="font-size:1.7rem;font-weight:900;color:{{ $color }};line-height:1;">{{ $val }}</div>
            <div style="font-size:.78rem;color:#475569;margin-top:6px;font-weight:600;">{{ $label }}</div>
        </div>
        @endforeach
    </div>

    {{-- Apologies list --}}
    <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.05);">
        @forelse($apologies as $apology)
            @php
                $st = $apology->status;
                $badge = $st==='approved'
                    ? 'background:#dcfce7;color:#15803d;'
                    : ($st==='rejected' ? 'background:#fee2e2;color:#b91c1c;' : 'background:#fef9c3;color:#a16207;');
            @endphp
            <div style="padding:18px 20px;border-bottom:1px solid #f1f5f9;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:14px;flex-wrap:wrap;">
                    <div style="flex:1;min-width:240px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                            <span style="font-size:15px;font-weight:700;color:#111827;">{{ $apology->session->title ?? 'محاضرة' }}</span>
                            <span style="font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px;{{ $badge }}">{{ $apology->statusLabelAr() }}</span>
                        </div>
                        @if($apology->session?->scheduled_at)
                            <div style="font-size:12px;color:#94a3b8;margin-bottom:6px;">📅 {{ \Carbon\Carbon::parse($apology->session->scheduled_at)->format('Y/m/d H:i') }}</div>
                        @endif
                        <div style="font-size:13px;color:#1e293b;background:#f8fafc;border-radius:10px;padding:10px 12px;">
                            <span style="color:#64748b;">السبب:</span> {{ $apology->reason }}
                        </div>
                        @if($apology->attachment_path)
                            <a href="{{ asset('storage/'.$apology->attachment_path) }}" target="_blank"
                               style="display:inline-flex;align-items:center;gap:6px;margin-top:8px;font-size:12px;color:#0071AA;font-weight:600;text-decoration:none;">📎 عرض المرفق</a>
                        @endif
                        @if($apology->review_note)
                            <div style="font-size:12px;color:#475569;margin-top:8px;background:#f1f5f9;border-radius:8px;padding:8px 10px;">
                                <strong>رد الإدارة:</strong> {{ $apology->review_note }}
                            </div>
                        @endif
                        @if($apology->reviewed_at)
                            <div style="font-size:11px;color:#94a3b8;margin-top:6px;">تمت المراجعة في {{ $apology->reviewed_at->format('Y/m/d H:i') }}</div>
                        @endif
                    </div>
                    <div style="flex-shrink:0;">
                        @if($st==='approved')
                            <span style="font-size:28px;">✅</span>
                        @elseif($st==='rejected')
                            <span style="font-size:28px;">❌</span>
                        @else
                            <span style="font-size:28px;">⏳</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div style="padding:48px;text-align:center;color:#94a3b8;font-size:14px;">
                لم تقدّم أي أعذار غياب بعد.<br>
                <span style="font-size:12px;">يمكنك تقديم عذر من صفحة <a href="{{ route('student.my-sessions') }}" style="color:#0071AA;font-weight:600;">محاضراتي</a> بالضغط على المحاضرة التي تغيبت عنها.</span>
            </div>
        @endforelse
    </div>

    <div style="margin-top:16px;">{{ $apologies->links() }}</div>
</div>
@endsection
