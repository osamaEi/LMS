@extends('layouts.dashboard')

@section('title', 'تعديل المسار التعليمي')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.programs.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل المسار التعليمي</h1>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400">تعديل بيانات المسار: {{ $program->name }}</p>
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

<form action="{{ route('admin.programs.update', $program) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- اسم المسار -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    اسم المسار <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $program->name) }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="مثال: دبلوم البرمجة وتطوير الويب">
            </div>

            <!-- كود المسار -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    كود المسار <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="code"
                       value="{{ old('code', $program->code) }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="مثال: PROG-001">
            </div>

            <!-- نوع المسار -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    نوع المسار <span class="text-error-500">*</span>
                </label>
                <select name="type"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">اختر النوع</option>
                    <option value="diploma" {{ old('type', $program->type) === 'diploma' ? 'selected' : '' }}>دبلوم</option>
                    <option value="training" {{ old('type', $program->type) === 'training' ? 'selected' : '' }}>تدريبي</option>
                </select>
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
                <select name="status"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="active" {{ old('status', $program->status) === 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ old('status', $program->status) === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                </select>
            </div>

            <!-- الوصف -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الوصف
                </label>
                <textarea name="description"
                          rows="4"
                          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                          placeholder="وصف تفصيلي عن المسار التعليمي وأهدافه ومحتوياته...">{{ old('description', $program->description) }}</textarea>
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
@endsection
