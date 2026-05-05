@extends('layouts.dashboard')
@section('title', 'إضافة صفحة')

@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    .form-hero {
        background: linear-gradient(135deg, #0071aa 0%, #005a8a 50%, #004a7a 100%);
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
        border-color: #0071aa;
        box-shadow: 0 0 0 3px rgba(0,113,170,0.1);
        background: #fff;
    }
    .form-input.error { border-color: #f87171; }
    .section-card {
        background: #fff;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        overflow: visible;
    }
    .section-header {
        padding: 0.9rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #f8fafc;
        border-radius: 1rem 1rem 0 0;
    }
    .section-header svg { width: 16px; height: 16px; color: #0071aa; }
    .section-header span { font-size: 0.85rem; font-weight: 700; color: #1e293b; }
    .section-body { padding: 1.25rem; }
    .btn-save {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1.75rem;
        background: #0071aa;
        color: #fff !important;
        border: none;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.15s;
        text-decoration: none;
    }
    .btn-save:hover { background: #005a8a; }
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
    .btn-secondary:hover { background: #f1f5f9; border-color: #cbd5e1; }
    /* Quill editor */
    .ql-toolbar.ql-snow {
        border-radius: 0.75rem 0.75rem 0 0;
        border-color: #e2e8f0;
        background: #f8fafc;
        font-family: 'Cairo', sans-serif !important;
    }
    .ql-container.ql-snow {
        border-radius: 0 0 0.75rem 0.75rem;
        border-color: #e2e8f0;
        min-height: 280px;
        font-size: 14px;
        font-family: 'Cairo', sans-serif !important;
    }
    .ql-editor { min-height: 260px; }
    .ql-editor.ql-blank::before { font-style: normal; color: #94a3b8; }
    .editor-lang-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 999px;
        margin-bottom: 0.6rem;
    }
    .badge-ar { background: #fef3c7; color: #92400e; }
    .badge-en { background: #dbeafe; color: #1e40af; }
    .toggle-wrap {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: #f8fafc;
        border-radius: 0.75rem;
        border: 1.5px solid #e2e8f0;
    }
    .toggle-label { font-size: 0.85rem; font-weight: 600; color: #374151; }
    .toggle-desc  { font-size: 0.72rem; color: #94a3b8; }
    .slug-preview {
        font-size: .72rem;
        color: #94a3b8;
        margin-top: 4px;
    }
    .slug-preview strong { color: #0071aa; }
</style>
@endpush

@section('content')
<div class="p-4 max-w-4xl mx-auto">

    {{-- Hero --}}
    <div class="form-hero p-5 mb-5 text-white">
        <div class="relative z-10 flex items-center gap-3">
            <a href="{{ route('admin.pages.index') }}"
               class="flex-shrink-0 w-9 h-9 rounded-xl bg-white bg-opacity-15 flex items-center justify-center hover:bg-opacity-25 transition-all">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold">إضافة صفحة جديدة</h1>
                <p class="text-blue-200 text-xs mt-0.5">صفحات المحتوى — سياسات، شروط، تعريفية</p>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-4">
        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.pages.store') }}" method="POST" class="space-y-4">
        @csrf

        {{-- Basic Info --}}
        <div class="section-card">
            <div class="section-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>معلومات الصفحة</span>
            </div>
            <div class="section-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">العنوان (عربي) <span style="color:#ef4444">*</span></label>
                        <input type="text" name="title_ar" id="title_ar" value="{{ old('title_ar') }}" required
                               class="form-input {{ $errors->has('title_ar') ? 'error' : '' }}"
                               placeholder="مثال: سياسة الخصوصية">
                        @error('title_ar')<p style="color:#ef4444;font-size:.75rem;margin-top:4px">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">العنوان (إنجليزي)</label>
                        <input type="text" name="title_en" value="{{ old('title_en') }}" dir="ltr"
                               class="form-input" placeholder="Privacy Policy">
                    </div>
                    <div>
                        <label class="form-label">الرابط (Slug) <span style="color:#ef4444">*</span></label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}" required dir="ltr"
                               class="form-input {{ $errors->has('slug') ? 'error' : '' }}"
                               placeholder="privacy-policy">
                        <p class="slug-preview">أحرف إنجليزية صغيرة وشرطة — الرابط: /page/<strong id="slug-preview-text">slug</strong></p>
                        @error('slug')<p style="color:#ef4444;font-size:.75rem;margin-top:4px">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">التصنيف <span style="color:#ef4444">*</span></label>
                        <select name="category" class="form-input">
                            <option value="legal"   {{ old('category')==='legal'   ? 'selected':'' }}>⚖️ قانوني</option>
                            <option value="support" {{ old('category')==='support' ? 'selected':'' }}>🎧 دعم</option>
                            <option value="about"   {{ old('category')==='about'   ? 'selected':'' }}>📌 تعريفي</option>
                            <option value="other"   {{ old('category')==='other'   ? 'selected':'' }}>📄 أخرى</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 toggle-wrap">
                    <input type="checkbox" name="is_published" id="is_published" value="1"
                           {{ old('is_published', '1') ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 accent-blue-600">
                    <div>
                        <div class="toggle-label">نشر الصفحة فوراً</div>
                        <div class="toggle-desc">إذا لم تحدد، ستُحفظ كمسودة غير منشورة</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Arabic Content --}}
        <div class="section-card">
            <div class="section-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h12"/>
                </svg>
                <span>المحتوى العربي</span>
                <span class="editor-lang-badge badge-ar" style="margin-right:auto">عربي — RTL</span>
            </div>
            <div class="section-body">
                <div id="editor-ar"></div>
                <input type="hidden" name="content_ar" id="content_ar_input">
                @error('content_ar')<p style="color:#ef4444;font-size:.75rem;margin-top:6px">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- English Content --}}
        <div class="section-card">
            <div class="section-header">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h12"/>
                </svg>
                <span>المحتوى الإنجليزي</span>
                <span class="editor-lang-badge badge-en" style="margin-right:auto">English — LTR</span>
            </div>
            <div class="section-body">
                <div id="editor-en" dir="ltr"></div>
                <input type="hidden" name="content_en" id="content_en_input">
            </div>
        </div>

        {{-- Actions --}}
        <div class="section-card">
            <div class="section-body">
                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" class="btn-save">
                        <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        إضافة الصفحة
                    </button>
                    <a href="{{ route('admin.pages.index') }}" class="btn-secondary">
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
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function () {
    var toolbarOptions = [
        [{ 'header': [1, 2, 3, 4, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        [{ 'indent': '-1' }, { 'indent': '+1' }],
        [{ 'align': [] }],
        ['link', 'blockquote', 'code-block'],
        ['clean']
    ];

    var quillAr = new Quill('#editor-ar', {
        theme: 'snow',
        direction: 'rtl',
        placeholder: 'اكتب المحتوى العربي هنا...',
        modules: { toolbar: toolbarOptions }
    });
    quillAr.root.setAttribute('dir', 'rtl');
    quillAr.root.style.fontFamily = "'Cairo', sans-serif";
    quillAr.root.style.fontSize = '14px';
    quillAr.root.style.lineHeight = '1.8';

    var quillEn = new Quill('#editor-en', {
        theme: 'snow',
        placeholder: 'Write English content here...',
        modules: { toolbar: toolbarOptions }
    });
    quillEn.root.style.fontFamily = "'Cairo', sans-serif";
    quillEn.root.style.fontSize = '14px';

    var initAr = @json(old('content_ar', ''));
    var initEn = @json(old('content_en', ''));
    if (initAr) quillAr.clipboard.dangerouslyPasteHTML(initAr);
    if (initEn) quillEn.clipboard.dangerouslyPasteHTML(initEn);

    document.querySelector('form').addEventListener('submit', function () {
        document.getElementById('content_ar_input').value = quillAr.root.innerHTML;
        document.getElementById('content_en_input').value = quillEn.root.innerHTML;
    });

    // Auto-generate slug from Arabic title
    var titleInput = document.getElementById('title_ar');
    var slugInput  = document.getElementById('slug');
    var slugPreview = document.getElementById('slug-preview-text');
    titleInput.addEventListener('input', function () {
        if (slugInput.dataset.manual) return;
        var val = this.value.trim().toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[^a-z0-9\-]/g, '');
        slugInput.value = val;
        slugPreview.textContent = val || 'slug';
    });
    slugInput.addEventListener('input', function () {
        this.dataset.manual = '1';
        slugPreview.textContent = this.value || 'slug';
    });
    // Update preview on page load if slug already has value
    if (slugInput.value) slugPreview.textContent = slugInput.value;
})();
</script>
@endpush
