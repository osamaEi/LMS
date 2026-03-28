@extends('layouts.dashboard')

@section('title', 'المساعد الذكي')

@push('styles')
<style>
    .chat-wrap { max-width: 760px; margin: 0 auto; }

    .chat-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);
        border-radius: 20px;
        padding: 1.5rem 2rem;
        color: white;
        margin-bottom: 1.25rem;
        position: relative;
        overflow: hidden;
    }
    .chat-header::before {
        content: '';
        position: absolute;
        top: -30%; left: -5%;
        width: 280px; height: 280px;
        background: radial-gradient(circle, rgba(255,255,255,0.07) 0%, transparent 70%);
        border-radius: 50%;
    }

    /* Chat window */
    .chat-window {
        background: white;
        border-radius: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        overflow: hidden;
    }
    .messages-area {
        height: 420px;
        overflow-y: auto;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: .75rem;
        background: #f8fafc;
        scroll-behavior: smooth;
    }

    /* Bubbles */
    .bubble-me {
        align-self: flex-end;
        max-width: 74%;
        background: #0071AA;
        color: white;
        border-radius: 18px 4px 18px 18px;
        padding: .65rem 1rem;
    }
    .bubble-ai {
        align-self: flex-start;
        max-width: 74%;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 4px 18px 18px 18px;
        padding: .65rem 1rem;
    }
    .bubble-admin {
        align-self: flex-start;
        max-width: 74%;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 4px 18px 18px 18px;
        padding: .65rem 1rem;
    }
    .bubble-label {
        font-size: .65rem;
        font-weight: 700;
        opacity: .7;
        margin-bottom: 3px;
    }
    .bubble-time {
        font-size: .6rem;
        opacity: .5;
        margin-top: 3px;
        text-align: left;
    }

    /* Typing indicator */
    .typing-indicator {
        display: none;
        align-items: center;
        gap: .5rem;
        align-self: flex-start;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 4px 18px 18px 18px;
        padding: .65rem 1rem;
        font-size: .8rem;
        color: #64748b;
    }
    .dots span {
        display: inline-block;
        width: 6px; height: 6px;
        background: #94a3b8;
        border-radius: 50%;
        animation: blink 1.2s infinite;
    }
    .dots span:nth-child(2) { animation-delay: .2s; }
    .dots span:nth-child(3) { animation-delay: .4s; }
    @keyframes blink {
        0%, 80%, 100% { opacity: .2; transform: scale(.8); }
        40%            { opacity: 1;  transform: scale(1.1); }
    }

    /* Input area */
    .input-area {
        padding: 1rem 1.25rem;
        background: white;
        border-top: 1px solid #f1f5f9;
        display: flex;
        gap: .75rem;
        align-items: flex-end;
    }
    .msg-input {
        flex: 1;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: .7rem 1rem;
        font-size: .9rem;
        resize: none;
        outline: none;
        transition: border-color .15s;
        font-family: inherit;
        max-height: 120px;
    }
    .msg-input:focus { border-color: #0071AA; }
    .send-btn {
        width: 42px; height: 42px;
        background: #0071AA;
        color: white;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background .15s;
        flex-shrink: 0;
    }
    .send-btn:hover { background: #005a88; }
    .send-btn:disabled { background: #94a3b8; cursor: not-allowed; }

    /* Start chat */
    .start-box {
        background: white;
        border-radius: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        padding: 2rem;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6 chat-wrap">

    {{-- Header --}}
    <div class="chat-header">
        <div class="relative z-10 flex items-center gap-3">
            <div class="w-11 h-11 rounded-full bg-white bg-opacity-20 flex items-center justify-center text-xl">🤖</div>
            <div>
                <div class="font-bold text-lg">المساعد الذكي</div>
                <div class="text-sm opacity-75">
                    @if($conversation && $conversation->isAiResponding())
                        <span class="inline-flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-green-300 animate-pulse"></span>
                            يكتب...
                        </span>
                    @else
                        متاح الآن · يرد خلال ثوانٍ
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($conversation)
    {{-- ─── Active conversation ─────────────────────────────────────── --}}
    <div class="chat-window">
        <div class="messages-area" id="messagesArea">

            {{-- Welcome message --}}
            <div class="bubble-ai">
                <div class="bubble-label text-blue-600">🤖 المساعد الذكي</div>
                <div class="text-sm text-gray-700">مرحباً {{ auth()->user()->name }}! كيف أقدر أساعدك اليوم؟</div>
            </div>

            {{-- History --}}
            @foreach($conversation->messages as $msg)
                @if($msg->isFromCustomer())
                <div class="bubble-me">
                    <div class="text-sm leading-relaxed">{{ $msg->content }}</div>
                    <div class="bubble-time">{{ $msg->created_at->format('H:i') }}</div>
                </div>
                @elseif($msg->isFromAi())
                <div class="bubble-ai">
                    <div class="bubble-label text-blue-600">🤖 المساعد الذكي</div>
                    <div class="text-sm text-gray-700 leading-relaxed">{{ $msg->content }}</div>
                    <div class="bubble-time">{{ $msg->created_at->format('H:i') }}</div>
                </div>
                @else
                <div class="bubble-admin">
                    <div class="bubble-label text-orange-600">👤 فريق الدعم</div>
                    <div class="text-sm text-gray-700 leading-relaxed">{{ $msg->content }}</div>
                    <div class="bubble-time">{{ $msg->created_at->format('H:i') }}</div>
                </div>
                @endif
            @endforeach

            {{-- Typing indicator --}}
            <div class="typing-indicator" id="typingIndicator">
                <span>🤖 يكتب</span>
                <div class="dots"><span></span><span></span><span></span></div>
            </div>
        </div>

        {{-- Input --}}
        <div class="input-area">
            <textarea id="msgInput" class="msg-input" rows="1"
                      placeholder="اكتب رسالتك..." maxlength="2000"></textarea>
            <button id="sendBtn" class="send-btn" title="إرسال">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M22 2L11 13M22 2L15 22 11 13 2 9l20-7z"/>
                </svg>
            </button>
        </div>
    </div>

    @else
    {{-- ─── No conversation yet ─────────────────────────────────────── --}}
    <div class="start-box">
        <div class="text-5xl mb-4">💬</div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">ابدأ محادثة مع المساعد الذكي</h2>
        <p class="text-gray-500 text-sm mb-6">اسأل عن أي شيء — التسجيل، المواد، الدفع، أو أي مساعدة أخرى</p>

        <form action="{{ route('student.chat.store') }}" method="POST" class="max-w-lg mx-auto">
            @csrf
            <textarea name="message" rows="4"
                      class="w-full border-2 border-gray-200 focus:border-blue-400 rounded-2xl px-4 py-3 text-sm resize-none outline-none transition mb-4 font-inherit"
                      placeholder="مثال: كيف أسجّل في برنامج الدبلوم؟"
                      required maxlength="2000"></textarea>
            @error('message')
            <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
            @enderror
            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-2xl font-semibold text-sm transition">
                ابدأ المحادثة 🚀
            </button>
        </form>
    </div>
    @endif

</div>
@endsection

@if($conversation)
@push('scripts')
<script>
    const messagesArea    = document.getElementById('messagesArea');
    const typingIndicator = document.getElementById('typingIndicator');
    const msgInput        = document.getElementById('msgInput');
    const sendBtn         = document.getElementById('sendBtn');
    const csrfToken       = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    const sendUrl  = '{{ route("student.chat.send", $conversation) }}';
    const pollUrl  = '{{ route("student.chat.messages", $conversation) }}';
    let   lastId   = {{ $conversation->messages->last()?->id ?? 0 }};

    // Scroll to bottom
    messagesArea.scrollTop = messagesArea.scrollHeight;

    // Auto-grow textarea
    msgInput.addEventListener('input', () => {
        msgInput.style.height = 'auto';
        msgInput.style.height = Math.min(msgInput.scrollHeight, 120) + 'px';
    });

    // Send on Enter (Shift+Enter = newline)
    msgInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    sendBtn.addEventListener('click', sendMessage);

    async function sendMessage() {
        const text = msgInput.value.trim();
        if (!text) return;

        // Optimistic UI: show bubble immediately
        appendBubble({ sender_type: 'customer', content: text, created_at: now() });
        msgInput.value = '';
        msgInput.style.height = 'auto';
        sendBtn.disabled = true;
        typingIndicator.style.display = 'flex';
        messagesArea.scrollTop = messagesArea.scrollHeight;

        try {
            await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ message: text }),
            });
        } catch (e) {
            // ignore
        }

        sendBtn.disabled = false;
    }

    function appendBubble(msg) {
        let el;
        if (msg.sender_type === 'customer') {
            el = `<div class="bubble-me">
                    <div class="text-sm leading-relaxed">${escapeHtml(msg.content)}</div>
                    <div class="bubble-time">${msg.created_at}</div>
                  </div>`;
        } else if (msg.sender_type === 'ai') {
            el = `<div class="bubble-ai">
                    <div class="bubble-label text-blue-600">🤖 المساعد الذكي</div>
                    <div class="text-sm text-gray-700 leading-relaxed">${escapeHtml(msg.content)}</div>
                    <div class="bubble-time">${msg.created_at}</div>
                  </div>`;
        } else {
            el = `<div class="bubble-admin">
                    <div class="bubble-label text-orange-600">👤 فريق الدعم</div>
                    <div class="text-sm text-gray-700 leading-relaxed">${escapeHtml(msg.content)}</div>
                    <div class="bubble-time">${msg.created_at}</div>
                  </div>`;
        }
        typingIndicator.insertAdjacentHTML('beforebegin', el);
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }

    function escapeHtml(t) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(t));
        return d.innerHTML;
    }

    function now() {
        return new Date().toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' });
    }

    // Poll every 3 seconds for AI/admin replies
    setInterval(async () => {
        try {
            const res  = await fetch(`${pollUrl}?after_id=${lastId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();

            data.messages.forEach(msg => {
                // Skip echoing back the customer's own messages (already shown optimistically)
                if (msg.sender_type !== 'customer') {
                    lastId = msg.id;
                    appendBubble(msg);
                } else {
                    lastId = msg.id;
                }
            });

            typingIndicator.style.display = data.ai_responding ? 'flex' : 'none';
        } catch (e) {
            // ignore
        }
    }, 3000);
</script>
@endpush
@endif
