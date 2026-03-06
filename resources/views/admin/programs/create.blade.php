@extends('layouts.dashboard')

@section('title', 'إضافة دبلومة تعليمي جديد')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.programs.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إضافة دبلومة تعليمي جديد</h1>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400">أدخل بيانات الدبلومة والأرباع الدراسية وقم بتعيين المواد في خطوة واحدة</p>
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

<form action="{{ route('admin.programs.store') }}" method="POST" id="programForm">
    @csrf

    {{-- ===== بيانات الدبلومة ===== --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 mb-6">
        <div class="p-5 border-b border-gray-200 dark:border-gray-800" style="background: linear-gradient(135deg,#3b82f6,#1d4ed8); border-radius: 12px 12px 0 0;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h2 class="text-base font-bold text-white">بيانات الدبلومة</h2>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">اسم الدبلومة (عربي)</label>
                    <input type="text" name="name_ar" value="{{ old('name_ar') }}"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           placeholder="مثال: دبلوم البرمجة وتطوير الويب">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">اسم الدبلومة (إنجليزي)</label>
                    <input type="text" name="name_en" value="{{ old('name_en') }}" dir="ltr"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           placeholder="Web Development Diploma">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">كود الدبلومة</label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           placeholder="مثال: PROG-001">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المدة بالأشهر</label>
                    <input type="number" name="duration_months" value="{{ old('duration_months') }}" min="1"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           placeholder="مثال: 12">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">السعر (ريال)</label>
                    <input type="number" name="price" value="{{ old('price') }}" min="0" step="0.01"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           placeholder="مثال: 5000.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الحالة</label>
                    <select name="status"
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="active" {{ old('status','active') === 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الوصف (عربي)</label>
                    <textarea name="description_ar" rows="3"
                              class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                              placeholder="وصف تفصيلي عن الدبلومة وأهدافه...">{{ old('description_ar') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الوصف (إنجليزي)</label>
                    <textarea name="description_en" rows="3" dir="ltr"
                              class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                              placeholder="Detailed description of the diploma...">{{ old('description_en') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== الأرباع الدراسية ===== --}}
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 mb-6">
        <div class="p-5 border-b border-gray-200 dark:border-gray-800" style="background: linear-gradient(135deg,#7c3aed,#5b21b6); border-radius: 12px 12px 0 0;">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h2 class="text-base font-bold text-white">الأرباع الدراسية والمواد</h2>
                </div>
                <button type="button" onclick="addTerm()"
                        class="flex items-center gap-2 rounded-lg bg-white/20 hover:bg-white/30 px-4 py-2 text-sm font-medium text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    إضافة ربع
                </button>
            </div>
        </div>

        <div id="termsContainer" class="divide-y divide-gray-100 dark:divide-gray-800">
            {{-- Terms added dynamically --}}
        </div>

        <div id="noTermsMsg" class="p-10 text-center">
            <div class="w-14 h-14 rounded-full bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-900 dark:text-white">لم تُضَف أرباع بعد</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">اضغط "إضافة ربع" لإضافة ربع دراسي وتعيين مواده</p>
        </div>
    </div>

    {{-- ===== أزرار الحفظ ===== --}}
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.programs.index') }}"
           class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
            إلغاء
        </a>
        <button type="submit"
                class="rounded-lg px-6 py-2.5 text-sm font-medium text-white transition-colors"
                style="background: linear-gradient(135deg,#3b82f6,#1d4ed8);">
            حفظ الدبلومة
        </button>
    </div>
</form>

<script>
const ALL_SUBJECTS = @json($subjects);
let termCount = 0;

function addTerm() {
    termCount++;
    const idx = termCount - 1;
    document.getElementById('noTermsMsg').style.display = 'none';

    const div = document.createElement('div');
    div.id = 'term_' + termCount;
    div.className = 'p-6';
    div.innerHTML = buildTermHTML(idx, termCount);
    document.getElementById('termsContainer').appendChild(div);
}

function removeTerm(id) {
    document.getElementById('term_' + id).remove();
    if (document.querySelectorAll('#termsContainer > div').length === 0) {
        document.getElementById('noTermsMsg').style.display = '';
    }
}

function buildTermHTML(idx, num) {
    const subjectChips = ALL_SUBJECTS.map(s => `
        <span onclick="toggleChip(this, ${idx}, ${s.id})"
              data-id="${s.id}"
              class="chip inline-flex items-center gap-1 cursor-pointer rounded-full px-3 py-1 text-xs font-medium border border-gray-300 bg-white text-gray-700 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 hover:border-purple-400 transition-colors select-none"
              style="margin:2px;">
            ${s.name_ar || s.name_en}
            ${s.code ? '<span style="opacity:.6;">· '+s.code+'</span>' : ''}
        </span>
    `).join('');

    return `
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold text-white" style="background:linear-gradient(135deg,#7c3aed,#5b21b6);">${num}</div>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">الربع الدراسي ${num}</h3>
            <button type="button" onclick="removeTerm(${num})"
                    class="mr-auto text-xs text-red-500 hover:text-red-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                حذف
            </button>
        </div>
        <input type="hidden" name="terms[${idx}][term_number]" value="${num}">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4 mb-4">
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">اسم الربع (عربي)</label>
                <input type="text" name="terms[${idx}][name_ar]"
                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                       placeholder="الربع الأول">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">اسم الربع (إنجليزي)</label>
                <input type="text" name="terms[${idx}][name_en]" dir="ltr"
                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                       placeholder="First Term">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">تاريخ البداية</label>
                <input type="date" name="terms[${idx}][start_date]"
                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">تاريخ النهاية</label>
                <input type="date" name="terms[${idx}][end_date]"
                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">الحالة</label>
            <select name="terms[${idx}][status]"
                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white">
                <option value="upcoming">قادم</option>
                <option value="active">نشط</option>
                <option value="completed">مكتمل</option>
                <option value="inactive">غير نشط</option>
            </select>
        </div>
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">المواد الدراسية <span id="selectedCount_${idx}" class="text-purple-600 font-bold">(0 محدد)</span></label>
                <input type="text" oninput="filterSubjects(this, ${idx})" placeholder="بحث..."
                       class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs dark:bg-gray-800 dark:border-gray-700 dark:text-white w-40">
            </div>
            <div id="chipsContainer_${idx}" class="rounded-lg border border-gray-200 dark:border-gray-700 p-3" style="max-height:180px;overflow-y:auto;">
                ${subjectChips}
            </div>
            <div id="hiddenSubjects_${idx}"></div>
        </div>
    `;
}

function toggleChip(el, termIdx, subjectId) {
    const selected = el.classList.toggle('chip-selected');
    if (selected) {
        el.style.background = 'linear-gradient(135deg,#7c3aed,#5b21b6)';
        el.style.color = 'white';
        el.style.borderColor = '#7c3aed';
        // add hidden input
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = `terms[${termIdx}][subjects][]`;
        inp.value = subjectId;
        inp.id = `sub_${termIdx}_${subjectId}`;
        document.getElementById('hiddenSubjects_' + termIdx).appendChild(inp);
    } else {
        el.style.background = '';
        el.style.color = '';
        el.style.borderColor = '';
        const inp = document.getElementById(`sub_${termIdx}_${subjectId}`);
        if (inp) inp.remove();
    }
    // update count
    const count = document.querySelectorAll(`#chipsContainer_${termIdx} .chip-selected`).length;
    document.getElementById('selectedCount_' + termIdx).textContent = `(${count} محدد)`;
}

function filterSubjects(input, termIdx) {
    const q = input.value.toLowerCase();
    document.querySelectorAll(`#chipsContainer_${termIdx} .chip`).forEach(chip => {
        chip.style.display = chip.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>

@endsection
