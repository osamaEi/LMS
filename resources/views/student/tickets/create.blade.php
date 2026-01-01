@extends('layouts.dashboard')

@section('title', 'تذكرة جديدة')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('student.tickets.index') }}"
           class="w-10 h-10 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تذكرة جديدة</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">أرسل استفسارك وسنقوم بالرد عليك في أقرب وقت</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <form action="{{ route('student.tickets.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Subject -->
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    عنوان التذكرة <span class="text-red-500">*</span>
                </label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                       class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2"
                       style="--tw-ring-color: #4f46e5;"
                       placeholder="اكتب عنوان واضح يصف مشكلتك">
                @error('subject')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category & Priority -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        التصنيف <span class="text-red-500">*</span>
                    </label>
                    <select name="category" id="category" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">اختر التصنيف</option>
                        <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>دعم فني</option>
                        <option value="academic" {{ old('category') == 'academic' ? 'selected' : '' }}>أكاديمي</option>
                        <option value="financial" {{ old('category') == 'financial' ? 'selected' : '' }}>مالي</option>
                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        الأولوية <span class="text-red-500">*</span>
                    </label>
                    <select name="priority" id="priority" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <option value="">اختر الأولوية</option>
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                    </select>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Message -->
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    تفاصيل المشكلة <span class="text-red-500">*</span>
                </label>
                <textarea name="message" id="message" rows="6" required
                          class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                          placeholder="اشرح مشكلتك بالتفصيل لنتمكن من مساعدتك بشكل أفضل...">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tips -->
            <div class="rounded-xl p-4" style="background-color: #eff6ff;">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="font-medium" style="color: #1e40af;">نصائح للحصول على رد سريع:</h4>
                        <ul class="mt-2 text-sm space-y-1" style="color: #1e40af;">
                            <li>• اكتب عنوان واضح ومحدد للمشكلة</li>
                            <li>• اشرح الخطوات التي قمت بها قبل حدوث المشكلة</li>
                            <li>• أرفق أي رسائل خطأ ظهرت لك</li>
                            <li>• حدد المتصفح ونظام التشغيل المستخدم (إن كانت المشكلة تقنية)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('student.tickets.index') }}"
                   class="px-6 py-2.5 text-gray-700 dark:text-gray-300 font-medium rounded-xl border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    إلغاء
                </a>
                <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 text-white font-medium rounded-xl transition-all"
                        style="background-color: #4f46e5;">
                    <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    إرسال التذكرة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
