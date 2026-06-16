@extends('layouts.dashboard')

@section('title', 'أعذار الغياب')

@section('content')
<div style="direction:rtl;font-family:'Segoe UI',sans-serif;" class="mx-auto max-w-screen-xl p-4 md:p-6">

    {{-- Hero --}}
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 55%,#0071AA 100%);border-radius:20px;padding:24px 28px;margin-bottom:24px;position:relative;overflow:hidden;">
        <div style="display:flex;align-items:center;gap:14px;position:relative;z-index:1;">
            <div style="width:48px;height:48px;background:rgba(255,255,255,.12);border-radius:13px;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="white" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm-2 14l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/></svg>
            </div>
            <div>
                <p style="color:rgba(255,255,255,.5);font-size:12px;margin:0 0 2px;">{{ now()->translatedFormat('l، d F Y') }}</p>
                <h1 style="color:white;font-size:20px;font-weight:700;margin:0;">أعذار الغياب</h1>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;border:1px solid #86efac;color:#15803d;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-weight:600;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div style="background:#fee2e2;border:1px solid #fca5a5;color:#b91c1c;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-weight:600;">{{ session('error') }}</div>
    @endif

    {{-- Status filter tabs --}}
    <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
        @php
            $tabs = [
                'pending'  => ['قيد المراجعة', '#f59e0b', $counts['pending']],
                'approved' => ['مقبولة', '#16a34a', $counts['approved']],
                'rejected' => ['مرفوضة', '#dc2626', $counts['rejected']],
            ];
        @endphp
        @foreach($tabs as $key => [$label, $color, $count])
            <a href="{{ route('admin.apologies.index', ['status' => $key]) }}"
               style="display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:12px;text-decoration:none;font-size:13px;font-weight:700;
                      {{ $status === $key ? "background:$color;color:#fff;" : 'background:#f1f5f9;color:#475569;' }}">
                {{ $label }}
                <span style="background:{{ $status === $key ? 'rgba(255,255,255,.25)' : '#e2e8f0' }};color:{{ $status === $key ? '#fff' : '#475569' }};border-radius:20px;padding:1px 8px;font-size:11px;">{{ $count }}</span>
            </a>
        @endforeach
    </div>

    {{-- Apologies list --}}
    <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.05);">
        @forelse($apologies as $apology)
            <div style="padding:18px 20px;border-bottom:1px solid #f1f5f9;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:14px;">
                    <div style="flex:1;min-width:240px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                            <span style="font-size:15px;font-weight:700;color:#111827;">{{ $apology->student->name ?? '—' }}</span>
                            @php $st = $apology->status; @endphp
                            <span style="font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px;
                                {{ $st==='approved' ? 'background:#dcfce7;color:#15803d;' : ($st==='rejected' ? 'background:#fee2e2;color:#b91c1c;' : 'background:#fef9c3;color:#a16207;') }}">
                                {{ $apology->statusLabelAr() }}
                            </span>
                        </div>
                        <div style="font-size:13px;color:#475569;margin-bottom:4px;">
                            📚 المحاضرة: <strong>{{ $apology->session->title ?? '—' }}</strong>
                            @if($apology->session?->scheduled_at)
                                — {{ \Carbon\Carbon::parse($apology->session->scheduled_at)->format('Y/m/d H:i') }}
                            @endif
                        </div>
                        <div style="font-size:13px;color:#1e293b;background:#f8fafc;border-radius:10px;padding:10px 12px;margin-top:8px;">
                            <span style="color:#64748b;">السبب:</span> {{ $apology->reason }}
                        </div>
                        @if($apology->attachment_path)
                            <a href="{{ asset('storage/'.$apology->attachment_path) }}" target="_blank"
                               style="display:inline-flex;align-items:center;gap:6px;margin-top:8px;font-size:12px;color:#0071AA;font-weight:600;text-decoration:none;">
                                📎 عرض المرفق
                            </a>
                        @endif
                        @if($apology->review_note)
                            <div style="font-size:12px;color:#64748b;margin-top:8px;">ملاحظة المراجعة: {{ $apology->review_note }}</div>
                        @endif
                        @if($apology->reviewed_at)
                            <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                                بواسطة {{ $apology->reviewer->name ?? '—' }} · {{ $apology->reviewed_at->format('Y/m/d H:i') }}
                            </div>
                        @endif
                    </div>

                    @if($apology->isPending())
                        <div style="display:flex;flex-direction:column;gap:8px;min-width:200px;">
                            <form method="POST" action="{{ route('admin.apologies.approve', $apology) }}" style="display:flex;flex-direction:column;gap:6px;">
                                @csrf
                                <input type="text" name="review_note" placeholder="ملاحظة (اختياري)"
                                       style="border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;font-size:12px;">
                                <button type="submit" style="background:#16a34a;color:#fff;border:none;border-radius:9px;padding:9px;font-size:13px;font-weight:700;cursor:pointer;">✓ قبول العذر</button>
                            </form>
                            <form method="POST" action="{{ route('admin.apologies.reject', $apology) }}">
                                @csrf
                                <button type="submit" style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:9px;padding:9px;font-size:13px;font-weight:700;cursor:pointer;width:100%;">✗ رفض العذر</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div style="padding:48px;text-align:center;color:#94a3b8;font-size:14px;">لا توجد أعذار في هذه القائمة.</div>
        @endforelse
    </div>

    <div style="margin-top:16px;">{{ $apologies->links() }}</div>
</div>
@endsection
