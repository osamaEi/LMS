@extends('layouts.dashboard')
@section('title', $session->title_ar ?: $session->title_en ?: 'تفاصيل الجلسة')

@section('content')
<div style="direction:rtl;max-width:860px;margin:0 auto;">

{{-- Back --}}
<div style="margin-bottom:16px;">
    <a href="{{ route('student.my-program') }}"
       style="display:inline-flex;align-items:center;gap:6px;color:#0071AA;font-size:.85rem;font-weight:600;text-decoration:none;">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        العودة إلى البرنامج
    </a>
</div>

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

{{-- Hero card --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 55%,#0071AA 100%);border-radius:18px;padding:24px 28px;margin-bottom:20px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-40px;left:-40px;width:160px;height:160px;background:rgba(255,255,255,.04);border-radius:50%;pointer-events:none;"></div>
    <div style="position:relative;z-index:1;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div>
                <p style="color:rgba(255,255,255,.55);font-size:.75rem;margin:0 0 5px;">
                    جلسة #{{ $session->session_number ?? '—' }}
                    @if($session->scheduled_at)
                    · {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d H:i') }}
                    @endif
                </p>
                <h1 style="color:white;font-size:1.25rem;font-weight:800;margin:0 0 8px;">
                    {{ $session->title_ar ?: $session->title_en ?: 'جلسة بدون عنوان' }}
                </h1>
                @if($session->description_ar || $session->description_en)
                <p style="color:rgba(255,255,255,.7);font-size:.85rem;margin:0;">{{ $session->description_ar ?: $session->description_en }}</p>
                @endif
            </div>
            {{-- Status badge --}}
            @if(!is_null($session->started_at) && is_null($session->ended_at))
            <span style="background:#22c55e;color:white;font-size:.75rem;font-weight:700;padding:5px 13px;border-radius:20px;display:inline-flex;align-items:center;gap:5px;">
                <span style="width:7px;height:7px;border-radius:50%;background:white;display:inline-block;animation:pulse 2s infinite;"></span>
                مباشر الآن
            </span>
            @elseif(!is_null($session->ended_at))
            <span style="background:rgba(255,255,255,.15);color:rgba(255,255,255,.8);font-size:.75rem;font-weight:700;padding:5px 13px;border-radius:20px;">
                مكتملة
            </span>
            @endif
        </div>

        {{-- Stats row --}}
        <div style="display:flex;gap:16px;margin-top:16px;flex-wrap:wrap;">
            @if($attendance)
            <div style="background:rgba(255,255,255,.1);border-radius:10px;padding:8px 14px;display:flex;align-items:center;gap:7px;">
                <svg width="14" height="14" fill="none" stroke="{{ $attendance->attended ? '#86efac' : '#fca5a5' }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $attendance->attended ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/></svg>
                <span style="font-size:.78rem;color:{{ $attendance->attended ? '#86efac' : '#fca5a5' }};font-weight:600;">
                    {{ $attendance->attended ? 'حضرت هذه الجلسة' : 'لم تحضر هذه الجلسة' }}
                </span>
            </div>
            @endif
            @if($session->files->count())
            <div style="background:rgba(255,255,255,.1);border-radius:10px;padding:8px 14px;">
                <span style="font-size:.78rem;color:rgba(255,255,255,.8);">{{ $session->files->count() }} ملف مرفق</span>
            </div>
            @endif
            @if($session->homework)
            <div style="background:rgba(255,255,255,.1);border-radius:10px;padding:8px 14px;">
                <span style="font-size:.78rem;color:rgba(255,255,255,.8);">
                    واجب
                    @if($mySubmission) · <span style="color:#86efac;">سلّمت</span> @endif
                </span>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ===== Files Section ===== --}}
@if($session->files->count() > 0)
<div style="background:white;border-radius:16px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.05);margin-bottom:16px;">
    <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:9px;">
        <div style="width:32px;height:32px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);border-radius:9px;display:flex;align-items:center;justify-content:center;">
            <svg width="15" height="15" fill="white" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
        </div>
        <h2 style="font-size:.95rem;font-weight:800;color:#111827;margin:0;">ملفات الجلسة</h2>
        <span style="background:#8b5cf6;color:white;font-size:.72rem;font-weight:700;padding:2px 8px;border-radius:20px;">{{ $session->files->count() }}</span>
    </div>
    <div style="padding:14px 20px;display:flex;flex-direction:column;gap:8px;">
        @foreach($session->files as $file)
        <a href="{{ asset('storage/'.$file->file_path) }}" target="_blank"
           style="display:flex;align-items:center;gap:10px;padding:11px 14px;border:1.5px solid #e5e7eb;border-radius:11px;text-decoration:none;transition:border-color .15s;"
           onmouseover="this.style.borderColor='#8b5cf6'" onmouseout="this.style.borderColor='#e5e7eb'">
            <div style="width:36px;height:36px;background:#f3f0ff;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="16" height="16" fill="#7c3aed" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
            </div>
            <div style="min-width:0;flex:1;">
                <div style="font-size:.875rem;font-weight:700;color:#111827;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ $file->title ?? $file->file_name ?? 'ملف' }}
                </div>
                @if($file->description)
                <div style="font-size:.75rem;color:#9ca3af;margin-top:1px;">{{ $file->description }}</div>
                @endif
            </div>
            <svg width="14" height="14" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- ===== Homework Section ===== --}}
@if($session->homework)
@php $hw = $session->homework; @endphp
<div style="background:white;border-radius:16px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.05);margin-bottom:16px;">
    <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:9px;">
        <div style="width:32px;height:32px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:9px;display:flex;align-items:center;justify-content:center;">
            <svg width="15" height="15" fill="white" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        </div>
        <h2 style="font-size:.95rem;font-weight:800;color:#111827;margin:0;">الواجب</h2>
        @if($mySubmission)
        <span style="background:#dcfce7;color:#15803d;font-size:.72rem;font-weight:700;padding:2px 9px;border-radius:20px;display:inline-flex;align-items:center;gap:4px;">
            <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
            سلّمت
            @if($mySubmission->grade !== null)
            <span style="background:#16a34a;color:white;border-radius:20px;padding:0 6px;">{{ $mySubmission->grade }}/100</span>
            @endif
        </span>
        @endif
    </div>
    <div style="padding:16px 20px;">
        {{-- Title + due date --}}
        <h3 style="font-size:1rem;font-weight:800;color:#111827;margin:0 0 6px;">
            {{ $hw->title_ar ?: $hw->title_en ?: 'واجب بدون عنوان' }}
        </h3>
        @if($hw->due_date)
        @php $isOverdue = $hw->due_date->isPast(); @endphp
        <p style="font-size:.78rem;color:{{ $isOverdue ? '#dc2626' : '#9ca3af' }};margin:0 0 10px;">
            التسليم: {{ $hw->due_date->format('Y/m/d') }}
            @if($isOverdue)<span style="background:#fee2e2;color:#dc2626;border-radius:20px;padding:1px 7px;margin-right:4px;">متأخر</span>@endif
        </p>
        @endif

        {{-- Description --}}
        @if($hw->description_ar || $hw->description_en)
        <p style="font-size:.875rem;color:#374151;line-height:1.65;margin:0 0 12px;white-space:pre-line;">{{ $hw->description_ar ?: $hw->description_en }}</p>
        @endif

        {{-- Homework file --}}
        @if($hw->file_path)
        <div style="margin-bottom:14px;">
            <a href="{{ asset('storage/'.$hw->file_path) }}" target="_blank"
               style="display:inline-flex;align-items:center;gap:6px;padding:7px 13px;background:#fef3c7;border:1px solid #fde68a;border-radius:8px;color:#d97706;font-size:.8rem;font-weight:600;text-decoration:none;">
                <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                تحميل ملف الواجب ({{ $hw->file_name }})
            </a>
        </div>
        @endif

        {{-- Teacher feedback --}}
        @if($mySubmission && $mySubmission->feedback)
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;padding:11px 14px;margin-bottom:14px;font-size:.85rem;color:#15803d;">
            <strong>ملاحظة المدرس:</strong> {{ $mySubmission->feedback }}
        </div>
        @endif

        {{-- Existing submission --}}
        @if($mySubmission)
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:11px;padding:14px 16px;margin-bottom:12px;">
            <p style="font-size:.75rem;font-weight:700;color:#6b7280;margin:0 0 6px;">إجابتك المسلّمة</p>
            @if($mySubmission->content)
            <p style="font-size:.875rem;color:#374151;margin:0 0 8px;white-space:pre-line;">{{ $mySubmission->content }}</p>
            @endif
            @if($mySubmission->file_path)
            <a href="{{ asset('storage/'.$mySubmission->file_path) }}" target="_blank"
               style="display:inline-flex;align-items:center;gap:5px;padding:5px 11px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:7px;color:#0071AA;font-size:.78rem;font-weight:600;text-decoration:none;">
                <svg width="11" height="11" fill="currentColor" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                {{ $mySubmission->file_name }}
            </a>
            @endif
            <p style="font-size:.72rem;color:#9ca3af;margin:8px 0 0;">
                سُلِّم: {{ $mySubmission->submitted_at ? $mySubmission->submitted_at->format('Y/m/d H:i') : \Carbon\Carbon::parse($mySubmission->created_at)->format('Y/m/d H:i') }}
            </p>
        </div>

        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <button onclick="document.getElementById('submit-form').style.display=document.getElementById('submit-form').style.display==='none'?'block':'none'"
                    style="font-size:.8rem;font-weight:700;color:#d97706;background:none;border:none;cursor:pointer;padding:0;text-decoration:underline;">
                تعديل الإجابة
            </button>
            <span style="color:#d1d5db;">·</span>
            <form action="{{ route('student.homework.submission.delete', $mySubmission->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('حذف التسليم؟')">
                @csrf @method('DELETE')
                <button type="submit" style="font-size:.8rem;font-weight:700;color:#dc2626;background:none;border:none;cursor:pointer;padding:0;text-decoration:underline;">
                    حذف التسليم
                </button>
            </form>
        </div>

        @else
        <button onclick="document.getElementById('submit-form').style.display='block';this.style.display='none'"
                style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:linear-gradient(135deg,#f59e0b,#d97706);color:white;border:none;border-radius:10px;font-size:.875rem;font-weight:700;cursor:pointer;box-shadow:0 2px 10px rgba(245,158,11,.3);">
            <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
            تسليم الواجب
        </button>
        @endif

        {{-- Submit form --}}
        <div id="submit-form" style="display:none;margin-top:14px;">
            <form action="{{ route('student.homework.submit', $hw->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom:10px;">
                    <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:4px;">إجابتك <span style="color:#6b7280;font-weight:400;">(نص)</span></label>
                    <textarea name="content" rows="4" placeholder="اكتب حل الواجب هنا..."
                              style="width:100%;border:1.5px solid #e5e7eb;border-radius:9px;padding:9px 12px;font-size:.875rem;outline:none;resize:vertical;box-sizing:border-box;font-family:inherit;"
                              onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e5e7eb'">{{ $mySubmission->content ?? '' }}</textarea>
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
                    <button type="button"
                            onclick="document.getElementById('submit-form').style.display='none'"
                            style="padding:9px 16px;background:#f1f5f9;color:#374151;border:none;border-radius:9px;font-size:.875rem;font-weight:600;cursor:pointer;">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

</div>
@endsection
