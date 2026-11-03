<?php

declare(strict_types=1);

namespace Tavaan\Engine\View;

/**
 * Escaper — escape ایمن برای HTML/Attribute/JS/URL.
 */
final class Escaper
{
    public static function html(mixed $value): string
    {
        return \htmlspecialchars((string) $value, \ENT_QUOTES, 'UTF-8');
    }

    public static function attr(mixed $value): string
    {
        return \htmlspecialchars((string) $value, \ENT_QUOTES, 'UTF-8');
    }

    public static function js(mixed $value): string
    {
        $json = \json_encode($value, \JSON_HEX_TAG | \JSON_HEX_APOS | \JSON_HEX_QUOT | \JSON_HEX_AMP | \JSON_UNESCAPED_UNICODE);
        return $json === false ? '' : $json;
    }

    public static function url(mixed $value): string
    {
        return \rawurlencode((string) $value);
    }

    /**
     * css
     */
    public static function css(mixed $value): string
    {
        $escaped = self::html($value);
        return str_replace(["<", ">", "&", '"'], ["\3C ", "\3E ", "\26 ", "\22 "], $escaped);
    }
}
