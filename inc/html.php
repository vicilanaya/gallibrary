<?php
/**
 * HTML output helpers for the GAL Resource Library.
 */
declare(strict_types=1);

/**
 * Escape text for HTML element content and attribute values.
 */
function h(?string $text): string
{
    return htmlspecialchars($text ?? '', ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
}

/**
 * Allow only web, email and phone links. Anything else (e.g. "javascript:")
 * is replaced with "#" so a typo in phpMyAdmin can't create a dangerous link.
 */
function safe_url(string $url): string
{
    $url = trim($url);
    return preg_match('~^(https?://|mailto:|tel:)~i', $url) === 1 ? $url : '#';
}

/**
 * Anchor id for a section heading: "education" for English,
 * "education-es" for Spanish (and "<slug>-<lang>" for any other language).
 */
function section_anchor(string $slug, string $lang): string
{
    return $lang === 'en' ? $slug : $slug . '-' . $lang;
}
