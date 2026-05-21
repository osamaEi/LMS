@extends('layouts.dashboard')
@section('title', 'واجب الجلسة')

@section('content')
@php
    $hw          = $session->homework;
    $submissions = $hw ? $hw->submissions->sortByDesc('submitted_at') : collect();
    $entityName  = $session->subject->name_ar ?? $session->program->name_ar ?? '—';
    $backRoute   = $session->subject_id
        ? route('teacher.my-subjects.show', $session->subject_id)
        : ($session->program_id ? route('teacher.my-courses.show', $session->program_id) : '#');
    $dt = $session->scheduled_at ? \Carbon\Carbon::parse($session->scheduled_at) : null;
@endphp

<div style="direction:rtl;max-width:860px;margin:0 auto;">

{{-- Alerts --}}
@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-right:4px solid #22c55e;border-radius:10px;padding:12px 16px;margin-bottom:16px;color:#15803d;font-size:.875rem;font-weight:600;display:flex;align-items:center;gap:8px;">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif
@if($errors->any())
<div style="background:#fff1f2;border:1px solid #fecaca;border-right:4px solid #ef4444;border-radius:10px;padding:12px 16px;margin-bottom:16px;">
    <ul style="margin:0;padding-right:16px;color:#dc2626;font-size:.85rem;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

{{-- Header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
    <div>
        <div style="font-size:.8rem;color:#6b7280;margin-bottom:4px;">
            <a href="{{ $backRoute }}" style="color:#6b7280;text-decoration:none;">{{ $entityName }}</a>
            <span style="margin:0 6px;">›</span>
            <span>جلسة #{{ $session->session_number }}</span>
        </div>
        <h1 style="font-size:1.3rem;font-weight:800;color:#111827;margin:0;">واجب الجلسة</h1>
        @if($dt)
        <p style="font-size:.8rem;color:#6b7280;margin:4px 0 0;">
            {{ $dt->format('Y/m/d · H:i') }}
            @if($session->duration_minutes) · {{ $session->duration_minutes }} دقيقة @endif
        </p>
        @endif
    </div>
    <a href="{{ $backRoute }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.8rem;font-weight:600;color:#374151;text-decoration:none;background:white;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        رجوع
    </a>
</div>

{{-- ══ HOMEWORK CARD ══ --}}
<div style="background:white;border:1.5px solid #f1f5f9;border-radius:16px;overflow:hidden;margin-bottom:20px;">
    <div style="padding:14px 20px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:36px;height:36px;background:rgba(255,255,255,.2);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" fill="white" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            </div>
            <h2 style="color:white;font-size:1rem;font-weight:800;margin:0;">الواجب المنزلي</h2>
        </div>
        @if($hw)
        <div style="display:flex;gap:8px;">
            <button onclick="toggleEditForm()"
                    style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;background:rgba(255,255,255,.2);border:none;border-radius:7px;color:white;font-size:.78rem;font-weight:700;cursor:pointer;">
                <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                تعديل
            </button>
            <form action="{{ route('teacher.sessions.homework.destroy', $session) }}" method="POST" onsubmit="return confirm('حذف الواجب؟')">
                @csrf @method('DELETE')
                <button type="submit"
                        style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;background:rgba(239,68,68,.3);border:none;border-radius:7px;color:white;font-size:.78rem;font-weight:700;cursor:pointer;">
                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                    حذف
                </button>
            </form>
        </div>
        @endif
    </div>

    <div style="padding:20px 22px;">
        @if($hw)
        {{-- Existing homework display --}}
        <div id="hw-display">
            <h3 style="font-size:1rem;font-weight:800;color:#111827;margin:0 0 8px;">{{ $hw->title_ar ?: $hw->title_en ?: 'واجب بدون عنوان' }}</h3>
            @if($hw->description_ar || $hw->description_en)
            <p style="font-size:.875rem;color:#374151;line-height:1.65;margin:0 0 12px;white-space:pre-line;">{{ $hw->description_ar ?: $hw->description_en }}</p>
            @endif
            <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
                @if($hw->due_date)
                <span style="display:inline-flex;align-items:center;gap:5px;font-size:.8rem;color:#6b7280;background:#f9fafb;border:1px solid #e5e7eb;padding:5px 10px;border-radius:7px;">
                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/></svg>
                    موعد التسليم: <strong>{{ $hw->due_date->format('Y/m/d') }}</strong>
                </span>
                @endif
                @if($hw->file_path)
                <a href="{{ asset('storage/'.$hw->file_path) }}" target="_blank"
                   style="display:inline-flex;align-items:center;gap:5px;font-size:.8rem;color:#0071AA;background:#eff6ff;border:1px solid #bfdbfe;padding:5px 10px;border-radius:7px;text-decoration:none;font-weight:600;">
                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"/></svg>
                    {{ $hw->file_name }}
                </a>
                @endif
                <span style="display:inline-flex;align-items:center;gap:5px;font-size:.8rem;color:#6b7280;background:#f9fafb;border:1px solid #e5e7eb;padding:5px 10px;border-radius:7px;">
                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                    {{ $submissions->count() }} تسليم
                </span>
            </div>
        </div>

        {{-- Edit form --}}
        <div id="hw-edit" style="display:none;margin-top:16px;">
            <form action="{{ route('teacher.sessions.homework.update', $session) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                    <div>
                        <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:4px;">العنوان (عربي)</label>
                        <input type="text" name="title_ar" value="{{ old('title_ar', $hw->title_ar) }}"
                               style="width:100%;border:1.5px solid #e5e7eb;border-radius:8px;padding:8px 11px;font-size:.85rem;outline:none;box-sizing:border-box;"
                               onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e5e7eb'">
                    </div>
                    <div>
                        <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:4px;">موعد التسليم</label>
                        <input type="date" name="due_date" value="{{ old('due_date', $hw->due_date?->format('Y-m-d')) }}"
                               style="width:100%;border:1.5px solid #e5e7eb;border-radius:8px;padding:8px 11px;font-size:.85rem;outline:none;box-sizing:border-box;"
                               onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e5e7eb'">
                    </div>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:4px;">وصف الواجب</label>
                    <textarea name="description_ar" rows="3"
                              style="width:100%;border:1.5px solid #e5e7eb;border-radius:8px;padding:8px 11px;font-size:.85rem;outline:none;resize:vertical;box-sizing:border-box;"
                              onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e5e7eb'">{{ old('description_ar', $hw->description_ar) }}</textarea>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:4px;">ملف جديد (اختياري)</label>
                    <input type="file" name="file"
                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:8px;padding:7px 11px;font-size:.85rem;background:white;box-sizing:border-box;">
                    @if($hw->file_path)
                    <p style="font-size:.75rem;color:#6b7280;margin:3px 0 0;">الملف الحالي: {{ $hw->file_name }} — ارفع ملفاً جديداً للاستبدال</p>
                    @endif
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="submit"
                            style="padding:8px 18px;background:linear-gradient(135deg,#f59e0b,#d97706);color:white;border:none;border-radius:8px;font-size:.85rem;font-weight:700;cursor:pointer;">
                        حفظ التعديلات
                    </button>
                    <button type="button" onclick="toggleEditForm()"
                            style="padding:8px 16px;background:#f1f5f9;color:#374151;border:none;border-radius:8px;font-size:.85rem;font-weight:600;cursor:pointer;">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>

        @else
        {{-- Add homework form --}}
        <form action="{{ route('teacher.sessions.homework.store', $session) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                <div>
                    <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:4px;">عنوان الواجب <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="title_ar" value="{{ old('title_ar') }}" required
                           placeholder="مثال: حل تمارين الفصل الثالث"
                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:8px;padding:9px 12px;font-size:.875rem;outline:none;box-sizing:border-box;"
                           onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
                <div>
                    <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:4px;">موعد التسليم</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                           style="width:100%;border:1.5px solid #e5e7eb;border-radius:8px;padding:9px 12px;font-size:.875rem;outline:none;box-sizing:border-box;"
                           onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:4px;">وصف الواجب</label>
                <textarea name="description_ar" rows="4" value="{{ old('description_ar') }}"
                          placeholder="اكتب تفاصيل الواجب والمطلوب من الطلاب..."
                          style="width:100%;border:1.5px solid #e5e7eb;border-radius:8px;padding:9px 12px;font-size:.875rem;outline:none;resize:vertical;box-sizing:border-box;"
                          onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#e5e7eb'">{{ old('description_ar') }}</textarea>
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:4px;">إرفاق ملف (اختياري)</label>
                <input type="file" name="file"
                       style="width:100%;border:1.5px solid #e5e7eb;border-radius:8px;padding:8px 12px;font-size:.875rem;background:white;box-sizing:border-box;">
                <p style="font-size:.75rem;color:#9ca3af;margin:3px 0 0;">PDF، Word، صور — حد أقصى 20MB</p>
            </div>
            <button type="submit"
                    style="display:inline-flex;align-items:center;gap:8px;padding:10px 22px;background:linear-gradient(135deg,#f59e0b,#d97706);color:white;border:none;border-radius:9px;font-size:.875rem;font-weight:700;cursor:pointer;box-shadow:0 3px 12px rgba(245,158,11,.35);">
                <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                إضافة الواجب
            </button>
        </form>
        @endif
    </div>
</div>

{{-- ══ SUBMISSIONS ══ --}}
@if($hw)
<div style="background:white;border:1.5px solid #f1f5f9;border-radius:16px;overflow:hidden;">
    <div style="padding:14px 20px;background:linear-gradient(135deg,rgba(0,113,170,.06),rgba(0,113,170,.02));border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:8px;height:8px;border-radius:50%;background:#0071AA;"></div>
            <h2 style="font-size:.95rem;font-weight:800;color:#111827;margin:0;">حلول الطلاب</h2>
        </div>
        <span style="font-size:.8rem;color:#6b7280;background:#f1f5f9;padding:4px 12px;border-radius:20px;font-weight:600;">
            {{ $submissions->count() }} تسليم
        </span>
    </div>

    @if($submissions->isEmpty())
    <div style="padding:48px;text-align:center;">
        <div style="width:52px;height:52px;background:#f9fafb;border-radius:13px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
            <svg width="22" height="22" fill="none" stroke="#9ca3af" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <p style="font-size:.875rem;font-weight:700;color:#374151;margin:0 0 4px;">لا توجد تسليمات بعد</p>
        <p style="font-size:.8rem;color:#9ca3af;margin:0;">سيظهر هنا حلول الطلاب بعد تسليمهم للواجب</p>
    </div>
    @else
    <div style="display:flex;flex-direction:column;">
        @foreach($submissions as $sub)
        @php
            $hasGrade = $sub->grade !== null;
        @endphp
        <div style="padding:16px 20px;border-bottom:1px solid #f9fafb;" id="sub-{{ $sub->id }}">
            <div style="display:flex;align-items:flex-start;gap:12px;">
                {{-- Avatar --}}
                <div style="width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#0071AA,#005a88);display:flex;align-items:center;justify-content:center;color:white;font-size:15px;font-weight:800;flex-shrink:0;">
                    {{ mb_substr($sub->student->name ?? '?', 0, 1) }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:6px;margin-bottom:6px;">
                        <div>
                            <p style="font-weight:800;color:#111827;margin:0;font-size:.9rem;">{{ $sub->student->name ?? 'غير معروف' }}</p>
                            <p style="font-size:.75rem;color:#9ca3af;margin:1px 0 0;">
                                {{ $sub->submitted_at ? $sub->submitted_at->format('Y/m/d H:i') : \Carbon\Carbon::parse($sub->created_at)->format('Y/m/d H:i') }}
                            </p>
                        </div>
                        @if($hasGrade)
                        <span style="background:#dcfce7;color:#15803d;font-size:.78rem;font-weight:700;padding:3px 10px;border-radius:20px;">
                            درجة: {{ $sub->grade }}
                        </span>
                        @endif
                    </div>

                    {{-- Content --}}
                    @if($sub->content)
                    <div style="background:#f9fafb;border-radius:9px;padding:10px 13px;margin-bottom:8px;">
                        <p style="font-size:.85rem;color:#374151;margin:0;line-height:1.6;white-space:pre-line;">{{ $sub->content }}</p>
                    </div>
                    @endif

                    {{-- File --}}
                    @if($sub->file_path)
                    <div style="margin-bottom:8px;">
                        <a href="{{ asset('storage/'.$sub->file_path) }}" target="_blank"
                           style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:7px;color:#0071AA;font-size:.8rem;font-weight:600;text-decoration:none;">
                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"/></svg>
                            {{ $sub->file_name }}
                        </a>
                    </div>
                    @endif

                    {{-- Grade + Feedback form --}}
                    <div>
                        <button onclick="toggleFeedback({{ $sub->id }})"
                                style="font-size:.78rem;font-weight:700;color:#0071AA;background:none;border:none;cursor:pointer;padding:0;text-decoration:underline;">
                            {{ $hasGrade ? 'تعديل التقييم والملاحظات' : '+ إضافة درجة وملاحظة' }}
                        </button>
                        <div id="feedback-{{ $sub->id }}" style="display:none;margin-top:10px;">
                            <form action="{{ route('teacher.sessions.homework.grade', [$session, $sub->id]) }}" method="POST">
                                @csrf @method('PUT')
                                <div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
                                    <div>
                                        <label style="display:block;font-size:.72rem;font-weight:700;color:#374151;margin-bottom:3px;">الدرجة</label>
                                        <input type="number" name="grade" value="{{ $sub->grade }}" min="0" max="100" placeholder="0–100"
                                               style="width:80px;border:1.5px solid #e5e7eb;border-radius:7px;padding:6px 9px;font-size:.85rem;outline:none;"
                                               onfocus="this.style.borderColor='#0071AA'" onblur="this.style.borderColor='#e5e7eb'">
                                    </div>
                                    <div style="flex:1;min-width:160px;">
                                        <label style="display:block;font-size:.72rem;font-weight:700;color:#374151;margin-bottom:3px;">ملاحظة للطالب</label>
                                        <input type="text" name="feedback" value="{{ $sub->feedback }}" placeholder="أحسنت، يمكن تحسين..."
                                               style="width:100%;border:1.5px solid #e5e7eb;border-radius:7px;padding:6px 9px;font-size:.85rem;outline:none;box-sizing:border-box;"
                                               onfocus="this.style.borderColor='#0071AA'" onblur="this.style.borderColor='#e5e7eb'">
                                    </div>
                                    <button type="submit"
                                            style="padding:7px 14px;background:linear-gradient(135deg,#0071AA,#005a88);color:white;border:none;border-radius:7px;font-size:.8rem;font-weight:700;cursor:pointer;white-space:nowrap;">
                                        حفظ
                                    </button>
                                </div>
                            </form>
                        </div>
                        @if($sub->feedback)
                        <p style="font-size:.78rem;color:#6b7280;margin:5px 0 0;font-style:italic;">ملاحظتك: {{ $sub->feedback }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endif

</div>

<script>
function toggleEditForm() {
    const display = document.getElementById('hw-display');
    const edit    = document.getElementById('hw-edit');
    if (!display || !edit) return;
    const isHidden = edit.style.display === 'none';
    edit.style.display    = isHidden ? 'block' : 'none';
    display.style.display = isHidden ? 'none' : 'block';
}
function toggleFeedback(id) {
    const el = document.getElementById('feedback-' + id);
    if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>
@endsection
