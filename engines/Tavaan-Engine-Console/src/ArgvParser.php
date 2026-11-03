<?php

declare(strict_types=1);

namespace Tavaan\Engine\Console;

/**
 * ArgvParser — پارس $argv به arguments و options.
 */
final class ArgvParser
{
    /**
     * @param list<string> $argv
     * @return array{0: list<string>, 1: array<string, mixed>}
     */
    public static function parse(array $argv): array
    {
        $arguments = [];
        $options = [];
        $i = 0;
        while ($i < \count($argv)) {
            $arg = $argv[$i];
            // Long option: --name=value or --name
            if (\str_starts_with($arg, '--')) {
                $body = \substr($arg, 2);
                if (\str_contains($body, '=')) {
                    [$k, $v] = \explode('=', $body, 2);
                    $options[$k] = $v;
                } else {
                    // Look ahead for value or treat as flag
                    if (isset($argv[$i + 1]) && !\str_starts_with($argv[$i + 1], '--')) {
                        $options[$body] = $argv[$i + 1];
                        $i++;
                    } else {
                        $options[$body] = true;
                    }
                }
            } elseif (\str_starts_with($arg, '-')) {
                // Short option: -f or -fvalue
                $body = \substr($arg, 1);
                if (\strlen($body) === 1) {
                    if (isset($argv[$i + 1]) && !\str_starts_with($argv[$i + 1], '-')) {
                        $options[$body] = $argv[$i + 1];
                        $i++;
                    } else {
                        $options[$body] = true;
                    }
                } else {
                    $options[$body[0]] = \substr($body, 1);
                }
            } else {
                $arguments[] = $arg;
            }
            $i++;
        }
        return [$arguments, $options];
    }
}
