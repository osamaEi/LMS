<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\WhatsappConversation;
use App\Models\WhatsappMessage;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class WhatsAppChatController extends Controller
{
    public function __construct(private WhatsAppService $whatsAppService)
    {
    }

    /**
     * List all conversations with channel tabs and stats.
     */
    public function index(Request $request): View
    {
        $query = WhatsappConversation::with(['latestMessage', 'user'])
            ->withCount('messages');

        if ($request->filled('channel') && $request->channel !== 'all') {
            $query->where('channel', $request->channel);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $conversations = $query->orderByDesc('last_message_at')->paginate(20)->withQueryString();

        $stats = [
            'total'             => WhatsappConversation::count(),
            'web'               => WhatsappConversation::where('channel', 'web')->count(),
            'whatsapp'          => WhatsappConversation::where('channel', 'whatsapp')->count(),
            'ai_responding'     => WhatsappConversation::where('status', 'ai_responding')->count(),
            'unread'            => WhatsappConversation::where('unread_admin_count', '>', 0)->count(),
            'avg_response_time' => $this->calcAvgResponseTime(),
        ];

        return view('admin.whatsapp-chat.index', compact('conversations', 'stats'));
    }

    /**
     * AI training / prompt settings page.
     */
    public function settings(): View
    {
        $settings = [
            'ai_platform_info'    => Setting::get('ai_platform_info', ''),
            'ai_name'             => Setting::get('ai_name', ''),
            'ai_tone'             => Setting::get('ai_tone', 'friendly'),
            'ai_language'         => Setting::get('ai_language', 'ar'),
            'ai_steps'            => json_decode(Setting::get('ai_steps', '[]'), true) ?: [],
            'ai_faqs'             => json_decode(Setting::get('ai_faqs', '[]'), true) ?: [],
            'ai_forbidden'        => json_decode(Setting::get('ai_forbidden', '[]'), true) ?: [],
            'ai_forbidden_reply'  => Setting::get('ai_forbidden_reply', 'عذراً، لا أستطيع المساعدة في هذا الموضوع. يمكنك التواصل مع فريق الدعم.'),
            'ai_smart_links'      => json_decode(Setting::get('ai_smart_links', '[]'), true) ?: [],
        ];
        return view('admin.whatsapp-chat.settings', compact('settings'));
    }

    /**
     * Show a single conversation with all messages.
     */
    public function show(WhatsappConversation $whatsappChat): View
    {
        $whatsappChat->load('messages');

        // Mark all messages as read for admin
        $whatsappChat->update(['unread_admin_count' => 0]);

        return view('admin.whatsapp-chat.show', ['conversation' => $whatsappChat]);
    }

    /**
     * Admin sends a manual reply via WhatsApp.
     */
    public function reply(Request $request, WhatsappConversation $whatsappChat): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:4096',
        ]);

        try {
            // Send via WhatsApp API
            $this->whatsAppService->sendTextMessage($whatsappChat->wa_id, $validated['message']);

            // Save to database
            WhatsappMessage::create([
                'conversation_id' => $whatsappChat->id,
                'direction'       => 'outbound',
                'sender_type'     => 'admin',
                'content'         => $validated['message'],
                'is_read'         => true,
            ]);

            $whatsappChat->update([
                'last_message_at' => now(),
                'status'          => 'open',
            ]);

            return back()->with('success', 'تم إرسال ردك عبر واتساب بنجاح');

        } catch (\Exception $e) {
            Log::error('Admin WhatsApp reply failed', [
                'conversation_id' => $whatsappChat->id,
                'error'           => $e->getMessage(),
            ]);

            return back()->with('error', 'فشل إرسال الرسالة: ' . $e->getMessage());
        }
    }

    /**
     * Save the structured AI settings.
     */
    public function savePrompt(Request $request): RedirectResponse
    {
        $request->validate([
            'ai_platform_info'    => 'nullable|string|max:5000',
            'ai_smart_links'      => 'nullable|array',
            'ai_smart_links.*.topic' => 'nullable|string|max:200',
            'ai_smart_links.*.url'   => 'nullable|string|max:500',
            'ai_smart_links.*.label' => 'nullable|string|max:200',
            'ai_name'            => 'nullable|string|max:100',
            'ai_tone'            => 'required|in:formal,friendly,neutral',
            'ai_language'        => 'required|in:ar,en,both',
            'ai_steps'           => 'nullable|array',
            'ai_steps.*'         => 'nullable|string|max:500',
            'ai_faqs'            => 'nullable|array',
            'ai_faqs.*.q'        => 'nullable|string|max:500',
            'ai_faqs.*.a'        => 'nullable|string|max:1000',
            'ai_forbidden'       => 'nullable|array',
            'ai_forbidden.*'     => 'nullable|string|max:200',
            'ai_forbidden_reply' => 'nullable|string|max:500',
        ]);

        Setting::set('ai_platform_info', $request->input('ai_platform_info', ''));

        $smartLinks = array_values(array_filter($request->input('ai_smart_links', []), fn($l) => !empty(trim($l['topic'] ?? '')) && !empty(trim($l['url'] ?? ''))));
        Setting::set('ai_smart_links', json_encode($smartLinks, JSON_UNESCAPED_UNICODE));
        Setting::set('ai_name',            $request->input('ai_name', ''));
        Setting::set('ai_tone',            $request->input('ai_tone', 'friendly'));
        Setting::set('ai_language',        $request->input('ai_language', 'ar'));

        // Filter empty entries before saving
        $steps = array_values(array_filter($request->input('ai_steps', []), fn($s) => !empty(trim($s ?? ''))));
        Setting::set('ai_steps', json_encode($steps, JSON_UNESCAPED_UNICODE));

        $faqs = array_values(array_filter($request->input('ai_faqs', []), fn($f) => !empty(trim($f['q'] ?? '')) && !empty(trim($f['a'] ?? ''))));
        Setting::set('ai_faqs', json_encode($faqs, JSON_UNESCAPED_UNICODE));

        $forbidden = array_values(array_filter($request->input('ai_forbidden', []), fn($f) => !empty(trim($f ?? ''))));
        Setting::set('ai_forbidden', json_encode($forbidden, JSON_UNESCAPED_UNICODE));

        Setting::set('ai_forbidden_reply', $request->input('ai_forbidden_reply', ''));

        return back()->with('success', 'تم حفظ إعدادات الذكاء الاصطناعي بنجاح ✓');
    }

    /**
     * Close a conversation.
     */
    public function close(WhatsappConversation $whatsappChat): RedirectResponse
    {
        $whatsappChat->update(['status' => 'closed']);

        return back()->with('success', 'تم إغلاق المحادثة');
    }

    /**
     * AJAX polling endpoint — returns messages newer than after_id.
     */
    public function getNewMessages(Request $request, WhatsappConversation $whatsappChat): JsonResponse
    {
        $afterId = (int) $request->get('after_id', 0);

        $messages = $whatsappChat->messages()
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get()
            ->map(fn ($m) => [
                'id'          => $m->id,
                'sender_type' => $m->sender_type,
                'content'     => $m->content,
                'direction'   => $m->direction,
                'created_at'  => $m->created_at->format('H:i'),
                'created_at_human' => $m->created_at->diffForHumans(),
            ]);

        return response()->json([
            'messages'      => $messages,
            'ai_responding' => $whatsappChat->fresh()->isAiResponding(),
        ]);
    }

    /**
     * Calculate average AI response time in minutes across all conversations.
     */
    private function calcAvgResponseTime(): float
    {
        // Find pairs of inbound→outbound messages and average the gap
        $inbound = WhatsappMessage::where('direction', 'inbound')
            ->whereNotNull('created_at')
            ->orderBy('conversation_id')
            ->orderBy('id')
            ->get()
            ->groupBy('conversation_id');

        $totalMinutes = 0;
        $count = 0;

        foreach ($inbound as $convId => $customerMessages) {
            foreach ($customerMessages as $customerMsg) {
                $reply = WhatsappMessage::where('conversation_id', $convId)
                    ->where('direction', 'outbound')
                    ->where('id', '>', $customerMsg->id)
                    ->orderBy('id')
                    ->first();

                if ($reply) {
                    $totalMinutes += $customerMsg->created_at->diffInMinutes($reply->created_at);
                    $count++;
                }
            }
        }

        return $count > 0 ? round($totalMinutes / $count, 1) : 0;
    }
}
