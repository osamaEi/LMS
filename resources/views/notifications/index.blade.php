@extends('layouts.dashboard')
@section('title', 'الإشعارات')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="p-2.5 rounded-xl shadow" style="background:linear-gradient(135deg,#0071AA,#005a88)">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">الإشعارات</h1>
                @if($unreadCount > 0)
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $unreadCount }} إشعار غير مقروء</p>
                @endif
            </div>
        </div>

        @if($unreadCount > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold transition-all"
                    style="background:rgba(0,113,170,.1);color:#0071AA">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                تعليم الكل كمقروء
            </button>
        </form>
        @endif
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-5 py-4 rounded-xl border-r-4 border-green-500"
         style="background:rgba(34,197,94,.08)">
        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <p class="text-green-800 dark:text-green-300 font-medium">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 px-5 py-4 rounded-xl border-r-4 border-red-500"
         style="background:rgba(239,68,68,.08)">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <p class="text-red-800 dark:text-red-300 font-medium">{{ session('error') }}</p>
    </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">

        {{-- ═══ Notifications List ═══ --}}
        <div class="flex-1 min-w-0">
            @if($notifications->count() > 0)
            <div class="space-y-3">
                @foreach($notifications as $notification)
                @php
                    $data      = $notification->data;
                    $isUnread  = is_null($notification->read_at);
                    $type      = $data['notification_type'] ?? 'session_created';
                    $actionUrl = $data['action_url'] ?? '#';
                    $title     = $data['title'] ?? $data['session_title'] ?? 'إشعار';
                    $msgBody   = $data['body'] ?? $data['message_ar'] ?? '';
                @endphp
                <a href="{{ $actionUrl }}"
                   class="flex items-start gap-4 p-4 rounded-2xl border transition-all hover:shadow-md"
                   @if($isUnread)
                   onclick="fetch('/notifications/{{ $notification->id }}/read',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}})"
                   @endif
                   style="{{ $isUnread ? 'background:linear-gradient(90deg,rgba(0,113,170,.06) 0%,#fff 100%);border-color:rgba(0,113,170,.2)' : 'background:#fff;border-color:rgba(0,0,0,.07)' }}">

                    {{-- Icon --}}
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center shadow-sm"
                         style="{{ $isUnread ? 'background:linear-gradient(135deg,#0071AA,#005a88)' : 'background:#f3f4f6' }}">
                        @if($type === 'session_updated')
                        <svg class="w-5 h-5" style="color:{{ $isUnread ? 'white' : '#9ca3af' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        @elseif($type === 'custom')
                        <svg class="w-5 h-5" style="color:{{ $isUnread ? 'white' : '#9ca3af' }}" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                        </svg>
                        @else
                        <svg class="w-5 h-5" style="color:{{ $isUnread ? 'white' : '#9ca3af' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $title }}</p>
                        @if($msgBody)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $msgBody }}</p>
                        @endif
                        <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                            <span class="text-[11px] text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                            @if(!empty($data['subject_name']))
                            <span class="text-[11px] text-gray-500">{{ $data['subject_name'] }}</span>
                            @endif
                            @if(!empty($data['scheduled_at_formatted']))
                            <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-medium"
                                  style="background:#eff6ff;color:#0071AA">{{ $data['scheduled_at_formatted'] }}</span>
                            @endif
                            @if(!empty($data['sender_name']))
                            <span class="text-[11px] text-gray-400">من: {{ $data['sender_name'] }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Unread dot --}}
                    @if($isUnread)
                    <div class="flex-shrink-0 mt-1.5">
                        <span class="block w-2.5 h-2.5 rounded-full"
                              style="background:linear-gradient(135deg,#0071AA,#0ea5e9)"></span>
                    </div>
                    @endif
                </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>

            @else
            <div class="flex flex-col items-center py-20 text-center bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mb-4"
                     style="background:linear-gradient(135deg,#eff6ff,#dbeafe)">
                    <svg class="w-10 h-10 text-blue-300" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">لا توجد إشعارات</h3>
                <p class="text-sm text-gray-400 mt-1">ستظهر هنا جميع التنبيهات والتحديثات</p>
            </div>
            @endif
        </div>

        {{-- ═══ Send Notification Panel (Admin Only) ═══ --}}
        @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
        <div class="lg:w-80 shrink-0">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden sticky top-6">

                {{-- Panel Header --}}
                <div class="px-6 py-5" style="background:linear-gradient(135deg,#1e3a5f 0%,#0071AA 100%)">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-xl" style="background:rgba(255,255,255,.15)">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white">إرسال إشعار مخصص</h3>
                            <p class="text-xs" style="color:rgba(255,255,255,.7)">أرسل إشعاراً للمستخدمين</p>
                        </div>
                    </div>
                </div>

                {{-- Form --}}
                <form action="{{ route('notifications.send') }}" method="POST" class="p-6 space-y-5">
                    @csrf

                    {{-- Validation errors --}}
                    @if($errors->any())
                    <div class="p-3 rounded-xl text-xs text-red-700 space-y-0.5"
                         style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2)">
                        @foreach($errors->all() as $err)
                        <p>• {{ $err }}</p>
                        @endforeach
                    </div>
                    @endif

                    {{-- Target --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">
                            المستهدفون <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-1.5">
                            @foreach([['student','الطلاب'],['teacher','المعلمون'],['all','الجميع']] as [$val, $lab])
                            <label class="cursor-pointer">
                                <input type="radio" name="target" value="{{ $val }}"
                                       id="t-{{ $val }}"
                                       {{ old('target', 'student') === $val ? 'checked' : '' }}
                                       class="sr-only"
                                       onchange="updateTargetUI()">
                                <span id="lbl-{{ $val }}"
                                      class="block text-center py-2.5 rounded-xl text-xs font-bold border-2 transition-all select-none">
                                    {{ $lab }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Title --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            عنوان الإشعار <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="title"
                               value="{{ old('title') }}"
                               required
                               maxlength="255"
                               placeholder="مثال: تنبيه مهم للطلاب..."
                               class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700
                                      px-3 py-2.5 text-sm text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                    </div>

                    {{-- Body --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            نص الإشعار <span class="text-red-500">*</span>
                        </label>
                        <textarea name="body"
                                  required
                                  maxlength="1000"
                                  rows="4"
                                  placeholder="اكتب تفاصيل الإشعار هنا..."
                                  class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700
                                         px-3 py-2.5 text-sm text-gray-900 dark:text-white
                                         focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 resize-none">{{ old('body') }}</textarea>
                        <p class="text-[11px] text-gray-400 mt-1">بحد أقصى 1000 حرف</p>
                    </div>

                    {{-- Action URL --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            رابط الإجراء
                            <span class="font-normal text-gray-400">(اختياري)</span>
                        </label>
                        <input type="url"
                               name="action_url"
                               value="{{ old('action_url') }}"
                               placeholder="https://..."
                               class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700
                                      px-3 py-2.5 text-sm text-gray-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400">
                        <p class="text-[11px] text-gray-400 mt-1">سيُوجَّه المستخدم إليه عند الضغط على الإشعار</p>
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-sm font-bold text-white shadow-lg transition-all hover:opacity-90 active:scale-95"
                            style="background:linear-gradient(135deg,#0071AA,#005a88)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        إرسال الإشعار
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>
</div>

<script>
function updateTargetUI() {
    var colors = { student: '#059669', teacher: '#2563eb', all: '#7c3aed' };
    ['student', 'teacher', 'all'].forEach(function (v) {
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
document.addEventListener('DOMContentLoaded', updateTargetUI);
</script>
@endsection
