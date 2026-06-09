{{-- Reusable term card: renders one term's header + subjects table.
     Expects: $term (with loaded subjects). Optional: $classId (string|int) to scope the Add-Subject modal. --}}
@php $classId = $classId ?? ''; $showTeachers = $showTeachers ?? true; @endphp
<div style="background:white;border-radius:18px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.04);">

    {{-- Term Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-bottom:1px solid #e2e8f0;">
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#1a3a5c,#2563eb);display:flex;align-items:center;justify-content:center;color:white;font-size:14px;font-weight:800;flex-shrink:0;">
                {{ $term->term_number }}
            </div>
            <div>
                <span style="font-size:14px;font-weight:700;color:#1e293b;">{{ $term->name }}</span>
                <span style="font-size:11px;color:#94a3b8;margin-right:8px;">· {{ $term->subjects->count() }} مقرر · {{ $term->subjects->sum('credits') }} ساعة</span>
            </div>
            @php
                $tsBg    = match($term->status) { 'active'=>'#dcfce7','upcoming'=>'#dbeafe', default=>'#f1f5f9' };
                $tsColor = match($term->status) { 'active'=>'#16a34a','upcoming'=>'#2563eb', default=>'#64748b' };
                $tsLabel = match($term->status) { 'active'=>'نشط','upcoming'=>'قادم', default=>'مكتمل' };
            @endphp
            <div x-data="{ open: false }" class="relative inline-block">
                <button @click="open = !open" @click.outside="open = false"
                        style="display:inline-flex;align-items:center;gap:4px;border-radius:9999px;padding:.2rem .75rem;font-size:.68rem;font-weight:700;border:none;cursor:pointer;background:{{ $tsBg }};color:{{ $tsColor }};">
                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $tsColor }};display:inline-block;"></span>
                    {{ $tsLabel }}
                    <svg style="width:10px;height:10px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" x-cloak
                     style="position:absolute;top:110%;right:0;width:120px;background:white;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.12);border:1px solid #e2e8f0;z-index:50;overflow:hidden;">
                    @foreach(['active'=>['نشط','#dcfce7','#16a34a'],'upcoming'=>['قادم','#dbeafe','#2563eb'],'completed'=>['مكتمل','#f1f5f9','#64748b']] as $val=>[$lbl,$bg,$clr])
                    @if($term->status !== $val)
                    <form method="POST" action="{{ route('admin.terms.toggle-status', $term) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $val }}">
                        <button type="submit"
                                style="width:100%;text-align:right;padding:8px 12px;font-size:12px;font-weight:600;color:{{ $clr }};background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:6px;">
                            <span style="width:8px;height:8px;border-radius:50%;background:{{ $clr }};display:inline-block;flex-shrink:0;"></span>
                            {{ $lbl }}
                        </button>
                    </form>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:6px;">
            <button @click="openSubjectModal({{ $term->id }}, '{{ addslashes($term->name) }}', '{{ $classId }}')"
                    style="display:flex;align-items:center;gap:5px;padding:7px 12px;border-radius:9px;background:linear-gradient(135deg,#7c3aed,#8b5cf6);color:white;font-size:11px;font-weight:700;border:none;cursor:pointer;">
                <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                مقرر
            </button>
            <a href="{{ route('admin.terms.edit', $term) }}"
               style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:9px;border:1.5px solid #e2e8f0;background:white;color:#64748b;text-decoration:none;"
               class="hover:bg-gray-50 transition-colors" title="تعديل الربع">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </a>
        </div>
    </div>

    {{-- Subjects Table --}}
    @if($term->subjects->isNotEmpty())
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:2px solid #f1f5f9;">
                    <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;width:36px;">#</th>
                    <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الكود</th>
                    <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">المقرر</th>
                    <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;" class="hidden md:table-cell">English</th>
                    <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;width:60px;">س.م</th>
                    @if($showTeachers)
                    <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">المدرب/ون</th>
                    @endif
                    <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;width:80px;">الحالة</th>
                    <th style="padding:10px 16px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;width:90px;">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($term->subjects as $idx => $subject)
                @php
                    $allTeachers = $subject->teachers->isNotEmpty()
                        ? $subject->teachers
                        : ($subject->teacher ? collect([$subject->teacher]) : collect());
                @endphp
                <tr style="border-bottom:1px solid #f8fafc;transition:background .15s;" onmouseover="this.style.background='#fafbff'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px;color:#cbd5e1;font-size:11px;">{{ $idx + 1 }}</td>
                    <td style="padding:12px 16px;" dir="ltr">
                        <span style="font-family:monospace;font-size:11px;font-weight:700;color:#2563eb;background:#eff6ff;padding:2px 8px;border-radius:6px;">{{ $subject->code }}</span>
                    </td>
                    <td style="padding:12px 16px;">
                        <span style="font-weight:600;color:#1e293b;">{{ $subject->name_ar ?: $subject->name_en }}</span>
                    </td>
                    <td style="padding:12px 16px;color:#94a3b8;font-size:12px;" dir="ltr" class="hidden md:table-cell">{{ $subject->name_en }}</td>
                    <td style="padding:12px 16px;text-align:center;">
                        <span style="font-size:12px;font-weight:700;color:#475569;background:#f1f5f9;padding:2px 8px;border-radius:9999px;">{{ $subject->credits ?? '—' }}</span>
                    </td>

                    {{-- Teachers cell --}}
                    @if($showTeachers)
                    <td style="padding:12px 16px;">
                        <button @click="openTeacherModal({{ $subject->id }}, '{{ addslashes($subject->name_ar ?: $subject->name_en) }}', {{ json_encode($allTeachers->pluck('id')->all()) }})"
                                style="display:flex;align-items:center;gap:6px;background:none;border:none;cursor:pointer;padding:0;text-align:right;">
                            @if($allTeachers->isNotEmpty())
                                <div style="display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
                                    @foreach($allTeachers->take(2) as $t)
                                    <span style="display:inline-flex;align-items:center;gap:4px;background:#f0f4ff;border:1px solid #c7d2fe;border-radius:9999px;padding:3px 8px;font-size:11px;font-weight:600;color:#3730a3;">
                                        <svg style="width:10px;height:10px;color:#6366f1;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        {{ $t->name }}
                                    </span>
                                    @endforeach
                                    @if($allTeachers->count() > 2)
                                    <span style="background:#e0e7ff;color:#4338ca;border-radius:9999px;padding:3px 7px;font-size:10px;font-weight:700;">+{{ $allTeachers->count() - 2 }}</span>
                                    @endif
                                </div>
                            @else
                                <span style="display:inline-flex;align-items:center;gap:4px;color:#94a3b8;font-size:12px;border:1.5px dashed #e2e8f0;border-radius:9999px;padding:3px 10px;">
                                    <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    تعيين مدرب
                                </span>
                            @endif
                        </button>
                    </td>
                    @endif

                    {{-- Status --}}
                    <td style="padding:12px 16px;text-align:center;">
                        @php $isActive = $subject->status === 'active'; @endphp
                        <div x-data="{ open: false }" class="relative inline-block">
                            <button @click="open = !open" @click.outside="open = false"
                                    style="display:inline-flex;align-items:center;gap:4px;border-radius:9999px;padding:4px 10px;font-size:11px;font-weight:700;border:none;cursor:pointer;{{ $isActive ? 'background:#dcfce7;color:#16a34a;' : 'background:#f1f5f9;color:#64748b;' }}">
                                <span style="width:6px;height:6px;border-radius:50%;background:{{ $isActive ? '#22c55e' : '#cbd5e1' }};display:inline-block;"></span>
                                {{ $isActive ? 'نشط' : 'مقفل' }}
                                <svg style="width:10px;height:10px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak
                                 style="position:absolute;left:0;margin-top:4px;width:110px;background:white;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.12);border:1px solid #e2e8f0;z-index:50;overflow:hidden;">
                                @if(!$isActive)
                                <form method="POST" action="{{ route('admin.subjects.toggle-status', $subject) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" style="width:100%;text-align:right;padding:8px 12px;font-size:12px;font-weight:600;color:#16a34a;background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:6px;">
                                        <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;flex-shrink:0;"></span> تنشيط
                                    </button>
                                </form>
                                @endif
                                @if($isActive)
                                <form method="POST" action="{{ route('admin.subjects.toggle-status', $subject) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="inactive">
                                    <button type="submit" style="width:100%;text-align:right;padding:8px 12px;font-size:12px;font-weight:600;color:#64748b;background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:6px;">
                                        <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        قفل
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td style="padding:12px 16px;text-align:center;">
                        <div style="display:flex;align-items:center;justify-content:center;gap:4px;">
                            {{-- Edit --}}
                            <button @click="openEditSubject({
                                        id: {{ $subject->id }},
                                        code: '{{ addslashes($subject->code) }}',
                                        name_ar: '{{ addslashes($subject->name_ar) }}',
                                        name_en: '{{ addslashes($subject->name_en) }}',
                                        credits: '{{ $subject->credits }}',
                                        status: '{{ $subject->status }}'
                                    })"
                                    style="display:flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:#f0f4ff;color:#2563eb;border:none;cursor:pointer;"
                                    title="تعديل المقرر">
                                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            {{-- Delete --}}
                            <button @click="openDeleteSubject({{ $subject->id }}, '{{ addslashes($subject->name_ar ?: $subject->name_en) }}')"
                                    style="display:flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:8px;background:#fff1f2;color:#ef4444;border:none;cursor:pointer;"
                                    title="حذف المقرر">
                                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top:2px solid #f1f5f9;background:#f8fafc;">
                    <td colspan="4" style="padding:10px 16px;font-size:12px;font-weight:600;color:#64748b;">المجموع — {{ $term->subjects->count() }} مقرر</td>
                    <td style="padding:10px 16px;text-align:center;font-size:13px;font-weight:800;color:#2563eb;">{{ $term->subjects->sum('credits') }}</td>
                    <td colspan="{{ $showTeachers ? 3 : 2 }}"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @else
    <div style="padding:32px;text-align:center;">
        <div style="width:48px;height:48px;border-radius:14px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
            <svg style="width:22px;height:22px;color:#cbd5e1;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <p style="font-size:13px;color:#94a3b8;margin-bottom:12px;">لا توجد مقررات في هذا الربع</p>
        <button @click="openSubjectModal({{ $term->id }}, '{{ addslashes($term->name) }}', '{{ $classId }}')"
                style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:9px;background:linear-gradient(135deg,#7c3aed,#8b5cf6);color:white;font-size:12px;font-weight:700;border:none;cursor:pointer;">
            <svg style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            إضافة مقرر
        </button>
    </div>
    @endif
</div>
