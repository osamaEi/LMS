@extends('layouts.dashboard')

@section('title', 'تعديل الاستبيان')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.surveys.show', $survey) }}" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل الاستبيان</h2>
            <p class="text-gray-600 dark:text-gray-400">{{ $survey->title }}</p>
        </div>
    </div>

    <form action="{{ route('admin.surveys.update', $survey) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">عنوان الاستبيان</label>
                    <input type="text" name="title" value="{{ $survey->title }}" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الحالة</label>
                    <select name="status" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 dark:bg-gray-700 dark:text-white">
                        <option value="draft" {{ $survey->status === 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="active" {{ $survey->status === 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="closed" {{ $survey->status === 'closed' ? 'selected' : '' }}>مغلق</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">تاريخ الانتهاء</label>
                    <input type="datetime-local" name="ends_at" value="{{ $survey->ends_at?->format('Y-m-d\TH:i') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الوصف</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 dark:bg-gray-700 dark:text-white">{{ $survey->description }}</textarea>
                </div>
            </div>
        </div>

        <!-- Questions (Read-only after creation) -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">الأسئلة</h3>
            <p class="text-sm text-gray-500 mb-4">لا يمكن تعديل الأسئلة بعد إنشاء الاستبيان للحفاظ على سلامة البيانات</p>

            <div class="space-y-3">
                @foreach($survey->questions as $question)
                <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 flex items-center justify-center bg-brand-500 text-white rounded-full text-xs">{{ $loop->iteration }}</span>
                        <span class="text-gray-900 dark:text-white">{{ $question->question }}</span>
                        <span class="text-xs text-gray-500">({{ $question->type }})</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.surveys.show', $survey) }}" class="px-6 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900">إلغاء</a>
            <button type="submit" class="px-6 py-2 text-white bg-brand-500 rounded-lg hover:bg-brand-600">حفظ التغييرات</button>
        </div>
    </form>
</div>
@endsection
