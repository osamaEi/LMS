@extends('layouts.dashboard')

@section('title', 'إنشاء استبيان جديد')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.surveys.index') }}" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">إنشاء استبيان جديد</h2>
            <p class="text-gray-600 dark:text-gray-400">قياس رضا المستفيدين (معيار NELC 1.2.11)</p>
        </div>
    </div>

    <form action="{{ route('admin.surveys.store') }}" method="POST" class="space-y-6" x-data="surveyForm()">
        @csrf

        <!-- Basic Info -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">معلومات الاستبيان</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">عنوان الاستبيان *</label>
                    <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 dark:bg-gray-700 dark:text-white" placeholder="مثال: استبيان رضا المتدربين عن البرنامج">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نوع الاستبيان *</label>
                    <select name="type" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 dark:bg-gray-700 dark:text-white">
                        <option value="student">للمتدربين</option>
                        <option value="teacher">للمدربين</option>
                        <option value="general">عام</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المادة (اختياري)</label>
                    <select name="subject_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 dark:bg-gray-700 dark:text-white">
                        <option value="">استبيان عام</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">إلزامي؟</label>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="is_mandatory" value="1" class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                        <span class="text-gray-700 dark:text-gray-300">نعم، الاستبيان إلزامي</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">تاريخ البدء</label>
                    <input type="datetime-local" name="starts_at" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">تاريخ الانتهاء</label>
                    <input type="datetime-local" name="ends_at" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الوصف</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 dark:bg-gray-700 dark:text-white" placeholder="وصف مختصر للاستبيان..."></textarea>
                </div>
            </div>
        </div>

        <!-- Questions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">الأسئلة</h3>
                <button type="button" @click="addQuestion()" class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-brand-500 border border-brand-500 rounded-lg hover:bg-brand-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    إضافة سؤال
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(question, index) in questions" :key="index">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-brand-500 text-white rounded-full text-sm font-medium" x-text="index + 1"></span>
                            <button type="button" @click="removeQuestion(index)" class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نص السؤال *</label>
                                <input type="text" x-model="question.question" :name="'questions['+index+'][question]'" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 dark:bg-gray-700 dark:text-white">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نوع السؤال *</label>
                                <select x-model="question.type" :name="'questions['+index+'][type]'" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 dark:bg-gray-700 dark:text-white">
                                    <option value="rating">تقييم (1-5)</option>
                                    <option value="text">نص حر</option>
                                    <option value="yes_no">نعم/لا</option>
                                    <option value="multiple_choice">اختيار متعدد</option>
                                </select>
                            </div>

                            <div class="flex items-center gap-4">
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" x-model="question.is_required" :name="'questions['+index+'][is_required]'" value="1" class="rounded border-gray-300 text-brand-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">إلزامي</span>
                                </label>
                                <label class="inline-flex items-center gap-2" x-show="question.type === 'rating'">
                                    <input type="checkbox" x-model="question.requires_comment_on_low_rating" :name="'questions['+index+'][requires_comment_on_low_rating]'" value="1" class="rounded border-gray-300 text-brand-500">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">تعليق عند التقييم المنخفض</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="questions.length === 0" class="text-center py-8 text-gray-500">
                <p>لم تتم إضافة أي أسئلة بعد</p>
                <button type="button" @click="addQuestion()" class="mt-2 text-brand-500 hover:text-brand-700">إضافة سؤال</button>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.surveys.index') }}" class="px-6 py-2 text-gray-700 dark:text-gray-300 hover:text-gray-900">إلغاء</a>
            <button type="submit" class="px-6 py-2 text-white bg-brand-500 rounded-lg hover:bg-brand-600">حفظ الاستبيان</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function surveyForm() {
    return {
        questions: [
            { question: '', type: 'rating', is_required: true, requires_comment_on_low_rating: true }
        ],
        addQuestion() {
            this.questions.push({ question: '', type: 'rating', is_required: true, requires_comment_on_low_rating: false });
        },
        removeQuestion(index) {
            this.questions.splice(index, 1);
        }
    }
}
</script>
@endpush
@endsection
