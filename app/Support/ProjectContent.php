<?php

namespace App\Support;

use DOMDocument;
use DOMElement;

final class ProjectContent
{
    public static function html(?string $content): string
    {
        if (! $content) return '';
        // Older records contain plain text rather than editor HTML.
        if ($content === strip_tags($content)) {
            return '<p>'.nl2br(e($content), false).'</p>';
        }
        $safe = strip_tags((string) str($content)->sanitizeHtml(), '<p><br><strong><b><em><i><ul><ol><li><h3><a>');
        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8"><html><body>'.$safe.'</body></html>', LIBXML_NONET);
            // Descriptions cannot introduce styles, event handlers, images or a second H1.
            foreach ($document->getElementsByTagName('*') as $node) {
                if (! $node instanceof DOMElement) continue;
                foreach (iterator_to_array($node->attributes) as $attribute) {
                    if ($node->tagName !== 'a' || ! in_array($attribute->name, ['href', 'title'], true)) {
                        $node->removeAttribute($attribute->name);
                    }
                }
                if ($node->tagName === 'a' && $node->hasAttribute('href')) {
                    $href = $node->getAttribute('href');
                    if (! preg_match('~^(https?://|mailto:|/(?!/)|#)~i', $href)) $node->removeAttribute('href');
                }
            }
            $body = $document->getElementsByTagName('body')->item(0);
            $html = '';
            foreach ($body?->childNodes ?? [] as $node) $html .= $document->saveHTML($node);
            return $html;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }
}
