@extends('layouts.dashboard')

@section('title', 'سجل الحضور')

@section('content')
<div style="direction:rtl;font-family:'Segoe UI',sans-serif;" class="mx-auto max-w-screen-xl p-4 md:p-6">

    {{-- Hero --}}
    <div style="background:linear-gradient(135deg,#0071AA 0%,#004d77 100%);border-radius:20px;padding:24px 28px;margin-bottom:24px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-50px;left:-50px;width:200px;height:200px;background:rgba(255,255,255,.06);border-radius:50%;pointer-events:none;"></div>
        <div style="display:flex;align-items:center;gap:14px;position:relative;z-index:1;">
            <div style="width:48px;height:48px;background:rgba(255,255,255,.12);border-radius:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="24" height="24" fill="white" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            </div>
            <div>
                <p style="color:rgba(255,255,255,.55);font-size:12px;margin:0 0 2px;">{{ now()->translatedFormat('l، d F Y') }}</p>
                <h1 style="color:white;font-size:20px;font-weight:700;margin:0;">سجل الحضور</h1>
                <p style="color:rgba(255,255,255,.6);font-size:12px;margin:2px 0 0;">الجلسات التي انضممت إليها</p>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:24px;">
        <div style="background:#fff;border:1px solid #f1f5f9;border-radius:16px;padding:20px;display:flex;align-items:center;gap:16px;box-shadow:0 1px 4px rgba(0,0,0,.04);">
            <div style="width:52px;height:52px;border-radius:14px;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="26" height="26" fill="#16a34a" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            </div>
            <div>
                <div style="font-size:1.9rem;font-weight:900;color:#16a34a;line-height:1;">{{ $attendedSessions }}</div>
                <div style="font-size:.8rem;color:#6b7280;margin-top:4px;font-weight:600;">جلسة حضرتها</div>
            </div>
        </div>
        <div style="background:#fff;border:1px solid #f1f5f9;border-radius:16px;padding:20px;display:flex;align-items:center;gap:16px;box-shadow:0 1px 4px rgba(0,0,0,.04);">
            <div style="width:52px;height:52px;border-radius:14px;background:#e0f2fe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="26" height="26" fill="#0071AA" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm1 11h-2V7h2v6z"/></svg>
            </div>
            <div>
                <div style="font-size:1.9rem;font-weight:900;color:#0071AA;line-height:1;">{{ number_format($totalMinutes) }}</div>
                <div style="font-size:.8rem;color:#6b7280;margin-top:4px;font-weight:600;">دقيقة حضور</div>
            </div>
        </div>
    </div>

    {{-- Sessions list --}}
    <div style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.05);">
        <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;background:#fafafa;">
            <span style="font-size:15px;font-weight:700;color:#111827;">الجلسات التي حضرتها</span>
        </div>

        @forelse($attendances as $att)
            @php
                $s = $att->session;
                $title = $s->title_ar ?: ($s->subject->name_ar ?? $s->program->name_ar ?? 'جلسة');
                $sub   = $s->subject->name_ar ?? $s->program->name_ar ?? '';
            @endphp
            <div style="display:flex;align-items:center;gap:14px;padding:14px 20px;border-bottom:1px solid #f8fafc;">
                <div style="width:42px;height:42px;border-radius:12px;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" fill="#16a34a" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:14px;font-weight:700;color:#111827;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $title }}</p>
                    @if($sub)
                    <p style="font-size:12px;color:#6b7280;margin:2px 0 0;">{{ $sub }}</p>
                    @endif
                </div>
                <div style="text-align:left;flex-shrink:0;">
                    <span style="background:#dcfce7;color:#15803d;font-size:.72rem;font-weight:700;padding:3px 12px;border-radius:20px;">✓ حاضر</span>
                    @if($att->joined_at)
                    <div style="font-size:11px;color:#94a3b8;margin-top:4px;">{{ \Carbon\Carbon::parse($att->joined_at)->format('Y/m/d h:i A') }}</div>
                    @endif
                </div>
            </div>
        @empty
            <div style="padding:48px;text-align:center;color:#94a3b8;font-size:14px;">
                لم تحضر أي جلسة بعد.<br>
                <span style="font-size:12px;">سيتم تسجيل حضورك تلقائياً بمجرد انضمامك لأي جلسة من <a href="{{ route('student.my-sessions') }}" style="color:#0071AA;font-weight:600;">محاضراتي</a>.</span>
            </div>
        @endforelse
    </div>

    @if($attendances->hasPages())
    <div style="margin-top:16px;">{{ $attendances->links() }}</div>
    @endif
</div>
@endsection
