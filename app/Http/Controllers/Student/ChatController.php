<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWhatsAppAiResponse;
use App\Models\WhatsappConversation;
use App\Models\WhatsappMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    /**
     * Show the student's active chat (or the start-chat page).
     */
    public function index(): View
    {
        $conversation = WhatsappConversation::where('user_id', auth()->id())
            ->where('channel', 'web')
            ->where('status', '!=', 'closed')
            ->latest()
            ->first();

        return view('student.chat.index', compact('conversation'));
    }

    /**
     * Start a new web conversation and send the first message.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        // If an open conversation already exists, redirect to it
        $existing = WhatsappConversation::where('user_id', auth()->id())
            ->where('channel', 'web')
            ->where('status', '!=', 'closed')
            ->latest()
            ->first();

        if ($existing) {
            return redirect()->route('student.chat.index');
        }

        $user = auth()->user();

        $conversation = WhatsappConversation::create([
            'channel'            => 'web',
            'user_id'            => $user->id,
            'customer_name'      => $user->name,
            'phone_number'       => $user->phone ?? '',
            'status'             => 'open',
            'last_message_at'    => now(),
            'unread_admin_count' => 1,
        ]);

        WhatsappMessage::create([
            'conversation_id' => $conversation->id,
            'direction'       => 'inbound',
            'sender_type'     => 'customer',
            'content'         => $validated['message'],
            'is_read'         => false,
        ]);

        ProcessWhatsAppAiResponse::dispatch($conversation, $validated['message']);

        return redirect()->route('student.chat.index');
    }

    /**
     * Send a message in an existing conversation (AJAX).
     */
    public function sendMessage(Request $request, WhatsappConversation $conversation): JsonResponse
    {
        abort_if($conversation->user_id !== auth()->id(), 403);

        $validated = $request->validate(['message' => 'required|string|max:2000']);

        WhatsappMessage::create([
            'conversation_id' => $conversation->id,
            'direction'       => 'inbound',
            'sender_type'     => 'customer',
            'content'         => $validated['message'],
            'is_read'         => false,
        ]);

        $conversation->update([
            'last_message_at'    => now(),
            'unread_admin_count' => $conversation->unread_admin_count + 1,
            'status'             => 'open',
        ]);

        ProcessWhatsAppAiResponse::dispatch($conversation, $validated['message']);

        return response()->json(['success' => true]);
    }

    /**
     * AJAX polling — return messages newer than after_id.
     */
    public function getMessages(Request $request, WhatsappConversation $conversation): JsonResponse
    {
        abort_if($conversation->user_id !== auth()->id(), 403);

        $afterId  = (int) $request->get('after_id', 0);
        $messages = $conversation->messages()
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get()
            ->map(fn ($m) => [
                'id'          => $m->id,
                'sender_type' => $m->sender_type,
                'content'     => $m->content,
                'created_at'  => $m->created_at->format('H:i'),
            ]);

        return response()->json([
            'messages'      => $messages,
            'ai_responding' => $conversation->fresh()->isAiResponding(),
        ]);
    }
}
