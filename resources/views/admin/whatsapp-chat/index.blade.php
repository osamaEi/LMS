@extends('layouts.dashboard')

@section('title', 'المحادثات الذكية')

@push('styles')
<style>
    .chat-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
        border-radius: 20px;
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
        color: white;
    }
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.1rem 1.25rem;
        box-shadow: 0 1px 6px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: .85rem;
    }
    .stat-icon {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; flex-shrink: 0;
    }
    .tab-btn {
        padding: .5rem 1.25rem;
        border-radius: 10px;
        font-size: .875rem;
        font-weight: 600;
        transition: all .15s;
        color: #64748b;
    }
    .tab-btn.active {
        background: white;
        color: #1e293b;
        box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    }
    .conv-row:hover { background: #f8fafc; cursor: pointer; }
    .pulse-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #8b5cf6; display: inline-block;
        animation: pulse 1.4s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: .4; transform: scale(1.4); }
    }
    .channel-badge-web { background:#dbeafe; color:#1d4ed8; }
    .channel-badge-whatsapp { background:#dcfce7; color:#15803d; }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6">

    {{-- Header --}}
    <div class="chat-header">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-xl font-bold mb-0.5">المحادثات الذكية 💬</h1>
                <p class="text-sm opacity-70">كل محادثات العملاء — موقع وواتساب — في مكان واحد</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.whatsapp-chat.settings') }}"
                   class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition flex items-center gap-1.5">
                    🤖 تدريب الـ AI
                </a>
                <a href="{{ route('admin.whatsapp-chat.index') }}"
                   class="bg-white bg-opacity-10 hover:bg-opacity-20 text-white px-4 py-2 rounded-xl text-sm font-medium transition">
                    تحديث
                </a>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f1f5f9;">💬</div>
            <div>
                <div class="text-xl font-bold text-gray-800">{{ $stats['total'] }}</div>
                <div class="text-xs text-gray-400">الإجمالي</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#dbeafe;">🌐</div>
            <div>
                <div class="text-xl font-bold text-blue-600">{{ $stats['web'] }}</div>
                <div class="text-xs text-gray-400">موقع</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#dcfce7;">📱</div>
            <div>
                <div class="text-xl font-bold text-green-600">{{ $stats['whatsapp'] }}</div>
                <div class="text-xs text-gray-400">واتساب</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#ede9fe;">🤖</div>
            <div>
                <div class="text-xl font-bold text-purple-600">{{ $stats['ai_responding'] }}</div>
                <div class="text-xs text-gray-400">AI يرد الآن</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fee2e2;">🔴</div>
            <div>
                <div class="text-xl font-bold text-red-500">{{ $stats['unread'] }}</div>
                <div class="text-xs text-gray-400">غير مقروءة</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef9c3;">⚡</div>
            <div>
                <div class="text-xl font-bold text-yellow-600">{{ $stats['avg_response_time'] }}</div>
                <div class="text-xs text-gray-400">متوسط الرد (د)</div>
            </div>
        </div>
    </div>

    {{-- Table card --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

        {{-- Tabs + Filter --}}
        <div class="p-4 border-b bg-gray-50 flex flex-wrap gap-3 items-center">
            {{-- Channel tabs --}}
            <div class="flex gap-1 bg-gray-100 p-1 rounded-xl">
                <a href="{{ route('admin.whatsapp-chat.index', array_merge(request()->query(), ['channel' => 'all'])) }}"
                   class="tab-btn {{ request('channel', 'all') === 'all' ? 'active' : '' }}">الكل</a>
                <a href="{{ route('admin.whatsapp-chat.index', array_merge(request()->query(), ['channel' => 'web'])) }}"
                   class="tab-btn {{ request('channel') === 'web' ? 'active' : '' }}">🌐 الموقع</a>
                <a href="{{ route('admin.whatsapp-chat.index', array_merge(request()->query(), ['channel' => 'whatsapp'])) }}"
                   class="tab-btn {{ request('channel') === 'whatsapp' ? 'active' : '' }}">📱 واتساب</a>
            </div>

            {{-- Status filter --}}
            <form method="GET" class="flex gap-2 flex-wrap flex-1">
                @if(request('channel'))
                    <input type="hidden" name="channel" value="{{ request('channel') }}">
                @endif
                <select name="status" onchange="this.form.submit()"
                        class="border border-gray-200 rounded-xl px-3 py-1.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="all"          {{ request('status', 'all') === 'all'          ? 'selected' : '' }}>جميع الحالات</option>
                    <option value="open"          {{ request('status') === 'open'          ? 'selected' : '' }}>مفتوحة</option>
                    <option value="ai_responding" {{ request('status') === 'ai_responding' ? 'selected' : '' }}>AI يرد</option>
                    <option value="closed"        {{ request('status') === 'closed'        ? 'selected' : '' }}>مغلقة</option>
                </select>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="بحث بالاسم..."
                       class="border border-gray-200 rounded-xl px-3 py-1.5 text-sm flex-1 min-w-[160px] focus:outline-none focus:ring-2 focus:ring-blue-300">
                <button type="submit" class="bg-gray-800 text-white px-4 py-1.5 rounded-xl text-sm font-medium transition hover:bg-gray-700">بحث</button>
                @if(request('search') || (request('status', 'all') !== 'all'))
                <a href="{{ route('admin.whatsapp-chat.index', request('channel') ? ['channel' => request('channel')] : []) }}"
                   class="border border-gray-200 text-gray-500 hover:bg-gray-50 px-3 py-1.5 rounded-xl text-sm transition">مسح</a>
                @endif
            </form>
        </div>

        {{-- Conversations table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-gray-400 text-xs uppercase tracking-wider border-b">
                        <th class="px-4 py-3 text-right">العميل</th>
                        <th class="px-4 py-3 text-right">القناة</th>
                        <th class="px-4 py-3 text-right">آخر رسالة</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-center">غير مقروء</th>
                        <th class="px-4 py-3 text-right">آخر نشاط</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($conversations as $conv)
                    <tr class="conv-row transition"
                        onclick="window.location='{{ route('admin.whatsapp-chat.show', $conv) }}'">

                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-sm flex items-center justify-center flex-shrink-0">
                                    {{ mb_substr($conv->display_name, 0, 1) }}
                                </div>
                                <span class="font-medium text-gray-800">{{ $conv->display_name }}</span>
                                @if($conv->unread_admin_count > 0)
                                    <span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
                                @endif
                            </div>
                        </td>

                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-lg text-xs font-semibold
                                {{ $conv->channel === 'whatsapp' ? 'channel-badge-whatsapp' : 'channel-badge-web' }}">
                                {{ $conv->channel_label }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-gray-500">
                            @if($latest = $conv->latestMessage->first())
                                <span class="truncate block max-w-[220px]">{{ Str::limit($latest->content, 55) }}</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-lg text-xs font-semibold {{ $conv->status_color_class }}">
                                @if($conv->isAiResponding())<span class="pulse-dot mr-1"></span>@endif
                                {{ $conv->status_label }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-center">
                            @if($conv->unread_admin_count > 0)
                                <span class="inline-flex items-center justify-center w-5 h-5 bg-red-500 text-white rounded-full text-xs font-bold">{{ $conv->unread_admin_count }}</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-gray-400 text-xs">
                            {{ $conv->last_message_at?->diffForHumans() ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-14 text-center text-gray-400">
                            <div class="text-4xl mb-2">💬</div>
                            <div class="text-sm">لا توجد محادثات بعد</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($conversations->hasPages())
        <div class="p-4 border-t">{{ $conversations->links() }}</div>
        @endif
    </div>

</div>
@endsection
