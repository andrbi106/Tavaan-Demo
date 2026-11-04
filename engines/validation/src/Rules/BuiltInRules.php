<?php

declare(strict_types=1);

namespace Tavaan\Engine\Validation\Rules;

use Tavaan\Engine\Validation\Contracts\RuleInterface;

/**
 * Required — فیلد باید موجود و غیر خالی باشد.
 */
final class Required implements RuleInterface
{
    public function passes(string $attribute, mixed $value, array $data): bool
    {
        if (!\array_key_exists($attribute, $data)) return false;
        if ($value === null) return false;
        if (\is_string($value) && \trim($value) === '') return false;
        if (\is_array($value) && \count($value) === 0) return false;
        return true;
    }

    public function message(): string
    {
        return ':attribute الزامی است.';
    }
}

/**
 * String — مقدار باید رشته باشد.
 */
final class StringType implements RuleInterface
{
    public function passes(string $a, mixed $value, array $data): bool
    {
        return \is_string($value);
    }

    public function message(): string
    {
        return ':attribute باید رشته باشد.';
    }
}

/**
 * Integer — مقدار باید عدد صحیح باشد.
 */
final class Integer implements RuleInterface
{
    public function passes(string $a, mixed $value, array $data): bool
    {
        return \filter_var($value, \FILTER_VALIDATE_INT) !== false;
    }

    public function message(): string
    {
        return ':attribute باید عدد صحیح باشد.';
    }
}

/**
 * Numeric — مقدار باید عددی باشد.
 */
final class Numeric implements RuleInterface
{
    public function passes(string $a, mixed $value, array $data): bool
    {
        return \is_numeric($value);
    }

    public function message(): string
    {
        return ':attribute باید عدد باشد.';
    }
}

/**
 * Email — مقدار باید ایمیل معتبر باشد.
 */
final class Email implements RuleInterface
{
    public function passes(string $a, mixed $value, array $data): bool
    {
        return \filter_var($value, \FILTER_VALIDATE_EMAIL) !== false;
    }

    public function message(): string
    {
        return ':attribute باید ایمیل معتبر باشد.';
    }
}

/**
 * Min — حداقل طول/مقدار.
 */
final class Min implements RuleInterface
{
    public function __construct(private readonly int $min) {}

    public function passes(string $a, mixed $value, array $data): bool
    {
        if (\is_string($value)) return \mb_strlen($value) >= $this->min;
        if (\is_numeric($value)) return (float) $value >= $this->min;
        if (\is_array($value)) return \count($value) >= $this->min;
        return false;
    }

    public function message(): string
    {
        return ":attribute باید حداقل {$this->min} باشد.";
    }
}

/**
 * Max — حداکثر طول/مقدار.
 */
final class Max implements RuleInterface
{
    public function __construct(private readonly int $max) {}

    public function passes(string $a, mixed $value, array $data): bool
    {
        if (\is_string($value)) return \mb_strlen($value) <= $this->max;
        if (\is_numeric($value)) return (float) $value <= $this->max;
        if (\is_array($value)) return \count($value) <= $this->max;
        return false;
    }

    public function message(): string
    {
        return ":attribute باید حداکثر {$this->max} باشد.";
    }
}

/**
 * Between — مقدار در بازه.
 */
final class Between implements RuleInterface
{
    public function __construct(
        private readonly int $min,
        private readonly int $max,
    ) {}

    public function passes(string $a, mixed $value, array $data): bool
    {
        if (\is_string($value)) {
            $len = \mb_strlen($value);
            return $len >= $this->min && $len <= $this->max;
        }
        if (\is_numeric($value)) {
            $v = (float) $value;
            return $v >= $this->min && $v <= $this->max;
        }
        if (\is_array($value)) {
            $c = \count($value);
            return $c >= $this->min && $c <= $this->max;
        }
        return false;
    }

    public function message(): string
    {
        return ":attribute باید بین {$this->min} و {$this->max} باشد.";
    }
}

/**
 * In — مقدار باید یکی از مقادیر داده‌شده باشد.
 *
 * @param list<string> $values
 */
final class In implements RuleInterface
{
    public function __construct(private readonly array $values) {}

    public function passes(string $a, mixed $value, array $data): bool
    {
        return \in_array($value, $this->values, true);
    }

    public function message(): string
    {
        return ':attribute باید یکی از مقادیر مجاز باشد.';
    }
}

/**
 * Nullable — فیلد می‌تواند null باشد (در صورت null، بررسی دیگر قواعد را رد می‌کند).
 */
final class Nullable implements RuleInterface
{
    public function passes(string $a, mixed $value, array $data): bool
    {
        return true;  // فقط در logic validator استفاده می‌شود
    }

    public function message(): string
    {
        return '';
    }
}

/**
 * Boolean
 */
final class BooleanType implements RuleInterface
{
    public function passes(string $a, mixed $value, array $data): bool
    {
        return \is_bool($value) || \in_array($value, [0, 1, '0', '1', true, false], true);
    }

    public function message(): string
    {
        return ':attribute باید boolean باشد.';
    }
}

/**
 * Array
 */
final class ArrayType implements RuleInterface
{
    public function passes(string $a, mixed $value, array $data): bool
    {
        return \is_array($value);
    }

    public function message(): string
    {
        return ':attribute باید آرایه باشد.';
    }
}

/**
 * Confirmed — فیلد باید تایید شود (مثلاً password و password_confirmation).
 */
final class Confirmed implements RuleInterface
{
    public function passes(string $attribute, mixed $value, array $data): bool
    {
        $confirmationField = $attribute . '_confirmation';
        return isset($data[$confirmationField]) && $data[$confirmationField] === $value;
    }

    public function message(): string
    {
        return 'تاییدیه :attribute مطابقت ندارد.';
    }
}

/**
 * Regex — اعتبارسنجی با الگوی regex.
 */
final class Regex implements RuleInterface
{
    public function __construct(private readonly string $pattern) {}

    public function passes(string $a, mixed $value, array $data): bool
    {
        if (!\is_string($value)) return false;
        return \preg_match($this->pattern, $value) === 1;
    }

    public function message(): string
    {
        return 'فرمت :attribute نامعتبر است.';
    }
}

/**
 * IranMobile — شماره موبایل ایرانی.
 */
final class IranMobile implements RuleInterface
{
    public function passes(string $a, mixed $value, array $data): bool
    {
        if (!\is_string($value)) return false;
        return (bool) \preg_match('/^09\d{9}$/', $value);
    }

    public function message(): string
    {
        return ':attribute باید شماره موبایل ایرانی معتبر باشد (09XXXXXXXXX).';
    }
}
