@extends('layouts.dashboard')

@section('title', 'إنشاء اختبار جديد')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
        <div class="flex items-center gap-4">
            <a href="{{ route('teacher.quizzes.index', $subject->id) }}"
               class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
               style="background-color: rgba(255,255,255,0.2);">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <p class="text-white/80 text-sm">{{ $subject->name }}</p>
                <h1 class="text-2xl font-bold">إنشاء اختبار جديد</h1>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('teacher.quizzes.store', $subject->id) }}" method="POST">
        @csrf

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Title Arabic -->
                <div>
                    <label for="title_ar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        عنوان الاختبار (عربي) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title_ar" id="title_ar" value="{{ old('title_ar') }}" required
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                    @error('title_ar')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title English -->
                <div>
                    <label for="title_en" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        عنوان الاختبار (إنجليزي)
                    </label>
                    <input type="text" name="title_en" id="title_en" value="{{ old('title_en') }}"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        نوع الاختبار <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                        <option value="quiz" {{ old('type') === 'quiz' ? 'selected' : '' }}>اختبار قصير</option>
                        <option value="exam" {{ old('type') === 'exam' ? 'selected' : '' }}>امتحان</option>
                        <option value="homework" {{ old('type') === 'homework' ? 'selected' : '' }}>واجب</option>
                    </select>
                </div>

                <!-- Duration -->
                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        المدة (بالدقائق)
                    </label>
                    <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes') }}" min="1"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                           placeholder="اتركه فارغاً لوقت غير محدود">
                </div>

                <!-- Total Marks -->
                <div>
                    <label for="total_marks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        الدرجة الكلية <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', 100) }}" min="1" step="0.5" required
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                </div>

                <!-- Pass Marks -->
                <div>
                    <label for="pass_marks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        درجة النجاح <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="pass_marks" id="pass_marks" value="{{ old('pass_marks', 50) }}" min="0" step="0.5" required
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                </div>

                <!-- Max Attempts -->
                <div>
                    <label for="max_attempts" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        عدد المحاولات المسموحة <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="max_attempts" id="max_attempts" value="{{ old('max_attempts', 1) }}" min="1" required
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                </div>

                <!-- Starts At -->
                <div>
                    <label for="starts_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        تاريخ البداية
                    </label>
                    <input type="datetime-local" name="starts_at" id="starts_at" value="{{ old('starts_at') }}"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                </div>

                <!-- Ends At -->
                <div>
                    <label for="ends_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        تاريخ النهاية
                    </label>
                    <input type="datetime-local" name="ends_at" id="ends_at" value="{{ old('ends_at') }}"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                </div>

                <!-- Description Arabic -->
                <div class="lg:col-span-2">
                    <label for="description_ar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        وصف الاختبار (عربي)
                    </label>
                    <textarea name="description_ar" id="description_ar" rows="3"
                              class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">{{ old('description_ar') }}</textarea>
                </div>
            </div>

            <!-- Options -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">خيارات الاختبار</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <label class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <input type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <div>
                            <span class="block font-medium text-gray-900 dark:text-white">ترتيب عشوائي للأسئلة</span>
                            <span class="text-xs text-gray-500">تغيير ترتيب الأسئلة لكل طالب</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <input type="checkbox" name="shuffle_answers" value="1" {{ old('shuffle_answers') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <div>
                            <span class="block font-medium text-gray-900 dark:text-white">ترتيب عشوائي للإجابات</span>
                            <span class="text-xs text-gray-500">تغيير ترتيب الخيارات</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <input type="checkbox" name="show_results" value="1" {{ old('show_results', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <div>
                            <span class="block font-medium text-gray-900 dark:text-white">عرض النتيجة</span>
                            <span class="text-xs text-gray-500">عرض الدرجة بعد التسليم</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <input type="checkbox" name="show_correct_answers" value="1" {{ old('show_correct_answers') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <div>
                            <span class="block font-medium text-gray-900 dark:text-white">عرض الإجابات الصحيحة</span>
                            <span class="text-xs text-gray-500">عرض التصحيح للطالب</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Active Status -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span class="font-medium text-gray-900 dark:text-white">تفعيل الاختبار</span>
                </label>
            </div>

            <!-- Submit -->
            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('teacher.quizzes.index', $subject->id) }}"
                   class="px-6 py-2.5 rounded-xl font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    إلغاء
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl font-bold text-white transition-all"
                        style="background-color: #8b5cf6;">
                    إنشاء الاختبار
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
