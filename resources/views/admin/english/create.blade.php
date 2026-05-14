@extends('layouts.dashboard')

@section('title', 'إضافة دورة لغة إنجليزية')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.english.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إضافة دورة لغة إنجليزية</h1>
    </div>
</div>

@if($errors->any())
<div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 dark:bg-red-900/20 dark:border-red-800">
    <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-300 space-y-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.english.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 mb-6">
        <div class="p-5 border-b border-gray-200 dark:border-gray-800" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);border-radius:12px 12px 0 0;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                    </svg>
                </div>
                <h2 class="text-base font-bold text-white">بيانات الدورة</h2>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المستوى <span class="text-red-500">*</span></label>
                    <select name="level" required
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="">— اختر المستوى —</option>
                        @foreach($levelNames as $num => $label)
                            <option value="{{ $num }}" {{ old('level') == $num ? 'selected' : '' }}>
                                {{ $num }} — {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">اسم الدورة (عربي)</label>
                    <input type="text" name="name_ar" value="{{ old('name_ar') }}"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           placeholder="مثال: اللغة الإنجليزية المستوى التمهيدي">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">اسم الدورة (إنجليزي)</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}" dir="ltr"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           placeholder="English Language — Introductory Level">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">كود الدورة</label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           placeholder="مثال: ENG-000">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المدة بالأشهر</label>
                    <input type="number" name="duration_months" value="{{ old('duration_months', 1) }}" min="1"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">السعر (ريال)</label>
                    <input type="number" name="price" value="{{ old('price') }}" min="0" step="0.01"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الحالة</label>
                    <select name="status"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="active"   {{ old('status', 'active') === 'active'   ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المشرف الأكاديمي</label>
                    <input type="text" name="supervisor_name" value="{{ old('supervisor_name') }}"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           placeholder="اكتب اسم المشرف الأكاديمي">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الوصف (عربي)</label>
                    <textarea name="description_ar" rows="3"
                              class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('description_ar') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الوصف (إنجليزي)</label>
                    <textarea name="description_en" rows="3" dir="ltr"
                              class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('description_en') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">صورة الدورة</label>
                    <label for="programImage" class="block cursor-pointer">
                        <div id="imagePreviewWrap"
                             class="relative w-full rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 overflow-hidden transition-colors hover:border-blue-400"
                             style="height:220px;">
                            <div id="imageEmptyState" class="absolute inset-0 flex flex-col items-center justify-center gap-3">
                                <div class="w-14 h-14 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                                    <svg class="w-7 h-7 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">انقر لرفع صورة الدورة</p>
                            </div>
                            <img id="imagePreview" src="" alt="" class="hidden w-full h-full object-cover">
                        </div>
                        <input id="programImage" type="file" name="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                    </label>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
                <a href="{{ route('admin.english.index') }}"
                   class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                    إلغاء
                </a>
                <button type="submit"
                        class="rounded-lg px-6 py-2.5 text-sm font-medium text-white transition-colors"
                        style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">
                    إضافة الدورة
                </button>
            </div>
        </div>
    </div>
</form>

<script>
function previewImage(input) {
    const preview    = document.getElementById('imagePreview');
    const emptyState = document.getElementById('imageEmptyState');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (emptyState) emptyState.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
