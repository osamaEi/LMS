@extends('layouts.dashboard')

@section('title', 'تفاصيل الرسالة')

@push('styles')
<style>
    .msg-hero {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #003d5c 100%);
        border-radius: 1.25rem;
        position: relative; overflow: hidden;
    }
    .msg-hero::before {
        content: ''; position: absolute; inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .info-chip {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 12px; border-radius: 999px;
        background: #f1f5f9; color: #475569;
        font-size: 0.8rem; font-weight: 500;
    }
    .info-chip svg { width: 14px; height: 14px; color: #94a3b8; }
    .status-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 12px; border-radius: 999px;
        font-size: 0.75rem; font-weight: 700;
    }
    .status-badge::before { content:''; width:7px; height:7px; border-radius:50%; background:currentColor; }
    .message-bubble {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 1rem;
        padding: 1.5rem;
        line-height: 1.9;
        color: #374151;
        font-size: 0.925rem;
        white-space: pre-line;
        position: relative;
    }
    .message-bubble::before {
        content: '"';
        position: absolute; top: -8px; right: 20px;
        font-size: 3rem; color: #cbd5e1; line-height: 1;
        font-family: Georgia, serif;
    }
</style>
@endpush

@section('content')
<div class="p-6 max-w-3xl mx-auto">

    {{-- Alert --}}
    @if(session('success'))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Hero --}}
    @php
        $statusConfig = [
            'new'      => ['label' => 'جديد',    'class' => 'bg-blue-100 text-blue-700'],
            'read'     => ['label' => 'مقروء',   'class' => 'bg-amber-100 text-amber-700'],
            'replied'  => ['label' => 'تم الرد', 'class' => 'bg-green-100 text-green-700'],
            'archived' => ['label' => 'مؤرشف',   'class' => 'bg-gray-200 text-gray-600'],
        ];
        $sc = $statusConfig[$contact->status] ?? ['label' => $contact->status, 'class' => 'bg-gray-100 text-gray-600'];
        $initials = mb_substr($contact->first_name, 0, 1) . mb_substr($contact->last_name, 0, 1);
    @endphp

    <div class="msg-hero p-6 mb-6 text-white">
        <div class="relative z-10">
            {{-- Back --}}
            <a href="{{ route('admin.contacts.index') }}"
               class="inline-flex items-center gap-2 text-blue-200 hover:text-white text-sm mb-5 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                العودة إلى صندوق الرسائل
            </a>

            <div class="flex items-center gap-4">
                {{-- Avatar --}}
                <div class="w-14 h-14 rounded-2xl bg-white bg-opacity-20 flex items-center justify-center text-xl font-bold flex-shrink-0">
                    {{ $initials }}
                </div>
                <div class="flex-1 min-w-0">
                    <h1 class="text-xl font-bold mb-1">{{ $contact->first_name }} {{ $contact->last_name }}</h1>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-blue-200 text-sm">{{ $contact->email }}</span>
                        <span class="status-badge {{ $sc['class'] }}">{{ $sc['label'] }}</span>
                    </div>
                </div>
                <div class="text-left text-blue-200 text-xs hidden sm:block">
                    <div>{{ $contact->created_at->format('Y/m/d') }}</div>
                    <div>{{ $contact->created_at->format('H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Chips --}}
    <div class="flex flex-wrap gap-2 mb-6">
        <span class="info-chip">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            {{ $contact->email }}
        </span>
        @if($contact->phone)
        <span class="info-chip">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            {{ $contact->phone }}
        </span>
        @endif
        @if($contact->category)
        <span class="info-chip">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            {{ $contact->category }}
        </span>
        @endif
    </div>

    {{-- Subject + Message --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-5">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-1">الموضوع</p>
            <h2 class="text-gray-900 font-bold text-lg">{{ $contact->subject }}</h2>
        </div>

        <div class="p-6">
            <div class="message-bubble">{{ $contact->message }}</div>

            @if($contact->attachment)
            <div class="mt-5 pt-5 border-t border-gray-100">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mb-2">المرفق</p>
                <a href="{{ Storage::url($contact->attachment) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-xl text-sm font-medium hover:bg-blue-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    عرض المرفق
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Actions Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm font-semibold text-gray-700 mb-4">إدارة الرسالة</p>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Status dropdown --}}
            <form action="{{ route('admin.contacts.update-status', $contact) }}" method="POST" class="flex items-center gap-2 flex-1 min-w-0">
                @csrf
                @method('PATCH')
                <select name="status"
                        class="flex-1 border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="new"      {{ $contact->status === 'new'      ? 'selected' : '' }}>🔵 جديد</option>
                    <option value="read"     {{ $contact->status === 'read'     ? 'selected' : '' }}>👁 مقروء</option>
                    <option value="replied"  {{ $contact->status === 'replied'  ? 'selected' : '' }}>✅ تم الرد</option>
                    <option value="archived" {{ $contact->status === 'archived' ? 'selected' : '' }}>📁 مؤرشف</option>
                </select>
                <button type="submit"
                        class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition-colors whitespace-nowrap">
                    حفظ الحالة
                </button>
            </form>

            {{-- Divider --}}
            <div class="h-9 w-px bg-gray-200 hidden sm:block"></div>

            {{-- Delete --}}
            <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST"
                  onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة نهائياً؟')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 border border-red-200 text-red-600 rounded-xl text-sm font-medium hover:bg-red-600 hover:text-white hover:border-red-600 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    حذف الرسالة
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
