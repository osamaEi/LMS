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
                       maxlength="10"
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

    {{-- التعيينات --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mt-6">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#e0f2fe;">
                <svg style="width:16px;height:16px;color:#0071AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <h2 class="text-base font-bold text-gray-900 dark:text-white">التعيينات</h2>
            <span class="text-xs text-gray-400 font-medium">(اختياري — يمكن التعيين لاحقاً)</span>
        </div>

        {{-- Tabs --}}
        <div x-data="{ tab: 'diploma' }" class="w-full">

            {{-- Tab Headers --}}
            <div class="flex gap-1 mb-5 border-b border-gray-200 dark:border-gray-700">
                <button type="button" @click="tab='diploma'"
                    :class="tab==='diploma' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 text-sm transition-colors">
                    مواد الدبلومات
                    @if($subjectsByProgram->isNotEmpty())
                    <span class="mr-1 text-xs bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded-full">
                        {{ $subjectsByProgram->sum(fn($p) => $p->terms->sum(fn($t) => $t->subjects->count())) }}
                    </span>
                    @endif
                </button>
                <button type="button" @click="tab='course'"
                    :class="tab==='course' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 text-sm transition-colors">
                    الدورات
                    @if($coursePrograms->isNotEmpty())
                    <span class="mr-1 text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded-full">{{ $coursePrograms->count() }}</span>
                    @endif
                </button>
                <button type="button" @click="tab='english'"
                    :class="tab==='english' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 text-sm transition-colors">
                    اللغة الإنجليزية
                    @if($englishPrograms->isNotEmpty())
                    <span class="mr-1 text-xs bg-green-100 text-green-700 px-1.5 py-0.5 rounded-full">{{ $englishPrograms->count() }}</span>
                    @endif
                </button>
            </div>

            {{-- Tab: Diploma Subjects --}}
            <div x-show="tab==='diploma'">
                <div class="mb-3">
                    <input type="text" placeholder="بحث في المواد..."
                           oninput="filterItems(this,'diploma-item')"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:border-blue-500">
                </div>
                @forelse($subjectsByProgram as $prog)
                    @foreach($prog->terms as $term)
                        @if($term->subjects->isNotEmpty())
                        <div class="mb-4">
                            <p class="text-xs font-semibold mb-2 px-1" style="color:#7c3aed;">
                                {{ $prog->name_ar }} — {{ $term->name_ar ?? 'الفصل '.$term->term_number }}
                            </p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                                @foreach($term->subjects as $subj)
                                <label class="diploma-item flex items-center gap-2 p-2.5 rounded-lg border cursor-pointer transition-all hover:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 border-gray-200 dark:border-gray-700"
                                       data-name="{{ strtolower($subj->name_ar . ' ' . $subj->name_en) }}">
                                    <input type="checkbox" name="subjects[]" value="{{ $subj->id }}"
                                           class="w-4 h-4 rounded text-purple-600 border-gray-300 focus:ring-purple-500"
                                           {{ in_array($subj->id, old('subjects', [])) ? 'checked' : '' }}>
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 leading-tight">{{ $subj->name_ar ?: $subj->name_en }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @endforeach
                @empty
                <p class="text-sm text-gray-400 text-center py-8">لا توجد مواد دبلومات</p>
                @endforelse
            </div>

            {{-- Tab: Course Programs --}}
            <div x-show="tab==='course'">
                <div class="mb-3">
                    <input type="text" placeholder="بحث في الدورات..."
                           oninput="filterItems(this,'course-item')"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:border-blue-500">
                </div>
                @forelse($coursePrograms as $prog)
                <label class="course-item flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-all hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 border-gray-200 dark:border-gray-700 mb-2"
                       data-name="{{ strtolower($prog->name_ar) }}">
                    <input type="checkbox" name="programs[]" value="{{ $prog->id }}"
                           class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500"
                           {{ in_array($prog->id, old('programs', [])) ? 'checked' : '' }}>
                    <div>
                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $prog->name_ar }}</div>
                        <div class="text-xs text-gray-400">دورة تدريبية</div>
                    </div>
                </label>
                @empty
                <p class="text-sm text-gray-400 text-center py-8">لا توجد دورات</p>
                @endforelse
            </div>

            {{-- Tab: English Programs --}}
            <div x-show="tab==='english'">
                <div class="mb-3">
                    <input type="text" placeholder="بحث في برامج اللغة..."
                           oninput="filterItems(this,'english-item')"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:border-blue-500">
                </div>
                @forelse($englishPrograms as $prog)
                <label class="english-item flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-all hover:border-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 border-gray-200 dark:border-gray-700 mb-2"
                       data-name="{{ strtolower($prog->name_ar) }}">
                    <input type="checkbox" name="programs[]" value="{{ $prog->id }}"
                           class="w-4 h-4 rounded text-green-600 border-gray-300 focus:ring-green-500"
                           {{ in_array($prog->id, old('programs', [])) ? 'checked' : '' }}>
                    <div>
                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $prog->name_ar }}</div>
                        <div class="text-xs text-gray-400">برنامج لغة إنجليزية</div>
                    </div>
                </label>
                @empty
                <p class="text-sm text-gray-400 text-center py-8">لا توجد برامج إنجليزي</p>
                @endforelse
            </div>

        </div>{{-- end x-data --}}
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
function filterItems(input, cls) {
    const val = input.value.toLowerCase().trim();
    document.querySelectorAll('.' + cls).forEach(el => {
        el.style.display = (!val || el.dataset.name.includes(val)) ? '' : 'none';
    });
}
</script>
@endpush
@endsection
