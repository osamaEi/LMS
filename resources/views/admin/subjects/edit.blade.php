@extends('layouts.dashboard')

@section('title', 'تعديل المادة الدراسية')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.subjects.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل المادة الدراسية</h1>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400">تعديل بيانات المادة الدراسية</p>
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

<form action="{{ route('admin.subjects.update', $subject) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- الفصل الدراسي -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الفصل الدراسي <span class="text-error-500">*</span>
                </label>
                <select name="term_id"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">اختر الفصل الدراسي</option>
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" {{ old('term_id', $subject->term_id) == $term->id ? 'selected' : '' }}>
                            {{ $term->name }} - {{ $term->program->name ?? '' }} (الفصل {{ $term->term_number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- المعلم -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    المعلم <span class="text-error-500">*</span>
                </label>
                <select name="teacher_id"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">اختر المعلم</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id', $subject->teacher_id) == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- اسم المادة بالعربي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    اسم المادة (عربي) <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="name_ar"
                       value="{{ old('name_ar', $subject->name_ar) }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="مثال: مقدمة في البرمجة">
            </div>

            <!-- اسم المادة بالإنجليزي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    اسم المادة (إنجليزي) <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="name_en"
                       value="{{ old('name_en', $subject->name_en) }}"
                       required
                       dir="ltr"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="Example: Introduction to Programming">
            </div>

            <!-- كود المادة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    كود المادة <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="code"
                       value="{{ old('code', $subject->code) }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="مثال: CS101">
            </div>

            <!-- الساعات المعتمدة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الساعات المعتمدة
                </label>
                <input type="number"
                       name="credits"
                       value="{{ old('credits', $subject->credits) }}"
                       min="1"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="مثال: 3">
            </div>

            <!-- صورة البانر -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    صورة البانر
                </label>
                @if($subject->banner_photo)
                <div class="mb-3">
                    <img src="{{ Storage::url($subject->banner_photo) }}" alt="صورة البانر الحالية" class="h-32 w-auto rounded-lg object-cover">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">الصورة الحالية</p>
                </div>
                @endif
                <input type="file"
                       name="banner_photo"
                       accept="image/*"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-900 dark:file:text-brand-200">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">الصيغ المدعومة: JPEG, PNG, JPG, GIF, WEBP - الحد الأقصى: 2MB</p>
            </div>

            <!-- الحالة -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الحالة <span class="text-error-500">*</span>
                </label>
                <select name="status"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="active" {{ old('status', $subject->status) === 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ old('status', $subject->status) === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    <option value="completed" {{ old('status', $subject->status) === 'completed' ? 'selected' : '' }}>مكتمل</option>
                </select>
            </div>

            <!-- الوصف بالعربي -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الوصف (عربي)
                </label>
                <textarea name="description_ar"
                          rows="4"
                          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                          placeholder="وصف تفصيلي عن المادة الدراسية ومحتوياتها...">{{ old('description_ar', $subject->description_ar) }}</textarea>
            </div>

            <!-- الوصف بالإنجليزي -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الوصف (إنجليزي)
                </label>
                <textarea name="description_en"
                          rows="4"
                          dir="ltr"
                          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                          placeholder="Detailed description of the subject and its contents...">{{ old('description_en', $subject->description_en) }}</textarea>
            </div>
        </div>

        <!-- الأزرار -->
        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
            <a href="{{ route('admin.subjects.index') }}"
               class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                إلغاء
            </a>
            <button type="submit"
                    class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                تحديث المادة
            </button>
        </div>
    </div>
</form>
@endsection
