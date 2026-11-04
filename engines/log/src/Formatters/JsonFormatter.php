<?php

declare(strict_types=1);

namespace Tavaan\Engine\Log\Formatters;

use Tavaan\Engine\Log\Contracts\FormatterInterface;
use Tavaan\Engine\Log\LogRecord;

/**
 * JSON Formatter — خروجی JSON Lines.
 */
final class JsonFormatter implements FormatterInterface
{
    public function format(LogRecord $record): string
    {
        return (string) \json_encode($record->toArray(), \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
    }
}
