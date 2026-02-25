@extends('layouts.dashboard')

@section('title', 'تفاصيل الرسالة')

@section('content')
<div style="padding:1.5rem;direction:rtl;max-width:900px;margin:0 auto;">

    {{-- Success / Error Alerts --}}
    @if(session('success'))
    <div style="margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;padding:14px 18px;background:#f0fdf4;border:1px solid #86efac;border-radius:14px;color:#166534;font-size:.9rem;">
        <svg style="width:18px;height:18px;flex-shrink:0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div style="margin-bottom:1.25rem;padding:14px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:14px;color:#991b1b;font-size:.9rem;">
        @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
    </div>
    @endif

    @php
        $statusConfig = [
            'new'      => ['label' => 'جديد',    'bg' => '#dbeafe', 'color' => '#1e40af', 'dot' => '#3b82f6'],
            'read'     => ['label' => 'مقروء',   'bg' => '#fef9c3', 'color' => '#854d0e', 'dot' => '#eab308'],
            'replied'  => ['label' => 'تم الرد', 'bg' => '#dcfce7', 'color' => '#166534', 'dot' => '#22c55e'],
            'archived' => ['label' => 'مؤرشف',   'bg' => '#f1f5f9', 'color' => '#475569', 'dot' => '#94a3b8'],
        ];
        $sc = $statusConfig[$contact->status] ?? ['label' => $contact->status, 'bg' => '#f1f5f9', 'color' => '#475569', 'dot' => '#94a3b8'];
        $initials = mb_strtoupper(mb_substr($contact->first_name, 0, 1) . mb_substr($contact->last_name, 0, 1));
        $avatarColors = ['#6366f1','#0891b2','#0f766e','#7c3aed','#db2777','#ea580c','#0071AA'];
        $avatarColor = $avatarColors[crc32($contact->email) % count($avatarColors)];
    @endphp

    {{-- ===== HERO ===== --}}
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 60%,#0f2744 100%);border-radius:1.5rem;padding:2rem;margin-bottom:1.5rem;position:relative;overflow:hidden;">
        {{-- Decoration circles --}}
        <div style="position:absolute;top:-40px;left:-40px;width:200px;height:200px;border-radius:50%;background:rgba(99,102,241,.08);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-30px;right:-30px;width:160px;height:160px;border-radius:50%;background:rgba(0,113,170,.1);pointer-events:none;"></div>

        <div style="position:relative;z-index:1;">
            {{-- Back button --}}
            <a href="{{ route('admin.contacts.index') }}"
               style="display:inline-flex;align-items:center;gap:8px;color:#93c5fd;font-size:.85rem;text-decoration:none;margin-bottom:1.5rem;transition:color .2s;"
               onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#93c5fd'">
                <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                العودة إلى صندوق الرسائل
            </a>

            <div style="display:flex;align-items:flex-start;gap:1.25rem;flex-wrap:wrap;">
                {{-- Avatar --}}
                <div style="width:64px;height:64px;border-radius:1rem;background:{{ $avatarColor }};display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:800;color:#fff;flex-shrink:0;box-shadow:0 4px 15px rgba(0,0,0,.3);">
                    {{ $initials }}
                </div>

                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:.4rem;">
                        <h1 style="color:#fff;font-size:1.4rem;font-weight:800;margin:0;">
                            {{ $contact->first_name }} {{ $contact->last_name }}
                        </h1>
                        {{-- Status badge --}}
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:999px;background:{{ $sc['bg'] }};color:{{ $sc['color'] }};font-size:.75rem;font-weight:700;">
                            <span style="width:7px;height:7px;border-radius:50%;background:{{ $sc['dot'] }};display:inline-block;"></span>
                            {{ $sc['label'] }}
                        </span>
                    </div>

                    <div style="display:flex;flex-wrap:wrap;gap:1rem;color:#94a3b8;font-size:.85rem;">
                        <span style="display:flex;align-items:center;gap:5px;">
                            <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ $contact->email }}
                        </span>
                        @if($contact->phone)
                        <span style="display:flex;align-items:center;gap:5px;">
                            <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $contact->phone }}
                        </span>
                        @endif
                        <span style="display:flex;align-items:center;gap:5px;">
                            <svg style="width:14px;height:14px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $contact->created_at->format('Y/m/d') }} · {{ $contact->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                {{-- Quick status chip --}}
                @if($contact->category)
                <div style="flex-shrink:0;">
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;background:rgba(99,102,241,.2);color:#a5b4fc;font-size:.8rem;font-weight:600;">
                        <svg style="width:13px;height:13px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        {{ $contact->category }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== TWO-COLUMN LAYOUT ===== --}}
    <div style="display:grid;grid-template-columns:1fr 320px;gap:1.25rem;align-items:start;">

        {{-- LEFT COLUMN: Message + Reply --}}
        <div style="display:flex;flex-direction:column;gap:1.25rem;">

            {{-- Message Card --}}
            <div style="background:#fff;border-radius:1.25rem;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.05);">
                {{-- Card Header --}}
                <div style="padding:1rem 1.5rem;border-bottom:1px solid #f1f5f9;background:linear-gradient(135deg,rgba(0,113,170,.04),rgba(0,113,170,.01));display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(0,113,170,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:17px;height:17px;color:#0071AA" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size:.7rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin:0;">الموضوع</p>
                        <h2 style="font-size:1rem;font-weight:700;color:#1e293b;margin:0;">{{ $contact->subject ?? '(بدون موضوع)' }}</h2>
                    </div>
                </div>

                {{-- Message Body --}}
                <div style="padding:1.5rem;">
                    <div style="position:relative;background:#f8fafc;border:1px solid #e2e8f0;border-radius:1rem;padding:1.5rem;line-height:1.9;color:#374151;font-size:.925rem;white-space:pre-line;">
                        <span style="position:absolute;top:-10px;right:18px;font-size:3.5rem;color:#cbd5e1;line-height:1;font-family:Georgia,serif;">"</span>
                        {{ $contact->message }}
                    </div>

                    @if($contact->attachment)
                    <div style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid #f1f5f9;">
                        <p style="font-size:.75rem;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.6rem;">المرفق</p>
                        <a href="{{ Storage::url($contact->attachment) }}" target="_blank"
                           style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;background:#eff6ff;color:#1d4ed8;border-radius:10px;font-size:.875rem;font-weight:600;text-decoration:none;transition:background .2s;"
                           onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                            <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            عرض المرفق
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Reply Form Card --}}
            <div style="background:#fff;border-radius:1.25rem;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.05);">
                <div style="padding:1rem 1.5rem;border-bottom:1px solid #f1f5f9;background:linear-gradient(135deg,rgba(34,197,94,.06),rgba(22,163,74,.02));display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(34,197,94,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg style="width:17px;height:17px;color:#16a34a" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size:1rem;font-weight:700;color:#1e293b;margin:0;">الرد على الرسالة</h3>
                        <p style="font-size:.8rem;color:#64748b;margin:0;">سيتم إرسال الرد إلى {{ $contact->email }} وإشعار المدير العام</p>
                    </div>
                </div>

                <form action="{{ route('admin.contacts.reply', $contact) }}" method="POST" style="padding:1.5rem;">
                    @csrf

                    <div style="margin-bottom:1rem;">
                        <label style="display:block;font-size:.85rem;font-weight:600;color:#374151;margin-bottom:.5rem;">
                            نص الرد
                            <span style="color:#ef4444;">*</span>
                        </label>
                        <textarea name="reply_message" rows="5"
                                  placeholder="اكتب ردك هنا..."
                                  style="width:100%;border:1.5px solid #e2e8f0;border-radius:12px;padding:14px 16px;font-size:.9rem;color:#374151;resize:vertical;outline:none;transition:border-color .2s;background:#f8fafc;box-sizing:border-box;font-family:inherit;"
                                  onfocus="this.style.borderColor='#0071AA';this.style.background='#fff'"
                                  onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc'"
                                  required minlength="10">{{ old('reply_message') }}</textarea>
                        @error('reply_message')
                        <p style="color:#ef4444;font-size:.8rem;margin-top:.3rem;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
                        <div style="display:flex;align-items:center;gap:6px;padding:8px 12px;background:#fef9c3;border-radius:8px;color:#92400e;font-size:.8rem;">
                            <svg style="width:14px;height:14px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            سيُحدَّث حالة الرسالة إلى "تم الرد" تلقائياً
                        </div>

                        <button type="submit"
                                style="display:inline-flex;align-items:center;gap:8px;padding:12px 24px;background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;border:none;border-radius:12px;font-size:.9rem;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(22,163,74,.3);transition:all .2s;"
                                onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 18px rgba(22,163,74,.4)'"
                                onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(22,163,74,.3)'">
                            <svg style="width:17px;height:17px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            إرسال الرد
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- RIGHT COLUMN: Sidebar --}}
        <div style="display:flex;flex-direction:column;gap:1.25rem;">

            {{-- Contact Info Card --}}
            <div style="background:#fff;border-radius:1.25rem;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.05);">
                <div style="padding:.85rem 1.25rem;border-bottom:1px solid #f1f5f9;background:#fafafa;">
                    <h4 style="font-size:.85rem;font-weight:700;color:#1e293b;margin:0;">معلومات المُرسِل</h4>
                </div>
                <div style="padding:1.25rem;display:flex;flex-direction:column;gap:.85rem;">

                    {{-- Avatar + name --}}
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:44px;height:44px;border-radius:.75rem;background:{{ $avatarColor }};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:1rem;flex-shrink:0;">
                            {{ $initials }}
                        </div>
                        <div>
                            <div style="font-weight:700;color:#1e293b;font-size:.9rem;">{{ $contact->first_name }} {{ $contact->last_name }}</div>
                            <div style="font-size:.78rem;color:#64748b;">مُرسَل {{ $contact->created_at->diffForHumans() }}</div>
                        </div>
                    </div>

                    <div style="height:1px;background:#f1f5f9;"></div>

                    {{-- Info rows --}}
                    @php
                        $infoRows = [
                            ['icon'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label'=>'البريد', 'value'=>$contact->email, 'copy'=>true],
                            ['icon'=>'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'label'=>'الجوال', 'value'=>$contact->phone ?? '—', 'copy'=>false],
                            ['icon'=>'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'label'=>'التصنيف', 'value'=>$contact->category ?? '—', 'copy'=>false],
                            ['icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'label'=>'التاريخ', 'value'=>$contact->created_at->format('Y/m/d H:i'), 'copy'=>false],
                        ];
                    @endphp
                    @foreach($infoRows as $row)
                    <div style="display:flex;align-items:flex-start;gap:10px;">
                        <div style="width:30px;height:30px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;">
                            <svg style="width:14px;height:14px;color:#64748b" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $row['icon'] }}"/>
                            </svg>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:.72rem;color:#94a3b8;margin-bottom:1px;">{{ $row['label'] }}</div>
                            <div style="font-size:.85rem;color:#374151;font-weight:500;word-break:break-all;">{{ $row['value'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Status Management Card --}}
            <div style="background:#fff;border-radius:1.25rem;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.05);">
                <div style="padding:.85rem 1.25rem;border-bottom:1px solid #f1f5f9;background:#fafafa;">
                    <h4 style="font-size:.85rem;font-weight:700;color:#1e293b;margin:0;">إدارة الحالة</h4>
                </div>
                <div style="padding:1.25rem;">
                    <form action="{{ route('admin.contacts.update-status', $contact) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <select name="status"
                                style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 14px;font-size:.875rem;background:#f8fafc;color:#374151;outline:none;margin-bottom:.85rem;cursor:pointer;"
                                onfocus="this.style.borderColor='#0071AA'" onblur="this.style.borderColor='#e2e8f0'">
                            <option value="new"      {{ $contact->status === 'new'      ? 'selected' : '' }}>🔵 جديد</option>
                            <option value="read"     {{ $contact->status === 'read'     ? 'selected' : '' }}>👁️ مقروء</option>
                            <option value="replied"  {{ $contact->status === 'replied'  ? 'selected' : '' }}>✅ تم الرد</option>
                            <option value="archived" {{ $contact->status === 'archived' ? 'selected' : '' }}>📁 مؤرشف</option>
                        </select>
                        <button type="submit"
                                style="width:100%;padding:10px;background:#0071AA;color:#fff;border:none;border-radius:10px;font-size:.875rem;font-weight:600;cursor:pointer;transition:background .2s;"
                                onmouseover="this.style.background='#005a88'" onmouseout="this.style.background='#0071AA'">
                            حفظ الحالة
                        </button>
                    </form>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div style="background:#fff;border-radius:1.25rem;border:1px solid #fee2e2;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.05);">
                <div style="padding:.85rem 1.25rem;border-bottom:1px solid #fee2e2;background:#fff5f5;">
                    <h4 style="font-size:.85rem;font-weight:700;color:#991b1b;margin:0;">منطقة الخطر</h4>
                </div>
                <div style="padding:1.25rem;">
                    <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة نهائياً؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;border:1.5px solid #fca5a5;color:#dc2626;background:#fff;border-radius:10px;font-size:.875rem;font-weight:600;cursor:pointer;transition:all .2s;"
                                onmouseover="this.style.background='#dc2626';this.style.color='#fff';this.style.borderColor='#dc2626'"
                                onmouseout="this.style.background='#fff';this.style.color='#dc2626';this.style.borderColor='#fca5a5'">
                            <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            حذف الرسالة نهائياً
                        </button>
                    </form>
                </div>
            </div>

        </div>{{-- end right column --}}
    </div>{{-- end grid --}}

</div>
@endsection
