<?php

declare(strict_types=1);

namespace Tavaan\Engine\Log\Formatters;

use Tavaan\Engine\Log\Contracts\FormatterInterface;
use Tavaan\Engine\Log\LogRecord;

/**
 * Line Formatter — خروجی متنی ساده با interpolation.
 *
 * Pattern: [<timestamp>] <level>.<channel>: <message> <context>
 */
final class LineFormatter implements FormatterInterface
{
    public function __construct(
        private readonly string $format = '[{ts}] {level}.{channel}: {message} {context}',
    ) {
    }

    public function format(LogRecord $record): string
    {
        $message = $record->message;
        // Interpolate {placeholders} from context
        foreach ($record->context as $k => $v) {
            $message = \str_replace('{' . $k . '}', $this->stringify($v), $message);
        }
        $contextStr = $record->context === [] ? '' : ' ' . \json_encode($record->context, \JSON_UNESCAPED_UNICODE);
        return \strtr($this->format, [
            '{ts}' => $record->timestamp->format('Y-m-d H:i:s.u'),
            '{level}' => $record->level->name(),
            '{channel}' => $record->channel,
            '{message}' => $message,
            '{context}' => $contextStr,
        ]);
    }

    private function stringify(mixed $v): string
    {
        if ($v === null) {
            return 'null';
        }
        if (\is_scalar($v)) {
            return (string) $v;
        }
        return \json_encode($v, \JSON_UNESCAPED_UNICODE) ?: '';
    }
}
