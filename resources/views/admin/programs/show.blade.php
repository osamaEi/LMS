@extends('layouts.dashboard')
@section('title', 'عرض الدبلوم')

@section('content')
<div x-data="{
    termModal: false,
    subjectModal: false,
    teacherModal: false,
    currentTermId: null,
    currentTermName: '',
    currentSubjectId: null,
    currentSubjectName: '',
    currentTeacherId: '',
    openTermModal()   { this.termModal = true; },
    openSubjectModal(id, name) { this.currentTermId = id; this.currentTermName = name; this.subjectModal = true; },
    openTeacherModal(sid, sname, tid) { this.currentSubjectId = sid; this.currentSubjectName = sname; this.currentTeacherId = String(tid ?? ''); this.teacherModal = true; }
}">

    {{-- ── Header ── --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.programs.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $program->name }}</h1>
                @if($program->status === 'active')
                    <span style="background:#dcfce7;color:#16a34a;border-radius:9999px;padding:.15rem .65rem;font-size:.72rem;font-weight:600;">نشط</span>
                @else
                    <span style="background:#f3f4f6;color:#6b7280;border-radius:9999px;padding:.15rem .65rem;font-size:.72rem;font-weight:600;">غير نشط</span>
                @endif
            </div>
            <p class="text-xs text-gray-400 mt-0.5" dir="ltr">{{ $program->code }}</p>
        </div>
        <a href="{{ route('admin.programs.edit', $program) }}"
           class="flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-700 px-3 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            تعديل
        </a>
        <button @click="openTermModal()"
                class="flex items-center gap-1.5 rounded-lg bg-brand-500 px-3 py-2 text-xs font-medium text-white hover:bg-brand-600 transition-colors">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            إضافة ربع
        </button>
    </div>

    {{-- ── Flash ── --}}
    @if(session('success'))
    <div class="mb-4 flex items-center gap-2 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 px-4 py-3 text-sm text-green-800 dark:text-green-200">
        <svg class="h-4 w-4 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- ── Stats ── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $program->terms->count() }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">الأرباع</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-2xl font-bold text-purple-600">{{ $program->terms->sum('subjects_count') }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">المقررات</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-2xl font-bold text-green-600">{{ $program->terms->where('status','active')->count() }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">أرباع نشطة</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="text-2xl font-bold text-blue-600">{{ $program->terms->flatMap->subjects->sum('credits') }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">إجمالي الساعات</div>
        </div>
    </div>

    {{-- ── Terms ── --}}
    <div class="space-y-4">
        @forelse($program->terms as $term)
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">

            {{-- Term header --}}
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/60 dark:bg-gray-800/40">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold text-brand-600 dark:text-brand-400"
                          style="background:rgba(99,102,241,.1);">{{ $term->term_number }}</span>
                    <div>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $term->name }}</span>
                        <span class="text-xs text-gray-400 mr-2">· {{ $term->subjects->count() }} مقرر · {{ $term->subjects->sum('credits') }} ساعة</span>
                    </div>
                    @if($term->status === 'active')
                        <span style="background:#dcfce7;color:#16a34a;border-radius:9999px;padding:.1rem .55rem;font-size:.68rem;font-weight:600;">نشط</span>
                    @elseif($term->status === 'upcoming')
                        <span style="background:#dbeafe;color:#2563eb;border-radius:9999px;padding:.1rem .55rem;font-size:.68rem;font-weight:600;">قادم</span>
                    @else
                        <span style="background:#f3f4f6;color:#6b7280;border-radius:9999px;padding:.1rem .55rem;font-size:.68rem;font-weight:600;">مكتمل</span>
                    @endif
                </div>
                <div class="flex items-center gap-1">
                    <button @click="openSubjectModal({{ $term->id }}, '{{ addslashes($term->name) }}')"
                            title="إضافة مادة"
                            class="flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium text-purple-700 bg-purple-50 hover:bg-purple-100 dark:text-purple-300 dark:bg-purple-900/20 dark:hover:bg-purple-900/40 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        مادة
                    </button>
                    <a href="{{ route('admin.terms.edit', $term) }}"
                       class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="تعديل الربع">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Subjects table --}}
            @if($term->subjects->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs font-semibold text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-800">
                            <th class="px-4 py-2.5 text-right w-8">#</th>
                            <th class="px-4 py-2.5 text-right">الكود</th>
                            <th class="px-4 py-2.5 text-right">المقرر</th>
                            <th class="px-4 py-2.5 text-right hidden md:table-cell">English</th>
                            <th class="px-4 py-2.5 text-center w-14">س.م</th>
                            <th class="px-4 py-2.5 text-right">المدرب</th>
                            <th class="px-4 py-2.5 text-center w-20">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800/60">
                        @foreach($term->subjects as $idx => $subject)
                        <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-800/30 transition-colors">
                            <td class="px-4 py-2.5 text-xs text-gray-400">{{ $idx + 1 }}</td>
                            <td class="px-4 py-2.5" dir="ltr">
                                <span class="font-mono text-xs font-semibold text-brand-600 dark:text-brand-400 bg-brand-50 dark:bg-brand-900/20 px-2 py-0.5 rounded">{{ $subject->code }}</span>
                            </td>
                            <td class="px-4 py-2.5 font-medium text-gray-900 dark:text-white text-sm">{{ $subject->name_ar ?: $subject->name_en }}</td>
                            <td class="px-4 py-2.5 text-xs text-gray-400 hidden md:table-cell" dir="ltr">{{ $subject->name_en }}</td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded-full px-2 py-0.5">{{ $subject->credits ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-2.5">
                                <button @click="openTeacherModal({{ $subject->id }}, '{{ addslashes($subject->name_ar ?: $subject->name_en) }}', {{ $subject->teacher_id ?? 'null' }})"
                                        class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400 hover:text-brand-600 dark:hover:text-brand-400 transition-colors group">
                                    <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-brand-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    {{ $subject->teacher?->name ?? 'تعيين مدرب' }}
                                </button>
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                @php $isActive = $subject->status === 'active'; @endphp
                                <div x-data="{ open: false }" class="relative inline-block">
                                    <button @click="open = !open" @click.outside="open = false"
                                            class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold border transition-colors"
                                            style="{{ $isActive ? 'background:#dcfce7;color:#16a34a;border-color:#bbf7d0;' : 'background:#f3f4f6;color:#6b7280;border-color:#e5e7eb;' }}">
                                        {{ $isActive ? 'نشط' : 'مقفل' }}
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="open" x-cloak
                                         class="absolute left-0 mt-1 w-28 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">
                                        @if(!$isActive)
                                        <form method="POST" action="{{ route('admin.subjects.toggle-status', $subject) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="active">
                                            <button type="submit" class="w-full text-right px-3 py-2 text-xs font-medium text-green-700 hover:bg-green-50 dark:hover:bg-green-900/20 flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span> تنشيط
                                            </button>
                                        </form>
                                        @endif
                                        @if($isActive)
                                        <form method="POST" action="{{ route('admin.subjects.toggle-status', $subject) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="inactive">
                                            <button type="submit" class="w-full text-right px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                قفل
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/30 text-xs font-semibold text-gray-500 dark:text-gray-400">
                            <td colspan="4" class="px-4 py-2">المجموع — {{ $term->subjects->count() }} مقرر</td>
                            <td class="px-4 py-2 text-center text-brand-600 dark:text-brand-400 font-bold">{{ $term->subjects->sum('credits') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="px-5 py-6 text-center text-xs text-gray-400">
                لا توجد مقررات — <button @click="openSubjectModal({{ $term->id }}, '{{ addslashes($term->name) }}')" class="text-brand-500 hover:underline">إضافة مادة</button>
            </div>
            @endif
        </div>
        @empty
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-12 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">لا توجد أرباع دراسية بعد</p>
            <button @click="openTermModal()" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                إضافة ربع دراسي
            </button>
        </div>
        @endforelse
    </div>

    {{-- ══════════════════════════════════════════
         MODAL: Add Term
    ══════════════════════════════════════════ --}}
    <div x-show="termModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
        <div @click="termModal = false" style="position:absolute;inset:0;background:rgba(0,0,0,0.5);"></div>
        <div @click.stop style="position:relative;background:white;border-radius:14px;width:100%;max-width:440px;box-shadow:0 25px 60px rgba(0,0,0,0.25);" class="dark:bg-gray-800">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">إضافة ربع دراسي</h3>
                <button @click="termModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.terms.store') }}" method="POST">
                @csrf
                <input type="hidden" name="program_id" value="{{ $program->id }}">
                <div class="px-5 py-4 space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">اسم الربع *</label>
                            <input type="text" name="name_ar" required placeholder="الفصل التدريبي الأول"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">رقم الربع *</label>
                            <input type="number" name="term_number" required min="1" value="{{ $program->terms->count() + 1 }}"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">تاريخ البداية</label>
                            <input type="date" name="start_date"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">تاريخ النهاية</label>
                            <input type="date" name="end_date"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">الحالة</label>
                        <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="upcoming">قادم</option>
                            <option value="active">نشط</option>
                            <option value="completed">مكتمل</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 px-5 py-3 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" @click="termModal = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">إلغاء</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">إضافة الربع</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MODAL: Add Subject
    ══════════════════════════════════════════ --}}
    <div x-show="subjectModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
        <div @click="subjectModal = false" style="position:absolute;inset:0;background:rgba(0,0,0,0.5);"></div>
        <div @click.stop style="position:relative;background:white;border-radius:14px;width:100%;max-width:480px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 25px 60px rgba(0,0,0,0.25);" class="dark:bg-gray-800">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex-shrink-0">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">إضافة مادة دراسية</h3>
                    <p class="text-xs text-gray-400 mt-0.5" x-text="currentTermName"></p>
                </div>
                <button @click="subjectModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form action="{{ route('admin.subjects.store') }}" method="POST" style="display:flex;flex-direction:column;overflow:hidden;flex:1;">
                @csrf
                <input type="hidden" name="program_id" value="{{ $program->id }}">
                <input type="hidden" name="term_id" :value="currentTermId">
                <div class="px-5 py-4 space-y-3 overflow-y-auto flex-1">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">كود المادة *</label>
                            <input type="text" name="code" required dir="ltr" placeholder="MATH 101"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white font-mono focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">الساعات</label>
                            <input type="number" name="credits" min="1" max="12" value="3"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">الاسم العربي *</label>
                        <input type="text" name="name_ar" required placeholder="مبادئ الرياضيات"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">الاسم الإنجليزي</label>
                        <input type="text" name="name_en" dir="ltr" placeholder="Mathematics"
                               class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">المدرب</label>
                            <select name="teacher_id" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="">— بدون مدرب —</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">الحالة</label>
                            <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="active">نشط</option>
                                <option value="inactive">غير نشط</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2 px-5 py-3 border-t border-gray-100 dark:border-gray-700 flex-shrink-0">
                    <button type="button" @click="subjectModal = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 transition-colors">إلغاء</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors" style="background:linear-gradient(135deg,#7c3aed,#5b21b6);">إضافة المادة</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MODAL: Assign Teacher
    ══════════════════════════════════════════ --}}
    <div x-show="teacherModal" x-cloak style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:1rem;">
        <div @click="teacherModal = false" style="position:absolute;inset:0;background:rgba(0,0,0,0.5);"></div>
        <div @click.stop style="position:relative;background:white;border-radius:14px;width:100%;max-width:360px;box-shadow:0 25px 60px rgba(0,0,0,0.25);" class="dark:bg-gray-800">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">تعيين مدرب</h3>
                    <p class="text-xs text-gray-400 mt-0.5" x-text="currentSubjectName"></p>
                </div>
                <button @click="teacherModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" :action="'/admin/subjects/' + currentSubjectId + '/assign-teacher'">
                @csrf @method('PATCH')
                <div class="px-5 py-4">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">اختر المدرب</label>
                    <select name="teacher_id" x-model="currentTeacherId"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— بدون مدرب —</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2 px-5 py-3 border-t border-gray-100 dark:border-gray-700">
                    <button type="button" @click="teacherModal = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 transition-colors">إلغاء</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">حفظ</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
