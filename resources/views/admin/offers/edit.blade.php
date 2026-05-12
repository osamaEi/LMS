@extends('layouts.dashboard')

@section('title', 'تعديل العرض: ' . $offer->title_ar)

@push('styles')
<style>
    .form-page { max-width:800px; margin:0 auto; }
    .form-hero { background:linear-gradient(135deg,#0071AA 0%,#005a88 100%); border-radius:24px; padding:1.75rem 2rem; margin-bottom:1.75rem; position:relative; overflow:hidden; }
    .form-hero::before { content:''; position:absolute; inset:0; background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); animation:slidePattern 25s linear infinite; }
    @keyframes slidePattern { 0%{transform:translateX(0)} 100%{transform:translateX(-60px)} }
    .f-card { background:#fff; border-radius:20px; padding:1.75rem; box-shadow:0 1px 3px rgba(0,0,0,.05); margin-bottom:1.25rem; }
    .dark .f-card { background:#1f2937; }
    .f-section-title { font-size:.8rem; font-weight:800; color:#6b7280; text-transform:uppercase; letter-spacing:.8px; margin-bottom:1.1rem; display:flex; align-items:center; gap:.5rem; }
    .f-section-title::after { content:''; flex:1; height:1px; background:#f1f5f9; }
    .dark .f-section-title::after { background:#374151; }
    .f-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media(max-width:640px) { .f-grid { grid-template-columns:1fr; } }
    .f-group { margin-bottom:1rem; }
    .f-group:last-child { margin-bottom:0; }
    .f-label { display:block; font-size:.82rem; font-weight:700; color:#374151; margin-bottom:.45rem; }
    .dark .f-label { color:#d1d5db; }
    .f-required { color:#ef4444; margin-right:.2rem; }
    .f-input, .f-select, .f-textarea { width:100%; padding:.7rem 1rem; border:2px solid #e5e7eb; border-radius:12px; font-size:.88rem; color:#374151; background:#fff; transition:border .2s, box-shadow .2s; box-sizing:border-box; }
    .dark .f-input, .dark .f-select, .dark .f-textarea { background:#374151; border-color:#4b5563; color:#f9fafb; }
    .f-input:focus, .f-select:focus, .f-textarea:focus { outline:none; border-color:#0071AA; box-shadow:0 0 0 3px rgba(0,113,170,.1); }
    .f-textarea { resize:vertical; min-height:90px; }
    .f-hint { font-size:.73rem; color:#9ca3af; margin-top:.3rem; }
    .f-radio-group { display:flex; gap:.75rem; }
    .f-radio-option { flex:1; display:flex; align-items:center; gap:.6rem; padding:.7rem 1rem; border:2px solid #e5e7eb; border-radius:12px; cursor:pointer; transition:all .2s; }
    .f-radio-option:has(input:checked) { border-color:#0071AA; background:rgba(0,113,170,.06); }
    .dark .f-radio-option { border-color:#4b5563; }
    .f-radio-option input { accent-color:#0071AA; width:16px; height:16px; }
    .f-radio-lbl { font-size:.85rem; font-weight:700; color:#374151; }
    .dark .f-radio-lbl { color:#d1d5db; }
    .upload-zone { border:2px dashed #d1d5db; border-radius:14px; padding:1.25rem; text-align:center; cursor:pointer; transition:all .2s; position:relative; }
    .upload-zone:hover { border-color:#0071AA; background:rgba(0,113,170,.03); }
    .upload-zone input { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
    .f-btn-row { display:flex; gap:.75rem; justify-content:flex-end; padding-top:.5rem; }
    .f-btn-submit { display:inline-flex; align-items:center; gap:.5rem; padding:.8rem 1.75rem; border-radius:12px; background:linear-gradient(135deg,#0071AA,#005a88); color:#fff; font-weight:800; font-size:.9rem; border:none; cursor:pointer; transition:all .2s; }
    .f-btn-submit:hover { transform:translateY(-1px); box-shadow:0 6px 20px rgba(0,113,170,.3); }
    .f-btn-cancel { display:inline-flex; align-items:center; gap:.5rem; padding:.8rem 1.5rem; border-radius:12px; background:#f3f4f6; color:#6b7280; font-weight:700; font-size:.9rem; border:none; cursor:pointer; text-decoration:none; transition:all .2s; }
    .dark .f-btn-cancel { background:#374151; color:#d1d5db; }
    .error-msg { font-size:.75rem; color:#ef4444; margin-top:.3rem; display:block; }
    #img-preview { max-width:100%; height:130px; object-fit:cover; border-radius:10px; margin-top:.75rem; }
    .current-img { width:100%; height:130px; object-fit:cover; border-radius:10px; margin-bottom:.5rem; display:block; }
</style>
@endpush

@section('content')
<div class="form-page" dir="rtl">

    {{-- Hero --}}
    <div class="form-hero">
        <div style="position:relative;z-index:5;display:flex;align-items:center;gap:1rem;">
            <a href="{{ route('admin.offers.index') }}" style="width:40px;height:40px;border-radius:12px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;color:#fff;text-decoration:none;flex-shrink:0;">
                <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <div>
                <p style="font-size:.75rem;color:rgba(255,255,255,.65);margin:0 0 .2rem;">العروض والخصومات</p>
                <h1 style="font-size:1.5rem;font-weight:900;color:#fff;margin:0;">تعديل: {{ $offer->title_ar }}</h1>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div style="background:#fee2e2;border:1px solid #fecaca;border-radius:14px;padding:1rem 1.25rem;margin-bottom:1.25rem;color:#991b1b;font-size:.85rem;">
        <strong>يرجى تصحيح الأخطاء التالية:</strong>
        <ul style="margin:.5rem 0 0;padding-right:1.25rem;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.offers.update', $offer) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- Basic Info --}}
        <div class="f-card">
            <div class="f-section-title">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                المعلومات الأساسية
            </div>
            <div class="f-grid">
                <div class="f-group">
                    <label class="f-label"><span class="f-required">*</span>عنوان العرض (عربي)</label>
                    <input name="title_ar" class="f-input" type="text" value="{{ old('title_ar',$offer->title_ar) }}" required>
                    @error('title_ar')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="f-group">
                    <label class="f-label">عنوان العرض (إنجليزي)</label>
                    <input name="title_en" class="f-input" type="text" value="{{ old('title_en',$offer->title_en) }}">
                </div>
            </div>
            <div class="f-grid">
                <div class="f-group">
                    <label class="f-label">وصف العرض (عربي)</label>
                    <textarea name="description_ar" class="f-textarea">{{ old('description_ar',$offer->description_ar) }}</textarea>
                </div>
                <div class="f-group">
                    <label class="f-label">وصف العرض (إنجليزي)</label>
                    <textarea name="description_en" class="f-textarea">{{ old('description_en',$offer->description_en) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Discount Settings --}}
        <div class="f-card">
            <div class="f-section-title">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A2 2 0 013 10V5a2 2 0 012-2z"/></svg>
                إعدادات الخصم
            </div>
            <div class="f-group">
                <label class="f-label"><span class="f-required">*</span>نوع الخصم</label>
                <div class="f-radio-group">
                    <label class="f-radio-option">
                        <input type="radio" name="discount_type" value="percentage" {{ old('discount_type',$offer->discount_type)=='percentage'?'checked':'' }} onchange="toggleDiscountFields(this.value)">
                        <span class="f-radio-lbl">📊 نسبة مئوية (%)</span>
                    </label>
                    <label class="f-radio-option">
                        <input type="radio" name="discount_type" value="fixed" {{ old('discount_type',$offer->discount_type)=='fixed'?'checked':'' }} onchange="toggleDiscountFields(this.value)">
                        <span class="f-radio-lbl">💰 مبلغ ثابت (ر.س)</span>
                    </label>
                    <label class="f-radio-option">
                        <input type="radio" name="discount_type" value="override" {{ old('discount_type',$offer->discount_type)=='override'?'checked':'' }} onchange="toggleDiscountFields(this.value)">
                        <span class="f-radio-lbl">🏷️ سعر مباشر (تجاوز)</span>
                    </label>
                </div>
            </div>

            {{-- discount_value row (hidden when override) --}}
            <div id="row-discount-value" class="f-grid">
                <div class="f-group">
                    <label class="f-label"><span class="f-required">*</span>قيمة الخصم</label>
                    <input name="discount_value" id="inp-discount-value" class="f-input" type="number" step="0.01" min="0.01" value="{{ old('discount_value',$offer->discount_value) }}">
                    <span class="f-hint">للنسبة: أدخل 20 تعني 20% — للمبلغ الثابت: أدخل 100 تعني 100 ر.س</span>
                    @error('discount_value')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="f-group">
                    <label class="f-label">كود العرض</label>
                    <input name="code" class="f-input" type="text" value="{{ old('code',$offer->code) }}" style="text-transform:uppercase;font-family:monospace;letter-spacing:1px;">
                    @error('code')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- offer_price row (visible only when override) --}}
            <div id="row-offer-price" style="display:none;" class="f-grid">
                <div class="f-group">
                    <label class="f-label"><span class="f-required">*</span>السعر الجديد بعد العرض (ر.س)</label>
                    <input name="offer_price" id="inp-offer-price" class="f-input" type="number" step="0.01" min="0" value="{{ old('offer_price',$offer->offer_price) }}" placeholder="مثال: 499">
                    <span class="f-hint">هذا السعر سيحل محل سعر البرنامج الأصلي بالكامل</span>
                    @error('offer_price')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="f-group">
                    <label class="f-label">كود العرض</label>
                    <input name="code" class="f-input" type="text" value="{{ old('code',$offer->code) }}" style="text-transform:uppercase;font-family:monospace;letter-spacing:1px;" disabled>
                    <span class="f-hint" style="color:#f59e0b;">⚡ سعر العرض المباشر لا يحتاج كوداً — يُطبَّق تلقائياً</span>
                </div>
            </div>
        </div>

        {{-- Program & Validity --}}
        <div class="f-card">
            <div class="f-section-title">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                البرنامج والصلاحية
            </div>
            <div class="f-grid">
                <div class="f-group">
                    <label class="f-label">البرنامج المخصص له</label>
                    <select name="program_id" class="f-select">
                        <option value="">🌐 جميع البرامج</option>
                        @foreach($programs as $prog)
                        <option value="{{ $prog->id }}" {{ old('program_id',$offer->program_id)==$prog->id?'selected':'' }}>{{ $prog->name_ar }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="f-group">
                    <label class="f-label">الحد الأقصى للاستخدامات</label>
                    <input name="max_uses" class="f-input" type="number" min="1" value="{{ old('max_uses',$offer->max_uses) }}" placeholder="غير محدود">
                </div>
            </div>
            <div class="f-grid">
                <div class="f-group">
                    <label class="f-label"><span class="f-required">*</span>تاريخ البداية</label>
                    <input name="start_date" class="f-input" type="date" value="{{ old('start_date',$offer->start_date->format('Y-m-d')) }}" required>
                    @error('start_date')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="f-group">
                    <label class="f-label"><span class="f-required">*</span>تاريخ الانتهاء</label>
                    <input name="end_date" class="f-input" type="date" value="{{ old('end_date',$offer->end_date->format('Y-m-d')) }}" required>
                    @error('end_date')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        {{-- Image & Status --}}
        <div class="f-card">
            <div class="f-section-title">
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                الصورة والحالة
            </div>
            <div class="f-grid">
                <div class="f-group">
                    <label class="f-label">صورة العرض</label>
                    @if($offer->image)
                        <img src="{{ asset('storage/'.$offer->image) }}" class="current-img" alt="الصورة الحالية">
                        <p style="font-size:.73rem;color:#9ca3af;margin-bottom:.5rem;">⬆ الصورة الحالية — ارفع صورة جديدة للاستبدال</p>
                    @endif
                    <div class="upload-zone" onclick="this.querySelector('input').click()">
                        <input type="file" name="image" accept="image/*" onchange="previewImg(this)">
                        <div id="upload-placeholder">
                            <svg style="width:30px;height:30px;color:#d1d5db;margin:0 auto .4rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v8"/></svg>
                            <p style="font-size:.78rem;color:#9ca3af;margin:0;">{{ $offer->image ? 'اختر صورة بديلة' : 'اسحب أو انقر لرفع صورة' }}</p>
                        </div>
                        <img id="img-preview" style="display:none;max-width:100%;height:110px;object-fit:cover;border-radius:8px;margin-top:.5rem;" alt="معاينة">
                    </div>
                </div>
                <div class="f-group">
                    <label class="f-label"><span class="f-required">*</span>حالة العرض</label>
                    <div class="f-radio-group" style="flex-direction:column;">
                        <label class="f-radio-option">
                            <input type="radio" name="status" value="active" {{ old('status',$offer->status)=='active'?'checked':'' }}>
                            <span class="f-radio-lbl" style="color:#10b981;">✅ نشط — يظهر للطلاب</span>
                        </label>
                        <label class="f-radio-option">
                            <input type="radio" name="status" value="inactive" {{ old('status',$offer->status)=='inactive'?'checked':'' }}>
                            <span class="f-radio-lbl" style="color:#6b7280;">⏸ غير نشط — مخفي</span>
                        </label>
                    </div>
                    @if($offer->uses_count > 0)
                    <div style="background:#fef3c7;border-radius:10px;padding:.6rem .9rem;margin-top:.75rem;font-size:.75rem;color:#92400e;">
                        📊 هذا العرض استُخدم <strong>{{ $offer->uses_count }}</strong> مرة
                        @if($offer->max_uses) من أصل {{ $offer->max_uses }} @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="f-btn-row">
            <a href="{{ route('admin.offers.index') }}" class="f-btn-cancel">إلغاء</a>
            <button type="submit" class="f-btn-submit">
                <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                حفظ التعديلات
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function previewImg(input) {
    if (input.files && input.files[0]) {
        var preview = document.getElementById('img-preview');
        var placeholder = document.getElementById('upload-placeholder');
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function toggleDiscountFields(type) {
    var rowDiscount = document.getElementById('row-discount-value');
    var rowOverride = document.getElementById('row-offer-price');
    var inpDiscount = document.getElementById('inp-discount-value');
    var inpOverride = document.getElementById('inp-offer-price');

    if (type === 'override') {
        rowDiscount.style.display = 'none';
        rowOverride.style.display = '';
        inpDiscount.removeAttribute('required');
        inpOverride.setAttribute('required', '');
    } else {
        rowDiscount.style.display = '';
        rowOverride.style.display = 'none';
        inpDiscount.setAttribute('required', '');
        inpOverride.removeAttribute('required');
    }
}

(function() {
    var checked = document.querySelector('input[name="discount_type"]:checked');
    if (checked) toggleDiscountFields(checked.value);
})();
</script>
@endpush
@endsection
