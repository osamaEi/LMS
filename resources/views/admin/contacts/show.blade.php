@extends('layouts.dashboard')
@section('title', 'تفاصيل الرسالة')
@section('content')

@php
    $statusMap = [
        'new'      => ['label' => 'جديد',    'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',   'dot' => 'bg-blue-500'],
        'read'     => ['label' => 'مقروء',   'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300', 'dot' => 'bg-yellow-400'],
        'replied'  => ['label' => 'تم الرد', 'class' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',  'dot' => 'bg-green-500'],
        'archived' => ['label' => 'مؤرشف',   'class' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',        'dot' => 'bg-gray-400'],
        'pending'  => ['label' => 'معلق',    'class' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300','dot' => 'bg-orange-400'],
        'closed'   => ['label' => 'مغلق',    'class' => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',        'dot' => 'bg-gray-400'],
    ];
    $sc           = $statusMap[$contact->status] ?? ['label' => $contact->status, 'class' => 'bg-gray-100 text-gray-600', 'dot' => 'bg-gray-400'];
    $initials     = mb_strtoupper(mb_substr($contact->first_name, 0, 1) . mb_substr($contact->last_name, 0, 1));
    $avatarColors = ['#6366f1','#0891b2','#0f766e','#7c3aed','#db2777','#ea580c','#0071AA'];
    $avatarColor  = $avatarColors[abs(crc32($contact->email)) % count($avatarColors)];
@endphp

{{-- ── Alerts ── --}}
@if(session('success'))
<div class="mb-5 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
    <svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
    {{ session('success') }}
</div>
@endif
@if($errors->any())
<div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
    @foreach($errors->all() as $err)<p>{{ $err }}</p>@endforeach
</div>
@endif

{{-- ── Header ── --}}
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.contacts.index') }}"
       class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
        <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white truncate">
                {{ $contact->subject ?? '(بدون موضوع)' }}
            </h1>
            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ $sc['class'] }}">
                <span class="h-1.5 w-1.5 rounded-full {{ $sc['dot'] }}"></span>
                {{ $sc['label'] }}
            </span>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
            {{ $contact->first_name }} {{ $contact->last_name }} · {{ $contact->email }}
            · {{ $contact->created_at->format('Y/m/d H:i') }}
        </p>
    </div>
</div>

{{-- ── Two-column layout ── --}}
<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

    {{-- ════ LEFT (2/3) ════ --}}
    <div class="lg:col-span-2 flex flex-col gap-5">

        {{-- Original Message --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            <div class="flex items-center gap-3 border-b border-gray-100 dark:border-gray-800 px-5 py-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/30">
                    <svg class="h-4 w-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wider">الرسالة الواردة</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $contact->subject ?? '(بدون موضوع)' }}</p>
                </div>
            </div>
            <div class="p-5">
                <div class="rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 px-5 py-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                    {{ $contact->message }}
                </div>
                @if($contact->attachment)
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <a href="{{ Storage::url($contact->attachment) }}" target="_blank"
                       class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        عرض المرفق
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Sent Reply (if exists) --}}
        @if($contact->reply_message)
        <div class="rounded-xl border border-green-200 bg-white dark:border-green-800 dark:bg-gray-900 overflow-hidden">
            <div class="flex items-center gap-3 border-b border-green-100 dark:border-green-900 bg-green-50 dark:bg-green-900/20 px-5 py-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/40">
                    <svg class="h-4 w-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-green-800 dark:text-green-300">تم إرسال الرد بنجاح</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        بواسطة {{ $contact->replied_by ?? '—' }}
                        @if($contact->replied_at)
                         · {{ \Carbon\Carbon::parse($contact->replied_at)->format('Y/m/d H:i') }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="p-5">
                <div class="rounded-lg border border-green-100 dark:border-green-900 bg-green-50 dark:bg-green-900/10 px-5 py-4 text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line border-r-4 border-r-green-500">
                    {{ $contact->reply_message }}
                </div>
            </div>
        </div>
        @endif

        {{-- Reply Form --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            <div class="flex items-center gap-3 border-b border-gray-100 dark:border-gray-800 px-5 py-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#0071AA]/10">
                    <svg class="h-4 w-4 text-[#0071AA]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $contact->reply_message ? 'إرسال رد جديد' : 'الرد على الرسالة' }}
                    </p>
                    <p class="text-xs text-gray-400">سيُرسَل إلى {{ $contact->email }}</p>
                </div>
            </div>
            <form action="{{ route('admin.contacts.reply', $contact) }}" method="POST" class="p-5">
                @csrf
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        نص الرد <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reply_message" rows="5"
                              placeholder="اكتب ردك هنا..."
                              required minlength="10"
                              class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white resize-y focus:border-[#0071AA] focus:ring-2 focus:ring-[#0071AA]/20 outline-none transition-colors">{{ old('reply_message') }}</textarea>
                    @error('reply_message')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <p class="flex items-center gap-1.5 text-xs text-amber-600 dark:text-amber-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        سيُحدَّث الحالة إلى "تم الرد" تلقائياً
                    </p>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold text-white transition-opacity hover:opacity-90"
                            style="background:#0071AA;">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        إرسال الرد
                    </button>
                </div>
            </form>
        </div>

    </div>

    {{-- ════ RIGHT (1/3) ════ --}}
    <div class="flex flex-col gap-5">

        {{-- Sender Info --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            <div class="border-b border-gray-100 dark:border-gray-800 px-5 py-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">معلومات المُرسِل</p>
            </div>
            <div class="p-5">
                {{-- Avatar --}}
                <div class="flex items-center gap-3 mb-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-base font-bold text-white"
                         style="background:{{ $avatarColor }}">{{ $initials }}</div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $contact->first_name }} {{ $contact->last_name }}</p>
                        <p class="text-xs text-gray-400">{{ $contact->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="space-y-3">
                    @foreach([
                        ['path' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => 'البريد',     'value' => $contact->email],
                        ['path' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'label' => 'الجوال',     'value' => $contact->phone ?? '—'],
                        ['path' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'label' => 'التصنيف',   'value' => $contact->category ?? '—'],
                        ['path' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                   'label' => 'التاريخ',   'value' => $contact->created_at->format('Y/m/d H:i')],
                    ] as $row)
                    <div class="flex items-start gap-3">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800 mt-0.5">
                            <svg class="h-3.5 w-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $row['path'] }}"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs text-gray-400">{{ $row['label'] }}</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 break-all">{{ $row['value'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
            <div class="border-b border-gray-100 dark:border-gray-800 px-5 py-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">إدارة الحالة</p>
            </div>
            <div class="p-5">
                <form action="{{ route('admin.contacts.update-status', $contact) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <select name="status"
                            class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white outline-none focus:border-[#0071AA] transition-colors"
                            style="color-scheme:light;">
                        <option value="new"      {{ $contact->status==='new'      ?'selected':'' }}>🔵 جديد</option>
                        <option value="read"     {{ $contact->status==='read'     ?'selected':'' }}>👁️ مقروء</option>
                        <option value="replied"  {{ $contact->status==='replied'  ?'selected':'' }}>✅ تم الرد</option>
                        <option value="archived" {{ $contact->status==='archived' ?'selected':'' }}>📁 مؤرشف</option>
                        <option value="closed"   {{ $contact->status==='closed'   ?'selected':'' }}>🔒 مغلق</option>
                    </select>
                    <button type="submit"
                            class="w-full rounded-lg py-2.5 text-sm font-semibold text-white transition-opacity hover:opacity-90"
                            style="background:#0071AA;">
                        حفظ الحالة
                    </button>
                </form>
            </div>
        </div>

        {{-- Danger --}}
        <div class="rounded-xl border border-red-200 bg-white dark:border-red-900 dark:bg-gray-900 overflow-hidden">
            <div class="border-b border-red-100 dark:border-red-900 bg-red-50 dark:bg-red-900/20 px-5 py-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-red-600 dark:text-red-400">منطقة الخطر</p>
            </div>
            <div class="p-5">
                <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST"
                      onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة نهائياً؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 rounded-lg border border-red-300 bg-white px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-600 hover:text-white hover:border-red-600 dark:bg-transparent dark:border-red-800 dark:text-red-400 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        حذف الرسالة نهائياً
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection
