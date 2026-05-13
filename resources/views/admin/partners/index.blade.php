@extends('layouts.dashboard')
@section('title', 'شركائنا')

@push('styles')
<style>
    .form-hero {
        background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 50%, #4c1d95 100%);
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
    .section-card { background:#fff; border-radius:1rem; border:1px solid #e2e8f0; }
    .section-header {
        padding:.9rem 1.25rem;
        border-bottom:1px solid #f1f5f9;
        display:flex; align-items:center; gap:.5rem;
        background:#f8fafc; border-radius:1rem 1rem 0 0;
    }
    .section-header svg { width:16px; height:16px; color:#7c3aed; }
    .section-header span { font-size:.85rem; font-weight:700; color:#1e293b; }
    .section-body { padding:1.25rem; }
    .form-label { display:block; font-size:.8rem; font-weight:600; color:#374151; margin-bottom:.35rem; }
    .form-input {
        width:100%; border:1.5px solid #e2e8f0; border-radius:.75rem;
        padding:.6rem .9rem; font-size:.875rem; background:#f8fafc; color:#1e293b;
        transition:border-color .15s,box-shadow .15s; outline:none;
    }
    .form-input:focus { border-color:#7c3aed; box-shadow:0 0 0 3px rgba(124,58,237,.1); background:#fff; }
    .btn-save {
        display:inline-flex; align-items:center; gap:.5rem;
        padding:.6rem 1.5rem; background:#7c3aed; color:#fff !important;
        border:none; border-radius:.75rem; font-size:.875rem; font-weight:700;
        cursor:pointer; transition:background .15s;
    }
    .btn-save:hover { background:#6d28d9; }
    .btn-danger {
        display:inline-flex; align-items:center;
        padding:.4rem .9rem; background:#fee2e2; color:#dc2626 !important;
        border:none; border-radius:.5rem; font-size:.78rem; font-weight:600;
        cursor:pointer; transition:background .15s; text-decoration:none;
    }
    .btn-danger:hover { background:#fecaca; }
    .partner-card {
        display:flex; align-items:center; gap:1rem;
        padding:1rem 1.25rem; border-bottom:1px solid #f1f5f9;
        transition:background .15s;
    }
    .partner-card:last-child { border-bottom:none; }
    .partner-card:hover { background:#fafafa; }
    .partner-logo-wrap {
        width:96px; height:56px; border-radius:.625rem;
        background:#f8fafc; border:1.5px solid #e2e8f0;
        display:flex; align-items:center; justify-content:center;
        overflow:hidden; flex-shrink:0;
    }
    .partner-logo-wrap img { max-width:80px; max-height:44px; object-fit:contain; }
    .inactive-card { opacity:.5; }
    .upload-zone {
        border:2px dashed #e2e8f0; border-radius:.75rem;
        padding:1.25rem; text-align:center; cursor:pointer;
        transition:border-color .2s,background .2s; background:#f8fafc;
    }
    .upload-zone:hover { border-color:#7c3aed; background:#f5f3ff; }
</style>
@endpush

@section('content')
<div class="p-4 max-w-5xl mx-auto">

    {{-- Hero --}}
    <div class="form-hero p-5 mb-5 text-white">
        <div class="relative z-10 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold">شركائنا</h1>
                <p class="text-purple-200 text-xs mt-0.5">شعارات الشركاء تظهر في قسم خاص بالصفحة الرئيسية</p>
            </div>
            <div class="mr-auto text-sm font-bold bg-white/15 px-3 py-1.5 rounded-lg">
                {{ $partners->count() }} شريك
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

    @if($errors->any())
    <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-4">
        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

        {{-- Add form (right column) --}}
        <div class="lg:col-span-2">
            <div class="section-card sticky top-4">
                <div class="section-header">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>إضافة شريك جديد</span>
                </div>
                <div class="section-body">
                    <form action="{{ route('admin.partners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="form-label">اسم الجهة / الشريك <span style="color:#ef4444">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="form-input" placeholder="مثال: وزارة التعليم">
                        </div>
                        <div>
                            <label class="form-label">شعار (لوجو) <span style="color:#ef4444">*</span></label>
                            <label for="logoInput" class="block cursor-pointer">
                                <div class="upload-zone">
                                    <img id="logoPreview" src="" alt="" style="display:none;max-height:60px;margin:0 auto 8px;object-fit:contain;">
                                    <svg id="uploadIcon" class="w-8 h-8 mx-auto mb-2" style="color:#a78bfa" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <p id="uploadText" style="font-size:.8rem;color:#64748b">انقر لاختيار الشعار</p>
                                    <p style="font-size:.72rem;color:#94a3b8;margin-top:4px">PNG, JPG, SVG — شفاف يفضل</p>
                                </div>
                                <input id="logoInput" type="file" name="logo" accept="image/*" class="hidden" onchange="previewLogo(this,'logoPreview','uploadIcon','uploadText')">
                            </label>
                        </div>
                        <div>
                            <label class="form-label">رابط الموقع (اختياري)</label>
                            <input type="url" name="url" value="{{ old('url') }}"
                                   class="form-input" dir="ltr" placeholder="https://...">
                        </div>
                        <div>
                            <label class="form-label">ترتيب العرض</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                   class="form-input" placeholder="0">
                        </div>
                        <button type="submit" class="btn-save w-full justify-center">
                            <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            إضافة الشريك
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Partners list (left column) --}}
        <div class="lg:col-span-3">
            <div class="section-card">
                <div class="section-header">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span>قائمة الشركاء ({{ $partners->count() }})</span>
                </div>

                @if($partners->isEmpty())
                <div class="p-10 text-center">
                    <svg class="w-12 h-12 mx-auto mb-3" style="color:#d8b4fe" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <p style="font-size:.9rem;font-weight:600;color:#6b7280">لم تُضَف جهات بعد</p>
                    <p style="font-size:.78rem;color:#9ca3af;margin-top:4px">أضف أول شريك من النموذج المجاور</p>
                </div>
                @else
                @foreach($partners as $partner)
                <div class="partner-card {{ $partner->is_active ? '' : 'inactive-card' }}">
                    <div class="partner-logo-wrap">
                        <img src="{{ Storage::url($partner->logo) }}" alt="{{ $partner->name }}">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div style="font-weight:700;font-size:.9rem;color:#111827">{{ $partner->name }}</div>
                        @if($partner->url)
                        <a href="{{ $partner->url }}" target="_blank" style="font-size:.72rem;color:#7c3aed;direction:ltr;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px;">
                            {{ $partner->url }}
                        </a>
                        @endif
                        <div style="font-size:.7rem;color:#9ca3af;margin-top:2px">ترتيب: {{ $partner->sort_order }}</div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        {{-- Toggle --}}
                        <form action="{{ route('admin.partners.toggle', $partner) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" title="{{ $partner->is_active ? 'إخفاء' : 'إظهار' }}"
                                    style="padding:5px 10px;border-radius:6px;border:none;cursor:pointer;font-size:.75rem;font-weight:600;background:{{ $partner->is_active ? '#d1fae5' : '#f3f4f6' }};color:{{ $partner->is_active ? '#065f46' : '#6b7280' }}">
                                {{ $partner->is_active ? '✓ ظاهر' : '✗ مخفي' }}
                            </button>
                        </form>

                        {{-- Edit trigger --}}
                        <button onclick="openEdit({{ $partner->id }}, '{{ addslashes($partner->name) }}', '{{ $partner->url ?? '' }}', {{ $partner->sort_order }}, '{{ Storage::url($partner->logo) }}')"
                                style="padding:5px 10px;border-radius:6px;border:1.5px solid #e2e8f0;cursor:pointer;font-size:.75rem;font-weight:600;background:#fff;color:#374151">
                            تعديل
                        </button>

                        {{-- Delete --}}
                        <form action="{{ route('admin.partners.destroy', $partner) }}" method="POST"
                              onsubmit="return confirm('حذف {{ addslashes($partner->name) }}؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger">حذف</button>
                        </form>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Edit modal --}}
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:1.25rem;width:100%;max-width:480px;margin:1rem;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,.2);">
        <div style="background:linear-gradient(135deg,#7c3aed,#5b21b6);padding:1.25rem 1.5rem;display:flex;align-items:center;justify-content:space-between;">
            <span style="color:#fff;font-weight:700;font-size:.95rem">تعديل بيانات الشريك</span>
            <button onclick="closeEdit()" style="color:rgba(255,255,255,.7);background:none;border:none;cursor:pointer;font-size:1.25rem;line-height:1">×</button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data" style="padding:1.5rem;space-y:1rem;">
            @csrf @method('PUT')
            <div style="margin-bottom:1rem">
                <label class="form-label">اسم الجهة <span style="color:#ef4444">*</span></label>
                <input type="text" name="name" id="editName" required class="form-input">
            </div>
            <div style="margin-bottom:1rem">
                <label class="form-label">شعار جديد (اتركه فارغاً للإبقاء على الحالي)</label>
                <div style="margin-bottom:.5rem;text-align:center;">
                    <img id="editCurrentLogo" src="" alt="" style="max-height:50px;object-fit:contain;display:inline-block;border-radius:6px;border:1.5px solid #e2e8f0;padding:4px 8px;">
                </div>
                <label for="editLogoInput" class="block cursor-pointer">
                    <div class="upload-zone" style="padding:.75rem">
                        <img id="editLogoPreview" src="" alt="" style="display:none;max-height:50px;margin:0 auto 6px;object-fit:contain;">
                        <p style="font-size:.78rem;color:#64748b">انقر لاختيار شعار جديد</p>
                    </div>
                    <input id="editLogoInput" type="file" name="logo" accept="image/*" class="hidden"
                           onchange="previewLogo(this,'editLogoPreview',null,null)">
                </label>
            </div>
            <div style="margin-bottom:1rem">
                <label class="form-label">رابط الموقع</label>
                <input type="url" name="url" id="editUrl" class="form-input" dir="ltr" placeholder="https://...">
            </div>
            <div style="margin-bottom:1.5rem">
                <label class="form-label">ترتيب العرض</label>
                <input type="number" name="sort_order" id="editSortOrder" min="0" class="form-input">
            </div>
            <div style="display:flex;gap:.75rem">
                <button type="submit" class="btn-save" style="flex:1;justify-content:center">حفظ التغييرات</button>
                <button type="button" onclick="closeEdit()" style="flex:1;padding:.6rem;border-radius:.75rem;border:1.5px solid #e2e8f0;background:#fff;font-weight:600;font-size:.875rem;cursor:pointer;color:#64748b">إلغاء</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewLogo(input, previewId, iconId, textId) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const preview = document.getElementById(previewId);
        preview.src = e.target.result;
        preview.style.display = 'block';
        if (iconId) document.getElementById(iconId).style.display = 'none';
        if (textId) document.getElementById(textId).textContent = input.files[0].name;
    };
    reader.readAsDataURL(input.files[0]);
}

function openEdit(id, name, url, sortOrder, logoUrl) {
    document.getElementById('editForm').action = '/admin/partners/' + id;
    document.getElementById('editName').value = name;
    document.getElementById('editUrl').value = url;
    document.getElementById('editSortOrder').value = sortOrder;
    document.getElementById('editCurrentLogo').src = logoUrl;
    document.getElementById('editLogoPreview').style.display = 'none';
    const modal = document.getElementById('editModal');
    modal.style.display = 'flex';
}

function closeEdit() {
    document.getElementById('editModal').style.display = 'none';
}

document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEdit();
});
</script>
@endpush
