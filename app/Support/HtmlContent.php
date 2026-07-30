<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Sichere Ausgabe von Editor-Inhalten (HTML oder Legacy-Klartext).
 * Entfernt Scripts, Event-Handler und gefährliche URLs.
 */
class HtmlContent
{
    /** @var list<string> */
    private const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'strike', 'a',
        'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'blockquote', 'code', 'pre', 'hr', 'sub', 'sup',
        'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td',
        'img', 'span', 'div', 'figure', 'figcaption',
    ];

    /** @var array<string, list<string>> */
    private const ALLOWED_ATTRS = [
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height'],
        'td' => ['colspan', 'rowspan'],
        'th' => ['colspan', 'rowspan'],
        'span' => ['class'],
        'div' => ['class'],
        'p' => ['class'],
        'h1' => ['class'],
        'h2' => ['class'],
        'h3' => ['class'],
        'code' => ['class'],
        'pre' => ['class'],
    ];

    public static function toHtml(?string $content): HtmlString
    {
        $content = trim((string) $content);
        if ($content === '') {
            return new HtmlString('');
        }

        if (! preg_match('/<[a-z][\s\S]*>/i', $content)) {
            return new HtmlString('<div class="cms-content">'.nl2br(e($content), false).'</div>');
        }

        return new HtmlString('<div class="cms-content">'.self::sanitize($content).'</div>');
    }

    public static function sanitize(string $html): string
    {
        $html = preg_replace('#<(script|iframe|object|embed|form|link|meta|style|svg|math)[^>]*>.*?</\1>#is', '', $html) ?? '';
        $html = preg_replace('#<(script|iframe|object|embed|form|link|meta|style|svg|math)[^>]*/?>#is', '', $html) ?? '';

        $allowed = '<'.implode('><', self::ALLOWED_TAGS).'>';
        $html = strip_tags($html, $allowed);

        $html = preg_replace_callback(
            '/<([a-z0-9]+)(\s[^>]*)?>/i',
            function (array $m): string {
                $tag = strtolower($m[1]);
                $attrs = $m[2] ?? '';
                if ($attrs === '' || ! isset(self::ALLOWED_ATTRS[$tag])) {
                    return '<'.$tag.'>';
                }

                $clean = [];
                if (preg_match_all('/([a-z0-9:-]+)\s*=\s*(["\'])(.*?)\2/is', $attrs, $parts, PREG_SET_ORDER)) {
                    foreach ($parts as $part) {
                        $name = strtolower($part[1]);
                        $value = html_entity_decode($part[3], ENT_QUOTES | ENT_HTML5, 'UTF-8');

                        if (str_starts_with($name, 'on')) {
                            continue;
                        }
                        if (! in_array($name, self::ALLOWED_ATTRS[$tag], true)) {
                            continue;
                        }

                        if (in_array($name, ['href', 'src'], true) && ! self::isSafeUrl($value)) {
                            continue;
                        }

                        if ($name === 'target') {
                            $value = '_blank';
                            $clean['rel'] = 'noopener noreferrer';
                        }

                        if ($name === 'rel') {
                            $value = 'noopener noreferrer';
                        }

                        $clean[$name] = e($value);
                    }
                }

                if ($tag === 'a' && isset($clean['href']) && ! isset($clean['rel'])) {
                    $clean['rel'] = 'noopener noreferrer';
                }

                $attrHtml = '';
                foreach ($clean as $k => $v) {
                    $attrHtml .= ' '.$k.'="'.$v.'"';
                }

                return '<'.$tag.$attrHtml.'>';
            },
            $html
        ) ?? '';

        return $html;
    }

    /**
     * Sanitize optional rich text. Empty / whitespace-only → null.
     */
    public static function sanitizeOptional(?string $html): ?string
    {
        $html = trim((string) $html);
        if ($html === '') {
            return null;
        }

        $sanitized = self::sanitize($html);
        if (self::plainText($sanitized) === '' && ! preg_match('/<(img|hr)\b/i', $sanitized)) {
            return null;
        }

        return $sanitized;
    }

    /**
     * Sanitize required rich text; throws validation if empty after sanitize.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function sanitizeRequired(string $html, string $field, ?string $label = null): string
    {
        $sanitized = self::sanitize($html);
        if (self::plainText($sanitized) === '' && ! preg_match('/<(img|hr)\b/i', $sanitized)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                $field => __('validation.required', ['attribute' => $label ?? $field]),
            ]);
        }

        return $sanitized;
    }

    public static function plainText(?string $html): string
    {
        return trim(html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private static function isSafeUrl(string $url): bool
    {
        $url = trim($url);
        if ($url === '' || str_starts_with($url, '#')) {
            return true;
        }
        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return true;
        }

        $lower = strtolower($url);
        if (preg_match('#^(javascript|data|vbscript)\s*:#i', $lower)) {
            return false;
        }

        return (bool) preg_match('#^(https?:|mailto:|tel:)#i', $url);
    }
}
