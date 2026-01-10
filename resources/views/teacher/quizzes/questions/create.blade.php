@extends('layouts.dashboard')

@section('title', 'إضافة سؤال')

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

    .option-row input[type="checkbox"] {
        width: 22px;
        height: 22px;
        border-radius: 6px;
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

    /* True/False Options */
    .tf-option {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        border-radius: 14px;
        border: 2px solid #e5e7eb;
        background: #f9fafb;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .tf-option:hover {
        border-color: #d1d5db;
    }

    .tf-option.selected {
        border-color: #0071AA;
        background: rgba(0, 113, 170, 0.05);
    }

    .tf-option input[type="radio"] {
        width: 20px;
        height: 20px;
        accent-color: #0071AA;
    }

    .tf-option span {
        font-weight: 600;
        color: #374151;
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
    }

    .add-btn:hover {
        background: #cce9f5;
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

    .btn-secondary {
        background: #e6f4fa;
        color: #0071AA;
    }

    .btn-secondary:hover {
        background: #cce9f5;
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

    .dark .tf-option {
        background: #374151;
        border-color: #4b5563;
    }

    .dark .tf-option span {
        color: #d1d5db;
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
                <h1 class="text-2xl font-bold text-white">إضافة سؤال جديد</h1>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('teacher.quizzes.questions.store', [$subject->id, $quiz->id]) }}" method="POST" enctype="multipart/form-data" id="questionForm">
        @csrf

        <div class="form-card">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Question Type -->
                <div>
                    <label for="type" class="form-label">
                        نوع السؤال <span class="required">*</span>
                    </label>
                    <select name="type" id="type" required onchange="handleTypeChange()" class="form-select">
                        <option value="multiple_choice" {{ old('type') === 'multiple_choice' ? 'selected' : '' }}>اختيار من متعدد</option>
                        <option value="true_false" {{ old('type') === 'true_false' ? 'selected' : '' }}>صح وخطأ</option>
                        <option value="short_answer" {{ old('type') === 'short_answer' ? 'selected' : '' }}>إجابة قصيرة</option>
                        <option value="essay" {{ old('type') === 'essay' ? 'selected' : '' }}>مقالي</option>
                    </select>
                </div>

                <!-- Marks -->
                <div>
                    <label for="marks" class="form-label">
                        الدرجة <span class="required">*</span>
                    </label>
                    <input type="number" name="marks" id="marks" value="{{ old('marks', 1) }}" min="0" step="any" required class="form-input">
                </div>

                <!-- Order -->
                <div>
                    <label for="order" class="form-label">
                        الترتيب <span class="required">*</span>
                    </label>
                    <input type="number" name="order" id="order" value="{{ old('order', $nextOrder) }}" min="1" required class="form-input">
                </div>
            </div>

            <!-- Question Text -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Question Arabic -->
                <div>
                    <label for="question_ar" class="form-label">
                        نص السؤال (عربي) <span class="required">*</span>
                    </label>
                    <textarea name="question_ar" id="question_ar" rows="4" required class="form-textarea" placeholder="اكتب نص السؤال هنا...">{{ old('question_ar') }}</textarea>
                    @error('question_ar')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Question English -->
                <div>
                    <label for="question_en" class="form-label">
                        نص السؤال (إنجليزي)
                    </label>
                    <textarea name="question_en" id="question_en" rows="4" class="form-textarea" placeholder="Enter question text here...">{{ old('question_en') }}</textarea>
                </div>
            </div>

            <!-- Image Upload -->
            <div class="mb-8">
                <label for="image" class="form-label">
                    صورة السؤال (اختياري)
                </label>
                <input type="file" name="image" id="image" accept="image/*" class="form-input">
                <p class="hint-text">الصور المدعومة: JPG, PNG, GIF (حد أقصى 2 ميجابايت)</p>
            </div>

            <!-- Multiple Choice Options -->
            <div id="multipleChoiceSection">
                <div class="section-title">
                    <span>الخيارات</span>
                    <button type="button" onclick="addOption()" class="add-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        إضافة خيار
                    </button>
                </div>
                <div id="optionsContainer">
                    @for($i = 0; $i < 4; $i++)
                        <div class="option-row">
                            <input type="checkbox" name="options[{{ $i }}][is_correct]" value="1" title="تحديد كإجابة صحيحة">
                            <input type="text" name="options[{{ $i }}][text_ar]" placeholder="الخيار {{ $i + 1 }}" class="option-input">
                            <button type="button" onclick="removeOption(this)" class="remove-btn" title="حذف الخيار">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endfor
                </div>
                <p class="hint-text">حدد الإجابة الصحيحة بالضغط على المربع بجانب الخيار</p>
            </div>

            <!-- True/False Section -->
            <div id="trueFalseSection" class="hidden">
                <div class="section-title">
                    <span>الإجابة الصحيحة</span>
                </div>
                <div class="flex items-center gap-4">
                    <label class="tf-option" onclick="selectTF(this)">
                        <input type="radio" name="correct_answer" value="true" {{ old('correct_answer') === 'true' ? 'checked' : '' }}>
                        <span>صح</span>
                    </label>
                    <label class="tf-option" onclick="selectTF(this)">
                        <input type="radio" name="correct_answer" value="false" {{ old('correct_answer') === 'false' ? 'checked' : '' }}>
                        <span>خطأ</span>
                    </label>
                </div>
            </div>

            <!-- Explanation -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <label for="explanation_ar" class="form-label">
                    شرح الإجابة (اختياري)
                </label>
                <textarea name="explanation_ar" id="explanation_ar" rows="3" class="form-textarea" placeholder="شرح يظهر للطالب بعد إجابة السؤال...">{{ old('explanation_ar') }}</textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('teacher.quizzes.show', [$subject->id, $quiz->id]) }}" class="btn btn-cancel">
                    إلغاء
                </a>
                <button type="submit" name="add_another" value="1" class="btn btn-secondary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    حفظ وإضافة سؤال آخر
                </button>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
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
        <div class="option-row">
            <input type="checkbox" name="options[${optionIndex}][is_correct]" value="1" title="تحديد كإجابة صحيحة">
            <input type="text" name="options[${optionIndex}][text_ar]" placeholder="الخيار ${optionIndex + 1}" class="option-input">
            <button type="button" onclick="removeOption(this)" class="remove-btn" title="حذف الخيار">
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

function selectTF(label) {
    document.querySelectorAll('.tf-option').forEach(el => el.classList.remove('selected'));
    label.classList.add('selected');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    handleTypeChange();

    // Mark selected TF option
    const checkedTF = document.querySelector('input[name="correct_answer"]:checked');
    if (checkedTF) {
        checkedTF.closest('.tf-option').classList.add('selected');
    }
});
</script>
@endsection
