@extends('layouts.dashboard')

@section('title', 'إضافة  مدرب  جديد')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.teachers.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إضافة  مدرب  جديد</h1>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400">إضافة  مدرب  جديد إلى النظام</p>
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

<form action="{{ route('admin.teachers.store') }}" method="POST">
    @csrf

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- الاسم -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الاسم الكامل <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="الاسم الكامل لل مدرب ">
            </div>

            <!-- البريد الإلكتروني -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    البريد الإلكتروني <span class="text-error-500">*</span>
                </label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="example@email.com">
            </div>

            <!-- رقم الهوية -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رقم الهوية <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="national_id"
                       value="{{ old('national_id') }}"
                       required
                       maxlength="10"
                       pattern="\d{10}"
                       inputmode="numeric"
                       oninput="this.value=this.value.replace(/\D/g,'')"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="رقم الهوية الوطنية (10 أرقام)">
            </div>

            <!-- رقم الهاتف -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رقم الهاتف
                </label>
                <input type="text"
                       name="phone"
                       value="{{ old('phone') }}"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="05xxxxxxxx">
            </div>

            <!-- الجنس والجنسية -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الجنس</label>
                    <select name="gender"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="">اختر</option>
                        <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>أنثى</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الجنسية</label>
                    <select name="nationality"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="">اختر الجنسية</option>
                        @foreach(['سعودي','إماراتي','كويتي','بحريني','قطري','عُماني','يمني','مصري','أردني','سوري','لبناني','عراقي','فلسطيني','سوداني','مغربي','جزائري','تونسي','ليبي','باكستاني','هندي','بنغلاديشي','فلبيني','إندونيسي','أخرى'] as $nat)
                        <option value="{{ $nat }}" {{ old('nationality') === $nat ? 'selected' : '' }}>{{ $nat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- كلمة المرور -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    كلمة المرور <span class="text-error-500">*</span>
                </label>
                <input type="password"
                       name="password"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="كلمة المرور (8 أحرف على الأقل)">
            </div>

            <!-- تأكيد كلمة المرور -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    تأكيد كلمة المرور <span class="text-error-500">*</span>
                </label>
                <input type="password"
                       name="password_confirmation"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="أعد كتابة كلمة المرور">
            </div>
        </div>

    </div>

    {{-- قسم المواد والمقررات --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mt-6">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#e0f2fe;">
                <svg style="width:16px;height:16px;color:#0071AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h2 class="text-base font-bold text-gray-900 dark:text-white">المواد والمقررات</h2>
            <span class="text-xs text-gray-400 font-medium">(اختياري — يمكن التعيين لاحقاً)</span>
        </div>

        {{-- بحث سريع --}}
        <div class="mb-4">
            <input type="text" id="subjectSearch" placeholder="بحث في المواد..."
                   oninput="filterSubjects(this.value)"
                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:border-blue-500">
        </div>

        @if($subjectsByProgram->isNotEmpty())
        <div class="mb-5">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">مواد الدبلومات</p>
            @foreach($subjectsByProgram as $prog)
                @foreach($prog->terms as $term)
                    @if($term->subjects->isNotEmpty())
                    <div class="mb-3">
                        <p class="text-xs font-semibold mb-2" style="color:#7c3aed;">
                            {{ $prog->name_ar }} — {{ $term->name_ar ?? 'الفصل '.$term->term_number }}
                        </p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 subjects-group">
                            @foreach($term->subjects as $subj)
                            <label class="subject-item flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition-all hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 border-gray-200 dark:border-gray-700"
                                   data-name="{{ strtolower($subj->name_ar . ' ' . $subj->name_en) }}">
                                <input type="checkbox" name="subjects[]" value="{{ $subj->id }}"
                                       class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500"
                                       {{ in_array($subj->id, old('subjects', [])) ? 'checked' : '' }}>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 leading-tight">{{ $subj->name_ar ?: $subj->name_en }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            @endforeach
        </div>
        @endif

        @if($courseSubjects->isNotEmpty())
        <div>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">مواد الدورات والبرامج</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 subjects-group">
                @foreach($courseSubjects as $subj)
                <label class="subject-item flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition-all hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 border-gray-200 dark:border-gray-700"
                       data-name="{{ strtolower($subj->name_ar . ' ' . $subj->name_en) }}">
                    <input type="checkbox" name="subjects[]" value="{{ $subj->id }}"
                           class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500"
                           {{ in_array($subj->id, old('subjects', [])) ? 'checked' : '' }}>
                    <div class="min-w-0">
                        <div class="text-xs font-semibold text-gray-700 dark:text-gray-300 leading-tight truncate">{{ $subj->name_ar ?: $subj->name_en }}</div>
                        @if($subj->program)
                        <div class="text-xs text-gray-400 truncate">{{ Str::limit($subj->program->name_ar, 20) }}</div>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>
        </div>
        @endif

        @if($subjectsByProgram->isEmpty() && $courseSubjects->isEmpty())
        <p class="text-sm text-gray-400 text-center py-6">لا توجد مواد مضافة في النظام بعد</p>
        @endif
    </div>

        <!-- الأزرار -->
        <div class="mt-6 flex items-center justify-end gap-3">
            <a href="{{ route('admin.teachers.index') }}"
               class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                إلغاء
            </a>
            <button type="submit"
                    class="rounded-lg px-6 py-2.5 text-sm font-medium text-white transition-colors"
                    style="background:#0071AA;">
                إضافة المدرب
            </button>
        </div>
</form>

@push('scripts')
<script>
function filterSubjects(val) {
    val = val.toLowerCase().trim();
    document.querySelectorAll('.subject-item').forEach(el => {
        el.style.display = (!val || el.dataset.name.includes(val)) ? '' : 'none';
    });
}
</script>
@endpush
@endsection
