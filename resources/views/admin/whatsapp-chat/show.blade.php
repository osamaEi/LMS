@extends('layouts.dashboard')

@section('title', 'محادثة: ' . $conversation->display_name)

@push('styles')
<style>
    .chat-header {
        background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
        border-radius: 20px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        color: white;
    }
    .chat-container {
        height: 480px;
        overflow-y: auto;
        padding: 1rem 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        scroll-behavior: smooth;
    }
    /* Customer bubble — right side (RTL) */
    .bubble-customer {
        align-self: flex-end;
        max-width: 72%;
        background: #dcf8c6;
        border-radius: 16px 4px 16px 16px;
        padding: .65rem 1rem;
        position: relative;
    }
    /* AI bubble — left side */
    .bubble-ai {
        align-self: flex-start;
        max-width: 72%;
        background: #fff;
        border: 1px solid #ede9fe;
        border-radius: 4px 16px 16px 16px;
        padding: .65rem 1rem;
    }
    /* Admin bubble — left side */
    .bubble-admin {
        align-self: flex-start;
        max-width: 72%;
        background: #dbeafe;
        border-radius: 4px 16px 16px 16px;
        padding: .65rem 1rem;
    }
    .bubble-label {
        font-size: 0.65rem;
        font-weight: 700;
        margin-bottom: 3px;
        opacity: .7;
    }
    .bubble-time {
        font-size: 0.62rem;
        opacity: .55;
        text-align: left;
        margin-top: 3px;
    }
    .ai-typing {
        display: none;
        align-items: center;
        gap: .5rem;
        padding: .5rem 1rem;
        background: #ede9fe;
        border-radius: 12px;
        width: fit-content;
        font-size: .8rem;
        color: #7c3aed;
    }
    .typing-dots span {
        display: inline-block;
        width: 7px; height: 7px;
        background: #8b5cf6;
        border-radius: 50%;
        animation: blink 1.2s infinite;
    }
    .typing-dots span:nth-child(2) { animation-delay: .2s; }
    .typing-dots span:nth-child(3) { animation-delay: .4s; }
    @keyframes blink {
        0%, 80%, 100% { opacity: .2; transform: scale(.8); }
        40%            { opacity: 1;  transform: scale(1.1); }
    }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="chat-header flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.whatsapp-chat.index') }}"
               class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white w-9 h-9 flex items-center justify-center rounded-xl transition">
                ←
            </a>
            <div class="w-10 h-10 rounded-full bg-white bg-opacity-20 flex items-center justify-center font-bold text-lg">
                {{ mb_substr($conversation->display_name, 0, 1) }}
            </div>
            <div>
                <div class="font-bold text-lg">{{ $conversation->display_name }}</div>
                <div class="text-sm opacity-80">{{ $conversation->phone_number }}</div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-xl text-xs font-semibold {{ $conversation->status_color_class }}">
                {{ $conversation->status_label }}
            </span>
            @if($conversation->isOpen())
            <form action="{{ route('admin.whatsapp-chat.close', $conversation) }}" method="POST">
                @csrf
                <button type="submit"
                        class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-3 py-1 rounded-xl text-xs transition"
                        onclick="return confirm('إغلاق هذه المحادثة؟')">
                    إغلاق
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
        ✓ {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
        ✗ {{ session('error') }}
    </div>
    @endif

    {{-- Chat window --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-4">

        {{-- Messages --}}
        <div class="chat-container" id="chatContainer">
            @forelse($conversation->messages as $msg)
                @if($msg->isFromCustomer())
                <div class="bubble-customer">
                    <div class="bubble-label text-green-700">{{ $conversation->display_name }}</div>
                    <div class="text-gray-800 text-sm leading-relaxed">{{ $msg->content }}</div>
                    <div class="bubble-time">{{ $msg->created_at->format('H:i') }}</div>
                </div>
                @elseif($msg->isFromAi())
                <div class="bubble-ai">
                    <div class="bubble-label text-purple-600">🤖 ردّ تلقائي (AI)</div>
                    <div class="text-gray-800 text-sm leading-relaxed">{{ $msg->content }}</div>
                    <div class="bubble-time">{{ $msg->created_at->format('H:i') }}</div>
                </div>
                @else
                <div class="bubble-admin">
                    <div class="bubble-label text-blue-600">👤 فريق الدعم</div>
                    <div class="text-gray-800 text-sm leading-relaxed">{{ $msg->content }}</div>
                    <div class="bubble-time">{{ $msg->created_at->format('H:i') }}</div>
                </div>
                @endif
            @empty
            <div class="text-center text-gray-400 py-8 text-sm">لا توجد رسائل بعد</div>
            @endforelse

            {{-- AI typing indicator --}}
            <div class="ai-typing" id="aiTyping">
                <span>🤖 المساعد الذكي يكتب</span>
                <div class="typing-dots">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>

        {{-- Reply form --}}
        @if($conversation->isOpen() || $conversation->isAiResponding())
        <div class="border-t p-4">
            <form action="{{ route('admin.whatsapp-chat.reply', $conversation) }}" method="POST">
                @csrf
                <div class="flex gap-3 items-end">
                    <textarea name="message" rows="3"
                              placeholder="اكتب ردك هنا... (سيتم إرساله مباشرةً عبر واتساب)"
                              class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-400"
                              required maxlength="4096"></textarea>
                    <button type="submit"
                            class="bg-green-500 hover:bg-green-600 text-white px-5 py-3 rounded-xl font-medium text-sm transition flex items-center gap-2 h-fit">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 2L11 13M22 2L15 22 11 13 2 9l20-7z"/>
                        </svg>
                        إرسال
                    </button>
                </div>
            </form>
        </div>
        @else
        <div class="border-t p-4 text-center text-sm text-gray-400 bg-gray-50">
            هذه المحادثة مغلقة
        </div>
        @endif
    </div>

    {{-- Conversation info --}}
    <div class="bg-white rounded-2xl shadow-sm p-4 text-sm text-gray-600 grid grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <div class="text-xs text-gray-400 mb-1">عدد الرسائل</div>
            <div class="font-semibold">{{ $conversation->messages->count() }}</div>
        </div>
        <div>
            <div class="text-xs text-gray-400 mb-1">بدأت في</div>
            <div class="font-semibold">{{ $conversation->created_at->format('Y/m/d H:i') }}</div>
        </div>
        <div>
            <div class="text-xs text-gray-400 mb-1">آخر رسالة</div>
            <div class="font-semibold">{{ $conversation->last_message_at?->diffForHumans() ?? '—' }}</div>
        </div>
        <div>
            <div class="text-xs text-gray-400 mb-1">حالة المحادثة</div>
            <span class="px-2 py-0.5 rounded-lg text-xs font-semibold {{ $conversation->status_color_class }}">
                {{ $conversation->status_label }}
            </span>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const chatContainer = document.getElementById('chatContainer');
    const aiTyping      = document.getElementById('aiTyping');
    const pollUrl       = '{{ route("admin.whatsapp-chat.messages", $conversation) }}';
    const csrfToken     = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    let lastId = {{ $conversation->messages->last()?->id ?? 0 }};

    // Scroll to bottom on load
    chatContainer.scrollTop = chatContainer.scrollHeight;

    function appendBubble(msg) {
        let cls, label;
        if (msg.sender_type === 'customer') {
            cls   = 'bubble-customer';
            label = '<div class="bubble-label text-green-700">{{ $conversation->display_name }}</div>';
        } else if (msg.sender_type === 'ai') {
            cls   = 'bubble-ai';
            label = '<div class="bubble-label text-purple-600">🤖 ردّ تلقائي (AI)</div>';
        } else {
            cls   = 'bubble-admin';
            label = '<div class="bubble-label text-blue-600">👤 فريق الدعم</div>';
        }

        const div = document.createElement('div');
        div.className = cls;
        div.innerHTML = `
            ${label}
            <div class="text-gray-800 text-sm leading-relaxed">${escapeHtml(msg.content)}</div>
            <div class="bubble-time">${msg.created_at}</div>
        `;
        chatContainer.insertBefore(div, aiTyping);
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(text));
        return d.innerHTML;
    }

    // Poll for new messages every 3 seconds
    setInterval(async () => {
        try {
            const res  = await fetch(`${pollUrl}?after_id=${lastId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await res.json();

            data.messages.forEach(msg => {
                lastId = msg.id;
                appendBubble(msg);
            });

            aiTyping.style.display = data.ai_responding ? 'flex' : 'none';

        } catch (e) {
            // silently ignore network errors
        }
    }, 3000);
</script>
@endpush
