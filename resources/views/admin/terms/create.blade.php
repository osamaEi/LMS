@extends('layouts.dashboard')

@section('title', 'إضافة فصل دراسي جديد')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.terms.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إضافة فصل دراسي جديد</h1>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400">أدخل بيانات الفصل الدراسي الجديد</p>
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

<form action="{{ route('admin.terms.store') }}" method="POST">
    @csrf

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- المسار التعليمي -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    المسار التعليمي <span class="text-error-500">*</span>
                </label>
                <select name="program_id"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">اختر المسار التعليمي</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ old('program_id', request('program_id')) == $program->id ? 'selected' : '' }}>
                            {{ $program->name_ar }} ({{ $program->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- رقم الفصل -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رقم الفصل <span class="text-error-500">*</span>
                </label>
                <input type="number"
                       name="term_number"
                       value="{{ old('term_number') }}"
                       required
                       min="1"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="مثال: 1">
            </div>

            <!-- الحالة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الحالة <span class="text-error-500">*</span>
                </label>
                <select name="status"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="upcoming" {{ old('status') === 'upcoming' ? 'selected' : '' }}>قادم</option>
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                </select>
            </div>

            <!-- اسم الفصل بالعربي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    اسم الفصل (عربي) <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="name_ar"
                       value="{{ old('name_ar') }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="مثال: الفصل الدراسي الأول">
            </div>

            <!-- اسم الفصل بالإنجليزي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    اسم الفصل (إنجليزي) <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="name_en"
                       value="{{ old('name_en') }}"
                       required
                       dir="ltr"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="Example: First Semester">
            </div>

            <!-- تاريخ البدء -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    تاريخ البدء <span class="text-error-500">*</span>
                </label>
                <input type="date"
                       name="start_date"
                       value="{{ old('start_date') }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <!-- تاريخ الانتهاء -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    تاريخ الانتهاء <span class="text-error-500">*</span>
                </label>
                <input type="date"
                       name="end_date"
                       value="{{ old('end_date') }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>
        </div>

        <!-- الأزرار -->
        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
            <a href="{{ route('admin.terms.index') }}"
               class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                إلغاء
            </a>
            <button type="submit"
                    class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                حفظ الفصل الدراسي
            </button>
        </div>
    </div>
</form>
@endsection
