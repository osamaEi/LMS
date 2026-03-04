@extends('layouts.dashboard')

@section('title', 'إدارة المواد الدراسية')

@section('content')
<div style="direction:rtl; font-family:'Segoe UI',sans-serif;">

    {{-- Hero Section --}}
    <div style="background:linear-gradient(135deg,#0f766e 0%,#0d9488 50%,#059669 100%);border-radius:20px;padding:36px 32px;margin-bottom:28px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-40px;left:-40px;width:200px;height:200px;background:rgba(255,255,255,0.05);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-60px;right:20%;width:280px;height:280px;background:rgba(255,255,255,0.04);border-radius:50%;"></div>

        <div style="position:relative;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:20px;">
            <div>
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:48px;height:48px;background:rgba(255,255,255,0.15);border-radius:14px;display:flex;align-items:center;justify-content:center;">
                        <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <h1 style="color:white;font-size:24px;font-weight:700;margin:0;">المواد الدراسية</h1>
                        <p style="color:rgba(255,255,255,0.75);font-size:14px;margin:2px 0 0;">إدارة جميع المواد الدراسية في النظام</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.subjects.create') }}"
               style="display:inline-flex;align-items:center;gap:8px;background:white;color:#0f766e;padding:11px 22px;border-radius:12px;font-weight:600;font-size:14px;text-decoration:none;box-shadow:0 4px 14px rgba(0,0,0,0.15);">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                إضافة مادة جديدة
            </a>
        </div>

        {{-- Stats Row --}}
        <div style="position:relative;z-index:1;display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-top:28px;">
            @php
                $statItems = [
                    ['label'=>'إجمالي المواد','value'=>$stats['total'],'icon'=>'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253','bg'=>'rgba(255,255,255,0.18)'],
                    ['label'=>'مواد نشطة','value'=>$stats['active'],'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','bg'=>'rgba(167,243,208,0.25)'],
                    ['label'=>'مواد غير نشطة','value'=>$stats['inactive'],'icon'=>'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z','bg'=>'rgba(254,202,202,0.25)'],
                    ['label'=>'مواد مكتملة','value'=>$stats['completed'],'icon'=>'M5 13l4 4L19 7','bg'=>'rgba(255,255,255,0.1)'],
                ];
            @endphp
            @foreach($statItems as $stat)
            <div style="background:{{ $stat['bg'] }};backdrop-filter:blur(10px);border-radius:14px;padding:16px;border:1px solid rgba(255,255,255,0.15);">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <div style="color:white;font-size:22px;font-weight:700;line-height:1;">{{ $stat['value'] }}</div>
                        <div style="color:rgba(255,255,255,0.75);font-size:12px;margin-top:2px;">{{ $stat['label'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span style="color:#15803d;font-size:14px;font-weight:500;">{{ session('success') }}</span>
    </div>
    @endif

    {{-- Filter Bar --}}
    @php $hasFilters = $filters['search'] || $filters['status'] || $filters['programId'] || $filters['teacherId'] || $filters['noTeacher']; @endphp
    <div style="background:white;border-radius:14px;padding:18px 20px;margin-bottom:20px;border:1px solid #e5e7eb;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <form method="GET" action="{{ route('admin.subjects.index') }}">
            {{-- Row 1: search + submit --}}
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                <div style="position:relative;flex:1;">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2"
                         style="position:absolute;right:13px;top:50%;transform:translateY(-50%);pointer-events:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ $filters['search'] }}"
                           placeholder="البحث بالاسم أو الكود أو المعلم..."
                           style="width:100%;padding:10px 40px 10px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:#f9fafb;outline:none;box-sizing:border-box;"
                           onfocus="this.style.borderColor='#0d9488'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
                <button type="submit"
                        style="white-space:nowrap;padding:10px 22px;background:#0f766e;color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    بحث وتصفية
                </button>
                @if($hasFilters)
                <a href="{{ route('admin.subjects.index') }}"
                   style="white-space:nowrap;padding:10px 16px;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    مسح الفلاتر
                </a>
                @endif
                <div style="color:#6b7280;font-size:13px;white-space:nowrap;margin-right:auto;">
                    <span style="font-weight:700;color:#111827;">{{ $subjects->total() }}</span> مادة
                </div>
            </div>

            {{-- Row 2: dropdowns --}}
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:10px;align-items:center;">

                {{-- Status --}}
                <div style="position:relative;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2"
                         style="position:absolute;right:11px;top:50%;transform:translateY(-50%);pointer-events:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <select name="status"
                            style="width:100%;padding:9px 34px 9px 12px;border:1.5px solid {{ $filters['status'] ? '#0d9488' : '#e5e7eb' }};border-radius:10px;font-size:13px;color:#374151;background:#f9fafb;outline:none;appearance:none;cursor:pointer;">
                        <option value="">كل الحالات</option>
                        <option value="active"    {{ $filters['status'] === 'active'    ? 'selected' : '' }}>نشط</option>
                        <option value="inactive"  {{ $filters['status'] === 'inactive'  ? 'selected' : '' }}>غير نشط</option>
                        <option value="completed" {{ $filters['status'] === 'completed' ? 'selected' : '' }}>مكتمل</option>
                    </select>
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2.5"
                         style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                {{-- Program --}}
                <div style="position:relative;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2"
                         style="position:absolute;right:11px;top:50%;transform:translateY(-50%);pointer-events:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <select name="program_id"
                            style="width:100%;padding:9px 34px 9px 12px;border:1.5px solid {{ $filters['programId'] ? '#0d9488' : '#e5e7eb' }};border-radius:10px;font-size:13px;color:#374151;background:#f9fafb;outline:none;appearance:none;cursor:pointer;">
                        <option value="">كل الدبلومات</option>
                        @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ $filters['programId'] == $program->id ? 'selected' : '' }}>
                            {{ $program->name_ar ?: $program->name_en }}
                        </option>
                        @endforeach
                    </select>
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2.5"
                         style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                {{-- Teacher --}}
                <div style="position:relative;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2"
                         style="position:absolute;right:11px;top:50%;transform:translateY(-50%);pointer-events:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <select name="teacher_id"
                            style="width:100%;padding:9px 34px 9px 12px;border:1.5px solid {{ $filters['teacherId'] ? '#0d9488' : '#e5e7eb' }};border-radius:10px;font-size:13px;color:#374151;background:#f9fafb;outline:none;appearance:none;cursor:pointer;">
                        <option value="">كل المعلمين</option>
                        @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ $filters['teacherId'] == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                        @endforeach
                    </select>
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2.5"
                         style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                {{-- Unassigned toggle --}}
                <label style="display:inline-flex;align-items:center;gap:7px;cursor:pointer;white-space:nowrap;padding:9px 14px;border-radius:10px;border:1.5px solid {{ $filters['noTeacher'] ? '#dc2626' : '#e5e7eb' }};background:{{ $filters['noTeacher'] ? '#fef2f2' : '#f9fafb' }};">
                    <input type="checkbox" name="no_teacher" value="1" {{ $filters['noTeacher'] ? 'checked' : '' }}
                           style="width:14px;height:14px;accent-color:#dc2626;cursor:pointer;">
                    <span style="font-size:13px;font-weight:500;color:{{ $filters['noTeacher'] ? '#dc2626' : '#6b7280' }};">بدون معلم</span>
                </label>

            </div>

            {{-- Active filter chips --}}
            @if($hasFilters)
            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:12px;padding-top:12px;border-top:1px dashed #e5e7eb;">
                <span style="font-size:12px;color:#9ca3af;align-self:center;">فلاتر مفعّلة:</span>
                @if($filters['search'])
                <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;background:#ede9fe;color:#6d28d9;border-radius:20px;font-size:12px;font-weight:600;">
                    بحث: {{ $filters['search'] }}
                </span>
                @endif
                @if($filters['status'])
                <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;background:#d1fae5;color:#065f46;border-radius:20px;font-size:12px;font-weight:600;">
                    الحالة: {{ ['active'=>'نشط','inactive'=>'غير نشط','completed'=>'مكتمل'][$filters['status']] ?? $filters['status'] }}
                </span>
                @endif
                @if($filters['programId'])
                @php $selProg = $programs->firstWhere('id', $filters['programId']); @endphp
                <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;background:#dbeafe;color:#1e40af;border-radius:20px;font-size:12px;font-weight:600;">
                    الدبلومة: {{ $selProg?->name_ar ?: $selProg?->name_en }}
                </span>
                @endif
                @if($filters['teacherId'])
                @php $selTeacher = $teachers->firstWhere('id', $filters['teacherId']); @endphp
                <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;background:#fef3c7;color:#92400e;border-radius:20px;font-size:12px;font-weight:600;">
                    المعلم: {{ $selTeacher?->name }}
                </span>
                @endif
                @if($filters['noTeacher'])
                <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;background:#fee2e2;color:#991b1b;border-radius:20px;font-size:12px;font-weight:600;">
                    بدون معلم
                </span>
                @endif
            </div>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,0.06);">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:linear-gradient(90deg,#f0fdfa,#f7fffe);border-bottom:1.5px solid #e5e7eb;">
                        <th style="padding:14px 20px;text-align:right;font-size:12px;font-weight:700;color:#6b7280;letter-spacing:.5px;">الكود</th>
                        <th style="padding:14px 20px;text-align:right;font-size:12px;font-weight:700;color:#6b7280;letter-spacing:.5px;">اسم المادة</th>
                        <th style="padding:14px 20px;text-align:right;font-size:12px;font-weight:700;color:#6b7280;letter-spacing:.5px;">المعلم</th>
                        <th style="padding:14px 20px;text-align:right;font-size:12px;font-weight:700;color:#6b7280;letter-spacing:.5px;">الفصل / الدبلومة</th>
                        <th style="padding:14px 20px;text-align:right;font-size:12px;font-weight:700;color:#6b7280;letter-spacing:.5px;">الدروس</th>
                        <th style="padding:14px 20px;text-align:right;font-size:12px;font-weight:700;color:#6b7280;letter-spacing:.5px;">الحالة</th>
                        <th style="padding:14px 20px;text-align:right;font-size:12px;font-weight:700;color:#6b7280;letter-spacing:.5px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                    @php
                        $statusStyle = match($subject->status) {
                            'active'    => ['bg'=>'#dcfce7','text'=>'#15803d','dot'=>'#22c55e','label'=>'نشط'],
                            'completed' => ['bg'=>'#f3f4f6','text'=>'#6b7280','dot'=>'#9ca3af','label'=>'مكتمل'],
                            default     => ['bg'=>'#fef9c3','text'=>'#854d0e','dot'=>'#eab308','label'=>'غير نشط'],
                        };
                        $codeColors = ['#6366f1','#0891b2','#059669','#d97706','#dc2626','#7c3aed','#0284c7'];
                        $codeColor = $codeColors[crc32($subject->code ?? '') % count($codeColors)];
                        $initials = collect(explode(' ', $subject->teacher->name ?? 'غير'))->take(2)->map(fn($w) => mb_substr($w, 0, 1))->join('');
                        $avatarColors = [['#6366f1','#a5b4fc'],['#059669','#6ee7b7'],['#d97706','#fcd34d'],['#dc2626','#fca5a5'],['#7c3aed','#c4b5fd'],['#0284c7','#7dd3fc']];
                        $avatarColor = $avatarColors[$subject->teacher_id % count($avatarColors) ?? 0] ?? ['#6366f1','#a5b4fc'];
                    @endphp
                    <tr style="border-bottom:1px solid #f3f4f6;transition:background .15s;" onmouseover="this.style.background='#f0fdf9'" onmouseout="this.style.background='white'">
                        {{-- Code --}}
                        <td style="padding:16px 20px;">
                            <span style="display:inline-block;background:{{ $codeColor }}15;color:{{ $codeColor }};border:1px solid {{ $codeColor }}30;padding:4px 10px;border-radius:8px;font-size:12px;font-weight:700;font-family:monospace;">
                                {{ $subject->code }}
                            </span>
                        </td>
                        {{-- Name --}}
                        <td style="padding:16px 20px;">
                            <div style="font-weight:600;color:#111827;font-size:14px;">{{ $subject->name }}</div>
                            @if($subject->credits)
                            <div style="color:#9ca3af;font-size:12px;margin-top:2px;">{{ $subject->credits }} ساعة معتمدة</div>
                            @endif
                        </td>
                        {{-- Teacher --}}
                        <td style="padding:16px 20px;">
                            @if($subject->teacher)
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,{{ $avatarColor[0] }},{{ $avatarColor[1] }});display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <span style="color:white;font-size:12px;font-weight:700;">{{ $initials }}</span>
                                </div>
                                <span style="font-size:13px;color:#374151;font-weight:500;">{{ $subject->teacher->name }}</span>
                            </div>
                            @else
                            <span style="color:#d1d5db;font-size:13px;">غير محدد</span>
                            @endif
                        </td>
                        {{-- Term / Program --}}
                        <td style="padding:16px 20px;">
                            <div style="font-size:13px;font-weight:500;color:#374151;">{{ $subject->term->name ?? '—' }}</div>
                            <div style="font-size:12px;color:#9ca3af;margin-top:2px;">{{ $subject->term->program->name ?? '' }}</div>
                        </td>
                        {{-- Sessions --}}
                        <td style="padding:16px 20px;">
                            <div style="display:inline-flex;align-items:center;gap:6px;background:#f0fdfa;border-radius:20px;padding:4px 12px;">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#0d9488" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <span style="color:#0f766e;font-weight:600;font-size:13px;">{{ $subject->sessions_count }}</span>
                                <span style="color:#5eead4;font-size:12px;">درس</span>
                            </div>
                        </td>
                        {{-- Status --}}
                        <td style="padding:16px 20px;">
                            <div style="display:inline-flex;align-items:center;gap:6px;background:{{ $statusStyle['bg'] }};border-radius:20px;padding:5px 12px;">
                                <div style="width:7px;height:7px;border-radius:50%;background:{{ $statusStyle['dot'] }};{{ $subject->status === 'active' ? 'box-shadow:0 0 6px '.$statusStyle['dot'].';' : '' }}"></div>
                                <span style="color:{{ $statusStyle['text'] }};font-size:13px;font-weight:600;">{{ $statusStyle['label'] }}</span>
                            </div>
                        </td>
                        {{-- Actions --}}
                        <td style="padding:16px 20px;">
                            <div style="display:flex;align-items:center;gap:6px;">
                                <a href="{{ route('admin.subjects.show', $subject) }}"
                                   style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;background:#ede9fe;color:#7c3aed;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;"
                                   onmouseover="this.style.background='#ddd6fe'" onmouseout="this.style.background='#ede9fe'">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    عرض
                                </a>
                                <a href="{{ route('admin.subjects.edit', $subject) }}"
                                   style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;background:#fef3c7;color:#d97706;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;"
                                   onmouseover="this.style.background='#fde68a'" onmouseout="this.style.background='#fef3c7'">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    تعديل
                                </a>
                                <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه المادة؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;background:#fee2e2;color:#dc2626;border:none;border-radius:8px;cursor:pointer;"
                                            onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding:60px 20px;text-align:center;">
                            <div style="display:flex;flex-direction:column;align-items:center;gap:12px;">
                                <div style="width:72px;height:72px;background:#f0fdfa;border-radius:20px;display:flex;align-items:center;justify-content:center;">
                                    <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="#5eead4" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div style="font-weight:600;color:#374151;font-size:16px;">
                                    @if($hasFilters) لا توجد نتائج للفلاتر المحددة @else لا توجد مواد دراسية @endif
                                </div>
                                <div style="color:#9ca3af;font-size:14px;">
                                    @if($hasFilters) جرب تغيير خيارات الفلترة @else ابدأ بإضافة أول مادة دراسية @endif
                                </div>
                                @if($hasFilters)
                                <a href="{{ route('admin.subjects.index') }}" style="margin-top:4px;padding:8px 18px;background:#0f766e;color:white;border-radius:10px;font-size:13px;text-decoration:none;">عرض الكل</a>
                                @else
                                <a href="{{ route('admin.subjects.create') }}" style="margin-top:4px;padding:8px 18px;background:#0f766e;color:white;border-radius:10px;font-size:13px;text-decoration:none;">إضافة مادة دراسية</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subjects->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f3f4f6;">
            {{ $subjects->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
