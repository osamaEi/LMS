{{-- TAB: Students --}}
<div id="ctab-students">
    <div style="background:white;border-radius:18px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9;background:#fafafa;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:15px;font-weight:700;color:#111827;">طلاب المجموعة</span>
                <span style="background:#dbeafe;color:#2563eb;border-radius:9999px;padding:.12rem .55rem;font-size:.68rem;font-weight:700;">{{ $studentsCount }}</span>
            </div>
            <button onclick="openStudentsModal({{ $class->id }}, '{{ addslashes($class->name) }}')"
                    style="display:flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;background:linear-gradient(135deg,#0071AA,#004d77);color:white;font-size:12px;font-weight:700;border:none;cursor:pointer;">
                <svg style="width:14px;height:14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                إدارة الطلاب
            </button>
        </div>
        @if($class->students->isNotEmpty())
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                    <tr style="border-bottom:2px solid #f1f5f9;background:#fafafa;">
                        <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;width:40px;">#</th>
                        <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الاسم</th>
                        <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">البريد</th>
                        <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الهوية</th>
                        <th style="padding:11px 16px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;">الجوال</th>
                        <th style="padding:11px 16px;text-align:center;font-size:11px;font-weight:700;color:#94a3b8;width:90px;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($class->students as $i => $student)
                    <tr style="border-bottom:1px solid #f8fafc;">
                        <td style="padding:12px 16px;color:#cbd5e1;font-size:11px;">{{ $i + 1 }}</td>
                        <td style="padding:12px 16px;font-weight:600;color:#1e293b;">{{ $student->name }}</td>
                        <td style="padding:12px 16px;color:#475569;" dir="ltr">{{ $student->email }}</td>
                        <td style="padding:12px 16px;color:#64748b;" dir="ltr">{{ $student->national_id ?? '—' }}</td>
                        <td style="padding:12px 16px;color:#64748b;" dir="ltr">{{ $student->phone ?? '—' }}</td>
                        <td style="padding:12px 16px;text-align:center;">
                            <a href="{{ route('admin.students.show', $student->id) }}"
                               style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;font-size:11px;color:#0071AA;background:#e0f2fe;border:1px solid #bae6fd;border-radius:7px;font-weight:600;text-decoration:none;">
                                <svg style="width:12px;height:12px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                عرض
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding:48px;text-align:center;">
            <p style="font-size:13px;color:#94a3b8;margin-bottom:12px;">لا يوجد طلاب في هذه المجموعة بعد</p>
            <button onclick="openStudentsModal({{ $class->id }}, '{{ addslashes($class->name) }}')"
                    style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:9px;background:linear-gradient(135deg,#0071AA,#004d77);color:white;font-size:12px;font-weight:700;border:none;cursor:pointer;">
                إضافة طلاب
            </button>
        </div>
        @endif
    </div>
</div>
