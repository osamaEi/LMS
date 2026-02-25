@extends('layouts.dashboard')

@section('title', 'إدارة الفصول الدراسية')

@section('content')
<div style="direction:rtl; font-family:'Segoe UI',sans-serif;">

    {{-- Hero Section --}}
    <div style="background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 50%,#6d28d9 100%);border-radius:20px;padding:36px 32px;margin-bottom:28px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-40px;left:-40px;width:200px;height:200px;background:rgba(255,255,255,0.05);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-60px;right:20%;width:280px;height:280px;background:rgba(255,255,255,0.04);border-radius:50%;"></div>

        <div style="position:relative;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:20px;">
            <div>
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                    <div style="width:48px;height:48px;background:rgba(255,255,255,0.15);border-radius:14px;display:flex;align-items:center;justify-content:center;">
                        <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 style="color:white;font-size:24px;font-weight:700;margin:0;">الفصول الدراسية</h1>
                        <p style="color:rgba(255,255,255,0.75);font-size:14px;margin:2px 0 0;">إدارة جميع الفصول الدراسية في النظام</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.terms.create') }}"
               style="display:inline-flex;align-items:center;gap:8px;background:white;color:#4f46e5;padding:11px 22px;border-radius:12px;font-weight:600;font-size:14px;text-decoration:none;transition:all .2s;box-shadow:0 4px 14px rgba(0,0,0,0.15);">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                إضافة فصل دراسي
            </a>
        </div>

        {{-- Stats Row --}}
        <div style="position:relative;z-index:1;display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-top:28px;">
            @php
                $statItems = [
                    ['label'=>'إجمالي الفصول','value'=>$stats['total'],'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z','bg'=>'rgba(255,255,255,0.18)'],
                    ['label'=>'فصول نشطة','value'=>$stats['active'],'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','bg'=>'rgba(167,243,208,0.25)'],
                    ['label'=>'فصول قادمة','value'=>$stats['upcoming'],'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','bg'=>'rgba(147,197,253,0.25)'],
                    ['label'=>'فصول مكتملة','value'=>$stats['completed'],'icon'=>'M5 13l4 4L19 7','bg'=>'rgba(255,255,255,0.1)'],
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

    {{-- Search Bar --}}
    <div style="background:white;border-radius:14px;padding:16px 20px;margin-bottom:20px;border:1px solid #e5e7eb;display:flex;align-items:center;gap:12px;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
        <form method="GET" action="{{ route('admin.terms.index') }}" style="flex:1;display:flex;align-items:center;gap:10px;">
            <div style="position:relative;flex:1;">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="البحث باسم الفصل أو المسار..."
                       style="width:100%;padding:10px 44px 10px 14px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;background:#f9fafb;outline:none;box-sizing:border-box;"
                       onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#e5e7eb'">
            </div>
            <button type="submit" style="padding:10px 20px;background:#4f46e5;color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;">بحث</button>
            @if($search)
            <a href="{{ route('admin.terms.index') }}" style="padding:10px 16px;background:#f3f4f6;color:#6b7280;border-radius:10px;font-size:14px;text-decoration:none;">مسح</a>
            @endif
        </form>
        <div style="color:#6b7280;font-size:13px;white-space:nowrap;">
            {{ $terms->total() }} فصل
        </div>
    </div>

    {{-- Table --}}
    <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,0.06);">
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:linear-gradient(90deg,#f8f7ff,#faf9ff);border-bottom:1.5px solid #e5e7eb;">
                        <th style="padding:14px 20px;text-align:right;font-size:12px;font-weight:700;color:#6b7280;letter-spacing:.5px;text-transform:uppercase;">الفصل</th>
                        <th style="padding:14px 20px;text-align:right;font-size:12px;font-weight:700;color:#6b7280;letter-spacing:.5px;">المسار التعليمي</th>
                        <th style="padding:14px 20px;text-align:right;font-size:12px;font-weight:700;color:#6b7280;letter-spacing:.5px;">التواريخ</th>
                        <th style="padding:14px 20px;text-align:right;font-size:12px;font-weight:700;color:#6b7280;letter-spacing:.5px;">المواد</th>
                        <th style="padding:14px 20px;text-align:right;font-size:12px;font-weight:700;color:#6b7280;letter-spacing:.5px;">الحالة</th>
                        <th style="padding:14px 20px;text-align:right;font-size:12px;font-weight:700;color:#6b7280;letter-spacing:.5px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($terms as $term)
                    @php
                        $statusColor = match($term->status) {
                            'active'    => ['bg'=>'#dcfce7','text'=>'#15803d','dot'=>'#22c55e','label'=>'نشط'],
                            'upcoming'  => ['bg'=>'#dbeafe','text'=>'#1d4ed8','dot'=>'#3b82f6','label'=>'قادم'],
                            'completed' => ['bg'=>'#f3f4f6','text'=>'#6b7280','dot'=>'#9ca3af','label'=>'مكتمل'],
                            default     => ['bg'=>'#f3f4f6','text'=>'#6b7280','dot'=>'#9ca3af','label'=>'غير محدد'],
                        };
                        $termColors = ['#818cf8','#a78bfa','#c084fc','#e879f9','#38bdf8','#34d399','#fb923c'];
                        $termColor = $termColors[($term->term_number - 1) % count($termColors)] ?? '#818cf8';
                    @endphp
                    <tr style="border-bottom:1px solid #f3f4f6;transition:background .15s;" onmouseover="this.style.background='#fafafe'" onmouseout="this.style.background='white'">
                        {{-- Term Name --}}
                        <td style="padding:16px 20px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="width:42px;height:42px;border-radius:12px;background:{{ $termColor }}20;border:2px solid {{ $termColor }}40;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <span style="color:{{ $termColor }};font-weight:700;font-size:15px;">{{ $term->term_number }}</span>
                                </div>
                                <div>
                                    <div style="font-weight:600;color:#111827;font-size:14px;">{{ $term->name }}</div>
                                    @if($term->name_en)
                                    <div style="color:#9ca3af;font-size:12px;margin-top:1px;">{{ $term->name_en }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        {{-- Program --}}
                        <td style="padding:16px 20px;">
                            @if($term->program)
                            <div style="display:inline-flex;align-items:center;gap:6px;background:#f0f0ff;border-radius:8px;padding:5px 10px;">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#6366f1" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span style="color:#4f46e5;font-size:13px;font-weight:500;">{{ $term->program->name }}</span>
                            </div>
                            @else
                            <span style="color:#d1d5db;font-size:13px;">—</span>
                            @endif
                        </td>
                        {{-- Dates --}}
                        <td style="padding:16px 20px;">
                            <div style="font-size:13px;color:#374151;display:flex;align-items:center;gap:5px;">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $term->start_date->format('Y/m/d') }}
                            </div>
                            <div style="font-size:12px;color:#9ca3af;margin-top:3px;padding-right:18px;">
                                حتى {{ $term->end_date->format('Y/m/d') }}
                            </div>
                        </td>
                        {{-- Subjects --}}
                        <td style="padding:16px 20px;">
                            <div style="display:inline-flex;align-items:center;gap:6px;background:#f0f9ff;border-radius:20px;padding:4px 12px;">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#0284c7" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span style="color:#0369a1;font-weight:600;font-size:13px;">{{ $term->subjects_count }}</span>
                                <span style="color:#7dd3fc;font-size:12px;">مادة</span>
                            </div>
                        </td>
                        {{-- Status --}}
                        <td style="padding:16px 20px;">
                            <div style="display:inline-flex;align-items:center;gap:6px;background:{{ $statusColor['bg'] }};border-radius:20px;padding:5px 12px;">
                                <div style="width:7px;height:7px;border-radius:50%;background:{{ $statusColor['dot'] }};{{ $term->status === 'active' ? 'box-shadow:0 0 6px '.$statusColor['dot'].';' : '' }}"></div>
                                <span style="color:{{ $statusColor['text'] }};font-size:13px;font-weight:600;">{{ $statusColor['label'] }}</span>
                            </div>
                        </td>
                        {{-- Actions --}}
                        <td style="padding:16px 20px;">
                            <div style="display:flex;align-items:center;gap:6px;">
                                <a href="{{ route('admin.terms.show', $term) }}"
                                   style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;background:#ede9fe;color:#7c3aed;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;transition:all .15s;"
                                   onmouseover="this.style.background='#ddd6fe'" onmouseout="this.style.background='#ede9fe'">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    عرض
                                </a>
                                <a href="{{ route('admin.terms.edit', $term) }}"
                                   style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;background:#fef3c7;color:#d97706;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;transition:all .15s;"
                                   onmouseover="this.style.background='#fde68a'" onmouseout="this.style.background='#fef3c7'">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    تعديل
                                </a>
                                <form action="{{ route('admin.terms.destroy', $term) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا الفصل؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;background:#fee2e2;color:#dc2626;border:none;border-radius:8px;cursor:pointer;transition:all .15s;"
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
                        <td colspan="6" style="padding:60px 20px;text-align:center;">
                            <div style="display:flex;flex-direction:column;align-items:center;gap:12px;">
                                <div style="width:72px;height:72px;background:#f5f3ff;border-radius:20px;display:flex;align-items:center;justify-content:center;">
                                    <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="#a78bfa" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div style="font-weight:600;color:#374151;font-size:16px;">
                                    @if($search) لا توجد نتائج لـ "{{ $search }}" @else لا توجد فصول دراسية @endif
                                </div>
                                <div style="color:#9ca3af;font-size:14px;">
                                    @if($search) جرب البحث بكلمات مختلفة @else ابدأ بإضافة أول فصل دراسي @endif
                                </div>
                                @if($search)
                                <a href="{{ route('admin.terms.index') }}" style="margin-top:4px;padding:8px 18px;background:#4f46e5;color:white;border-radius:10px;font-size:13px;text-decoration:none;">عرض الكل</a>
                                @else
                                <a href="{{ route('admin.terms.create') }}" style="margin-top:4px;padding:8px 18px;background:#4f46e5;color:white;border-radius:10px;font-size:13px;text-decoration:none;">إضافة فصل دراسي</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($terms->hasPages())
        <div style="padding:16px 20px;border-top:1px solid #f3f4f6;">
            {{ $terms->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
