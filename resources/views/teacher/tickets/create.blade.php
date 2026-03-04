@extends('layouts.dashboard')

@section('title', 'إنشاء تذكرة جديدة')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('teacher.tickets.index') }}" class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إنشاء تذكرة جديدة</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">تواصل مع فريق الدعم الفني</p>
        </div>
    </div>
</div>

@if($errors->any())
<div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-600 dark:bg-red-900 dark:text-red-200">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form -->
    <div class="lg:col-span-2">
        <form action="{{ route('teacher.tickets.store') }}" method="POST" enctype="multipart/form-data" class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden shadow-lg">
            @csrf

            <div class="p-6 border-b border-gray-200 dark:border-gray-700" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <h2 class="text-lg font-semibold text-white">معلومات التذكرة</h2>
                <p class="text-sm mt-1" style="color: rgba(255,255,255,0.8);">يرجى ملء جميع الحقول المطلوبة</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Subject -->
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        عنوان التذكرة <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-colors"
                           placeholder="أدخل عنوان موجز للمشكلة">
                </div>

                <!-- Category & Priority -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            الفئة <span class="text-red-500">*</span>
                        </label>
                        <select name="category" id="category" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-colors">
                            <option value="">-- اختر الفئة --</option>
                            <option value="technical" {{ old('category') == 'technical' ? 'selected' : '' }}>تقني</option>
                            <option value="academic" {{ old('category') == 'academic' ? 'selected' : '' }}>أكاديمي</option>
                            <option value="financial" {{ old('category') == 'financial' ? 'selected' : '' }}>مالي</option>
                            <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            الأولوية <span class="text-red-500">*</span>
                        </label>
                        <select name="priority" id="priority" required
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-colors">
                            <option value="">-- اختر الأولوية --</option>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                            <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                        </select>
                    </div>
                </div>

                <!-- Message -->
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        تفاصيل المشكلة <span class="text-red-500">*</span>
                    </label>
                    <textarea name="message" id="message" rows="6" required
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-colors"
                              placeholder="اشرح مشكلتك بالتفصيل...">{{ old('message') }}</textarea>
                </div>

                <!-- Attachment -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">صورة مرفقة (اختياري)</label>
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-5 text-center cursor-pointer transition-colors hover:border-blue-400"
                         id="dropzone-teacher"
                         ondragover="event.preventDefault(); this.style.borderColor='#3b82f6';"
                         ondragleave="this.style.borderColor='';"
                         ondrop="handleDrop(event, 'attachment-teacher', 'preview-teacher', 'dropzone-teacher')">
                        <input type="file" name="attachment" id="attachment-teacher" accept="image/*" class="hidden"
                               onchange="previewImage(this, 'preview-teacher')">
                        <div id="preview-teacher" style="display:none;" class="mb-3">
                            <img id="preview-teacher-img" src="" alt="معاينة" class="max-h-48 rounded-lg mx-auto">
                        </div>
                        <div id="dropzone-teacher-placeholder">
                            <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm text-gray-500 mb-1">اسحب الصورة هنا أو
                                <button type="button" onclick="document.getElementById('attachment-teacher').click()"
                                        class="text-blue-500 font-semibold hover:underline bg-transparent border-none cursor-pointer">اختر صورة</button>
                            </p>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">PNG, JPG, GIF, WEBP - الحد الأقصى 5MB</p>
                    </div>
                    @error('attachment')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3" style="background: #f9fafb;">
                <a href="{{ route('teacher.tickets.index') }}"
                   class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    إلغاء
                </a>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white rounded-lg transition-all shadow-lg hover:shadow-xl"
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    إرسال التذكرة
                </button>
            </div>
        </form>
    </div>

    <!-- Sidebar Tips -->
    <div class="space-y-6">
        <!-- Tips Card -->
        <div class="rounded-xl shadow-lg p-5" style="background: linear-gradient(135deg, #0071AA 0%, #0f172a 100%);">
            <h3 class="text-base font-bold text-white mb-4 pb-2" style="border-bottom: 1px solid rgba(255,255,255,0.2);">نصائح لتذكرة فعالة</h3>

            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" style="background: rgba(255,255,255,0.2); color: white;">1</div>
                    <p class="text-sm text-white" style="opacity: 0.9;">اكتب عنوان واضح يصف المشكلة</p>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" style="background: rgba(255,255,255,0.2); color: white;">2</div>
                    <p class="text-sm text-white" style="opacity: 0.9;">اختر الفئة المناسبة لتوجيه التذكرة</p>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" style="background: rgba(255,255,255,0.2); color: white;">3</div>
                    <p class="text-sm text-white" style="opacity: 0.9;">اشرح المشكلة بالتفصيل مع الخطوات</p>
                </div>

                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" style="background: rgba(255,255,255,0.2); color: white;">4</div>
                    <p class="text-sm text-white" style="opacity: 0.9;">حدد الأولوية بدقة حسب أهمية المشكلة</p>
                </div>
            </div>
        </div>

        <!-- Priority Guide -->
        <div class="rounded-xl shadow-lg p-5" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
            <h3 class="text-base font-bold text-white mb-4 pb-2" style="border-bottom: 1px solid rgba(255,255,255,0.2);">دليل الأولويات</h3>

            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-3 h-3 rounded-full" style="background: #6b7280;"></div>
                    <div>
                        <span class="text-sm font-medium text-white">منخفضة</span>
                        <p class="text-xs" style="color: rgba(255,255,255,0.7);">استفسارات عامة</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-3 h-3 rounded-full" style="background: #3b82f6;"></div>
                    <div>
                        <span class="text-sm font-medium text-white">متوسطة</span>
                        <p class="text-xs" style="color: rgba(255,255,255,0.7);">مشاكل لا تؤثر على العمل</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-3 h-3 rounded-full" style="background: #f59e0b;"></div>
                    <div>
                        <span class="text-sm font-medium text-white">عالية</span>
                        <p class="text-xs" style="color: rgba(255,255,255,0.7);">مشاكل تؤثر على العمل</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="rounded-xl shadow-lg p-5" style="background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);">
            <h3 class="text-sm font-bold text-white mb-2">للحالات الطارئة</h3>
            <p class="text-xs leading-relaxed" style="color: rgba(255,255,255,0.9);">
                في حالة وجود مشكلة طارئة تؤثر على سير العمل بشكل كامل، يرجى التواصل مباشرة مع الإدارة.
            </p>
        </div>
    </div>
</div>
@push('scripts')
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const img = document.getElementById(previewId + '-img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
function handleDrop(event, inputId, previewId, dropzoneId) {
    event.preventDefault();
    document.getElementById(dropzoneId).style.borderColor = '';
    const input = document.getElementById(inputId);
    input.files = event.dataTransfer.files;
    previewImage(input, previewId);
}
</script>
@endpush
@endsection
