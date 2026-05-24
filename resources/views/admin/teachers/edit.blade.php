@extends('layouts.dashboard')

@section('title', 'تعديل بيانات المدرب')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.teachers.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل بيانات المدرب</h1>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400">تعديل بيانات: {{ $teacher->name }}</p>
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

<form action="{{ route('admin.teachers.update', $teacher) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

            <!-- الاسم -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الاسم الكامل <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $teacher->name) }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="الاسم الكامل للمدرب">
            </div>

            <!-- البريد الإلكتروني -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    البريد الإلكتروني <span class="text-error-500">*</span>
                </label>
                <input type="email"
                       name="email"
                       value="{{ old('email', $teacher->email) }}"
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
                       value="{{ old('national_id', $teacher->national_id) }}"
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
                       value="{{ old('phone', $teacher->phone) }}"
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
                        <option value="male"   {{ old('gender', $teacher->gender) === 'male'   ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ old('gender', $teacher->gender) === 'female' ? 'selected' : '' }}>أنثى</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الجنسية</label>
                    <select name="nationality"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="">اختر الجنسية</option>
                        @foreach(['سعودي','إماراتي','كويتي','بحريني','قطري','عُماني','يمني','مصري','أردني','سوري','لبناني','عراقي','فلسطيني','سوداني','مغربي','جزائري','تونسي','ليبي','باكستاني','هندي','بنغلاديشي','فلبيني','إندونيسي','أخرى'] as $nat)
                        <option value="{{ $nat }}" {{ old('nationality', $teacher->nationality) === $nat ? 'selected' : '' }}>{{ $nat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- تغيير كلمة المرور -->
            <div class="md:col-span-2 border-t border-gray-200 dark:border-gray-700 pt-6 mt-2">
                <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-1">تغيير كلمة المرور</h3>
                <p class="text-sm text-gray-400 dark:text-gray-500 mb-4">اترك الحقول فارغة إذا كنت لا تريد تغيير كلمة المرور</p>
            </div>

            <!-- كلمة المرور الجديدة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    كلمة المرور الجديدة
                </label>
                <input type="password"
                       name="password"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="كلمة مرور جديدة (8 أحرف على الأقل)">
            </div>

            <!-- تأكيد كلمة المرور -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    تأكيد كلمة المرور
                </label>
                <input type="password"
                       name="password_confirmation"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="أعد كتابة كلمة المرور">
            </div>

        </div>

        <!-- الأزرار -->
        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
            <a href="{{ route('admin.teachers.index') }}"
               class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                إلغاء
            </a>
            <button type="submit"
                    class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                حفظ التعديلات
            </button>
        </div>
    </div>
</form>
@endsection
