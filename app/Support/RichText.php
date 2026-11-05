<?php

namespace App\Support;

/**
 * Sanitizing / rendering of rich-text (Quill) HTML stored in description fields.
 * Legacy plain-text values are still supported: they are escaped on render.
 */
class RichText
{
    /** Tags the editor may produce, with the attributes each may keep. */
    private const TAGS = [
        'p' => [], 'br' => [], 'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [], 's' => [],
        'ol' => [], 'ul' => [], 'li' => [], 'h2' => [], 'h3' => [], 'blockquote' => [], 'pre' => [], 'code' => [],
        'span' => [], 'a' => ['href'],
    ];

    /** Elements dropped together with their content. */
    private const DROP = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select', 'svg', 'math', 'template', 'noscript', 'head', 'title', 'meta', 'link'];

    /** Clean editor HTML for storage; null when the editor is empty. */
    public static function sanitize(?string $html): ?string
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

        return self::plain($out) === '' ? null : $out;
    }

    /** Safe HTML for display, whatever was stored. */
    public static function render(?string $value): string
    {
        $value = (string) $value;
        if ($value === strip_tags($value)) {
            return nl2br(e($value), false);
        }

        return (string) self::sanitize($value);
    }

    /** Plain text of a stored value (for cards / excerpts / meta tags). */
    public static function plain(?string $value): string
    {
        $value = preg_replace('~<(br|/p|/li|/h[1-6]|/blockquote|/pre)\b[^>]*>~i', '$0 ', (string) $value);
        $text = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/[\s\x{00A0}]+/u', ' ', $text));
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
            if (in_array($tag, self::DROP, true)) {
                $node->removeChild($child);
                continue;
            }
            if (! array_key_exists($tag, self::TAGS)) {
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
                } elseif (in_array($name, self::TAGS[$tag], true)
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
