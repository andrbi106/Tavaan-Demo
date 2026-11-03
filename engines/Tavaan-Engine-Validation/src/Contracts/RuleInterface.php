<?php

declare(strict_types=1);

namespace Tavaan\Engine\Validation\Contracts;

/**
 * قرارداد Rule — هر قاعدهٔ اعتبارسنجی این را پیاده‌سازی می‌کند.
 */
interface RuleInterface
{
    /**
     * بررسی اعتبارسنجی.
     *
     * @param string $attribute نام فیلد
     * @param mixed $value مقدار
     * @param array<string, mixed> $data همه‌ی داده‌ها
     */
    public function passes(string $attribute, mixed $value, array $data): bool;

    public function message(): string;
}
