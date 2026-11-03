<?php

declare(strict_types=1);

namespace Tavaan\Engine\I18n\Contracts;

/**
 * قرارداد Translator.
 */
interface TranslatorInterface
{
    public function get(string $key, array $replace = [], ?string $locale = null): string;

    public function choice(string $key, int $number, array $replace = [], ?string $locale = null): string;

    public function has(string $key, ?string $locale = null): bool;

    public function setLocale(string $locale): void;

    public function getLocale(): string;

    public function getFallback(): string;

    public function addNamespace(string $namespace, string $hint): void;

    /**
     * @param array<string, mixed> $lines
     */
    public function addLines(array $lines, string $locale, string $namespace = '*'): void;
}
