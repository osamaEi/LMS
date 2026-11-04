<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class SystemIssue extends Model
{
    use SoftDeletes;

    public const STATUSES = [
        'open'        => ['label' => 'مفتوحة',        'color' => '#2563eb', 'bg' => '#dbeafe'],
        'in_progress' => ['label' => 'قيد المعالجة',  'color' => '#d97706', 'bg' => '#fef3c7'],
        'waiting'     => ['label' => 'بانتظار رد',    'color' => '#7c3aed', 'bg' => '#ede9fe'],
        'resolved'    => ['label' => 'تم الحل',       'color' => '#16a34a', 'bg' => '#dcfce7'],
        'closed'      => ['label' => 'مغلقة',         'color' => '#64748b', 'bg' => '#f1f5f9'],
    ];

    public const PRIORITIES = [
        'low'    => ['label' => 'منخفضة', 'color' => '#64748b', 'bg' => '#f1f5f9'],
        'medium' => ['label' => 'متوسطة', 'color' => '#0891b2', 'bg' => '#cffafe'],
        'high'   => ['label' => 'مرتفعة', 'color' => '#ea580c', 'bg' => '#ffedd5'],
        'urgent' => ['label' => 'عاجلة',  'color' => '#dc2626', 'bg' => '#fee2e2'],
    ];

    protected $fillable = [
        'title',
        'description',
        'voice_path',
        'status',
        'priority',
        'created_by',
        'assigned_to',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(SystemIssueTag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(SystemIssueComment::class)->orderBy('created_at');
    }

    /** Files attached to the issue itself (not to a comment). */
    public function attachments(): HasMany
    {
        return $this->hasMany(SystemIssueAttachment::class)->whereNull('comment_id')->orderBy('id');
    }

    /** Every file of the issue, including those on comments. */
    public function allAttachments(): HasMany
    {
        return $this->hasMany(SystemIssueAttachment::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status]['label'] ?? $this->status;
    }

    public function statusStyle(): string
    {
        $s = self::STATUSES[$this->status] ?? self::STATUSES['closed'];
        return "background:{$s['bg']};color:{$s['color']}";
    }

    public function priorityLabel(): string
    {
        return self::PRIORITIES[$this->priority]['label'] ?? $this->priority;
    }

    public function priorityStyle(): string
    {
        $p = self::PRIORITIES[$this->priority] ?? self::PRIORITIES['low'];
        return "background:{$p['bg']};color:{$p['color']}";
    }

    public function voiceUrl(): ?string
    {
        return $this->voice_path ? Storage::disk('public')->url($this->voice_path) : null;
    }

    /** Safe HTML of the description (rich-text, or legacy plain text). */
    public function descriptionHtml(): string
    {
        return self::renderDescription($this->description);
    }

    // ── Rich-text description ─────────────────────────────────────────

    /** Tags the editor may produce, with the attributes each may keep. */
    private const RICH_TAGS = [
        'p' => [], 'br' => [], 'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [], 's' => [],
        'ol' => [], 'ul' => [], 'li' => [], 'h2' => [], 'h3' => [], 'blockquote' => [], 'pre' => [], 'code' => [],
        'span' => [], 'a' => ['href'],
    ];

    /** Elements dropped together with their content. */
    private const RICH_DROP = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select', 'svg', 'math', 'template', 'noscript', 'head', 'title', 'meta', 'link'];

    /** Clean editor HTML for storage; null when the editor is empty. */
    public static function sanitizeDescription(?string $html): ?string
    {
        $html = trim((string) $html);
        if ($html === '') {
            return null;
        }
        if ($html === strip_tags($html)) {
            return $html; // plain text — escaped on render
        }

        $doc = new \DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $root = $doc->getElementsByTagName('div')->item(0);
        if (! $root) {
            return null;
        }
        self::cleanNode($root);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        return trim(html_entity_decode(strip_tags($out), ENT_QUOTES | ENT_HTML5, 'UTF-8'), " \t\n\r\0\x0B\u{00A0}") === '' ? null : $out;
    }

    /** Safe HTML for display, whatever was stored. */
    public static function renderDescription(?string $value): string
    {
        $value = (string) $value;
        if ($value === strip_tags($value)) {
            return nl2br(e($value), false);
        }

        return (string) self::sanitizeDescription($value);
    }

    private static function cleanNode(\DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof \DOMText) {
                continue;
            }
            if (! $child instanceof \DOMElement) {
                $node->removeChild($child);
                continue;
            }

            $tag = strtolower($child->tagName);
            if (in_array($tag, self::RICH_DROP, true)) {
                $node->removeChild($child);
                continue;
            }
            if (! array_key_exists($tag, self::RICH_TAGS)) {
                // Unknown wrapper: keep its (cleaned) content, drop the tag.
                self::cleanNode($child);
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            foreach (iterator_to_array($child->attributes) as $attr) {
                $name = strtolower($attr->name);
                if ($name === 'class') {
                    $classes = array_filter(preg_split('/\s+/', $attr->value), fn ($c) => preg_match('/^ql-[a-z0-9-]+$/', $c));
                    $classes ? $child->setAttribute('class', implode(' ', $classes)) : $child->removeAttribute($attr->name);
                } elseif (in_array($name, self::RICH_TAGS[$tag], true)
                    && $name === 'href' && preg_match('~^(https?://|mailto:|/|#)~i', trim($attr->value))) {
                    continue;
                } else {
                    $child->removeAttribute($attr->name);
                }
            }
            if ($tag === 'a') {
                $child->setAttribute('target', '_blank');
                $child->setAttribute('rel', 'noopener noreferrer nofollow');
            }

            self::cleanNode($child);
        }
    }
}
