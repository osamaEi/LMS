@extends('layouts.dashboard')

@section('title', 'تعديل السؤال')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #004466 100%);
        border-radius: 20px;
        padding: 1.5rem 2rem;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 60%;
        height: 200%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .back-btn {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .back-btn:hover {
        background: rgba(255,255,255,0.25);
        transform: translateX(4px);
    }

    .form-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
        padding: 2rem;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-label .required {
        color: #ef4444;
        margin-right: 0.25rem;
    }

    /* Custom Input Styles */
    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 0.95rem;
        color: #1f2937;
        background-color: #f9fafb;
        transition: all 0.2s ease;
        outline: none;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        border-color: #0071AA;
        background-color: white;
        box-shadow: 0 0 0 3px rgba(0, 113, 170, 0.1);
    }

    .form-input::placeholder,
    .form-textarea::placeholder {
        color: #9ca3af;
    }

    .form-select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: left 0.75rem center;
        background-size: 1.25rem;
        padding-left: 2.5rem;
    }

    .form-textarea {
        resize: vertical;
        min-height: 80px;
    }

    /* Section Title */
    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #111827;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Options Container */
    .option-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 14px;
        background: #f9fafb;
        border: 2px solid #e5e7eb;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }

    .option-row:hover {
        border-color: #d1d5db;
    }

    .option-row input[type="radio"] {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #d1d5db;
        cursor: pointer;
        accent-color: #10b981;
        flex-shrink: 0;
    }

    .option-row .option-input {
        flex: 1;
        padding: 0.625rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 0.95rem;
        color: #1f2937;
        background-color: white;
        transition: all 0.2s ease;
        outline: none;
    }

    .option-row .option-input:focus {
        border-color: #0071AA;
        box-shadow: 0 0 0 3px rgba(0, 113, 170, 0.1);
    }

    .option-row .option-input::placeholder {
        color: #9ca3af;
    }

    .remove-btn {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ef4444;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .remove-btn:hover {
        background: #fee2e2;
    }

    /* Add Button */
    .add-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #0071AA;
        background: #e6f4fa;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: 0.75rem;
    }

    .add-btn:hover {
        background: #cce9f5;
    }

    /* Current Image */
    .current-image-box {
        padding: 1rem;
        border-radius: 14px;
        background: #f9fafb;
        border: 2px solid #e5e7eb;
        margin-bottom: 1rem;
    }

    .current-image-box img {
        max-width: 300px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
    }

    /* Submit Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0071AA, #005a88);
        color: white;
        box-shadow: 0 4px 15px rgba(0, 113, 170, 0.25);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 113, 170, 0.35);
    }

    .btn-cancel {
        background: #f3f4f6;
        color: #6b7280;
    }

    .btn-cancel:hover {
        background: #e5e7eb;
        color: #374151;
    }

    /* Hint Text */
    .hint-text {
        margin-top: 0.5rem;
        font-size: 0.8rem;
        color: #6b7280;
    }

    /* Dark Mode */
    .dark .form-card {
        background: #1f2937;
        border-color: #374151;
    }

    .dark .form-label {
        color: #d1d5db;
    }

    .dark .form-input,
    .dark .form-select,
    .dark .form-textarea {
        background-color: #374151;
        border-color: #4b5563;
        color: #f9fafb;
    }

    .dark .form-input:focus,
    .dark .form-select:focus,
    .dark .form-textarea:focus {
        background-color: #1f2937;
        border-color: #0071AA;
    }

    .dark .section-title {
        color: #f9fafb;
        border-color: #374151;
    }

    .dark .option-row {
        background: #374151;
        border-color: #4b5563;
    }

    .dark .option-row .option-input {
        background-color: #1f2937;
        border-color: #4b5563;
        color: #f9fafb;
    }

    .dark .current-image-box {
        background: #374151;
        border-color: #4b5563;
    }

    .dark .btn-cancel {
        background: #374151;
        color: #9ca3af;
    }

    .dark .btn-cancel:hover {
        background: #4b5563;
        color: #f9fafb;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="page-header">
        <div class="relative z-10 flex items-center gap-4">
            <a href="{{ route('teacher.quizzes.show', [$subject->id, $quiz->id]) }}" class="back-btn">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <p class="text-white/70 text-sm font-medium">{{ $quiz->title_ar }}</p>
                <h1 class="text-2xl font-bold text-white">تعديل السؤال</h1>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('teacher.quizzes.questions.update', [$subject->id, $quiz->id, $question->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-card">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Question Type -->
                <div>
                    <label for="type" class="form-label">
                        نوع السؤال <span class="required">*</span>
                    </label>
                    <select name="type" id="type" required onchange="toggleOptions()" class="form-select">
                        <option value="multiple_choice" {{ old('type', $question->type) === 'multiple_choice' ? 'selected' : '' }}>اختيار من متعدد</option>
                        <option value="true_false" {{ old('type', $question->type) === 'true_false' ? 'selected' : '' }}>صح أو خطأ</option>
                        <option value="short_answer" {{ old('type', $question->type) === 'short_answer' ? 'selected' : '' }}>إجابة قصيرة</option>
                        <option value="essay" {{ old('type', $question->type) === 'essay' ? 'selected' : '' }}>مقالي</option>
                    </select>
                </div>

                <!-- Marks -->
                <div>
                    <label for="marks" class="form-label">
                        الدرجة <span class="required">*</span>
                    </label>
                    <input type="number" name="marks" id="marks" value="{{ old('marks', $question->marks) }}" min="0" step="any" required class="form-input">
                </div>

                <!-- Order -->
                <div>
                    <label for="order" class="form-label">
                        الترتيب
                    </label>
                    <input type="number" name="order" id="order" value="{{ old('order', $question->order) }}" min="1" class="form-input">
                </div>
            </div>

            <!-- Question Text -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Question Arabic -->
                <div>
                    <label for="question_text_ar" class="form-label">
                        نص السؤال (عربي) <span class="required">*</span>
                    </label>
                    <textarea name="question_text_ar" id="question_text_ar" rows="4" required class="form-textarea" placeholder="اكتب نص السؤال هنا...">{{ old('question_text_ar', $question->question_text_ar) }}</textarea>
                </div>

                <!-- Question English -->
                <div>
                    <label for="question_text_en" class="form-label">
                        نص السؤال (إنجليزي)
                    </label>
                    <textarea name="question_text_en" id="question_text_en" rows="4" class="form-textarea" placeholder="Enter question text here...">{{ old('question_text_en', $question->question_text_en) }}</textarea>
                </div>
            </div>

            <!-- Image Upload -->
            <div class="mb-8">
                <label class="form-label">صورة السؤال</label>
                @if($question->image_path)
                <div class="current-image-box">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 font-medium">الصورة الحالية:</p>
                    <img src="{{ Storage::url($question->image_path) }}" alt="صورة السؤال">
                </div>
                @endif
                <input type="file" name="image" accept="image/*" class="form-input">
                <p class="hint-text">اتركه فارغاً للاحتفاظ بالصورة الحالية. الصور المدعومة: JPG, PNG, GIF (حد أقصى 2 ميجابايت)</p>
            </div>

            <!-- Options for Multiple Choice -->
            <div id="optionsContainer" class="{{ in_array($question->type, ['multiple_choice', 'true_false']) ? '' : 'hidden' }} mb-8">
                <div class="section-title">
                    <span>الخيارات</span>
                </div>
                <p class="hint-text mb-4" style="margin-top: -1rem;">اختر الإجابة الصحيحة بالضغط على الدائرة بجانب الخيار</p>

                <div id="optionsList">
                    @foreach($question->options as $index => $option)
                    <div class="option-row">
                        <input type="radio" name="correct_option" value="{{ $index }}" {{ $option->is_correct ? 'checked' : '' }}>
                        <input type="text" name="options[{{ $index }}][text_ar]"
                               value="{{ old('options.' . $index . '.text_ar', $option->option_text_ar) }}"
                               class="option-input" placeholder="نص الخيار (عربي)">
                        <input type="hidden" name="options[{{ $index }}][id]" value="{{ $option->id }}">
                        @if($index > 1)
                        <button type="button" onclick="removeOption(this)" class="remove-btn" title="حذف الخيار">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                        @else
                        <div style="width: 38px;"></div>
                        @endif
                    </div>
                    @endforeach
                </div>

                <button type="button" onclick="addOption()" class="add-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    إضافة خيار
                </button>
            </div>

            <!-- Explanation -->
            <div class="mb-8">
                <label for="explanation_ar" class="form-label">
                    شرح الإجابة (اختياري)
                </label>
                <textarea name="explanation_ar" id="explanation_ar" rows="3" class="form-textarea" placeholder="سيظهر للطالب بعد تسليم الاختبار...">{{ old('explanation_ar', $question->explanation_ar) }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-6 border-t border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('teacher.quizzes.show', [$subject->id, $quiz->id]) }}" class="btn btn-cancel">
                    إلغاء
                </a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    حفظ التغييرات
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    let optionCount = {{ $question->options->count() }};

    function toggleOptions() {
        const type = document.getElementById('type').value;
        const container = document.getElementById('optionsContainer');

        if (type === 'multiple_choice' || type === 'true_false') {
            container.classList.remove('hidden');

            if (type === 'true_false') {
                const optionsList = document.getElementById('optionsList');
                optionsList.innerHTML = `
                    <div class="option-row">
                        <input type="radio" name="correct_option" value="0">
                        <input type="text" name="options[0][text_ar]" value="صح" class="option-input" readonly>
                        <div style="width: 38px;"></div>
                    </div>
                    <div class="option-row">
                        <input type="radio" name="correct_option" value="1">
                        <input type="text" name="options[1][text_ar]" value="خطأ" class="option-input" readonly>
                        <div style="width: 38px;"></div>
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
        newOption.className = 'option-row';
        newOption.innerHTML = `
            <input type="radio" name="correct_option" value="${optionCount}">
            <input type="text" name="options[${optionCount}][text_ar]" class="option-input" placeholder="نص الخيار (عربي)">
            <button type="button" onclick="removeOption(this)" class="remove-btn" title="حذف الخيار">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        `;
        optionsList.appendChild(newOption);
        optionCount++;
    }

    function removeOption(button) {
        const rows = document.querySelectorAll('.option-row');
        if (rows.length > 2) {
            button.closest('.option-row').remove();
            // Re-index remaining options
            document.querySelectorAll('.option-row').forEach((row, index) => {
                row.querySelector('input[type="radio"]').value = index;
                const textInput = row.querySelector('input[type="text"]');
                textInput.name = `options[${index}][text_ar]`;
            });
        } else {
            alert('يجب أن يكون هناك خياران على الأقل');
        }
    }
</script>
@endsection
