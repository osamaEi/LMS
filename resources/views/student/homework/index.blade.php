@extends('layouts.dashboard')
@section('title', 'الواجبات المنزلية')

@php
    $submitted = $homeworks->filter(fn($h) => isset($mySubmissions[$h->id]))->count();
    $pending   = $homeworks->count() - $submitted;
@endphp

@section('content')
<div style="direction:rtl;max-width:1100px;margin:0 auto;">

{{-- Alerts --}}
@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-right:4px solid #22c55e;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#15803d;font-size:.875rem;font-weight:600;display:flex;align-items:center;gap:8px;">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif
@if($errors->any())
<div style="background:#fff1f2;border-right:4px solid #ef4444;border-radius:10px;padding:12px 16px;margin-bottom:16px;">
    <ul style="margin:0;padding-right:16px;color:#dc2626;font-size:.85rem;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

{{-- Hero --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 50%,#0071AA 100%);border-radius:22px;padding:32px 36px;margin-bottom:26px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-50px;left:-50px;width:220px;height:220px;background:rgba(255,255,255,.06);border-radius:50%;pointer-events:none;"></div>
    <div style="position:relative;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:18px;">
        <div style="display:flex;align-items:center;gap:16px;">
            <div style="width:58px;height:58px;background:rgba(255,255,255,.18);border-radius:15px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="28" height="28" fill="white" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            </div>
            <div>
                <h1 style="color:white;font-size:1.6rem;font-weight:800;margin:0;">الواجبات المنزلية</h1>
                <p style="color:rgba(255,255,255,.7);font-size:.92rem;margin:5px 0 0;">جميع الواجبات المرتبطة بجلساتك</p>
            </div>
        </div>
        <div style="display:flex;gap:12px;">
            @foreach([[$homeworks->count(),'إجمالي'],[$submitted,'سلّمت'],[$pending,'متبقي']] as [$v,$l])
            <div style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.12);border-radius:14px;padding:11px 20px;text-align:center;min-width:72px;">
                <div style="font-size:24px;font-weight:900;color:white;line-height:1.1;">{{ $v }}</div>
                <div style="font-size:12px;color:rgba(255,255,255,.7);margin-top:2px;">{{ $l }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@if($homeworks->isEmpty())
<div style="background:white;border:1.5px solid #f1f5f9;border-radius:16px;padding:48px;text-align:center;">
    <div style="width:64px;height:64px;background:#e0f2fe;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
        <svg width="28" height="28" fill="#0071AA" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
    </div>
    <p style="font-size:.9rem;font-weight:700;color:#374151;margin:0 0 4px;">لا توجد واجبات حتى الآن</p>
    <p style="font-size:.8rem;color:#9ca3af;margin:0;">ستظهر هنا الواجبات بعد إضافتها من المدرس</p>
</div>
@else
<div style="display:flex;flex-direction:column;gap:18px;">
    @foreach($homeworks as $hw)
    @php
        $sub        = $mySubmissions[$hw->id] ?? null;
        $isOverdue  = $hw->due_date && $hw->due_date->isPast();
        $isDueSoon  = $hw->due_date && !$isOverdue && $hw->due_date->diffInDays(now()) <= 3;
        $borderColor = $sub ? '#86efac' : ($isOverdue ? '#fca5a5' : ($isDueSoon ? '#fde68a' : '#e5e7eb'));
        $entityName  = $hw->session->subject->name_ar ?? $hw->session->program->name_ar ?? '—';
    @endphp
    <div style="background:white;border:1.5px solid {{ $borderColor }};border-radius:16px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.05);">

        {{-- Status stripe --}}
        <div style="height:5px;background:{{ $sub ? 'linear-gradient(90deg,#22c55e,#16a34a)' : ($isOverdue ? '#ef4444' : ($isDueSoon ? '#f59e0b' : 'linear-gradient(90deg,#0071AA,#004d77)')) }};"></div>

        <div style="padding:22px 24px;">
            {{-- Top row --}}
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:10px;">
                <div>
                    <div style="display:flex;align-items:center;gap:6px;font-size:.75rem;color:#6b7280;margin-bottom:5px;flex-wrap:wrap;">
                        <span>{{ $entityName }}</span>
                        <span>·</span>
                        <span>{{ $hw->session->title ?? ('جلسة #'.$hw->session->session_number) }}</span>
                        @if($hw->due_date)
                        <span style="font-weight:700;color:{{ $isOverdue ? '#dc2626' : ($isDueSoon ? '#d97706' : '#6b7280') }};">
                            · التسليم: {{ $hw->due_date->format('Y/m/d') }}
                            @if($isOverdue) <span style="background:#fee2e2;color:#dc2626;border-radius:20px;padding:1px 7px;">متأخر</span>
                            @elseif($isDueSoon) <span style="background:#fef3c7;color:#d97706;border-radius:20px;padding:1px 7px;">قريباً</span>
                            @endif
                        </span>
                        @endif
                    </div>
                    <h3 style="font-size:1.1rem;font-weight:800;color:#111827;margin:0;">
                        {{ $hw->title_ar ?: $hw->title_en ?: 'واجب بدون عنوان' }}
                    </h3>
                </div>

                @if($sub)
                <span style="display:inline-flex;align-items:center;gap:5px;background:#dcfce7;color:#15803d;font-size:.75rem;font-weight:700;padding:4px 12px;border-radius:20px;white-space:nowrap;flex-shrink:0;">
                    <svg width="11" height="11" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                    تم التسليم
                    @if($sub->grade !== null)
                    <span style="background:#16a34a;color:white;border-radius:20px;padding:1px 8px;">{{ $sub->grade }}/100</span>
                    @endif
                </span>
                @endif
            </div>

            {{-- Description --}}
            @if($hw->description_ar || $hw->description_en)
            <p style="font-size:.85rem;color:#374151;line-height:1.65;margin:0 0 12px;white-space:pre-line;">{{ $hw->description_ar ?: $hw->description_en }}</p>
            @endif

            {{-- Homework file --}}
            @if($hw->file_path)
            <div style="margin-bottom:12px;">
                <a href="{{ asset('storage/'.$hw->file_path) }}" target="_blank"
                   style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#e0f2fe;border:1px solid #bae6fd;border-radius:8px;color:#0071AA;font-size:.85rem;font-weight:600;text-decoration:none;">
                    <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                    تحميل ملف الواجب ({{ $hw->file_name }})
                </a>
            </div>
            @endif

            {{-- Teacher feedback on submission --}}
            @if($sub && $sub->feedback)
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 13px;margin-bottom:12px;font-size:.83rem;color:#15803d;">
                <strong>ملاحظة المدرس:</strong> {{ $sub->feedback }}
            </div>
            @endif

            {{-- Submission area --}}
            @if($sub)
            {{-- Already submitted – show what was sent + option to re-submit --}}
            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:13px 15px;margin-bottom:10px;">
                <p style="font-size:.78rem;font-weight:700;color:#6b7280;margin:0 0 6px;">إجابتك المسلّمة</p>
                @if($sub->content)
                <p style="font-size:.85rem;color:#374151;margin:0 0 8px;white-space:pre-line;">{{ $sub->content }}</p>
                @endif
                @if($sub->file_path)
                <a href="{{ asset('storage/'.$sub->file_path) }}" target="_blank"
                   style="display:inline-flex;align-items:center;gap:5px;padding:5px 11px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;color:#0071AA;font-size:.78rem;font-weight:600;text-decoration:none;">
                    <svg width="11" height="11" fill="currentColor" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                    {{ $sub->file_name }}
                </a>
                @endif
                <p style="font-size:.72rem;color:#9ca3af;margin:8px 0 0;">
                    سُلِّم: {{ $sub->submitted_at ? $sub->submitted_at->format('Y/m/d H:i') : \Carbon\Carbon::parse($sub->created_at)->format('Y/m/d H:i') }}
                </p>
            </div>

            {{-- Re-submit toggle --}}
            <div>
                <button onclick="toggleSubmit('{{ $hw->id }}')"
                        style="font-size:.82rem;font-weight:700;color:#0071AA;background:none;border:none;cursor:pointer;padding:0;text-decoration:underline;">
                    تعديل الإجابة
                </button>
                &nbsp;·&nbsp;
                <form action="{{ route('student.homework.submission.delete', $sub->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('حذف التسليم؟')">
                    @csrf @method('DELETE')
                    <button type="submit" style="font-size:.78rem;font-weight:700;color:#dc2626;background:none;border:none;cursor:pointer;padding:0;text-decoration:underline;">
                        حذف التسليم
                    </button>
                </form>
            </div>

            @else
            {{-- No submission yet – show submit button --}}
            <button onclick="toggleSubmit('{{ $hw->id }}')"
                    style="display:inline-flex;align-items:center;gap:7px;padding:11px 22px;background:linear-gradient(135deg,#0071AA,#004d77);color:white;border:none;border-radius:10px;font-size:.9rem;font-weight:700;cursor:pointer;box-shadow:0 2px 10px rgba(0,113,170,.3);">
                <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                تسليم الواجب
            </button>
            @endif

            {{-- Submit / re-submit form --}}
            <div id="submit-{{ $hw->id }}" style="display:none;margin-top:14px;">
                <form action="{{ route('student.homework.submit', $hw->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div style="margin-bottom:10px;">
                        <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:4px;">إجابتك <span style="color:#6b7280;font-weight:400;">(نص)</span></label>
                        <textarea name="content" rows="4" placeholder="اكتب حل الواجب هنا..."
                                  style="width:100%;border:1.5px solid #e5e7eb;border-radius:9px;padding:9px 12px;font-size:.875rem;outline:none;resize:vertical;box-sizing:border-box;font-family:inherit;"
                                  onfocus="this.style.borderColor='#0071AA'" onblur="this.style.borderColor='#e5e7eb'">{{ $sub->content ?? '' }}</textarea>
                    </div>
                    <div style="margin-bottom:12px;">
                        <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:4px;">أو ارفع ملف</label>
                        <input type="file" name="file"
                               style="width:100%;border:1.5px solid #e5e7eb;border-radius:9px;padding:7px 12px;font-size:.875rem;background:white;box-sizing:border-box;">
                        <p style="font-size:.72rem;color:#9ca3af;margin:3px 0 0;">PDF، Word، صور — حد أقصى 20MB</p>
                    </div>
                    <div style="display:flex;gap:8px;">
                        <button type="submit"
                                style="padding:9px 20px;background:linear-gradient(135deg,#16a34a,#15803d);color:white;border:none;border-radius:9px;font-size:.875rem;font-weight:700;cursor:pointer;">
                            تأكيد التسليم
                        </button>
                        <button type="button" onclick="toggleSubmit('{{ $hw->id }}')"
                                style="padding:9px 16px;background:#f1f5f9;color:#374151;border:none;border-radius:9px;font-size:.875rem;font-weight:600;cursor:pointer;">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

</div>

<script>
function toggleSubmit(id) {
    const el = document.getElementById('submit-' + id);
    if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>
@endsection
