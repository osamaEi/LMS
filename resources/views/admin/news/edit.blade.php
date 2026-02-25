@extends('layouts.dashboard')

@section('title', 'تعديل الخبر')

@push('styles')
<style>
    .form-hero {
        background: linear-gradient(135deg, #059669 0%, #047857 50%, #065f46 100%);
        border-radius: 1.25rem;
        position: relative;
        overflow: hidden;
    }
    .form-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .form-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.35rem;
    }
    .form-input {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.6rem 0.9rem;
        font-size: 0.875rem;
        background: #f8fafc;
        color: #1e293b;
        transition: border-color 0.15s, box-shadow 0.15s;
        outline: none;
    }
    .form-input:focus {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5,150,105,0.1);
        background: #fff;
    }
    .form-input.error { border-color: #f87171; }
    .section-card {
        background: #fff;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .section-header {
        padding: 0.9rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #f8fafc;
    }
    .section-header svg { width: 16px; height: 16px; color: #059669; }
    .section-header span { font-size: 0.85rem; font-weight: 700; color: #1e293b; }
    .section-body { padding: 1.25rem; }
    .img-upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 0.75rem;
        padding: 1.25rem;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        background: #f8fafc;
    }
    .img-upload-zone:hover { border-color: #059669; background: #f0fdf4; }
    .current-img-wrap {
        position: relative;
        display: inline-block;
        margin-bottom: 0.75rem;
    }
    .current-img-wrap img {
        height: 100px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid #e2e8f0;
    }
    .current-img-badge {
        position: absolute;
        bottom: -6px;
        right: -6px;
        background: #059669;
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 999px;
    }
    .btn-save {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1.75rem;
        background: #059669;
        color: #fff !important;
        border: none;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.15s;
        text-decoration: none;
    }
    .btn-save:hover { background: #047857; }
    .btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1.5rem;
        background: #fff;
        color: #64748b !important;
        border: 1.5px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
    }
    .btn-secondary:hover { background: #f1f5f9; border-color: #cbd5e1; color: #475569 !important; }
</style>
@endpush

@section('content')
<div class="p-6 max-w-4xl mx-auto">

    {{-- Hero --}}
    <div class="form-hero p-5 mb-6 text-white">
        <div class="relative z-10 flex items-center gap-4">
            <a href="{{ route('admin.news.index') }}"
               class="flex-shrink-0 w-9 h-9 rounded-xl bg-white bg-opacity-15 flex items-center justify-center hover:bg-opacity-25 transition-all">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold">تعديل الخبر</h1>
                <p class="text-green-200 text-xs mt-0.5 truncate max-w-xs">{{ Str::limit($news->title_ar, 60) }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        {{-- Titles --}}
        <div class="section-card">
            <div class="section-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>العنوان</span>
            </div>
            <div class="section-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">العنوان بالعربية <span style="color:#ef4444">*</span></label>
                        <input type="text" name="title_ar" value="{{ old('title_ar', $news->title_ar) }}"
                               class="form-input {{ $errors->has('title_ar') ? 'error' : '' }}">
                        @error('title_ar') <p style="color:#ef4444;font-size:0.75rem;margin-top:4px">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">العنوان بالإنجليزية</label>
                        <input type="text" name="title_en" value="{{ old('title_en', $news->title_en) }}"
                               class="form-input" dir="ltr">
                    </div>
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div class="section-card">
            <div class="section-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h12"/>
                </svg>
                <span>المحتوى</span>
            </div>
            <div class="section-body space-y-4">
                <div>
                    <label class="form-label">المحتوى بالعربية <span style="color:#ef4444">*</span></label>
                    <textarea name="body_ar" rows="6"
                              class="form-input {{ $errors->has('body_ar') ? 'error' : '' }}">{{ old('body_ar', $news->body_ar) }}</textarea>
                    @error('body_ar') <p style="color:#ef4444;font-size:0.75rem;margin-top:4px">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">المحتوى بالإنجليزية</label>
                    <textarea name="body_en" rows="4"
                              class="form-input" dir="ltr">{{ old('body_en', $news->body_en) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Image + Meta --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Image Upload --}}
            <div class="section-card">
                <div class="section-header">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>الصورة</span>
                </div>
                <div class="section-body">
                    @if($news->image)
                    <div style="text-align:center;margin-bottom:12px;">
                        <div class="current-img-wrap">
                            <img id="currentImg" src="{{ Storage::url($news->image) }}" alt="">
                            <span class="current-img-badge">الحالية</span>
                        </div>
                        <p style="font-size:0.72rem;color:#94a3b8;margin-top:4px">ارفع صورة جديدة للاستبدال</p>
                    </div>
                    @endif
                    <div class="img-upload-zone" onclick="document.getElementById('imageInput').click()">
                        <img id="imgPreview" src="" alt="" style="display:none;max-height:100px;margin:0 auto 8px;border-radius:8px;object-fit:cover;">
                        <svg id="uploadIcon" style="width:36px;height:36px;color:#94a3b8;margin:0 auto 6px;display:block;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <p id="uploadText" style="font-size:0.8rem;color:#64748b;">انقر لاختيار صورة جديدة</p>
                        <p style="font-size:0.72rem;color:#94a3b8;margin-top:4px">PNG, JPG — الحد الأقصى 2 ميجا</p>
                    </div>
                    <input type="file" id="imageInput" name="image" accept="image/*" style="display:none"
                           onchange="previewImg(this)">
                    @error('image') <p style="color:#ef4444;font-size:0.75rem;margin-top:4px">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Meta --}}
            <div class="section-card">
                <div class="section-header">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>الإعدادات</span>
                </div>
                <div class="section-body space-y-4">
                    <div>
                        <label class="form-label">تاريخ النشر</label>
                        <input type="date" name="published_at"
                               value="{{ old('published_at', $news->published_at ? $news->published_at->format('Y-m-d') : '') }}"
                               class="form-input">
                    </div>
                    <div>
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-input">
                            <option value="active"   {{ old('status', $news->status) === 'active'   ? 'selected' : '' }}>✅ نشط — يظهر في الموقع</option>
                            <option value="inactive" {{ old('status', $news->status) === 'inactive' ? 'selected' : '' }}>🔴 غير نشط — مخفي</option>
                        </select>
                    </div>
                    <div style="padding:12px;background:#f0fdf4;border-radius:10px;border:1px solid #bbf7d0;">
                        <p style="font-size:0.75rem;color:#15803d;font-weight:600;">
                            آخر تعديل: {{ $news->updated_at->diffForHumans() }}
                        </p>
                        <p style="font-size:0.7rem;color:#6b7280;margin-top:2px;">
                            أُنشئ في: {{ $news->created_at->format('Y/m/d H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="section-card">
            <div class="section-body">
                <div class="flex items-center gap-3">
                    <button type="submit" class="btn-save">
                        <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        حفظ التغييرات
                    </button>
                    <a href="{{ route('admin.news.index') }}" class="btn-secondary">
                        <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        إلغاء
                    </a>
                </div>
            </div>
        </div>

    </form>
</div>

<script>
function previewImg(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const preview = document.getElementById('imgPreview');
        const icon    = document.getElementById('uploadIcon');
        const text    = document.getElementById('uploadText');
        preview.src = e.target.result;
        preview.style.display = 'block';
        icon.style.display = 'none';
        text.textContent = file.name;
        const cur = document.getElementById('currentImg');
        if (cur) cur.style.opacity = '0.4';
    };
    reader.readAsDataURL(file);
}
</script>
@endsection
