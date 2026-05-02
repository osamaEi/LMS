@extends('layouts.dashboard')

@section('title', 'تعديل الدبلومة التعليمي')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.programs.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل الدبلومة التعليمي</h1>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400">تعديل بيانات الدبلومة: {{ $program->name }}</p>
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
            <!-- اسم الدبلومة بالعربي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    اسم الدبلومة (عربي) <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="name_ar"
                       value="{{ old('name_ar', $program->name_ar) }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="مثال: دبلوم البرمجة وتطوير الويب">
            </div>

            <!-- اسم الدبلومة بالإنجليزي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    اسم الدبلومة (إنجليزي) <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="name_en"
                       value="{{ old('name_en', $program->name_en) }}"
                       required
                       dir="ltr"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="Example: Web Development Diploma">
            </div>

            <!-- كود الدبلومة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    كود الدبلومة <span class="text-error-500">*</span>
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
                    <option value="diploma"     {{ old('type', $program->type) === 'diploma'     ? 'selected' : '' }}>دبلوم</option>
                    <option value="training"    {{ old('type', $program->type) === 'training'    ? 'selected' : '' }}>تدريب</option>
                    <option value="certificate" {{ old('type', $program->type) === 'certificate' ? 'selected' : '' }}>شهادة</option>
                </select>
            </div>

            <!-- المشرف الأكاديمي -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المشرف الأكاديمي</label>
                <select name="supervisor_id"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">-- بدون مشرف --</option>
                    @foreach($supervisors as $sup)
                    <option value="{{ $sup->id }}" {{ old('supervisor_id', $program->supervisor_id) == $sup->id ? 'selected' : '' }}>
                        {{ $sup->name }}
                        @if($sup->role === 'teacher') (معلم) @elseif($sup->role === 'admin') (مدير) @endif
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- الوصف بالعربي -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الوصف (عربي)
                </label>
                <textarea name="description_ar"
                          rows="3"
                          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                          placeholder="وصف تفصيلي عن الدبلومة التعليمي وأهدافه ومحتوياته...">{{ old('description_ar', $program->description_ar) }}</textarea>
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

            <!-- صورة الدبلومة -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">صورة الدبلومة</label>
                <div class="flex items-start gap-5">
                    <!-- Preview -->
                    <div class="flex-shrink-0 w-28 h-28 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 flex items-center justify-center overflow-hidden">
                        @if($program->image)
                            <img id="imagePreview" src="{{ Storage::url($program->image) }}" alt="صورة الدبلومة" class="w-full h-full object-cover">
                        @else
                            <svg id="imagePreviewIcon" class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <img id="imagePreview" src="" alt="" class="w-full h-full object-cover hidden">
                        @endif
                    </div>
                    <!-- Controls -->
                    <div class="flex-1">
                        <div class="flex items-center gap-3 flex-wrap">
                            <label for="programImage"
                                   class="inline-flex items-center gap-2 cursor-pointer rounded-lg px-4 py-2.5 text-sm font-medium text-white transition-opacity hover:opacity-90"
                                   style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                {{ $program->image ? 'تغيير الصورة' : 'اختر صورة' }}
                            </label>
                            @if($program->image)
                            <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-red-500 hover:text-red-700">
                                <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-red-500">
                                حذف الصورة الحالية
                            </label>
                            @endif
                        </div>
                        <input id="programImage" type="file" name="image" accept="image/*" class="hidden"
                               onchange="previewImage(this)">
                        <p id="imageFileName" class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            {{ $program->image ? basename($program->image) : 'لم يتم اختيار ملف' }}
                        </p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">JPEG, PNG, GIF, WebP — الحد الأقصى 2 ميجابايت</p>
                    </div>
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
    const preview = document.getElementById('imagePreview');
    const icon    = document.getElementById('imagePreviewIcon');
    const label   = document.getElementById('imageFileName');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (icon) icon.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
        label.textContent = input.files[0].name;
    }
}
</script>
@endsection
