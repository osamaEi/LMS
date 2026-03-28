<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\WhatsappConversation;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatService
{
    private string $apiKey;
    private string $model          = 'claude-sonnet-4-6';
    private string $apiUrl         = 'https://api.anthropic.com/v1/messages';
    private int    $maxTokens      = 600;
    private int    $contextMessages = 10;

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.key', '');
    }

    /**
     * Call Claude API and return the AI response text.
     *
     * @throws Exception on API failure (triggers job retry)
     */
    public function sendMessage(WhatsappConversation $conversation, string $newMessage): string
    {
        $response = Http::withHeaders([
            'x-api-key'         => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->post($this->apiUrl, [
            'model'      => $this->model,
            'max_tokens' => $this->maxTokens,
            'system'     => $this->buildSystemPrompt(),
            'messages'   => $this->buildMessages($conversation, $newMessage),
        ]);

        if (! $response->successful()) {
            Log::error('Claude API error', [
                'status'          => $response->status(),
                'body'            => $response->body(),
                'conversation_id' => $conversation->id,
            ]);
            throw new Exception('Claude API returned ' . $response->status() . ': ' . $response->body());
        }

        $text = $response->json('content.0.text', '');

        if (empty($text)) {
            throw new Exception('Claude API returned empty response');
        }

        return $text;
    }

    /**
     * Build the system prompt — identity + strict formatting rules only.
     * Per Anthropic best practices: role/persona goes here, knowledge base goes in first user turn.
     * Write in plain text (no markdown) so Claude mirrors the style.
     */
    private function buildSystemPrompt(): string
    {
        $name     = Setting::get('ai_name', '');
        $tone     = Setting::get('ai_tone', 'friendly');
        $language = Setting::get('ai_language', 'ar');

        $botName  = !empty($name) ? $name : 'المساعد الذكي';

        $toneMap = [
            'formal'   => 'تحدث بأسلوب رسمي ومهني.',
            'friendly' => 'تحدث بأسلوب ودي وبسيط ومريح.',
            'neutral'  => 'تحدث بأسلوب محايد واحترافي.',
        ];
        $toneInstruction = $toneMap[$tone] ?? $toneMap['friendly'];

        $langMap = [
            'ar'   => 'أجب دائما باللغة العربية فقط.',
            'en'   => 'Always respond in English only.',
            'both' => 'أجب بنفس لغة العميل.',
        ];
        $langInstruction = $langMap[$language] ?? $langMap['ar'];

        // Written in plain text on purpose — Claude mirrors the prompt style
        return "اسمك {$botName} وأنت مساعد دعم لمنصة تعليمية. {$toneInstruction} {$langInstruction}

قواعد الكتابة التي يجب الالتزام بها في كل رد دون استثناء:
اكتب ردودك كنص عادي مكوّن من جمل وفقرات متدفقة فقط.
لا تستخدم أي تنسيق مثل النص الغامق أو العناوين أو القوائم النقطية.
لا تضع نجمة أو شرطة أو رمز # أو أي رمز تنسيق آخر.
لا تستخدم أي رموز تعبيرية أو إيموجي في ردودك.
إذا احتجت لسرد عدة نقاط، اكتبها في جمل متتالية ضمن فقرة واحدة أو استخدم الأرقام فقط مثل: أولاً... ثانياً... ثالثاً.
ابدأ ردك مباشرة دون مقدمات مثل بالطبع أو بكل سرور أو سؤال رائع.";
    }

    /**
     * Build messages array in Claude API format.
     *
     * Per Anthropic best practices:
     * - Knowledge base, guardrails and examples go in the FIRST user turn (not system prompt)
     * - Claude handles bulk context better in user turns
     * - Then follow with the real conversation history
     */
    private function buildMessages(WhatsappConversation $conversation, string $newMessage): array
    {
        $messages = [];

        // ── First user turn: inject knowledge base + guardrails ──────────────────
        $context = $this->buildContextBlock();
        if (!empty($context)) {
            $messages[] = ['role' => 'user',      'content' => $context];
            $messages[] = ['role' => 'assistant', 'content' => 'فهمت جميع المعلومات والتعليمات. سأجيب على أسئلة العملاء بناء على هذه المعلومات فقط وبنص عادي بدون أي تنسيق.'];
        }

        // ── Conversation history (exclude the latest customer message to avoid duplication) ──
        $history = $conversation->messages()
            ->latest('id')
            ->limit($this->contextMessages)
            ->get()
            ->reverse()
            ->values()
            ->filter(fn($msg) => $msg->content !== $newMessage || !$msg->isFromCustomer());

        $lastRole = 'assistant';

        foreach ($history as $msg) {
            $role = $msg->isFromCustomer() ? 'user' : 'assistant';

            if ($role === $lastRole) {
                // Merge consecutive same-role messages into one
                if ($role === 'user') {
                    $last = array_pop($messages);
                    $messages[] = ['role' => 'user', 'content' => $last['content'] . "\n" . $msg->content];
                }
                continue;
            }

            $messages[] = ['role' => $role, 'content' => $msg->content];
            $lastRole   = $role;
        }

        // ── New customer message — always last and clearly marked ────────────────
        if ($lastRole === 'user') {
            // Append to existing user turn if last was also user
            $last = array_pop($messages);
            $messages[] = ['role' => 'user', 'content' => $last['content'] . "\n\nالسؤال الحالي: " . $newMessage];
        } else {
            $messages[] = ['role' => 'user', 'content' => $newMessage];
        }

        return $messages;
    }

    /**
     * Build the context block injected as the first user turn.
     * Contains: platform info, steps, FAQs, forbidden topics, and examples.
     */
    private function buildContextBlock(): string
    {
        $platformInfo = Setting::get('ai_platform_info', '');
        $steps        = json_decode(Setting::get('ai_steps', '[]'), true) ?: [];
        $faqs         = json_decode(Setting::get('ai_faqs', '[]'), true) ?: [];
        $forbidden    = json_decode(Setting::get('ai_forbidden', '[]'), true) ?: [];
        $forbidReply  = Setting::get('ai_forbidden_reply', 'عذراً، هذا الموضوع خارج نطاق مساعدتي. تواصل مع فريق الدعم مباشرة.');

        if (empty($platformInfo) && empty($steps) && empty($faqs) && empty($forbidden)) {
            return '';
        }

        $parts = [];

        if (!empty($platformInfo)) {
            $parts[] = "<context>\n{$platformInfo}\n</context>";
        }

        if (!empty($steps)) {
            $stepsText = implode("\n", array_map(fn($s, $i) => ($i + 1) . '. ' . $s, $steps, array_keys($steps)));
            $parts[] = "<steps>\nاتبع هذه الخطوات عند الرد على كل رسالة:\n{$stepsText}\n</steps>";
        }

        if (!empty($faqs)) {
            $faqLines = [];
            foreach ($faqs as $faq) {
                if (!empty($faq['q']) && !empty($faq['a'])) {
                    $faqLines[] = "س: {$faq['q']}\nج: {$faq['a']}";
                }
            }
            if (!empty($faqLines)) {
                $parts[] = "<faqs>\nإذا سأل العميل عن أي مما يلي، استخدم هذه الإجابات:\n" . implode("\n\n", $faqLines) . "\n</faqs>";
            }
        }

        if (!empty($forbidden)) {
            $forbiddenList = implode("\n", array_map(fn($f) => "- {$f}", $forbidden));
            $parts[] = "<forbidden>\nالموضوعات التالية ممنوعة تماماً، إذا سأل عنها العميل قل فقط: \"{$forbidReply}\"\n{$forbiddenList}\n</forbidden>";
        }

        $guardrails = "<guardrails>
أجب فقط بناء على المعلومات الواردة في هذه الرسالة.
إذا لم تجد الإجابة في المعلومات المتاحة، قل: عذراً، لا أملك معلومات كافية حول هذا الموضوع. يمكنك التواصل مع فريق الدعم مباشرة.
لا تخمّن أو تخترع معلومات غير موجودة في السياق.
</guardrails>";

        $parts[] = $guardrails;

        // Smart links — inject so AI knows to include them
        $smartLinks = json_decode(Setting::get('ai_smart_links', '[]'), true) ?: [];
        if (!empty($smartLinks)) {
            $linkLines = [];
            foreach ($smartLinks as $link) {
                if (!empty($link['topic']) && !empty($link['url'])) {
                    $label = !empty($link['label']) ? $link['label'] : $link['topic'];
                    $linkLines[] = "- إذا سأل عن: {$link['topic']} → أرسل هذا الرابط: {$link['url']} ({$label})";
                }
            }
            if (!empty($linkLines)) {
                $parts[] = "<smart_links>\nعند الإجابة على الموضوعات التالية، أضف الرابط المناسب في نهاية ردك:\n" . implode("\n", $linkLines) . "\n</smart_links>";
            }
        }

        return implode("\n\n", $parts);
    }
}
