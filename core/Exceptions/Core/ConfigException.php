<?php
declare(strict_types=1);
namespace Tavaan\Core\Exceptions\Core;

use Tavaan\Core\Exceptions\TavaanException;

class ConfigException extends TavaanException
{
    public static function forMissingKey(string $key): self
    {
        return new self('core.config.missing_key', ['key' => $key], "کلید پیکربندی '{$key}' یافت نشد.");
    }
    public static function forInvalidValue(string $key, mixed $value): self
    {
        return new self('core.config.invalid_value', ['key' => $key, 'value' => $value], "مقدار نامعتبر برای کلید '{$key}'.");
    }
}
