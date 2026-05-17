@extends('layouts.dashboard')

@section('title', 'تعديل الدبلوم التعليمي')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.programs.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل الدبلوم التعليمي</h1>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400">تعديل بيانات الدبلوم: {{ $program->name }}</p>
</div>

@if($errors->any())
<div class="mb-4 rounded-lg bg-error-50 p-4 dark:bg-error-900">
    <ul class="list-disc list-inside text-sm text-error-600 dark:text-error-200">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.programs.update', $program) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- اسم الدبلوم بالعربي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    اسم الدبلوم (عربي) <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="name_ar"
                       value="{{ old('name_ar', $program->name_ar) }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="مثال: دبلوم البرمجة وتطوير الويب">
            </div>

            <!-- اسم الدبلوم بالإنجليزي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    اسم الدبلوم (إنجليزي) <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="name_en"
                       value="{{ old('name_en', $program->name_en) }}"
                       required
                       dir="ltr"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="Example: Web Development Diploma">
            </div>

            <!-- كود الدبلوم -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    كود الدبلوم <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="code"
                       value="{{ old('code', $program->code) }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="مثال: PROG-001">
            </div>

            <!-- المدة بالأشهر -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    المدة بالأشهر
                </label>
                <input type="number"
                       name="duration_months"
                       value="{{ old('duration_months', $program->duration_months) }}"
                       min="1"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="مثال: 12">
            </div>

            <!-- السعر -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    السعر (ريال سعودي)
                </label>
                <input type="number"
                       name="price"
                       value="{{ old('price', $program->price) }}"
                       min="0"
                       step="0.01"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="مثال: 5000.00">
            </div>

            <!-- الحالة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الحالة <span class="text-error-500">*</span>
                </label>
                <select name="status" required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="active"   {{ old('status', $program->status) === 'active'   ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ old('status', $program->status) === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                </select>
            </div>

            <!-- نوع البرنامج -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نوع البرنامج</label>
                <select name="type"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">-- اختر النوع --</option>
                    <option value="training"     {{ old('type', $program->type) === 'training'     ? 'selected' : '' }}>تدريبي</option>
                    <option value="developmental"{{ old('type', $program->type) === 'developmental'? 'selected' : '' }}>تطويري</option>
                    <option value="qualifying"   {{ old('type', $program->type) === 'qualifying'   ? 'selected' : '' }}>تأهيلي</option>
                    <option value="diploma"      {{ old('type', $program->type) === 'diploma'      ? 'selected' : '' }}>دبلوم</option>
                </select>
            </div>

            <!-- الكاتيجوري (للدورات التطويرية/التأهيلية) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الكاتيجوري
                    <span class="font-normal text-gray-400 text-xs">(للدورات التطويرية/التأهيلية)</span>
                </label>
                <input type="text" name="category" value="{{ old('category', $program->category) }}"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="مثال: إدارة وأعمال، تقنية المعلومات...">
            </div>

            <!-- المشرف الأكاديمي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المشرف الأكاديمي</label>
                <input type="text" name="supervisor_name" value="{{ old('supervisor_name', $program->supervisor_name) }}"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="اكتب اسم المشرف الأكاديمي">
            </div>

            <!-- الوصف بالعربي -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الوصف (عربي)
                </label>
                <textarea name="description_ar"
                          rows="3"
                          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                          placeholder="وصف تفصيلي عن الدبلوم التعليمي وأهدافه ومحتوياته...">{{ old('description_ar', $program->description_ar) }}</textarea>
            </div>

            <!-- الوصف بالإنجليزي -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الوصف (إنجليزي)
                </label>
                <textarea name="description_en"
                          rows="3"
                          dir="ltr"
                          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                          placeholder="Detailed description of the educational program, its objectives and content...">{{ old('description_en', $program->description_en) }}</textarea>
            </div>

            <!-- صورة الدبلوم -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">صورة الدبلوم</label>
                <label for="programImage" class="block cursor-pointer">
                    <div id="imagePreviewWrap"
                         class="relative w-full rounded-xl border-2 border-dashed overflow-hidden transition-colors hover:border-blue-400
                                {{ $program->image ? 'border-gray-200' : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800' }}"
                         style="height:220px;">
                        @if($program->image)
                            <img id="imagePreview" src="{{ Storage::url($program->image) }}" alt="صورة الدبلوم" class="w-full h-full object-cover">
                            <div id="imageOverlay" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                <span class="text-white text-sm font-medium">تغيير الصورة</span>
                            </div>
                        @else
                            <div id="imageEmptyState" class="absolute inset-0 flex flex-col items-center justify-center gap-3">
                                <div class="w-14 h-14 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                                    <svg class="w-7 h-7 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">انقر لرفع صورة الدبلوم</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">JPEG, PNG, GIF, WebP — الحد الأقصى 2 ميجابايت</p>
                                </div>
                            </div>
                            <img id="imagePreview" src="" alt="" class="hidden w-full h-full object-cover">
                            <div id="imageOverlay" class="hidden absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                <span class="text-white text-sm font-medium">تغيير الصورة</span>
                            </div>
                        @endif
                    </div>
                    <input id="programImage" type="file" name="image" accept="image/*" class="hidden"
                           onchange="previewImage(this)">
                </label>
                <div class="flex items-center justify-between mt-2">
                    <p id="imageFileName" class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $program->image ? basename($program->image) : 'لم يتم اختيار ملف' }}
                    </p>
                    @if($program->image)
                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-red-500 hover:text-red-700">
                        <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-red-500">
                        حذف الصورة
                    </label>
                    @endif
                </div>
            </div>
        </div>

        <!-- الأزرار -->
        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
            <a href="{{ route('admin.programs.index') }}"
               class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                إلغاء
            </a>
            <button type="submit"
                    class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                حفظ التعديلات
            </button>
        </div>
    </div>
</form>
<script>
function previewImage(input) {
    const preview    = document.getElementById('imagePreview');
    const emptyState = document.getElementById('imageEmptyState');
    const overlay    = document.getElementById('imageOverlay');
    const label      = document.getElementById('imageFileName');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (emptyState) emptyState.classList.add('hidden');
            if (overlay)    overlay.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
        if (label) label.textContent = input.files[0].name;
    }
}
</script>
@endsection
