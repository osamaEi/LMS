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

@if($errors->any())
<div style="background:#fef2f2;border:1px solid #fecaca;border-right:4px solid #ef4444;border-radius:12px;padding:14px 18px;margin-bottom:20px;">
    @foreach($errors->all() as $error)
        <div style="color:#b91c1c;font-size:13px;font-weight:600;">{{ $error }}</div>
    @endforeach
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
                <p style="color:rgba(255,255,255,.6);font-size:12px;margin:4px 0 0;">إضافة وإدارة الواجبات حسب المقرر</p>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
            <div style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:8px 18px;text-align:center;min-width:72px;">
                <div style="font-size:20px;font-weight:800;color:#86efac;line-height:1.1;">{{ $homeworks->count() }}</div>
                <div style="font-size:11px;color:rgba(255,255,255,.55);margin-top:2px;">إجمالي الواجبات</div>
            </div>
            @unless($subjects->isEmpty())
            <button type="button" onclick="openHwModal()"
                    style="display:inline-flex;align-items:center;gap:7px;padding:11px 20px;background:linear-gradient(135deg,#16a34a,#15803d);color:white;border:none;border-radius:12px;font-size:.85rem;font-weight:700;cursor:pointer;box-shadow:0 4px 14px rgba(22,163,74,.4);">
                <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                إضافة واجب جديد
            </button>
            @endunless
        </div>
    </div>
</div>

{{-- ══ Add homework MODAL ══ --}}
@unless($subjects->isEmpty())
<div id="hwModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(15,23,42,.55);align-items:flex-start;justify-content:center;padding:24px 12px;overflow-y:auto;">
  <div style="background:white;border-radius:18px;width:100%;max-width:640px;box-shadow:0 24px 60px rgba(0,0,0,.3);margin:auto;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:9px;padding:18px 24px;border-bottom:1px solid #f1f5f9;">
        <div style="display:flex;align-items:center;gap:9px;">
            <div style="width:32px;height:32px;background:linear-gradient(135deg,#16a34a,#15803d);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                <svg width="15" height="15" fill="white" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
            </div>
            <h2 style="font-size:.95rem;font-weight:800;color:#111827;margin:0;">إضافة واجب جديد</h2>
        </div>
        <button type="button" onclick="closeHwModal()" style="width:32px;height:32px;background:#f3f4f6;border:none;border-radius:8px;cursor:pointer;color:#374151;font-size:18px;">×</button>
    </div>

    <form action="{{ route('teacher.homework.store') }}" method="POST" enctype="multipart/form-data"
          style="display:grid;grid-template-columns:1fr 1fr;gap:14px;padding:22px 24px;">
        @csrf

        <div>
            <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:5px;">المقرر <span style="color:#ef4444;">*</span></label>
            <select name="subject_id" required
                    style="width:100%;padding:9px 12px;border:1.5px solid #d1d5db;border-radius:9px;font-size:.85rem;background:white;">
                <option value="">— اختر المقرر —</option>
                @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>
                    {{ $subject->name_ar ?: $subject->name_en }}@if($subject->code) ({{ $subject->code }})@endif
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:5px;">المجموعة (الكلاس)</label>
            <select name="class_id"
                    style="width:100%;padding:9px 12px;border:1.5px solid #d1d5db;border-radius:9px;font-size:.85rem;background:white;">
                <option value="">— كل المسجّلين بالمقرر —</option>
                @foreach($classes as $class)
                <option value="{{ $class->id }}" @selected(old('class_id') == $class->id)>
                    {{ $class->name }}@if($class->program) — {{ $class->program->name_ar }}@endif
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:5px;">عنوان الواجب (عربي)</label>
            <input type="text" name="title_ar" value="{{ old('title_ar') }}"
                   style="width:100%;padding:9px 12px;border:1.5px solid #d1d5db;border-radius:9px;font-size:.85rem;">
        </div>
        <div>
            <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:5px;">عنوان الواجب (إنجليزي)</label>
            <input type="text" name="title_en" value="{{ old('title_en') }}"
                   style="width:100%;padding:9px 12px;border:1.5px solid #d1d5db;border-radius:9px;font-size:.85rem;">
        </div>

        <div style="grid-column:1 / -1;">
            <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:5px;">وصف الواجب</label>
            <textarea name="description_ar" rows="3"
                      style="width:100%;padding:9px 12px;border:1.5px solid #d1d5db;border-radius:9px;font-size:.85rem;resize:vertical;">{{ old('description_ar') }}</textarea>
        </div>

        <div>
            <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:5px;">تاريخ التسليم</label>
            <input type="date" name="due_date" value="{{ old('due_date') }}"
                   style="width:100%;padding:9px 12px;border:1.5px solid #d1d5db;border-radius:9px;font-size:.85rem;">
        </div>
        <div>
            <label style="display:block;font-size:.78rem;font-weight:700;color:#374151;margin-bottom:5px;">ملف مرفق (اختياري)</label>
            <input type="file" name="file"
                   style="width:100%;padding:7px 12px;border:1.5px solid #d1d5db;border-radius:9px;font-size:.8rem;background:white;">
        </div>

        <div style="grid-column:1 / -1;display:flex;justify-content:flex-end;gap:8px;padding-top:4px;">
            <button type="button" onclick="closeHwModal()"
                    style="padding:9px 20px;background:#f3f4f6;color:#374151;border:none;border-radius:9px;font-size:.85rem;font-weight:700;cursor:pointer;">
                إلغاء
            </button>
            <button type="submit"
                    style="display:inline-flex;align-items:center;gap:6px;padding:9px 20px;background:linear-gradient(135deg,#16a34a,#15803d);color:white;border:none;border-radius:9px;font-size:.85rem;font-weight:700;cursor:pointer;box-shadow:0 2px 8px rgba(22,163,74,.3);">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                إضافة الواجب
            </button>
        </div>
    </form>
  </div>
</div>
@endunless

{{-- ══ Existing homework ══ --}}
<div>
    <div style="display:flex;align-items:center;gap:9px;margin-bottom:14px;">
        <div style="width:32px;height:32px;background:linear-gradient(135deg,#0071AA,#005a88);border-radius:9px;display:flex;align-items:center;justify-content:center;">
            <svg width="15" height="15" fill="white" viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        </div>
        <h2 style="font-size:.95rem;font-weight:800;color:#111827;margin:0;">الواجبات الحالية</h2>
        <span style="background:#0071AA;color:white;font-size:.72rem;font-weight:700;padding:2px 9px;border-radius:20px;">{{ $homeworks->count() }}</span>
    </div>

    @if($homeworks->isEmpty())
    <div style="background:white;border:1.5px dashed #d1d5db;border-radius:14px;padding:36px;text-align:center;">
        <p style="font-size:.85rem;color:#9ca3af;margin:0;">لا توجد واجبات بعد</p>
    </div>
    @else
    <div style="display:flex;flex-direction:column;gap:10px;">
        @foreach($homeworks as $homework)
        @php $subCount = $homework->submissions()->count(); @endphp
        <div style="background:white;border:1.5px solid #bbf7d0;border-radius:13px;overflow:hidden;">
            <div style="display:flex;align-items:stretch;">
                <div style="width:4px;flex-shrink:0;background:linear-gradient(180deg,#16a34a,#15803d);"></div>
                <div style="flex:1;padding:13px 15px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                        <div style="min-width:0;flex:1;">
                            <p style="font-size:.72rem;color:#6b7280;margin:0 0 3px;">
                                {{ $homework->subject->name_ar ?? $homework->program->name_ar ?? $homework->session->subject->name_ar ?? $homework->session->program->name_ar ?? '—' }}
                                @if($homework->programClass)
                                <span style="display:inline-block;background:#eff6ff;color:#0071AA;font-size:.66rem;font-weight:700;padding:1px 8px;border-radius:20px;margin-right:4px;">🏫 {{ $homework->programClass->name }}</span>
                                @endif
                            </p>
                            <p style="font-size:.86rem;font-weight:700;color:#111827;margin:0 0 2px;">
                                📋 {{ $homework->title_ar ?: $homework->title_en ?: 'واجب بدون عنوان' }}
                            </p>
                            <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                                @if($homework->due_date)
                                <span style="font-size:.72rem;color:#9ca3af;">التسليم: {{ $homework->due_date->format('Y/m/d') }}</span>
                                @endif
                                <span style="font-size:.72rem;color:#0071AA;font-weight:600;background:#eff6ff;padding:1px 7px;border-radius:20px;">{{ $subCount }} تسليم</span>
                            </div>
                        </div>
                        <div style="display:flex;gap:6px;flex-shrink:0;align-items:flex-start;">
                            <a href="{{ route('teacher.homework.show', $homework) }}"
                               style="display:inline-flex;align-items:center;gap:4px;padding:5px 11px;background:linear-gradient(135deg,#0071AA,#005a88);color:white;border-radius:7px;font-size:.72rem;font-weight:700;text-decoration:none;">
                                <svg width="10" height="10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zm0 12a5 5 0 110-10 5 5 0 010 10zm0-8a3 3 0 100 6 3 3 0 000-6z"/></svg>
                                عرض التسليمات
                            </a>
                            <form action="{{ route('teacher.homework.destroy', $homework) }}" method="POST"
                                  onsubmit="return confirm('حذف الواجب؟')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        style="display:inline-flex;align-items:center;gap:4px;padding:5px 11px;background:linear-gradient(135deg,#ef4444,#dc2626);color:white;border:none;border-radius:7px;font-size:.72rem;font-weight:700;cursor:pointer;">
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
</div>

<style>
@media (max-width: 640px) {
    form[action*="homework"] { grid-template-columns: 1fr !important; }
}
</style>

<script>
function openHwModal() { document.getElementById('hwModal').style.display = 'flex'; document.body.style.overflow = 'hidden'; }
function closeHwModal() { document.getElementById('hwModal').style.display = 'none'; document.body.style.overflow = ''; }
// Close on backdrop click
document.getElementById('hwModal')?.addEventListener('click', e => { if (e.target.id === 'hwModal') closeHwModal(); });
// Close on Escape
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeHwModal(); });
// Re-open automatically if the form had validation errors
@if($errors->any())
openHwModal();
@endif
</script>
@endsection
