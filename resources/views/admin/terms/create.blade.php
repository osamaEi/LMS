@extends('layouts.dashboard')

@section('title', 'إضافة ربع تدريبي جديد')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.terms.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إضافة ربع تدريبي جديد</h1>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400">أدخل بيانات الفصل التدريبي الجديد</p>
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
            <!-- الدبلوم -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الدبلوم <span class="text-error-500">*</span>
                </label>
                <select name="program_id" id="program_select" required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">اختر الدبلوم</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ old('program_id', request('program_id')) == $program->id ? 'selected' : '' }}>
                            {{ $program->name_ar }} ({{ $program->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- رقم الربع -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رقم الربع <span class="text-error-500">*</span>
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

            <!-- اسم الربع بالعربي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    اسم الربع (عربي) <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="name_ar"
                       value="{{ old('name_ar') }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="مثال: الفصل التدريبي الأول">
            </div>

            <!-- اسم الربع بالإنجليزي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    اسم الربع (إنجليزي) <span class="text-error-500">*</span>
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

        </div>

        {{-- Subjects Picker --}}
        <div class="mt-6 border-t border-gray-200 dark:border-gray-800 pt-6">
            <div class="flex items-center justify-between mb-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    المقررات المرتبطة بهذا الربع
                    <span id="selected-count" class="mr-2 text-xs font-normal text-brand-600">(0 محدد)</span>
                </label>
                <div style="position:relative;width:260px">
                    <input type="text" id="subj-search" placeholder="ابحث في المقررات..."
                           oninput="filterSubjects(this.value)"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           style="padding-right:2.2rem">
                    <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#9ca3af;pointer-events:none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                </div>
            </div>

            <div id="subjects-list"
                 class="rounded-xl border border-gray-200 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-800 overflow-y-auto"
                 style="max-height:320px">
                @forelse($allSubjects as $subject)
                <label data-name="{{ strtolower($subject->name_ar . ' ' . $subject->name_en . ' ' . $subject->code) }}"
                       class="subj-row flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                    <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}"
                           onchange="updateCount()"
                           class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $subject->name_ar }}</span>
                        @if($subject->name_en)
                            <span class="text-xs text-gray-400 mr-1">/ {{ $subject->name_en }}</span>
                        @endif
                        <span class="text-xs text-gray-400 mr-1">· {{ $subject->code }}</span>
                    </div>
                    @if($subject->program)
                    <span class="text-xs text-gray-400 whitespace-nowrap">{{ $subject->program->name_ar }}</span>
                    @endif
                </label>
                @empty
                <p class="text-sm text-gray-400 p-4 text-center">لا توجد مقررات. <a href="{{ route('admin.subjects.create') }}" class="text-brand-600 underline">أضف مقرراً الآن</a></p>
                @endforelse
                <p id="no-results" class="text-sm text-gray-400 p-4 text-center" style="display:none">لا توجد نتائج</p>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">اختر المقررات التي ستُدرَّس في هذا الربع</p>
        </div>

        <!-- الأزرار -->
        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
            <a href="{{ route('admin.terms.index') }}"
               class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                إلغاء
            </a>
            <button type="submit"
                    class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                حفظ الربع التدريبي 
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
function filterSubjects(q) {
    q = q.toLowerCase();
    let visible = 0;
    document.querySelectorAll('.subj-row').forEach(row => {
        const match = row.dataset.name.includes(q);
        row.style.display = match ? 'flex' : 'none';
        if (match) visible++;
    });
    document.getElementById('no-results').style.display = visible === 0 ? 'block' : 'none';
}

function updateCount() {
    const n = document.querySelectorAll('input[name="subject_ids[]"]:checked').length;
    document.getElementById('selected-count').textContent = `(${n} محدد)`;
}
</script>
@endpush
@endsection
