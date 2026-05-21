@extends('layouts.dashboard')
@section('title', 'الواجبات المنزلية')

@section('content')
<div style="direction:rtl;max-width:1100px;margin:0 auto;">

{{-- Alert --}}
@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-right:4px solid #22c55e;border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span style="color:#15803d;font-size:14px;font-weight:600;">{{ session('success') }}</span>
</div>
@endif

{{-- Hero --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 55%,#0071AA 100%);border-radius:20px;padding:28px 32px;margin-bottom:24px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-50px;left:-50px;width:200px;height:200px;background:rgba(255,255,255,.04);border-radius:50%;pointer-events:none;"></div>
    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:50px;height:50px;background:linear-gradient(135deg,#0071AA,#005a88);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 16px rgba(0,113,170,.4);">
                <svg width="24" height="24" fill="white" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            </div>
            <div>
                <h1 style="color:white;font-size:20px;font-weight:800;margin:0;">الواجبات المنزلية</h1>
                <p style="color:rgba(255,255,255,.6);font-size:12px;margin:4px 0 0;">إدارة الواجبات لكل جلسة</p>
            </div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            @foreach([
                [$withHomework->count() + $withoutHomework->count(), 'إجمالي الجلسات', 'rgba(255,255,255,.75)'],
                [$withHomework->count(),                             'بها واجب',        '#86efac'],
                [$withoutHomework->count(),                          'بدون واجب',       '#fde68a'],
            ] as [$v,$l,$c])
            <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:8px 18px;text-align:center;min-width:72px;">
                <div style="font-size:20px;font-weight:800;color:{{ $c }};line-height:1.1;">{{ $v }}</div>
                <div style="font-size:11px;color:rgba(255,255,255,.55);margin-top:2px;">{{ $l }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Two-column grid --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    {{-- ══ Sessions WITH homework ══ --}}
    <div>
        <div style="display:flex;align-items:center;gap:9px;margin-bottom:14px;">
            <div style="width:32px;height:32px;background:linear-gradient(135deg,#16a34a,#15803d);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                <svg width="15" height="15" fill="white" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            </div>
            <h2 style="font-size:.95rem;font-weight:800;color:#111827;margin:0;">جلسات بها واجب</h2>
            <span style="background:#16a34a;color:white;font-size:.72rem;font-weight:700;padding:2px 9px;border-radius:20px;">{{ $withHomework->count() }}</span>
        </div>

        @if($withHomework->isEmpty())
        <div style="background:white;border:1.5px dashed #d1d5db;border-radius:14px;padding:36px;text-align:center;">
            <p style="font-size:.85rem;color:#9ca3af;margin:0;">لا توجد جلسات بها واجب بعد</p>
        </div>
        @else
        <div style="display:flex;flex-direction:column;gap:10px;">
            @foreach($withHomework as $session)
            @php
                $subCount = $session->homework->submissions()->count() ?? 0;
            @endphp
            <div style="background:white;border:1.5px solid #bbf7d0;border-radius:13px;overflow:hidden;transition:box-shadow .15s;">
                <div style="display:flex;align-items:stretch;">
                    <div style="width:4px;flex-shrink:0;background:linear-gradient(180deg,#16a34a,#15803d);"></div>
                    <div style="flex:1;padding:13px 15px;">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                            <div style="min-width:0;flex:1;">
                                <p style="font-size:.72rem;color:#6b7280;margin:0 0 3px;">{{ $session->subject->name_ar ?? ($session->program->name_ar ?? '—') }}</p>
                                <h4 style="font-size:.875rem;font-weight:700;color:#111827;margin:0 0 4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                    {{ $session->title_ar ?: $session->title_en ?: ('جلسة #'.$session->session_number) }}
                                </h4>
                                <p style="font-size:.78rem;font-weight:600;color:#d97706;margin:0 0 2px;">
                                    📋 {{ $session->homework->title_ar ?: $session->homework->title_en ?: 'واجب بدون عنوان' }}
                                </p>
                                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                                    @if($session->homework->due_date)
                                    <span style="font-size:.72rem;color:#9ca3af;">
                                        التسليم: {{ $session->homework->due_date->format('Y/m/d') }}
                                    </span>
                                    @endif
                                    <span style="font-size:.72rem;color:#0071AA;font-weight:600;background:#eff6ff;padding:1px 7px;border-radius:20px;">
                                        {{ $subCount }} تسليم
                                    </span>
                                </div>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:5px;flex-shrink:0;">
                                <a href="{{ route('teacher.sessions.show', $session) }}"
                                   style="display:inline-flex;align-items:center;gap:4px;padding:5px 11px;background:linear-gradient(135deg,#0071AA,#005a88);color:white;border-radius:7px;font-size:.72rem;font-weight:700;text-decoration:none;">
                                    <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                                    تعديل
                                </a>
                                <form action="{{ route('teacher.sessions.homework.destroy', $session) }}" method="POST"
                                      onsubmit="return confirm('حذف الواجب؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            style="display:inline-flex;align-items:center;gap:4px;padding:5px 11px;background:linear-gradient(135deg,#ef4444,#dc2626);color:white;border:none;border-radius:7px;font-size:.72rem;font-weight:700;cursor:pointer;width:100%;">
                                        <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ══ Sessions WITHOUT homework ══ --}}
    <div>
        <div style="display:flex;align-items:center;gap:9px;margin-bottom:14px;">
            <div style="width:32px;height:32px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                <svg width="15" height="15" fill="white" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
            </div>
            <h2 style="font-size:.95rem;font-weight:800;color:#111827;margin:0;">جلسات بدون واجب</h2>
            <span style="background:#d97706;color:white;font-size:.72rem;font-weight:700;padding:2px 9px;border-radius:20px;">{{ $withoutHomework->count() }}</span>
        </div>

        @if($withoutHomework->isEmpty())
        <div style="background:white;border:1.5px dashed #d1d5db;border-radius:14px;padding:36px;text-align:center;">
            <p style="font-size:.85rem;color:#9ca3af;margin:0;">جميع الجلسات لديها واجب 🎉</p>
        </div>
        @else
        <div style="display:flex;flex-direction:column;gap:10px;">
            @foreach($withoutHomework as $session)
            <div style="background:white;border:1.5px solid #f1f5f9;border-radius:13px;overflow:hidden;">
                <div style="display:flex;align-items:stretch;">
                    <div style="width:4px;flex-shrink:0;background:#d1d5db;"></div>
                    <div style="flex:1;padding:13px 15px;display:flex;align-items:center;justify-content:space-between;gap:10px;">
                        <div style="min-width:0;flex:1;">
                            <p style="font-size:.72rem;color:#6b7280;margin:0 0 3px;">{{ $session->subject->name_ar ?? ($session->program->name_ar ?? '—') }}</p>
                            <h4 style="font-size:.875rem;font-weight:700;color:#111827;margin:0 0 3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                {{ $session->title_ar ?: $session->title_en ?: ('جلسة #'.$session->session_number) }}
                            </h4>
                            @if($session->scheduled_at)
                            <p style="font-size:.72rem;color:#9ca3af;margin:0;">
                                {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d · H:i') }}
                            </p>
                            @endif
                        </div>
                        <a href="{{ route('teacher.sessions.show', $session) }}"
                           style="flex-shrink:0;display:inline-flex;align-items:center;gap:5px;padding:7px 13px;background:linear-gradient(135deg,#f59e0b,#d97706);color:white;border-radius:9px;font-size:.78rem;font-weight:700;text-decoration:none;white-space:nowrap;box-shadow:0 2px 8px rgba(245,158,11,.3);">
                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                            إضافة واجب
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>
</div>

<style>
@media (max-width: 640px) {
    div[style*="grid-template-columns:1fr 1fr"] {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endsection
