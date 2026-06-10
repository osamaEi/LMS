@extends('layouts.dashboard')

@section('title', 'إضافة متدرب جديد')

@section('content')

{{-- Page Header --}}
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.students.index') }}"
       class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
        <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إضافة متدرب جديد</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">أدخل بيانات المتدرب وأسنده للبرامج</p>
    </div>
</div>

{{-- Validation Errors --}}
@if($errors->any())
<div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
    <ul class="list-inside list-disc space-y-1 text-sm text-red-600 dark:text-red-300">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
@csrf

<div class="space-y-0 rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">

    {{-- ─── Personal Info ─── --}}
    <div class="p-6">
        <h2 class="mb-5 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">المعلومات الشخصية</h2>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    الاسم <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="input-field">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    البريد الإلكتروني <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required dir="ltr"
                       class="input-field">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    رقم الهوية <span class="text-red-500">*</span>
                </label>
                <input type="text" name="national_id" value="{{ old('national_id') }}" required dir="ltr"
                       maxlength="10" minlength="10" pattern="[0-9]{10}" inputmode="numeric"
                       oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)"
                       title="رقم الهوية يجب أن يكون 10 أرقام"
                       class="input-field">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">رقم الهاتف</label>
                <input type="text" name="phone" value="{{ old('phone') }}" dir="ltr" maxlength="10"
                       class="input-field">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">الجنس</label>
                <x-select name="gender" :options="['' => '— اختر —', 'male' => 'ذكر', 'female' => 'أنثى']" :selected="old('gender')" />
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">تاريخ الميلاد</label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                       class="input-field">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">الجنسية</label>
                <input type="text" name="nationality" value="{{ old('nationality') }}"
                       class="input-field">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">الحالة</label>
                <x-select name="status"
                    :options="['active' => 'نشط', 'pending' => 'قيد المراجعة', 'inactive' => 'غير نشط', 'suspended' => 'موقوف']"
                    :selected="old('status', 'active')" />
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">التخصص</label>
                <input type="text" name="specialization" value="{{ old('specialization') }}"
                       class="input-field">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">المؤهل التعليمي</label>
                <x-select name="specialization_type"
                    :options="['' => '— اختر —', 'ثانوي' => 'ثانوي', 'دبلوم' => 'دبلوم', 'بكالوريوس' => 'بكالوريوس', 'ماجستير' => 'ماجستير', 'دكتوراه' => 'دكتوراه']"
                    :selected="old('specialization_type')" />
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">تاريخ التخرج</label>
                <input type="date" name="date_of_graduation" value="{{ old('date_of_graduation') }}"
                       class="input-field">
            </div>

        </div>

        <div class="mt-5">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">نبذة شخصية</label>
            <textarea name="bio" rows="3" placeholder="اكتب نبذة مختصرة..."
                      class="input-field resize-none">{{ old('bio') }}</textarea>
        </div>
    </div>

    {{-- ─── Photo ─── --}}
    <div class="border-t border-gray-100 p-6 dark:border-gray-800">
        <h2 class="mb-5 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">الصورة الشخصية</h2>
        <div class="flex items-center gap-5">
            <img id="photo-preview"
                 src="https://ui-avatars.com/api/?name=%20&background=0071AA&color=fff&size=120&bold=true"
                 alt="preview"
                 class="h-20 w-20 rounded-2xl border-2 border-gray-200 object-cover dark:border-gray-700">
            <div>
                <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white transition-opacity hover:opacity-90" style="background:#0071AA;">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    اختر صورة
                    <input type="file" name="profile_photo" accept="image/jpg,image/jpeg,image/png"
                           class="hidden" onchange="previewPhoto(this)">
                </label>
                <p class="mt-1.5 text-xs text-gray-400">JPG أو PNG · بحد أقصى 2 ميجابايت</p>
            </div>
        </div>
    </div>

    {{-- ─── Password ─── --}}
    <div class="border-t border-gray-100 p-6 dark:border-gray-800">
        <h2 class="mb-5 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">كلمة المرور</h2>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    كلمة المرور <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" required dir="ltr" autocomplete="new-password"
                       class="input-field">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                    تأكيد كلمة المرور <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password_confirmation" required dir="ltr" autocomplete="new-password"
                       class="input-field">
            </div>
        </div>
    </div>

    {{-- ─── Programs ─── --}}
    <div class="border-t border-gray-100 p-6 dark:border-gray-800">
        <h2 class="mb-5 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">البرامج المسجّلة</h2>
        @php
            $typeLabels   = ['diploma' => 'دبلومة', 'course' => 'دورة', 'english' => 'إنجليزي', 'training' => 'تدريب'];
            $typeColors   = ['diploma' => '#7c3aed', 'course' => '#0071AA', 'english' => '#059669', 'training' => '#d97706'];
            $assigned     = old('program_ids', []);
        @endphp
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            @forelse($programs as $p)
            @php $color = $typeColors[$p->type] ?? '#6b7280'; @endphp
            <label class="program-card group relative flex cursor-pointer items-start gap-3 rounded-xl border-2 p-4 transition-all duration-150"
                   style="--accent:{{ $color }};"
                   x-data
                   :class="$el.querySelector('input').checked
                       ? 'border-[var(--accent)] bg-[color-mix(in_srgb,var(--accent)_8%,transparent)]'
                       : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'">

                <input type="checkbox" name="program_ids[]" value="{{ $p->id }}"
                       {{ in_array($p->id, $assigned) ? 'checked' : '' }}
                       class="mt-0.5 h-4 w-4 shrink-0 rounded"
                       style="accent-color:{{ $color }};"
                       @change="
                           const checked = $event.target.checked;
                           $el.closest('label').classList.toggle('border-[var(--accent)]', checked);
                           $el.closest('label').classList.toggle('bg-[color-mix(in_srgb,var(--accent)_8%,transparent)]', checked);
                           $el.closest('label').classList.toggle('border-gray-200', !checked);
                           $el.closest('label').classList.toggle('dark:border-gray-700', !checked);
                       ">

                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-900 dark:text-white leading-snug">{{ $p->name_ar }}</span>
                    </div>
                    <span class="mt-1 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium text-white"
                          style="background:{{ $color }};">
                        {{ $typeLabels[$p->type] ?? $p->type }}
                    </span>
                </div>

                {{-- checkmark icon shown when selected --}}
                <svg class="checkmark-icon h-5 w-5 shrink-0 opacity-0 transition-opacity"
                     style="color:{{ $color }};"
                     fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </label>
            @empty
            <p class="text-sm text-gray-400">لا توجد برامج نشطة.</p>
            @endforelse
        </div>
        <p class="mt-3 text-xs text-gray-400">أول برنامج مختار يُعتبر البرنامج الأساسي. إسناد المجموعة يتم لاحقاً من صفحة المجموعات.</p>
    </div>

    {{-- ─── Actions ─── --}}
    <div class="flex items-center justify-end gap-3 border-t border-gray-100 p-6 dark:border-gray-800">
        <a href="{{ route('admin.students.index') }}"
           class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
            إلغاء
        </a>
        <button type="submit"
                class="rounded-lg px-6 py-2.5 text-sm font-medium text-white transition-opacity hover:opacity-90"
                style="background:#0071AA;">
            إضافة المتدرب
        </button>
    </div>

</div>
</form>

@push('styles')
<style>
.input-field {
    width: 100%;
    border-radius: 0.5rem;
    border: 1px solid #d1d5db;
    background: #ffffff;
    padding: 0.625rem 1rem;
    font-size: 0.875rem;
    color: #111827;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.input-field:focus {
    border-color: #0071AA;
    box-shadow: 0 0 0 2px rgba(0,113,170,.2);
}
.dark .input-field {
    background: #1f2937;
    border-color: #374151;
    color: #f9fafb;
}
.dark .input-field:focus {
    border-color: #0071AA;
}
.program-card:has(input:checked) .checkmark-icon { opacity: 1; }
.program-card:has(input:checked) {
    border-color: var(--accent) !important;
    background-color: color-mix(in srgb, var(--accent) 8%, transparent) !important;
}
</style>
@endpush

@push('scripts')
<script>
function previewPhoto(input) {
    if (!input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => document.getElementById('photo-preview').src = e.target.result;
    reader.readAsDataURL(input.files[0]);
}
</script>
@endpush

@endsection
