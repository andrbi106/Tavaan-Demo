<?php

declare(strict_types=1);

namespace Tavaan\Engine\Validation;

use Tavaan\Engine\Validation\Contracts\RuleInterface;
use Tavaan\Engine\Validation\Rules\Required;
use Tavaan\Engine\Validation\Rules\StringType;
use Tavaan\Engine\Validation\Rules\Integer;
use Tavaan\Engine\Validation\Rules\Numeric;
use Tavaan\Engine\Validation\Rules\Email;
use Tavaan\Engine\Validation\Rules\Min;
use Tavaan\Engine\Validation\Rules\Max;
use Tavaan\Engine\Validation\Rules\Between;
use Tavaan\Engine\Validation\Rules\In;
use Tavaan\Engine\Validation\Rules\Nullable;
use Tavaan\Engine\Validation\Rules\BooleanType;
use Tavaan\Engine\Validation\Rules\ArrayType;
use Tavaan\Engine\Validation\Rules\Confirmed;
use Tavaan\Engine\Validation\Rules\Regex;
use Tavaan\Engine\Validation\Rules\IranMobile;

/**
 * Validator — اعتبارسنجی داده‌ها.
 *
 * @see docs/03-engines/validation.md (DOC-051)
 */
final class Validator
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, list<string|RuleInterface>> $rules
     * @param array<string, string> $messages
     * @param array<string, string> $attributes
     */
    public function __construct(
        private array $data,
        private readonly array $rules = [],
        private readonly array $messages = [],
        private readonly array $attributes = [],
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, list<string|RuleInterface>> $rules
     */
    public static function make(array $data, array $rules, array $messages = [], array $attributes = []): self
    {
        return new self($data, $rules, $messages, $attributes);
    }

    /**
     * @return array<string, list<string>>
     */
    public function errors(): array
    {
        $errors = [];
        foreach ($this->rules as $field => $rules) {
            $rules = (array) $rules;
            $value = $this->data[$field] ?? null;
            // If nullable and value is null, skip other rules
            $nullable = false;
            foreach ($rules as $rule) {
                if ($rule instanceof Nullable) {
                    $nullable = true;
                    break;
                }
                if (\is_string($rule) && $rule === 'nullable') {
                    $nullable = true;
                    break;
                }
            }
            if ($nullable && $value === null) continue;
            foreach ($rules as $rule) {
                $ruleInstance = $this->resolveRule($rule);
                if ($ruleInstance === null) continue;
                if ($ruleInstance instanceof Nullable) continue;
                if (!$ruleInstance->passes($field, $value, $this->data)) {
                    $message = $this->messages[$field] ?? $ruleInstance->message();
                    $message = \str_replace(':attribute', $this->attributes[$field] ?? $field, $message);
                    $errors[$field][] = $message;
                    // Stop at first error for this field
                    break;
                }
            }
        }
        return $errors;
    }

    public function passes(): bool
    {
        return empty($this->errors());
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * @return array<string, mixed>
     */
    public function validate(): array
    {
        if ($this->fails()) {
            throw new Exceptions\ValidationException($this->errors());
        }
        return \array_intersect_key($this->data, $this->rules);
    }

    /**
     * @return list<string>
     */
    public function allErrors(): array
    {
        $all = [];
        foreach ($this->errors() as $errors) {
            foreach ($errors as $e) $all[] = $e;
        }
        return $all;
    }

    private function resolveRule(string|RuleInterface $rule): ?RuleInterface
    {
        if ($rule instanceof RuleInterface) return $rule;
        if (!\is_string($rule)) return null;
        // Parse "name:param1,param2"
        $parts = \explode(':', $rule, 2);
        $name = $parts[0];
        $params = isset($parts[1]) ? \explode(',', $parts[1]) : [];
        return $this->createRule($name, $params);
    }

    /**
     * @param list<string> $params
     */
    private function createRule(string $name, array $params): ?RuleInterface
    {
        return match ($name) {
            'required' => new Required(),
            'string' => new StringType(),
            'integer', 'int' => new Integer(),
            'numeric' => new Numeric(),
            'email' => new Email(),
            'boolean', 'bool' => new BooleanType(),
            'array' => new ArrayType(),
            'nullable' => new Nullable(),
            'confirmed' => new Confirmed(),
            'iran_mobile' => new IranMobile(),
            'min' => new Min((int) ($params[0] ?? 0)),
            'max' => new Max((int) ($params[0] ?? 0)),
            'between' => new Between((int) ($params[0] ?? 0), (int) ($params[1] ?? 0)),
            'in' => new In($params),
            'regex' => new Regex($params[0] ?? '//'),
            default => null,
        };
    }

    /**
     * extend
     */
    public function extend(string $rule, callable $callback): self
    {
        $this->customRules[$rule] = $callback;
        return $this;
    }
    /**
     * forDto
     */
    public function forDto(string $dtoClass): mixed
    {
        $data = $this->validate();
        return new $dtoClass($data);
    }
    /**
     * replacer
     */
    public function replacer(string $rule, callable $callback): self
    {
        $this->replacers[$rule] = $callback;
        return $this;
    }
    /**
     * sometimes
     */
    public function sometimes(string $field, array|string $rules, callable $callback): self
    {
        if ($callback($this->data)) {
            $existing = $this->rules[$field] ?? [];
            $this->rules[$field] = array_merge($existing, (array) $rules);
        }
        return $this;
    }
}
