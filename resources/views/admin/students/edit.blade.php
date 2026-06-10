@extends('layouts.dashboard')

@section('title', 'تعديل بيانات المتدرب ')

@section('content')
<style>
    select option { color:#111827 !important; background:#ffffff !important; }
</style>
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.students.show', $student) }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل بيانات المتدرب </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $student->name }} · {{ $student->email }}</p>
        </div>
    </div>
</div>

@if($errors->any())
<div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-4 dark:bg-red-900/20 dark:border-red-800">
    <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-300 space-y-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.students.update', $student) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 space-y-6">

        {{-- Personal Info --}}
        <div>
            <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">المعلومات الشخصية</h2>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        الاسم <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name"
                           value="{{ old('name', $student->name) }}"
                           required
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        البريد الإلكتروني <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email"
                           value="{{ old('email', $student->email) }}"
                           required dir="ltr"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        رقم الهوية <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="national_id"
                           value="{{ old('national_id', $student->national_id) }}"
                           required dir="ltr"
                           maxlength="10" minlength="10" pattern="[0-9]{10}" inputmode="numeric"
                           oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)"
                           title="رقم الهوية يجب أن يكون 10 أرقام"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        رقم الهاتف
                    </label>
                    <input type="text" name="phone"
                           value="{{ old('phone', $student->phone) }}"
                           dir="ltr"
                           maxlength="10"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الجنس</label>
                    <select name="gender" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="">— اختر —</option>
                        <option value="male"   {{ old('gender', $student->gender) === 'male'   ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>أنثى</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">تاريخ الميلاد</label>
                    <input type="date" name="date_of_birth"
                           value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الجنسية</label>
                    <input type="text" name="nationality"
                           value="{{ old('nationality', $student->nationality) }}"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الحالة</label>
                    <select name="status" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="active"    {{ old('status', $student->status) === 'active'    ? 'selected' : '' }}>نشط</option>
                        <option value="pending"   {{ old('status', $student->status) === 'pending'   ? 'selected' : '' }}>قيد المراجعة</option>
                        <option value="inactive"  {{ old('status', $student->status) === 'inactive'  ? 'selected' : '' }}>غير نشط</option>
                        <option value="suspended" {{ old('status', $student->status) === 'suspended' ? 'selected' : '' }}>موقوف</option>
                    </select>
                </div>

            </div>
        </div>

        {{-- Academic Info --}}
        <div class="border-t border-gray-200 dark:border-gray-800 pt-6">
            <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">البيانات الأكاديمية</h2>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">نوع المؤهل التدريبي</label>
                    <input type="text" name="specialization"
                           value="{{ old('specialization', $student->specialization) }}"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">المؤهل التعليمي</label>
                    <select name="specialization_type" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="">— اختر —</option>
                        @foreach(['diploma'=>'دبلوم','bachelor'=>'بكالوريوس','master'=>'ماجستير','phd'=>'دكتوراه','training'=>'تدريب مهني'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('specialization_type', $student->specialization_type) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                        @if($student->specialization_type && !in_array($student->specialization_type, ['diploma','bachelor','master','phd','training']))
                            <option value="{{ $student->specialization_type }}" selected>{{ $student->specialization_type }}</option>
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">تاريخ التخرج</label>
                    <input type="date" name="date_of_graduation"
                           value="{{ old('date_of_graduation', $student->date_of_graduation?->format('Y-m-d')) }}"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>

            </div>

            <div class="mt-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">نبذة شخصية</label>
                <textarea name="bio" rows="3"
                          placeholder="اكتب نبذة مختصرة..."
                          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('bio', $student->bio) }}</textarea>
            </div>
        </div>

        {{-- Photo --}}
        <div class="border-t border-gray-200 dark:border-gray-800 pt-6">
            <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">الصورة الشخصية</h2>
            <div class="flex items-center gap-5">
                <img id="photo-preview"
                     src="{{ $student->profile_photo ? asset('storage/'.$student->profile_photo) : 'https://ui-avatars.com/api/?name='.urlencode($student->name).'&background=0071AA&color=fff&size=120&bold=true' }}"
                     alt="{{ $student->name }}"
                     class="w-20 h-20 rounded-2xl object-cover border-2 border-gray-200 dark:border-gray-700">
                <div>
                    <label class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white" style="background:#0071AA;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        اختر صورة
                        <input type="file" name="profile_photo" accept="image/jpg,image/jpeg,image/png"
                               class="hidden" onchange="previewPhoto(this)">
                    </label>
                    <p class="text-xs text-gray-400 mt-1.5">JPG أو PNG · بحد أقصى 2 ميجابايت</p>
                </div>
            </div>
        </div>

        {{-- Password --}}
        <div class="border-t border-gray-200 dark:border-gray-800 pt-6">
            <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">تغيير كلمة المرور <span class="font-normal normal-case text-gray-400">(اتركها فارغة للإبقاء على الحالية)</span></h2>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        كلمة المرور الجديدة
                    </label>
                    <input type="password" name="password"
                           dir="ltr" autocomplete="new-password"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        تأكيد كلمة المرور
                    </label>
                    <input type="password" name="password_confirmation"
                           dir="ltr" autocomplete="new-password"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>

            </div>
        </div>

        {{-- Assigned Programs --}}
        <div class="border-t border-gray-200 dark:border-gray-800 pt-6">
            <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">البرامج المسجّلة</h2>
            @php
                $typeLabels = ['diploma'=>'دبلومة','course'=>'دورة','english'=>'إنجليزي','training'=>'تدريب'];
                $assigned = old('program_ids', $assignedProgramIds ?? []);
            @endphp
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                @forelse($programs as $p)
                <label class="flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-2.5 cursor-pointer hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">
                    <input type="checkbox" name="program_ids[]" value="{{ $p->id }}"
                           {{ in_array($p->id, $assigned) ? 'checked' : '' }}
                           class="w-4 h-4" style="accent-color:#0071AA;">
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $p->name_ar }}</span>
                    <span class="text-xs text-gray-400 ms-auto">{{ $typeLabels[$p->type] ?? $p->type }}</span>
                </label>
                @empty
                <p class="text-sm text-gray-400">لا توجد برامج نشطة.</p>
                @endforelse
            </div>
            <p class="text-xs text-gray-400 mt-2">أول برنامج مختار يُعتبر البرنامج الأساسي. إسناد المجموعة (الكلاس) يتم من صفحة المجموعات.</p>
        </div>

        {{-- Buttons --}}
        <div class="flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
            <a href="{{ route('admin.students.show', $student) }}"
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
