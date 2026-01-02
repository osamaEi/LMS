@extends('layouts.dashboard')

@section('title', 'إضافة سؤال')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
        <div class="flex items-center gap-4">
            <a href="{{ route('teacher.quizzes.show', [$subject->id, $quiz->id]) }}"
               class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
               style="background-color: rgba(255,255,255,0.2);">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <p class="text-white/80 text-sm">{{ $quiz->title_ar }}</p>
                <h1 class="text-2xl font-bold">إضافة سؤال جديد</h1>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('teacher.quizzes.questions.store', [$subject->id, $quiz->id]) }}" method="POST" enctype="multipart/form-data" id="questionForm">
        @csrf

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Question Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        نوع السؤال <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" required onchange="handleTypeChange()"
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                        <option value="multiple_choice" {{ old('type') === 'multiple_choice' ? 'selected' : '' }}>اختيار من متعدد</option>
                        <option value="true_false" {{ old('type') === 'true_false' ? 'selected' : '' }}>صح وخطأ</option>
                        <option value="short_answer" {{ old('type') === 'short_answer' ? 'selected' : '' }}>إجابة قصيرة</option>
                        <option value="essay" {{ old('type') === 'essay' ? 'selected' : '' }}>مقالي</option>
                    </select>
                </div>

                <!-- Marks -->
                <div>
                    <label for="marks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        الدرجة <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="marks" id="marks" value="{{ old('marks', 1) }}" min="0.1" step="0.5" required
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                </div>

                <!-- Order -->
                <div>
                    <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        الترتيب <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="order" id="order" value="{{ old('order', $nextOrder) }}" min="1" required
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                </div>

                <!-- Question Arabic -->
                <div class="lg:col-span-3">
                    <label for="question_ar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        نص السؤال (عربي) <span class="text-red-500">*</span>
                    </label>
                    <textarea name="question_ar" id="question_ar" rows="3" required
                              class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">{{ old('question_ar') }}</textarea>
                    @error('question_ar')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Question English -->
                <div class="lg:col-span-3">
                    <label for="question_en" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        نص السؤال (إنجليزي)
                    </label>
                    <textarea name="question_en" id="question_en" rows="2"
                              class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">{{ old('question_en') }}</textarea>
                </div>

                <!-- Image -->
                <div class="lg:col-span-3">
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        صورة السؤال (اختياري)
                    </label>
                    <input type="file" name="image" id="image" accept="image/*"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                </div>
            </div>

            <!-- Multiple Choice Options -->
            <div id="multipleChoiceSection" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">الخيارات</h3>
                    <button type="button" onclick="addOption()"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors"
                            style="background-color: #ede9fe; color: #5b21b6;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        إضافة خيار
                    </button>
                </div>
                <div id="optionsContainer" class="space-y-3">
                    @for($i = 0; $i < 4; $i++)
                        <div class="option-row flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                            <input type="checkbox" name="options[{{ $i }}][is_correct]" value="1"
                                   class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                            <input type="text" name="options[{{ $i }}][text_ar]" placeholder="الخيار {{ $i + 1 }}"
                                   class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                            <button type="button" onclick="removeOption(this)"
                                    class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endfor
                </div>
                <p class="mt-2 text-sm text-gray-500">حدد الإجابة الصحيحة بالضغط على المربع بجانب الخيار</p>
            </div>

            <!-- True/False Section -->
            <div id="trueFalseSection" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 hidden">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">الإجابة الصحيحة</h3>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <input type="radio" name="correct_answer" value="true" {{ old('correct_answer') === 'true' ? 'checked' : '' }}
                               class="text-green-600 focus:ring-green-500">
                        <span class="font-medium text-gray-900 dark:text-white">صح</span>
                    </label>
                    <label class="flex items-center gap-3 p-4 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <input type="radio" name="correct_answer" value="false" {{ old('correct_answer') === 'false' ? 'checked' : '' }}
                               class="text-red-600 focus:ring-red-500">
                        <span class="font-medium text-gray-900 dark:text-white">خطأ</span>
                    </label>
                </div>
            </div>

            <!-- Explanation -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <label for="explanation_ar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    شرح الإجابة (اختياري)
                </label>
                <textarea name="explanation_ar" id="explanation_ar" rows="2"
                          class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                          placeholder="شرح يظهر للطالب بعد إجابة السؤال">{{ old('explanation_ar') }}</textarea>
            </div>

            <!-- Submit -->
            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('teacher.quizzes.show', [$subject->id, $quiz->id]) }}"
                   class="px-6 py-2.5 rounded-xl font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    إلغاء
                </a>
                <button type="submit" name="add_another" value="1"
                        class="px-6 py-2.5 rounded-xl font-medium transition-all"
                        style="background-color: #ede9fe; color: #5b21b6;">
                    حفظ وإضافة سؤال آخر
                </button>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl font-bold text-white transition-all"
                        style="background-color: #8b5cf6;">
                    حفظ السؤال
                </button>
            </div>
        </div>
    </form>
</div>

<script>
let optionIndex = 4;

function handleTypeChange() {
    const type = document.getElementById('type').value;
    const multipleChoiceSection = document.getElementById('multipleChoiceSection');
    const trueFalseSection = document.getElementById('trueFalseSection');

    if (type === 'multiple_choice') {
        multipleChoiceSection.classList.remove('hidden');
        trueFalseSection.classList.add('hidden');
    } else if (type === 'true_false') {
        multipleChoiceSection.classList.add('hidden');
        trueFalseSection.classList.remove('hidden');
    } else {
        multipleChoiceSection.classList.add('hidden');
        trueFalseSection.classList.add('hidden');
    }
}

function addOption() {
    const container = document.getElementById('optionsContainer');
    const html = `
        <div class="option-row flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
            <input type="checkbox" name="options[${optionIndex}][is_correct]" value="1"
                   class="rounded border-gray-300 text-green-600 focus:ring-green-500">
            <input type="text" name="options[${optionIndex}][text_ar]" placeholder="الخيار ${optionIndex + 1}"
                   class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
            <button type="button" onclick="removeOption(this)"
                    class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    optionIndex++;
}

function removeOption(button) {
    const rows = document.querySelectorAll('.option-row');
    if (rows.length > 2) {
        button.closest('.option-row').remove();
    } else {
        alert('يجب أن يكون هناك خياران على الأقل');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', handleTypeChange);
</script>
@endsection
