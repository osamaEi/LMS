@extends('layouts.dashboard')

@section('title', 'رسائل التواصل')

@push('styles')
<style>
    .contacts-page { --primary: #0071AA; }

    .hero-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #003d5c 100%);
        border-radius: 1.25rem;
        position: relative;
        overflow: hidden;
    }
    .hero-header::before {
        content: '';
        position: absolute; inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        animation: slidePattern 20s linear infinite;
    }
    @keyframes slidePattern { 0%{transform:translateX(0)} 100%{transform:translateX(-60px)} }

    .stat-card {
        border-radius: 1rem;
        transition: all 0.3s cubic-bezier(.4,0,.2,1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1.25rem 1rem;
        text-align: center;
    }
    .stat-card::after {
        content: '';
        position: absolute; bottom: 0; left: 0; right: 0;
        height: 3px;
        background: currentColor;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }
    .stat-card:hover::after { opacity: 0.4; }
    .stat-card.active-filter::after { opacity: 0.6; }

    .contact-row { transition: background 0.15s; }
    .contact-row:hover { background: #f8fafd !important; }
    .contact-row.is-new td:first-child { border-right: 3px solid #3b82f6; }

    .avatar-circle {
        width: 38px; height: 38px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.875rem;
        flex-shrink: 0;
    }

    .status-pill {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 999px;
        font-size: 0.72rem; font-weight: 600; white-space: nowrap;
    }
    .status-pill::before { content:''; width:6px; height:6px; border-radius:50%; background:currentColor; display:inline-block; }

    .action-btn {
        display: inline-flex; align-items: center; gap-4px;
        padding: 5px 12px; border-radius: 8px;
        font-size: 0.75rem; font-weight: 500;
        transition: all 0.2s; cursor: pointer; border: 1.5px solid;
    }
    .action-btn:hover { transform: translateY(-1px); }

    .empty-state { padding: 4rem 2rem; text-align: center; }
    .empty-state svg { width: 64px; height: 64px; margin: 0 auto 1rem; opacity: 0.25; }
</style>
@endpush

@section('content')
<div class="p-6 contacts-page">

    {{-- Hero Header --}}
    <div class="hero-header p-6 mb-6 text-white">
        <div class="relative z-10 flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-white bg-opacity-20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold">صندوق رسائل التواصل</h1>
                </div>
                <p class="text-blue-200 text-sm">مراجعة وإدارة الرسائل الواردة من زوار الموقع</p>
            </div>
            @if($counts['new'] > 0)
            <div class="bg-white bg-opacity-15 rounded-xl px-4 py-3 text-center">
                <div class="text-3xl font-bold">{{ $counts['new'] }}</div>
                <div class="text-blue-200 text-xs mt-0.5">رسالة جديدة تنتظر</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif

    @php
        $filters = [
            'all'      => ['label' => 'الكل'],
            'new'      => ['label' => 'جديد'],
            'read'     => ['label' => 'مقروء'],
            'replied'  => ['label' => 'تم الرد'],
            'archived' => ['label' => 'مؤرشف'],
        ];
        $active = request('status', 'all');
    @endphp
    {{-- Filter tabs --}}
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px">
        @foreach($filters as $key => $f)
        <a href="{{ route('admin.contacts.index', $key !== 'all' ? ['status' => $key] : []) }}"
           style="padding:6px 16px;border-radius:999px;font-size:0.82rem;font-weight:600;border:1.5px solid;text-decoration:none;transition:all 0.15s;
                  {{ $active === $key ? 'background:#0071AA;color:#fff;border-color:#0071AA' : 'background:#fff;color:#64748b;border-color:#e2e8f0' }}">
            {{ $f['label'] }}
            <span style="margin-right:4px;font-size:0.78rem;opacity:0.75">({{ $counts[$key] }})</span>
        </a>
        @endforeach
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Table header bar --}}
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18M3 14h18M3 18h18"/>
                </svg>
                <span class="text-sm font-semibold text-gray-700">
                    {{ $filters[$active]['label'] ?? 'الكل' }}
                    <span class="text-gray-400 font-normal">({{ $contacts->total() }})</span>
                </span>
            </div>
            @if($counts['new'] > 0)
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                {{ $counts['new'] }} جديدة
            </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-right">
                        <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">المرسل</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">التواصل</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">الموضوع</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">الحالة</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">التاريخ</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($contacts as $contact)
                    @php
                        $initials = mb_substr($contact->first_name, 0, 1) . mb_substr($contact->last_name, 0, 1);
                        $avatarColors = [
                            'new'      => 'background:#EFF6FF;color:#1d4ed8',
                            'read'     => 'background:#FFFBEB;color:#b45309',
                            'replied'  => 'background:#F0FDF4;color:#15803d',
                            'archived' => 'background:#F3F4F6;color:#6b7280',
                        ];
                        $avatarStyle = $avatarColors[$contact->status] ?? 'background:#F3F4F6;color:#374151';

                        $statusConfig = [
                            'new'      => ['label' => 'جديد',    'class' => 'bg-blue-100 text-blue-700'],
                            'read'     => ['label' => 'مقروء',   'class' => 'bg-amber-100 text-amber-700'],
                            'replied'  => ['label' => 'تم الرد', 'class' => 'bg-green-100 text-green-700'],
                            'archived' => ['label' => 'مؤرشف',   'class' => 'bg-gray-200 text-gray-600'],
                        ];
                        $sc = $statusConfig[$contact->status] ?? ['label' => $contact->status, 'class' => 'bg-gray-100 text-gray-600'];
                    @endphp
                    <tr class="contact-row {{ $contact->status === 'new' ? 'is-new' : '' }}"
                        style="{{ $contact->status === 'new' ? 'background:#fafcff' : '' }}">

                        {{-- Sender --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="avatar-circle" style="{{ $avatarStyle }}">{{ $initials }}</div>
                                <div>
                                    <div class="font-semibold text-gray-800 text-sm">
                                        {{ $contact->first_name }} {{ $contact->last_name }}
                                        @if($contact->status === 'new')
                                            <span class="mr-1 inline-block w-2 h-2 rounded-full bg-blue-500 align-middle"></span>
                                        @endif
                                    </div>
                                    @if($contact->category)
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $contact->category }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Contact info --}}
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-1.5 text-gray-600 text-xs mb-1">
                                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                {{ $contact->email }}
                            </div>
                            @if($contact->phone)
                            <div class="flex items-center gap-1.5 text-gray-500 text-xs">
                                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                {{ $contact->phone }}
                            </div>
                            @endif
                        </td>

                        {{-- Subject --}}
                        <td class="px-4 py-4">
                            <p class="text-gray-800 text-sm {{ $contact->status === 'new' ? 'font-semibold' : '' }}">
                                {{ Str::limit($contact->subject, 45) }}
                            </p>
                            <p class="text-gray-400 text-xs mt-0.5 line-clamp-1">{{ Str::limit($contact->message, 55) }}</p>
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-4">
                            <span class="status-pill {{ $sc['class'] }}">{{ $sc['label'] }}</span>
                        </td>

                        {{-- Date --}}
                        <td class="px-4 py-4">
                            <div class="text-gray-600 text-xs font-medium">{{ $contact->created_at->format('Y/m/d') }}</div>
                            <div class="text-gray-400 text-xs">{{ $contact->created_at->format('H:i') }}</div>
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.contacts.show', $contact) }}"
                                   class="action-btn border-blue-300 text-blue-600 hover:bg-blue-600 hover:text-white hover:border-blue-600">
                                    <svg class="w-3.5 h-3.5 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    فتح
                                </a>
                                <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="action-btn border-red-300 text-red-500 hover:bg-red-500 hover:text-white hover:border-red-500">
                                        <svg class="w-3.5 h-3.5 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="text-gray-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-gray-400 font-medium">لا توجد رسائل في هذه الفئة</p>
                                <p class="text-gray-300 text-sm mt-1">جرب تصفية مختلفة</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($contacts->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $contacts->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
