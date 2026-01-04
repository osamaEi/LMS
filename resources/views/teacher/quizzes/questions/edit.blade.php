@extends('layouts.dashboard')

@section('title', 'تعديل السؤال')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(135deg, #0071AA, #005a88);">
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
                <h1 class="text-2xl font-bold">تعديل السؤال</h1>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('teacher.quizzes.questions.update', [$subject->id, $quiz->id, $question->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
            <div class="space-y-6">
                <!-- Question Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        نوع السؤال <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="type" required
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                            onchange="toggleOptions()">
                        <option value="multiple_choice" {{ old('type', $question->type) === 'multiple_choice' ? 'selected' : '' }}>اختيار من متعدد</option>
                        <option value="true_false" {{ old('type', $question->type) === 'true_false' ? 'selected' : '' }}>صح أو خطأ</option>
                        <option value="short_answer" {{ old('type', $question->type) === 'short_answer' ? 'selected' : '' }}>إجابة قصيرة</option>
                        <option value="essay" {{ old('type', $question->type) === 'essay' ? 'selected' : '' }}>مقالي</option>
                    </select>
                </div>

                <!-- Question Text Arabic -->
                <div>
                    <label for="question_text_ar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        نص السؤال (عربي) <span class="text-red-500">*</span>
                    </label>
                    <textarea name="question_text_ar" id="question_text_ar" rows="3" required
                              class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">{{ old('question_text_ar', $question->question_text_ar) }}</textarea>
                </div>

                <!-- Question Text English -->
                <div>
                    <label for="question_text_en" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        نص السؤال (إنجليزي)
                    </label>
                    <textarea name="question_text_en" id="question_text_en" rows="3"
                              class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">{{ old('question_text_en', $question->question_text_en) }}</textarea>
                </div>

                <!-- Marks -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="marks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            الدرجة <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="marks" id="marks" value="{{ old('marks', $question->marks) }}" min="0.5" step="0.5" required
                               class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                    </div>
                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            الترتيب
                        </label>
                        <input type="number" name="order" id="order" value="{{ old('order', $question->order) }}" min="1"
                               class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                    </div>
                </div>

                <!-- Image -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        صورة السؤال
                    </label>
                    @if($question->image_path)
                    <div class="mb-3 p-3 rounded-xl" style="background-color: #f9fafb;">
                        <p class="text-sm text-gray-600 mb-2">الصورة الحالية:</p>
                        <img src="{{ Storage::url($question->image_path) }}" alt="صورة السؤال" class="max-w-xs rounded-lg">
                    </div>
                    @endif
                    <input type="file" name="image" accept="image/*"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                    <p class="text-xs text-gray-500 mt-1">اتركه فارغاً للاحتفاظ بالصورة الحالية</p>
                </div>

                <!-- Options for Multiple Choice -->
                <div id="optionsContainer" class="{{ in_array($question->type, ['multiple_choice', 'true_false']) ? '' : 'hidden' }}">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        الخيارات <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-3">اختر الإجابة الصحيحة بالضغط على الدائرة</p>

                    <div id="optionsList" class="space-y-3">
                        @foreach($question->options as $index => $option)
                        <div class="option-row flex items-center gap-3">
                            <input type="radio" name="correct_option" value="{{ $index }}"
                                   class="w-5 h-5 text-emerald-600 focus:ring-emerald-500"
                                   {{ $option->is_correct ? 'checked' : '' }}>
                            <input type="text" name="options[{{ $index }}][text_ar]"
                                   value="{{ old('options.' . $index . '.text_ar', $option->option_text_ar) }}"
                                   class="flex-1 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                                   placeholder="نص الخيار (عربي)">
                            <input type="hidden" name="options[{{ $index }}][id]" value="{{ $option->id }}">
                            @if($index > 1)
                            <button type="button" onclick="removeOption(this)"
                                    class="w-10 h-10 rounded-xl flex items-center justify-center text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            @else
                            <div class="w-10"></div>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <button type="button" onclick="addOption()"
                            class="mt-3 text-purple-600 hover:text-purple-700 text-sm font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        إضافة خيار
                    </button>
                </div>

                <!-- Explanation -->
                <div>
                    <label for="explanation_ar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        شرح الإجابة (اختياري)
                    </label>
                    <textarea name="explanation_ar" id="explanation_ar" rows="2"
                              class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                              placeholder="سيظهر للطالب بعد تسليم الاختبار">{{ old('explanation_ar', $question->explanation_ar) }}</textarea>
                </div>
            </div>

            <!-- Submit -->
            <div class="mt-6 flex items-center justify-end gap-3">
                <a href="{{ route('teacher.quizzes.show', [$subject->id, $quiz->id]) }}"
                   class="px-6 py-2.5 rounded-xl font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    إلغاء
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl font-bold text-white transition-all"
                        style="background-color: #0071AA;">
                    حفظ التغييرات
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let optionCount = {{ $question->options->count() }};

    function toggleOptions() {
        const type = document.getElementById('type').value;
        const container = document.getElementById('optionsContainer');

        if (type === 'multiple_choice' || type === 'true_false') {
            container.classList.remove('hidden');

            if (type === 'true_false') {
                // Set to two options for true/false
                const optionsList = document.getElementById('optionsList');
                optionsList.innerHTML = `
                    <div class="option-row flex items-center gap-3">
                        <input type="radio" name="correct_option" value="0" class="w-5 h-5 text-emerald-600 focus:ring-emerald-500">
                        <input type="text" name="options[0][text_ar]" value="صح"
                               class="flex-1 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500" readonly>
                        <div class="w-10"></div>
                    </div>
                    <div class="option-row flex items-center gap-3">
                        <input type="radio" name="correct_option" value="1" class="w-5 h-5 text-emerald-600 focus:ring-emerald-500">
                        <input type="text" name="options[1][text_ar]" value="خطأ"
                               class="flex-1 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500" readonly>
                        <div class="w-10"></div>
                    </div>
                `;
                optionCount = 2;
            }
        } else {
            container.classList.add('hidden');
        }
    }

    function addOption() {
        const optionsList = document.getElementById('optionsList');
        const newOption = document.createElement('div');
        newOption.className = 'option-row flex items-center gap-3';
        newOption.innerHTML = `
            <input type="radio" name="correct_option" value="${optionCount}" class="w-5 h-5 text-emerald-600 focus:ring-emerald-500">
            <input type="text" name="options[${optionCount}][text_ar]"
                   class="flex-1 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                   placeholder="نص الخيار (عربي)">
            <button type="button" onclick="removeOption(this)"
                    class="w-10 h-10 rounded-xl flex items-center justify-center text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        `;
        optionsList.appendChild(newOption);
        optionCount++;
    }

    function removeOption(button) {
        button.closest('.option-row').remove();
        // Re-index remaining options
        document.querySelectorAll('.option-row').forEach((row, index) => {
            row.querySelector('input[type="radio"]').value = index;
            const textInput = row.querySelector('input[type="text"]');
            textInput.name = `options[${index}][text_ar]`;
        });
    }
</script>
@endpush
@endsection
