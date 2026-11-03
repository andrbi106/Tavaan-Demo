<?php

declare(strict_types=1);

namespace Tavaan\Engine\Console;

/**
 * SignatureParser — پارس signature کلاسیک Tavaan:
 *   command:name {arg} {opt?} {--flag} {--opt=default}
 */
final class SignatureParser
{
    public static function parse(string $signature): array
    {
        $parts = \preg_split('/\s+/', \trim($signature)) ?: [];
        $name = \array_shift($parts) ?? '';
        $arguments = [];
        $options = [];

        foreach ($parts as $part) {
            if (!\preg_match('/^\{(.+)\}$/', $part, $m)) continue;
            $spec = $m[1];
            if (\str_starts_with($spec, '--')) {
                $options[] = self::parseOption(\substr($spec, 2));
            } else {
                $arguments[] = self::parseArgument($spec);
            }
        }
        return [
            'name' => $name,
            'arguments' => $arguments,
            'options' => $options,
        ];
    }

    private static function parseArgument(string $spec): array
    {
        $parts = \explode(':', $spec, 2);
        $name = \trim($parts[0]);
        $description = $parts[1] ?? '';
        $optional = \str_ends_with($name, '?');
        $name = \rtrim($name, '?');
        return [
            'name' => $name,
            'required' => !$optional,
            'description' => \trim($description),
        ];
    }

    private static function parseOption(string $spec): array
    {
        $parts = \explode(':', $spec, 2);
        $name = \trim($parts[0]);
        $description = $parts[1] ?? '';
        $default = null;
        $hasValue = true;

        // Check for "=default" syntax
        if (\preg_match('/^([^\s=]+)=(.*)$/', $name, $m)) {
            $name = $m[1];
            $default = $m[2];
        } elseif (!\str_contains($name, '=')) {
            $hasValue = false;
        }
        return [
            'name' => $name,
            'has_value' => $hasValue,
            'default' => $default,
            'description' => \trim($description),
        ];
    }
}
