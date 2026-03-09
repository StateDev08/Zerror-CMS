<?php

namespace App\Support;

class ForumFormat
{
    /**
     * Convert [quote author="..."]...[/quote] to <blockquote> and escape the rest.
     */
    public static function quoteToHtml(string $body): string
    {
        $out = '';
        $rest = $body;
        while (preg_match('/\[quote\s+author="((?:[^"]|&quot;)*)"\s*\]/s', $rest, $m, PREG_OFFSET_CAPTURE)) {
            $start = $m[0][1];
            $authorRaw = str_replace('&quot;', '"', $m[1][0]);
            $author = htmlspecialchars($authorRaw, ENT_QUOTES, 'UTF-8');
            $afterOpen = substr($rest, $start + strlen($m[0][0]));
            $close = '[/quote]';
            $endPos = stripos($afterOpen, $close);
            if ($endPos === false) {
                $out .= htmlspecialchars(substr($rest, 0, $start + strlen($m[0][0])), ENT_QUOTES, 'UTF-8');
                $rest = $afterOpen;
                continue;
            }
            $quoteBody = substr($afterOpen, 0, $endPos);
            $out .= htmlspecialchars(substr($rest, 0, $start), ENT_QUOTES, 'UTF-8');
            $out .= '<blockquote class="border-l-4 border-amber-500 pl-4 my-2 text-gray-600 dark:text-gray-400" cite="' . $author . '">';
            $out .= '<cite class="text-xs block mb-1">' . $author . '</cite>';
            $out .= nl2br(htmlspecialchars(trim($quoteBody), ENT_QUOTES, 'UTF-8'));
            $out .= '</blockquote>';
            $rest = substr($afterOpen, $endPos + strlen($close));
        }
        $out .= nl2br(htmlspecialchars($rest, ENT_QUOTES, 'UTF-8'));
        return $out;
    }
}
