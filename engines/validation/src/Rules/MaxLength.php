<?php
declare(strict_types=1);

namespace Tavaan\Engine\Validation\Rules;

use Tavaan\Engine\Validation\Contracts\RuleInterface;

/**
 * MaxLength — فیلد باید حداکثر n کاراکتر داشته باشد.
 */
final class MaxLength implements RuleInterface
{
    public function __construct(
        private readonly int $max,
    ) {}

    public function passes(string $attribute, mixed $value, array $data): bool
    {
        if ($value === null) return true;
        if (is_string($value)) return mb_strlen($value) <= $this->max;
        if (is_array($value)) return count($value) <= $this->max;
        return true;
    }

    public function message(): string
    {
        return "فیلد باید حداکثر {$this->max} کاراکتر داشته باشد.";
    }
}
