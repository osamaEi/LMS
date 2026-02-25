@extends('layouts.dashboard')

@section('title', 'عرض المادة الدراسية')

@section('content')
<div style="direction:rtl; font-family:'Segoe UI',sans-serif;">

    {{-- Back Button --}}
    <div style="margin-bottom:20px;display:flex;align-items:center;gap:10px;">
        <a href="{{ route('admin.subjects.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:white;border:1.5px solid #e5e7eb;border-radius:10px;color:#6b7280;font-size:13px;font-weight:500;text-decoration:none;transition:all .15s;"
           onmouseover="this.style.borderColor='#0d9488';this.style.color='#0d9488'" onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#6b7280'">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة للمواد
        </a>
        <span style="color:#d1d5db;">›</span>
        <span style="color:#9ca3af;font-size:13px;">{{ $subject->name }}</span>
    </div>

    {{-- Subject Hero Header --}}
    <div style="border-radius:20px;overflow:hidden;margin-bottom:24px;position:relative;box-shadow:0 4px 20px rgba(0,0,0,0.12);">
        @if($subject->banner_photo)
        <div style="height:200px;position:relative;">
            <img src="{{ Storage::url($subject->banner_photo) }}" alt="{{ $subject->name }}" style="width:100%;height:100%;object-fit:cover;">
            <div style="position:absolute;inset:0;background:linear-gradient(to bottom,rgba(0,0,0,0.2),rgba(0,0,0,0.65));"></div>
        </div>
        @else
        <div style="height:160px;background:linear-gradient(135deg,#0f766e 0%,#0d9488 50%,#059669 100%);position:relative;">
            <div style="position:absolute;top:-30px;left:-30px;width:160px;height:160px;background:rgba(255,255,255,0.05);border-radius:50%;"></div>
            <div style="position:absolute;bottom:-40px;right:15%;width:200px;height:200px;background:rgba(255,255,255,0.04);border-radius:50%;"></div>
        </div>
        @endif
        <div style="position:absolute;bottom:0;left:0;right:0;padding:24px 28px;display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div style="display:flex;align-items:flex-end;gap:14px;">
                {{-- Subject Icon --}}
                <div style="width:56px;height:56px;background:white;border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.2);flex-shrink:0;">
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#0d9488" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <h1 style="color:white;font-size:22px;font-weight:700;margin:0;text-shadow:0 1px 4px rgba(0,0,0,0.3);">{{ $subject->name }}</h1>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:6px;flex-wrap:wrap;">
                        <span style="background:rgba(255,255,255,0.2);backdrop-filter:blur(8px);color:white;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;border:1px solid rgba(255,255,255,0.25);">
                            {{ $subject->code }}
                        </span>
                        @php
                            $statusBadge = match($subject->status) {
                                'active'    => ['bg'=>'rgba(167,243,208,0.3)','text'=>'white','label'=>'نشط','dot'=>'#86efac'],
                                'completed' => ['bg'=>'rgba(209,213,219,0.3)','text'=>'white','label'=>'مكتمل','dot'=>'#d1d5db'],
                                default     => ['bg'=>'rgba(253,230,138,0.3)','text'=>'white','label'=>'غير نشط','dot'=>'#fcd34d'],
                            };
                        @endphp
                        <span style="display:inline-flex;align-items:center;gap:5px;background:{{ $statusBadge['bg'] }};backdrop-filter:blur(8px);color:{{ $statusBadge['text'] }};padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;border:1px solid rgba(255,255,255,0.2);">
                            <span style="width:6px;height:6px;border-radius:50%;background:{{ $statusBadge['dot'] }};display:inline-block;"></span>
                            {{ $statusBadge['label'] }}
                        </span>
                        @if($subject->credits)
                        <span style="background:rgba(255,255,255,0.15);backdrop-filter:blur(8px);color:white;padding:3px 10px;border-radius:20px;font-size:12px;border:1px solid rgba(255,255,255,0.2);">
                            {{ $subject->credits }} ساعة معتمدة
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            {{-- Action Buttons --}}
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('admin.subjects.edit', $subject) }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:white;color:#0f766e;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;box-shadow:0 2px 8px rgba(0,0,0,0.15);">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    تعديل المادة
                </a>
                <a href="{{ route('admin.sessions.index', ['subject_id' => $subject->id]) }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:rgba(255,255,255,0.15);backdrop-filter:blur(8px);color:white;border:1px solid rgba(255,255,255,0.3);border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    إدارة الدروس
                </a>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">

        {{-- Main Info --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- Subject Details Card --}}
            <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,0.06);">
                <div style="padding:18px 24px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;background:#f0fdfa;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#0d9488" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 style="font-size:16px;font-weight:700;color:#111827;margin:0;">معلومات المادة</h2>
                </div>
                <div style="padding:24px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                        {{-- Code --}}
                        <div style="background:#f9fafb;border-radius:12px;padding:16px;">
                            <div style="color:#9ca3af;font-size:12px;font-weight:500;margin-bottom:6px;">كود المادة</div>
                            <span style="background:#dcfce7;color:#15803d;padding:4px 12px;border-radius:8px;font-size:14px;font-weight:700;font-family:monospace;">{{ $subject->code }}</span>
                        </div>
                        {{-- Status --}}
                        <div style="background:#f9fafb;border-radius:12px;padding:16px;">
                            <div style="color:#9ca3af;font-size:12px;font-weight:500;margin-bottom:6px;">حالة المادة</div>
                            @php
                                $s = match($subject->status) {
                                    'active'    => ['bg'=>'#dcfce7','text'=>'#15803d','label'=>'نشط'],
                                    'completed' => ['bg'=>'#f3f4f6','text'=>'#6b7280','label'=>'مكتمل'],
                                    default     => ['bg'=>'#fef9c3','text'=>'#854d0e','label'=>'غير نشط'],
                                };
                            @endphp
                            <span style="background:{{ $s['bg'] }};color:{{ $s['text'] }};padding:4px 12px;border-radius:8px;font-size:13px;font-weight:600;">{{ $s['label'] }}</span>
                        </div>
                        {{-- Term --}}
                        <div style="background:#f9fafb;border-radius:12px;padding:16px;">
                            <div style="color:#9ca3af;font-size:12px;font-weight:500;margin-bottom:6px;">الفصل الدراسي</div>
                            <div style="color:#111827;font-size:14px;font-weight:600;">{{ $subject->term->name ?? '—' }}</div>
                        </div>
                        {{-- Program --}}
                        <div style="background:#f9fafb;border-radius:12px;padding:16px;">
                            <div style="color:#9ca3af;font-size:12px;font-weight:500;margin-bottom:6px;">المسار التعليمي</div>
                            <div style="color:#111827;font-size:14px;font-weight:600;">{{ $subject->term->program->name ?? '—' }}</div>
                        </div>
                        @if($subject->credits)
                        <div style="background:#f9fafb;border-radius:12px;padding:16px;">
                            <div style="color:#9ca3af;font-size:12px;font-weight:500;margin-bottom:6px;">الساعات المعتمدة</div>
                            <div style="color:#111827;font-size:18px;font-weight:700;">{{ $subject->credits }} <span style="font-size:13px;color:#6b7280;font-weight:400;">ساعة</span></div>
                        </div>
                        @endif
                        {{-- Sessions Count --}}
                        <div style="background:#f9fafb;border-radius:12px;padding:16px;">
                            <div style="color:#9ca3af;font-size:12px;font-weight:500;margin-bottom:6px;">عدد الدروس</div>
                            <div style="color:#111827;font-size:18px;font-weight:700;">{{ $subject->sessions->count() }} <span style="font-size:13px;color:#6b7280;font-weight:400;">درس</span></div>
                        </div>
                    </div>

                    @if($subject->description)
                    <div style="margin-top:20px;padding-top:20px;border-top:1px solid #f3f4f6;">
                        <div style="color:#9ca3af;font-size:12px;font-weight:500;margin-bottom:8px;">وصف المادة</div>
                        <p style="color:#374151;font-size:14px;line-height:1.7;margin:0;background:#f9fafb;border-radius:10px;padding:14px;">{{ $subject->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Quick Actions Card --}}
            <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,0.06);">
                <div style="padding:18px 24px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;background:#fef3c7;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#d97706" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h2 style="font-size:16px;font-weight:700;color:#111827;margin:0;">إجراءات سريعة</h2>
                </div>
                <div style="padding:20px 24px;display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                    <a href="{{ route('admin.subjects.edit', $subject) }}"
                       style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 12px;background:#f0fdfa;border-radius:12px;text-decoration:none;border:1.5px solid #99f6e4;transition:all .15s;"
                       onmouseover="this.style.background='#ccfbf1'" onmouseout="this.style.background='#f0fdfa'">
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#0d9488" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span style="font-size:12px;font-weight:600;color:#0f766e;text-align:center;">تعديل المادة</span>
                    </a>
                    <a href="{{ route('admin.sessions.index', ['subject_id' => $subject->id]) }}"
                       style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 12px;background:#ede9fe;border-radius:12px;text-decoration:none;border:1.5px solid #c4b5fd;transition:all .15s;"
                       onmouseover="this.style.background='#ddd6fe'" onmouseout="this.style.background='#ede9fe'">
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span style="font-size:12px;font-weight:600;color:#6d28d9;text-align:center;">إدارة الدروس</span>
                    </a>
                    <a href="{{ route('admin.subjects.index') }}"
                       style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:16px 12px;background:#f3f4f6;border-radius:12px;text-decoration:none;border:1.5px solid #e5e7eb;transition:all .15s;"
                       onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                        <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        <span style="font-size:12px;font-weight:600;color:#6b7280;text-align:center;">جميع المواد</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div style="display:flex;flex-direction:column;gap:20px;">

            {{-- Stats Card --}}
            <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,0.06);">
                <div style="padding:18px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;background:#f0fdfa;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#0d9488" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h2 style="font-size:15px;font-weight:700;color:#111827;margin:0;">إحصائيات</h2>
                </div>
                <div style="padding:20px;">
                    @php
                        $sideStats = [
                            ['label'=>'عدد الدروس','value'=>$subject->sessions->count(),'icon'=>'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z','color'=>'#0d9488','bg'=>'#f0fdfa'],
                        ];
                        if ($subject->credits) {
                            $sideStats[] = ['label'=>'الساعات المعتمدة','value'=>$subject->credits,'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','color'=>'#7c3aed','bg'=>'#f5f3ff'];
                        }
                    @endphp
                    <div style="display:flex;flex-direction:column;gap:12px;">
                        @foreach($sideStats as $stat)
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;background:{{ $stat['bg'] }};border-radius:12px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:32px;height:32px;background:white;border-radius:8px;display:flex;align-items:center;justify-content:center;box-shadow:0 1px 4px rgba(0,0,0,0.08);">
                                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="{{ $stat['color'] }}" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}"/>
                                    </svg>
                                </div>
                                <span style="font-size:13px;color:#374151;font-weight:500;">{{ $stat['label'] }}</span>
                            </div>
                            <span style="font-size:20px;font-weight:700;color:{{ $stat['color'] }};">{{ $stat['value'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Teacher Card --}}
            @if($subject->teacher)
            <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,0.06);">
                <div style="padding:18px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;background:#ede9fe;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h2 style="font-size:15px;font-weight:700;color:#111827;margin:0;">المعلم المسؤول</h2>
                </div>
                <div style="padding:20px;">
                    @php
                        $tInitials = collect(explode(' ', $subject->teacher->name))->take(2)->map(fn($w) => mb_substr($w, 0, 1))->join('');
                    @endphp
                    <div style="display:flex;align-items:center;gap:14px;padding:14px;background:#f8f7ff;border-radius:12px;border:1px solid #ede9fe;">
                        <div style="width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,#7c3aed,#a78bfa);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(124,58,237,0.3);">
                            <span style="color:white;font-weight:700;font-size:18px;">{{ $tInitials }}</span>
                        </div>
                        <div>
                            <div style="font-size:15px;font-weight:700;color:#111827;">{{ $subject->teacher->name }}</div>
                            <div style="font-size:12px;color:#9ca3af;margin-top:2px;">{{ $subject->teacher->email }}</div>
                            @if($subject->teacher->phone)
                            <div style="font-size:12px;color:#9ca3af;margin-top:1px;">{{ $subject->teacher->phone }}</div>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('admin.teachers.show', $subject->teacher) }}"
                       style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:12px;padding:9px;background:#ede9fe;border-radius:10px;color:#7c3aed;font-size:13px;font-weight:600;text-decoration:none;transition:all .15s;"
                       onmouseover="this.style.background='#ddd6fe'" onmouseout="this.style.background='#ede9fe'">
                        عرض ملف المعلم
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
            @endif

            {{-- Term / Program Card --}}
            @if($subject->term)
            <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,0.06);">
                <div style="padding:18px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;background:#f0f0ff;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#4f46e5" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 style="font-size:15px;font-weight:700;color:#111827;margin:0;">الفصل والمسار</h2>
                </div>
                <div style="padding:16px 20px;display:flex;flex-direction:column;gap:10px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px;background:#f9fafb;border-radius:10px;">
                        <span style="font-size:12px;color:#9ca3af;font-weight:500;">الفصل الدراسي</span>
                        <span style="font-size:13px;font-weight:600;color:#374151;">{{ $subject->term->name }}</span>
                    </div>
                    @if($subject->term->program)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px;background:#f9fafb;border-radius:10px;">
                        <span style="font-size:12px;color:#9ca3af;font-weight:500;">المسار التعليمي</span>
                        <span style="font-size:13px;font-weight:600;color:#374151;">{{ $subject->term->program->name }}</span>
                    </div>
                    @endif
                    <a href="{{ route('admin.terms.show', $subject->term) }}"
                       style="display:flex;align-items:center;justify-content:center;gap:6px;padding:9px;background:#f0f0ff;border-radius:10px;color:#4f46e5;font-size:13px;font-weight:600;text-decoration:none;transition:all .15s;"
                       onmouseover="this.style.background='#e0e0ff'" onmouseout="this.style.background='#f0f0ff'">
                        عرض الفصل الدراسي
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection
