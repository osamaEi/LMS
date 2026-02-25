@extends('layouts.dashboard')
@section('title', 'الإشعارات')

@section('content')
<div style="padding:1.5rem;direction:rtl;max-width:1100px;margin:0 auto;">

    {{-- ===== HERO ===== --}}
    <div style="background:linear-gradient(135deg,#1e3a5f 0%,#0071AA 60%,#005a88 100%);border-radius:1.5rem;padding:2rem;margin-bottom:1.75rem;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-50px;left:-50px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-40px;right:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.04);pointer-events:none;"></div>

        <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div style="display:flex;align-items:center;gap:1rem;">
                <div style="width:56px;height:56px;border-radius:1rem;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:28px;height:28px;color:#fff" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                    </svg>
                </div>
                <div>
                    <h1 style="color:#fff;font-size:1.6rem;font-weight:800;margin:0;line-height:1.2;">الإشعارات</h1>
                    <p style="color:rgba(255,255,255,.75);font-size:.875rem;margin:.25rem 0 0;">
                        @if($unreadCount > 0)
                            <span style="display:inline-flex;align-items:center;gap:5px;">
                                <span style="width:8px;height:8px;border-radius:50%;background:#fbbf24;display:inline-block;animation:pulse 2s infinite;"></span>
                                {{ $unreadCount }} إشعار غير مقروء
                            </span>
                        @else
                            جميع الإشعارات مقروءة
                        @endif
                    </p>
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
                {{-- Stats chips --}}
                <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;background:rgba(255,255,255,.12);color:#fff;font-size:.8rem;font-weight:600;">
                    <svg style="width:13px;height:13px" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
                    {{ $notifications->total() }} إشعار
                </span>

                @if($unreadCount > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit"
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:rgba(255,255,255,.18);color:#fff;border:none;border-radius:10px;font-size:.85rem;font-weight:700;cursor:pointer;transition:background .2s;"
                            onmouseover="this.style.background='rgba(255,255,255,.28)'"
                            onmouseout="this.style.background='rgba(255,255,255,.18)'">
                        <svg style="width:15px;height:15px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        تعليم الكل كمقروء
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div style="margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;padding:14px 18px;background:#f0fdf4;border:1px solid #86efac;border-radius:14px;color:#166534;font-size:.9rem;">
        <svg style="width:18px;height:18px;flex-shrink:0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;padding:14px 18px;background:#fef2f2;border:1px solid #fca5a5;border-radius:14px;color:#991b1b;font-size:.9rem;">
        <svg style="width:18px;height:18px;flex-shrink:0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- ===== MAIN LAYOUT ===== --}}
    <div style="display:grid;grid-template-columns:1fr 320px;gap:1.25rem;align-items:start;">

        {{-- ===== LEFT: Notifications List ===== --}}
        <div>
            @if($notifications->count() > 0)

            <div style="display:flex;flex-direction:column;gap:.75rem;">
                @foreach($notifications as $notification)
                @php
                    $data      = $notification->data;
                    $isUnread  = is_null($notification->read_at);
                    $type      = $data['notification_type'] ?? 'session_created';
                    $actionUrl = $data['action_url'] ?? '#';
                    $title     = $data['title'] ?? $data['session_title'] ?? 'إشعار';
                    $msgBody   = $data['body'] ?? $data['message_ar'] ?? '';
                    $sender    = $data['sender_name'] ?? '';

                    // Icon + color by type
                    $iconBg = $isUnread ? 'linear-gradient(135deg,#0071AA,#005a88)' : '#f3f4f6';
                    $iconColor = $isUnread ? 'white' : '#9ca3af';
                    if ($type === 'custom') {
                        $typeBadge = ['label' => 'مخصص', 'bg' => '#eff6ff', 'color' => '#1d4ed8'];
                    } elseif ($type === 'session_updated') {
                        $typeBadge = ['label' => 'تحديث جلسة', 'bg' => '#fff7ed', 'color' => '#c2410c'];
                    } else {
                        $typeBadge = ['label' => 'جلسة جديدة', 'bg' => '#f0fdf4', 'color' => '#15803d'];
                    }
                @endphp

                <a href="{{ $actionUrl }}"
                   @if($isUnread)
                   onclick="fetch('/notifications/{{ $notification->id }}/read',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}})"
                   @endif
                   style="display:flex;align-items:flex-start;gap:14px;padding:16px 18px;border-radius:1rem;border:1.5px solid {{ $isUnread ? 'rgba(0,113,170,.25)' : '#f1f5f9' }};background:{{ $isUnread ? 'linear-gradient(90deg,rgba(0,113,170,.06) 0%,#fff 100%)' : '#fff' }};text-decoration:none;transition:all .2s;box-shadow:{{ $isUnread ? '0 2px 10px rgba(0,113,170,.08)' : '0 1px 3px rgba(0,0,0,.04)' }};"
                   onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.1)';this.style.transform='translateY(-1px)'"
                   onmouseout="this.style.boxShadow='{{ $isUnread ? '0 2px 10px rgba(0,113,170,.08)' : '0 1px 3px rgba(0,0,0,.04)' }}';this.style.transform='translateY(0)'">

                    {{-- Icon --}}
                    <div style="flex-shrink:0;width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:{{ $iconBg }};box-shadow:{{ $isUnread ? '0 2px 8px rgba(0,113,170,.25)' : 'none' }};">
                        @if($type === 'session_updated')
                        <svg style="width:20px;height:20px;color:{{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        @elseif($type === 'custom')
                        <svg style="width:20px;height:20px;color:{{ $iconColor }}" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                        </svg>
                        @else
                        <svg style="width:20px;height:20px;color:{{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                            <p style="font-size:.9rem;font-weight:700;color:#111827;margin:0;line-height:1.35;">{{ $title }}</p>
                            <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                                <span style="display:inline-flex;padding:2px 10px;border-radius:999px;font-size:.7rem;font-weight:700;background:{{ $typeBadge['bg'] }};color:{{ $typeBadge['color'] }};">{{ $typeBadge['label'] }}</span>
                                @if($isUnread)
                                <span style="display:block;width:9px;height:9px;border-radius:50%;background:linear-gradient(135deg,#0071AA,#38bdf8);flex-shrink:0;"></span>
                                @endif
                            </div>
                        </div>

                        @if($msgBody)
                        <p style="font-size:.82rem;color:#6b7280;margin:4px 0 0;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $msgBody }}</p>
                        @endif

                        <div style="display:flex;align-items:center;gap:.75rem;margin-top:7px;flex-wrap:wrap;">
                            <span style="font-size:.75rem;color:#9ca3af;display:flex;align-items:center;gap:4px;">
                                <svg style="width:12px;height:12px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                            @if($sender)
                            <span style="font-size:.75rem;color:#64748b;display:flex;align-items:center;gap:4px;">
                                <svg style="width:12px;height:12px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ $sender }}
                            </span>
                            @endif
                            @if(!empty($data['scheduled_at_formatted']))
                            <span style="display:inline-flex;padding:2px 10px;border-radius:999px;font-size:.72rem;font-weight:600;background:#eff6ff;color:#0071AA;">{{ $data['scheduled_at_formatted'] }}</span>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div style="margin-top:1.5rem;">
                {{ $notifications->links() }}
            </div>

            @else
            {{-- Empty state --}}
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:5rem 2rem;text-align:center;background:#fff;border-radius:1.25rem;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(0,0,0,.04);">
                <div style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,#eff6ff,#dbeafe);display:flex;align-items:center;justify-content:center;margin-bottom:1.5rem;">
                    <svg style="width:44px;height:44px;color:#93c5fd" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                    </svg>
                </div>
                <h3 style="font-size:1.1rem;font-weight:700;color:#374151;margin:0;">لا توجد إشعارات بعد</h3>
                <p style="font-size:.875rem;color:#9ca3af;margin:.5rem 0 0;">ستظهر هنا جميع التنبيهات والتحديثات فور وصولها</p>
            </div>
            @endif
        </div>

        {{-- ===== RIGHT: Send Notification Panel (Admin only) ===== --}}
        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
        <div style="display:flex;flex-direction:column;gap:1.25rem;">

            {{-- Send Card --}}
            <div style="background:#fff;border-radius:1.25rem;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.05);position:sticky;top:1.5rem;">
                {{-- Header --}}
                <div style="padding:1.25rem 1.5rem;background:linear-gradient(135deg,#1e3a5f 0%,#0071AA 100%);">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:18px;height:18px;color:#fff" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-size:.95rem;font-weight:800;color:#fff;margin:0;">إرسال إشعار مخصص</h3>
                            <p style="font-size:.75rem;color:rgba(255,255,255,.7);margin:2px 0 0;">للمستخدمين عبر المنصة</p>
                        </div>
                    </div>
                </div>

                {{-- Form --}}
                <form action="{{ route('notifications.send') }}" method="POST" style="padding:1.25rem;display:flex;flex-direction:column;gap:1rem;">
                    @csrf

                    @if($errors->any())
                    <div style="padding:10px 14px;background:#fef2f2;border:1px solid #fca5a5;border-radius:10px;font-size:.8rem;color:#991b1b;">
                        @foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
                    </div>
                    @endif

                    {{-- Target selector --}}
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:700;color:#374151;margin-bottom:.6rem;">المستهدفون <span style="color:#ef4444;">*</span></label>
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;">
                            @foreach([['student','الطلاب','#059669'],['teacher','المعلمون','#2563eb'],['all','الجميع','#7c3aed']] as [$val, $lab, $col])
                            <label style="cursor:pointer;">
                                <input type="radio" name="target" value="{{ $val }}" id="t-{{ $val }}"
                                       {{ old('target', 'student') === $val ? 'checked' : '' }}
                                       style="display:none;" onchange="updateTarget()">
                                <span id="lbl-{{ $val }}"
                                      style="display:block;text-align:center;padding:9px 4px;border-radius:10px;font-size:.78rem;font-weight:700;border:2px solid #e5e7eb;color:#6b7280;cursor:pointer;transition:all .18s;user-select:none;">
                                    {{ $lab }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Title --}}
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:700;color:#374151;margin-bottom:.4rem;">عنوان الإشعار <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required maxlength="255"
                               placeholder="مثال: تنبيه مهم..."
                               style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 14px;font-size:.875rem;color:#374151;outline:none;background:#f8fafc;box-sizing:border-box;"
                               onfocus="this.style.borderColor='#0071AA';this.style.background='#fff'"
                               onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc'">
                    </div>

                    {{-- Body --}}
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:700;color:#374151;margin-bottom:.4rem;">نص الإشعار <span style="color:#ef4444;">*</span></label>
                        <textarea name="body" required maxlength="1000" rows="4"
                                  placeholder="اكتب تفاصيل الإشعار هنا..."
                                  style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 14px;font-size:.875rem;color:#374151;outline:none;background:#f8fafc;resize:vertical;box-sizing:border-box;font-family:inherit;"
                                  onfocus="this.style.borderColor='#0071AA';this.style.background='#fff'"
                                  onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc'">{{ old('body') }}</textarea>
                        <p style="font-size:.72rem;color:#9ca3af;margin-top:4px;">بحد أقصى 1000 حرف</p>
                    </div>

                    {{-- Action URL --}}
                    <div>
                        <label style="display:block;font-size:.8rem;font-weight:700;color:#374151;margin-bottom:.4rem;">
                            رابط الإجراء <span style="font-weight:400;color:#9ca3af;">(اختياري)</span>
                        </label>
                        <input type="url" name="action_url" value="{{ old('action_url') }}"
                               placeholder="https://..."
                               style="width:100%;border:1.5px solid #e2e8f0;border-radius:10px;padding:10px 14px;font-size:.875rem;color:#374151;outline:none;background:#f8fafc;box-sizing:border-box;"
                               onfocus="this.style.borderColor='#0071AA';this.style.background='#fff'"
                               onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc'">
                        <p style="font-size:.72rem;color:#9ca3af;margin-top:4px;">يُوجَّه المستخدم إليه عند الضغط</p>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:13px;background:linear-gradient(135deg,#0071AA,#005a88);color:#fff;border:none;border-radius:12px;font-size:.9rem;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(0,113,170,.3);transition:all .2s;"
                            onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 18px rgba(0,113,170,.4)'"
                            onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(0,113,170,.3)'">
                        <svg style="width:17px;height:17px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        إرسال الإشعار
                    </button>
                </form>
            </div>

            {{-- Info card --}}
            <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:1rem;padding:1rem 1.25rem;">
                <p style="font-size:.8rem;font-weight:700;color:#0369a1;margin:0 0 .5rem;">معلومات</p>
                <ul style="margin:0;padding:0 1rem;font-size:.78rem;color:#0c4a6e;line-height:1.8;list-style-type:disc;">
                    <li>يتم حفظ الإشعارات في قاعدة البيانات</li>
                    <li>تظهر الإشعارات فوراً في جرس كل مستخدم</li>
                    <li>تُرسَل إشعارات التواصل تلقائياً للمدير العام</li>
                </ul>
            </div>

        </div>
        @endif

    </div>
</div>

<style>
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
</style>

<script>
function updateTarget() {
    ['student','teacher','all'].forEach(function(v) {
        var radio = document.getElementById('t-' + v);
        var lbl   = document.getElementById('lbl-' + v);
        if (!radio || !lbl) return;
        if (radio.checked) {
            lbl.style.background  = 'linear-gradient(135deg,#0071AA,#005a88)';
            lbl.style.color       = 'white';
            lbl.style.borderColor = 'transparent';
            lbl.style.boxShadow   = '0 2px 8px rgba(0,113,170,.3)';
        } else {
            lbl.style.background  = 'transparent';
            lbl.style.color       = '#6b7280';
            lbl.style.borderColor = '#e5e7eb';
            lbl.style.boxShadow   = 'none';
        }
    });
}
document.addEventListener('DOMContentLoaded', updateTarget);
</script>
@endsection
