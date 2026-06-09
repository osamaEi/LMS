@extends('layouts.dashboard')

@section('title', 'تعديل الدورة')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.courses.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل الدورة</h1>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400">تعديل بيانات الدورة: {{ $program->name_ar }}</p>
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

<form action="{{ route('admin.courses.update', $program) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 mb-6">
        <div class="p-5 border-b border-gray-200 dark:border-gray-800" style="background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:12px 12px 0 0;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h2 class="text-base font-bold text-white">بيانات الدورة</h2>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">اسم الدورة (عربي)</label>
                    <input type="text" name="name_ar" value="{{ old('name_ar', $program->name_ar) }}"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-amber-500 focus:ring-2 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           placeholder="مثال: دورة إدارة المشاريع">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">اسم الدورة (إنجليزي)</label>
                    <input type="text" name="name_en" value="{{ old('name_en', $program->name_en) }}" dir="ltr"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-amber-500 focus:ring-2 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           placeholder="Project Management Course">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">كود الدورة</label>
                    <input type="text" name="code" value="{{ old('code', $program->code) }}"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-amber-500 focus:ring-2 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           placeholder="مثال: CRS-001">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المدة بالساعات</label>
                    <input type="number" name="duration_hours" value="{{ old('duration_hours', $program->duration_hours) }}" min="1"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-amber-500 focus:ring-2 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           placeholder="مثال: 20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">السعر (ريال)</label>
                    <input type="number" name="price" value="{{ old('price', $program->price) }}" min="0" step="0.01"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-amber-500 focus:ring-2 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الحالة</label>
                    <select name="status"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-amber-500 focus:ring-2 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="active"   {{ old('status', $program->status) === 'active'   ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ old('status', $program->status) === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نوع الدورة</label>
                    <select name="course_type"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-amber-500 focus:ring-2 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="">— اختر النوع —</option>
                        <option value="developmental" {{ old('course_type', $program->course_type) === 'developmental' ? 'selected' : '' }}>تطويري</option>
                        <option value="qualifying"    {{ old('course_type', $program->course_type) === 'qualifying'    ? 'selected' : '' }}>تأهيلي</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المشرف الأكاديمي</label>
                    <input type="text" name="supervisor_name" value="{{ old('supervisor_name', $program->supervisor_name) }}"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-amber-500 focus:ring-2 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           placeholder="اكتب اسم المشرف الأكاديمي">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الوصف (عربي)</label>
                    <textarea name="description_ar" rows="3"
                              class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-amber-500 focus:ring-2 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('description_ar', $program->description_ar) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الوصف (إنجليزي)</label>
                    <textarea name="description_en" rows="3" dir="ltr"
                              class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-amber-500 focus:ring-2 focus:ring-amber-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('description_en', $program->description_en) }}</textarea>
                </div>

                {{-- صورة الدورة --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">صورة الدورة</label>
                    <label for="programImage" class="block cursor-pointer">
                        <div id="imagePreviewWrap"
                             class="relative w-full rounded-xl border-2 border-dashed overflow-hidden transition-colors hover:border-amber-400
                                    {{ $program->image ? 'border-gray-200' : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800' }}"
                             style="height:220px;">
                            @if($program->image)
                                <img id="imagePreview" src="{{ Storage::url($program->image) }}" alt="صورة الدورة" class="w-full h-full object-cover">
                                <div id="imageOverlay" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <span class="text-white text-sm font-medium">تغيير الصورة</span>
                                </div>
                            @else
                                <div id="imageEmptyState" class="absolute inset-0 flex flex-col items-center justify-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">انقر لرفع صورة الدورة</p>
                                </div>
                                <img id="imagePreview" src="" alt="" class="hidden w-full h-full object-cover">
                                <div id="imageOverlay" class="hidden absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                    <span class="text-white text-sm font-medium">تغيير الصورة</span>
                                </div>
                            @endif
                        </div>
                        <input id="programImage" type="file" name="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                    </label>
                    @if($program->image)
                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-red-500 hover:text-red-700 mt-2">
                        <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-red-500">
                        حذف الصورة الحالية
                    </label>
                    @endif
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
                <a href="{{ route('admin.courses.index') }}"
                   class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                    إلغاء
                </a>
                <button type="submit"
                        class="rounded-lg px-6 py-2.5 text-sm font-medium text-white transition-colors"
                        style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                    حفظ التعديلات
                </button>
            </div>
        </div>
    </div>
</form>

<script>
function previewImage(input) {
    const preview    = document.getElementById('imagePreview');
    const emptyState = document.getElementById('imageEmptyState');
    const overlay    = document.getElementById('imageOverlay');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (emptyState) emptyState.classList.add('hidden');
            if (overlay)    overlay.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
