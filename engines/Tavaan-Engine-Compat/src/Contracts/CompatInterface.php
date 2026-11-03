<?php

declare(strict_types=1);

namespace Tavaan\Engine\Compat\Contracts;

/**
 * قرارداد موتور Compat — سازگاری و Polyfill.
 *
 * @see \Tavaan\Engine\Compat\Compat
 * @api 1.0
 * @see docs/03-engines/compat.md (DOC-083E)
 */
interface CompatInterface
{
    /**
     * بررسی وجود یک افزونهٔ PHP.
     *
     * @param string $extension نام افزونه (e.g. 'mbstring', 'gd', 'zip')
     * @return bool true اگر افزونه بارگذاری شده باشد
     */
    public function has(string $extension): bool;

    /**
     * بررسی وجود حداقل یکی از افزونه‌های داده‌شده.
     *
     * @param list<string> $extensions
     */
    public function hasAny(array $extensions): bool;

    /**
     * درخواست اجباری یک افزونه برای قابلیت مشخص.
     *
     * @param string $extension نام افزونه
     * @param string $capability نام قابلیت (e.g. 'image.resize')
     * @throws \Tavaan\Engine\Compat\EngineCapabilityException اگر افزونه موجود نباشد
     */
    public function require(string $extension, string $capability): void;

    /**
     * گزارش کامل وضعیت همهٔ افزونه‌های زیربنایی.
     *
     * @return array<string, array{required: bool, loaded: bool, fallback: string}>
     */
    public function report(): array;

    /**
     * تشخیص درایور فعال برای یک قابلیت.
     *
     * @param string $capability 'image'|'zip'|'http'|'math'|'unicode'|'mime'
     * @return string نام درایور فعال (e.g. 'imagick', 'gd', 'none')
     */
    public function driver(string $capability): string;

    /**
     * نسخهٔ PHP جاری.
     */
    public function version(): string;
}
