<?php
declare(strict_types=1);

namespace Tavaan\Engine\Validation\Rules;

use Attribute;

/**
 * کلاس پایه برای validation attributeهای PHP 8.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ValidationAttribute
{
    public function __construct(
        public readonly ?string $message = null,
    ) {}

    public function rule(): string
    {
        return '';
    }

    public function passes(string $attribute, mixed $value, array $data = []): bool
    {
        return true;
    }
}
