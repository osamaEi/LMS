@extends('layouts.dashboard')
@section('title', 'تسليمات الواجب')

@section('content')
@php
    $entityName = $homework->subject->name_ar
        ?? $homework->program->name_ar
        ?? $homework->session->subject->name_ar
        ?? $homework->session->program->name_ar
        ?? '—';
@endphp
<div style="direction:rtl;max-width:1000px;margin:0 auto;">

{{-- Alert --}}
@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-right:4px solid #22c55e;border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span style="color:#15803d;font-size:14px;font-weight:600;">{{ session('success') }}</span>
</div>
@endif

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;border-right:4px solid #ef4444;border-radius:12px;padding:14px 18px;margin-bottom:20px;">
    @foreach($errors->all() as $error)
        <div style="color:#b91c1c;font-size:13px;font-weight:600;">{{ $error }}</div>
    @endforeach
</div>
@endif

{{-- Back link --}}
<a href="{{ route('teacher.homework.index') }}"
   style="display:inline-flex;align-items:center;gap:5px;font-size:.8rem;color:#6b7280;text-decoration:none;margin-bottom:14px;">
    <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
    رجوع للواجبات
</a>

{{-- Hero --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 55%,#0071AA 100%);border-radius:20px;padding:24px 28px;margin-bottom:24px;">
    <p style="color:rgba(255,255,255,.6);font-size:12px;margin:0 0 5px;">{{ $entityName }}</p>
    <h1 style="color:white;font-size:20px;font-weight:800;margin:0 0 8px;">
        📋 {{ $homework->title_ar ?: $homework->title_en ?: 'واجب بدون عنوان' }}
    </h1>
    @if($homework->description_ar || $homework->description_en)
    <p style="color:rgba(255,255,255,.75);font-size:13px;margin:0 0 10px;line-height:1.6;">{{ $homework->description_ar ?: $homework->description_en }}</p>
    @endif
    <div style="display:flex;gap:14px;flex-wrap:wrap;align-items:center;">
        @if($homework->due_date)
        <span style="font-size:.78rem;color:rgba(255,255,255,.7);">التسليم: {{ $homework->due_date->format('Y/m/d') }}</span>
        @endif
        <span style="font-size:.78rem;color:#86efac;font-weight:700;">{{ $submissions->count() }} تسليم</span>
        @if($homework->file_url)
        <a href="{{ $homework->file_url }}" target="_blank"
           style="display:inline-flex;align-items:center;gap:5px;font-size:.78rem;color:#fff;background:rgba(255,255,255,.15);padding:4px 12px;border-radius:20px;text-decoration:none;">
            <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
            ملف الواجب
        </a>
        @endif
    </div>
</div>

{{-- Submissions --}}
<div style="display:flex;align-items:center;gap:9px;margin-bottom:14px;">
    <h2 style="font-size:.95rem;font-weight:800;color:#111827;margin:0;">التسليمات</h2>
    <span style="background:#0071AA;color:white;font-size:.72rem;font-weight:700;padding:2px 9px;border-radius:20px;">{{ $submissions->count() }}</span>
</div>

@if($submissions->isEmpty())
<div style="background:white;border:1.5px dashed #d1d5db;border-radius:14px;padding:40px;text-align:center;">
    <p style="font-size:.9rem;color:#9ca3af;margin:0;">لا توجد تسليمات بعد</p>
</div>
@else
<div style="display:flex;flex-direction:column;gap:12px;">
    @foreach($submissions as $sub)
    @php $fileUrl = $sub->file_path ? (filter_var($sub->file_path, FILTER_VALIDATE_URL) ? $sub->file_path : asset('storage/'.$sub->file_path)) : null; @endphp
    <div style="background:white;border:1.5px solid #e5e7eb;border-radius:14px;overflow:hidden;">
        <div style="display:flex;align-items:stretch;">
            <div style="width:4px;flex-shrink:0;background:{{ $sub->grade !== null ? 'linear-gradient(180deg,#16a34a,#15803d)' : '#0071AA' }};"></div>
            <div style="flex:1;padding:15px 18px;">
                {{-- Student header --}}
                <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#0071AA,#005a88);display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:.9rem;flex-shrink:0;">
                            {{ mb_substr($sub->student->name ?? '؟', 0, 1) }}
                        </div>
                        <div>
                            <p style="font-size:.85rem;font-weight:700;color:#111827;margin:0;">{{ $sub->student->name ?? 'طالب' }}</p>
                            <p style="font-size:.7rem;color:#9ca3af;margin:1px 0 0;">
                                @if($sub->student->student_code)<span dir="ltr">{{ $sub->student->student_code }}</span> · @endif
                                {{ $sub->submitted_at?->format('Y/m/d H:i') ?? '—' }}
                            </p>
                        </div>
                    </div>
                    @if($sub->grade !== null)
                    <span style="font-size:.78rem;font-weight:800;color:#16a34a;background:#f0fdf4;padding:4px 12px;border-radius:20px;">{{ $sub->grade }}/{{ $sub->max_grade ?? 100 }}</span>
                    @else
                    <span style="font-size:.72rem;font-weight:600;color:#d97706;background:#fffbeb;padding:4px 12px;border-radius:20px;">لم يُقيّم</span>
                    @endif
                </div>

                {{-- Content --}}
                @if($sub->content)
                <div style="background:#f9fafb;border-radius:10px;padding:10px 13px;margin-bottom:10px;">
                    <p style="font-size:.82rem;color:#374151;margin:0;white-space:pre-wrap;line-height:1.6;">{{ $sub->content }}</p>
                </div>
                @endif

                @if($fileUrl)
                <a href="{{ $fileUrl }}" target="_blank"
                   style="display:inline-flex;align-items:center;gap:6px;font-size:.78rem;color:#0071AA;font-weight:600;text-decoration:none;margin-bottom:10px;">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6z"/></svg>
                    {{ $sub->file_name ?: 'ملف مرفق' }}
                </a>
                @endif

                {{-- Grade form --}}
                <form action="{{ route('teacher.homework.grade', [$homework, $sub->id]) }}" method="POST"
                      style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;border-top:1px solid #f3f4f6;padding-top:10px;">
                    @csrf @method('PUT')
                    <div style="width:75px;">
                        <label style="display:block;font-size:.7rem;font-weight:700;color:#374151;margin-bottom:3px;">الدرجة</label>
                        <input type="number" name="grade" min="0" value="{{ $sub->grade }}" placeholder="مثال 4"
                               style="width:100%;padding:7px 10px;border:1.5px solid #d1d5db;border-radius:8px;font-size:.82rem;">
                    </div>
                    <div style="font-size:1.1rem;font-weight:700;color:#9ca3af;padding-bottom:6px;">/</div>
                    <div style="width:75px;">
                        <label style="display:block;font-size:.7rem;font-weight:700;color:#374151;margin-bottom:3px;">من</label>
                        <input type="number" name="max_grade" min="1" required value="{{ $sub->max_grade ?? 100 }}" placeholder="مثال 5"
                               style="width:100%;padding:7px 10px;border:1.5px solid #d1d5db;border-radius:8px;font-size:.82rem;">
                    </div>
                    <div style="flex:1;min-width:180px;">
                        <label style="display:block;font-size:.7rem;font-weight:700;color:#374151;margin-bottom:3px;">ملاحظات</label>
                        <input type="text" name="feedback" value="{{ $sub->feedback }}" maxlength="500" placeholder="ملاحظات للطالب (اختياري)"
                               style="width:100%;padding:7px 10px;border:1.5px solid #d1d5db;border-radius:8px;font-size:.82rem;">
                    </div>
                    <button type="submit"
                            style="padding:8px 16px;background:linear-gradient(135deg,#16a34a,#15803d);color:white;border:none;border-radius:8px;font-size:.8rem;font-weight:700;cursor:pointer;">
                        حفظ التقييم
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
</div>
@endsection
